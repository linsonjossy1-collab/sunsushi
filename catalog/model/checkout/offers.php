<?php

class ModelCheckoutOffers extends Model {

    public function getOffer($offer_id) {
        $this->load->model('account/order');
        $status = true;
        $offers_query = $this->db->query("SELECT DISTINCT *  FROM " . DB_PREFIX . "offers  WHERE offer_id='" . $offer_id . "' ");
        //return $offers_query->row;

        if ($offers_query->num_rows) {

            if ($offers_query->row['logged'] || $offers_query->row['newsletter']) {
                if(!$this->customer->getId()) {
                    $status = false;
                }
            }

              //NEW
            $coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "offer_history` ch WHERE ch.offer_id = '" . (int) $offer_id . "'");
            if ($offers_query->row['max_usage'] > 0 && ($coupon_history_query->row['total'] >= $offers_query->row['max_usage'])) {
                $status = false;
            }
            if ($this->customer->getId()) {
                $coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "offer_history` ch WHERE ch.offer_id = '" . (int) $offer_id . "' AND ch.customer_id = '" . (int) $this->customer->getId() . "'");
                if ($offers_query->row['customer_max_usage'] > 0 && ($coupon_history_query->row['total'] >= $offers_query->row['customer_max_usage'])) {
                    $status = false;
                }
            }
            //NEW
            if ($offers_query->row['offer_first_orderonly'] && $this->customer->getId()) {

                if($offers_query->row['basedon_date']) {
                    $date_added_query = ' AND date_added >= "'.$offers_query->row['date_start'].' 00:00:01"';
                } else {
                    $date_added_query = '';
                }

                $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND order_status_id NOT IN (0,7,10) ".$date_added_query);


                $order_count = $query->row['total'];
                $order_count++;
                $order_count_status = false;
                switch($offers_query->row['offer_first_orderonly']){
                    case 0://For all Orders
                        $order_count_status = true;
                        break;
                    case 1://For Specific Order Count (ie for 1st/ 2nd / 5th Order 
                        if($order_count == $offers_query->row['minimum_order_count']){
                            $order_count_status = true;
                        }
                        break;
                    case 2: // betqeen 2 orders ( between customer 5 -10 Orders)
                        if($order_count >= $offers_query->row['minimum_order_count'] && $order_count <= $offers_query->row['maximum_order_count']) {
                            $order_count_status = true;
                        }
                        break;
                    case 3 ://For every 5 th order  
                        if(fmod($order_count,$offers_query->row['minimum_order_count'])==0){
                            $order_count_status = true;
                        }
                        break;
                }
                $status = $order_count_status;
                /*
                //if ($order_count > 0) {
                if($offers_query->row['offer_first_orderonly']==1) {

                    if($order_count == $offers_query->row['minimum_order_count']) {
                        //no issues
                    } else {
                        //$this->log->write($this->customer->getId().'->'.$order_count.'->'.$offers_query->row['name']);
                        $status = false;
                    }

                } else if($offers_query->row['offer_first_orderonly']==2) {

                    if($order_count < $offers_query->row['minimum_order_count'] || $order_count > $offers_query->row['maximum_order_count']) {
                        $status = false;
                    }

                } else if($order_count== $offers_query->row['offer_first_orderonly']) {
                    //matched
                } else {
                    $status = false;
                }
                */
            }
            /*
            if($offers_query->row['offer_first_orderonly'] && (isset($this->session->data['guest']) || isset($this->session->data['noofferfirstime']))){
                $status = false;
            }
            if ($offers_query->row['offer_first_orderonly'] && $this->customer->getId()) {
                $order_count = $this->model_account_order->getTotalOrders();
                if ($order_count > 0) {
                    $status = false;
                }
            } else if($offers_query->row['order_count'] && $status){
                $order_count = $this->model_account_order->getTotalOrders();
                if ($order_count < $offers_query->row['order_count']) {
                    $status = false;
                }
            }
            */
            if($status && $offers_query->row['newsletter']) {
                if (isset($this->session->data['customer_id'])) { 
                    $customer_id = $this->session->data['customer_id'];
                } else {
                    $customer_id = $this->customer->getId();
                }
                $q = $this->db->query("SELECT newsletter FROM ".DB_PREFIX."customer where customer_id = '".(int)$customer_id."' AND newsletter = 1");

                //$this->log->write("SELECT newsletter FROM ".DB_PREFIX."customer where customer_id = '".(int)$customer_id."' AND newsletter = 1");
                if(!isset($q->row['newsletter'])) {
                    $status = false;
                }
            }


            
            $offer_product_data = $this->getOfferToProduct($offer_id);
            // print_r($offer_product_data);die();
            if ($offer_product_data) {
                $offer_product = false;

                foreach ($this->cart->getProducts() as $product) {
                    if (in_array($product['product_id'], $offer_product_data)) {
                        $offer_product = true;
                        // echo  $product['product_id'];die();
                        break;
                    }
                }

                if (!$offer_product) {
                    $status = false;
                }
            }
        } else {
            $status = false;
        }

        if ($status) {
            return array(
                'offer_id' => $offers_query->row['offer_id'],
                'name' => $offers_query->row['name'],
                'offer_type' => $offers_query->row['offer_type'],
                'total_amount' => $offers_query->row['total_amount'],
                'total_quantity' => $offers_query->row['total_quantity'],
                'offer_to_coupon' => $offers_query->row['offer_to_coupon'],
                'coupon_code' => $offers_query->row['coupon_code'],
                'max_usage' => $offers_query->row['max_usage'],
                'customer_max_usage' => $offers_query->row['customer_max_usage'],
                'shipping_cost' => $offers_query->row['shipping_cost'],
                'date_start' => $offers_query->row['date_start'],
                'date_end' => $offers_query->row['date_end'],
                'product' => $offer_product_data,
                'shipping' => $offers_query->row['shipping'],
                'coupon_select' => $offers_query->row['coupon_select'],
                'product_option_apply' => $offers_query->row['product_option_apply'],
                'special_products_apply' => $offers_query->row['special_products_apply'],
                'discounted_products_apply' => $offers_query->row['discounted_products_apply'],
                'discount_type' => $offers_query->row['discount_type'],
                'discount_amount' => $offers_query->row['discount_amount'],
                'max_discount' => $offers_query->row['max_discount'],
                'product_quantity_buy' => $offers_query->row['product_quantity_buy'],
                'product_quantity_free' => $offers_query->row['product_quantity_free'],
                'quantity_product_sale_type' => $offers_query->row['quantity_product_sale_type'],
                'status' => $offers_query->row['status'],
                'offer_times' => $offers_query->row['offer_specific_days'],
                'offer_message' => $offers_query->row['offer_message']
            );
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

    public function redeem($offer_id, $order_id, $customer_id, $amount) {

        $offers_query = $this->db->query("SELECT  *  FROM " . DB_PREFIX . "offer_history  WHERE offer_id='" . $offer_id . "'  AND order_id='" . $order_id . "'");
        //$offer_results=if ($offers_query->num_rows);
        if (!$offers_query->num_rows) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "offer_history` SET offer_id = '" . (int) $offer_id . "', order_id = '" . (int) $order_id . "', customer_id = '" . (int) $customer_id . "', amount = '" . (float) $amount . "', date_added = NOW()");
        }
        $offer_query = $this->db->query("SELECT DISTINCT *  FROM " . DB_PREFIX . "offers  WHERE offer_id='" . $offer_id . "'");
        if($offer_query->row['offer_to_coupon']==1){
            $this->db->query("UPDATE`" . DB_PREFIX . "offer_to_coupon` SET usage_count = usage_count+1 WHERE offer_id = '" . (int) $offer_id . "' AND coupon_code = '" . $this->session->data['coupon']. "'");
        }
    }
   
    
    public function getCoupon($code) {
        $status = true;
       // echo $code;
        $coupon_query=$this->db->query("SELECT *,co.coupon_code as couponcode FROM " . DB_PREFIX . "offer_to_coupon co LEFT JOIN " . DB_PREFIX . "offers o ON (co.offer_id = o.offer_id) WHERE co.coupon_code = '".$code."'");
        
        if (!empty($coupon_query->rows)) {
            foreach ($coupon_query->rows as $coupon_details) {
                if ($coupon_details['total_amount'] >= $this->cart->getSubTotal()) {
                    $status = false;
                }

                if($coupon_details['coupon_case_sensitive']=='1' and $coupon_details['couponcode']!=$code){
                    $status = false;
                    //echo "1";
                }
                $coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "offer_history` ch WHERE ch.offer_id = '" . (int) $coupon_details['offer_id'] . "'");

                if ($coupon_details['max_usage'] > 0 && ($coupon_history_query->row['total'] >= $coupon_details['max_usage'])) {
                    $status = false;
                }

                if ($coupon_details['logged'] && !$this->customer->getId()) {
                    $status = false;
                }

                if ($this->customer->getId()) {
                    $coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "offer_history` ch WHERE ch.offer_id = '" . (int) $coupon_details['offer_id'] . "' AND ch.customer_id = '" . (int) $this->customer->getId() . "'");

                    if ($coupon_details['customer_max_usage'] > 0 && ($coupon_history_query->row['total'] >= $coupon_details['customer_max_usage'])) {
                        $status = false;
                    }
                }

                $coupon_product_data = array();

                $coupon_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_to_product WHERE offer_id = '" . (int) $coupon_details['offer_id'] . "'");
                if (!empty($coupon_product_query)) {
                    foreach ($coupon_product_query->rows as $result) {
                        $coupon_product_data[] = $result['product_id'];
                    }
                    if ($coupon_product_data) {
                        $coupon_product = false;

                        foreach ($this->cart->getProducts() as $product) {
                            if (in_array($product['product_id'], $coupon_product_data)) {
                                $coupon_product = true;
                                break;
                            }
                        }

                        if (!$coupon_product) {
                            $status = false;
                        }
                    }
                }
                if($status){
                     return array(
                        'offer_id' => $coupon_details['offer_id'],
                        'code' => $coupon_details['couponcode'],
                        'total' => $coupon_details['total_amount'],
                        'date_start' => $coupon_details['date_start'],
                        'date_end' => $coupon_details['date_end'],
                        'uses_total' => $coupon_details['max_usage'],
                        'uses_customer' => $coupon_details['customer_max_usage'],
                        'status' => $coupon_details['status'],
                         'case_sensitive'=> $coupon_details['coupon_case_sensitive']
                    );
                    break;
                }
            }
        } else {
            $status = false;
        }
        //$coupon_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers WHERE coupon_code = '" . $this->db->escape($code) . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");

        return $status;

        
    }


    public function validateCoupon($code) {
        $result['status'] = 0;
        $result['msg'] = '';
        $coupon_query=$this->db->query("SELECT *,co.coupon_code as couponcode FROM " . DB_PREFIX . "offer_to_coupon co LEFT JOIN " . DB_PREFIX . "offers o ON (co.offer_id = o.offer_id) WHERE co.coupon_code = '".$code."'");
        if (!empty($coupon_query->rows)) {
            foreach ($coupon_query->rows as $coupon_details) {
                if ($coupon_details['total_amount'] >= $this->cart->getSubTotal()) {
                    $result['msg'] = 'Minimum Cart amount not reached ';
                }

                if($coupon_details['coupon_case_sensitive']=='1' and $coupon_details['couponcode']!=$code){
                    $result['msg'] = 'Coupon code not matching';
                }
                $coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "offer_history` ch WHERE ch.offer_id = '" . (int) $coupon_details['offer_id'] . "'");

                if ($coupon_details['max_usage'] > 0 && ($coupon_history_query->row['total'] >= $coupon_details['max_usage'])) {
                    $result['msg'] = 'Maximum Usage limit exceeded';
                }

                if ($coupon_details['logged'] && !$this->customer->getId()) {
                    $result['msg'] = 'You must login to apply this coupon';
                }

                if ($this->customer->getId()) {
                    $coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "offer_history` ch WHERE ch.offer_id = '" . (int) $coupon_details['offer_id'] . "' AND ch.customer_id = '" . (int) $this->customer->getId() . "'");

                    if ($coupon_details['customer_max_usage'] > 0 && ($coupon_history_query->row['total'] >= $coupon_details['customer_max_usage'])) {
                        $result['msg'] = 'You have exceeded the maximum usage limit for this coupon';
                    }
                }

                $shippingcheck = false;

                if(!$result['msg']) {
                    $offer_shipping = unserialize($coupon_details['applied_shipping']);
                    if (!empty($offer_shipping)) {
                        if (isset($this->session->data['shipping_method']['code'])) {
                            if(!in_array($this->session->data['shipping_method']['code'], $offer_shipping)) {
                                $result['msg'] = 'Coupon code available shipping method not selected';
                            }
                        } else {
                            $result['msg'] = 'Coupon code will be available after selecting the specified shipping method';
                            $shippingcheck = true;
                        }
                    }
                    $offer_payments = unserialize($coupon_details['applied_payments']);
                    if (!empty($offer_payments)) {
                        if (isset($this->session->data['payment_method']['code'])) {
                            if(!in_array($this->session->data['payment_method']['code'], $offer_payments)) {
                                $result['msg'] = 'Coupon code available payment method not selected';
                            }
                        } else {
                            $result['msg'] = 'Coupon code will be available after selecting the specified payment method';
                            $shippingcheck = true;
                        }
                    }
                }

                if($result['msg']=='') {
                    
                    $offer_status = $this->checkOffertimes($coupon_details['offer_specific_days']);
                    if(!$offer_status){
                        $result['msg'] = 'The selected coupon is not currently available.';
                    }
                }

                if($result['msg']=='' || $shippingcheck) {
                    $coupon_product_data = array();

                    $p_total = 0;

                    $coupon_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_to_product WHERE offer_id = '" . (int) $coupon_details['offer_id'] . "'");
                    if (!empty($coupon_product_query)) {
                        foreach ($coupon_product_query->rows as $result) {
                            $coupon_product_data[] = $result['product_id'];
                        }
                        if ($coupon_product_data) {
                            $coupon_product = false;

                            foreach ($this->cart->getProducts() as $product) {
                                if (in_array($product['product_id'], $coupon_product_data)) {
                                    $coupon_product = true;

                                    $p_total+=$product['total'];

                                    if($p_total > $coupon_details['total_amount']) {
                                        break;
                                    }

                                }
                            }

                            if (!$coupon_product) {
                                $result['msg'] = 'Coupon Products not found in your cart';
                            } else if($p_total < $coupon_details['total_amount']) {
                                $result['msg'] = 'Minimum cart amount for coupon product not reached. ';
                            }
                        }
                    }
                    if($result['msg']=='' || $shippingcheck){
                        return array(
                            'offer_id' => $coupon_details['offer_id'],
                            'code' => $coupon_details['couponcode'],
                            'total' => $coupon_details['total_amount'],
                            'date_start' => $coupon_details['date_start'],
                            'date_end' => $coupon_details['date_end'],
                            'uses_total' => $coupon_details['max_usage'],
                            'uses_customer' => $coupon_details['customer_max_usage'],
                            'status' => $coupon_details['status'],
                            'case_sensitive'=> $coupon_details['coupon_case_sensitive'],
                            'msg' =>$result['msg'],
                            'status'=>1,
                            'shipping_check' => $shippingcheck
                        );
                        break;
                    }
                }
            }
        } else {
            $result['msg'] = 'Invalid Coupon Code';
        }
        return $result;
    }

    private function checkOffertimes($ofrtime){
       
        if (!empty($ofrtime)){
            $offer_times = unserialize($ofrtime);
            if(isset($this->session->data['order_requested_time']) && $this->session->data['order_requested_time'] > strtotime('now')){
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

    
    public function checkCuoponavailability(){
        $query = $this->db->query("SELECT *  FROM " . DB_PREFIX . "offers  WHERE offer_to_coupon='1'");
        if(!empty($query->rows)){
            return true;
        }else{
            return false;;
        }
    }
     public function getPosOfferImages($image_type,$width,$height){
        $offer_images = array();
        $this->load->model('tool/image');
        $query = $this->db->query("SELECT $image_type  FROM " . DB_PREFIX . "offers WHERE ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1' ");
        //echo "SELECT $image_type  FROM " . DB_PREFIX . "offers";
        if(!empty($query->rows)){
            foreach ($query->rows as $result) {
                if (file_exists(DIR_IMAGE . $result[$image_type])) {
                     $offer_images[]=$this->model_tool_image->resize($result[$image_type], $width, $height);
                }               
            }
        }
        //print_r($offer_images);
        return $offer_images;
    }
    public function getOfferImages($image_type,$width,$height){
        $offer_images = array();
        $this->load->model('tool/image');
        $query = $this->db->query("SELECT name,$image_type  FROM " . DB_PREFIX . "offers WHERE ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1' ");
        //echo "SELECT $image_type  FROM " . DB_PREFIX . "offers";
        if(!empty($query->rows)){
            foreach ($query->rows as $result) {
                if ($result[$image_type] && file_exists(DIR_IMAGE . $result[$image_type])) {
                    $offer_images[]=array(
                        'title'=>$result['name'],
                        'image'=>$this->model_tool_image->resize($result[$image_type], $width, $height)
                    );
                }               
            }
        }
        //print_r($offer_images);
        return $offer_images;
    }
}
?>