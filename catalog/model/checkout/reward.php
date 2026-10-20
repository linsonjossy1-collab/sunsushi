<?php
class ModelCheckoutReward extends Model {
    public function earnrewardpointcart() {
	$total = 0;
        $total_rewads = 0;
        $consider_product=false;
        if($this->config->get('reward_points_status')){

            
            $taxes = $this->cart->getTaxes();

            $this->load->model('setting/extension');

            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                }
            }

            $total_cal = $total;

            
            $no_offers = true;
            if(!$this->config->get('config_reward_points_work_offers')){
                foreach($total_data as $total_datacode){
                    if($total_datacode['code'] == 'offers'){
                        $no_offers = false;
                    }
                }
            }

            if($no_offers) {

                if($this->config->get('reward_exclude_product')){                    
                    foreach ($products as $cart_key => $product) {
                        if(in_array($product['product_id'],$this->config->get('reward_exclude_product'))){                            
                            $total_cal -= (float)$product['price'];
                        }
                    }
                }
                if($this->config->get('config_reward_points_exclude_total')){                
                    foreach ($this->config->get('config_reward_points_exclude_total') as $exclude_code){
                        foreach($total_data as $total_datacode){
                            if($total_datacode['code'] == 'reward') continue;    //skip reward total already applied
                            if($total_datacode['code'] == $exclude_code && $total_datacode['value'] >0 ){
                                    $total_cal -= (float)$total_datacode['value'];
                            }
                        }
                    }                    
                } 
                $min_order_amount = $this->config->get('config_reward_points_min_order_amount');
                if( $min_order_amount < $total_cal) {
                    if($this->config->get('config_reward_points_type')=="I") {
                        $consider_product=true;
                    } else {
                        if($this->config->get('config_reward_points_type')=="P") {
                            if($this->config->get('config_reward_point_consider_decimal')) {
                                $total_rewads =  floatval($this->config->get('config_reward_points_points')) *  $total_cal;
                            } else {
                                $total_rewads =  floatval($this->config->get('config_reward_points_points')) * floor($total_cal);
                            } 
                        } else if($this->config->get('config_reward_points_type')=="F") {   
                             if($min_order_amount && $this->config->get('config_repeat_reward') && $total_cal > $min_order_amount*2 ) {
                                $total_rewards = $this->config->get('config_reward_points_points') * (int)($total_cal/$min_order_amount);
                             } else {
                                $total_rewads = $this->config->get('config_reward_points_points');
                             }
                             
                        } else {
                            $multipleorder = $this->config->get('config_reward_point_order_amount');  
                            $repeat = $this->config->get('config_repeat_reward');
                            if($multipleorder) {
                                $min_order_amount = 0;
                                foreach($multipleorder as $multiples) {
                                    if($multiples[0] < $total_cal) {
                                        if($multiples[0] > $min_order_amount) {
                                            $total_rewads = $multiples[1];
                                            $min_order_amount = $multiples[0];
                                        }
                                    } else {
                                        $repeat = false;
                                    }
                                }
                                if($min_order_amount && $repeat && $total_cal > $min_order_amount*2 ) {
                                    $total_rewads = $total_rewads * (int)($total_cal/$min_order_amount);
                                }
                            }
                        }

                        if($this->config->get('config_reward_points_consider_product')) {
                            $consider_product=true;
                        }

                        //echo "reward point : ".$total_rewads."<br>";
                        //add reward point for store
                        $addreward = $this->config->get('config_reward_points_store_fixed');
                        $store_id = $this->config->get('config_store_id');
                        if(isset($addreward[$store_id])) {
                        if($addreward[$store_id]) {
                            $total_rewads+=$addreward[$store_id];
                        } else {
                            $addreward = $this->config->get('config_reward_points_store_xtimes');
                            if($addreward[$store_id]) {
                                $total_rewads*=$addreward[$store_id];
                            }
                        }
                        }

                        //echo "store point : ".$total_rewads."<br>";
                        //add reward point for shipping method
                        if (isset($this->session->data['shipping_method']['code'])) {
                            $data['shipping_code'] = $this->session->data['shipping_method']['code'];
                            $addreward = $this->config->get('config_reward_points_shipping_methode_fixed');
                            $sel_times_reward = explode('.',$data['shipping_code']);
                            if(isset($addreward[$sel_times_reward[0]])) {
                            if($addreward[$sel_times_reward[0]]) {
                                $total_rewads+=$addreward[$sel_times_reward[0]];
                            } else {
                                $addreward = $this->config->get('config_reward_points_shipping_methode_xtimes');
                                if($addreward[$sel_times_reward[0]]) {
                                    $total_rewads*=$addreward[$sel_times_reward[0]];
                                }
                            }
                            }
                        } else {
                            $data['shipping_code'] = '';
                        }
                        //echo "shipping point : ".$total_rewads."<br>";
                        //add reward point for payment method
                        if (isset($this->session->data['payment_method']['code'])) {
                            $data['payment_code'] = $this->session->data['payment_method']['code'];
                            $sel_times_reward = explode('.',$data['payment_code']);
                            $addreward = $this->config->get('config_reward_points_payment_methode_fixed');
                            if(isset($addreward[$sel_times_reward[0]])) {
                            if($addreward[$sel_times_reward[0]]) {
                                $total_rewads+=$addreward[$sel_times_reward[0]];
                            } else {
                                $addreward = $this->config->get('config_reward_points_payment_methode_xtimes');
                                if($addreward[$sel_times_reward[0]]) {
                                    $total_rewads*=$addreward[$sel_times_reward[0]];
                                }
                            }
                            }
                        } else {
                            $data['payment_code'] = '';
                        }                        
                        //echo "payment point : ".$total_rewads."<br>";
                    }
                }

                if($consider_product) {
                    foreach ($this->cart->getcartProducts() as $product) {
                        $total_rewads+= $product['reward'];
                    }
                }

            }
        }
        return $total_rewads;
    }
    public function showredeempoints() {
        $json = [];
	$total = 0;
        $total_data ;
        $points_total = 0;
        $this->load->model('account/order');
        if(!isset($this->session->data['customer_id'])) {
            $ordercount = 0;
            $points = 0;
        } else {
            $ordercount = $this->model_account_order->getOrderCount($this->session->data['customer_id']);
            $points = $this->customer->getRewardPoints();
        }
        if($points && $this->config->get('reward_points_status')) { // IF points 
            $offerexist = $this->checkoffers($total,$total_data); // to check offers & get total
            if($this->config->get('config_redeem_points_work_offers') || !$offerexist ) {  // WORK with oFFER OR NO offers
                if($this->config->get('config_redeem_amount') ) {
                    if(!$this->config->get('config_redeem_points_min_activate') || $points >= $this->config->get('config_redeem_points_min_activate')) { // 0 OR Minimum point
                        if(!$this->config->get('config_min_reward_order') ||  $this->config->get('config_min_reward_order') <= $ordercount) { // 0 or Minimum orders
                            if($this->config->get('redeem_exclude_product')){                    
                                foreach ($products as $cart_key => $product) {
                                    if(in_array($product['product_id'],$this->config->get('redeem_exclude_product'))){                            
                                        $total -= (float)$product['price'];
                                    }
                                }
                            }
                            if($this->config->get('config_redeem_points_exclude_total')){                
                                foreach ($this->config->get('config_redeem_points_exclude_total') as $exclude_code){
                                    foreach($total_data as $total_datacode){
                                        if($total_datacode['code'] == 'reward') continue;    //skip reward total already applied
                                        if($total_datacode['code'] == $exclude_code && $total_datacode['value'] >0 ){
                                                $total -= (float)$total_datacode['value'];
                                        }
                                    }
                                }                    
                            }
                            if($points >= $this->config->get('config_redeem_points_min_points') && $total >= $this->config->get('config_redeem_points_min_order_amount')){
                                $json['reward_status'] = true;
                                $json['label_reward'] = $this->language->get('label_reward');
                                $json['reward_pointstatus'] = $this->config->get('config_redeem_amount');

                                $max_point = ($this->config->get('config_redeem_points_max_points')) ? $this->config->get('config_redeem_points_max_points') : $points;

                                if($points < $max_point){
                                    $max_point =  $points;
                                }

                                // $this->session->data['toal_without_reward'] = $total - $this->config->get('config_redeem_points_min_order_amount');
                               

                                // $points_total = floor( $this->session->data['toal_without_reward'] * ($this->config->get('config_redeem_points_points'))); 
                                $points_total = floor( $total * ($this->config->get('config_redeem_points_points')));   
                                if($points_total < $max_point){
                                    $max_point =  $points_total;
                                }  
                                // $this->session->data['max_point'] = $max_point;  
                                $json['max_point'] = $max_point;
                                $json['entry_reward'] =  sprintf($this->language->get('text_max_reward_point') , $points , $max_point) ;
                 
                                /*

                                if(isset($this->session->data['reward'])) {
                                    if($this->config->get('config_redeem_points_points')) {
                                        $this->session->data['toal_without_reward']+= $this->session->data['reward_point']/ $this->config->get('config_redeem_points_points');
                                    }
                                    if(isset($this->session->data['reward_free_products'])) {
                                        foreach ($this->cart->getProducts() as $product) {
                                            if(isset($this->session->data['reward_free_products'][$product['product_id']])) {
                                                $this->session->data['toal_without_reward']+= $product['price'] * $this->session->data['reward_free_products'][$product['product_id']]['qty'];
                                            }                                
                                        }
                                    }

                                    if($points > $this->session->data['reward'] + $max_point) {
                                        $max_point = $this->session->data['reward'] + $max_point;
                                    }
                                    $json['entry_reward'] =  sprintf($this->language->get('text_max_reward_point') , $points , $max_point) ;
                                } else {
                                    $json['entry_reward'] =  sprintf($this->language->get('text_max_reward_point') , $points , $max_point) ;
                                }

                                $json['total_without_reward'] = $this->session->data['toal_without_reward'];               
                                */

                                 $json['reward_total_points'] = $points;

                            }
                            
                            
                        }
                        
                        
                    }
                   
                }
                if($this->config->get('config_redeem_free_product') ) { 
                    $redeem_freeproducts = $this->redeemfreeproducts($points);

                    if(!empty($redeem_freeproducts)) {
                        $json['redeem_freeproducts'] = []; //  $redeem_freeproducts;
                        $json['redeemproducts'] = isset($this->session->data['reward_free_products'])?$this->session->data['reward_free_products']: array();
                        $json['reward_status'] = true;
                        $json['reward_total_points'] = $points;
                        foreach($redeem_freeproducts as $redeem_freeproduct) {
                            if(isset($json['redeemproducts'][$redeem_freeproduct['product_id']])){
                                $rwselectedqty = $json['redeemproducts'][$redeem_freeproduct['product_id']]['qty'];
                            }else {
                                $rwselectedqty = 0;
                            }
                            for($i=1; $i<=((int)$redeem_freeproduct['cart_qty']);$i++){
                                if($i <= $rwselectedqty) {
                                    $redeem_freeproduct['selected'] = 1;
                                }else {
                                    $redeem_freeproduct['selected'] = 0;
                                }
                                $json['redeem_freeproducts'][] = $redeem_freeproduct;
                            }
                        }
                    }
                }
                
            }
            
        }
        if(!$json) {
            $json['reward_status'] = false;
        }
        return $json;
    }
    public function redeemfreeproducts($points) {
        $redeem_freeproducts  = array();
        if($this->config->get('config_redeem_free_product')) {
            $this->load->model('catalog/product');
            $cart_product = array();
            foreach($this->cart->getProducts() as $cartproduct) {
                $cart_product[$cartproduct['product_id']] = array('name' => $cartproduct['name'], 'price' => $cartproduct['price'], 'quantity' => $cartproduct['quantity']);
                if(isset($this->session->data['reward_free_products'][$cartproduct['product_id']]) && $this->session->data['reward_free_products'][$cartproduct['product_id']]['qty']>$cartproduct['quantity']) {
                    $this->session->data['reward_free_products'][$cartproduct['product_id']]['qty'] =  $cartproduct['quantity'];
                }
            }
            $max_free = $this->config->get('config_free_product_multiple');
            foreach($this->config->get('config_free_products') as $freegroup) {
                if($freegroup['amount'] <= $points) {
                    foreach($freegroup['free_products'] as $product) {
                        if(isset($cart_product[$product])) {                           
                            if($max_free && $max_free < $cart_product[$product]['quantity']) {
                                $cart_product[$product]['quantity'] = $max_free;
                            }                 
                            $redeem_freeproducts[] = array(
                                'amount' => $freegroup['amount'],
                                'product_id' => $product,
                                'name' => $cart_product[$product]['name'],
                                'price' =>$cart_product[$product]['price'],
                                'cart_qty' =>$cart_product[$product]['quantity'],
                            ); 
                        } else {
                            if (isset($this->session->data['reward_free_products'][$product])) {
                                unset($this->session->data['reward_free_products'][$product]);
                            }
                            $product_info = $this->model_catalog_product->getProduct($product);
                            $options = array();
                            foreach ($this->model_catalog_product->getProductOptions($product) as $option) {
                                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                                    $option_value_data = array();

                                    foreach ($option['option_value'] as $option_value) {
                                        if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {

                                            $option_value_data[] = array(
                                                'product_option_value_id' => $option_value['product_option_value_id'],
                                                'option_value_id' => $option_value['option_value_id'],
                                                'name' => $option_value['name']
                                            );
                                        }
                                    }                

                                    $options[] = array(
                                        'product_option_id' => $option['product_option_id'],
                                        'option_id' => $option['option_id'],
                                        'name' => $option['name'],
                                        'type' => $option['type'],
                                        'option_value' => $option_value_data,
                                        'required' => $option['required']
                                    );
                                } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                                    $options[] = array(
                                        'product_option_id' => $option['product_option_id'],
                                        'option_id' => $option['option_id'],
                                        'name' => $option['name'],
                                        'type' => $option['type'],
                                        'option_value' => $option['option_value'],
                                        'required' => $option['required']
                                    );
                                }
                            }

                            $redeem_freeproducts[] = array(
                                'amount' => $freegroup['amount'],
                                'product_id' => $product,
                                'name' => $product_info['name'],
                                'price' =>$product_info['price'],
                                'product_options' =>$options,
                                'cart_qty' =>0,
                            ); 
                        }
                    }
                }
               
            }
        }
   
        /* else {
            // $this->session->data['reward_free_products'] = array();
            return false;
        }
         * 
         */
        return $redeem_freeproducts;
        
    }
    public function removefreeproduct() {    
        $success = 0;    
        if(isset($this->session->data['reward_free_products'][$this->request->post['product']])) {
            if($this->session->data['reward_free_products'][$this->request->post['product']]['qty']>1) {
                $this->session->data['reward_free_products'][$this->request->post['product']]['qty']--; 
                $success = 1;
            } else {
                unset($this->session->data['reward_free_products'][$this->request->post['product']]);
                $success = 0;
            }
        }
       
        return $success;
    }
    public function validateReward() {
        $json = array();
        if(!$this->config->get('reward_points_status')) {
            $json['error']['warning'] = 'Reward point disabled.';
        } else if(!isset($this->session->data['customer_id'])) {
            $json['error']['warning'] = 'Please login to use reward point';
        }  else {
            unset($this->session->data['reward_free_products']);
            unset($this->session->data['reward']);
            unset($this->session->data['reward_point']);
            
            $this->load->model('account/order');      
            $ordercount = $this->model_account_order->getOrderCount($this->session->data['customer_id']);
            if((int)$this->config->get('config_min_reward_order') <= $ordercount) {
                $points = $this->customer->getRewardPoints(); 
                if($points) {
                    $customer_points = $points;
                    // Redeem free products
                    if($this->config->get('config_redeem_free_product') && isset($this->request->post['redeem_products']) ) {
                        //print_r(count($this->request->post['redeem_products'])); die();

                        if($this->config->get('config_free_product_multiple')==0 || count($this->request->post['redeem_products']) <= $this->config->get('config_free_product_multiple')) {
                            foreach($this->cart->getProducts() as $product) {
                               $cart_product[$product['product_id']] = array('quantity' => $product['quantity'], 'price' => $product['price']);
                            }

                            foreach($this->config->get('config_free_products') as $freegroup) {
                                if($freegroup['amount'] <= $points) {
                                    foreach($freegroup['free_products'] as $product) {
                                        if(isset($cart_product[$product])) {
                                            $cart_product[$product]['point'] = $freegroup['amount'];
                                        }
                                    }
                                }
                            }
                            foreach($this->request->post['redeem_products'] as $key => $product) {
                                if(isset($cart_product[$product['product_id']]) && $cart_product[$product['product_id']]['point'] ) { // FREE AND IN CART
                                    if($points >= $cart_product[$product['product_id']]['point'] ) { // P
                                        $points -= (int)$cart_product[$product['product_id']]['point']; // POINT MINUS
                                        $cart_product[$product['product_id']]['quantity'] -= 1; // REDEEMED QTY MINIS
                                        if(!$cart_product[$product['product_id']]['quantity']) { // IF NO MORE ITEM UNSET THAT ITEM
                                            unset($cart_product[$product['product_id']]); 
                                        }
                                        if(!isset($this->session->data['reward_free_products'][$product['product_id']])) {
                                            $this->session->data['reward_free_products'][$product['product_id']]= array('qty'=>1,'amount'=>$product['amount']);
                                        } else {
                                            $this->session->data['reward_free_products'][$product['product_id']]['qty']++; 
                                        }
                                    }
                                }
                            }
                        }else {
                            $json['error']['warning'] = 'Maximum no.of free product is '.$max_free;
                        }

                    }else {
                        unset($this->session->data['reward_free_products']);
                    }
                    // Redeem points
                    if($this->config->get('config_redeem_amount') && isset($this->request->post['reward']) && $this->request->post['reward'] && $points) {
                        if($points >= $this->config->get('config_redeem_points_min_activate')) {
                            if(($points < $this->request->post['reward'])){
                                $this->request->post['reward'] = $points;
                            }
                            $redeem_point = (int)$this->request->post['reward'];
                            if($this->config->get('config_redeem_points_min_points')==0 || $redeem_point >= $this->config->get('config_redeem_points_min_points')){
                                
                                if($this->config->get('config_redeem_points_max_points')==0 || $redeem_point <= $this->config->get('config_redeem_points_max_points')){
                                    $this->session->data['reward_point'] = $this->request->post['reward']; // reward point to show on textbox
                                }
                                else {
                                     $json['error']['warning'] = 'Maximum reward point that can be applied is '.$this->config->get('config_redeem_points_max_points');
                                }
                            }
                            else {
                                $json['error']['warning'] = "The Minimum number of points that can be applied is " .$this->config->get('config_redeem_points_min_points').' now is '.$redeem_point;
                            }
                        }
                        else {
                            $json['error']['warning'] = "You don't have ".$this->config->get('config_redeem_points_min_activate')." points to enable reward point";
                        }
                    }else {
                        unset($this->session->data['reward_point']);
                    }
                    
            
                } else {
                    $json['error']['warning'] = "You have no points to redeem";
                }
            }else {
                $json['error']['warning'] = 'Minimum no.of order is '.$ordercount;
            }
        }
        if(!isset($json['error']['warning'])) {          
            $json['points'] = ($customer_points - $points + $this->request->post['reward']) ; 
            $this->session->data['reward'] = $this->session->data['reward_point'] ;
            $this->language->load('checkout/cart');
            $json['success'] = $this->language->get('text_reward');
        }
        return $json;
    }
    private function checkoffers(&$total ,&$total_data) {
        $total_data = array();
        $total = 0 ;
        $taxes = $this->cart->getTaxes();

        $this->load->model('setting/extension');
        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('total/' . $result['code']);

                $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
            }
        }

        $offers = false;
        if(!$this->config->get('config_redeem_points_work_offers')){
            foreach($total_data as $total_datacode){
                if($total_datacode['code'] == 'offers'){
                    $offers = true;
                    break;
                }
            }
        }
        return $offers;
    }
}
?>