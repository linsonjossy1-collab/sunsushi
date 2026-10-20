<?php
require_once DIR_SYSTEM .'vendor/fcm/fcm.php';
class ModelDrivermanagementDrivermanagement extends Model {

    public function getOrders() {
        $sql = ("SELECT order_id, firstname, lastname, shipping_address_1, shipping_address_2, shipping_city,telephone, requested_time, total, comment, shipping_postcode  FROM `" . DB_PREFIX . "order` o WHERE (shipping_code = 'home.home' AND (o.order_status_id IN ( '" . implode($this->config->get('drivermanagement_order_status'), "', '") . "' )) AND (order_id NOT IN (SELECT order_id FROM  " . DB_PREFIX . "deliverys d WHERE d.time_assigned >= CURDATE() AND d.time_delivered IS NULL)) ) ORDER BY o.requested_time ASC"); //AND o.requested_time> NOW()
        $order = $this->db->query($sql);
        return $order->rows;
    }

    public function getAssignedOrders() {
        $sql = ("SELECT d.order_id, d.driver_id, d.time_assigned,d.driver_commission, d.total, o.requested_time,o.order_status_id,s.name, t.employee_name FROM " . DB_PREFIX . "deliverys d LEFT JOIN " . DB_PREFIX . "employee t ON (d.driver_id = t.employee_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id=d.order_id) LEFT JOIN " . DB_PREFIX . "order_status s ON s.order_status_id=o.order_status_id WHERE d.time_assigned >= CURDATE() AND (o.order_status_id NOT IN ( '" . implode($this->config->get('drivermanagement_complete_status'), "', '") . "' ) AND o.order_status_id NOT IN ( '" . implode($this->config->get('drivermanagement_cancel_status'), "', '") . "' )) ");
        $order = $this->db->query($sql);
        return $order->rows;
    }

    public function assignDrivertodeliver($driver_id, $order) {
        $orders = (explode(",", $order));
        foreach ($orders as $ordr) {
            $query = $this->db->query("SELECT order_id, payment_code, total FROM `" . DB_PREFIX . "order` o WHERE order_id = '" . (int) $ordr . "'");
            $odrs = $query->rows;
            foreach ($odrs as $odr) {
                $ordrs[] = $odr;
            }
            $odrs = '';
        }
        
        $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE `key` = 'unaassigned_order_track'");
        if(!empty($query->row)){
            $order_deltrack =unserialize($query->row['value']);
        }else{
            $order_deltrack =  array();
        }
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
         $push_order_ids = '';
        foreach ($ordrs as $ordr) {
            $sql = ("INSERT INTO " . DB_PREFIX . "deliverys SET order_id = '" . (int) $ordr['order_id'] . "', driver_id = '" . (int) $driver_id . "',route_id = '" . (int) $ordr['order_id']  . "', time_assigned = NOW(), paid = '" . ($ordr['payment_code'] == 'cod' ? '0' : '1') . "', total = '" . $ordr['total'] . "'");
            $this->db->query($sql);
            $key = array_search((int)$ordr['order_id'], $order_deltrack); 
            if($key!==FALSE){
                unset($order_deltrack[$key]);
            }  
            $push_order_ids .= "#". $ordr['order_id']. " ";
        } 
        if($push_order_ids) { 
            /*
            if($this->config->get('driverappapi_firebase_api_key')) {
                
                $admnapptokens = $this->getFirebaseTokens($driver_id);
                if($admnapptokens) {
                    $push_data = array(
                            'devicetoken' => $admnapptokens,
                            'title' => 'Order Assigned', 
                            'message' => 'Order Assigned '.$push_order_ids,
                            'data' => array(
                                'order_id' => $push_order_ids,
                                'driver_id' => $driver_id,
                                'action' => 'order_assigned',
                                'title'=> 'Order Assigned',
                                'body'=>'Order Assigned '.$push_order_ids,
                            )
                        );
                    $fcm = new FCM($this->config->get('driverappapi_firebase_api_key'));
                    $response = $fcm->send($push_data);
                    $this->log->write('Driver APP -  :'.print_r($response, true));
                }
            }
            */
        }
        
        $ownerpush = "New Orders  #$push_order_ids";
        $notification_data = array( 'title' => 'New Orders',
                    'message' => $ownerpush, 
                    'data' => array(
                            'order_id' => $push_order_ids,
                            'driver_id' => $driver_id,
                            'action' => 'new_order',
                            'title'=> 'New Orders',
                            'body'=>$ownerpush
                    )
            );
        $wbskdata = json_encode(array('driver_id'=>$driver_id,'notification'=>$notification_data ));
        $this->websocket->connect($wbskdata);
        
        $delivery_id = $this->db->getLastId();                      
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'unaassigned_order_track'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'unaassigned_order_track', `value` = '" . $this->db->escape(serialize($order_deltrack)) . "'");
        return $delivery_id;
    } 

    public function UnassignOrder($order_id) {
        $delivery_id = $this->getDelivery_id($order_id);
        $this->db->query("DELETE FROM " . DB_PREFIX . "driver_petty_cash WHERE delivery_id = '" . (int) $delivery_id['delivery_id'] . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "deliverys WHERE order_id = '" . (int) $order_id . "'");
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET commission= '' WHERE order_id = '" . (int) $order_id . "'");
        $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE `key` = 'unaassigned_order_track'");
        if(!empty($query->row)){
            $order_deltrack =unserialize($query->row['value']);
        }else{
            $order_deltrack =  array();
        }
        if(!in_array($order_id, $order_deltrack)){
            $order_deltrack[]=(int)$order_id;
        }
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'unaassigned_order_track'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'unaassigned_order_track', `value` = '" . $this->db->escape(serialize($order_deltrack)) . "'");
        $driver_id = $delivery_id['driver_id'];
        /*
        if($this->config->get('driverappapi_firebase_api_key')) {
            $admnapptokens = $this->getFirebaseTokens($driver_id);
            if($admnapptokens) {
                $push_data = array(
                    'devicetoken' => $admnapptokens,
                    'title' => 'Order Unassigned',
                    'message' => 'Unassigned #'. $order_id, 
                    'data' => array(
                        'order_id' => $order_id,
                        'driver_id' => $driver_id,
                        'action' => 'order_unassigned',
                        'title'=> 'Order Unassigned',
                        'body'=>'Unassigned #'. $order_id, 
                    )
                );
                $fcm = new FCM($this->config->get('driverappapi_firebase_api_key'));
                $response = $fcm->send($push_data);
            }
        }
        */
        $ownerpush = "Unassigned #$order_id";
        $notification_data = array('title' => 'Order Unassigned',
                'message' => $ownerpush, 
                'data' => array(
                        'order_id' => $order_id,
                        'driver_id' => $driver_id,
                        'action' => 'order_unassigned',
                        'title'=> 'Order Unassigned',
                        'body'=>$ownerpush
                )
        );
         
        $wbskdata = json_encode(array('driver_id'=>$driver_id,'notification'=>$notification_data ));
        $this->websocket->connect($wbskdata);
        
        return;
    }

    public function ReassignOrder($order_id, $driver_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
        $old_driver_id = 0;
        $query = $this->db->query("SELECT driver_id FROM " . DB_PREFIX . "deliverys  WHERE order_id = '" . (int) $order_id . "'");
        if(isset($query->row['driver_id'])) {
            $old_driver_id = $query->row['driver_id'];
        }
        
        $this->db->query("UPDATE " . DB_PREFIX . "deliverys SET driver_id = '" . (int) $driver_id . "', time_assigned = NOW() WHERE order_id = '" . (int) $order_id . "'");
        $delivery_id = $this->getDelivery_id($order_id);
        $this->db->query("UPDATE " . DB_PREFIX . "driver_petty_cash SET driver_id = '" . (int) $driver_id . "',dpc_time = NOW() WHERE delivery_id = '" . (int) $delivery_id['delivery_id'] . "'");
        
        /*
        if($this->config->get('driverappapi_firebase_api_key')) {
            $push_data = array(
                'devicetoken' => $this->getFirebaseTokens($driver_id), 
                'title' => 'Odrer Assigned', 
                'message' => 'Odrer Assigned #'. $order_id, 
                'data' => array(
                    'order_id' => $order_id,
                    'driver_id' => $driver_id,
                    'action' => 'order_assigned',
                    'title'=> 'Order Assigned',
                    'body'=>'Odrer Assigned #'. $order_id, 
                )
            );
            $fcm = new FCM($this->config->get('driverappapi_firebase_api_key'));
            $response = $fcm->send($push_data);
            
            // Old customer notify
            if($old_driver_id) {
                $push_data = array(
                    'devicetoken' => $this->getFirebaseTokens($old_driver_id),
                    'title' => 'Order Unassigned',
                    'message' => 'Unassigned #'. $order_id,
                    'data' => array(
                        'order_id' => $order_id,
                        'driver_id' => $old_driver_id,
                        'action' => 'order_unassigned',
                        'title'=> 'Order Unassigned',
                        'body'=>'Unassigned #'. $order_id,
                    )
                );
                $response = $fcm->send($push_data);
            }
        
        }
        */
        // $this->websocket->connect('d-'.$driver_id);
        // Assign Order
        $ownerpush = "Status has been changed to assigned of order id #$order_id";
        $notification_data = array(
                'title' => 'Order Assigned',
                'message' => $ownerpush, 
                'data' => array(
                        'order_id' => $order_id,
                        'driver_id' => $driver_id,
                        'action' => 'order_assigned',
                        'title'=> 'Order Assigned',
                        'body'=>$ownerpush
                )
        );
         
        $wbskdata = json_encode(array('driver_id'=>$driver_id,'notification'=>$notification_data ));
        $this->websocket->connect($wbskdata);
        
        // Old Driver
        if($old_driver_id) {
            $ownerpush = "Unassigned #$order_id";
            $notification_data = array('title' => 'Order Unassigned',
                    'message' => $ownerpush, 
                    'data' => array(
                            'order_id' => $order_id,
                            'driver_id' => $old_driver_id,
                            'action' => 'order_unassigned',
                            'title'=> 'Order Unassigned',
                            'body'=>$ownerpush
                    )
            );

            $wbskdata = json_encode(array('driver_id'=>$old_driver_id,'notification'=>$notification_data ));
            $this->websocket->connect($wbskdata);
        }
        return;
    }

    public function getDelivery_id($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "deliverys d WHERE order_id = '" . (int) $order_id . "'");
        return $query->row;
    }

    public function getDrivers() {
        $sql = "SELECT * FROM " . DB_PREFIX . "employee r ORDER BY r.employee_name ASC";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function todaytimes($driver_id) {
        $sql = ("SELECT employee_clock_time  FROM " . DB_PREFIX . "employee  WHERE employee_id='" . (int) $driver_id . "'");
        $order = $this->db->query($sql);
        return $order->row;
    }
    
    public function getTotalDrivers() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "employee");
        return $query->row['total'];
    }

    public function isDriverCheckedin($driver_id) {
        $status = FALSE;
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "employee_clock_inout  dc  WHERE dc.employee_id='" . $driver_id . "' AND  dc.employee_clock_in >= CURDATE() AND dc.employee_clock_out IS NULL ORDER BY employee_clock_id DESC LIMIT 1");
        if (!empty($query->row)) {
            $status = TRUE;
        }
        return $status;
    }

    public function orderassignedtodriver($driver_id) {
        $sql = ("SELECT d.order_id, o.order_status_id,o.shipping_firstname,o.shipping_lastname,o.shipping_address_1,o.shipping_address_2,o.shipping_postcode,o.shipping_method,o.payment_method,o.payment_code,o.telephone,o.comment,o.total,o.date_added,o.requested_time FROM " . DB_PREFIX . "deliverys d LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id =d.order_id) WHERE (d.driver_id = '" . (int) $driver_id . "' AND d.time_assigned >= CURDATE() AND d.time_delivered IS NULL ) ");
        $order = $this->db->query($sql);
        $orders = $order->rows;
        $order_assigned = array();
        if ($orders) {
            $this->load->model('localisation/order_status');
            
            foreach ($orders as $odr) {
                $query = $this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order` o WHERE (order_id = '" . (int) $odr['order_id'] . "' AND (o.order_status_id NOT IN ( '" . implode($this->config->get('drivermanagement_complete_status'), "', '") . "' ) AND o.order_status_id NOT IN ( '" . implode($this->config->get('drivermanagement_cancel_status'), "', '") . "' )) )");
                $order_status = $query->rows;
                if($order_status){
                    $postcode  = $this->postcode->getPostcodeLag($odr['shipping_postcode']);
                    $odr['postcode'] = array('postcode'=>isset($postcode['postcode'])?$postcode['postcode']:'','latitude'=>isset($postcode['latitude'])?$postcode['latitude']:'','longitude'=>isset($postcode['longitude'])?$postcode['longitude']:'','street'=>isset($postcode['street'])?$postcode['street']:'','city'=>isset($postcode['city'])?$postcode['city']:'');
                    $odr['order_paid'] = ($odr["payment_code"] == 'cod' ? 0 : 1);
                    $order_status_name = $this->model_localisation_order_status->getOrderStatusDescriptions($odr['order_status_id']);
                    $odr['order_status'] = (isset($order_status_name[1]['name'])?$order_status_name[1]['name']:'');
                    $order_assigned[] = $odr;
//                foreach ($order_status as $status) {
//                    $order_assigned[] = array(
//                        'order_id' => $odr['order_id'],
//                        'order_status_id'   => $status['order_status_id']
//                        );
//                }
                }
            }
        }
        return $order_assigned;
    }

    public function driverclockin($driver_id,$user_id=0,$time=false,$clock_id=0) {
        if (!$this->isDriverCheckedin($driver_id) ) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
            if($clock_id){
                if($time){
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "employee_clock_inout` SET employee_clock_id='".(int)$clock_id."',employee_id = '" . (int) $driver_id . "', employee_clock_in = '".$time."',user_id='".(int)$user_id."'");
                }else{
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "employee_clock_inout` SET employee_id = '" . (int) $driver_id . "', employee_clock_in = NOW(),user_id='".(int)$user_id."'");
                }
            }else{
                if($time){
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "employee_clock_inout` SET employee_id = '" . (int) $driver_id . "', employee_clock_in = '".$time."',user_id='".(int)$user_id."'");
                }else{
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "employee_clock_inout` SET employee_id = '" . (int) $driver_id . "', employee_clock_in = NOW(),user_id='".(int)$user_id."'");
                }
            }
            
            
            $lst_login = $this->db->query("SELECT employee_clock_in,employee_clock_out FROM " . DB_PREFIX . "employee_clock_inout WHERE employee_id='" . (int) $driver_id . "' AND employee_clock_out != '0000-00-00 00:00:00'  ORDER BY employee_clock_id DESC LIMIT 1");
            $last_login = $lst_login->rows;
            foreach ($last_login as $time) {
                $balance = $this->db->query("SELECT dpc_dr FROM " . DB_PREFIX . "driver_petty_cash WHERE driver_id = '" . (int) $driver_id . "' AND (('" . $time['employee_clock_in'] . "'<= dpc_time) && (dpc_time<='" . $time['employee_clock_out'] . "')) AND dpc_trans_type='B'");
                $balance_forward = $balance->rows;
            }
            if ($balance_forward) {
                foreach ($balance_forward as $forward_balance) {
                    $trans_type = 'F';
                    $this->addPettycash($driver_id, $trans_type, $forward_balance['dpc_dr'], '');
                }
            }
            /*
            if($this->config->get('driverappapi_firebase_api_key')) {
                $push_data = array(
                        'devicetoken' => $this->getFirebaseTokens($driver_id),
                        'title' => 'Clocked in',
                        'message' => 'Driver Clocked IN', 
                        'data' => array(
                            'driver_id' => $driver_id,
                            'action' => 'clocked',
                            'title'=> 'Clocked in',
                            'body'=>'Driver Clocked IN'
                        )
                );
                $fcm = new FCM($this->config->get('driverappapi_firebase_api_key'));
                $response = $fcm->send($push_data);
            }
           */
            $ownerpush = "Driver Clocked in";
            $notification_data = array('title' => 'Clocked in',
                'message' => $ownerpush, 
                'data' => array(
                        'driver_id' => $driver_id,
                        'action' => 'clocked',
                        'title'=> 'Clocked in',
                        'body'=>$ownerpush
                )                 );
            $wbskdata = json_encode(array('driver_id'=>$driver_id,'notification'=>$notification_data ));
            $this->websocket->connect($wbskdata);
            $this->log->write(print_r('Driver Clocked in', true));
        }
    }
     public function updatedriverclockin($clock_id,$user_id,$time){  
       
        $timein = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "employee_clock_inout  dc  WHERE dc.employee_clock_id='" . $clock_id . "'");
       
        if ($timein->num_rows) {
            
            $history = unserialize($timein->row['editing_history']);
            
             
            $history[] = array('updated_user_id'=>$user_id,'prev_in_time'=>$timein->row['employee_clock_in']);
            //$this->log->write(print_r($history,true));
            $this->db->query("UPDATE `" . DB_PREFIX . "employee_clock_inout` SET  employee_clock_in = '".$time."',user_id='".(int)$user_id."',editing_history = '". serialize($history)."' WHERE  employee_clock_id ='" . (int) $clock_id . "'");
            /*
            if($this->config->get('driverappapi_firebase_api_key')) {
                $push_data = array(
                    'devicetoken' => $this->getFirebaseTokens($user_id),
                    'title' => 'Clocked In', 
                    'message' => 'Driver Clock IN', 
                    'data' => array(
                        'driver_id' => $user_id,
                        'action' => 'clocked',
                        'body'=>'Clock IN',
                        'title'=> 'Driver Clocked in',
                    )
                );
                $fcm = new FCM($this->config->get('driverappapi_firebase_api_key'));
                $response = $fcm->send($push_data);
            }
            */
            $ownerpush = "Driver Clocked in";
            $notification_data = array('title' => 'Clocked in',
                'message' => $ownerpush, 
                'data' => array(
                        'driver_id' => $user_id,
                        'action' => 'clocked',
                        'title'=> 'Clocked in',
                        'body'=>$ownerpush
                )                 );
            $wbskdata = json_encode(array('driver_id'=>$user_id,'notification'=>$notification_data ));
            $this->websocket->connect($wbskdata);
            $this->log->write(print_r('UPDATE Driver Clocked in', true));
        }
        
    }
    public function updatedriverclockout($clock_id,$user_id,$time){
        $timein = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "employee_clock_inout  dc  WHERE dc.employee_clock_id='" . $clock_id . "'");
        if ($timein->num_rows) {
            $history = unserialize($timein->row['editing_history']);
            $history[] = array('updated_user_id'=>$user_id,'prev_out_time'=>$timein->row['employee_clock_out']);
            $this->db->query("UPDATE `" . DB_PREFIX . "employee_clock_inout` SET  employee_clock_out = '".$time."',user_id='".(int)$user_id."',editing_history = '". serialize($history)."' WHERE  employee_clock_id ='" . (int) $clock_id . "'");
            /*
            if($this->config->get('driverappapi_firebase_api_key')) {
                $push_data = array('devicetoken' => $this->getFirebaseTokens($user_id), 'title' => 'Driver App', 'message' => 'Clock Out ', 'data' => array(
                            'driver_id' => $user_id,
                            'action' => 'clock'
                    ));
                $fcm = new FCM($this->config->get('driverappapi_firebase_api_key'));
                $response = $fcm->send($push_data);
                $this->log->write('Driver APP -  :'.print_r($response, true));
            }
            */
            $ownerpush = "Driver Clocked Out";
            $notification_data = array(
                    'title' => 'Clocked Out',
                    'message' => $ownerpush, 
                    'data' => array(
                            'driver_id' => $user_id,
                            'action' => 'clocked',
                            'title'=> 'Clocked Out',
                            'body'=>$ownerpush
                    )
            );
            $wbskdata = json_encode(array('driver_id'=>$user_id,'notification'=>$notification_data ));
            $this->websocket->connect($wbskdata);
            $this->log->write(print_r('UPDATE Driver Clocked OUT', true));
        }
        
    }
    public function driverclockout($driver_id,$user_id=0,$time1=false) {
        $this->log->write(print_r('Driver Clocked OUT 11', true));
        $timein = $this->db->query("SELECT employee_clock_in FROM " . DB_PREFIX . "employee_clock_inout  dc  WHERE dc.employee_id='" . $driver_id . "' AND  dc.employee_clock_in >= CURDATE() AND dc.employee_clock_out IS NULL");
        $time_in = $timein->rows;
        foreach ($time_in as $time) {
            $query = $this->db->query("SELECT  SUM(dpc_cr) as credit,SUM(dpc_dr) as debit FROM " . DB_PREFIX . "driver_petty_cash WHERE driver_id = '" . (int) $driver_id . "' AND ('" . $time['employee_clock_in'] . "'<= dpc_time) && (dpc_time<=NOW()) AND dpc_trans_type!='R'");
        }
        $totals = $query->rows;
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
        foreach ($totals as $total) {
            $balance = $total['credit'] - $total['debit'];
            if ($balance != 0) {
                $trans_type = 'B';
                $this->addPettycash($driver_id, $trans_type, $balance, '');
            }
        }
        if($time1){
            $this->db->query("UPDATE `" . DB_PREFIX . "employee_clock_inout` SET  employee_clock_out = '".$time1."',user_id='".(int)$user_id."' WHERE  employee_id='" . (int) $driver_id . "' ORDER BY employee_clock_id DESC LIMIT 1 ");
        }else{
            $this->db->query("UPDATE `" . DB_PREFIX . "employee_clock_inout` SET  employee_clock_out = NOW(),user_id='".(int)$user_id."' WHERE  employee_id='" . (int) $driver_id . "' ORDER BY employee_clock_id DESC LIMIT 1 ");
        }
         $this->log->write(print_r('Driver Clocked OUT 222', true));
        /*
         if($this->config->get('driverappapi_firebase_api_key')) {
            $push_data = array('devicetoken' => $this->getFirebaseTokens($driver_id), 'title' => 'Driver App', 'message' => 'Clock Out ', 'data' => array(
                        'driver_id' => $driver_id,
                        'action' => 'clock'
                ));
            $fcm = new FCM($this->config->get('driverappapi_firebase_api_key'));
            $response = $fcm->send($push_data);
            //$this->log->write('Driver APP -  :'.print_r($response, true));
        }
        */
         $this->log->write(print_r('Driver Clocked OUT 33', true));
        $ownerpush = "Driver Clocked Out";
        $notification_data = array(
                'title' => 'Clocked Out',
                'message' => $ownerpush, 
                'data' => array(
                        'driver_id' => $driver_id,
                        'action' => 'clocked',
                        'title'=> 'Clocked Out',
                        'body'=>$ownerpush
                )
        );
         $this->log->write(print_r('Driver Clocked OUT 444', true));
        $wbskdata = json_encode(array('driver_id'=>$driver_id,'notification'=>$notification_data ));
        $this->websocket->connect($wbskdata);
        $this->log->write(print_r('Driver Clocked OUT', true));
    }

    public function validatedriverclockout($driver_id) {
        $timein = $this->db->query("SELECT employee_clock_in FROM " . DB_PREFIX . "employee_clock_inout  dc  WHERE dc.employee_id='" . $driver_id . "' AND  dc.employee_clock_in >= CURDATE() AND dc.employee_clock_out IS NULL");
        $time_in = $timein->rows;
        foreach ($time_in as $time) {
            $query = $this->db->query("SELECT  SUM(dpc_cr) as credit,SUM(dpc_dr) as debit FROM " . DB_PREFIX . "driver_petty_cash WHERE driver_id = '" . (int) $driver_id . "' AND ('" . $time['employee_clock_in'] . "'<= dpc_time) && (dpc_time<=NOW()) AND dpc_trans_type!='R'");
        }
        $totals = $query->rows;
        $status = FALSE;
        foreach ($totals as $total) {
            $balance = $total['credit'] - $total['debit'];
            if ($balance != 0) {
                $balance_return = $this->currency->format($balance, $this->config->get('config_currency'));
                return $balance;
            } else {
                return $status;
            }
        }
    }

    public function clockoutreassign($driver_id, $n_driver_id) {
        $orders = $this->orderassignedtodriver($driver_id);
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
        foreach ($orders as $order) {
            $this->db->query("UPDATE " . DB_PREFIX . "deliverys SET driver_id = '" . (int) $n_driver_id . "', time_assigned = NOW() WHERE order_id = '" . (int) $order . "'");
            $delivery_id = $this->getDelivery_id($order);
            $this->db->query("UPDATE " . DB_PREFIX . "driver_petty_cash SET driver_id = '" . (int) $n_driver_id . "' WHERE delivery_id = '" . (int) $delivery_id['delivery_id'] . "'");
        }
        return;
    }

    public function listOtherClockedinDrivers($order_id) {
        $sql = ("SELECT dc.employee_id, d.employee_name FROM " . DB_PREFIX . "employee_clock_inout dc LEFT JOIN " . DB_PREFIX . "employee d ON dc.employee_id = d.employee_id  WHERE ((dc.employee_clock_in >= CURDATE() AND dc.employee_clock_out IS NULL) AND dc.employee_id!=(SELECT driver_id FROM " . DB_PREFIX . "deliverys de WHERE de.order_id='" . $order_id . "') )");
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function listallClockedinDrivers($driver_id) {
        $sql = ("SELECT dc.employee_id, d.employee_name FROM " . DB_PREFIX . "employee_clock_inout dc LEFT JOIN " . DB_PREFIX . "employee d ON dc.employee_id = d.employee_id  WHERE ((dc.employee_clock_in >= CURDATE() AND dc.employee_clock_out IS NULL) AND dc.employee_id!='" . (int) $driver_id . "')");
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function listClockedinDrivers() {
        $sql = ("SELECT dc.employee_id, d.employee_name FROM " . DB_PREFIX . "employee_clock_inout dc LEFT JOIN " . DB_PREFIX . "employee d ON dc.employee_id = d.employee_id  WHERE ((dc.employee_clock_in >= CURDATE() AND dc.employee_clock_out IS NULL))");
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function AssignedOrders($driver_id) {
        $sql = ("SELECT d.order_id FROM " . DB_PREFIX . "deliverys d LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id=d.order_id) WHERE d.driver_id = '" . (int) $driver_id . "' AND d.time_assigned >= CURDATE() AND (o.order_status_id NOT IN ( '" . implode($this->config->get('drivermanagement_complete_status'), "', '") . "' ) AND o.order_status_id NOT IN ( '" . implode($this->config->get('drivermanagement_cancel_status'), "', '") . "' )) ");
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function addPettycash($driver_id, $trans_type, $pettycash_amount, $order_id) {
        if ($order_id) {
            $delivery_id = $this->getDelivery_id($order_id);
            $d_id = $delivery_id['delivery_id'];
        } else {
            $d_id = '0';
        }
        if ($trans_type == 'F') {
            $time = date("Y-m-d h:i:s", time() + 5);
        } else {
            $time = date("Y-m-d h:i:s", time());
        }
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
        if ($trans_type == 'N') {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . $driver_id . "', dpc_time ='" . $time . "', dpc_trans_type = '" . $trans_type . "', delivery_id = '" . $d_id . "', dpc_cr = '" . $this->db->escape($pettycash_amount) . "', dpc_dr = '" . $this->db->escape($pettycash_amount) . "'");
            $this->db->query("UPDATE " . DB_PREFIX . "deliverys SET driver_commission= '" . $this->db->escape($pettycash_amount) . "' WHERE delivery_id = '" . (int) $d_id . "'");
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET commission= '" . $this->db->escape($pettycash_amount) . "' WHERE order_id = '" . (int) $order_id . "'");
        } else {
            $type = $this->db->query("SELECT transaction_type FROM " . DB_PREFIX . "driver_pettycash_transaction_type WHERE transction_index='" . $trans_type . "'");
            $cash_type = $type->row;
            if ($cash_type['transaction_type'] == 'credit') {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . $driver_id . "', dpc_time ='" . $time . "', dpc_trans_type = '" . $trans_type . "', delivery_id = '" . $d_id . "', dpc_cr = '" . $this->db->escape($pettycash_amount) . "'");
            } else {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "driver_petty_cash` SET driver_id = '" . $driver_id . "', dpc_time ='" . $time . "', dpc_trans_type = '" . $trans_type . "', delivery_id = '" . $d_id . "', dpc_dr = '" . $this->db->escape($pettycash_amount) . "'");
            }
        }
        return;
    }

    public function sendmessage($driver_id, $order_id, $message) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_message SET driver_id = '" . (int) $driver_id . "', order_id = '" . (int) $order_id . "', message_sent = '" . $this->db->escape($message) . "', time = NOW()");
        return $this->db->getLastId();
    }

    public function getlatlon($pieces) {
        $sql = ("SELECT shipping_postcode, latitude, longitude  FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "uk_postcodes u ON (o.shipping_postcode = u.postcode) WHERE o.order_id='" . (int) $pieces . "'");
        $latlon = $this->db->query($sql);
        return $latlon->rows;
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
    public function registerDevice($devicename) {
        // $this->log->write($devicename); 
        $this->db->query("INSERT INTO " . DB_PREFIX . "driver_device  SET driver_device_name = '" . $this->db->escape($devicename) . "'");
        return $this->db->getLastId();
    }
    
    public function coordinates($data) {
       // $this->log->write(print_r($data,true));
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
        $this->db->query("INSERT INTO " . DB_PREFIX . "driver_location  SET driver_device_id = '" . $this->db->escape($data['device_id']) . "', user_id= '" . $this->db->escape($data['user_id']) . "', location_time= NOW(), latitude= '" . $this->db->escape($data['latitude']) . "', longitude= '" . $this->db->escape($data['longitude']) . "' "); //'" . $this->db->escape($data['location_time']) . "'
        return $this->db->getLastId();
    }
    
    public function getlastcoordinates($updatedtime) {
        $drivers = $this->getDrivers();
        foreach ($drivers as $key=> $driver) {
           $drivers[$key]['clocked'] = $this->isDriverCheckedin($driver['employee_id']);
            if ($updatedtime) {
                $query = $this->db->query("SELECT l.driver_device_id,l.location_time,l.latitude,l.longitude FROM " . DB_PREFIX . "driver_location l LEFT JOIN  " . DB_PREFIX . "employee d ON (l.user_id = d.user_id)   WHERE d.employee_id = " . $driver['employee_id'] . "  AND l.location_time > '" . $updatedtime . "' ORDER BY l.location_time");
            }
            else {
                $query = $this->db->query("SELECT l.driver_device_id,l.location_time,l.latitude,l.longitude FROM " . DB_PREFIX . "driver_location l LEFT JOIN  " . DB_PREFIX . "employee d ON (l.user_id = d.user_id) WHERE d.employee_id = " . $driver['employee_id'] . "  ORDER BY l.location_time DESC LIMIT 10");
            }
            $drivers[$key]['locations'] =  $query->rows;
        }
        return $drivers;
    }
    
     public function updateDeviceFirebase($data) { 
        // $this->log->write($devicename); 
        $this->db->query("UPDATE " . DB_PREFIX . "driver_device  SET firebasetoken = '" . $this->db->escape($data['firebasetoken']) . "', employee_id = '" . $this->db->escape($data['employee_id']) . "' WHERE driver_device_id = '" . $this->db->escape($data['device_id']) . "'");
        return $this->db->getLastId();
    }
    public function getFirebaseTokens($employee_id) {
        // $this->log->write($devicename); 
        $query = $this->db->query("SELECT firebasetoken FROM " . DB_PREFIX . "driver_device  WHERE  employee_id = '" . $this->db->escape($employee_id) . "' AND  firebasetoken <> ''");
        $tokens = array();
        foreach($query->rows as $row) {
            $tokens[] = $row['firebasetoken'];
        }
        return $tokens;
    }
    
    public function brodcastpush($order_id,$order_status_id) {
        $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '1'");
        if(in_array($order_status_id,$this->config->get('drivermanagement_order_status') )) { // Brodcast Orders
            //$admnapptokens = $this->getFirebaseTokens();
            $admnapptokens =  array(); //hide broadcast orders
            $ownerpush = "Status has been changed to " . $order_status_query->row['name'] . " of #" . $order_id;
            $order_action = 'new_order';
            $push_tile = "New Order";

        }
        elseif (in_array($order_status_id,$this->config->get('drivermanagement_complete_status') )) {
            $ownerpush = "Status has been changed to " . $order_status_query->row['name'] . " of #" . $order_id;
            $order_action = 'order_completed';
            $push_tile = "Order completed";
            $driver_to_order = $this->getDelivery_id($order_id);
            if(isset($driver_to_order['driver_id'])) {
                $admnapptokens = $this->getFirebaseTokens($driver_to_order['driver_id']);
            }

        }  
        elseif (in_array($order_status_id,$this->config->get('drivermanagement_cancel_status') )) {
            $ownerpush = "Status has been changed to " . $order_status_query->row['name'] . " of #" . $order_id;
            $order_action = 'order_cancel';
            $push_tile = "Order Canceled";
            $driver_to_order = $this->getDelivery_id($order_id);
            if(isset($driver_to_order['driver_id'])) {
                $admnapptokens = $this->getFirebaseTokens($driver_to_order['driver_id']);
            }
        }  
        elseif (in_array($order_status_id,$this->config->get('drivermanagement_alert_status') )) {
            $ownerpush = "Status has been changed to " . $order_status_query->row['name'] . " of #" . $order_id;
            $order_action = 'order_ready';
            $push_tile = "Order Ready";
            $driver_to_order = $this->getDelivery_id($order_id);
            if(isset($driver_to_order['driver_id'])) {
                $admnapptokens = $this->getFirebaseTokens($driver_to_order['driver_id']);
            }

        }  
        elseif (in_array($order_status_id,$this->config->get('drivermanagement_driver_status') )) {
            $ownerpush = "Status has been changed to " . $order_status_query->row['name'] . " of #" . $order_id;
            $order_action = 'order_takeout';
            $push_tile = "Order Takeout";
            $driver_to_order = $this->getDelivery_id($order_id);
            if(isset($driver_to_order['driver_id'])) {
                $admnapptokens = $this->getFirebaseTokens($driver_to_order['driver_id']);
            }

        }
        elseif (in_array($order_status_id,$this->config->get('drivermanagement_cooking_status') )) {
            $ownerpush = "Status has been changed to " . $order_status_query->row['name'] . " of #" . $order_id;
            $order_action = 'order_cooking';
            $push_tile = "Order Cooking";
            $driver_to_order = $this->getDelivery_id($order_id);
            if(isset($driver_to_order['driver_id'])) {
                $admnapptokens = $this->getFirebaseTokens($driver_to_order['driver_id']);
            }

        }
        /*
        if( !isset($this->session->data['deliveryapp']) && $this->config->get('driverappapi_firebase_api_key')) {
            if(isset($admnapptokens) && $admnapptokens) {
                if(isset($driver_to_order['driver_id'])) {
                    $driver_id = $driver_to_order['driver_id'];
                } else {
                    $driver_id = 0;
                }
                $push_data = array('devicetoken' => $admnapptokens,
                                    'title' => $push_tile,
                                    'message' => $ownerpush, 
                                    'body'=>$ownerpush,
                                    'data' => array(
                                            'order_id' => $order_id,
                                            'action' => $order_action,
                                            'title'=> $push_tile,
                                            'body'=>$ownerpush,
                                            'driver_id' => $driver_id
                                    )
                            );


                $fcm = new FCM($this->config->get('driverappapi_firebase_api_key'));
                $response = $fcm->send($push_data);
                $this->log->write('ModelDrivermanagementDrivermanagement 426: ' . print_r($response,true));
            }
            
        }
        */
        if(!isset($this->session->data['deliveryapp']) ){
            $notification_data = array('title' => $push_tile,
                    'message' => $ownerpush, 
                    'body'=>$ownerpush,
                    'data' => array(
                            'order_id' => $order_id,
                           // 'action' => $order_action,
                            'action' => 'order_status_changed',
                            'title'=> $push_tile,
                            'body'=>$ownerpush,
                            'driver_id' => $driver_id
                    )
            );
            $wbskdata = json_encode(array('driver_id'=>'All','notification'=>$notification_data ));
            $this->websocket->connect($wbskdata);
        }
        
    }
    
    
}