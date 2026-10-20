<?php

class ControllerPaymentPaymetryx extends Controller {

    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('paymetryx.log');
    }

    public function index() {
        $this->load->language('payment/paymetryx');

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $amount = $this->currency->format($order_info['total'],$order_info['currency_code'] ,'', true); 
        $data = array();
        $data['merchantID'] = $this->config->get('paymetryx_merchant_id');
        $data['action'] = 'SALE';
        $data['type'] = 1;
        $data['amount'] = round($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false)*100);
        $data['countryCode'] = '826';
        $data['currencyCode'] = '826';
        $data['transactionUnique'] =  $this->session->data['order_id'] . 'P' .uniqid(); // $this->session->data['order_id'] . 'P' . random_int(1000, 9999);
        $data['orderRef'] =  $this->session->data['order_id'] ;
        // $data['formResponsive'] = "Y";
        
        $data['callbackURL'] =  $this->url->link('payment/paymetryx/callback','','SSL');
        $data['redirectURL'] =   $this->url->link('payment/paymetryx/response','','SSL');
       
        $data['customerName'] = preg_replace('/[^a-z\d ]/i', ' ',htmlspecialchars_decode($order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']));
        $data['customerAddress'] = preg_replace('/[^a-z\d ]/i', ' ',htmlspecialchars_decode($order_info['payment_address_1'] . ' ' . $order_info['payment_address_2']));
        $data['customerPostcode'] = $order_info['payment_postcode'];
        $data['customerEmail'] =  $order_info['email'];
        $data['customerPhone'] =  $order_info['telephone'];
        
     
        $signature = $this->createSignature($data,$this->config->get('paymetryx_secret'));
        $data['signature'] = $signature;
        $this->data['form_data'] = $data;

     //   $this->log->write('SIG : ' . print_r($signature,true));
        
        $this->data['button_confirm'] = 'Pay '.$amount;
        $this->data['button_back'] = $this->language->get('button_back');
        $this->data['action'] = 'https://gateway.paymetryx.com/paymentform/';
        
      
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paymetryx.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/paymetryx.tpl';
        } else {
            $this->template = 'default/template/payment/paymetryx.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function response() {
        $res = $this->request->post;
        $this->load->language('payment/paymetryx');
        // Extract the return signature as this isn't hashed
        $signature = null;
        if (isset($res['signature'])) {
            $signature = $res['signature'];
            unset($res['signature']);
        }

        if (!$signature || $signature !== $this->createSignature($res, $this->config->get('paymetryx_secret'))) {
            // You should exit gracefully
            $this->log->write('response  : ' . print_r($this->request->post, true));
            $this->log->write('response : '. 'Sorry, the signature check failed');
            $this->data['text_response'] = $this->language->get('text_decline');
            $this->data['text_message'] = 'Sorry, the signature check failed';
            $this->data['text_link'] = sprintf($this->language->get('text_link'), $this->url->link('checkout/checkoutetm', '', 'SSL'));
            $this->data['response_url'] = 'checkout/checkoutetm';
        }
        else {
            // Check the response code
            if ($res['responseCode'] === "0") {
                $this->redirect($this->url->link('checkout/success'));
            } else {
               // echo "<p>Failed to take payment: " . htmlentities($res['responseMessage']) . 
               // "</p>";
                $this->data['text_response'] = $this->language->get('text_decline');
                $this->data['text_message'] = htmlentities($res['responseMessage']);
                $this->data['text_link'] = sprintf($this->language->get('text_link'), $this->url->link('checkout/checkoutetm', '', 'SSL'));
                $this->data['response_url'] = 'checkout/checkoutetm';
            }
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paymetryx_response.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/paymetryx_response.tpl';
        } else {
            $this->template = 'default/template/payment/paymetryx_response.tpl';
        }
        $this->response->setOutput($this->render());
        
    }
    public function callback() {
        $this->log->write('callback : ' . print_r($this->request->post, true));
       
        $res = $this->request->post;
        $signature = null;
        if (isset($res['signature'])) {
         $signature = $res['signature'];
         unset($res['signature']);
        } 
        if (!$signature || $signature !== $this->createSignature($res, $this->config->get('paymetryx_secret'))) {
        // You should exit gracefully
            $this->log->write('callback : ' .'Sorry, the signature check failed' );
            die('Sorry, the signature check failed');
        }
        // Check the response code
        if ($res['responseCode'] === "0") {
            $this->load->model('checkout/order');
            $order_id = $this->request->post['orderRef'];
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            if (!$order_info['order_status_id']) { //  Missing order 
                $this->model_checkout_order->confirm($order_id, $this->config->get('paymetryx_order_status_id'));
            }
            echo "Thank you for your payment";
        } else {
            echo "Failed to take payment: " . htmlentities($res['responseMessage']) ."";
            $this->log->write('callback : Failed to take payment: ' . htmlentities($res['responseMessage']) );
        }

        die();
    }
   
     // Function to create a message signature
    private function createSignature(array $data, $key) {
        // Sort by field name
        ksort($data);
        // Create the URL encoded signature string
        $ret = http_build_query($data, '', '&');
        // Normalise all line endings (CRNL|NLCR|NL|CR) to just NL (%0A)
        $ret = str_replace(array('%0D%0A', '%0A%0D', '%0D'), '%0A', $ret);
        // Hash the signature string and the key together
        return hash('SHA512', $ret . $key);
    }

}
