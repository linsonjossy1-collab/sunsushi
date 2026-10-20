<?php

require_once( DIR_SYSTEM . 'vendor/squareup/vendor/autoload.php');

use Square\Environment;
use Square\Models\CreateOrderRequest;
use Square\Models\CreateCheckoutRequest;
use Square\Models\Order;
use Square\Models\OrderLineItem;
use Square\Models\Money;
use Square\Models\Address;
use Square\Exceptions\ApiException;
use Square\SquareClient;

class ControllerPaymentSquareCheckout extends Controller {

    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('squareup.log');
    }

    public function index() {
        $this->language->load('payment/square_checkout');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $amount = (int) ($this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false) * 100);
        $order_id = $this->session->data['order_id'];
        $idempotency_key = 'ETMSQR0' . $order_id;
        // Initialize the authorization for Square


        $client = new SquareClient([
            'accessToken' => $this->config->get('square_checkout_access_token'),
            'environment' => ($this->config->get('square_checkout_mode') ? Environment::SANDBOX : Environment::PRODUCTION)
        ]);
        $status = true;
        // make sure we actually are on a POST with an amount
        // This example assumes the order information is retrieved and hard coded
        // You can find different ways to retrieve order information and fill in the following lineItems object.
        try {
            $location_id = $this->config->get('square_checkout_location_id');

            $checkout_api = $client->getCheckoutApi();

            // Monetary amounts are specified in the smallest unit of the applicable currency.
            // This amount is in cents. It's also hard-coded for $1.00, which isn't very useful.
            // Set currency to the currency for the location
            $currency = $client->getLocationsApi()->retrieveLocation($location_id)->getResult()->getLocation()->getCurrency();
            $money_A = new Money();
            $money_A->setCurrency($currency);
            $money_A->setAmount($amount);

            $item_A = new OrderLineItem(1);
            $item_A->setName('Order ID #' . $order_id);
            $item_A->setBasePriceMoney($money_A);

            // Create a new order and add the line items as necessary.
            $order = new Order($location_id);
            $order->setLineItems([$item_A]);

            $order->setReferenceId($order_id);
            $create_order_request = new CreateOrderRequest();
            $create_order_request->setOrder($order);
            
            /*
            // Address 
            $address = new Address;
            $address->setAddressLine1(preg_replace('/[^a-z\d ]/i', ' ',htmlspecialchars_decode($order_info['payment_address_1'])));
            $address->setAddressLine2(preg_replace('/[^a-z\d ]/i', '', htmlspecialchars_decode($order_info['payment_address_2'])));
           // $address->setAddressLine3($value);
            $address->setLocality(preg_replace('/[^a-z\d ]/i', '',htmlspecialchars_decode($order_info['payment_city'])));
            //$address->setSublocality($value);
           // $address->setAdministrativeDistrictLevel1($value);
            $address->setPostalCode($order_info['payment_postcode']);
            $address->setCountry('UK');
            $address->setFirstName( html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'));
            $address->setLastName(html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'));
            */
            // Similar to payments you must have a unique idempotency key.
            $checkout_request = new CreateCheckoutRequest($idempotency_key, $create_order_request); // uniqid()
            // Set a custom redirect URL, otherwise a default Square confirmation page will be used
            $checkout_request->setRedirectUrl(htmlspecialchars_decode($this->url->link('payment/square_checkout/confirm', 'order_id=' . $order_id, 'SSL')));
            $checkout_request->setPrePopulateBuyerEmail($order_info['email']);
            $response = $checkout_api->createCheckout($location_id, $checkout_request);
        } catch (ApiException $e) {
            // If an error occurs, output the message
            $this->log->write(
                    ' Caught exception!' . "\n" .
                    ' Response body: ' . print_r($e->getResponseBody(), true) . "\n" .
                    ' Context: ' . print_r($e->getContext(), true));
            $status = false;
            $this->data['action'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
        }

        // If there was an error with the request we will
        // print them to the browser screen here
        if ($response->isError()) {
            $errors = $response->getErrors();
            $err_content = '';
            foreach ($errors as $error) {
                $err_content .= $error->getDetail() . "\n";
            }
            $this->log->write('Api response has Errors' . $err_content);
            $status = false;
            $this->data['action'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
        }

        $this->log->write('CreateOrderRequest response : ' . print_r($response->getBody(), true));
        if ($status) {
            $this->data['action'] = $response->getResult()->getCheckout()->getCheckoutPageUrl();
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/square_checkout.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/square_checkout.tpl';
        } else {
            $this->template = 'default/template/payment/square_checkout.tpl';
        }
        $this->render();
    }

    public function confirm() {
        $transaction_id = isset($this->request->get['transactionId']) ? $this->request->get['transactionId'] : null;
        if (is_null($transaction_id)) {
            $this->log->write('Invalid card data : ' . print_r($this->request->get, true));
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            return;
        }
        $client = new SquareClient([
            'accessToken' => $this->config->get('square_checkout_access_token'),
            'environment' => ($this->config->get('square_checkout_mode') ? Environment::SANDBOX : Environment::PRODUCTION)
        ]);

        $this->load->model('checkout/order');
        $order_id = $this->request->get['order_id'];
        $order_info = $this->model_checkout_order->getOrder($order_id);

        try {
            $orders_api = $client->getOrdersApi();
            $response = $orders_api->retrieveOrder($transaction_id);
        } catch (ApiException $e) {
            // If an error occurs, output the message
            $this->log->write('Caught exception!' . "\n" .
                    ' Response body:' . print_r($e->getResponseBody(), true) . "\n" .
                    ' Context:' . print_r($e->getContext(), true) . "\n");
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            return;
        }
        if ($response->isError()) {
            $errors = $response->getErrors();
            $err_cnt = '';
            foreach ($errors as $error) {
                $err_cnt .= $error->getDetail() . "\n";
            }

            $this->log->write('Api response has Errors:' . $err_cnt);
            $this->redirect($this->url->link('checkout/checkoutetm', '', 'SSL'));
            return;
        } else {
            $sq_order = $response->getResult()->getOrder();
            $this->log->write('Sucsess : ' . print_r($response->getBody(), true));
            $this->model_checkout_order->confirm($order_id, $this->config->get('square_checkout_order_status_id'));
            $this->redirect($this->url->link('checkout/success', '', 'SSL'));
        }
    }

    public function webhook() {

        if ($this->request->server['REQUEST_METHOD'] == 'POST'){
            $callbackBody = file_get_contents('php://input');
            $callbackSignature = getallheaders()['X-Square-Signature'];
            
            $this->log->write('webhook request_body : ' . print_r($callbackBody, true));
            # Validate the signature
            if ($this->isValidCallback($callbackBody, $callbackSignature)){
                $callbackBodyJson = json_decode($callbackBody);

                # If the notification indicates a PAYMENT_UPDATED event...
                if (property_exists($callbackBodyJson, 'type') and $callbackBodyJson->type == 'payment.updated' 
                        and $callbackBodyJson->data->object->payment->status == 'COMPLETED') {

                    $transaction_id = $callbackBodyJson->data->object->payment->order_id;
                   
                    $square_client = new SquareClient([
                        'accessToken' => $this->config->get('square_checkout_access_token'),
                        'environment' => ($this->config->get('square_checkout_mode') ? Environment::SANDBOX : Environment::PRODUCTION)
                    ]);
        
                    try {
                       
                        $order_response = $square_client->getOrdersApi()->retrieveOrder($transaction_id);

                        if ($order_response->isSuccess()) {
                            $squre_order = $order_response->getResult()->getOrder();
                            # Perform an action based on the returned payment (in this case, simply log it)
                            $this->log->write("Webhook squre_order SUCCES :". print_r(json_encode($squre_order->jsonSerialize()),true));
                            $this->load->model('checkout/order');
                            $order_id = $squre_order->getReferenceId();
                            $order_info = $this->model_checkout_order->getOrder($order_id);
                            if(!$order_info['order_status_id']){ // Missing order
                                $this->model_checkout_order->confirm($order_id, $this->config->get('square_checkout_order_status_id'));
                            }

                        } else {
                            $errors = $order_response->getErrors();
                            $this->log->write("Webhook api_response ERR :". print_r($errors,true));
                        }
 
                   } catch (ApiException $e) {
                        // If an error occurs, output the message
                        $this->log->write(
                                ' webhook Caught exception!' . "\n" .
                                ' Response body: ' . print_r($e->getResponseBody(), true) . "\n" .
                                ' Context: ' . print_r($e->getContext(), true));
                         $this->data['action'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
                    }
                }
                else {
                     // If an error occurs, output the message
                        $this->log->write(' webhook Not COMPLETED OR  type  not passing ',);
                       //  $this->data['action'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
                }
            }
            else {
              # Fail if the signature is invalid
              $this->log->write("Webhook event with invalid signature detected!");
              $this->log->write('webhook request_body : ' . print_r($callbackBody, true));
              $this->log->write('webhook callbackSignature : ' . print_r($callbackSignature, true));
            }
        }
        http_response_code(200);
        echo "OK";
        exit();
    }
    private function isValidCallback($callbackBody, $callbackSignature) {

        if($this->config->get('square_checkout_webhook_secret') && !empty($this->config->get('square_checkout_webhook_secret'))) {
            $webhookSignatureKey = $this->config->get('square_checkout_webhook_secret');
            $webhookUrl = HTTPS_SERVER.  'index.php?route=payment/square_checkout/webhook';
                # Combine your webhook notification URL and the JSON body of the incoming request into a single string
            $stringToSign = $webhookUrl . $callbackBody;

            # Generate the HMAC-SHA1 signature of the string, signed with your webhook signature key
            $stringSignature = base64_encode(hash_hmac('sha1', $stringToSign, $webhookSignatureKey, true));

            # Hash the signatures a second time (to protect against timing attacks)
            # and compare them
            return (sha1($stringSignature) === sha1($callbackSignature));
        }else {
             $this->log->write('webhook Signature not validated ');
            return true;
        }
        
      }

}

