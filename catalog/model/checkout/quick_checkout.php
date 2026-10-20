<?php

class ModelCheckoutQuickCheckout extends Model {
    
    public function getPosquickcheckoutitems() {
        $query = $this->db->query("SELECT id,name FROM `" . DB_PREFIX . "pos_quick_checkout_items` ORDER BY id ASC");
        return $query->rows;
        
    }
    
     public function addQuickckeckoutItem($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "pos_quick_checkout_items` SET `name`='".$this->db->escape($data['name'])."'");
        return $this->db->getLastId();
    }
     public function updateQuickckeckoutItem($id) {
        $data = $this->session->data;
        unset($data['language']);
        unset($data['currency']);
        unset($data['browser_type']);
        unset($data['user_id']);
        unset($data['token']);
        unset($data['waklin_customer_id']);
        unset($data['shipping_methods']);//if needed
        unset($data['payment_methods']);//if needed 
        unset($data['quick_btn_id']);//if needed 
        $this->db->query("UPDATE `" . DB_PREFIX . "pos_quick_checkout_items` SET `data`='".$this->db->escape(serialize($data))."'  WHERE `id`='".(int) $id."'");
        //return $this->db->getLastId();
     }
     public function deleteQuickckeckoutItem($id) {
         $this->db->query("DELETE FROM `" . DB_PREFIX . "pos_quick_checkout_items` WHERE `id`='".(int) $id."'");
     }
     public function deleteUnassigneditems() {
         $this->db->query("DELETE FROM `" . DB_PREFIX . "pos_quick_checkout_items` WHERE `data`=''");
     }
     
     public function getItemdetails($id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_quick_checkout_items` WHERE `id`='".(int) $id."'");
        return $query->row;
     }
     public function createItemdata($id){
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_quick_checkout_items` WHERE `id`='".(int) $id."'");
        $details = $query->row;
        if(!empty($details)){
            $data_details = unserialize($details['data']);
            foreach ($data_details as $key => $data) {
                $this->session->data[$key] = $data;
            }
        }
        
     }
}

?>