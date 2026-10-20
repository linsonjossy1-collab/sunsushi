<?php
class ControllerPaymentIpgPayment extends Controller {
    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('ipgpayment.log');
    }
    protected function index() {
        $this->language->load('payment/ipg_payment');
        $this->load->model('checkout/order');
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

        try {
            $this->load->library('ipg-payment');

            if ($this->config->get('ipg_payment_mode') == 'test') {
                $payments = (new IpgPayment($this->registry))->testEnvironment(array(
                    "merchantId" => $this->config->get('ipg_payment_marchant'),
                    "password" => $this->config->get('ipg_payment_password'),
                ));
            } else {
                $payments = (new IpgPayment($this->registry))->productionEnvironment(array(
                    "merchantId" => $this->config->get('ipg_payment_marchant'),
                    "password" => $this->config->get('ipg_payment_password'),
                ));
            }
            $purchase = $payments->purchase();
            $purchase->allowOriginUrl($this->config->get('config_url'))->
                    merchantLandingPageUrl($this->url->link('payment/ipg_payment/paycheck'))->
                    merchantNotificationUrl($this->url->link('payment/ipg_payment/callback'))->
                    channel(IpgPayment::CHANNEL_ECOM)->
                    userDevice(IpgPayment::USER_DEVICE_DESKTOP)->
                    amount($amount)->
                    country("GB")->
                    currency($order_info['currency_code'])->
                    paymentSolutionId("500")->
                    brandId($this->config->get('ipg_payment_brandid'))->
                    merchantTxId($this->session->data['order_id'])
                    ;
            $token = $purchase->token();
            $this->data['token'] = $token->token;
            $this->data['merchantId'] = $token->merchantId;

            $this->data['ipg_payment_javascripturl'] = $purchase->javaScriptUrl();
            $this->data['ipg_payment_baseurl'] = $purchase->BaseUrl();

            $this->data['ipg_payment_cancel'] = $this->url->link('payment/ipg_payment/callback', '', 'SSL');
            $this->data['ipg_payment_succuss'] = $this->url->link('payment/ipg_payment/paycheck', '', 'SSL');
        } catch (Exception $e) {

            $this->log->write("Exception -IPG");
            $this->log->write($e->getMessage());
            $this->log->write($e->getCode());
            $this->log->write($e->getFile());
            $this->log->write($e->getLine());
        }

        $ipg_tempate = 'ipg_payment_js';
        if ($this->config->get('ipg_payment_redirect') == 'redirect') {
            $ipg_tempate = 'ipg_payment_redirect';
        }

        $this->data['ipg_payment_cancel'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
        $this->data['ipg_payment_succuss'] = $this->url->link('payment/ipg_payment/success', '', 'SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/' . $ipg_tempate . '.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/' . $ipg_tempate . '.tpl';
        } else {
            $this->template = 'default/template/payment/' . $ipg_tempate . '.tpl';
        }

        $this->render();
    }

    public function paycheck() {
        $this->log->write('IPG Payment - success');
        $this->log->write(print_r($this->request->post,true));
        if($this->request->post['result'] != 'success'){
            $this->redirect($this->url->link('checkout/checkoutetm'));
        }
        else {
            $this->load->model('checkout/order');
            $order_id = $this->request->post['merchantTxId'];
            try {
                if($order_id) { 
                $this->load->library('ipg-payment');
                if ($this->config->get('ipg_payment_mode') == 'test') {
                    $payment = (new IpgPayment($this->registry))->testEnvironment(array(
                        "merchantId" => $this->config->get('ipg_payment_marchant'),
                        "password" => $this->config->get('ipg_payment_password'),
                    ));
                } else {
                    $payment = (new IpgPayment($this->registry))->productionEnvironment(array(
                        "merchantId" => $this->config->get('ipg_payment_marchant'),
                        "password" => $this->config->get('ipg_payment_password'),
                    ));
                }
                $order_status = $payment->status_check();
                
                  
                    $order_status->allowOriginUrl($this->config->get('config_url'))
                        ->merchantId($this->config->get('ipg_payment_marchant'))
                        ->merchantTxId($order_id)  // ORDER_ID
                        ->timestamp(time());
                    $status_check =  $order_status->execute();
                                        
                    if($status_check->status == 'ERROR'){
                        $this->log->write("IPG FAIL :- ".$status_check->status." Status updated Order id : ".$order_id);
                        $this->redirect($this->url->link('checkout/checkoutetm'));
                    
                    }
                    elseif($status_check->status =='CAPTURED' || $status_check->status =='SUCCESS'){
                        $this->model_checkout_order->confirm($order_id, $this->config->get('ipg_payment_order_status_id'));
                         $this->redirect($this->url->link('checkout/success'));
                    }
                    elseif(!$status_check->status){
                        $this->log->write("IPG STATUS :- ".$status_check->status." Status updated Order id : ".$order_id."Payment process not completed or page reloaded before payment.");
                        $this->redirect($this->url->link('checkout/checkoutetm'));
                }
                }
            } catch (Exception $e) {
                $this->log->write("Exception -IPG");
                $this->log->write($e->getMessage());
                $this->log->write($e->getCode());
                $this->log->write($e->getFile());
                $this->log->write($e->getLine());
                $this->redirect($this->url->link('checkout/checkoutetm'));
            }

            }
            }

    public function callback() {

        $this->log->write('IPG Payment - call');
        $this->log->write(print_r($this->request->post,true));


        }

    }
?>