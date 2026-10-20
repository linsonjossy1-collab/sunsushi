<?php
class ModelToolEposHistory extends Model {
     
    public function insertcommentdata($data){
        if(isset($data['customer_id'])){ 
            $query = $this->db->query("SELECT cmd_id,priority_id FROM " . DB_PREFIX . "comment_history WHERE comment ='" .$this->db->escape($data['comment']) ."' AND customer_id='".$data['customer_id']."' ");        
            if ($query->row) {            
                 $this->db->query("UPDATE " . DB_PREFIX . "comment_history SET priority_id = " .((int) $query->row['priority_id'] + 1) .",customer_id='".(int)$data['customer_id']."',is_profile_comment='".(int)(isset($data['is_profile_comment'])?$data['is_profile_comment']:0)."' WHERE cmd_id =" .(int) $query->row['cmd_id']."");            
            } else{               
                $this->db->query("INSERT INTO " . DB_PREFIX . "comment_history SET comment = '" .$this->db->escape($data['comment']) ."',priority_id=1,customer_id='".(int)$data['customer_id']."',is_profile_comment='".(int)(isset($data['is_profile_comment'])?$data['is_profile_comment']:0)."'");          
            }
        }else{ 
            $query = $this->db->query("SELECT cmd_id,priority_id FROM " . DB_PREFIX . "comment_history WHERE comment ='" .$this->db->escape($data['comment']) ."'");        
            if ($query->row) {            
                 $this->db->query("UPDATE " . DB_PREFIX . "comment_history SET priority_id = " .((int) $query->row['priority_id'] + 1) ." WHERE cmd_id =" .(int) $query->row['cmd_id']."");            
            } else{               
                $this->db->query("INSERT INTO " . DB_PREFIX . "comment_history SET comment = '" .$this->db->escape($data['comment']) ."',priority_id=1");          
            }
        }
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'settings_modified'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'settings_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");
    }
    public function insertahocdata($data){        
          $sql = $this->db->query("SELECT adhoc_priority_id,adhoc_product_id FROM " . DB_PREFIX . "adhoc_productpopup WHERE adhoc_product_name ='" .$this->db->escape($data['product_name']) ."' AND adhoc_product_price = " .$data['price']."");                         
          if ($sql->row) {            
               $this->db->query("UPDATE " . DB_PREFIX . "adhoc_productpopup SET adhoc_priority_id = " .((int) $sql->row['adhoc_priority_id'] + 1) ." WHERE adhoc_product_id =" .(int) $sql->row['adhoc_product_id']."");          
          } else{
               $this->db->query("INSERT INTO " . DB_PREFIX . "adhoc_productpopup SET adhoc_product_name = '" .$this->db->escape($data['product_name']) ."',adhoc_product_price = " .$data['price'].",adhoc_priority_id=1");
          }        
          $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'settings_modified'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'settings_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");
    }
    public function insertadhockdiscount($data){   
        $sql = $this->db->query("SELECT adhoc_discount_id,adhocdiscount_priority_id FROM " . DB_PREFIX . "adhoc_discountpopup WHERE adhoc_discount_name ='" .$this->db->escape($data['discount_name']) ."' AND adhoc_discount_amt = " .$data['discount_amount']. " AND adhoc_discount_type = '" .$data['discount_type']."' AND group_id = " .$data['group_id']. "");                        
        if ($sql->row) {                 
            $this->db->query("UPDATE " . DB_PREFIX . "adhoc_discountpopup SET adhocdiscount_priority_id = " .((int) $sql->row['adhocdiscount_priority_id'] + 1) ." WHERE adhoc_discount_id =" .(int) $sql->row['adhoc_discount_id']. "");            
        } else{              
            $this->db->query("INSERT INTO " . DB_PREFIX . "adhoc_discountpopup SET adhoc_discount_name = '" .$this->db->escape($data['discount_name']) ."',adhoc_discount_amt = " .$data['discount_amount'].",adhoc_discount_type = '" .$data['discount_type'] ."',group_id = " .$data['group_id'].",group_name = '" .$this->db->escape($data['group_name']). "' ,adhoc_item_id=".$data['discount_pdt_id'].",adhocdiscount_priority_id=1");
        } 
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'settings_modified'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'settings_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");
    } 
    public function getadhocproducts() {        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "adhoc_productpopup ORDER BY adhoc_priority_id DESC");
        return $query->rows;
    }
    public function getComments() {        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "comment_history  ORDER BY priority_id DESC");
        return $query->rows;
    }    
    public function getAdhocDiscount() {        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "adhoc_discountpopup  ORDER BY adhocdiscount_priority_id DESC");
        return $query->rows;
    }
   
 }
 ?>