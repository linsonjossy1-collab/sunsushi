<?php
class ModelPaymentHandpointNetwork extends Model {
  public function getMethod($address, $total) {
    $this->load->language('payment/handpoint_network');
    $query = $this->db->query(
      "SELECT zone_to_geo_zone_id FROM " . DB_PREFIX . "zone_to_geo_zone
      WHERE geo_zone_id = '" . (int) $this->config->get('handpoint_network_geo_zone') . "'
      AND country_id = '" . (int) $address['country_id'] . "'
      AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')"
    );
    if(!$this->config->get('handpoint_network_geo_zone'))
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
        'code'     => 'handpoint_network',
        'title'    => $this->language->get('text_title'),
        'sort_order' => $this->config->get('handpoint_network_sort_order'),
        'commision' => ($this->config->get('handpoint_network_commision')?sprintf($this->language->get('text_payment_commision_charges'), $this->currency->format( $this->config->get('handpoint_network_commision'))):'')
      );
    }
    return $method_data;
  }
  public function setThreeDSRef($order_id, $threeDSRef){
      if($this->getThreeDSRef($order_id)){
          // UPDATE
          $this->db->query("UPDATE " . DB_PREFIX . "handpoint_network_threeds SET threedsref = '" . $this->db->escape($threeDSRef)  . "' WHERE order_id  = '" . (int)$order_id . "'");
      }else {
          // NEW
          $this->db->query("INSERT INTO " . DB_PREFIX . "handpoint_network_threeds SET order_id = '" . $order_id . "', threedsref = '" . $this->db->escape($threeDSRef) . "'");
      }
  }
  public function getThreeDSRef($order_id){
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "handpoint_network_threeds  WHERE order_id = '" . (int) $order_id . "'");
      if($query->row){
          return $query->row['threedsref'];
      }else {
          return false;
      }
  }
}
?>
