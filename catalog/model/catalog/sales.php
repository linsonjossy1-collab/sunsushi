<?php

class ModelCatalogSales extends Model {

    public function getStoresalesreport($data) {
        $sql = "SELECT COUNT(o.order_id) as count, SUM(o.total) as total , o.store_id, o.store_name FROM `" . DB_PREFIX . "order` o WHERE o.order_status_id > '0'";

        if (!empty($data['filter_date_start'])) {
            $sql .= "AND  date_added > '" . $this->db->escape($data['filter_date_start']) . "'";
        }

        if (!empty($data['filter_date_end'])) {
            $sql .= " AND date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }
        $sql .=" AND store_id = '".$this->config->get('pos_to_storeid')."'  GROUP BY store_id";
        //echo $sql;
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getStorePayemntreport($data) {
        $sql = "SELECT COUNT(DISTINCT o.order_id) as count, SUM(ot.value) as total  FROM `" . DB_PREFIX . "order` o LEFT OUTER JOIN `" . DB_PREFIX . "order_total` ot ON(o.order_id=ot.order_id) WHERE  o.order_status_id > '0' AND store_id='" . $data['store_id'] . "' AND payment_code='" . $data['paymentcode'] . "' AND ot.code='total'";
        if (!empty($data['filter_date_start'])) {
            $sql .= " AND date_added > '" . $this->db->escape($data['filter_date_start']) . "'";
        }
        if (!empty($data['filter_date_end'])) {
            $sql .= " AND date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }
        $sql .=" AND store_id = '".$data['store_id'] ."'";
        $query = $this->db->query($sql);
        return $query->row;
    }

public function getSplitpaymentReport($data){
        $sql = "SELECT count(ot.title) as count,SUM(ot.value) as payedtotal,ot.title FROM `" . DB_PREFIX . "order_split_total` ot LEFT JOIN `" . DB_PREFIX . "order_split` os  ON(ot.order_split_id=os.order_split_id) LEFT JOIN `" . DB_PREFIX . "order` o ON(os.order_id=o.order_id) WHERE  o.order_status_id > '0'";
        if(!empty($data['filter_date_start'])) {
            $sql .= " AND o.date_added > '" . $this->db->escape($data['filter_date_start']) . "'";
        }
        if(!empty($data['filter_date_end'])) {
            $sql .= " AND o.date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }
        $sql .=" AND store_id = '".$data['store_id']."'";
        $sql .=' GROUP BY ot.title';
        //echo $sql;
        $query = $this->db->query($sql);
        return $query->rows;
        
    }
    public function getSubpaymentReport($data){
        $sql = "SELECT COUNT(DISTINCT o.order_id) as count, SUM(ot.value) as total,ot.title as title  FROM `" . DB_PREFIX . "order` o LEFT OUTER JOIN `" . DB_PREFIX . "order_total` ot ON(o.order_id=ot.order_id) WHERE  o.order_status_id > '0' AND payment_code='" . $data['paymentcode'] . "' AND ot.code='cashtotal' AND ot.title LIKE 'Cash'";
        if (!empty($data['filter_date_start'])) {
            $sql .= " AND o.date_added > '" . $this->db->escape($data['filter_date_start']) . "'";
        }
        if (!empty($data['filter_date_end'])) {
            $sql .= " AND o.date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }
        $sql .=" AND store_id = '".$data['store_id']."'";
        //echo $sql;
        $query = $this->db->query($sql);
        //echo $sql;
        
        //echo "<br>";
        return $query->row;
    }
    
    public function getStoreDeliveryreport($data) {
        $sql = "SELECT COUNT(o.order_id) as count, SUM(o.total) as total,o.shipping_method as name  FROM `" . DB_PREFIX . "order` o WHERE  o.order_status_id > '0' AND store_id='" . $data['store_id'] . "' AND shipping_code='" . $data['shippingcode'] . '.' . $data['shippingcode'] . "'";
        if (!empty($data['filter_date_start'])) {
            $sql .= " AND date_added > '" . $this->db->escape($data['filter_date_start']) . "'";
        }

        if (!empty($data['filter_date_end'])) {
            $sql .= " AND date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }
        $sql .=" AND store_id = '".$data['store_id']."'";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getTotalStoresales($data) {
        $sql = "SELECT COUNT(o.order_id) as count, SUM(o.total) as total FROM `" . DB_PREFIX . "order` o WHERE  o.order_status_id > '0'";
        if (!empty($data['filter_date_start'])) {
            $sql .= " AND date_added > '" . $this->db->escape($data['filter_date_start']) . "'";
        }

        if (!empty($data['filter_date_end'])) {
            $sql .= " AND date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }

        if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
            //$sql .= " WHERE o.order_status_id = '" . (int) $data['filter_order_status_id'] . "'";
        } else {
            //$sql .= " WHERE o.order_status_id > '0'";
        }
        $sql .=" AND store_id = '".$data['store_id']."'";
        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getStorePayemntTotal($data) {
        $sql = "SELECT COUNT(o.order_id) as count, SUM(o.total) as total   FROM `" . DB_PREFIX . "order` o WHERE payment_code='" . $data['paymentcode'] . "' AND o.order_status_id > '0'";
        if (!empty($data['filter_date_start'])) {
            $sql .= " AND date_added > '" . $this->db->escape($data['filter_date_start']) . "'";
        }

        if (!empty($data['filter_date_end'])) {
            $sql .= " AND date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }
        $sql .=" AND store_id = '".$data['store_id']."'";
        
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getStoreDeliveryTotal($data) {
        $sql = "SELECT COUNT(o.order_id) as count, SUM(o.total) as total   FROM `" . DB_PREFIX . "order` o WHERE shipping_code='" . $data['shippingcode'] . '.' . $data['shippingcode'] . "' AND o.order_status_id > '0'";
        if (!empty($data['filter_date_start'])) {
            $sql .= " AND date_added > '" . $this->db->escape($data['filter_date_start']) . "'";
        }

        if (!empty($data['filter_date_end'])) {
            $sql .= " AND date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }

        $sql .=" AND store_id = '".$data['store_id']."'";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getProductSalesreport($data) {
        $sql = "SELECT SUM(op.quantity) as qty,SUM(op.total) as price,op.name as product_name FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON(op.order_id=o.order_id) WHERE o.order_status_id > '0'";
        if (isset($data['store_id'])) {
            $sql .= " AND store_id ='" . $data['store_id'] . "'";
        }
        if (!empty($data['filter_date_start'])) {
            $sql .= " AND date_added > '" . $this->db->escape($data['filter_date_start']) . "'";
        }

        if (!empty($data['filter_date_end'])) {
            $sql .= " AND date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }
        $sql .=' GROUP BY product_id ORDER BY qty  DESC';
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getBundleSalesreport($data) {
        $sql = "SELECT SUM(ob.quantity) as qty,ob.name as product_name FROM `" . DB_PREFIX . "order_bundle` ob LEFT JOIN `" . DB_PREFIX . "order` o ON(ob.order_id=o.order_id) WHERE o.order_status_id > '0'";
        if (!empty($data['filter_date_start'])) {
            $sql .= " AND date_added > '" . $this->db->escape($data['filter_date_start']) . "'";
        }

        if (!empty($data['filter_date_end'])) {
            $sql .= " AND date_added <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }
        $sql .=' GROUP BY bundle_product_id ORDER BY qty  DESC';
        $query = $this->db->query($sql);
        return $query->rows;
    }
    
    public function addSalesReport($data) {
        $data = json_decode(base64_decode($data),true);
        //echo $data['skip_time'];
        $this->db->query("INSERT INTO `" . DB_PREFIX . "day_sales_report` SET end_day_time = '".(isset($data['report_time'])?$data['report_time']:'NOW()')."', report_time = NOW(),details = '".serialize($data)."',system_id='".(isset($this->session->data['system_id'])?$this->session->data['system_id']:0)."',user_id='".(isset($this->session->data['system_id'])?$this->session->data['user_id']:0)."',report_from_time='".$data['report_from_time']."'");
        
    }
    public function getLastreporttime(){
        //echo "SELECT DISTINCT end_day_time FROM `" . DB_PREFIX . "day_sales_report` ORDER BY report_id DESC";
        $query = $this->db->query("SELECT DISTINCT end_day_time FROM `" . DB_PREFIX . "day_sales_report` ORDER BY report_id DESC LIMIT 1");
        if($query->row){
            return $query->row['end_day_time'];
        }else{
            return false;
        }
        
        
    }
    
    public function checkneworders($start,$end){
        $query = $this->db->query("SELECT o.date_added FROM `" . DB_PREFIX . "order` o WHERE o.date_added > '".$start."'  AND  o.date_added <= '".$end."' ORDER BY o.date_added DESC");
        return $query->rows;
    }
    
    public function checktodayreportsaved(){
         $last_report_time = $this->getLastreporttime();
            //echo "<br>";
            //echo date('Y-m-d H:i:s',strtotime('now')).'-';
            $current_time = strtotime('now');
            $today = strtolower(date('l'));
            $yesterday = strtolower(date('l', strtotime('yesterday')));
            $tomorrow = strtolower(date('l', strtotime('tomorrow')));
            $today_times = $today . '_store_times';
            $yesterday_times = $yesterday . '_store_times';
            $tomorrow_times = $tomorrow . '_store_times';
            $store_open_times = array();
            $today_open_times = $this->config->get($today_times);
            $yesterday_open_times = $this->config->get($yesterday_times);
            $tomorrow_open_times = $this->config->get($tomorrow_times);  
            $tomorrow_times = $this->config->get($tomorrow . '_store_times');
        
            $day_times = $this->config->get($today . '_store_times');
            //print_r($day_times);
            if(isset($day_times[key($day_times)]['start']) and !empty($day_times[key($day_times)]['start'])){
                $start_times =  explode(':', $day_times[key($day_times)]['start']);
                $end_times   =  explode(':', $day_times[key(array_reverse($day_times, true))]['end']);
                $today_start = date('Y-m-d H:i:s',  mktime((int)$start_times[0],(int)$start_times[1],0, date("m"), date("d"), date("Y")));
                //$today_end  = (($start_times[0] > $end_times[0])?date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d")+1, date("Y"))):date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d"), date("Y"))));
            }
            if(isset($tomorrow_times[key($tomorrow_times)]['start']) and !empty($tomorrow_times[key($tomorrow_times)]['start'])){
                $start_times =  explode(':', $tomorrow_times[key($tomorrow_times)]['start']);
                $end_times   =  explode(':', $tomorrow_times[key(array_reverse($tomorrow_times, true))]['end']);
                $tomorrow_start = date('Y-m-d H:i:s',  mktime((int)$start_times[0],(int)$start_times[1],0, date("m"), date("d")+1, date("Y")));
                //$today_end  = (($start_times[0] > $end_times[0])?date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d")+1, date("Y"))):date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d"), date("Y"))));
            }
            $i = 0;
            foreach ($yesterday_open_times as $yesterday_open_time) {
              if(empty($yesterday_open_time['start'])){
                  continue;
              }
              $yes_start_times = explode(':', $yesterday_open_time['start']);
              $yes_end_times = explode(':', $yesterday_open_time['end']);
              $store_open_times[$i]['start']=mktime((int)$yes_start_times[0],(int) $yes_start_times[1], 0, date("m"), date("d") - 1 , date("Y"));
              $store_open_times[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 1 , date("Y"));
              if ((int)$yes_start_times[0] > (int)$yes_end_times[0])
              {
                  $store_open_times[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d"), date("Y"));
              }
              $store_open_times[$i]['org_start']=  date('Y-m-d H:i:s',$store_open_times[$i]['start']);
              $store_open_times[$i]['org_end']=  date('Y-m-d H:i:s',$store_open_times[$i]['end']);      
              $i++;
          }
            foreach ($today_open_times as $today_open_time) {
                if(empty($today_open_time['start'])){
                    continue;
                }
                $tod_start_times = explode(':', $today_open_time['start']);
                $tod_end_times = explode(':', $today_open_time['end']);
                $store_open_times[$i]['start']=mktime((int)$tod_start_times[0], (int)$tod_start_times[1], 0, date("m"), date("d"), date("Y"));

                $store_open_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d"), date("Y"));
                if ((int)$tod_start_times[0] > (int)$tod_end_times[0])
                {

                    $store_open_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+1, date("Y"));
                }    
                $store_open_times[$i]['org_start']=  date('Y-m-d H:i:s',$store_open_times[$i]['start']);
              $store_open_times[$i]['org_end']=  date('Y-m-d H:i:s',$store_open_times[$i]['end']); 
                $i++;
            }
       
            foreach ($tomorrow_open_times as $tomorrow_open_time) {
            if(empty($tomorrow_open_time['start'])){
                continue;
            }
            $tom_start_times = explode(':', $tomorrow_open_time['start']);
            $tom_end_times = explode(':', $tomorrow_open_time['end']);
            $store_open_times[$i]['start']=mktime((int)$tom_start_times[0], (int)$tom_start_times[1], 0, date("m"), date("d")+1, date("Y"));
            
            $store_open_times[$i]['end']=mktime((int)$tom_end_times[0],(int) $tom_end_times[1], 0, date("m"), date("d")+1, date("Y"));
            if ((int)$tom_start_times[0] > (int)$tom_end_times[0])
            {
                $store_open_times[$i]['end']=mktime((int)$tom_end_times[0], (int)$tom_end_times[1], 0, date("m"), date("d")+2, date("Y"));
            }    
            $store_open_times[$i]['org_start']=  date('Y-m-d H:i:s',$store_open_times[$i]['start']);
            $store_open_times[$i]['org_end']=  date('Y-m-d H:i:s',$store_open_times[$i]['end']); 
            $i++;
        }
        
        
            foreach ($store_open_times as $store_open_time) {
                if ($current_time >= $store_open_time['start'] && $current_time <= $store_open_time['end']) {
                     $today =  strtolower(date('l', $store_open_time['start']));
                     $tomorrow =  strtolower(date('l',strtotime('+1 day',  $store_open_time['start'])));
                     $tomorrow_times = $this->config->get($tomorrow . '_store_times');
        
                            $day_times = $this->config->get($today . '_store_times');
                            if(isset($day_times[key($day_times)]['start']) and !empty($day_times[key($day_times)]['start'])){
                                $start_times =  explode(':', $day_times[key($day_times)]['start']);
                                $end_times   =  explode(':', $day_times[key(array_reverse($day_times, true))]['end']);
                                $today_start = date('Y-m-d H:i:s',  mktime((int)$start_times[0],(int)$start_times[1],0, date('m', $store_open_time['start']), date('d', $store_open_time['start']), date('Y', $store_open_time['start'])));
                                //$today_end  = (($start_times[0] > $end_times[0])?date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d")+1, date("Y"))):date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d"), date("Y"))));
                            }
                            if(isset($tomorrow_times[key($tomorrow_times)]['start']) and !empty($tomorrow_times[key($tomorrow_times)]['start'])){
                                $start_times =  explode(':', $tomorrow_times[key($tomorrow_times)]['start']);
                                $end_times   =  explode(':', $tomorrow_times[key(array_reverse($tomorrow_times, true))]['end']);
                                $tomorrow_start = date('Y-m-d H:i:s',  mktime((int)$start_times[0],(int)$start_times[1],0, date("m",strtotime('+1 day',  $store_open_time['start'])), date("d",strtotime('+1 day',  $store_open_time['start'])), date("Y",strtotime('+1 day',  $store_open_time['start']))));
                                //$today_end  = (($start_times[0] > $end_times[0])?date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d")+1, date("Y"))):date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d"), date("Y"))));
                            }
                     break;
                }
            }
        //new
        
        //print_r($store_open_times);
        //echo $today.'--'.$tomorrow;
        $status = false;
       if((strtotime($last_report_time) > strtotime($today_start)) and ($current_time < strtotime($tomorrow_start))){
            $status = true;
           //echo "sss";
        }
        
        return $status;
        
    }
    
    public function getreportdata($report_date){
        $query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "day_sales_report` WHERE end_day_time='".$report_date."'");
        return $query->row;
    }
    public function getAllstores() {
        $query = $this->db->query("SELECT  store_id,value FROM `" . DB_PREFIX . "setting` WHERE `key`='config_nick_name'");
        return $query->rows;
    } 
}

?>