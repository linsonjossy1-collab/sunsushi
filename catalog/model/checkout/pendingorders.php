<?php 
class ModelCheckoutPendingorders extends Model {
	
      public function getPendingorders() {
		//$query =  $this->db->query("SELECT o.order_id,o.customer_id, o.firstname, o.lastname, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, o.total,o.date_added, o.requested_time,CONCAT(o.shipping_firstname, ' ', o.shipping_lastname) AS shipping_name,o.shipping_address_1,o.shipping_address_2,o.shipping_postcode,o.shipping_city,o.telephone,o.shipping_method,o.shipping_code  FROM `" . DB_PREFIX . "order` o  WHERE o.order_status_id = '1'  AND o.store_id = '" . (int)$this->config->get('pos_to_storeid') . "'  ORDER BY o.date_added DESC ");
                
                $sql = "SELECT o.order_id,o.customer_id, o.firstname, o.lastname,CONCAT(o.firstname, ' ', o.lastname) AS customer , (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, o.total,o.date_added, o.requested_time,CONCAT(o.shipping_firstname, ' ', o.shipping_lastname) AS shipping_name,o.shipping_address_1,o.shipping_address_2,o.shipping_postcode,o.shipping_city,o.telephone,o.shipping_method,o.shipping_code,o.system_id  FROM `" . DB_PREFIX . "order` o WHERE ";
                if($this->config->get('pos_main_orderstatuses')){
                    $sql .=" o.order_status_id IN (".implode(",",$this->config->get('pos_main_orderstatuses')).")  ORDER BY o.date_added DESC";
                }else{
                    $sql .=" o.order_status_id = '1'  ORDER BY o.date_added DESC";
                }
                //echo $sql;die();
                $query = $this->db->query($sql);
		return $query->rows;
	} 
        public function getPendingOrderDetails($order_id) {
            $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "edited_pending_orders WHERE order_id='".(int)$order_id."' ORDER BY id DESC LIMIT 1 ");
            return $query->row;
        }   
        public function gettotalPayedtotal($order_id){
            $query=$this->db->query("SELECT SUM(payed_total) as total  FROM " . DB_PREFIX . "edited_pending_orders WHERE order_id='".(int)$order_id."' AND payed_total>0");
            return $query->row['total'];
        }
         public function getKitchenorders() {
		//$query =  $this->db->query("SELECT o.order_id,o.customer_id, o.firstname, o.lastname, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, o.total,o.date_added, o.requested_time,CONCAT(o.shipping_firstname, ' ', o.shipping_lastname) AS shipping_name,o.shipping_address_1,o.shipping_address_2,o.shipping_postcode,o.shipping_city,o.telephone,o.shipping_method,o.shipping_code  FROM `" . DB_PREFIX . "order` o  WHERE o.order_status_id = '1'  AND o.store_id = '" . (int)$this->config->get('pos_to_storeid') . "'  ORDER BY o.date_added DESC ");
                
                $sql = "SELECT SUM(op.quantity) as qty,o.order_id,o.order_status_id ,o.date_added, o.requested_time FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON(op.order_id=o.order_id) WHERE ";
                if($this->config->get('pos_kitchen_orderstatuses')){
                    $order_status = $this->config->get('pos_kitchen_orderstatuses');
                    //print_r($order_status);
                     //key(array_reverse($order_status));
                     unset($order_status[key(array_reverse($order_status,true))]);
                    $sql .=" o.order_status_id IN (".implode(",",$order_status).")  AND o.store_id = '" . (int)$this->config->get('pos_to_storeid') . "' GROUP BY o.order_id ASC  ORDER BY o.date_added ASC";
                }else{
                    $sql .=" o.order_status_id = '1'  AND o.store_id = '" . (int)$this->config->get('pos_to_storeid') . "' GROUP BY o.order_id ASC ORDER BY o.date_added ASC";
                }
                //echo $sql;die();
                $query = $this->db->query($sql);
		return $query->rows;
	} 
}
?>