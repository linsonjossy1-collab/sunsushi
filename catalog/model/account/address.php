<?php
class ModelAccountAddress extends Model {
	public function addAddress($data) {
                 if(isset($data['customer_id'])) {
                $customer_id = (int)$data['customer_id'];
            }
            else {
                $customer_id = (int)$this->customer->getId();
            } 
		$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . $customer_id . "', firstname = '" . $this->db->escape((isset($data['firstname']))?$data['firstname']:'') . "', lastname = '" . $this->db->escape(isset($data['lastname'])?$data['lastname']:'') . "', company = '" . $this->db->escape(isset($data['company'])?$data['company']:'') . "', company_id = '" . $this->db->escape(isset($data['company_id']) ? $data['company_id'] : '') . "', tax_id = '" . $this->db->escape(isset($data['tax_id']) ? $data['tax_id'] : '') . "', address_1 = '" . $this->db->escape(isset($data['address_1'])?$data['address_1']:'') . "', address_2 = '" . $this->db->escape(isset($data['address_2'])?$data['address_2']:'') . "', postcode = '" . $this->db->escape(isset($data['postcode'])?$data['postcode']:'') . "', city = '" . $this->db->escape(isset($data['city'])?$data['city']:'') . "', zone_id = '" . (int)(isset($data['zone_id'])?$data['zone_id']:$this->config->get('config_zone_id')) . "', country_id = '" . (int)(isset($data['country_id'])?$data['country_id']:$this->config->get('config_country_id')) . "'");
		
		$address_id = $this->db->getLastId();
		
		if (!empty($data['default'])) {
                    $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
                    $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "',date_modified = NOW() WHERE customer_id = '" . (int)$customer_id . "'");
		}
		
		return $address_id;
	}
	
	public function editAddress($address_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "address SET firstname = '" . $this->db->escape(isset($data['firstname'])?$data['firstname']:'') . "', lastname = '" . $this->db->escape(isset($data['lastname'])?$data['lastname']:'') . "', company = '" . $this->db->escape(isset($data['company'])?$data['company']:'') . "', company_id = '" . $this->db->escape(isset($data['company_id']) ? $data['company_id'] : '') . "', tax_id = '" . $this->db->escape(isset($data['tax_id']) ? $data['tax_id'] : '') . "', address_1 = '" . $this->db->escape(isset($data['address_1'])?$data['address_1']:'') . "', address_2 = '" . $this->db->escape(isset($data['address_2'])?$data['address_2']:'') . "', postcode = '" . $this->db->escape(isset($data['postcode'])?$data['postcode']:'') . "', city = '" . $this->db->escape(isset($data['city'])?$data['city']:'') . "', zone_id = '" . (int)(isset($data['zone_id'])?$data['zone_id']:$this->config->get('config_zone_id')) . "', country_id = '" .(int)(isset($data['country_id'])?$data['country_id']:$this->config->get('config_country_id')). "' WHERE address_id  = '" . (int)$address_id . "' AND customer_id = '" . (int)(isset($data['customer_id'])?$data['customer_id']:$this->customer->getId()) . "'");
	
		if (!empty($data['default'])) {
                    $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
                    $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "',date_modified = NOW() WHERE customer_id = '" . (int)(isset($data['customer_id'])?$data['customer_id']:$this->customer->getId()) . "'");
		}
	}
	
	public function deleteAddress($address_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	}	
	
	public function getAddress($address_id) {
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		
		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}		
			
			$address_data = array(
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'company_id'     => $address_query->row['company_id'],
				'tax_id'         => $address_query->row['tax_id'],
				'address_1'      => str_replace('\n',",",$address_query->row['address_1']),//$address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
			
			return $address_data;
		} else {
			return false;	
		}
	}
	
	public function getAddresses() {
		$address_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	
		foreach ($query->rows as $result) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$result['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$result['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}		
		
			$address_data[$result['address_id']] = array(
				'address_id'     => $result['address_id'],
				'firstname'      => $result['firstname'],
				'lastname'       => $result['lastname'],
				'company'        => $result['company'],
				'company_id'     => $result['company_id'],
				'tax_id'         => $result['tax_id'],				
				'address_1'      => str_replace('\n',",",$result['address_1']),//$result['address_1'],
				'address_2'      => $result['address_2'],
				'postcode'       => $result['postcode'],
				'city'           => $result['city'],
				'zone_id'        => $result['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $result['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		}		
		
		return $address_data;
	}	
	
	public function getTotalAddresses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	
		return $query->row['total'];
	}
         public function getAddressByID($address_id) {
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");
		
		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}		
			
			$address_data = array(
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
                                'address'        => $address_query->row['address_1'],
				'address_1'      => str_replace('\n',",",$address_query->row['address_1']),//$address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
                                'customer_id'    => $address_query->row['customer_id'],
                                'country_id'     => $address_query->row['country_id'],
                                'zone_id'        => $address_query->row['zone_id']
			);
			
			return $address_data;
		} else {
			return false;	
		}
	}
}
?>