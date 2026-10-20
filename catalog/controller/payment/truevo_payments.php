<?php
class ControllerPaymentTruevoPayments extends Controller {
    private $log;
    private $checkout_error;
    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('truevo.log');
    }
    public function index() {
        
        $this->language->load('payment/truevo_payments');
        
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'],$order_info['currency_code'] ,'', true); 
        $this->data['button_confirm'] = 'Pay '.$amount;
        $this->data['button_back'] = $this->language->get('button_back');
        $this->checkout_error='';
        $checkoutid = $this->server_checkout($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false),$this->session->data['order_id']);
        if($checkoutid) {
            $this->data['checkoutid'] = $checkoutid;
        }
        else {
             $this->data['backclick'] = 1;
        }
        $this->data['error_description'] = $this->checkout_error;
        $this->data['action'] = $this->url->link('payment/truevo_payments/send', 'order_id='.$this->session->data['order_id'], 'SSL');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/truevo_payments.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/truevo_payments.tpl';
        } else {
                $this->template = 'default/template/payment/truevo_payments.tpl';
        }	
        $this->render();	
	
    }
    public function send() {
        $resourcePath = isset($this->request->get['resourcePath']) ? $this->request->get['resourcePath'] : null;
        if (is_null($resourcePath)) {
            $this->log->write('Invalid card data : ' . print_r($this->request->get, true));
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            return;
        }
        $response = $this->server_payment($resourcePath);
        
        if ($response['sucsess'] === true) {
            $this->load->model('checkout/order');
            $this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('truevo_payments_order_status_id'));
            $this->redirect($this->url->link('checkout/success', '', 'SSL'));
            return ;
        } else {
            $this->log->write('TruevoException Errors ! : '. print_r($this->request->post, true) . 'Error message: '.print_r($response, true));
            $this->session->data['truevo_error'] = $response['description'];
            $this->redirect($this->url->link('payment/truevo_payments/failure', '', 'SSL'));
            return ;
        }
        

    }

    public function webhook(){
        http_response_code(200);
        echo "OK";
        exit();
    }
    public function failure() {
        $this->language->load('payment/truevo_payments');
        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] 			 = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language']  		 = $this->language->get('code');
        $this->data['direction'] 		 = $this->language->get('direction');
        //Page titles
        $this->data['title'] 			 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] 	 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['text_response'] 	 = $this->language->get('text_response');
        $this->data['error_response'] 	 = isset($this->session->data['truevo_error'])?$this->session->data['truevo_error']:'';
        //Error text
        $this->data['text_failure']      = $this->language->get('text_failure');
        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkoutetm'));
        //Mismatch
        $this->data['continue'] = $this->url->link('checkout/checkoutetm');
        $this->template = $this->config->get('config_template') . '/template/payment/truevo_payments_failure.tpl';
        $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
        );
        unset($this->session->data['wolrdpay_error']);
        $this->response->setOutput($this->render());
    }
    private function server_checkout($amount,$order_id) {
        if($this->config->get('truevo_payments_live_demo')){
            $url = "https://truevo.eu/v1/checkouts";
        }
        else {
            $url = "https://test.truevo.eu/v1/checkouts";
        }
        $data = "entityId=". $this->config->get('truevo_payments_entityId') .
                "&amount=$amount" . //92.00
                "&currency=GBP" .
                "&paymentType=DB" .
                "&descriptor=" . $this->config->get('config_name') . ' - '.$order_id ;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer '. $this->config->get('truevo_payments_access_token') ));
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
        if($http_status==200) {
            $responsejson = json_decode($responseData,true);
            if($responsejson['id']) {
                return $responsejson['id'];
            }
            else {
                $this->checkout_error = isset($responsejson['result']['description'])?$responsejson['result']['description']:' 129: Truevo communication Error';
                $this->log->write('CURL Response: ' . print_r($responsejson,true));
                return false;
            }
        }
        else {
            $responsejson = json_decode($responseData,true);
            $this->log->write('CURL Status: ' . $http_status . ' =>' . print_r($responseData,true));
            $this->checkout_error = isset($responsejson['result']['description'])?$responsejson['result']['description']:' 136: Truevo communication Error';
            return false;
        }
    }
    private function server_payment($resourcePath) {
        $result = array();
        if($this->config->get('truevo_payments_live_demo')){
            $url = "https://truevo.eu/" . $resourcePath; // v1/checkouts/$id/payment";
        }
        else {
            $url = "https://test.truevo.eu/". $resourcePath ; //v1/checkouts/$id/payment";
        }
        $url .= "?entityId=". $this->config->get('truevo_payments_entityId');
        $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer ' . $this->config->get('truevo_payments_access_token')));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$responseData = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)) {
          //  $this->log->write('CURL Pay Error: ' . curl_error($ch));
            $result['sucsess'] = false ;
            $result['description'] = curl_error($ch);
            return $result;
        }
        curl_close($ch);
       // $this->log->write('CURL Pay Response: ' . print_r($responseData,true));
        
        if($http_status==200) {
            $responsejson = json_decode($responseData,true);
            if($responsejson['result']['code']) {
                $pattern_sucsess = '/^(000\.000\.|000\.100\.1|000\.[36])/';
                if(preg_match($pattern_sucsess, $responsejson['result']['code'])==1) {
                    $result['sucsess'] = true ;
                    $result['description'] = $responsejson['result']['description'];
                }
                else {
                    $result['sucsess'] = false ;
                    $result['description'] = $responsejson['result']['description'];
                }
                $this->log->write(' Response: ' . print_r($responsejson,true));
            }
            else {
                $this->log->write('Response Error: ' . print_r($responsejson,true));
                $result['sucsess'] = false ;
                $result['description'] = 'Techinical Error';
            }
        }
        else {
            $this->log->write('Response communication Error: ' . $http_status . ' =>' . print_r($responseData,true));
            $result['sucsess'] = false ;
            $result['description'] = 'Server communication error';
        }
        return $result;
    }           
     
}
?>