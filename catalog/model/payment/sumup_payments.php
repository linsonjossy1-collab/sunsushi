<?php 
class ModelPaymentSumupPayments extends Model {
	public function getMethod($address, $total) {
		$this->language->load('payment/sumup_payments');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('sumup_payments_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('sumup_payments_total') > 0 && $this->config->get('sumup_payments_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('sumup_payments_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}	

		$method_data = array();

		if ($status) {  
			$method_data = array(
				'code'       => 'sumup_payments',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('sumup_payments_sort_order'),
                                'commision' => ($this->config->get('sumup_payments_commision')?sprintf($this->language->get('text_payment_commision_charges'), $this->currency->format( $this->config->get('sumup_payments_commision'))):'') 
			);
		}

		return $method_data;
	}
        
        
}
?>