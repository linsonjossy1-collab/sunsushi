<?php

class ControllerPaymentDojoCheckout extends Controller {

    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('dojo.log');
    }

    public function index() {
        $this->language->load('payment/dojo_checkout');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = (int) ($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) * 100);
        $order_id = $this->session->data['order_id'];
        
        $post_data =[ 'amount'=>['value'=>$amount,'currencyCode'=>'GBP'],
                      'reference' => 'Order '. $order_id,
                      'description' =>  $this->config->get('config_name') . ' #'.$order_id ,
                     'config'=> [
                          'redirectUrl' => htmlspecialchars_decode($this->url->link('payment/dojo_checkout/success', 'order_id=' . $order_id, 'SSL')),
                          'cancelUrl' => htmlspecialchars_decode($this->url->link('payment/dojo_checkout/cancel', 'order_id=' . $order_id, 'SSL'))
                      ],
                      'paymentMethods' => ['Card','Wallet'],
                      
                    ] ;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.dojo.tech/payment-intents",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic '.$this->config->get('dojo_checkout_apikey'),
                'content-type: application/json',
                'version: 2022-04-07'
            ],
            
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      
        curl_close($curl);

        if ($err || $http_status != 200 ) {
            $this->log->write("cURL Error client #:" . $err);
            $this->data['action'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
        } else {
            $this->log->write("cURL Resposnse #:" . $response);
            $json = json_decode($response,true); 
            $this->data['action'] = 'https://pay.dojo.tech/checkout/'.$json['id'];
            $this->db->query("INSERT INTO  `" . DB_PREFIX . "dojo_checkout` SET paymentIntentId = '".$json['id'] ."', order_id = '". $order_id ."'  ");
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/dojo_checkout.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/dojo_checkout.tpl';
        } else {
            $this->template = 'default/template/payment/dojo_checkout.tpl';
        }
        $this->render();
    }

    public function success() {
        $this->log->write( ' success  :' . print_r($this->request->get,true). print_r($this->request->post,true));
        if (isset($this->request->get['order_id'])) {
              $this->redirect($this->url->link('checkout/success', '', 'SSL'));
        }
        $this->redirect($this->url->link('payment/dojo_checkout/cancel', '', 'SSL'));
    }

    public function cancel() {
        $this->log->write( ' cancel  :' . print_r($this->request->get,true). print_r($this->request->post,true));
        $timeout = 10;    
        $this->language->load('payment/dojo_checkout');

        $this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

        if ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) || (isset($this->request->server['HTTP_X_FORWARDED_PROTO']) && $this->request->server['HTTP_X_FORWARDED_PROTO'] == 'https')) {
            $this->data['base'] = HTTPS_SERVER;
        } else {
            $this->data['base'] = HTTP_SERVER;
        }

        $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

        $this->data['text_failure'] = $this->language->get('text_failure');

        $this->data['timeout'] = $timeout * 1000;

        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $timeout, $this->url->link('checkout/checkoutetm', '', 'SSL'));

        $this->data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/checkoutetm';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/dojo_checkout_failure.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/dojo_checkout_failure.tpl';
        } else {
            $this->template = 'default/template/payment/dojo_checkout_failure.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function webhook() {
        $payload = @file_get_contents('php://input');
        $this->log->write( ' webhook event :' . print_r($payload,true));
        
        $HEADER = getallheaders();
        $signatureHeader = $HEADER['Dojo-Signature'];
        
        $dojo_signature = str_replace("sha256=", '', $signatureHeader);
        $dojo_signature = str_replace("-", '', $dojo_signature);
        $dojo_signature = strtolower($dojo_signature);
        
        $signature =  hash_hmac('SHA256', $payload, $this->config->get('dojo_checkout_webhook_secret')) ;

        if ($dojo_signature ==  $signature) {
            $event = json_decode($payload,true); 
            if(isset($event['data']['paymentIntentId']) && $event['data']['paymentStatus'] == 'Captured') {

                $order_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "dojo_checkout WHERE  paymentIntentId = '". $event['data']['paymentIntentId']. "'");
                if(isset($order_query->row['order_id'])) {
                    $order_id  = $order_query->row['order_id'] ;
                    $this->load->model('checkout/order');
                    $order_info = $this->model_checkout_order->getOrder($order_id);
                    if (!$order_info['order_status_id']) { // Missing order
                         $this->model_checkout_order->confirm($order_id, $this->config->get('dojo_checkout_order_status_id'));
                    }
                }
            }
        
        }else {
            $this->log->write( ' webhook hash error ' . $signatureHeader . ' = ' . $signature );
        }
        http_response_code(200);
        echo "OK";
        exit();
    }

}

?>