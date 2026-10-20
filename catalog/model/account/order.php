<?php
class ModelAccountOrder extends Model {
	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND order_status_id > '0'");
	
		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';				
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}
			
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';				
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}
			
			return array(
				'order_id'                => $order_query->row['order_id'],
                                'display_id'                => $order_query->row['display_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],				
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],				
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],	
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],				
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],	
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
                                'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip'],
                                'requested_time'          => $order_query->row['requested_time']
                                
			);
		} else {
			return false;	
		}
	}
	 
	public function getOrders($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
		
		if ($limit < 1) {
			$limit = 1;
		}	
		
		$query = $this->db->query("SELECT o.order_id,o.display_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value,o.comment FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);	
	
		return $query->rows;
	}
	
	public function getOrderProducts($order_id) {
            $query = $this->db->query("SELECT *,op.name as name, pd.shortcode FROM " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "product_description pd ON(op.product_id=pd.product_id) WHERE op.order_id = '" . (int)$order_id . "'");
            //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		return $query->rows;
	}
	
	public function getOrderOptions($order_id, $order_product_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
                $query = $this->db->query("SELECT op.*,pv.option_id,pv.option_value_id,pv.price FROM " . DB_PREFIX . "order_option op LEFT JOIN " . DB_PREFIX . "product_option_value pv ON (op.product_option_value_id = pv.product_option_value_id)  WHERE op.order_id = '" . (int)$order_id . "' AND op.order_product_id = '" . (int)$order_product_id . "'");
		return $query->rows;
	}
	
	public function getOrderVouchers($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->rows;
	}
	
	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");
	
		return $query->rows;
	}	

	public function getOrderHistories($order_id) {
		$query = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND oh.notify = '1' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added");
	
		return $query->rows;
	}	

	public function getOrderDownloads($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "' ORDER BY name");
	
		return $query->rows; 
	}	

	public function getTotalOrders() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND order_status_id > '0'");
		
		return $query->row['total'];
	}
		
	public function getTotalOrderProductsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->row['total'];
	}
	
	public function getTotalOrderVouchersByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->row['total'];
	}	
	
        public function getOrderforreordering($order_id) {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'  AND order_status_id > '0'");
	
		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';				
}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}
			
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';				
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}
			
			return array(
				'order_id'                => $order_query->row['order_id'],
                                'display_id'              => $order_query->row['display_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],				
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],				
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],	
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],				
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],	
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip'],
                                'requested_time'          => $order_query->row['requested_time']
			);
		} else {
			return false;	
		}
	}
        
        public function getOrdereditingDetails($order_id) {
        $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "edited_pending_orders WHERE order_id='".(int)$order_id."'");
        return $query->rows;
    }
    
    public function getEditedProductDetails($key,$quantity,$customer_group_id,$currency_code,$currency_val) {
        $product = explode(':', $key);
        $product_id = $product[0];
        if (isset($product[1])) {
            if ($product[1] != '')
                $options = unserialize(base64_decode($product[1]));
            else
                $options = array();
        }else {
            $options = array();
        }
        //IF BUNDLED
        if (isset($product[2]) and $product[2] != '') {
            $bundled_products = unserialize(base64_decode($product[2]));
            $bundled_option_price = 0;
            foreach ($bundled_products as $i => $bundled_product) {
                $bundled_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int) $bundled_product['product_id'] . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
                $option_price = 0;
                $option_points = 0;
                $option_weight = 0;
                $option_data = array();
                if (isset($bundled_product['options'])) {
                    foreach ($bundled_product['options'] as $product_option_id => $option_value) {
                        $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, po.default, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int) $product_option_id . "' AND po.product_id = '" . (int) $bundled_product['product_id'] . "' AND od.language_id = '" . (int) $this->config->get('config_language_id') . "'");
                        if ($option_query->num_rows) {
                            if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
                                $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int) $option_value . "' AND pov.product_option_id = '" . (int) $product_option_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

                                if ($option_value_query->num_rows) {
                                    if ($option_value_query->row['price_prefix'] == '+') {
                                        $option_price += $option_value_query->row['price'];
                                        $bundled_option_price += $option_value_query->row['price'];
                                    } elseif ($option_value_query->row['price_prefix'] == '-') {
                                        $option_price -= $option_value_query->row['price'];
                                        $bundled_option_price -= $option_value_query->row['price'];
                                    }

                                    $option_data[] = array(
                                        'name' => $option_query->row['name'],
                                        'option_value' => $option_value_query->row['name'],
                                        'type' => $option_query->row['type'],
                                        'quantity' => $option_value_query->row['quantity'],
                                        'price' => $option_value_query->row['price'],
                                        'price_prefix' => $option_value_query->row['price_prefix'],
                                    );
                                }
                            } elseif ($option_query->row['type'] == 'checkbox' && is_array($option_value)) {
                                foreach ($option_value as $product_option_value_id) {
                                    $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int) $product_option_value_id . "' AND pov.product_option_id = '" . (int) $product_option_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

                                    if ($option_value_query->num_rows) {
                                        if ($option_value_query->row['price_prefix'] == '+') {
                                            $option_price += $option_value_query->row['price'];
                                            $bundled_option_price += $option_value_query->row['price'];
                                        } elseif ($option_value_query->row['price_prefix'] == '-') {
                                            $option_price -= $option_value_query->row['price'];
                                            $bundled_option_price -= $option_value_query->row['price'];
                                        }
                                        $option_data[] = array(
                                            'name' => $option_query->row['name'],
                                            'option_value' => (($option_query->row['default']) == 1 ? 'No ' : '') . $option_value_query->row['name'],
                                        );
                                    }
                                }
                            } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                                $option_data[] = array(
                                    'name' => $option_query->row['name'],
                                    'option_value' => $option_value
                                );
                            }
                        }
                    }
                }

                $price = $bundled_product_query->row['price'];
                // Product Discounts
                $discount_quantity = 0;
                $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND quantity <= '" . (int) $discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

                if ($product_discount_query->num_rows) {
                    $price = $product_discount_query->row['price'];
                }

                // Product Specials
                $product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

                if ($product_special_query->num_rows) {
                    $price = $product_special_query->row['price'];
                }

                $bundled_details[] = array(
                    'name' => $bundled_product_query->row['name'],
                    'option' => $option_data,
                    'price' => ($price + $option_price),
                    'total' => ($price + $option_price) * $quantity
                );
            }
        } 
        else {
            $bundled_option_price = 0;
            $bundled_details = array();
        }
        //BUNDLED
        
        $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int) $product_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "'");
        if ($product_query->num_rows) {
            $option_price = 0;
            $option_points = 0;
            $option_weight = 0;
            $option_data = array();
            foreach ($options as $product_option_id => $option_value) {
                $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, po.default, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int) $product_option_id . "' AND po.product_id = '" . (int) $product_id . "' AND od.language_id = '" . (int) $this->config->get('config_language_id') . "'");
                if ($option_query->num_rows) {
                    if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
                        $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int) $option_value . "' AND pov.product_option_id = '" . (int) $product_option_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "'");
                        if ($option_value_query->num_rows) {
                            if ($option_value_query->row['price_prefix'] == '+') {
                                $option_price += $option_value_query->row['price'];
                            } elseif ($option_value_query->row['price_prefix'] == '-') {
                                $option_price -= $option_value_query->row['price'];
                            }
                            $option_data[] = array(
                                'name' => $option_query->row['name'],
                                'option_value' => $option_value_query->row['name']
                            );
                        }
                    } elseif ($option_query->row['type'] == 'checkbox' && is_array($option_value)) {
                        foreach ($option_value as $product_option_value_id) {
                            $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int) $product_option_value_id . "' AND pov.product_option_id = '" . (int) $product_option_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

                            if ($option_value_query->num_rows) {
                                if ($option_value_query->row['price_prefix'] == '+') {
                                    $option_price += $option_value_query->row['price'];
                                } elseif ($option_value_query->row['price_prefix'] == '-') {
                                    $option_price -= $option_value_query->row['price'];
                                }

                                $option_data[] = array(
                                    'name' => $option_query->row['name'],
                                    'option_value' => (($option_query->row['default']) == 1 ? 'No ' : '') . $option_value_query->row['name']
                                );
                            }
                        }
                    } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                        $option_data[] = array(
                            'name' => $option_query->row['name'],
                            'option_value' => $option_value
                        );
                    }
                }
            }
            $price = $product_query->row['price'];
            $discount_quantity = 0;
            $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND quantity <= '" . (int) $discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");
            if ($product_discount_query->num_rows) {
                $price = $product_discount_query->row['price'];
            }

            // Product Specials
            $product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

            if ($product_special_query->num_rows) {
                $price = $product_special_query->row['price'];
            }
        }
        if (isset($product[3])) {
            $adhoc_product = unserialize(base64_decode($product[3]));
            $price = $adhoc_product['price'];
        }
        return array(
            'name' => ((isset($product[3])) ? $adhoc_product['name'] : $product_query->row['name']),
            'model' => $product_query->row['model'],
            'option' => $option_data,
            'quantity' => $quantity,
            //'price' => ($price + $option_price + $bundled_option_price),
            //'total' => (($price + $option_price + $bundled_option_price) * $quantity ),
            'price' => $this->currency->format(($price + $option_price + $bundled_option_price) + ($this->config->get('config_tax') ? $product_query->row['tax_class_id'] : 0), $currency_code, $currency_val),
            'total' => $this->currency->format(($price + $option_price + $bundled_option_price) + ($this->config->get('config_tax') ? ($product_query->row['tax_class_id'] * $quantity) : 0), $currency_code, $currency_val),
            'bundled_data' => $bundled_details
        );
    }

    public function getAddress($address_id,$customer_id) {
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$customer_id . "'");
		
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
				'address_1'      => $address_query->row['address_1'],
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
    public function getOrderAllHistories($order_id) {
		$query = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added");
	
		return $query->rows;
	}
          public function getOrderSplits($order_id){
        $split_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_split WHERE order_id = '" . (int)$order_id . "'");
	if($query->rows){
            foreach ($query->rows as $key=>$ordr_split) {
                $split_data[$key]=$ordr_split;
                $split_prodt_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_split_product WHERE order_split_id = '" . (int)$ordr_split['order_split_id'] . "'");
                $split_data[$key]['products']=$split_prodt_query->rows;
                $split_total_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_split_total WHERE order_split_id = '" . (int)$ordr_split['order_split_id'] . "'");
                $split_data[$key]['total']=$split_total_query->rows;
                $split_data[$key]['payments']=  unserialize($ordr_split['payment_history']);
            }
        }
        return $split_data;
    }
    public function getOrderToppings($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
        return $query->rows;
    }
    public function getOrderDressings($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_dressings WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
        return $query->rows;
    }
    public function getOrderBundle($order_id,$order_product_id) {
        $bundledata = array();
        //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
        $query = $this->db->query("SELECT ob.*,pd.shortcode FROM " . DB_PREFIX . "order_bundle ob LEFT JOIN " . DB_PREFIX . "order_product op ON(ob.order_product_id=op.order_product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON(op.product_id=pd.product_id) WHERE ob.order_id = '" . (int)$order_id . "' AND ob.order_product_id='".(int)$order_product_id."'");
        foreach ($query->rows as $bundle) {
            $product_data=array('product_id'=>$bundle['bundle_product_id'],'name'=>$bundle['name'],'quantity'=>$bundle['quantity'],'shortcode'=>$bundle['shortcode']) ;
            $optiondata = array();
            $bundle_option_qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle_option WHERE order_bundle_product_id = '" . $bundle['order_bundle_product_id'] . "'");
            foreach($bundle_option_qry->rows as $order_option){
                if($order_option['type']=='checkbox'){
                    $optiondata[$order_option['product_option_id']][]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>0,'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>'');
                }else{
                    $optiondata[$order_option['product_option_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>0,'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>'');
                }
            }
            $bundle_toppings_qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle_toppings WHERE order_bundle_product_id = '" . (int)$bundle['order_bundle_product_id'] . "'");
            $topping_data = array();
            foreach($bundle_toppings_qry->rows as $order_topping){
                $topping_data[$order_topping['product_topping_id']][$order_topping['product_topping_value_id']]=array('name'=>$order_topping['value'],'qty'=>$order_topping['quantity'],'total_value'=>$order_topping['price'],'price_value'=>$order_topping['price'],'price'=>$order_topping['price'],'topping_id'=>$order_topping['product_topping_id'],'topping_value_id'=>$order_topping['product_topping_value_id'],'toppingname'=>$order_topping['name'],'shortcode'=>$order_topping['shortcode']);
            }
            $bundle_dressings_qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle_dressings WHERE order_bundle_product_id = '" . (int)$bundle['order_bundle_product_id'] . "'");
            $dressing_data = array();
            foreach($bundle_dressings_qry->rows as $order_dressing){
                $dressing_data[$order_dressing['product_dressing_id']][$order_dressing['product_dressing_value_id']]=array('name'=>$order_dressing['value'],'qty'=>1,'total_value'=>'','price_value'=>'','dressing_id'=>$order_dressing['product_dressing_id'],'dressing_value_id'=>$order_dressing['product_dressing_value_id'],'dressingname'=>$order_dressing['name'],'shortcode'=>$order_dressing['shortcode']);
            }
            $bundledata[] = array('bundled_id'=>$bundle['bundle_id'],'product'=>$product_data,'option'=>$optiondata,'toppings'=>$topping_data,'dressings'=>$dressing_data);
        }

        return $bundledata;
    }
    public function getallOrderHistories($order_id) {
        $query = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added LIMIT 5");
        return $query->rows;
    }
    public function checkProductmodifedafterOrder($order_id,$product_id){
        $order_info = $this->getOrder($order_id);
        $status = true;
        if(!empty($order_info)){ //date_modified
            $order_date = $order_info['date_added'];
            $p_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "' AND p.date_modified <='".$order_date."' ");
            if ($p_query->num_rows) {
                $status = false;
            }
        }
        return $status;
    }
    public function previousOrders($order_id, $customer_id) {
        if(intval($customer_id)>0) {
            $preo_query = $this->db->query("SELECT count(*) previousorders FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . $customer_id . "' AND  order_id < '" . $order_id . "'");

            if ($preo_query->num_rows) {
                            return $preo_query->row['previousorders'];
            } else {
                return 0;
            }
        }
        else {
            return 0;
        }
    }
     //New Functions for formated order datas
    public function getFormattedOrderProducts($order_id){ 
            $query = $this->db->query("SELECT *,REPLACE(name,'&amp;','and') AS name FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
            $deal_prodcuts = $this->getDealProducts($order_id);
            $product_data = array();
            if ($query->num_rows) {
                foreach($query->rows as $product){
                    $product['option']= $this->getOrderOptions($order_id,$product['order_product_id']);
                    $product['toppings']=$this->getOrderToppings($order_id,$product['order_product_id']);
                    $product['dressings']=$this->getOrderDressings($order_id,$product['order_product_id']);
                    if($this->checkProductmodifedafterOrder($order_id,$product['product_id'])){
                        $product['product_modified'] = 1;
                    }
                    $product_data[$product['order_product_id']]=$product;
                    $product_data[$product['order_product_id']]['bundle']=array();
                }
            }
          //  print_r($product_data);
            //print_r($deal_prodcuts);
            foreach($deal_prodcuts as $order_product_id=>$dealprodcuts){
                $product_data[$order_product_id]['bundle']=array();
                foreach($dealprodcuts as $deal_prodcut){
                    $product_details = $this->getOrderProductdata($deal_prodcut['bundle_order_product_id']);
                    $product_details['bundle_id']=$deal_prodcut['bundle_id'];
                    $product_details['quantity']=$deal_prodcut['quantity'];
                    $product_details['options']= $this->getOrderOptions($order_id,$deal_prodcut['bundle_order_product_id']);
                    $product['toppings']=array();  //getOrderToppings & Dressings
                    $product['dressings']=array();  //getOrderToppings & Dressings    
                    $product_data[$order_product_id]['deal_item']=1;
                    $product_data[$order_product_id]['bundle'][] = $product_details;
                    $product_data[$deal_prodcut['bundle_order_product_id']]['quantity']-=(int)$deal_prodcut['quantity']*(int)$product_data[$order_product_id]['quantity'];
                    if($product_data[$deal_prodcut['bundle_order_product_id']]['quantity']<=0){
                        unset($product_data[$deal_prodcut['bundle_order_product_id']]);
                    }
                }
            }
            //print_r($product_data);
            return $product_data;
        }
        /*
        public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
        */
        public function getConsolidatedOrderProducts($order_id){ 
            $deal_prodcuts = $this->getDealProducts($order_id);
            //$op_query = $this->db->query("SELECT *,REPLACE(name,'&amp;','and') AS name FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' ORDER BY order_product_id");                        
            $op_query = $this->db->query("SELECT *,REPLACE(op.name,'&amp;','and') AS name,pd.shortcode FROM " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "product_description pd ON(op.product_id=pd.product_id) WHERE op.order_id = '" . (int)$order_id . "' ORDER BY op.order_product_id");                        
            $product_data = array();
             $k=0;
            foreach($op_query->rows as $order_product){
                if(isset($deal_prodcuts[$order_product['order_product_id']])){
                    $order_product['name'] = html_entity_decode($order_product['name']);
                    $order_product['total'] = number_format($order_product['total'], 2);
                    $order_product['qty']=$order_product['quantity'];
                    $order_product['total_value']=$order_product['total'];
                    $order_product['price_value']=$order_product['price'];
                    $order_product['item_index'] =$k;
                    $order_product['main_index'] =$k;
                    $order_product['main_product_id']=$order_product['main_product_id'];                    
                    $order_product ['bundle'] = $deal_prodcuts[$order_product['order_product_id']];
                    $order_options = $this->getOrderOptions($order_id,$order_product['order_product_id']);
                    $option_data=array();
                    if(!empty($order_options)){
                        $kj=1;
                        foreach($order_options as $order_option){
                            if($order_option['product_option_id']==0){
                                $order_option['product_option_id']=time()+$kj;
                            }
                            if($order_option['product_option_value_id']==0){
                                $order_option['product_option_value_id']=time()+$kj;
                            }
                            if($order_option['type']=='checkbox'){
                                $option_data[$order_option['product_option_id']][$order_option['product_option_value_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                            }else{
                                $option_data[$order_option['product_option_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                            }
                            $kj++;
                        }
                    }
                    $order_product['option']= $option_data;
                    $order_toppings = $this->getOrderToppings($order_id,$order_product['order_product_id']);
                    $topping_data = array();
                    if(!empty($order_toppings)){
                        foreach($order_toppings as $data){
                            $topping_data[$data['product_topping_id']][$data['product_topping_value_id']]=array('name'=>$data['value'],'qty'=>$data['quantity'],'total_value'=>$data['price'],'price_value'=>$data['price'],'price'=>$data['price'],'topping_id'=>$data['product_topping_id'],'topping_value_id'=>$data['product_topping_value_id'],'toppingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                        }
                    }
                    $order_product['toppings']=$topping_data;
                    $order_dressings=$this->getOrderDressings($order_id,$order_product['order_product_id']);
                    $dressing_data = array();
                    if(!empty($order_dressings)){
                        foreach($order_dressings as $data){
                            $dressing_data[$data['product_dressing_id']][$data['product_dressing_value_id']]=array('name'=>$data['value'],'qty'=>1,'total_value'=>'','price_value'=>'','dressing_id'=>$data['product_dressing_id'],'dressing_value_id'=>$data['product_dressing_value_id'],'dressingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                        }
                    }
                    $order_product['dressings']=$dressing_data;
                    $product_data[$order_product['order_product_id']] = $order_product;
                    $k++;
                }
            }
           
            foreach($op_query->rows as $order_product){
                if(!isset($deal_prodcuts[$order_product['order_product_id']])){
                    $order_product['name'] = html_entity_decode($order_product['name']);
                    $order_product['total'] = number_format($order_product['total'], 2);
                    $order_product['qty']=$order_product['quantity'];
                    $order_product['total_value']=$order_product['total'];
                    $order_product['price_value']=$order_product['price'];
                    $order_product['item_index'] =$k;
                    $order_product['main_index'] =$k;
                    $order_product['main_product_id']=$order_product['main_product_id'];
                   
                    $order_options = $this->getOrderOptions($order_id,$order_product['order_product_id']);
                    $option_data=array();
                    if(!empty($order_options)){
                        $kk=1;
                        foreach($order_options as $order_option){
                            if($order_option['product_option_id']==0){
                                $order_option['product_option_id']=time()+$kk;
                            }
                            if($order_option['product_option_value_id']==0){
                                $order_option['product_option_value_id']=time()+$kk;
                            }
                            if($order_option['type']=='checkbox'){
                                $option_data[$order_option['product_option_id']][$order_option['product_option_value_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                            }else{
                                $option_data[$order_option['product_option_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                            }
                            $kk++;
                        }
                    }
                    $order_product['option']= $option_data;
                    $order_toppings = $this->getOrderToppings($order_id,$order_product['order_product_id']);
                    $topping_data = array();
                    if(!empty($order_toppings)){
                        foreach($order_toppings as $data){
                            $topping_data[$data['product_topping_id']][$data['product_topping_value_id']]=array('name'=>$data['value'],'qty'=>$data['quantity'],'total_value'=>$data['price'],'price_value'=>$data['price'],'price'=>$data['price'],'topping_id'=>$data['product_topping_id'],'topping_value_id'=>$data['product_topping_value_id'],'toppingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                        }
                    }
                    $order_product['toppings']=$topping_data;
                    $order_dressings=$this->getOrderDressings($order_id,$order_product['order_product_id']);
                    $dressing_data = array();
                    if(!empty($order_dressings)){
                        foreach($order_dressings as $data){
                            $dressing_data[$data['product_dressing_id']][$data['product_dressing_value_id']]=array('name'=>$data['value'],'qty'=>1,'total_value'=>'','price_value'=>'','dressing_id'=>$data['product_dressing_id'],'dressing_value_id'=>$data['product_dressing_value_id'],'dressingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                        }
                    }
                    $order_product['dressings']=$dressing_data;
                    $product_data[$order_product['order_product_id']] = $order_product;
                    $k++;
                }
            }
            
            $return_data = array();
           
            foreach ($product_data as $data){
                $bundle_data = array(); 
                if(isset($data['bundle'])){
                    foreach($data['bundle'] as $bdata){
                        if(!isset($bundle_data[$bdata['bundle_id']])){
                            $bundle_data[$bdata['bundle_id']]=[];
                        }
                        $bundle_data[$bdata['bundle_id']][]=array('item_index'=>$product_data[$bdata['bundle_order_product_id']]['item_index'],'qty'=>$product_data[$bdata['bundle_order_product_id']]['quantity']);
                    }
                }
                $data['bundle_data'] = $bundle_data;
                unset($data['bundle']);
                $return_data [] = $data;                
            }               
            
            return $return_data;
           //
        }
        private function getDealProducts($order_id){
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int)$order_id . "'");
            $bundle_data = array();
            if ($query->num_rows) {
                foreach($query->rows as $bundled_product){
                    if(isset($bundle_data[$bundled_product['order_product_id']])){
                        $bundle_data[$bundled_product['order_product_id']][]=$bundled_product;
                    }else{
                        $bundle_data[$bundled_product['order_product_id']]=array();
                        $bundle_data[$bundled_product['order_product_id']][]=$bundled_product;
                    }
                }
            }
            return $bundle_data;
        }
        private function getOrderProductdata($order_product_id){
            $query = $this->db->query("SELECT *,REPLACE(name,'&amp;','and') AS name FROM " . DB_PREFIX . "order_product WHERE order_product_id = '" . (int)$order_product_id . "'");
            return $query->row;
        }

        public function getOrderCount($customer_id) {
            $query=$this->db->query("SELECT count(order_id) AS counts FROM `". DB_PREFIX . "order` WHERE  customer_id = '".$customer_id."'");
            return $query->row['counts'];
        }
         public function getTablename($table_id) {
            $query=$this->db->query("SELECT name FROM `". DB_PREFIX . "store_tables` WHERE  table_id = '".(int)$table_id."'");
            return $query->row['name'];
        }
         
        public function getOrderStatus($order_id) {
		$query = $this->db->query("SELECT o.order_status_id, os.name AS status  FROM `" . DB_PREFIX . "order_history` o LEFT JOIN " . DB_PREFIX . "order_status os ON o.order_status_id = os.order_status_id WHERE o.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY order_history_id DESC LIMIT 1 ");
	
		return $query->row;
	}	
        public function getOrderKitchenStatus($order_id) {
            $retdata = array();
            $query = $this->db->query("SELECT track_id,status,system_id  FROM `" . DB_PREFIX . "live_orders_to_kitchen`  WHERE order_id  = '" . (int)$order_id . "'");
            foreach($query->rows as $data){
                $retdata[$data['system_id']]=array('track_id'=>$data['track_id'],'status'=>$data['status']);
            }
            return $retdata;
        }
        public function getLiveOrderKitchenStatus($order_id) {
            $retdata = array();
            $query = $this->db->query("SELECT track_id,status,system_id  FROM `" . DB_PREFIX . "live_orders_to_kitchen`  WHERE live_order_id  = '" . (int)$order_id . "'");
            foreach($query->rows as $data){
                $retdata[$data['system_id']]=array('track_id'=>$data['track_id'],'status'=>$data['status']);
            }
            return $retdata;
        }
         public function getStuartdetails($order_id) {
            $query = $this->db->query("SELECT *  FROM `" . DB_PREFIX . "stuart_delivery`  WHERE order_id  = '" . (int)$order_id . "'");
            return $query->row;
        } 
}
?>