<?php
class ModelDrivermanagementDriverlist extends Model {
	public function addDriver($data) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($data['username']) . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', firstname = '" . $this->db->escape($data['name']) . "', user_group_id = '15', status = '1', date_added = NOW()");
                
		$this->db->query("INSERT INTO " . DB_PREFIX . "driver SET user_id='" . (int)$this->db->getLastId() . "',driver_name = '" . $this->db->escape($data['name']) . "', driver_address = '" . $this->db->escape(strip_tags($data['address'])) . "', driver_mobile = '" . $this->db->escape($data['driver_mobile']) . "', driver_telephone = '" . $this->db->escape($data['driver_telephone']) . "',driver_clock_time = '" . $this->db->escape(serialize($data['drivertimes'])) . "'");
	
	}
	
	public function editDriver($driver_id, $data) {
                $this->db->query("UPDATE `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($data['username']) . "', firstname = '" . $this->db->escape($data['name']) . "', user_group_id = '15', status = '1' WHERE user_id = '" . (int)$data['user_id'] . "'");
		
		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "user` SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE user_id = '" . (int)$data['user_id'] . "'");
		}
                
		$this->db->query("UPDATE " . DB_PREFIX . "driver SET driver_name = '" . $this->db->escape($data['name']) . "', driver_address = '" . $this->db->escape(strip_tags($data['address'])) . "', driver_mobile = '" . $this->db->escape($data['driver_mobile']) . "',  driver_telephone = '" . $this->db->escape($data['driver_telephone']) . "',driver_clock_time = '" . $this->db->escape(serialize($data['drivertimes'])) . "' WHERE driver_id = '" . (int)$driver_id . "'");
	
	}
	
	public function deleteDriver($driver_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "driver WHERE driver_id = '" . (int)$driver_id . "'");
		
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
	
}