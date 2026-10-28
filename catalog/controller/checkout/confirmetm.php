<?php

class ControllerCheckoutConfirmetm extends Controller { 

    public function index() {
        $redirect = '';
        if ($this->cart->hasShipping()) {
            // Validate if shipping address has been set.		
            $this->load->model('account/address');

            if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
                $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
            } elseif (isset($this->session->data['guest'])) {
                $shipping_address = $this->session->data['guest']['shipping'];
            }
            if (empty($shipping_address)) {
                $redirect = $this->url->link('checkout/checkout', '', 'SSL');
            }

            // Validate if shipping method has been set.	
            if (!isset($this->session->data['shipping_method'])) {
               
                $redirect = $this->url->link('checkout/checkout', '', 'SSL');
            }
        } else {
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
        }

        // Validate if payment address has been set.
        $this->load->model('account/address');

        if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } elseif (isset($this->session->data['guest'])) {
            $payment_address = $this->session->data['guest']['payment'];
        }

       
        if (empty($payment_address)) {
            
            $redirect = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate if payment method has been set.	
        if (!isset($this->session->data['payment_method'])) {
           
            $redirect = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.	
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $redirect = $this->url->link('checkout/cart');
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
                $redirect = $this->url->link('checkout/cart');

                break;
            }
        }

        if (!$redirect) {

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

                    $etm_t = microtime(true); $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes); $GLOBALS['etm_perf'][] = array('confirmetm total/' . $result['code'], microtime(true) - $etm_t); // timing for ?etm_perf=1
                }
            }

            $sort_order = array();

            foreach ($total_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $total_data);

            $this->language->load('checkout/checkout');

            $data = array();

            $data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
            $data['store_id'] = $this->config->get('config_store_id');
            $data['store_name'] = $this->config->get('config_name');

            if ($data['store_id']) {
                $data['store_url'] = $this->config->get('config_url');
            } else {
                $data['store_url'] = HTTP_SERVER;
            }

            if ($this->customer->isLogged()) {
                $data['customer_id'] = $this->customer->getId();
                $data['customer_group_id'] = $this->customer->getCustomerGroupId();
                $data['firstname'] = $this->customer->getFirstName();
                $data['lastname'] = $this->customer->getLastName();
                $data['email'] = $this->customer->getEmail();
                $data['telephone'] = $this->customer->getTelephone();
                $data['fax'] = $this->customer->getFax();

                $this->load->model('account/address');

                $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
            } elseif (isset($this->session->data['guest'])) {
                $data['customer_id'] = 0;
                $data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
                $data['firstname'] = $this->session->data['guest']['firstname'];
                $data['lastname'] = $this->session->data['guest']['lastname'];
                $data['email'] = $this->session->data['guest']['email'];
                $data['telephone'] = $this->session->data['guest']['telephone'];
                $data['fax'] = $this->session->data['guest']['fax'];

                $payment_address = $this->session->data['guest']['payment'];
            }

            $data['payment_firstname'] = $payment_address['firstname'];
            $data['payment_lastname'] = $payment_address['lastname'];
            $data['payment_company'] = $payment_address['company'];
            $data['payment_company_id'] = $payment_address['company_id'];
            $data['payment_tax_id'] = $payment_address['tax_id'];
            $data['payment_address_1'] = $payment_address['address_1'];
            $data['payment_address_2'] = $payment_address['address_2'];
            $data['payment_city'] = $payment_address['city'];
            $data['payment_postcode'] = $payment_address['postcode'];
            $data['payment_zone'] = $payment_address['zone'];
            $data['payment_zone_id'] = $payment_address['zone_id'];
            $data['payment_country'] = $payment_address['country'];
            $data['payment_country_id'] = $payment_address['country_id'];
            $data['payment_address_format'] = $payment_address['address_format'];

            if (isset($this->session->data['payment_method']['title'])) {
                $data['payment_method'] = $this->session->data['payment_method']['title'];
            } else {
                $data['payment_method'] = '';
            }

            if (isset($this->session->data['payment_method']['code'])) {
                $data['payment_code'] = $this->session->data['payment_method']['code'];
            } else {
                $data['payment_code'] = '';
            }

            if ($this->cart->hasShipping()) {
                if ($this->customer->isLogged()) {
                    $this->load->model('account/address');

                    $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
                } elseif (isset($this->session->data['guest'])) {
                    $shipping_address = $this->session->data['guest']['shipping'];
                }

                $data['shipping_firstname'] = $shipping_address['firstname'];
                $data['shipping_lastname'] = $shipping_address['lastname'];
                $data['shipping_company'] = $shipping_address['company'];
                $data['shipping_address_1'] = $shipping_address['address_1'];
                $data['shipping_address_2'] = $shipping_address['address_2'];
                $data['shipping_city'] = $shipping_address['city'];
                $data['shipping_postcode'] = $shipping_address['postcode'];
                $data['shipping_zone'] = $shipping_address['zone'];
                $data['shipping_zone_id'] = $shipping_address['zone_id'];
                $data['shipping_country'] = $shipping_address['country'];
                $data['shipping_country_id'] = $shipping_address['country_id'];
                $data['shipping_address_format'] = $shipping_address['address_format'];

                if (isset($this->session->data['shipping_method']['title'])) {
                    $data['shipping_method'] = $this->session->data['shipping_method']['title'];
                } else {
                    $data['shipping_method'] = '';
                }

                if (isset($this->session->data['shipping_method']['code'])) {
                    $data['shipping_code'] = $this->session->data['shipping_method']['code'];
                } else {
                    $data['shipping_code'] = '';
                }
            } else {
                $data['shipping_firstname'] = '';
                $data['shipping_lastname'] = '';
                $data['shipping_company'] = '';
                $data['shipping_address_1'] = '';
                $data['shipping_address_2'] = '';
                $data['shipping_city'] = '';
                $data['shipping_postcode'] = '';
                $data['shipping_zone'] = '';
                $data['shipping_zone_id'] = '';
                $data['shipping_country'] = '';
                $data['shipping_country_id'] = '';
                $data['shipping_address_format'] = '';
                $data['shipping_method'] = '';
                $data['shipping_code'] = '';
            }

            $product_data = array();

            // Reward points
            //echo $total;die();
            /*
            $this->load->model('setting/setting');
            $total_rewads = 0;
            $rewards = $this->model_setting_setting->getSetting('rewardpoints');
            $consider_product=false;
            if (!empty($rewards)) {
                if (isset($rewards[5]['points']) && $rewards[5]['status'] == 1) {
                   if($rewards[5]['consider_product_reward']==1){
                        $consider_product =true;
                    }
                    if((float)$total >=(float)$rewards[5]['amount']){
                    $total_rewads = floatval($rewards[5]['points']);
                    //chk product reward consider
                    
                   }
                    
                } elseif (isset($rewards[0]['points']) && $rewards[0]['status'] == 1) {
                    $total_rewads = floor($total) * floatval($rewards[0]['points']);
                } else {
                    $total_rewads = 0;
                }
            }
            */

            //New Reward points
            $total_rewads = 0;
            $consider_product=false;
            $total_cal = $total;
            if($this->config->get('reward_points_status')){
                
                $no_offers = true;
                if(!$this->config->get('config_reward_points_work_offers')){
                    foreach($total_data as $total_datacode){
                        if($total_datacode['code'] == 'offers'){
                            $no_offers = false;
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
                    if( $min_order_amount < $total_cal) {
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
                                 if($min_order_amount && $this->config->get('config_repeat_reward') && $total_cal > $min_order_amount*2 ) {
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
                                        if($multiples[0] < $total_cal) {
                                            if($multiples[0] > $min_order_amount) {
                                                $total_rewads = $multiples[1];
                                                $min_order_amount = $multiples[0];
                                            }
                                        } else {
                                            $repeat = false;
                                        }
                                    }
                                    if($min_order_amount && $repeat && $total_cal > $min_order_amount*2 ) {
                                        $total_rewads = $total_rewads * (int)($total_cal/$min_order_amount);
                                    }
                                }
                            }

                            if($this->config->get('config_reward_points_consider_product')) {
                                $consider_product=true;
                            }

                            //echo "reward point : ".$total_rewads."<br>";
                            //add reward point for store
                            $addreward = $this->config->get('config_reward_points_store_fixed');
                            if($addreward[$data['store_id']]) {
                                $total_rewads+=$addreward[$data['store_id']];
                            } else {
                                $addreward = $this->config->get('config_reward_points_store_xtimes');
                                if($addreward[$data['store_id']]) {
                                    $total_rewads*=$addreward[$data['store_id']];
                                }
                            }
                            //echo "store point : ".$total_rewads."<br>";
                            //add reward point for shipping method
                            $addreward = $this->config->get('config_reward_points_shipping_methode_fixed');
                            $sel_shipping = explode('.',$data['shipping_code']);
                            if($addreward[$sel_shipping[0]]) {
                                $total_rewads+=$addreward[$sel_shipping[0]];
                            } else {
                                $addreward = $this->config->get('config_reward_points_shipping_methode_xtimes');
                                if($addreward[$sel_shipping[0]]) {
                                    $total_rewads*=$addreward[$sel_shipping[0]];
                                }
                            }
                            //echo "shipping point : ".$total_rewads."<br>";

                            //add reward point for payment method
                            $addreward = $this->config->get('config_reward_points_payment_methode_fixed');
                            if($addreward[$data['payment_code']]) {
                                $total_rewads+=$addreward[$data['payment_code']];
                            } else {
                                $addreward = $this->config->get('config_reward_points_payment_methode_xtimes');
                                if($addreward[$data['payment_code']]) {
                                    $total_rewads*=$addreward[$data['payment_code']];
                                }
                            }
                            
                            //echo "payment point : ".$total_rewads."<br>";
                        }
                    }

                }
            }

            


            foreach ($this->cart->getcartProducts() as $cart_key => $product) {
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
                        'type' => $option['type'],
                        'group'=> $option['group']
                    );
                }
                  $productbundle=array();
                  /*
                 foreach ($product['bundled_data'] as $bundled_product) {
                $bundle_options = array();
                foreach ($bundled_product['option'] as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['option_value'];
                    } else {
                        $value = $this->encryption->decrypt($option['option_value']);
                    }

                    $bundle_options[] = array(
                        'product_option_id' => $option['product_option_id'],
                        'product_option_value_id' => $option['product_option_value_id'],
                        'option_id' => $option['option_id'],
                        'option_value_id' => $option['option_value_id'],
                        'name' => $option['name'],
                        'value' => $value,
                        'shortcode'=>$option['shortcode'],
                        'type' => $option['type']
                    );
                }
                
                //$chk_default_options = $this->model_catalog_product->getDefaultOptions($bundled_product['product_id']);
                $productbundle[] = array(
                    'main_product_id'=>$bundled_product['main_product_id'],
                    'product_id' => $bundled_product['product_id'],
                    'bundled_id' => $bundled_product['bundled_id'],
                    'name' => $bundled_product['name'],
                    'shortcode'=>$bundled_product['shortcode'],
                    'model' => $bundled_product['model'],
                    'option' => $bundle_options,
                    'quantity' => $bundled_product['quantity'],
                   'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                   // 'default' => (!empty($chk_default_options) ? 1 : 0)
                );
            }
            */
                $product_data[] = array(
                    'product_id' => $product['product_id'],
                    'cart_key' => $cart_key,
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'download' => $product['download'],
                    'quantity' => $product['quantity'],
                    'subtract' => $product['subtract'],
                    'price' => (isset($products[$cart_key]) && isset($products[$cart_key]['price']))?$products[$cart_key]['price']:$product['price'],
                    'total' => (isset($products[$cart_key]) && isset($products[$cart_key]['total']))?$products[$cart_key]['total']:$product['total'],
                    'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                    // 'reward'     => $product['reward']
                    // Reward points
                    'reward' => (($consider_product)?($total_rewads+$product['reward']):$total_rewads),
                    'bundle'=> $product['bundled_data'],
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
            $data['order_requested_time'] = isset($this->session->data['order_requested_time'])?$this->session->data['order_requested_time']: time();
            $data['total'] = $total;

            if (isset($this->request->cookie['tracking'])) {
                $this->load->model('affiliate/affiliate');

                $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);

                if ($affiliate_info) {
                    $data['affiliate_id'] = $affiliate_info['affiliate_id'];
                    $data['commission'] = ($total / 100) * $affiliate_info['commission'];
                } else {
                    $data['affiliate_id'] = 0;
                    $data['commission'] = 0;
                }
            } else {
                $data['affiliate_id'] = 0;
                $data['commission'] = 0;
            }

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

            if (isset($this->request->server['HTTP_USER_AGENT'])) {
                $data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
            } else {
                $data['user_agent'] = '';
            }

            if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
            } else {
                $data['accept_language'] = '';
            }
            $this->load->model('checkout/order');
            
            if(isset($this->session->data['order_id'])){
                
                $pre_order_details =  $this->model_checkout_order->getOrder($this->session->data['order_id']);
                if($pre_order_details)
                {
                    if($pre_order_details['order_status_id'] == 0)
                    {
                        $data['order_id']  =  $this->session->data['order_id'];
                        $this->model_checkout_order->addCheckoutHistory($pre_order_details);   
                    }
                    else{
                        unset($data['order_id']);
                    }
                }
            }
            if(isset($this->session->data['tableordering']) && isset($this->session->data['dine_table_id'])){		
                  $data['dinein_table_id'] = (int)$this->session->data['dine_table_id'];
                  if(isset($this->session->data['people_count'])){
                      $data['peoples'] = (int)$this->session->data['people_count'];
                  }
            }
            
            //CHECK DELIVERY
            if($data['shipping_code']=='home.home' && $data['shipping_postcode']!=''){
                $this->log->write(print_r(array("DEL QUOTE FINAL CHECK"),true));
                //$data['shipping_postcode']='NN6 9LB';
                $shipping_address = array('firstname' => '',
                    'lastname' => '',
                    'company' => '',
                    'company_id' => '',
                    'tax_id' => '',
                    'address_1' => '',
                    'address_2' => '',
                    'postcode' => $data['shipping_postcode'],
                    'city' => '',
                    'zone_id' => $this->config->get('config_zone_id'),
                    'zone' => '',
                    'zone_code' => '',
                    'country_id' => $this->config->get('config_country_id'),
                    'country' => '',
                    'iso_code_2' => '',
                    'iso_code_3' => '',
                    'address_format' => '');
                $this->load->language('shipping/home');
                $this->load->model('shipping/home');
                 $quote = $this->model_shipping_home->getQuote($shipping_address);
                 if (empty($quote) || (isset($quote['delivery_msg_status'])) && $quote['delivery_msg_status']!='') {
                    $this->data['redirect'] = $this->url->link('checkout/checkoutetm', '', 'SSL');;
                 }
                $this->log->write(print_r($quote ,true));
            }
            
            
            
            $this->session->data['order_id'] = $this->model_checkout_order->addOrder($data);
            if($this->session->data['order_id'] && empty($data['order_id']))
            {
                $idempotency_key = $this->session->data['order_id'].md5($this->session->data['order_id']);
                $this->session->data['idempotency_key'] =  substr($idempotency_key, 0, 15);
            }
            
            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_model'] = $this->language->get('column_model');
            $this->data['column_quantity'] = $this->language->get('column_quantity');
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_total'] = $this->language->get('column_total');

            $this->data['products'] = array();

            foreach ($this->cart->getProducts() as $product) {
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
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                    );
                }

                $this->data['products'][] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'quantity' => $product['quantity'],
                    'subtract' => $product['subtract'],
                    'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
                    'total' => $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax'))),
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'])
                );
            }

            // Gift Voucher
            $this->data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $this->data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount'])
                    );
                }
            }

            $this->data['totals'] = $total_data;
            $this->data['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);
           
        } else {
            $this->data['redirect'] = $redirect;
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/confirmetm.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/confirmetm.tpl';
        } else {
            $this->template = 'default/template/checkout/confirmetm.tpl';
        }

        $this->response->setOutput($this->render());
    }

    public function confirmbutton() {
        $this->language->load('checkout/checkout');

        
        if ($this->config->get('config_checkout_id')&& $this->config->get('config_account_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
            $information_info2 =  $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

            if ($information_info && $information_info2) {
                $this->data['text_agree'] = sprintf($this->language->get('text_agree'), $information_info['title'],$information_info2['title']);
                //$this->data['text_mobile_checkout_agree'] = sprintf($this->language->get('text_mobile_checkout_agree'), $this->url->link('information/information', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
            } else {
                $this->data['text_agree'] = '';
                //$this->data['text_mobile_checkout_agree']='';
            }
        } else {
            $this->data['text_agree'] = '';
           // $this->data['text_mobile_checkout_agree']='';
        }

        if (isset($this->session->data['agree'])) {
            $this->data['agree'] = $this->session->data['agree'];
        } else {
            $this->data['agree'] = '';
        }
        $title = '';
        if(isset($this->session->data['shipping_method']['title'])) {
            $title = $this->session->data['shipping_method']['title'];
        }
        // print_r($this->session->data);
        $this->data['button_continue'] = sprintf($this->language->get('text_confirmbutton'),$title);
        // Check shipping available
        if(!$this->cart->hasProducts() && (isset($this->session->data['vouchers']) && !empty($this->session->data['vouchers']))) {
            $this->data['shipping_method']= true;
        } else if(!isset($this->session->data['shipping_method'])) {
            $this->data['shipping_method']= false;
        }
        else {
            $this->data['shipping_method']= true;
        }

        //bipin
        $this->load->model('checkout/order');
        $logged_customer_id = '';
        
        if($this->customer->isLogged()){
            $logged_customer_id = $this->customer->getId();
            /* $pendingOrdersChecking = $this->model_checkout_order->getPendingOrders($logged_customer_id);

            if($pendingOrdersChecking && sizeof($pendingOrdersChecking)> 0)
            {

                foreach ($pendingOrdersChecking as $pending_order) {
                    $pending_order['order_id']; 
                    $order_info = $this->model_checkout_order->getOrder($pending_order['order_id']);
                    $this->getChild('payment/'.$order_info['payment_code'].'/'.$order_info['payment_code'],['order_id' => $pending_order['order_id'],'request'=>'stripe_response_pending']);
                }
            }  
             * 
             */ 
            $pendingOrdersChecking = $this->model_checkout_order->getPendingOrders($logged_customer_id);
            if($pendingOrdersChecking && sizeof($pendingOrdersChecking)> 0)
            {
                $interval = true;
            }
        }
        if(isset($interval) && $interval == true){
            $active_orders = ['status'=>'false', 'msg' => "Your previous transaction is under progress! Please try after some time."];
        }
        else{
            $active_orders = $this->checkActiveOrders();
        }
        if($active_orders)
        {
            $this->data['payment_hold'] = $active_orders;
        }
        //bipin
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/confirmbutton.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/confirmbutton.tpl';
        } else {
            $this->template = 'default/template/checkout/confirmbutton.tpl';
        }

        $this->response->setOutput($this->render());
    }

    public function validate() {
        $this->language->load('checkout/checkout');
        $this->log->write(print_r(array("CONFIRMETM VALIDATE"),true));
        $this->log->write(print_r(array($this->session->data),true));
        $this->log->write(print_r(array($this->request->post),true));
        $this->log->write(print_r(array($this->request->get),true));
        $json = array();

        if ($this->config->get('config_checkout_id')) { // config_account_id
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

            if ($information_info && !isset($this->request->post['agree'])) {
                $json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
            }
        }

        $this->response->setOutput(json_encode($json));
    }
    public function finalcart() {
       
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

                    $etm_t = microtime(true); $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes); $GLOBALS['etm_perf'][] = array('confirmetm total/' . $result['code'], microtime(true) - $etm_t); // timing for ?etm_perf=1
                }
            }

            $sort_order = array();

            foreach ($total_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $total_data);

            $this->language->load('checkout/checkout');

            
            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_model'] = $this->language->get('column_model');
            $this->data['column_quantity'] = $this->language->get('column_quantity');
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_total'] = $this->language->get('column_total');

            $this->data['products'] = array();

            foreach ($this->cart->getProducts() as $product) {
                $option_data = array();
                $product_times = array();
                if($product['specific_time']==1){
                    $today_times = $this->postcode->getTodayStoretime();
                    if($today_times && !empty($today_times)){
                        $today = strtolower(date('l',$today_times['start']));
                    }else{
                        $today = strtolower(date('l'));
                    }
                    if(!empty($product['available_times']) && isset($product['available_times'][$today])){
                        $product_times = $product['available_times'][$today];
                    }
                }
                //print_r($product['available_times']);
                foreach ($product['option'] as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['option_value'];
                    } else {
                        $filename = $this->encryption->decrypt($option['option_value']);

                        $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                    }

                    $option_data[] = array(
                        'name' => $option['name'],
                        'value' => html_entity_decode($value)
                    );
                }
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
                        'value' => html_entity_decode($value),
                        'type' => $option['type']
                    );
                }
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($bundled_product['price'], $bundled_product['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = false;
                }

                // Display prices
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $total = $this->currency->format($this->tax->calculate($bundled_product['price'], $bundled_product['tax_class_id'], $this->config->get('config_tax')) * $bundled_product['quantity']);
                } else {
                    $total = false;
                }
                //$chk_default_options = $this->model_catalog_product->getDefaultOptions($bundled_product['product_id']);
                $productbundle[] = array(
                    'product_id' => $bundled_product['product_id'],
                   // 'thumb' => $image,
                    'name' => $bundled_product['name'],
                    'model' => $bundled_product['model'],
                    'option' => $bundle_options,
                    'quantity' => $bundled_product['quantity'],
                    'price' => $price,
                    'total' => $total,
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    'toppings'=>$bundled_product['toppings'],
                    'dressings'=>$bundled_product['dressings'],
                        // 'default' => (!empty($chk_default_options) ? 1 : 0)
                );
            }

                $this->data['products'][] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'quantity' => $product['quantity'],
                    'subtract' => $product['subtract'],
                    'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
                    'total' => $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax'))),
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    'bundle'=>$productbundle,
                    'available_times'=> $product_times,
                    'toppings'=>$product['toppings'],
                    'dressings'=>$product['dressings'], 
                );
            }

            // Gift Voucher
            $this->data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $this->data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount'])
                    );
                }
            }

            $this->data['totals'] = $total_data;
            $this->data['finalpricetext'] = '';
            foreach($total_data as $total){
                if($total['code']=='total') {
                    $this->data['finalpricetext'] = $total['text'];
                    break;
                }
            }
            $title = '';
        if(isset($this->session->data['shipping_method']['title'])) {
            $title = $this->session->data['shipping_method']['title'];
        }

        if ($this->config->get('tips_status') && !$this->config->get('tips_eposonly')) {
            $this->data['tips_status'] = 1;
            if($this->config->get('tips_percentage_online')) {
                $tips_percentages = explode(',', $this->config->get('tips_percentage_online'));
                foreach($tips_percentages as $tips_percentage) {
                    $tips_value = round ($this->cart->getSubTotal() * floatval($tips_percentage)/100,2);
                    $this->data['tips_percentages'][] = array('percentage'=>$tips_percentage,'value'=>$tips_value,'text'=>$this->currency->format($tips_value));
                }
            }
            $this->data['tips_value'] = isset($this->session->data['tips'])?$this->session->data['tips'] :0;
        }
        else {
             $this->data['tips_status'] = 0;
        }
        // print_r($this->session->data);
        $this->data['button_continue'] = sprintf($this->language->get('text_confirmbutton'),$title);
        
        if(isset($this->session->data['payment_method']['code']) && $this->session->data['payment_method']['code']!='cod') {
            $this->data['button_continue'] .= $this->language->get('text_and_pay');
        } 

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/finalcartetm.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/finalcartetm.tpl';
        } else {
            $this->template = 'default/template/checkout/finalcartetm.tpl';
        }

        $this->response->setOutput($this->render());
    }

    public function tips() {
        $json = array();
        if (isset($this->request->post['tips'])) {
            if(floatval($this->request->post['tips']) >0) {
                $this->session->data['tips'] = floatval($this->request->post['tips']);
            }
            else {
                unset($this->session->data['tips']);
            }
            $json['success'] ='ok';
        }
        $this->response->setOutput(json_encode($json));
    }

    //bipin
    public function checkActiveOrders()
    {
        $this->load->model('checkout/order');
        $user_id = $this->customer->getId();
        // $status_update = $this->getChild('checkout/success/orderCheckCron');
        $pending_orders =  $this->model_checkout_order->getOrdersOfCustomer($user_id);
        if($pending_orders->num_rows <= 0 ||  (int)$this->config->get('config_hold_checkout') === 0)
        {
            return  ['status' => 'true'];
        }
        else{
            $msg = "Another order has already been placed";
            if($pending_orders->row)
            {
                $order_id = $pending_orders->row['order_id'];
                $time = strtotime($pending_orders->row['date_added']);
                $order_placed_time =  (round(abs($time - time()) / 60,0)-1). " minute";
                if($order_placed_time < 1)
                {
                    $order_placed_time = 1;
                }
                $msg .= " with order id #".$order_id." before ".$order_placed_time.'.';
            }
            else{
                $msg .='.';
            }
            $interval = 15;
            if($this->config->get('config_hold_checkout_time') > 0)
            {
                $interval = $this->config->get('config_hold_checkout_time');
            }
            $telephone = $this->config->get('config_telephone');
            return ['status'=>'false', 'msg' =>$msg." If you need to amend this order please call us on <a href='tel:".$telephone."'>".$telephone."</a>."];
        }
    }
    //bipin

}

?>