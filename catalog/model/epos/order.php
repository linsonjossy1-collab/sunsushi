<?php
class ModelEposOrder extends Model {
    public function deleteOrder($order_id){
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND order_id = '" . (int)$order_id . "'");
        if ($order_query->num_rows) {
                $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

                foreach($product_query->rows as $product) {
                        $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");

                        $option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

                        foreach ($option_query->rows as $option) {
                                $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
                        }
                }
        }
        $order_split_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_split` WHERE order_id = '" . (int)$order_id . "'");
        if ($order_split_query->num_rows) {
            foreach($order_split_query->rows as $order_split) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_split_product WHERE order_split_id = '" . (int)$order_split['order_split_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_split_total WHERE order_split_id = '" . (int)$order_split['order_split_id'] . "'");
            }
        } 
        
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order` SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_product` SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_option` SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_download` SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_voucher` SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_total` SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_history` SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_fraud` SELECT * FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_bundle` SELECT * FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_product_dressings` SELECT * FROM " . DB_PREFIX . "order_product_dressings WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_product_toppings` SELECT * FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int)$order_id . "'");
        
        
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_split` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
        //$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_transaction WHERE order_id = '" . (int)$order_id . "'");
        
        // Track Order delete
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_deleted_track SET order_id = '" . (int)$order_id . "', deleted_time = '" . date("Y-m-d H:i:s", time()) . "'");
         
        $this->cache->delete('eposorderexportdata');
        /*
         $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE `key` = 'order_deltrack'");
         if(!empty($query->row)){
             $order_deltrack =unserialize($query->row['value']);
            // $this->log->write(print_r($liveorder_deltrack,true));
             // return $data;
         }else{
             $order_deltrack =  array('first_order_id'=>0,'del_orders'=>array());
         }
         $order_deltrack['del_orders'][] = (int)$order_id ;  // last deleted rec added
         
         $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` ORDER BY order_id LIMIT 1 ");
         if(!empty($query->row)){
             $first_order_id  = $query->row['order_id'];
         }
         else {
             $first_order_id  = 0 ;
         }
         $order_deltrack['first_order_id'] = $first_order_id;
         if($order_deltrack['del_orders']) {
             foreach ($order_deltrack['del_orders'] as $key => $t_order_id ) {
                 if ($t_order_id < $first_order_id ) {
                     unset($order_deltrack['del_orders'][$key]);
                 }
             }
         }
         // save to settings
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'order_deltrack'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'order_deltrack', `value` = '" . $this->db->escape(serialize($order_deltrack)) . "'");
        
        */
        
    }
    public function confirm($order_id,$order_status_id){
        //$order_status_id = $this->config->get('config_order_status_id');
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $order_status_id . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "' WHERE order_id = '" . (int) $order_id . "'");
        
        //$this->websocket->connect('o-'.$order_id);
        return;
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        
        

            //Auto ADD Reward Points .... 
            
            $this->load->model('setting/setting');
            $rewards = $this->model_setting_setting->getSetting('rewardpoints');
            if(!empty($rewards) and isset($rewards[5])){
                if($rewards[5]['autoadd']==1 and $this->config->get('config_reawrd_order_status_id')==$order_status_id){
                    if((float)$order_info['total']>=(float)$rewards[5]['amount']){
                         $this->load->model('account/reward');
                         $reward_qry=$this->db->query("SELECT SUM(reward) as reward FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
                         $this->model_account_reward->addReward('Order ID #'.$order_id, $reward_qry->row['reward'],$order_id);
                    }
                }
            }
    }
    public function updateLiveorder($data) {
         unset($data['previous_data']);
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "live_orders WHERE `live_order_id`='".(int)$data['live_order_id']."'");
            
        $live_delquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "live_order_deleted_track WHERE `live_order_id`='".(int)$data['live_order_id']."'");
        if(!empty($live_delquery->rows)){
            $this->db->query("DELETE  FROM " . DB_PREFIX . "live_orders WHERE `live_order_id`='".(int)$data['live_order_id']."'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "live_order_deleted_track SET live_order_id = '" . (int)$data['live_order_id'] . "', deleted_time = '" . date("Y-m-d H:i:s", time()) . "'");
            return;
        }
        if(!empty($query->rows)){
            /*
            $livedata =unserialize($query->row['data']);//currenttrack
            if(isset($livedata['currenttrack']) && isset($data['currenttrack']) && (int)$data['currenttrack']<=(int)$livedata['currenttrack']){
                $data['previous_data']=$livedata;
            }   
            */
            $this->db->query("UPDATE " . DB_PREFIX . "live_orders SET `description` = '".(isset($data['table_id'])?$data['table_id']:0)."',`date_modified` = NOW(), `data`= '" . $this->db->escape($data['data']) . "' ,`lock` ='" . (int)(isset($data['lock'])?$data['lock']:0) . "' ,`system_id` ='" . (int)(isset($data['system_id'])?$data['system_id']:0) . "',`dinein_table_id`='".(isset($data['table_id'])?$data['table_id']:0)."' WHERE live_order_id='".(int)$data['live_order_id']."'"); 
        }else{
            //$this->db->query("DELETE  FROM " . DB_PREFIX . "live_orders WHERE `dinein_table_id`='".(int)$data['live_order_id']."'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "live_orders SET live_order_id='".(int)$data['live_order_id']."', `description` = '".(isset($data['table_id'])?$data['table_id']:0)."',`date_added` = NOW(),`date_modified` = NOW(), `data`= '" .$this->db->escape($data['data']) . "' ,`lock` ='" . (int)(isset($data['lock'])?$data['lock']:0) . "' ,`system_id` ='" . (isset($data['system_id'])?$data['system_id']:0) . "',`dinein_table_id`='".(int)(isset($data['table_id'])?$data['table_id']:0)."'");
        }
       
    }
     public function updateLiveEatinOrder($data) {
        unset($data['previous_data']);
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
        $query = $this->db->query("SELECT live_order_id FROM " . DB_PREFIX . "live_orders WHERE `dinein_table_id`='".(int)$data['table_id']."'");
        if(!empty($query->rows)){
            foreach($query->rows as $row){
                $this->db->query("DELETE  FROM " . DB_PREFIX . "live_orders WHERE `live_order_id`='".(int)$row['live_order_id']."'");
                //$this->db->query("INSERT INTO " . DB_PREFIX . "live_order_deleted_track SET live_order_id = '" . (int)$row['live_order_id'] . "', deleted_time = '" . date("Y-m-d H:i:s", time()) . "'");
            }           
        }
        $this->db->query("INSERT INTO " . DB_PREFIX . "live_orders SET live_order_id='".(int)$data['live_order_id']."', `description` = '".(isset($data['table_id'])?$data['table_id']:0)."',`date_added` = NOW(),`date_modified` = NOW(), `data`= '" .$this->db->escape($data['data']) . "' ,`lock` ='" . (int)(isset($data['lock'])?$data['lock']:0) . "' ,`system_id` ='" . (isset($data['system_id'])?$data['system_id']:0) . "',`dinein_table_id`='".(int)(isset($data['table_id'])?$data['table_id']:0)."'");
    
    }
    public function deleteLiveorder($data){
         if(!isset($data['swapped'])){  
            $this->db->query("DELETE  FROM " . DB_PREFIX . "live_orders WHERE `live_order_id`='".(int)$data['live_order_id']."'");
        }        
        if(isset($data['dinein_table_id'])){
            $this->db->query("DELETE  FROM " . DB_PREFIX . "live_orders WHERE `dinein_table_id`='".(int)$data['dinein_table_id']."'");
        }
        
        // Track delete
        // Track Order delete
        $this->db->query("INSERT INTO " . DB_PREFIX . "live_order_deleted_track SET live_order_id = '" . (int)$data['live_order_id'] . "', deleted_time = '" . date("Y-m-d H:i:s", time()) . "'");
        /*
         $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE `key` = 'liveorder_deltrack'");
         if(!empty($query->row)){
             $liveorder_deltrack =unserialize($query->row['value']);
            // $this->log->write(print_r($liveorder_deltrack,true));
             // return $data;
         }else{
             $liveorder_deltrack =  array('first_live_order_id'=>0,'del_orders'=>array());
         }
         $liveorder_deltrack['del_orders'][] = (int)$data['live_order_id'] ;  // last deleted rec added
        
         //$query = $this->db->query("SELECT live_order_id FROM " . DB_PREFIX . "live_orders ORDER BY live_order_id LIMIT 1 ");
         $query = $this->db->query("SELECT MIN(CONVERT(SUBSTRING(live_order_id,-14),INTEGER)) as live_order_id FROM live_orders");
         if(!empty($query->row)){
             $first_live_order_id  = $query->row['live_order_id'];
              $liveorder_deltrack['first_live_order_id'] = $first_live_order_id;
         }
         else {
             //$first_live_order_id  = 0 ;
         }
        //$liveorder_deltrack['first_live_order_id'] = $first_live_order_id;
        $this->log->write(print_r(array("LIVE-DEL"=>$first_live_order_id),true));
         if($liveorder_deltrack['del_orders'] && isset($first_live_order_id)) {
             foreach ($liveorder_deltrack['del_orders'] as $key => $live_order_id ) {
                 if ($live_order_id < $first_live_order_id ) {
                     unset($liveorder_deltrack['del_orders'][$key]);
                 }
             }
         }
         // save to settings
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'liveorder_deltrack'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'liveorder_deltrack', `value` = '" . $this->db->escape(serialize($liveorder_deltrack)) . "'");
        $this->log->write(print_r(array("LIVE DELETE SUCCESS"),true));
         */
        
    }
    public function updateEpossettings($system_id,$data){  
        //$this->log->write('1www');  
        $this->db->query("UPDATE " . DB_PREFIX . "pos_client_system SET system_config='" . $this->db->escape(serialize($data)) . "' WHERE system_id = '" . (int) $system_id . "'");
        $settings = $this->getPrintsettings($system_id);
        if(empty($settings) || $settings==''){
            $print_settings= $this->posclient->defaultprintsettings($system_id,$data['defaultprinter']);
            $this->posclient->addPosclientPrintsettings($print_settings,$system_id);   
        }
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'settings_modified'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'settings_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");
        //$this->log->write('2www');  
    }
    public function getPrintsettings($system_id) {
        $query = $this->db->query("SELECT print_settings FROM " . DB_PREFIX . "pos_client_system WHERE `system_id`='".(int)$system_id."'");
         if(!empty($query->row)){
             $data =unserialize($query->row['print_settings']);
            // $this->log->write(print_r(array('id'=>$system_id,'dta'=>$data),true));
             return $data;
         }else{
             return array();
         }
    }
    public function getallPrintsettings() {  
        $data =  array();
        $query = $this->db->query("SELECT print_settings,system_id FROM " . DB_PREFIX . "pos_client_system");
        if(!empty($query->rows)){
            foreach ($query->rows as $row) {
                $data[$row['system_id']]=unserialize($row['print_settings']);
            }
        }
        return $data;
    }
    public function addOrderHistory($order_id,$data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = NOW(), user_id = '" . (int) ((isset($data['user_id']))?$data['user_id']:0) . "' WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");
        if ($data['notify']) {
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
			$language = new Language($order_info['language_directory']);
			$language->load($order_info['language_filename']);
			$language->load('mail/order');
 
			$subject = sprintf($language->get('text_subject'), $order_info['store_name'], $order_id);

			$message  = $language->get('text_order') . ' ' . $order_id . "\n";
			$message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";
			
			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$data['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
				
			if ($order_status_query->num_rows) {
				$message .= $language->get('text_order_status') . "\n";
				$message .= $order_status_query->row['name'] . "\n\n";
			}
			
			if ($order_info['customer_id']) {
				$message .= $language->get('text_link') . "\n";
				$message .= html_entity_decode($order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";
			}
			
			if ($data['comment']) {
				$message .= $language->get('text_comment') . "\n\n";
				$message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
			}

			$message .= $language->get('text_footer');

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');
			$mail->setTo($order_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($order_info['store_name']);
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
         $this->websocket->connect('o-'.$order_id);
    }
     public function deletepreviousorders() { 
        //return ;
        $store_opens =  $this->postcode->getLastDaysOpeningtimes() ;
        $delday =time();
        $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE `key` = 'order_deltrack'");
         if(!empty($query->row)){
             $order_deltrack =unserialize($query->row['value']);
            // $this->log->write(print_r($liveorder_deltrack,true));
             // return $data;
         }else{
             $order_deltrack =  array('first_order_id'=>0,'del_orders'=>array());
         }
        switch ($this->config->get('config_order_id_delete_previous')){
            case '0':
                return;
            case 'all':
                $delday = time();
                break;
            case 'yday':
                $delday = strtotime($store_opens[0]['end']);
                break;
            case 'lweek':
                 $delday = strtotime("-1 week", strtotime($store_opens[0]['end']));
                break;
             case 'lmonth':
                 $delday = strtotime("-1 month", strtotime($store_opens[0]['end']));
                break;

        }
        $ordes_query =  $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order`  WHERE  order_status_id IN (" . implode(',', $this->config->get('config_order_id_delete_status')) .") AND requested_time < '" . date("Y-m-d H:i:s",$delday) . "'" );
        if($ordes_query->num_rows) {
            
            foreach($ordes_query->rows as $order){
                $order_deltrack['del_orders'][] = (int)$order['order_id'];  // last deleted rec added
                /*
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order` SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_product` SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_option` SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_download` SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_voucher` SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_total` SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_history` SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_fraud` SELECT * FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_bundle` SELECT * FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_bundle_option` SELECT * FROM " . DB_PREFIX . "order_bundle_option WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_split` SELECT * FROM " . DB_PREFIX . "order_split WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_product_toppings` SELECT * FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_product_dressings` SELECT * FROM " . DB_PREFIX . "order_product_dressings WHERE order_id = '" . (int)$order['order_id'] . "'");
                */
                $this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int)$order['order_id'] . "'");
                //$this->db->query("DELETE FROM " . DB_PREFIX . "order_bundle_option WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_split WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int)$order['order_id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_product_dressings WHERE order_id = '" . (int)$order['order_id'] . "'");
            }

            // Track Order delete
            $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` ORDER BY order_id LIMIT 1 ");
            if(!empty($query->row)){
                $first_order_id  = $query->row['order_id'];
            }
            else {
                $first_order_id  = 0 ;
            }
            $order_deltrack['first_order_id'] = $first_order_id;
            if($order_deltrack['del_orders']) {
                foreach ($order_deltrack['del_orders'] as $key => $t_order_id ) {
                    if ($t_order_id < $first_order_id ) {
                        unset($order_deltrack['del_orders'][$key]);
                    }
                }
            }
            // save to settings
            $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'order_deltrack'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'order_deltrack', `value` = '" . $this->db->escape(serialize($order_deltrack)) . "'");
        }
    }
    public function getOrders($date,$page) {
        $limit =100;
        $limitstrat = $page * $limit;
        if ($limitstrat > 0 ) {
            $limitstrat += 1;
        }
       $orders = array();
        $this->load->model('account/order');
        if($date!=''){ 
            $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE date_modified>'".date('Y-m-d H:i:s',$date)."' AND order_status_id!='0' LIMIT $limitstrat, $limit");
        }else{
            $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id!='0' AND date_added>(NOW() - INTERVAL 7 DAY) LIMIT $limitstrat, $limit");
        }
        //$this->log->write(print_r($order_query->rows,true));
        if($order_query->rows){
            foreach($order_query->rows as $order_data){
                //$order_data[$k]=$order_data;
                //$product_data=array();
                /*
                $order_products = $this->model_account_order->getOrderProducts($order_data['order_id']);
                $k=0;
                foreach ($order_products as $order_product) {
                    $order_product['name'] = html_entity_decode($order_product['name']);
                    $order_product['total'] = number_format($order_product['total'], 2);
                    $order_product['qty']=$order_product['quantity'];
                    $order_product['total_value']=$order_product['total'];
                    $order_product['price_value']=$order_product['price'];
                    $order_product['main_index'] =$k;
                    $order_product['main_product_id']=$order_product['main_product_id'];
                    $product_data[$k] = $order_product;
                    $order_options = $this->model_account_order->getOrderOptions($order_data['order_id'],$order_product['order_product_id']);
                    $option_data=array();
                    if(!empty($order_options)){
                        foreach($order_options as $order_option){
                            if($order_option['type']=='checkbox'){
                                $option_data[$order_option['product_option_id']][$order_option['product_option_value_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                            }else{
                                $option_data[$order_option['product_option_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                            }
                        }
                    }
                    $product_data[$k]['option']=$option_data;
                    $topping_data = array();
                    $order_toppings = $this->model_account_order->getOrderToppings($order_data['order_id'],$order_product['order_product_id']);
                    if(!empty($order_toppings)){
                        foreach($order_toppings as $data){
                            $topping_data[$data['product_topping_id']][$data['product_topping_value_id']]=array('name'=>$data['value'],'qty'=>$data['quantity'],'total_value'=>$data['price'],'price_value'=>$data['price'],'price'=>$data['price'],'topping_id'=>$data['product_topping_id'],'topping_value_id'=>$data['product_topping_value_id'],'toppingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                        }
                    }
                    $dressing_data = array();
                    $order_dressings = $this->model_account_order->getOrderDressings($order_data['order_id'],$order_product['order_product_id']);
                    if(!empty($order_dressings)){
                        foreach($order_dressings as $data){
                            $dressing_data[$data['product_dressing_id']][$data['product_dressing_value_id']]=array('name'=>$data['value'],'qty'=>1,'total_value'=>'','price_value'=>'','dressing_id'=>$data['product_dressing_id'],'dressing_value_id'=>$data['product_dressing_value_id'],'dressingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                        }
                    }
                    
                    $product_data[$k]['bundled']=$this->model_account_order->getOrderBundle($order_data['order_id'],$order_product['order_product_id']);
                    $product_data[$k]['toppings'] =$topping_data;
                    $product_data[$k]['dressings'] = $dressing_data;
                    $k++;
                }
               
                $order_data['items']=$product_data;
                */  
                $product_data = $this->model_account_order->getConsolidatedOrderProducts($order_data['order_id']);
                $order_data['items']=$product_data;
                $order_data['splits']=$this->model_account_order->getOrderSplits($order_data['order_id']);
                $order_data['vouchers'] =$this->model_account_order->getOrderVouchers($order_data['order_id']);
                $order_totals = $this->model_account_order->getOrderTotals($order_data['order_id']);
                $total_data=array();
                foreach ($order_totals as $order_total) { 
                    $total_data[]=array('code'=>$order_total['code'],'name'=>$order_total['title'],'value'=>  number_format($order_total['value'],2));
                }
                $order_data['totals']=$total_data;
                $order_histories=$this->model_account_order->getallOrderHistories($order_data['order_id']);
                $order_data['histories'] = $order_histories;
                $time_difference = 0;
                if(count($order_histories)>1){
                    $difference = strtotime($order_histories[count($order_histories)-1]['date_added']) - strtotime($order_histories[0]['date_added']);
                    $days = floor($difference/86400);  
                    if($days>0){
                        $time_difference =24;
                    }else{
                        $time_difference = floor($difference/3600);
                    }
                    
                }  
                $order_data['time_difference'] = $time_difference;
                $order_data['unix_date_added'] = strtotime($order_data['date_added']);
                $order_data['unix_requested_time'] = strtotime($order_data['requested_time']);
                $order_data['payed_amts']=  unserialize($order_data['payment_history']);
                $order_data['previous_paymemts']=  unserialize($order_data['previous_paymemts']);
                if($order_data['shipping_code']=='dine.dine' && isset($order_data['dinein_table_id']) && $order_data['dinein_table_id']!='' && $order_data['dinein_table_id']!='0'){
                    $order_data['dinein_table_name']=$this->model_account_order->getTablename($order_data['dinein_table_id']);
                } 
                $orders[]=$order_data;
            }
        }
        //print_r($orders);
        return $orders;
    }
    public function getOrderCount($date='') {
        //use  ORDER BY date_modified ASC
        if($date!=''){  
            $order_query = $this->db->query("SELECT count(*) as cnt FROM `" . DB_PREFIX . "order` WHERE date_modified>'".date('Y-m-d H:i:s',$date)."' AND order_status_id!='0'");
        }else{
            $order_query = $this->db->query("SELECT count(*) as cnt FROM `" . DB_PREFIX . "order` WHERE order_status_id!='0'");
        }
        return $order_query->row['cnt'];
    }
     public function addPushhistory($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "epos_push_history SET push_id = '" . $data['psuh_id'] . "',tk_id = '" . (isset($data['tk_id'])?$data['tk_id']:0) . "', method = '" . $data['method'] . "', data = '" . $this->db->escape(serialize($data['data'])) . "',  date_added = NOW()");
    }
    public function addLiveorderstatus($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "live_order_status SET live_order_id = '" . $data['live_order_id'] . "', track_id = '" . $data['track_id'] . "', dinein_table_id = '" . $data['table_id'] . "',  status = 0");
    }
    public function updateLiveorderstatus($data) {
        $this->db->query("UPDATE " . DB_PREFIX . "live_order_status SET status = 1 WHERE live_order_id = '" . $data['live_order_id'] . "' AND track_id = '" . $data['track_id'] . "' AND dinein_table_id = '" . $data['table_id'] . "'");
    }
}
?>