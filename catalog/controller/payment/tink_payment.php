<?php

class ControllerPaymentTinkPayment extends Controller {

    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('tink_payment.log');
    }

    public function index() {
        $this->language->load('payment/tink_payment');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false);
        $order_id = $this->session->data['order_id'];

        $access_token = $this->getToken();
        if ($access_token) {

            $post_data = [
                "destinations" => [
                    [
                        "accountNumber" => $this->config->get('tink_payment_destinations_account'),
                        "type" => $this->config->get('tink_payment_destinations_accounttype')
                    ]
                ],
                "amount" => $amount,
                "currency" => $this->config->get('tink_payment_currency'),
                "market" => $this->config->get('tink_payment_market'),
                "metadata" => ['order_id' => $this->session->data['order_id']],
                "recipientName" => $this->config->get('tink_payment_recipientname'),
                "sourceMessage" => $this->config->get('config_name') . ' #' . $order_id,
                "remittanceInformation" => [
                    "type" => "UNSTRUCTURED",
                    "value" => "CREDITOR REFERENCE"
                ],
                "paymentScheme" => "SEPA_CREDIT_TRANSFER"
                    ];

            $this->log->write(print_r(json_encode($post_data), true));

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.tink.com/api/v1/payments/requests",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($post_data),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json'
                ),
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($err || $http_status != 200) {
                $this->log->write("cURL Error client #:" . $err . ' status:' . $http_status);
                $this->data['action'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
            } else {
                $json = json_decode($response, true);
                $this->data['action'] = 'https://link.tink.com/1.0/pay/?client_id=' . $this->config->get('tink_payment_client_id') . '&redirect_uri='
                        . urlencode($this->url->link('payment/tink_payment/callback', '', 'SSL')) . '&market=IT&locale=en_US&payment_request_id=' . $json['id'];
            }
        } else {
            $this->data['action'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
        }

        $this->log->write($this->data['action']);
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/tink_payment.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/tink_payment.tpl';
        } else {
            $this->template = 'default/template/payment/tink_payment.tpl';
        }

        $this->render();
    }

    public function callback() {
        // $this->log->write('callback  : ' . print_r($this->request->get, true));
        if (isset($this->request->get['error'])) {
            $this->log->write('error  : ' . print_r($this->request->get, true));
            $this->session->data['tink_payment_error'] = $this->request->get['message'];
            $this->redirect($this->url->link('payment/tink_payment/cancel', '', 'SSL'));
            return;
        }

        $payment_request_id = isset($this->request->get['payment_request_id']) ? $this->request->get['payment_request_id'] : null;
        if (is_null($payment_request_id)) {
            $this->log->write('Invalid card data : ' . print_r($this->request->get, true));
            $this->redirect($this->url->link('payment/tink_payment/cancel', '', 'SSL'));
            return;
        } else {
            $access_token = $this->getToken();
            if ($access_token) {

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://api.tink.com/api/v1/payments/requests/" . $payment_request_id . "/transfers",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $access_token,
                        'Content-Type: application/json'
                    ),
                ]);

                $response = curl_exec($curl);
                $err = curl_error($curl);
                $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                curl_close($curl);

                if ($err || $http_status != 200) {
                    $this->log->write("cURL Error client #:" . $err . ' status:' . $http_status);
                    $this->redirect($this->url->link('payment/tink_payment/cancel', '', 'SSL'));
                } else {
                    //$this->log->write("cURL Resposnse #:" . $response);
                    $json = json_decode($response, true);
                    // $this->log->write('Sucsess : ' . print_r($json, true));

                    if ($json['paymentRequestCreatedTransfers'][0]['status'] == 'SENT') {
                        $order_id = $json['paymentRequestCreatedTransfers'][0]['metadata']['order_id'];
                        $this->load->model('checkout/order');
                        $order_info = $this->model_checkout_order->getOrder($order_id);
                        if (!$order_info['order_status_id']) { // Missing order
                             $this->model_checkout_order->confirm($order_id, $this->config->get('tink_payment_order_status_id'));
                        }
                        $this->redirect($this->url->link('checkout/success', '', 'SSL'));
                    } else {
                        $this->log->write('error payment  : ' . print_r($json, true));
                        $this->session->data['tink_payment_error'] = $json['paymentRequestCreatedTransfers'][0]['statusMessage'];
                        $this->redirect($this->url->link('payment/tink_payment/cancel', '', 'SSL'));
                        return;
                    }
                }
            } else {

                $this->log->write('callback tocken error');
                #TODO
                $this->redirect($this->url->link('checkout/success', '', 'SSL'));
            }
        }
    }

    public function cancel() {

        $timeout = 10;
        $this->language->load('payment/tink_payment');

        $this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

        if ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) || (isset($this->request->server['HTTP_X_FORWARDED_PROTO']) && $this->request->server['HTTP_X_FORWARDED_PROTO'] == 'https')) {
            $this->data['base'] = HTTPS_SERVER;
        } else {
            $this->data['base'] = HTTP_SERVER;
        }

        $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

        $this->data['text_failure'] = (isset($this->session->data['tink_payment_error']) ? ($this->session->data['tink_payment_error'] . ' ') : '') . $this->language->get('text_failure');
        unset($this->session->data['tink_payment_error']);

        $this->data['timeout'] = $timeout * 1000;

        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $timeout, $this->url->link('checkout/checkoutetm', '', 'SSL'));

        $this->data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/checkoutetm';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/tink_payment_failure.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/tink_payment_failure.tpl';
        } else {
            $this->template = 'default/template/payment/tink_payment_failure.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function webhook() {
        $payload = @file_get_contents('php://input');
        //$this->log->write(' webhook event : ' . print_r($payload, true));

        $HEADER = getallheaders();
        $signatureHeader = $HEADER['X-Tink-Signature'];

        $keyValues = explode(",", $signatureHeader);

        // Get the list of valid keys
        $validKeys = ["t", "v1"];

        // Create an array to store the values
        $values = [];

        // Loop through the key-value pairs
        foreach ($keyValues as $keyValue) {

            // Split the key-value pair into key and value
            list($key, $value) = explode("=", $keyValue);

            // Check if the key is valid
            if (in_array($key, $validKeys)) {

                // Add the value to the array of values
                $values[] = $value;
            }
        }

        $timestamp = $values[0];
        $tink_signature = $values[1];

        $signature = hash_hmac('SHA256', $timestamp . '.' . $payload, $this->config->get('tink_payment_webhook_secret'));

        if ($tink_signature == $signature) {
            $event = json_decode($payload, true);
            if (isset($event['content']['metadata']['order_id']) && $event['content']['metadata']['order_id'] && $event['content']['status'] = 'SENT') {
                $order_id = $event['content']['metadata']['order_id'];
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);
                if (!$order_info['order_status_id']) { // Missing order
                    $this->model_checkout_order->confirm($order_id, $this->config->get('tink_payment_order_status_id'));
                }
            }
        } else {
            $this->log->write(' webhook hash error ' . $tink_signature . ' = ' . $signature);
        }
        http_response_code(200);
        echo "OK";
        exit();
    }

    private function getToken() {

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.tink.com/api/v1/oauth/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => 'client_id=' . $this->config->get('tink_payment_client_id') . '&client_secret=' . $this->config->get('tink_payment_client_secret') . '&grant_type=client_credentials&scope=payment:read,payment:write'
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        if ($err || $http_status != 200) {
            $this->log->write("cURL Error client #:" . $err);
            return false;
        } else {
            // $this->log->write("cURL Resposnse #:" . $response);
            $json = json_decode($response, true);
            return $json['access_token'];
        }
    }

}

?>