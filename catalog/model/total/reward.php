<?php
class ModelTotalReward extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
            
            if ($this->config->get('reward_points_status') && (isset($this->session->data['reward']) || isset($this->session->data['reward_free_products'])) ) {
                $this->log->write('Reward Total  session reward: ' . print_r($this->session->data['reward'],true) );
                $this->log->write('Reward Total  session free_products: ' . print_r($this->session->data['reward_free_products'],true) );
                
                $this->load->language('total/reward');
                $points = $this->customer->getRewardPoints();
                // FREE PRODUCTS
                if(isset($this->session->data['reward_free_products'])) {
                    $discount_total = 0;				
                    $points_total = 0;
                    $free_reward = '';
                    unset($this->session->data['reward_point']);
                    $cart_product = array();
                    $free_product = array();
                    foreach($this->cart->getProducts() as $cartproduct) {
                        if(isset($this->session->data['reward_free_products'][$cartproduct['product_id']])) {
                            if($this->session->data['reward_free_products'][$cartproduct['product_id']]['qty']) {
                                $query = $this->db->query("SELECT price FROM ".DB_PREFIX."product WHERE product_id = '".$cartproduct['product_id']."' AND status = 1 ");
                                if(!isset($query->row['price'])) {
                                    continue;
                                }
                                $cart_product[$cartproduct['product_id']] = array('name' => $cartproduct['name'], 'price' => $query->row['price'], 'quantity' => $cartproduct['quantity']);		
                                if($this->session->data['reward_free_products'][$cartproduct['product_id']]['qty']>$cartproduct['quantity']) {
                                    $this->session->data['reward_free_products'][$cartproduct['product_id']]['qty'] =  $cartproduct['quantity'];
                                    $cart_product[$cartproduct['product_id']]['qty'] = $cartproduct['quantity'];
                                } else {
                                    $cart_product[$cartproduct['product_id']]['qty'] = $this->session->data['reward_free_products'][$cartproduct['product_id']]['qty'];
                                }
                            }
                        }
                    }
                    if($cart_product) {
                        $free_count = 0;
                        foreach($this->config->get('config_free_products') as $freegroup) {
                            if($freegroup['amount'] <= $points) {
                                foreach($freegroup['free_products'] as $product) {
                                    if( $this->config->get('config_free_product_multiple') && ($free_count == $this->config->get('config_free_product_multiple'))) {
                                        break 2;
                                    }
                                    if(isset($cart_product[$product])) { 
                                        if( $this->config->get('config_free_product_multiple') && (($free_count + $cart_product[$product]['qty']) > $this->config->get('config_free_product_multiple'))) {
                                            // FREE QTY GREATER SET TO MAX FREE COUNT. 
                                            $cart_product[$product]['qty'] = $this->config->get('config_free_product_multiple') - $free_count; 
                                        }
                                        $current_discount_amount = $cart_product[$product]['price'] * $cart_product[$product]['qty'];
                                        $current_discount_point = $cart_product[$product]['qty']*$freegroup['amount'];

                                        if(($points_total + $current_discount_point) <= $points) {
                                            $free_count +=  $cart_product[$product]['qty'];
                                            $points_total += $current_discount_point;
                                            $discount_total += $current_discount_amount;
                                            $free_reward .= ($free_reward?', ':'').$cart_product[$product]['name'] ; // .'('.$cart_product[$product]['qty'].' x&nbsp; '.$freegroup['amount'].' points) ';
                                            if(isset($this->session->data['reward_point'])){
                                                $this->session->data['reward_point'] += $current_discount_point;
                                            }else{
                                                $this->session->data['reward_point'] = $current_discount_point;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                    }
                    if($discount_total) {
                        $total_data[] = array(
                        'code'       => 'reward',
                        'title'      => $free_reward . ' '. sprintf($this->language->get('text_reward'), $this->session->data['reward_point']),//.$free_reward,
                        'text'       => $this->currency->format(-$discount_total),
                        'value'      => -$discount_total,
                        'sort_order' => $this->config->get('reward_sort_order')
                        );    
                        $total -= $discount_total;
                        $points -=$points_total; // Minus Free products points
                    }
                }
                // POINT REDEEM
                if ($this->config->get('config_redeem_amount') &&  $this->session->data['reward'] && $this->session->data['reward'] <= $points) {
                    $discount_total = 0;				
                    //$points_total = 0;
                    $free_reward = '';
                    //unset($this->session->data['reward_point']);


                    $this->session->data['reward_point'] = $this->session->data['reward']; // - $points_total;
                    $discount_total+= ($this->session->data['reward_point']) / $this->config->get('config_redeem_points_points');
//                                } else {
//                                        $this->session->data['reward'] = $points_total;
//                                }


                    if($discount_total) {
                        $total_data[] = array(
                        'code'       => 'reward',
                        'title'      => sprintf($this->language->get('text_reward'), $this->session->data['reward_point']),
                        'text'       => $this->currency->format(-$discount_total),
                        'value'      => -$discount_total,
                        'sort_order' => $this->config->get('reward_sort_order')
                        );

                        $total -= $discount_total;
                    }
                } 
            }
	}
	
	public function confirm($order_info, $order_total) {
		$this->load->language('total/reward');
		
		$points = 0;
		
		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');
		
		if ($start && $end) {  
			$points = substr($order_total['title'], $start, $end - $start);
		}	
		
		if ($points) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$order_info['customer_id'] . "', description = '" . $this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_info['order_id'])) . "', points = '" . (float)-$points . "', date_added = NOW()");				
		}
	}		
}
?>