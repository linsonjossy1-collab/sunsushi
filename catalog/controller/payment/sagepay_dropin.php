<?php
class ControllerPaymentSagepayDropin extends Controller {
    private $log;
    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('sagepay.log');
    }
    public function index() {
        
        $this->language->load('payment/sagepay_dropin');
        
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'],$order_info['currency_code'] ,'', true); 
        $this->data['button_confirm'] = 'Pay '.$amount;
        $this->data['button_back'] = $this->language->get('button_back');
        
        $this->data['entry_firstname'] = $this->language->get('entry_firstname');
        $this->data['entry_lastname'] = $this->language->get('entry_lastname');
        $this->data['entry_address'] = $this->language->get('entry_address');
        $this->data['entry_city'] = $this->language->get('entry_city');
        $this->data['entry_postcode'] = $this->language->get('entry_postcode');

        if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
            $this->load->model('account/address');
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $shipping_address = $this->session->data['guest']['shipping'];
        } else {
            $shipping_address =[];
        }
        $this->data['shipping_address'] = $shipping_address;
       
        $this->data['order_id'] = $this->session->data['order_id'];
        
        $checkoutid = $this->server_checkout($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false),$this->session->data['order_id']);
        if($checkoutid) {
            $this->data['checkoutid'] = $checkoutid;
        }
        else {
             $this->data['backclick'] = 1;
        }
        
        $this->data['action'] = $this->url->link('payment/sagepay_dropin/send', '', 'SSL');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/sagepay_dropin.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/sagepay_dropin.tpl';
        } else {
                $this->template = 'default/template/payment/sagepay_dropin.tpl';
        }	
        $this->render();	
	
    }
    public function send() {
       // $this->log->write(print_r($this->request->post, true));
        if (!isset($this->request->post['merchantSessionKey']) ||  !isset($this->request->post['card-identifier'])) {
            $this->log->write('Invalid card data : ' . print_r($this->request->post, true));
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            return;
        }
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->request->post['order_id']);
       
        try {
        $paymentsucsess = false;
        if($this->config->get('sagepay_dropin_test')=='live'){
            $url = "https://pi-live.sagepay.com/api/v1/transactions";
        }
        else {
            $url = "https://pi-test.sagepay.com/api/v1/transactions";
        }
        $amount = (int)($this->currency->format($order_info['total'],$order_info['currency_code'],$order_info['currency_value'],false) * 100);
            $data = '{
     "transactionType": "Payment",
     "paymentMethod": {
         "card": {
             "merchantSessionKey": "'. $this->request->post['merchantSessionKey'] .'",
             "cardIdentifier": "'.$this->request->post['card-identifier'].'"
         }
     },
     "vendorTxCode": "demotransaction-'.$this->request->post['order_id'].'",
     "amount": '.$amount.',
     "currency": "GBP",
     "description": "'.$this->config->get('config_name') . ' - '.$this->request->post['order_id'] .'",
     "apply3DSecure": "UseMSPSetting",
     "customerFirstName": "'.$this->request->post['firstname'].'",
     "customerLastName": "'.$this->request->post['lastname'].'",
     "billingAddress": {
         "address1": "'.$this->request->post['address1'].'",
         "city": "'.$this->request->post['city'].'",
         "postalCode": "'.$this->request->post['postalCode'].'",
         "country": "GB"
     },
     "entryMethod": "Ecommerce"
    }';
           //  $this->log->write('CURL DATA: ' . print_r($data,true));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       'Authorization: Basic '. base64_encode($this->config->get('sagepay_dropin_integration_key').':'.$this->config->get('sagepay_dropin_integration_password')),
                       'Cache-Control: no-cache',
                       'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            // $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if(curl_errno($ch)) {
                $this->log->write('CURL Pay Error: ' . curl_error($ch));
                $paymentsucsess = false;
            }
            curl_close($ch);
          //  $this->log->write('CURL Pay Response: ' . print_r($responseData,true));
            $responsejson = json_decode($responseData,true);
           // if($http_status==201) {
                
                if(isset($responsejson['status']) ) {
                    if($responsejson['status']=='Ok') {
                        $paymentsucsess = true;
                        $this->log->write('Response sucsess: ' . print_r($responsejson,true));
                        $this->model_checkout_order->confirm($this->request->post['order_id'], $this->config->get('sagepay_dropin_order_status_id'));
                        $this->redirect($this->url->link('checkout/success', '', 'SSL'));
                        return ;
                    }
                    elseif($responsejson['status']=='3DAuth' && isset($responsejson['creq'])) {
                        $this->session->data['acsUrl']=$responsejson['acsUrl'];
                        $this->session->data['creq']=$responsejson['cReq'];
                        $this->session->data['threeDSSessionData']=$responsejson['transactionId'];
                        $this->redirect($this->url->link('payment/sagepay_dropin/threediframe', '', 'SSL'));
                        return;
                    }
                    elseif($responsejson['status']=='3DAuth' && isset($responsejson['paReq'])) {
                        $this->session->data['acsUrl']=$responsejson['acsUrl'];
                        $this->session->data['paReq']=$responsejson['paReq'];
                        $this->session->data['order_id']=$this->request->post['order_id'];
                        $this->session->data['transactionId']= $responsejson['transactionId'];
                        $this->redirect($this->url->link('payment/sagepay_dropin/threedredirect', '', 'SSL'));
                        return;
                    }
                }
                else {
                    $this->log->write('Response Error: ' . print_r($responsejson,true));
                    $paymentsucsess = false;
                }
            /*}
            else {
                $this->log->write('Response communication Error: ' . $http_status . ' =>' . print_r($responseData,true));
                $paymentsucsess = false;
                $this->session->data['sagepay_error'] = 'Server communication error';
            }
             * 
             */
          
        
        } catch (Exception $e) {
           // echo 'Error message: '. $e->getMessage();
            $this->log->write(' Errors ! : '. print_r($this->request->post, true) . 'Error message: '. $e->getMessage());
            //$this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            $this->session->data['sagepay_error']= $e->getMessage();
            $paymentsucsess = false;
            
           
        }
        if(!$paymentsucsess) { // payment sucsess
            $comment ='';
            if(isset($responsejson)) {
                foreach($responsejson as $key=>$val) {
                    if(!is_array($val)) {
                        $comment .= $key .': '.$val ."\n";
                    }
                }
            }
            else {
                $comment = $this->session->data['sagepay_error'];
            }
            $this->model_checkout_order->confirm($this->request->post['order_id'], $this->config->get('sagepay_dropin_failed_order_status_id'),$comment,false);
            $this->redirect($this->url->link('payment/sagepay_dropin/failure', '', 'SSL'));
        }

    }

    public function webhook(){
        http_response_code(200);
        echo "OK";
        exit();
    }
    public function threediframe() {
        $this->language->load('payment/sagepay_dropin');
        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] 			 = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language']  		 = $this->language->get('code');
        $this->data['direction'] 		 = $this->language->get('direction');
        //Page titles
        $this->data['title'] 			 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] 	 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['acsUrl'] = $this->session->data['acsUrl'];
        $this->data['creq'] = $this->session->data['creq'];
        $this->data['threeDSSessionData'] = $this->session->data['threeDSSessionData'];
        $this->data['action'] = $this->url->link('payment/sagepay_dropin/threediframeresponse','','SSL');
        $this->template = $this->config->get('config_template') . '/template/payment/sagepay_dropin_threediframe.tpl';
        $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
        );
        unset($this->session->data['sagepay_error']);
        $this->response->setOutput($this->render());
    }
    public function threediframeresponse() {
        $this->log->write(print_r($this->request->post, true));
    }
    public function threedredirect() {
        $this->language->load('payment/sagepay_dropin');
        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] 			 = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language']  		 = $this->language->get('code');
        $this->data['direction'] 		 = $this->language->get('direction');
        //Page titles
        $this->data['title'] 			 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] 	 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['acsUrl'] = $this->session->data['acsUrl'];
        $this->data['paReq'] = $this->session->data['paReq'];
        $this->data['md'] = $this->session->data['transactionId'];
        $this->data['action'] = $this->url->link('payment/sagepay_dropin/threedredirectresponse','order_id='.$this->session->data['order_id'],'SSL');
        $this->template = $this->config->get('config_template') . '/template/payment/sagepay_dropin_threedredirect.tpl';
        $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
        );
        unset($this->session->data['sagepay_error']);
        $this->response->setOutput($this->render());
    }
    public function threedredirectresponse() {
        $this->log->write(print_r($this->request->get, true));
        $this->log->write(print_r($this->request->post, true));
        
        try {
            $this->load->model('checkout/order');
            $transactionId = $this->request->post['MD'];
            if($this->config->get('sagepay_dropin_test')=='live'){
                $url = "https://pi-live.sagepay.com/api/v1/transactions/$transactionId/3d-secure";
            }
            else {
                $url = "https://pi-test.sagepay.com/api/v1/transactions/$transactionId/3d-secure";
            }
       
            $data = '{"paRes": "'.$this->request->post['PaRes'].'"}';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       'Authorization: Basic '. base64_encode($this->config->get('sagepay_dropin_integration_key').':'.$this->config->get('sagepay_dropin_integration_password')),
                       'Cache-Control: no-cache',
                       'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response3d = curl_exec($ch);
            if(curl_errno($ch)) {
                $this->log->write('CURL Pay Error: ' . curl_error($ch));
                return ;
            }
            curl_close($ch);
            $response3djson = json_decode($response3d,true);
            if(isset($response3djson['status']) && $response3djson['status']=='Authenticated' ) {
                if($this->config->get('sagepay_dropin_test')=='live'){
                    $url = "https://pi-live.sagepay.com/api/v1/transactions/$transactionId";
                }
                else {
                    $url = "https://pi-test.sagepay.com/api/v1/transactions/$transactionId";
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                           'Authorization: Basic '. base64_encode($this->config->get('sagepay_dropin_integration_key').':'.$this->config->get('sagepay_dropin_integration_password')),
                           'Cache-Control: no-cache',
                           'Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $responseData = curl_exec($ch);
                // $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if(curl_errno($ch)) {
                    $this->log->write('CURL Pay Error: ' . curl_error($ch));
                }
                curl_close($ch);
                $responsejson = json_decode($responseData,true);
               
                if(isset($responsejson['status']) ) {
                    if($responsejson['status']=='Ok') {
                        $this->log->write('Response sucsess: ' . print_r($responsejson,true));
                        $this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('sagepay_dropin_order_status_id'));
                        $this->redirect($this->url->link('checkout/success', '', 'SSL'));
                        return ;
                    }
                }
                else {
                    $this->log->write('Response Error: ' . print_r($responsejson,true));
                }
               
                $comment ='';
                if(isset($responsejson)) {
                    foreach($responsejson as $key=>$val) {
                        if(!is_array($val)) {
                            $comment .= $key .': '.$val ."\n";
                        }
                    }
                }
                else {
                    $comment = $this->session->data['sagepay_error'];
                }
                $this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('sagepay_dropin_failed_order_status_id'),$comment,false);
                $this->redirect($this->url->link('payment/sagepay_dropin/failure', '', 'SSL'));
                
            }
            else {
                $this->log->write('3D Response Error: ' . print_r($response3djson,true));
                $this->session->data['sagepay_error']= "3-D Secure authentication  Failed";
                $this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('sagepay_dropin_failed_order_status_id'),'3-D Secure authentication  Failed',false);
                $this->redirect($this->url->link('payment/sagepay_dropin/failure', '', 'SSL'));
                   
            }
        
        }catch (Exception $e) {
            $this->session->data['sagepay_error']= "3-D Secure sagepay communication error";
            $this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('sagepay_dropin_failed_order_status_id'),'3-D Secure sagepay communication error',false);
            $this->redirect($this->url->link('payment/sagepay_dropin/failure', '', 'SSL'));
        }
        
    }
    public function failure() {
        $this->language->load('payment/sagepay_dropin');
        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] 			 = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language']  		 = $this->language->get('code');
        $this->data['direction'] 		 = $this->language->get('direction');
        //Page titles
        $this->data['title'] 			 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] 	 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['text_response'] 	 = $this->language->get('text_response');
        $this->data['error_response'] 	 = isset($this->session->data['sagepay_error'])?$this->session->data['sagepay_error']:'';
        //Error text
        $this->data['text_failure']      = $this->language->get('text_failure');
        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkoutetm'));
        //Mismatch
        $this->data['continue'] = $this->url->link('checkout/checkoutetm');
        $this->template = $this->config->get('config_template') . '/template/payment/sagepay_dropin_failure.tpl';
        $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
        );
        unset($this->session->data['sagepay_error']);
        $this->response->setOutput($this->render());
    }
     private function server_checkout() {
        if($this->config->get('sagepay_dropin_test')=='live'){
            $url = "https://pi-live.sagepay.com/api/v1/merchant-session-keys";
            $data = '{"vendorName": "VENDOR_NAME"}' ;
        }
        else {
            $url = "https://pi-test.sagepay.com/api/v1/merchant-session-keys";
            $data = '{"vendorName": "sandbox"}' ;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization: Basic '. base64_encode($this->config->get('sagepay_dropin_integration_key').':'.$this->config->get('sagepay_dropin_integration_password')),
                   'Cache-Control: no-cache',
                   'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)) {
            $this->log->write('CURL Error: ' . curl_error($ch));
            return false;
        }
        curl_close($ch);
        if($http_status==201) {
            $responsejson = json_decode($responseData,true);
            if($responsejson['merchantSessionKey']) {
                return $responsejson['merchantSessionKey'];
            }
            else {
                $this->checkout_error = ' 129: sagepay communication Error';
                $this->log->write('CURL Response: ' . print_r($responsejson,true));
                return false;
            }
        }
        else {
            $responsejson = json_decode($responseData,true);
            $this->log->write('CURL Status: ' . $http_status . ' =>' . print_r($responseData,true));
            $this->checkout_error = ' 136: sagepay communication Error';
            return false;
        }
    }
    
}
?>