<?php
class ModelAccountGrouporder extends Model {
	public function addMembers($data,$store_id=0) {
		// $this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$this->customer->getId() . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', company_id = '" . $this->db->escape($data['company_id']) . "', tax_id = '" . $this->db->escape($data['tax_id']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "'");
		$membersids= array();
                $grouporderdelete = false;
		$customer_id = (int)$this->customer->getId();
                $this->deleteMembers($store_id);
                $seletedmembers = isset($data['member_select'])?$data['member_select']: array();
		if(isset($data['grouporderdelete'])) {
                    $grouporderdelete =true; 
                }
		if (!empty($data['member_name'])) {
                       foreach($data['member_name'] as $key=>$member_name) {
                           if(isset($data['member_email'][$key]) && !empty($data['member_email'][$key] )) {
                               if($grouporderdelete && in_array($key, $seletedmembers)) {
                                   
                               }
                               else {
                                  $this->db->query("INSERT INTO " . DB_PREFIX . "customer_grouporder_members SET customer_id = '" . $customer_id . "', member_name = '" . $this->db->escape($member_name) . "', member_email = '" . $this->db->escape($data['member_email'][$key]) . "', store_id = '" . $store_id . "'");
                                  if(!empty($seletedmembers)  && in_array($key, $seletedmembers) ) {
                                    $membersids[] = $this->db->getLastId();
                                  
                                  }
                                  if(empty($seletedmembers)) {
                                     $membersids[] = $this->db->getLastId();
                                    
                                  }
                                 
                               }
                           }
                       }
		//	$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		}
		
		return $membersids;
	}
	
		
	public function deleteMembers($store_id=0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_grouporder_members WHERE store_id = '" . (int)$store_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	}	
	
	public function getMembers($store_id=0) {
		$members_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_grouporder_members WHERE store_id = '" . (int)$store_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		
		if ($members_query->num_rows) {
                        $members = array();
                        foreach ($members_query->rows as $result) {
			 $members[] = array('member_id'=> $result ['member_id'],'customer_id'=> $result ['customer_id'],'store_id'=> $result ['store_id'],'member_name'=> $result ['member_name'],'member_email'=> $result ['member_email']);
                        }
                        return $members;
		} else {
			return false;	
		}
	}
        public function setGrouporder($membersids,$store_id=0) {
            
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_grouporder SET customer_id = '" . (int)$this->customer->getId() . "', grouporder_time = NOW(), store_id = '" . $store_id . "'");
            $grouporderid = $this->db->getLastId();
            $customer_name = $this->customer->getFirstName() .' ' . $this->customer->getLastName();
            foreach ($membersids as $membersid) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "customer_grouporder_client SET grouporder_id = '" . $grouporderid . "',  member_id = '" . (int) $membersid . "'");
                $grouporder_client_id = $this->db->getLastId();
                
                $member_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_grouporder_members WHERE member_id = '" . (int)$membersid . "'");
                if ($member_query->num_rows) {
                    
                    
                    $this->language->load('mail/grouporder');

                    $subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

                    $message = sprintf($this->language->get('text_welcome'), $member_query->row['member_name']) . "\n\n";

                    $message .= sprintf($this->language->get('text_welcome_message'), $this->config->get('config_name')).  "\n\n";
                    $message .= sprintf($this->language->get('text_services'),$customer_name) . "\n\n";
                    $message .= $this->language->get('text_thanks') . "\n";
                    $message .= $customer_name . "\n\n"; 
                    $message .= sprintf($this->language->get('text_group_bottom'), $this->url->link('account/grouporder/clinetgorders', 'goclientid='. $grouporder_client_id  . '', 'SSL'),$this->config->get('config_name')) ;

                    $mail = new Mail();
                    $mail->protocol = $this->config->get('config_mail_protocol');
                    $mail->parameter = $this->config->get('config_mail_parameter');
                    $mail->hostname = $this->config->get('config_smtp_host');
                    $mail->username = $this->config->get('config_smtp_username');
                    $mail->password = $this->config->get('config_smtp_password');
                    $mail->port = $this->config->get('config_smtp_port');
                    $mail->timeout = $this->config->get('config_smtp_timeout');				
                    $mail->setTo($member_query->row['member_email']);
                    $mail->setFrom($this->config->get('config_email'));
                    $mail->setSender($this->config->get('config_name'));
                    $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                  //  echo $message ;
                   // echo PHP_EOL.  '<br />';
                    $mail->send();
                   // echo $this->url->link('account/grouporder/clinetgorders', 'goclientid='. $grouporder_client_id  . '', 'SSL');
                }
            }
            return $grouporderid;
        }
       public function getGrouporder($grouporderid) {
           $this->clientProductsToCart($grouporderid);
           $members_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_grouporder_members m, " . DB_PREFIX . "customer_grouporder_client c WHERE c.grouporder_id = '" . (int)$grouporderid . "' AND c.member_id = m. member_id");
		
		if ($members_query->num_rows) {
                        
                        return $members_query->rows;
		} else {
			return false;	
		}
       }
        public function getPendingGrouporder($store_id=0) {
              $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_grouporder WHERE grouporder_time < DATE_SUB(NOW(),INTERVAL 1 DAY) AND   customer_id = '" . (int)$this->customer->getId() . "' AND grouporder_status=0");
              if ($query->num_rows) {
                  foreach ($query->rows as $grouporder_id) {
                      $this->db->query("UPDATE " . DB_PREFIX . "customer_grouporder SET grouporder_status=1 WHERE grouporder_id = '" . (int)$grouporder_id . "'");
                      $this->db->query("UPDATE " . DB_PREFIX . "customer_grouporder_client SET grouporder_stage='closed' WHERE grouporder_id = '" . (int)$grouporder_id . "'");
                  }
              }
              $grouporder_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_grouporder WHERE customer_id = '" . (int)$this->customer->getId() . "' AND store_id='". (int)$store_id ."' AND grouporder_status=0");
		
		if ($grouporder_query->num_rows) {
                        
                        return $grouporder_query->row['grouporder_id'];
		} else {
			return false;	
		}
        }
         public function deleteGrouporder($grouporderid) {
           $this->db->query("UPDATE " . DB_PREFIX . "customer_grouporder SET grouporder_status=1 WHERE grouporder_id = '" . (int)$grouporderid . "'");
           $this->db->query("UPDATE " . DB_PREFIX . "customer_grouporder_client SET grouporder_stage='closed' WHERE grouporder_id = '" . (int)$grouporderid . "'");
           return true;
       }
       public function isGrouporderClient($goclientid) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_grouporder_client WHERE    grouporder_client_id = '" . (int)$goclientid. "' AND grouporder_stage<>'closed'");
              if ($query->num_rows) {
                  $this->db->query("UPDATE " . DB_PREFIX . "customer_grouporder_client SET grouporder_stage='visit' WHERE     grouporder_client_id = '" . (int)$goclientid. "'");
                  $member = $this->db->query("SELECT c.firstname,c.lastname,m.member_name  FROM " . DB_PREFIX . "customer_grouporder_members m, " . DB_PREFIX . "customer c WHERE m.member_id = '" . (int)$query->row['member_id'] . "' AND c.customer_id  = m.customer_id ");
                  if ($member->num_rows) {
                     $this->session->data['groupordermember'] = $member->row;
                  }
                  return true;
              }
              else {
                  return false;
              }
       }
       public function addProductsGrouporderClient($goclientid) {
           if ($this->cart->hasProducts()) {
               $products = $this->cart->getProducts();
               $client_products = array();
               foreach($products as $product) {
                   $product_key = explode(':', $product['key']);
		
				// Options
				if (isset($product_key[1])) {
					$options = unserialize(base64_decode($product_key[1]));
				} else {
					$options = array();
				} 
                   $client_products[] = array('product_id' => $product['product_id'],'quantity'=>$product['quantity'],'options'=>$options);
               }
           
               $this->db->query("UPDATE " . DB_PREFIX . "customer_grouporder_client SET grouporder_stage='order' , cart_products ='". base64_encode(serialize($client_products)) ."'  WHERE     grouporder_client_id = '" . (int)$goclientid. "'");
               return true; 
           }
           else {
               return false;
           }
       }
       public function clientProductsToCart($grouporderid) {
             $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_grouporder_client WHERE    grouporder_id = '" . (int)$grouporderid. "' AND grouporder_stage='order'");
              if ($query->num_rows) {
                  foreach($query->rows as $grouporder_client) {
                    $products = unserialize(base64_decode($grouporder_client['cart_products']));
                    foreach($products as $product) {
                         $this->cart->add($product['product_id'], $product['quantity'], $product['options']);
                    }
                    
                    $this->db->query("UPDATE " . DB_PREFIX . "customer_grouporder_client SET grouporder_stage='closed' WHERE     grouporder_client_id = '" . (int)$grouporder_client['grouporder_client_id']. "'");
                  }
              }
              
       }

}
?>