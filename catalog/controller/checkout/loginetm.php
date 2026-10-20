<?php  
class ControllerCheckoutLoginetm extends Controller { 
	public function index() {
		$this->language->load('checkout/checkout');
		
		$this->data['text_new_customer'] = $this->language->get('text_new_customer');
		$this->data['text_returning_customer'] = $this->language->get('text_returning_customer');
		$this->data['text_checkout'] = $this->language->get('text_checkout');
		$this->data['text_register'] = $this->language->get('text_register');
		$this->data['text_guest'] = $this->language->get('text_guest');
		$this->data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
		$this->data['text_register_account'] = $this->language->get('text_register_account');
		$this->data['text_forgotten'] = $this->language->get('text_forgotten');
 
		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_password'] = $this->language->get('entry_password');
		
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_login'] = $this->language->get('button_login');
		
		$this->data['guest_checkout'] = ($this->config->get('config_guest_checkout') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());
		
		if (isset($this->session->data['account'])) {
			$this->data['account'] = $this->session->data['account'];
		} else {
			$this->data['account'] = 'register';
		}
		
		$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/loginetm.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/checkout/loginetm.tpl';
		} else {
			$this->template = 'default/template/checkout/loginetm.tpl';
		}
				
		$this->response->setOutput($this->render());
	}
	
	public function validate() {
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkoutetm', '', 'SSL');			
		}
		
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}	
                if (empty($this->request->post['email']) || trim($this->request->post['email'])=='' || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
                   $json['error']['warning'] = $this->language->get('error_login');
                }
		if (!$json) {
			if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
				$json['error']['warning'] = $this->language->get('error_login');
			}
		
			$this->load->model('account/customer');
		
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
			
			if ($customer_info && !$customer_info['approved']) {
				$json['error']['warning'] = $this->language->get('error_approved');
			}		
		}
		
		if (!$json) {
			unset($this->session->data['guest']);
				
			// Default Addresses
			$this->load->model('account/address');
			 $in_address_satus = false;
                        if(isset($this->session->data['postcode'])){
                            $address_info = $this->model_account_address->getAddresses();
                            if(!empty($address_info)){
                                foreach($address_info as $address){
                                    if($this->session->data['postcode'] ==$address['postcode']){
                                        $in_address_satus = $address['address_id'];
                                        break;
                                    }
                                }
                            }
                        }
                        if($in_address_satus){
                           
                            $address_info = $this->model_account_address->getAddress($in_address_satus);
                        }else{
                            $address_info = $this->model_account_address->getAddress($this->customer->getAddressId());
                        }	
			//print_r($address_info);							
			if ($address_info) {
				if ($this->config->get('config_tax_customer') == 'shipping') {
					$this->session->data['shipping_country_id'] = $address_info['country_id'];
					$this->session->data['shipping_zone_id'] = $address_info['zone_id'];
					
				}
				
				if ($this->config->get('config_tax_customer') == 'payment') {
					$this->session->data['payment_country_id'] = $address_info['country_id'];
					$this->session->data['payment_zone_id'] = $address_info['zone_id'];
				}
                                
                                $this->session->data['payment_address_id'] =($in_address_satus)?$in_address_satus:$this->customer->getAddressId();
                                $this->session->data['shipping_address_id'] =$this->session->data['payment_address_id'];
                                $this->session->data['postcode'] = $address_info['postcode'];
                                $this->session->data['shipping_postcode'] = $address_info['postcode'];	
                               
			} else {
				unset($this->session->data['shipping_country_id']);	
				unset($this->session->data['shipping_zone_id']);	
				unset($this->session->data['shipping_postcode']);
				unset($this->session->data['payment_country_id']);	
				unset($this->session->data['payment_zone_id']);	
			}					
                        if(isset($this->request->post['remember_me'])){
                            $json['a1_s1_gkl_'] = base64_encode($this->customer->getId());//Hash key && JSON ENCODED DATA	
                        }
			$json['redirect'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
		}
					
		$this->response->setOutput(json_encode($json));		
	}

    public function validateemail() {
        $this->language->load('checkout/checkout');

        $this->load->model('account/customer');
        $json = array();
        if(isset($this->request->get['email'])){
            $this->request->post['email'] = $this->request->get['email'];
        }

        if ((utf8_strlen($this->request->post['email']) > 96)  || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email']) ) {
            $json['error']['email'] = $this->language->get('error_email');
        } else if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])=='0') {
             $json['error']['warning'] = $this->language->get('error_login');
        }
      
        if(!isset($json['error'])){
            $this->response->setOutput(json_encode(array('valid'=>true)));
        }else{
            $this->response->setOutput(json_encode($json));
        } 
        
    }
	
}
?>