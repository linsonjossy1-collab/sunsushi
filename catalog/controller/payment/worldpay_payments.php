<?php
require_once( DIR_SYSTEM .'vendor/worldpay-lib-php/init.php');
use Worldpay\Worldpay;
use Worldpay\WorldpayException;

class ControllerPaymentWorldpayPayments extends Controller {
    private $log;
    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('worldpay.log');
    }
    public function index() {
        
        $this->language->load('payment/worldpay_payments');
        
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'],$order_info['currency_code'] ,'', true); 
        $this->data['button_confirm'] = 'Pay '.$amount;
        $this->data['button_back'] = $this->language->get('button_back');
        $this->data['worldpay_client_key'] = $this->config->get('worldpay_payments_client_key');
       
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
        $this->data['worldpay_billing_address'] = $this->config->get('worldpay_payments_billing_address');
       
        $this->data['order_id'] = $this->session->data['order_id'];
        $this->data['action'] = $this->url->link('payment/worldpay_payments/send', '', 'SSL');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/worldpay_payments.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/worldpay_payments.tpl';
        } else {
                $this->template = 'default/template/payment/worldpay_payments.tpl';
        }	
        $this->render();	
	
    }
    public function send() {
        $wordpaytoken = isset($this->request->post['token']) ? $this->request->post['token'] : null;
        if (is_null($wordpaytoken)) {
            $this->log->write('Invalid card data : ' . print_r($this->request->post, true));
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            return;
        }
      
        $worldpay = new Worldpay($this->config->get('worldpay_payments_service_key'));
        
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->request->post['order_id']);
        if(isset($this->request->post['postalCode'])) {
            $billing_address = array(
                "address1"=>$this->request->post['address1'],
             //   "address2"=> 'A village',
             //   "address3"=> '',
                "postalCode"=> $this->request->post['postalCode'],
                "city"=> $this->request->post['city'],
            //    "state"=> '',
                "countryCode"=> 'GB',
            );
        }
        else {
            $billing_address = array(); 
        }
        try {
            $response = $worldpay->createOrder(array(
                'token' => $wordpaytoken,
                'amount' => (int) ($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) * 100),
                'currencyCode' => 'GBP',
                'name' => $order_info['payment_firstname'] . ' '.$order_info['payment_lastname'],
                'billingAddress' => $billing_address,
                'orderDescription' => $order_info['store_name'] . ' #'.$this->request->post['order_id'],
                'customerOrderCode' => $this->request->post['order_id'],
                'shopperEmailAddress'=> $order_info['email'],
                'shopperIpAddress' => $order_info['ip'],
            ));
            if ($response['paymentStatus'] === 'SUCCESS') {
               // $worldpayOrderCode = $response['orderCode'];
                $this->log->write('Sucsess : ' . print_r($response, true));
                $this->model_checkout_order->confirm($this->request->post['order_id'], $this->config->get('worldpay_payments_order_status_id'));
                $this->redirect($this->url->link('checkout/success', '', 'SSL'));
                return ;
            } else {
                // throw new WorldpayException(print_r($response, true));
                $this->log->write('WorldpayException Errors ! : '. print_r($this->request->post, true) . 'Error message: '.print_r($response, true));
                //$this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
                $this->session->data['wolrdpay_error'] = $response['paymentStatusReason'];
                $this->redirect($this->url->link('payment/worldpay_payments/failure', '', 'SSL'));
                return ;
            }
        } catch (WorldpayException $e) {
            $error =  'Error code: ' .$e->getCustomCode() .'
            HTTP status code:' . $e->getHttpStatusCode() . '
            Error description: ' . $e->getDescription()  . '
            Error message: ' . $e->getMessage();
            $this->log->write('WorldpayException Errors ! : '. print_r($this->request->post, true) . 'Error message: '.$error);
            //$this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            $this->session->data['wolrdpay_error'] = $e->getDescription();
            $this->redirect($this->url->link('payment/worldpay_payments/failure', '', 'SSL'));
            return ;
        } catch (Exception $e) {
           // echo 'Error message: '. $e->getMessage();
            $this->log->write(' Errors ! : '. print_r($this->request->post, true) . 'Error message: '. $e->getMessage());
            //$this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            $this->session->data['wolrdpay_error']= $e->getMessage();
            $this->redirect($this->url->link('payment/worldpay_payments/failure', '', 'SSL'));
            return;
        }

    }

    public function webhook(){
        http_response_code(200);
        echo "OK";
        exit();
    }
    public function failure() {
        $this->language->load('payment/worldpay_payments');
        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] 			 = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language']  		 = $this->language->get('code');
        $this->data['direction'] 		 = $this->language->get('direction');
        //Page titles
        $this->data['title'] 			 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] 	 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['text_response'] 	 = $this->language->get('text_response');
        $this->data['error_response'] 	 = isset($this->session->data['wolrdpay_error'])?$this->session->data['wolrdpay_error']:'';
        //Error text
        $this->data['text_failure']      = $this->language->get('text_failure');
        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkoutetm'));
        //Mismatch
        $this->data['continue'] = $this->url->link('checkout/checkoutetm');
        $this->template = $this->config->get('config_template') . '/template/payment/worldpay_payments_failure.tpl';
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
                
     
}
?>