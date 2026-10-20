<?php

class ControllerPaymentTrustpayments extends Controller {
    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('trustpayments.log');
    }
    protected function index() {
        $this->language->load('payment/trustpayments');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['action'] = 'https://payments.securetrading.net/process/payments/choice';
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->data['currency_code'] = $order_info['currency_code'];
        $this->data['mainamount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $this->data['sitereference'] = $this->config->get('trustpayments_storeid');
        $this->data['successfulurlredirect'] = $this->url->link('payment/trustpayments/success', '', 'SSL');
        $this->data['declinedurlredirect'] = $this->url->link('payment/trustpayments/fail', '', 'SSL');
        $this->data['successfulurlnotification'] = $this->url->link('payment/trustpayments/callback', 'acd=Y', 'SSL');
        $this->data['declinedurlnotification'] = $this->url->link('payment/trustpayments/callback', 'acd=N', 'SSL');
        $this->data['sitesecuritytimestamp'] = gmdate("Y-m-d H:i:s");
        $this->data['oid'] = $this->session->data['order_id'];
        if ($this->config->get('trustpayments_password')) {
            $genstring = $this->data['currency_code'] . $this->data['mainamount'] . $this->data['sitereference'] . "FINAL" . "2" . "default" . "st_paymentcardonly" . "STR-6" . "STR-7" . "STR-8" . "STR-9" . $this->data['successfulurlredirect'] . $this->data['declinedurlredirect'] . htmlspecialchars_decode($this->data['successfulurlnotification']) . htmlspecialchars_decode($this->data['declinedurlnotification']) . $this->data['oid'] . $this->data['sitesecuritytimestamp'] . $this->config->get('trustpayments_password');
            $this->data['sitesecurity'] = 'h' . hash("sha256", $genstring);
        } else {
            $this->data['sitesecurity'] = '';
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/trustpayments.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/trustpayments.tpl';
        } else {
            $this->template = 'default/template/payment/trustpayments.tpl';
        }
        $this->render();
    }

    public function fail() {
        $this->log->write('Trustpayments ->'.  print_r($this->request->post, true));
        $this->redirect($this->url->link('checkout/checkoutetm'));
    }

    public function success() {
        $this->log->write('success : ' . print_r($this->request->get, true));
        if (isset($this->request->get['orderreference'])) {
            $this->load->model('checkout/order');
            $this->language->load('payment/trustpayments');
            $order_id = $this->request->get['orderreference'];
            $order_info = $this->model_checkout_order->getOrder($order_id);

            $message = '';

            if (isset($this->request->get['errorcode'])) {
                $message .= 'errorcode: ' . $this->request->get['errorcode'] . "\n";
            }

            if (isset($this->request->get['orderreference'])) {
                $message .= 'orderreference: ' . $this->request->get['orderreference'] . "\n";
            }

            if (isset($this->request->get['requestreference'])) {
                $message .= 'requestreference: ' . $this->request->get['requestreference'] . "\n";
            }

            if (isset($this->request->get['settlestatus'])) {
                $message .= 'settlestatus: ' . $this->request->get['settlestatus'] . "\n";
            }

            if (isset($this->request->get['transactionreference'])) {
                $message .= 'transactionreference: ' . $this->request->get['transactionreference'] . "\n";
            }
            if ($this->config->get('trustpayments_password')) {
                $keys = array_keys($this->request->get);
                sort($keys);

                $keys_ignore = array('route', 'notificationreference', 'responsesitesecurity');

                $string_to_hash = '';

                foreach ($keys as $key) {
                    if (!in_array($key, $keys_ignore)) {
                        $string_to_hash .= $this->request->get[$key];
                    }
                }
                $string_to_hash .= $this->config->get('trustpayments_password');

                $response_mismatch = "";
                if (!hash_equals(hash('sha256', $string_to_hash), $this->request->get['responsesitesecurity'])) {
                    $response_mismatch = $this->language->get('text_hash_mismatch');
                }
            } else {
                $response_mismatch = "";
            }
            if (!$order_info['order_status_id']) {
                $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
                if ($response_mismatch) {
                    $this->model_checkout_order->update($order_id, $this->config->get('trustpayments_order_status_id'), $response_mismatch, false);
                }
            } elseif ($order_info['order_status_id'] == $this->config->get('trustpayments_waiting_order_status_id')) {
                $this->model_checkout_order->update($order_id, $this->config->get('trustpayments_order_status_id'), $message, false);
                if ($response_mismatch) {
                    $this->model_checkout_order->update($order_id, $this->config->get('trustpayments_order_status_id'), $message . $response_mismatch, false);
                }
            }
            $this->redirect($this->url->link('checkout/success'));
        } else {
            $this->redirect($this->url->link('checkout/checkoutetm'));
        }
    }

    public function callback() {
        $this->log->write('callback : ' . print_r($this->request->post, true));
        $this->log->write('callback : ' . print_r($this->request->get, true));
        if (isset($this->request->get['acd']) && isset($this->request->post['orderreference'])) {
            $this->load->model('checkout/order');
            $this->language->load('payment/trustpayments');
            $order_id = $this->request->post['orderreference'];
            $order_info = $this->model_checkout_order->getOrder($order_id);

            $message = '';

            if (isset($this->request->post['errorcode'])) {
                $message .= 'errorcode: ' . $this->request->post['errorcode'] . "\n";
            }

            if (isset($this->request->post['orderreference'])) {
                $message .= 'orderreference: ' . $this->request->post['orderreference'] . "\n";
            }

            if (isset($this->request->post['requestreference'])) {
                $message .= 'requestreference: ' . $this->request->post['requestreference'] . "\n";
            }

            if (isset($this->request->post['settlestatus'])) {
                $message .= 'settlestatus: ' . $this->request->post['settlestatus'] . "\n";
            }

            if (isset($this->request->post['transactionreference'])) {
                $message .= 'transactionreference: ' . $this->request->post['transactionreference'] . "\n";
            }
            if ($this->config->get('trustpayments_password')) {
                $keys = array_keys($this->request->post);
                sort($keys);
                $keys_ignore = array('notificationreference', 'responsesitesecurity');
                $string_to_hash = '';
                foreach ($keys as $key) {
                    if (!in_array($key, $keys_ignore)) {
                        $string_to_hash .= $this->request->post[$key];
                    }
                }
                $string_to_hash .= $this->config->get('trustpayments_password');

                $response_mismatch = "";
                if (!hash_equals(hash('sha256', $string_to_hash), $this->request->post['responsesitesecurity'])) {
                    $response_mismatch = $this->language->get('text_hash_mismatch');
                }
            } else {
                $response_mismatch = "";
            }
            if ($this->request->get['acd'] == 'Y') {
                if (!$order_info['order_status_id'] || $order_info['order_status_id'] == $this->config->get('trustpayments_waiting_order_status_id')) { // Missing order
                    $this->model_checkout_order->confirm($order_id, $this->config->get('trustpayments_order_status_id'));
                    if ($response_mismatch) {
                        $this->model_checkout_order->update($order_id, $this->config->get('trustpayments_order_status_id'), $response_mismatch, false);
                    }
                }
                $this->model_checkout_order->update($order_id, $this->config->get('trustpayments_order_status_id'), $message, false);
            } else {
                $this->log->write(print_r($this->request->post, true));
            }
        }
        echo "OK";
        die();
    }

}

?>