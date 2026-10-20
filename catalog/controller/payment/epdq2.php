<?php
class ControllerPaymentePDQ2 extends Controller {
        private $log;

        public function __construct($registry) {
            parent::__construct($registry);
            $this->log = new Log('epdq.log');
        }
	protected function index() {
                

		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		if($this->config->get('epdq2_test') == 1) {
			$this->data['action'] = 'https://mdepayments.epdq.co.uk/ncol/test/orderstandard.asp';
		} else {
			$this->data['action'] = 'https://payments.epdq.co.uk/ncol/prod/orderstandard.asp';
		}
		
		$this->data['pspid'] = $this->config->get('epdq2_pspid');

		$this->data['accepturl'] = $this->url->link('payment/epdq2/accept', '', 'SSL');
		$this->data['declineurl'] = $this->url->link('payment/epdq2/failure', '', 'SSL');
		$this->data['exceptionurl'] = $this->url->link('payment/epdq2/failure', '', 'SSL');
		$this->data['cancelurl'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_SERVER . "image/";
		} else {
			$server = HTTP_SERVER . "image/";
		}
		
//		if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
//			$this->data['logo'] = $server . $this->config->get('config_logo');
//		} else {
//			$this->data['logo'] = '';
//		}
//		
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && (strlen($_SERVER['HTTP_ACCEPT_LANGUAGE']) > 2)) {
			$this->data['language'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);
		} else {
			$this->data['language'] = 'en_US';
		}
		
		$this->data['currency'] =  $order_info['currency_code'];
		
		$this->data['amount'] =  (int)($order_info['total']*100);
		
		$this->data['orderid'] =  $order_info['order_id'];
		
		$this->data['cn'] =  $order_info['payment_firstname'] . " " . $order_info['payment_lastname'];
		
		$this->data['owneraddress'] = $order_info['payment_address_1'];
		
		$payment_city = $order_info['payment_city'];
		$payment_city .= ($payment_city != "") ? ", " . $order_info['payment_zone'] : $order_info['payment_zone'];
		$this->data['ownertown'] = $payment_city;
		
		$this->data['ownerzip'] = $order_info['payment_postcode'];
		$this->data['ownercty'] = $order_info['payment_iso_code_2'];
		$this->data['ownertelno'] = $order_info['telephone'];
		$this->data['email'] =  $order_info['email'];

		$this->data['shasign'] =  $this->generateSHA_IN($this->data, $this->config->get('epdq2_passphrase_in'));
		
		$this->data['back'] = $this->url->link('checkout/checkout', '', 'SSL');
                $this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['button_back'] = $this->language->get('button_back');
		
		$this->id = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/epdq2.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/epdq2.tpl';
		} else {
			$this->template = 'default/template/payment/epdq2.tpl';
		}	
		
		$this->render();
	}
	
	public function accept() {
                $this->log->write('accept : ' . print_r($this->request->get, true));
		$this->language->load('payment/epdq2');
		
		$this->load->model('checkout/order');
		
		$this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
			$this->data['base'] = HTTP_SERVER;
		} else {
			$this->data['base'] = HTTPS_SERVER;
		}
		
		$this->data['charset'] = $this->language->get('charset');
		$this->data['language'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		
		$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
		
		$this->data['text_response'] = $this->language->get('text_response');
		$this->data['text_success'] = $this->language->get('text_success');
		$this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), HTTPS_SERVER . 'index.php?route=checkout/success');
		$this->data['text_failure'] = $this->language->get('text_failure');
		
		if (isset($this->request->get['STATUS']) && ($this->request->get['STATUS'] == 9) && (isset($this->request->get['SHASIGN']))) {
			
			$SHASign = strtoupper($this->generateSHA_OUT($this->request->get, $this->config->get('epdq2_passphrase_out')));
			
			if($SHASign == $this->request->get['SHASIGN'])
			{							
				$this->model_checkout_order->confirm($this->request->get['orderID'], $this->config->get('epdq2_order_status_id'));
				
				$comment = '';
				
				if (isset($this->request->get['PSPID'])) {
					$comment .= "PSPID: " . $this->request->get['PSPID'] . "\n";
				}
				
				if (isset($this->request->get['orderID'])) {
					$comment .= "orderID: " . $this->request->get['orderID'] . "\n";
				}
				
				if (isset($this->request->get['PM'])) {
					$comment .= "PM: " . $this->request->get['PM'] . "\n";
				}
				
				if (isset($this->request->get['ACCEPTANCE'])) {
					$comment .= "ACCEPTANCE: " . $this->request->get['ACCEPTANCE'] . "\n";
				}
				
				if (isset($this->request->get['STATUS'])) {
					$comment .= "STATUS: " . $this->request->get['STATUS'] . "\n";
				}
				
				if (isset($this->request->get['CARDNO'])) {
					$comment .= "CARDNO: " . $this->request->get['CARDNO'] . "\n";
				}
				
				if (isset($this->request->get['PAYID'])) {
					$comment .= "PAYID: " . $this->request->get['PAYID'] . "\n";
				}
				
				if (isset($this->request->get['NC ERROR'])) {
					$comment .= "NC ERROR: " . $this->request->get['NC ERROR'] . "\n";
				}
				
				if (isset($this->request->get['BRAND'])) {
					$comment .= "BRAND: " . $this->request->get['BRAND'];
				}
				
				$this->model_checkout_order->update($this->request->get['orderID'], $this->config->get('epdq2_order_status_id'), $comment, TRUE);
			}
		}
		
		header("Location:  " . $this->url->link('checkout/success', '', 'SSL'));
	}
	
	public function failure() {
                $this->log->write('failure : ' . print_r($this->request->get, true));
		$this->language->load('payment/epdq2');
		
		$this->load->model('checkout/order');
		
		$this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
			$this->data['base'] = HTTP_SERVER;
		} else {
			$this->data['base'] = HTTPS_SERVER;
		}
		
		$this->data['charset'] = $this->language->get('charset');
		$this->data['language'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		
		$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
		
		$this->data['text_response'] = $this->language->get('text_response');
		$this->data['text_success'] = $this->language->get('text_success');
		$this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), HTTPS_SERVER . 'index.php?route=checkout/error');
		$this->data['text_failure'] = $this->language->get('text_failure');
		
		if (isset($this->request->get['STATUS']) && (isset($this->request->get['SHASIGN']))) {
			
			$this->session->data['NCERROR'] = $this->request->get['NCERROR'];
			
			$this->request->get['NCERROR'] . "\n";
			
			$SHASign = strtoupper($this->generateSHA_OUT($this->request->get, $this->config->get('epdq2_passphrase_out')));
			
										
				$this->model_checkout_order->confirm($this->request->get['orderID'], $this->config->get('epdq2_declined_order_status_id'));
				
				$comment = '';
				
				if (isset($this->request->get['PSPID'])) {
					$comment .= "PSPID: " . $this->request->get['PSPID'] . "\n";
				}
				
				if (isset($this->request->get['orderID'])) {
					$comment .= "orderID: " . $this->request->get['orderID'] . "\n";
				}
				
				if (isset($this->request->get['PM'])) {
					$comment .= "PM: " . $this->request->get['PM'] . "\n";
				}
				
				if (isset($this->request->get['ACCEPTANCE'])) {
					$comment .= "ACCEPTANCE: " . $this->request->get['ACCEPTANCE'] . "\n";
				}
				
				if (isset($this->request->get['STATUS'])) {
					$comment .= "STATUS: " . $this->request->get['STATUS'] . "\n";
				}
				
				if (isset($this->request->get['CARDNO'])) {
					$comment .= "CARDNO: " . $this->request->get['CARDNO'] . "\n";
				}
				
				if (isset($this->request->get['PAYID'])) {
					$comment .= "PAYID: " . $this->request->get['PAYID'] . "\n";
				}
				
				if (isset($this->request->get['NCERROR'])) {
					$comment .= "NCERROR: " . $this->request->get['NCERROR'] . "\n";
				}
				
				if (isset($this->request->get['BRAND'])) {
					$comment .= "BRAND: " . $this->request->get['BRAND'];
				}
				
				$this->model_checkout_order->update($this->request->get['orderID'], $this->config->get('epdq2_declined_order_status_id'), $comment, TRUE);

		}
		
		header("Location:" . $this->url->link('checkout/checkout', '', 'SSL'));
	}
	
	protected function generateSHA_IN($vars, $shain) {

		require_once(DIR_SYSTEM . "library/mhash.php");
		$vars = array_change_key_case($vars, CASE_UPPER);
		ksort($vars);
		
		$string = "";
		foreach($vars as $key => $value) {
			if($value == "" || !isset($value)) {
				continue;
			}
			
			if($key == "SHASIGN" || $key == "BUTTON_CONFIRM" || $key == "BUTTON_BACK" || $key == "ACTION") {
				continue;
			}
			if($key == "AMOUNT") {
				$avalue = round($value, 2);
				$string.= strtoupper($key) . "=" . $avalue . $shain;
				continue;
			}
                        $value = str_replace(array("\r", "\n", "\r\n")," ",$value);
			$string.= strtoupper($key) . "=" . $value . $shain;
		}
		
		$sha1 = new fbMhash();
		return strtoupper($sha1->mhashhex(MHASH_SHA1, $string));
	}
	
	protected function generateSHA_OUT($vars, $shaout) {
		require_once(DIR_SYSTEM . "library/mhash.php");
		$vars = array_change_key_case($vars, CASE_UPPER);
		ksort($vars);
		
		$string = "";
		foreach($vars as $key => $value) {
			if($value == "" || !isset($value)) {
				continue;
			}
			
			if($key == "SHASIGN" || $key == "_ROUTE_" || $key == "ROUTE") {
				continue;
			}
			
			if($key == "AMOUNT") {
				$avalue = round($value, 2);
				$string.= strtoupper($key) . "=" . $avalue . $shaout;
				continue;
			}
			$string.= strtoupper($key) . "=" . $value . $shaout;
		}
		
		$sha1 = new fbMhash();
		
		return strtoupper($sha1->mhashhex(MHASH_SHA1, $string));
	}
	
	public function callback() {
                $this->log->write('callback : ' . print_r($this->request->post, true));
		$this->load->model('checkout/order');

		if (isset($this->request->post['STATUS'])) {
			$order_info = $this->model_checkout_order->getOrder($this->request->post['orderID']);
			
			//check if order is confirmed
			if($order_info['order_status_id'] == 0) {
				// if not confirmed confirm and notify
				$SHASign = $this->generateSHA_OUT($this->request->post, $this->config->get('epdq2_passphrase_out'));
				if($SHASign == $this->request->post['SHASIGN']) {
					if($this->request->post['STATUS'] == 9) {
						$this->model_checkout_order->confirm($this->request->post['orderID'], $this->config->get('epdq2_order_status_id'));
						
						$comment = '';
						
						if (isset($this->request->post['PSPID'])) {
							$comment .= "PSPID: " . $this->request->post['PSPID'] . "\n";
						}
						
						if (isset($this->request->post['orderID'])) {
							$comment .= "orderID: " . $this->request->post['orderID'] . "\n";
						}
						
						if (isset($this->request->post['PM'])) {
							$comment .= "PM: " . $this->request->post['PM'] . "\n";
						}
						
						if (isset($this->request->post['ACCEPTANCE'])) {
							$comment .= "ACCEPTANCE: " . $this->request->post['ACCEPTANCE'] . "\n";
						}
						
						if (isset($this->request->post['STATUS'])) {
							$comment .= "STATUS: " . $this->request->post['STATUS'] . "\n";
						}
						
						if (isset($this->request->post['CARDNO'])) {
							$comment .= "CARDNO: " . $this->request->post['CARDNO'] . "\n";
						}
						
						if (isset($this->request->post['PAYID'])) {
							$comment .= "PAYID: " . $this->request->post['PAYID'] . "\n";
						}
						
						if (isset($this->request->post['NCERROR'])) {
							$comment .= "NC ERROR: " . $this->request->post['NCERROR'] . "\n";
						}
						
						if (isset($this->request->post['BRAND'])) {
							$comment .= "BRAND: " . $this->request->post['BRAND'];
						}
						
						$this->model_checkout_order->confirm($this->request->post['orderID'], $this->config->get('epdq2_order_status_id'), $comment, TRUE);
					} else {
						$this->model_checkout_order->confirm($this->request->post['orderID'], $this->config->get('epdq2_declined_order_status_id'));
						
						$comment = '';
						
						if (isset($this->request->post['PSPID'])) {
							$comment .= "PSPID: " . $this->request->post['PSPID'] . "\n";
						}
						
						if (isset($this->request->post['orderID'])) {
							$comment .= "orderID: " . $this->request->post['orderID'] . "\n";
						}
						
						if (isset($this->request->post['PM'])) {
							$comment .= "PM: " . $this->request->post['PM'] . "\n";
						}
						
						if (isset($this->request->post['ACCEPTANCE'])) {
							$comment .= "ACCEPTANCE: " . $this->request->post['ACCEPTANCE'] . "\n";
						}
						
						if (isset($this->request->post['STATUS'])) {
							$comment .= "STATUS: " . $this->request->post['STATUS'] . "\n";
						}
						
						if (isset($this->request->post['CARDNO'])) {
							$comment .= "CARDNO: " . $this->request->post['CARDNO'] . "\n";
						}
						
						if (isset($this->request->post['PAYID'])) {
							$comment .= "PAYID: " . $this->request->post['PAYID'] . "\n";
						}
						
						if (isset($this->request->post['NCERROR'])) {
							$comment .= "NC ERROR: " . $this->request->post['NCERROR'] . "\n";
						}
						
						if (isset($this->request->post['BRAND'])) {
							$comment .= "BRAND: " . $this->request->post['BRAND'];
						}
						
						$this->model_checkout_order->update($this->request->post['orderID'], $this->config->get('epdq2_declined_order_status_id'), $comment, TRUE);
					}
				}
			}
		}
		
		$this->http_response_code(200);
	}

    protected function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }

}
?>