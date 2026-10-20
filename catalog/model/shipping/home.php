<?php

class ModelShippingHome extends Model {

    function getQuote($address, $store_id = NULL) {
        $this->load->language('shipping/home');
        $del_title = $this->language->get('text_title');
       // $this->load->model('checkout/postcodes');
        $this->load->model('setting/setting');

        $home_settings = $this->model_setting_setting->getSetting('home', $store_id);
        $store_settings = $this->model_setting_setting->getSetting('config', $store_id);

        if ($store_id == NULL) {
            $conf_store_postal_code = $this->config->get('config_postalcode');
            $conf_home_geo_zone_id = $this->config->get('home_geo_zone_id');
            $conf_home_sort_order = $this->config->get('home_sort_order');
            $conf_home_other_prizes_include = $this->config->get('home_other_prizes_include');
        } else {
            $conf_store_postal_code = $store_settings['config_postalcode'];
            $conf_home_geo_zone_id = $home_settings['home_geo_zone_id'];
            $conf_home_sort_order = $home_settings['home_sort_order'];
            $conf_home_other_prizes_include = $home_settings['home_other_prizes_include'];
        }
        unset($this->session->data['avilableshippingtime']);
        if(!isset($address['postcode']) || $address['postcode']==''){
            $method_data = array(
                'code' => '',
                'title' => $del_title,
                'quote' => '',
                'sort_order' => $conf_home_sort_order,
                'error' => false,
                'delivery_msg_status' => $this->language->get('no_delivery')
            );
            return $method_data;
        }
        //$this->log->write(print_r(array('store_id'=>$store_id.'sssssaaaaaa'), true));
        //Forced Delivery
         if(isset($this->session->data['forced_delivery'])){
            $quote_data = array();

            $quote_data['home'] = array(
                'code' => 'home.home',
                'title' => $this->language->get('text_description'),
                'cost' => $this->session->data['forced_delivery'],
                'tax_class_id' => 0,
                'text' => $this->currency->format($this->session->data['forced_delivery'])
            );

            $method_data = array(
                'code' => 'home',
                'title' => $del_title,
                'quote' => $quote_data,
                'sort_order' => $conf_home_sort_order,
                'error' => false,
                'delivery_msg_status' => ''
            );
            return $method_data;
        }
        //Check Category Products are available for shipping
        $status_chk =FALSE;
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $categories = $this->postcode->getcategories();
        if($this->cart->hasProducts()){
            $message='No '.$del_title.' to ';
            foreach ($this->cart->getProducts() as $product) {
                //Check Category Shippings
                if (!empty($product['shipping_methodes']) and !in_array('home', $product['shipping_methodes'])) {//
                    $status_chk = true;
                    $message .=$product['name'] . ',';
                }
                if(!$status_chk){//check category
                    $product_categories = $this->model_catalog_product->getProductCategories($product['product_id']);
                    $category_id = 0;
                    foreach ($product_categories as $prd_category_id) {
                         if(in_array($prd_category_id, $categories)){
                            $category_id =$prd_category_id;
                             break;
                        }
                    }
                    $cat_data = $this->model_catalog_category->getCategory($category_id); 
                    $cat_shipp_data = unserialize($cat_data['shipping_methodes']);
                    $cat_shipping = !empty($cat_shipp_data)?$cat_shipp_data:array();
                    if (!empty($cat_shipping) and !in_array('home', $cat_shipping)) {//
                        $status_chk = true;
                        $message .=$cat_data['name'] . ',';
                    }
                }
            }
                        
        }
        if($status_chk){
            $method_data = array(
                'code' => '',
                'title' => $del_title,
                'quote' => '',
                'sort_order' => $conf_home_sort_order,
                'error' => false,
                'delivery_msg_status' => $message
            );
             return $method_data;
        }
        //
        //payemnt or delivery 
        /*
        $status_chk =FALSE;
        if($this->cart->hasProducts()){
            
            $message='No home Delivery to the  products ';
            foreach ($this->cart->getProducts() as $product){      
                if(!empty($product['shipping_methodes']) and !in_array('home.home', $product['shipping_methodes'])){//
                    $status_chk=true;
                    $message .=$product['name'].',';
                } 
            }
            if($status_chk){
               $method_data = array(
                'code' => '',
                'title' => '',
                'quote' => '',
                'sort_order' => $conf_home_sort_order,
                'error' => false,
                'delivery_msg_status' =>  $message 
            );
            return $method_data;
            }
        }
        */
        //
        
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $conf_home_geo_zone_id . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");
        //$status = true;  
        $msg_status = FALSE;
        if (!$conf_home_geo_zone_id) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }
        $addres_postcode = $this->postcode->postcodeformat($address['postcode']);

        $store_postcode = $this->postcode->postcodeformat($conf_store_postal_code);
        $postcode_chk = $this->postcode->getPostcodeDetails($addres_postcode);
        // print_r($postcode_chk);
        //New
        $distance = $this->postcode->getDeliveryDistance($store_postcode, $addres_postcode);
        if ($conf_home_other_prizes_include) {
            $cart_amount = $this->cart->getSubTotal();
        } else {
            // $cart_amount = $this->cart->getSubTotal();
            $cart_amount = $this->cart->getSubTotal() + $this->getCarttotal();
        }

        if (!$this->cart->hasProducts()) {
            $cart_amount = 9999; //if(min cart amount)less than this will fail this logic
        }
        if (!empty($postcode_chk)) {

            //$delivery_postcode_options = $this->config->get('home_delivery_postcode_options');
            if ($store_id == NULL) {
                $delivery_postcode_options = $this->config->get('home_delivery_postcode_options');
            } else {
                $delivery_postcode_options = $home_settings['home_delivery_postcode_options'];
            }
            if (!empty($delivery_postcode_options)) {
                //print_r($delivery_postcode_options);
                $delivery_status = FALSE;
                foreach ($delivery_postcode_options as $postcode_option) {
                    $shipping_postcodes = explode("\n", $postcode_option['postcode']);
                    // print_r($shipping_postcodes);
                    //die();
                    foreach ($shipping_postcodes as $ship_postcode) {
                        //$ship_postcode = str_replace(' ', '', trim($ship_postcode));
                        //  echo "-";
                        //echo substr(str_replace(' ','',$addres_postcode), 0, strlen($ship_postcode));
                        //echo "-";
                        //echo "</br>";
                        if ($ship_postcode == substr($addres_postcode, 0, strlen($ship_postcode))) {
                            //  echo "mm";
                            if ($postcode_option['extra_shippingamount'] == '') {
                                //  $status = false;
                                if ($cart_amount >= $postcode_option['cartamount']) {

                                    if ((float) $postcode_option['shippingamount'] == 0) {
                                        // echo "111";
                                        $msg_status = TRUE;
                                        $msg = $this->language->get('text_free_delivery') . ' ' . $this->currency->format($postcode_option['cartamount']);
                                    }
                                } else {
                                    //echo "22";
                                    $msg_status = TRUE;
                                    $msg = $this->language->get('text_min_cart_amount') . ' ' . $this->currency->format($postcode_option['cartamount']);
                                }
                            } else {
                                if ((float) $postcode_option['shippingamount'] == 0) {
                                    //echo "111";
                                    $msg_status = TRUE;
                                    $msg = $this->language->get('text_free_delivery') . ' ' . $this->currency->format($postcode_option['cartamount']);
                                }
                            }
                             if($postcode_option['postcode_distance']==''){
                            if ($cart_amount >= $postcode_option['cartamount']) {
                                //echo "1";
                                if ($postcode_option['shippingamount'] == '') {

                                    break;
                                } else {

                                    $charges = (float) ($postcode_option['shippingamount']);
                                    $delivery_status = true;
                                    break 2;
                                }
                            } else {
                                //echo "2";
                                if ($postcode_option['extra_shippingamount'] == '') {
                                    //$delivery_status = false;
                                    break;
                                } else {
                                    $charges = (float) ($postcode_option['extra_shippingamount']);
                                    $delivery_status = true;
                                    break 2;
                                }
                            }
                         }else{
                                 if ($cart_amount >= $postcode_option['cartamount']) {
                                //echo "1";
                                 if ($postcode_option['shippingamount'] == '') {

                                    break;
                                } else {
                                 if($distance <= (float) $postcode_option['postcode_distance']){
                                      $charges = (float) ($postcode_option['shippingamount']);
                                 }else{
                                   $charges = (float) ($postcode_option['shippingamount']) + ($distance - (float) $postcode_option['postcode_distance'])*(float) ($postcode_option['extramile_charge']);
                                   
                                 }
                                    $delivery_status = true;
                                    break 2;
                                }
                            } else {
                                //echo "2";
                                if ($postcode_option['extra_shippingamount'] == '') {
                                    //$delivery_status = false;
                                    break;
                                } else {
                                     if($distance <= (float) $postcode_option['postcode_distance']){
                                      $charges = (float) ($postcode_option['shippingamount'])+(float) ($postcode_option['extra_shippingamount']);
                                     }else{
                                    $charges = (float) ($postcode_option['shippingamount'])+(float) ($postcode_option['extra_shippingamount']) + ($distance - (float) $postcode_option['postcode_distance'])*(float) ($postcode_option['extramile_charge']);
                                   
                                    }
                                   // $charges = (float) ($postcode_option['extra_shippingamount']);
                                    $delivery_status = true;
                                    break 2;
                                }
                            }
                            }
                            
                            
                            
                    }
                }
                 }

                if ($delivery_status) {
                    //   echo "ddd";
                    $quote_data = array();

                    $quote_data['home'] = array(
                        'code' => 'home.home',
                        'title' => $this->language->get('text_description'),
                        'cost' => $charges,
                        'tax_class_id' => 0,
                        'text' => $this->currency->format($charges)
                    );

                    $method_data = array(
                        'code' => 'home',
                        'title' => $del_title,
                        'quote' => $quote_data,
                        'sort_order' => $conf_home_sort_order,
                        'error' => false,
                        'delivery_msg_status' =>''// (($msg_status) ? $msg : '')
                    );
                    return $method_data;
                } else {
                    // echo "jji";
                    $method_data = array(
                        'code' => '',
                        'title' => $del_title,
                        'quote' => '',
                        'sort_order' => $conf_home_sort_order,
                        'error' => false,
                        'delivery_msg_status' => (($msg_status) ? $msg : '')
                    );
                }
                // die();
            }
        }
        //New
        if (!empty($postcode_chk)) {
            if ($store_id == NULL) {
                $conf_home_delivery_area = $this->config->get('home_delivery_area');
                $config_specific_postcode_area = $this->config->get('config_specific_postcode_area');
            } else {
                $conf_home_delivery_area = $home_settings['home_delivery_area'];
                $config_specific_postcode_area = $home_settings['config_specific_postcode_area'];
            }
            switch ($conf_home_delivery_area) {
                case 1:if ($this->postcode->getPostcodeFirstPart($store_postcode) != $this->postcode->getPostcodeFirstPart($addres_postcode)) {
                        $status = false;
                        $msg_status = true;
                        $msg = $this->language->get('no_delivery');
                    }
                    break;
                case 2: if ($this->postcode->getPostcodeFirsttwo($store_postcode) != $this->postcode->getPostcodeFirsttwo($addres_postcode)) {
                        $status = false;
                        $msg_status = true;
                        $msg = $this->language->get('no_delivery');
                    }
                    break;
                case 3: $postcodes = explode("\n", $config_specific_postcode_area);
                    //print_r($postcodes);
                    //echo $this->config->get('config_specific_postcode_area');
                    foreach ($postcodes as $postcode) {
                        $postcode = trim($postcode);
                        //echo $postcode."-";
                        //echo $address['postcode'];
                        //  echo $this->model_checkout_postcodes->getPostcodeFirstPart($address['postcode']);
                        if ($postcode == substr($addres_postcode, 0, strlen($postcode))) {
                            $status = true;
                            //echo $postcode;
                            break;
                        } else {
                            $status = false;
                            $msg_status = true;
                            $msg = $this->language->get('no_delivery');
                        }
                    }
                    break;
            }
            // echo $this->config->get('config_postalcode')."</br>";
            //echo $address['postcode']."</br>";

            $distance = $this->postcode->getDeliveryDistance($store_postcode, $addres_postcode);
//            $cart_amount = $this->cart->getSubTotal();
//            if (!$this->cart->hasProducts()) {
//                $cart_amount = 9999; //if(min cart amount)less than this will fail this logic
//            }
        } else {
            $status = false;
        }
        //echo $distance."</br>";
        //echo $cart_amount;
        if ($status) {
            $chargeset = false;
            if ($store_id == NULL) {
                $conf_home_delivery_options = $this->config->get('home_delivery_options');
            } else {
                $conf_home_delivery_options = $home_settings['home_delivery_options'];
            }
            //print_r($this->config->get('home_delivery_options'));
            // if(!empty($this->config->get('home_delivery_options')))
            //echo $distance; 
            foreach ($conf_home_delivery_options as $delivery_option) {
                //echo $delivery_option['distance'];
                if ($distance <= (float) $delivery_option['distance']) {
                    if ($delivery_option['extra_shippingamount'] == '') {
                        //  $status = false;
                        if ($cart_amount >= $delivery_option['cartamount']) {

                            if ((float) $delivery_option['shippingamount'] == 0) {
                                // echo "111";   
                                $msg_status = TRUE;
                                $msg = $this->language->get('text_free_delivery') . ' ' . $this->currency->format($delivery_option['cartamount']);
                            }
                        } else {
                            //echo "22";   
                            $msg_status = TRUE;
                            $msg = $this->language->get('text_min_cart_amount') . '  ' . $this->currency->format($delivery_option['cartamount']);
                             break;
                            
                        }
                    } else {
                        if ((float) $delivery_option['shippingamount'] == 0) {
                            //echo "111";   
                            $msg_status = TRUE;
                            $msg = $this->language->get('text_free_delivery') . ' ' . $this->currency->format($delivery_option['cartamount']);
                        }
                    }
                    if ($cart_amount >= $delivery_option['cartamount']) {
                        if ($delivery_option['shippingamount'] == '') {
                            $status = false;
                            //break;
                        } else {
                            $charges = (float) ($delivery_option['shippingamount']);
                            $chargeset = true;
                            $status = true;
                            break;
                        }
                    } else {
                        if ($delivery_option['extra_shippingamount'] == '') {
                            $status = false;
                            //break;
                        } else {
                            $charges = (float) ($delivery_option['extra_shippingamount']);
                            $chargeset = true;
                            $status = true;
                            break;
                        }
                    }
                } else {
                    $msg_status = true;
                    $msg = $this->language->get('no_delivery');
                }
            }
            //if
            //echo $delivery_option['distance'];
            if (!empty($delivery_option)) {
                if ((float) $delivery_option['distance'] && !$chargeset) {
                    if ($store_id == NULL) {
                        $conf_home_extra_shipping_status = $this->config->get('home_extra_shipping_status');
                        $conf_home_extra_mile_max_distance = $this->config->get('home_extra_mile_max_distance');
                        $conf_home_extra_shipping_rate = $this->config->get('home_extra_shipping_rate');
                    } else {
                        $conf_home_extra_shipping_status = $home_settings['home_extra_shipping_status'];
                        $conf_home_extra_mile_max_distance = $home_settings['home_extra_mile_max_distance'];
                        $conf_home_extra_shipping_rate = $home_settings['home_extra_shipping_rate'];
                    }
                    //$status = false;
                    if ($conf_home_extra_shipping_status == 1 && $distance >= (float) $delivery_option['distance']) {
                        $additional_distance = $distance - (float) $delivery_option['distance'];
                        if ($conf_home_extra_mile_max_distance !== '') {
                            if ($distance < $conf_home_extra_mile_max_distance) {
                                if ($cart_amount >= $delivery_option['cartamount']) {
                                    if ($delivery_option['shippingamount'] == '') {
                                        $status = false;
                                    } else {
                                        $charges = (float) ($delivery_option['shippingamount']) + (float) $additional_distance * (float) $conf_home_extra_shipping_rate;
                                    }
                                } else {

                                    $msg_status = TRUE;
                                    $msg = $this->language->get('text_min_cart_amount') . ' ' . $this->currency->format($delivery_option['cartamount']);

                                    if ($delivery_option['extra_shippingamount'] == '') {
                                        $status = false;
                                    } else {
                                        $charges = (float) ($delivery_option['extra_shippingamount']) + (float) $additional_distance * (float) $conf_home_extra_shipping_rate;
                                    }
                                }
                            } else {
                                $status = false;
                                $msg_status = true;
                                $msg = $this->language->get('no_delivery');
                            }
                        } else { 
                            if ($cart_amount >= $delivery_option['cartamount']) {
                                if ($delivery_option['shippingamount'] == '') {
                                    $status = false;
                                } else {
                                    $charges = (float) ($delivery_option['shippingamount']) + $additional_distance * (float) ($delivery_option['shippingamount']);
                                }
                            } else {
                                $msg_status = TRUE;
                                $msg = $this->language->get('text_min_cart_amount') . ' ' . $this->currency->format($delivery_option['cartamount']);

                                if ($delivery_option['extra_shippingamount'] == '') {
                                    $status = false;
                                } else {
                                    $charges = (float) ($delivery_option['extra_shippingamount']) + $additional_distance * (float) ($delivery_option['extra_shippingamount']);
                                }
                            }
                        }
                    } else {
                        $status = false;
                    }
                } else {
                    if (!$chargeset) {
                        if ($cart_amount >= $delivery_option['cartamount']) {
                            $charges = (float) ($delivery_option['shippingamount']);
                        } else {
                            $charges = (float) ($delivery_option['extra_shippingamount']);
                        }
                    }
                }
            } else {
                $status = FALSE;
            }
        }

        $method_data = array();
        
        if ($status) {
            $quote_data = array();

            $quote_data['home'] = array(
                'code' => 'home.home',
                'title' => $this->language->get('text_description'),
                'cost' => $charges,
                'tax_class_id' => 0,
                'text' => $this->currency->format($charges)
            );

            $method_data = array(
                'code' => 'home',
                'title' => $del_title,
                'quote' => $quote_data,
                'sort_order' => $conf_home_sort_order,
                'error' => false,
                'delivery_msg_status' =>''// (($msg_status) ? $msg : '')
            );
        } else {
            if( !isset($msg) || !$msg) {
                $msg_status = true;
                $msg = $this->language->get('no_delivery');
            }
            $method_data = array(
                'code' => '',
                'title' => $del_title,
                'quote' => '',
                'sort_order' => $conf_home_sort_order,
                'error' => false,
                'delivery_msg_status' => (($msg_status) ? $msg : '')
            );
        }

        return $method_data;
    }

    public function getCarttotal() {
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
        //print_r($total_data);
        $offer_total = 0;

        foreach ($total_data as $key => $value) {
            if (in_array($value['code'], array('offers', 'coupon')))
                $offer_total +=$value['value'];
        }

        return $offer_total;
        //print_r($total_data);
    }

}

?>