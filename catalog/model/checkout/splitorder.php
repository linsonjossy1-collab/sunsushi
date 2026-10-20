<?php

class ModelCheckoutSplitorder extends Model {

    //INSERT INTO order_split  order_id,split_id,payment_total,status=0
    public function saveSplit($data,$print_areas=array()){
        $order_id = (isset($this->session->data['order_id']))?$this->session->data['order_id']:0;
        $this->deleteOrderSplits($order_id);
        $split_no=1;
        if(!empty($data)){
        foreach($data as $split){
            if($split['split_type']=='item'){
                $splitype='I';
            }else if($split['split_type']=='Equal'){
                $splitype='E';
            }else{
                $splitype='C';
            }
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_split` SET order_id=".(int)$order_id." , split_id=".(int)$split_no.",payment_amount='". (float)$split['amount']."',split_type='".$splitype."',status='".((isset($split['payed']))?1:0)."',`print_status`=".(!empty($print_areas)?0:1).",print_areas='".  serialize($print_areas)."'");
            $order_split_id = $this->db->getLastId();
            if(isset($split['products'])){
                foreach ($split['products'] as $cart_key=>$split_product){
                    $product = explode(':', $cart_key);
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_split_product` SET order_split_id=".(int)$order_split_id.",product_id=".(int)$product[0]." ,quantity=".(int)$split_product." ,cart_key='".$this->db->escape($cart_key)."'");
                }
            }
            if(isset($split['payments'])){
                foreach ($split['payments'] as $code=>$amount){
                    $payment_code = (isset($this->session->data['payment_methods'][$code]['title']) ? $this->session->data['payment_methods'][$code]['title'] : $code);
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_split_total` SET order_split_id=".(int)$order_split_id.",code='payment' ,title='".$this->db->escape($payment_code)."' ,text='',value='".(float)$amount."',sort_order=''");
                }
            }
            //if(discounts)  add another total as - 
            $split_no++;
        }
        }
        
    }
    public function editSplit(){
        
    }
    public function addOrdertoSplits($details,$split_no) {
        $ordes_query = $this->db->query("INSERT INTO `" . DB_PREFIX . "order_split` SET order_id=".(int)$details['order_id']." , split_id=".(int)$split_no.",payment_amount='". $this->db->escape($details['payment_amount'])."',split_type='".$details['split_type']."',status=0");
        return $this->db->getLastId();
    }

    public function getSplittedOrderDetails($order_id){
        $ordes_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_split`  WHERE  order_id=".(int) $order_id."");
        return $ordes_query->rows;
    }
    //UPDATE order_split set payment details ,status =1
    public function paySplit($data) {
       $this->db->query("UPDATE `" . DB_PREFIX . "order_split` SET payment_method='". $this->db->escape($data['payment_method'])."',payment_code='". $this->db->escape($data['payment_code'])."',status=1 WHERE order_split_id='".(int)$data['order_split_id']."'");
    }

    //Split id
    public function removeSplit() {
        
    }

    public function checkSplittedOrder($order_id) {
        $ordes_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_split`  WHERE  order_id=".(int) $order_id."");
        return $ordes_query->rows;
    }
    
    public function deleteOrderSplits($order_id){
        $ordes_query = $this->db->query("SELECT order_split_id FROM `" . DB_PREFIX . "order_split`  WHERE  order_id=".(int) $order_id."");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_split`  WHERE  order_id=".(int) $order_id."");
        foreach($ordes_query->rows as $row){
            $this->db->query("DELETE FROM `" . DB_PREFIX . "order_split_product`  WHERE  order_split_id=".(int) $row['order_split_id']."");
            $this->db->query("DELETE FROM `" . DB_PREFIX . "order_split_total`  WHERE  order_split_id=".(int) $row['order_split_id']."");
        } 
    }
    
    public function getSplitProducts($order_split_id){
        $cart_keys = array();
        $ordes_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_split_product`  WHERE  order_split_id=".(int) $order_split_id."");
        return $ordes_query->rows;
    }
    
    public function getSplitTotals($order_split_id){
        $ordes_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_split_total`  WHERE  order_split_id=".(int) $order_split_id."");
        return $ordes_query->rows;
    }
    
    public function getSplittedorderByid($order_split_id) {
        $ordes_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_split`  WHERE  order_split_id=".(int) $order_split_id."");
        return $ordes_query->row;
    }
    
    public function addProductOrderSplits($details,$split_no){ // change split no to dynamic .. 
        $ordes_query = $this->db->query("INSERT INTO `" . DB_PREFIX . "order_split` SET order_id=".(int)$details['order_id']." , split_id=".(int)$split_no.",split_type='".$details['split_type']."',status=0");
        return $this->db->getLastId();
    }
    
    public function addProducttoSplit($data){
        $this->db->query("INSERT INTO `" . DB_PREFIX . "order_split_product` SET order_split_id=".(int)$data['order_split_id'].",product_id=".(int)$data['product_id']." ,quantity=".(int)$data['quantity']." ,cart_key=".$data['cart_key']."");
        return $this->db->getLastId();
    }
    public function removeProductFromSplit(){
        //UPDATE QUANTITY CHECK   OR DELETE
        //$this->db->query("DELETE FROM `" . DB_PREFIX . "order_split_product` cart_key=".$data['cart_key']."");
    }
     public function updateSplitprintStatus($print_split_id,$split_print_settings) {
        $this->db->query("UPDATE`" . DB_PREFIX . "order_split` SET `print_status`=".(!empty($split_print_settings)?0:1).",print_areas='".  serialize($split_print_settings)."'  WHERE  order_split_id=".(int) $print_split_id."");
    }
    public function checkSplitstoPrinted() {
        $split_query =  $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_split` WHERE print_status=0 ORDER BY `order_split_id` ASC LIMIT 0 , 1");
        if($split_query->num_rows) {
            return $split_query->row;
        }
        else {
            return false;
        }
    }
}

?>