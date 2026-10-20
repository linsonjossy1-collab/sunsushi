<?php

require_once DIR_SYSTEM . '/vendor/connect2pay/vendor/autoload.php';

use PayXpert\Connect2Pay\Connect2PayClient;
use PayXpert\Connect2Pay\containers\request\PaymentPrepareRequest;
use PayXpert\Connect2Pay\containers\Order;
use PayXpert\Connect2Pay\containers\Shipping;
use PayXpert\Connect2Pay\containers\Shopper;
use PayXpert\Connect2Pay\containers\Account;
use PayXpert\Connect2Pay\containers\constant\OrderShippingType;
use PayXpert\Connect2Pay\containers\constant\PaymentMethod;
use PayXpert\Connect2Pay\containers\constant\PaymentMode;
use PayXpert\Connect2Pay\containers\constant\AccountAge;
use PayXpert\Connect2Pay\containers\constant\AccountLastChange;
use PayXpert\Connect2Pay\containers\constant\AccountPaymentMeanAge;

class ControllerPaymentEpaymax extends Controller {

    private $connect2pay = "https://connect2.payxpert.com";
    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('epaymax.log');
    }

    public function index() {
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');

        // $this->data['action'] = $this->url->link('payment/epaymax/paymentredirect', '', 'SSL');

        $this->load->model('checkout/order');


        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $c2pClient = new Connect2PayClient($this->getGatewayUrl(), $this->config->get('epaymax_originator'), $this->getApiKey());

        $prepareRequest = new PaymentPrepareRequest();
        $shopper = new Shopper();
        // $account = new Account();
        $order = new Order();
        //  $shipping = new Shipping();
        // Set all information for the payment
        if($this->config->get('epaymax_seamless')) {
            $prepareRequest->setPaymentMethod(PaymentMethod::CREDIT_CARD);
        }
        $prepareRequest->setPaymentMode(PaymentMode::SINGLE);
        // To charge €25.99
        $prepareRequest->setCurrency($order_info['currency_code']);
        $prepareRequest->setAmount($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100);
        // Extra custom data that are returned with the payment status
        $ctrlCustomData = $this->getOrderIdentStringHash($order_info);
        $prepareRequest->setCtrlCustomData($ctrlCustomData);
        // Where the customer will be redirected after the payment
        $prepareRequest->setCtrlRedirectURL($this->url->link('payment/epaymax/returnpayment', '', 'SSL'));
        // URL on the merchant site that will receive the callback notification
        $prepareRequest->setCtrlCallbackURL($this->url->link('payment/epaymax/callback', '', 'SSL'));

        $order->setId($order_info['order_id']);
        $order->setShippingType(OrderShippingType::DIGITAL_GOODS);
        $OrderDescription =  html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8') . ' '. 
                $this->session->data['order_id'] . ' - ' . date('Y-m-d H:i:s P');
        $order->setDescription($OrderDescription);

        $shopper->setId($order_info['customer_id']);
        $shopper->setFirstName(html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'))
                ->setLastName(html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'));
        $shopper->setAddress1(trim(html_entity_decode($order_info['payment_address_1'] . " " . $order_info['payment_address_2'], ENT_QUOTES, 'UTF-8')));
        $shopper->setZipcode(html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8'))
                ->setCity(html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8'))
                ->setState(html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8'))
                ->setCountryCode($order_info['payment_iso_code_2']);
        // $shopper->setHomePhonePrefix("212")->setHomePhone("12345678");
        $shopper->setHomePhonePrefix(("44"))
                ->setHomePhone(html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8'));
        $shopper->setEmail(html_entity_decode($order_info['email'], ENT_QUOTES, 'UTF-8'));

        /*  $account->setAge(AccountAge::LESS_30_DAYS);
          $account->setDate("20210106");
          $account->setLastChange(AccountLastChange::LESS_30_DAYS);
          $account->setLastChangeDate("20210106");
          $account->setPaymentMeanAge(AccountPaymentMeanAge::LESS_30_DAYS);
          $account->setPaymentMeanDate("20210106");
          // Set 'true' to trigger SCA challenge
          $account->setSuspicious(false);

          $shipping->setName("Lady Gogo");
          $shipping->setAddress1("125 Main Street");
          $shipping->setZipcode("ABC-5678")->setState("NY")->setCity("New York")->setCountryCode("US");
          $shipping->setPhone(html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8'));

          $shopper->setAccount($account);
         * 
         */
        $prepareRequest->setShopper($shopper);
        $prepareRequest->setOrder($order);
        // $prepareRequest->setShipping($shipping);

        if ($this->config->get('epaymax_3dsecure')) {
            $prepareRequest->setSecure3d(true);
        }

        // Merchant notifications
        $merchantNotifications = $this->config->get('epaymax_merchant_notifications');
        if (isset($merchantNotifications) && $merchantNotifications != null) {
            if ($merchantNotifications == 'enabled') {
                $prepareRequest->setMerchantNotification(true);
                $prepareRequest->setMerchantNotificationTo($this->config->get('epaymax_merchant_notifications_to'));
                $prepareRequest->setMerchantNotificationLang($this->config->get('epaymax_merchant_notifications_lang'));
            } else if ($merchantNotifications == 'disabled') {
                $prepareRequest->setMerchantNotification(false);
            }
        }
        //   $this->log->write(print_r($prepareRequest,true));
        //  $this->log->write(print_r($c2pClient,true));
        if ($c2pClient->validate()) {
            $result = $c2pClient->preparePayment($prepareRequest);
            if ($result !== false) {
                // The customer token info returned by the payment page could be saved in session (may
                // be used later when the customer will be redirected from the payment page)
                $this->session->data['epaymax_MerchantToken'] = $result->getMerchantToken();

                // The merchantToken must also be used later to validate the callback to avoid that anyone
                // could call it and abusively validate the payment. It may be stored in local database for this.
                $this->data['epaymax_seamless'] = $this->config->get('epaymax_seamless');
                $this->data['seamless_action'] = $this->url->link('payment/epaymax/payment', '', 'SSL');
                $this->data['action'] = $c2pClient->getCustomerRedirectURL($result);
                $this->data['order_id'] = $this->session->data['order_id'];
                $this->data['epaymax_customerToken'] = $result->getCustomerToken();
                // Now redirect the customer to the payment page
                //  header('Location: ' . $c2pClient->getCustomerRedirectURL($result));
            } else {
                // echo "Payment preparation error occurred: " . $c2pClient->getClientErrorMessage() . "\n";
                $message = "ePayMax payment module (paymentredirect):\n Error in prepareTransaction: \n";
                $message .= "Order id:" . $order_info['order_id'] . "\n";
                $message .= "Result code:" . $c2pClient->getReturnCode() . "\n";
                $message .= "Preparation error occurred: " . $c2pClient->getClientErrorMessage() . "\n";
                $this->debugLog($c2pClient->getClientErrorMessage());
                $this->data['continue'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
                $timeout = "10";
                $this->data['timeout'] = $timeout * 1000;
                $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $timeout, $this->url->link('checkout/checkoutetm', '', 'SSL'));
                $this->data['text_failure'] = nl2br($message);
            }
        } else {
            $message = "ePayMax payment module (paymentredirect):\n Error in validate function: \n";
            $message .= "Order id:" . $order_info['order_id'] . "\n";
            $message .= "Validation error occurred: " . $c2pClient->getClientErrorMessage() . "\n";
            
            $this->debugLog($c2pClient->getClientErrorMessage());
            $this->data['continue'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
            $timeout = "10";
            $this->data['timeout'] = $timeout * 1000;
            $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $timeout, $this->url->link('checkout/checkoutetm', '', 'SSL'));
            $this->data['text_failure'] = nl2br($message);
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/epaymax.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/epaymax.tpl';
        } else {
            $this->template = 'default/template/payment/epaymax.tpl';
        }
        $this->render();
    }
    public function payment() {
        
        $this->data['epaymax_customerToken'] = $this->request->post["epaymax_customerToken"];
        $this->data['order_id'] = $this->request->post["order_id"];
        $this->data['back_action'] =  $this->url->link('checkout/checkoutetm', '', 'SSL');
        $this->data['returnseamless_action'] =  $this->url->link('payment/epaymax/returnseamlesspayment', '', 'SSL');
        
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/epaymax_seamless.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/epaymax_seamless.tpl';
        } else {
            $this->template = 'default/template/payment/epaymax_seamless.tpl';
        }

        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
    public function returnseamlesspayment() {
        $this->load->model('checkout/order');

        $this->load->language('payment/epaymax');

       // $merchantToken = $this->session->data['epaymax_MerchantToken'];
       // $data = $this->request->post["data"];
        $epaymax_resposnse = $this->request->post["epaymax_resposnse"];
        $order_id = $this->request->post["order_id"];
        $this->log->write($epaymax_resposnse);
        $epaymax_resposnse = json_decode(html_entity_decode($epaymax_resposnse));
        
        $this->log->write(print_r($epaymax_resposnse,true));
        $success = false;
        $message = "Unknow error";

        if ($epaymax_resposnse->transaction) {
          
          //  $message = "ePayMax payment module:\n";
            $message = "Received a new transaction status from returnpayment.\n";
            $message .= "Error code: " . $epaymax_resposnse->transaction->resultCode . "\n";
            $message .= "Message: " . $epaymax_resposnse->transaction->resultMessage . "\n";
            $message .= "Transaction ID: " . $epaymax_resposnse->transaction->transactionID . "\n";
            $message .= "Order ID: " . $order_id . "\n";

            $order_info = $this->model_checkout_order->getOrder($order_id);
            if ($order_info) {
                if ($epaymax_resposnse->transaction->resultCode == "000") {

                 //   $this->model_checkout_order->confirm($order_id, $this->config->get('epaymax_order_status_id'));
                 //   $this->model_checkout_order->update($order_id, $this->config->get('epaymax_order_status_id'), $message, false);

                    $success = true;
                } else {

                 //   $this->model_checkout_order->confirm($order_id, $this->config->get('epaymax_order_denied_status_id'));
                  //  $this->model_checkout_order->update($order_id, $this->config->get('epaymax_order_denied_status_id'), $message, false);
                }
                $this->debugLog($message);
            } else {
                $this->debugLog("ePayMax payment module (returnpayment): Unable to get order information: " . $order_id);
            }
        } else {
            $this->debugLog("ePayMax payment module (returnpayment): Unable to handleRedirectStatus with merchantToken: " . $merchantToken);
        }

        if ($success) {
            $this->redirect($this->url->link('checkout/success', '', 'SSL'));
        } else {
            $this->displayFailure($this->language->get('error_declined'), 5);
        }
    }
    
    public function returnpayment() {
        $this->load->model('checkout/order');

        $this->load->language('payment/epaymax');

        $merchantToken = $this->session->data['epaymax_MerchantToken'];
        $data = $this->request->post["data"];
        $customer = $this->request->post["customer"];
        $success = false;
        $message = "Unknow error";

      //  $this->log->write(' returnpayment : ' . print_r($this->request->post,true));
        // Setup the connection and redirect Status
        $c2pClient = new Connect2PayClient($this->getGatewayUrl(), $this->config->get('epaymax_originator'), $this->getApiKey());
        if ($c2pClient->handleRedirectStatus($data, $merchantToken)) {
            $status = $c2pClient->getStatus();
            $order_id = $status->getOrderID();

          //  $message = "ePayMax payment module:\n";
            $message = "Received a new transaction status from returnpayment.\n";
            $message .= "Error code: " . $c2pClient->getStatus()->getErrorCode() . "\n";
            $message .= "Message: " . $status->getErrorMessage() . "\n";
          //  $message .= "Transaction ID: " . $status->getTransactionID() . "\n";
            $message .= "Order ID: " . $order_id . "\n";

            $order_info = $this->model_checkout_order->getOrder($order_id);
            if ($order_info) {
                if ($status->getErrorCode() == 0) {

                 //   $this->model_checkout_order->confirm($order_id, $this->config->get('epaymax_order_status_id'));
                 //   $this->model_checkout_order->update($order_id, $this->config->get('epaymax_order_status_id'), $message, false);

                    $success = true;
                } else {

                 //   $this->model_checkout_order->confirm($order_id, $this->config->get('epaymax_order_denied_status_id'));
                  //  $this->model_checkout_order->update($order_id, $this->config->get('epaymax_order_denied_status_id'), $message, false);
                }
                $this->debugLog($message);
            } else {
                $this->debugLog("ePayMax payment module (returnpayment): Unable to get order information: " . $order_id);
            }
        } else {
            $this->debugLog("ePayMax payment module (returnpayment): Unable to handleRedirectStatus with merchantToken: " . $merchantToken);
        }

        if ($success) {
            $this->redirect($this->url->link('checkout/success', '', 'SSL'));
        } else {
            $this->displayFailure($this->language->get('error_declined'), 5);
        }
    }

    public function callback() {
        $this->load->model('checkout/order');
        $c2pClient = new Connect2PayClient($this->getGatewayUrl(), $this->config->get('epaymax_originator'), $this->getApiKey());
        if ($c2pClient->handleCallbackStatus()) {
            $status = $c2pClient->getStatus();
            $order_id = $status->getOrderID();
$this->log->write(' callback : ' . print_r($status,true));
           // $message = "ePayMax payment module:\n";
            $message = "Received a new transaction status callback from " . $_SERVER["REMOTE_ADDR"] . ".\n";
            $message .= "Error code: " . $c2pClient->getStatus()->getErrorCode() . "\n";

            if ($status->getErrorCode() >= 0) {
                $transaction = $status->getLastInitialTransactionAttempt();
                $message .= "Message: " . $status->getErrorMessage() . "\n";
                $message .= "Transaction ID: " . $transaction->getTransactionID() . "\n";
            }

            $message .= "Order ID: " . $order_id . "\n";
// $this->log->write(' callback : ' . print_r($message,true));
            $order_info = $this->model_checkout_order->getOrder($order_id);
            if ($order_info) {
                // Validate the call with ctrlCustomData
                $callValid = false;
                $hash = $this->getOrderIdentStringHash($order_info);
                if ($hash !== $status->getCtrlCustomData()) {
                    $message .= "Callback validation failed. Expected '" . $hash . "' but got '" . $status->getCtrlCustomData() . "'\n";
                } else {
                    $message .= "Callback validation success.\n";
                    $callValid = true;
                }

                if ($callValid) {
                    // Transaction error code
                    if ($status->getErrorCode() == 0) {

                        $this->model_checkout_order->confirm($order_id, $this->config->get('epaymax_order_status_id'));
                        $this->model_checkout_order->update($order_id, $this->config->get('epaymax_order_status_id'), $message, false);
                    } else {

                      //  $this->model_checkout_order->confirm($order_id, $this->config->get('epaymax_order_denied_status_id'));
                      //  $this->model_checkout_order->update($order_id, $this->config->get('epaymax_order_denied_status_id'), $message, false);
                    }

                    $this->sendCallbackSuccess();
                } else {
                    $this->sendCallbackError();
                }

                $this->debugLog($message);
            } else {
                $this->debugLog("ePayMax payment module (callback): Unable to get order information: " . $order_id);
                $this->sendCallbackError();
            }
        } else {
            $this->debugLog("ePayMax payment module (callback): Received an incorrect status from " . $_SERVER["REMOTE_ADDR"]);
            $this->sendCallbackError();
        }
    }

    private function displayFailure($message, $timeout = "10") {
        if ($this->config->get('epaymax_debuglog')) {
            $this->log->write(str_replace("\n", ", ", $message));
        }


        if ($this->config->get('epaymax_failure_page') > 0) {
            $this->language->load('payment/epaymax');

            $this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

            if ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) || (isset($this->request->server['HTTP_X_FORWARDED_PROTO']) && $this->request->server['HTTP_X_FORWARDED_PROTO'] == 'https')) {
                $this->data['base'] = HTTPS_SERVER;
            } else {
                $this->data['base'] = HTTP_SERVER;
            }

            $this->data['charset'] = $this->language->get('charset');
            $this->data['language'] = $this->language->get('code');
            $this->data['direction'] = $this->language->get('direction');

            $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

            $this->data['text_response'] = $this->language->get('text_response');
            $this->data['text_failure'] = nl2br($message);

            $this->data['timeout'] = $timeout * 1000;

            $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $timeout, $this->url->link('checkout/checkoutetm', '', 'SSL'));

            $this->data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/checkoutetm';

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/epaymax_failure.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/epaymax_failure.tpl';
            } else {
                $this->template = 'default/template/payment/epaymax_failure.tpl';
            }
            $this->response->setOutput($this->render());
        } else {
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
        }
    }

    /**
     * Send a response to mark this transaction as notified successfully
     */
    private function sendCallbackSuccess() {
        $response = array(
            "status" => "OK",
            "message" => "Status recorded"
        );
        header("Content-type: application/json");
        echo json_encode($response);
    }

    /**
     * Send a response to warn about a notification error
     */
    private function sendCallbackError() {
        $response = array(
            "status" => "KO",
            "message" => "Error handling the callback"
        );
        header("Content-type: application/json");
        echo json_encode($response);
    }

    private function getOrderIdentStringHash($order) {
        return hash('sha256',
                $this->getApiKey() . '**' . $order['ip'] . '**' . $order['date_added'] . '**' . $order['total'] . '**' . $order['store_id'] . '**' .
                $order['customer_id'] . '**' . $order['order_id'] . '**' . $this->getApiKey());
    }

    private function getGatewayUrl() {
        $url = ltrim(trim($this->config->get('epaymax_connect2pay_url')), "/");
        if (!empty($url)) {
            return $url;
        }
        return $this->connect2pay;
    }

    private function getApiKey() {
        return html_entity_decode($this->config->get('epaymax_password'), ENT_QUOTES, 'UTF-8');
    }

    private function debugLog($message) {
        if ($this->config->get('epaymax_debuglog')) {

            $this->log->write($message);
        }
    }

}

?>