<?php
class ControllerEpos2Worker extends Controller {
    private $error = array();
    private function jsonp($json, $encode = false) {
        $this->response->addHeader('Access-Control-Allow-Origin: *');
        $this->response->addHeader('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
        $this->response->addHeader('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
        $this->response->addHeader('Access-Control-Allow-Credentials: true');
        $this->response->setCompression(9);
        if (isset($this->request->get['callback'])) {
            return $this->request->get['callback'] . '(' . ($encode ? json_encode($json) : $json ) . ')';
        } else {
            return ($encode ? json_encode($json) : $json);
        }
    }
    public function index() {
        $orders=$this->orders();
        $json =array();
        //$this->log->write(print_r(array('timeee'=>$this->config->get('last_data_modified_time'),'unixtime'=>strtotime($this->config->get('last_data_modified_time'))),true));         
        if(!empty($orders)){
            $json['orders']=$orders;
            $json['last_order_updated_date'] = strtotime($orders[count($orders)-1]['date_modified']); 
        }
         $order_updated_date = ($this->config->get('last_data_modified_time'))?strtotime($this->config->get('last_data_modified_time')):time();
        if($this->config->get('last_data_modified_time')){
            //$this->log->write(print_r(array('lasttimeee'=>$this->config->get('last_data_modified_time'),'unixtime'=>strtotime($this->config->get('last_data_modified_time'))),true));         
        }else{
            //$this->log->write(print_r(array('nowtimeee'=>time(),'unixtime'=>time()),true));         
        }
        
        $settings=$this->settings();
        if(!empty($settings)){
            $json['settings']=$settings;
        }
        $driver_data = $this->drivermanegement();
        if(!empty($driver_data)){
            $json['drivers']=$driver_data;
        }
        $eposprints = $this->eposprints();
        if(!empty($eposprints)){
            $json['eposprint']=$eposprints;
        }
        $onlinesettings = $this->onlinesettings();
        if(!empty($onlinesettings)){
            $json['onlinesettings']=$onlinesettings;
        }
       
        $customers = $this->customers();
        if(!empty($customers)){
            $json['customers']=$customers;
        }
        $report_data = $this->getSavedreports();
        if(!empty($report_data) && isset($report_data['last_zreport_time'])){
            //$this->log->write(print_r('pull report',true));
            //$this->log->write(print_r($report_data,true));
            $json['z_report']=$report_data['report_data'];
            $json['last_zreport_time']=$report_data['last_zreport_time'];
        }
        $onlineorders=$this->onlineorder();
        if(!empty($onlineorders)){
            if(!empty($onlineorders['onlineorders'])){
                $json['onlineorders']=$onlineorders['onlineorders'];
            }
            if(!empty($onlineorders['missing_orders'])){
                $json['missing_orders']=$onlineorders['missing_orders'];
            }             
        }
        $live_orders=$this->liveorders();
        if(!empty($live_orders)){
            $json['live_orders']=$live_orders;
        }
        //$this->log->write(print_r(array('worker'=>time ()),true));
        if(!empty($json)){
            $json['order_updated_date'] = time();
            if(!isset($json['last_order_updated_date'])){
                $json['last_order_updated_date'] = time()-300;
            }
            //$json['order_updated_date'] = time();
        }
        $deleted_track = $this->getDeletedorders();
        if(!isset($this->request->post['last_order_deleted_date']) || $this->request->post['last_order_deleted_date']==''){
             $json['last_order_deleted_date'] = time();
        } 
        if(!empty($deleted_track) && $deleted_track['maxtime']>0){
            $json['delete'] =$deleted_track;
            $json['last_order_deleted_date'] = $deleted_track['maxtime'];       
        }  

        //$json['delete']['del_live_orders'] =  unserialize( $this->config->get('liveorder_deltrack'));
        //$json['delete']['del_orders'] =  unserialize( $this->config->get('order_deltrack'));
        $json['onlinepayments'] =array();//$this->Onlinepayments();
        $json['c']=1; 
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    /*
    public function orders(){ 
        $orders = array();
        $this->load->model('account/order');
        if($this->request->post['order_updated_date']){ 
            //$this->log->write(date('Y-m-d H:i:s',$this->request->post['order_updated_date']));
            $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE date_modified>'".date('Y-m-d H:i:s',$this->request->post['order_updated_date'])."' AND order_status_id!='0' ORDER BY order_id DESC LIMIT 0,100");
        }else{
            $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id!='0' ORDER BY order_id DESC LIMIT 0,100");
        }
        //$this->log->write(print_r($order_query->rows,true));
        if($order_query->rows){
            foreach($order_query->rows as $order_data){
                //$order_data[$k]=$order_data;
                $product_data=array();
                $order_products = $this->model_account_order->getOrderProducts($order_data['order_id']);
                $k=0;
                foreach ($order_products as $order_product) {
                    $order_product['name'] = html_entity_decode($order_product['name']);
                    $order_product['total'] = number_format($order_product['total'], 2);
                    $order_product['qty']=$order_product['quantity'];
                    $order_product['total_value']=$order_product['total'];
                    $order_product['price_value']=$order_product['price'];
                    $order_product['main_index'] =$k;
                    $product_data[$k] = $order_product;
                    $order_options = $this->model_account_order->getOrderOptions($order_data['order_id'],$order_product['order_product_id']);
                    $option_data=array();
                    if(!empty($order_options)){
                        foreach($order_options as $order_option){
                            if($order_option['type']=='checkbox'){
                                $option_data[$order_option['product_option_id']][$order_option['product_option_value_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>'','type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                            }else{
                                $option_data[$order_option['product_option_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>'','type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                            }
                        }
                    }
                    $product_data[$k]['option']=$option_data;
                    $topping_data = array();
                    $order_toppings = $this->model_account_order->getOrderToppings($order_data['order_id'],$order_product['order_product_id']);
                    if(!empty($order_toppings)){
                        foreach($order_toppings as $data){
                            $topping_data[$data['product_topping_id']][$data['product_topping_value_id']]=array('name'=>$data['value'],'qty'=>$data['quantity'],'total_value'=>$data['price'],'price_value'=>$data['price'],'price'=>$data['price'],'topping_id'=>$data['product_topping_id'],'topping_value_id'=>$data['product_topping_value_id'],'toppingname'=>$data['name']);
                        }
                    }
                    $dressing_data = array();
                    $order_dressings = $this->model_account_order->getOrderDressings($order_data['order_id'],$order_product['order_product_id']);
                    if(!empty($order_dressings)){
                        foreach($order_dressings as $data){
                            $dressing_data[$data['product_dressing_id']][$data['product_dressing_value_id']]=array('name'=>$data['value'],'qty'=>1,'total_value'=>'','price_value'=>'','dressing_id'=>$data['product_dressing_id'],'dressing_value_id'=>$data['product_dressing_value_id'],'dressingname'=>$data['name']);
                        }
                    }
                    
                    $product_data[$k]['bundled']=$this->model_account_order->getOrderBundle($order_data['order_id'],$order_product['order_product_id']);
                    $product_data[$k]['toppings'] =$topping_data;
                    $product_data[$k]['dressings'] = $dressing_data;
                    $k++;
                }
               
                $order_data['items']=$product_data;
                $order_data['splits']=$this->model_account_order->getOrderSplits($order_data['order_id']);
                $order_totals = $this->model_account_order->getOrderTotals($order_data['order_id']);
                $total_data=array();
                foreach ($order_totals as $order_total) { 
                    $total_data[]=array('code'=>$order_total['code'],'name'=>$order_total['title'],'value'=>  number_format($order_total['value'],2));
                }
                $order_data['totals']=$total_data;
                $order_histories=$this->model_account_order->getallOrderHistories($order_data['order_id']);
                $order_data['histories'] = $order_histories;
                $time_difference = 0;
                if(count($order_histories)>1){
                    $difference = strtotime($order_histories[count($order_histories)-1]['date_added']) - strtotime($order_histories[0]['date_added']);
                    $days = floor($difference/86400);  
                    if($days>0){
                        $time_difference =24;
                    }else{
                        $time_difference = floor($difference/3600);
                    }
                    
                }  
                $order_data['time_difference'] = $time_difference;
                $order_data['unix_date_added'] = strtotime($order_data['date_added']);
                $order_data['unix_requested_time'] = strtotime($order_data['requested_time']);
                $orders[]=$order_data;
            }
        }
        //print_r($orders);
        return $orders;
    }    
    
      

     */
    
    public function orders(){   // For New Order Downloads and worker
        $orders = array();
        $this->load->model('account/order');
        if(isset($this->request->post['last_order_updated_date']) && $this->request->post['last_order_updated_date']!=''){ 
            
            $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE date_modified>'".date('Y-m-d H:i:s',$this->request->post['last_order_updated_date'])."' AND order_status_id!='0'");
            if($order_query->rows){
                foreach($order_query->rows as $order_data){
                    /*
                    $product_data=array();
                    $order_products = $this->model_account_order->getOrderProducts($order_data['order_id']);
                    $k=0;
                    foreach ($order_products as $order_product) {
                        $order_product['name'] = html_entity_decode($order_product['name']);
                        $order_product['total'] = number_format($order_product['total'], 2);
                        $order_product['qty']=$order_product['quantity'];
                        $order_product['total_value']=$order_product['total'];
                        $order_product['price_value']=$order_product['price'];
                        $order_product['main_index'] =$k;
                        $order_product['main_product_id']=$order_product['main_product_id'];
                        $product_data[$k] = $order_product;
                        $order_options = $this->model_account_order->getOrderOptions($order_data['order_id'],$order_product['order_product_id']);
                        $option_data=array();
                        if(!empty($order_options)){
                            foreach($order_options as $order_option){
                                if($order_option['type']=='checkbox'){
                                    $option_data[$order_option['product_option_id']][$order_option['product_option_value_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                                }else{
                                    $option_data[$order_option['product_option_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                                }
                            }
                        }
                        $product_data[$k]['option']=$option_data;
                        $topping_data = array();
                        $order_toppings = $this->model_account_order->getOrderToppings($order_data['order_id'],$order_product['order_product_id']);
                        if(!empty($order_toppings)){
                            foreach($order_toppings as $data){
                                $topping_data[$data['product_topping_id']][$data['product_topping_value_id']]=array('name'=>$data['value'],'qty'=>$data['quantity'],'total_value'=>$data['price'],'price_value'=>$data['price'],'price'=>$data['price'],'topping_id'=>$data['product_topping_id'],'topping_value_id'=>$data['product_topping_value_id'],'toppingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                            }
                        }
                        $dressing_data = array();
                        $order_dressings = $this->model_account_order->getOrderDressings($order_data['order_id'],$order_product['order_product_id']);
                        if(!empty($order_dressings)){
                            foreach($order_dressings as $data){
                                $dressing_data[$data['product_dressing_id']][$data['product_dressing_value_id']]=array('name'=>$data['value'],'qty'=>1,'total_value'=>'','price_value'=>'','dressing_id'=>$data['product_dressing_id'],'dressing_value_id'=>$data['product_dressing_value_id'],'dressingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                            }
                        }

                        $product_data[$k]['bundled']=$this->model_account_order->getOrderBundle($order_data['order_id'],$order_product['order_product_id']);
                        $product_data[$k]['toppings'] =$topping_data;
                        $product_data[$k]['dressings'] = $dressing_data;
                        $k++;
                    }

                    $order_data['items']=$product_data;
                    $order_data['splits']=$this->model_account_order->getOrderSplits($order_data['order_id']);
                    $order_totals = $this->model_account_order->getOrderTotals($order_data['order_id']);
                    $total_data=array();
                    foreach ($order_totals as $order_total) { 
                        $total_data[]=array('code'=>$order_total['code'],'name'=>$order_total['title'],'value'=>  number_format($order_total['value'],2));
                    }
                    $order_data['totals']=$total_data;
                    $order_histories=$this->model_account_order->getallOrderHistories($order_data['order_id']);
                    $order_data['histories'] = $order_histories;
                    $time_difference = 0;
                    if(count($order_histories)>1){
                        $difference = strtotime($order_histories[count($order_histories)-1]['date_added']) - strtotime($order_histories[0]['date_added']);
                        $days = floor($difference/86400);  
                        if($days>0){
                            $time_difference =24;
                        }else{
                            $time_difference = floor($difference/3600);
                        }
                    }  
                    $order_data['time_difference'] = $time_difference;
                    $order_data['unix_date_added'] = strtotime($order_data['date_added']);
                    $order_data['unix_requested_time'] = strtotime($order_data['requested_time']);
                    $orders[]=$order_data;
                    * 
                    */
                    $orders[]=$this->getOrderData($order_data);
                }
            }
        }
        return $orders;
    }  
    
    private function liveorders(){
        $live_data = array();
        if($this->request->post['order_updated_date'] && $this->request->post['order_updated_date']!=''){
            
            $live_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "live_orders` WHERE 	date_modified>'".date('Y-m-d H:i:s',$this->request->post['order_updated_date'])."'");
        }else{
            $live_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "live_orders` ");
        }
         // $this->log->write(print_r(array('liveworker---'=>$this->request->post['order_updated_date']),true));         
        if($live_query->rows){
            foreach($live_query->rows as $live_order){
                $live_order['date_modified']=  strtotime($live_order['date_modified'])*1000;
                $live_data[]=$live_order;
            }
        }
        /*
        if($live_query->rows){
            foreach($live_query->rows as $live_order){
                $data = unserialize($live_order['data']);
                $data['date_modified']=  strtotime($live_order['date_modified'])*1000;
                $live_data[]=array(
                    'live_order_id'=>$live_order['live_order_id'],
                    'date_added'=>$live_order['date_added'],
                    'date_modified'=>$live_order['date_modified'],
                    'data'=>$data,
                    'lock'=>$live_order['lock'],
                    'table_id'=>$live_order['dinein_table_id'],
                    'cart_total'=>$live_order['cart_total'],
                    'system_id'=>$live_order['system_id']
                );
            }
            
            //$this->log->write(print_r($live_data,true));
        }
        */
        return $live_data;
    }
    private function settings(){
        $data=array();
        return $data;
    }
    private function drivermanegement(){
        $driver_data = array();
        
        if($this->request->post['order_updated_date'] && $this->request->post['order_updated_date']!=''){
           
            $assignedorder = $this->db->query("SELECT * FROM `" . DB_PREFIX . "deliverys` WHERE time_assigned >'".date('Y-m-d H:i:s',$this->request->post['order_updated_date'])."'");
            $clockeddriver = $this->db->query("SELECT * FROM `" . DB_PREFIX . "employee_clock_inout` WHERE employee_clock_in >'".date('Y-m-d H:i:s',$this->request->post['order_updated_date'])."' AND employee_clock_out IS NULL");
            $clockedoutdriver = $this->db->query("SELECT * FROM `" . DB_PREFIX . "employee_clock_inout` WHERE employee_clock_out >'".date('Y-m-d H:i:s',$this->request->post['order_updated_date'])."' AND employee_clock_out IS NOT NULL");
            if($assignedorder->rows){
                $driver_data['assignedorders'] = $assignedorder->rows;
            }
            if($clockeddriver->rows){
               $driver_data['clocked'] =  $clockeddriver->rows;
            }
            if($this->request->post['order_updated_date'] && $clockedoutdriver->rows){
                $driver_data['clockedout'] =  $clockedoutdriver->rows;
            }
            
            if($this->config->get('unaassigned_order_track')){
                $driver_data['unaassigned_orders'] =  unserialize( $this->config->get('unaassigned_order_track'));
            }
        }else{ 
            $assignedorder = $this->db->query("SELECT * FROM `" . DB_PREFIX . "deliverys` ");
            //$clockeddriver = $this->db->query("SELECT * FROM `" . DB_PREFIX . "driver_clock_inout` WHERE DATE(driver_clock_in) = CURDATE() AND driver_clock_out IS NULL ");
            $clockeddriver = $this->db->query("SELECT * FROM `" . DB_PREFIX . "employee_clock_inout` WHERE  employee_clock_in> (NOW() - INTERVAL 188 DAY)");
            //
            
            if($assignedorder->rows){
                $driver_data['assignedorders'] = $assignedorder->rows;
            }
            if($clockeddriver->rows){
               $driver_data['clocked'] =  $clockeddriver->rows;
            }
            if($this->request->post['order_updated_date'] && $clockedoutdriver->rows){
                $driver_data['clockedout'] =  $clockedoutdriver->rows;
            }
            if($this->request->post['order_updated_date']){
                $this->load->model('drivermanagement/drivermanagement');
                $driver_data['geolocations'] = $this->model_drivermanagement_drivermanagement->getlastcoordinates(date('Y-m-d H:i:s',$this->request->post['order_updated_date']));
            }
            if($this->config->get('unaassigned_order_track')){
                $driver_data['unaassigned_orders'] =  unserialize( $this->config->get('unaassigned_order_track'));
            }
        }
        
        
        return $driver_data;
    }
    private function eposprints() {
        if(isset($this->request->post['system_id']) && isset($this->request->post['order_updated_date']) && $this->request->post['order_updated_date']!=''){
            $this->load->model('epos/print');
            return $this->model_epos_print->getPrintqueue($this->request->post['system_id'],$this->request->post['order_updated_date']);
        }
    }
    private function onlinesettings(){
        $updated_online_settings = array();
        if(isset($this->request->post['order_updated_date']) && $this->request->post['order_updated_date']!=''){
            //$this->log->write(date('Y-m-d H:i:s',$this->request->post['order_updated_date']));
            //$this->log->write(date('Y-m-d H:i:s',$this->config->get('websetting_modified')));
            if($this->config->get('websetting_modified') && $this->request->post['order_updated_date'] < $this->config->get('websetting_modified')){
                $updated_online_settings  = unserialize($this->config->get('onlineorder_settings'));
                //$this->log->write(print_r($updated_online_settings,true));
            }
        }
        return $updated_online_settings;
    }
      public function customers(){ 
        $this->load->model('epos/customer');
        $data = array();
        if(isset($this->request->post['order_updated_date']) && $this->request->post['order_updated_date']!=''){
            $cust_qry = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "customer WHERE  date_modified>'".date('Y-m-d H:i:s',$this->request->post['order_updated_date'])."'");
             if(!empty($cust_qry->rows)){
                foreach ($cust_qry->rows as $customer) {
                    $json = $this->model_epos_customer->getCustomer($customer['customer_id']);
                    $json['addresses'] = $this->model_epos_customer->getAddresses($customer['customer_id']);
                    $json['reward'] = $this->model_epos_customer->getRewardPoints($customer['customer_id']);
                    $json['comments'] = array();//$this->model_epos_customer->getcustomerComments($customer['customer_id']);
                    $json['orders'] = array(); 
                    $data[]=$json;
                }
            }
        }
        return $data;
    }
    private function onlineorder(){ 
        $orders = array();
        $this->load->model('account/order');
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE system_id = 0 AND order_status_id=".$this->config->get('config_order_status_id') ." AND NOW() - INTERVAL 1 DAY < date_added");// ORDER BY date_modified ASC
       // $this->log->write("SELECT * FROM `" . DB_PREFIX . "order` WHERE system_id = 0 AND order_status_id=".$this->config->get('config_order_status_id') ." AND NOW() - INTERVAL 1 DAY < date_added");// ORDER BY date_modified ASC
        if($order_query->rows){
            foreach($order_query->rows as $order_data){
                $orders[]=$this->getOrderData($order_data);
            }
        }
        $other_orders =array();        
        if(!empty($orders)){
            $other_orders['onlineorders']=$orders;
        }
        $missing_orders = array();
        $order_row_missing = $this->getMissingOrders();
        if($order_row_missing){
            foreach($order_row_missing as $order_data){
                $missing_orders[]=$this->getOrderData($order_data);
            }
        }
        if(!empty($missing_orders)){
            $other_orders['missing_orders']= $missing_orders;
        }
        //$this->log->write(print_r($other_orders,true));
        return $other_orders;
        //$this->response->setOutput($this->jsonp(json_encode($json))); 
    }
    private function getOrderData($order_data) { 
        $product_data=array();
        /*
        $order_products = $this->model_account_order->getOrderProducts($order_data['order_id']);
        $k=0;
        foreach ($order_products as $order_product) {
            $order_product['name'] = html_entity_decode($order_product['name']);
            $order_product['total'] = number_format($order_product['total'], 2);
            $order_product['qty']=$order_product['quantity'];
            $order_product['total_value']=$order_product['total'];
            $order_product['price_value']=$order_product['price'];
            $order_product['main_index'] =$k;
            $order_product['main_product_id']=$order_product['main_product_id'];
            $product_data[$k] = $order_product;
            $order_options = $this->model_account_order->getOrderOptions($order_data['order_id'],$order_product['order_product_id']);
            $option_data=array();
            if(!empty($order_options)){
                foreach($order_options as $order_option){
                    if($order_option['type']=='checkbox'){
                        $option_data[$order_option['product_option_id']][$order_option['product_option_value_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                    }else{
                        $option_data[$order_option['product_option_id']]=array('product_option_value_id'=>$order_option['product_option_value_id'],'name'=>html_entity_decode($order_option['value']),'price_value'=>$order_option['price'],'option_name'=>html_entity_decode($order_option['name']),'value'=>html_entity_decode($order_option['value']),'product_option_id'=>$order_option['product_option_id'],'shortcode'=>$order_option['shortcode'],'type'=>$order_option['type'],'group'=>$order_option['group'],'option_id'=>$order_option['option_value_id']);
                    }
                }
            }
            $product_data[$k]['option']=$option_data;
            $topping_data = array();
            $order_toppings = $this->model_account_order->getOrderToppings($order_data['order_id'],$order_product['order_product_id']);
            if(!empty($order_toppings)){
                foreach($order_toppings as $data){
                    $topping_data[$data['product_topping_id']][$data['product_topping_value_id']]=array('name'=>$data['value'],'qty'=>$data['quantity'],'total_value'=>$data['price'],'price_value'=>$data['price'],'price'=>$data['price'],'topping_id'=>$data['product_topping_id'],'topping_value_id'=>$data['product_topping_value_id'],'toppingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                }
            }
            $dressing_data = array();
            $order_dressings = $this->model_account_order->getOrderDressings($order_data['order_id'],$order_product['order_product_id']);
            if(!empty($order_dressings)){
                foreach($order_dressings as $data){
                    $dressing_data[$data['product_dressing_id']][$data['product_dressing_value_id']]=array('name'=>$data['value'],'qty'=>1,'total_value'=>'','price_value'=>'','dressing_id'=>$data['product_dressing_id'],'dressing_value_id'=>$data['product_dressing_value_id'],'dressingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                }
            }

            $product_data[$k]['bundled']=$this->model_account_order->getOrderBundle($order_data['order_id'],$order_product['order_product_id']);
            $product_data[$k]['toppings'] =$topping_data;
            $product_data[$k]['dressings'] = $dressing_data;
            $k++;
        }
        */
        //$order_data['items']=$product_data;
        $order_data['items']=$this->model_account_order->getConsolidatedOrderProducts($order_data['order_id']); 
        $order_data['splits']=$this->model_account_order->getOrderSplits($order_data['order_id']);
        $order_totals = $this->model_account_order->getOrderTotals($order_data['order_id']);
        $total_data=array();
        foreach ($order_totals as $order_total) { 
            $total_data[]=array('code'=>$order_total['code'],'name'=>$order_total['title'],'value'=>  number_format($order_total['value'],2));
        }
        $order_data['totals']=$total_data;
        $order_histories=$this->model_account_order->getallOrderHistories($order_data['order_id']);
        $order_data['histories'] = $order_histories;
        $time_difference = 0;
        if(count($order_histories)>1){
            $difference = strtotime($order_histories[count($order_histories)-1]['date_added']) - strtotime($order_histories[0]['date_added']);
            $days = floor($difference/86400);  
            if($days>0){
                $time_difference =24;
            }else{
                $time_difference = floor($difference/3600);
            }
        }   
        $order_data['time_difference'] = $time_difference;
        $order_data['unix_date_added'] = strtotime($order_data['date_added']);
        $order_data['unix_requested_time'] = strtotime($order_data['requested_time']);
        $order_data['payed_amts']=  unserialize($order_data['payment_history']);
        $order_data['previous_paymemts']=  unserialize($order_data['previous_paymemts']);
        $order_data['prevordercount'] =$this->model_account_order->previousOrders($order_data['order_id'],$order_data['customer_id']);
        $order_data['vouchers'] =$this->model_account_order->getOrderVouchers($order_data['order_id']);
        if($order_data['shipping_code']=='dine.dine' && isset($order_data['dinein_table_id']) && $order_data['dinein_table_id']!='' && $order_data['dinein_table_id']!='0'){
            $order_data['dinein_table_name']=$this->model_account_order->getTablename($order_data['dinein_table_id']);
        } 
        if ($this->config->get('config_stuart_status') &&  $order_data['shipping_code']=='home.home') {
            $order_data['stuart_details'] =$this->model_account_order->getStuartdetails($order_data['order_id']);
        }
        $order_data['kitchenstatuses'] =$this->model_account_order->getOrderKitchenStatus($order_data['order_id']);
        return $order_data;
    }
    private function getMissingOrders() {
        return array();
        $last_missing_orderchecked  = $this->config->get('last_missing');
        // $this->log->write("SELECT * FROM `" . DB_PREFIX . "order` WHERE system_id = 0 AND order_id > $last_missing_orderchecked AND order_status_id = 0 AND NOW()- INTERVAL 600 SECOND > date_added ORDER BY order_id");
        $query_orders = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE system_id = 0 AND store_url!='' AND order_id > $last_missing_orderchecked AND order_status_id = 0 AND NOW()- INTERVAL 600 SECOND > date_added ORDER BY order_id");
        //$this->log->write("SELECT * FROM `" . DB_PREFIX . "order` WHERE system_id = 0 AND order_id > $last_missing_orderchecked AND order_status_id = 0 AND NOW()- INTERVAL 600 SECOND > date_added ORDER BY order_id");
        //$this->log->write(print_r($query_orders->rows,true));
        foreach ($query_orders->rows as $key=>$order) {
          
            $query_duplicate_orders = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE system_id = 0 AND order_id > '". $order['order_id'] . "' " .
                    " AND firstname = '" . $this->db->escape($order['firstname']) ."' ".
                    " AND lastname = '" . $this->db->escape($order['lastname']) ."' ".
                    " AND email = '" . $order['email'] ."' ".
                    " AND telephone = '" . $order['telephone'] ."' ".
                    " AND shipping_firstname = '" . $this->db->escape($order['shipping_firstname']) ."' ".
                    " AND shipping_lastname = '" . $this->db->escape($order['shipping_lastname']) ."' ".
                    " AND shipping_address_1 = '" .$this->db->escape( $order['shipping_address_1']) ."' ".
                    " AND shipping_postcode = '" . $this->db->escape($order['shipping_postcode']) ."' ".
                    " AND ip = '" . $order['ip'] ."' ".
                    " AND user_agent = '" . $this->db->escape($order['user_agent']) ."' ".
                    
                    " AND date_added BETWEEN date_added - INTERVAL 1440 SECOND AND  date_added + INTERVAL 1440 SECOND ORDER BY order_id");
            
            if($query_duplicate_orders->num_rows){
                unset($query_orders->rows[$key]);
             // $this->log->write( $order['order_id'] ." " . $key . " | ");
                $this->config->set('last_missing',$order['order_id']);
                $this->load->model('epos/order');
                $this->model_epos_order->addOrderHistory($order['order_id'],array('order_status_id'=>$this->config->get('pos_order_void_status'),'notify'=>0,'comment'=>'DUPLICATE ORDER #' . $order['order_id']));
            }
            
        }
       // $this->log->write(print_r($query_orders->rows,true));
        return $query_orders->rows;
    }
    public function getSavedreports(){
        $data = array();
        if(isset($this->request->post['order_updated_date']) && $this->request->post['order_updated_date']!=''){
            $report_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "day_sales_report` WHERE  report_time > '".(int)$this->request->post['order_updated_date']."'");
            if(!empty($report_query->rows)){
                foreach ($report_query->rows as $rowdata) {
                    $data['report_data'][]=array(
                        'report_time'=>$rowdata['report_time'],
                        'details'=>  unserialize($rowdata['details']),
                        'system_id'=>$rowdata['system_id'],
                        'user_id'=>$rowdata['user_id']
                    );
                }
                $data['last_zreport_time']=  $report_query->rows[count($report_query->rows)-1]['report_time'];
            }
        }else{//if not last report id get last 30 days reports
            $report_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "day_sales_report` ORDER BY report_id DESC LIMIT 30");            
            if(!empty($report_query->rows)){
                foreach ($report_query->rows as $rowdata) {
                    $data['report_data'][]=array(
                        'report_time'=>$rowdata['report_time'],
                        'details'=>  unserialize($rowdata['details']),
                        'system_id'=>$rowdata['system_id'],
                        'user_id'=>$rowdata['user_id']
                    );
                }
                $data['last_zreport_time']=$report_query->rows[0]['report_time'];
            }
        }
        return $data;
    }
     private function getDeletedorders() {
        if(isset($this->request->post['last_order_deleted_date']) && $this->request->post['last_order_deleted_date']!=''){
            //del_live_orders
            //del_orders
            $deleted_orders = array();
            $order_del_qry = $this->db->query("SELECT order_id FROM " . DB_PREFIX . "order_deleted_track WHERE  deleted_time>('".date('Y-m-d H:i:s',$this->request->post['last_order_deleted_date'])."'- INTERVAL 3 HOUR)");
           
            if($order_del_qry->rows) {    
                $deleted_orders['del_orders'] = array();
                $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` ORDER BY order_id LIMIT 1 ");
                if(!empty($query->row)){
                    $deleted_orders['del_orders']['first_order_id']  = $query->row['order_id'];
                }
                foreach($order_del_qry->rows as $row){
                     $deleted_orders['del_orders']['del_orders'][]=$row['order_id'];
                }               
            }
            $live_order_del_qry = $this->db->query("SELECT live_order_id FROM " . DB_PREFIX . "live_order_deleted_track WHERE live_order_id!=0 AND  deleted_time>('".date('Y-m-d H:i:s',$this->request->post['last_order_deleted_date'])."'- INTERVAL 900 SECOND)");
            if($live_order_del_qry->rows) {     
                $deleted_orders['del_live_orders'] = array();
                foreach($live_order_del_qry->rows as $drow){
                    $deleted_orders['del_live_orders']['del_orders'][] =$drow['live_order_id'];
                }                
            }
            
            $delmax = $this->db->query("SELECT MAX(deleted_time) as deletedtime FROM `order_deleted_track`"); 
            $deleted_orders['maxtime']=0;
            if(!empty($delmax->row)){
                 $deleted_orders['maxtime']= strtotime($delmax->row['deletedtime']);
            }
            $livedelmax = $this->db->query("SELECT MAX(deleted_time) as deletedtime FROM `live_order_deleted_track`");
            if(!empty($livedelmax->row) &&  $deleted_orders['maxtime']<strtotime($livedelmax->row['deletedtime'])){
               $deleted_orders['maxtime']= strtotime($livedelmax->row['deletedtime']);
            }
            
            return $deleted_orders;
        }
         return array();
    }
     public function eposoffine(){
        //$this->log->write('WEBSOK'. print_r($this->request->post,true));
        $this->db->query("UPDATE " . DB_PREFIX . "pos_client_system SET  online = 0 WHERE system_id = '" .(int)((isset($this->request->post['system_id'])?$this->request->post['system_id']:0))  . "'");
     }
        
     // Kitchen
    public function eposkitchen() {
         $orders =$this->eposkitchendata(); 
         $this->response->setOutput($this->jsonp(json_encode($orders)));
     }
    public function eposkitchendata() { 
      
        $this->request->post['last_order_updated_date'] = 0;
        $this->load->model('account/order');
        $pos_clients = $this->posclient->getPosClients();
        $tv_screens = array();
        foreach ($pos_clients as $pos_client) {
            if( strtoupper(substr($pos_client['system_name'],0,4)) ==  '#TV-'){
                $disp_settings = unserialize($pos_client['display_settings']);
                $tv_screens[$pos_client['system_id']] = (isset($disp_settings['products']) && !empty($disp_settings['products']))?$disp_settings['products']:array();
            }
        }
        //print_r($tv_screens);die();
        // Orders
        $orders = array('orders'=>array());
        
        $system_id = ((isset($this->session->data['system_id'])?$this->session->data['system_id']:0)); 
        
        if($system_id) {
             $display_settings = $this->posclient->getDisplaySettings($system_id);
             if(isset($display_settings['order_statuses']) && !empty($display_settings['order_statuses'])) {
                 $pos_kitcher_orders_statuss = $display_settings['order_statuses'];
                 // print_r($display_settings['order_statuses']);
                // $orders['order_statuses'] = $display_settings['order_statuses'];
             }
             else {
                 $pos_kitcher_orders_statuss = $this->config->get('pos_kitchen_orderstatuses');
             }
        }
        else {
            $pos_kitcher_orders_statuss = $this->config->get('pos_kitchen_orderstatuses');
        }
      
        $pos_kitchen_item_status = $this->config->get('pos_kitchen_item_status');
        $defaul_kitchen_item_status ='';
        foreach ($pos_kitchen_item_status as $item_status){
            if($item_status['type']==1){
                $defaul_kitchen_item_status = $item_status['name'];
            }
        }
        
        $live_kitcn_qry = $this->db->query("SELECT *  FROM `" . DB_PREFIX . "live_orders_to_kitchen`");
        $live_kitchen_array = array();
        $live_kitchen_array_copy = array();
        $live_order_track_times =array();
        $order_kitchen_array_copy = array();
        $current_live_orders = [];
        if($live_kitcn_qry->rows){
            foreach ($live_kitcn_qry->rows as $kittrack){
                if($kittrack['live_order_id']!=0){
                    if(!isset($live_kitchen_array[$kittrack['live_order_id']])){
                        $live_kitchen_array[$kittrack['live_order_id']]=[];                    
                    }
                    $live_kitchen_array[$kittrack['live_order_id']][] = $kittrack['track_id'];
                    $kittrack['items'] = unserialize($kittrack['items']);
                    $live_kitchen_array_copy[$kittrack['live_order_id'].'_'.$kittrack['track_id'].'_'.$kittrack['system_id']] = $kittrack;
                   // $current_live_orders[$kittrack['live_order_id']] =
                    if(!isset($live_order_track_times[$kittrack['live_order_id']])){
                        $live_order_track_times[$kittrack['live_order_id']]=[];
                    }
                    $live_order_track_times[$kittrack['live_order_id']][$kittrack['track_id']] = $kittrack['date_added'];
                }else{
                    if(!isset($order_kitchen_array_copy[$kittrack['order_id']])){
                        $order_kitchen_array_copy[$kittrack['order_id']]=[];
                    }
                    $kittrack['items'] = unserialize($kittrack['items']);
                    $order_kitchen_array_copy[$kittrack['order_id']][$kittrack['system_id']] =$kittrack ;
                }
            }
        }
        
                        
        $live_qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "live_orders`");
        
        
        // print_r($live_kitchen_array_copy);
        foreach($live_qry->rows as $live_data){ 
            
            //live_order_id  dinein_table_id
            //$this->log->write(print_r(json_decode(zlib_decode(base64_decode($live_data['data'])),true),true));
            $data = json_decode(zlib_decode(base64_decode($live_data['data'])),true);//'items'
            //$this->log->write(print_r($data,true));
            $current_live_orders[$live_data['live_order_id']] = (isset($data['dinein_table_name'])?$data['dinein_table_name']:$live_data['dinein_table_id']);
            if($live_data['lock']==0){
            if(isset($live_kitchen_array[$live_data['live_order_id']])){
                //$this->log->write(print_r("PLOAA",true));
                if(count($live_kitchen_array[$live_data['live_order_id']])!=count($data['track'])){
                    foreach($data['track'] as $track_index=>$track){
                        if(!in_array($track_index, $live_kitchen_array[$live_data['live_order_id']])){
                            foreach ($tv_screens as $screen_id=>$screenitems) {
                                $items= array();
                                foreach($track['added'] as $tradded){
                                    if((!empty($screenitems) && !empty($data['items']) && isset($data['items'][$tradded['item_index']]) && in_array($data['items'][$tradded['item_index']]['product_id'], $screenitems)) || empty($screenitems)){
                                        $itemdata =$data['items'][$tradded['item_index']];
                                        $itemdata['qty'] =$tradded['added_qty'];
                                        $itemdata['quantity'] =$tradded['added_qty'];
                                        $items[]=$itemdata;
                                    }
                                }
                                if(!empty($items)){
                                    $this->db->query("INSERT INTO " . DB_PREFIX . "live_orders_to_kitchen  SET  live_order_id  = " . $live_data['live_order_id'] ." ,order_id='".(int)(isset($data['order_id'])?$data['order_id']:0)."', track_id = " .$track_index. " , items = '" . $this->db->escape(serialize($items)) . "', status = '" .$defaul_kitchen_item_status . "',date_added='".$track['date_added']."',system_id='".(int)$screen_id."'");
                                    $live_kitchen_array_copy[$live_data['live_order_id'].'_'.$track_index.'_'.$screen_id] = array('id'=>$this->db->getLastId(),'live_order_id'=>$live_data['live_order_id'],'order_id'=>(int)(isset($data['order_id'])?$data['order_id']:0),'track_id'=>$track_index,'items'=>$items,'status'=>(int) $this->config->get('config_order_status_id'),'date_added'=>$track['date_added'],'system_id'=>(int)$screen_id);
                                }
                                
                            }
                           
                            //'table_name'=>(isset($data['dinein_table_name'])?$data['dinein_table_name']:$live_data['dinein_table_id'])
                            if(!isset($live_order_track_times[$live_data['live_order_id']])){
                                $live_order_track_times[$live_data['live_order_id']]=[];
                            }
                            $live_order_track_times[$live_data['live_order_id']][$track_index] = $track['date_added'];
                        }                        
                    } 
                }                
            }
            else{               
                foreach($data['track'] as $track_index=>$track){
                    foreach ($tv_screens as $screen_id=>$screenitems) {                        
                        $items= array();
                        foreach($track['added'] as $tradded){
                            //$this->log->write(print_r("--------FOR-----",true));                            
                           if((!empty($screenitems) && !empty($data['items']) && isset($data['items'][$tradded['item_index']]) && in_array($data['items'][$tradded['item_index']]['product_id'], $screenitems)) || empty($screenitems)){                            
                                $itemdata = $data['items'][$tradded['item_index']];
                                $itemdata['qty'] =$tradded['added_qty'];
                                $itemdata['quantity'] =$tradded['added_qty'];
                                $items[]=$itemdata;
                            }
                        }
                        if(!empty($items)){
                            $this->db->query("INSERT INTO " . DB_PREFIX . "live_orders_to_kitchen  SET  live_order_id  = " . $live_data['live_order_id'] ." ,order_id='".(int)(isset($data['order_id'])?$data['order_id']:0)."',track_id = " .$track_index. " , items = '" . $this->db->escape(serialize($items)) . "', status = '" . $defaul_kitchen_item_status . "',date_added='".$track['date_added']."',system_id='".(int)$screen_id."'");
                            $live_kitchen_array_copy[$live_data['live_order_id'].'_'.$track_index.'_'.$screen_id] = array('id'=>$this->db->getLastId(),'live_order_id'=>$live_data['live_order_id'],'order_id'=>(int)(isset($data['order_id'])?$data['order_id']:0),'track_id'=>$track_index,'items'=>$items,'status'=>(int) $this->config->get('config_order_status_id'),'date_added'=>$track['date_added'],'system_id'=>(int)$screen_id);
                        }
                        
                    }                    
                    //(isset($data['dinein_table_name'])?$data['dinein_table_name']:$live_data['dinein_table_id'])
                    if(!isset($live_order_track_times[$live_data['live_order_id']])){
                        $live_order_track_times[$live_data['live_order_id']]=[];
                    }
                    $live_order_track_times[$live_data['live_order_id']][$track_index] = $track['date_added'];
                }
            }                                               
        }                
        }                
        //print_r($live_kitchen_array_copy); 
        //die();
        // $this->log->write(print_r($live_kitchen_array_copy,true));
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE  order_status_id IN (" . implode(',', $pos_kitcher_orders_statuss) .") AND NOW() - INTERVAL 1 DAY < date_added AND shipping_code!='dine.dine'" );
        //$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE  order_status_id IN (" . implode(',', $pos_kitcher_orders_statuss) .")" );
        if($order_query->rows){
            foreach($order_query->rows as $order_data){
                $orders['orders'][$order_data['order_id']]=$this->getOrderData($order_data);
            }
        }
        
        // }
        
        $query_orders = $this->db->query("SELECT DISTINCT order_id,live_order_id,track_id,system_id  FROM " . DB_PREFIX . "kitchen_items ");
        $kitchen_orders = array();
        
        $prev_live_kitchen_orders=array();
        
       // $this->log->write(print_r($query_orders->rows,true));
        foreach($query_orders->rows as $item_data){
            
            if($item_data['live_order_id']!=0){
                
                if(!isset($prev_live_kitchen_orders[$item_data['live_order_id']])){
                    $prev_live_kitchen_orders[$item_data['live_order_id']]=[];
                }                
                
                $prev_live_kitchen_orders[$item_data['live_order_id']][]=$item_data['track_id'];
                
            }else{
                
                $kitchen_orders[] = $item_data['order_id'];                
                
            }
            
        }
        
        $kitchen_orders = array_unique($kitchen_orders);
        
        

        // $orders = array('orders'=>array_slice($this->orders(),2,8));
         //$this->log->write("LV KT ARR CPy");
        //$this->log->write(print_r($live_kitchen_array_copy,true));
        //$this->log->write(print_r($prev_live_kitchen_orders,true));
        
        foreach($orders['orders'] as $key => $order){
            if(!in_array($order['order_id'],$kitchen_orders)) {  
                foreach ($tv_screens as $screen_id=>$screenitems) {
                    $items = array();
                    foreach( $order['items'] as $item) {
                        if((!empty($screenitems) && in_array($item['product_id'], $screenitems)) || empty($screenitems)){
                            $items[]=$item;
                            $this->db->query("INSERT INTO " . DB_PREFIX . "kitchen_items  SET  order_id  = " . $order['order_id'] ." , order_product_id = " .$item['order_product_id']. " ,live_order_id=0, product_id = " . (($item['product_id'] && $item['product_id']!='')!='0'?$item['product_id']:$item['order_product_id']). ", item = '" . $this->db->escape(serialize($item)) ."', product_name ='".$this->db->escape($item['name']) . "', order_type='O', item_status = '" . $defaul_kitchen_item_status . "',system_id='".(int)$screen_id."'");
                        
                            
                        }
                    }
                    if(!empty($items)){ 
                        
                        $this->db->query("INSERT INTO " . DB_PREFIX . "live_orders_to_kitchen  SET  live_order_id=0,order_id='".(int)$order['order_id']."',track_id = 0 , items = '" . $this->db->escape(serialize($items)) . "', status = '" . $defaul_kitchen_item_status. "',date_added='".$order['date_added']."',system_id='".(int)$screen_id."'");
                        if(!isset($order_kitchen_array_copy[$order['order_id']])){
                            $order_kitchen_array_copy[$order['order_id']]=[];
                        }
                        $order_kitchen_array_copy[$order['order_id']][$screen_id]=array('live_order_id'=>0,'order_id'=>(int)$order['order_id'],'track_id'=>0,'items'=>$items,'status'=>$defaul_kitchen_item_status,'date_added'=>$order['date_added'],'system_id'=>$screen_id);
                    }
                }                                
            }
            else{
                $okey = array_search($order['order_id'], $kitchen_orders);
                if (false !== $okey) {
                    unset($kitchen_orders[$okey]);
                }
            }
            $kitcehnstatuses = [];
            $orders['orders'][$key]['items'] = array();
            $query_items = $this->db->query("SELECT kitchen_item_id,item,item_status,system_id	FROM " . DB_PREFIX . "kitchen_items WHERE order_id  = " . $order['order_id'] ."");
            $screen_items =[];
            foreach( $query_items->rows as $cartitem) {
                if(!isset($screen_items[$cartitem['system_id']])){
                    $screen_items[$cartitem['system_id']]=array();
                }
                if(!isset($kitcehnstatuses[$cartitem['system_id']])){
                    //$defaul_kitchen_item_status
                    $kitcehnstatuses[$cartitem['system_id']]=(isset($order_kitchen_array_copy[$key]) && isset($order_kitchen_array_copy[$key][$cartitem['system_id']]))?$order_kitchen_array_copy[$key][$cartitem['system_id']]['status']:$defaul_kitchen_item_status;
                }
                $cartitem =  $cartitem + unserialize($cartitem['item']);  
                unset($cartitem['item']);
                $screen_items[$cartitem['system_id']][] = $cartitem;
            }
            $orders['orders'][$key]['items'] = $screen_items;
            $orders['orders'][$key]['kitchenstatuses']=$this->model_account_order->getOrderKitchenStatus($key);;
            
            /*
            // print_r($order['items']);
            //$query_orders = $this->db->query("SELECT * FROM " . DB_PREFIX . "kitchen_items WHERE order_id  = " . $order['order_id'] ."");
            if(!in_array($order['order_id'],$kitchen_orders)) {  
                //$this->log->write(print_r(array("INSERTED-".$order['order_id']),true));
                foreach( $order['items'] as $item) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "kitchen_items  SET  order_id  = " . $order['order_id'] ." , order_product_id = " .$item['order_product_id']. " , product_id = " . $item['product_id'] . ", item = '" . $this->db->escape(serialize($item)) ."', product_name ='".$this->db->escape($item['name']) . "', order_type='O', item_status = '" . $defaul_kitchen_item_status . "'");
                }
            }
            else {
                $okey = array_search($order['order_id'], $kitchen_orders);
                if (false !== $okey) {
                    unset($kitchen_orders[$okey]);
                }
            }
            $orders['orders'][$key]['items'] = array();
            $query_items = $this->db->query("SELECT kitchen_item_id,item,item_status FROM " . DB_PREFIX . "kitchen_items WHERE order_id  = " . $order['order_id'] ."");
            foreach( $query_items->rows as $cartitem) {
                $cartitem =  $cartitem + unserialize($cartitem['item']);  
                unset($cartitem['item']);
                $orders['orders'][$key]['items'][] = $cartitem;
            }
            */
        }
        
        
        foreach($live_kitchen_array_copy as $livekitchendata){
            if(!isset($current_live_orders[$livekitchendata['live_order_id']])){
                $this->db->query("DELETE FROM  " . DB_PREFIX . "live_orders_to_kitchen  WHERE  live_order_id  = " . $livekitchendata['live_order_id']." ");
                $this->db->query("DELETE FROM  " . DB_PREFIX . "kitchen_items  WHERE  live_order_id  = " . $livekitchendata['live_order_id']." ");
            }else{
                if(!isset($prev_live_kitchen_orders[$livekitchendata['live_order_id']]) || !in_array($livekitchendata['track_id'], $prev_live_kitchen_orders[$livekitchendata['live_order_id']])){
                    //
                    foreach($livekitchendata['items'] as $item){
                        $this->db->query("INSERT INTO " . DB_PREFIX . "kitchen_items  SET  live_order_id  = '".$livekitchendata['live_order_id']."' ,order_id='".(isset($livekitchendata['order_id'])?$livekitchendata['order_id']:0)."', track_id = '".$livekitchendata['track_id']."',  item_index = " . $item['item_index'] . ", product_id = " . $item['product_id'] . ", item = '" . $this->db->escape(serialize($item)) ."', product_name ='".$this->db->escape($item['name']) . "', order_type='L', item_status = '" . $defaul_kitchen_item_status . "',system_id='".(int)$livekitchendata['system_id']."'");
                    }
                } 
            }                      
        }
        
        //$this->log->write(print_r('testtttt',true)); 
        // $orders = array('orders'=>array());//remove this
        //print_r(array('testtt'));
        $query_live_items = $this->db->query("SELECT * FROM " . DB_PREFIX . "kitchen_items WHERE order_type='L'");
        //print_r($query_live_items->rows);
        foreach( $query_live_items->rows as $liveitem1) {
            $liveitem =  unserialize($liveitem1['item']); 
            $liveitem['kitchen_item_id']=$liveitem1['kitchen_item_id'];
            $liveitem['order_id']=$liveitem1['order_id'];
            $liveitem['order_product_id']=$liveitem1['order_product_id'];
            $liveitem['live_order_id']=$liveitem1['live_order_id'];
            $liveitem['track_id']=$liveitem1['track_id'];
            $liveitem['item_index']=$liveitem1['item_index'];
            $liveitem['order_type']=$liveitem1['order_type'];
            $liveitem['product_id']=$liveitem1['product_id'];
            $liveitem['product_name']=$liveitem1['product_name'];
            $liveitem['item_status']=$liveitem1['item_status'];
            $liveitem['system_id']=$liveitem1['system_id'];
            if($current_live_orders[$liveitem['live_order_id']]=='0'){
                if(!isset($orders['orders'][$liveitem['live_order_id']])){
                    $orders['orders'][$liveitem['live_order_id']] = array('live_order_id'=>$liveitem['live_order_id'],'order_id'=>$liveitem['order_id'],'items'=>array(),'order_status_id'=>(isset($live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]) && isset($live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]['status']))?$live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]['status']:$this->config->get('config_order_status_id'),'status'=>(isset($live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]) && isset($live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]['status']))?$live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]['status']:$this->config->get('config_order_status_id'),'live'=>1,'track_id'=>(int)$liveitem['track_id']+1,'table_name'=>$current_live_orders[$liveitem['live_order_id']],'unix_date_added'=>(int)$live_order_track_times[$liveitem['live_order_id']][$liveitem['track_id']]);                
                }

                //print_r(unserialize($liveitem['item']));

                if(!isset($orders['orders'][$liveitem['live_order_id']]['items'][$liveitem['system_id']])){
                    $orders['orders'][$liveitem['live_order_id']]['items'][$liveitem['system_id']]=[];
                }
                $orders['orders'][$liveitem['live_order_id']]['items'][$liveitem['system_id']][]=$liveitem;     
                $orders['orders'][$liveitem['live_order_id']]['kitchenstatuses']=$this->model_account_order->getLiveOrderKitchenStatus($liveitem['live_order_id']);;

            }else{
            if(!isset($orders['orders'][$liveitem['live_order_id'].'_'.$liveitem['track_id']])){
                $orders['orders'][$liveitem['live_order_id'].'_'.$liveitem['track_id']] = array('live_order_id'=>$liveitem['live_order_id'],'order_id'=>$liveitem['order_id'],'items'=>array(),'order_status_id'=>(isset($live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]) && isset($live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]['status']))?$live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]['status']:$this->config->get('config_order_status_id'),'status'=>(isset($live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]) && isset($live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]['status']))?$live_kitchen_array_copy[$liveitem['live_order_id'].'_'.$liveitem['track_id'].'_'.$liveitem['system_id']]['status']:$this->config->get('config_order_status_id'),'live'=>1,'track_id'=>(int)$liveitem['track_id']+1,'table_name'=>$current_live_orders[$liveitem['live_order_id']],'unix_date_added'=>(int)$live_order_track_times[$liveitem['live_order_id']][$liveitem['track_id']]);                
            }
            
            //print_r(unserialize($liveitem['item']));
            
            if(!isset($orders['orders'][$liveitem['live_order_id'].'_'.$liveitem['track_id']]['items'][$liveitem['system_id']])){
                $orders['orders'][$liveitem['live_order_id'].'_'.$liveitem['track_id']]['items'][$liveitem['system_id']]=[];
            }
            $orders['orders'][$liveitem['live_order_id'].'_'.$liveitem['track_id']]['items'][$liveitem['system_id']][]=$liveitem;     
            $orders['orders'][$liveitem['live_order_id'].'_'.$liveitem['track_id']]['kitchenstatuses']=$this->model_account_order->getLiveOrderKitchenStatus($liveitem['live_order_id']);;

        }
        }
        //print_r($orders);
        //die();
        //$this->log->write(print_r($kitchen_orders,true));
        // delete order served
    
        foreach($kitchen_orders as $kitchen_order_id) {
            $this->db->query("DELETE FROM  " . DB_PREFIX . "kitchen_items  WHERE  order_id  = " . $kitchen_order_id." ");
            $this->db->query("DELETE FROM  " . DB_PREFIX . "live_orders_to_kitchen  WHERE  order_id  = " . $kitchen_order_id." ");
        }
        
        $query_items = $this->db->query("SELECT item_status, product_id, product_name ,system_id  FROM " . DB_PREFIX . "kitchen_items");
        $orders['kitchenitems'] = array();
        
        foreach( $query_items->rows as $kitchenitem) {
            if(!isset($orders['kitchenitems'][$kitchenitem['item_status']])){
                $orders['kitchenitems'][$kitchenitem['item_status']] = array();
            }
            $orders['kitchenitems'][$kitchenitem['item_status']][] = $kitchenitem;
        }
        //print_r($orders['kitchenitems']);
        //die();
        return $orders;
        
        //$this->response->setOutput($this->jsonp(json_encode($orders)));
    }
    public function eposkitchenitemupdate() {
        //$this->log->write(print_r($this->request->post,true));
        if(isset($this->request->post['kitchen_item_id']) && isset($this->request->post['item_status'])) {
            $this->db->query("UPDATE  " . DB_PREFIX . "kitchen_items  SET   item_status = '" . $this->request->post['item_status'] . "' WHERE kitchen_item_id='". $this->request->post['kitchen_item_id'] ."'");
            $this->websocket->connect('o-'.$this->request->post['kitchen_item_id']);
        }
        
    }
    public function eposkitchenliveorderupdate() {
        if(isset($this->request->post['live_order_id']) && isset($this->request->post['track_id']) ) {// && 
            $this->db->query("UPDATE  " . DB_PREFIX . "live_orders_to_kitchen  SET   status = '" .(int) $this->request->post['status_id'] . "' WHERE live_order_id='". (int)$this->request->post['live_order_id'] ."' AND track_id='".(int)$this->request->post['track_id']."' AND system_id='".$this->request->post['system_id']."'");
            $this->websocket->connect('lo-'.$this->request->post['live_order_id']);
        }
    }
    public function eposkitchenorderupdate() {
        if(isset($this->request->post['order_id']) && isset($this->request->post['status_id'])) {
            $this->load->model('checkout/order');
            $this->model_checkout_order->changeOrderStatus((int)$this->request->post['order_id'] ,array('order_status_id'=>(int)$this->request->post['status_id'],'notify'=>0,'comment'=>''));
            $this->websocket->connect('o-'.$this->request->post['order_id']);
        }
    }
    private function Onlinepayments() {
        //$this->log->write("SELECT * FROM `" . DB_PREFIX . "epos_payonline` WHERE rq_time > NOW()- INTERVAL 600 SECOND ");
        //$this->log->write("SELECT * FROM `" . DB_PREFIX . "epos_payonline` WHERE rq_time>'".date('Y-m-d H:i:s',$this->request->post['order_updated_date'])."' - INTERVAL 600 SECOND");
        if($this->request->post['order_updated_date']){            
            $pay_qru = $this->db->query("SELECT * FROM `" . DB_PREFIX . "epos_payonline` WHERE rq_time>'".date('Y-m-d H:i:s',$this->request->post['order_updated_date'])."' - INTERVAL 600 SECOND");
        }else{
            $pay_qru = $this->db->query("SELECT * FROM `" . DB_PREFIX . "epos_payonline` WHERE rq_time > NOW()- INTERVAL 600 SECOND     ");
        }
        return $pay_qru->rows;
    }
    
}