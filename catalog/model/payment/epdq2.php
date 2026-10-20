<?php 
class ModelPaymentePDQ2 extends Model {
  	public function getMethod($address) {
		$this->language->load('payment/epdq2');
		
		if ($this->config->get('epdq2_status')) {
      		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('epdq2_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
			
			if (!$this->config->get('epdq2_geo_zone_id')) {
        		$status = TRUE;
      		} elseif ($query->num_rows) {
      		  	$status = TRUE;
      		} else {
     	  		$status = FALSE;
			}	
      	} else {
			$status = FALSE;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'         => 'epdq2',
        		'title'      => $this->language->get('text_title'),
			'sort_order' => $this->config->get('epdq2_sort_order'),
                        'commision' => ($this->config->get('epdq2_commision')?sprintf($this->language->get('text_payment_commision_charges'), $this->currency->format( $this->config->get('epdq2_commision'))):'') 
      		);
    	}
   
    	return $method_data;
  	}
}
?>