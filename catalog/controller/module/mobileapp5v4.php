<?php
/* VERSION 5 ionic 6 */
class ControllerModuleMobileapp5v4 extends Controller {
    private $error = array();
    private function jsonp($json, $encode = false) {
        $this->securitycheck();
        $this->response->addHeader("Content-Type: application/x-www-form-urlencoded; charset=utf-8"); 
        $this->response->addHeader("Accept: application/json, text/plain");
        $this->response->addHeader("cache-control: no-cache"); 
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            $this->response->addHeader("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        }else {
            $this->response->addHeader('Access-Control-Allow-Origin: *');
        }
        $this->response->addHeader('Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS, TRACE, PATCH, CONNECT');
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
            $this->response->addHeader("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }else {
            $this->response->addHeader('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Accept, Authorization, X-Request-With, Access-Control-Request-Method, Access-Control-Request-Headers');
        }
        $this->response->addHeader('Access-Control-Allow-Credentials: true');
        $this->response->setCompression(9);
        if (isset($this->request->get['callback'])) {
            return $this->request->get['callback'] . '(' . ($encode ? json_encode($json) : $json ) . ')';
        } else {
            return ($encode ? json_encode($json) : $json);
        }
    }

    public function index() {
        echo "mobileapp 5";
    }
    
    private function securitycheck(){
        if ($this->config->get('config_mobileapi_security_key')) {
            $security_key = $this->config->get('config_mobileapi_security_key');
            $pos = strpos($this->request->server['HTTP_USER_AGENT'],$security_key );
            if ($pos === false) {
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
            $this->request->server['HTTP_USER_AGENT'] = str_replace($security_key, '', $this->request->server['HTTP_USER_AGENT']);
        }
        
    }   

    public function getProductsIDs() {
        $this->load->model('catalog/product');
        $json['proIDS'] = $this->model_catalog_product->getAllProductIDs();
        $pro_last_modified = $this->model_catalog_product->getLastProductyModified();
        $json['bundled_combined_ids'] = $this->model_catalog_product->getBundledCombinedIDs();

        $this->load->model('catalog/category');
        $cat_last_modified = $this->model_catalog_category->getLastCategoryModified();

        $json['last_modified'] = (($cat_last_modified < $pro_last_modified ) ? $pro_last_modified: $cat_last_modified);

        $catg_query = $this->db->query("SELECT count(*) catg_cnt FROM " . DB_PREFIX . "category WHERE status = '1'");
        $json['totalCategories'] = $catg_query->row['catg_cnt'];
        $this->response->setOutput($this->jsonp($json, true));
    }

    public function getLastModifiedTime(){
        $this->load->model('catalog/product');
        $pro_last_modified = $this->model_catalog_product->getLastProductyModified();
        
        $this->load->model('catalog/category');
        $cat_last_modified = $this->model_catalog_category->getLastCategoryModified();
        $prod_cnt = count($this->model_catalog_product->getAllProductIDs());

        $json['last_modified'] = (($cat_last_modified < $pro_last_modified ) ? $pro_last_modified: $cat_last_modified);
        $json['totalProducts'] = $prod_cnt;

        $catg_query = $this->db->query("SELECT count(*) catg_cnt FROM " . DB_PREFIX . "category WHERE status = '1'");
        $json['totalCategories'] = $catg_query->row['catg_cnt'];

        $this->response->setOutput($this->jsonp($json, true));
    }

    public function getLastModified() {
        $json = array();
        $this->load->model('catalog/category'); 
        $cat_last_modified = $this->model_catalog_category->getLastCategoryModified();
        $this->load->model('catalog/product');
        $pro_last_modified = $this->model_catalog_product->getLastProductyModified();
        $json['last_modified'] = (($cat_last_modified < $pro_last_modified ) ? $pro_last_modified: $cat_last_modified);
        $this->response->setOutput($this->jsonp($json, true));
    }

    public function login() {
        $json = array();
        $json['request'] = $_REQUEST;
        if (isset($this->request->post['email']) and isset($this->request->post['password'])) {
            $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($this->request->post['email'])) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($this->request->post['password']) . "'))))) OR password = '" . $this->db->escape(md5($this->request->post['password'])) . "') AND status = '1' AND approved = '1' AND is_blacklisted = '0'");
            if ($customer_query->num_rows) {
                if(isset($this->request->post['fcm_token']) && $this->request->post['fcm_token']) {
                    $this->db->query("UPDATE " . DB_PREFIX . "customer SET fcm_token = '" . $this->db->escape($this->request->post['fcm_token']) . "', user_agent ='" . $this->db->escape('APP:'.$this->request->server['HTTP_USER_AGENT']) . "' WHERE customer_id = '" . (int) $customer_query->row['customer_id'] . "'");
                    $customer_query->row['fcm_token'] = $this->request->post['fcm_token'];
                }
                $json['success'] = array('customer_id' => $this->_encodeCustmerID($customer_query->row['customer_id']), 'address_id' => $customer_query->row['address_id'],
                    'fcm_token'=>$customer_query->row['fcm_token']);
            } else {
                $json['error'] = 'No match for E-Mail Address and/or Password';
            }
        }
        else {
            $json['error'] = 'Wrong Parameter';
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    public function register() {
        $json = array();
        //$this->log->write(print_r($this->request->post,true));
        $this->load->model('account/customer');
        $postcode = $this->postcode->postcodeformat($this->request->post['postcode']);
        $postcode_details = $this->postcode->getPostcodeDetails($postcode);
        if (!isset($json['error']) and empty($postcode_details)) {
            $this->error['postcode'] = 'Invalid Postcode';
        }
        $this->request->post['country_id']=$this->config->get('config_country_id');
        $this->request->post['zone_id'] = 0;
        $this->request->post['company']='';
        if(isset($this->request->post['house_number'])) {
            $this->request->post['address_1']= $this->request->post['house_number']. ' ' . $this->request->post['street'];
        } else {
            $this->request->post['address_1']= $this->request->post['street'];
        }
        $this->request->post['user_agent'] =  'APP:'.$this->request->server['HTTP_USER_AGENT'];
        if($this->validateRegister()) {
            /* if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
                $json['error'] = 'Email Already Registered';
            }*/ 

            if (!isset($json['error'])) {
    
                $this->request->post['postcode']=$postcode;
                
                $this->model_account_customer->addCustomer($this->request->post);
                $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($this->request->post['email'])) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($this->request->post['password']) . "'))))) OR password = '" . $this->db->escape(md5($this->request->post['password'])) . "') AND status = '1' AND approved = '1'");
                if ($customer_query->num_rows) {
                    $json['success'] = array('customer_id' => $this->_encodeCustmerID($customer_query->row['customer_id']), 'address_id' => $customer_query->row['address_id']);
                } else {
                    $json['error'] = 'Tech ERROR';
                }
    
                if($this->config->get('wocmailchimp_new_customer') == 1 ){
			$mcData = array();
			$mcData['email'] = $this->request->post['email'];
			$mcData['firstname'] =  html_entity_decode($this->request->post['firstname'], ENT_QUOTES, 'UTF-8');
			$mcData['lastname'] = html_entity_decode($this->request->post['lastname'], ENT_QUOTES, 'UTF-8');
			
			$this->getChild('module/wocmailchimp/subscribe', $mcData);
            }
    
        }
        }
        else {
            $json['error']  = $this->error;
        }
        
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    private function validateRegister() {
        $this->language->load('account/register');
    	if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
      		$this->error['firstname'] = $this->language->get('error_firstname');
    	}

    	if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
      		$this->error['lastname'] = $this->language->get('error_lastname');
    	}

    	if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
      		$this->error['email'] = $this->language->get('error_email');
    	}

    	if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
      		$this->error['warning'] = $this->language->get('error_exists');
    	}
		
    	if ((utf8_strlen($this->request->post['telephone']) < 10) || (utf8_strlen($this->request->post['telephone']) > 11)) {
      		$this->error['telephone'] = $this->language->get('error_telephone');
    	}
		
		// Customer Group
		$this->load->model('account/customer_group');
		
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
			
		
		
    	if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
      		$this->error['address_1'] = $this->language->get('error_address_1');
    	}

//    	if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
//      		$this->error['city'] = $this->language->get('error_city');
//    	}

		$this->load->model('localisation/country');
		
		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
		
		if ($country_info) {
			if ($country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
				$this->error['postcode'] = $this->language->get('error_postcode');
			}
			
			// VAT Validation
			$this->load->helper('vat');
			
			if ($this->config->get('config_vat') && $this->request->post['tax_id'] && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
				$this->error['tax_id'] = $this->language->get('error_vat');
			}
		}


    	if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
      		$this->error['password'] = $this->language->get('error_password');
    	}

    	if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
  	}

    /*
     * get Cart Details
     *
     * param @  customer_id,address_id ,products = array(),coupon ,suggested_dealid,remove
     * return @ json = array(products,totals,shipping_methodes,payment_methodes,requested_times,error_coupon,deals)
     */

    public function cartdetails($from=false) {
       //$this->log->write('CARTDETAILS ');
        //$this->log->write(print_r($this->request->post,true));
       //$this->log->write(print_r($this->session->data,true));
       $json = array();
        $this->request->post['customer_id']  =  $this->_decodeCustmerID($this->request->post['customer_id'] );
        if (!isset($this->request->post['customer_id']) || $this->request->post['customer_id']=='') {
            // $json = array('error'=>'No customer_id');
             $json = ["coupon"=>"yes","voucher"=>"yes",
                 "reward" =>"no","smart_deals" =>[],"also_bought"=>[],
                 "shipping_warnings"=>[],"shipping_methodes_available"=>[],
                 "freeproducts"=>["offer_products"=>[]],"requested_times"=>[],
                 "closed_message_head"=>"Login Error",
                 "closed_message_content" =>"Logout and try to login again",
                 "products"=>[],
                 "totals" =>[["code"=>"total","title"=>"Total","text"=>"","value"=>0.00,"sort_order"=>"20"]],
                 "storeopened"=>0];
           //  $this->log->write(print_r($this->request->post, true));
             $this->response->setOutput($this->jsonp($json, true));
             return;
        }

        $login_status = $this->customer->loginID($this->request->post['customer_id']);
        if(!$login_status){
            $json['closed_message_head']= 'Your account requires approval before you can login';
            $json['closed_message_content']='';
            $this->response->setOutput($this->jsonp($json, true));
        }

        $POST = $this->request->post;
         if(!$from){
            $this->session->data['cart'] = array();
        }
        $customer_qry = $this->db->query("SELECT *  FROM " . DB_PREFIX . "customer  WHERE customer_id='" . $this->request->post['customer_id'] . "'");
            if ($customer_qry->num_rows) {
                if ($customer_qry->row['cart'] && is_string($customer_qry->row['cart'])) {
                    $cart = unserialize($customer_qry->row['cart']);
                    foreach ($cart as $item_key => $qty) {
                        $this->session->data['cart'][$item_key] = (int) $qty;
                    }
                }
        }
        //
        if (isset($this->request->post['remove'])) {
            if(is_array($this->request->post['remove'])) {
                foreach ($this->request->post['remove'] as $remove_key ) {
                    $this->cart->remove($remove_key);
                }
            }
            else {
                $this->cart->remove($this->request->post['remove']);
            }
            #TODO Offer & Coupon calculations
        }
      
        if (isset($this->request->post['products'])) {
            
            $products = json_decode(htmlspecialchars_decode($this->request->post['products']),true);
             // $this->log->write(print_r($products, true));
           $ki=1;
            foreach ($products as $product) {
                //  parse_str(htmlspecialchars_decode($product), $output);
                if(is_array($product)) {
                    $output = $product;
                }
                else {
                    parse_str($product, $output);
                }
                if(isset($output['main_product_id'])){
                    $output['bundleidval']=$ki.''.time(); 
                    $output['mobileapp']=1;
                }
                $this->request->post = $POST + $output; // + $this->request->get;
                $this->getChild('checkout/cart/add');
                $ki++;
            }
        }
        $this->request->post = $POST;


        //Coupon / Voucher /Reward Status
        if(!isset($this->session->data['mobileapp'])){
            $this->session->data['mobileapp']=1;
        }
        $this->load->model('checkout/offers');
        if (!isset($this->session->data['coupon']) && $this->model_checkout_offers->checkCuoponavailability()) {
            $json['coupon'] = 'yes';
        } else {
            $json['coupon'] = 'no';
        }

        if (!isset($this->session->data['voucher']) && $this->config->get('voucher_status')) {
            $json['voucher'] = 'yes';
        } else {
            $json['voucher'] = 'no';
        }

        $json['tips_percentages'] = array();
        if($this->config->get('tips_status')) {
            $json['tips'] = 'yes';
            if(isset($this->request->post['tips']) && $this->request->post['tips']) {
                $this->session->data['tips'] = $this->request->post['tips'];
            } else {
                unset($this->session->data['tips']);
            }
            if($this->config->get('tips_percentage_online')) {
                $tips_percentages = explode(',', $this->config->get('tips_percentage_online'));
                foreach($tips_percentages as $tips_percentage) {
                    $tips_value = round ($this->cart->getSubTotal() * floatval($tips_percentage)/100,2);
                    $json['tips_percentages'][] = array('percentage'=>$tips_percentage,'value'=>$tips_value,'text'=>$this->currency->format($tips_value));
                }
            }
        } else {
            $json['tips'] = 'no';
        }


        if (isset($this->request->post['update'])) {
            $this->cart->update($this->request->post['update'],$this->request->post['quantity']);

        }
        //$smart_deals = $this->getChild('module/smartoffers/getsmartoffers');
        ///$this->log->write(print_r('nnnn',true));
        //$this->log->write(print_r($this->session->data['cart'],true));
        if (isset($this->request->post['converted_deal'])) {
            //$this->log->write(print_r('DEALLL',true));
            //$this->log->write(print_r(json_decode(html_entity_decode($this->request->post['converted_deal'])),true));
            $this->session->data['converted_deal'] = json_decode(html_entity_decode($this->request->post['converted_deal']),true);
            //$this->log->write(print_r($this->session->data['converted_deal'],true));
            $this->getChild('module/smartoffers/convertitemstodeal');
            //$smart_deals = '';
        }
        //$this->log->write(print_r('Deal check aaaa',true));
        $smart_deals=[];
        /*
        $this->getChild('module/smartoffers/getsmartoffers');
        if(isset($this->session->data['available_deal_suggestions']) && !empty($this->session->data['available_deal_suggestions'])){
           // $this->log->write(print_r('Deal check 22222',true));
            //$this->log->write(print_r($this->session->data['available_deal_suggestions'],true));
            $smart_deals = $this->session->data['available_deal_suggestions'];
        }        
        */
        if (isset($this->session->data['cart'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart='" . serialize($this->session->data['cart']) . "'  WHERE customer_id='" . $this->request->post['customer_id'] . "'");
        }
        //Coupon Code
        $this->language->load('checkout/checkout');
        if (isset($this->request->post['coupon'])) {
            $this->load->model('checkout/offers');

            $this->request->post['coupon'] = trim($this->request->post['coupon']);
            $offer_couponinfo= $this->model_checkout_offers->validateCoupon($this->request->post['coupon']);
            if ($offer_couponinfo['msg']) {
                $json['error_coupon'] = $offer_couponinfo['msg'];
            } else {
                if ($offer_couponinfo['case_sensitive'] == 1) {
                    $this->session->data['coupon'] = $this->request->post['coupon'];
                } else {
                    $this->session->data['coupon'] = trim(strtoupper($this->request->post['coupon']));
                }
                $this->getChild('module/freeproducts');
            }
        }

        if (isset($this->request->post['voucher'])) {
            $this->language->load('checkout/checkout');
            $this->load->model('checkout/voucher');

            $voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);

            if (!$voucher_info) {
                $json['error_voucher'] = $this->language->get('error_voucher');
            } else {
                $this->session->data['voucher'] = $this->request->post['voucher'];
            }
        }
        /*
        //new reward point calculation
        $points_total = 0;
        $json['reward'] = 'no';
        if($this->config->get('reward_points_status')) {
            $points = $this->customer->getRewardPoints();
            $min_point = (int)$this->config->get('config_redeem_points_min_points');

            $json['reward_points'] = $points;
            if($points && $min_point >= $points && $points >= (int)$this->config->get('config_redeem_points_min_activate')) {
                $json['reward'] = 'yes';
            }

            if (isset($this->request->post['reward']) || !isset($this->session->data['reward'])) {

                $max_point = ($this->config->get('config_redeem_points_max_points')) ? $this->config->get('config_redeem_points_max_points') : $points;

                $this->load->model('account/order');      
                $ordercount = $this->model_account_order->getOrderCount($this->request->post['customer_id']);


                if ((int)$this->config->get('config_min_reward_order') > $ordercount) {
                    $json['error_reward'] = 'Minimum no.of order is '.$ordercount;
                } else if(isset($this->request->post['reward'])) {
                    if (empty($this->request->post['reward'])) {
                        $json['error_reward'] = $this->language->get('error_reward');
                    } else if ($this->request->post['reward'] > $points) {
                        $json['error_reward'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
                    } else if ($this->request->post['reward'] > $max_point) {
                        $json['error_reward'] = sprintf($this->language->get('error_maximum'), $max_point);
                    } else if ($min_point!=0 && $this->request->post['reward'] < $min_point) {
                        $json['error_reward'] = "The Minimum number of points that can be applied is " .$min_point;
                    }
                    if(isset($this->request->post['redeem_products']) && $this->request->post['redeem_products']) { 
                        unset($json['error_reward']); // USING Reward products
                    }
                }
                // $this->log->write(print_r("kkkk 5".$json['error_reward'], true));
                if(!isset($json['error_reward'])) {

                    $total_data = array();
                    $total = 0 ;
                    $taxes = $this->cart->getTaxes();


                    $this->load->model('setting/extension');
                    $results = $this->model_setting_extension->getExtensions('total');

                    foreach ($results as $key => $value) {
                        $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                    }

                    array_multisort($sort_order, SORT_ASC, $results);

                    foreach ($results as $result) {
                        if ($this->config->get($result['code'] . '_status')) {
                            $this->load->model('total/' . $result['code']);

                            $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                        }
                    }
                
                    $no_offers = true;
                    if(!$this->config->get('config_redeem_points_work_offers')){
                        foreach($total_data as $total_datacode){
                            if($total_datacode['code'] == 'offers'){
                                $no_offers = false;
                            }
                        }
                    }

                    if($no_offers){
                        $totals = $total;
                        if($this->config->get('redeem_exclude_product')){                    
                            foreach ($products as $cart_key => $product) {
                                if(in_array($product['product_id'],$this->config->get('redeem_exclude_product'))){                            
                                    $totals -= (float)$product['price'];
                                }
                            }
                        }
                        if($this->config->get('config_redeem_points_exclude_total')){                
                            foreach ($this->config->get('config_redeem_points_exclude_total') as $exclude_code){
                                foreach($total_data as $total_datacode){
                                    if($total_datacode['code'] == 'reward') continue;    //skip reward total already applied
                                    if($total_datacode['code'] == $exclude_code && $total_datacode['value'] >0 ){
                                            $totals -= (float)$total_datacode['value'];
                                    }
                                }
                            }                    
                        } 

                        $points_total = floor( $totals / ($this->config->get('config_redeem_points_points')/100));

                        if($totals < $this->config->get('config_redeem_points_min_order_amount')) {
                            $json['error_reward'] = 'The Total amount needed to spend to get reward is '.$this->config->get('config_redeem_points_min_order_amount');
                        } else {
                            $max_point = ($this->config->get('config_redeem_points_max_points')) ? $this->config->get('config_redeem_points_max_points') : $points;
                            if($points_total < $max_point){
                                $max_point =  $points_total;
                            }
                            if($points < $max_point){
                                $max_point =  $points;
                            }
                            $json['reward'] = ($points && $points_total) ? 'yes' : 'no';
                            $json['reward_points'] = ($points_total > $points ? $points : $points_total);
                        }

                        $min_total = $totals - $this->config->get('config_redeem_points_min_order_amount');
                        if(isset($this->session->data['reward'])) {
                            if($this->config->get('config_redeem_points_points')) {
                                $min_total+= $this->session->data['reward_point']/ $this->config->get('config_redeem_points_points');
                            }
                            if(isset($this->session->data['free_products'])) {
                                foreach ($this->cart->getProducts() as $product) {
                                    if(isset($this->session->data['free_products'][$product['product_id']])) {
                                        $min_total+= $product['price'] * $this->session->data['free_products'][$product['product_id']]['qty'];
                                    }                                
                                }
                            }
                        }

                        if (!isset($json['error_reward']) && isset($this->request->post['reward'])) {
                            $this->session->data['reward'] = $this->request->post['reward'];
                        }
                        $json['redeem_freeproducts'] =[];
                        $redeem_freeproducts = $this->redeemfreeproducts($points);
                        $json['redeemproducts'] = isset($this->session->data['free_products'])?$this->session->data['free_products']: array();
                        
                        foreach($redeem_freeproducts as $redeem_freeproduct) {
                            if(isset($json['redeemproducts'][$redeem_freeproduct['product_id']])){
                                $rwselectedqty = $json['redeemproducts'][$redeem_freeproduct['product_id']]['qty'];
                            }else {
                                $rwselectedqty = 0;
                            }
                            for($i=1; $i<=((int)$redeem_freeproduct['cart_qty']);$i++){
                                if($i <= $rwselectedqty) {
                                    $redeem_freeproduct['selected'] = 1;
                                }else {
                                    $redeem_freeproduct['selected'] = 0;
                                }
                                $json['redeem_freeproducts'][] = $redeem_freeproduct;
                            }
                        }             
                        
                    
                    } else {
                        $json['reward'] = 'no';
                    }
                } 
                // #TODO unset($this->session->data['free_products']);
                // #TODO unset($this->session->data['reward_point']);
                if(!isset($json['reward']) || $json['reward']=='no') {
                    unset($this->session->data['reward']);
                } else if($this->config->get('config_redeem_free_product')) {

                    $reward = $this->session->data['reward'];   
                    $max_free = $this->config->get('config_free_product_multiple'); 
                    if(!$max_free) {
                        $max_free = $reward;
                    }            
                    $cart_product = array();
                    foreach($this->cart->getProducts() as $cartproduct) {
                        $cart_product[$cartproduct['product_id']] = array('name' => $cartproduct['name'], 'price' => $cartproduct['price'], 'quantity' => $cartproduct['quantity']);
                    }

                    foreach($this->config->get('config_free_products') as $freegroup) {
                        if($freegroup['amount'] <= $reward) {
                            foreach($freegroup['free_products'] as $product) {
                                if(!$max_free || $reward <=0 || $min_total < 0) {
                                    break;
                                } else if(isset($cart_product[$product]) && ($cart_product[$product]['price'] < $min_total) && $reward >= $freegroup['amount']) {  
                                    $qty = floor($min_total/$cart_product[$product]['price']);
                                    if($qty > $cart_product[$product]['quantity']) {
                                        $qty = $cart_product[$product]['quantity'];
                                    } else if ($qty > $max_free) {
                                        $qty = $max_free;
                                    }
                                    $min_total-=$qty * $cart_product[$product]['price'];
                                    $reward-=$qty * $freegroup['amount'];
                                    $max_free-=$qty;
                                    //$this->log->write($qty.' - '.$cart_product[$product]['name'].' reward : '.$reward);
                                    $this->session->data['free_products'][$product] =  array('qty'=>$qty,'amount'=>$freegroup['amount']);        
                                }
                            }
                        }
                    }
                    unset($cart_product);
                    $this->session->data['reward_point'] = $reward;
                } else if(isset($this->session->data['reward'])) {
                    $this->session->data['reward_point'] = $this->session->data['reward']; 
                }

                if(!$this->config->get('config_redeem_amount')) {
                    $this->session->data['reward_point'] = 0;
                }
            }
            if($this->config->get('config_redeem_amount') && $json['reward'] == 'yes') {
                $json['reward_redeem_amount'] = 'yes'; 
            }else {
                 $json['reward_redeem_amount'] = 'no'; 
            }
        
        }
        */
        $json['reward_status'] = 'no';
        if($this->config->get('reward_points_status')) {
            $this->load->model('checkout/reward');
            if(isset($this->request->post['redeem_products']) || isset($this->request->post['reward'])) {
                if(isset($this->request->post['redeem_products']) && $this->request->post['redeem_products']) { 
                    $this->request->post['redeem_products'] = json_decode(html_entity_decode($this->request->post['redeem_products']),true);
                }
                $validateRwjson = $this->model_checkout_reward->validateReward();
                if(isset($validateRwjson['points'])) {
                    $json['reward'] = $validateRwjson['points'];
                }else {
                    $json['reward'] = '';
                }
                if(isset($validateRwjson['error']['warning'])) {
                    $json['error_reward'] = $validateRwjson['error']['warning'];
                }
            
            }
            $rewardjson = $this->model_checkout_reward->showredeempoints();

            if($rewardjson['reward_status']){
                $json['reward_status'] = 'yes';
                $json['entry_reward'] = '';
                $json['max_point'] = 0;
                $json['reward_pointstatus'] ='no';

                if(isset($rewardjson['reward_pointstatus'])) {
                    $json['reward_pointstatus'] = ($rewardjson['reward_pointstatus']?'yes':'no');
                }
                
                if(isset($rewardjson['label_reward'])) {    
                    $json['label_reward'] = $rewardjson['label_reward'];
                }
                if(isset($rewardjson['max_point'])) {
                    $json['max_point'] = $rewardjson['max_point'];
                }
                if(isset($rewardjson['entry_reward'])) {
                    $json['entry_reward'] = $rewardjson['entry_reward'];
                }
               
                if(isset($rewardjson['redeem_freeproducts'])) {
                    $json['redeem_freeproducts']=$rewardjson['redeem_freeproducts'];
                }else {
                    $json['redeem_freeproducts']=[];
                }
                
                if(isset($rewardjson['reward_total_points'])) {
                    $json['reward_total_points']=$rewardjson['reward_total_points'];
                }


                //show earned reward point
                /*
                if (isset($this->request->post['reward'])) {
                    $json['reward'] = $this->request->post['reward'];
                } else
                 
                if (isset($this->session->data['reward'])) {
                    $json['reward'] = $this->session->data['reward'];
                } else {
                    $json['reward'] = '';
                }
                 * 
                 */
            }else {
               
                unset($this->session->data['reward_point']);
                unset($this->session->data['reward']);
                unset($this->session->data['reward_free_products']);
                 
            }
            // $this->showearnrewardpoint();
        }

        $json['smart_deals'] =$smart_deals;// json_decode($smart_deals);
        $json['also_bought'] = json_decode($this->getChild('module/also_bought/getmobilerelatedproduct'));
        unset($this->session->data['payment_method']);
        //Totals -- Return
        $this->load->model('account/address');
        $this->load->model('setting/extension');
        $this->load->model('tool/image');
        $total_data = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();
        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
            $sort_order = array();
            $results = $this->model_setting_extension->getExtensions('total');
            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }
            array_multisort($sort_order, SORT_ASC, $results);
            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);
                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                }
                $sort_order = array();

                foreach ($total_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }
                array_multisort($sort_order, SORT_ASC, $total_data);
            }
        }

       // unset($this->session->data['free_products']);
       // unset($this->session->data['reward_point']);
        //cart products
        $json['products'] = array();
        //$this->log->write(print_r(array('pollll'),true));
        //$this->log->write(print_r($this->cart->getProducts(),true));
        $not_available_items = array();
        foreach ($this->cart->getProducts() as $key => $product) {
           if($product['subtract']=='1'){
                if(!isset($not_available_items[$product['product_id']])){
                    $not_available_items[$product['product_id']]=array('name'=>$product['name'],'tot_qty'=>$product['available_qty'],'in_qty'=>$product['quantity']);
                }else{
                    $not_available_items[$product['product_id']]['in_qty'] +=$product['quantity'];
                }
            }
            if ($product['image']) {
                $image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $image = '';
            }

            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['option_value'];
                } else {
                    $filename = $this->encryption->decrypt($option['option_value']);

                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                }

                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                    'type' => $option['type'],
                    'quantity'=>$option['quantity'],
                    'price'=>$option['price'],
                    'price_prefix'=>$option['price_prefix'],
                );
            }

            //Bundled_dispaly
            $productbundle = array();
            foreach ($product['bundled_data'] as $bundled_product) {
                $bundle_options = array();
                foreach ($bundled_product['option'] as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['option_value'];
                    } else {
                        $filename = $this->encryption->decrypt($option['option_value']);

                        $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                    }

                    $bundle_options[] = array(
                        'name' => $option['name'],
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                        'type' => $option['type'],
                        'quantity'=>$option['quantity'],
                        'price'=>$option['price'],
                        'price_prefix'=>$option['price_prefix'],
                    );
                }
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($bundled_product['price'], $bundled_product['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = false;
                }

                // Display prices
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $total_price = $this->currency->format($this->tax->calculate($bundled_product['price'], $bundled_product['tax_class_id'], $this->config->get('config_tax')) * $bundled_product['quantity']);
                } else {
                    $total_price = false;
                }
                //$chk_default_options = $this->model_catalog_product->getDefaultOptions($bundled_product['product_id']);
                $productbundle[] = array(
                    'product_id' => $bundled_product['product_id'],
                    //'thumb' => $image,
                    'name' => $bundled_product['name'],
                    'model' => $bundled_product['model'],
                    'option' => $bundle_options,
                    'quantity' => $bundled_product['quantity'],
                    'toppings'=>$bundled_product['toppings'],
                    'dressings'=>$bundled_product['dressings'],
                    'price' => $price,
                    'total' => $total_price,
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    'default' => (!empty($chk_default_options) ? 1 : 0)
                );
            }
            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
            } else {
                $price = false;
            }
            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $total_price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
            } else {
                $total_price = false;
            }

            // $chk_default_options = $this->model_catalog_product->getDefaultOptions($product['product_id']);
            $toppings = (isset($product['toppings']))?$product['toppings']:array();
            
            $json['products'][$key] = array(
                //'key' => $product['key'],
                'product_id' => $product['product_id'],
                //'thumb' => $image,
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'quantity' => $product['quantity'],
                'price' => $price,
                'total' => $total_price,
                //'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                //'default' => (!empty($chk_default_options) ? 1 : 0),
                'bundle' => $productbundle,
                'toppings'=>$toppings,   
                'dressings'=>$product['dressings'],
            );
        }
        //Customer Details -- Return
        //Shipping && Payment Return
        $shipping_methodes = array();
        $shipping_methodes_warnings = array();
        $payment_methodes = array();
        $shipping_methodes_extra = array();
        if(!isset($this->request->post['address_id']) or empty($this->request->post['address_id'])) {
            $this->request->post['address_id'] = $this->customer->getAddressId();
        }
        $address = $this->model_account_address->getAddress($this->request->post['address_id']);

        if (!empty($address)) {

            $quote_data = array();
            $results = (isset($this->request->post['dinein_table_id']))?array():$this->model_setting_extension->getExtensions('shipping');

            $totals = isset($totals)? $totals : $total;

            $this->load->model('catalog/category');
            $this->load->model('catalog/product');

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('shipping/' . $result['code']);
                    if (method_exists($this->{'model_shipping_' . $result['code']}, 'isposonly'))
                        continue;
                    $status_chk = FALSE;
                    if ($this->cart->hasProducts()) {
                        $message = '';
                        $checked_min_category = array();
                        $checked_delivery_category = array();
                        foreach ($this->cart->getProducts() as $cart_product) {
                            if (!empty($cart_product['shipping_methodes']) and ! in_array($result['code'], $cart_product['shipping_methodes'])) {//
                                $status_chk = true;
                                // $shipping_methodes_warnings[$result['code']] = 'Product' . $quote['delivery_msg_status'];
                                $message.=$cart_product['name'] . ', ';
                            } else {
                                // check minimum cart for product delivery
                                if(isset($cart_product['minimum_amount'][$result['code']])) {
                                    if($totals < $cart_product['minimum_amount'][$result['code']]) {
                                        $status_chk = true;
                                        $no_delivery[$cart_product['name']]['minimum']  = $cart_product['minimum_amount'][$result['code']];
                                    }
                                } else {
                                    // check minimum cart for category delivery
                                    $product_categories = $this->model_catalog_product->getProductCategories($cart_product['product_id']);
                                    foreach ($product_categories as $prd_category_id) {
                                        if(!in_array($prd_category_id, $checked_min_category)) {
                                            $min_cart = $this->model_catalog_category->getCategoryMinimumCart($prd_category_id);
                                            if( isset($min_cart[$result['code']]) && $totals < $min_cart[$result['code']]) {
                                                $status_chk = true;
                                                $cat_data = $this->model_catalog_category->getCategory($prd_category_id); 
                                                $no_delivery[$cat_data['name']]['minimum']  = $min_cart[$result['code']];
                                                //break;                                               
                                            }

                                            $checked_min_category[] = $prd_category_id;
                                        }
                                    }
                                }

                                $current_time = strtotime(date('H:i'));
                                if(!empty($cart_product['shipping_time']) && isset($cart_product['shipping_time'][$result['code']][strtolower(date('l'))])) {
                                    foreach($cart_product['shipping_time'][$result['code']][strtolower(date('l'))] as $delivery_time) {

                                        $end_time = strtotime($delivery_time['end']);

                                        if($delivery_time['start']) {
                                            $start_time = strtotime($delivery_time['start']);
                                            if($current_time >= $start_time) {
                                                if($delivery_time['end'] &&  $current_time >= $end_time) {
                                                    $del_btwn = sprintf($this->language->get('error_delivery_between_time'),date('h:i a',$start_time),date('h:i a', $end_time));
                                                } else {
                                                    $del_btwn = '';
                                                    break 2;
                                                }
                                            } else {
                                                //$del_btwn = sprintf($this->language->get('error_delivery_between_time'),$delivery_time['start'],$delivery_time['end']);
                                                $del_btwn = sprintf($this->language->get('error_delivery_after_time'),date('h:i a', $start_time));
                                            }

                                        } else if($delivery_time['end'] &&  $current_time >= $end_time) {
                                            $del_btwn = sprintf($this->language->get('error_delivery_till_time'),date('h:i a', $end_time));
                                        }
                                    }
                                    if($del_btwn) {
                                        $status_chk = true;
                                        $cat_data = $this->model_catalog_category->getCategory($prd_category_id); 
                                        $no_delivery[$cart_product['name']]['time']  = $del_btwn;
                                    }
                                } else {
                                    
                                    $del_btwn = '';
                                    $product_categories = $this->model_catalog_product->getProductCategories($cart_product['product_id']);
                                    foreach ($product_categories as $prd_category_id) {
                                        if(!in_array($prd_category_id, $checked_delivery_category)) {
                                            $del_time = $this->model_catalog_category->getCategoryShippingTime($prd_category_id);
                                            if(!empty($del_time[$result['code']]) && isset($del_time[$result['code']][strtolower(date('l'))])) {
                                                foreach($del_time[$result['code']][strtolower(date('l'))] as $delivery_time) {

                                                    $end_time = strtotime($delivery_time['end']);
    
                                                    if($delivery_time['start']) {
                                                        $start_time = strtotime($delivery_time['start']);
                                                        if($current_time >= $start_time) {
                                                            if($delivery_time['end'] &&  $current_time >= $end_time) {
                                                                //$del_btwn = sprintf($this->language->get('error_delivery_between_time'),$delivery_time['start'],$delivery_time['end']);
                                                                $del_btwn = sprintf($this->language->get('error_delivery_between_time'),date('h:i a',$start_time),date('h:i a', $end_time));
                                                            } else {
                                                                $del_btwn = '';
                                                                break 2;
                                                            }
                                                        } else {
                                                            //$del_btwn = sprintf($this->language->get('error_delivery_between_time'),$delivery_time['start'],$delivery_time['end']);
                                                            //$del_btwn = sprintf($this->language->get('error_delivery_after_time'),$delivery_time['start']);
                                                            $del_btwn = sprintf($this->language->get('error_delivery_after_time'),date('h:i a', $start_time));
                                                        }
    
                                                    } else if($delivery_time['end'] &&  $current_time >= $end_time) {
                                                        //$del_btwn = sprintf($this->language->get('error_delivery_till_time'),$delivery_time['end']);
                                                        $del_btwn = sprintf($this->language->get('error_delivery_till_time'),date('h:i a', $end_time));
                                                    }
                                                }
                                                if($del_btwn) {
                                                    $status_chk = true;
                                                    $cat_data = $this->model_catalog_category->getCategory($prd_category_id); 
                                                    //$message .= $cat_data['name'].$del_btwn."<br>";
                                                    $no_delivery[$cat_data['name']]['time']  = $del_btwn;
                                                }
                                            }
                                            $checked_delivery_category[] = $prd_category_id;
                                        }
                                        
                                    }
                                }


                            }
                        }
                    }
                    if (!$status_chk) {
                        $quote = $this->{'model_shipping_' . $result['code']}->getQuote($address);
                        if ($quote and $quote['code']) {
                       // if ($quote && !$quote['delivery_msg_status']) {
                            $quote_data[$result['code']] = array(
                                'title' => $quote['title'],
                                'quote' => $quote['quote'],
                                'code'  => $result['code'], 
                                'sort_order' => $quote['sort_order'],
                                'error' => $quote['error'],
                                'msg' => (isset($quote['delivery_msg_status'])) ? $quote['delivery_msg_status'] : ''
                            );
                        }
                        elseif ($quote['delivery_msg_status']) {
                            // Shipping method warnings
                            $shipping_methodes_warnings[$result['code']] = $quote['delivery_msg_status'];
                            $shipping_methodes_extra[$result['code']] = array(
                                'title' => $quote['title'],
                                'quote' => $quote['quote'],
                                'code'  => $result['code'], 
                                'sort_order' => $quote['sort_order'],
                                'error' => $quote['error'],
                                'msg' => (isset($quote['delivery_msg_status'])) ? $quote['delivery_msg_status'] : ''
                            );
                        }
                    }else{
                        $quote = $this->{'model_shipping_' . $result['code']}->getQuote($address);
                        if($quote['delivery_msg_status']) {
                            $shipping_methodes_warnings[$result['code']] = $quote['delivery_msg_status'];
                        } else {
                            
                            if($message) {
                                $shipping_methodes_warnings[$result['code']][0] = $this->language->get('error_not_available').$message;
                            }
                            /*
                            if(isset($no_delivery)) {
                                foreach($no_delivery as $key=> $value) {
                                    $message = $key;
                                    if(isset($value['minimum'])) {
                                        $message.=sprintf($this->language->get('error_minimum_cart'), $value['minimum']);
                                    }
                                    if(isset($value['time'])) {
                                        $message.=$value['time'];
                                    }
                                    $shipping_methodes_warnings[$result['code']][] = $message;
                                }  
                            }
                            */
                        }

                        $shipping_methodes_extra[$result['code']] = array(
                            'title' => $quote['title'],
                            'quote' => $quote['quote'],
                            'code'  => $result['code'], 
                            'sort_order' => $quote['sort_order'],
                            'error' => $quote['error'],
                            'msg' => (isset($quote['delivery_msg_status'])) ? $quote['delivery_msg_status'] : ''
                        );
                        
                        
                    }

                }
            }

            /*
            unset($checked_min_category);
            unset($checked_delivery_category);
            */

            $sort_order = array();

            foreach ($quote_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $quote_data);

            $shipping_methodes = $quote_data;
            //shipping end
            //Payment -
            $method_data = array();

            #payment for selected shipping only
            if(isset($this->request->post['shipping_method']) && $this->request->post['shipping_method']) {
                $shippingcode = explode(".",$this->request->post['shipping_method']);
                $paymentAvail = $this->config->get($shippingcode[0].'_payment_methodes');
				if($paymentAvail=="") {
                    unset($paymentAvail);
				}
				unset($shippingcode);
            }
            #payment for selected shipping only

            //$this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('payment');
            foreach ($results as $result) {
                $this->load->model('payment/' . $result['code']);
                if (method_exists($this->{'model_payment_' . $result['code']}, 'isposonly'))
                    continue;
                if ($this->config->get($result['code'] . '_status')) {
                    $pay_chk = false;
                    if ($this->cart->hasProducts()) {
                        foreach ($this->cart->getProducts() as $cart_product) {
                            if (!empty($cart_product['payment_methodes']) and ! in_array($result['code'], $cart_product['payment_methodes'])) {
                                $pay_chk = true;
                            }
                        }
                    }
                    #payment for selected shipping only
                    if(isset($paymentAvail)) {
						if(!in_array($result['code'],$paymentAvail)) {
							$pay_chk = true;
                            //$message='No '.$this->language->get('text_title').' to this shipping method ';
						}
					}
                    if (!$pay_chk) {

                        $method = $this->{'model_payment_' . $result['code']}->getMethod($address, $total);
                        if ($method) {
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

            $payment_methodes = $method_data;
        }

        
        $json['shipping_methodes'] = $shipping_methodes;
        $json['payment_methodes'] = $payment_methodes;

        $json['shipping_warnings'] = $shipping_methodes_warnings;
        $json['shipping_methodes_available'] = $shipping_methodes_extra;
        // $json['shipping_methodes'] = $this->getChild('checkout/shipping_methodetm'); //$this->data['shipping_method']
        // $json['payment_methodes'] = $this->getChild('checkout/payment_methodetm');
        //$json['freeproducts'] = json_decode($this->getChild('module/freeproducts/mobileapp')); //newfreeproduct

        $json['freeproducts'] = json_decode($this->getChild('module/freeproducts/mobileapp'));  
        
        // print_r($json['deliveries']);
        $json['requested_times'] = array();
        if(isset($this->request->post['shipping_method'])){
            if (isset($json['shipping_methodes']['home']) && $this->request->post['shipping_method']=='home.home') {
                $shipping_method = 'home.home';
                $store_delivery_time = $this->config->get('home_delivery_storetime');
            }
            elseif ($this->request->post['shipping_method']=='collect.collect') {
                $shipping_method = 'collect.collect';
                $store_delivery_time = $this->config->get('collect_in_storetime');
            }
            // #TODO other methods
            /* else {
                $shipping_method = 'collect.collect';
                $store_delivery_time = $this->config->get('collect_in_storetime');
            } */
        }
        else {
            if (isset($json['shipping_methodes']['home'])) {
                $shipping_method = 'home.home';
                $store_delivery_time = $this->config->get('home_delivery_storetime');
            } else {
                $shipping_method = 'collect.collect';
                $store_delivery_time = $this->config->get('collect_in_storetime');
            }
        }
        $current_time = strtotime('now');

        if ($this->config->get('config_online_orders')) {
            $json['closed_message_head']= $this->language->get('text_onlinemsg_head');
            $json['closed_message_content']=$this->language->get('text_onlinemsg_content');
        }else{
            if(!!$this->config->get('config_preordering_status')){
                if($this->config->get('config_holiday_online_orders') && $this->postcode->isStoreHoliday()) {
                    $json['closed_message_head']=$this->language->get('text_holidaymsg_head');
                    $json['closed_message_content']=$this->language->get('text_holidaymsg_content');
                }else if ($this->config->get('config_store_time_orders') && !$this->postcode->isStoreOpened($this->config->get('config_store_id'))) {
                    $json['closed_message_head']=$this->language->get('text_storetimemsg_head');
                    $json['closed_message_content']=$this->language->get('text_storetimemsg_content');
                }
            }
        }
        if(!$this->config->get('config_preordering_status') && !isset($json['closed_message_head']) && $this->postcode->isStoreHoliday()){
            $json['closed_message_head']=$this->language->get('text_holidaymsg_head');
            $json['closed_message_content']=$this->language->get('text_holidaymsg_content');
        }
        if(!isset($json['closed_message_head'])){
            $json['requested_times'] = (isset($this->request->post['dinein_table_id']))?array():$this->postcode->getnewrequestedtimes($store_delivery_time, $current_time, 0, $shipping_method);
            if(empty($json['requested_times']) && isset($this->session->data['product_no_times'])){
                $json['closed_message_head']=$this->language->get('error_avaliable_times');
                $json['closed_message_content']=$this->language->get('error_avaliable_times');
            }
        }
        else {
            // STORE ORDERS CLOSED
            unset($json['products']);
            unset($json['shipping_methodes']);
            unset($json['payment_methodes']);
            unset($json['totals']);
        }
        
        if(!isset($json['closed_message_head']) && !empty($not_available_items)){
            $notin = array();            
            foreach($not_available_items as $itm){
                if($itm['in_qty']>$itm['tot_qty']){
                    $notin[] = $itm['name'];
                }
            }
            if(count($notin)>1){
                $json['closed_message_head'] .= implode(" ",$notin);
                $json['closed_message_head'] .=' , etc are not avaialble';
            }else if (count($notin)==1) {
                $json['closed_message_head'] .= implode(" ",$notin);
                $json['closed_message_head'] .=' is not avaialble';
            }
        }
        unset($this->session->data['product_no_times']);
        $json['totals'] = $total_data;

        //print_r($json);
        //$this->log->write(print_r($json, true));
        $json['storeopened'] = $this->postcode->isStoreOpened($this->config->get('store_id'))?1:0;
        
        $this->response->setOutput($this->jsonp($json, true));
        //die();
    }
    
    private function redeemfreeproducts($points) {
        //list all free product
        $this->load->model('catalog/product');
        $redeem_freeproducts  = array();
        $cart_product = array();
        foreach($this->cart->getProducts() as $cartproduct) {
            $cart_product[$cartproduct['product_id']] = array('name' => $cartproduct['name'], 'price' => $cartproduct['price'], 'quantity' => $cartproduct['quantity']);
            if(isset($this->session->data['free_products'][$cartproduct['product_id']]) && $this->session->data['free_products'][$cartproduct['product_id']]['qty']>$cartproduct['quantity']) {
                $this->session->data['free_products'][$cartproduct['product_id']]['qty'] =  $cartproduct['quantity'];
            }
        }
        if($this->config->get('config_redeem_free_product')) {

            if(isset($this->request->post['redeem_products']) && $this->request->post['redeem_products']) {     
                $this->request->post['redeem_products'] = json_decode(html_entity_decode($this->request->post['redeem_products']),true);
                foreach($this->request->post['redeem_products'] as $product) {
                    if(!isset($this->session->data['free_products'][$product['product_id']])) {
                        $this->session->data['free_products'][$product['product_id']]= array('qty'=>1,'amount'=>$product['amount']);
                    } else {
                        $this->session->data['free_products'][$product['product_id']]['qty']++; 
                    }
                }
            }
            $max_free = $this->config->get('config_free_product_multiple');
            foreach($this->config->get('config_free_products') as $freegroup) {
                if($freegroup['amount'] <= $points) {
                    foreach($freegroup['free_products'] as $product) {
                        if(isset($cart_product[$product])) {                           
                            if($max_free && $max_free < $cart_product[$product]['quantity']) {
                                $cart_product[$product]['quantity'] = $max_free;
                            }                 
                            $redeem_freeproducts[] = array(
                                'amount' => $freegroup['amount'],
                                'product_id' => $product,
                                'name' => $cart_product[$product]['name'],
                                'price' =>$cart_product[$product]['price'],
                                'cart_qty' =>$cart_product[$product]['quantity'],
                            ); 
                        } else {
                            if (isset($this->session->data['free_products'][$product])) {
                            unset($this->session->data['free_products'][$product]);
                        }
                            $product_info = $this->model_catalog_product->getProduct($product);
                            $options = array();
                            foreach ($this->model_catalog_product->getProductOptions($product) as $option) {
                                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                                    $option_value_data = array();

                                    foreach ($option['option_value'] as $option_value) {
                                        if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {

                                            $option_value_data[] = array(
                                                'product_option_value_id' => $option_value['product_option_value_id'],
                                                'option_value_id' => $option_value['option_value_id'],
                                                'name' => $option_value['name']
                                            );
                    }
                }                

                                    $options[] = array(
                                        'product_option_id' => $option['product_option_id'],
                                        'option_id' => $option['option_id'],
                                        'name' => $option['name'],
                                        'type' => $option['type'],
                                        'option_value' => $option_value_data,
                                        'required' => $option['required']
                                    );
                                } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                                    $options[] = array(
                                        'product_option_id' => $option['product_option_id'],
                                        'option_id' => $option['option_id'],
                                        'name' => $option['name'],
                                        'type' => $option['type'],
                                        'option_value' => $option['option_value'],
                                        'required' => $option['required']
                                    );
            }
                            }

                            $redeem_freeproducts[] = array(
                                'amount' => $freegroup['amount'],
                                'product_id' => $product,
                                'name' => $product_info['name'],
                                'price' =>$product_info['price'],
                                'product_options' =>$options,
                                'cart_qty' =>0,
                            ); 
                        }
                    }
                }                
            }
        } else {
            $this->session->data['free_products'] = array();
        }
        return $redeem_freeproducts;
    }

    /*
     * Order Preview
     *
     * params @ customer_id,address_id,shipping_method,payment_method,order_requested_time,comment
     * return @ json = array(order_id,shipping_method,payment_method,delivery_time,totals)
     */

    public function revieworder() { // reviewtableorder use $this->request->post['dinein']
        $data = array();
        //$this->log->write(print_r($this->request->post,true));
        $this->request->post['customer_id'] = $this->_decodeCustmerID($this->request->post['customer_id']);
        if ($this->customer->loginID($this->request->post['customer_id'])) {
        $this->load->model('account/address');
        $this->load->model('setting/extension');

        $error = false;
        $customer_qry = $this->db->query("SELECT *  FROM " . DB_PREFIX . "customer  WHERE customer_id='" . $this->request->post['customer_id'] . "'");
        if ($customer_qry->num_rows) {
            if ($customer_qry->row['cart'] && is_string($customer_qry->row['cart'])) {
                $cart = unserialize($customer_qry->row['cart']);
                foreach ($cart as $item_key => $qty) {
                    $this->session->data['cart'][$item_key] = (int) $qty;
                }
            } else {
                $error = true;
                $json['error'] = 'Shopping Cart is Empty';
            }
        } else {
            $error = true;
            $json['error'] = 'Unauthorized access';
        }
        //if(!$error){
        $products = $this->cart->getProducts();
        foreach ($products as $product) {
            $product_total = 0;
            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }
        }
        // }

        if($product_total==0) {
            $error = true;
            $json['error'] = 'No items found in cart!';
        }

        if(isset($this->request->post['shipping_method'])) {
            $shippingcode = explode(".",$this->request->post['shipping_method']);
            $paymentAvail = $this->config->get($shippingcode[0].'_payment_methodes');
            if($paymentAvail) {
                $paymnt_mthd = $this->request->post['payment_method'];
                if(!in_array($paymnt_mthd,$paymentAvail)) {
                    $error = true;
                    $json['error'] = 'This payment method is not available with selected shipping method';
                }
            }
        }

        if($error) {
            $this->response->setOutput($this->jsonp($json, true));
            return;
        }

        if($this->config->get('tips_status')) {
            if(isset($this->request->post['tips']) && $this->request->post['tips']) {
                $this->session->data['tips'] = $this->request->post['tips'];
            } else {
                unset($this->session->data['tips']);
            }
        }

        if (isset($this->request->post['coupon'])) {
            $this->load->model('checkout/offers');
            $this->request->post['coupon'] = trim($this->request->post['coupon']);
            $offer_couponinfo = $this->model_checkout_offers->getCoupon($this->request->post['coupon']);
            if ($offer_couponinfo['case_sensitive'] == 1) {
                $this->session->data['coupon'] = $this->request->post['coupon'];
            } else {
                $this->session->data['coupon'] = trim(strtoupper($this->request->post['coupon']));
            }
        }
        if (isset($this->request->post['voucher'])) {
             $this->session->data['voucher'] = $this->request->post['voucher'];
        }
        /*
        if (isset($this->request->post['reward'])) {
             $this->session->data['reward'] = $this->request->post['reward'];
        }
         * 
         */
        if($this->config->get('reward_points_status')) {
           if(isset($this->request->post['redeem_products']) || isset($this->request->post['reward'])) {
               if(isset($this->request->post['redeem_products']) && $this->request->post['redeem_products']) { 
                   $this->request->post['redeem_products'] = json_decode(html_entity_decode($this->request->post['redeem_products']),true);
               }
               $this->load->model('checkout/reward');
               $this->model_checkout_reward->validateReward();
           }
        }
        if(!isset($this->session->data['mobileapp'])){
            $this->session->data['mobileapp']=1;
        }
        if(!isset($this->request->post['address_id']) or empty($this->request->post['address_id'])) {
            $this->request->post['address_id'] = $this->customer->getAddressId();
        }
        $customer_address = $this->model_account_address->getAddress($this->request->post['address_id']);
        $total_data = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();
        $this->load->model('setting/extension');
        $sort_order = array();
        $tot_results = $this->model_setting_extension->getExtensions('total');

        foreach ($tot_results as $key => $value) {
            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $tot_results);

        foreach ($tot_results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('total/' . $result['code']);
                $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
            }
        }
        $sort_order = array();
        foreach ($total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $total_data);

        $shipping_methodes = array();
        $shipping = $this->model_setting_extension->getExtensions('shipping');
        foreach ($shipping as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('shipping/' . $result['code']);
                //if (method_exists($this->{'model_shipping_' . $result['code']}, 'isposonly'))
                if (!isset($this->request->post['dinein']) && method_exists($this->{'model_shipping_' . $result['code']}, 'isposonly'))
                    continue;
                $status_chk = FALSE;
                if ($this->cart->hasProducts()) {
                    foreach ($this->cart->getProducts() as $cart_product) {
                        if (!empty($cart_product['shipping_methodes']) and ! in_array($result['code'], $cart_product['shipping_methodes'])) {//
                            $status_chk = true;
                        }
                    }
                }
                if (!$status_chk) {
                    $quote = $this->{'model_shipping_' . $result['code']}->getQuote($customer_address);
                    if ($quote and $quote['title']) {
                        $shipping_methodes[$result['code']] = array(
                            'title' => $quote['title'],
                            'quote' => $quote['quote'],
                            'sort_order' => $quote['sort_order'],
                            'error' => $quote['error'],
                            'msg' => (isset($quote['delivery_msg_status'])) ? $quote['delivery_msg_status'] : ''
                        );
                    }
                }
            }
        }
        $sort_order = array();
        foreach ($shipping_methodes as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $shipping_methodes);
        $this->session->data['shipping_methods'] = $shipping_methodes;
        $payment_methodes = array();
        $payments = $this->model_setting_extension->getExtensions('payment');
        $pay_chk = false;
        foreach ($payments as $result) {
            $this->load->model('payment/' . $result['code']);
            if (method_exists($this->{'model_payment_' . $result['code']}, 'isposonly'))
                continue;
            if ($this->config->get($result['code'] . '_status')) {
                $pay_chk = false;
                if ($this->cart->hasProducts()) {
                    foreach ($this->cart->getProducts() as $cart_product) {
                        if (!empty($cart_product['payment_methodes']) and ! in_array($result['code'], $cart_product['payment_methodes'])) {
                            $pay_chk = true;
                        }
                    }
                }
                if (!$pay_chk) {
                    $method = $this->{'model_payment_' . $result['code']}->getMethod($customer_address, $total);
                    if ($method) {
                        $payment_methodes[$result['code']] = $method;
                    }
                }
            }
        }

        $sort_order = array();

        foreach ($payment_methodes as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $payment_methodes);
        $this->session->data['payment_methods'] = $payment_methodes;
        if(isset($this->request->post['dinein'])) {
            $sel_shipping = explode('.', 'dine.dine');
        }else {
            $sel_shipping = explode('.', $this->request->post['shipping_method']);
        }
        $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$sel_shipping[0]]['quote'][$sel_shipping[1]];
        $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']]; // $this->request->post['payment_method'];
        $this->session->data['order_requested_time'] = $this->request->post['order_requested_time'];
        $this->session->data['comment'] = (isset($this->request->post['comment'])) ? strip_tags($this->request->post['comment']) : '';

        //Save Add Start

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

                $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
            }
        }

        $sort_order = array();

        foreach ($total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $total_data);


        $data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
        $data['store_id'] = $this->config->get('config_store_id');
        $data['store_name'] = $this->config->get('config_name');
        if ($data['store_id']) {
            $data['store_url'] = $this->config->get('config_url');
        } else {
            $data['store_url'] = HTTPS_SERVER;
        }
        $data['customer_id'] = $this->customer->getId();
        $data['customer_group_id'] = $this->customer->getCustomerGroupId();
        $data['firstname'] = $this->customer->getFirstName();
        $data['lastname'] = $this->customer->getLastName();
        $data['email'] = $this->customer->getEmail();
        $data['telephone'] = $this->customer->getTelephone();
        $data['fax'] = $this->customer->getFax();

        $data['payment_firstname'] = $customer_address['firstname'];
        $data['payment_lastname'] = $customer_address['lastname'];
        $data['payment_company'] = $customer_address['company'];
        $data['payment_company_id'] = $customer_address['company_id'];
        $data['payment_tax_id'] = $customer_address['tax_id'];
        $data['payment_address_1'] = $customer_address['address_1'];
        $data['payment_address_2'] = $customer_address['address_2'];
        $data['payment_city'] = $customer_address['city'];
        $data['payment_postcode'] = $customer_address['postcode'];
        $data['payment_zone'] = $customer_address['zone'];
        $data['payment_zone_id'] = $customer_address['zone_id'];
        $data['payment_country'] = $customer_address['country'];
        $data['payment_country_id'] = $customer_address['country_id'];
        $data['payment_address_format'] = $customer_address['address_format'];

        $data['payment_method'] = $this->session->data['payment_method']['title'];
        $data['payment_code'] = $this->session->data['payment_method']['code'];
        if(in_array($data['payment_code'] , array('eway','stripe_payments'))) {
            $data['payment_inpage']='yes';
        }
        else {
            $data['payment_inpage']='no';
        }

        $data['shipping_firstname'] = $customer_address['firstname'];
        $data['shipping_lastname'] = $customer_address['lastname'];
        $data['shipping_company'] = $customer_address['company'];
        $data['shipping_address_1'] = $customer_address['address_1'];
        $data['shipping_address_2'] = $customer_address['address_2'];
        $data['shipping_city'] = $customer_address['city'];
        $data['shipping_postcode'] = $customer_address['postcode'];
        $data['shipping_zone'] = $customer_address['zone'];
        $data['shipping_zone_id'] = $customer_address['zone_id'];
        $data['shipping_country'] = $customer_address['country'];
        $data['shipping_country_id'] = $customer_address['country_id'];
        $data['shipping_address_format'] = $customer_address['address_format'];

        $data['shipping_method'] = $this->session->data['shipping_method']['title'];
        $data['shipping_code'] = $this->session->data['shipping_method']['code'];

        if(isset($this->request->post['dinein'])) {
            $data['dinein_table_id'] = $this->request->post['dinein_table_id'];
            $data['peoples'] = (isset($this->request->post['dinein_no_of_people']))?(int)$this->request->post['dinein_no_of_people']:0;
            $data['order_from'] =6;//Waiter App
        }
        $product_data = array();

        // Reward points
        //echo $total;die();
        $total_rewads = 0;
        $this->load->model('setting/setting');
        
        
        $consider_product=false;
        $total_cal = $total;
        if($this->config->get('reward_points_status')){
            
            $no_offers = true;
            if(!$this->config->get('config_reward_points_work_offers')){
                foreach($total_data as $total_datacode){
                    if($total_datacode['code'] == 'offers'){
                        $no_offers = true;
                    }
                }
            }


            if($no_offers) {

                if($this->config->get('reward_exclude_product')){                    
                    foreach ($products as $cart_key => $product) {
                        if(in_array($product['product_id'],$this->config->get('reward_exclude_product'))){                            
                            $total_cal -= (float)$product['price'];
                        }
                    }
                }
                if($this->config->get('config_reward_points_exclude_total')){                
                    foreach ($this->config->get('config_reward_points_exclude_total') as $exclude_code){
                        foreach($total_data as $total_datacode){
                            if($total_datacode['code'] == 'reward') continue;    //skip reward total already applied
                            if($total_datacode['code'] == $exclude_code && $total_datacode['value'] >0 ){
                                    $total_cal -= (float)$total_datacode['value'];
                            }
                        }
                    }                    
                } 
                $min_order_amount = $this->config->get('config_reward_points_min_order_amount');
                if($total_cal && $min_order_amount <= $total_cal) {
                    if($this->config->get('config_reward_points_type')=="I") {
                        $consider_product=true;
                    } else {
                        if($this->config->get('config_reward_points_type')=="P") {
                            if($this->config->get('config_reward_point_consider_decimal')) {
                                $total_rewads =  floatval($this->config->get('config_reward_points_points')) *  $total_cal;
                            } else {
                                $total_rewads =  floatval($this->config->get('config_reward_points_points')) * floor($total_cal);
                            } 
                        } else if($this->config->get('config_reward_points_type')=="F") {   
                             if($min_order_amount && $this->config->get('config_repeat_reward') && $total_cal > $min_order_amount ) {
                                $total_rewards = $this->config->get('config_reward_points_points') * (int)($total_cal/$min_order_amount);
                             } else {
                                $total_rewads = $this->config->get('config_reward_points_points');
                             }
                             
                        } else {
                            $multipleorder = $this->config->get('config_reward_point_order_amount');  
                            $repeat = $this->config->get('config_repeat_reward');
                            if($multipleorder) {
                                $min_order_amount = 0;
                                foreach($multipleorder as $multiples) {
                                    if($multiples[0] <= $total_cal) {
                                        if($multiples[0] >= $min_order_amount) {
                                            $total_rewads = $multiples[1];
                                            $min_order_amount = $multiples[0];
                                        }
                                    } else {
                                        $repeat = false;
                                    }
                                }
                                if($min_order_amount && $repeat && $total_cal >= $min_order_amount*2 ) {
                                    $total_rewads = $total_rewads * (int)($total_cal/$min_order_amount);
                                }
                            }
                        }

                        if($this->config->get('config_reward_points_consider_product')) {
                            $consider_product=true;
                        }

                        //echo "reward point : ".$total_rewads."<br>";
                        //add reward point for store
                        $multipoints = $this->config->get('config_reward_points_store');
                        if($multipoints && isset($multipoints[$data['store_id']])) {
                        $addreward = $this->config->get('config_reward_points_store_fixed');
                        if($addreward[$data['store_id']]) {
                            $total_rewads+=$addreward[$data['store_id']];
                        } else {
                            $addreward = $this->config->get('config_reward_points_store_xtimes');
                            if($addreward[$data['store_id']]) {
                                $total_rewads*=$addreward[$data['store_id']];
                            }
                        }
                        }
                        //echo "store point : ".$total_rewads."<br>";
                        //add reward point for shipping method
                        if($data['shipping_code'] && $this->config->get('config_reward_points_shipping_methodes')) {
                            $multipoints = $this->config->get('config_reward_points_shipping_methodes');
                            $sel_shipping = explode('.',$data['shipping_code']);
                            if($multipoints && in_array($sel_shipping[0],$multipoints)) {
                        $addreward = $this->config->get('config_reward_points_shipping_methode_fixed');
                        if($addreward[$sel_shipping[0]]) {
                            $total_rewads+=$addreward[$sel_shipping[0]];
                        } else {
                            $addreward = $this->config->get('config_reward_points_shipping_methode_xtimes');
                            if($addreward[$sel_shipping[0]]) {
                                $total_rewads*=$addreward[$sel_shipping[0]];
                            }
                        }
                            }
                        }

                        //add reward point for payment method
                        $multipoints = $this->config->get('config_reward_points_payment_methodes');
                        if($data['payment_code'] && $multipoints && in_array($data['payment_code'],$multipoints)) {
                        $addreward = $this->config->get('config_reward_points_payment_methode_fixed');
                        if($addreward[$data['payment_code']]) {
                            $total_rewads+=$addreward[$data['payment_code']];
                        } else {
                            $addreward = $this->config->get('config_reward_points_payment_methode_xtimes');
                            if($addreward[$data['payment_code']]) {
                                $total_rewads*=$addreward[$data['payment_code']];
                            }
                        }
                        } 
                        
                       
                        
                    }
                }
                $points = $this->customer->getRewardPoints(); // #TODO NEW
                $this->redeemfreeproducts($points); // #TODO NEW

            }
        }      

        
        foreach ($this->cart->getcartProducts() as $key=>$product) {
            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['option_value'];
                } else {
                    $value = $this->encryption->decrypt($option['option_value']);
                }

                $option_data[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_option_value_id' => $option['product_option_value_id'],
                    'option_id' => $option['option_id'],
                    'option_value_id' => $option['option_value_id'],
                    'name' => $option['name'],
                    'shortcode'=>$option['shortcode'],
                    'value' => $value,
                    'type' => $option['type']
                );
            }
            $productbundle = array();
           
            $product_data[] = array(
                'cart_key'=>$key,
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'download' => $product['download'],
                'quantity' => $product['quantity'],
                'subtract' => $product['subtract'],
                'price' => (isset($products[$key]) && isset($products[$key]['price']))?$products[$key]['price']:$product['price'],
                'total' => (isset($products[$key]) && isset($products[$key]['price']))?$products[$key]['total']:$product['total'],
                'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                // 'reward'     => $product['reward']
                // Reward points
                'reward' => (($consider_product) ? ($total_rewads + $product['reward']) : $total_rewads),
                'bundle' => $product['bundled_data'],
                'toppings'=>$product['toppings'],
                'dressings'=>$product['dressings'],
            );
            $total_rewads = 0;
        }

        // Gift Voucher
        $voucher_data = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $voucher_data[] = array(
                    'description' => $voucher['description'],
                    'code' => substr(md5(mt_rand()), 0, 10),
                    'to_name' => empty($voucher['to_name']) ? $data['firstname'] . ' ' . $data['lastname'] : $voucher['to_name'],
                    'to_email' => empty($voucher['to_email']) ? $data['email'] : $voucher['to_email'],
                    'from_name' => empty($voucher['from_name']) ? $this->config->get('config_name') : $voucher['from_name'],
                    'from_email' => empty($voucher['from_email']) ? $this->config->get('config_email') : $voucher['from_email'],
                    'voucher_theme_id' => $voucher['voucher_theme_id'],
                    'message' => $voucher['message'],
                    'amount' => $voucher['amount']
                );
            }
        }

        $data['products'] = $product_data;
        $data['vouchers'] = $voucher_data;
        $data['totals'] = $total_data;
        $data['comment'] = isset($this->session->data['comment']) ? $this->session->data['comment'] : '';
        //$this->log->write('MOB 5');
        //$this->log->write($this->session->data['order_requested_time']);
        //$this->log->write(time());
        $data['order_requested_time'] = isset($this->session->data['order_requested_time']) ? $this->session->data['order_requested_time'] : time();
        $data['total'] = $total;
        $data['affiliate_id'] = 0;
        $data['commission'] = 0;
        $data['language_id'] = $this->config->get('config_language_id');
        $data['currency_id'] = $this->currency->getId();
        $data['currency_code'] = $this->currency->getCode();
        $data['currency_value'] = $this->currency->getValue($this->currency->getCode());
        $data['ip'] = $this->request->server['REMOTE_ADDR'];

        if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
            $data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
            $data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
        } else {
        $data['forwarded_ip'] = '';
        }

    
        $data['user_agent'] =  'APP:'. $this->request->server['HTTP_USER_AGENT'];
        if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
            $data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
        } else {
        $data['accept_language'] = '';
        }
        $this->session->data['shipping_address_id']=$this->request->post['address_id'];
        $this->session->data['payment_address_id']=$this->request->post['address_id'];
        $this->load->model('checkout/order');

        $pendingOrdersChecking = $this->model_checkout_order->getPendingOrders($this->request->post['customer_id']);
        if($pendingOrdersChecking && sizeof($pendingOrdersChecking)> 0)
        {
            $data['payment_under_process_msg'] = 'Your previous payment is not processed yet. Please wait!';
        }
        else{
            $pending_orders =  $this->model_checkout_order->getOrdersOfCustomer($this->request->post['customer_id']);
            if(isset($this->request->post['action']) && $this->request->post['action'] == 'review') {
                #get review
            }
            else if($pending_orders->num_rows <= 0 ||  (int)$this->config->get('config_hold_checkout') === 0){
                $this->session->data['order_id'] = $this->model_checkout_order->addOrder($data);
                $data['order_id'] = $this->session->data['order_id'];
            }
            else{
                $msg = "Another order has already been placed";
                if($pending_orders->row){
                    $order_id = $pending_orders->row['order_id'];
                    $time = strtotime($pending_orders->row['date_added']);
                    $order_placed_time =  (round(abs($time - time()) / 60,0)-1). " minute";
                    $msg .= " with order id ".$order_id." before ".$order_placed_time.'.';
                }
                else{
                    $msg .='.';
                }

                $telephone = $this->config->get('config_telephone');
                $data['checkout_hold_msg'] = $msg." If you need to amend this order please call us on ".$telephone;
            }
        }
        //Save Order End
        //$shipping_address = $this->model_account_address->getAddress($this->request->post['address_id']);
                
        //$json['shipping_method'] = $this->session->data['shipping_method']['title'];
        // $json['payment_method'] = $this->session->data['payment_method']['title'];

        $data['delivery_time'] = ($this->session->data['order_requested_time'] <= time() ? 'AS SOON AS POSSIBLE':date("h:i A (l)",  $this->session->data['order_requested_time']));
        // $data['delivery_time'] = date("h:i A (l)", $this->session->data['order_requested_time']);
        //$json['totals'] = $total_data;
         if(isset($this->request->post['dinein'])) {
             $data['dinein'] = true;
         }
        }
        $this->response->setOutput($this->jsonp(json_encode($data)));
    }
    
    //order_id,customer_id
    public function confirmorder() {
        $json = array();
        $this->request->post['payment_code'] ='cod';
        //$this->log->write('CONFIRM ORDER');
        //$this->log->write(print_r($this->request->post,true));
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->request->post['order_id']);
        if ($order_info) {
            $order_info['total_text'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

            unset($this->session->data['cart']);
            unset($this->session->data['track']);
            unset($this->session->data['reward']);
            unset($this->session->data['order_requested_time']);
            unset($this->session->data['comment']);
            unset($this->session->data['shipping_address_id']);
            unset($this->session->data['payment_address_id']);
            unset($this->session->data['order_id']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['shipping']);


            $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart=''  WHERE customer_id='" . $order_info['customer_id'] . "'");
            // $this->log->write("UPDATE " . DB_PREFIX . "customer SET cart=''  WHERE customer_id='" . $order_info['customer_id'] . "'");
          //  $this->log->write(print_r($this->session->data, true));

            $json = $order_info;
            $json['success'] = 'success';
        } else {
            $json['error'] = 'Techinical Isssue';
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    //order_id,customer_id
    public function orderpaymenturl() {
      //  $this->log->write(' ORDER PAYMENT CALL');
       //(print_r($this->request->get, true));       
        $this->session->data['order_id'] = $this->request->get['order_id'];
        $this->session->data['mobileapp_payment'] = 'Yes';
        if(isset($this->request->get['inbr']) && $this->request->get['inbr']=='safari') {
            $this->session->data['mobileapp_inappsafari'] = 'Yes';
        }
        $this->log->write(' ORDER PAYMENT CALL');
        $this->log->write(print_r($this->request->get,true));
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        if ($order_info) {
            if ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) 
                    ||  (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&  $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))  {
                $this->data['base'] = $this->config->get('config_ssl');
            } else {
                $this->data['base'] = $this->config->get('config_url');
            }
            
            if ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')))
                ||  (isset($this->request->server['HTTP_X_FORWARDED_PROTO']) &&  $this->request->server['HTTP_X_FORWARDED_PROTO'] == 'https')) {
                $server = HTTPS_IMAGE;
            } else {
                $server = HTTP_IMAGE;
            }
            $this->data['name'] = $this->config->get('config_name');
            if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
                $this->data['logo'] = $server . $this->config->get('config_logo');
            } else {
                    $this->data['logo'] = '';
            }
            if(isset($this->session->data['mobileapp_inappsafari'])) {
                $this->data['back_url'] = $this->config->get('config_iphoneapp_url_scheme').'://?paystatus=failed';
            }else {
            $this->data['back_url'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
            }
            
            $this->data['payment'] =  $this->getChild('payment/' . $order_info['payment_code']);
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/orderpaymenturl.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/module/orderpaymenturl.tpl';
            } else {
                $this->template = 'default/template/module/orderpaymenturl.tpl';
            }
            
           /* $this->response->addHeader('Access-Control-Allow-Origin: *');
            $this->response->addHeader('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
            $this->response->addHeader('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
            $this->response->addHeader('Access-Control-Allow-Credentials: true');
            * 
            */
            $this->response->setOutput($this->render());
        } else {
            $json = array();
            $json['error'] = 'Techinical Isssue';
            $this->response->setOutput($this->jsonp(json_encode($json)));
        }

    }

    //order_id,customer_id
    public function applepay() {
      //  $this->log->write(' ORDER applepay CALL');
        $this->session->data['order_id'] = $this->request->post['order_id'];
        unset($this->session->data['stripe_checkout']);
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        if ($order_info) {
         //   $this->log->write('  applepay IN' .  json_encode($this->request->post));
           
            $data_payment =  $this->getChild('payment/stripe_checkout/applepaysendmobile');

            $json = array();
            if(isset($this->session->data['stripe_checkout'])) {
                $json = $this->session->data['stripe_checkout'];
            }
            else {
                $json['error'] = 'Stripe not returned';
            }
           
         //   $this->log->write('  applepay OUT' .  json_encode($json));
            $this->response->setOutput($this->jsonp(json_encode($json)));
        } else {
            $json = array();
            $json['error'] = 'Techinical Isssue';
            $this->response->setOutput($this->jsonp(json_encode($json)));
        }

    }

    /*
     * Customer Orders
     *
     * params @ customer_id
     * return @ json = array(orders,total_reward,rewards)
     */

    public function getcustomerorders() {
        $json = array();
        if ($this->customer->loginID($this->request->post['customer_id'])) {
        $this->load->model('account/order');
       $results = $this->model_account_order->getOrders(0, 5);

        foreach ($results as $result) {
            $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
            $voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);
            $products = $this->model_account_order->getOrderProducts($result['order_id']);
            $product_names = array();
            foreach ($products as $product) {
                $product_names[] = $product['name'];
            }

            $json['orders'][] = array(
                'order_id' => $result['order_id'],
                'productsname' => implode(',', array_unique($product_names)),
                'name' => $result['firstname'] . ' ' . $result['lastname'],
                'status' => $result['status'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'products' => ($product_total + $voucher_total),
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'comment'=>$result['comment'],
                'order_statuses' => $this->model_account_order->getOrderHistories($result['order_id']),
            );
        }
        $this->load->model('account/reward');
        $json['total_reward'] = $this->model_account_reward->getTotalPoints();

        $results = $this->model_account_reward->getRewards();

        foreach ($results as $result) {
            $json['rewards'][] = array(
                'order_id' => $result['order_id'],
                'points' => $result['points'],
                'description' => $result['description'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'href' => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL')
            );
        }
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    /*
     * Reorder
     *
     * params @ customer_id,address_id,order_id
     * return @ json = cartdetails array
     */

    public function reorder() {
      // $this->log->write('REORDER ');
      // $this->log->write(print_r($this->request->post,true));
        $customdertoken =$this->request->post['customer_id'];
        $this->request->post['customer_id'] = $this->_decodeCustmerID($this->request->post['customer_id']);

        if ($this->customer->loginID($this->request->post['customer_id'])) {
        $this->load->model('account/order');
        $orderid = isset($this->request->post['orderid'])?$this->request->post['orderid'] :$this->request->post['order_id'];
        //$produts_items = $this->model_account_order->getOrderProducts($orderid);
        $produts_names = array();
        if(!isset($this->session->data['mobileapp'])){
            $this->session->data['mobileapp']=1;
        }
        $order_products = $this->model_account_order->getFormattedOrderProducts($orderid);
                //print_r($order_products);die();
        foreach($order_products as $order_product){
              if(isset($order_product['product_modified'])){
                continue;
            }
            $option_data = array();
            foreach ($order_product['option'] as $order_option) {
                 if ($order_option['type'] == 'select' || $order_option['type'] == 'radio') {
                    $option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
                } elseif ($order_option['type'] == 'checkbox') {
                    $option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
                } elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
                    $option_data[$order_option['product_option_id']] = $order_option['value'];
                } elseif ($order_option['type'] == 'file') {
                    $option_data[$order_option['product_option_id']] = $this->encryption->encrypt($order_option['value']);
                }
            }
            $topping_data = array();
            if(!empty($order_product['toppings'])){
                foreach ($order_product['toppings'] as $order_topping) {
                    if(!isset($topping_data[$order_topping['product_topping_id']])){
                        $topping_data[$order_topping['product_topping_id']] =  array();
                    }
                    $topping_data[$order_topping['product_topping_id']][$order_topping['product_topping_value_id']] = $order_topping['quantity'];
                }
            }
            if(!empty($topping_data)){
                $option_data['topping'] = $topping_data;
            }                                
                
              if(isset($order_product['deal_item']) && $order_product['bundle']) {
                  $dealproducts = array();
                  foreach ($order_product['bundle'] as $k => $bundled_product) {
                      $bund_modified = $this->model_account_order->checkProductmodifedafterOrder($orderid,$bundled_product['product_id']);
                      if($bund_modified) {
                          continue 2;
                      }
                      $bundled_data = array();
                      $bundled_data['main_product_id'] = $order_product['product_id'];
                      $bundled_data['bundle_id'] = $bundled_product['bundle_id'];
                      $bundled_data['bundleidval'] = strtotime('now');
  
                      if(isset($bundled_product['options'])) {
                          foreach($bundled_product['options'] as $bundleoption) {
                              if ($bundleoption['type'] == 'select' || $bundleoption['type'] == 'radio') {
                                  $bundled_data[$bundleoption['product_option_id']] = $bundleoption['product_option_value_id'];
                              } elseif ($bundleoption['type'] == 'checkbox') {
                                  $bundled_data[$bundleoption['product_option_id']][] = $bundleoption['product_option_value_id'];
                              } elseif ($bundleoption['type'] == 'text' || $bundleoption['type'] == 'textarea' || $bundleoption['type'] == 'date' || $bundleoption['type'] == 'datetime' || $bundleoption['type'] == 'time') {
                                  $bundled_data[$bundleoption['product_option_id']] = $bundleoption['value'];
                              } elseif ($bundleoption['type'] == 'file') {
                                  $bundled_data[$bundleoption['product_option_id']] = $this->encryption->encrypt($bundleoption['value']);
                              }
                          }
                      }
                      
                      if(isset($bundled_product['toppings'])) {
                          foreach($bundled_product['toppings'] as $order_row) {
                              $bundled_data['topping'][$order_now['product_topping_id']][$order_row['product_topping_value_id']]= $order_row['quantity']; 
                          }
                      }
  
                      if(isset($bundled_product['dressings'])) {
                          foreach($bundled_product['dressings'] as $order_row) {
                              $bundled_data['dressing'][$order_row['product_dressing_id']][$order_row['product_dressing_value_id']]= $order_row['quantity']; 
                          }
                      }   
                      $dealproducts[] =  array('product_id'=>$bundled_product['product_id'],'quantity'=>$bundled_product['quantity'],'option'=>$bundled_data);
                  }
  
                  $total_deal_item = count($dealproducts)-1;
                  for($i=0; $i < $total_deal_item; $i++ ) { 
                      $this->cart->add($dealproducts[$i]['product_id'], $dealproducts[$i]['quantity'], $dealproducts[$i]['option']);
                  }
  
                  if (!empty($option_data)) {
                      $option_data['bundled'] = $bundled_data;
                  } else {
                      $order_product['product_id'] = $bundled_product['product_id']; 
                      $option_data = $bundled_data;
                  }         
                  $option_data['dealcomplete'] = $bundled_product['product_id'];      
              }                              
            $this->cart->add($order_product['product_id'], $order_product['quantity'], $option_data);
        }
        $this->request->post['customer_id'] = $customdertoken;//$this->request->post['customer_id'];
        $this->request->post['address_id'] = $this->request->post['address_id'];
        return $this->cartdetails(true);
    }
    }

    /*
     * Customer data
     *
     * params @ customer_id
     * return @ json = array
     */

    public function getcustomerdetails() {
        //$this->customer->loginID($this->request->post['customer_id']);
        $this->request->post['customer_id']  =  $this->_decodeCustmerID($this->request->post['customer_id'] );
        $json = array();
        $this->load->model('account/customer');
        $json = $this->model_account_customer->getCustomer($this->request->post['customer_id']);
        $json['customer_id'] = $this->_encodeCustmerID($json['customer_id']);
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    /*
     * Customer data
     * params @ customer_id,firstname,lastname,email,telephone
     * return @ json = array
     */

    public function changecustomerdetails() {
        $json = array();
        $this->request->post['customer_id']  =  $this->_decodeCustmerID($this->request->post['customer_id'] );
        if ($this->customer->loginID($this->request->post['customer_id'])) {
        $this->request->post['fax'] = '';
        $this->load->model('account/customer');
        //echo $this->customer->getEmail();
        if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $json['error'] = 'Email Already Registered';
        }
        if (!isset($json['error'])) {
            $this->request->post['user_agent'] = 'APP:'. $this->request->server['HTTP_USER_AGENT'];
            $this->model_account_customer->editCustomer($this->request->post);
            $json['success'] = 'ok';
        }
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    /*
     * Customer All Adress
     *
     * params @ customer_id
     * return @ json = array
     */

    public function getcustomeraddresses() {
        $results = array();
        $this->request->post['customer_id']  =  $this->_decodeCustmerID($this->request->post['customer_id'] );
        if ($this->customer->loginID($this->request->post['customer_id'])) {
        $this->load->model('account/address');
        $results = $this->model_account_address->getAddresses();
        }
        $this->response->setOutput($this->jsonp(json_encode($results)));
    }

    /*
     * params @ customer_id,address_id, firstname, lastname, address, telephone , postcode
     * return @ json = array
     */

    public function changeaddress() {
        $json = array();
        $this->request->post['customer_id']  =  $this->_decodeCustmerID($this->request->post['customer_id'] );
        if ($this->customer->loginID($this->request->post['customer_id'])) {
        //$this->load->model('checkout/postcodes');
        $postcode = $this->postcode->postcodeformat($this->request->post['postcode']);
        $postcode_details = $this->postcode->getPostcodeDetails($postcode);
        if (empty($postcode_details)) {
            $json['error'] = 'Invalid Postcode';
        }
        if (!isset($json['error'])) {
            $this->load->model('account/address');
            $this->db->query("UPDATE " . DB_PREFIX . "address SET  firstname = '" . $this->db->escape($this->request->post['firstname']) . "', lastname = '" . $this->db->escape($this->request->post['lastname']) . "', company = '', company_id = '', tax_id = '', address_1 = '" . $this->db->escape($this->request->post['address']) . "', address_2 = '', city = '".$this->db->escape(isset($this->request->post['city'])?$this->request->post['city']:'')."', postcode = '" . $this->db->escape($postcode) . "', country_id = '', zone_id = '' WHERE address_id  = '" . (int) $this->request->post['address_id'] . "' AND customer_id = '" . (int) $this->customer->getId() . "'");
            $json['success'] = 'ok';
        }
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    /*
     * params @ customer_id, firstname, lastname, address, telephone , postcode
     * return @ json = array
     */

    public function addadress() {
      //  $this->log->write(print_r($this->request->post,true));
       // $this->load->model('checkout/postcodes');
        $postcode = $this->postcode->postcodeformat($this->request->post['postcode']);
        $postcode_details = $this->postcode->getPostcodeDetails($postcode);
        if (empty($postcode_details)) {
            $json['error'] = 'Invalid Postcode';
        }
        if (!isset($json['error'])) {
            $this->request->post['customer_id'] = $this->_decodeCustmerID($this->request->post['customer_id']);
            
            $this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int) $this->request->post['customer_id'] . "', firstname = '" . $this->db->escape($this->request->post['firstname']) . "', lastname = '" . $this->db->escape($this->request->post['lastname']) . "', company = '', company_id = '', tax_id = '', address_1 = '" . $this->db->escape($this->request->post['address']) . "', address_2 = '', city = '".$this->db->escape(isset($this->request->post['city'])?$this->request->post['city']:'')."', postcode = '" . $this->db->escape($postcode) . "', country_id = '', zone_id = ''");
            $address_id = $this->db->getLastId();
            $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int) $address_id . "' WHERE customer_id = '" . (int) $this->request->post['customer_id'] . "'");
            $json['success'] = array('customer_id' => $this->_decodeCustmerID($this->request->post['customer_id']), 'address_id' => $address_id);
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    /*
     * params @ customer_id, password,email,new_password
     * return @ json = array
     */

    public function changepassword() {
            $json = [];
          if (isset($this->request->post['email']) and isset($this->request->post['password'])) {
              // $this->log->write(print_r($this->request->post,true));
            $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($this->request->post['email'])) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($this->request->post['password']) . "'))))) OR password = '" . $this->db->escape(md5($this->request->post['password'])) . "') AND status = '1' AND approved = '1'");
            if ($customer_query->num_rows) {
                $this->request->post['customer_id']  =  $this->_decodeCustmerID($this->request->post['customer_id'] );
                $this->customer->loginID($this->request->post['customer_id']);
                $this->load->model('account/customer');
                $this->model_account_customer->editPassword($this->customer->getEmail(), $this->request->post['new_password']);
               // $this->log->write($this->customer->getEmail());
              //  $this->log->write($this->request->post['new_password']); 
                $json['success'] = 'ok';
                            
            } else {
                $json['error'] = 'No match for E-Mail Address and/or Password';
            }
        }
        else {
            $json['error'] = 'Wrong Parameter';
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
        /*
            $this->customer->loginID($this->request->post['customer_id']);
            $this->load->model('account/customer');
            $this->model_account_customer->editPassword($this->customer->getEmail(), $this->request->post['password']);
            $json['success'] = 'ok';
            $this->response->setOutput($this->jsonp(json_encode($json)));
        */
    }

    /*
     * params @ order_id
     * return @ json = array
     */

    public function getorderstatuses() {
        $json = array();
        if (isset($this->request->post['order_id'])) {
            $this->load->model('account/order');
            $json['order_statuses'] = $this->model_account_order->getallOrderHistories($this->request->post['order_id']);
            //$this->log->write('Mobileapp getorderstatuses :' . print_r($json['order_statuses'],true));
             if(!empty($json['order_statuses'])) {
                if(isset($json['order_statuses'][count($json['order_statuses'])-1]['status']) && $json['order_statuses'][count($json['order_statuses'])-1]['status']=='Failed'){
                    $json['payment_status'] = 'failed';
                }else {
                    $json['payment_status'] = 'success';
                }
            }else {
                $json['payment_status'] = 'failed';
            }
            
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    /*
     * Customer All Adress
     *
     * params @ email
     * return @ json = array
     */

    public function forgottenPassword() {
        $json = array();
        
        $this->load->model('account/customer');
        $this->language->load('account/forgotten');
        
        if (!isset($this->request->post['email'])) {
            $json['error_warning'] = $this->language->get('error_email');
        } elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $json['error_warning'] = $this->language->get('error_email');
        } else {
            // Add to activity log
            $this->language->load('mail/forgotten');
            $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

            if ($customer_info) {
                $this->load->model('account/auth');

                ### Generate an auth token
                $auth_token = "";
                while($auth_token=="") 
                {
                    $auth_token = $this->model_account_auth->generateRandomString(32);

                    // Verify it's unique and insert into auth table
                    if(!$this->model_account_auth->unique_auth_token($auth_token)) 
                    {
                        // Not unique, reset and try again
                        $auth_token = "";
                    }
                }

                $this->model_account_auth->insert_auth_token($auth_token, $customer_info['customer_id']);  
                
    
                ## Send the email with the Reset Pwd link

                $subject = sprintf($this->language->get('mail_subject'), $this->config->get('config_name'));
     
            $message  = sprintf($this->language->get('text_greeting'), $this->config->get('config_name')) . "\n\n";
                
                #$message .= sprintf($this->language->get('mail_click_reset'), $this->url->link('account/forgotten/resetPassword&auth='.$auth_token, '', 'SSL')) . "\n\n";
                $message .= $this->language->get('mail_click_reset') . "\n\n";
                $message .= $this->url->link('account/forgotten/resetPassword&auth='.$auth_token, '', 'SSL'). "\n\n\n";

            $message .=$this->language->get('text_thanks')."\n";
            $message .=$this->config->get('config_name');

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($this->request->post['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($this->config->get('config_name'));
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                $mail->setText($message, ENT_QUOTES, 'UTF-8');
            $mail->send();

            $json['text_message'] = $this->language->get('text_message');

        }
            else
            {
                $json['error_warning'] = $this->language->get('error_email');
            }
        }

        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    /*
     * params @ order_id,customer_id
     * return @ json = array
     */

    public function logout(){
        unset($this->session->data['cart']);
        unset($this->session->data['track']);
        unset($this->session->data['reward']);
        unset($this->session->data['order_requested_time']);
        unset($this->session->data['comment']);
        unset($this->session->data['shipping_address_id']);
        unset($this->session->data['payment_address_id']);
        unset($this->session->data['order_id']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['shipping_method']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['shipping']);
        $this->customer->logout(); 
        $this->response->setOutput($this->jsonp(json_encode(array('success'=>true))));
    }
    
    public function getorder() {
        $json = array();
        $this->request->post['customer_id']  =  $this->_decodeCustmerID($this->request->post['customer_id'] );
        if ($this->customer->loginID($this->request->post['customer_id'])) {
            if (isset($this->request->post['order_id'])) {
                $this->load->model('account/order');
                $json = $this->model_account_order->getOrder($this->request->post['order_id']);
                // ORDER STATUS
                $query = $this->db->query("SELECT name  FROM  " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int) $json['order_status_id'] . "'");
                if ($query->num_rows) {
                     $json['order_status'] = $query->row['name'];
                }
                else {
                     $json['order_status'] = '';
                }
                $odrerproducts = $this->model_account_order->getFormattedOrderProducts($this->request->post['order_id']);
               // $this->log->write(print_r('dsfdfgfg',true));
                //$this->log->write(print_r($json,true));
                foreach ($odrerproducts as $odrerproduct) {
                    $odrerproduct['price'] = $this->currency->format($odrerproduct['price'] + ($this->config->get('config_tax') ? $odrerproduct['tax'] : 0), $json['currency_code'], $json['currency_value']);
                    $odrerproduct['total'] = $this->currency->format($odrerproduct['total'] + ($this->config->get('config_tax') ? ($odrerproduct['tax'] * $odrerproduct['quantity']) : 0), $json['currency_code'], $json['currency_value']);
                   // $odrerproduct['href'] = $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $odrerproduct['product_id'], 'SSL');
                    $json['products'][]=$odrerproduct;
                }

                // Voucher
                $this->data['vouchers'] = array();

                $vouchers = $this->model_account_order->getOrderVouchers($this->request->post['order_id']);

                foreach ($vouchers as $voucher) {
                    $json['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount'], $json['currency_code'], $json['currency_value'])
                    );
                }

                $json['totals'] = $this->model_account_order->getOrderTotals($this->request->post['order_id']);
                
                // Delivery track
                $json['map'] = '';
                $date_added = date('Y-m-d',strtotime($json['date_added']));
                if($date_added == date('Y-m-d') && $json['order_status_id']!==$this->config->get('config_complete_status_id')) {
                    $takeoutorders = array();
                    if($this->config->get('drivermanagement_cancel_status')) {
                        foreach($this->config->get('drivermanagement_cancel_status') as $stats) {
                            $takeoutorders[$stats] = 1;
                        }                    
                    }
                    if($this->config->get('drivermanagement_complete_status')) {
                        foreach($this->config->get('drivermanagement_complete_status') as $stats) {
                            $takeoutorders[$stats] = 1;
                        }                    
                    }
                    if(!isset($takeoutorders[$json['order_status_id']])) {
                       // $query = $this->db->query("SELECT  latitude, longitude FROM ".DB_PREFIX."driver_location WHERE user_id = (SELECT e.user_id FROM ".DB_PREFIX."employee e LEFT JOIN ".DB_PREFIX."deliverys d ON e.employee_id = d.driver_id WHERE d.order_id = '".$this->request->post['order_id']."' order by d.delivery_id  DESC LIMIT 1 ) ORDER BY driver_location_id DESC LIMIT 1");
                       // if($query->rows) {   
                          //  $json['driver_postion'] = array('lat' => $query->row['latitude'], 'lng' => $query->row['longitude']); 
                            $this->data['driver_postion'] = '{ lat: 51.378577, lng: -0.584284 }';
                            $store_position = $this->postcode->getPostcodeDetails($this->config->get('config_postalcode'));
                            if($store_position) {
                                $json['store_position'] = array('lat' => $store_position['latitude'], 'lng' => $store_position['longitude']);
                            }
                            $customer_position = $this->postcode->getPostcodeDetails($json['shipping_postcode']);
                            if($customer_position) {
                                $json['customer_position'] = array('lat' => $customer_position['latitude'], 'lng' => $customer_position['longitude']);    
                            }
                            if($store_position && $customer_position) {
                                $json['map'] = HTTPS_SERVER . 'mobileapi5v4/getorderlocationmap&order_id='.$this->request->post['order_id'];
                            }
                      //  }
                    }
                }

                $json['order_statuses'] = $this->model_account_order->getallOrderHistories($this->request->post['order_id']);
                
               
                // ORDER STATUS TRACK
                $this->load->model('checkout/order');
                $order_status = $this->model_checkout_order->getOrderStatuses();
               // $this->data['order_status_id'] =  $json['order_status_id'];

                $shippingcode = explode('.',$json['shipping_code']);
                $code = $shippingcode[0];

                $track_statuses = $this->config->get('config_track_status');
                if($track_statuses) {
                    $statids = array();
                    foreach($order_status as $status) {
                        $orderstatus[$status['order_status_id']] = $status['name'];
                        //$statids[] = $status['order_status_id'];
                    }

                    $sort_order = array();
                    foreach ($track_statuses as $key => $value) {
                        if(isset($value[$code]) && $value[$code]) {                        
                            $sort_order[$key] = $value['sort_order'];
                            $track_status[$key]['name'] = $orderstatus[$key];
                            $track_status[$key]['id'] = $key;
                            $track_status[$key]['class'] = $value['class'];
                            $track_status[$key]['date_added'] = '';
                            $statids[] = $key;
                        }
                    }

                    if($statids) {
                        $query = $this->db->query("SELECT MAX(date_added) as date_added, order_status_id FROM ".DB_PREFIX."order_history WHERE order_id = '" . (int)$this->request->post['order_id'] . "' AND order_status_id IN (".implode(',',$statids).") GROUP BY order_status_id  ORDER BY date_added  ");

                        foreach($query->rows as $row) {
                            $track_status[$row['order_status_id']]['date_added'] = date('h:i a',strtotime($row['date_added']));
                        }

                        array_multisort($sort_order, SORT_ASC, $track_status);

                    }
                    $json['track_status'] = $track_status;
                } else {
                    $json['track_status'] = array(); 
                }
                // Rrquested time
                $json['entry_requested_time'] = 'Requested time';
                $json['entry_eta_time'] = 'Estimated time';

                if($json['shipping_code']=='home.home') {
                    $json['entry_delivery_time'] = 'Delivery time';
                    $sorting_time = ((int)$this->config->get('home_delivery_storetime') + (int)$this->config->get('home_sorting_time')+(int)$this->config->get('home_pan_time'))*60;
                } else {
                    $json['entry_delivery_time'] = 'Collection time';
                    $sorting_time = ((int)$this->config->get('collect_in_storetime') + (int)$this->config->get('collect_sorting_time')+(int)$this->config->get('collect_pan_time'))*60;
                }

                $dateadded = strtotime($json['date_added']);
                $requestedtime = strtotime($json['requested_time']);

                if($requestedtime <= $dateadded) {
                    $json['requested_time'] = 'AS SOON AS POSSIBLE';
                } else {
                    $json['requested_time'] = date('d/m/Y h:i A',$requestedtime);
                }
               
                if(isset($json['delivery_time'])) { 
                    if( strtotime($json['delivery_time']) <= $dateadded) {
                        if($json['requested_time']=='AS SOON AS POSSIBLE') {
                            $json['delivery_time'] = date('d/m/Y h:i A',$requestedtime+$sorting_time);
                        } else {
                            $json['delivery_time'] = $json['requested_time'];
                        }
                    } else {
                        $$json['delivery_time'] = date('d/m/Y h:i A',strtotime($json['delivery_time']));
                    }
                }else {
                     $json['delivery_time'] = '';
                }
                if(isset($json['eta_time'])) {
                    $json['eta_time'] = date('h:i A', $json['eta_time']);
                }else {
                    $json['eta_time'] = ''; 
                }
              
            }
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
     public function getorderlocationmap() {
     //    print_r($this->request->get);
        $this->session->data['order_id'] = $this->request->get['order_id'];
       // $this->load->model('account/order');
       // $order_info = $this->model_account_order->getOrder($this->request->get['order_id']);
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->request->get['order_id']);
      //  print_r($order_info); die();
        if ($order_info) {
            if ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) 
                    ||  (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&  $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))  {
                $this->data['base'] = $this->config->get('config_ssl');
            } else {
                $this->data['base'] = $this->config->get('config_url');
            }
            
            if ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')))
                ||  (isset($this->request->server['HTTP_X_FORWARDED_PROTO']) &&  $this->request->server['HTTP_X_FORWARDED_PROTO'] == 'https')) {
                $server = HTTPS_IMAGE;
            } else {
                $server = HTTP_IMAGE;
            }
            
            $this->data['back_url'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
           
            
            $this->data['locationmap'] =   $this->getChild('account/order/locationmap',$order_info);
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/orderlocationmap.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/module/orderlocationmap.tpl';
            } else {
                $this->template = 'default/template/module/orderlocationmap.tpl';
            }
            $this->response->setOutput($this->render());
        } else {
            $json = array();
            $json['error'] = 'Techinical Isssue';
            $this->response->setOutput(json_encode($json));
        }
        /*
        $json = array();
        if($this->request->post['order_id']) {
            $order_id = (int)$this->request->post['order_id'];
            $query = $this->db->query("SELECT  latitude, longitude FROM ".DB_PREFIX."driver_location WHERE user_id = (SELECT e.user_id FROM ".DB_PREFIX."employee e LEFT JOIN ".DB_PREFIX."deliverys d ON e.employee_id = d.driver_id WHERE d.order_id = '".$order_id."' order by d.delivery_id  DESC LIMIT 1 ) ORDER BY driver_location_id DESC LIMIT 1 ");
            if($query->rows) {
                $json = array('lat' => (float)$query->row['latitude'], 'lng' => (float)$query->row['longitude']); 
            }
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
         * 
         */
        
    }
    public function checkorderpaid() {
        $json = array();
        if (isset($this->request->post['order_id'])) {
           $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$this->request->post['order_id'] . "'  AND order_status_id > '0'");
           if ($order_query->num_rows) {
               $json['success'] = 1 ;
           }
           else {
                $json['success'] = 0 ;
           }
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    public function getstoreinfo() {

        $this->load->language('common/footer');
        
        $json = array();
        $json['name'] = $this->config->get('config_name');
        $json['owner'] = $this->config->get('config_owner');
        $json['address'] = $this->config->get('config_address');
        $json['email'] = $this->config->get('config_contactus_email');
        $json['telephone'] = $this->config->get('config_telephone');
        $json['postalcode'] = $this->config->get('config_postalcode');
        $json['map_url'] = $this->config->get('config_google_map');
        $json['powered_text'] = $this->language->get('text_mobile_powered');
        $json['coordinates'] = $this->postcode->getPostcodeLag($json['postalcode']);
        if($this->config->get('config_table_reservation_alert_mail')!==null) {
            $json['tablebooking'] = 1;
        }
       
        $json['store_url'] = HTTPS_SERVER;
        $json['store_times'] = array();
//        $json['store_times']['sunday'] = $this->config->get('sunday_store_times');
//        $json['store_times']['monday'] = $this->config->get('monday_store_times');
//        $json['store_times']['tuesday'] = $this->config->get('tuesday_store_times');
//        $json['store_times']['wednesday'] = $this->config->get('wednesday_store_times');
//        $json['store_times']['thursday'] = $this->config->get('thursday_store_times');
//        $json['store_times']['friday'] = $this->config->get('friday_store_times');
//        $json['store_times']['saturday'] = $this->config->get('saturday_store_times');

        $json['map_image'] =  $json['store_url'] .'image/mobile/map_'.$this->config->get('config_store_id').'.png';
        //Store Times
        $this->load->model('setting/setting');
       $json['store_times']=$this->model_setting_setting->getStoretimes();
       
        //Available Payments
        $json['payments'] = array();
        $this->load->model('setting/extension');
        $payments = $this->model_setting_extension->getExtensions('payment');
        foreach ($payments as $payment) {
            if ($this->config->get($payment['code'] . '_status')) {
                $this->load->model('payment/' . $payment['code']);
                if(method_exists($this->{'model_payment_' . $payment['code']}, 'isposonly')){
                    continue;
                }
                $this->load->language('payment/'. $payment['code']);
                $json['payments'][] = $this->language->get('text_title');
            }
        }
        //delivery
        $json['allshipping_methods'] = array();
        $results = $this->model_setting_extension->getExtensions('shipping');
        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('shipping/' . $result['code']);
                if (method_exists($this->{'model_shipping_' . $result['code']}, 'isposonly'))
                    continue;
                $this->load->language('shipping/'. $result['code']);
                $json['allshipping_methods'][]=array('code'=>$result['code'],'title'=>$this->language->get('text_title'));
            }
        }

        if($this->config->get('config_mobileapi_dine_status')){
            $json['app_dine_status']=1;
            $table_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store_tables`");
            if(!empty($table_query->rows)){
                foreach ($table_query->rows as $row) {
                    $tablesettings = unserialize($row['settings']);
                    $status = 1;
                    if(!empty($tablesettings) && isset($tablesettings['tb']) && $tablesettings['tb']=='1'){
                        $status = 0;
                    }
                    $json['store_tables'][]=array('table_id'=>$row['table_id'],'name'=>$row['name'],'table_status'=>$status);
                }
            }
            $json['comment_history']=array('No sauce','Is the order is ready ?','Test comment','Anybody there');
            $json['app_dine_menu']=$this->config->get('config_mobileapi_dine_menu');
            $json['dine_in_people_status'] = $this->config->get('config_dine_in_people_status');
        }
        if($this->config->get('config_mobileapi_ordering_status')){
            $json['app_ordering_status']=1;
            $json['app_ordering_menu']=$this->config->get('config_mobileapi_ordering_menu');
        }    
        $json['delivery_details']=$this->model_setting_setting->getDdeliverydetails();
        $json['storeopened'] = $this->postcode->isStoreOpened($this->config->get('store_id'))?1:0; 
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }


    public function storeclosemessage() {
        $store_closed = $this->config->get('config_store_close');
        if (strpos($store_closed, '{{time}}') !== false) {
            $days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
            $time = '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
            foreach($days as $day) {
                $time.= '<tr>';
                $time.= '<td> '.ucfirst($day).' </td>';
                $time.= '<td>';
                $time.= '<table>';
                $monday_times=$this->config->get($day.'_store_times');
                foreach($monday_times as $monday_time) {
                    $time.='<tr>';
                    if(!empty($monday_time['start'])){
                        $start=explode(':',$monday_time['start']);
                        $end=explode(':',$monday_time['end']);
                        if((int)$start[0]>12){
                            $time.='<td>'.((int)$start[0]-12).':'.$start[1].' pm </td>';
                        } else {
                            $time.='<td>'.((int)$start[0]).':'.$start[1].' am </td>'; 
                        }
                        if((int)$end[0]>12){
                            $time.='<td> '.((int)$end[0]-12).':'.$end[1].' pm </td>';
                        } else {
                            $time.='<td> '.((int)$end[0]).':'.$end[1].' am </td>'; 
                        }
                    } else {
                        $time.='<td> Closed </td>';
                    }
                    $time.='</tr>';
                }
                $time.= '</table>';
                $time.= '</td>';
                $time.= '</tr>';
            }
            $time.= '</table>';
            $store_closed1 = str_replace('{{time}}',$time,$store_closed);
            $store_closed = $store_closed1;
            
        }
        if (strpos($store_closed, '{{address}}') !== false) {
            $time  = $this->config->get('config_address');
            $store_closed1 = str_replace('{{address}}',$time,$store_closed);
            $store_closed = $store_closed1;
            
        }
        if (strpos($store_closed, '{{telephone}}') !== false) {
            $time  = $this->config->get('config_telephone');
            $store_closed1 = str_replace('{{telephone}}',$time,$store_closed);
            $store_closed = $store_closed1;            
        }
        return $store_closed1;
    }


    public function getstoreopened() {
        $json = array();
        $json['storeopened'] = $this->postcode->isStoreOpened($this->config->get('store_id'))?1:0;
        if ( $json['storeopened'] ==0 ) {
            $this->language->load('checkout/checkout');
            if ($this->config->get('config_online_orders')) {
                // disable online orders
                $json['online_orders_closed']  =  $this->config->get('config_online_orders'); // NEW
                
                $json['closed_message_head']= $this->language->get('text_onlinemsg_head');
                $json['closed_message_content']=$this->storeclosemessage();
                if(!$json['closed_message_content']) {
                    $json['closed_message_content']=$this->language->get('text_onlinemsg_content');
                }
            }else{
                if($this->config->get('config_holiday_online_orders') && $this->postcode->isStoreHoliday()) {
                    // disable online orders on holiday
                    $json['closed_message_head']=$this->language->get('text_holidaymsg_head');
                    $json['closed_message_content']=$this->storeclosemessage();
                    if(!$json['closed_message_content']) {
                        $json['closed_message_content']=$this->language->get('text_holidaymsg_content');
                    }
                }else if ($this->config->get('config_store_time_orders') && !$this->postcode->isStoreOpened($this->config->get('config_store_id'))) {
                    //store not opened
                    $json['closed_message_head']=$this->language->get('text_storetimemsg_head');
                    //$json['closed_message_content']=$this->language->get('text_holidaymsg_content');
                    $json['closed_message_content']=$this->language->get('text_storetimemsg_content');
                }
            }
            if(!isset($json['closed_message_head'])) {
                $json['closed_message_head'] = $this->postcode->getStoretimeMessage();
                $json['closed_message_content']= $this->language->get('text_holidaymsg_content');
            }
            /*
            if(!isset($json['closed_message_head']) && $this->postcode->isStoreHoliday()){
                $json['closed_message_head']=$this->language->get('text_holidaymsg_head');
                $json['closed_message_content']=$this->language->get('text_holidaymsg_content');
            }
            */
        }
        if ($this->config->get('config_mobile_orders')) { 
            $this->language->load('checkout/checkout');
            $json['mobile_orders_closed']  =  $this->config->get('config_mobile_orders'); // NEW
            $json['mobileclosed_message_head'] = $this->language->get('mobileclosed_message_head');
            $json['mobileclosed_message_content'] = $this->language->get('mobileclosed_message_content');
            $json['site_href'] = HTTPS_SERVER;
        }
        
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    /*
     *
     */

    public function getMenus() {
        $json =array();
        $json = $this->cache->get('product.mobile.menus.' . (int) $this->config->get('config_language_id'));
        
        if (!$json) {
            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            $json = array();
            $json['cat_last_modified'] = $this->model_catalog_category->getLastCategoryModified();

            $results = $this->model_catalog_category->getCategories(0);
            $parent_id = $results[0]['category_id'];
            foreach($results as $result){
                $json['category'][$result['category_id']] = $this->getmenu($result['category_id']);
            }
            $json['menus'] = $json['category'][$parent_id] ;
            $json['categories'] = json_encode($json['category']);  

            $this->cache->set('product.mobile.menus.' . (int) $this->config->get('config_language_id'), $json);
	}
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    private function getmenu($parent_id) {
        $category = array();
        $results = $this->model_catalog_category->getCategories($parent_id);
        if ($results) {
            foreach ($results as $result) {
                if ($result['status']) {
                    if ($result['mobile_app_image']) {
                        $image = $this->model_tool_image->resize($result['mobile_app_image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
                    } else {
                        $image = false;
                    }
                    // Products
                    $data = array(
                        'filter_category_id' => $result['category_id'],
                        'start' => 0,
                        'limit' => 500
                    );

                    $products = array();
                    $products_sets = $this->model_catalog_product->getProducts($data);
                    foreach ($products_sets as $product) {
                        $products[] = $product['product_id'];
                    }

                    $category[] = array(
                        'category_id' => $result['category_id'],
                        'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                        'description' => strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),
                        'image' => $image,
                        'parent_id' => $result['parent_id'],
                        'categories' => $this->getmenu($result['category_id']),
                        'products' => $products,
                        'vegetarian'=>$result['vegetarian'],
                        'hot'=>$result['hot_count']
                    );
                }
            }
            return $category;
        } else {
            return NULL;
        }
    }
    public function getProduct() {
        
     //   $this->request->post += $this->request->get;     
        $this->request->get['product_id'] = $this->request->post['product_id'];
        $json = array();
        // echo "aaa";
       if (isset($this->request->get['product_id'])) {
            //echo "ssss";die();
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            $product_id = $this->request->get['product_id'];
            //echo "aaa";die();

            $product_info = $this->model_catalog_product->getProduct($product_id);
            $option_data = array();
            foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                    $option_value_data = array();

                    foreach ($option['option_value'] as $option_value) {
                        
                        if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                            $option_value_data[] = array(
                                'product_option_value_id' => $option_value['product_option_value_id'],
                                'option_value_id' => $option_value['option_value_id'],
                                'name' => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
                                'shortcode'=> isset($option_value['shortcode'])?strip_tags(html_entity_decode($option_value['shortcode'], ENT_QUOTES, 'UTF-8')):'',
                                //'image' => $image,
                                'price' => (float) $option_value['price'] ? $this->currency->format($option_value['price']) : false,
                                'price_prefix' => $option_value['price_prefix'],
                                'price_value' => ($option_value['price_prefix']=='-' ? -1 : 1 ) * (float) $option_value['price'] ? $this->currency->format($option_value['price'],'','',false) : 0,
                                'master_option_value'=>$option_value['master_option_value']
                            );
                        }
                    }
                    //$option_value['price_prefix']
                    $option_data[] = array(
                        'product_option_id' => $option['product_option_id'],
                        'option_id' => $option['option_id'],
                        'name' => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
                        'type' => $option['type'],
                        'option_value' => $option_value_data,
                        'required' => $option['required'],
                        'default' => $option['default'],
                        'master_option'  => $option['master_option'],
                        'master_option_value' => $option['master_option_value'],
                        'minimum' => $option['minimum'],
                        'maximum' => $option['maximum']
                    );
                } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                    $option_data[] = array(
                        'product_option_id' => $option['product_option_id'],
                        'option_id' => $option['option_id'],
                        'name' => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
                        'type' => $option['type'],
                        'option_value' => $option['option_value'],
                        'required' => $option['required'],
                        'image'=>false,
                        'master_option'     => $option['master_option'],
                        'master_option_value' => $option['master_option_value'],
                        'minimum' => $option['minimum'],
                        'maximum' => $option['maximum']
                    );
                }
            }
            if ($product_info['mobile_app_image']) {
                $image = $this->model_tool_image->resize($product_info['mobile_app_image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
            } else {
                $image = false;
            }
            $bunled_data = $this->model_catalog_product->getProductBundledItems($product_id);
            
            $bundled_items = array();
            $specialdealstatus = false;
            if (!empty($bunled_data)) {
                foreach ($bunled_data as $item) {
                    if($item['special_price']=='1'){
                        $specialdealstatus = true;
                    }
                    $bundled_produts = array();
                    foreach ($item['products'] as $bundled_product) { 
                      //  print_r($item['products']);                        exit();
                        $bundled_prod = $this->model_catalog_product->getProduct($bundled_product['product_id']);
                        $bundled_prod['name'] = strip_tags(html_entity_decode($bundled_prod['name'])); 
                        $bundled_prod['extra_price'] = $bundled_product['extra_price'];
                        if (isset($bundled_product['optionvalues'])) {
                            // $bundled_prod['options'] = 1;

                           // $bundled_options = $this->model_catalog_product->getBundledProductOptions($item['bundle_id'], $bundled_product['product_id']);

                            if (!empty($bundled_product['optionvalues'])) { 
                                foreach ($this->model_catalog_product->getProductOptions($bundled_product['product_id']) as $option) {
                                    if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                                        $option_value_data = array();

                                        foreach ($option['option_value'] as $option_value) {
                                            if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                                if (in_array($option_value['product_option_value_id'], $bundled_product['optionvalues'])) {
                                                    $option_value_data[] = array(
                                                        'product_option_value_id' => $option_value['product_option_value_id'],
                                                        'option_value_id' => $option_value['option_value_id'],
                                                        'name' => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
                                                        'shortcode'=> isset($option_value['shortcode'])?strip_tags(html_entity_decode($option_value['shortcode'], ENT_QUOTES, 'UTF-8')):'',
                                                        'image' => false,
                                                        'price' => (float) $option_value['price'] ? $this->currency->format($option_value['price']) : false,
                                                        'price_prefix' => '',
                                                        'price_value' => ($option_value['price_prefix']=='-' ? -1 : 1 ) *  (float) $option_value['price'] ? $this->currency->format($option_value['price'],'','',false) : 0,
                                                        'master_option_value'=>$option_value['master_option_value']
                                                    );
                                                }
                                            }
                                        }
                                        //$option_value['price_prefix']
                                        if (!empty($option_value_data)) {
                                            $bundled_prod['option'][] = array(
                                                'product_option_id' => $option['product_option_id'],
                                                'product_id' => $bundled_product['product_id'],
                                                'option_id' => $option['option_id'],
                                                'name' => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
                                                'type' => $option['type'],
                                                'option_value' => $option_value_data,
                                                'required' => $option['required'],
                                                'default' => $option['default'],
                                                'image' => false,
                                                'master_option'     => $option['master_option'],
                                                'master_option_value' => $option['master_option_value'],
                                                'minimum' => $option['minimum'],
                                                'maximum' => $option['maximum']
                                            );
                                        }
                                    }
                                }
                            }
                        } else {
                            $bundled_prod['option'] = null;
                        }
                        $bundled_prod['toppings']=  $bundled_prod['product_toppings'];
                        $bundled_prod['dressings'] =  $bundled_prod['product_dressings'];
                        if($bundled_prod['dealcondition']=='H') {
                            $bundled_prod['price'] = (float)($bundled_prod['price']/2) + $bundled_prod['extra_price'];
                        } else {
                            $bundled_prod['price'] =$bundled_prod['extra_price'];
                        }
                        $bundled_prod['price'] = (float) $bundled_prod['price'] ? $this->currency->format($bundled_prod['price']) : false;
                        $bundled_prod['price_value'] = (float) $bundled_prod['price'] ? $this->currency->format($bundled_prod['price'],'','',false) : 0;
                        if ($product_info['mobile_app_image']) {
                            $bundle_image = $this->model_tool_image->resize($bundled_prod['mobile_app_image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
                        } else {
                            $bundle_image = false;
                        }
                        $bundled_prod['image'] = $bundle_image;
                        $bundled_prod['description']=strip_tags(html_entity_decode($bundled_prod['description'], ENT_QUOTES, 'UTF-8'));
                        $bundled_prod['free_options']= unserialize($bundled_prod['free_options']);
                        $bundled_prod['option_groups']= unserialize($bundled_prod['option_groups']);

                        $bundled_prod['shortcode'] =  isset($bundled_prod['shortcode'])?strip_tags(html_entity_decode($bundled_prod['shortcode'], ENT_QUOTES, 'UTF-8')):'';
                        $bundled_prod['shipping_prices'] = unserialize($bundled_prod['shipping_prices']);
                        $bundled_prod['shipping_methodes'] = unserialize($bundled_prod['shipping_methodes']);
                        $bundled_prod['payment_methodes'] = unserialize($bundled_prod['payment_methodes']);
                        $bundled_produts[] = $bundled_prod;
                    } 

                    $bundled_items[] = array('bundled_id' => $item['bundle_id'],
                        'bundled_qty' => $item['bundle_quantity'],
                        'bundle_name' => $item['bundle_name'],
                        //'special_price' => $item['special_price'],
                        //'extraprice' => $item['extraprice'],
                        //'ismainitem' => $item['ismainitem'],
                        'products' => $bundled_produts
                    );
                }
              
            }
            $json = array( 
                'product_id' => $product_info['product_id'],
                'name' => strip_tags(html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')),
                'model' => $product_info['model'],
                'option' => $option_data,
                'price' => (float) $product_info['price'] ? $this->currency->format($product_info['price']) : false,
                'price_value' => (float) $product_info['price'] ? $this->currency->format($product_info['price'],'','',false) : 0,
                'image' => $image,
                'isspecialdeal'=>$specialdealstatus,
                'bundled_items' => $bundled_items,
                'special_deal' => (!empty($bundled_items)) ? 1 : 0,
                'description'=>strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')),
                'toppings'=>  $product_info['product_toppings'],
                'dressings'=>  $product_info['product_dressings'],
                'epos_bgcolor'=>$product_info['epos_bgcolor'],
                'epos_fgcolor'=>$product_info['epos_fgcolor'], 
                'free_options'=>unserialize($product_info['free_options']),
                'option_groups'=>unserialize($product_info['option_groups']),
                'sort_order'=>$product_info['sort_order'],
                'shortcode'=>  isset($product_info['shortcode'])?strip_tags(html_entity_decode($product_info['shortcode'], ENT_QUOTES, 'UTF-8')):'',
                'shipping_prices'=>unserialize($product_info['shipping_prices']),
                'shipping_methodes'=>unserialize($product_info['shipping_methodes']),
                'payment_methodes'=>unserialize($product_info['payment_methodes']),
                'dealsuggestion'=>$product_info['dealsuggestion'],
                'dealcondition'=>$product_info['dealcondition'],
                'tax_class_id'=>$product_info['tax_class_id'],
                'free_toppings'=>$product_info['free_toppings'],
                'max_toppings'=>$product_info['maximum_toppings'],
                'max_dressings'=>$product_info['maximum_dressings'],
                'vegetarian'=>$product_info['vegetarian'],
                'hot'=>$product_info['hot_count']
            );
            //print_r($json);
       }
        if (isset($this->request->post['page'])) {
           return  json_encode($json);
        }
       $this->response->setOutput($this->jsonp(json_encode($json)));
       
    }
    
    public function getProducts() {
       // $this->request->post = array();
        if (isset($this->request->post['page'])) {
            $page = $this->request->post['page'];

        }
        else {
            $page = 0;
            $this->request->post['page'] = $page;
        }

        //pulk: code change for adding records per page feature
        if (isset($this->request->post['limit'])) {
            $limit = $this->request->post['limit'];
        }
        else {
            $limit = 10;
            $this->request->post['limit'] = $limit;
        }

        $this->load->model('catalog/product');
        $proIDS = $this->model_catalog_product->getAllProductIDs();

        //pulk: code change for adding records per page feature
        $proIDS_pages = array_chunk($proIDS, $limit, true);
        //$proIDS_pages = array_chunk($proIDS, 10, true);

        if(isset($proIDS_pages[$page])) {
            $currentpage = $proIDS_pages[$page];
            $json = array();
            foreach ($currentpage as $poductid) {
                $this->request->post['product_id'] = $poductid['product_id'];

                $json[$poductid['product_id']] = $this->getProduct();
            }
            $this->response->setOutput($this->jsonp($json,true));
        }
        else {
           // exit();
            $this->response->setOutput($this->jsonp(array(),true));

        }
       // $this->response->setOutput($this->jsonp($json,true));
    }

     /*
     * params @ customer_id [optional]
     * return @ json = array
     */
    public function getOffers() {
        $json = array();
        $this->load->model('tool/image');
        if($this->config->get('config_app_offer_img')){
            if ($this->config->get('config_app_offer_img') && file_exists(DIR_IMAGE . $this->config->get('config_app_offer_img')) && is_file(DIR_IMAGE . $this->config->get('config_app_offer_img'))) {
                //$json[] = $this->model_tool_image->resize($this->config->get('config_app_offer_img'), $this->config->get('config_app_image_width'), $this->config->get('config_app_image_height'));
                $json['home'] =  array('title'=>$this->config->get('config_title'),
                    'src'=>$this->model_tool_image->resize($this->config->get('config_app_offer_img'), $this->config->get('config_app_image_width'), $this->config->get('config_app_image_height')),
                    'text' => '',
                'date_start'=>'',
                'date_end'=>'',
                'position' => 'top');
            }
        }
        $this->load->model('checkout/offers');
        $offers = $this->model_checkout_offers->getOfferImages('mobile_app_image',$this->config->get('config_app_image_width'),$this->config->get('config_app_image_height'));
        $json['offers'] = array();
        foreach($offers as $offer) {
            $json['offers'][] = array (
                'title' => $offer['title'],
                'src' => $offer['image'],
                'text' => $offer['text'],
                'date_start'=>$offer['date_start'],
                'date_end'=>$offer['date_end'],
                'position' => 'botton'
            );

        }
        //$json[] = '<img title="saving with this app" alt="saving with this app" src="'. HTTPS_SERVER .'image/data/offers.png">';
        $this->response->setOutput($this->jsonp($json,true));
    }
     /*
     * params @ customer_id [optional]
     * params @ name [optional]
     * params @ email [optional]
     * params @ feedback
     * params @ stars [0-5]
     * return @ json = array
     */

    public function submitFeedback() {
        $results = array();
        
        if (isset($this->request->post['customer_id'])) {
            $this->request->post['customer_id']  =  $this->_decodeCustmerID($this->request->post['customer_id'] );
            $this->customer->loginID($this->request->post['customer_id']);
            if(!isset($this->request->post['name'])){
                $this->request->post['name'] = $this->customer->getFirstName() .' ' . $this->customer->getLastName();
            }
            if(!isset($this->request->post['email'])){
                 $this->request->post['email'] = $this->customer->getEmail();
            }
        }
        if(!isset($this->request->post['feedback'])) {
            $results['error']['feedback'] = 'error';
        }
        if(!isset($this->request->post['name'])){
            $results['error']['name'] = 'error';
        }
        if(!isset($this->request->post['email'])){
            $results['error']['email'] = 'error';
        }
        if(!isset($this->request->post['stars'])){
            $results['error']['stars'] = 'error';
        }
        if(empty($results['error'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "feedback SET name = '" . $this->db->escape($this->request->post['name']) . "',feedback = '" . $this->db->escape( $this->request->post['feedback']) . "', email = '" . $this->db->escape( $this->request->post['email']) . "'");
        }
        $this->response->setOutput($this->jsonp(json_encode($results)));
    }

     public function searchpostcode() {
       // $this->load->model('checkout/postcodes');
        //$this->log->write(print_r($this->request->post,true));
        if(!isset($this->request->post['postcode']) || strlen($this->request->post['postcode'])<4) {
            $json['error'] = 'Invalid Postcode';
        } else {
            $postcode = $this->postcode->postcodeformat($this->request->post['postcode']);
            $postcode_details = $this->postcode->getPostcodeDetails($postcode);
            if (empty($postcode_details)) {
                $json['error'] = 'Invalid Postcode';
            }
        }
        if (!isset($json['error'])) {
             $json['postcode'] = $postcode_details;
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    /*
     * return @ array('topping_id','name','sort_order','topping_values'=>array([0]=>array('topping_value_id','name','price','sort_order')))
     * 
     */
    public function gettoppings(){
        $this->load->model('catalog/product');
        $toppings = $this->model_catalog_product->getToppings(false);
       // print_r($toppings);
        $this->response->setOutput($this->jsonp($toppings,true));
        
    }
    /*
     ** return @ array('dressing_id','name','sortorder','dressing_values'=>array([0]=>array('dressing_value_id','name','sort_order'))) 
     */
    public function getdressings(){
        $this->load->model('catalog/product');
        $dressings = $this->model_catalog_product->getDressings(false);
        $this->response->setOutput($this->jsonp($dressings,true));
    }
    
    
    /*
    **  return @Output home page banner 
    */
    public function getHomeBanner() {
        // $this->response->addHeader('Access-Control-Allow-Origin: *');

        // $this->response->setCompression(9);
        // $this->response->setOutput($this->config->get('config_home_page_banner'));
        
    //     $json = array();
    //    $json['banner'] = htmlspecialchars(
    //        $this->config->get('config_home_page_banner')
    //     );
        /*
        $json = array();
        $json['position'] = 'top';
        $json['banner'] = $this->config->get('config_home_page_banner');
        $this->response->setOutput($this->jsonp($json)); 
        */
        $json_response = '';
        if($this->config->get('config_home_notification_popup_content')) {
            $json_response.= '<div style="padding:8px; background: #fff;">';
            if($this->config->get('config_home_notification_popup_heading')) {
                $json_response.= '<h3>'.$this->config->get('config_home_notification_popup_heading').'</h3>';
    }
            $json_response.= htmlspecialchars_decode($this->config->get('config_home_notification_popup_content'));
            $json_response.='</div>';
        } else {
            $json_response = '';
        }
        $this->response->setOutput($this->jsonp($json_response, true));
    
    }
    
    /*
     ** 
     */
    public function getAppVersion(){
        $json = array();
        $detect = new Mobile_Detect;
        $versions = array();
        $update_url = '';
        $upstatus =false;
        if( $detect->isiOS() ){
            $versions = explode(",",$this->config->get('config_iphone_version'));
            $update_url = $this->config->get('config_iphone_appurl');
            $upstatus = (trim($this->config->get('config_iphone_version'))=='')?false:true;
        }
        elseif( $detect->isAndroidOS() ){
          $versions = explode(",",$this->config->get('config_android_version'));
            $update_url = $this->config->get('config_android_appurl');
            $upstatus = (trim($this->config->get('config_android_version'))=='')?false:true;
        }
        elseif(stripos($this->request->server['HTTP_USER_AGENT'], 'Macintosh')!==false) { // Ipad
            $this->log->write('iPad');
            $versions = explode(",",$this->config->get('config_iphone_version'));
            $update_url = $this->config->get('config_iphone_appurl');
            $upstatus = (trim($this->config->get('config_iphone_version'))=='')?false:true;
        }
        else{
            $this->log->write('No os');
        }
        
        if(!$upstatus || in_array($this->request->post['version'],$versions) || empty($update_url)){
             $json['force_update'] = false;
        }else{
        // app needs updation
          $json['force_update'] = true;
          $json['update_url'] = $update_url;
        }

        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
   public function getStores(){
        $json = array();
        $this->load->model('setting/store');
        $this->data['store_info'] = array();
        $storeids[] = 0;
        $storeid_results = $this->model_setting_store->getStores();
        foreach ($storeid_results as $storeid_res) {
            if($storeid_res['store_id']!= $this->config->get('pos_to_storeid')){
                $storeids[] = $storeid_res['store_id'];
            }
        }
        $this->load->model('setting/setting');
        foreach ($storeids as $id) {
             $config = $this->model_setting_setting->getSetting('config',$id);
             if($id==0) {
                 $config['config_url'] = HTTPS_SERVER;
             }
             $json['store_info'][$id] = array(
                 'config_url' => $config['config_url'],
                 'config_logo' => $config['config_logo'],
                 'config_title' => $config['config_title'],
                 'config_name' => $config['config_name'],
                 'config_address' => $config['config_address'],
                 'config_postalcode' => $config['config_postalcode'],
                 'config_telephone' => $config['config_telephone'],
                 'map_image' => $config['config_url'] .'/image/mobile/map_'.$id.'.png',
             );

        }

        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    // TABLE Reservations
    public function getTRtimes() {
        $timestamp = strtotime($this->request->post['date']);
        $today = strtolower(date('l', $timestamp));
        $current_time = strtotime('now');
      
        $today_times = $today . '_store_times';
      
        $today_open_times = $this->config->get($today_times);
        $requested_times = array();
        $store_delivery_time = $this->config->get('config_collect_storetime');
        foreach ($today_open_times as $key => $today_time) {
            if (!empty($today_time['start'])) {
                $start_time = explode(':', $today_time['start']);
                $end_time = explode(':', $today_time['end']);

                $first_time = mktime((int) $start_time[0], (int) $start_time[1], 0, date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp)) + ( $store_delivery_time * 60);
                if ($timestamp > $first_time) {
                    $first_time = $timestamp - ($timestamp % (900 )) + 900;
                }

                if ($start_time[0] > $end_time[0]) {
                    $second_time = mktime((int) $end_time[0], (int) $end_time[1], 0, date("m", $timestamp), date("d", $timestamp) + 1, date("Y", $timestamp)) - ( $store_delivery_time * 60);
                } else {
                    $second_time = mktime((int) $end_time[0], (int) $end_time[1], 0, date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp)) - ( $store_delivery_time * 60);
                }

                if ($current_time < $second_time) { //&& 
                    if ($current_time > $first_time) {
                        $current_time += ( $store_delivery_time * 60);
                        $timeintchange = ($current_time % (900 ));

                        if ($timeintchange < 300)  // 300 = 5 minits
                            $current_time = $current_time - $timeintchange;
                        else
                            $current_time = $current_time - $timeintchange + 900;
                        $first_time = $current_time;
                    }
                    //if ($preparation_time == 0) {
                     //   $requested_times[$first_time - 900] = $this->language->get('text_asap_time');
                  //  }
                    for ($i = $first_time; $i <= $second_time; $i = $i + 900) {

                        $requested_times[$i] = date("h:i A (l)", $i);
                    }
                }
            }
        }
        $this->response->setOutput($this->jsonp(json_encode($requested_times)));
      
    }
    public function tablereservation() {
        $this->language->load('information/tablereservation');
        $this->load->model('catalog/tablereservation');
        $json = array();
        if (!isset($this->request->post['reservation_time'])) {
            $json['error']['reservation_time']  = $this->language->get('error_reservation_time');
	    } else if($this->request->post['reservation_time'] < strtotime('now')) {
            $json['error']['reservation_time'] = $this->language->get('error_time');
	}
        if (!isset($this->request->post['persons'])) {
            $json['error']['persons'] = $this->language->get('error_persons');
	}
        if (!isset($this->request->post['name'])) {
            $json['error']['name'] = $this->language->get('error_name');
	    } else if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
            $json['error']['name'] = $this->language->get('error_name');
	}
        if (!isset($this->request->post['phone'])) {
            $json['error']['phone'] = $this->language->get('error_phone');
	    } else {
            $this->request->post['phone'] = preg_replace('/\D+/', '', $this->request->post['phone']);
            if ((utf8_strlen($this->request->post['phone']) < 10) || (utf8_strlen($this->request->post['phone']) > 11)) {
                $json['error']['phone'] = $this->language->get('error_phone');
	}
        }
        if (!isset($this->request->post['email'])) {
            $json['error']['email'] = $this->language->get('error_email');
	    } else if (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $json['error']['email'] = $this->language->get('error_email');
	}
        if (!$json) {
            $this->model_catalog_tablereservation->addReservation($this->request->post);
            //$this->redirect($this->url->link('information/tablereservation/success'));
            $json['success'] = 1 ;
            $json['success_message'] = $this->language->get('text_message') ;
        }
        else {
            $json['success'] = 0 ;
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    //post order_id customer_id comment
    //return success
     public function sendnotification(){    
        /*
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }                
        */        
        if ($this->request->post['order_id']  && $this->request->post['order_id']!='') {
            $note_id = $this->request->post['customer_id'].''.substr(time(),-6);
            $this->load->model('epos/epos_history');        
            $comment = (isset($this->request->post['table'])?$this->request->post['table']:'');
            $this->model_epos_epos_history->addnote(array('note_id'=>$note_id,'note'=>$comment."\n\r".$this->request->post['comment'],'order_id'=>$this->request->post['order_id']));             
        }        
        $this->response->setOutput($this->jsonp(json_encode(array('success'=>'Notification Successfully sent'))));
        
    }
    //post order_id customer_id dinein_table_id new_table_id
    public function changetable(){
        if ($this->request->post['order_id']  && $this->request->post['order_id']!='' && $this->customer->isLogged()) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET dinein_table_id='".(int)$this->request->post['new_table_id']."'  WHERE order_id=".(int)$this->request->post['order_id'] ."");
        }
        $this->response->setOutput($this->jsonp(json_encode(array('success'=>'Table Successfully Changed'))));
    }
    public function deleteaccount() {
        $this->language->load('account/deleteaccount');
        $this->request->post['customer_id'] = $this->_decodeCustmerID($this->request->post['customer_id']);
        if (isset($this->request->post['customer_id']) && isset($this->request->post['agree'])) {
            $this->load->model('account/deleteaccount');
            $this->model_account_deleteaccount->deleteCustomer($this->request->post['customer_id']);
            $json['success'] = $this->language->get('text_success');
        }
        else {
            $json['text_your_details'] = $this->language->get('text_your_details');
            $json['text_your_agree'] = $this->language->get('text_your_agree');
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    private function _encodeCustmerID($Customer_id){
        $sitehexcode = $this->_siteHexCode();
       return $Customer_id.$sitehexcode;
    }
    private function _decodeCustmerID($mobileCustomerToken){
        $sitehexcode = $this->_siteHexCode();
        if(strpos($mobileCustomerToken, $sitehexcode)) {
              $Customer_id =  str_replace($sitehexcode, '', $mobileCustomerToken);
          return $Customer_id;
        }
        return false; 
    }
    private function _siteHexCode() {
        // 
        return strval(preg_replace('/[^a-zA-Z0-9]/', '',base64_encode(parse_url(HTTPS_SERVER, PHP_URL_HOST)))); 
    }
}
