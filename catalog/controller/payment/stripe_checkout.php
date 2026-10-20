<?php

require_once DIR_SYSTEM . '/vendor/stripe-php/init.php';

use Stripe\Stripe;
use Stripe\Error\Card;
use Stripe\PaymentIntent;
use Stripe\Webhook;

class ControllerPaymentStripeCheckout extends Controller {
    private $log;
    private $log_file = 'stripe.log';

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log($this->log_file);
    }
    public function index() {

        $this->language->load('payment/stripe_checkout');


        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], '', true);
        $this->data['button_confirm'] = 'Pay ' . $amount;
        $this->data['button_back'] = $this->language->get('button_back');

        if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
            $this->load->model('account/address');
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $shipping_address = $this->session->data['guest']['shipping'];
        } else {
            $shipping_address = [];
        }
        $amount = (int) strval($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) * 100);
        
        $stripe_session = [
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                    'price_data' => [
                        'currency' => $order_info['currency_code'],
                        'product_data' => [
                            'name' => 'Order ID #' . $this->session->data['order_id'],
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                        ]],
                    'customer_email' => $order_info['email'],
                    'payment_intent_data' => ['metadata' => ['order_id'  => $this->session->data['order_id'],
                            'customer'  => $order_info['payment_firstname']. ' ' .$order_info['payment_lastname'],
                            'email'     => $order_info['email'],
                            'phone'     => $order_info['telephone'],
                            'site'  => HTTPS_SERVER
                    ]],
                    'mode' => 'payment',
                    'success_url' => htmlspecialchars_decode($this->url->link('payment/stripe_checkout/success', 'order_id=' . $this->session->data['order_id'].'&session_id={CHECKOUT_SESSION_ID}', 'SSL')),
                    'cancel_url' => htmlspecialchars_decode($this->url->link('payment/stripe_checkout/cancel', 'order_id=' . $this->session->data['order_id'], 'SSL')),
        ];
        
        if($this->config->get('stripe_connect_account_id')) { // Connect account
            $stripe_connect_commission = $this->config->get('stripe_checkout_connect_commision');
            $stripe_commission_option  = $this->config->get('stripe_checkout_connect_commission_option');
            switch ($stripe_commission_option) {
                case 'fixed' :
                    $stripe_connect_application_fee_amount = (float)$this->currency->format($stripe_connect_commission, $order_info['currency_code'], 1.00000, false) ; 
                    break;
                case 'percent':
                    $stripe_connect_application_fee_amount = (float)$this->currency->format(($order_info['total'] * ($stripe_connect_commission / 100)), $order_info['currency_code'], 1.00000, false) ;
                    break;
                case 'mixed':
                    $stripe_split_com = explode('+', $stripe_connect_commission);   
                    $stripe_connect_application_fee_amount  = (float)$this->currency->format($stripe_split_com[0], $order_info['currency_code'], 1.00000, false) ; 
                    $stripe_connect_application_fee_amount  += (float)$this->currency->format(($order_info['total'] * ($stripe_split_com[1] / 100)), $order_info['currency_code'], 1.00000, false) ;
                    break;
                default :
                    $stripe_connect_application_fee_amount = 0.0;
                    break;
            }
            $stripe_connect_application_fee_amount = (int)($stripe_connect_application_fee_amount * 100);
            
            $stripe_session['payment_intent_data']['application_fee_amount'] = $stripe_connect_application_fee_amount;
              
        }

        Stripe::setApiKey($this->config->get('stripe_checkout_private_key'));
        if($this->config->get('stripe_connect_account_id')) { // Connect account
            $checkout_session = \Stripe\Checkout\Session::create($stripe_session, ['stripe_account' => $this->config->get('stripe_connect_account_id')]);
        }else {
        $checkout_session = \Stripe\Checkout\Session::create($stripe_session);
        }

        $this->data['session_url'] = $checkout_session->url;
        $this->data['continue'] = $this->url->link('checkout/checkoutetm', '', 'SSL'); 
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/stripe_checkout.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/stripe_checkout.tpl';
        } else {
            $this->template = 'default/template/payment/stripe_checkout.tpl';
        }
        $this->render();
    }

    public function success() {
        if (isset($this->request->get['session_id'])) {
            Stripe::setApiKey($this->config->get('stripe_checkout_private_key'));
            if($this->config->get('stripe_connect_account_id')) { // Connect account
                $stripe_session = \Stripe\Checkout\Session::retrieve($this->request->get['session_id'], ['stripe_account' => $this->config->get('stripe_connect_account_id')]);
            }else {
            $stripe_session = \Stripe\Checkout\Session::retrieve($this->request->get['session_id']);
            }
           // $this->log->write(print_r($stripe_session,true));
           
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->request->get['order_id']);

            if ($stripe_session['payment_status'] == 'paid') {
                if (!$order_info['order_status_id']) { // Missing order
                   $this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('stripe_checkout_order_status_id'));
                }
                $this->redirect($this->url->link('checkout/success', '', 'SSL'));
            }
        }

         $this->redirect($this->url->link('payment/stripe_checkout/cancel', '', 'SSL'));
    }

    public function cancel() {
        $timeout = 10;    
        $this->language->load('payment/stripe_checkout');

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

        $this->data['text_failure'] = $this->language->get('text_failure');

        $this->data['timeout'] = $timeout * 1000;

        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $timeout, $this->url->link('checkout/checkoutetm', '', 'SSL'));

        $this->data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/checkoutetm';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/stripe_checkout_failure.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/stripe_checkout_failure.tpl';
        } else {
            $this->template = 'default/template/payment/stripe_checkout_failure.tpl';
        }
        $this->response->setOutput($this->render());
    }


    public function webhook() {
        $payload = @file_get_contents('php://input');
        $this->log->write(print_r($payload,true));
        $event = null;
        $server = false;
        if (isset($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
            // THIS IS SERVER
            $server = true;
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        } else {
            $HEADER = getallheaders();
            $sig_header = $HEADER['Stripe-Authorization'];
        }

        try {
            Stripe::setApiKey($this->config->get('stripe_checkout_private_key'));
            $event = Webhook::constructEvent(
                            $payload, $sig_header, $this->config->get('stripe_checkout_webhook_secret')
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }
        if ($server && $event->data->object->metadata->site != HTTPS_SERVER) {
            $headers = array(
                'Content-type: application/xml',
                'Stripe-Authorization: ' . $sig_header,
            );
            //$this->log->write(print_r($payload,true));
            $ch = curl_init($event->data->object->metadata->site . 'index.php?route=payment/stripe_connect/webhook');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            http_response_code(200);
            echo "SWITCH";
            exit();
        }
        if ($event->data->object->metadata->site != HTTPS_SERVER) {
            //  NOT SERVER and site is deiffrent 
            http_response_code(200);
            exit();
        }
       // $this->log->write(print_r($event,true));
        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($paymentIntent->metadata->order_id);
                $this->model_checkout_order->deleteOrderToStatuscheck($paymentIntent->metadata->order_id);
                if (!$order_info['order_status_id']) { // Missing order
                    $this->model_checkout_order->confirm($paymentIntent->metadata->order_id, $this->config->get('stripe_checkout_order_status_id'));
                } elseif ($order_info['order_status_id'] == 10) { // Faild Order
                    // Prevent pending update in order model 
                    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '0', date_modified = '" . date("Y-m-d H:i:s", time()) . "' WHERE order_id = '" . (int) $paymentIntent->metadata->order_id . "'");
                    $this->model_checkout_order->confirm($paymentIntent->metadata->order_id, $this->config->get('stripe_checkout_order_status_id'));
                }
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentMethod
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($paymentIntent->metadata->order_id);
                $this->model_checkout_order->deleteOrderToStatuscheck($paymentIntent->metadata->order_id);
                if (!$order_info['order_status_id']) { // Missing order
                    $this->model_checkout_order->confirm($paymentIntent->metadata->order_id, '10', $paymentIntent->last_payment_error->message, false);
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
        
        $amount = (int) ($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) * 100);
        Stripe::setApiKey($this->config->get('stripe_checkout_private_key'));
        $error = null;
        try {
            if($this->config->get('stripe_connect_account_id')) { // Connect account
                $charge = PaymentIntent::create([
                        "amount" => $amount,
                        "currency" => $order_info['currency_code'],
                        "payment_method_types" => ["card"],
                        'metadata' => ['order_id' => $this->request->post['order_id'],
                            'customer' => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'],
                            'email' => $order_info['email'],
                            'phone' => $order_info['telephone'],
                            'site' => HTTPS_SERVER
                        ],
                        'description' => 'Order ID# ' . $this->request->post['order_id'],
                        "payment_method_data" => ["type" => "card", "card" => ["token" => $this->request->post['token']]],
                        "confirm" => true,
                            ],
                            array("stripe_account"=> $this->config->get('stripe_connect_account_id')),
                            array("idempotency_key" => 'ETMSTRIPECS0' . $this->request->post['order_id'])
                );
            }
            else {
                $charge = PaymentIntent::create([
                            "amount" => $amount,
                            "currency" => $order_info['currency_code'],
                            "payment_method_types" => ["card"],
                            'metadata' => ['order_id' => $this->request->post['order_id'],
                                'customer' => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'],
                                'email' => $order_info['email'],
                                'phone' => $order_info['telephone'],
                                'site' => HTTPS_SERVER
                            ],
                            'description' => 'Order ID# ' . $this->request->post['order_id'],
                            "payment_method_data" => ["type" => "card", "card" => ["token" => $this->request->post['token']]],
                            "confirm" => true,
                                ],
                                array("idempotency_key" => 'ETMSTRIPECS0' . $this->request->post['order_id'])
                );
            }
        } catch (Card $e) {
            // Error card processing
            $error = $e->jsonBody['error'];
            $this->log->write(print_r($error,true));
        }
        //create object to use as json
        $json = array();

        //If successful log transaction in opencart system
        if (!$error) {

            if ($charge['next_action'] != null) {
                $json['pi'] = $charge;
            } else {
                $this->model_checkout_order->confirm($this->request->post['order_id'], $this->config->get('stripe_checkout_order_status_id'));
                //$this->model_checkout_order->deleteOrderToStatuscheck($this->session->data['order_id']);
                $json['success'] = $this->url->link('checkout/success', '', 'SSL');
            }
        } else {
            // $this->model_checkout_order->deleteOrderToStatuscheck($this->session->data['order_id']);
            $json['error'] = (string) $error['message'];
            $json['details'] = $error;
        }
        // $this->log->write(print_r($json,true));

        $this->session->data['stripe_checkout'] = $json;
        $this->response->setOutput(json_encode($json));
    }

}

?>