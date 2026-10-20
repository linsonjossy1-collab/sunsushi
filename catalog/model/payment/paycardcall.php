<?php 
class ModelPaymentPaycardcall extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('payment/paycardcall');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('card_payment_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
	
		if ($this->config->get('paycardcall_total') > $total) {
		       $status = false;
		} elseif  (!empty($this->session->data['vouchers'])) {
                       $status = false;
                } elseif (!$this->config->get('paycardcall_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'paycardcall',
        		'title'      => $this->language->get('text_title'),
			'sort_order' => $this->config->get('paycardcall_sort_order'),
                        'commision' => ($this->config->get('paycardcall_commision')?sprintf($this->language->get('text_payment_commision_charges'), $this->currency->format( $this->config->get('paycardcall_commision'))):'') 
      		);
    	}
   
    	return $method_data;
  	}
}
?>