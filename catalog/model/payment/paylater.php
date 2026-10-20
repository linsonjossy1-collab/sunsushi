<?php 
class ModelPaymentPaylater extends Model {
    public function getMethod($address, $total) {
            $this->load->language('payment/paylater');

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('paylater_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

            if ($this->config->get('paylater_total') > $total) {
                   $status = false;
            } elseif  (!empty($this->session->data['vouchers'])) {
                   $status = false;
            } elseif (!$this->config->get('paylater_geo_zone_id')) {
                    $status = true;
            } elseif ($query->num_rows) {
                    $status = true;
            } else {
                    $status = false;
            }

            $method_data = array();
            if(!isset($this->session->data['tableordering']) || !isset($this->session->data['dine_table_id'])){		
                $status = false;
            }

            if ($status) {  
                $method_data = array( 
                        'code'       => 'paylater',
                        'title'      => $this->language->get('text_title'),
                        'sort_order' => $this->config->get('paylater_sort_order')
                );
            }

        return $method_data;
    }  	        
}
?>