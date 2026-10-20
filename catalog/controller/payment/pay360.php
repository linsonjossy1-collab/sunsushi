<?php

class ControllerPaymentPay360 extends Controller {

    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('pay360.log');
    }

    public function index() {
        $this->load->language('payment/pay360');
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

        $post = array(
            "session" => array(
                "returnUrl" => array("url" => HTTPS_SERVER . 'index.php?route=payment/pay360/response&order_id=' . $this->session->data['order_id']),
                "cancelUrl" => array("url" => HTTPS_SERVER . 'index.php?route=payment/pay360/responsecancel&order_id=' . $this->session->data['order_id']),
                "transactionNotification" => array("url" => HTTPS_SERVER . 'index.php?route=payment/pay360/precallback&order_id=' . $this->session->data['order_id'],
                    "format" => "REST_JSON")
            ),
            "transaction" => array("merchantReference" => "" . $this->session->data['order_id'],
                "money" => array("amount" => array("fixed" => $amount),
                    "currency" => "GBP"
                )
            ),
            "customer" => array("registered" => false,
                "details" => array(
                    "name" => preg_replace('/[^a-z\d ]/i', ' ', htmlspecialchars_decode($order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'])),
                    "address" => [
                        "line1" => preg_replace('/[^a-z\d ]/i', ' ', htmlspecialchars_decode($order_info['payment_address_1'])),
                        "line2" => preg_replace('/[^a-z\d ]/i', ' ', htmlspecialchars_decode($order_info['payment_address_2'])),
                        "city" => preg_replace('/[^a-z\d ]/i', ' ', htmlspecialchars_decode($order_info['payment_city'])),
                        "postcode" => $order_info['payment_postcode'],
                        "countryCode" => "GBR"
                    ],
                    "telephone" => $this->_validatephone($order_info['telephone']),
                    "emailAddress" => $order_info['email'],
                    "ipAddress" => $order_info['ip'],
                    "defaultCurrency" => "GBP"
                )
            )
        );


        $instid = $this->config->get('pay360_hosted_cashier_instid'); // Hosted Cashier Installation ID

        $endpoint = "/hosted/rest/sessions/$instid/payments";

        $response = $this->_apicall($post, $endpoint);
        if (isset($response['redirectUrl'])) {
            $this->data['payredirect_url'] = $response['redirectUrl'];
        }

        $this->data['response_url'] = 'checkout/checkoutetm';
        $this->data['button_back'] = $this->language->get('button_back');


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pay360.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/pay360.tpl';
        } else {
            $this->template = 'default/template/payment/pay360.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function response() {
        if (isset($this->request->get['sessionId'])) {

            $instid = $this->config->get('pay360_hosted_cashier_instid'); // Hosted Cashier Installation ID
            $sessionId = $this->request->get['sessionId'];

            $endpoint = "/hosted/rest/sessions/$instid/$sessionId/status";

            $response = $this->_apicall(array(), $endpoint);

            if (isset($response['hostedSessionStatus']['transactionState']['transactionState']) && $response['hostedSessionStatus']['transactionState']['transactionState'] == 'SUCCESS') {
                $this->redirect($this->url->link('checkout/success'));
            }
        }
        else {
              $this->log->write('response wrong: ' . print_r($this->request->get, true));
        }

        $this->load->language('payment/pay360');

        $this->data['text_response'] = $this->language->get('text_decline');
        $this->data['text_message'] = ""; // htmlentities($res['responseMessage']);
        $this->data['text_link'] = sprintf($this->language->get('text_link'), $this->url->link('checkout/checkoutetm', '', 'SSL'));
        $this->data['response_url'] = 'checkout/checkoutetm';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pay360_response.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/pay360_response.tpl';
        } else {
            $this->template = 'default/template/payment/pay360_response.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function responsecancel() {
        $this->load->language('payment/pay360');
        $this->data['text_response'] = $this->language->get('text_decline');
        $this->data['text_message'] = ""; // htmlentities($res['responseMessage']);
        $this->data['text_link'] = sprintf($this->language->get('text_link'), $this->url->link('checkout/checkoutetm', '', 'SSL'));
        $this->data['response_url'] = 'checkout/checkoutetm';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pay360_response.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/pay360_response.tpl';
        } else {
            $this->template = 'default/template/payment/pay360_response.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function precallback() {
        $data = json_decode(file_get_contents("php://input"), true);

        $this->log->write('callback : ' . print_r($data, true));

        if (isset($data['transaction']['status']) && $data['transaction']['status'] == "SUCCESS") {

            $order_id = $this->request->get['order_id'];
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            if (!$order_info['order_status_id']) { //  Missing order 
                $this->model_checkout_order->confirm($order_id, $this->config->get('pay360_order_status_id'));
            }
            echo "Thank you for your payment";
        } else {
            echo "Failed to take payment";
            $this->log->write('callback : Failed to take payment: ' . print_r($data, true));
        }

        die();
    }

    // Function to create a message signature
    private function _apicall(array $post, $endpoint) {
        if ($this->config->get('pay360_payments_mode')) {
            $host = 'https://api.mite.pay360.com';
        } else {
            $host = 'https://api.pay360.com';
        }
        $u = $this->config->get('pay360_api_username'); // "LCX6E5MRANBVXNHMMOAMF5EONE"; // API Username
        $p = $this->config->get('pay360_api_password'); // "6Jg+HEma9wlvRYhrVLmdnQ=="; // API Password


        $ch = curl_init($host . $endpoint);
        if (!empty($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        }
        curl_setopt($ch, CURLOPT_USERPWD, "$u:$p");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $this->log->write('API : ' . $response);

        $obj = json_decode($response, true);
        return $obj;
    }

    private function _validatephone($mobile) {
        if ('0' == substr("" . trim($mobile), 0, 1)) {
            $mobile = '0044' . substr(trim($mobile), 1);
            return $mobile;
        } else {
            return false;
        }
    }

}
