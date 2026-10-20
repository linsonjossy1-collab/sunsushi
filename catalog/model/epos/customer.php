<?php
class ModelEposCustomer extends Model {
						
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT customer_id, store_id, firstname, lastname, email, telephone,phone_number1,phone_number2,address_id, customer_group_id, loaltycard, store_ac_no, online_customer_id,is_blacklisted FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		
		return $query->row;
	}
	public function getCustomers() {
		$sql = "SELECT * FROM " . DB_PREFIX . "customer WHERE  customer_id <>'".(int)$this->config->get('pos_walkin_customer_id')."'";
		$query = $this->db->query($sql);
		
		return $query->rows;	
	}
        public function getAddresses($customer_id) {
		$address_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
	
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
				'address_1'      => $result['address_1'],//str_replace('\n',",",$result['address_1']),//
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
				'address_format' => $address_format,
                                'delivery_charge'=> $result['delivery_charge'],
			);
		}		
		
		return $address_data;
	}
        public function getOrders($customer_id,$start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
		
		if ($limit < 1) {
			$limit = 1;
		}	
		
		$query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value,o.order_status_id  FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$customer_id . "' AND o.order_status_id > '0' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);	
		return $query->rows;
	}
        public function customersCount($date) {
            $data = array();
            if($date!=''){  
                 $query = $this->db->query("SELECT count(*) as cnt  FROM `" . DB_PREFIX . "customer` WHERE date_modified>'".date('Y-m-d H:i:s',$date)."'");	
            }else{
                $query = $this->db->query("SELECT count(*) as cnt  FROM `" . DB_PREFIX . "customer`");	
            }
            
            $data['customers'] =  $query->row['cnt'];
            //$query = $this->db->query("SELECT count(*) as cnt  FROM `" . DB_PREFIX . "address`");
            //$data['address'] =  $query->row['cnt'];
            return $data;
        }
        public function lastOrderModified() {
            $query = $this->db->query("SELECT MAX(date_modified) last_modified FROM `" . DB_PREFIX . "order` ");
            return $query->row['last_modified'];
        }
        
  	public function getBalance($customer_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
	
		return $query->row['total'];
  	}	
		
  	public function getRewardPoints($customer_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
	
		return $query->row['total'];	
  	}
        public function getCustomerdata($page,$date) {
            $this->load->model('account/order');
            $limit =100;
            $limitstrat = $page * $limit;
            if ($limitstrat > 0 ) {
                $limitstrat += 1;
            }
             if($date!=''){ 
                $sql = "SELECT customer_id FROM " . DB_PREFIX . "customer WHERE  customer_id <>'".(int)$this->config->get('pos_walkin_customer_id')."' AND date_modified>'".date('Y-m-d H:i:s',$date)."' LIMIT $limitstrat, $limit";
            }else{
                $sql = "SELECT customer_id FROM " . DB_PREFIX . "customer WHERE  customer_id <>'".(int)$this->config->get('pos_walkin_customer_id')."' LIMIT $limitstrat, $limit";
            }
            
            $query = $this->db->query($sql);
            $data = array();
            if(!empty($query->rows)){
                foreach ($query->rows as $customer) {
                    $json = $this->getCustomer($customer['customer_id']);
                    $json['addresses'] = $this->getAddresses($customer['customer_id']);
                    $json['reward'] = $this->getRewardPoints($customer['customer_id']);
                    $json['orders'] = array();
                    /* commented in v35 ,in epos Customer orders taken from order table  
                    $results = $this->getOrders($customer['customer_id']);
                    foreach ($results as $result) {
                        $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
                        $voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);
                        $products = $this->model_account_order->getOrderProducts($result['order_id']);
                        $product_names = array();
                        foreach ($products as $product){
                            $product_names[] = $product['name'];
                        }
                        $json['orders'][] = array(
                            'order_id' => $result['order_id'],
                            'order_status_id' => $result['order_status_id'],
                            'productsname' => implode(',', array_unique($product_names)),
                            'name' => $result['firstname'] . ' ' . $result['lastname'],
                            'status' => $result['status'],
                            'date_added' => $result['date_added'],//date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                            'products' => ($product_total + $voucher_total),
                            'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                            'shipping_address'=>  isset($result['shipping_address'])?$result['shipping_address']:''
                        );
                    }
                    */
                    $data[]=$json;
                }
            }
            return $data;	
	}
        public function getTodaysRewardPoints($data = array()) { 

		//$sql = "SELECT cr.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, SUM(cr.points) AS points, COUNT(o.order_id) AS orders, SUM(o.total) AS total FROM " . DB_PREFIX . "customer_reward cr LEFT JOIN `" . DB_PREFIX . "customer` c ON (cr.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (cr.order_id = o.order_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cr.customer_id <>" . $this->config->get('pos_walkin_customer_id') . " ";
                $sql = "SELECT cr.customer_id,cr.date_added,cr.description, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, cr.points, cr.order_id, o.total AS total FROM " . DB_PREFIX . "customer_reward cr LEFT JOIN `" . DB_PREFIX . "customer` c ON (cr.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (cr.order_id = o.order_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cr.customer_id <>" . $this->config->get('pos_walkin_customer_id') . " ";
		
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND cr.date_added >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND cr.date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
				
                $sql .= " ORDER BY points DESC";
		//$sql .= " GROUP BY cr.customer_id ORDER BY points DESC";
				
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                $this->log->write(print_r($sql,true));
		$query = $this->db->query($sql);
	
		return $query->rows;
	}
        public function getcustomerComments($customer_id) {        
            $query = $this->db->query("SELECT ch.comment,ch.customer_id,ch.product_id,ch.is_profile_comment FROM " . DB_PREFIX . "comment_history ch WHERE ch.customer_id='".$customer_id."' AND ch.is_profile_comment=1  ORDER BY priority_id DESC ");
            return $query->rows;
        }  
}
?>
