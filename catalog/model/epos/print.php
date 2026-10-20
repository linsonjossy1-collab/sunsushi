<?php
class ModelEposPrint extends Model {
    public function createPintqueue($system_id,$data,$status=0) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'last_data_modified_time'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_time', `key` = 'last_data_modified_time', `value` = NOW()");
        $this->db->query("INSERT INTO " . DB_PREFIX . "pos_print SET data = '" . $this->db->escape($data). "', system_id = '".(int)$system_id."', status = '".(int)$status."', `time` = NOW()");
        return $this->db->getLastId();
    }
    public function getPrintqueue($system_id,$order_updated_date){
        $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "pos_print WHERE system_id='". $system_id ."' AND  status=0  AND `time`>'".date('Y-m-d H:i:s',$order_updated_date)."' - INTERVAL 600 SECOND ORDER BY queue_id ASC ");
        $printdata = array();
        
        foreach($query->rows as $key=>$row){
            $row['data'] = html_entity_decode($row['data']);
            $printdata[]=$row;            
        }
        //$this->log->write(print_r($printdata,true));
        $this->db->query("UPDATE " . DB_PREFIX . "pos_print SET status = 1 WHERE system_id='". $system_id ."' AND  status = 0  AND `time`>'".date('Y-m-d H:i:s',$order_updated_date)."' - INTERVAL 600 SECOND");
        return $printdata;
    }
    public function DeletePrintqueue($queue_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "pos_print WHERE queue_id='".(int)$queue_id."'");
    }
    public function UpdatePrintqueue($queue_id,$status) {
         $this->db->query("UPDATE " . DB_PREFIX . "pos_print SET status = '" .(int)$status. "' WHERE queue_id='".$queue_id."'");
    }
    public function getPrintlist(){
        $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "pos_print WHERE  status<>2  ORDER BY queue_id ASC ");
        return $query->rows;
    }
}
?>