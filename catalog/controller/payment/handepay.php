<?php
/**
 * NOTE: OpenCart uses camelCase for functions/variables and
 * 		 snake_case for database fields (e.g. order_status_id).
 *
 * 		 Retrieved model data simply returns field name and their
 * 		 value in snake_case rather than camelCase otherwise used
 * 		 in OpenCart's PHP coding standards
 */
class ControllerPaymentHandepay extends Controller {
     private $log;
	public function __construct($obj) {
		parent::__construct($obj);
		$this->load->language('payment/handepay');
		$this->data['merchantid']     = $this->config->get('handepay_merchantid');
		$this->data['merchantsecret'] = $this->config->get('handepay_merchantsecret');
		$this->data['countrycode'] = $this->config->get('handepay_countrycode');
		$this->data['currencycode'] = $this->config->get('handepay_currencycode');
                $this->log = new Log('handepay.log');
	}
	protected function index() {

		$this->load->language('payment/handepay');

		if ($this->config->get('handepay_module_type') == 'hosted') {
			$this->createHostedForm();
		}

		if ($this->config->get('handepay_module_type') == 'direct') {
			$this->createDirectForm();
		}

		if($this->config->get('handepay_module_type') == 'iframe'){
			$this->createEmbeddedForm();
		}

		$this->render();

	}

	public function createHostedForm() {

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$orderId = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$this->data['amount'] = (int)($this->currency->format(
			$orderId['total'],
			$orderId['currency_code'],
			$orderId['currency_value'],
			false
		) * 100);

		$this->data['trans_id']  = $this->session->data['order_id'];
		$this->data['callback']  = $this->url->link('payment/handepay/callback', '', 'SSL');
                $this->data['redirect']  = $this->url->link('payment/handepay/success', '', 'SSL');
		$this->data['bill_name'] = $orderId['payment_firstname'] . ' ' . $orderId['payment_lastname'];

		$this->data['bill_addr'] = "";
		$addressFields = [
			'payment_address_1',
			'payment_address_2',
			'payment_city',
		//	'payment_zone',
			'payment_country'
		];
		foreach ($addressFields as $item) {
			$this->data['bill_addr'] .= $orderId[$item] . ($item == 'payment_country' ? "" : ",\n");
		}

		$this->data['bill_post_code'] = $orderId['payment_postcode'];
		$this->data['bill_email']     = $orderId['email'];
		$this->data['bill_tel']       = $orderId['telephone'];
		$this->template = $this->config->get('config_template') . '/template/payment/handepay_hosted.tpl';
	}

	public function createEmbeddedForm() {

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$orderId = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$this->data['amount'] = (int)($this->currency->format(
			$orderId['total'],
			$orderId['currency_code'],
			$orderId['currency_value'],
			false
		) * 100);

		$this->data['trans_id']  = $this->session->data['order_id'];
		$this->data['callback']  = $this->url->link('payment/handepay/callback', '', 'SSL');
                $this->data['redirect']  = $this->url->link('payment/handepay/success', '', 'SSL');
		$this->data['bill_name'] = $orderId['payment_firstname'] . ' ' . $orderId['payment_lastname'];

		$this->data['bill_addr'] = "";
		$addressFields = [
			'payment_address_1',
			'payment_address_2',
			'payment_city',
		//	'payment_zone',
			'payment_country'
		];
		foreach ($addressFields as $item) {
			$this->data['bill_addr'] .= $orderId[$item] . ($item == 'payment_country' ? "" : ",\n");
		}

		$this->data['bill_post_code'] = $orderId['payment_postcode'];
		$this->data['bill_email']     = $orderId['email'];
		$this->data['bill_tel']       = $orderId['telephone'];

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/handepay_iframe.tpl')
		) {
			$this->template = $this->config->get('config_template') . '/template/payment/handepay_iframe.tpl';
		} else {

			$this->template = 'default/template/payment/handepay_iframe.tpl';

		}

	}

	public function createDirectForm() {

		$this->data['cards'] = array();

		$this->data['cards'][] = array(
			'text'  => 'Visa',
			'value' => 'VISA'
		);

		$this->data['cards'][] = array(
			'text'  => 'MasterCard',
			'value' => 'MC'
		);

		$this->data['cards'][] = array(
			'text'  => 'Visa Delta/Debit',
			'value' => 'DELTA'
		);

		$this->data['cards'][] = array(
			'text'  => 'Solo',
			'value' => 'SOLO'
		);

		$this->data['cards'][] = array(
			'text'  => 'Maestro',
			'value' => 'MAESTRO'
		);

		$this->data['cards'][] = array(
			'text'  => 'Visa Electron UK Debit',
			'value' => 'UKE'
		);

		$this->data['cards'][] = array(
			'text'  => 'American Express',
			'value' => 'AMEX'
		);

		$this->data['cards'][] = array(
			'text'  => 'Diners Club',
			'value' => 'DC'
		);

		$this->data['cards'][] = array(
			'text'  => 'Japan Credit Bureau',
			'value' => 'JCB'
		);

		$this->data['cc_cardholder_name'] 		= $this->language->get('cc_cardholder_name');
		$this->data['cc_card_number']      		= $this->language->get('cc_card_number');
		$this->data['cc_card_start_date']       = $this->language->get('cc_card_start_date');
		$this->data['cc_card_start_date_help']  = $this->language->get('cc_card_start_date_help');
		$this->data['cc_card_expiry_date'] 		= $this->language->get('cc_card_expiry_date');
		$this->data['cc_card_cvv']         		= $this->language->get('cc_card_cvv');
		$this->data['cc_card_type']        		= $this->language->get('cc_card_type');
		$this->data['cc_card_issue']       		= $this->language->get('cc_card_issue');
		$this->data['cc_card_issue_help']  		= $this->language->get('cc_card_issue_help');
		$this->data['text_credit_card']    		= $this->language->get('text_credit_card');
		$this->data['button_confirm']      		= $this->language->get('button_confirm');


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/handepay_direct.tpl')
		) {
			$this->template = $this->config->get('config_template') . '/template/payment/handepay_direct.tpl';
		} else {
			$this->template = 'default/template/payment/handepay_direct.tpl';
		}

	}
	public function getResponseTemplate(){
		return array(
			'orderRef',
			'signature',
			'responseCode',
			'transactionUnique',
			'responseMessage',
			'action'
		);
	}
	public function hasKeys($array, $keys) {
		$checkKeys = array_keys($array);
		$str = '';
		foreach ($keys as $key){
			if(!array_key_exists($key, $array)) {
				return false;
			}
		}
		return true;
	}
	/**
	 * callback is used with the hosted form integration after a payment attempt to further process the order
	 */
	public function success() {
		//Setup page headers
		$isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
		$this->data['base'] 			 = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
		$this->data['language']  		 = $this->language->get('code');
		$this->data['direction'] 		 = $this->language->get('direction');
		//Page titles
		$this->data['title'] 			 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
		$this->data['heading_title'] 	 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
		$this->data['text_response'] 	 = $this->language->get('text_response');
		//Success text
		$this->data['text_success'] 	 = $this->language->get('text_success');
		$this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
		//Error text
		$this->data['text_failure']      = $this->language->get('text_failure');
		$this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));
		//Mismatch
		$this->data['text_mismatch']	 = $this->language->get('text_mismatch');
		//Start processing response data
		$data = $this->request->post;
               // $this->log->write('Handepay success: ' .print_r($data,true));
		$error = false;
		//Make sure it's a valid request
		if ($this->hasKeys($data, $this->getResponseTemplate())) {
			$this->load->model('checkout/order');
			$orderId = $data['transactionUnique'];
			$order = $this->model_checkout_order->getOrder($orderId);
			$amountExpected = (int)($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false) * 100);
			if (intval($data['responseCode']) == 0 && $data['amountReceived'] == $amountExpected) {
				//Only update if the order id has not been properly set yet
				if ($order['order_status_id'] == 0) {
					$this->model_checkout_order->confirm($orderId, $this->config->get('handepay_order_status_id'));
					$message = $this->buildMessage();
					$this->model_checkout_order->update($orderId, $this->config->get('handepay_order_status_id'), $message, false);
				}

				$this->data['continue'] = $this->url->link('checkout/success');
				$this->template = $this->config->get('config_template') . '/template/payment/handepay_success.tpl';
				$this->children = array(
					'common/column_left',
					'common/column_right',
					'common/content_top',
					'common/content_bottom',
					'common/footer',
					'common/header'
				);
				$this->response->setOutput($this->render());
			} else {
				$error = true;
			}
		} else {
			$error = true;
		}
		if ($error) {
			$this->data['continue'] = $this->url->link('checkout/checkoutetm');
			$this->template = $this->config->get('config_template') . '/template/payment/handepay_failure.tpl';
			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
			$this->response->setOutput($this->render());
		}

	}
        /**
	 * callback is used with the hosted form integration after a payment attempt to further process the order
	 */
	public function callback() {
		//Setup page headers
		$isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
		$this->data['base'] 			 = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
		$this->data['language']  		 = $this->language->get('code');
		$this->data['direction'] 		 = $this->language->get('direction');
		//Page titles
		$this->data['title'] 			 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
		$this->data['heading_title'] 	 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
		$this->data['text_response'] 	 = $this->language->get('text_response');
		//Success text
		$this->data['text_success'] 	 = $this->language->get('text_success');
		$this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
		//Error text
		$this->data['text_failure']      = $this->language->get('text_failure');
		$this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));
		//Mismatch
		$this->data['text_mismatch']	 = $this->language->get('text_mismatch');
		//Start processing response data
		$data = $this->request->post;
                $this->log->write('Handepay callback: '. print_r($data,true));
		$error = false;
		//Make sure it's a valid request
		if ($this->hasKeys($data, $this->getResponseTemplate())) {
			$this->load->model('checkout/order');
			$orderId = $data['transactionUnique'];
			$order = $this->model_checkout_order->getOrder($orderId);
			$amountExpected = (int)($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false) * 100);
			if (intval($data['responseCode']) == 0 && $data['amountReceived'] == $amountExpected) {
				//Only update if the order id has not been properly set yet
				if ($order['order_status_id'] == 0) {
					$this->model_checkout_order->confirm($orderId, $this->config->get('handepay_order_status_id'));
					$message = $this->buildMessage();
					$this->model_checkout_order->update($orderId, $this->config->get('handepay_order_status_id'), $message, false);
				}
                                /*
				$this->data['continue'] = $this->url->link('checkout/success');
				$this->template = $this->config->get('config_template') . '/template/payment/handepay_success.tpl';
				$this->children = array(
					'common/column_left',
					'common/column_right',
					'common/content_top',
					'common/content_bottom',
					'common/footer',
					'common/header'
				);
				$this->response->setOutput($this->render());
                                 * 
                                 */
			} else {
				$error = true;
			}
		} else {
			$error = true;
		}
		if ($error) {
			/* $this->data['continue'] = $this->url->link('checkout/cart');
			$this->template = $this->config->get('config_template') . '/template/payment/handepay_failure.tpl';
			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
			$this->response->setOutput($this->render());
                         * 
                         */
                        $this->log->write('Handepay callback: '. print_r($data,true));     
		}
                echo "OK";
                die();

	}

	private function buildMessage() {
		$data = $this->request->post;

		$msg = "Payment " . ($data['responseCode'] == 0 ? "Successful" : "Unsuccessful") . "<br/><br/>" .
				"Amount Received: " . (isset($data['amountReceived']) ? floatval($data['amountReceived']) / 100 : 'Unknown') . "<br/>" .
				"Message: \"" . ucfirst($data['responseMessage']) . "\"</br>" .
				"Xref: " . $data['xref'] . "<br/>" .
				(isset($data['cv2Check']) ? "CV2 Check: " . ucfirst($data['cv2Check']) . "</br>": '') .
				(isset($data['addressCheck']) ? "Address Check: " . ucfirst($data['addressCheck']) . "</br>": '') .
				(isset($data['postcodeCheck']) ? "Postcode Check: " . ucfirst($data['postcodeCheck']) . "</br>": '');

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
		return $msg;
	}
    public function send() {
        /* if ($this->config->get('sagepay_direct_test') == 'live') {
            $url = 'https://live.handepay.co.uk/gateway/service/vspdirect-register.vsp';
        } elseif ($this->config->get('sagepay_direct_test') == 'test') {
            $url = 'https://test.handepay.co.uk/gateway/service/vspdirect-register.vsp';		
        } elseif ($this->config->get('sagepay_direct_test') == 'sim') {
            $url = 'https://test.handepay.co.uk/Simulator/VSPDirectGateway.asp';
        } 
         * 
         */		
        $url = 'https://live.handepay.co.uk/gateway/service/vspdirect-register.vsp';
        
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data = array();

        $data['VPSProtocol'] = '2.23';
        $data['ReferrerID'] = 'E511AF91-E4A0-42DE-80B0-09C981A3FB61';
        $data['Vendor'] = $this->config->get('sagepay_direct_vendor');
        $data['VendorTxCode'] = $this->session->data['order_id'];
        $data['Amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false);
        $data['Currency'] = $this->currency->getCode();
        $data['Description'] = substr($this->config->get('config_name'), 0, 100);
        $data['CardHolder'] = $this->request->post['cc_owner'];
        $data['CardNumber'] = $this->request->post['cc_number'];
        $data['ExpiryDate'] = $this->request->post['cc_expire_date_month'] . substr($this->request->post['cc_expire_date_year'], 2);
        $data['CardType'] = $this->request->post['cc_type'];
        $data['TxType'] = $this->config->get('sagepay_direct_transaction');
        $data['StartDate'] = $this->request->post['cc_start_date_month'] . substr($this->request->post['cc_start_date_year'], 2);
        $data['IssueNumber'] = $this->request->post['cc_issue'];
        $data['CV2'] = $this->request->post['cc_cvv2'];

        $data['BillingSurname'] = substr($order_info['payment_lastname'], 0, 20);
        $data['BillingFirstnames'] = substr($order_info['payment_firstname'], 0, 20);
        $data['BillingAddress1'] = substr($order_info['payment_address_1'], 0, 100);

        if ($order_info['payment_address_2']) {
        $data['BillingAddress2'] = $order_info['payment_address_2'];
        }

        $data['BillingCity'] = substr($order_info['payment_city'], 0, 40);
        $data['BillingPostCode'] = substr($order_info['payment_postcode'], 0, 10);
        $data['BillingCountry'] = $order_info['payment_iso_code_2'];

        if ($order_info['payment_iso_code_2'] == 'US') {
                $data['BillingState'] = $order_info['payment_zone_code'];
        }

        $data['BillingPhone'] = substr($order_info['telephone'], 0, 20);

        if ($this->cart->hasShipping()) {
                $data['DeliverySurname'] = substr($order_info['shipping_lastname'], 0, 20);
                $data['DeliveryFirstnames'] = substr($order_info['shipping_firstname'], 0, 20);
                $data['DeliveryAddress1'] = substr($order_info['shipping_address_1'], 0, 100);

                if ($order_info['shipping_address_2']) {
                $data['DeliveryAddress2'] = $order_info['shipping_address_2'];
                }		

                $data['DeliveryCity'] = substr($order_info['shipping_city'], 0, 40);
                $data['DeliveryPostCode'] = substr($order_info['shipping_postcode'], 0, 10);
                $data['DeliveryCountry'] = $order_info['shipping_iso_code_2'];

                if ($order_info['shipping_iso_code_2'] == 'US') {
                        $data['DeliveryState'] = $order_info['shipping_zone_code'];
                }

                $data['CustomerName'] = substr($order_info['firstname'] . ' ' . $order_info['lastname'], 0, 100);
                $data['DeliveryPhone'] = substr($order_info['telephone'], 0, 20);
        } else {
                $data['DeliveryFirstnames'] = $order_info['payment_firstname'];
        $data['DeliverySurname'] = $order_info['payment_lastname'];
        $data['DeliveryAddress1'] = $order_info['payment_address_1'];

                if ($order_info['payment_address_2']) {
                $data['DeliveryAddress2'] = $order_info['payment_address_2'];
                }

        $data['DeliveryCity'] = $order_info['payment_city'];
        $data['DeliveryPostCode'] = $order_info['payment_postcode'];
        $data['DeliveryCountry'] = $order_info['payment_iso_code_2'];

                if ($order_info['payment_iso_code_2'] == 'US') {
                        $data['DeliveryState'] = $order_info['payment_zone_code'];
                }

                $data['DeliveryPhone'] = $order_info['telephone'];			
        }		

        $data['CustomerEMail'] = substr($order_info['email'], 0, 255);
        $data['Apply3DSecure'] = '0';
        $data['ClientIPAddress'] = $this->request->server['REMOTE_ADDR'];

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = array();

        $response_info = explode(chr(10), $response);

        foreach ($response_info as $string) {
                if (strpos($string, '=')) {
                        $parts = explode('=', $string, 2);

                        $data[trim($parts[0])] = trim($parts[1]);
                }
        }

        $json = array();

        if ($data['Status'] == '3DAUTH') {
                $json['ACSURL'] = $data['ACSURL'];
                $json['MD'] = $data['MD'];
                $json['PaReq'] = $data['PAReq'];
                $json['TermUrl'] = $this->url->link('payment/handepay/callback');
        } elseif ($data['Status'] == 'OK' || $data['Status'] == 'AUTHENTICATED' || $data['Status'] == 'REGISTERED') {
                $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));

                $message = '';

                if (isset($data['TxAuthNo'])) {
                        $message .= 'TxAuthNo: ' . $data['TxAuthNo'] . "\n";
                }

                if (isset($data['AVSCV2'])) {
                        $message .= 'AVSCV2: ' . $data['AVSCV2'] . "\n";
                }

                if (isset($data['AddressResult'])) {
                        $message .= 'AddressResult: ' . $data['AddressResult'] . "\n";
                }

                if (isset($data['PostCodeResult'])) {
                        $message .= 'PostCodeResult: ' . $data['PostCodeResult'] . "\n";
                }

                if (isset($data['CV2Result'])) {
                        $message .= 'CV2Result: ' . $data['CV2Result'] . "\n";
                }

                if (isset($data['3DSecureStatus'])) {
                        $message .= '3DSecureStatus: ' . $data['3DSecureStatus'] . "\n";
                }

                if (isset($data['CAVV'])) {
                        $message .= 'CAVV: ' . $data['CAVV'] . "\n";
                }

                $this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('handepay_order_status_id'), $message, false);

                $json['success'] = $this->url->link('checkout/success'); 			
        } else {
                $json['error'] = $data['StatusDetail'];
        }

        $this->response->setOutput(json_encode($json));
    }

}
