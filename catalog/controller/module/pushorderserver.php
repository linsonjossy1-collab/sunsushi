<?php

class ControllerModulePushorderserver extends Controller {

    public function index() {
        $json = array();

        if (!$this->config->get('pushorderserver_status') || !isset($this->request->post['security_key']) || $this->request->post['security_key'] != $this->config->get('pushorderserver_securitykey')) {
            $json['error'] = "auth_error";
        } else {

            if (isset($this->request->post['method'])) {
                switch ($this->request->post['method']) {
                    case 'lastorder':
                        //echo "SELECT offline_order_id FROM `" . DB_PREFIX . "order`  WHERE  store_id=" . $this->config->get('pushorderserver_storeid') . " ORDER BY offline_order_id DESC LIMIT 1 ";
                        $ordes_query = $this->db->query("SELECT offline_order_id FROM `" . DB_PREFIX . "order`  WHERE  store_id=" . $this->config->get('pushorderserver_storeid') . " ORDER BY offline_order_id DESC LIMIT 1 ");
                        if ($ordes_query->row) {
                            $json['lastorder'] = $ordes_query->row['offline_order_id'];
                        } else {
                            $json['lastorder'] = 0;
                        }
                        break;
                    case 'push':
                        $this->push();
                        break;
                }
            } else {
                $json['error'] = "no_method";
            }
        }
        echo json_encode($json);
        exit();
    }

    private function push() {
        $json = array();
        //echo print_r(json_decode(base64_decode($this->request->post['orders'])));  
        if (isset($this->request->post['orders'])) {
            $this->load->model('checkout/order');
            foreach (json_decode(base64_decode($this->request->post['orders'])) as $order) {
                // Insert to online store
                $order =  (array)$order;
                $order['offline_order_id'] = $order['order_id'];
                $order['store_id'] = $this->config->get('pushorderserver_storeid');

                $json['orders'][$order['offline_order_id']] =  $this->model_checkout_order->syncOrder($order);
                
                // Add Order history
            }
            $json['success'] = 'OK';
        }

        echo json_encode($json);
        exit();
    }

}

?>