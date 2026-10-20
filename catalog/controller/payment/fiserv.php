<?php
 
class ControllerPaymentFiserv extends Controller {
    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('fiserv.log');
    }
    protected function index() {
            $this->language->load('payment/fiserv');
            $this->load->model('checkout/order');
	    $this->data['button_confirm'] = $this->language->get('button_confirm');

            $order_id = $this->session->data['order_id'];
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);  
         
            $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
            
            $this->data['cusomer_email'] = $order_info['email'];
            $this->data['cusomer_phone'] = $order_info['telephone'];
            $this->data['cusomer_address_1'] = $order_info['payment_address_1'];
            $this->data['cusomer_address_2'] = $order_info['payment_address_2'];
            $this->data['cusomer_city'] = $order_info['payment_city'];
            $this->data['cusomer_postcode'] = $order_info['payment_postcode'];
            $this->data['cusomer_country_code'] = $order_info['payment_country_id'];
            $this->data['cusomer_name'] = html_entity_decode($order_info['payment_firstname'] .' ' . $order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
           
            $this->data['responseFailURL'] = $this->url->link('payment/fiserv/fail');
            $this->data['responseSuccessURL'] = $this->url->link('payment/fiserv/success');
            $this->data['transactionNotificationURL'] = $this->url->link('payment/fiserv/callback');
            
            
            if($this->config->get('fiserv_mode')=='test') {
                $this->data['fiserv_action'] = 'https://test.ipg-online.com/connect/gateway/processing';
            }
            else {
                // Live url
                $this->data['fiserv_action'] ='https://www.ipg-online.com/connect/gateway/processing';
            }
            $this->data['txndatetime'] =  date("Y:m:d-H:i:s");
            $this->session->data['txndatetime'] = $this->data['txndatetime'];

            $this->data['hash'] = $this->createHash($amount, $this->data['txndatetime']);
            $this->data['storename'] =$this->config->get('fiserv_storeid');
            $this->data['order_id'] = $order_id;
            $this->data['amount'] = $amount;
            $this->data['curr'] = $this->config->get('fiserv_currency');
            $this->data['txnmode'] = $this->config->get('fiserv_txn_mode');
            $this->data['txntype'] = $this->config->get('fiserv_txntype');
            $this->data['timezone']         = date_default_timezone_get();
		
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/fiserv.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/payment/fiserv.tpl';
            } else {
                    $this->template = 'default/template/payment/fiserv.tpl';
            }	
            $this->render();
	}

    public function fail() {
        if($this->verifyresponse()){
            // payment failed
           $this->log->write('Finserve ::  Message: '. print_r($this->request->post,true));
           $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));

        }
        else {
            // Tech error
            $this->log->write('Finserve :: Callback EROR : Message: '. print_r($this->request->post,true));
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
        }
	
    }
        
    public function success() {
            $order_id = $this->request->post['oid'];
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            if ($order_info) {
                if($this->verifyresponse()){
                    if($this->request->post['approval_code'][0] ==  'Y' || $this->request->post['status'] == 'APPROVED') {
                        if (!$order_info['order_status_id']) { //  Missing order 
                            $this->model_checkout_order->confirm($order_id, $this->config->get('fiserv_order_status_id'));
                        }
                        $this->redirect($this->url->link('checkout/success', '', 'SSL'));
                    }
                    else {
                        // payment failed
                        $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
                    }
                }
                else {
                    // Tech error
                    $this->log->write('Finserve :: Callback : Message: '. print_r($this->request->post,true));
                    /*echo "There was an error processing your subscription.  
                      To try again please <a href='".HTTPS_SERVER."'><b><u>click here</u></b></a><br><BR>
                      Please contact our customer care department at <a href='mailto:".$this->config->get('config_email')."'><b><u>".$this->config->get('config_email')."</u></b></a>";
                    die(); */
                    $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
                }

            }
            else {
                // No order to process
                $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            }
            
			
	}
    private function verifyresponse(){
        $stringToHash = $this->config->get('fiserv_secret') . $this->request->post['approval_code'] . $this->request->post['chargetotal'] . $this->request->post['currency']  . $this->request->post['txndatetime']  . $this->config->get('fiserv_storeid'); 
        $ascii = bin2hex($stringToHash);
        if($this->request->post['response_hash'] && $this->request->post['response_hash'] ==  hash("sha256", $ascii)){
            return true;
        }
        else {
            return false;
        } 

    }   
    private function createHash($chargetotal, $dateTime) {
        $storename      = $this->config->get('fiserv_storeid');
        $sharedSecret   = $this->config->get('fiserv_secret');
        $currency   = $this->config->get('fiserv_currency');
        $stringToHash   = $storename . $dateTime . $chargetotal . $currency . $sharedSecret;
        $ascii = bin2hex($stringToHash);
        return hash('sha256',$ascii);
    }
    public function callback() {
        $this->load->model('checkout/order');

        $this->log->write('______________________FInserv___CALLBACK________________');
        $this->log->write(print_r($this->request->post,true));

        if(isset($this->request->post['oid']) && isset($this->request->post['approval_code'])){
            $order_id = $this->request->post['oid'];
            if($this->request->post['approval_code'][0] ==  'Y' || $this->request->post['status'] == 'APPROVED' ){
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);
                if (!$order_info['order_status_id']) { //  Missing order 
                    $this->model_checkout_order->confirm($order_id, $this->config->get('fiserv_order_status_id'),'Payment successful',1);
                }
            }
            /* if($this->request->post['approval_code'][0] ==  'N' || $this->request->post['status'] == 'DECLINED'){
                $this->model_checkout_order->confirm($order_id, '10',$this->request->post['fail_reason']);
            }*/
        }
        echo "OK";
        die();
        
    }

}

?>