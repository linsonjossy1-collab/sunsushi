<?php

class ControllerPaymentRmsPayment extends Controller {

    private $log;

    public function __construct($obj) {
        parent::__construct($obj);
        $this->load->language('payment/rms_payment');
        $this->log = new Log('rms_payment.log');
    }

    protected function index() {

        $this->load->language('payment/rms_payment');

        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $amount = (int) ($this->currency->format(
                        $order_info['total'],
                        $order_info['currency_code'],
                        $order_info['currency_value'],
                        false
                ) * 100);

        $data = array(
            'merchantID' => $this->config->get('rms_payment_merchantid'),
            'action' => 'SALE',
            'type' => 1,
            'countryCode' => $this->config->get('rms_payment_countrycode'),
            'currencyCode' => $this->config->get('rms_payment_currencycode'),
            'amount' => $amount,
            'duplicateDelay' => 900,
            'orderRef' => $this->config->get('config_name') . ' ' . $this->session->data['order_id'],
            'transactionUnique' => $this->session->data['order_id'],
            'redirectURL' => $this->url->link('payment/rms_payment/send', '', 'SSL'),
            'callbackURL' => $this->url->link('payment/rms_payment/callback', '', 'SSL'),
        );
        if ($order_info['payment_lastname']) {
            $data['customerName'] = trim($order_info['payment_firstname'] . " " . $order_info['payment_lastname']);
        }
        if ($order_info['payment_address_1']) {
            $data['customerAddress'] = trim($order_info['payment_address_1'] . " " . $order_info['payment_address_2']);
        }
        if ($order_info['payment_city']) {
            $data['customerAddress'] .= "\n" . $order_info['payment_city'];
        }
        if ($order_info['payment_postcode']) {
            $data['customerPostcode'] = $order_info['payment_postcode'];
        }
        if ($order_info['email']) {
            $data['customerEmail'] = $order_info['email'];
        }
        if ($order_info['telephone']) {
            $data['customerPhone'] = $order_info['telephone'];
        }
        $data['signature'] = $this->createSignature($data);
        $this->data['formdata'] = $data;
        $this->data['url'] = 'https://gateway.retailmerchantservices.co.uk/paymentform/';
        $this->template = $this->config->get('config_template') . '/template/payment/rms_payment.tpl';

        $this->render();
    }

    private function createSignature(array $data) {
        // Sort by field name
        ksort($data);
        // Create the URL encoded signature string
        $ret = http_build_query($data, '', '&');
        // Normalise all line endings (CRNL|NLCR|NL|CR) to just NL (%0A)
        $ret = str_replace(array('%0D%0A', '%0A%0D', '%0D'), '%0A', $ret);
        // Hash the signature string and the key together
        return hash('SHA512', $ret . $this->config->get('rms_payment_merchantsecret'));
    }

    public function send() {
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language'] = $this->language->get('code');
        $this->data['direction'] = $this->language->get('direction');
        //Page titles
        $this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['text_response'] = $this->language->get('text_response');
        //Success text
        $this->data['text_success'] = $this->language->get('text_success');
        $this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
        //Error text
        $this->data['text_failure'] = $this->language->get('text_failure');
        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));
        //Mismatch
        $this->data['text_mismatch'] = $this->language->get('text_mismatch');
        //Start processing response data
        $data = $this->request->post;
        // $this->log->write('RmsPayment success: ' .print_r($data,true));
        $signature = null;
        if (isset($data['signature'])) {
            $signature = $data['signature'];
            unset($data['signature']);
        }
        if (!$signature || $signature !== $this->createSignature($data)) {
            // $error = true;
            $this->request->post['signature_error'] = ' ** signature error ** ';
        }
        //else {
            //Make sure it's a valid request

            $this->load->model('checkout/order');
            $orderId = $data['transactionUnique'];
            $order = $this->model_checkout_order->getOrder($orderId);
            if ($data['responseCode'] === "0") {
                //Only update if the order id has not been properly set yet
                if ($order['order_status_id'] == 0) {
                    $this->model_checkout_order->confirm($orderId, $this->config->get('rms_payment_order_status_id'));
                    $message = $this->buildMessage();
                    $this->model_checkout_order->update($orderId, $this->config->get('rms_payment_order_status_id'), $message, false);
                }

                $this->data['continue'] = $this->url->link('checkout/success');
                $this->template = $this->config->get('config_template') . '/template/payment/rms_payment_success.tpl';
                $this->children = array(
                    'common/column_left',
                    'common/column_right',
                    'common/content_top',
                    'common/content_bottom',
                    'common/footer',
                    'common/header'
                );
                $this->response->setOutput($this->render());
            } else {
                $error = true;
            }
       // }

        if ($error) {
            $this->data['continue'] = $this->url->link('checkout/checkoutetm');
            $this->template = $this->config->get('config_template') . '/template/payment/rms_payment_failure.tpl';
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

    /**
     * callback is used with the hosted form integration after a payment attempt to further process the order
     */
    public function callback() {
       // $this->log->write('RMS - CALLBACK CALL ');
        $this->log->write('RmsPayment callback: ' . print_r($this->request->post, true));
        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language'] = $this->language->get('code');
        $this->data['direction'] = $this->language->get('direction');
        //Page titles
        $this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['text_response'] = $this->language->get('text_response');
        //Success text
        $this->data['text_success'] = $this->language->get('text_success');
        $this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
        //Error text
        $this->data['text_failure'] = $this->language->get('text_failure');
        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));
        //Mismatch
        $this->data['text_mismatch'] = $this->language->get('text_mismatch');
        //Start processing response data
        $data = $this->request->post;
        //  $this->log->write('RmsPayment callback: '. print_r($data,true));
        $error = false;
        $data = $this->request->post;
        // $this->log->write('RmsPayment success: ' .print_r($data,true));
        $signature = null;
        if (isset($data['signature'])) {
            $signature = $data['signature'];
            unset($data['signature']);
        }
        if (!$signature || $signature !== $this->createSignature($data)) {
            // $error = true;
            $this->request->post['signature_error'] = ' ** signature error ** ';
        }
        //else {
            $this->load->model('checkout/order');
            $orderId = $data['transactionUnique'];
            $order = $this->model_checkout_order->getOrder($orderId);

            if ($data['responseCode'] === "0") {
                //Only update if the order id has not been properly set yet
                if ($order['order_status_id'] == 0) {
                    $this->model_checkout_order->confirm($orderId, $this->config->get('rms_payment_order_status_id'));
                    $message = $this->buildMessage();
                    $this->model_checkout_order->update($orderId, $this->config->get('rms_payment_order_status_id'), $message, false);
                }
            } else {
                $error = true;
            }
        // }
        if ($error) {
          //  $this->log->write('RmsPayment callback: ' . print_r($data, true));
            // #TODO Remove log line 157
        }
        echo "OK";
        die();
    }

    private function buildMessage() {
        $data = $this->request->post;

        $msg = "Payment " . ($data['responseCode'] == 0 ? "Successful" : "Unsuccessful") . "<br/><br/>" .
                "Amount Received: " . (isset($data['amountReceived']) ? floatval($data['amountReceived']) / 100 : 'Unknown') . "<br/>" .
                "Message: \"" . ucfirst($data['responseMessage']) . "\"</br>" .
                "Xref: " . $data['xref'] . "<br/>" .
                (isset($data['cv2Check']) ? "CV2 Check: " . ucfirst($data['cv2Check']) . "</br>" : '') .
                (isset($data['addressCheck']) ? "Address Check: " . ucfirst($data['addressCheck']) . "</br>" : '') .
                (isset($data['postcodeCheck']) ? "Postcode Check: " . ucfirst($data['postcodeCheck']) . "</br>" : '');

        if (isset($data['threeDSEnrolled'])) {
            switch ($data['threeDSEnrolled']) {
                case "Y":
                    $enrolledtext = "Enrolled.";
                    break;
                case "N":
                    $enrolledtext = "Not Enrolled.";
                    break;
                case "U";
                    $enrolledtext = "Unable To Verify.";
                    break;
                case "E":
                    $enrolledtext = "Error Verifying Enrolment.";
                    break;
                default:
                    $enrolledtext = "Integration unable to determine enrolment status.";
                    break;
            }
            $msg .= "<br />3D Secure enrolled check outcome: \"" . $enrolledtext . "\"";
        }

        if (isset($data['threeDSAuthenticated'])) {
            switch ($data['threeDSAuthenticated']) {
                case "Y":
                    $authenticatedtext = "Authentication Successful";
                    break;
                case "N":
                    $authenticatedtext = "Not Authenticated";
                    break;
                case "U";
                    $authenticatedtext = "Unable To Authenticate";
                    break;
                case "A":
                    $authenticatedtext = "Attempted Authentication";
                    break;
                case "E":
                    $authenticatedtext = "Error Checking Authentication";
                    break;
                default:
                    $authenticatedtext = "Integration unable to determine authentication status.";
                    break;
            }
            $msg .= "<br />3D Secure authenticated check outcome: \"" . $authenticatedtext . "\"";
        }
        if(isset($data['signature_error'])) {
            $msg .= "<br />" . $data['signature_error'];
        }
        return $msg;
    }

}
