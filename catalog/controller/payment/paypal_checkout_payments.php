<?php

require_once( DIR_SYSTEM . 'vendor/paypal-checkout/vendor/autoload.php');

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class ControllerPaymentPaypalCheckoutPayments extends Controller {

    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('paypal_checkout.log');
    }

    public function index() {

        $this->language->load('payment/paypal_checkout_payments');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], '', true);
        $this->data['button_confirm'] = 'Pay ' . $amount;
        $this->data['button_back'] = $this->language->get('button_back');
        $this->data['paypal_checkout_client_id'] = $this->config->get('paypal_checkout_payments_client_id');
        $this->data['currency'] = $order_info['currency_code'];

        $this->data['order_id'] = $this->session->data['order_id'];
        $this->data['action'] = $this->url->link('payment/paypal_checkout_payments/send', '', 'SSL');
        $this->data['capture'] = $this->url->link('payment/paypal_checkout_payments/capture', '', 'SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paypal_checkout_payments.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/paypal_checkout_payments.tpl';
        } else {
            $this->template = 'default/template/payment/paypal_checkout_payments.tpl';
        }
        $this->render();
    }

    public function send() {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);
        if ($data && is_array($data)) {
            $this->request->post += $data;
        }
        $order_id = isset($this->request->post['order_id']) ? $this->request->post['order_id'] : null;
        if (is_null($order_id)) {
            $this->log->write('Invalid card data : ' . print_r($this->request->post, true));
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            return;
        }

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);

        if ($this->config->get('paypal_checkout_payments_test')) {
            $environment = new SandboxEnvironment($this->config->get('paypal_checkout_payments_client_id'), $this->config->get('paypal_checkout_payments_client_secret'));
        } else {
            $environment = new ProductionEnvironment($this->config->get('paypal_checkout_payments_client_id'), $this->config->get('paypal_checkout_payments_client_secret'));
        }

        $paypal_client = new PayPalHttpClient($environment);



        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);


        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
            "reference_id" => $order_id,
            "amount" => [
                "value" => $amount,
                "currency_code" => $order_info['currency_code']
            ]
                ]],
            "payer" => [
                "email_address"=>$order_info['email'],
                "name" =>["given_name"=>$order_info['shipping_firstname'] , "surname"=>$order_info['shipping_lastname'] ],
              //  "phone" => ["phone_number"=>$order_info['telephone']],
                "address"=>["address_line_1"=>$order_info['shipping_address_1'] ,
                    "address_line_2"=> $order_info['shipping_address_2'],
                    "admin_area_2"=>$order_info['shipping_city'],
                    "postal_code" => $order_info['shipping_postcode'],
                    "country_code"=>$order_info['shipping_iso_code_2']]
            ],
            "application_context" => [
                "cancel_url" => $this->url->link('payment/paypal_checkout_payments/failure', '', 'SSL'),
                "return_url" => $this->url->link('payment/paypal_checkout_payments/capture', '', 'SSL')
            ]
        ];
        $telephone = $order_info['telephone'];
        if($this->validukphoneNumber($telephone)) {
            if('0' == substr("".$telephone,0,1) ) {
                $telephone = '0044' . substr($telephone,1);
                $request->body['payer']['phone'] = ["phone_number" =>  [  "national_number" => $telephone]];
            }
            
        }
        $this->log->write(print_r($request, true));
        try {
            // Call API with your client and get a response for your call
            $response = $paypal_client->execute($request);

            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            $this->log->write(' --- ORDER CREATE -- ');
            $this->log->write(print_r(json_encode($response), true));
            $this->response->setOutput(json_encode($response->result));
        } catch (HttpException $ex) {

            $this->log->write(print_r($ex->getMessage(), true));
        }
    }

    public function capture() {
        $this->log->write(' --- ORDER CAPTURE -- ');
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);
        if ($data && is_array($data)) {
            $this->request->post += $data;
        }

        $json = [];

        if (isset($this->request->post['paypalorderID'])) {
            $paypalorderID = $this->request->post['paypalorderID'];

            if ($this->config->get('paypal_checkout_payments_test')) {
                $environment = new SandboxEnvironment($this->config->get('paypal_checkout_payments_client_id'), $this->config->get('paypal_checkout_payments_client_secret'));
            } else {
                $environment = new ProductionEnvironment($this->config->get('paypal_checkout_payments_client_id'), $this->config->get('paypal_checkout_payments_client_secret'));
            }

            $paypal_client = new PayPalHttpClient($environment);

            $request = new OrdersCaptureRequest($paypalorderID);

            $response = $paypal_client->execute($request);

            $this->log->write(print_r(json_encode($response), true));

            if ($response->result->status == 'COMPLETED') {

                $this->load->model('checkout/order');

                $this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('paypal_checkout_payments_order_status_id'));
                $json['success'] = $this->url->link('checkout/success', '', 'SSL');
            } else {

                // $this->session->data['paypal_error'] = $this->language->get('entry_paypal_error');

                $json['failure'] = $this->url->link('payment/paypal_checkout_payments/failure', '', 'SSL');
            }
            $json['response'] = $response;
        } else {

            $json['failure'] = $this->url->link('payment/paypal_checkout_payments/failure', '', 'SSL');
        }

        $this->response->setOutput(json_encode($json));
    }

    public function webhook() {
        http_response_code(200);
        echo "OK";
        exit();
    }

    public function failure() {
        $this->language->load('payment/paypal_checkout_payments');
        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language'] = $this->language->get('code');
        $this->data['direction'] = $this->language->get('direction');
        //Page titles
        $this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['text_response'] = $this->language->get('text_response');
        $this->data['error_response'] = isset($this->session->data['paypal_error']) ? $this->session->data['paypal_error'] : '';
        //Error text
        $this->data['text_failure'] = $this->language->get('text_failure');
        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkoutetm'));
        //Mismatch
        $this->data['continue'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
        $this->template = $this->config->get('config_template') . '/template/payment/paypal_checkout_payments_failure.tpl';
        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );
        unset($this->session->data['paypal_error']);
        $this->response->setOutput($this->render());
    }
    private function validukphoneNumber($number)
    {
        //phone number can't include letters.
        if (preg_match("/[A-Z]/i", $number) != 0) {
            return false;
        }
        $number = preg_replace('/\D+/', '', $number);
        $len    = strlen($number);
        return $number[0] == 0 // first number is 0
            && (($len == 11 && ($number[1] != "0"))    // 11 digits is fine
            || ($len == 10     // 10 digits is fine if 01 or 08
                && ($number[1] == 1 || $number[1] == 8)));
    }

}

?>