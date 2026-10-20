<?php
class ControllerPaymentEpdq extends Controller {

    protected function index() {

        $this->load->language('payment/epdq');

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');
        if ($this->config->get('epdq_test')) {
            $this->data['action'] = 'https://mdepayments.epdq.co.uk/ncol/test/orderstandard.asp'; // This is a constant for both test and live
        }
        else{
            $this->data['action'] =  'https://payments.epdq.co.uk/ncol/prod/orderstandard.asp';
        }

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

        $this->data['epdq_pspid'] = $this->config->get('epdq_pspid'); // epdq2210606
        $PW = $this->config->get('epdq_passphrase'); //HASKSONLINEVKSYC0u4


        $this->data['amount'] = $this->currency->format($order_info['total'], $currency, FALSE, FALSE) * 100;
        // End minimum requirements

        $this->data['order_id'] = $this->session->data['order_id'];
        $this->data['description'] = (($this->config->get('config_name')) ? $this->config->get('config_name') : $this->config->get('config_store'));

        $this->data['billing_fullname'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        if ($order_info['payment_address_2']) {
            $this->data['billing_address'] = $order_info['payment_address_1'] . " " . $order_info['payment_address_2']; // . "\r\n"; . $order_info['payment_city'] . "\r\n" . $order_info['payment_zone'] . "\r\n";
        } else {
            $this->data['billing_address'] = $order_info['payment_address_1'] ; // . "\r\n"  . $order_info['payment_city'] . "\r\n" . $order_info['payment_zone'] . "\r\n";
        }
        $this->data['billing_postcode'] = $order_info['payment_postcode'];
        $this->data['billing_city'] =  $order_info['payment_city'] ;
        $this->data['billing_country'] =  $order_info['payment_country'] ;

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

        $this->data['success_url'] = $this->url->link('checkout/epdq/confirm', '', 'SSL');
        $this->data['cancel_url'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
        $this->data['declined_url'] = $this->url->link('checkout/checkoutetm', 'method=decline', 'SSL');
        $this->data['exception_url'] = $this->url->link('checkout/epdq/confirm', 'method=exception', 'SSL');
        
        $this->data['test'] = false;
        if ($this->config->get('epdq_test')) {
            $this->data['test_transaction'] = '100';
            $this->data['test_success_url'] = $this->url->link('checkout/success');
            $this->data['test'] = true;
        }

        if ($this->request->get['route'] != 'checkout/guest/confirm') {
            $this->data['back'] = $this->url->link('checkout/payment', '', 'SSL');
        } else {
            $this->data['back'] = $this->url->link('checkout/guest', '', 'SSL');
        }
        
        $this->data['currency']= $currency;
        $this->data['fonttype']= "Helvetica, Arial";
        $this->data['ShopperLocale']= "en_GB";
        $this->data['logo'] = HTTPS_IMAGE .  $this->config->get('config_logo');
        $this->data['PMLISTTYPE'] = 1;
        $this->data['BGCOLOR'] = "#d1ecf3";
        $this->data['BUTTONBGCOLOR']  = "#cccccc";
        $this->data['BUTTONTXTCOLOR']	="#005588";
        $this->data['TXTCOLOR']="#005588";
        $this->data['TITLE'] = $this->config->get('config_name');
        $this->data['TBLBGCOLOR']  = "#ffffff"; 
        $this->data['TBLTXTCOLOR']="#005588";
      
  	
	//= create string to hash (digest) using values of options/details above
	$DigestivePlain =
	"AMOUNT=" . $this->data['amount']  . $PW .
	"BGCOLOR=" . $this->data['BGCOLOR'] . $PW .
	"BUTTONBGCOLOR=" . $this->data['BUTTONBGCOLOR']  . $PW .
	"BUTTONTXTCOLOR=" . $this->data['BUTTONTXTCOLOR'] . $PW .
	"CN=" . $this->data['billing_fullname']  . $PW . 
	"COM=" . $this->data['description']  . $PW . 
	"CURRENCY=" . $currency . $PW .
	"EMAIL=" . $this->data['email_address'] . $PW .
	"FONTTYPE=" . $this->data['fonttype'] . $PW .
	"LANGUAGE=" . $this->data['ShopperLocale'] . $PW .
	"LOGO=" .$this->data['logo'] . $PW .
	"ORDERID=" . $this->data['order_id'] . $PW .
	"OWNERADDRESS=" . $this->data['billing_address'] . $PW .
	"OWNERCTY=" . $this->data['billing_country'] . $PW .
	"OWNERTELNO=" . $this->data['customer_phone_number'] . $PW . 
	"OWNERTOWN=" . $this->data['billing_city'] . $PW .
	"OWNERZIP=" . $this->data['billing_postcode'] . $PW .
	"PMLISTTYPE=". $this->data['PMLISTTYPE'] . $PW .
	"PSPID=" . $this->data['epdq_pspid'] . $PW .
	"TBLBGCOLOR=" . $this->data['TBLBGCOLOR'] . $PW .
	"TBLTXTCOLOR=" . $this->data['TBLTXTCOLOR'] . $PW .
	"TITLE=" . $this->data['TITLE'] . $PW .
	"TXTCOLOR=" .  $this->data['TXTCOLOR'] . $PW .
	"";
	//echo $DigestivePlain;
	//=SHA encrypt the string=//
	$this->data['strHashedString_plain'] = strtoupper(sha1($DigestivePlain));
	

        $this->id = 'payment';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/epdq.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/epdq.tpl';
        } else {
            $this->template = 'default/template/payment/epdq.tpl';
        }

        // write to log
        try {
            $this->log->write( PHP_EOL .  ' ePDQ GO : ' . $this->data['order_id']. ' - ' . $this->data['billing_fullname'] .' - ' . $this->data['email_address'] .' - ' . $this->data['amount'] );
        } catch (Exception $ex) {

        }
        
        $this->render();
    }

    public function confirm() {
        $this->load->model('checkout/order');
	$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('epdq_order_status_id'));
        $this->redirect($this->url->link('checkout/success'));
    }

    

}

?>