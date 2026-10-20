<?php

require_once DIR_SYSTEM . 'vendor/handpoint_network/gateway.php';

class ControllerPaymentHandpointNetwork extends Controller {

    private $log;
    private $gateway;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('handpoint.log');

        $this->gateway = new \P3\SDK\Gateway();
        $this->gateway::$merchantID = $this->config->get('handpoint_network_merchant_id');
        $this->gateway::$merchantSecret = html_entity_decode($this->config->get('handpoint_network_merchantsecret')); 
        $this->gateway::$directUrl = $this->getGatewayURL('direct');
        $this->gateway::$hostedUrl = $this->getGatewayURL('Hosted');
    }

    protected function index() {

        $integrationType = $this->config->get('handpoint_network_module_type');

        if (in_array($integrationType, ['hosted', 'hosted_v2', 'hosted_v3'], true)) {
            return $this->createHostedForm($integrationType);
        }

        if (in_array($integrationType, ['hostedfields'], true)) {
            return $this->createHostedFieldsForm($integrationType);
        }

        if (in_array($integrationType, ['direct'], true)) {
            return $this->createDirectForm();
        }
    }

    private function createHostedForm($type = 'hosted') {
        $parameters = $this->captureOrder();
        $parameters['callbackURL'] = $this->url->link('payment/handpoint_network/callback', '', 'SSL'); // 'XDEBUG_SESSION_START=session_name'
        $parameters['redirectURL'] = $this->url->link('payment/handpoint_network/hostedredirect', '', 'SSL'); // 'XDEBUG_SESSION_START=session_name';


        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['includeDeviceData'] = true;
        $this->data['isHostedModal'] = $type === 'hosted_v3';


        if (in_array($type, ['hosted', 'hosted_v3'], true)) {

            if ($type === 'hosted_v3') {
                // $this->data['form_hosted_modal_url'] =  $this->getGatewayURL('hosted_v3');
                $parameters['hostedUrl'] = $this->getGatewayURL('hosted_v3');
            } else {
                // $this->data['form_hosted_url'] =  $this->getGatewayURL('hosted');
                $parameters['hostedUrl'] = $this->getGatewayURL('hosted');
            }
            $this->data['formhtml'] = $this->gateway::hostedRequest($parameters, ['submitAttrs' => 'id="button-confirm" class="btn btn-primary button"']);
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/handpoint_network_hosted.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/handpoint_network_hosted.tpl';
            } else {
                $this->template = 'default/template/payment/handpoint_network_hosted.tpl';
            }
        }

        if (in_array($type, ['hosted_v2'], true)) {
            // $this->data['form_hosted_url'] = $this->getGatewayURL('hosted_v2');
            $parameters['hostedUrl'] = $this->getGatewayURL('hosted_v3');
            $this->data['formhtml'] = $this->gateway::hostedRequest($parameters, ['formAttrs' => 'name="payment-form"']);
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/handpoint_network_iframe.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/handpoint_network_iframe.tpl';
            } else {
                $this->template = 'default/template/payment/handpoint_network_iframe.tpl';
            }
        }
        $this->render();
    }

    private function createDirectForm() {
        $this->data['cc_card_number'] = $this->language->get('cc_card_number');
        $this->data['cc_card_start_date'] = $this->language->get('cc_card_start_date');
        $this->data['cc_card_expiry_date'] = $this->language->get('cc_card_expiry_date');
        $this->data['cc_card_cvv'] = $this->language->get('cc_card_cvv');
        $this->data['text_credit_card'] = $this->language->get('text_credit_card');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['process_url'] = $this->url->link('payment/handpoint_network/process_direct_request', '', 'SSL');

        $this->document->addScript('catalog/view/javascript/payform.js');

        $deviceData = [
            'deviceChannel' => 'browser',
            'deviceIdentity' => (isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : null),
            'deviceTimeZone' => '0',
            'deviceCapabilities' => '',
            'deviceScreenResolution' => '1x1x1',
            'deviceAcceptContent' => (isset($_SERVER['HTTP_ACCEPT']) ? htmlentities($_SERVER['HTTP_ACCEPT']) : null),
            'deviceAcceptEncoding' => (isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? htmlentities($_SERVER['HTTP_ACCEPT_ENCODING']) : null),
            'deviceAcceptLanguage' => (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? htmlentities($_SERVER['HTTP_ACCEPT_LANGUAGE']) : null),
            'deviceAcceptCharset' => (isset($_SERVER['HTTP_ACCEPT_CHARSET']) ? htmlentities($_SERVER['HTTP_ACCEPT_CHARSET']) : null),
        ];

        $monthOptions = [];
        foreach (range(1, 12) as $value) {
            $monthOptions[str_pad($value, 2, '0', STR_PAD_LEFT)] = $value;
        }


        $yearOptions = [];

        foreach (range(date("Y"), date("Y") + 12) as $value) {
            $yearOptions[substr($value, 2)] = $value;
        }

        $this->data['deviceData'] = $deviceData;
        $this->data['monthOptions'] = $monthOptions;
        $this->data['yearOptions'] = $yearOptions;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/handpoint_network_direct.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/handpoint_network_direct.tpl';
        } else {
            $this->template = 'default/template/payment/handpoint_network_direct.tpl';
        }
        $this->render();
    }

    public function process_direct_request(): array {
        $redirect_url = $this->url->link('payment/handpoint_network/callback', 'XDEBUG_SESSION_START=session_name', 'SSL'); // .'&XDEBUG_SESSION_START=session_name';

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cardNumber'], $_POST['cardExpiryMonth'], $_POST['cardExpiryYear'], $_POST['cardCVV'], $_POST['browserInfo'])
        ) {
            $args = array_merge(
                    $this->captureOrder(),
                    $_POST['browserInfo'],
                    [
                        'cardNumber' => str_replace(' ', '', $_POST['cardNumber']),
                        'cardExpiryMonth' => $_POST['cardExpiryMonth'],
                        'cardExpiryYear' => $_POST['cardExpiryYear'],
                        'cardCVV' => $_POST['cardCVV'],
                        'threeDSRedirectURL' => $redirect_url,
                        'threeDSVersion' => 2,
                    ]
            );

            $response = $this->gateway->directRequest($args);
            setcookie('xref', $response['xref'], time() + 315);

            $this->callback($response);
        }

        throw new InvalidArgumentException('Something went wrong with processing direct request, please check $_POST data');
    }

    private function createHostedFieldsForm() {
        
        $this->data['process_url'] = str_replace('&amp;', '&',$this->url->link('payment/handpoint_network/process_fields_request', 'od='.$this->session->data['order_id'], 'SSL'));

        $deviceData = [
            'deviceChannel' => 'browser',
            'deviceIdentity' => (isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : null),
            'deviceTimeZone' => '0',
            'deviceCapabilities' => '',
            'deviceScreenResolution' => '1x1x1',
            'deviceAcceptContent' => (isset($_SERVER['HTTP_ACCEPT']) ? htmlentities($_SERVER['HTTP_ACCEPT']) : null),
            'deviceAcceptEncoding' => (isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? htmlentities($_SERVER['HTTP_ACCEPT_ENCODING']) : null),
            'deviceAcceptLanguage' => (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? htmlentities($_SERVER['HTTP_ACCEPT_LANGUAGE']) : null),
            'deviceAcceptCharset' => (isset($_SERVER['HTTP_ACCEPT_CHARSET']) ? htmlentities($_SERVER['HTTP_ACCEPT_CHARSET']) : null),
        ];

        $this->data['deviceData'] = $deviceData;
        $this->data['merchant_id'] = $this->config->get('handpoint_network_merchant_id');
        $this->data['hostedfieldurl'] = str_replace('&amp;', '&',$this->url->link('payment/handpoint_network/process_fields_request', 'od='.$this->session->data['order_id'], 'SSL'));
        
        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        
        $bill_name = str_replace('&amp;', '&', $order['payment_firstname'] . ' ' . $order['payment_lastname']);

        $bill_address = str_replace('&amp;', '&', $order['payment_address_1']);
        $addressFields = [
            'payment_address_2',
        //    'payment_city',
      //      'payment_zone',
         //   'payment_country'
        ];
        $bill_city =  str_replace('&amp;', '&', $order['payment_city']);
        $bill_country_code =  str_replace('&amp;', '&', $order['payment_iso_code_2']);
        foreach ($addressFields as $item) {
            if (!empty($order[$item])) {
                $bill_address .= ", " . str_replace('&amp;', '&', $order[$item]);
            }
        }
        $this->data['customerName'] = $bill_name;
        $this->data['customerAddress'] = $bill_address;
        $this->data['customerTown'] = $bill_city;
        $this->data['customerPostCode'] = trim($order['payment_postcode']);
        $this->data['customerEmail'] =  $order['email'];
        $this->data['customerPhone'] = @$order['telephone'];
        
        $this->data['customerFirstname'] = $order['payment_firstname'] ;
        $this->data['customerLastname'] = $order['payment_lastname'];
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/handpoint_network_hostedfields.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/handpoint_network_hostedfields.tpl';
        } else {
            $this->template = 'default/template/payment/handpoint_network_hostedfields.tpl';
        }
        
        $this->log->write('setp 1 ' ); 
        $this->render();
    }

    public function process_fields_request() {
        
        $this->language->load('payment/handpoint_network');
        // $this->log->write('post $post :' . print_r($this->request->post, true));

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_id = $this->request->get['od'];
            $redirect_url = str_replace('&amp;', '&',$this->url->link('payment/handpoint_network/callback', 'XDEBUG_SESSION_START=session_name', 'SSL')); // .'&XDEBUG_SESSION_START=session_name';
            if (!isset($this->request->post['threeDSResponse']) && isset($this->request->post['paymentToken'])) {
                // Initial request
                $this->log->write('setp 2 ' ); 
                $req = array_merge(
                        $this->captureOrder(),
                        $_POST['browserInfo'],
                        [
                            'paymentToken' => $this->request->post['paymentToken'],
                            'duplicateDelay' => '60',
                            'callbackURL' => $this->url->link('payment/handpoint_network/callback', '', 'SSL'),
                            // The following fields are mandatory for 3DS v2
                            'remoteAddress' => $_SERVER['REMOTE_ADDR'],
                            'threeDSRedirectURL' => str_replace('&amp;', '&',$this->url->link('payment/handpoint_network/process_fields_request', 'od='.$order_id, 'SSL')),
                        // The following field allows options to be passed for 3DS v2
                        // and the values here are for demonstration purposes only
                        /* 'threeDSOptions' => array(
                          'paymentAccountAge' => '20221211',
                          'paymentAccountAgeIndicator' => '05',
                          ),
                         * 
                         */
                        ]
                );
                $this->log->write('API REQ : '. print_r($req,true));
                $res = $this->gateway::directRequest($req);
                $this->log->write('Field $response :' . print_r($res, true));
            } else {
                // 3DS response
                $this->log->write('setp 3  $post :' . print_r($this->request->post, true));
              //  $this->request->post['threeDSResponse'] = ['threeDSMethodData' => $this->request->post['threeDSMethodData']];
                $this->load->model('payment/handpoint_network');
                $threeDSRef = $this->model_payment_handpoint_network->getThreeDSRef($order_id);
                $req = array(
                    // The following field are only required for tbe benefit of the SDK 
                    'merchantID' => $this->config->get('handpoint_network_merchant_id'),
                    'action' => 'SALE',
                    //'threeDSRef' => $_SESSION['threeDSRef'],
                   // 'threeDSRef' => $_COOKIE['threeDSRef'],
                     'threeDSRef' => $threeDSRef,
                    'threeDSResponse' => $this->request->post,
                );
                $this->log->write('API REQ 3DS: '. print_r($req,true));
                $res = $this->gateway::directRequest($req);
               
               
                $this->log->write('Field 3DS $response :' . print_r($res, true));
            }

         
            // print $res['responseCode'];
            // Check the response code
            if ($res['responseCode'] === $this->gateway::RC_3DS_AUTHENTICATION_REQUIRED) {
                // Send request to the ACS server displaying response in an IFRAME
                // Silently POST the 3DS request to the ACS in the IFRAME
                // Remember the threeDSRef as need it when the ACS responds
                // setcookie('threeDSRef', $res['threeDSRef'], time() + 500);
                // SAVE TO DB
                $this->load->model('payment/handpoint_network');
                $this->model_payment_handpoint_network->setThreeDSRef($order_id,$res['threeDSRef']);
                //  $_SESSION['threeDSRef'] = $res['threeDSRef'];
                $this->response->setOutput($this->silentPost($res['threeDSURL'], $res['threeDSRequest']));

                //$this->response->setOutput(json_encode(['html' => $this->silentPost($res['threeDSURL'], $res['threeDSRequest'],'threeds_acs')]));
                return;
            } else if ($res['responseCode'] === $this->gateway::RC_SUCCESS) {

                // echo "<p>Thank you for your payment.</p>";
                $this->on_successful_transaction($res);
                return;
            } else {
                // echo "<p>Failed to take payment: " . htmlentities($res['responseMessage']) . "</p>";
                $this->log->write("Failed to take payment: " . htmlentities($res['responseMessage']));
                if(isset($res["addressCheck"]) && ($res["addressCheck"]== "not matched" || $res["postcodeCheck"] == "not matched")) {
                     $this->session->data['handpoint_error']= $this->language->get('error_address_error');;
                }
                else {
                    $this->session->data['handpoint_error']= $res['responseMessage'];
                }
                $this->response->redirect($this->url->link('payment/handpoint_network/failure', '', 'SSL'));
                //$this->response->setOutput(json_encode(['location' => $url]));
                return;
            }

            // $this->callback($response);
        } else {
            $this->log->write(' process_fields_request failed POST');
            $this->session->data['handpoint_error']=  $this->language->get('error_technical_error');
            $this->response->redirect($this->url->link('payment/handpoint_network/failure', '', 'SSL'));
            // $this->response->setOutput(json_encode(['location' => $url]));
            return;
        }
    }

    public function hostedredirect($response = null) {
        $this->log->write('hostedredirect $post :' . print_r($this->request->post, true));
        $merchant_id = $this->config->get('handpoint_network_merchant_id');

        // v1
        if (isset($_REQUEST['MD'], $_REQUEST['PaRes'])) {
            $req = array(
                'action' => 'SALE',
                'merchantID' => $merchant_id,
                'xref' => $_COOKIE['xref'],
                'threeDSMD' => $_REQUEST['MD'],
                'threeDSPaRes' => $_REQUEST['PaRes'],
                'threeDSPaReq' => ($_REQUEST['PaReq'] ?? null),
            );

            $response = $this->gateway::directRequest($req);
        }

        // v2
        if (isset($_POST['threeDSMethodData']) || isset($_POST['cres'])) {
            $req = array(
                'merchantID' => $merchant_id,
                'action' => 'SALE',
                // The following field must be passed to continue the 3DS request
                'threeDSRef' => $_COOKIE['threeDSRef'],
                'threeDSResponse' => $_POST,
            );

            $response = $this->gateway::directRequest($req);
        }

        $data = $response ?? $_POST;

        try {
            //  $this->createWallet($data);

            $this->gateway::verifyResponse($data);
            // Check the response code
            if ($data['responseCode'] === $this->gateway::RC_3DS_AUTHENTICATION_REQUIRED) {

                // Send request to the ACS server
                $threeDSVersion = (int) str_replace('.', '', $data['threeDSVersion']);

                return $onThreeDSRequired($threeDSVersion, $response);
            } else if ($data['responseCode'] === $this->gateway::RC_SUCCESS) {
                $this->on_successful_transaction($data);
            } else {
                // Signature mismatch
                throw new \RuntimeException("Failed to take payment: " . htmlentities($response['responseMessage']));
            }
        } catch (\Exception $exception) {

            $url = $this->url->link('checkout/checkoutetm', '', true);
            $this->redirectUrl($url);
        }
    }

    public function callback() {
        $this->log->write('callback  ');
        //   $this->log->write('callback  $post :' . print_r($this->request->post, true));
        $response = $this->request->post;

        try {

            $this->gateway::verifyResponse($response);
            // Check the response code
            if ($response['responseCode'] === $this->gateway::RC_SUCCESS) {
                $this->on_successful_transaction($response);
            } else {
                // Signature mismatch
                throw new \RuntimeException("Failed to take payment: " . htmlentities($response['responseMessage']));
            }
        } catch (\Exception $exception) {

//            $url = $this->url->link('checkout/checkoutetm', '', true);
//            $this->redirectUrl($url);
        }
    }

    /**
     * @return array
     */
    private function captureOrder() {
        $merchant_id = $this->config->get('handpoint_network_merchant_id'); // $this->config->get( 'handpoint_network_merchantid');
        $form_responsive = $this->config->get('handpoint_network_form_responsive');

        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        // $amount = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
        // $amount = \P3\SDK\AmountHelper::calculateAmountByCurrency($amount, $order['currency_code']);
        $amount = (int) strval($this->currency->format($order['total'], $order['currency_code'], 1.00000, false) * 100);

        $trans_id = $this->session->data['order_id'];
        
//        if(isset($this->request->post['customerName']) && utf8_strlen($this->request->post['customerName'])>3 ) {
//            $bill_name = str_replace('&amp;', '&', trim($this->request->post['customerName']));
        if(isset($this->request->post['customerFirstname']) && utf8_strlen($this->request->post['customerFirstname'])>2 && isset($this->request->post['customerLastname']) && utf8_strlen($this->request->post['customerLastname'])>2) {
            $bill_name = str_replace('&amp;', '&', trim($this->request->post['customerFirstname']. ' ' . $this->request->post['customerLastname']));
        }else {
        $bill_name = str_replace('&amp;', '&', $order['payment_firstname'] . ' ' . $order['payment_lastname']);
        }

        if(isset($this->request->post['customerTown']) && utf8_strlen($this->request->post['customerTown'])>3 ) {
            $bill_city =  str_replace('&amp;', '&', trim($this->request->post['customerTown']));
        }else {
            $bill_city =  str_replace('&amp;', '&', $order['payment_city']);
        }
        if(isset($this->request->post['customerPostCode']) && utf8_strlen($this->request->post['customerPostCode'])>3 ) {
            $bill_postcode =   trim($this->request->post['customerPostCode']);
        }else{
            $bill_postcode =   $order['payment_postcode'];
        }
        
        $bill_country_code =  str_replace('&amp;', '&', $order['payment_iso_code_2']);
        
        if(isset($this->request->post['customerAddress']) && utf8_strlen($this->request->post['customerAddress'])>5 ) {
            $bill_address = str_replace('&amp;', '&', trim($this->request->post['customerAddress']));
            $bill_address .= ", " . $bill_city . ", " . str_replace('&amp;', '&', $order['payment_country']);
        }else {
        $bill_address = str_replace('&amp;', '&', $order['payment_address_1']);
        $addressFields = [
            'payment_address_2',
            'payment_city',
      //      'payment_zone',
            'payment_country'
        ];

        foreach ($addressFields as $item) {
            if (!empty($order[$item])) {
                $bill_address .= ", " . str_replace('&amp;', '&', $order[$item]);
            }
        }
        }
        $bill_country_code =  str_replace('&amp;', '&', $order['payment_iso_code_2']);
        if(empty($bill_country_code)) {
            $bill_country_code ='GB';
        }

        $params = array(
            "action" => "SALE",
            "merchantID" => $merchant_id,
            "amount" => $amount,
            'type' => 1,
            "countryCode" => $bill_country_code,
            "currencyCode" => $order['currency_code'],
            "transactionUnique" => $trans_id,
            "orderRef" => "Order " . $trans_id,
            "customerName" => $bill_name,
            "customerAddress" => $bill_address,
            "customerTown"=> $bill_city,
            "customerCountryCode" => $bill_country_code,
            "customerPostCode" =>$bill_postcode,
            "customerEmail" => $order['email'],
            "customerPhone" => @$order['telephone'],
//            "item1Description" => "Order " . $trans_id,
//            "item1Quantity" => 1,
//            "item1GrossValue" => $amount,
            "formResponsive" => $form_responsive,
//            'merchantCategoryCode' => 5411,
//            'customerOCSESSID' => session_id() // $this->session->getId()
        );

        if (filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $params['remoteAddress'] = $_SERVER['REMOTE_ADDR'];
        }

        /**
         * Wallets
         */
        if (
                $this->config->get('handpoint_network_customer_wallets') === 'Y' && $this->config->get('handpoint_network_module_type') === 'hosted_v3' && $this->customer->isLogged()
        ) {

            $table_name = DB_PREFIX . 'payment_network_wallets';
            $sql = "SELECT * FROM `$table_name` WHERE merchant_id = '{$this->db->escape($params['merchantID'])}' AND customer_email = '{$this->db->escape($params['customerEmail'])}'";

            $wallets = $this->db->query($sql);

            //If the customer wallet record exists.
            if ($wallets->num_rows > 0) {
                //Add walletID to request.
                $params['walletID'] = $wallets->rows[0]['wallet_id'];
            } else {
                //Create a new wallet.
                $params['walletStore'] = 'Y';
            }
            $params['walletEnabled'] = 'Y';
            $params['walletRequired'] = 'Y';
        }

        return $params;
    }

    /**
     * Wallet creation.
     *
     * A wallet will always be created if a walletID is returned. Even if payment fails.
     */
    protected function createWallet($response) {
        $this->session->start($response['customerOCSESSID']);
        $this->registry->set('customer', new Cart\Customer($this->registry));

        //when the wallets is enabled, the user is logged in and there is a wallet ID in the response.
        if ($this->config->get('handpoint_network_customer_wallets') === 'Y' && $this->config->get('handpoint_network_module_type') === 'hosted_v3' && $this->customer->isLogged() && isset($response['walletID'])) {

            $walletID = $this->db->escape($response['walletID']);
            $merchantID = $this->config->get('handpoint_network_merchant_id');
            $customerEmail = $this->db->escape($this->customer->getEmail());

            $table_name = DB_PREFIX . 'payment_network_wallets';

            $sql = "SELECT * FROM `$table_name` WHERE merchant_id = '$merchantID' AND customer_email = '$customerEmail'";
            $wallets = $this->db->query($sql);

            //If the customer wallet record does not exists.
            if ($wallets->num_rows == 0) {
                //Add walletID to request.
                $sql = "INSERT INTO `$table_name` (`merchant_id`, `customer_email`, `wallet_id`) VALUES ('$merchantID', '$customerEmail', '$walletID');";
                $this->db->query($sql);
            }
        }
    }

    public function on_successful_transaction($data) {
        $this->log->write('on_successful : ' . print_r($data, true));
        $this->load->model('checkout/order');
        $orderId = $data['transactionUnique'];
        $order = $this->model_checkout_order->getOrder($orderId);

        if ($order['order_status_id'] == 0) {
            $msg = "Payment " . ($data['responseCode'] == 0 ? "Successful" : "Unsuccessful") . "<br/><br/>" .
                    "Amount Received: " . (isset($data['amountReceived']) ? floatval($data['amountReceived']) / 100 : 'Unknown') . "<br/>" .
                    "Message: \"" . ucfirst($data['responseMessage']) . "\"</br>" .
                    "cardType: " . (isset($data['cardType']) ? $data['cardType'] : 'Unknown') . "<br/>" .
                    "cardScheme: " . (isset($data['cardScheme']) ? $data['cardScheme'] : 'Unknown') . "<br/>" .
                    "cardTypeCode: " . (isset($data['cardTypeCode']) ? $data['cardTypeCode'] : 'Unknown') . "<br/>" .
                    "cardSchemeCode: " . (isset($data['cardSchemeCode']) ? $data['cardSchemeCode'] : 'Unknown') . "<br/>" .
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

            $this->model_checkout_order->confirm($orderId, $this->config->get('handpoint_network_order_status'), '');
            $this->model_checkout_order->update($orderId, $this->config->get('handpoint_network_order_status'), $msg, false);
        }
        $url = $this->url->link('checkout/success', '', 'SSL');

        if ($this->config->get('handpoint_network_module_type') == 'hostedfields') {
            // $this->response->setOutput(json_encode(['location' => $url]));.
            $this->response->redirect($url);
        } else {
            $this->redirectUrl($url);
        }
    }

    /**
     * @param string $url
     */
    public function redirectUrl(string $url): void {
        $isIframe = $this->config->get('handpoint_network_module_type') == 'hosted_v2';
        if ($isIframe) {
            $this->response->setOutput("<script>top.location = '$url';</script>");
        } else {
            $this->response->redirect($url);
        }
    }

    /**
     * Get Gateway URL
     * 
     * This function get the correct getway URL based on the intergration type
     * 
     * @param string $intergrationType
     */
    protected function getGatewayURL(string $intergrationType) {

        // Get base URL and trim any ending slashes
        $baseURL = $this->config->get('handpoint_network_gateway_url');
        $baseURL = trim($baseURL, '/\\');

        // Switch on the intergration type to retrieve the correct URL
        switch ($intergrationType) {
            // Hosted form intergration names
            case 'Hosted' :
            case 'hosted' :
            case 'hosted_v2' :
                // If paymentform/* is in the base URL just return
                // it as is. Other wise append /hosted/
                if (preg_match('/paymentform(\/.*)?/', $baseURL)) {
                    return $baseURL;
                } else {
                    return $baseURL . '/hosted/';
                }
                break;
            // HFv2 intergration names
            case 'hfv2' :
            case 'Modal' :
            case 'hosted_v3' :
                $url = preg_replace('/paymentform(\/.*)?/', '', $baseURL, 1);
                $url = trim($url, '/\\');
                return $url . '/hosted/modal/';
            case 'direct':
                return $baseURL . '/direct/';
                break;
            default :
                throw new \Exception('Intergration type unknown');
                break;
        }
    }

    // Render HTML to silently POST data to URL in target brower window 
    private function silentPost($url = '?', array $post = null, $target = '_self') {
        $url = htmlentities($url);
        $target = htmlentities($target);
        $fields = '';

        if ($post) {
            foreach ($post as $name => $value) {
                $fields .= $this->gateway::fieldToHtml($name, $value);
            }
        }
        $style = (isset($res['threeDSRequest']['threeDSMethodData']) ? 'display: none;' : '');
        //$ret = "<iframe name=\"threeds_acs\" style=\"height:420px; width:420px; {$style}\"></iframe>\n";

        $ret = " Loading ... 
 <form id=\"silentPost\" action=\"{$url}\" method=\"post\" target=\"{$target}\">
 {$fields}
 <noscript><input type=\"submit\" value=\"Continue\"></noscript>
 </form>
 <script>
 window.setTimeout('document.forms.silentPost.submit()', 0);
 </script>
 ";

        return $ret;
    }
    
    public function failure() {
        $this->language->load('payment/handpoint_network');
        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] 			 = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language']  		 = $this->language->get('code');
        $this->data['direction'] 		 = $this->language->get('direction');
        //Page titles
        $this->data['title'] 			 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] 	 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['text_response'] 	 = $this->language->get('text_response');
        $this->data['error_response'] 	 = isset($this->session->data['handpoint_error'])?$this->session->data['handpoint_error']:'';
        //Error text
        $this->data['text_failure']      = $this->language->get('text_failure');
        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkoutetm'));
        //Mismatch
        $this->data['continue'] = $this->url->link('checkout/checkoutetm');
        $this->template = $this->config->get('config_template') . '/template/payment/handpoint_network_failure.tpl';
        $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
        );
        unset($this->session->data['handpoint_error']);
        $this->response->setOutput($this->render());
    }
 }