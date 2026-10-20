<?php
class ModelTotalCustomCharges extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if($this->config->get('custom_charges_status')) {
                    $disc_amt = 0;
                    if($this->config->get('custom_chargedata')){
                        foreach ($this->config->get('custom_chargedata') as $chargedata) {
                            if($chargedata['total']=='S'){
                                $tot_amt = $this->cart->getSubTotal();
                            }else{
                                $tot_amt = $this->cart->getTotal();
                            }
                            if(in_array($this->config->get('config_store_id'), $chargedata['stores'])){
                               
                                $status = true;
                                if(!empty($chargedata['shipping']) && isset($this->session->data['shipping_method'])){
                                    $shipping = explode('.', $this->session->data['shipping_method']['code']);
                                    if(!in_array($shipping[0], $chargedata['shipping'])){
                                        $status=false;
                                    }
                                }
                                if(!empty($chargedata['payments']) && isset($this->session->data['payment_method'])){
                                     
                                    if(!in_array($this->session->data['payment_method']['code'], $chargedata['payments'])){
                                        $status=false;
                                    }
                                }
                                if($status){
                                    switch ($chargedata['option']){
                                        case 1:
                                            $disc_amt =(float)$chargedata['amount'];
                                            $total += $disc_amt;
                                            $total_data[] = array(
                                                'code' => 'custom_charges',
                                                'title' => $chargedata['name'],
                                                'text' => $this->currency->format($disc_amt),
                                                'value' => $disc_amt,
                                                'sort_order' => $this->config->get('custom_charges_sort_order')
                                            );
                                            break;
                                        case 2:
                                            $disc_amt =(float)$chargedata['amount'];
                                            $total -= $disc_amt;
                                            $total_data[] = array(
                                                'code' => 'custom_charges',
                                                'title' => $chargedata['name'],
                                                'text' => $this->currency->format(-$disc_amt),
                                                'value' => -$disc_amt,
                                                'sort_order' => $this->config->get('custom_charges_sort_order')
                                            );
                                            break;
                                        case 3:
                                            $disc_amt = $tot_amt / 100 * $chargedata['amount'];
                                            $total += $disc_amt;
                                            $total_data[] = array(
                                                'code' => 'custom_charges',
                                                'title' => $chargedata['name'],
                                                'text' => $this->currency->format($disc_amt),
                                                'value' => $disc_amt,
                                                'sort_order' => $this->config->get('custom_charges_sort_order')
                                            );
                                            break;
                                        case 4:
                                            $disc_amt = $tot_amt / 100 * $chargedata['amount'];
                                            $total -= $disc_amt;
                                            $total_data[] = array(
                                                'code' => 'custom_charges',
                                                'title' => $chargedata['name'],
                                                'text' => $this->currency->format(-$disc_amt),
                                                'value' => -$disc_amt,
                                                'sort_order' => $this->config->get('custom_charges_sort_order')
                                            );
                                            break;
                                    }
                                    
                                }
                            }
                        }
                    }
                }
	}	
}
?>