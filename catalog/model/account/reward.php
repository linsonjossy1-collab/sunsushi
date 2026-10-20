<?php

class ModelAccountReward extends Model {

    public function getRewards($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int) $this->customer->getId() . "'";

        $sort_data = array(
            'points',
            'description',
            'date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY date_added";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalRewards() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int) $this->customer->getId() . "'");

        return $query->row['total'];
    }

    public function getTotalPoints() {
        $query = $this->db->query("SELECT SUM(points) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int) $this->customer->getId() . "' GROUP BY customer_id");

        if ($query->num_rows) {
            return $query->row['total'];
        } else {
            return 0;
        }
    }

    /* New uption for reward points */

    public function addReward($description = '', $points = '', $order_id = 0,$customer_id=false) {
        $this->load->model('account/customer');
        if($customer_id===false){
            $customer_info = $this->model_account_customer->getCustomer((int) $this->customer->getId());
            $customer_id = (int) $this->customer->getId();
        }else{
            $customer_info = $this->model_account_customer->getCustomer((int) $customer_id);
        }
         // NEW UPDATE
        if ($order_id) {
            $reward_total = $this->getTotalCustomerRewardsByOrderId($order_id);
            if ($reward_total) {
                return false;
            }
            
        }
        if ($customer_info) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int) $order_id . "', points = '" . (int) $points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");
            if(empty($customer_info['email']) || $customer_info['email']=='' || !$this->config->get('config_reward_points_mail')){
                return ;
            }
            $this->language->load('mail/customer');

            if ($order_id) {
                 $this->load->model('checkout/order');

                $order_info = $this->model_checkout_order->getOrder($order_id);

                if ($order_info) {
                     if($order_info['store_name']!='' && $order_info['store_name']!='EPOS'){
                        $store_name = $order_info['store_name'];
                    }else{
                        $store_name = $this->config->get('config_name');
                    } 
                } else {
                    $store_name = $this->config->get('config_name');
                }
            } else {
                $store_name = $this->config->get('config_name');
            }

            $message = sprintf($this->language->get('text_reward_received'), $points) . "\n\n";
            $message .= sprintf($this->language->get('text_reward_total'), $this->getTotalPoints());

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($customer_info['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($store_name);
            $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_reward_subject'), $store_name), ENT_QUOTES, 'UTF-8'));
            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
            $mail->send();
        }
    }
    
    
    public function addCoupon($msmedia, $customer_id) {
        $startdate = date("Y-m-d");
        $date = strtotime("+7 day");
        $enddate = date("Y-m-d", $date);
        $couponcode = $this->createCouponcode();
        $this->language->load('mail/customer');
        $this->load->model('account/customer');
        $coupondata = array();
        if ($msmedia == 'facebook') {
            $couponcode = 'FB' . $couponcode . $customer_id;
            $coupondata = $this->getSocialofferDetails(1);
        }
        if ($msmedia == 'tweet') {
            $couponcode = 'TW' . $couponcode . $customer_id;
            $coupondata = $this->getSocialofferDetails(2);
        }
        //$couponcode='TW3BSAT';
        $coupon_info = $this->getCouponbycode($couponcode);
         $customer_info = $this->model_account_customer->getCustomer((int) $customer_id);
          $store_name = $this->config->get('config_name');
        if (empty($coupon_info)) {
            if (!empty($coupondata)) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "coupon SET name = '" . $this->db->escape($couponcode) . "', code = '" . $this->db->escape($couponcode) . "', discount = '" . (float) $coupondata['discount'] . "', type = '" . $this->db->escape($coupondata['type']) . "', total = '" . (float) $coupondata['total'] . "', logged = '" . (int) $coupondata['logged'] . "', shipping = '" . (int) $coupondata['shipping'] . "', date_start ='" . $this->db->escape($coupondata['date_start']) . "', date_end = '" . $this->db->escape($coupondata['date_end']) . "', uses_total = '" . (int) $coupondata['uses_total'] . "', uses_customer = '" . (int) $coupondata['uses_customer'] . "', status = '" . (int) $coupondata['status'] . "', date_added = NOW()");
                $c_id = $this->db->getLastId();
                $this->addCouponproduct($c_id, $msmedia);
            } else {
                $this->db->query("INSERT INTO " . DB_PREFIX . "coupon SET name = '" . $this->db->escape($couponcode) . "', code = '" . $this->db->escape($couponcode) . "', discount = 10, type = 'P', total = 10, logged = 0, shipping = 0, date_start ='" . $startdate . "', date_end = '" . $enddate . "', uses_total = 1, uses_customer = 1, status = 1, date_added = NOW()");
            }
//            $message = sprintf($this->language->get('text_coupon_code'));
//            if ($msmedia == 'facebook') {
//               $message .= sprintf($this->language->get('text_fb_like'));
//            }
//            if ($msmedia == 'tweet') {
//                $message .= sprintf($this->language->get('text_twitter_tweets'));
//            }
//            $message .=' '.$couponcode. "\n\n";
//            $message .='Coupon can be used Between '. $coupondata['date_start'].' and '.$coupondata['date_end'];
            //$message = sprintf($this->language->get('text_coupon_code'), $points) . "\n\n";
            
           $message='Hi '.$customer_info['firstname']."\n";
            $message.='Here is your'.$this->config->get('config_name').'  Facebook Like Code'."\n";
            $message.='Copon code -- '.$couponcode."\n";
            //$message.="<a href='www.delichez.co.uk'>Click here to order now.</a>"."\n";
            $message.="Use your coupon on checkout."."\n\n\n";
            $message.='Thanks once again'."\n\n";
            $message.=$this->config->get('config_owner')."\n";
            $message.='Owner'."\n";
            $message.=$this->config->get('config_name');        
            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($customer_info['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($store_name);
            $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_coupon_subject'), $store_name), ENT_QUOTES, 'UTF-8'));
            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
            $mail->send();
            return $couponcode;
        } else {
            //echo "EXISTS";
            $this->addCoupon($msmedia);
            // die();
        }
    }

    public function getCouponbycode($code) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "coupon WHERE code = '" . $this->db->escape($code) . "'");

        return $query->row;
    }

    private function createCouponcode() {
        $len = 4;
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        for ($i = 0; $i < $len; $i++) {
            $pos = rand(0, strlen($chars) - 1);
            $string .= $chars{$pos};
        }
        return $string;
    }

    public function getCustomercoupon($msmedia, $customer_id) {
        //$query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "coupon WHERE substr(`code`,7) LIKE '" . $customer_id . "' ");
        //print_r($query->row);
        //	return $query->row; 
        // echo $msmedia;die();
        //echo "SELECT  * FROM " . DB_PREFIX . "coupon WHERE substr(`code`,0,1) LIKE 'F'";die();
        if ($msmedia == 'facebook') {
            // echo "SELECT  * FROM " . DB_PREFIX . "coupon WHERE substr(`code`,7) LIKE '" . $customer_id . "'";die();
            //$couponcode = 'F'.$couponcode.$customer_id;
            $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "coupon WHERE  substr(`code`,1,2) LIKE 'FB' AND substr(`code`,7) LIKE '" . $customer_id . "' ");
            //print_r($query->row);
            //return $query->row; 
        }
        if ($msmedia == 'tweet') {
            //$couponcode = 'T'.$couponcode.$customer_id;
            $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "coupon WHERE substr(`code`,1,2) LIKE 'TW' AND substr(`code`,7) LIKE '" . $customer_id . "' ");
        }
        //print_r($query->row);die();
        return $query->row;
    }

    public function getSocialofferDetails($id) {

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "social_media_offer_details WHERE id = '" . (int) $id . "'");

        return $query->row;

        //return $query->rows;
    }

    public function getSocialofferProductDetails($id) {
        $coupon_product_data = array();
//echo "SELECT * FROM " . DB_PREFIX . "social_media_offer_product WHERE social_offer_id = '" . (int) $id . "'";die();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "social_media_offer_product WHERE social_offer_id = '" . (int) $id . "'");

        foreach ($query->rows as $result) {
            $coupon_product_data[] = $result['product_id'];
        }

        return $coupon_product_data;
        //	return $query->rows;
    }

    public function addCouponproduct($id, $msmedia) {
        $coupon_prodcuts = array();
        if ($msmedia == 'facebook') {
            $coupon_prodcuts = $this->getSocialofferProductDetails(1);
        } else {
            $coupon_prodcuts = $this->getSocialofferProductDetails(2);
        }
        if (!empty($coupon_prodcuts)) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int) $id . "'");
            foreach ($coupon_prodcuts as $product_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "coupon_product SET coupon_id = '" . (int) $id . "', product_id = '" . (int) $product_id . "'");
            }
        }
    }

    public function getSocialmediaOffresbyname($msmedia) {
        $details = array();
        if ($msmedia == 'facebook') {
            //$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "social_media_offer_details WHERE id = 1 AND status=1");
            $details = $this->getSocialofferDetails(1);
        } else {
           // $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "social_media_offer_details WHERE id = 2 AND status=1");
            $details = $this->getSocialofferDetails(2);
        }
        return $details;
    }

//    public function addCoupon($msmedia, $customer_id) {
//        $startdate = date("Y-m-d");
//        $date = strtotime("+7 day");
//        $enddate = date("Y-m-d", $date);
//        $couponcode = $this->createCouponcode();
//        $this->language->load('mail/customer');
//        $this->load->model('account/customer');
//        if ($msmedia == 'facebook') {
//            $couponcode = 'F' . $couponcode . $customer_id;
//        }
//        if ($msmedia == 'tweet') {
//            $couponcode = 'T' . $couponcode . $customer_id;
//        }
//        //$couponcode='TW3BSAT';
//        $coupon_info = $this->getCouponbycode($couponcode);
//        if (empty($coupon_info)) {
//            $customer_info = $this->model_account_customer->getCustomer((int) $customer_id);
//            $this->db->query("INSERT INTO " . DB_PREFIX . "coupon SET name = '" . $this->db->escape($couponcode) . "', code = '" . $this->db->escape($couponcode) . "', discount = 10, type = 'P', total = 10, logged = 0, shipping = 0, date_start ='" . $startdate . "', date_end = '" . $enddate . "', uses_total = 1, uses_customer = 1, status = 1, date_added = NOW()");
//            $store_name = $this->config->get('config_name');
//            $message = sprintf($this->language->get('text_coupon_code'));
//            if ($msmedia == 'facebook') {
//               $message .= sprintf($this->language->get('text_fb_like'));
//            }
//            if ($msmedia == 'tweet') {
//                $message .= sprintf($this->language->get('text_twitter_tweets'));
//            }
//            $message .=' '.$couponcode;
//            //$message = sprintf($this->language->get('text_coupon_code'), $points) . "\n\n";
//            
//
//            $mail = new Mail();
//            $mail->protocol = $this->config->get('config_mail_protocol');
//            $mail->parameter = $this->config->get('config_mail_parameter');
//            $mail->hostname = $this->config->get('config_smtp_host');
//            $mail->username = $this->config->get('config_smtp_username');
//            $mail->password = $this->config->get('config_smtp_password');
//            $mail->port = $this->config->get('config_smtp_port');
//            $mail->timeout = $this->config->get('config_smtp_timeout');
//            $mail->setTo($customer_info['email']);
//            $mail->setFrom($this->config->get('config_email'));
//            $mail->setSender($store_name);
//            $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_coupon_subject'), $store_name), ENT_QUOTES, 'UTF-8'));
//            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
//            $mail->send();
//
//            return $couponcode;
//        } else {
//            //echo "EXISTS";
//            $this->addCoupon($msmedia);
//            // die();
//        }
//    }
//
//    public function getCouponbycode($code) {
//        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "coupon WHERE code = '" . $this->db->escape($code) . "'");
//
//        return $query->row;
//    }
//
//    private function createCouponcode() {
//        $len = 3;
//        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
//        $string = '';
//        for ($i = 0; $i < $len; $i++) {
//            $pos = rand(0, strlen($chars) - 1);
//            $string .= $chars{$pos};
//        }
//        return $string;
//    }
//
//    public function getCustomercoupon($msmedia, $customer_id) {
//        //$query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "coupon WHERE substr(`code`,7) LIKE '" . $customer_id . "' ");
//        //print_r($query->row);
//        //	return $query->row; 
//        // echo $msmedia;die();
//        //echo "SELECT  * FROM " . DB_PREFIX . "coupon WHERE substr(`code`,0,1) LIKE 'F'";die();
//        if ($msmedia == 'facebook') {
//            // echo "SELECT  * FROM " . DB_PREFIX . "coupon WHERE substr(`code`,7) LIKE '" . $customer_id . "'";die();
//            //$couponcode = 'F'.$couponcode.$customer_id;
//            $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "coupon WHERE  substr(`code`,1,1) LIKE 'F' AND substr(`code`,5) LIKE '" . $customer_id . "' ");
//            //print_r($query->row);
//            //return $query->row; 
//        }
//        if ($msmedia == 'tweet') {
//            //$couponcode = 'T'.$couponcode.$customer_id;
//            $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "coupon WHERE substr(`code`,1,1) LIKE 'T' AND substr(`code`,5) LIKE '" . $customer_id . "' ");
//        }
//        //print_r($query->row);die();
//        return $query->row;
//    }
 public function getTotalCustomerRewardsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");
	
		return $query->row['total'];
	}
        
}

?>