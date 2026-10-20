<?php
require_once( DIR_SYSTEM . 'vendor/sumup/vendor/autoload.php');
use SumUp\SumUp;
use SumUp\Exceptions\SumUpAuthenticationException;
use SumUp\Exceptions\SumUpResponseException;
use SumUp\Exceptions\SumUpSDKException;


class ControllerPaymentSumupPayments extends Controller {

    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('sumup.log');
    }

    public function index() {
        $this->language->load('payment/sumup_payments');

       
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], '', true);
        $amounttosup = $this->currency->format($order_info['total'],$order_info['currency_code'],$order_info['currency_value'],false);
        try {

            $sumup = new SumUp([
                'app_id'     => $this->config->get('sumup_payments_app_id'),
                'app_secret' => $this->config->get('sumup_payments_client_secret'),
                'grant_type' => 'client_credentials',
          //      'scopes'      => ['payments', 'transactions.history', 'user.app-settings', 'user.profile_readonly']
            ]);
            
            $merchantCode = $this->config->get('sumup_payments_merchant_code');
            $checkoutRef = 'ETM'.$this->session->data['order_id'];
            $checkoutService = $sumup->getCheckoutService();
            
            $returnURL =  html_entity_decode($this->url->link('payment/sumup_payments/callback', 'order_id='.$this->session->data['order_id'], 'SSL'));
            
            $redirectURL =  html_entity_decode($this->url->link('payment/sumup_payments/send', 'order_id='.$this->session->data['order_id'], 'SSL'));
            
            $checkoutResponse = $checkoutService->create($amounttosup, 'GBP', $checkoutRef, $merchantCode, 'one-time payment',null,$returnURL,$redirectURL);
            $this->log->write(print_r($checkoutResponse,true));
            $checkoutId = $checkoutResponse->getBody()->id;
            //  pass the $chekoutId to the front-end to be processed
        } catch (SumUpAuthenticationException $e) {
           // echo 'Authentication error: ' . $e->getMessage();
            $this->log->write('Authentication error: ' . $e->getMessage());
        } catch (SumUpResponseException $e) {
           // echo 'Response error: ' . $e->getMessage(); //. print_r($e,true) ;
           // $this->log->write('Response error: ' .  print_r($e,true));
             $this->log->write('Response error: ' . $e->getMessage());
        } catch(SumUpSDKException $e) {
           // echo 'SumUp SDK error: ' . $e->getMessage();
             $this->log->write('SumUp SDK error: ' . $e->getMessage());
        }

        
        $this->data['button_confirm'] = 'Pay ' . $amount;
        $this->data['button_back'] = $this->language->get('button_back');
        $this->data['checkoutId'] = $checkoutId;
        $this->data['order_id'] = $this->session->data['order_id'];
        $this->data['paymentaction'] = $this->url->link('payment/sumup_payments/send', '', 'SSL');
       
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/sumup_payments.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/sumup_payments.tpl';
        } else {
            $this->template = 'default/template/payment/sumup_payments.tpl';
        }
        $this->render();
    }

    public function send() {
        
        $this->log->write('send : ' . print_r($this->request->post, true));
        
        $this->language->load('payment/sumup_payments');
        $this->load->model('checkout/order');
        $order_id = $this->request->post['order_id'];
        $checkoutRef = 'ETM'.$order_id;
        try {
            $sumup = new SumUp([
                    'app_id'     => $this->config->get('sumup_payments_app_id'),
                    'app_secret' => $this->config->get('sumup_payments_client_secret'),
                    'grant_type' => 'client_credentials',
              //      'scopes'      => ['payments', 'transactions.history', 'user.app-settings', 'user.profile_readonly']
                ]);

            $checkoutService = $sumup->getCheckoutService();
            $checkoutResponse = $checkoutService->findByReferenceId($checkoutRef);
            $this->log->write('findByReferenceId : ' . print_r($checkoutResponse,true));
        } catch (SumUpAuthenticationException $e) {
           // echo 'Authentication error: ' . $e->getMessage();
            $this->log->write('Authentication error: ' . $e->getMessage());
        } catch (SumUpResponseException $e) {
           // echo 'Response error: ' . $e->getMessage(); //. print_r($e,true) ;
           // $this->log->write('Response error: ' .  print_r($e,true));
             $this->log->write('Response error: ' . $e->getMessage());
        } catch(SumUpSDKException $e) {
           // echo 'SumUp SDK error: ' . $e->getMessage();
             $this->log->write('SumUp SDK error: ' . $e->getMessage());
        }
        $json = array();
        
        if($checkoutResponse->getBody()[0]->status=='PAID'  ) {
            $this->model_checkout_order->confirm($order_id, $this->config->get('sumup_payments_order_status_id'));
            $json['success'] = $this->url->link('checkout/success', '', 'SSL');
        } else {
            $this->model_checkout_order->confirm($order_id, $this->config->get('sumup_payments_faild_order_status_id'),'',false);
            $json['fail'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
            $json['error_message'] = $this->language->get('text_payment_error');
           
        }
        $this->response->setOutput(json_encode($json));
    }
    public function callback() {
        
        $this->log->write('callback post : ' . print_r($this->request->post, true));
        $this->log->write('callback get : ' . print_r($this->request->get, true));

        if(isset($this->request->get['order_id'])) {
            $this->language->load('payment/sumup_payments');
            $this->load->model('checkout/order');
        
            $order_id = $this->request->get['order_id'];
            $checkoutRef = 'ETM'.$order_id;
            try {
                $sumup = new SumUp([
                        'app_id'     => $this->config->get('sumup_payments_app_id'),
                        'app_secret' => $this->config->get('sumup_payments_client_secret'),
                        'grant_type' => 'client_credentials',
                  //      'scopes'      => ['payments', 'transactions.history', 'user.app-settings', 'user.profile_readonly']
                    ]);

                $checkoutService = $sumup->getCheckoutService();
                $checkoutResponse = $checkoutService->findByReferenceId($checkoutRef);
                $this->log->write('callback findByReferenceId : ' . print_r($checkoutResponse,true));
            } catch (SumUpAuthenticationException $e) {
                $this->log->write('callback Authentication error: ' . $e->getMessage());
            } catch (SumUpResponseException $e) {
                 $this->log->write('callback Response error: ' . $e->getMessage());
            } catch(SumUpSDKException $e) {
                 $this->log->write('callback SumUp SDK error: ' . $e->getMessage());
            }

            if($checkoutResponse->getBody()[0]->status=='PAID'  ) {
                $order_info = $this->model_checkout_order->getOrder($order_id);
                if(!$order_info['order_status_id']){
                    $this->model_checkout_order->confirm($order_id, $this->config->get('sumup_payments_order_status_id'));
                }elseif($order_info['order_status_id'] == $this->config->get('sumup_payments_faild_order_status_id')){ // Faild Order
                    // Prevent pending update in order model 
                    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '0', date_modified = '" . date("Y-m-d H:i:s", time()) . "' WHERE order_id = '" . (int) $paymentIntent->metadata->order_id . "'");
                    $this->model_checkout_order->confirm($order_id, $this->config->get('sumup_payments_order_status_id'));
                }
                $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
            } else {
                $this->model_checkout_order->confirm($order_id, $this->config->get('sumup_payments_faild_order_status_id'),'',false);
                $this->response->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            }
        }
    }
    
    

}

?>