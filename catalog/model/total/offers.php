<?php

class ModelTotalOffers extends Model {

    public function getTotal(&$total_data, &$total, &$taxes) {
        //echo "DEY";  
        $this->load->language('total/offers');
        $this->load->model('checkout/offers');

        $offers = $this->getOffers();

        $discount_given_products = array();
        $added_as_free_products = array();
        $offer_details = array();
        $today = strtolower(date('l'));
        unset($this->session->data['offer_start_time']);
        unset($this->session->data['offer_end_time']);     
        $cart_products = $this->cart->getProducts();
        $discounted_amount = 0;
        foreach ($offers as $offer) {
            //echo "SSS";
            //print_r($cart_products);
            /*
            if($offer['offer_first_orderonly'] && isset($this->session->data['guest'])){ // BLOCK FIRST ORDER DISCOUNTS FOR GUESTS
                continue;
            }
            */
            $applied_devices = unserialize($offer['applied_devices']);
            if(!empty($applied_devices)){
                if(isset($this->session->data['mobileapp'])){
                    if(!in_array('M', $applied_devices)) {
                        continue;
                    }
                }else{
                    if(!in_array('O', $applied_devices)) {
                        continue;
                    }
                }
            }else{
                continue;
            }
            $offer_stores = $this->getOfferToStore($offer['offer_id']);
            if(!in_array($this->config->get('config_store_id'), $offer_stores)){
                continue;
            }
            if ($offer['offer_first_orderonly']) {
                if(!$this->customer->getId()){
                    continue;
                }

                if($offer['basedon_date']) {
                    $date_added_query = ' AND date_added >= "'.$offer['date_start'].' 00:00:01"';
                } else {
                    $date_added_query = '';
                }

                $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND order_status_id NOT IN (0,7,10) ".$date_added_query);

                $order_count = $query->row['total'];

                //$this->log->write($offer['name'].'->'.$order_count.' first order -->'.$offer['offer_first_orderonly']);

                /*
                $this->load->model('account/order');
                $order_count = $this->model_account_order->getTotalOrders();
                */
                $order_count++;
                $order_count_status = false;
                switch($offer['offer_first_orderonly']){
                    case 0://For all Orders
                        $order_count_status = true;
                        break;
                    case 1://For Specific Order Count (ie for 1st/ 2nd / 5th Order 
                        if($order_count == $offer['minimum_order_count']){
                            $order_count_status = true;
                        }
                        break;
                    case 2: // betqeen 2 orders ( between customer 5 -10 Orders)
                        if($order_count >= $offer['minimum_order_count'] && $order_count <= $offer['maximum_order_count']) {
                            $order_count_status = true;
                        }
                        break;
                    case 3 ://For every 5 th order  
                        if(fmod($order_count,$offer['minimum_order_count'])==0){
                            $order_count_status = true;
                        }
                        break;
                }
                if(!$order_count_status){
                    continue;
                }
                /*
                if($offer['offer_first_orderonly']==1) {

                    if($order_count == $offer['minimum_order_count']) {
                        //no issues
                    } else {
                        $this->log->write($order_count.' , min.order '.$offer['minimum_order_count']);
                        continue;
                    }

                } else if($offer['offer_first_orderonly']==2) {

                    if($order_count < $offer['minimum_order_count'] || $order_count > $offer['maximum_order_count']) {
                        //$this->log->write($order_count.' , min.order '.$offer['minimum_order_count'].', max'.$offer['maximum_order_count']);
                        continue;
                    }

                } else if($order_count == $offer['offer_first_orderonly']) {
                    //matched
                } else {
                    //$this->log->write('no match '.$order_count.' , min.order '.$offer['minimum_order_count'].', max'.$offer['maximum_order_count']);
                    continue;
                }
                */
            } 
            $offered_products = array();
            $discounted_products = array();
            $discount_offer_det = array();
            $free_products_info = array();
            $free_products_nos_chk = 0;
            $discount_cart_qtys = array();                             
            $status = FALSE;
            //Delivery or payment offer check
            $offer_shipping = unserialize($offer['applied_shipping']);
            $offer_payments = unserialize($offer['applied_payments']);
            if (!empty($offer_shipping)) {
                // print_r($offer['applied_shipping']);
                //echo "SSS";
                if (isset($this->session->data['shipping_method']['code']) and in_array($this->session->data['shipping_method']['code'], $offer_shipping)) {
                    $status = true;
                } else {
                    // $status = FALSE;
                    continue;
                }
            }
            if (!empty($offer_payments)) {
                // print_r($offer['applied_shipping']);
                if (isset($this->session->data['payment_method']['code']) and in_array($this->session->data['payment_method']['code'], $offer_payments)) {
                    $status = true;
                } else {
                    // $status = FALSE;
                   continue;
                }
            }
            //Delivery or payment offer check
            //echo $offer['offer_id'].'-';
            $multi_status =  true;
            if ($offer['multi_offers'] == 0) {

                foreach ($total_data as $value) {
                    //echo $value['code'];
                    if (isset($value['code']) && $value['code'] == 'offers') {
                        //echo $offer['offer_id'].'-';
                        $multi_status = FALSE;
                        break;
                        //echo "1";die();
                    }
                }
            }
            if(!$multi_status){
                continue;
            }
            $offer_info = $this->model_checkout_offers->getOffer($offer['offer_id']);
            if ($offer_info) {
            //Time Check
            $offer_times = isset($offer_info['offer_times'])? unserialize($offer_info['offer_times']):[];
            $offer_status = $this->checkOffertimes($offer_times);
            if(!$offer_status){
                continue;
            }
            
             //Time Check
            // print_r($offer_info);die();
            
                $discount_total = 0;
                //##cart Subtotal Calculation
                if (!$offer_info['product']) {
                    $sub_total = $this->cart->getSubTotal();
                } else {
                    $sub_total = 0;

                    foreach ($cart_products as $product) {
                        if (in_array($product['product_id'], $offer_info['product'])) {
                            $sub_total += $product['total'];
                        }
                    }
                }
                //##cart Subtotal Calculation
                //New Qty / Amt Checking 
                if ($offer_info['product']) {
                    //$cart_products = $cart_products;// $this->cart->getProducts();
                    $excluded_options = $this->model_checkout_offers->getExcludedoptions($offer_info['offer_id']);
                    foreach ($offer_info['product'] as $offer_det) {
                        foreach ($cart_products as $cart_key => $cart) {

                            $excluded_status = FALSE;
                            foreach ($cart['option'] as $option) {
                                foreach ($excluded_options as $excluded_option) {
                                    if ($excluded_option['option_value_id'] == $option['product_option_value_id']) {
                                        $excluded_status = TRUE;
                                        break 2;
                                    }
                                }
                            }
                            if (!$excluded_status) {
                                if ($cart['product_id'] == $offer_det) {
                                    //if (!in_array($cart['product_id'], $added_as_free_products)) {

                                        $offered_products[$cart_key] = array('id' => $cart['product_id'], 'quantity' => $cart['quantity'], 'price' => $cart['price'], 'total_price' => $cart['total']);
                                    //}
                                }
                            }
                        }
                    }
                    $total_cart_qty = 0;
                    $total_cart_price = 0;
                    foreach ($offered_products as $qty) {
                        $total_cart_qty+=$qty['quantity'];
                    }
                    foreach ($offered_products as $price) {
                        $total_cart_price+=$price['price']*$price['quantity'];
                    }
                } 
                else {
                    $total_cart_qty = $this->cart->countProducts();
                    $total_cart_price = $this->cart->getSubTotal();
                     $total_cart_price  -= $discounted_amount ;
                }
                $quantity_status = false;
                $offer_quantity_ranges = unserialize($offer_info['total_quantity']);
                $offer_discounts = unserialize($offer_info['discount_amount']);
                $offer_free_nos = unserialize($offer_info['product_quantity_free']);
                if (!empty($offer_quantity_ranges)) {

                    foreach ($offer_quantity_ranges as $key => $quantity_range) {
                        if (isset($quantity_range['qty'])) {
                            if ($total_cart_qty >= $quantity_range['qty']) {
                                $quantity_status = true;
                                $discount_applied_qty = $quantity_range['qty'];
                                $offer_discount_amount = (isset($offer_discounts[$key]))?$offer_discounts[$key]:'';//$offer_discounts[$key];
                                $main_index = $key;
                                //break;
                            }
                        } else {
                            if ($total_cart_qty >= $quantity_range['range'][0]) {
                                $quantity_status = true;
                                $discount_applied_qty = $quantity_range['range'][0];
                                $offer_discount_amount = (isset($offer_discounts[$key]))?$offer_discounts[$key]:'';//$offer_discounts[$key];
                                $main_index = $key;
                                //break;
                            }
                        }
                    }
                } else {
                    $quantity_status = true;
                    $discount_applied_qty = 1;
                    $offer_discount_amount = (isset($offer_discounts[0]))?$offer_discounts[0]:'';//$offer_discounts[0];
                    $main_index = 0;
                }
                if ($quantity_status and $total_cart_price >= $offer_info['total_amount']) {
                    $status = true;
                } else {
                    $status = false;
                }
                $free_products_info = $this->getOfferToFreeProduct($offer_info['offer_id']);
                //New Qty / Amt Checking 

                
         
                
         
                //New
                $coupons = array();
                if ($offer_info['offer_to_coupon'] == 1) {
                    $coupons = $this->getOfferCouponcodes($offer['offer_id']);
                    if ((!isset($this->session->data['coupon'])) or !in_array($this->session->data['coupon'], $coupons)) {
                        $status = FALSE;
                    }
                }
                if ($status) {
                    //Free Shipping 
                    /*
                    if ($offer_info['shipping'] && isset($this->session->data['shipping_method'])) {
                        if (!empty($this->session->data['shipping_method']['tax_class_id'])) {
                            $tax_rates = $this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

                            foreach ($tax_rates as $tax_rate) {
                                if ($tax_rate['type'] == 'P') {
                                    $taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
                                }
                            }
                        }

                        $discount_total += $this->session->data['shipping_method']['cost'];
                        
                         if (!empty($offer_times) and isset($offer_times[$today])) {
                            //$this->session->data['offer_start_time']=$offer_times[$today]['start'];
                            //$this->session->data['offer_end_time']=$offer_times[$today]['end']; 
                        }
                        $total_data[] = array(
                            'code' => 'offers',
                            'title' => $offer_info['name'] . '(' . (($offer_info['offer_to_coupon'] == 1 and isset($this->session->data['coupon'])) ? $this->session->data['coupon'] : '') . ')',
                            'text' => $this->currency->format(-$discount_total),
                            'value' => -$discount_total,
                            'sort_order' =>$this->config->get('offers_sort_order'),
                            'offer_sort_order' =>$offer['sort_order'],
                            'offer_name' => $offer_info['name'],
                            'free_product_total_count' => $free_products_nos_chk,
                            'free_products' => $free_products_info,
                            'free_product_auto_add' => $offer['free_product_auto_add'],
                            'ofeer_sale_type' => $offer['offer_type'],
                            'offer_id' => $offer['offer_id'],
                            'cart_amount_nedded' => $offer['total_amount'] 
                        );
                        $total -= $discount_total;
                        $offer_details[] = array(
                            'code' => 'offers',
                            'title' => $offer_info['name'],
                            'text' => $this->currency->format(-$discount_total),
                            'value' => -$discount_total,
                            'sort_order' =>$this->config->get('offers_sort_order'),
                            'offer_sort_order' =>$offer['sort_order'],
                            'offer_name' => $offer_info['name'],
                            'free_product_total_count' => $free_products_nos_chk,
                            'free_products' => $free_products_info,
                            'free_product_auto_add' => $offer['free_product_auto_add'],
                            'ofeer_sale_type' => $offer['offer_type'],
                            'offer_id' => $offer['offer_id'],
                            'iscoupon' => $offer['offer_to_coupon'],
                            'cart_amount_nedded' => $offer['total_amount'],
                            'coupon_code' => $coupons//$offer['coupon_code']  -- chk in offer display and freeproducts add
                        );
                         return $offer_details;
                    }
                    
                    */
                    //Free Shipping END
                    if ((float) $offer_info['total_quantity'] == 0) {
                        $offer_quantity = 1;
                    } else {
                        $offer_quantity = $offer_info['total_quantity'];
                    }
                    if ($offer_info['quantity_product_sale_type'] == 1) {
                        $totaldiscount_nos = (floor($total_cart_qty / $discount_applied_qty)) * $offer_free_nos[$main_index];
                        $free_products_nos_chk = (floor($total_cart_qty / $discount_applied_qty)) * $offer_free_nos[$main_index];                
                    }else{
                        $totaldiscount_nos = $offer_free_nos[$main_index];
                        $free_products_nos_chk = $offer_free_nos[$main_index];
                    }
                    if($total_cart_qty==$discount_applied_qty){
                        $free_products_nos_chk = $offer_free_nos[$main_index];
                    }
                    //echo $free_products_nos_chk;
                    if ($offer_info['offer_type']) {//Free Product Or Buy - Get Type
                        $disc_qtys = 0;
                        $balnc_qty=0;
                        $free_cart_items=array();
                        
                        if(!empty($offer_info['product'])){
                            //sort with product price  DESC
                             //New Code
                            if(!empty($free_products_info)){
                               foreach ($free_products_info as $free_id) {
                                    foreach ($cart_products as $cart_key => $cart) {
                                        if ($cart['product_id'] == $free_id) {
                                            if (!in_array($cart['product_id'], $added_as_free_products) && !isset($offered_products[$cart_key])) {
                                                $offered_products[$cart_key] = array('id' => $cart['product_id'], 'quantity' => $cart['quantity'], 'price' => $cart['price'], 'total_price' => $cart['total']);
                                            }
                                        }
                                    }
                                } 
                            }
                            
                            //New Code
                            $sort_order = array();
                            $exoff= array();
                            foreach ($offered_products as $of_key=>$offered_product) {
                                 $sort_order[] = $offered_product['price'];
                                 $exoff[] = array('key'=>$of_key,'price'=>$offered_product['price']);
                            }
                            array_multisort($sort_order, SORT_DESC, $exoff);
                            $newoff = array();
                            foreach($exoff as $exct){
                                $newoff[$exct['key']] = $offered_products[$exct['key']];
                            }
                            $offered_products = $newoff;
                            
                            
                            $sort_order = array();
                            $excart= array();
                            foreach ($cart_products as $ikey=>$value) {
                                $sort_order[] = $value['price'];
                                $excart[] = array('key'=>$ikey,'price'=>$value['price']);
                            }
                            array_multisort($sort_order, SORT_ASC, $excart);
                            $newcart = array();
                            foreach($excart as $exct){
                                $newcart[$exct['key']] = $cart_products[$exct['key']];
                            }
                            $cart_items = $newcart;
                            
                            $temp_lock = array();  
                            $locked_items = array();  
                            $free_cart_items = array();  
                            $bal_count =0;
                            $disc_count =0;
                                foreach ($offered_products as $ckey=>$cart_item) {
                              /* 
                              if(isset($temp_lock[$ckey])){ 
                                     $cart_item['quantity'] -= $temp_lock[$ckey];
                                     if($cart_item['quantity']==0){
                                         continue;
                                     }
                              }*/
                              if(floor(($bal_count+$cart_item['quantity'])/($discount_applied_qty+$offer_free_nos[$main_index]))>0){

                                  $disc_count = floor(($bal_count+$cart_item['quantity'])/($discount_applied_qty+$offer_free_nos[$main_index]));                                    
                                 //echo "--";
                                  $bal_count = fmod($bal_count+$cart_item['quantity'], $discount_applied_qty+$offer_free_nos[$main_index]);
                                 //echo "-->>";
                                  //echo $cart_item['quantity']-$bal_count;
                                  $temp_lock [$ckey] = $cart_item['quantity']-$bal_count; 
                                  /*
                                  if(isset($cart_products[$ckey])){
                                      if($cart_products[$ckey]['quantity']-$temp_lock [$ckey]<=0){
                                          unset($cart_products[$ckey]);
                                      }else{
                                          //echo $discount_quantity.''.$f_key;
                                          $cart_products[$ckey]['quantity'] -= $temp_lock [$ckey];
                                      }
                                  }
                                  */ 
                                  //echo $ckey.'</br>';
                              }else{
                                  //echo $ckey.'</br>';
                                  $bal_count +=$cart_item['quantity'];
                                  $temp_lock [$ckey] = $cart_item['quantity'];
                              }
                              //echo $bal_count.'--'.$disc_count.'-->>';
                              //print_r($temp_lock);
                              foreach ($cart_items as $cartkey=>$cart_product) {// Least price sorted Free Items
                                 if(isset($free_cart_items[$cartkey])){ 
                                     //print_r($cart_product);
                                     $cart_product['quantity'] -= $free_cart_items[$cartkey];
                                     //echo $cart_product['quantity'].'-'.$temp_lock[$cartkey].'-->';
                                     if($cart_product['quantity']==0){
                                         continue;
                                     }
                                 }
                                 if($disc_count>0){
                                      if(in_array($cart_product['product_id'], $free_products_info)) { 
                                          $applied_qty = ($disc_count>=$cart_product['quantity'])?$cart_product['quantity']:$disc_count;
                                          if(isset($free_cart_items[$cartkey])){
                                              $free_cart_items[$cartkey] += $applied_qty;
                                          }else{
                                              $free_cart_items[$cartkey] = $applied_qty;
                                          }
                                          if(isset($temp_lock[$cartkey])){
                                              $temp_lock[$cartkey] +=$applied_qty;
                                          }else{
                                              $temp_lock[$cartkey] =$applied_qty;
                                          }

                                          $disc_count -=$applied_qty;
                                      }
                                 }
                              }
                          }
                          
                           if ($offer_info['quantity_product_sale_type'] == 1) {
                                //$totaldiscount_nos = (floor($total_cart_qty / $discount_applied_qty)) * $offer_free_nos[$main_index];
                                $free_products_nos_chk = (floor($bal_count+$disc_count / $discount_applied_qty)) * $offer_free_nos[$main_index];                
                            }else{
                                //$totaldiscount_nos = $offer_free_nos[$main_index];
                                $free_products_nos_chk = $offer_free_nos[$main_index];
                            }
                           // echo '<='.$free_products_nos_chk.'=>';
                        }
                        else{
                           
                            if ($offer_info['quantity_product_sale_type'] == 1) {
                                $totaldiscount_nos = (floor($total_cart_qty / $discount_applied_qty)) * $offer_free_nos[$main_index];
                                $free_products_nos_chk = (floor($total_cart_qty / $discount_applied_qty)) * $offer_free_nos[$main_index];                
                            }else{
                                $totaldiscount_nos = $offer_free_nos[$main_index];
                                $free_products_nos_chk = $offer_free_nos[$main_index];
                            }
                            //echo $totaldiscount_nos.'-'.$free_products_nos_chk;
                            //print_r($cart_items);
                            foreach ($cart_products as $cartkey=>$cart_product) {// Least price sorted Free Items
                                   if(isset($free_cart_items[$cartkey])){ 
                                       //print_r($cart_product);
                                       $cart_product['quantity'] -= $free_cart_items[$cartkey];
                                       //echo $cart_product['quantity'].'-'.$temp_lock[$cartkey].'-->';
                                       if($cart_product['quantity']==0){
                                           continue;
                                       }
                                   }
                                   if($totaldiscount_nos>0){
                                    if(in_array($cart_product['product_id'], $free_products_info)) { 
                                        $applied_qty = ($totaldiscount_nos>=$cart_product['quantity'])?$cart_product['quantity']:$totaldiscount_nos;
                                        if(isset($free_cart_items[$cartkey])){
                                            $free_cart_items[$cartkey] += $applied_qty;
                                        }else{
                                            $free_cart_items[$cartkey] = $applied_qty;
                                        }
                                       $totaldiscount_nos -=$applied_qty;
                                    }
                                   }
                                }
                                foreach ($free_cart_items as $f_key=>$qty) {
                                     if(isset($cart_products[$f_key])){
                                          $total_cart_price -= $cart_products[$f_key]['price'];
                                     }                                    
                                 }
                                 if($total_cart_price < $offer_info['total_amount']){
                                     continue;
                                 }
                                 
                        }
                        //print_r($free_cart_items);
                        $offer_discounts = unserialize($offer_info['discount_amount']);
                        $j = 0;
                        foreach ($free_cart_items as $f_key=>$qty) {
                            
                            $product=$cart_products[$f_key];
                            $product['quantity'] = $qty;
                            $cart_option_status = true;
                            $excld_optn_price =0;
                            $free_product_options = $this->getOfferFreeProductDetails($offer['offer_id'], $product['product_id']);
                            $available_options = unserialize($free_product_options['product_options']);
                            foreach ($product['option'] as $cart_product_option) {
                                if (isset($available_options[$cart_product_option['product_option_id']])) {
                                    if (is_array($available_options[$cart_product_option['product_option_id']])) {
                                        if (!in_array($cart_product_option['product_option_value_id'], $available_options[$cart_product_option['product_option_id']])) {
                                            $excld_optn_price +=(float)$cart_product_option['price'];
                                        }
                                    }/* else {
                                        if ($cart_product_option['product_option_value_id'] != $available_options[$cart_product_option['product_option_id']]) {
                                            $excld_optn_price +=(float)$cart_product_option['price'];
                                            $cart_option_status = false;
                                        }
                                    }*/
                                }
                                else{
                                    $excld_optn_price +=(float)$cart_product_option['price'];
                                }
                            }
                            if ($cart_option_status) {
                                //print_r($product) ;
                                    $discount_quantity = $product['quantity'];
                                    switch ($free_product_options['discount type']) {

                                        case 'F'://Fixed Amt
                                            $discount_total +=$free_product_options['price'] * $discount_quantity;
                                            break;
                                        case 'P'://Percentage Disc
                                            $discount_total +=($free_product_options['price'] / 100) * ($product['price']-$excld_optn_price) * $discount_quantity;
                                            break;
                                        case 'R'://Fixed Price
                                            //$this->cart->setDiscount($f_key, ($product['price']-$excld_optn_price- $free_product_options['price']) * $discount_quantity);
                                            $discount_total +=($product['price']-$excld_optn_price - $free_product_options['price']) * $discount_quantity;
                                            break;
                                        case 'N'://Not Individual
                                             switch ($offer_info['discount_type']) {
                                                case 'F'://Fixed Amt
                                                    $discount_total +=  $offer_discount_amount* $discount_quantity;
                                                    break;
                                                case 'P':
                                                    $discount_total +=($offer_discount_amount / 100) * ($product['price']-$excld_optn_price) * $discount_quantity;
                                                    break;
                                                case 'R':
                                                    //$this->cart->setDiscount($f_key, ($product['price']-$excld_optn_price- $offer_discount_amount) * $discount_quantity);
                                                    $discount_total +=($product['price']-$excld_optn_price- $offer_discount_amount) * $discount_quantity;
                                                    break;

                                             }
                                            break; 
                                    }
                                    $discount_given_products[] = $product['product_id'];
                                    $discounted_products[$product['product_id']] = $product;
                                    $discounted_products[$product['product_id']]['given_disc_type'] = $offer_discount_amount . ' % Discount For ' . $product['name'];
                                    $added_as_free_products[] = $product['product_id'];
                                    if ($offer_info['quantity_product_sale_type'] == 0){
                                        $free_products_nos_chk -=$discount_quantity;
                                    }   
                                    //
                                    $j += $discount_quantity;
                                   if(isset($cart_products[$f_key])){
                                        if($cart_products[$f_key]['quantity']-$discount_quantity<=0){
                                            unset($cart_products[$f_key]);
                                        }else{
                                            //echo $discount_quantity.''.$f_key;
                                            $cart_products[$f_key]['quantity'] -=$discount_quantity;
                                        }
                                    }
                                  unset($free_products_info[$f_key]);
                            }
                        }                       
                      //print_r($cart_products);  
                    } 
                    else {//Cash Discount
                        $discounted_products[0] = $offer_info['name'];
                        $discounted_products[0]['given_disc_type'] = '';

                        if (!empty($offered_products)) {//Specific Products
                            if ($offer_info['quantity_product_sale_type'] == 1) {//Proportional
                                //$totaldiscount_nos = (floor($total_cart_qty / $discount_applied_qty));
                                //$free_products_nos_chk = (floor($total_cart_qty / $discount_applied_qty));
                                switch ($offer_info['discount_type']) {
                                    case 'F'://Fixed Amt
                                        $discount_total = $totaldiscount_nos * $offer_discount_amount;
                                        break;
                                    case 'P'://Percentage Discount
                                        $discount_applied_products = array();
                                        foreach ($offered_products as $offer_product) {
                                            if (!in_array($offer_product['id'], $discount_given_products)) {//check for already discounted
                                                if ($totaldiscount_nos <= $offer_product['quantity']) {
                                                    $discount_total += $offer_product['price'] / 100 * $totaldiscount_nos * $offer_discount_amount;
                                                    $discount_applied_products[] = array('product_id' => $offer_product['id'], 'quantity' => $totaldiscount_nos);
                                                    $discount_given_products[] = $offer_product['id'];
                                                    break;
                                                } else {
                                                    $totaldiscount_nos -= $offer_product['quantity'];
                                                    $discount_total += $offer_product['price'] / 100 * $offer_product['quantity'] * $offer_discount_amount;
                                                    $discount_applied_products[] = array('product_id' => $offer_product['id'], 'quantity' => $offer_product['quantity']);
                                                    $discount_given_products[] = $offer_product['id'];
                                                }
                                            }
                                        }
                                        break;
                                    case 'R'://Fixed Price
                                        foreach ($offered_products as $key => $product) {
                                            //$this->cart->setDiscount($key, ($product['price'] - $offer_discount_amount) * $product['quantity']);
                                            $discount_total +=($product['price'] - $offer_discount_amount) * $product['quantity'];
                                        }

                                        break;
                                }
                                //$totaldiscount_nos = floor($total_cart_qty / $offer_quantity);
                            } else {//Fixed
                                //$totaldiscount_nos = (floor($total_cart_qty / $discount_applied_qty));
                                //$free_products_nos_chk = (floor($total_cart_qty / $discount_applied_qty));
                                $totaldiscount_nos = $total_cart_qty;
                                //echo $total_cart_price.'-'.$offer_discount_amount;
                                 switch ($offer_info['discount_type']) {
                                      case 'F'://Fixed Amt
                                        $discount_total +=  $offer_discount_amount;
                                        break;
                                      case 'P':
                                          $discount_total = $total_cart_price / 100 * $offer_discount_amount;  
                                        break;  
                                      case 'R':
                                          $discount_applied_products = array();
                                            foreach ($offered_products as $offer_product) {
                                                if (!in_array($offer_product['id'], $discount_given_products)) {
                                                    //$this->cart->setDiscount($key, ($offer_product['price'] - $offer_discount_amount) * $offer_product['quantity']);
                                                     $discount_total +=($offer_product['price'] - $offer_discount_amount) * $offer_product['quantity'];
                                                }
                                            }    
                                        break;  
                                 }
                            }
                            
                        } else { //All Items in Cart
                            if ($offer_info['quantity_product_sale_type'] == 1) {//PROPORTIONAL
                                $totaldiscount_nos = (floor($total_cart_qty / $discount_applied_qty));
                                $free_products_nos_chk = (floor($total_cart_qty / $discount_applied_qty));
                                switch ($offer_info['discount_type']) {

                                    case 'F'://Fixed Amt
                                        $discount_total += $totaldiscount_nos * $offer_discount_amount;
                                        break;
                                    case 'P'://Percentage Discount
                                        $j = 0;
                                        foreach ($cart_products as $product) {
                                            if ($j <= $totaldiscount_nos) {
                                                $discount_total += $product['price'] / 100 * $offer_discount_amount;
                                                $discounted_products[$product['product_id']]['given_disc_type'] = $offer_discount_amount . ' % of ' . $product['name'] . 'Discount';
                                                $discount_given_products[] = $product['product_id'];
                                                $j++;
                                            }
                                        }
                                        break;
                                    //$discount_total = $this->cart->getSubTotal() / 100 * $offer_info['discount_amount'];
                                    case 'R'://Fixed Price
                                        $m = 0;
                                        foreach ($cart_products as $key => $product) {
                                            if ($m <= $totaldiscount_nos) {
                                                //$this->cart->setDiscount($key, ($product['price'] - $offer_discount_amount) * $product['quantity']);
                                                $discount_total +=($product['price'] - $offer_discount_amount) * $product['quantity'];
                                                $m++;
                                            }
                                        }

                                        break;
                                }
                            } else {//FIXED
                                switch ($offer_info['discount_type']) {
                                    case 'F'://Fixed Amt
                                        $discount_total +=$offer_discount_amount;
                                        break;
                                    case 'P'://Percentage Discount
                                        $discount_total += $total_cart_price / 100 * $offer_discount_amount; // $this->cart->getSubTotal()
                                        break;
                                    case 'R'://Fixed Price
                                        foreach ($cart_products as $key => $product) {
                                            //$this->cart->setDiscount($key, ($product['price'] - $offer_discount_amount) * $product['quantity']);
                                            $discount_total +=($product['price'] - $offer_discount_amount) * $product['quantity'];
                                        }

                                        break;
                                }
                            }
                        }
                    }
                    if ((float) $offer_info['max_discount'] and (float) $discount_total > (float) $offer_info['max_discount']) {
                        $discount_total = (float) $offer_info['max_discount'];
                    }
                }
                if ($offer_info['shipping'] && isset($this->session->data['shipping_method'])) {
                    if (!empty($this->session->data['shipping_method']['tax_class_id'])) {
                        $tax_rates = $this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

                        foreach ($tax_rates as $tax_rate) {
                            if ($tax_rate['type'] == 'P') {
                                $taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
                            }
                        }
                    }

                    $discount_total += $this->session->data['shipping_method']['cost'];
                }
                foreach ($discounted_products as $discounted_product) {
                    $discount_offer_det[] = isset($discounted_product['given_disc_type'])?$discounted_product['given_disc_type']:null;
                    //implode($discounted_product, $discount_applied_products)
                }
                //print_r($offered_products);
                //print_r($added_as_free_products);
//echo $free_products_nos_chk;
                if ($status) {
                    //  
                    if ($discounted_products) {
                        //    echo $offer_info['name'].'</br/>';
                        if (!empty($offer_times) and isset($offer_times[$today])) {
                            //$this->session->data['offer_start_time']=$offer_times[$today]['start'];
                            //$this->session->data['offer_end_time']=$offer_times[$today]['end']; 
                        }
                        $discounted_amount +=$discount_total;
                        $total_data[] = array(
                            'code' => 'offers',
                            'title' => $offer_info['name'] .(($offer_info['offer_to_coupon'] == 1 and isset($this->session->data['coupon'])) ?  '(' . $this->session->data['coupon'] . ')': '') ,
                            'text' => $this->currency->format(-$discount_total),
                            'value' => -$discount_total,
                            'sort_order' =>$this->config->get('offers_sort_order'),
                            'offer_sort_order' =>$offer['sort_order'],
                            'offer_name' => $offer_info['name'],
                            'free_product_total_count' => $free_products_nos_chk,
                            'free_products' => $free_products_info,
                            'free_product_auto_add' => $offer['free_product_auto_add'],
                            'ofeer_sale_type' => $offer['offer_type'],
                            'offer_id' => $offer['offer_id'],
                            'cart_amount_nedded' => $offer['total_amount']
                        );
                        $total -= $discount_total;
                        $offer_details[] = array(
                            'code' => 'offers',
                            'title' => $offer_info['name'],
                            'text' => $this->currency->format(-$discount_total),
                            'value' => -$discount_total,
                            'sort_order' =>$this->config->get('offers_sort_order'),
                            'offer_sort_order' =>$offer['sort_order'],
                            'offer_name' => $offer_info['name'],
                            'free_product_total_count' => $free_products_nos_chk,
                            'free_products' => $free_products_info,
                            'free_product_auto_add' => $offer['free_product_auto_add'],
                            'ofeer_sale_type' => $offer['offer_type'],
                            'offer_id' => $offer['offer_id'],
                            'iscoupon' => $offer['offer_to_coupon'],
                            'cart_amount_nedded' => $offer['total_amount'],
                            'coupon_code' =>$coupons// ''//$offer['coupon_code']  -- chk in offer display and freeproducts add
                        );
                    } else {
                        //  echo $offer_info['name'].'</br/>';
                        $offer_details[] = array(
                            'code' => 'offers',
                            'title' => '',
                            'text' => '',
                            'value' => '',
                            'sort_order' =>$this->config->get('offers_sort_order'),
                            'offer_sort_order' =>$offer['sort_order'],
                            'offer_name' => $offer_info['name'],
                            'free_product_total_count' => $free_products_nos_chk,
                            'free_products' => $free_products_info,
                            'free_product_auto_add' => $offer['free_product_auto_add'],
                            'ofeer_sale_type' => $offer['offer_type'],
                            'offer_id' => $offer['offer_id'],
                            'iscoupon' => $offer['offer_to_coupon'],
                            'cart_amount_nedded' => $offer['total_amount'],
                            'coupon_code' => $coupons//$offer['coupon_code']  -- chk in offer display and freeproducts add
                        );
                    }
                }
            }
            // print_r($total_data);
        }

        // $offer_array = (isset($total_data)?$total_data:array()) + (isset($offer_details)?$offer_details:array());
        return $offer_details;
        //Return Value used  For Offer Popup/Message  
    }

    public function getOffers() {
        $offers = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers WHERE  ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1' ORDER BY sort_order");
        return $offers->rows;
       
    }

    public function getOfferToFreeProduct($offer_id) {
        $query = $this->db->query("SELECT prod_id  FROM " . DB_PREFIX . "offer_to_freeproduct  WHERE offer_id='" . $offer_id . "' AND `discount type`!='N'");
        $data = $query->rows;
        $datas = array();
        foreach ($data as $result) {
            $datas[] = $result['prod_id'];
        }
        return $datas;
    }

    public function showpopupfree() {
        
    }

    public function getOfferFreeProductDetails($offer_id, $product_id) {
        $query = $this->db->query("SELECT DISTINCT *  FROM " . DB_PREFIX . "offer_to_freeproduct  WHERE offer_id='" . $offer_id . "' AND prod_id='" . $product_id . "' ");
        return $query->row;
    }
    public function getOfferToFreeProductOptions($offer_id){
        $product_data= array();
        $query = $this->db->query("SELECT *  FROM " . DB_PREFIX . "offer_to_freeproduct  WHERE offer_id='" . $offer_id . "'");
        if(!empty($query->rows)){
            foreach($query->rows as $data){
                $product_data[$data['prod_id']] = array(
                     'options'=>  unserialize($data['product_options']),
                     'auto_add'=>$data['auto_add'],
                     'discount_type'=>$data['discount type'],
                     'price'=>$data['price']                
                );
            }
        }
        return $product_data;
    }
    public function timeinbetween($H1, $M1, $H2, $M2, $current_time) {
        $first_time = mktime($H1, $M1, 0, date("m"), date("d"), date("Y"));
        $second_time = mktime($H2, $M2, 0, date("m"), date("d"), date("Y"));
        if ($H1 > $H2){
           $second_time = mktime($H2, $M2, 0, date("m"), date("d") + 1, date("Y"));                         
        }
        if ($current_time < $second_time && $current_time > $first_time) {
            return true;
        } else {
            return false;
        }
    }

    public function confirm($order_info, $order_total) {
        //$this->load->model('total/offers');
        $offer_details = array();
        $offer_product_total = 0;
        $taxes = 0;
        $this->getTotal($offer_details, $offer_product_total, $taxes);
        foreach ($offer_details as $offer) {
            if ($offer['code'] != '') {
                $this->load->model('checkout/offers');
                $this->model_checkout_offers->redeem($offer['offer_id'], $order_info['order_id'], $order_info['customer_id'], $offer['value']);
            }
        }
    }

    public function getOfferCouponcodes($offer_id) {
        $query = $this->db->query("SELECT coupon_code  FROM " . DB_PREFIX . "offer_to_coupon  WHERE offer_id='" . $offer_id . "' ");
        $data = $query->rows;
        $datas = array();
        $i = 0;
        foreach ($data as $value) {
            foreach ($value as $pid) {
                $datas[$i] = $pid;
            }
            $i++;
        }
        return $datas;
    }
    public function getOfferToStore($offer_id) {
        $query = $this->db->query("SELECT store_id  FROM " . DB_PREFIX . "offer_to_store  WHERE offer_id='" . $offer_id . "' ");
        $data = $query->rows;
        $datas = array();
        foreach ($data as $result) {
            $datas[] = $result['store_id'];
        } 
        return $datas;
    }
    private function checkOffertimes($offer_times){
       
            if (!empty($offer_times)){
                if(isset($this->session->data['order_requested_time'])){
                    $offerday = strtolower(date('l', $this->session->data['order_requested_time']));
                    $date = date('d', $this->session->data['order_requested_time']);
                    $month = date('m', $this->session->data['order_requested_time']);
                    $year = date('Y', $this->session->data['order_requested_time']);
                    $order_time = $this->session->data['order_requested_time'];
                 }else{
                    $offerday = strtolower(date('l', strtotime('now')));
                    $date = date('d', strtotime('now'));
                    $month = date('m', strtotime('now'));
                    $year = date('Y', strtotime('now'));
                    $order_time = strtotime('now');
                } 
               if(isset($offer_times[$offerday])){
                   $start_times =  explode(':',$offer_times[$offerday]['start']);
                   $end_times =  explode(':',$offer_times[$offerday]['end']);
                   $offer_start_time=mktime((int)$start_times[0],(int) $start_times[1], 0, $month, $date, $year);
                   $offer_end_time=mktime((int)$end_times[0], (int)$end_times[1], 0, $month, $date , $year);
                   if ((int)$start_times[0] > (int)$end_times[0]){
                        $offer_end_time=mktime((int)$end_times[0], (int)$end_times[1], 0, $month, $date+1, $year);
                   }
                    if ($order_time >= $offer_start_time && $order_time <= $offer_end_time) {
                        return true;
                    }else{
                        return false;
                    }
               }else{
                   return false;
               }
            }else{
                return true;
            }
    }
    public function getOfferToProduct($offer_id) {
        $query = $this->db->query("SELECT product_id  FROM " . DB_PREFIX . "offer_to_product  WHERE offer_id='" . $offer_id . "' ");
        $data = $query->rows;
        $datas = array();
        $i = 0;
        foreach ($data as $k => $value) {
            foreach ($value as $pid) {
                $datas[$i] = $pid;
            }
            $i++;
        }
        return $datas;
    }
    public function getExcludedoptions($offer_id) {
        $query = $this->db->query("SELECT *  FROM " . DB_PREFIX . "offer_to_exclude_options  WHERE offer_id='" . $offer_id . "' ");
        $data = $query->rows;
        return $data;
    }
    public function getOfferdetails($store_id=0){
        $offer_data = array();
        $offer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers o WHERE ((o.date_start = '0000-00-00' OR o.date_start < NOW()) AND (o.date_end = '0000-00-00' OR o.date_end > NOW())) AND o.status = '1' ORDER BY o.sort_order ASC");
        if($offer_query->num_rows) {
            foreach ($offer_query->rows as $offer) {
                $offer_sores = $this->getOfferToStore($offer['offer_id']);
                if(!in_array($store_id, $offer_sores)){
                    continue;
                }
                $offer_data[]=array(
                    'offer_id'=>$offer['offer_id'],
                    'name'=>$offer['name'],
                    'offer_type'=>$offer['offer_type'],
                    'total_amount'=>(float)$offer['total_amount'],
                    'total_quantity'=> unserialize($offer['total_quantity']),
                    'offer_to_coupon'=>$offer['offer_to_coupon'],   
                    'coupon_case_sensitive'=>$offer['coupon_case_sensitive'],
                    'max_usage'=>$offer['max_usage'],
                    'customer_max_usage'=>$offer['customer_max_usage'],
                    'shipping_cost'=>$offer['shipping_cost'],
                    'date_start'=>$offer['date_start'],
                    'date_end'=>$offer['date_end'],
                    'logged'=>$offer['logged'],
                    'shipping'=>$offer['shipping'],
                    'multi_offers'=>$offer['multi_offers'],
                    'coupon_select'=>$offer['coupon_select'],
                    'product_option_apply'=>$offer['product_option_apply'],
                    'special_products_apply'=>$offer['special_products_apply'],
                    'discounted_products_apply'=>$offer['discounted_products_apply'],
                    'discount_type'=>$offer['discount_type'],
                    'discount_amount'=>unserialize($offer['discount_amount']),
                    'max_discount'=>(float)$offer['max_discount'],
                    'product_quantity_buy'=>$offer['product_quantity_buy'],
                    'product_quantity_free'=>unserialize($offer['product_quantity_free']),
                    'quantity_product_sale_type'=>$offer['quantity_product_sale_type'],
                    'sort_order'=>$offer['sort_order'],
                    'free_product_auto_add'=>$offer['free_product_auto_add'],
                    'offer_specific_days'=>unserialize($offer['offer_specific_days']),
                    'offer_first_orderonly'=>$offer['offer_first_orderonly'],
                    'applied_payments'=>unserialize($offer['applied_payments']), 
                    'applied_shipping'=>unserialize($offer['applied_shipping'])
                );
            }
        }
        return $offer_data ;
    }
}

?>