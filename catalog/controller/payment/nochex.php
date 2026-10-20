<?php

// Nochex via form will work for both simple "Seller" account and "Merchant" account holders
// Nochex via APC maybe only avaiable to "Merchant" account holders only - site docs a bit vague on this point
class ControllerPaymentNochex extends Controller {
    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('nochex.log');
    }
    protected function index() {

        $this->load->language('payment/nochex');

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');

        $this->data['action'] = 'https://secure.nochex.com/'; // This is a constant for both test and live

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->data['error'] = (isset($this->session->data['error'])) ? $this->session->data['error'] : NULL;
        unset($this->session->data['error']);

        // Check for supported currency, otherwise convert to GBP.
        $supported_currencies = array('GBP');

        if (in_array($order_info['currency_code'], $supported_currencies)) {
            $currency = $order_info['currency_code'];
        } else {
            $currency = 'GBP';
        }

        //$this->data = array();
        // Nochex minimum requirements
        // The merchant ID is usually your Nochex registered email address but can be altered for "Merchant" accounts see below
        $this->data['merchant_id'] = $this->config->get('nochex_email');

        if ($this->config->get('nochex_email') != $this->config->get('nochex_merchant')) { // This MUST be changed on your Nochex account!!!!
            $this->data['merchant_id'] = $this->config->get('nochex_merchant');
        }

        $this->data['amount'] = $this->currency->format($order_info['total'], $currency, FALSE, FALSE);
        // End minimum requirements

        $this->data['order_id'] = $this->session->data['order_id'];
        $this->data['description'] = (($this->config->get('config_name')) ? $this->config->get('config_name') : $this->config->get('config_store'));

        $this->data['billing_fullname'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        if ($order_info['payment_address_2']) {
            $this->data['billing_address'] = $order_info['payment_address_1'] . "\r\n" . $order_info['payment_address_2'] . "\r\n" . $order_info['payment_city'] . "\r\n" . $order_info['payment_zone'] . "\r\n";
        } else {
            $this->data['billing_address'] = $order_info['payment_address_1'] . "\r\n" . $order_info['payment_city'] . "\r\n" . $order_info['payment_zone'] . "\r\n";
        }
        $this->data['billing_postcode'] = $order_info['payment_postcode'];

        $this->data['delivery_fullname'] = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
        if ($order_info['shipping_address_2']) {
            $this->data['delivery_address'] = $order_info['shipping_address_1'] . "\r\n" . $order_info['shipping_address_2'] . "\r\n" . $order_info['shipping_city'] . "\r\n" . $order_info['shipping_zone'] . "\r\n";
        } else {
            $this->data['delivery_address'] = $order_info['shipping_address_1'] . "\r\n" . $order_info['shipping_city'] . "\r\n" . $order_info['shipping_zone'] . "\r\n";
        }
        $this->data['delivery_postcode'] = $order_info['shipping_postcode'];

        $this->data['email_address'] = $order_info['email'];
        $this->data['customer_phone_number'] = $order_info['telephone'];

        $this->data['hide_billing_details'] = 'true'; // So customer can't change address settings

        $this->data['success_url'] = $this->url->link('checkout/success', '', 'SSL');
        $this->data['cancel_url'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
        //   $this->data['cancel_url']         = $this->url->link('checkout/checkout', '', 'SSL');
        $this->data['declined_url'] = $this->url->link('payment/nochex/callback', 'method=decline', 'SSL');
        $this->data['callback_url'] = $this->url->link('payment/nochex/callback', '&order=' . $this->session->data['order_id'], 'SSL');
        $this->data['test'] = false;
        if ($this->config->get('nochex_test')) {
            $this->data['test_transaction'] = '100';
            $this->data['test_success_url'] = $this->url->link('checkout/success');
            $this->data['test'] = true;
        }

        if ($this->request->get['route'] != 'checkout/guest/confirm') {
            $this->data['back'] = $this->url->link('checkout/payment', '', 'SSL');
        } else {
            $this->data['back'] = $this->url->link('checkout/guest', '', 'SSL');
        }

        $this->id = 'payment';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/nochex.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/nochex.tpl';
        } else {
            $this->template = 'default/template/payment/nochex.tpl';
        }

        // write to log
        try {
            $this->log->write( PHP_EOL .  ' NOCHECH GO : ' . $this->data['order_id']. ' - ' . $this->data['billing_fullname'] .' - ' . $this->data['email_address'] .' - ' . $this->data['amount'] );
        } catch (Exception $ex) {

        }
        
        $this->render();
    }

    public function confirm() {
        
    }

    public function callback() {

        // Log
        try {
            $this->log->write(PHP_EOL . ' NOCHEX CALLBACK : ' . print_r($this->request->post, TRUE));
        } catch (Exception $ex) {
            
        }
        $this->load->language('payment/nochex');

        if (isset($this->request->get['method']) && $this->request->get['method'] == 'decline') {
            $this->session->data['error'] = $this->language->get('error_declined');
            $this->redirect((isset($this->session->data['guest'])) ? $this->url->link('checkout/guest_step_3', '', 'SSL') : $this->url->link('checkout/confirm', '', 'SSL'));
        }

        if (isset($this->request->post['order_id'])) {
            $order_id = $this->request->post['order_id'];
        } else {
            $order_id = 0;
        }

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($order_id);

        if (!$order_info) {
            $this->session->data['error'] = $this->language->get('error_no_order');
            $this->redirect((isset($this->session->data['guest'])) ? $this->url->link('checkout/guest_step_3', '', 'SSL') : $this->url->link('checkout/confirm', '', 'SSL'));
        }

        
        $amount = $this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], FALSE, FALSE);
        if($amount != $this->request->post['amount']) {
            $this->session->data['error'] = 'Amount not matching..';
            $this->log->write(PHP_EOL . ' NOCHEX AMOUNT ISSUE : ' . print_r($this->request->post, TRUE));
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
        }
       
        // Fraud Verification Step.
        $request = http_build_query($_POST);

        $ch = curl_init('https://secure.nochex.com/apc/apc.aspx');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        curl_close($ch);

        if (strcmp($response, 'AUTHORISED') == 0) {
            $this->model_checkout_order->confirm($order_id, $this->config->get('nochex_order_status_id'), 'APC AUTHORISED.');
        } else {
            $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'), 'APC response DECLINED. Manually check the transaction.');
        }

        // Since it returned, the customer should see success.
        // It's up to the store owner to manually verify payment.
        $this->redirect($this->url->link('checkout/success', '', 'SSL'));
    }

}

?>