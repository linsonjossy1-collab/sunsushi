<?php

class ModelDrivermanagementDriverdetails extends Model {

    public function getDriverDetails($driver_id) {
        $sql = $this->db->query("SELECT driver_id,driver_name,driver_mobile FROM " . DB_PREFIX . "driver r WHERE driver_id='" . (int) $driver_id . "'");
        return $sql->rows;
    }

    public function getDrivertodayorder($driver_id) {
        $sql = ("SELECT d.*, o.firstname, o.lastname,o.shipping_address_1,o.shipping_address_2,o.shipping_city,o.telephone,o.order_status_id,o.requested_time,s.name,s.order_status_id,p.latitude,p.longitude FROM " . DB_PREFIX . "deliverys d LEFT JOIN `" . DB_PREFIX . "order` o ON o.order_id=d.order_id LEFT JOIN " . DB_PREFIX . "uk_postcodes p ON p.postcode=o.shipping_postcode LEFT JOIN " . DB_PREFIX . "order_status s ON s.order_status_id=o.order_status_id WHERE (driver_id = '" . (int) $driver_id . "' AND time_assigned >= CURDATE() AND (o.order_status_id IN ( '" . implode($this->config->get('drivermanagement_complete_status'), "', '") . "' ) OR o.order_status_id IN ( '" . implode($this->config->get('drivermanagement_cancel_status'), "', '") . "' ))) ORDER BY order_id ");
        $order = $this->db->query($sql);
        return $order->rows;
    }

    public function getDriverliveorder($driver_id) {
        $timein = $this->db->query("SELECT driver_clock_in FROM " . DB_PREFIX . "driver_clock_inout  dc  WHERE dc.driver_id='" . $driver_id . "' AND  dc.driver_clock_in >= CURDATE() AND dc.driver_clock_out IS NULL");
        $time_in = $timein->rows;
        foreach ($time_in as $time) {
            $sql = ("SELECT d.*, o.firstname, o.lastname,o.shipping_address_1,o.shipping_address_2,o.shipping_city,o.telephone,o.order_status_id,o.requested_time,s.name,s.order_status_id,p.latitude,p.longitude FROM " . DB_PREFIX . "deliverys d LEFT JOIN `" . DB_PREFIX . "order` o ON o.order_id=d.order_id LEFT JOIN " . DB_PREFIX . "uk_postcodes p ON p.postcode=o.shipping_postcode LEFT JOIN " . DB_PREFIX . "order_status s ON s.order_status_id=o.order_status_id WHERE (d.driver_id = '" . (int) $driver_id . "' AND  ('" . $time[driver_clock_in] . "'<= d.time_assigned) && (d.time_assigned<=NOW())) AND o.order_status_id  NOT IN ( '" . implode($this->config->get('drivermanagement_complete_status'), "', '") . "' ) AND o.order_status_id NOT IN ( '" . implode($this->config->get('drivermanagement_cancel_status'), "', '") . "' ) ORDER BY order_id ");
            $order = $this->db->query($sql);
        }
        return $order->rows;
    }

    public function DriverAttendance($driver_id) {
        $qry = $this->db->query("SELECT  driver_clock_in,driver_clock_out FROM " . DB_PREFIX . "driver_clock_inout WHERE driver_id = '" . (int) $driver_id . "'");
        return $qry->rows;
    }

    public function getStatus($data) {
        foreach ($data as $status_id) {
            $status = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . " order_status WHERE order_status_id='" . (int) $status_id . "'");
            $statuses = $status->rows;
            foreach ($statuses as $statuss) {
                $order_status[] = $statuss;
            }
        }
        return $order_status;
    }

    public function getDeliveryHistory($driver_id, $date) {
        $sql = ("SELECT d.*, o.requested_time, o.total, s.name FROM " . DB_PREFIX . "deliverys d LEFT JOIN `" . DB_PREFIX . "order` o ON o.order_id=d.order_id LEFT JOIN " . DB_PREFIX . "order_status s ON s.order_status_id=o.order_status_id WHERE (driver_id = '" . (int) $driver_id . "' ) AND time_assigned LIKE '" . $date . "%'");
        $order = $this->db->query($sql);
        return $order->rows;
    }

    public function DriverOrders($driver_id, $date) {
        $qry = $this->db->query("SELECT  order_id FROM " . DB_PREFIX . "deliverys WHERE driver_id = '" . (int) $driver_id . "' AND time_assigned LIKE '" . $date . "%'");
        return $qry->rows;
    }

    public function DriverMessages($order_id) {
        $qry = $this->db->query("SELECT  * FROM " . DB_PREFIX . "order_message WHERE order_id = '" . (int) $order_id . "'");
        return $qry->rows;
    }

    public function isDriverCheckedin($driver_id) {
        $status = FALSE;
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "driver_clock_inout  dc  WHERE dc.driver_id='" . $driver_id . "' AND  dc.driver_clock_in >= CURDATE() AND dc.driver_clock_out IS NULL ORDER BY driver_clock_id DESC LIMIT 1");
        if (!empty($query->row)) {
            $status = TRUE;
        }
        return $status;
    }

    public function Deliverydetails($data) {
        $c_status = $this->config->get('drivermanagement_complete_status');
        foreach ($this->config->get('drivermanagement_driver_status') as $d_status) {
            if ($data['status_id'] != $d_status) {
                $this->UpdateOrderStatus($data['driver_id'], $data['order_id'], $d_status);
            }  
            if ($data['status'] == '' && ($data['status'] != $d_status)) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $c_status[0] . "' WHERE order_id = '" . (int) $data['order_id'] . "'");
        }
        }
        $c_name = array(date('Y-m-d', time()), $data['delivered_time']);
        $time = implode(" ", $c_name);
        if ($data['cash_received'] > $data['cash']) {
            $cash = $data['cash_received'] - $data['cash'];
            $trans_type = 'T';
            $this->addPettycash($data['driver_id'], $trans_type, $cash, $data['order_id']);
        }
        if ($data['cash']) {
            $trans_type = 'R';
            $this->addPettycash($data['driver_id'], $trans_type, $data['cash'], $data['order_id']);
        }
        
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET delivery_time = '" . $time . "' WHERE order_id = '" . (int) $data['order_id'] . "'");
        $this->db->query("UPDATE " . DB_PREFIX . "deliverys SET time_delivered = '" . $time . "', cash_received = '" . $data['cash'] . "', delivery_comment ='" . $data['comment'] . "' WHERE order_id = '" . (int) $data['order_id'] . "'");
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET delivery_time = '" . $time . "' WHERE order_id = '" . (int) $order_id . "'");
        return;
    }

    public function addPettycash($driver_id, $trans_type, $pettycash_amount, $order_id) {
        if ($order_id) {
            $query = $this->db->query("SELECT delivery_id FROM " . DB_PREFIX . "deliverys d WHERE order_id = '" . (int) $order_id . "'");
            $delivery_id = $query->row;
            $d_id = $delivery_id['delivery_id'];
        } else {
            $d_id = '0';
        }
        if ($trans_type == 'T' || $trans_type == 'N') {
            if ($trans_type == 'N') {
                $this->db->query("UPDATE " . DB_PREFIX . "deliverys SET driver_commission= '" . $this->db->escape($pettycash_amount) . "' WHERE delivery_id = '" . (int) $d_id . "'");
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET commission= '" . $this->db->escape($pettycash_amount) . "' WHERE order_id = '" . (int) $order_id . "'");
            }
            $this->db->query("INSERT INTO " . DB_PREFIX . "driver_petty_cash SET driver_id = '" . (int) $driver_id . "', dpc_time = NOW(), dpc_trans_type = '" . $trans_type . "', delivery_id = '" . $d_id . "', dpc_cr = '" . $this->db->escape($pettycash_amount) . "', dpc_dr = '" . $this->db->escape($pettycash_amount) . "'");
        } else {
            $type = $this->db->query("SELECT transaction_type FROM " . DB_PREFIX . "driver_pettycash_transaction_type WHERE transction_index='" . $trans_type . "'");
            $cash_type = $type->row;
            if (!empty($cash_type) && $cash_type['transaction_type'] == 'credit') {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . (int) $driver_id . "', dpc_time =NOW(), dpc_trans_type = '" . $trans_type . "', delivery_id = '" . $d_id . "', dpc_cr = '" . $this->db->escape($pettycash_amount) . "'");
            } else {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . (int) $driver_id . "', dpc_time =NOW(), dpc_trans_type = '" . $trans_type . "', delivery_id = '" . $d_id . "', dpc_dr = '" . $this->db->escape($pettycash_amount) . "'");
            }
        }
        return $this->db->getLastId();
    }

    public function UpdateOrderStatus($driver_id, $order_id, $status_id,$order_comment='') { 
        $data = array('order_status_id'=>$status_id,'comment'=>$order_comment);
        $this->load->model('sale/order');
        $this->model_sale_order->addOrderHistory($order_id, $data);
                
       // $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $status_id . "' WHERE order_id = '" . (int) $order_id . "'");
        foreach ($this->config->get('drivermanagement_driver_status')as $status) {
            if ($status_id == $status) {
                $query = $this->db->query("SELECT order_id, payment_code, total FROM `" . DB_PREFIX . "order` o WHERE order_id = '" . (int) $order_id . "'");
                $odrs = $query->rows;
                foreach ($odrs as $ordr) {
                    if ($ordr['payment_code'] == 'cod') {
                        $trans_type = 'O';
                        $this->addPettycash($driver_id, $trans_type, $ordr['total'], $ordr['order_id']);
                    }
                }
            } else {
                foreach ($this->config->get('drivermanagement_cancel_status')as $status) {
                    if ($status_id == $status) {
                        $delivery_id = $this->getDelivery_id($order_id);
                        $this->db->query("DELETE FROM " . DB_PREFIX . "driver_petty_cash WHERE delivery_id = '" . (int) $delivery_id['delivery_id'] . "' AND dpc_trans_type = 'O'");
                    }
                }
            }
       }
        $this->websocket->connect('d-'.$driver_id);
        return;
    }

    public function getDelivery_id($order_id) {
        $query = $this->db->query("SELECT delivery_id FROM " . DB_PREFIX . "deliverys d WHERE order_id = '" . (int) $order_id . "'");
        return $query->row;
    }

    public function getDriverliveorderstatus($driver_id) {
        $timein = $this->db->query("SELECT driver_clock_in FROM " . DB_PREFIX . "driver_clock_inout  dc  WHERE dc.driver_id='" . $driver_id . "' AND  dc.driver_clock_in >= CURDATE() AND dc.driver_clock_out IS NULL");
        $time_in = $timein->rows;
        foreach ($time_in as $time) {
            $sql = ("SELECT d.order_id FROM " . DB_PREFIX . "deliverys d LEFT JOIN `" . DB_PREFIX . "order` o ON o.order_id=d.order_id WHERE (d.driver_id = '" . (int) $driver_id . "' AND  ('" . $time[driver_clock_in] . "'<= d.time_assigned) && (d.time_assigned<=NOW())) AND o.order_status_id  IN ( '" . implode($this->config->get('drivermanagement_alert_status'), "', '") . "' ) ORDER BY order_id ");
            $order = $this->db->query($sql);
        }
        return $order->rows;
    }
    public function getDriverDetailsUserID($user_id) {
        $sql = $this->db->query("SELECT driver_id,driver_name,driver_mobile FROM " . DB_PREFIX . "driver r WHERE user_id='" . (int) $user_id . "'");
        return $sql->row;
    }
}
