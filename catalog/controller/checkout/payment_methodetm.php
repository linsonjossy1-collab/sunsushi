<?php  
class ControllerCheckoutPaymentMethodetm extends Controller {
  	public function index() {
		$this->language->load('checkout/checkout');
		
		$this->load->model('account/address');
		
		if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);		
		} elseif (isset($this->session->data['guest'])) {
			$payment_address = $this->session->data['guest']['payment'];
		}
                if(empty($payment_address)) {
                    $payment_address = array('firstname'=>'',
                       'lastname'=>'',
                        'company' =>'',
                        'company_id' =>'',
                        'tax_id' =>'',
                        'address_1' =>'',
                        'address_2' =>'', 
                        'postcode' => isset($_SESSION['postcode'])?$_SESSION['postcode']:'',
                        'city' => '',
                        'zone_id' => $this->config->get('config_zone_id'),
                        'zone' => '',
                        'zone_code' =>'',
                        'country_id' => $this->config->get('config_country_id'),
                        'country' => '',
                        'iso_code_2' => '',
                        'iso_code_3' => '',
                        'address_format'=>''    );
                }
		

		if (!empty($payment_address)) {
			// Totals
			$total_data = array();					
			$total = 0;
			$taxes = $this->cart->getTaxes();
			
			$this->load->model('setting/extension');
			
			$sort_order = array(); 
			
			$results = $this->model_setting_extension->getExtensions('total');
			
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
			
			array_multisort($sort_order, SORT_ASC, $results);
			
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);
		
					$etm_t = microtime(true); $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes); $GLOBALS['etm_perf'][] = array('payment_methodetm total/' . $result['code'], microtime(true) - $etm_t); // timing for ?etm_perf=1
				}
			}

			//get payment method for selected shipping
			if(isset($this->session->data['shipping_method']['code'])) {
				$shippingcode = explode(".",$this->session->data['shipping_method']['code']);
				$paymentAvail = $this->config->get($shippingcode[0].'_payment_methodes');
				if($paymentAvail=="") {
					unset($paymentAvail);
				}
				unset($shippingcode);
			}
			
			// Payment Methods
			$method_data = array();
			
			$this->load->model('setting/extension');
			
			$results = $this->model_setting_extension->getExtensions('payment');
	
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
				     //product availability for shipping
                    $this->load->language('payment/'. $result['code']);
                    $status_chk =FALSE;
                     if($this->cart->hasProducts()){
                         $message='No '.$this->language->get('text_title').' to the  products ';
                         foreach ($this->cart->getProducts() as $product) {
                             
                            if (!empty($product['payment_methodes']) and !in_array($result['code'], $product['payment_methodes'])) {//
                                $status_chk = true;
                                $message .=$product['name'] . ',';
                            }
                        }
					 }
					 
					//payment method for selected shipping
					if(isset($paymentAvail)) {
						if(!in_array($result['code'],$paymentAvail)) {
							$status_chk = true;
							$message='No '.$this->language->get('text_title').' to this shipping method ';
						}

					}


                    //product availability for shipping 	
                    $this->load->model('payment/' . $result['code']);
					if(method_exists($this->{'model_payment_' . $result['code']}, 'isposonly'))
                                     continue;
					$etm_t = microtime(true); $method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $total); 
					 $GLOBALS['etm_perf'][] = array('payment_methodetm getMethod/' . $result['code'], microtime(true) - $etm_t); // timing for ?etm_perf=1
					if ($method) {
                                            if($status_chk){
                                                $method['code']='';
                                                $method['msg']=$message;
                                                $method_data[$result['code']] = $method;
                                            }else{
                                                $method_data[$result['code']] = $method;
                                            }
						
					}
				}
			}
						 
			$sort_order = array(); 
		  
			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}
	
			array_multisort($sort_order, SORT_ASC, $method_data);			
			
			$this->session->data['payment_methods'] = $method_data;			
		}			
		
		$this->data['text_payment_method'] = $this->language->get('text_payment_method');
//                // Credit card  Charges
//                $cc_charge = (float)$this->config->get('pp_express_checkout_commision');
//                if($cc_charge) {
//                    $this->data['text_payment_method_charges'] = sprintf($this->language->get('text_payment_method_charges'),$this->currency->format($cc_charge));
//                }
//                else {
//                    $this->data['text_payment_method_charges'] = '';
//                }
		$this->data['text_comments'] = $this->language->get('text_comments');

		$this->data['button_continue'] = $this->language->get('button_continue');
                
                //
		if (empty($this->session->data['payment_methods'])) {
			$this->data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact'));
		} else if(isset($this->session->data['eway_failed'])){
                    $this->data['error_warning']=$this->session->data['eway_failed'];
                    unset($this->session->data['eway_failed']);
                }else{
			$this->data['error_warning'] = '';
		}	
               
		if (isset($this->session->data['payment_methods'])) {
			$this->data['payment_methods'] = $this->session->data['payment_methods']; 
		} else {
			$this->data['payment_methods'] = array();
		}
	        if (!isset($this->session->data['payment_method']['code'])) {
                   $defaultmethod =  current($this->data['payment_methods']);
                   
                    if(isset($defaultmethod['code']))
                        $this->session->data['payment_method']['code']= $defaultmethod['code'];
                }
                
		if (isset($this->session->data['payment_method']['code'])) {
			$this->data['code'] = $this->session->data['payment_method']['code'];
		} else {
			$this->data['code'] = '';
		}

		if (isset($this->session->data['comment'])) {
			$this->data['comment'] = $this->session->data['comment'];
		} else {
			$this->data['comment'] = '';
		}
		
		if ($this->config->get('config_checkout_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
			
			if ($information_info) {
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}
		
		if (isset($this->session->data['agree'])) { 
			$this->data['agree'] = $this->session->data['agree'];
		} else {
			$this->data['agree'] = '';
		}
			
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/payment_methodetm.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/checkout/payment_methodetm.tpl';
		} else {
			$this->template = 'default/template/checkout/payment_methodetm.tpl';
		}
		
		$this->response->setOutput($this->render());
  	}
	
	public function validate() {
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		// Validate if payment address has been set.
		$this->load->model('account/address');
		
		if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);		
		} elseif (isset($this->session->data['guest'])) {
			$payment_address = $this->session->data['guest']['payment'];
		}	
		// Validate cart has products and has stock.			
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');				
		}	
		
		// Validate minimum quantity requirments.			
		$products = $this->cart->getProducts();
				
		foreach ($products as $product) {
			$product_total = 0;
				
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}		
			
			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');
				
				break;
			}				
		}
											
		if (!$json) {
			if (!isset($this->request->post['payment_method'])) {
				$json['error']['warning'] = $this->language->get('error_payment');
			} else {
				if (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
				   
                                    $json['error']['warning'] = $this->language->get('error_payment');
                                      
				}
			}	

			if (!$json) {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
			}							
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>