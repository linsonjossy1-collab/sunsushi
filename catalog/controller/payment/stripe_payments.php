<?php
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Order;
use Stripe\Account;
use Stripe\Customer;
use Stripe\Error\InvalidRequest;
use Stripe\Error\Card;
use Stripe\Error\Base;
use Stripe\Error\ApiConnection;
use Stripe\Error\Authentication;
use Stripe\Error\Error;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\PaymentMethod;

class ControllerPaymentStripePayments extends Controller {
    public function index() {
        
        $this->language->load('payment/stripe_payments');
        $this->data['text_credit_card'] = $this->language->get('text_credit_card');
        $this->data['text_new_card'] = $this->language->get('text_new_card');    
        $this->data['text_saved_card'] = $this->language->get('text_saved_card');
        $this->data['text_wait'] = $this->language->get('text_wait');
        $this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
        $this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
        $this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
        $this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
        $this->data['text_save_card_later'] = $this->language->get('text_save_card_later');
        $this->data['text_remove_button'] = $this->language->get('text_remove_button');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'],$order_info['currency_code'] ,'', true); 
        $this->data['button_confirm'] = 'Pay '.$amount;
        $this->data['button_back'] = $this->language->get('button_back');
        $this->data['stripe_payments_public_key'] = $this->config->get('stripe_payments_public_key');

        if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
            $this->load->model('account/address');
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $shipping_address = $this->session->data['guest']['shipping'];
        } else {
            $shipping_address =[];
        }
        $this->data['shipping_address'] = $shipping_address;
        $this->data['stripe_billing_address'] = $this->config->get('stripe_billing_address');
        $amount = (int)($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) * 100);    
        
        Stripe::setApiKey($this->config->get('stripe_payments_private_key'));
        $this->data['paymentmethod_saved'] ='';
        if($this->config->get('stripe_payments_cardsave')) {
            // SAVED Stripe
            $this->data['stripe_cardsave'] = 1;
            $savedpayments = $this->customer->getPaymentCards();
           // $this->log->write(print_r($savedpayments,true));
            if(isset($savedpayments['stripe_payments']) && $savedpayments['stripe_payments']['payment_method'] ){
                /*$listpaymentmethods =   PaymentMethod::all([
                    'customer' => $savedpayments['stripe_payments']['customer'],
                    'type' => 'card',
                  ]);
                $this->log->write(print_r($listpaymentmethods,true));
                 * 
                 */
                $payment_method = PaymentMethod::retrieve($savedpayments['stripe_payments']['payment_method']);
                // $this->log->write(print_r($payment_method,true));
                if(isset($payment_method->card)) {
                    $this->data['payment_method_card'] = $payment_method->card;
                    $PaymentIntentSaved = PaymentIntent::create([
                        "amount" => $amount,
                        "currency" => $order_info['currency_code'],
                        'customer' => $savedpayments['stripe_payments']['customer'],
                        'payment_method' => $savedpayments['stripe_payments']['payment_method'],
                        'metadata' => ['order_id'  => $this->session->data['order_id'],
                            'customer'  => $order_info['payment_firstname']. ' ' .$order_info['payment_lastname'],
                            'email'     => $order_info['email'],
                            'phone'     => $order_info['telephone'],
                            'site'  => HTTPS_SERVER
                        ],
                        'description' => 'Order ID# '. $this->session->data['order_id'],

                    ]);
                    $this->data['savedcard'] = 1;
                    $this->data['client_secret_saved'] = $PaymentIntentSaved->client_secret;
                    $this->data['paymentmethod_saved'] = $savedpayments['stripe_payments']['payment_method'];
                }
                $this->data['save_customer_id'] = $savedpayments['stripe_payments']['customer'];
            }
        }
        
        $PaymentIntent = PaymentIntent::create([
                "amount" => $amount,
                "currency" => $order_info['currency_code'],
                "payment_method_types" => ["card"],
                'metadata' => ['order_id'  => $this->session->data['order_id'],
                    'customer'  => $order_info['payment_firstname']. ' ' .$order_info['payment_lastname'],
                    'email'     => $order_info['email'],
                    'phone'     => $order_info['telephone'],
                    'site'  => HTTPS_SERVER
                ],
                'description' => 'Order ID# '. $this->session->data['order_id'],
                'setup_future_usage' => 'on_session',
            ]
        );
        
        $this->data['client_secret'] = $PaymentIntent->client_secret;
        
        
        $this->data['amount'] = $amount;
        $this->data['currency'] = strtolower($order_info['currency_code']);
        $this->data['paymentRequest_label'] = $order_info['store_name'];
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/stripe_payments.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/stripe_payments.tpl';
        } else {
                $this->template = 'default/template/payment/stripe_payments.tpl';
        }	
        $this->render();	
	
    }
    public function send() {
       // $this->log->write(print_r($this->request->post,true));
        $json = array();
        if(isset($this->request->post['paymentIntent'])) {
            $paymentIntent = $this->request->post['paymentIntent'];
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            
            if($paymentIntent['status']=='succeeded') {
                $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('stripe_payments_order_status_id'));
                $json['success'] = $this->url->link('checkout/success', '', 'SSL');
                // SAVE Cutomer id
                if($this->config->get('stripe_payments_cardsave') && $paymentIntent['setup_future_usage']== 'on_session' && isset($this->request->post['stripe_save_card']) ){
                    Stripe::setApiKey($this->config->get('stripe_payments_private_key'));
                    if(isset($this->request->post['save_customer_id'])) {
                        $payment_method = PaymentMethod::retrieve($paymentIntent['payment_method']);
                        $payment_method->attach(['customer' => $this->request->post['save_customer_id']]);
                        $save_cards = array('payment_method' => $paymentIntent['payment_method'],'customer' => $this->request->post['save_customer_id'] );
                    }
                    else {
                        $customer = Customer::create([
                            'payment_method' => $paymentIntent['payment_method'],
                        ]);
                       // $this->log->write(print_r($customer,true));
                        $save_cards = array('payment_method' => $paymentIntent['payment_method'],'customer' => $customer->id );
                    }
                    
                    $this->customer->setPaymentCards('stripe_payments',$save_cards);
                }
            }
            
        }
        if(!$json) {
            $this->language->load('payment/stripe_payments');
            $json['error'] = $this->language->get('entry_stripe_error');
            $json['details'] = $this->language->get('entry_stripe_error');
        }
            
        $this->response->setOutput(json_encode($json));
    }
    
    public function sendsaved() {
       // $this->log->write(print_r($this->request->post,true));
        $json = array();
        if(isset($this->request->post['paymentIntent'])) {
            $paymentIntent = $this->request->post['paymentIntent'];
            $this->load->model('checkout/order');           
            if($paymentIntent['status']=='succeeded') {
                $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('stripe_payments_order_status_id'));
                $json['success'] = $this->url->link('checkout/success', '', 'SSL');
            }
            
        }
        if(!$json) {
            $this->language->load('payment/stripe_payments');
            $json['error'] = $this->language->get('entry_stripe_error');
            $json['details'] = $this->language->get('entry_stripe_error');
        }
            
        $this->response->setOutput(json_encode($json));
    }
    public function removecard() {
       // $this->log->write(print_r($this->request->post,true));
        $json = array('sucess'=>'ok');
        $this->customer->setPaymentCards('stripe_payments',array());
        if(isset($this->request->post['payment_method_key'])) {
            Stripe::setApiKey($this->config->get('stripe_payments_private_key'));
            $payment_method = PaymentMethod::retrieve($this->request->post['payment_method_key']);
            $payment_method->detach();      
        }
        $this->response->setOutput(json_encode($json));
    }
    public function applepay() {
        $json = array();
   
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = (int)($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) * 100);    

        Stripe::setApiKey($this->config->get('stripe_payments_private_key'));
        // Apple Pay
        $PaymentIntent_applepay  = PaymentIntent::create([
        'amount' => $amount,
        'currency' => $order_info['currency_code'],
        // Verify your integration in this guide by including this parameter
        'metadata' => ['integration_check' => 'accept_a_payment',
                        'order_id'  => $this->session->data['order_id'],
                        'customer'  => $order_info['payment_firstname']. ' ' .$order_info['payment_lastname'],
                        'email'     => $order_info['email'],
                        'phone'     => $order_info['telephone'],
                        'site'  => HTTPS_SERVER],
        ]);
        $json['client_secret_applepay'] = $PaymentIntent_applepay->client_secret;
            
        $this->response->setOutput(json_encode($json));
    }
    public function applepaysend() {
        $this->log->write(print_r($this->request->post,true));
        $json = array();
        
        $this->load->model('checkout/order');
       // $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

       // if($paymentIntent['status']=='succeeded') {
            $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('stripe_payments_order_status_id'));
            $json['success'] = $this->url->link('checkout/success', '', 'SSL');

      //  }
            
       
        if(!$json) {
            $this->language->load('payment/stripe_payments');
            $json['error'] = $this->language->get('entry_stripe_error');
            $json['details'] = $this->language->get('entry_stripe_error');
        }
            
        $this->response->setOutput(json_encode($json));
    }
    /*
    public function eposiframe() {
        $this->request->post['amount'] = 1000;
        $this->request->post['cartId'] = 23;
        $this->language->load('payment/stripe_payments');
        $this->data['title']                        = 'Terminal';    
        $this->data['text_credit_card']             = $this->language->get('text_credit_card');
        $this->data['text_wait']                    = $this->language->get('text_wait');
        $this->data['stripe_payments_public_key']   = $this->config->get('stripe_payments_pos_public_key');
        $this->data['amount']                       = $this->request->post['amount'];
        $this->data['note']                         = 'Order ID# ' . $this->request->post['cartId'];
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/stripe_eposiframe.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/stripe_eposiframe.tpl';
        } else {
            $this->template = 'default/template/payment/stripe_eposiframe.tpl';
        }
        $this->response->setOutput($this->render());
    }
    public function epossend() {
        $note_prefix  = 'Terminal';
        $response = array();
        
        if($_POST){
            $note_parts = array();
            if($note_prefix) $note_parts[] = $note_prefix;
            if($_POST['note']) $note_parts[] = $_POST['note'];

            $params = [
                'amount'                => $_POST['amount'],
                'currency'              => $this->currency->getCode(),
                'card'                  => $_POST['token'],
                "payment_method"        => $this->request->post['payment_method'],
                "description"           => implode(' - ', $note_parts),
                "payment_method_types"  => ["card"],
                "confirm"               => true,
            ];

            $response = array(
                'success' => false
            );
            try{
                Stripe::setApiKey($this->config->get('stripe_payments_pos_private_key'));
                $pay_intent = PaymentIntent::create($params);
                
                if($pay_intent['next_action'] != null)
                {
                    $response['pi'] = $pay_intent;
                } else { 
                    $response['success']    = true;
                    $response['id']         = $pay_intent->id;
                    $response['amount']     = number_format($pay_intent->amount / 100, 2);
                    $response['fee']        = number_format($pay_intent->fee / 100, 2);
                    $response['card_type']  = $pay_intent->card->type;
                    $response['card_last4'] = $pay_intent->card->last4;
                }
            }catch (Exception $e) {
                $response['error'] = $e->getMessage();
            }
        }
        $this->response->setOutput(json_encode($response));
    }
    *
    */
    public function eposiframe() {
        $this->language->load('payment/stripe_payments');
            $this->data['title'] = 'Terminal';
            
        $this->data['text_credit_card'] = $this->language->get('text_credit_card');
        $this->data['text_wait'] = $this->language->get('text_wait');

        $this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
        $this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
        $this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
        $this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
            $this->data['stripe_payments_public_key'] = $this->config->get('stripe_payments_pos_public_key');
            $this->data['demo_mode']         = $this->config->get('stripe_payments_pos_test');
            $this->data['amount']         = $this->request->post['amount'];
            $this->data['note']         ='Order ID# ' . $this->request->post['cartId'];
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/stripe_eposiframe.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/stripe_eposiframe.tpl';
        } else {
            $this->template = 'default/template/payment/stripe_eposiframe.tpl';
        }

        $this->response->setOutput($this->render());
    }
    public function epossend() {
        $note_prefix  = 'Terminal';
        $response = array();
        if($_POST){
            //require_once( DIR_SYSTEM .'vendor/stripe-php/lib/Stripe.php');
            $stripe = array(
                "secret_key"      => $this->config->get('stripe_payments_pos_private_key'),
                "publishable_key" => $this->config->get('stripe_payments_pos_public_key')
            );
            $note_parts = array();
            if($note_prefix) $note_parts[] = $note_prefix;
            if($_POST['note']) $note_parts[] = $_POST['note'];

            $params = array(
                'amount'        => $_POST['amount'],
                'currency'      => $this->currency->getCode(),
                'card'          => $_POST['token'],
                'description'   => implode(' - ', $note_parts)
            );

            $response = array(
                'success' => false
            );

            try{
                Stripe::setApiKey($stripe['secret_key']);
                $charge = Charge::create($params);

                $response['success']    = true;
                $response['id']         = $charge->id;
                $response['amount']     = number_format($charge->amount / 100, 2);
                $response['fee']        = number_format($charge->fee / 100, 2);
                $response['card_type']  = $charge->card->type;
                $response['card_last4'] = $charge->card->last4;
            }catch (Exception $e) {
                $response['error'] = $e->getMessage();
            }

            // echo json_encode($response);

        }
        $this->response->setOutput(json_encode($response));
    }
    public function updatePaymentIntentEpos(){
        Stripe::setApiKey($this->config->get('stripe_payments_pos_private_key'));
        $json= [];
        try {
            $payment_i = PaymentIntent::retrieve($this->request->post['paymentIntentId']);
            
            if($payment_i['status'] == 'success' || $payment_i['status'] == "succeeded"){
                    $json['success']    = true;
                    $json['id']         = $payment_i->id;
                    $json['amount']     = number_format($payment_i->amount / 100, 2);
                    $json['fee']        = number_format($payment_i->fee / 100, 2);
                    $json['card_type']  = $payment_i->card->type;
                    $json['card_last4'] = $payment_i->card->last4;
                    $json['success'] = $this->url->link('checkout/success', '', 'SSL');
            }
            else{
                $json['error'] = 'Error occured!';
            }
            
        } catch (Exception $exc) {
            $json['error']  = $exc->jsonBody['error']['message'];
        }
        $this->response->setOutput(json_encode($json));
    }

    public function webhook(){
        $payload = @file_get_contents('php://input');
        $event = null;
        $server = false;
        if(isset($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
            // THIS IS SERVER
            $server = true;
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        }
        else {
            $HEADER = getallheaders();
            $sig_header = $HEADER['Stripe-Authorization'];
        }
        
        try {
            Stripe::setApiKey($this->config->get('stripe_payments_private_key'));
            $event = Webhook::constructEvent(
                $payload, $sig_header, $this->config->get('stripe_payments_webhook_secret')
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }
        if ( $server && $event->data->object->metadata->site != HTTPS_SERVER) {
            $headers = array(
                'Content-type: application/xml',
                'Stripe-Authorization: ' . $sig_header,
            );
            //$this->log->write(print_r($payload,true));
            $ch = curl_init($event->data->object->metadata->site . 'index.php?route=payment/stripe_connect/webhook');
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
            curl_setopt( $ch,  CURLOPT_HTTPHEADER, $headers);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            curl_close($ch);
            http_response_code(200);
            echo "SWITCH";
            exit();
        }
        if ( $event->data->object->metadata->site != HTTPS_SERVER) {
          //  NOT SERVER and site is deiffrent 
           http_response_code(200);
            exit();
        }
     //   $this->log->write(print_r($event,true));
        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
               // handlePaymentIntentSucceeded($paymentIntent);
               // $this->log->write('payment_intent.succeeded');
               // $this->log->write(print_r($paymentIntent,true));
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($paymentIntent->metadata->order_id);
                $this->model_checkout_order->deleteOrderToStatuscheck($paymentIntent->metadata->order_id);
                if(!$order_info['order_status_id']){ // Missing order
                    $this->model_checkout_order->confirm($paymentIntent->metadata->order_id, $this->config->get('stripe_payments_order_status_id'));
                }
                elseif($order_info['order_status_id'] == 10){ // Faild Order
                    // Prevent pending update in order model 
                    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '0', date_modified = '" . date("Y-m-d H:i:s", time()) . "' WHERE order_id = '" . (int) $paymentIntent->metadata->order_id . "'");
                    $this->model_checkout_order->confirm($paymentIntent->metadata->order_id, $this->config->get('stripe_payments_order_status_id'));
                }
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentMethod
               // handlePaymentMethodAttached($paymentMethod)
               // $this->log->write('payment_intent.payment_failed');
                // $this->log->write(print_r($paymentIntent,true));
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($paymentIntent->metadata->order_id);
                $this->model_checkout_order->deleteOrderToStatuscheck($paymentIntent->metadata->order_id);
                if(!$order_info['order_status_id']){ // Missing order
                    $this->model_checkout_order->confirm($paymentIntent->metadata->order_id,  '10', $paymentIntent->last_payment_error->message,false);
                }
                break;
            // ... handle other event types
            default:
                // Unexpected event type
                http_response_code(400);
                exit();
        }

        http_response_code(200);
        echo "OK";
        exit();
    }
    // Apple Pay Mobile 
    public function applepaysendmobile() {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->request->post['order_id']);
        /*if($this->customer->isLogged()){
            $logged_customer_id = $this->customer->getId();
        }else{
            $logged_customer_id = $order_info['customer_id'];
        }
        */
       // $this->log->write('Applepaysendmobile');
      //  $this->log->write(print_r($this->request->post,true));
      //  $this->log->write(print_r($this->session->data,true));
        $amount = (int)($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) * 100);
        Stripe::setApiKey($this->config->get('stripe_payments_private_key'));
            $error = null;
            try {
                $charge = PaymentIntent::create([
                        "amount" => $amount,
                        "currency" => $order_info['currency_code'],
                        "payment_method_types" => ["card"],
                        'metadata' => ['order_id'  => $this->request->post['order_id'],
                            'customer'  => $order_info['payment_firstname']. ' ' .$order_info['payment_lastname'],
                            'email'     => $order_info['email'],
                            'phone'     => $order_info['telephone'],
                            'site'  => HTTPS_SERVER
                        ],
                        'description' => 'Order ID# '. $this->request->post['order_id'],
                       // "payment_method"    => $this->request->post['token'],
                        "payment_method_data" => ["type" => "card", "card" => ["token" => $this->request->post['token']]],
                        "confirm" => true,
                    ],
                    array( "idempotency_key" => 'ETMSTRIPECS0' .$this->request->post['order_id'])
                );
    

}
            catch(Card $e) {
                // Error card processing
                $error = $e->jsonBody['error'];
                
            }
        //create object to use as json
        $json = array();

        //If successful log transaction in opencart system
        if (!$error) {

            if($charge['next_action'] != null)
            {
                $json['pi'] = $charge;
            } else {
                $this->model_checkout_order->confirm($this->request->post['order_id'], $this->config->get('stripe_payments_order_status_id'));
                //$this->model_checkout_order->deleteOrderToStatuscheck($this->session->data['order_id']);
                $json['success'] = $this->url->link('checkout/success', '', 'SSL');
            }
        }
        else {
               // $this->model_checkout_order->deleteOrderToStatuscheck($this->session->data['order_id']);
                $json['error'] = (string)$error['message'];
                $json['details'] = $error;
        }
       // $this->log->write(print_r($json,true));
      
        $this->session->data['stripe_payment'] = $json;
        $this->response->setOutput(json_encode($json));
    }
}
?>