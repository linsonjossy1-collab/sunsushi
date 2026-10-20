<?php
class ModelCatalogDrivermanagement extends Model {
	public function addDriver($data) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($data['username']) . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', firstname = '" . $this->db->escape($data['name']) . "', user_group_id = '15', status = '1', date_added = NOW()");
                
		$this->db->query("INSERT INTO " . DB_PREFIX . "driver SET user_id='" . (int)$this->db->getLastId() . "',driver_name = '" . $this->db->escape($data['name']) . "', driver_address = '" . $this->db->escape(strip_tags($data['address'])) . "', driver_mobile = '" . $this->db->escape($data['driver_mobile']) . "', driver_telephone = '" . $this->db->escape($data['driver_telephone']) . "',driver_clock_time = '" . $this->db->escape(serialize($data['drivertimes'])) . "'");
	
		//$this->cache->delete('product');
	}
	
	public function editDriver($driver_id, $data) {
                $this->db->query("UPDATE `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($data['username']) . "', firstname = '" . $this->db->escape($data['name']) . "', user_group_id = '15', status = '1' WHERE user_id = '" . (int)$data['user_id'] . "'");
		
		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "user` SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE user_id = '" . (int)$data['user_id'] . "'");
		}
                
		$this->db->query("UPDATE " . DB_PREFIX . "driver SET driver_name = '" . $this->db->escape($data['name']) . "', driver_address = '" . $this->db->escape(strip_tags($data['address'])) . "', driver_mobile = '" . $this->db->escape($data['driver_mobile']) . "',  driver_telephone = '" . $this->db->escape($data['driver_telephone']) . "',driver_clock_time = '" . $this->db->escape(serialize($data['drivertimes'])) . "' WHERE driver_id = '" . (int)$driver_id . "'");
	
		//$this->cache->delete('product');
	}
	
	public function deleteDriver($driver_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "driver WHERE driver_id = '" . (int)$driver_id . "'");
		
		//$this->cache->delete('product');
	}
	
	public function getDriver($driver_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "driver r LEFT JOIN `" . DB_PREFIX . "user` u ON (r.user_id = u.user_id) WHERE  r.driver_id = '" . (int)$driver_id . "'");
		
		return $query->row;
	}

	public function getDrivers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "driver r";																																					  
		
		$sort_data = array(
			'r.driver_name'
			
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY r.driver_name";	
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
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}																																							  
																																							  
		$query = $this->db->query($sql);																																				
		
		return $query->rows;	
	}
	
	public function getTotalDrivers() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "driver");
		
		return $query->row['total'];
	}
	
	public function getTotalfeedbacksAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "feedback WHERE status = '0'");
		
		return $query->row['total'];
	}	
        
        public function assignDrivertodeliver($data){
            $qry=$this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "deliverys WHERE order_id = '" . (int)$data['order_id'] . "'");
            if(!empty($qry->row)){
            $this->db->query("DELETE FROM " . DB_PREFIX . "deliverys WHERE order_id = '" . (int)$data['order_id'] . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "driver_petty_cash WHERE delivery_id = '" . (int)$qry->row['delivery_id'] . "' AND (dpc_trans_type='C' OR dpc_trans_type='D')");
            }
            
            
            $this->db->query("INSERT INTO `" . DB_PREFIX . "deliverys` SET order_id = '" . (int)$data['order_id'] . "', driver_id = '" .(int)$data['driver_id'] . "', time_assigned = '" . ($data['time']=='asap'?'NOW()':$data['time']) . "', paid = '". ($data['payment']=='cod'?'0':'1')."', total = '".$this->db->escape($data['total'])."', driver_commission= '".$this->db->escape($data['commision'])."'");
            
            $delivery_id=$this->db->getLastId() ;  
            //Petty Cash Table insertion
            //commisiion
            //chk reassigned or updation(Not checked yet)
        
           
             //order total
             $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . (int)$data['driver_id'] . "', dpc_time =NOW(), dpc_trans_type = 'D', delivery_id = '". (int)$delivery_id."', dpc_cr = '".$this->db->escape($data['total'])."'");
              //commission
             $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . (int)$data['driver_id'] . "', dpc_time =NOW(), dpc_trans_type = 'C', delivery_id = '". (int)$delivery_id."', dpc_cr = '".$this->db->escape($data['commision'])."'");
            //die();
             
        }
        
      public function getAssignedDriverOrders($user_id){
          $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "deliverys de LEFT JOIN `" . DB_PREFIX . "driver` dr ON (de.driver_id = dr.driver_id) WHERE  dr.user_id = '" . (int)$user_id . "' ORDER BY de.time_assigned ASC");
     
          return $query->rows;
      }
      
      public function getDeliveryDetails($order_id){
          $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "deliverys de  WHERE  de.order_id = '" . (int)$order_id . "'");
          return $query->row;
          
      }
      
      public function driverclockin($driver_id){
          $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_clock_inout` SET driver_id = '" . (int)$driver_id . "', driver_clock_in = NOW()");
          return $this->db->getLastId() ;  
      }
      
      public function driverclockout($driver_id){
          //$this->db->query("UPDATE `" . DB_PREFIX . "driver_clock_inout` SET  driver_clock_out = NOW() WHERE  driver_clock_id='".(int)$driver_clock_id."'");
          //return $this->db->getLastId() ;
           $this->db->query("UPDATE `" . DB_PREFIX . "driver_clock_inout` SET  driver_clock_out = NOW() WHERE  driver_id	='".(int)$driver_id."' ORDER BY driver_clock_id DESC LIMIT 1 ");
      }
      
      public function updatedeliverydetails($data){
           $this->db->query("UPDATE `" . DB_PREFIX . "deliverys` SET  time_delivered = '".$this->db->escape($data['driver_delivery_time'])."', cash_received= '".$this->db->escape($data['driver_amt_recieved'])."', delivery_comment= '".$this->db->escape($data['driver_comments'])."' WHERE delivery_id='".(int)$data['driver_delivery_id']."'");
       //  $this->db->query("DELETE FROM " . DB_PREFIX . "driver_petty_cash WHERE delivery_id = '" . (int)$delivery_id . "' AND (dpc_trans_type=='C' OR dpc_trans_type=='D')");
          $this->db->query("UPDATE `" . DB_PREFIX . "driver_petty_cash` SET dpc_dr = '" . $this->db->escape($data['driver_amt_recieved']) . "' WHERE delivery_id='".(int)$data['driver_delivery_id']."' AND dpc_trans_type ='D'");
      //die();    
           //petty cash insertion
      }
      
       public function addPettycash($data){
          if($data['dpc_trans_type']=='S'){
          $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . (int)$data['checkin_driver_id'] . "', dpc_time =NOW(), dpc_trans_type = 'S', delivery_id = '0', dpc_cr = '".$this->db->escape($data['pettycash_amount'])."'");
          }else{
              $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . (int)$data['checkin_driver_id'] . "', dpc_time =NOW(), dpc_trans_type = 'E', delivery_id = '0', dpc_dr = '".$this->db->escape($data['pettycash_amount'])."'");
          }
          return $this->db->getLastId() ; 
      }
      
       public function getDriverPettycashDetails($driver_id){
          //check with today store start time
          $qry=$this->db->query("SELECT  * FROM " . DB_PREFIX . "driver_petty_cash WHERE driver_id = '" . (int)$driver_id . "' AND dpc_time>= CURDATE()");   
          return $qry->rows;
          
      }
      
       public function getDriverPettycashTotals($driver_id){
          //check with today store start time
          $qry=$this->db->query("SELECT  SUM(dpc_cr) as credit,SUM(dpc_dr) as debiit FROM " . DB_PREFIX . "driver_petty_cash WHERE driver_id = '" . (int)$driver_id . "' AND dpc_time>= CURDATE()");   
          return $qry->row;
          
      }
      
       public function getClockedinDrivers(){
          //check with today store start time
          $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "driver_clock_inout dc LEFT JOIN `" . DB_PREFIX . "driver` dr ON (dc.driver_id = dr.driver_id) WHERE  dc.driver_clock_in >= CURDATE() AND dc.driver_clock_out <=NOW() ");
     
          return $query->rows;
          
      }
      
      
      public function isDriverCheckedin($driver_id){
          //check with today store start time
          $status=FALSE;
          $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "driver_clock_inout  dc  WHERE dc.driver_id='".$driver_id."' AND  dc.driver_clock_in >= CURDATE() AND dc.driver_clock_out IS NULL ORDER BY driver_clock_id DESC LIMIT 1");
     
          if(!empty($query->row)){
              $status=TRUE;
          }
          return $status;
      }
      public function getDriverDetails($user_id){
          $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user u LEFT JOIN `" . DB_PREFIX . "driver` d ON (u.user_id = d.user_id) WHERE  u.user_id = '" . (int)$user_id . "'");
	  return $query->row;
          
      }
      
}
?>