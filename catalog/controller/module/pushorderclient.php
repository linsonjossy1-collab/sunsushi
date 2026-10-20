<?php

class ControllerModulePushorderclient extends Controller {

    public function index() {
       $json =array();
       if ($this->config->get('pushorderclient_onlineurl') && $this->config->get('pushorderclient_securitykey') && $this->config->get('pushorderclient_status')) {
            $last_updated_orderid = $this->getLastOrderUpdated();
            if ($last_updated_orderid !== false) {
                $orders= $this->push($last_updated_orderid);
                //print_r($orders);
               // $json ['order_ids']= $orders;
                echo "kk";
            }else{
                echo "ss";
                $json ['success']='ok';
            }
        }
         echo json_encode($json);
       // $this->response->setOutput(json_encode($json)); 
      exit();
    }
    public function pushorderonline() {
         $json =array();
       
            $last_updated_orderid = $this->getLastOrderUpdated();
            if ($last_updated_orderid !== false) {
                $orders= $this->push($last_updated_orderid);
                //print_r($orders);
               // $json ['order_ids']= $orders;
                 $json ['success']='ok';
            }else{
                $json ['error']='ok';
            }
 
            echo json_encode($json);
        exit();
    }
    public function checkneworders() {
         $json =array();
        if ($this->config->get('pushorderclient_onlineurl') && $this->config->get('pushorderclient_securitykey') && $this->config->get('pushorderclient_status')) {
            $last_updated_orderid = $this->getLastOrderUpdated();
            $order_ids = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` o WHERE o.order_id > '" . (int) $last_updated_orderid . "' AND o.order_status_id >0 ORDER BY o.order_id");
             if ($order_ids->num_rows) {
                  $json ['success']=  count($order_ids->rows);
             }else{
                  $json ['error']='ok';
             }
        }else{
             $json ['error']='ok';
        }    
        echo json_encode($json);
        //$this->response->setOutput(json_encode($json)); 
        //die();
      exit();
    }
    public function checklastupdatedid() {
         $json =array();
        $order_ids = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` o WHERE  o.order_status_id >0 ORDER BY o.order_id DESC LIMIT 1");
       if ($order_ids->num_rows) {
            if ($this->config->get('posonline_last_updated_id') == $order_ids->row['order_id'] or $this->config->get('posonline_last_updated_id') > $order_ids->row['order_id']){
                $json['response']=false;
            }else{
                    $json['response']=true;
            }
       }else{
            $json['response']=false;
       }
       echo json_encode($json);
       exit();  
    }
    private function getLastOrderUpdated() {
        $json = $this->serve_request('lastorder');
        //echo $json['lastorder'];
        //
        if (isset($json['lastorder'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND `group` = 'posonline'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$this->config->get('config_store_id') . "', `group` = 'posonline', `key` = 'posonline_last_updated_id', `value` = '" .(int)$json['lastorder'] . "'");
            return $json['lastorder'];
        } else {
            return false;
        }
    }

    private function push($last_updated_orderid) {
        $order_ids = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` o WHERE o.order_id > '" . (int) $last_updated_orderid . "' AND o.order_status_id >0 ORDER BY o.order_id  ASC LIMIT 5");
        //echo "SELECT order_id FROM `" . DB_PREFIX . "order` o WHERE o.order_id > '" . (int) $last_updated_orderid . "' ORDER BY o.order_id  ASC LIMIT 5";
        if ($order_ids->num_rows) {
            $this->load->model('checkout/order');
            $orders = array();
            foreach ($order_ids->rows as $id) {
                $order_id = $id['order_id'];
                $order = $this->model_checkout_order->getOrder($order_id);
                $order_products = $this->getOrderProducts($order_id);
                foreach ($order_products as $key => $order_product) {
                    $order_option = $this->getOrderOptions($order_id, $order_product['order_product_id']);
                    $order_product['option'] = $order_option;
                    //Bundled 
                    $bundled_products = $this->getOrderBundleProducts($order_id, $order_product['order_product_id']);
                    $order_product['download'] = array();
                    $order_product['bundle'] = array();
                    if (!empty($bundled_products)) {
                        // print_r($bundled_products);
                        $bundled_data = array();
                        foreach ($bundled_products as $k => $bundled_product) {
                            $bundled_data[$k] = $bundled_product;
                            $bundled_product_options = $this->getOrderBundledProductoptions($bundled_product['order_bundle_product_id']);
                            if (!empty($bundled_product_options)) {
                                $bundled_data[$k]['option'] = $bundled_product_options;
                            }
                        }
                        $order_product['bundle'] = $bundled_data;
                    }
                    //
                    $order['products'][] = $order_product;
                }
                $order['totals'] = $this->getOrderTotals($order_id);
                 $order['vouchers']=array();
                if(!isset($order['customer_group_id'])) {
                    $order['customer_group_id']=0;
                }
                if(!isset($order['payment_company_id'])) {
                    $order['payment_company_id']=0;
                }
                if(!isset($order['payment_tax_id'])) {
                    $order['payment_tax_id']=0;
                }
                if(!isset($order['affiliate_id'])) {
                    $order['affiliate_id']=0;
                }
                if(!isset($order['commission'])) {
                    $order['commission']=0;
                }
                //print_r($order);
                $orders[] = $order;
            }
           // print_r($orders);
            return $this->serve_request('push', array('orders' => base64_encode(json_encode($orders))));
        } else {
            return array();
        }
    }

    private function serve_request($method, $params = array()) {
        $json = array();
        $url_array = array('security_key' => $this->config->get('pushorderclient_securitykey'), 'method' => $method) + $params;
        // print_r($url_array);
        $data = http_build_query($url_array, '', '&');

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $this->config->get('pushorderclient_onlineurl') . 'index.php?route=module/pushorderserver');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $responseBody = curl_exec($curlHandle);
        $responseInfo = curl_getinfo($curlHandle);
        curl_close($curlHandle);
        if ($responseInfo['http_code'] == 200) { // suscuss
            //echo $responseBody;
            $out = json_decode($responseBody, true);
            if (!isset($out['error']) && is_array($out)) {
                
                $json = $out;
                
            } else {
                
                // print_r($out);
            }
        }
        return $json;
    }

    private function getOrderProducts($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");

        return $query->rows;
    }

    private function getOrderOptions($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $order_product_id . "'");

        return $query->rows;
    }

    private function getOrderBundleProducts($order_id, $product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int) $order_id . "' AND order_product_id='" . (int) $product_id . "'");

        return $query->rows;
    }

    private function getOrderBundledProductoptions($order_bundle_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle_option WHERE order_bundle_product_id = '" . (int) $order_bundle_product_id . "'");

        return $query->rows;
    }

    private function getOrderTotals($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order");

        return $query->rows;
    }

}

?>