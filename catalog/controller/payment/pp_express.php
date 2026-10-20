<?php

class ControllerPaymentPPExpress extends Controller {
    private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('paypalexpres.log');
    }
    protected function index() {
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        if ($order_info) {
            $this->data['business'] = $this->config->get('pp_standard_email');
            $this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

            $this->data['products'] = array();
            $nvProducts ="";
            $i=0;
            foreach ($this->cart->getProducts() as $product) {
                $option_data = array();
                $nvProductOptions="";
                foreach ($product['option'] as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['option_value'];
                    } else {
                        $filename = $this->encryption->decrypt($option['option_value']);

                        $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                    }

                    $option_data[] = array(
                        'name' => $option['name'],
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                    );
                    $nvProductOptions .=  urlencode($option['name']) . ": " . $value . " - ";
                }

                $this->data['products'][] = array(
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'price' => $this->currency->format($product['price'], $order_info['currency_code'], false, false),
                    'quantity' => $product['quantity'],
                    'option' => $option_data,
                    'weight' => $product['weight']
                );
                $nvProducts .="&L_NAME$i=" . urlencode($product['name']) ."&L_DESC$i=". $nvProductOptions. "&L_AMT$i=" .  $this->currency->format($product['price'], $order_info['currency_code'], false, false) 
                            . "&L_QTY$i=" .$product['quantity']. "&L_NUMBER$i=" . $product['model'];
                $i++;
            }

            $this->data['discount_amount_cart'] = 0;

            $total = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $order_info['currency_code'], false, false);

            if ($total > 0) {
                $this->data['products'][] = array(
                    'name' => $this->language->get('text_total'),
                    'model' => '',
                    'price' => $total,
                    'quantity' => 1,
                    'option' => array(),
                    'weight' => 0
                );
            } else {
                $this->data['discount_amount_cart'] -= $total;
            }

         
            $this->data['paymentaction'] = 'Authorization';
        
             $nvpStr = "&Amt=" . $this->currency->format($order_info['total'] , $order_info['currency_code'], false, false) . "&ReturnUrl=". $this->url->link('payment/pp_express/confirm'). "&CANCELURL=" . $this->url->link('checkout/checkout', '', 'SSL') ."&PAYMENTACTION=". $this->data['paymentaction'] ."&CURRENCYCODE=" .$order_info['currency_code'] ."&CUSTOM=". $this->session->data['order_id']. $nvProducts;
             $this->data['action'] = $this->SetExpressCheckout( $nvpStr ); 
          
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pp_express.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/pp_express.tpl';
            } else {
                $this->template = 'default/template/payment/pp_express.tpl';
            }

            $this->render();
        }
    }
    public function confirm() {
      
        $this->load->model('checkout/order');
        $order_id = $this->session->data['order_id'];
	$order_info = $this->model_checkout_order->getOrder($order_id);
        
        $httpParsedResponseAr = $this->GetExpressCheckoutDetails();
        if($httpParsedResponseAr) {
           $checkoutpayments = $this->DoExpressCheckoutPayment($httpParsedResponseAr,$this->currency->format($order_info['total'] , $order_info['currency_code'], false, false),$order_info['currency_code']);
           if($checkoutpayments) {
               $order_status_id = $this->config->get('config_order_status_id');
               $this->model_checkout_order->confirm($order_id, $order_status_id);
           }
           else {
               // Not Sussess
               $order_status_id = $this->config->get('pp_standard_denied_status_id');
               $this->model_checkout_order->update($order_id, $order_status_id);
               
           }
           $this->redirect($this->url->link('checkout/success'));
        }
        else {
            $this->redirect($this->url->link('checkout/checkout'));
        }
    }

    /**
     * Send HTTP POST Request
     *
     * @param	string	The API method name
     * @param	string	The POST Message fields in &name=value pair format
     * @return	array	Parsed HTTP Response body
     */
    private function PPHttpPost($methodName_, $nvpStr_) {
        $environment='';
        if ($this->config->get('pp_express_test')) {
            $environment = "sandbox";
        }
      

        // Set up your API credentials, PayPal end point, and API version.
        $API_UserName = urlencode($this->config->get('pp_express_username'));
        $API_Password = urlencode($this->config->get('pp_express_password'));
        $API_Signature = urlencode($this->config->get('pp_express_signature'));
        $API_Endpoint = "https://api-3t.paypal.com/nvp";
        if ("sandbox" === $environment || "beta-sandbox" === $environment) {
            $API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
        }
        $version = urlencode('51.0');

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // Get response from the server.
        $httpResponse = curl_exec($ch);

        if (!$httpResponse) {
            exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }

        // Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }

        return $httpParsedResponseAr;
    }

    private function GetExpressCheckoutDetails() {
        $this->log->write('GetExpressCheckoutDetails : ' . print_r($_REQUEST, true));
        /**
         * This example assumes that this is the return URL in the SetExpressCheckout API call.
         * The PayPal website redirects the user to this page with a token.
         */
        // Obtain the token from PayPal.
        if (!array_key_exists('token', $_REQUEST)) {
            exit('Token is not received.');
        }

        // Set request-specific fields.
        $token = urlencode(htmlspecialchars($_REQUEST['token']));

        // Add request-specific fields to the request string.
        $nvpStr = "&TOKEN=$token";

        // Execute the API operation; see the PPHttpPost function above.
        $httpParsedResponseAr = $this->PPHttpPost('GetExpressCheckoutDetails', $nvpStr);

        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
            // Extract the response details.
            $payerID = $httpParsedResponseAr['PAYERID'];
            $street1 = $httpParsedResponseAr["SHIPTOSTREET"];
            if (array_key_exists("SHIPTOSTREET2", $httpParsedResponseAr)) {
                $street2 = $httpParsedResponseAr["SHIPTOSTREET2"];
            }
            $city_name = $httpParsedResponseAr["SHIPTOCITY"];
            $state_province = $httpParsedResponseAr["SHIPTOSTATE"];
            $postal_code = $httpParsedResponseAr["SHIPTOZIP"];
            $country_code = $httpParsedResponseAr["SHIPTOCOUNTRYCODE"];
       
             $httpParsedResponseAr['TOKEN'] = $token;
            return $httpParsedResponseAr;
        } else {
            // exit('GetExpressCheckoutDetails failed: ' . print_r($httpParsedResponseAr, true));
            return false;
        }
    }

    private function SetExpressCheckout($nvpStr) {

        // Execute the API operation; see the PPHttpPost function above.
        $httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $nvpStr);
        
        $environment='';
        if ($this->config->get('pp_express_test')) {
            $environment = "sandbox";
        }

        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
            // Redirect to paypal.com.
            $token = urldecode($httpParsedResponseAr["TOKEN"]);
            $payPalURL = "https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=$token";
            if ("sandbox" === $environment || "beta-sandbox" === $environment) {
                $payPalURL = "https://www.$environment.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=$token";
            }
            return $payPalURL;
           //   header("Location: $payPalURL");
           // exit;
        } else {
            exit('SetExpressCheckout failed: ' . print_r($httpParsedResponseAr, true));
        }
    }

    private function DoExpressCheckoutPayment($CheckoutDeails,$paymentAmount,$currencyID) {
        /**
         * This example assumes that a token was obtained from the SetExpressCheckout API call.
         * This example also assumes that a payerID was obtained from the SetExpressCheckout API call
         * or from the GetExpressCheckoutDetails API call.
         */
        // Set request-specific fields.
        $payerID = urlencode($CheckoutDeails['PAYERID']);
        $token = urlencode($CheckoutDeails['TOKEN']);

        $paymentType = urlencode('Sale');   // or 'Sale' or 'Order'
          // Add request-specific fields to the request string.
        $nvpStr = "&TOKEN=$token&PAYERID=$payerID&PAYMENTACTION=$paymentType&AMT=$paymentAmount&CURRENCYCODE=$currencyID";

        // Execute the API operation; see the PPHttpPost function above.
        $httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $nvpStr);
        
      
        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
            //  exit('Express Checkout Payment Completed Successfully: ' . print_r($httpParsedResponseAr, true));
            return $httpParsedResponseAr;
        } else {
           // exit('DoExpressCheckoutPayment failed: ' . print_r($httpParsedResponseAr, true));
            return false;
        }
    }

}

?>