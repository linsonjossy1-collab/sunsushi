<?php
class ControllerPaymentMyposVirtual extends Controller {

    /**
     * @var Log $log
     */
    private $log;

    private $isTest;
    private $testPrefix;
    private $keyindex;
    private $sid;
    private $wallet_number;
    private $private_key;
    private $public_key;
    private $version;
    private $action;
    private $logging;
    private $paymentParametersRequired;
    private $paymentMethod;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->log = new Log('mypos_virtual.log');

        $this->isTest = $this->config->get('mypos_virtual_test') === '1' ? true : false;
        $this->logging = $this->config->get('mypos_virtual_logging') === '1' ? true : false;
        $this->version = '1.4';

        if (!$this->isTest){
            $this->sid           = $this->config->get('mypos_virtual_production_sid');
            $this->wallet_number = $this->config->get('mypos_virtual_production_wallet_number');
            $this->private_key   = $this->config->get('mypos_virtual_production_private_key');
            $this->public_key    = $this->config->get('mypos_virtual_production_public_certificate');
            $this->action        = $this->config->get('mypos_virtual_production_url');
            $this->keyindex      = $this->config->get('mypos_virtual_production_keyindex');
	        $this->paymentParametersRequired = $this->config->get('mypos_virtual_production_ppr');
            $this->testPrefix = null;
            $this->paymentMethod = $this->config->get('mypos_virtual_production_payment_method');
        }
        else{
            $this->sid           = $this->config->get('mypos_virtual_developer_sid');
            $this->wallet_number = $this->config->get('mypos_virtual_developer_wallet_number');
            $this->private_key   = $this->config->get('mypos_virtual_developer_private_key');
            $this->public_key    = $this->config->get('mypos_virtual_developer_public_certificate');
            $this->action        = $this->config->get('mypos_virtual_developer_url');
            $this->keyindex      = $this->config->get('mypos_virtual_developer_keyindex');
	        $this->paymentParametersRequired = $this->config->get('mypos_virtual_developer_ppr');
            $this->testPrefix = 'ETM00'; // null; // $this->config->get('mypos_virtual_test_prefix');
            $this->paymentMethod = $this->config->get('mypos_virtual_developer_payment_method');
        }
    }

	public function index() {
        $this->language->load('payment/mypos_virtual');

        $data['action'] = $this->url->link('payment/mypos_virtual/process_order', '', true);
        $data['button_confirm'] = $this->language->get('button_confirm');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/mypos_virtual.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/mypos_virtual.tpl';
        } else {
            $this->template = 'default/template/payment/mypos_virtual.tpl';
        }

        $this->data = $data;

        $this->render();
	}

    public function process_order() {
        $this->load->model('checkout/order');
        $data['continue'] = $this->url->link('checkout/success');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        // $this->model_checkout_order->confirm($this->session->data['order_id'], 1, 'Awaiting payment', true);
        $order_info['products'] =  $this->model_checkout_order->getFormattedOrderProducts($this->session->data['order_id']);
        $data['post'] = $this->create_post($order_info);

        $this->language->load('module/cart');
       // $this->cart->clear();
        $this->log(print_r($data,true));
       // exit();
        $return = '<form name="mypos_virtual" action="' . $this->action . '" method="post">';
        foreach ($data['post'] as $k => $value) {
            $return .= '<input type="hidden" name="' . $k . '" value="' . $value . '">';
        }
        $return .= '
            </form>
            <script>
                document.mypos_virtual.submit();
            </script>
        ';
         $this->log(print_r($return,true));
        echo $return;
    }

    public function check_payment_status() {

        if (array_key_exists('mypos-check-payment-status', $this->request->post) && $this->request->post['mypos-check-payment-status']
            && array_key_exists('order_id', $this->request->post)) {
            $this->load->model('checkout/order');
            $order_id = $this->request->post['order_id'];
            $data = $this->create_check_status_data($this->request->post['order_id']);

            //open connection
            $ch = curl_init($this->action);

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $this->action);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,1);
            curl_setopt($ch, CURLOPT_HEADER ,0); // DO NOT RETURN HTTP HEADERS
            curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); // RETURN THE CONTENTS OF THE CALL
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // Timeout on connect (2 minutes)

            //execute post
            $result = curl_exec($ch);
            curl_close($ch);


            // Parse xml
            $post = json_decode($result, true);

            $order = $this->model_checkout_order->getOrder($order_id);

            if ($this->is_valid_signature($post) && array_key_exists('PaymentStatus', $post)
                && array_key_exists('Amount', $post) && $post['Amount'] == $this->currency->format($order['total'], $order['currency_code'], false, false)
                && array_key_exists('Currency', $post) && $post['Currency'] == $order['currency_code'])
            {
                $this->log(' payment_status Received ' . $post['PaymentStatus'] . ' request for order: ' . $order_id . '.');
                switch ($post['PaymentStatus']) {
                    case 1:
                        if ($order['order_status_id']) {
                            $this->model_checkout_order->update($order_id, 2, 'myPOS Checkout has authorized transaction. Transaction ID: ' . $post['IPC_Trnref'], true);
                        }
                        else {
                            $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'), 'myPOS Checkout has authorized transaction. Transaction ID: ' . $post['IPC_Trnref'], true);
                        }
                        break;
                    case 3:
                        
                        if ($order['order_status_id']) {
                            $this->model_checkout_order->update($order_id, 7, 'User has cancelled the order.');
                        }
                       /* else {
                            $this->model_checkout_order->confirm($order_id, 7, 'User has cancelled the order.',false);
                        }
                        * 
                        */
                        break;
                }
            }

            if (array_key_exists('return_url', $this->request->post)) {

                $this->response->redirect(html_entity_decode($this->request->post['return_url']));
            }
        }
    }

    public function callback() {
        if ($this->is_valid_signature($this->request->post)){
            if (isset($this->request->post['OrderID'])) {
                if ($this->isTest) {
                    $this->request->post['OrderID'] = str_replace($this->testPrefix, '', $this->request->post['OrderID']);
                }
                $order_id = $this->request->post['OrderID'];
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);
            } else {
                die('Illegal Access');
            }

            

            if ($this->request->post['IPCmethod'] == 'IPCPurchaseNotify')
            {
                $this->log('Received ' . $this->request->post['IPCmethod'] . ' request for order: ' . $order_id . '.');
               
                if(!$order_info['order_status_id']) {
                    $this->model_checkout_order->confirm($order_id, $this->config->get('mypos_virtual_order_status_id'));
                }
                $this->model_checkout_order->update($order_id, $this->config->get('mypos_virtual_order_status_id'), 'myPOS Checkout has authorized transaction. Transaction ID: ' . $this->request->post['IPC_Trnref'], false);
                    
               // $this->redirect($this->url->link('checkout/success'));
                 
                echo "OK";
                exit;
            }
            else if ($this->request->post['IPCmethod'] == 'IPCPurchaseRollback'){
                $this->log('Received ' . $this->request->post['IPCmethod'] . ' request for order: ' . $order_id . '.');
               
                $this->model_checkout_order->update($order_id, $this->config->get('mypos_virtual_cancel_status_id'), 'myPOS Checkout Checkout has denied transaction.',true);
               
               // $this->redirect($this->url->link('checkout/checkoutetm'));
                echo "OK";
                exit;
            }
            else if ($this->request->post['IPCmethod'] == 'IPCPurchaseCancel'){
                $this->log('Received ' . $this->request->post['IPCmethod'] . ' request for order: ' . $order_id . '.');
                if($order_info['order_status_id']) { // Not Missing 
                   $this->model_checkout_order->update($order_id, $this->config->get('mypos_virtual_cancel_status_id'), 'User has cancelled the order.',false);
                }
                 
                $this->redirect($this->url->link('checkout/checkoutetm'));
                exit;
            }
            else if ($this->request->post['IPCmethod'] == 'IPCPurchaseOK'){
                $this->log('Received ' . $this->request->post['IPCmethod'] . ' request for order: ' . $order_id . '.');
                if(!$order_info['order_status_id']) { // Missing Order
                    $this->model_checkout_order->confirm($order_id, $this->config->get('mypos_virtual_order_status_id'), '', true);
                }
                $this->response->redirect($this->url->link('checkout/success'));
                
                exit;
            }
            else{
                $this->log('Invalid method received on notify url. Method name:' . $this->request->post['IPCmethod'] . '.');
                echo 'INVALID METHOD';
                exit;
            }
        }
        else{
            echo 'INVALID SIGNATURE';
            exit;
        }
    }

    public function create_post($order_info){
        $post = array();

        $post['IPCmethod'] = 'IPCPurchase';
        $post['IPCVersion'] = $this->version;
        $post['IPCLanguage'] = $this->language->get('code');
        $post['WalletNumber'] = $this->wallet_number;
        $post['SID'] = $this->sid;
        $post['keyindex'] = $this->keyindex;
        $post['Source'] = 'sc_opencart 1.8 ' . PHP_VERSION_ID . ' ' . VERSION;
	    $post['CardTokenRequest'] = 0;
	    $post['PaymentParametersRequired'] = $this->paymentParametersRequired;
        $post['PaymentMethod'] = $this->paymentMethod;

        $post['Amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
        $post['Currency'] = $order_info['currency_code'];
        $post['OrderID'] = (!is_null($this->testPrefix) ? $this->testPrefix : '') . $this->session->data['order_id'];
        $post['URL_OK'] = $this->url->link('payment/mypos_virtual/callback', '', true);
        $post['URL_CANCEL'] = $this->url->link('payment/mypos_virtual/callback', '', true);
        $post['URL_Notify'] = $this->url->link('payment/mypos_virtual/callback', '', true);
        $post['CustomerIP'] = $_SERVER['REMOTE_ADDR'];
        $post['CustomerEmail'] = $order_info['email'];
        $post['CustomerFirstNames'] =  $order_info['payment_firstname'];
        $post['CustomerFamilyName'] = $order_info['payment_lastname'];
        $post['CustomerCountry'] = $order_info['payment_iso_code_3'];
        $post['CustomerCity'] = $order_info['payment_city'];
        $post['CustomerZIPCode'] = $order_info['payment_postcode'];
        $post['CustomerAddress'] = $order_info['payment_address_1'];
        $post['CustomerPhone'] = $order_info['telephone'];
        $post['Note'] = 'myPOS Checkout OpenCart Extension';
        // $post['CartItems'] = $this->cart->countProducts();

        $index = 1;

        $data['products'] = array();

        foreach ($order_info['products'] as $product) {
            $post['Article_' . $index] = html_entity_decode(strip_tags($product['name']));
            $post['Quantity_' . $index] = $product['quantity'];
            $post['CartItems'] -= $product['quantity'] - 1;
            $post['Price_' . $index] = $this->currency->format($product['price'], $order_info['currency_code'], false, false);
            $post['Amount_' . $index] = $this->currency->format($product['total'], $order_info['currency_code'], false, false);
            $post['Currency_' . $index] = $order_info['currency_code'];
            $index++;
        }
        
        // Order Totals			
        $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $this->session->data['order_id'] . "' ORDER BY sort_order ASC");

        foreach ($order_total_query->rows as $order_total) {
            if($order_total['code']=='total' || $order_total['code']=='sub_total') {
                continue;
            }
            elseif ($order_total['value']==0) {
                 continue;
            }
            else {
                $this->addTotal($order_total, $post, $index, $order_info);
                $index++;
               // $post['CartItems'] += 1;
            }
        }
        $post['CartItems'] = $index - 1;
        $post['Signature'] = $this->create_signature($post);

        $this->log('Created POST data for order: ' . $post['OrderID'] . '.');

        return $post;
    }

    private function create_check_status_data($order_id){

        $post = array();
        $post['IPCmethod'] = 'IPCGetPaymentStatus';
        $post['IPCVersion'] = $this->version;
        $post['IPCLanguage'] = 'en';
        $post['WalletNumber'] = $this->wallet_number;
        $post['SID'] = $this->sid;
        $post['keyindex'] = $this->keyindex;
        $post['Source'] = 'sc_opencart 1.8 ' . PHP_VERSION_ID . ' ' . VERSION;

        $post['OrderID'] = $order_id;

        $post['OutputFormat'] = 'json';

        $post['Signature'] = $this->create_signature($post);

        return $post;
    }

    public function create_signature($post){
        $concData = base64_encode(implode('-', $post));
        $privKeyObj = openssl_get_privatekey($this->private_key);
        openssl_sign($concData, $signature, $privKeyObj, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    public function is_valid_signature($post){
        // Save signature
        $signature = $post['Signature'];

        // Remove signature from POST data array
        unset($post['Signature']);

        // Concatenate all values
        $concData = base64_encode(implode('-', $post));

        // Extract public key from certificate
        $pubKeyId = openssl_get_publickey($this->public_key);

        // Verify signature
        $result = openssl_verify($concData, base64_decode($signature), $pubKeyId, OPENSSL_ALGO_SHA256);

        //Free key resource
        openssl_free_key($pubKeyId);

        if ($result == 1)
        {
            $this->log('Valid signature.');
            return true;
        }
        else
        {
            $this->log('Invalid signature.');
            return false;
        }
    }

    private function log($message) {
        if ($this->logging){
            $this->log->write($message);
        }
    }

    private function addTotal($total, &$post, $index, $order_info){
        $post['Article_' . $index] = $total['title'];
        $post['Quantity_' . $index] = number_format(1, 0, '.', '');
        $post['Price_' . $index] = number_format($total['value'], 2, '.', '');
        $post['Amount_' . $index] = number_format($total['value'], 2, '.', '');
        $post['Currency_' . $index] = $order_info['currency_code'];
    }
}