<?php

class ControllerPaymentTakepaymentsGateway extends Controller {
    private $log;
    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('takepayments.log');
    }
    protected function index() {

        $this->language->load('payment/takepayments_gateway');
        $this->data += $this->language->load('payment/takepayments_gateway');
        $this->load->model('checkout/order');
        $this->data['form_hosted_url'] = 'https://gw1.tponlinepayments.com/paymentform/';
        //$this->data['form_hosted_url'] = 'https://gw1.tponlinepayments.com/hosted/';

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
//Order Info
        if ($order_info) {

            $merchant_id = $this->config->get('takepayments_gateway_merchant_id');
            $merchant_secret = $this->config->get('takepayments_gateway_merchantsecret');
            $country_code = $this->config->get('takepayments_gateway_currencycode');
            $currency_code = $this->config->get('takepayments_gateway_countrycode');
            $form_responsive = 'Y';
            if ($this->config->get('takepayments_gateway_module_type') == 'hosted') {
                $this->data['loadIframe'] = false;
            } else {
                $this->data['loadIframe'] = true;
            }

            $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false);

            // $amount = intval(bcmul(round($amount, 2), 100, 0));
            $amount = (int) strval($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) * 100);
            $trans_id = $this->session->data['order_id'];
            $callback = $this->url->link('payment/takepayments_gateway/callback', '', 'SSL');
            $bill_name = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . " " . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');

            $bill_addr = "";
            $addressFields = [
                'payment_address_1',
                'payment_address_2',
                'payment_city',
           //     'payment_zone',
                'payment_country'
            ];
            foreach ($addressFields as $item) {
                if(!empty($order_info[$item])) {
                $bill_addr .= preg_replace('/[^A-Za-z0-9\- ]/', '', htmlspecialchars_decode($order_info[$item])) . ($item == 'payment_country' ? "" : ",\n");
            }
            }

            $formdata = array(
                "merchantID" => $merchant_id,
                "amount" => $amount,
                "action" => "SALE",
                "type" => 1,
                "countryCode" => $country_code,
                "currencyCode" => $currency_code,
                "transactionUnique" => $trans_id,
                "orderRef" => "Order " . $trans_id,
                "redirectURL" => $callback,
                "callbackURL" => $callback,
                "formResponsive" => $form_responsive,
                "customerName" => preg_replace('/[^A-Za-z0-9\- ]/', '', htmlspecialchars_decode($bill_name)),
                "customerAddress" => $bill_addr,
                "customerPostCode" => preg_replace('/[^A-Za-z0-9\- ]/', '', htmlspecialchars_decode($order_info['payment_postcode'])),
                "customerEmail" => $order_info['email'],
                "customerPhone" => @$order_info['telephone'],
                "item1Description" => "Order " . $trans_id,
                "item1Quantity" => 1,
                "item1GrossValue" => $amount,
              //  'merchantCategoryCode' => 1731
            );
            ksort($formdata);

             $this->log->write(print_r($formdata,true));

            $signature = http_build_query($formdata, '', '&') . $merchant_secret;

            $formdata['signature'] = hash('SHA512', $signature);

            $this->data['formdata'] = $formdata;

            $this->data['button_confirm'] = 'Pay ' . $this->currency->format($order_info['total'], $order_info['currency_code'], '', true);

            $this->log->write('#'. $trans_id . ' ' . $amount);
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/takepayments_gateway.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/takepayments_gateway.tpl';
            } else {
                $this->template = 'default/template/payment/takepayments_gateway.tpl';
            }
            $this->render();
        }
    }

    /**
     * callback is used with the hosted form integration after a payment attempt to further process the order
     */
    public function callback() {
        
        // Start processing response data
        $data = $this->request->post;
        $error = false;
        $this->log->write(print_r($data,true));
        // Make sure it's a valid request
        if ($this->hasKeys($data, $this->getResponseTemplate())) {
            $this->load->model('checkout/order');
            $orderId = $data['transactionUnique'];
            $order = $this->model_checkout_order->getOrder($orderId);
           // $amountExpected = bcmul(round($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false), 2), 100, 0);
            $amountExpected = (int) strval($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false) * 100);
            if (intval($data['responseCode']) == 0 && $data['amountReceived'] == $amountExpected) {
                // Only update if the order id has not been properly set yet
                if ($order['order_status_id'] == 0) {
                    $this->model_checkout_order->confirm($orderId, $this->config->get('takepayments_gateway_order_status'), '');
                    $this->model_checkout_order->update($orderId, $this->config->get('takepayments_gateway_order_status'), $this->buildMessage());
                }else if($order['order_status_id'] == $this->config->get('takepayments_gateway_order_status_failed')){
                    $this->model_checkout_order->update($orderId, $this->config->get('takepayments_gateway_order_status'), '',true);
                    $this->model_checkout_order->update($orderId, $this->config->get('takepayments_gateway_order_status'), $this->buildMessage());
                }
                $url = $this->url->link('checkout/success', '', 'SSL');
                if ($isIframe) {
                    $this->response->setOutput("<script>top.location = '$url';</script>");
                } else {
                    $this->redirect($url);
                }
            } else {
                if ($order) {
                    try {
                        if ($order['order_status_id'] == 0) {
                            $this->model_checkout_order->confirm($orderId, $this->config->get('takepayments_gateway_order_status_failed'));
                            $this->model_checkout_order->update($orderId, $this->config->get('takepayments_gateway_order_status_failed'), $this->buildMessage());
                        }
                    } catch (Exception $e) {
                        // Order was not present
                    }
                }
                $error = true;
            }
        } else {
            // Don't try to delete an order here,
            // since it could be a fraudulent request!
            $error = true;
        }
        if ($error) {
            $url = $this->url->link('checkout/checkoutetm', '', 'SSL');
            if ($isIframe) {
                $this->response->setOutput("<script>top.location = '$url';</script>");
            } else {
                $this->redirect($url);
            }
        }
    }

    private function getResponseTemplate() {
        return array(
            'orderRef',
            'signature',
            'responseCode',
            'transactionUnique',
            'responseMessage',
            'action'
        );
    }

    private function hasKeys($array, $keys) {
        $checkKeys = array_keys($array);
        $str = '';
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
        }
        return true;
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
        return $msg;
    }

}
