<?php

ini_set("display_errors", 0);

class ControllerPaymentVivawallet extends Controller {
    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('vivawallet.log');
    }
    protected function index() {
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $vivawallet_merchantreference = 'REF' . substr(md5(uniqid(rand(), true)), 0, 9);
        $TmSecureKey = 'd2ViaXQuYnovbGljZW5zZS50eHQ='; // for extra encryption options
        $vivawallet_total_eur = $this->currency->format($order_info['total'], strtoupper($order_info['currency_code']), '', false);
        $vivawallet_total_cents = round($vivawallet_total_eur * 100);
        $vivawallet_orderid = $this->session->data['order_id'];

        if (strtoupper($this->language->get('code')) == 'GR' || strtoupper($this->language->get('code')) == 'EL') {
            $vivawallet_language = 'el-GR';
        } else {
            $vivawallet_language = 'en-US';
        }

        
        $client_id = $this->config->get('vivawallet_clientid');
        $client_secret = html_entity_decode($this->config->get('vivawallet_client_secret'));

        if($this->config->get('vivawallet_mode') == 'test'){
            $token_url = 'https://demo-accounts.vivapayments.com/connect/token';
            $order_url = 'https://demo-api.vivapayments.com/checkout/v2/orders'; 
            $vivawallet_url= 'https://demo.vivapayments.com/web/checkout';
        }else{
            $token_url = 'https://accounts.vivapayments.com/connect/token'; 
            $order_url = 'https://api.vivapayments.com/checkout/v2/orders'; 
            $vivawallet_url= 'https://www.vivapayments.com/web/checkout';
        }
        
       
        $token_data = [
            'grant_type' => 'client_credentials'
        ];

        $curl = curl_init($token_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($token_data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret),
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $token_response = curl_exec($curl);
        $this->log->write($token_response);
        if (curl_errno($curl)) {
            $this->log->write('Error fetching access token: ' . curl_error($curl));
            throw new Exception('Error fetching access token: ' . curl_error($curl));
        }
        curl_close($curl);

        $token_result = json_decode($token_response, true);
        if (!isset($token_result['access_token'])) {
            $this->log->write('Error fetching access token: ' . $token_response);
            throw new Exception('Error fetching access token: ' . $token_response);
        }

        $access_token = $token_result['access_token'];

        $currency_symbol = '';
        $currency_code = strtoupper($order_info['currency_code']);
        switch ($currency_code) {
            case 'EUR':
                $currency_symbol = 978;
                break;
            case 'GBP':
                $currency_symbol = 826;
                break;
            case 'BGN':
                $currency_symbol = 975;
                break;
            case 'RON':
                $currency_symbol = 946;
                break;
            default:
                $currency_symbol = 978;
        }

            // Step 2: Create a payment order
        
        $order_data = [
            'amount' => $vivawallet_total_cents, // Amount in cents
            'customerTrns' => 'Payment for Order #' . $vivawallet_orderid,
            'customer' => [
                'email' => $order_info['email'],
                'phone' => $order_info['telephone'],
                'fullName' => $order_info['firstname'] . ' ' . $order_info['lastname']
            ],
            'sourceCode' => $this->config->get('vivawallet_source'),
            'merchantTrns' => $vivawallet_orderid,
            'currencyCode' => $currency_symbol,
            'paymentTimeout' => 300
        ];

        $curl = curl_init($order_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($order_data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $this->log->write('Error creating payment order: ' . curl_error($curl));
            throw new Exception('Error creating payment order: ' . curl_error($curl));
        }
        curl_close($curl);

        $order_result = json_decode($response, true);
        if (!isset($order_result['orderCode']) || $order_result['ErrorCode'] != 0) {
            $this->log->write('Error creating payment order: ' . $response);
            throw new Exception('Error creating payment order: ' . $response);
            $OrderCode =     '';
            $ErrorCode =     $order_result['ErrorCode'];
            $ErrorText =     $order_result['ErrorText;'];
        }else {
            $ErrorCode = $order_result['ErrorCode'];
            $ErrorText = $order_result['ErrorText'];
            $OrderCode = $order_result['orderCode'];
        }
        
        $this->log->write('Payment order created successfully. Order Code: ' . $OrderCode);

       
        // Redirect customer to Smart Checkout
        // $checkout_url = 'https://www.vivapayments.com/web/checkout?ref=' . $OrderCode; // Use demo URL for testing
        // $this->response->redirect($checkout_url);

        $this->db->query("insert into oc_vivawallet_data (OrderCode, ErrorCode, ErrorText, Timestamp, ref, total_cost, currency, order_state) values ('" . $OrderCode . "','" . $ErrorCode . "','" . $ErrorText . "',now(),'" . $vivawallet_orderid . "','" . $vivawallet_total_cents . "','".$currency_symbol."','I')");


        $this->data['action'] = $vivawallet_url ; 
        $this->data['vivawallet_ordercode'] = $OrderCode;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/vivawallet.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/vivawallet.tpl';
        } else {
            $this->template = 'default/template/payment/vivawallet.tpl';
        }

        $this->render();
    }

    public function callback() {
        $this->log->write('VIVA: ' . $_SERVER['REQUEST_URI']);
        $this->log->write('VIVA POST: ' . print_r ($this->request->post,true));
//$this->load->language('payment/vivawallet');
        $this->language->load('payment/vivawallet');

        $this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

        if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
            $this->data['base'] = $this->config->get('config_url');
        } else {
            $this->data['base'] = $this->config->get('config_ssl');
        }

        $this->data['language'] = $this->language->get('code');
        $this->data['direction'] = $this->language->get('direction');
        $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['text_failure'] = $this->language->get('text_failure');
        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkoutetm', '', 'SSL'));

        //fail
        if (preg_match("/fail/i", $_SERVER['REQUEST_URI'])) {
            $tm_ref = $_GET['s'];

            if (isset($tm_ref) && $tm_ref != '') {

                $check_query = $this->db->query("select ref from oc_vivawallet_data where OrderCode='" . $this->db->escape($tm_ref) . "'");

                if ($check_query->rows) {
                    $oRecord = $check_query->rows[0];

                    $this->db->query("update oc_vivawallet_data set order_state='F' where OrderCode='" . $this->db->escape($tm_ref) . "'");
                }
            }

            $this->data['continue'] = $this->url->link('checkout/checkoutetm');
            $timeout = 10;    
            $this->data['timeout'] = $timeout * 1000;

            if (isset($oRecord['ref'])) {
                $order_id = $oRecord['ref'];
            } else {
                $order_id = 0;
            }

            if (!$this->customer->isLogged()) {
                $email_query = $this->db->query("SELECT email FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int) $order_id . "'");
                if ($email_query->num_rows) {
                    $this->customer->login($email_query->row['email'], '', true);
                }
            }

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/vivawallet_failure.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/vivawallet_failure.tpl';
            } else {
                $this->template = 'default/template/payment/vivawallet_failure.tpl';
            }

            $this->response->setOutput($this->render());
        }//end fail
        //success
        elseif (preg_match("/success/i", $_SERVER['REQUEST_URI'])) {

            $tm_ref = $_GET['s'];

            if (isset($tm_ref) && $tm_ref != '') {

                $check_query = $this->db->query("select ref from oc_vivawallet_data where OrderCode='" . $this->db->escape($tm_ref) . "'");

                if ($check_query->rows) {
                    $oRecord = $check_query->rows[0];

                    if (isset($oRecord['ref'])) {
                        $order_id = $oRecord['ref'];
                    } else {
                        $order_id = 0;
                    }

                    $this->db->query("update oc_vivawallet_data set order_state='P' where OrderCode='" . $this->db->escape($tm_ref) . "'");

                    $this->load->model('checkout/order');
                   // $order_info = $this->model_checkout_order->getOrder((int) $oRecord['ref']);
                    $this->model_checkout_order->confirm((int) $oRecord['ref'], $this->config->get('vivawallet_processed_status_id'));
                    $this->redirect($this->url->link('checkout/success'));
                }
            }
        }//end success	 
        else {
            //webhook
            $payload = @file_get_contents('php://input');
            $events = json_decode($payload, true);
            $this->log->write('CALLBACK :' . print_r($events,true));
            if(isset($events['EventData']['MerchantTrns'])){
                $order_id = $events['EventData']['MerchantTrns'];
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);
                if (!$order_info['order_status_id']) { // Missing order
                    $this->model_checkout_order->confirm($order_id, $this->config->get('vivawallet_processed_status_id'));
                } 
                echo 'OK';  // Return 200 OK to VIVA        
                exit();
            }else   { // url Verify call 
                $MerchantID = $this->config->get('vivawallet_merchantid');
                $Password = html_entity_decode($this->config->get('vivawallet_merchantpass'));  

                if($this->config->get('vivawallet_mode') == 'test'){
                    $environment_url = 'https://demo.vivapayments.com/api/messages/config/token';
                
                }else{
                    $environment_url = 'https://www.vivapayments.com/api/messages/config/token'; 

                }
                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => $environment_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic '. base64_encode($MerchantID . ':' . $Password)
                ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                echo $response;
                exit();
            }
        }// end  webhook
    }

//end callback
}

//end class
?>