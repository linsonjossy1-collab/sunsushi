<?php 
class ModelSettingPossetting extends Model {
	public function getSetting($group, $store_id = 0) { 
		$data = array(); 
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
		
		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$data[$result['key']] = $result['value'];
			} else {
				$data[$result['key']] = unserialize($result['value']);
			}
		}

		return $data;
	}
        
        public function setprintorderids($order_id){
            
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND `key` = 'print_onlineorderids'");
            if(!empty($query->row)){
                 $orderids = $this->config->get('print_onlineorderids');
                 $orderids[]=$order_id;              
                $this->db->query("UPDATE " . DB_PREFIX . "setting SET  `value` = '" . $this->db->escape(serialize($orderids)) . "', serialized = '1' WHERE `key` = 'print_onlineorderids'");
            }else{
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$this->config->get('config_store_id') . "', `group` = 'print', `key` = 'print_onlineorderids', `value` = '" . $this->db->escape(serialize(array($order_id))) . "', serialized = '1'");
            }
            
            
        }
        
       public function updateprintorderids($orderid) {
           
           $print_details = $this->getSetting('print');
           $new_ids = array();
           if(!empty($print_details) and !empty($print_details['print_onlineorderids'])){ 
               foreach($print_details['print_onlineorderids'] as $order_id){
                  if($order_id!=$orderid){
                    $new_ids[]=$order_id;      
                  } 
               }
           }
           //$ids=  array_shift($orderids);
           $this->db->query("UPDATE " . DB_PREFIX . "setting SET  `value` = '" . $this->db->escape(serialize($new_ids)) . "', serialized = '1' WHERE `key` = 'print_onlineorderids'");
       } 
       
        public function checkProductmodified($date_modified) {
        //$date_modified = $this->getLastProductModified();
        $query = $this->db->query("SELECT `product_id` FROM " . DB_PREFIX . "product WHERE `date_modified` > '" . $date_modified . "' ");
        if (!empty($query->rows)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkCategorymodified($date_modified) {
        //$date_modified = $this->checkCategorymodified();
        $query = $this->db->query("SELECT `category_id` FROM " . DB_PREFIX . "category WHERE `date_modified` > '" . $date_modified . "'");
        if (!empty($query->rows)) {
            return true;
        } else {
            return false;
        }
    }

    public function dumbData($table_list, $table) {
        $query = $this->db->query("SELECT  " . implode(',', $table_list) . "  FROM `" . $table . "`");
        return $result = $query->rows;
    }

    public function getFileds($table) {
        $query = $this->db->query("SHOW FIELDS FROM `" . $table . "`");
        return $result = $query->rows;
    }
    public function validatepin($pincode,$permission){
        $user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE pincode = '" . $this->db->escape(base64_encode($pincode)) . "'  AND status = '1'");

        if ($user_query->num_rows) {
            $user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int) $user_query->row['user_group_id'] . "'");
             $permissions = unserialize($user_group_query->row['permission']);
             if(in_array($permission, $permissions['modify'])){
                 $this->session->data['adhoc_applied_user']=(int) $user_query->row['user_id'];
                 return 1;
             }else{
                 return 2;
             }
        }else{
            return FALSE;
        }
    }
    
    
}
?>