<?php
require_once( DIR_SYSTEM .'vendor/worldpay/php-sdk/autoload.php');

use Worldpay\Api\AccessWorldpay;
use Worldpay\Api\Enums\ShippingMethod;

use Worldpay\Api\Entities\Customer;
use Worldpay\Api\Entities\Order;
use Worldpay\Api\Enums\Environment;
use Worldpay\Api\Providers\AccessWorldpayConfigProvider;
use Worldpay\Api\ValueObjects\BillingAddress;
// use Worldpay\Api\ValueObjects\ShippingAddress;
use Worldpay\Api\ValueObjects\ResultURLs;

class ControllerPaymentWorldpayAccess extends Controller {
    private $log;
    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('worldpay-access.log');
    }
    public function index() {
        
        $this->language->load('payment/worldpay_access');
        
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'],$order_info['currency_code'] ,'', true); 
        $this->data['button_confirm'] = 'Pay '.$amount;
        $this->data['button_back'] = $this->language->get('button_back');
//        $this->data['worldpay_client_key'] = $this->config->get('worldpay_access_client_key');
//       
//        $this->data['entry_address'] = $this->language->get('entry_address');
//        $this->data['entry_city'] = $this->language->get('entry_city');
//        $this->data['entry_postcode'] = $this->language->get('entry_postcode');

        if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
            $this->load->model('account/address');
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $shipping_address = $this->session->data['guest']['shipping'];
        } else {
            $shipping_address =[];
        }
//        $this->data['shipping_address'] = $shipping_address;
//        $this->data['worldpay_billing_address'] = $this->config->get('worldpay_access_billing_address');
//       
        $this->data['order_id'] = $this->session->data['order_id'];
       // $this->data['action'] = $this->url->link('payment/worldpay_access/send', '', 'SSL');
        
        $apiConfigProvider = AccessWorldpayConfigProvider::instance();
        if($this->config->get('worldpay_access_trymode')) {
            $apiConfigProvider->environment = Environment::TRY_MODE;
        }else {
            $apiConfigProvider->environment = Environment::LIVE_MODE;
        }
        $apiConfigProvider->username = $this->config->get('worldpay_access_username');
        $apiConfigProvider->password = $this->config->get('worldpay_access_password');
        $apiConfigProvider->merchantEntity = $this->config->get('worldpay_access_merchantentity');
        $apiConfigProvider->merchantNarrative = 'Order '.$this->session->data['order_id'];
        
        
        $billingAddress = new BillingAddress();
        $billingAddress->address1 = preg_replace('/[^a-z\d ]/i', '',htmlspecialchars_decode($order_info['payment_address_1']));
        $billingAddress->address2 = preg_replace('/[^a-z\d ]/i', '',htmlspecialchars_decode($order_info['payment_address_2']));
       // $billingAddress->address3 = 'Westminster';
        $billingAddress->postalCode = $order_info['payment_postcode'];
        $billingAddress->city = preg_replace('/[^a-z\d ]/i', '',htmlspecialchars_decode($order_info['payment_city']));
       // $billingAddress->state = 'Greater London';
        $billingAddress->countryCode = 'GB';
        
        $customer = new Customer();
        $customer->firstName = $order_info['payment_firstname'];
        $customer->lastName = $order_info['payment_lastname'];
        $customer->email = $order_info['email'];
        $customer->phoneNumber = $order_info['telephone'];
        
        $resulturls = new ResultURLs();
        $resulturls->successURL = $this->url->link('payment/worldpay_access/success','order_id=' . $this->session->data['order_id'],'SSL');
        $resulturls->pendingURL = $this->url->link('payment/worldpay_access/pending','order_id=' . $this->session->data['order_id'],'SSL');
        $resulturls->failureURL= $this->url->link('payment/worldpay_access/failure','order_id=' . $this->session->data['order_id'],'SSL');
        $resulturls->errorURL= $this->url->link('payment/worldpay_access/error','order_id=' . $this->session->data['order_id'],'SSL');
        $resulturls->cancelURL= $this->url->link('payment/worldpay_access/cancel','order_id=' . $this->session->data['order_id'],'SSL');
        $resulturls->expiryURL= $this->url->link('payment/worldpay_access/expiry','order_id=' . $this->session->data['order_id'],'SSL');

        $transactionReference = $this->session->data['order_id'] . '-ETM-' . bin2hex(random_bytes(8));
        
        $amount = (int) strval($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) * 100);
        
      //  $this->log->write('resulturls : ' . print_r($resulturls,true));
        try {
            $api = AccessWorldpay::config($apiConfigProvider);

            $apiResponse = $api->HPPSetup($amount)
                ->withTransactionReference($transactionReference)
                ->withBillingAddress($billingAddress)
                ->withCustomer($customer)
                ->withResultURLs($resulturls)   
                ->execute();

            /**
             * Use the URL received in the response to redirect your customer to complete their payment.
             */
            if ($apiResponse->statusCode === 200) {
                $decodedApiResponse = json_decode($apiResponse->rawResponse);
                $this->data['worldpay_http_url']  = $decodedApiResponse->url;

            } else {
               // throw new \Exception($apiResponse->rawResponse);
                $this->log->write(print_r($apiResponse,true));
                $this->data['worldpay_err']  =  $this->language->get('text_tech_issue');
            }
        } catch (\Exception|\Error $e) {
//            echo '<pre>';
//            var_dump($e->getMessage());
//            echo '</pre>';
            $this->log->write(print_r($e->getMessage(),true));
            $this->data['worldpay_err'] =  $this->language->get('text_tech_issue');
        }
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/worldpay_access.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/worldpay_access.tpl';
        } else {
                $this->template = 'default/template/payment/worldpay_access.tpl';
        }	
        $this->render();	
	
    }
    public function success() {
        $this->log->write('success get : ' . print_r($this->request->get,true));
        $this->redirect($this->url->link('checkout/success', '', 'SSL'));
    }
    public function pending(){
        $this->log->write('pending : ' . print_r($this->request->post,true));
        $this->log->write('pending get : ' . print_r($this->request->get,true));
        $this->redirect($this->url->link($this->url->link('checkout/checkoutetm','order_id=' . $this->request->get['order_id'],'SSL')));
    }
    
    
    public function failure() {
        $this->log->write('failure get : ' . print_r($this->request->get,true));

        $this->language->load('payment/worldpay_access');
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
        $this->template = $this->config->get('config_template') . '/template/payment/worldpay_access_failure.tpl';
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
    
    public function error(){
        $this->log->write('error  get : ' . print_r($this->request->get,true));
        $this->redirect($this->url->link($this->url->link('payment/worldpay_access/failure','order_id=' . $this->request->get['order_id'],'SSL')));
    }
    public function cancel() {
        $this->log->write('cancel get : ' . print_r($this->request->get,true));
        $this->redirect($this->url->link($this->url->link('checkout/checkoutetm')));
    }
    public function expiry() {
        $this->log->write('expiry get : ' . print_r($this->request->get,true));
        $this->redirect($this->url->link($this->url->link('payment/worldpay_access/failure','order_id=' . $this->request->get['order_id'],'SSL')));
    }
    
    public function webhook(){
        $this->log->write('webhook : ' . print_r($this->request->post,true));
        $this->log->write('webhook get : ' . print_r($this->request->get,true));
        
        $payload = @file_get_contents('php://input');
        $this->log->write('webhook payload : ' . print_r($payload,true));
        
        $event = json_decode($payload,true); 
        if(isset($event['eventDetails']['transactionReference'])){
            $order_id = strtok($event['eventDetails']['transactionReference'],'-ETM-');
            if($order_id && $event['eventDetails']['type']=='authorized' ) {   // sentForSettlement
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);
                if (!$order_info['order_status_id']) { // Missing order
                     $this->model_checkout_order->confirm($order_id, $this->config->get('worldpay_access_order_status_id'));
                }
            }
        }
        http_response_code(200);
        echo "OK";
        exit();
    }
                
     
}
?>