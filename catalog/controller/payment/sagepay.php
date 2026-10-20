<?php
class ControllerPaymentSagepay extends Controller {
        private $log;
        public function __construct($registry) {
            parent::__construct($registry);
            $this->log = new Log('sagepay.log');
        }
	protected function index() {
		$this->language->load('payment/sagepay');
		
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		
		if ($this->config->get('sagepay_test') == 'live') {
    		$this->data['action'] = 'https://live.sagepay.com/gateway/service/vspform-register.vsp';
		} elseif ($this->config->get('sagepay_test') == 'test') {
			$this->data['action'] = 'https://test.sagepay.com/gateway/service/vspform-register.vsp';		
		} elseif ($this->config->get('sagepay_test') == 'sim') {
    		$this->data['action'] = 'https://test.sagepay.com/simulator/vspformgateway.asp';
  		} 
		
		$vendor = $this->config->get('sagepay_vendor');
		$password = $this->config->get('sagepay_password');		
		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
                $data = array();
                $data['VendorTxCode'] = 'TxCode-'.$this->session->data['order_id'];
                $data['Amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
                $data['Currency']= $order_info['currency_code'];
                $data['Description']= sprintf($this->language->get('text_payment_description'), date($this->language->get('date_format_short')), $this->session->data['order_id']);
                $data['CustomerName'] = html_entity_decode($order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
                $data['CustomerEMail'] = $order_info['email'];
                $data['BillingSurname'] = $order_info['payment_lastname'];
                $data['BillingFirstnames'] = $order_info['payment_firstname'];
                $data['BillingAddress1'] = $order_info['payment_address_1'];
                $data['BillingCity'] = $order_info['payment_city'] ;
                $data['BillingPostCode'] = $order_info['payment_postcode'];	
                $data['BillingCountry'] = 'GB';
                $data['BillingPhone'] = $order_info['telephone'];
                $data['DeliveryFirstnames'] = $order_info['shipping_firstname'];
                $data['DeliverySurname'] = $order_info['shipping_lastname'];
                $data['DeliveryAddress1'] = $order_info['shipping_address_1'];
                $data['DeliveryCity'] = $order_info['shipping_city'];
                $data['DeliveryPostCode'] = $order_info['shipping_postcode'];
                $data['DeliveryCountry'] = 'GB';
                $data['DeliveryPhone'] = $order_info['telephone'];
                $data['SuccessURL'] = str_replace('&amp;', '&', $this->url->link('payment/sagepay/success', 'order_id=' . $this->session->data['order_id']));
                $data['FailureURL'] = str_replace('&amp;', '&', $this->url->link('checkout/checkout', '', 'SSL')) ;
                
		$this->data['transaction'] = $this->config->get('sagepay_transaction');
		$this->data['vendor'] = $vendor;
		
		$crypt_data = array();
   
		foreach($data as $key => $value){
   			$crypt_data[] = $key . '=' . $value;
		}
                $this->log->write(' ORDER :' .  print_r($crypt_data,true));
                $this->data['crypt'] = $this->encryptAes(implode('&', $crypt_data), $password);
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/sagepay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/sagepay.tpl';
		} else {
			$this->template = 'default/template/payment/sagepay.tpl';
		}	
		
		$this->render();		
	}
	
	public function success() {
            $this->log->write(' CALLBACK :' .  print_r($this->request->get,true));
		if (isset($this->request->get['crypt'])) {
			$password = $this->config->get('sagepay_password');	
                        $output = $this->decryptAes($this->request->get['crypt'],$password);
			$data = $this->getToken($output);
			if ($data && is_array($data)) {
				$this->load->model('checkout/order');
		
				$message = '';
		
				if (isset($data['VPSTxId'])) { 
					$message .= 'VPSTxId: ' . $data['VPSTxId'] . "\n";
				}

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
				
				if (isset($data['CardType'])) {
					$message .= 'CardType: ' . $data['CardType'] . "\n";
				}
				
				if (isset($data['Last4Digits'])) {
					$message .= 'Last4Digits: ' . $data['Last4Digits'] . "\n";
				}
                                if (isset($data['Status'])) {
					$message .= 'Status: ' . $data['Status'] . "\n";
				}
				
				if ($data['Status'] == 'OK') {
                                        $this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('config_order_status_id'));
					$this->model_checkout_order->update($this->request->get['order_id'], $this->config->get('sagepay_order_status_id'), $message, false);
                                        $this->redirect($this->url->link('checkout/success'));
				} else {
					
                                        $this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('sagepay_mismatch_order_status_id') , $message, false);
                                        $this->redirect($this->url->link('checkout/checkoutetm'));
				}
				
				
			}
		}
	}	 
	
	private function getToken($string) {
  		$tokens = array(
   			'Status',
    		'StatusDetail',
    		'VendorTxCode',
   			'VPSTxId',
    		'TxAuthNo',
    		'Amount',
   			'AVSCV2', 
    		'AddressResult', 
    		'PostCodeResult', 
    		'CV2Result', 
    		'GiftAid', 
    		'3DSecureStatus', 
    		'CAVV',
			'AddressStatus',
			'CardType',
			'Last4Digits',
			'PayerStatus',
			'CardType'
		);		
		
  		$output = array();
		$data = array();
  
  		for ($i = count($tokens) - 1; $i >= 0; $i--){
    		$start = strpos($string, $tokens[$i]);
    		
			if ($start !== false) {
     			$data[$i]['start'] = $start;
     			$data[$i]['token'] = $tokens[$i];
			}
		}
  
		sort($data);
		
		for ($i = 0; $i < count($data); $i++){
			$start = $data[$i]['start'] + strlen($data[$i]['token']) + 1;

			if ($i == (count($data) - 1)) {
				$output[$data[$i]['token']] = substr($string, $start);
			} else {
				$length = $data[$i+1]['start'] - $data[$i]['start'] - strlen($data[$i]['token']) - 2;
				
				$output[$data[$i]['token']] = substr($string, $start, $length);
			}      

		}
  
		return $output;
	}
       
       function encryptAes(string $string, string $key): string {
            // Generate the initialization vector if not provided.
            $iv = $key;

            // Encrypt the string.
            $encryptedString = openssl_encrypt($string, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

            // Encode the encrypted string in Base64.
            return '@' . bin2hex($encryptedString);
        }
        function decryptAes(string $strIn , string $key): string {
            $iv = $key;
           
             
            // Remove the first char which is @ to flag this is AES encrypted and HEX decoding.
           $hex = substr($strIn, 1);

           // Throw exception if string is malformed
           if (!preg_match('/^[0-9a-fA-F]+$/', $hex))
           {
               throw new SagepayApiException('Invalid encryption string');
           }
           $encryptedString = pack('H*', $hex);
            // Decrypt the string.
            $decryptedString = openssl_decrypt($encryptedString, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
            return $decryptedString;
            
        }

}
?>