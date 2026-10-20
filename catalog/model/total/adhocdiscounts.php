<?php

class ModelTotalAdhocdiscounts extends Model {

    public function getTotal(&$total_data, &$total, &$taxes) {
        if(isset($this->session->data['adhoc_discounts'])){
            foreach ($this->session->data['adhoc_discounts'] as $discount) {
                $disc_amt = 0;
                if($discount['discount_from_amt']==-2){//group discount
                    if(isset($discount['group_id']) && $discount['group_id']!=0){
                        $sub_total = $this->getGroupproductsTotal($discount['group_id']);
                    }else{
                        $sub_total = $this->cart->getSubTotal();
                    }
                    if ($discount['discount_type'] == 'F') {
                        $disc_amt = $discount['discounted_amt'];
                    } else {
                        $disc_amt = $sub_total / 100 * $discount['discounted_amt'];
                    }
                }else if($discount['discount_from_amt']==-1){//whole cart
                    if ($discount['discount_type'] == 'F') {
                        $disc_amt = $discount['discounted_amt'];
                    } else {
                        $disc_amt = $this->cart->getSubTotal() / 100 * $discount['discounted_amt'];
                    }
                }else{//item discount
                    if ($discount['discount_type'] == 'F') {
                        $disc_amt = $discount['discounted_amt'];
                    } else {
                        $disc_amt = $discount['discount_from_amt'] / 100 * $discount['discounted_amt'];
                    }
                }
                $total_data[] = array(
                        'code' => 'adhocdiscounts',
                        'title' => $discount['discount_name'],
                        'text' => $this->currency->format(-$disc_amt),
                        'value' => -$disc_amt,
                        'sort_order' => $this->config->get('adhocdiscounts_sort_order')
                );
                $total -= $disc_amt;//$discount['discount_amt'];
                  if(isset($this->session->data['order_id'])){
                     $this->db->query("INSERT INTO  " . DB_PREFIX . "pos_client_user_logs SET user_id = '" . (int) $discount['applied_user'] . "', system_id='" . (int)(isset($this->session->data['system_id'])?$this->session->data['system_id']:0)  . "',action='" . $discount['discount_name'].' - Order '.$this->session->data['order_id']. "',time='" . date("Y-m-d H:i:s", time()) . "'");
                 }
            }
        }
       
    }

    public function getAdhocdiscounts() {
        $discounts = $this->db->query("SELECT * FROM " . DB_PREFIX . "pos_discounts pd LEFT JOIN pos_discount_groups gr ON(pd.discount_group=gr.group_id)  ORDER BY discount_id ASC");
        return $discounts->rows;
    }

    public function getDiscount($discount_id) {
        $discounts = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "pos_discounts WHERE discount_id='" . (int) $discount_id . "'");
        return $discounts->row;
    }

     public function getGroupdiscounts() {
        $discounts = $this->db->query("SELECT * FROM " . DB_PREFIX . "pos_discounts pd  WHERE pd.group_discount<>0");
        //print_r($discounts->rows);die();
        return $discounts->rows;
    }

    public function getGroupdiscountproducts($group_id) {
        $prodts = array();
        $qry = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "pos_discount_groups_to_product pd  WHERE pd.group_id=" . $group_id . "");
        // print_r($discounts->rows);die();
        foreach ($qry->rows as $products) {
            $prodts[] = $products['product_id'];
        }
        return $prodts;
    }

    public function getGroupdetails($group_id){
         $qry = $this->db->query("SELECT group_name FROM " . DB_PREFIX . "pos_discount_groups pd  WHERE pd.group_id=" . $group_id . "");
         return $qry->row['group_name'];
    }

    public function applyGroupDiscount($discount_id) {
        $discount_details = $this->getDiscount($discount_id);
        $applied_products = $this->getGroupdiscountproducts($discount_details['discount_group']);
        $group_name = $this->getGroupdetails($discount_details['discount_group']);
        if (!empty($applied_products)) {
            $sub_total = 0;
            foreach ($this->cart->getProducts() as $product) {
                if (in_array($product['product_id'], $applied_products)) {
                    $sub_total += $product['total'];
                }
            }
        } else {
            $sub_total = $this->cart->getSubTotal();
        }
        
        if ($discount_details['discount_type'] == 'F') {
            $discount = $discount_details['discount_amount'];
        } else {
            $discount = $sub_total / 100 * $discount_details['discount_amount'];
        }
        $this->session->data['adhoc_discounts'][] = array(
            'discount_id' => (isset($this->session->data['adhoc_discounts'])) ? (key(array_reverse($this->session->data['adhoc_discounts'], true)) + 1) : 0,
            'discount_name' => $discount_details['discount_name'] .'('.$group_name.')',
            'discount_amt' => $discount,
            'applied_user' => (isset($this->session->data['adhoc_applied_user'])) ? $this->session->data['adhoc_applied_user'] : 0
        );
        unset($this->session->data['adhoc_applied_user']);
    }

    public function getDiscountGroups() {
        $discounts = $this->db->query("SELECT * FROM " . DB_PREFIX . "pos_discount_groups  ORDER BY group_id ASC");
        //print_r($discounts->rows);die();
        return $discounts->rows;
    }
    
     public function getGroupproductsTotal($group_id){
        $applied_products = $this->getGroupdiscountproducts($group_id); 
        if (!empty($applied_products)) {
            $sub_total = 0;
            foreach ($this->cart->getProducts() as $product) {
                if (in_array($product['product_id'], $applied_products)) {
                    $sub_total += $product['total'];
                }
            }
        } else {
            $sub_total = $this->cart->getSubTotal();
        }
        return $sub_total;
    }
}

?>