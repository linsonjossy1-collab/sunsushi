<?php
class ModelShippingCollect extends Model {
	function getQuote($address) {
		$this->load->language('shipping/collect');
		
                //payemnt or delivery 
        /*
                $status_chk=false;
        if($this->cart->hasProducts()){
            $message='No Collect From Store to the  products ';
            foreach ($this->cart->getProducts() as $product){
                if(!empty($product['shipping_methodes']) and !in_array('collect.collect', $product['shipping_methodes'])){//
                    $status_chk=true;
                    $message .=$product['name'].',';
                } 
            }
            if($status_chk){
                 $method_data = array(
                'code' => '',
                'title' => '',
                'quote' => '',
                'sort_order' => $this->config->get('collect_sort_order'),
                'error' => false,
                'delivery_msg_status' =>  $message 
            );
     
            return $method_data;
            }
        }
        */
        //
                
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('collect_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
	
		if (!$this->config->get('collect_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
//if ($this->config->get('home_distances')>$_SESSION['postcode_distance']) {
//			$status = false;
//		}
		$method_data = array();
	
		if ($status) {
			$quote_data = array();
			
      		$quote_data['collect'] = array(
        		'code'         => 'collect.collect',
        		'title'        => $this->language->get('text_description'),
        		'cost'         => 0.00,
        		'tax_class_id' => 0,
				'text'         => $this->currency->format(0.00)
      		);

      		$method_data = array(
        		'code'       => 'collect',
        		'title'      => $this->language->get('text_title'),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('collect_sort_order'),
        		'error'      => false
      		);
		}
	
		return $method_data;
	}
}
?>