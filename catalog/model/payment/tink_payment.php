<?php 
class ModelPaymentTinkPayment extends Model {
	public function getMethod($address, $total) {
		$this->language->load('payment/tink_payment');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('tink_payment_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('tink_payment_total') > 0 && $this->config->get('tink_payment_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('tink_payment_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}	

		$method_data = array();

		if ($status) {  
			$method_data = array(
				'code'       => 'tink_payment',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('tink_payment_sort_order'),
                                'commision' => ($this->config->get('tink_payment_commision')?sprintf($this->language->get('text_payment_commision_charges'), $this->currency->format( $this->config->get('tink_payment_commision'))):'') 
			);
		}

		return $method_data;
	}
        
        
}
?>