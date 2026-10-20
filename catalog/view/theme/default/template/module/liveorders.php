<?php 
class ModelCheckoutLiveorders extends Model {
	
    public function addLiveorder() {
        //$live_order=array();
        $live_order = $this->session->data;
        //print_r($live_order);die();
        unset($live_order['language']);
        unset($live_order['currency']);
        unset($live_order['browser_type']);
        unset($live_order['user_id']);
        unset($live_order['token']);
        unset($live_order['waklin_customer_id']);
        unset($live_order['shipping_methods']);//if needed
        unset($live_order['payment_methods']);//if needed
        //print_r($live_order);die();
         //Order_id to Live_order_id
        if(!isset($this->session->data['edited_pending_id'])){
            
            if ($this->config->get('config_order_id_unixtimestap')) {
                $order_id =  $this->unixtime();
                $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET order_id=" . $order_id.",`order_status_id`=0 ");
                $this->session->data['order_id'] = $order_id;
            }
            else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET `order_status_id`=0 ");
            $this->session->data['order_id']  = $this->db->getLastId();
            }
        }else{
            $this->session->data['order_id']=$this->session->data['edited_pending_id'];
        }
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "live_orders SET live_order_id='".(int)$this->session->data['order_id']."', `description` = '".(isset($this->session->data['dinein_table_name'])?$this->db->escape($this->session->data['dinein_table_name']):'')."',`date_added` = NOW(), `data`= '" . $this->db->escape(serialize($live_order)) . "' ,`lock` =1 ,`system_id` ='" . (int)(isset($this->session->data['system_id'])?$this->db->escape($this->session->data['system_id']):0) . "',`dinein_table_id`='".(int)(isset($this->session->data['dinein_table_id'])?$this->db->escape($this->session->data['dinein_table_id']):0)."',`cart_total`='".$this->cart->getTotal()."'");
        $this->session->data['live_order_id'] = $this->db->getLastId();
        $live_id = $this->db->getLastId();
         //Live Order Logs
        $this->addOrderLogs($this->db->getLastId(), $live_order);
        return $live_id;
        //Live Order Logs
    }
    
    public function updateLiveorder($live_order_id) {
        $live_order = $this->session->data;
        unset($live_order['language']);
        unset($live_order['currency']);
        unset($live_order['browser_type']);
        unset($live_order['user_id']);
        unset($live_order['token']);
        unset($live_order['waklin_customer_id']);
        unset($live_order['shipping_methods']);//if needed
        unset($live_order['payment_methods']);//if needed
        $this->db->query("UPDATE " . DB_PREFIX . "live_orders SET `data`= '" . $this->db->escape(serialize($live_order)) . "',`lock`=1,`system_id`='".(int)(isset($this->session->data['system_id'])?$this->db->escape($this->session->data['system_id']):0)."',`dinein_table_id`='".(int)(isset($this->session->data['dinein_table_id'])?$this->db->escape($this->session->data['dinein_table_id']):0)."',`cart_total`='".$this->cart->getTotal()."' WHERE  `live_order_id`='".(int)$live_order_id."'");
        
        $this->addOrderLogs($live_order_id, $live_order);
        return $live_order_id; 
    }
    
    public function deleteLiveorder($live_order_id) {
         unset($this->session->data['live_order_id']);
         unset($this->session->data['dinein_table_id']);
         unset($this->session->data['dinein_table_name']);
         $this->db->query("DELETE FROM " . DB_PREFIX . "live_orders where  `live_order_id`='".$live_order_id."'");
    }
    
    public function lockOrder($live_order_id) { 
         $this->db->query("UPDATE " . DB_PREFIX . "live_orders SET `lock`=1 WHERE  `live_order_id`='".(int)$live_order_id."'");
         

    }
    
    public function unlockOrder($live_order_id) {
         $this->db->query("UPDATE " . DB_PREFIX . "live_orders SET `lock`=0 WHERE  `live_order_id`='".(int)$live_order_id."'");
         
    }
    
    public function getLiveOrders() {
        // $query = $this->db->query("SELECT  DISTINCT *  FROM " . DB_PREFIX . "live_orders WHERE `lock`=0 ORDER BY `date_added` ASC");
        $query = $this->db->query("SELECT  DISTINCT *  FROM " . DB_PREFIX . "live_orders l LEFT JOIN " . DB_PREFIX . "store_tables s ON (l.dinein_table_id = s.table_id) WHERE l.`lock`=0 ORDER BY l.`date_added` ASC");
         return $query->rows;
    }
    
    public function getLiveOrder($live_order_id){
        $query = $this->db->query("SELECT  DISTINCT *  FROM " . DB_PREFIX . "live_orders WHERE live_order_id='".(int)$live_order_id."'");
        return $query->row;
    }
    
    public function addCancelledOrder(){
        $this->db->query("INSERT INTO " . DB_PREFIX . "cancelled_live_orders SET `time` = NOW(),`user_id` ='" . (int)(isset($this->session->data['user_id'])?$this->db->escape($this->session->data['user_id']):0) . "',`system_id` ='" . (int)(isset($this->session->data['system_id'])?$this->db->escape($this->session->data['system_id']):0) . "',`data`= '" . $this->db->escape(serialize($this->session->data)) . "'");
    
    } 
    
    public function addOrderLogs($live_order_id,$data){
         $query = $this->db->query("SELECT  DISTINCT *  FROM " . DB_PREFIX . "pos_order_logs WHERE `live_order_id`='".(int)$live_order_id."'   ORDER BY live_order_id DESC LIMIT 1");
         //return $query->row;
         if(!empty($query->row)){//Update
             if($query->row['system_id']==(isset($this->session->data['system_id'])?$this->db->escape($this->session->data['system_id']):0) and $query->row['user_id']==$this->session->data['user_id']){
                $this->db->query("UPDATE " . DB_PREFIX . "pos_order_logs SET `log_time` = NOW(),`data`= '" . $this->db->escape(serialize($data)). "' WHERE order_log_id ='".(int)$query->row['order_log_id']."'  "); 
             }else{
                 $this->db->query("INSERT INTO " . DB_PREFIX . "pos_order_logs SET `log_time` = NOW(),`live_order_id`='".(int)$live_order_id."',`system_id` ='" . (int)(isset($this->session->data['system_id'])?$this->db->escape($this->session->data['system_id']):0) . "',`user_id` ='" . (int)(isset($this->session->data['user_id'])?$this->db->escape($this->session->data['user_id']):0) . "',`data`= '" . $this->db->escape(serialize($data)) . "' "); 
             }    
         }else{//Insert
            $this->db->query("INSERT INTO " . DB_PREFIX . "pos_order_logs SET `log_time` = NOW(),`live_order_id`='".(int)$live_order_id."',`system_id` ='" . (int)(isset($this->session->data['system_id'])?$this->db->escape($this->session->data['system_id']):0) . "',`user_id` ='" . (int)(isset($this->session->data['user_id'])?$this->db->escape($this->session->data['user_id']):0) . "',`data`= '" . $this->db->escape(serialize($data)) . "' "); 
         }
         
    }
     public function getStoretables() {
          $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store_tables ORDER BY table_id ASC");
          return $query->rows;
    }
    
    public function getDineinOrders() {
        $dine_in_ids =array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "live_orders WHERE `dinein_table_id` <> 0");
          //print_r($query->rows);
          foreach ($query->rows as $detail) {
               // TIME  Calculation ... 
              $diff_seconds = strtotime('now')-strtotime($detail['date_added']);
             //$duration = ((floor($diff_seconds/3600))>0?floor($diff_seconds/3600).' Hrs &':'').floor(($diff_seconds%3600)/60).'Mins';
              $duration = floor($diff_seconds/3600) .':'.floor(($diff_seconds%3600)/60);
              $dine_in_ids[$detail['dinein_table_id']]=array('live_id'=>$detail['live_order_id'],'lock'=>$detail['lock'],'duration'=>$duration);
          }
          return $dine_in_ids;
    }
     public function tabledetails($table_id) {
          $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store_tables WHERE `table_id`='".(int)$table_id."'");
          return $query->row['name'];
    }
    public function changeDineinedtable($live_id,$table_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "live_orders SET `dinein_table_id`='".(int)$table_id."' WHERE  `live_order_id`='".(int)$live_id."'");
        //echo "UPDATE " . DB_PREFIX . "live_orders SET `dinein_table_id`='".(int)$table_id."' WHERE  `live_order_id`='".(int)$live_id."'";
    }
    public function swapDineinedtable($table1,$liveid1,$table2,$liveid2) {
         $this->db->query("UPDATE " . DB_PREFIX . "live_orders SET `dinein_table_id`='".(int)$table1."',`lock`=0 WHERE  `live_order_id`='".(int)$liveid2."'");
         $this->db->query("UPDATE " . DB_PREFIX . "live_orders SET `dinein_table_id`='".(int)$table2."',`lock`=1 WHERE  `live_order_id`='".(int)$liveid1."'");
        // echo "UPDATE " . DB_PREFIX . "live_orders SET `dinein_table_id`='".(int)$table1."' WHERE  `live_order_id`='".(int)$liveid2."'";
        // echo "UPDATE " . DB_PREFIX . "live_orders SET `dinein_table_id`='".(int)$table2."' WHERE  `live_order_id`='".(int)$liveid1."'";
     }
     public function mergeDineinedtable($table1,$liveid1,$table2,$liveid2) {
         $order1 = $this->getLiveOrder($liveid1);
         $order2 = $this->getLiveOrder($liveid2);
         $data1=  unserialize($order1['data']);
         $data2=  unserialize($order2['data']);
         //print_r($data1);
         //print_r($data2);
         $new_data = $data2;
         $new_data['cart']=$data1['cart']+$data2['cart'];
        // print_r($new_data);
         $this->db->query("UPDATE " . DB_PREFIX . "live_orders SET `dinein_table_id`=0 WHERE  `live_order_id`='".(int)$liveid1."'");
         $this->db->query("UPDATE " . DB_PREFIX . "live_orders SET `data`='".$this->db->escape(serialize($new_data))."' WHERE  `live_order_id`='".(int)$liveid2."'");
         
     }
     
     public function removeDeadlock() {
          $this->db->query("UPDATE " . DB_PREFIX . "live_orders SET `lock`=0 WHERE `system_id`='".(int)(isset($this->session->data['system_id'])?$this->db->escape($this->session->data['system_id']):0)."'");
     }
     public function checkliveordertime($live_order_id) {
         $query = $this->db->query("SELECT `date_added` FROM " . DB_PREFIX . "live_orders WHERE live_order_id='".(int)$live_order_id."'");
         $timestamp=strtotime('now')-strtotime($query->row['date_added']);
         return $timestamp;
        
         
     } 
     
    public function getTableorderdetails($table_id){
        $query = $this->db->query("SELECT  DISTINCT *  FROM " . DB_PREFIX . "live_orders WHERE dinein_table_id='".(int)$table_id."' ORDER BY  	dinein_table_id DESC");
        return $query->row;
    }
    public function chktablelocked($table_id){
        $query = $this->db->query("SELECT  DISTINCT `lock`  FROM " . DB_PREFIX . "live_orders WHERE dinein_table_id='".(int)$table_id."' ORDER BY  live_order_id DESC");
        if(!empty($query->row) and $query->row['lock']==1){
            return TRUE;
        }else{
             return FALSE;
        }
        //return $query->row['lock'];
    }
    
    public function getLivesystems() {
         $query = $this->db->query("SELECT `last_active_time` FROM " . DB_PREFIX . "pos_client_system WHERE system_id <> '".(int)((isset($this->session->data['system_id'])?$this->session->data['system_id']:0))."' AND last_active_time > DATE_SUB(NOW(), INTERVAL 1 MINUTE) ");
         //return $query->rows;
         if(!empty($query->rows)){
             return TRUE;
         }else{
             return FALSE;
         }
    }
    public function getTakeoutliveOrders() {
        // $query = $this->db->query("SELECT  DISTINCT *  FROM " . DB_PREFIX . "live_orders WHERE `lock`=0 ORDER BY `date_added` ASC");
        $query = $this->db->query("SELECT *  FROM " . DB_PREFIX . "live_orders l WHERE l.`lock`=0 AND l.`dinein_table_id`=0 ".(isset($this->session->data['system_id'])?'AND l.system_id='.$this->session->data['system_id'].'':'')." ORDER BY l.`date_added` ASC");
        //echo "SELECT *  FROM " . DB_PREFIX . "live_orders l WHERE l.`lock`=0 AND l.`dinein_table_id`=0 ORDER BY l.`date_added` ASC";
        return $query->rows;
    }
    public function getCurrentLiveorders() {
        $query = $this->db->query("SELECT l.live_order_id,l.date_added,l.cart_total,l.system_id,s.system_name FROM " . DB_PREFIX . "live_orders l LEFT JOIN " . DB_PREFIX . "pos_client_system s ON(l.system_id=s.system_id) WHERE l.system_id<>0 ");//WHERE system_id<>0
        return $query->rows;
    }
    /*
     * get Liveorders to be printed
     */
    public function checkPrintLiveOrders() {
     
            $ordes_query =  $this->db->query("SELECT * FROM `" . DB_PREFIX . "live_orders`  WHERE  print_status=0 ORDER BY `live_order_id` ASC LIMIT 0 , 1");
            //$ordes_query->rows;
             if($ordes_query->num_rows) {
                 return $ordes_query->row['live_order_id'];
             }
             else {
                 return false;
             }
         
    }
    public function setLiveprintStatus($order_id,$print_system_id){
        $print_settings=  $this->posclient->getPosprintSettings('cart',$print_system_id);
        //print_r($print_settings);
        $this->db->query("UPDATE `" . DB_PREFIX . "live_orders` SET `print_status`=0 , `print_areas`='" . $this->db->escape(serialize($print_settings)) . "'  WHERE `live_order_id`=".(int)$order_id."");
    }
    public function updateLiveprintStatus($order_id,$print_area=array()){
         $this->db->query("UPDATE `" . DB_PREFIX . "live_orders` SET `print_status`=".(!empty($print_area)?0:1).", `print_areas`='" . $this->db->escape(serialize($print_area)) . "'   WHERE `live_order_id`=".(int)$order_id."");
    }
    private function unixtime() {

            $unixtime = time () ;
            // $unix_query = $this->db->query("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE TABLE_NAME = '" . DB_PREFIX . "order'");
            //  $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` AUTO_INCREMENT = ". $unixtime .'0');
            $unix_query =  $this->db->query("SELECT  order_id FROM `" . DB_PREFIX . "order`  WHERE order_id = ". $unixtime );
            if($unix_query->num_rows  ) {
                // Order ID Exist
                // Not Update AUTO_INCREMENT
                sleep(1);
                return $this->unixtime();
    }
            else {
                return $unixtime;
            }
        }
    }
?>