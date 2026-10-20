<?php
class ModelPaymentTakepaymentsGateway extends Model {
  public function getMethod($address, $total) {
    $this->load->language('payment/takepayments_gateway');
    $query = $this->db->query(
      "SELECT zone_to_geo_zone_id FROM " . DB_PREFIX . "zone_to_geo_zone
      WHERE geo_zone_id = '" . (int) $this->config->get('takepayments_gateway_geo_zone') . "'
      AND country_id = '" . (int) $address['country_id'] . "'
      AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')"
    );
    if(!$this->config->get('takepayments_gateway_geo_zone'))
    {
      $status = true;
    }
    elseif($query->num_rows)
    {
      $status = true;
    }
    else
    {
      $status = false;
    }
    $method_data = array();
    if($status)
    {
      $method_data = array(
        'code'     => 'takepayments_gateway',
        'title'    => $this->language->get('text_title'),
        'sort_order' => $this->config->get('takepayments_gateway_sort_order'),
        'commision' => ($this->config->get('takepayments_gateway_sort_order')?sprintf($this->language->get('text_payment_commision_charges'), $this->currency->format( $this->config->get('takepayments_gateway_sort_order'))):'')
      );
    }
    return $method_data;
  }
}
?>
