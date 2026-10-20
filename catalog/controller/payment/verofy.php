<?php
 
class ControllerPaymentVerofy extends Controller {
    private $log;
    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('verofy.log');
    }
    protected function index() {
            $this->language->load('payment/verofy');
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
            $this->data['cusomer_firstname'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
            $this->data['cusomer_lastname'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
           
           // $this->data['responseFailURL'] = $this->url->link('payment/verofy/fail');
          //  $this->data['responseSuccessURL'] = $this->url->link('payment/verofy/success');
           // $this->data['callbackSuccessURL'] = $this->url->link('payment/verofy/callbacksuccess');
          //  $this->data['callbackFailURL'] = $this->url->link('payment/verofy/callbackfail');
            
          //  $this->data['stylesheet'] =  HTTPS_SERVER .'catalog/view/theme/default/stylesheet/verofy/verofy_pay.css';

           $this->data['verofy_action'] ='https://gateway.verofy.com';
           

      //    $this->data['verofy_action'] ='https://test-gateway.verofy.com';
            
            $hashcode_input = [];
            $hashcode_input['ekashu_3d_secure_verify'] = null;
            $hashcode_input['ekashu_amount'] = $amount;
            $hashcode_input['ekashu_amount_format'] = null;
            $hashcode_input['ekashu_auto_confirm'] = "true";
            $hashcode_input['ekashu_callback_failure_url'] = $this->url->link('payment/verofy/callbackfail','', 'SSL');
            $hashcode_input['ekashu_callback_include_post'] = null;
            $hashcode_input['ekashu_callback_success_url'] = $this->url->link('payment/verofy/callbacksuccess','', 'SSL');
            $hashcode_input['ekashu_card_address_editable'] = null;
            $hashcode_input['ekashu_card_address_required'] = "false";
            $hashcode_input['ekashu_card_address_verify'] = "check";
            $hashcode_input['ekashu_card_email_address_mandatory'] = "false";
            $hashcode_input['ekashu_card_phone_number_mandatory'] = null;
            $hashcode_input['ekashu_card_title_mandatory'] = "false";
            $hashcode_input['ekashu_card_zip_code_verify'] = "check";
            $hashcode_input['ekashu_currency'] = $order_info['currency_code'];
            $hashcode_input['ekashu_delivery_address_editable'] = null;
            $hashcode_input['ekashu_delivery_address_required'] = null;
            $hashcode_input['ekashu_delivery_email_address_mandatory'] = null;
            $hashcode_input['ekashu_delivery_phone_number_mandatory'] = null;
            $hashcode_input['ekashu_delivery_title_mandatory'] = null;
            $hashcode_input['ekashu_description'] = null;
            $hashcode_input['ekashu_device'] = null;
            $hashcode_input['ekashu_duplicate_check'] = "error";
            $hashcode_input['ekashu_duplicate_minutes'] = null;
            $hashcode_input['ekashu_failure_return_text'] = null;
            $hashcode_input['ekashu_failure_url'] =  null; // $this->url->link('payment/verofy/fail','', 'SSL');
            $hashcode_input['ekashu_hash_code_format'] = null;
            $hashcode_input['ekashu_hash_code_type'] = 'SHA256HMAC';
            $hashcode_input['ekashu_hash_code_version'] = '2.0.0';
            $hashcode_input['ekashu_include_post'] = null;
            $hashcode_input['ekashu_invoice_address_editable'] = null;
            $hashcode_input['ekashu_invoice_address_required'] = null;
            $hashcode_input['ekashu_invoice_email_address_mandatory'] = null;
            $hashcode_input['ekashu_invoice_phone_number_mandatory'] = null;
            $hashcode_input['ekashu_invoice_title_mandatory'] = null;
            $hashcode_input['ekashu_locale'] = null;
            $hashcode_input['ekashu_payment_methods'] = null;
            $hashcode_input['ekashu_reference'] = $order_id;
            $hashcode_input['ekashu_request_type'] = null;
            $hashcode_input['ekashu_return_text'] = null;
            $hashcode_input['ekashu_seller_address'] = null;
            $hashcode_input['ekashu_seller_email_address'] = null;
            $hashcode_input['ekashu_seller_id'] = $this->config->get('verofy_storeid');
            $hashcode_input['ekashu_seller_key'] = $this->config->get('verofy_sellerkey');
            $hashcode_input['ekashu_seller_name'] = null;
            $hashcode_input['ekashu_shortcut_icon'] = null;
            $hashcode_input['ekashu_style_sheet'] =   HTTPS_SERVER .'catalog/view/theme/default/stylesheet/verofy/verofy_pay.css';
            $hashcode_input['ekashu_success_url'] = $this->url->link('payment/verofy/success','', 'SSL');
            $hashcode_input['ekashu_title'] = null;
            $hashcode_input['ekashu_verification_value_mask'] = null;
            $hashcode_input['ekashu_verification_value_verify'] = null;
            $hashcode_input['ekashu_viewport'] = "device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no";

           // ksort($hashcode_input);
            
          //  $this->data['verofy_storeid'] =$this->config->get('verofy_storeid');
           // $this->data['verofy_sellerkey'] =$this->config->get('verofy_sellerkey');
             
           // $this->data['order_id'] = $order_id;
           // $this->data['amount'] = $amount;
         //   $this->data['curr'] = $order_info['currency_code'];
            $this->data['ekashu_return_url'] = $this->url->link('payment/verofy/fail','', 'SSL');
            $this->data['hashcode_input'] = $hashcode_input;
            if( $this->config->get('verofy_secret')) {
                $this->data['hash'] = $this->createHash($hashcode_input,$order_id);
            }
		
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/verofy.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/payment/verofy.tpl';
            } else {
                    $this->template = 'default/template/payment/verofy.tpl';
            }	
            $this->render();
	}

    public function fail() {
       $this->log->write('Verofy ::  fail: '. print_r($this->request->post,true));
        $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));

    }
        
    public function success() {
       $this->log->write('Verofy : 1 success: '.  print_r($this->request->post,true));
        $ekashu_auth_result = isset($this->request->post['ekashu_auth_result']) ? $this->request->post['ekashu_auth_result'] : '';
        $ekashu_auth_code = isset($this->request->post['ekashu_auth_code']) ? $this->request->post['ekashu_auth_code'] : '';
        $order_id = isset($this->request->post['ekashu_reference']) ? $this->request->post['ekashu_reference'] : '';
        $ekashu_transaction_id = isset($this->request->post['ekashu_transaction_id']) ? $this->request->post['ekashu_transaction_id'] : '';
        if (empty($ekashu_auth_result) || empty($order_id) || empty($ekashu_transaction_id) || empty($ekashu_auth_code) ) {
            $this->log->write('Verofy ::  success: '.  print_r($this->request->post,true));
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
        }
        else {
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            if ($order_info) {
                if (!$order_info['order_status_id']) { //  Missing order 
                    $this->model_checkout_order->confirm($order_id, $this->config->get('verofy_order_status_id'));
                }
                $this->redirect($this->url->link('checkout/success', '', 'SSL'));
            }
            else {
               $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
                
            }	
	}
    }
    
    private function createHash($hashcode_input) {
        /*$storename      = $this->config->get('verofy_storeid');
        $sharedSecret   = $this->config->get('verofy_secret');
        return sha1(base64_encode($sharedSecret . $storename . $order_id . $chargetotal)) ; */
        $sharedSecret   = $this->config->get('verofy_secret');
        ksort($hashcode_input);

        $hash_code = null;

        foreach ($hashcode_input as $name => $value)
        {
          $hash_code .= $value;
          $hash_code .= '&';
        }

        $hash_code = substr($hash_code, 0, -1);


        return base64_encode(hash_hmac('sha256', $hash_code, $sharedSecret, true));
 
    }
    public function callbacksuccess() {
        $this->log->write('Verofy : CALLBACK 1  success: '.  print_r($this->request->post,true));
        $ekashu_auth_result = isset($this->request->post['ekashu_auth_result']) ? $this->request->post['ekashu_auth_result'] : '';
        $ekashu_auth_code = isset($this->request->post['ekashu_auth_code']) ? $this->request->post['ekashu_auth_code'] : '';
        $order_id = isset($this->request->post['ekashu_reference']) ? $this->request->post['ekashu_reference'] : '';
        $ekashu_transaction_id = isset($this->request->post['ekashu_transaction_id']) ? $this->request->post['ekashu_transaction_id'] : '';
        if (empty($ekashu_auth_result) || empty($order_id) || empty($ekashu_transaction_id) || empty($ekashu_auth_code) ) {
            $this->log->write('______________________Verofy___CALLBACK________________');
            $this->log->write(print_r($this->request->post,true));
            echo "NO";
            die();
        }
        else {
            $this->load->model('checkout/order');
          //  $this->log->write('______________________Verofy___CALLBACK________________');
          //  $this->log->write(print_r($this->request->post,true));
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            if (!$order_info['order_status_id']) { //  Missing order 
                $this->model_checkout_order->confirm($order_id, $this->config->get('verofy_order_status_id'),'Payment successful',1);
            }
           
        }
        echo "OK";
        die();
        
    }
    public function callbackfail() {
        $this->log->write('______________________Verofy___CALLBACK___FAIL_____________');
        $this->log->write(print_r($this->request->post,true));

        echo "OK";
        die();
        
    }

}

?>