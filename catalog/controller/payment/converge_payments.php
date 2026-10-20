<?php
require_once DIR_SYSTEM . '/vendor/elavon_converge/autoload.php';

use Elavon\Converge2\Converge2;
use Elavon\Converge2\Util\Encryption;
use Elavon\Converge2\Client\ClientConfig;
use Elavon\Converge2\Request\Payload\OrderDataBuilder;
use Elavon\Converge2\DataObject\TransactionType;
use Elavon\Converge2\DataObject\CurrencyCode;
use Elavon\Converge2\DataObject\TrueFalseOrDefault;
use Elavon\Converge2\Request\Payload\PaymentSessionDataBuilder;

class ControllerPaymentConvergePayments extends Controller {
    private $log;
    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('converge.log');
    }
    public function index() {
       
        $this->language->load('payment/converge_payments');
        
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'],$order_info['currency_code'] ,'', true); 
        $this->data['button_confirm'] = 'Pay '.$amount;
        $this->data['button_back'] = $this->language->get('button_back');
        $this->data['converge_client_key'] = $this->config->get('converge_payments_client_key');
       
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
        $this->data['converge_billing_address'] = $this->config->get('converge_payments_billing_address');
       
        $this->data['order_id'] = $this->session->data['order_id'];
        $this->data['action'] = $this->url->link('payment/converge_payments/send', '', 'SSL');
        
        try {
            $c2_config = new ClientConfig();

            $c2_config->setMerchantAlias($this->config->get('converge_payments_merchant_alias'));
            $c2_config->setPublicKey($this->config->get('converge_payments_public_key'));
            $c2_config->setSecretKey($this->config->get('converge_payments_secret_key'));
            
            if($this->config->get('converge_payments_mode')) {
                $c2_config->setSandboxMode();
                $this->data['hpp_js'] = 'https://uat.hpp.converge.eu.elavonaws.com/client/index.js';
                $this->data['hpp_url'] = 'https://uat.hpp.converge.eu.elavonaws.com';
            }else {
                $c2_config->setProductionMode();
                $this->data['hpp_js'] = 'https://hpp.eu.convergepay.com/client/index.js';
                $this->data['hpp_url'] = 'https://hpp.eu.convergepay.com'; 
            }
            $converge2 = new Converge2($c2_config);

          
            $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) ;
                    
            $order_builder = new OrderDataBuilder();
            $order_builder->setTotalAmountCurrencyCode($amount, CurrencyCode::GBP);
            $order_builder->setDescription( $this->config->get('config_name') . ' Order #'.$this->data['order_id']);
          //  $order_builder->setItems($items);
          //  $order_builder->setShipTo($contact_builder->getData());
            $order_builder->setCustomReference('order #'.$this->data['order_id'] );
            $order_builder->setShopperEmailAddress($order_info['email']);
            $order_builder->setShopperReference('CUS '.$this->data['order_id']);
            $order_builder->setOrderReference('#'.$this->data['order_id'] );
         //   $order_builder->setCustomFields(['one' => '1', 'two' => '2']);
           $payload_data = $order_builder->getData();
            
            $Order_response = $converge2->createOrder($payload_data);
          //  print_r($payload_data);
            
        
        
            $payment_session_builder = new PaymentSessionDataBuilder();

            $payment_session_builder->setOrder($Order_response->getId());
           // $payment_session_builder->setBillTo($contact_builder->getData());
            $payment_session_builder->setReturnUrl(htmlspecialchars_decode($this->url->link('payment/converge_payments/send', 'order_id='. $this->data['order_id'], 'SSL')));
            $payment_session_builder->setCancelUrl(htmlspecialchars_decode ($this->url->link('payment/converge_payments/failure', 'order_id='. $this->data['order_id'], 'SSL')));
            $payment_session_builder->setOriginUrl($this->url->link('checkout/checkoutetm', '', 'SSL'));
            $payment_session_builder->setDefaultLanguageTag('en-GB');
            $payment_session_builder->setCustomReference('reference');
           // $payment_session_builder->setCustomFields(['one' => '1', 'two' => '2']);
            $payment_session_builder->setDoCreateTransaction(true);
            $payload_data = $payment_session_builder->getData();
            $payment_session_response = $converge2->createPaymentSession($payload_data);
            
            
            $this->data['converge_sessionId'] = $payment_session_response->getId();
            $this->data['public_key'] = $this->config->get('converge_payments_public_key');
            $this->data['merchant_alias'] =$this->config->get('converge_payments_merchant_alias');
          //  print_r($converge2);
         //   $this->log->write(print_r($payment_session_response,true));
        
        } catch (Exception $e) {
          print_r($e);
           
        }
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/converge_payments.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/converge_payments.tpl';
        } else {
                $this->template = 'default/template/payment/converge_payments.tpl';
        }	
        $this->render();	
	
    }
    public function send() {
        $this->log->write('Success data : ' . print_r($this->request->get, true));
        // $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));

         $c2_config = new ClientConfig();

        $c2_config->setMerchantAlias($this->config->get('converge_payments_merchant_alias'));
        $c2_config->setPublicKey($this->config->get('converge_payments_public_key'));
        $c2_config->setSecretKey($this->config->get('converge_payments_secret_key'));

        if($this->config->get('converge_payments_mode')) {
            $c2_config->setSandboxMode();
          
        }else {
            $c2_config->setProductionMode();
        }
        $converge2 = new Converge2($c2_config);
        
        $payment_session = $converge2->getPaymentSession($this->request->get['sessionId']);
        $trans =  $payment_session->getTransaction();
        
        $this->log->write(' --  PAYMRNT SESSION -- ');
        $this->log->write(print_r($payment_session,true));
        $trans_response =  $payment_session->getTransaction();
        $this->log->write(' --  TRANS -- ');
        $this->log->write(print_r($trans_response,true));
        
        if($trans_response) {
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->request->get['order_id']);
       
            $this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('converge_payments_order_status_id'));
            $this->redirect($this->url->link('checkout/success', '', 'SSL'));
        }
        else {
          //   $this->redirect($this->url->link('payment/converge_payments/failure', '', 'SSL'));
        }
        

    }

    public function webhook(){
        http_response_code(200);
        echo "OK";
        exit();
    }
    public function failure() {
        $this->log->write('failure data : ' . print_r($this->request->get, true));

        $this->language->load('payment/converge_payments');
        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] 			 = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language']  		 = $this->language->get('code');
        $this->data['direction'] 		 = $this->language->get('direction');
        //Page titles
        $this->data['title'] 			 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] 	 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['text_response'] 	 = $this->language->get('text_response');
        $this->data['error_response'] 	 = '';
        //Error text
        $this->data['text_failure']      = $this->language->get('text_failure');
        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkoutetm'));
        //Mismatch
        $this->data['continue'] = $this->url->link('checkout/checkoutetm');
        $this->template = $this->config->get('config_template') . '/template/payment/converge_payments_failure.tpl';
        $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
        );

        $this->response->setOutput($this->render());
    }
                
     
}
?>