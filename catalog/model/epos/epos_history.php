<?php
class ModelEposEposHistory extends Model {
    
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
        $data['product_name'] = str_replace('*','',$data['product_name']);
        $sql = $this->db->query("SELECT adhoc_priority_id,adhoc_product_id FROM " . DB_PREFIX . "adhoc_product_history WHERE adhoc_product_name ='" .$this->db->escape($data['product_name']) ."' AND adhoc_product_price = " .$data['price']."");                         
        if ($sql->row) {            
            $this->db->query("UPDATE " . DB_PREFIX . "adhoc_product_history SET adhoc_priority_id = " .((int) $sql->row['adhoc_priority_id'] + 1) ." WHERE adhoc_product_id =" .(int) $sql->row['adhoc_product_id']."");          
        } else{
            $this->db->query("INSERT INTO " . DB_PREFIX . "adhoc_product_history SET adhoc_product_name = '" .$this->db->escape($data['product_name']) ."',adhoc_product_price = " .$data['price'].",adhoc_priority_id=1");
        }                                   
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'settings_modified'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'settings_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");
    }
    public function insertadhockdiscount($data){
        $sql = $this->db->query("SELECT adhoc_discount_id,adhocdiscount_priority_id FROM " . DB_PREFIX . "adhoc_discount_history WHERE LOWER(adhoc_discount_name) =LOWER('" .$this->db->escape($data['discount_name']) ."') AND adhoc_discount_amt ='" .$data['discount_amount']. "' AND adhoc_discount_type = '" .$data['discount_type']."'");                        
        if ($sql->row) {                 
            $this->db->query("UPDATE " . DB_PREFIX . "adhoc_discount_history SET adhocdiscount_priority_id = " .((int) $sql->row['adhocdiscount_priority_id'] + 1) ." WHERE adhoc_discount_id =" .(int) $sql->row['adhoc_discount_id']. "");            
        } else{              
            $this->db->query("INSERT INTO " . DB_PREFIX . "adhoc_discount_history SET adhoc_discount_name = '" .$this->db->escape($data['discount_name']) ."',adhoc_discount_amt = '" .$data['discount_amount']."',adhoc_discount_type = '" .$data['discount_type'] ."',group_id = 0,group_name = '' ,adhoc_item_id=0,adhocdiscount_priority_id=1");
        } 
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'settings_modified'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'settings_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");
    } 
    public function getadhocproducts() {        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "adhoc_product_history ORDER BY adhoc_priority_id DESC");
        return $query->rows;
    }
    public function getComments() {        
        $query = $this->db->query("SELECT ch.comment,ch.customer_id,ch.product_id,ch.is_profile_comment FROM " . DB_PREFIX . "comment_history ch WHERE ch.comment!=''  ORDER BY priority_id DESC");
        return $query->rows;
    }    
    public function getAdhocDiscount() {        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "adhoc_discount_history  ORDER BY adhocdiscount_priority_id DESC");
        return $query->rows;
    }
     public function insertprodtcommentdata($data){
         $query = $this->db->query("SELECT cmd_id,priority_id FROM " . DB_PREFIX . "comment_history WHERE comment ='" .$this->db->escape($data['comment']) ."' AND customer_id=".(int)$data['customer_id']." AND product_id=".(int)$data['product_id']."");        
        if ($query->row) {            
             $this->db->query("UPDATE " . DB_PREFIX . "comment_history SET priority_id = " .((int) $query->row['priority_id'] + 1) ." WHERE cmd_id =" .(int) $query->row['cmd_id']."");            
        } else{               
            $this->db->query("INSERT INTO " . DB_PREFIX . "comment_history SET comment = '" .$this->db->escape($data['comment']) ."',priority_id=1,customer_id=".(int)$data['customer_id'].",product_id=".(int)$data['product_id']."");          
        }
    }  
    /*
     * Table Structure
     */
    
    public function eposCreateTable(){
      $this->db->query("CREATE TABLE IF NOT EXISTS  " . DB_PREFIX . "adhoc_discount_history (
          `adhoc_discount_id` int(11) NOT NULL AUTO_INCREMENT,
          `adhoc_discount_name` varchar(100)  NOT NULL,
          `adhoc_discount_amt` decimal(16,0) NOT NULL,
          `adhoc_discount_type` varchar(10)  NOT NULL,
          `adhocdiscount_priority_id` int(11) NOT NULL,
          `group_id` int(11) NOT NULL,
          `group_name` varchar(100)  NOT NULL,
          `adhoc_item_id` int(11) NOT NULL,
           PRIMARY KEY (`adhoc_discount_id`))"); 
      $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "adhoc_product_history (
          `adhoc_product_id` int(11) NOT NULL AUTO_INCREMENT,
          `adhoc_priority_id` int(11) NOT NULL,
          `adhoc_product_name` varchar(100)  NOT NULL,
          `adhoc_product_price` decimal(15,2) NOT NULL,
           PRIMARY KEY (`adhoc_product_id`))");   
      $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "comment_history (
           `cmd_id` int(11) NOT NULL AUTO_INCREMENT,
           `comment` text  NOT NULL,
           `priority_id` int(11) NOT NULL,
            PRIMARY KEY (`cmd_id`))");
    }
    
    /*
     * Table Structure
     */
    
    /*SANU*/
     public function getDiscounts() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pos_discounts");
        return $query->rows;
    }
    
    public function getAdhocdiscountGroups() {
        $discount_groups = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pos_discount_groups");
        if($query->num_rows){
            foreach ($query->rows as $group) {
                $cat_qry   = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "pos_discount_groups_to_category WHERE group_id='".$group['group_id']."'");
                $prodt_qry = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "pos_discount_groups_to_product WHERE group_id='".$group['group_id']."'");
                $prodts = array();
                foreach($prodt_qry->rows as $product){
                    $prodts[]=$product['product_id'];
                }
                $discount_groups[] = array(
                    'group_id'=>$group['group_id'],
                    'group_name'=>$group['group_name'],
                    'category'=>$cat_qry->rows,
                    'products'=>$prodts,
                );
            }
        }
        return $discount_groups;;
        
    }
    public function getNotificationmessages() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "predefined_notifications");		
        return $query->rows;
    }
    /*SANU*/
 }
 ?>