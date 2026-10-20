<?php
class ControllerPaymentPaymentSenseDirect extends Controller {
	protected function index() {
		$this->language->load('payment/paymentsense_direct');
		
		$this->data['text_credit_card'] = $this->language->get('text_credit_card');
		$this->data['text_start_date'] = $this->language->get('text_start_date');
		$this->data['text_issue'] = $this->language->get('text_issue');
		$this->data['text_wait'] = $this->language->get('text_wait');
		
		$this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$this->data['entry_cc_start_date'] = $this->language->get('entry_cc_start_date');
		$this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$this->data['entry_cc_issue'] = $this->language->get('entry_cc_issue');
		
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		
		$this->data['months'] = array();
		
		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text'  => strftime('%m - %B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();
		
		$this->data['year_valid'] = array();
		
		for ($i = $today['year'] - 10; $i < $today['year'] + 1; $i++) {	
			$this->data['year_valid'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)), 
				'value' => strftime('%y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		$this->data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$this->data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paymentsense_direct.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/paymentsense_direct.tpl';
		} else {
			$this->template = 'default/template/payment/paymentsense_direct.tpl';
		}	
		
		$this->render();		
	}
	
	public function send() {
		$this->load->model('checkout/order');
		
		$country_codes = array(
			'Afghanistan'=>'4',
			'Albania'=>'8',
			'Algeria'=>'12',
			'American Samoa'=>'16',
			'Andorra'=>'20',
			'Angola'=>'24',
			'Anguilla'=>'660',
			'Antarctica'=>'',
			'Antigua and Barbuda'=>'28',
			'Argentina'=>'32',
			'Armenia'=>'51',
			'Aruba'=>'533',
			'Australia'=>'36',
			'Austria'=>'40',
			'Azerbaijan'=>'31',
			'Bahamas'=>'44',
			'Bahrain'=>'48',
			'Bangladesh'=>'50',
			'Barbados'=>'52',
			'Belarus'=>'112',
			'Belgium'=>'56',
			'Belize'=>'84',
			'Benin'=>'204',
			'Bermuda'=>'60',
			'Bhutan'=>'64',
			'Bolivia'=>'68',
			'Bosnia and Herzegowina'=>'70',
			'Botswana'=>'72',
			'Brazil'=>'76',
			'Brunei Darussalam'=>'96',
			'Bulgaria'=>'100',
			'Burkina Faso'=>'854',
			'Burundi'=>'108',
			'Cambodia'=>'116',
			'Cameroon'=>'120',
			'Canada'=>'124',
			'Cape Verde'=>'132',
			'Cayman Islands'=>'136',
			'Central African Republic'=>'140',
			'Chad'=>'148',
			'Chile'=>'152',
			'China'=>'156',
			'Colombia'=>'170',
			'Comoros'=>'174',
			'Congo'=>'178',
			'Cook Islands'=>'180',
			'Costa Rica'=>'184',
			'Cote D\'Ivoire'=>'188',
			'Croatia'=>'384',
			'Cuba'=>'191',
			'Cyprus'=>'192',
			'Czech Republic'=>'196',
			'Democratic Republic of Congo'=>'203',
			'Denmark'=>'208',
			'Djibouti'=>'262',
			'Dominica'=>'212',
			'Dominican Republic'=>'214',
			'Ecuador'=>'218',
			'Egypt'=>'818',
			'El Salvador'=>'222',
			'Equatorial Guinea'=>'226',
			'Eritrea'=>'232',
			'Estonia'=>'233',
			'Ethiopia'=>'231',
			'Falkland Islands (Malvinas)'=>'238',
			'Faroe Islands'=>'234',
			'Fiji'=>'242',
			'Finland'=>'246',
			'France'=>'250',
			'French Guiana'=>'254',
			'French Polynesia'=>'258',
			'French Southern Territories'=>'',
			'Gabon'=>'266',
			'Gambia'=>'270',
			'Georgia'=>'268',
			'Germany'=>'276',
			'Ghana'=>'288',
			'Gibraltar'=>'292',
			'Greece'=>'300',
			'Greenland'=>'304',
			'Grenada'=>'308',
			'Guadeloupe'=>'312',
			'Guam'=>'316',
			'Guatemala'=>'320',
			'Guinea'=>'324',
			'Guinea-bissau'=>'624',
			'Guyana'=>'328',
			'Haiti'=>'332',
			'Honduras'=>'340',
			'Hong Kong'=>'344',
			'Hungary'=>'348',
			'Iceland'=>'352',
			'India'=>'356',
			'Indonesia'=>'360',
			'Iran (Islamic Republic of)'=>'364',
			'Iraq'=>'368',
			'Ireland'=>'372',
			'Israel'=>'376',
			'Italy'=>'380',
			'Jamaica'=>'388',
			'Japan'=>'392',
			'Jordan'=>'400',
			'Kazakhstan'=>'398',
			'Kenya'=>'404',
			'Kiribati'=>'296',
			'Korea, Republic of'=>'410',
			'Kuwait'=>'414',
			'Kyrgyzstan'=>'417',
			'Lao People\'s Democratic Republic'=>'418',
			'Latvia'=>'428',
			'Lebanon'=>'422',
			'Lesotho'=>'426',
			'Liberia'=>'430',
			'Libyan Arab Jamahiriya'=>'434',
			'Liechtenstein'=>'438',
			'Lithuania'=>'440',
			'Luxembourg'=>'442',
			'Macau'=>'446',
			'Macedonia'=>'807',
			'Madagascar'=>'450',
			'Malawi'=>'454',
			'Malaysia'=>'458',
			'Maldives'=>'462',
			'Mali'=>'466',
			'Malta'=>'470',
			'Marshall Islands'=>'584',
			'Martinique'=>'474',
			'Mauritania'=>'478',
			'Mauritius'=>'480',
			'Mexico'=>'484',
			'Micronesia, Federated States of'=>'583',
			'Moldova, Republic of'=>'498',
			'Monaco'=>'492',
			'Mongolia'=>'496',
			'Montserrat'=>'500',
			'Morocco'=>'504',
			'Mozambique'=>'508',
			'Myanmar'=>'104',
			'Namibia'=>'516',
			'Nauru'=>'520',
			'Nepal'=>'524',
			'Netherlands'=>'528',
			'Netherlands Antilles'=>'530',
			'New Caledonia'=>'540',
			'New Zealand'=>'554',
			'Nicaragua'=>'558',
			'Niger'=>'562',
			'Nigeria'=>'566',
			'Niue'=>'570',
			'Norfolk Island'=>'574',
			'Northern Mariana Islands'=>'580',
			'Norway'=>'578',
			'Oman'=>'512',
			'Pakistan'=>'586',
			'Palau'=>'585',
			'Panama'=>'591',
			'Papua New Guinea'=>'598',
			'Paraguay'=>'600',
			'Peru'=>'604',
			'Philippines'=>'608',
			'Pitcairn'=>'612',
			'Poland'=>'616',
			'Portugal'=>'620',
			'Puerto Rico'=>'630',
			'Qatar'=>'634',
			'Reunion'=>'638',
			'Romania'=>'642',
			'Russian Federation'=>'643',
			'Rwanda'=>'646',
			'Saint Kitts and Nevis'=>'659',
			'Saint Lucia'=>'662',
			'Saint Vincent and the Grenadines'=>'670',
			'Samoa'=>'882',
			'San Marino'=>'674',
			'Sao Tome and Principe'=>'678',
			'Saudi Arabia'=>'682',
			'Senegal'=>'686',
			'Seychelles'=>'690',
			'Sierra Leone'=>'694',
			'Singapore'=>'702',
			'Slovak Republic'=>'703',
			'Slovenia'=>'705',
			'Solomon Islands'=>'90',
			'Somalia'=>'706',
			'South Africa'=>'710',
			'Spain'=>'724',
			'Sri Lanka'=>'144',
			'Sudan'=>'736',
			'Suriname'=>'740',
			'Svalbard and Jan Mayen Islands'=>'744',
			'Swaziland'=>'748',
			'Sweden'=>'752',
			'Switzerland'=>'756',
			'Syrian Arab Republic'=>'760',
			'Taiwan'=>'158',
			'Tajikistan'=>'762',
			'Tanzania, United Republic of'=>'834',
			'Thailand'=>'764',
			'Togo'=>'768',
			'Tokelau'=>'772',
			'Tonga'=>'776',
			'Trinidad and Tobago'=>'780',
			'Tunisia'=>'788',
			'Turkey'=>'792',
			'Turkmenistan'=>'795',
			'Turks and Caicos Islands'=>'796',
			'Tuvalu'=>'798',
			'Uganda'=>'800',
			'Ukraine'=>'804',
			'United Arab Emirates'=>'784',
			'United Kingdom'=>'826',
			'United States'=>'840',
			'Uruguay'=>'858',
			'Uzbekistan'=>'860',
			'Vanuatu'=>'548',
			'Vatican City State (Holy See)'=>'336',
			'Venezuela'=>'862',
			'Viet Nam'=>'704',
			'Virgin Islands (British)'=>'92',
			'Virgin Islands (U.S.)'=>'850',
			'Wallis and Futuna Islands'=>'876',
			'Western Sahara'=>'732',
			'Yemen'=>'887',
			'Zambia'=>'894',
			'Zimbabwe'=>'716'
		);
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
        $data = array();
			
		if (in_array($order_info['payment_country'], array_keys($country_codes))) {
			$order_country = $country_codes[$order_info['payment_country']];
		} else {
			$order_country = '';
		}
		
		$data['OrderID'] = $this->session->data['order_id'];
		
		$data['MerchantID'] = $this->config->get('paymentsense_direct_mid');
		$data['MerchantPassword'] = $this->config->get('paymentsense_direct_pass');
		
		$data['Amount'] = round($order_info['total'],2)*100;
		$data['Currency'] = 826;
		
		$data['TransactionType'] = $this->config->get('paymentsense_direct_type');		
		
		$data['Description'] = "Order ID: ". $data['OrderID'];
		
		$data['CardName'] = $_POST['cc_owner'];
		$data['CardNumber'] = $_POST['cc_number'];
		$data['ExpiryDateMonth'] = $_POST['cc_expire_date_month'];
		$data['ExpiryDateYear'] = $_POST['cc_expire_date_year'];
		$data['StartDateMonth'] = $_POST['cc_start_date_month'];
		$data['StartDateYear'] = $_POST['cc_start_date_year'];
		$data['IssueNumber'] = $_POST['cc_issue'];
		$data['CV2'] = $_POST['cc_cvv2'];
		
		$data['CV2Policy'] = $this->config->get('paymentsense_direct_cv2_policy_1') . $this->config->get('paymentsense_direct_cv2_policy_2');
		$data['AVSPolicy'] = $this->config->get('paymentsense_direct_avs_policy_1') . $this->config->get('paymentsense_direct_avs_policy_2') . $this->config->get('paymentsense_direct_avs_policy_3') . $this->config->get('paymentsense_direct_avs_policy_4');
		
		$data['Address1'] = $order_info['payment_address_1'];
		
		if ($order_info['payment_address_2']) {
        	$data['Address2'] = $order_info['payment_address_2'];
		} else {
			$data['Address2'] = "";	
		}
		
		$data['Address3'] = "";
		$data['Address4'] = "";		
		$data['City'] = $order_info['payment_city'];
		$data['State'] = "";
		$data['PostCode'] = $order_info['payment_postcode'];		
		$data['CountryCode'] = $order_country;	
			
		$data['PhoneNumber'] = $order_info['telephone'];		
		$data['EmailAddress'] = $order_info['email'];
		$data['CustomerIPAddress'] = $this->request->server['REMOTE_ADDR'];
		
		$json = array();
		
		$headers = array(
			'SOAPAction:https://www.thepaymentgateway.net/CardDetailsTransaction',
			'Content-Type: text/xml; charset = utf-8',
			'Connection: close'
		);
		
		$xml = '<?xml version="1.0" encoding="utf-8"?>';
		$xml .= '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
		$xml .= '<soap:Body>';
		$xml .= '<CardDetailsTransaction xmlns="https://www.thepaymentgateway.net/">';
		$xml .= '<PaymentMessage>';
		$xml .= '<MerchantAuthentication MerchantID="'.$data['MerchantID'].'" Password="'.$data['MerchantPassword'].'" />';
		$xml .= '<TransactionDetails Amount="'.$data['Amount'].'" CurrencyCode="'. $data['Currency'] .'">';
		$xml .= '<MessageDetails TransactionType="'. $data['TransactionType'] .'" />';
		$xml .= '<OrderID>'.$data['OrderID'].'</OrderID>';
		$xml .= '<OrderDescription>'. $data['Description'] .'</OrderDescription>';
		$xml .= '<TransactionControl>';
		$xml .= '<EchoCardType>TRUE</EchoCardType>';
		$xml .= '<EchoAVSCheckResult>TRUE</EchoAVSCheckResult>';
		$xml .= '<EchoCV2CheckResult>TRUE</EchoCV2CheckResult>';
		$xml .= '<EchoAmountReceived>TRUE</EchoAmountReceived>';
		$xml .= '<DuplicateDelay>20</DuplicateDelay>';
		$xml .= '<AVSOverridePolicy>'. $data['AVSPolicy'] .'</AVSOverridePolicy>';
		$xml .= '<CV2OverridePolicy>'. $data['CV2Policy'] .'</CV2OverridePolicy>';
		$xml .= '<CustomVariables>';
		$xml .= '<GenericVariable Name="MyInputVariable" Value="Ping" />';
		$xml .= '</CustomVariables>';
		$xml .= '</TransactionControl>';
		$xml .= '</TransactionDetails>';
		$xml .= '<CardDetails>';
		$xml .= '<CardName>'.$data['CardName'].'</CardName>';
		$xml .= '<CardNumber>'.$data['CardNumber'].'</CardNumber>';
		if ($data['ExpiryDateMonth'] != "") $xml .= '<ExpiryDate Month="'.$data['ExpiryDateMonth'].'" Year="'.$data['ExpiryDateYear'].'" />';
		if ($data['StartDateMonth'] != "") $xml .= '<StartDate Month="'.$data['StartDateMonth'].'" Year="'.$data['StartDateYear'].'" />';
		$xml .= '<CV2>'.$data['CV2'].'</CV2>';
		if ($data['IssueNumber'] != "") $xml .= '<IssueNumber>'.$data['IssueNumber'].'</IssueNumber>';
		$xml .= '</CardDetails>';
		$xml .= '<CustomerDetails>';
		$xml .= '<BillingAddress>';
		$xml .= '<Address1>'.$data['Address1'].'</Address1>';
		if ($data['Address2'] != "") $xml .= '<Address2>'.$data['Address2'].'</Address2>';
		if ($data['Address3'] != "") $xml .= '<Address3>'.$data['Address3'].'</Address3>';
		if ($data['Address4'] != "") $xml .= '<Address4>'.$data['Address4'].'</Address4>';
		$xml .= '<City>'.$data['City'].'</City>';
		if ($data['State'] != "") $xml .= '<State>'.$data['State'].'</State>';
		$xml .= '<PostCode>'.$data['PostCode'].'</PostCode>';
		$xml .= '<CountryCode>'. $data['CountryCode'] .'</CountryCode>';
		$xml .= '</BillingAddress>';
		$xml .= '<EmailAddress>'.$data['EmailAddress'].'</EmailAddress>';
		$xml .= '<PhoneNumber>'.$data['PhoneNumber'].'</PhoneNumber>';
		$xml .= '<CustomerIPAddress>'.$data['CustomerIPAddress'].'</CustomerIPAddress>';
		$xml .= '</CustomerDetails>';
		$xml .= '<PassOutData>Some data to be passed out</PassOutData>';
		$xml .= '</PaymentMessage>';
		$xml .= '</CardDetailsTransaction>';
		$xml .= '</soap:Body>';
		$xml .= '</soap:Envelope>';
		
		//$json['error'] = $xml;
		
		$gwId = 1;
		$domain = "paymentsensegateway.com";
		$port = "4430";
		$transattempt = 1;
		$soapSuccess = false;
		
		while(!$soapSuccess && $gwId <= 3 && $transattempt <= 3) {		
						
			$url = 'https://gw'.$gwId.'.'.$domain.':'.$port.'/';
			
			//$url = 'https://gw1.paymentsensegateway.com:4430/';
			
			//=================================================================================
			
			$curl = curl_init();

			

			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); 
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$ret = curl_exec($curl);
			$err = curl_errno($curl);
			$retHead = curl_getinfo($curl);
			
			curl_close($curl);
			$curl = null;
			
			//$json['error'] .= "\r\rerr=". $err ."\r\r"."response=".$ret;
			
			if( $err == 0 ) {
				$StatusCode = null;
				$soapStatusCode = null;

				if( preg_match('#<StatusCode>([0-9]+)</StatusCode>#iU', $ret, $soapStatusCode) ) {
					$StatusCode = (int)$soapStatusCode[1];
					$AuthCode = null;
					$soapAuthCode = null;
					
					$CrossReference = null;
					$soapCrossReference = null;
					
					$Message = null;
					$soapMessage = null;
					if( preg_match('#<AuthCode>([a-zA-Z0-9]+)</AuthCode>#iU', $ret, $soapAuthCode) ) {
						$AuthCode = $soapAuthCode[1];
					}
					
					if( preg_match('#<TransactionOutputData.*CrossReference="([a-zA-Z0-9]+)".*>#iU', $ret, $soapCrossReference) ) {
						$CrossReference = $soapCrossReference[1];
					}
					
					if( preg_match('#<Message>(.+)</Message>#iU', $ret, $soapMessage) ) {
						$Message = $soapMessage[1];
					}

					if( $StatusCode != 50 ) {
						$soapSuccess = true;
						switch( $StatusCode ) {
							case 0:
								$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
								
								if( preg_match('#<AddressNumericCheckResult>(.+)</AddressNumericCheckResult>#iU', $ret, $soapAVSCheck) ) {
									$AVSCheck = $soapAVSCheck[1];
								}
								
								if( preg_match('#<PostCodeCheckResult>(.+)</PostCodeCheckResult>#iU', $ret, $soapPostCodeCheck) ) {
									$PostCodeCheck = $soapPostCodeCheck[1];
								}
								
								if( preg_match('#<CV2CheckResult>(.+)</CV2CheckResult>#iU', $ret, $soapCV2Check) ) {
									$CV2Check = $soapCV2Check[1];
								}
								
								$successmessage = 'AuthCode: ' . $AuthCode . " || " . 'CrossReference: ' . $CrossReference . " || " . 'AVS Check: ' . $AVSCheck . " || " . 'Postcode Check: ' . $PostCodeCheck . " || " . 'CV2 Check: ' . $CV2Check;					
								$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('paymentsense_direct_order_status_id'), $successmessage, false);
								$json['success'] = $this->url->link('checkout/success', '', 'SSL');
								break;

							case 3:
								if( preg_match('#<ThreeDSecureOutputData>.*<PaREQ>(.+)</PaREQ>.*<ACSURL>(.+)</ACSURL>.*</ThreeDSecureOutputData>#iU', $ret, $soap3DSec) ) {
									$PaREQ = $soap3DSec[1];
									$ACSurl = $soap3DSec[2];
									
									$json['ACSURL'] = $ACSurl;
									$json['MD'] = $CrossReference;
									$json['PaReq'] = $PaREQ;
									$json['TermUrl'] = $this->url->link('payment/paymentsense_direct/callback', '', 'SSL');
									
								} else {
									$json['error'] = 'Incorrect 3DSecure data.';
									$do = false;
								}

								break;
							
							case 4:
								// Referred
								$json['error'] = 'Your card has been referred - please try a different card';
								$do = false;
								break;
							
							case 5:
								// Declined
								$json['error'] = 'Your card has been declined - ';
								
								if( preg_match('#<AddressNumericCheckResult>(.+)</AddressNumericCheckResult>#iU', $ret, $soapAVSCheck) ) {
									$AVSCheck = $soapAVSCheck[1];
								}
								
								$failedreasons = "";
								
								if ($AVSCheck == "FAILED") {
									if ($failedreasons <> "") {
										$failedreasons .= " + AVS";
									} else {
										$failedreasons = "Billing address";
									}
								}
								
								if( preg_match('#<PostCodeCheckResult>(.+)</PostCodeCheckResult>#iU', $ret, $soapPostCodeCheck) ) {
									$PostCodeCheck = $soapPostCodeCheck[1];
								}
								
								if ($PostCodeCheck == "FAILED") {
									if ($failedreasons <> "") {
										$failedreasons .= " + Postcode";
									} else {
										$failedreasons = "Postcode";
									}
								}
								
								if( preg_match('#<CV2CheckResult>(.+)</CV2CheckResult>#iU', $ret, $soapCV2Check) ) {
									$CV2Check = $soapCV2Check[1];
								}
								
								if ($CV2Check == "FAILED") {
									if ($failedreasons <> "") {
										$failedreasons .= " + CV2";
									} else {
										$failedreasons = "CV2";
									}
								}
								
								if ($failedreasons <> "") {
									$json['error'] .= $failedreasons . " checks failed. ";
								}
																
								$json['error'] .= 'Please check your billing address and card details and try again';
								$do = false;
								break;

							case 20:
								// Duplicate
								// check the previous status in order to know if the transaction was a success
								
								if( preg_match('#<PreviousTransactionResult>.*<StatusCode>([0-9]+)</StatusCode>#iU', $ret, $soapStatus2) ) {
									if( $soapStatus2[1] == '0' ) {
										$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
										if( preg_match('#<AddressNumericCheckResult>(.+)</AddressNumericCheckResult>#iU', $ret, $soapAVSCheck) ) {
											$AVSCheck = $soapAVSCheck[1];
										}
										
										if( preg_match('#<PostCodeCheckResult>(.+)</PostCodeCheckResult>#iU', $ret, $soapPostCodeCheck) ) {
											$PostCodeCheck = $soapPostCodeCheck[1];
										}
										
										if( preg_match('#<CV2CheckResult>(.+)</CV2CheckResult>#iU', $ret, $soapCV2Check) ) {
											$CV2Check = $soapCV2Check[1];
										}
										
										$successmessage = 'AuthCode: ' . $AuthCode . " || " . 'CrossReference: ' . $CrossReference . " || " . 'AVS Check: ' . $AVSCheck . " || " . 'Postcode Check: ' . $PostCodeCheck . " || " . 'CV2 Check: ' . $CV2Check . ' || ' . '3D Secure: PASSED';					
				
										$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('paymentsense_direct_order_status_id'), $successmessage, false);
										$json['success'] = $this->url->link('checkout/success', '', 'SSL');
										break;
									} else if( $soapStatus2[1] == '4' ) {
										$json['error'] = 'Your card has been referred - please try a different card';
										$do = false;
										break;
									} else if( $soapStatus2[1] == '5' ) {
										$json['error'] = 'Your card has been declined - ' . str_replace("Card declined: ","",$Message) . ' checks failed.\nPlease check your billing address and card details and try again';
										$do = false;
										break;
									} else {
										$json['error'] = 'Duplicate transaction';
										$do = false;
									}
								} else {
									$json['error'] = 'Duplicate transaction';
									$do = false;
								}
								break;

							case 30:
							default:
								// generic error
								// read error message
								if( preg_match('#<Message>(.*)</Message>#iU', $ret, $msg) ) {
									$msg = $msg[1];
								} else {
									$msg = '';
								}
								$json['error'] = 'PaymentSense Error ('.$StatusCode.') :' . $msg;
								$do = false;
								break;
						}
					}
				}
			}
			
			if($transattempt <=3) {
				$transattempt++;
			} else {
				$transattempt = 1;
				$gwId++;
			}			
		}
		
		if (!method_exists($this->tax, 'getRates')) { //v1.5.1.2 or earlier
			$this->load->library('json');
			$this->response->setOutput(Json::encode($json));
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}	 
	
	public function callback() {
		
		$this->load->model('checkout/order');
		
		$headers = array(
			'SOAPAction:https://www.thepaymentgateway.net/ThreeDSecureAuthentication',
			'Content-Type: text/xml; charset = utf-8',
			'Connection: close'
		);
		
		$MerchantID = $this->config->get('paymentsense_direct_mid');
		$Password = $this->config->get('paymentsense_direct_pass');
		$CrossReference = $_POST['MD'];
		$PaRES = $_POST['PaRes'];
		
		$xml = '<?xml version="1.0" encoding="utf-8"?>';
		$xml .= '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
		$xml .= '<soap:Body>';
		$xml .= '<ThreeDSecureAuthentication xmlns="https://www.thepaymentgateway.net/">';
		$xml .= '<ThreeDSecureMessage>';
		$xml .= '<MerchantAuthentication MerchantID="'. $MerchantID .'" Password="'. $Password .'" />';
		$xml .= '<ThreeDSecureInputData CrossReference="'. $CrossReference .'">';
		$xml .= '<PaRES>'. $PaRES .'</PaRES>';
		$xml .= '</ThreeDSecureInputData>';
		$xml .= '<PassOutData>Some data to be passed out</PassOutData>';
		$xml .= '</ThreeDSecureMessage>';
		$xml .= '</ThreeDSecureAuthentication>';
		$xml .= '</soap:Body>';
		$xml .= '</soap:Envelope>';
		
		$gwId = 1;
		$domain = "paymentsensegateway.com";
		$port = "4430";
		$transattempt = 1;
		$soapSuccess = false;
		
		while(!$soapSuccess && $gwId <= 3 && $transattempt <= 3) {		
		
			//$url = 'https://gw1.paymentsensegateway.com:4430/';
						
			$url = 'https://gw'.$gwId.'.'.$domain.':'.$port.'/';
			
			//=================================================================================
			
			$curl = curl_init();

			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); 
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	 
			$ret = curl_exec($curl);
			$err = curl_errno($curl);
			$retHead = curl_getinfo($curl);
			
			curl_close($curl);
			$curl = null;
					
			if( $err == 0 ) {
				$StatusCode = null;
				$soapStatusCode = null;

				if( preg_match('#<StatusCode>([0-9]+)</StatusCode>#iU', $ret, $soapStatusCode) ) {
					$StatusCode = (int)$soapStatusCode[1];
					$AuthCode = null;
					$soapAuthCode = null;
					
					$CrossReference = null;
					$soapCrossReference = null;
					
					$Message = null;
					$soapMessage = null;
					if( preg_match('#<AuthCode>([a-zA-Z0-9]+)</AuthCode>#iU', $ret, $soapAuthCode) ) {
						$AuthCode = $soapAuthCode[1];
					}
					
					if( preg_match('#<TransactionOutputData.*CrossReference="([a-zA-Z0-9]+)".*>#iU', $ret, $soapCrossReference) ) {
						$CrossReference = $soapCrossReference[1];
					}

					if( $StatusCode != 50 ) {
						$soapSuccess = true;
						switch( $StatusCode ) {
							case 0:
								$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
								
								if( preg_match('#<AddressNumericCheckResult>(.+)</AddressNumericCheckResult>#iU', $ret, $soapAVSCheck) ) {
									$AVSCheck = $soapAVSCheck[1];
								}
								
								if( preg_match('#<PostCodeCheckResult>(.+)</PostCodeCheckResult>#iU', $ret, $soapPostCodeCheck) ) {
									$PostCodeCheck = $soapPostCodeCheck[1];
								}
								
								if( preg_match('#<CV2CheckResult>(.+)</CV2CheckResult>#iU', $ret, $soapCV2Check) ) {
									$CV2Check = $soapCV2Check[1];
								}
								
								$successmessage = 'AuthCode: ' . $AuthCode . " || " . 'CrossReference: ' . $CrossReference . " || " . 'AVS Check: ' . $AVSCheck . " || " . 'Postcode Check: ' . $PostCodeCheck . " || " . 'CV2 Check: ' . $CV2Check . ' || ' . '3D Secure: PASSED';					
												
								$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('paymentsense_direct_order_status_id'), $successmessage, false);
								$this->redirect($this->url->link('checkout/success', '', 'SSL')); 
								break;
													
							case 4:
								// Referred
								$json['error'] = 'Transaction Referred - Please try a different card';
								$do = false;
								break;
							
							case 5:
								// Declined
								$json['error'] = 'Transaction Declined';
								$do = false;
								break;
								
							case 20:
								// Duplicate
								// check the previous status in order to know if the transaction was a success
								
								if( preg_match('#<PreviousTransactionResult>.*<StatusCode>([0-9]+)</StatusCode>#iU', $ret, $soapStatus2) ) {
									if( $soapStatus2[1] == '0' ) {
										$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
										
										if( preg_match('#<AddressNumericCheckResult>(.+)</AddressNumericCheckResult>#iU', $ret, $soapAVSCheck) ) {
											$AVSCheck = $soapAVSCheck[1];
										}
										
										if( preg_match('#<PostCodeCheckResult>(.+)</PostCodeCheckResult>#iU', $ret, $soapPostCodeCheck) ) {
											$PostCodeCheck = $soapPostCodeCheck[1];
										}
										
										if( preg_match('#<CV2CheckResult>(.+)</CV2CheckResult>#iU', $ret, $soapCV2Check) ) {
											$CV2Check = $soapCV2Check[1];
										}
										
										$successmessage = 'AuthCode: ' . $AuthCode . " || " . 'CrossReference: ' . $CrossReference . " || " . 'AVS Check: ' . $AVSCheck . " || " . 'Postcode Check: ' . $PostCodeCheck . " || " . 'CV2 Check: ' . $CV2Check . ' || ' . '3D Secure: PASSED';					
														
										$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('paymentsense_direct_order_status_id'), $successmessage, false);
										$this->redirect($this->url->link('checkout/success', '', 'SSL')); 
										break;
									} elseif( $soapStatus2[1] == '4' ) {
										$json['error'] = 'Transaction Referred - Please try a different card';
										$do = false;
										break;
									} elseif( $soapStatus2[1] == '5' ) {
										$json['error'] = 'Transaction Declined' . $Message;
										$do = false;
										break;
									} else {
										$json['error'] = 'Duplicate transaction';
										$do = false;
										break;
									}
								} else {
									$json['error'] = 'Duplicate transaction';
									$do = false;
								}
								break;						

							case 30:
							default:
								// generic error
								// read error message
								if( preg_match('#<Message>(.*)</Message>#iU', $ret, $msg) ) {
									$msg = $msg[1];
								} else {
									$msg = '';
								}
								$json['error'] = 'PaymentSense Error ('.$StatusCode.')';
								$do = false;
								break;
						}
					}
				}
			}
			
			if($transattempt <=3) {
				$transattempt++;
			} else {
				$transattempt = 1;
				$gwId++;
			}			
		}
		if ($json['error'] <> "") {
			$this->language->load('payment/paymentsense_direct');
			
			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', '', 'SSL'),
				'separator' => false
			);
	
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('button_checkout'),
				'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
				'separator' => ' :: '
			);
			
			$this->data['heading_title'] = $this->language->get('text_payment_failed_title');
			$this->data['text_message'] = $this->language->get('text_payment_failed') . '<p><b>' . $json['error'] . '</b></p><p>Please try your payment again.</p>';
			$this->data['button_continue'] = $this->language->get('button_checkout');
			$this->data['continue'] = $this->url->link('checkout/checkout');
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/failed.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/common/failed.tpl';
			} else {
				$this->template = 'default/template/common/success.tpl';
			}
			
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
}
?>