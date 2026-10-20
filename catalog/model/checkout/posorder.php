<?php

class ModelCheckoutPosorder extends Model {

    public function addOrder($data) {

        $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int) $data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int) $data['customer_id'] . "', customer_group_id = '" . (int) $data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int) $data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int) $data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int) $data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int) $data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float) $data['total'] . "', affiliate_id = '" . (int) $data['affiliate_id'] . "', commission = '" . (float) $data['commission'] . "', language_id = '" . (int) $data['language_id'] . "', currency_id = '" . (int) $data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float) $data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" . $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = '" . date("Y-m-d H:i:s", time()) . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "',requested_time='" . date("Y-m-d H:i:s", isset($data['order_requested_time']) ? $data['order_requested_time'] : time()) . "', system_id = '" . (int) ((isset($this->session->data['system_id']))?$this->session->data['system_id']:0)  . "', user_id = '" . (int) ((isset($this->session->data['user_id']))?$this->session->data['user_id']:0) . "'");

        $order_id = $this->db->getLastId();
        $_SESSION['shipping'] = $this->db->escape($data['shipping_method']);
        foreach ($data['products'] as $product) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int) $order_id . "', product_id = '" . (int) $product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int) $product['quantity'] . "', price = '" . (float) $product['price'] . "', total = '" . (float) $product['total'] . "', tax = '" . (float) $product['tax'] . "', reward = '" . (int) $product['reward'] . "'");

            $order_product_id = $this->db->getLastId();

            foreach ($product['option'] as $option) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_option_id = '" . (int) $option['product_option_id'] . "', product_option_value_id = '" . (int) $option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
            }

            foreach ($product['download'] as $download) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int) ($download['remaining'] * $product['quantity']) . "'");
            }
              foreach ($product['bundle'] as $bundle_product) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle SET  order_product_id = '" . (int) $order_product_id . "',order_id = '" . (int) $order_id . "',bundle_id = '" . (int) $bundle_product['bundled_id'] . "',bundle_product_id = '" . (int) $bundle_product['product_id'] . "', name = '" . $this->db->escape($bundle_product['name']) . "', model = '" . $this->db->escape($bundle_product['model']) . "', quantity = '" . (int) $bundle_product['quantity'] . "'");

                $order_bundle_product_id = $this->db->getLastId();

                foreach ($bundle_product['option'] as $bunoption) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle_option SET order_bundle_product_id = '" . (int) $order_bundle_product_id . "', product_option_id = '" . (int) $bunoption['product_option_id'] . "', product_option_value_id = '" . (int) $bunoption['product_option_value_id'] . "', name = '" . $this->db->escape($bunoption['name']) . "', `value` = '" . $this->db->escape($bunoption['value']) . "', `type` = '" . $this->db->escape($bunoption['type']) . "'");
                }
            }
        }

        foreach ($data['vouchers'] as $voucher) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int) $order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int) $voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float) $voucher['amount'] . "'");
        }

        foreach ($data['totals'] as $total) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', text = '" . $this->db->escape($total['text']) . "', `value` = '" . (float) $total['value'] . "', sort_order = '" . (int) $total['sort_order'] . "'");
        }

        $this->db->query("INSERT INTO " . DB_PREFIX . "edited_pending_orders SET `order_id` = '" . (int) $order_id . "',`index`='0',`data`='".$this->db->escape(serialize($this->session->data))."',`payed_total`='".(float)(($data['payment_code']!='cod')?$data['total']:'0')."',`user_id`='".(int) ((isset($this->session->data['user_id']))?$this->session->data['user_id']:0)."',`edited_time`='".date("Y-m-d H:i:s", time()) ."'");
        return $order_id;
    }

    public function getOrder($order_id) {
        $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "'");

        if ($order_query->num_rows) {
            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int) $order_query->row['payment_country_id'] . "'");

            if ($country_query->num_rows) {
                $payment_iso_code_2 = $country_query->row['iso_code_2'];
                $payment_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $payment_iso_code_2 = '';
                $payment_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int) $order_query->row['payment_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $payment_zone_code = $zone_query->row['code'];
            } else {
                $payment_zone_code = '';
            }

            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int) $order_query->row['shipping_country_id'] . "'");

            if ($country_query->num_rows) {
                $shipping_iso_code_2 = $country_query->row['iso_code_2'];
                $shipping_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $shipping_iso_code_2 = '';
                $shipping_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int) $order_query->row['shipping_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $shipping_zone_code = $zone_query->row['code'];
            } else {
                $shipping_zone_code = '';
            }

            $this->load->model('localisation/language');

            $language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

            if ($language_info) {
                $language_code = $language_info['code'];
                $language_filename = $language_info['filename'];
                $language_directory = $language_info['directory'];
            } else {
                $language_code = '';
                $language_filename = '';
                $language_directory = '';
            }

            return array(
                'order_id' => $order_query->row['order_id'],
                'invoice_no' => $order_query->row['invoice_no'],
                'invoice_prefix' => $order_query->row['invoice_prefix'],
                'store_id' => $order_query->row['store_id'],
                'store_name' => $order_query->row['store_name'],
                'store_url' => $order_query->row['store_url'],
                'customer_id' => $order_query->row['customer_id'],
                'firstname' => $order_query->row['firstname'],
                'lastname' => $order_query->row['lastname'],
                'telephone' => $order_query->row['telephone'],
                'fax' => $order_query->row['fax'],
                'email' => $order_query->row['email'],
                'payment_firstname' => $order_query->row['payment_firstname'],
                'payment_lastname' => $order_query->row['payment_lastname'],
                'payment_company' => $order_query->row['payment_company'],
                'payment_address_1' => $order_query->row['payment_address_1'],
                'payment_address_2' => $order_query->row['payment_address_2'],
                'payment_postcode' => $order_query->row['payment_postcode'],
                'payment_city' => $order_query->row['payment_city'],
                'payment_zone_id' => $order_query->row['payment_zone_id'],
                'payment_zone' => $order_query->row['payment_zone'],
                'payment_zone_code' => $payment_zone_code,
                'payment_country_id' => $order_query->row['payment_country_id'],
                'payment_country' => $order_query->row['payment_country'],
                'payment_iso_code_2' => $payment_iso_code_2,
                'payment_iso_code_3' => $payment_iso_code_3,
                'payment_address_format' => $order_query->row['payment_address_format'],
                'payment_method' => $order_query->row['payment_method'],
                'payment_code' => $order_query->row['payment_code'],
                'shipping_firstname' => $order_query->row['shipping_firstname'],
                'shipping_lastname' => $order_query->row['shipping_lastname'],
                'shipping_company' => $order_query->row['shipping_company'],
                'shipping_address_1' => $order_query->row['shipping_address_1'],
                'shipping_address_2' => $order_query->row['shipping_address_2'],
                'shipping_postcode' => $order_query->row['shipping_postcode'],
                'shipping_city' => $order_query->row['shipping_city'],
                'shipping_zone_id' => $order_query->row['shipping_zone_id'],
                'shipping_zone' => $order_query->row['shipping_zone'],
                'shipping_zone_code' => $shipping_zone_code,
                'shipping_country_id' => $order_query->row['shipping_country_id'],
                'shipping_country' => $order_query->row['shipping_country'],
                'shipping_iso_code_2' => $shipping_iso_code_2,
                'shipping_iso_code_3' => $shipping_iso_code_3,
                'shipping_address_format' => $order_query->row['shipping_address_format'],
                'shipping_method' => $order_query->row['shipping_method'],
                'shipping_code' => $order_query->row['shipping_code'],
                'comment' => $order_query->row['comment'],
                'total' => $order_query->row['total'],
                'order_status_id' => $order_query->row['order_status_id'],
                'order_status' => $order_query->row['order_status'],
                'language_id' => $order_query->row['language_id'],
                'language_code' => $language_code,
                'language_filename' => $language_filename,
                'language_directory' => $language_directory,
                'currency_id' => $order_query->row['currency_id'],
                'currency_code' => $order_query->row['currency_code'],
                'currency_value' => $order_query->row['currency_value'],
                'ip' => $order_query->row['ip'],
                'forwarded_ip' => $order_query->row['forwarded_ip'],
                'user_agent' => $order_query->row['user_agent'],
                'accept_language' => $order_query->row['accept_language'],
                'date_modified' => $order_query->row['date_modified'],
                'date_added' => $order_query->row['date_added'],
                'requested_time' => $order_query->row['requested_time'],
                'system_id' => $order_query->row['system_id'],
                'user_id' => $order_query->row['user_id'],
            );
        } else {
            return false;
        }
    }

    public function confirm($order_id, $order_status_id, $comment = '', $notify = false) {
        $order_info = $this->getOrder($order_id);

        if ($order_info && !$order_info['order_status_id']) {
            // Fraud Detection
            if ($this->config->get('config_fraud_detection')) {
                $this->load->model('checkout/fraud');

                $risk_score = $this->model_checkout_fraud->getFraudScore($order_info);

                if ($risk_score > $this->config->get('config_fraud_score')) {
                    $order_status_id = $this->config->get('config_fraud_status_id');
                }
            }

            // Blacklist
            $status = false;

            $this->load->model('account/customer');

            if ($order_info['customer_id']) {
                $results = $this->model_account_customer->getIps($order_info['customer_id']);

                foreach ($results as $result) {
                    if ($this->model_account_customer->isBlacklisted($result['ip'])) {
                        $status = true;

                        break;
                    }
                }
            } else {
                $status = $this->model_account_customer->isBlacklisted($order_info['ip']);
            }

            if ($status) {
                $order_status_id = $this->config->get('config_order_status_id');
            }
            //Pending Orders edit
            if($order_status_id!=1){//update edited order
                   $this->db->query("DELETE `" . DB_PREFIX . "edited_pending_orders` WHERE order_id ='".(int)$order_id."'");
            }
            //Pending Orders edit
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $order_status_id . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "' WHERE order_id = '" . (int) $order_id . "'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '" . (int) $order_status_id . "', notify = '1', comment = '" . $this->db->escape(($comment && $notify) ? $comment : '') . "', date_added = '" . date("Y-m-d H:i:s", time()) . "'");

            $order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");

            foreach ($order_product_query->rows as $order_product) {
                $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int) $order_product['quantity'] . ") WHERE product_id = '" . (int) $order_product['product_id'] . "' AND subtract = '1'");

                $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $order_product['order_product_id'] . "'");

                foreach ($order_option_query->rows as $option) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int) $order_product['quantity'] . ") WHERE product_option_value_id = '" . (int) $option['product_option_value_id'] . "' AND subtract = '1'");
                }
            }

            $this->cache->delete('product');

            // Downloads
            $order_download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int) $order_id . "'");

            // Gift Voucher
            $this->load->model('checkout/voucher');

            $order_voucher_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int) $order_id . "'");

            foreach ($order_voucher_query->rows as $order_voucher) {
                $voucher_id = $this->model_checkout_voucher->addVoucher($order_id, $order_voucher);

                $this->db->query("UPDATE " . DB_PREFIX . "order_voucher SET voucher_id = '" . (int) $voucher_id . "' WHERE order_voucher_id = '" . (int) $order_voucher['order_voucher_id'] . "'");
            }

            // Send out any gift voucher mails
            if ($this->config->get('config_complete_status_id') == $order_status_id) {
                $this->model_checkout_voucher->confirm($order_id);
            }

            // Order Totals			
            $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order ASC");

            foreach ($order_total_query->rows as $order_total) {
                // TODO Google Checkout proper
                if (empty($order_total['code']))
                    continue;

                $this->load->model('total/' . $order_total['code']);

                if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
                    $this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);
                }
            }
           

          

          

          
            
            
        }
    }

    public function update($order_id, $order_status_id, $comment = '', $notify = false) {
        $order_info = $this->getOrder($order_id);

        if ($order_info && $order_info['order_status_id']) {
            // Fraud Detection
            if ($this->config->get('config_fraud_detection')) {
                $this->load->model('checkout/fraud');

                $risk_score = $this->model_checkout_fraud->getFraudScore($order_info);

                if ($risk_score > $this->config->get('config_fraud_score')) {
                    $order_status_id = $this->config->get('config_fraud_status_id');
                }
            }

            // Blacklist
            $status = false;

            $this->load->model('account/customer');

            if ($order_info['customer_id']) {

                $results = $this->model_account_customer->getIps($order_info['customer_id']);

                foreach ($results as $result) {
                    if ($this->model_account_customer->isBlacklisted($result['ip'])) {
                        $status = true;

                        break;
                    }
                }
            } else {
                $status = $this->model_account_customer->isBlacklisted($order_info['ip']);
            }

            if ($status) {
                $order_status_id = $this->config->get('config_order_status_id');
            }

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $order_status_id . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "' WHERE order_id = '" . (int) $order_id . "'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '" . (int) $order_status_id . "', notify = '" . (int) $notify . "', comment = '" . $this->db->escape($comment) . "', date_added = '" . date("Y-m-d H:i:s", time()) . "'");

           

            
        }
    }
     public function getOrderBundleProducts($order_id,$product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int)$order_id . "' AND order_product_id='".(int)$product_id."'");
		
		return $query->rows;
	}
         public function getOrderBundledProductoptions($order_bundle_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle_option WHERE order_bundle_product_id = '" . (int)$order_bundle_product_id . "'");
		
		return $query->rows;
	}

    public function deliveryTime($order_id) {
        $this->language->load('checkout/success');
        if ($this->config->get('config_live_delivery_storeclosed') || $this->postcode->isStoreOpened($this->config->get('config_store_id'))) {

            $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "' AND order_status_id<>1");
            if ($order_query->row) {
                $output = array('order_status' => $order_query->row['order_status'], 'order_status_id' => $order_query->row['order_status_id']);
                if (strtotime($order_query->row['delivery_time']) > 0) {
                    $output['delivery_time'] = date("l (d-m-y) h:i A", strtotime($order_query->row['delivery_time']));
                }

                // $output['requested_time'] = date("l (d-m-y) h:i A",strtotime ($order_query->row['requested_time']));
                $output['comment'] = $this->getOrderHistorieComment($order_id, $order_query->row['order_status_id']);
                return $output;
            } else {
                $live_delivery_timeout = (int) $this->config->get('config_live_delivery_timeout');

                if ($live_delivery_timeout > 0) {
                    $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "' AND order_status_id=1 AND '" . date("Y-m-d H:i:s") . "' > date_added  + INTERVAL $live_delivery_timeout SECOND ");
                    //  echo ("SELECT * FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "' AND order_status_id<>1 AND '" . date("Y-m-d H:i:s") . "' > date_added  + INTERVAL $live_delivery_timeout SECOND ");
                    if ($order_query->row) {

                        $this->update($order_id, $this->config->get('config_live_delivery_timeout_orderstatus'));
                        $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "' AND order_status_id<>1");
                        if ($order_query->row) {
                            $output = array('order_status' => $order_query->row['order_status'], 'order_status_id' => $order_query->row['order_status_id']);
                            $output['comment'] = $this->language->get('text_timeout');
                            return $output;
                        }
                    }
                }
                return array();
            }
        } else {
            // Store closed
            $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "' ");
            if ($order_query->row) {
                $output = array('order_status' => $order_query->row['order_status'], 'order_status_id' => $order_query->row['order_status_id']);
                $output['requested_time'] = date("l (d-m-y) h:i A", strtotime($order_query->row['requested_time']));
                $output['comment'] = $this->language->get('text_store_closed');
                return $output;
            }
        }
    }

    public function getOrderHistorieComment($order_id, $order_status_id) {

        $query = $this->db->query("SELECT  oh.comment FROM " . DB_PREFIX . "order_history oh  WHERE order_status_id = " . $order_status_id . " AND order_id=" . $order_id . " ORDER BY order_history_id DESC LIMIT 1");
        if ($query->row) {
            return $query->row['comment'];
        } else {
            return '';
        }
    }

    public function fetchOrderToServer($order_id){
        $order_details=  $this->getOrder($order_id); 
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $this->config->get('pos_toonlineurl'));
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $order_details);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $responseBody = curl_exec($curlHandle);
        $responseInfo = curl_getinfo($curlHandle);
        curl_close($curlHandle);
        //return $this->handleTextmarkerResponse($responseBody, $responseInfo);
    }
    //Home Delivery Assignments if Driver Cases 
    public function getTodaysTotalDeliveryOrders(){

        $dates = $this->postcode->getLastDaysOpeningtimes();
        $query = $this->db->query("SELECT count(*) as  count  FROM `" . DB_PREFIX . "order` o WHERE (o.date_added) > '" . $dates[key($dates)]['end'] . "' AND o.order_status_id > '0' AND o.order_status_id !='3' AND o.shipping_code='home.home'");
        return $query->row['count'];   
        
    }
    public function getTodaysTotalassignedorders(){

        $dates = $this->postcode->getLastDaysOpeningtimes();
       // $query = $this->db->query("SELECT count(*) as count  FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "deliverys` de ON (o.order_id = de.order_id) WHERE (o.date_added) > '" . $dates[key($dates)]['end'] . "' AND o.order_status_id > '0' AND o.order_status_id!='3' AND o.shipping_code='home.home'");
         $query = $this->db->query("SELECT count(*) as count  FROM `" . DB_PREFIX . "deliverys` de LEFT JOIN `" . DB_PREFIX . "order` o ON (de.order_id = o.order_id) WHERE (o.date_added) > '" . $dates[key($dates)]['end'] . "' AND o.order_status_id > '0' AND o.order_status_id!='3' AND o.shipping_code='home.home'");
        //print_r($query->rows);die();
        return $query->row['count'];
    }
    //get count
    public function getTodaysTotalDeliveredorders(){

        $dates = $this->postcode->getLastDaysOpeningtimes();
        $query = $this->db->query("SELECT count(*) as  count  FROM `" . DB_PREFIX . "order` o WHERE (o.date_added) > '" . $dates[key($dates)]['end'] . "' AND o.order_status_id='3' AND o.shipping_code='home.home'");
        return $query->row['count']; 
        
    }
    
    public function getTodaysUnDeliveredOrders(){

        $dates = $this->postcode->getLastDaysOpeningtimes();
        $query = $this->db->query("SELECT *,o.order_id as order_id ,de.order_id as del_orderid,o.total as total,de.total as del_total  FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "deliverys` de ON (o.order_id = de.order_id) WHERE (o.date_added) > '" . $dates[key($dates)]['end'] . "' AND o.order_status_id > '0' AND o.order_status_id!='3' AND o.shipping_code='home.home'");
        return $query->rows; 
        
    }
    
    public function getTodaysAssignedOrderIds(){
        $order_ids=array();

        $dates = $this->postcode->getLastDaysOpeningtimes();
        $query = $this->db->query("SELECT order_id  FROM `" . DB_PREFIX . "deliverys` d WHERE (d.time_assigned) > '" . $dates[key($dates)]['end'] . "'");
        if(!empty($query->rows)){
            foreach ($query->rows as $order) {
                $order_ids[]=$order['order_id'];
            }
            
        }
        return $order_ids;
        
    }
    
    public function getTodaysDeliveredOrders(){

         $dates = $this->postcode->getLastDaysOpeningtimes();
         $query = $this->db->query("SELECT *  FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "deliverys` de ON (o.order_id = de.order_id) WHERE (o.date_added) > '" . $dates[key($dates)]['end'] . "' AND o.order_status_id='3' AND o.shipping_code='home.home'");
         return $query->rows;  
    }
    
    public function updatePendingOrder($data,$order_id){
        //$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int) $data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int) $data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int) $data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float) $data['total'] . "', affiliate_id = '" . (int) $data['affiliate_id'] . "', commission = '" . (float) $data['commission'] . "', language_id = '" . (int) $data['language_id'] . "', currency_id = '" . (int) $data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float) $data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" . $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = '" . date("Y-m-d H:i:s", time()) . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "',requested_time='" . date("Y-m-d H:i:s", isset($data['order_requested_time']) ? $data['order_requested_time'] : time()) . "', system_id = '" . (int) ((isset($this->session->data['system_id']))?$this->session->data['system_id']:0) . "', user_id = '" . (int) ((isset($this->session->data['user_id']))?$this->session->data['user_id']:0) . "' WHERE `order_id`='".(int)$order_id."'");
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int) $data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int) $data['customer_id'] . "', customer_group_id = '" . (int) $data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int) $data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int) $data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int) $data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int) $data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float) $data['total'] . "', affiliate_id = '" . (int) $data['affiliate_id'] . "', commission = '" . (float) $data['commission'] . "', language_id = '" . (int) $data['language_id'] . "', currency_id = '" . (int) $data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float) $data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" . $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = '" . date("Y-m-d H:i:s", time()) . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "',requested_time='" . date("Y-m-d H:i:s", isset($data['order_requested_time']) ? $data['order_requested_time'] : time()) . "', system_id = '" . (int) ((isset($this->session->data['system_id']))?$this->session->data['system_id']:0) . "', user_id = '" . (int) ((isset($this->session->data['user_id']))?$this->session->data['user_id']:0) . "' WHERE `order_id`='".(int)$order_id."'");
        $_SESSION['shipping'] = $this->db->escape($data['shipping_method']);
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'"); 
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
	$this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
	$this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
        foreach ($data['products'] as $product) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int) $order_id . "', product_id = '" . (int) $product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int) $product['quantity'] . "', price = '" . (float) $product['price'] . "', total = '" . (float) $product['total'] . "', tax = '" . (float) $product['tax'] . "', reward = '" . (int) $product['reward'] . "'");

            $order_product_id = $this->db->getLastId();

            foreach ($product['option'] as $option) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_option_id = '" . (int) $option['product_option_id'] . "', product_option_value_id = '" . (int) $option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
            }

            foreach ($product['download'] as $download) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int) ($download['remaining'] * $product['quantity']) . "'");
            }
              foreach ($product['bundle'] as $bundle_product) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle SET  order_product_id = '" . (int) $order_product_id . "',order_id = '" . (int) $order_id . "',bundle_id = '" . (int) $bundle_product['bundled_id'] . "',bundle_product_id = '" . (int) $bundle_product['product_id'] . "', name = '" . $this->db->escape($bundle_product['name']) . "', model = '" . $this->db->escape($bundle_product['model']) . "', quantity = '" . (int) $bundle_product['quantity'] . "'");

                $order_bundle_product_id = $this->db->getLastId();

                foreach ($bundle_product['option'] as $bunoption) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle_option SET order_bundle_product_id = '" . (int) $order_bundle_product_id . "', product_option_id = '" . (int) $bunoption['product_option_id'] . "', product_option_value_id = '" . (int) $bunoption['product_option_value_id'] . "', name = '" . $this->db->escape($bunoption['name']) . "', `value` = '" . $this->db->escape($bunoption['value']) . "', `type` = '" . $this->db->escape($bunoption['type']) . "'");
                }
            }
        }

        foreach ($data['vouchers'] as $voucher) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int) $order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int) $voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float) $voucher['amount'] . "'");
        }

        foreach ($data['totals'] as $total) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', text = '" . $this->db->escape($total['text']) . "', `value` = '" . (float) $total['value'] . "', sort_order = '" . (int) $total['sort_order'] . "'");
        }

        //Pending Orders Add
        //$pending_data=array();
        $pending_data=array('products'=>$data['products'],'totals'=>$data['totals']);
        $index=0;
        $ind_qry=$this->db->query("SELECT `index` FROM " . DB_PREFIX . "edited_pending_orders WHERE order_id='".$order_id."' ORDER BY id DESC LIMIT 1 ");
        if(!empty($ind_qry->row)){
            $index = $ind_qry->row['index']+1;
        }
        $this->db->query("INSERT INTO " . DB_PREFIX . "edited_pending_orders SET `order_id` = '" . (int) $order_id . "',`index`='0',`data`='".$this->db->escape(serialize($this->session->data))."',`payed_total`='".(float)(($data['payment_code']!='cod')?$data['total']:0)."',`user_id`='".(int) ((isset($this->session->data['user_id']))?$this->session->data['user_id']:0)."',`edited_time`='".date("Y-m-d H:i:s", time()) ."'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '19', notify = '0', comment = '', date_added = '" . date("Y-m-d H:i:s", time()) . "'");
        //$this->session->data['edited_order_id']=$this->db->getLastId();
        //Pending Orders Add

    }
   public function isOnlineOrderAdded($online_order_id) {
             $ordes_query =  $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order`  WHERE  online_order_id=" .$online_order_id );
             if($ordes_query->num_rows  ) {
                 return true;
                 
             }
             else {
                 return false;
             }
        }
        public function onlineOrderHistory($order_id, $order_status_id, $comment = '') {
            $order_info = $this->getOrder($order_id);
            if ($order_info) {

                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $order_status_id . "', date_modified = '" . date("Y-m-d H:i:s",  time()) . "' WHERE order_id = '" . (int) $order_id . "'");

                $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '" . (int) $order_status_id . "', notify = '1', comment = '" . $this->db->escape(($comment) ? $comment : '') . "', date_added = '" . date("Y-m-d H:i:s",  time()) . "'");
            }
            
        }
}

?>