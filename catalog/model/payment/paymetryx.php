<?php
class ModelPaymentPaymetryx extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/paymetryx');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('paymetryx_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('paymetryx_total') > 0 && $this->config->get('paymetryx_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('paymetryx_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'paymetryx',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('paymetryx_sort_order'),
                                'commision' => ($this->config->get('paymetryx_commision')?sprintf($this->language->get('text_payment_commision_charges'), $this->currency->format( $this->config->get('paymetryx_commision'))):'') 
			);
		}

		return $method_data;
	}

	
}