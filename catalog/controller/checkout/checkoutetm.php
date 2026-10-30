<?php

class ControllerCheckoutCheckoutetm extends Controller {
    private $error = array();
    public function index() {
        if(isset($this->session->data['mobileapp_payment'])) { // FROM MOBILE APP
            $this->log->write(' MOBILE PAYMENT');
            if (isset($this->session->data['mobileapp_inappsafari'])) {
                $this->log->write(' MOBILE PAYMENT COMPLTE mobileapp_inappsafari');
                $this->data['continue'] =  $this->config->get('config_iphoneapp_url_scheme').'://?paystatus=success&order_id='.$this->session->data['order_id'];
                $this->data['button_continue'] = $this->language->get('button_continue');
            }
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/orderpaymenturl_success.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/module/orderpaymenturl_success.tpl';
            } else {
                $this->template = 'default/template/module/orderpaymenturl_success.tpl';
            }
            $this->response->setOutput($this->render());
            return;          
        }
        
        if(isset($this->session->data['added_bundle']) && $this->session->data['added_bundle']) {
            foreach($this->session->data['added_bundle'] as $main_product => $main_products) {
                foreach($main_products as $bkey=>$added_bundle){
                    foreach ($added_bundle as $bid=>$qty){
                        if(isset($this->session->data['cart'][$bkey])){
                            if(($this->session->data['cart'][$bkey]-(int)$qty)>0){
                                $this->session->data['cart'][$bkey]=$this->session->data['cart'][$bkey]-(int)$qty;
                        }else{
                                $this->cart->remove($bkey);
                        }
                        }               
                    }
                }
                unset($this->session->data['added_bundle'][$main_product]);
            }
        }    
        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $this->redirect($this->config->get('config_url') .'menu#');
        }
        $this->language->load('checkout/checkout');
        // Validate minimum quantity requirments.		
        
        if (isset($this->request->post['order_requested_time'])) {
            $this->session->data['order_requested_time'] = $this->request->post['order_requested_time'];
        }
        if (isset($this->request->post['delivery_method'])) {
			$this->session->data['delivery_method'] = $this->request->post['delivery_method'];		
		}

        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $this->redirect($this->url->link('checkout/cart'));
            }
        }


        // Group order
        if (isset($this->session->data['grouporderclient'])) {
            $this->redirect($this->url->link('account/grouporder/clinetchekout', '', 'SSL'));
        }
  

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_cart'),
            'href' => $this->url->link('checkout/cart'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('checkout/checkoutetm', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );


        $this->data['heading_title'] = $this->language->get('heading_title');
        
        $this->data['text_cart'] = $this->language->get('text_cart');
        $this->data['text_checkout_option'] = $this->language->get('text_checkout_option');
        $this->data['text_checkout_account'] = $this->language->get('text_checkout_account');
        $this->data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
        $this->data['text_checkout_shipping_address'] = $this->language->get('text_checkout_shipping_address');
        $this->data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');
        $this->data['text_checkout_payment_method'] = $this->language->get('text_checkout_payment_method');
        $this->data['text_checkout_confirm'] = $this->language->get('text_checkout_confirm');
        $this->data['text_modify'] = $this->language->get('text_modify');

        $this->data['text_checkout_help'] = $this->language->get('text_checkout_help');
        $this->data['text_new_customer'] = $this->language->get('text_new_customer');
        $this->data['text_existing_customer'] = $this->language->get('text_existing_customer');
        $this->data['text_guest'] = $this->language->get('text_guest');
        $this->data['text_new_customer_register'] = $this->language->get('text_new_customer_register');

        

        $this->language->load('checkout/cart');
        
        $this->data['text_next'] = $this->language->get('text_next');
        $this->data['text_next_choice'] = $this->language->get('text_next_choice');
        $this->data['text_use_coupon'] = $this->language->get('text_use_coupon');
        $this->data['text_use_voucher'] = $this->language->get('text_use_voucher');

        $this->data['text_select'] = $this->language->get('text_select');
        $this->data['text_none'] = $this->language->get('text_none');


        $this->data['entry_coupon'] = $this->language->get('entry_coupon');
        $this->data['entry_voucher'] = $this->language->get('entry_voucher');
       
        $this->data['entry_country'] = $this->language->get('entry_country');
        $this->data['entry_zone'] = $this->language->get('entry_zone');
        $this->data['entry_postcode'] = $this->language->get('entry_postcode');

        $this->data['button_coupon'] = $this->language->get('button_coupon');
        $this->data['button_voucher'] = $this->language->get('button_voucher');
        $this->data['button_reward'] = $this->language->get('button_reward');
        
        $this->data['button_processing'] = $this->language->get('button_processing');

       
        // $this->data['button_continue'] = $this->language->get('text_confirmbutton');
        $title = '';
        if(isset($this->session->data['shipping_method']['title'])) {
            $title = $this->session->data['shipping_method']['title'];
        }
        $this->data['button_continue'] = sprintf($this->language->get('text_confirmbutton'),$title);
        
        $this->data['button_continue_shopping'] = $this->language->get('button_continue_shopping');
        $this->data['label_coupon'] = $this->language->get('label_coupon');
        $this->data['label_voucher'] = $this->language->get('label_voucher');
        $this->data['label_reward'] = $this->language->get('label_reward'); 
        $this->data['label_enter_coupon'] = $this->language->get('label_enter_coupon');
        $this->data['label_enter_voucher'] = $this->language->get('label_enter_voucher');
        $this->data['button_apply'] = $this->language->get('button_apply');

        $this->data['text_checkout_from_warning'] = $this->language->get('text_checkout_from_warning');
        $this->data['text_customer_type_warning'] = $this->language->get('text_customer_type_warning');
        $this->data['text_customer_login_warning'] = $this->language->get('text_customer_login_warning');
        
        
        

        // Gift Voucher
        $this->data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $key => $voucher) {
                $this->data['vouchers'][] = array(
                    'key' => $key,
                    'description' => $voucher['description'],
                    'amount' => $this->currency->format($voucher['amount']),
                    'remove' => $this->url->link('checkout/cart', 'remove=' . $key)
                );
            }
        }
        if (isset($this->request->post['next'])) {
				$this->data['next'] = $this->request->post['next'];
			} else {
				$this->data['next'] = '';
			}
        //$this->data['coupon_status'] = $this->config->get('coupon_status');
       //Coupon Check
         $this->load->model('checkout/offers');
         if($this->model_checkout_offers->checkCuoponavailability()){
            $this->data['coupon_status'] = 1; 
         }else{
             $this->data['coupon_status'] = false; 
         }
        //Coupon Check
        if (isset($this->request->post['coupon'])) {
            $this->data['coupon'] = $this->request->post['coupon'];
        } elseif (isset($this->session->data['coupon'])) {
            $this->data['coupon'] = $this->session->data['coupon'];
        } else {
            $this->data['coupon'] = '';
        }

        $this->data['voucher_status'] = $this->config->get('voucher_status');

        if (isset($this->request->post['voucher'])) {
            $this->data['voucher'] = $this->request->post['voucher'];
        } elseif (isset($this->session->data['voucher'])) {
            $this->data['voucher'] = $this->session->data['voucher'];
        } else {
            $this->data['voucher'] = '';
        }
        
        if (isset($this->request->post['notes'])) {
            $this->session->data['comment'] = $this->request->post['notes'];
        }

        // new reward points     
        $this->data['reward_status'] = false;
        if($this->config->get('reward_points_status')) {
            $this->load->model('checkout/reward');
            $rewardjson = $this->model_checkout_reward->showredeempoints();

            if($rewardjson['reward_status']){
                $this->data['reward_status'] = true;
        $this->data['entry_reward'] = '';
        $this->data['max_point'] = 0;

                if(isset($rewardjson['reward_pointstatus'])) {
                    $this->data['reward_pointstatus'] = $rewardjson['reward_pointstatus'];
        }
            
                if(isset($rewardjson['label_reward'])) {    
                    $this->data['label_reward'] = $rewardjson['label_reward'];
            }
                if(isset($rewardjson['max_point'])) {
                    $this->data['max_point'] = $rewardjson['max_point'];
                }
                if(isset($rewardjson['entry_reward'])) {
                    $this->data['entry_reward'] = $rewardjson['entry_reward'];
            }
            
                if(isset($rewardjson['redeem_freeproducts'])) {
                    $this->data['redeem_freeproducts']=$rewardjson['redeem_freeproducts'];
                    }
            
                if(isset($rewardjson['reward_total_points'])) {
                    $this->data['reward_total_points']=$rewardjson['reward_total_points'];
                        }
//                if(isset($rewardjson['redeemproducts'])) {
//                    $this->data['redeemproducts'] = $rewardjson['redeemproducts'];
//                }
            
                //show earned reward point

                if (isset($this->request->post['reward'])) {
                    $this->data['reward'] = $this->request->post['reward'];
                } elseif (isset($this->session->data['reward'])) {
                    $this->data['reward'] = $this->session->data['reward'];
                } else {
                    $this->data['reward'] = '';
                }
            }else {
        
                unset($this->session->data['reward_point']);
                unset($this->session->data['reward']);
                unset($this->session->data['reward_free_products']);
                 
            }
            $this->showearnrewardpoint();
        }
      // new reward points  end
        
    if (isset($this->error['warning'])) {
		$this->data['error_warning'] = $this->error['warning'];
	} else {
		$this->data['error_warning'] = '';
	}
    // COMMENT
    if(isset($this->request->post['comment'])){
        $this->session->data['comment'] = strip_tags($this->request->post['comment']); 
    }
						
	if (isset($this->session->data['success'])) {
		$this->data['success'] = $this->session->data['success'];
		unset($this->session->data['success']);
	} else {
		$this->data['success'] = '';
	}
         $this->data['action'] = $this->url->link('checkout/checkoutetm');   
        //;
          $this->data['logged'] = $this->customer->isLogged();
        $this->data['shipping_required'] = $this->cart->hasShipping();
        if (!$this->data['logged']) {
            $this->data['login_content'] = $this->getChild('checkout/loginetm');
            $this->data['register_content'] = $this->getChild('checkout/registeretm');

            $this->data['guest_content'] = $this->getChild('checkout/guest');
           
        } else {
            $this->data['login_content'] = '';
            $this->data['register_content'] = $this->getChild('checkout/registeretm');
            $this->data['payment_addressetm_content'] = $this->getChild('checkout/payment_addressetm');
        }
        $this->data['shipping_method'] = $this->getChild('checkout/shipping_methodetm');
        $this->data['payment_method'] = $this->getChild('checkout/payment_methodetm');
        if(isset($this->session->data['product_no_times'])){
            $this->data['confirm_content'] = $this->getChild('checkout/shipping_methodetm/carttimedata');
        }else{
            $this->data['confirm_content'] = $this->getChild('checkout/confirmetm/finalcart');
        }
        
                   
        $this->data['confirm_buttom'] = $this->getChild('checkout/confirmetm/confirmbutton');
        //
        if ($this->config->get('config_checkout_id') && $this->config->get('config_account_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
            $information_info2 = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
            
            if ($information_info && $information_info2) {
                $this->data['link_agree'] = $this->url->link('information/information/info&information_id='. $this->config->get('config_checkout_id'));
                $this->data['link_agree_privacy'] = $this->url->link('information/information/info&information_id='. $this->config->get('config_account_id'));
                //sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
                //$this->data['text_mobile_checkout_agree'] = sprintf($this->language->get('text_mobile_checkout_agree'), $this->url->link('information/information', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
            } else {
                $this->data['link_agree'] = '';
                $this->data['link_agree_privacy'] ="";
                //$this->data['text_mobile_checkout_agree']='';
            }
        } else {
            $this->data['link_agree'] = '';
            $this->data['link_agree_privacy'] ="";            
           // $this->data['text_mobile_checkout_agree']='';
        }

        if (isset($this->session->data['delivery_method'])) {
			$delivery_method = $this->session->data['delivery_method'];
		} else {
			$delivery_method = '';
		}
		$this->data['delivery_method'] = $delivery_method;
        //
        if(isset($this->session->data['tableordering']) && isset($this->session->data['dine_table_id'])){		
            $this->data['dine_in_order']=1;
        }
        if(!isset($this->session->data['shipping_methods']) || empty($this->session->data['shipping_methods']) || !isset($this->session->data['shipping_method'])){
            $this->data['noshippingstatus']=1; 
        }
        $this->data['menushipping'] = $this->getChild('checkout/shipping_postcodes/menushipping');
        $this->data['also_bought']='';
        if(!isset($this->session->data['offerordering'])){
            $this->data['also_bought'] = $this->getChild('module/also_bought');
        }
        $this->data['back_menu'] = $this->config->get('config_url') . 'menu#';
        $this->data['order_total'] = $this->cart->getTotal();
        $this->data['cart_items']=$this->cart->getcartProducts();
        if(isset($this->session->data['upsellstatus']) && !isset($this->session->data['upsellskipped'])){
            $this->data['upsellstatus']=1;
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/checkoutetm.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/checkoutetm.tpl';
        } else {
            $this->template = 'default/template/checkout/checkoutetm.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header',
            'module/freeproducts',
        );

        $this->response->setOutput($this->render());
    }

    public function country() {
        $json = array();

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

        if ($country_info) {
            $this->load->model('localisation/zone');

            $json = array(
                'country_id' => $country_info['country_id'],
                'name' => $country_info['name'],
                'iso_code_2' => $country_info['iso_code_2'],
                'iso_code_3' => $country_info['iso_code_3'],
                'address_format' => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone' => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
                'status' => $country_info['status']
            );
        }

        $this->response->setOutput(json_encode($json));
    }

    public function validateCoupon() {
        $json = array();
        if (isset($this->request->post['coupon'])) {
             $this->language->load('checkout/checkout');
             $this->load->model('checkout/coupon');
             $this->load->model('checkout/offers');

            $json['c'] = '';
            if($this->request->post['coupon']=='') {
                if(isset($this->session->data['coupon'])) {
                    unset($this->session->data['coupon']);
                    $json['success'] = 'Coupon code removed successfully.';
                } else {
                    $json['error']['warning'] = 'No coupon code to remove.';
            }
            } else {
                $offer_couponinfo= $this->model_checkout_offers->validateCoupon($this->request->post['coupon']);
                if ($offer_couponinfo['msg']) {
                    $json['error']['warning'] = $offer_couponinfo['msg'];
                    if(isset($this->session->data['coupon'])) {
                        unset($this->session->data['coupon']);
                    }
                } else {
                if($offer_couponinfo['case_sensitive']==1){
                    $this->session->data['coupon'] = $this->request->post['coupon'];
                }else{
                    $this->session->data['coupon'] = trim(strtoupper($this->request->post['coupon']));
                }
                $json['c'] = 1;
                    
                 $this->language->load('checkout/cart');
                $json['success'] = $this->language->get('text_coupon');
            }
        }
        }
        $this->response->setOutput(json_encode($json));
    }

    public function validateVoucher() {
         $json = array();
        // Voucher
        if (isset($this->request->post['voucher'])) {
            
             $this->language->load('checkout/checkout');
             $this->load->model('checkout/voucher');

            $voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);

            if (!$voucher_info) {
                $json['error']['warning'] = $this->language->get('error_voucher');
            }
            else {
             
                $this->session->data['voucher'] = $this->request->post['voucher'];
                $this->language->load('checkout/cart');
                $json['success'] = $this->language->get('text_voucher');

            
            }
        }
        $this->response->setOutput(json_encode($json));
    }

    public function validateReward() {
        $this->load->model('checkout/reward');
        $json = $this->model_checkout_reward->validateReward();
        $this->response->setOutput(json_encode($json));
    }
    
    public function removefreeproduct() {    
  
        $this->load->model('checkout/reward');
        $json['success'] = $this->model_checkout_reward->removefreeproduct();
        $this->response->setOutput(json_encode($json));
    }

    public function showearnrewardpoint() {
        $this->load->model('checkout/reward');
        $this->data['earn_reward'] = $this->model_checkout_reward->earnrewardpointcart(); 
        $this->response->setOutput($this->data['earn_reward']);
    }
    public function skipupsell() {
        $this->session->data['upsellskipped']=true;
    }
            
            
}            
            