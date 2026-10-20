<?php

class ControllerPaymentLloydsBankcard extends Controller {
    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('lloydsbankcard.log');
    }
    protected function index() {

        $this->language->load('payment/lloyds_bankcard');
        $this->load->model('checkout/order');
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $this->data['txntype']          = $this->config->get('lloyds_bankcard_txntype');
        $this->data['timezone']         = date_default_timezone_get();
        $this->data['txndatetime']      = date("Y:m:d-H:i:s");
        $this->data['hash_algorithm']   = 'SHA256';
        $this->data['hash']             = $this->createHash($amount,$this->config->get('lloyds_bankcard_currency'),$this->data['txndatetime']);
        $this->data['storename']        = $this->config->get('lloyds_bankcard_store');
        $this->data['chargetotal']      = $amount;
        $this->data['currency']         = $this->config->get('lloyds_bankcard_currency');
        $this->data['oid']              = $this->session->data['order_id'];
        $this->data['mode']              = $this->config->get('lloyds_bankcard_mode');
        $this->data['responseSuccessURL'] =  $this->url->link('payment/lloyds_bankcard/successful', '', 'SSL');
        $this->data['responseFailURL'] =  $this->url->link('payment/lloyds_bankcard/fail', '', 'SSL');
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/lloyds_bankcard.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/lloyds_bankcard.tpl';
        } else {
            $this->template = 'default/template/payment/lloyds_bankcard.tpl';
        }

        $this->render();
    }

    public function successful() {
        $this->load->model('checkout/order');

        $this->log->write('______________________Lloyds_______SUCCESS_______');
        $this->log->write(print_r($this->request->post,true));

        if(isset($this->request->post['oid']) && isset($this->request->post['approval_code'])){
            $order_id = $this->request->post['oid'];
            if($this->request->post['approval_code'][0] ==  'Y' || $this->request->post['status'] == 'APPROVED' ){
                $this->model_checkout_order->confirm($order_id, $this->config->get('lloyds_bankcard_order_status_id'),'Payment successful',1);
                $this->redirect($this->url->link('checkout/success', '', 'SSL'));
            }

            if($this->request->post['approval_code'][0] ==  'N' || $this->request->post['status'] == 'DECLINED')
            {
                $this->model_checkout_order->confirm($order_id, '10',$this->request->post['fail_reason']);
                $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            }
        }
        else{
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
        }
        
    }
    public function fail() {
    

        $this->log->write('______________________Lloyds___FAIL_________________');
        $this->log->write(print_r($this->request->post,true));
        
        if(isset($this->request->post['oid']) && isset($this->request->post['approval_code'])){
            $order_id = $this->request->post['oid'];
            if($this->request->post['approval_code'][0] ==  'N' || $this->request->post['status'] == 'DECLINED'){   
                $this->load->model('checkout/order');
                $this->model_checkout_order->confirm($order_id, '10',$this->request->post['fail_reason'],false);
              //  $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            }
        }
        $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
       
        
    }
    public function callback() {
        $this->load->model('checkout/order');

        $this->log->write('______________________Lloyds___CALLBACK________________');
        $this->log->write(print_r($this->request->post,true));

        if(isset($this->request->post['oid']) && isset($this->request->post['approval_code'])){
            $order_id = $this->request->post['oid'];
            if($this->request->post['approval_code'][0] ==  'Y' || $this->request->post['status'] == 'APPROVED' ){
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);
                if (!$order_info['order_status_id']) { //  Missing order 
                    $this->model_checkout_order->confirm($order_id, $this->config->get('lloyds_bankcard_order_status_id'),'Payment successful',1);
                }
            }
            /* if($this->request->post['approval_code'][0] ==  'N' || $this->request->post['status'] == 'DECLINED'){
                $this->model_checkout_order->confirm($order_id, '10',$this->request->post['fail_reason']);
            }*/
        }
        echo "OK";
        die();
        
    }
    function createHash($chargetotal, $currency,$dateTime) {
        $storename      = $this->config->get('lloyds_bankcard_store');
        $sharedSecret   = $this->config->get('lloyds_bankcard_password');
        $stringToHash   = $storename . $dateTime . $chargetotal . $currency . $sharedSecret;
        $ascii = bin2hex($stringToHash);
        return hash('sha256',$ascii);
       }
}

?>