<?php
class ControllerEpos2Epos extends Controller {
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
        $json = array('epos'=>'epos');
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    public function Order() {
        $this->log->write(print_r(array('order push'),true));
        if (isset($this->request->post)){
            $reppusstatus = false;
            if(!isset($this->request->post['orderedit']) && !isset($this->request->post['paychange'])){
                $push_qry = $this->db->query("SELECT push_id,method FROM `" . DB_PREFIX . "epos_push_history` WHERE push_id='".(int)$this->request->post['pushid']."' AND tk_id='".(int)$this->request->post['tkid']."' AND method='order'");
                if(!empty($push_qry->rows)){
                    $this->log->write(print_r(array('order repeatpush'),true));
                    $reppusstatus = true;                                        
                }
            }       
            if(!$reppusstatus){
            $this->load->model('account/customer');
            $this->load->model('account/address');
            $this->load->model('epos/order');
            $this->load->model('checkout/order');
            $this->load->model('epos/epos_history');     
            $this->log->write(print_r(array('ORDER PUSH'),true));
            $data = array();
            //$data['order_id']=$this->request->post['order_id'];
            if(isset($this->request->post['order_id'])){
                $data['order_id']=$this->request->post['order_id'];
            }else{
                if(isset($this->request->post['pushid'])){
                    if($this->config->get('config_order_id_sequence')){
                        $display_order_id = $this->getTodaysLastOrderSequence()+1;
                    }
                    if($this->config->get('config_order_id_unixtimestap')) {
                        $order_id =  $this->unixtime();
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET order_id=" . $order_id.",`order_status_id`=0 ");
                    }
                    else {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET `order_status_id`=0 ");
                        $order_id = $this->db->getLastId();
                    }
                    if(!isset($display_order_id)){
                        $display_order_id = ($this->config->get('config_order_id_unixtimestap'))?substr($order_id, -5):$order_id;
                    }
                    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET display_id='".$display_order_id."'  WHERE order_id=".$order_id."");
                    $data['order_id']=$order_id;
                }
            }
            $this->log->write(print_r(array('ORDER PUSH'=>$data['order_id']),true));
            $data['store_id'] = (isset($this->request->post['store_id']))?$this->request->post['store_id']:  $this->config->get('pos_to_storeid');
            $data['live_order_id'] =  $this->request->post['live_order_id'];
            $data['store_url']='';
            if(isset($this->request->post['customer'])){//No  Walkin
                //$customerdata = (isset($this->request->post['order_id']))?json_decode(html_entity_decode($this->request->post['customer']),true):$this->request->post['customer'];
                if(!is_array($this->request->post['customer'])){
                    $customerdata = json_decode(html_entity_decode($this->request->post['customer']),true);
                }else{
                    $customerdata = $this->request->post['customer'];
                }
                if(isset($customerdata['firstname']) || isset($customerdata['lastname'])){
                if(isset($customerdata['type'])){
                    switch ($customerdata['type']) {
                        case 'new':    //New Customer
                            //firstname , lastname, email , telephone, fax, password , 
                            //company , address_1 , address_2, city, postcode, country_id, zone_id
                            $customerdata['address_1']=$customerdata['shipping_address_1'];
                            $customerdata['address_2']=$customerdata['shipping_address_2'];
                            $customerdata['postcode']=$customerdata['shipping_postcode'];
                            $data['customer_id'] = $this->model_account_customer->addCustomer($customerdata);
                            break;
                        case 'edit':    //Customer Data Edit    //customer_id
                             $this->model_account_customer->editCustomer($customerdata);
                            break;
                    }
                }
                if(isset($customerdata['address_type'])){ 
                            $address_data = array(
                                'customer_id'=>$customerdata['customer_id'],
                                'firstname'=>$customerdata['shipping_firstname'],
                                'lastname'=>$customerdata['shipping_lastname'],
                                'address_1'=>$customerdata['shipping_address_1'],
                                'address_2'=>$customerdata['shipping_address_2'],
                                'city'=>$customerdata['shipping_city'],
                                'postcode'=>$customerdata['shipping_postcode']
                            );
                    switch ($customerdata['address_type']){
                        case 'new':    //New Address
                            //customer_id ,firstname, lastname,address_1,address_2,postcode,city,
                            $this->model_account_address->addAddress($address_data); 
                            break;
                        case 'edit':    //Customer Address Edit
                            $this->model_account_address->editAddress($customerdata['shipping_address_id'],$address_data); 
                            break;
                    }
                }
                $data +=$customerdata;
                $data['payment_firstname']=(isset($customerdata['firstname']))?$customerdata['firstname']:'';
                $data['payment_lastname']=(isset($customerdata['lastname']))?$customerdata['lastname']:'';
                $data['payment_address_1']=(isset($customerdata['shipping_address_1']))?$customerdata['shipping_address_1']:'';
                $data['payment_address_2']=(isset($customerdata['shipping_address_2']))?$customerdata['shipping_address_2']:'';
                $data['payment_city']=(isset($customerdata['shipping_city']))?$customerdata['shipping_city']:'';
                $data['payment_postcode']=(isset($customerdata['shipping_postcode']))?$customerdata['shipping_postcode']:'';
                }else{
                    $data['customer_id']=$this->config->get('pos_walkin_customer_id');
                    $data['shipping_firstname']='Takeout';
                    $data['shipping_lastname']='';
                    $data['shipping_address_1']='';
                    $data['shipping_address_2']='';
                    $data['shipping_city']='';
                    $data['shipping_postcode']='';
                    $data['payment_firstname']='Takeout';
                    $data['payment_lastname']='';
                    $data['payment_address_1']='';
                    $data['payment_address_2']='';
                    $data['payment_city']='';
                    $data['payment_postcode']='';
                }                                
            }else{//Takeout Customer
                $data['customer_id']=$this->config->get('pos_walkin_customer_id');
                $data['shipping_firstname']='Takeout';
                $data['shipping_lastname']='';
                $data['shipping_address_1']='';
                $data['shipping_address_2']='';
                $data['shipping_city']='';
                $data['shipping_postcode']='';
                $data['payment_firstname']='Takeout';
                $data['payment_lastname']='';
                $data['payment_address_1']='';
                $data['payment_address_2']='';
                $data['payment_city']='';
                $data['payment_postcode']='';
            }
            //$shippingdata =  (isset($this->request->post['order_id']))?json_decode(html_entity_decode($this->request->post['shipping_method']),true):$this->request->post['shipping_method'];
            if(!is_array($this->request->post['shipping_method'])){
                $shippingdata = json_decode(html_entity_decode($this->request->post['shipping_method']),true);
            }else{
                $shippingdata = $this->request->post['shipping_method'];
            }
            //$paymentdata =   (isset($this->request->post['order_id']))?json_decode(html_entity_decode($this->request->post['payment_method']),true):$this->request->post['payment_method'];
            $paymentdata = array();
            if(!isset($this->request->post['eposonlinepay'])){
            if(!is_array($this->request->post['payment_method'])){
                $paymentdata = json_decode(html_entity_decode($this->request->post['payment_method']),true);
            }else{
                $paymentdata = $this->request->post['payment_method'];
            }
            }
           
            //if(isset($this->request->post['prevpayments']) && isset($this->request->post['orderedit'])){
             //$paymentdata = (!is_array($this->request->post['prevpayments']))?json_decode(html_entity_decode($this->request->post['prevpayments']),true):$this->request->post['prevpayments'];
            //}
            $data['shipping_method']=$shippingdata['name'];
            $data['shipping_code']=$shippingdata['code'].'.'.$shippingdata['code'];
            $data['payment_method']=(isset($paymentdata['name']))?$paymentdata['name']:'';
            $data['payment_code']=(isset($paymentdata['code']))?$paymentdata['code']:'';
            $data['customer_group_id'] =(isset($this->request->post['customer_group_id'])?$this->request->post['customer_group_id']:$this->config->get('config_customer_group_id')); //
            $data['order_status_id'] =(isset($this->request->post['order_status_id'])?$this->request->post['order_status_id']:$this->config->get('config_order_status_id')); //
            $data['user_agent']=(isset($this->request->post['user_agent'])?$this->request->post['user_agent']:'EPOS'); //'';
            $data['date_added']=(isset($this->request->post['date_added'])?trim(str_replace('"', '', html_entity_decode($this->request->post['date_added']))):date('Y-m-d H:i:s',time())); //'';
            $data['date_modified']=date('Y-m-d H:i:s',time());
            $data['order_requested_time']=isset($this->request->post['requested_time'])?$this->request->post['requested_time']:time();
            //$products = (isset($this->request->post['order_id']))?json_decode(html_entity_decode($this->request->post['items']),true):$this->request->post['items'];
            if(!is_array($this->request->post['items'])){
                $products = json_decode(html_entity_decode($this->request->post['items']),true);
            }else{
                $products = $this->request->post['items'];
            }
            $data['system_id']=(isset($this->request->post['system_id'])?$this->request->post['system_id']:0);
            $data['user_id']=(isset($this->request->post['user_id'])?$this->request->post['user_id']:0);
            $data['waiter_id']=(isset($this->request->post['waiter_id'])?$this->request->post['waiter_id']:0);
            $data['language_id'] = $this->config->get('config_language_id');
            $data['currency_id'] = $this->currency->getId();
            $data['currency_code'] = $this->currency->getCode();
            $data['currency_value'] = $this->currency->getValue($this->currency->getCode());
            $productdata = array();
            foreach ($products as $product) {
                if(isset($product['bundle_data']) && !empty($product['bundle_data'])){
                    continue;
                }
               
                $option_data = array();
                if($this->config->get('adhocproduct_product_id') && $this->config->get('adhocproduct_product_id')==$product['product_id']){
                    $this->model_epos_epos_history->insertahocdata(array('product_name'=>$product['name'],'price'=>$product['price_value']));
                }
               
                foreach ($product['option'] as $option) {
                    if(!isset($option['type'])){
                         foreach ($option as $checkbox) {
                            $option_data[] = array(
                                'product_option_id' => $checkbox['product_option_id'],
                                'product_option_value_id' => $checkbox['product_option_value_id'],
                                'option_id' => $checkbox['option_id'],
                                'option_value_id' => $checkbox['option_value_id'],
                                'name' => $checkbox['option_name'],
                                'shortcode'=>$checkbox['shortcode'],
                                'value' => $checkbox['name'],
                                'type' => $checkbox['type'],
                                'group'=> $checkbox['group']
                            );
                         }
                    }else{
                        if($option['type']=='text' || $option['type']=='textarea'){//INsert Comment History                           
                            $this->model_epos_epos_history->insertprodtcommentdata(array('comment'=>$option['name'],'product_id'=>$product['product_id'],'customer_id'=>(isset($data['customer_id']) && $data['customer_id']!=$this->config->get('pos_walkin_customer_id'))?$data['customer_id']:0));
                        }
                        $option_data[] = array(
                            'product_option_id' => $option['product_option_id'],
                            'product_option_value_id' => $option['product_option_value_id'],
                            'option_id' => (isset($option['option_id']))?$option['option_id']:1000,
                            'option_value_id' => (isset($option['option_value_id']))?$option['option_value_id']:100000,
                            'name' => $option['option_name'],
                            'shortcode'=>$option['shortcode'],
                            'value' => $option['name'],
                            'type' => $option['type'],
                            'group'=> $option['group']
                        );
                    }
                    
                }                              
                $topping_data =array();
                if(isset($product['toppings'])){
                    foreach ($product['toppings'] as $topping_id=>$topping_values) {
                        foreach ($topping_values as $topping_value) {
                            $topping_data[]=array(
                                'product_topping_id'=>$topping_id,
                                'product_topping_value_id'=>$topping_value['topping_value_id'],
                                'name'=>(isset($topping_value['btn']) && $topping_value['btn']!='')?'':$topping_value['toppingname'],
                                'topping_value'=>((isset($topping_value['btn']) && $topping_value['btn']!='')?$topping_value['btn']:'').' '.$topping_value['name'],
                                'price'=>$topping_value['price'],
                                'quantity'=>$topping_value['qty'],
                                'shortcode'=>((isset($topping_value['btnshort']))?$topping_value['btnshort']:'').' '.$topping_value['shortcode']
                            );
                        }
                    }
                }
                $dressing_data= array();
                if(isset($product['dressings'])){
                    foreach ($product['dressings'] as $dressing_id=>$dressing_values) {
                        foreach ($dressing_values as $dressing_value) {
                            $dressing_data[]=array(
                                'product_dressing_id'=>$dressing_id,
                                'product_dressing_value_id'=>$dressing_value['dressing_value_id'],
                                'name'=>(isset($dressing_value['btn']))?'':$dressing_value['dressingname'],
                                'dressing_value'=>((isset($dressing_value['btn']))?$dressing_value['btn']:'').' '.$dressing_value['name'],
                                'shortcode'=>((isset($dressing_value['btnshort']))?$dressing_value['btnshort']:'').' '.$dressing_value['shortcode']
                            );
                        }
                    }
                }
                //Bundled End
                $productdata[]=array(
                    'product_id' => $product['product_id'],
                    'cart_key' => $product['item_index'],
                    'name' => $product['name'],
                    'model' => '',
                    'option' => $option_data,
                    'toppings'=>array(),
                    'download' => array(),
                    'quantity' => $product['qty'],
                    'subtract' => '',
                    'price' => $product['price_value'],
                    'total' => $product['total_value'],
                    'bundle'=> array(),
                    'tax'=>'',
                    'reward'=>'',
                    'toppings'=>$topping_data,
                    'dressings'=>$dressing_data,
                    'main_product_id'=>(isset($product['orgproductid']))?$product['orgproductid']:0
                );
            }
            foreach ($products as $product) {
                if(isset($product['bundle_data']) && !empty($product['bundle_data'])){
                    $productbundle=array();                   
                    foreach ($product['bundle_data'] as $bundle_id => $bundledata){
                        foreach ($bundledata as $bundle){
                            $productbundle[$bundle['item_index']] = array($bundle_id=>$bundle['qty']);
                        }
                    }
                    $option_data = array();
                    foreach ($product['option'] as $option) {
                    if(!isset($option['type'])){
                         foreach ($option as $checkbox) {
                            $option_data[] = array(
                                'product_option_id' => $checkbox['product_option_id'],
                                'product_option_value_id' => $checkbox['product_option_value_id'],
                                'option_id' => $checkbox['option_id'],
                                'option_value_id' => $checkbox['option_value_id'],
                                'name' => $checkbox['option_name'],
                                'shortcode'=>$checkbox['shortcode'],
                                'value' => $checkbox['name'],
                                'type' => $checkbox['type'],
                                'group'=> $checkbox['group']
                            );
                         }
                    }else{
                        if($option['type']=='text' || $option['type']=='textarea'){//INsert Comment History                           
                            $this->model_epos_epos_history->insertprodtcommentdata(array('comment'=>$option['name'],'product_id'=>$product['product_id'],'customer_id'=>(isset($data['customer_id']) && $data['customer_id']!=$this->config->get('pos_walkin_customer_id'))?$data['customer_id']:0));
                        }
                        $option_data[] = array(
                            'product_option_id' => $option['product_option_id'],
                            'product_option_value_id' => $option['product_option_value_id'],
                            'option_id' => $option['option_id'],
                            'option_value_id' => $option['option_value_id'],
                            'name' => $option['option_name'],
                            'shortcode'=>$option['shortcode'],
                            'value' => $option['name'],
                            'type' => $option['type'],
                            'group'=> $option['group']
                        );
                    }
                    
                }               
                    $topping_data =array();
                    if(isset($product['toppings'])){
                        foreach ($product['toppings'] as $topping_id=>$topping_values) {
                            foreach ($topping_values as $topping_value) {
                                $topping_data[]=array(
                                    'product_topping_id'=>$topping_id,
                                    'product_topping_value_id'=>$topping_value['topping_value_id'],
                                    'name'=>(isset($topping_value['btn']) && $topping_value['btn']!='')?'':$topping_value['toppingname'],
                                    'topping_value'=>((isset($topping_value['btn']) && $topping_value['btn']!='')?$topping_value['btn']:'').' '.$topping_value['name'],
                                    'price'=>$topping_value['price'],
                                    'quantity'=>$topping_value['qty'],
                                    'shortcode'=>((isset($topping_value['btnshort']))?$topping_value['btnshort']:'').' '.$topping_value['shortcode']
                                );
                            }
                        }
                    }
                    $dressing_data= array();
                    if(isset($product['dressings'])){
                        foreach ($product['dressings'] as $dressing_id=>$dressing_values) {
                            foreach ($dressing_values as $dressing_value) {
                                $dressing_data[]=array(
                                    'product_dressing_id'=>$dressing_id,
                                    'product_dressing_value_id'=>$dressing_value['dressing_value_id'],
                                    'name'=>(isset($dressing_value['btn']))?'':$dressing_value['dressingname'],
                                    'dressing_value'=>((isset($dressing_value['btn']))?$dressing_value['btn']:'').' '.$dressing_value['name'],
                                    'shortcode'=>((isset($dressing_value['btnshort']))?$dressing_value['btnshort']:'').' '.$dressing_value['shortcode']
                                );
                            }
                        }
                    }
                    //Bundled End
                    $productdata[]=array(
                        'product_id' => $product['product_id'],
                        'cart_key' => $product['item_index'],
                        'name' => $product['name'],
                        'model' => '',
                        'option' => $option_data,
                        'toppings'=>array(),
                        'download' => array(),
                        'quantity' => $product['qty'],
                        'subtract' => '',
                        'price' => $product['price_value'],
                        'total' => $product['total_value'],
                        'bundle'=> $productbundle,
                        'tax'=>'',
                        'reward'=>'',
                        'toppings'=>$topping_data,
                        'dressings'=>$dressing_data,
                        'main_product_id'=>(isset($product['orgproductid']))?$product['orgproductid']:0
                    );
                }
                
            }
           
           
            $data['products']=$productdata;
            $data['vouchers']=array();
            //totals
            $ordercomment='';
            $data['ordercomments']=array();
            if(isset($this->request->post['comment'])){
                $order_comments = (!is_array($this->request->post['comment']))?json_decode(html_entity_decode($this->request->post['comment'])):$this->request->post['comment'];
                foreach($order_comments as $scommt){
                    $data['ordercomments'][]=array('comment'=>$scommt,'is_profile'=>0);
                    $ordercomment .=$scommt.'.';
                };
            }
            if(isset($this->request->post['customer_comments'])){
                $order_comments = (!is_array($this->request->post['customer_comments']))?json_decode(html_entity_decode($this->request->post['customer_comments'])):$this->request->post['customer_comments'];
                foreach($order_comments as $scommt){
                    $data['ordercomments'][]=array('comment'=>$scommt,'is_profile'=>1);
                    $ordercomment .=$scommt.'.';
                };
            }
            $data['comment']=$ordercomment;//(isset($this->request->post['comment']))?str_replace(array('\n','"'),' ', html_entity_decode($this->request->post['comment'])):'';
            $data['peoples']=(isset($this->request->post['peoples'])?$this->request->post['peoples']:0);
            // $this->log->write(print_r($this->request->post['totals'],true));
            $totaldata = (!is_array($this->request->post['totals']))?json_decode(html_entity_decode($this->request->post['totals']),true):$this->request->post['totals'];
            $totals = array();
            foreach ($totaldata as $code=>$total) {
                foreach($total as $value){
                    if($code=='shipping' && $data['shipping_code']=='home.home'){
                        $data['shipping_charge'] = $value['value'];
                    }
                    $totals[]=array(
                        'code'=>$code,
                        'title'=>$value['name'],
                        'text'=>$value['value'],
                        'value'=>$value['value'],
                        'sort_order'=>($this->config->get($code . '_sort_order'))?$this->config->get($code . '_sort_order'):0
                    );
                }
            }
            $data['totals']=$totals;
            $data['total']=$totaldata['total'][0]['value'];
            if(isset($this->request->post['splits'])){
                 $data['splits'] = (isset($this->request->post['order_id']))?json_decode(html_entity_decode($this->request->post['splits']),true):$this->request->post['splits'];
            }
            if(!isset($this->request->post['store_name']) || $this->request->post['store_name']==''){
                $data['store_name']='EPOS';
            }else{
                $data['store_name'] = trim(str_replace('"', '', html_entity_decode($this->request->post['store_name']))); 
            }
            if(isset($this->request->post['orderedit'])){
                $data['order_from']=0;
            }else{
                $data['order_from']=2;
            }
            if(isset($this->request->post['paychange'])){
                $data['cod_pay']=trim(str_replace('"', '', html_entity_decode($this->request->post['paychange']))).'- Paid'; 
            }
            if(isset($this->request->post['change_due'])){
                $data['change_due'] = $this->request->post['change_due'];
            }else{
                $data['change_due'] = 0;
            }
            if(isset($this->request->post['payed_amts'])){
                $data['payment_history'] = (!is_array($this->request->post['payed_amts']))?json_decode(html_entity_decode($this->request->post['payed_amts']),true):$this->request->post['payed_amts'];
            }else{
                $data['payment_history']=array();
            }
            if(isset($this->request->post['previous_paymemts'])){
                $data['previous_paymentdata'] = (!is_array($this->request->post['previous_paymemts']))?json_decode(html_entity_decode($this->request->post['previous_paymemts']),true):$this->request->post['previous_paymemts'];
            }
            if(isset($this->request->post['dinein_table_id'])){
                $data['dinein_table_id']=$this->request->post['dinein_table_id'];
            }
            //$this->log->write(print_r($data,true));
           
            if(isset($data['liveorderdata'])){
                //$this->log->write(print_r($data['liveorderdata'],true));        
                $this->log->write(print_r(array('DELETE LIVE 0'),true));
                $this->model_epos_order->deleteLiveorder($data['liveorderdata']);
                $this->log->write(print_r($data['liveorderdata'],true));
            }
            //if(!isset($this->request->post['eposonlinepay'])){
                if(isset($data['live_order_id'])){
                    $this->log->write(print_r(array('DELETE LIVE 1'),true));
                    $data['liveorderid']=(int)$data['live_order_id'];
                    $this->model_epos_order->deleteLiveorder(array('live_order_id'=>(int)$data['live_order_id'])); 
                    $this->log->write(print_r(array('live_order_id'=>(int)$data['live_order_id']),true));
                }else{
                    $this->log->write(print_r(array('DELETE LIVE 3'),true));
                    $data['liveorderid']=(int)$this->request->post['pushid'];
                    $this->model_epos_order->deleteLiveorder(array('live_order_id'=>(int)$this->request->post['pushid'])); 
                    $this->log->write(print_r(array('live_order_id'=>(int)$this->request->post['pushid']),true));
                }
                if(isset($data['live_order_id']) && $this->request->post['pushid']!=$data['live_order_id']){
                    $this->log->write(print_r(array('DELETE LIVE 4'),true));
                    $this->model_epos_order->deleteLiveorder(array('live_order_id'=>(int)$this->request->post['pushid'])); 
                    $this->log->write(print_r(array('live_order_id'=>(int)$this->request->post['pushid']),true));
                }
                if(isset($data['pushid']) && $this->request->post['pushid']!=$data['pushid']){
                    $this->log->write(print_r(array('DELETE LIVE 5'),true));
                    $this->model_epos_order->deleteLiveorder(array('live_order_id'=>(int)$data['pushid'])); 
                    $this->log->write(print_r(array('live_order_id'=>(int)$data['pushid']),true));
                }
            //}
            $order_id = $this->model_checkout_order->addOrder($data);
            //if(!isset($this->request->post['eposonlinepay'])){
                $this->model_epos_order->confirm($order_id,$data['order_status_id']);
            //}
            if(!isset($this->request->post['eposonlinepay']) && isset($this->request->post['adhoc_discounts']) && !is_array($this->request->post['adhoc_discounts'])){
                $adhdiscounts = json_decode(html_entity_decode($this->request->post['adhoc_discounts']),true);
                foreach($adhdiscounts as $adhdiscount){
                    $adhdiscount['name'] =  str_replace('on Order', '', $adhdiscount['name']);
                    if((!isset($adhdiscount['group_id']) || $adhdiscount['group_id']==0) && (!isset($adhdiscount['item_index']) || $adhdiscount['item_index']=='')){
                        //$this->model_epos_epos_history->insertadhockdiscount(array('discount_name'=>$adhdiscount['name'],'discount_amount'=>$adhdiscount['amt'],'discount_type'=>$adhdiscount['type']));
                    }
                }
            }
            if(isset($this->request->post['ep_id']) && isset($this->request->post['eposonlinepay'])){
                $this->db->query("UPDATE  " . DB_PREFIX . "epos_payonline  SET   status = '" .$this->request->post['paysttatus'] . "',order_id='".(int)$order_id."' WHERE ep_id='". (int)$this->request->post['ep_id'] ."'");            
            }
            
            $this->model_epos_order->addPushhistory(array('psuh_id'=>(int)$this->request->post['pushid'],'tk_id'=>(int)$this->request->post['tkid'],'method'=>'order','data'=>array())); 
            if($data['shipping_code']=='dine.dine' && isset($data['live_order_id'])){
                $this->db->query("DELETE  FROM " . DB_PREFIX . "live_orders_to_kitchen WHERE `live_order_id`='".(int)$data['live_order_id']."'");
                $this->db->query("DELETE  FROM " . DB_PREFIX . "kitchen_items WHERE `live_order_id`='".(int)$data['live_order_id']."'");
        
            }
            if($data['shipping_code']=='dine.dine' && isset($data['live_order_id'])){
                $this->db->query("DELETE  FROM " . DB_PREFIX . "live_orders_to_kitchen WHERE `live_order_id`='".(int)$data['live_order_id']."'");
                $this->db->query("DELETE  FROM " . DB_PREFIX . "kitchen_items WHERE `live_order_id`='".(int)$data['live_order_id']."'");
            }
            $this->db->query("DELETE  FROM " . DB_PREFIX . "live_orders_to_kitchen WHERE `order_id`='".(int)$order_id."'");
            $this->db->query("DELETE  FROM " . DB_PREFIX . "kitchen_items WHERE `order_id`='".(int)$order_id."'");
            // $this->websocket->connect('l');
            $this->websocket->connect('o-'.$order_id);
        }
        }
        $json = array('method'=>'order','pushid'=>$this->request->post['pushid']);
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    public function Orderstatus() {
        $this->load->model('checkout/order');
        $this->load->model('epos/order');
        //$this->log->write(print_r($this->request->post,true));
        if(isset($this->request->post['order_ids'])){
            $order_ids = json_decode(html_entity_decode($this->request->post['order_ids']),true);
            $data = $this->request->post;
            $data['comment'] =(isset($this->request->post['comment']))?str_replace(array('\n','"'),' ', html_entity_decode($this->request->post['comment'])):''; 
             if (isset($data['notify'])) {
                if(!is_array($data['notify'])){
                   $data['notify'] = json_decode(html_entity_decode($data['notify']),true);
                }
                if(empty($data['notify'])){
                    unset($data['notify']);
                }     
                if(!empty($data['notify']) && in_array('sms',$data['notify'])){
                
                    $data['notify_sms']=true;
                }
            }
            foreach ($order_ids as $order_id) {
                if($this->request->post['action']==0){//Order status changed 
                    $order_query = $this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
                    if($order_query->row['order_status_id']!=$this->request->post['order_status_id'] || trim($data['comment'])!=''){                        
                        $this->model_checkout_order->changeOrderStatus($order_id,$data);
                    }
                }else{//Order Deleted
                    $this->model_epos_order->deleteOrder($order_id);
                }
            }
            $this->websocket->connect('o-'.$order_id);
        }
        $json = array('method'=>'orderstatus','pushid'=>$this->request->post['pushid']);
        $this->response->setOutput($this->jsonp(json_encode($json)));   
      
    }
    public function Customer(){
        $push_qry = $this->db->query("SELECT push_id,method FROM `" . DB_PREFIX . "epos_push_history` WHERE push_id='".(int)$this->request->post['pushid']."' AND method='customer'");
        $repeatpush = false;
        if(!empty($push_qry->rows)){
            $repeatpush = true;
            $this->log->write(print_r(array('customer repeatpush'),true));
            
        }
        $json = array('method' => 'customer','pushid'=>$this->request->post['pushid']);
        if(!$repeatpush && isset($this->request->post['customer'])){
            $this->load->model('account/customer');
            $this->load->model('account/address');
            $customerdata = json_decode(html_entity_decode($this->request->post['customer']),true);
            if(isset($customerdata['type'])){
                switch ($customerdata['type']) {
                    case 'new':    //New Customer
                      
                        //firstname , lastname, email , telephone, fax, password , 
                        //company , address_1 , address_2, city, postcode, country_id, zone_id
                        $customerdata['address_1']=$customerdata['shipping_address_1'];
                        $customerdata['address_2']=$customerdata['shipping_address_2'];
                        $customerdata['postcode']=$customerdata['shipping_postcode']; 
                        $json['customer_id'] = $this->model_account_customer->addCustomer($customerdata);
                        $json['address_id'] = $this->model_account_customer->getCustomeraddressid($json['customer_id']);  
                        break;
                    case 'edit':    //Customer Data Edit    //customer_id
                         $this->model_account_customer->editCustomer($customerdata);
                        break;
                }
            }
            if(isset($customerdata['address_type'])){ 
                        $address_data = array(
                            'customer_id'=>$customerdata['customer_id'],
                            'firstname'=>$customerdata['shipping_firstname'],
                            'lastname'=>$customerdata['shipping_lastname'],
                            'address_1'=>$customerdata['shipping_address_1'],
                            'address_2'=>$customerdata['shipping_address_2'],
                            'city'=>$customerdata['shipping_city'],
                            'postcode'=>$customerdata['shipping_postcode']
                        );
                switch ($customerdata['address_type']){
                    case 'new':    //New Address
                        //customer_id ,firstname, lastname,address_1,address_2,postcode,city,
                        $json['address_id'] = $this->model_account_address->addAddress($address_data); 
                        $json['customer_id'] = $address_data['customer_id'];
                        break;
                    case 'edit':    //Customer Address Edit
                        $this->model_account_address->editAddress($customerdata['shipping_address_id'],$address_data); 
                        break;
                }
            }
            $this->websocket->connect('c-');
            $this->load->model('epos/order');
            $this->model_epos_order->addPushhistory(array('psuh_id'=>(int)$this->request->post['pushid'],'method'=>'customer','data'=>array())); 
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    public function Orderid(){
       if(isset($this->request->post['pushid'])){
            if($this->config->get('config_order_id_sequence')){
                $display_order_id = $this->getTodaysLastOrderSequence()+1;
            }
            if($this->config->get('config_order_id_unixtimestap')) {
                $order_id =  $this->unixtime();
                $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET order_id=" . $order_id.",`order_status_id`=0,date_added = '" . date("Y-m-d H:i:s", time()) . "',system_id='".(int)(isset($this->request->post['system_id'])?$this->request->post['system_id']:0)."',order_from='2'");
                $this->session->data['order_id'] = $order_id;
            }
            else {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET `order_status_id`=0,date_added = '" . date("Y-m-d H:i:s", time()) . "',system_id='".(int)$this->request->post['system_id']."',order_from='2'");
                $order_id = $this->db->getLastId();
            }
            if(isset($this->request->post['live'])){ // dinein_table_id
                $live_order_id = (int) (isset($this->request->post['live_order_id'])?$this->request->post['live_order_id']:$this->request->post['pushid']);
                $this->db->query("INSERT INTO " . DB_PREFIX . "live_orders SET live_order_id='".(int)$live_order_id."', `description` = '".(isset($this->request->post['dinein_table_id'])?$this->request->post['dinein_table_id']:0)."',`date_added` = NOW(),`date_modified` = NOW(), `data`= '' ,`lock` ='" . (int)(isset($this->request->post['lock'])?$this->request->post['lock']:0) . "' ,`system_id` ='" . (isset($this->request->post['system_id'])?$this->request->post['system_id']:0) . "',`dinein_table_id`='".(int)(isset($this->request->post['dinein_table_id'])?$this->request->post['dinein_table_id']:0)."'");
            }
            if(!isset($display_order_id)){
                $display_order_id = ($this->config->get('config_order_id_unixtimestap'))?substr($order_id, -5):$order_id;
            }
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET display_id='".$display_order_id."'  WHERE order_id=".$order_id."");
           // $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET `order_status_id`=0 ");
            $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'orderid','order_id'=>$order_id,'display_id'=>$display_order_id);
            $this->response->setOutput($this->jsonp(json_encode($json)));
       }
    }
    public function Liveorder(){
        
        $this->log->write(print_r('Liveeeeeeeeeeeeeeee',true));
        //$this->log->write(print_r($this->request->post,true));        
        $postdata =$this->request->post;// 
        //json_decode($this->request->post,true);
        $json = array('method'=>'liveorder','pushid'=>$this->request->post['pushid']); 
        //$data = array();
        /*
        foreach($postdata as $index=>$value){
            if(is_array($value)){
                $data[$index]=$value;
            }else{
                $data[$index]=json_decode(html_entity_decode($value),true);
            }
        }
        */ 
        $reppusstatus = false;
        $push_qry = $this->db->query("SELECT push_id,method FROM `" . DB_PREFIX . "epos_push_history` WHERE push_id='".(int)$this->request->post['pushid']."' AND tk_id='".(int)$this->request->post['tkid']."'  AND method='liveorder'");
        if(!empty($push_qry->rows)){
            $this->log->write(print_r(array('live order repeatpush'),true));
            $reppusstatus = true;                                        
        }
        if(isset($this->request->post['pushid']) && !$reppusstatus){
            $this->load->model('epos/order');
            if(isset($this->request->post['retake']) && isset($this->request->post['oldliveid'])){
                //$this->model_epos_order->deleteLiveorder(array('live_order_id'=>(int)$this->request->post['oldliveid']));
                $this->log->write(print_r(array('RETAKE LIVE'),true));                    
                 $this->db->query("DELETE  FROM " . DB_PREFIX . "live_order_deleted_track WHERE `live_order_id`='".(int)$this->request->post['oldliveid']."'");
                $this->log->write(print_r(array('live_order_id'=>(int)$this->request->post['oldliveid']),true));
            }

            $data = $this->request->post;
            $data['data'] = trim(html_entity_decode($data['data']),'"');
            $decodeddata = json_decode(zlib_decode(base64_decode($data['data'])),true);//'items'
            //$this->log->write(print_r($decodeddata,true));  
            $data['live_order_id'] = (int) (isset($this->request->post['live_order_id'])?$this->request->post['live_order_id']:$this->request->post['pushid']);
            if(!empty($decodeddata) && !isset($decodeddata['order_id'])){
                if($this->config->get('config_order_id_unixtimestap')) {
                    $order_id =  $this->unixtime();
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET order_id=" . $order_id.",`order_status_id`=0,date_added = '" . date("Y-m-d H:i:s", time()) . "',system_id='".(int)(isset($this->request->post['system_id'])?$this->request->post['system_id']:0)."',order_from='2'");               
                }
                else {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET `order_status_id`=0,date_added = '" . date("Y-m-d H:i:s", time()) . "',system_id='".(int)(isset($this->request->post['system_id'])?$this->request->post['system_id']:0)."',order_from='2'");
                    $order_id = $this->db->getLastId();
                }
                $data['order_id']  = $order_id;
                if($this->config->get('config_order_id_sequence')){
                    $display_order_id = $this->getTodaysLastOrderSequence()+1;
                }else{
                    $display_order_id = ($this->config->get('config_order_id_unixtimestap'))?substr($order_id, -5):$order_id;
                }
                 $this->db->query("UPDATE `" . DB_PREFIX . "order` SET display_id='".$display_order_id."'  WHERE order_id=".$order_id."");
            }

            if(isset($this->request->post['action']) && $this->request->post['action']==1){ 
                $json['pushid']=(int)$this->request->post['pushid'];
                $this->model_epos_order->deleteLiveorder($data);
            }else{
                $data['epos_check']=1; 
                $this->model_epos_order->updateLiveorder($data);
            }
            $this->websocket->connect('l');
            $this->model_epos_order->addPushhistory(array('psuh_id'=>(int)$this->request->post['pushid'],'tk_id'=>(int)$this->request->post['tkid'],'method'=>'liveorder','data'=>array())); 
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    public function settings(){ 
        if(isset($this->request->post['system_id'])){
            $system_id = $this->request->post['system_id'];
            $this->load->model('epos/order');
            $epos_printers = array();
            $normal_printers = array();
            if(isset($this->request->post['normal'])){
                //$normal = json_decode(html_entity_decode($this->request->post['normal']),true);
                $normal = $this->request->post['normal'];
                foreach ($normal as $key=>$value) {
                    if($value=='true'){
                        $normal_printers[] = array('printer_name'=>$key,'default'=>($key==$this->request->post['defaultprinter'])?1:0);
                    } 
                }
            }
            if(isset($this->request->post['epos'])){
               // $epos = json_decode(html_entity_decode($this->request->post['epos']),true);
                 $epos = $this->request->post['epos'];
                foreach ($epos as $value) {
                    $epos_printers[] = array(
                        'type'=>$value['type'],
                        'printer_name'=>$value['name'],
                        'host'=>$value['host'],
                        'port'=>$value['port'],
                    );
                }
            }
           
            $data = array('normalprinters'=>$normal_printers,'eposprinters'=>$epos_printers,'defaultprinter'=>$this->request->post['defaultprinter'],'sys_settings'=>$this->request->post['settings']);
           
            $this->model_epos_order->updateEpossettings($system_id,$data,$this->request->post['defaultprinter']);
              $this->websocket->connect('s');
            $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'Settings'); 
            $this->response->setOutput($this->jsonp(json_encode($json)));  
        }
    }
    public function getpostcodedata() {
        $json = array();
        if(isset($this->request->get['postcode'])){
            $store_postal_code = $this->postcode->postcodeformat($this->config->get('config_postalcode'));
            $destn_postcode =$this->postcode->postcodeformat($this->request->get['postcode']);
            if($this->config->get('pos_to_storeid')){
                $store_id =$this->config->get('pos_to_storeid');
            }else{
                $store_id = 0;
            }
            $distance = $this->postcode->googleDistance($store_postal_code,$destn_postcode,$store_id);
            $json = array('distance'=>$distance);
        } 
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    /*
     * params 
     * order_id  , action  , message , deliverytime
     */
    public function onlineorder() { 
        $this->load->model('epos/order');
        $this->load->model('checkout/order');
        $order_accepted_status_id=12;
        $order_regested_status_id=8;
        
        
        if(isset($this->request->post['order_id'])){
            $order = $this->model_checkout_order->getOrder($this->request->post['order_id']);
            if($order['order_status_id']!=$this->config->get('config_complete_status_id')){
                if($order['order_status_id']==0 || $order['order_status_id']==17) {
                    $order_accepted_status_id=17;
                    $order_regested_status_id=17;
                }
                $notify = 0;
                if(isset($this->request->post['notify'])){ 
                    $notify = 1;
                }
                $smsnotificn=false;
                if($notify && $this->config->get('pos_accept_print_notification') && in_array('sms',$this->config->get('pos_accept_print_notification'))){
                    $smsnotificn=true;
                }
                switch($this->request->post['action']){
                case 0:
                     if($order['shipping_code'] == 'home.home') {
                    $comment = (isset($this->request->post['deliverytime']))?'Delivery Time : '.html_entity_decode($this->request->post['deliverytime']):'';
                    }else{
                        $comment = (isset($this->request->post['deliverytime']))?'Collection Time : '.html_entity_decode($this->request->post['deliverytime']):'';
                    }
                    if($smsnotificn){
                        $notdata=array('order_status_id'=>$order_accepted_status_id,'notify'=>$notify,'comment'=>$comment,'notify_sms'=>true);
                    }else{
                        $notdata=array('order_status_id'=>$order_accepted_status_id,'notify'=>$notify,'comment'=>$comment);
                    }
                    //$this->model_epos_order->addOrderHistory($this->request->post['order_id'],array('order_status_id'=>$order_accepted_status_id,'notify'=>$notify,'comment'=>$comment));
                    if(isset($this->request->post['print'])){
                        $notdata['printonly']=1;
                    }
                    $this->model_checkout_order->changeOrderStatus((int)$this->request->post['order_id'] ,$notdata);
                    //$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_accepted_status_id . "', date_modified = NOW(), user_id = '" . (int) $this->request->post['user_id'] . "' WHERE order_id = '" . (int)$this->request->post['order_id'] . "'");
                    break; 
                case 1:
                    $this->model_checkout_order->changeOrderStatus((int)$this->request->post['order_id'] ,array('order_status_id'=>$order_regested_status_id,'notify'=>1,'comment'=>$this->request->post['message'],'reject'=>1));
                    //$this->model_epos_order->addOrderHistory($this->request->post['order_id'],array('order_status_id'=>$order_regested_status_id,'notify'=>1,'comment'=>$this->request->post['message']));
                    //$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_regested_status_id . "', date_modified = NOW(), user_id = '" . (int) $this->request->post['user_id'] . "' WHERE order_id = '" . (int)$this->request->post['order_id'] . "'");
                    break; 
                default: 
                    break;
            }
            }
            $this->websocket->connect('o-'.$this->request->post['order_id']);
            $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'onlineorder');
            $this->response->setOutput($this->jsonp(json_encode($json)));
        } 
    }
    // Driver Management
    public function clock() { 
        $this->load->model('drivermanagement/drivermanagement');
        if (isset($this->request->post['driver_id']) && isset($this->request->post['clock_status'])) {
            
            if(isset($this->request->post['clock_id'])){
                
                if ($this->request->post['clock_status'] == 1) {//UPDATE CLOCKED IN
            
                    $this->model_drivermanagement_drivermanagement->updatedriverclockin($this->request->post['clock_id'],$this->request->post['user_id'],$this->request->post['in_time']);
                }else{
            
                    $this->model_drivermanagement_drivermanagement->updatedriverclockout($this->request->post['clock_id'],$this->request->post['user_id'],$this->request->post['out_time']);
                }
            }else{
                if ($this->request->post['clock_status'] == 1) {
                    $this->model_drivermanagement_drivermanagement->driverclockin($this->request->post['driver_id'],$this->request->post['user_id'],$this->request->post['in_time'],$this->request->post['employee_clock_id']);
                } else {
                    $this->model_drivermanagement_drivermanagement->driverclockout($this->request->post['driver_id'],$this->request->post['user_id'],$this->request->post['out_time']);
                }
            }
        }
         $this->websocket->connect('d-');
        $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'clock');
        $this->response->setOutput($this->jsonp(json_encode($json)));
        
    }
    public function clockoutall() { 
        $this->websocket->connect('d-');
        
        if(!is_array($this->request->post['updateddata'])){
            $updateddata = json_decode(html_entity_decode($this->request->post['updateddata']),true);
        }else{
            $updateddata = $this->request->post['customer'];
        }
        $this->log->write(print_r($updateddata,true));
        $this->load->model('drivermanagement/drivermanagement');
        foreach ($updateddata as $clokedoutdata) {
            $this->model_drivermanagement_drivermanagement->updatedriverclockout($clokedoutdata['clock_id'],$clokedoutdata['user_id'],$clokedoutdata['outtime']);
               
        }
         $this->websocket->connect('d-');
        $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'clock');
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    public function assignorder() { 
        
        if (isset($this->request->post['driver_id']) && isset($this->request->post['orders'])) {
            $this->load->model('drivermanagement/drivermanagement');
            $orders = implode(",", json_decode(html_entity_decode($this->request->post['orders']),true));
            $this->model_drivermanagement_drivermanagement->assignDrivertodeliver($this->request->post['driver_id'], $orders);
        }
         $this->websocket->connect('d-');
        $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'assignorder');
        $this->response->setOutput($this->jsonp(json_encode($json)));
        
    }
    public function unassign() { 
        if (isset($this->request->post['order_id'])) {
            $this->load->model('drivermanagement/drivermanagement');
            $this->model_drivermanagement_drivermanagement->UnassignOrder($this->request->post['order_id']);
        }
         $this->websocket->connect('d-');
        $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'unassign');
        $this->response->setOutput($this->jsonp(json_encode($json)));
        
    }
    public function drivercommision() { 
        if(isset($this->request->post['order_id'])){
            
            $this->load->model('drivermanagement/drivermanagement');
            $this->model_drivermanagement_drivermanagement->addPettycash((int)$this->request->post['driver_id'],'N',(float)$this->request->post['amount'],(int)$this->request->post['order_id']);
        }
         $this->websocket->connect('d-');
        $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'drivercommision');
        $this->response->setOutput($this->jsonp(json_encode($json)));        
    }
    /*
     * 
     */
    public function issues() {
        $message=$this->request->post['comment'];
        foreach ($this->request->post['images'] as $image){
            $message .='<img src="'.$image.'">';
        }
        $mail = new Mail();
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->hostname = $this->config->get('config_smtp_host');
        $mail->username = $this->config->get('config_smtp_username');
        $mail->password = $this->config->get('config_smtp_password');
        $mail->port = $this->config->get('config_smtp_port');
        $mail->timeout = $this->config->get('config_smtp_timeout');
        $mail->setTo('sanu@netventure.in');
        $mail->setFrom('levan.sanu@gmail.com');
        $mail->setSender('EPOS_ISSUES');
        $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_coupon_subject'), 'DEMO'), ENT_QUOTES, 'UTF-8'));
        $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
        $mail->send();
        $json = array('issues'=>'issues'); 
        $this->response->setOutput($this->jsonp(json_encode($json)));  
    }
    public function Postcode() {
        $json = array();
        if(isset($this->request->post['postcode']) && $this->request->post['distance']){
            $postcode = trim(str_replace('"', '', html_entity_decode($this->request->post['postcode'])));
            $disdata=$this->postcode->getPostcodeDistance(html_entity_decode($postcode),$this->config->get('pos_to_storeid'));
            if(empty($disdata)){
                $this->postcode->adddistance(html_entity_decode($postcode),$this->config->get('pos_to_storeid'),(float)$this->request->post['distance']);
                //$this->db->query("INSERT INTO " . DB_PREFIX . "uk_postcode_distance SET postcode='" . $this->db->escape($postcode) . "',store_id='" . (int)$this->config->get('pos_to_storeid') . "',distance='" . (float)$this->request->post['distance'] . "'");
            }
            $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'postcode','success'=>'success');
        }
        
        $this->response->setOutput($this->jsonp(json_encode($json)));
   }
    public function getstreetdata(){ 
        $json = array();
        if(isset($this->request->get['postcode'])){
           $json = $this->postcode->getEtmPostcodedata($this->request->get['postcode']);
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    /*
     *  PRINT PUSH
     */
    public function eposprint(){
        $json = array(); 
        if(isset($this->request->post['system_id']) && isset($this->request->post['printdata']) ){
            $this->load->model('epos/print');
            $this->model_epos_print->createPintqueue($this->request->post['system_id'],trim(($this->request->post['printdata']),'"'));
            $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'eposprint');
            $this->websocket->connect('p');
        }
        
        $this->response->setOutput($this->jsonp($json,true));
    }
     public function eposlabelprint(){
        $json = array(); 
        if(isset($this->request->post['system_id']) && isset($this->request->post['printdata']) ){
            $this->load->model('epos/print');
            $labelprints = json_decode(html_entity_decode($this->request->post['printdata']),true);
            foreach($labelprints as $printdata){
                $this->model_epos_print->createPintqueue($this->request->post['system_id'],trim((htmlentities($printdata)),'"'));
            }           
            $json = array('pushid'=>(int)$this->request->post['pushid'],'method'=>'eposlabelprint');
            $this->websocket->connect('p');
        }
        
        $this->response->setOutput($this->jsonp($json,true));
    }
     private function unixtime() {

            $unixtime = time () ;
            // $unix_query = $this->db->query("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE TABLE_NAME = '" . DB_PREFIX . "order'");
            //  $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` AUTO_INCREMENT = ". $unixtime .'0');
            $unix_query =  $this->db->query("SELECT  order_id FROM `" . DB_PREFIX . "order`  WHERE order_id = ". $unixtime );
            if($unix_query->num_rows  ) {
                // Order ID Exist
                // Not Update AUTO_INCREMENT
                sleep(1);
                return $this->unixtime();
            }
            else {
                return $unixtime;
            }
        }
    /*
     * get todays order sequence id
     */
    private function getTodaysLastOrderSequence(){ 
        $current_time = strtotime('now');        
         $yesterday = strtolower(date('l', strtotime('yesterday')));
        $yesterday_times = $yesterday . '_store_times';
        $yesterday_open_times = $this->config->get($yesterday_times);
        foreach ($yesterday_open_times as $yesterday_open_time) {
            if(empty($yesterday_open_time['start']) ||  $yesterday_open_time['start']==''){
                continue;
            }
            $yes_start_times = explode(':', $yesterday_open_time['start']);
            $yes_end_times = explode(':', '03:00');//explode(':', $yesterday_open_time['end']);
            $yesterdayendtime=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 1 , date("Y"));
            if ((int)$yes_start_times[0] > (int)$yes_end_times[0])
            {
                $yesterdayendtime=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d"), date("Y"));
            }
        }
        if(isset($yesterdayendtime) && $current_time<=$yesterdayendtime){//In TOday
            $yesterdayendtime = $yesterdayendtime - 86400;
        }
        if(!isset($yesterdayendtime)){
            $yes_end_times = explode(':', '23:59');
            $yesterdayendtime=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 1 , date("Y"));
        }
        if(isset($yesterdayendtime) && $current_time > $yesterdayendtime){
            //$yesterdayendtime = $yesterdayendtime+10800;
            $ordes_query =  $this->db->query("SELECT display_id FROM `" . DB_PREFIX . "order`  WHERE  date_added > '" . date('Y-m-d H:i:s',$yesterdayendtime) . "' ORDER BY display_id DESC  LIMIT 0,1");
            if(!empty($ordes_query->rows)){
                return $ordes_query->row['display_id']; 
            }
        }
        return 0 ; 
        /*
        $current_time = strtotime('now');        
        $last_days_times = $this->postcode->getLastDaysOpeningtimes(); 
        foreach ($last_days_times as $last_days_time) {
            if($current_time > strtotime($last_days_time['end'])){
                $yester_day_close_time = $last_days_time['end'];
                break;
            }
        }
        if(isset($yester_day_close_time)){
            $ordes_query =  $this->db->query("SELECT display_id FROM `" . DB_PREFIX . "order`  WHERE  date_added > '" . $yester_day_close_time . "' ORDER BY display_id DESC  LIMIT 0,1");
            if(!empty($ordes_query->rows)){
                return $ordes_query->row['display_id']; 
            }else{
                return 0;
            }
        }
        return 0;
        */
    }
    
    public function ordercancel() {
        $data = array();
        $this->load->model('account/customer');
        $this->load->model('account/address');
        $this->load->model('epos/order');
        $this->load->model('checkout/order');
        $this->load->model('epos/epos_history'); 
        
        if (isset($this->request->post)){
           
            if(isset($this->request->post['order_id'])){
                $order_id= $this->request->post['order_id'];
                $data['store_id'] = (isset($this->request->post['store_id']))?$this->request->post['store_id']:  $this->config->get('pos_to_storeid');
                if(!isset($this->request->post['store_name']) || $this->request->post['store_name']==''){
                $data['store_name']='EPOS';
                }else{
                    $data['store_name'] = trim(str_replace('"', '', html_entity_decode($this->request->post['store_name']))); 
                }
                if(isset($this->request->post['orderedit'])){
                    $data['order_from']=0;
                }else{
                    $data['order_from']=2;
                }
                $data['store_url']='';
                $data['customer_group_id'] =(isset($this->request->post['customer_group_id'])?$this->request->post['customer_group_id']:$this->config->get('config_customer_group_id')); //
                $data['order_status_id'] =(isset($this->request->post['order_status_id'])?$this->request->post['order_status_id']:$this->config->get('config_order_status_id')); //
                $data['user_agent']=(isset($this->request->post['user_agent'])?$this->request->post['user_agent']:'EPOS'); //'';
                $data['date_added']=(isset($this->request->post['date_added'])?$this->request->post['date_added']:date('Y-m-d H:i:s',time())); //'';
                $data['system_id']=(isset($this->request->post['system_id'])?$this->request->post['system_id']:0);
                $data['user_id']=(isset($this->request->post['user_id'])?$this->request->post['user_id']:0);
                $data['waiter_id']=(isset($this->request->post['waiter_id'])?$this->request->post['waiter_id']:0);
                $data['language_id'] = $this->config->get('config_language_id');
                $data['currency_id'] = $this->currency->getId();
                $data['currency_code'] = $this->currency->getCode();
                $data['currency_value'] = $this->currency->getValue($this->currency->getCode());
                if(isset($this->request->post['customer'])){//No  Walkin
                $customerdata = (isset($this->request->post['order_id']))?json_decode(html_entity_decode($this->request->post['customer']),true):$this->request->post['customer'];
                if(isset($customerdata['type'])){
                    switch ($customerdata['type']) {
                        case 'new':    //New Customer
                            //firstname , lastname, email , telephone, fax, password , 
                            //company , address_1 , address_2, city, postcode, country_id, zone_id
                            $customerdata['address_1']=$customerdata['shipping_address_1'];
                            $customerdata['address_2']=$customerdata['shipping_address_2'];
                            $customerdata['postcode']=$customerdata['shipping_postcode'];
                            $data['customer_id'] = $this->model_account_customer->addCustomer($customerdata);
                            break;
                        case 'edit':    //Customer Data Edit    //customer_id
                             $this->model_account_customer->editCustomer($customerdata);
                            break;
                    }
                }
                if(isset($customerdata['address_type'])){ 
                            $address_data = array(
                                'customer_id'=>$customerdata['customer_id'],
                                'firstname'=>$customerdata['shipping_firstname'],
                                'lastname'=>$customerdata['shipping_lastname'],
                                'address_1'=>$customerdata['shipping_address_1'],
                                'address_2'=>$customerdata['shipping_address_2'],
                                'city'=>$customerdata['shipping_city'],
                                'postcode'=>$customerdata['shipping_postcode']
                            );
                    switch ($customerdata['address_type']){
                        case 'new':    //New Address
                            //customer_id ,firstname, lastname,address_1,address_2,postcode,city,
                            $this->model_account_address->addAddress($address_data); 
                            break;
                        case 'edit':    //Customer Address Edit
                            $this->model_account_address->editAddress($customerdata['shipping_address_id'],$address_data); 
                            break;
                    }
                }
                $data +=$customerdata;
                $data['payment_firstname']=(isset($customerdata['firstname']))?$customerdata['firstname']:'';
                $data['payment_lastname']=(isset($customerdata['lastname']))?$customerdata['lastname']:'';
                $data['payment_address_1']=(isset($customerdata['shipping_address_1']))?$customerdata['shipping_address_1']:'';
                $data['payment_address_2']=(isset($customerdata['shipping_address_2']))?$customerdata['shipping_address_2']:'';
                $data['payment_city']=(isset($customerdata['shipping_city']))?$customerdata['shipping_city']:'';
                $data['payment_postcode']=(isset($customerdata['shipping_postcode']))?$customerdata['shipping_postcode']:'';
            }else{//Takeout Customer
                    $data['customer_id']=$this->config->get('pos_walkin_customer_id');
                    $data['shipping_firstname']='Takeout';
                    $data['shipping_lastname']='';
                    $data['shipping_address_1']='';
                    $data['shipping_address_2']='';
                    $data['shipping_city']='';
                    $data['shipping_postcode']='';
                    $data['payment_firstname']='Takeout';
                    $data['payment_lastname']='';
                    $data['payment_address_1']='';
                    $data['payment_address_2']='';
                    $data['payment_city']='';
                    $data['payment_postcode']='';
                }
                 if(!is_array($this->request->post['shipping_method'])){
                $shippingdata = json_decode(html_entity_decode($this->request->post['shipping_method']),true);
            }else{
                $shippingdata = $this->request->post['shipping_method'];
            }
            //$paymentdata =   (isset($this->request->post['order_id']))?json_decode(html_entity_decode($this->request->post['payment_method']),true):$this->request->post['payment_method'];
            if(isset($this->request->post['payment_method'])){
                $paymentdata = (!is_array($this->request->post['payment_method']))?json_decode(html_entity_decode($this->request->post['payment_method']),true):$this->request->post['payment_method'];
                $data['payment_method']=$paymentdata['name'];
                $data['payment_code']=$paymentdata['code'];                
            }else{
                $data['payment_method']='';
                $data['payment_code']='';
            }
            
            if(isset($this->request->post['prevpayments']) && isset($this->request->post['orderedit'])){
                //$paymentdata = (!is_array($this->request->post['prevpayments']))?json_decode(html_entity_decode($this->request->post['prevpayments']),true):$this->request->post['prevpayments'];
            }
            $data['shipping_method']=$shippingdata['name'];
            $data['shipping_code']=$shippingdata['code'].'.'.$shippingdata['code'];
            
                $data['date_added']=(isset($this->request->post['date_added'])?$this->request->post['date_added']:date('Y-m-d H:i:s',time())); //'';
                $data['date_modified']=date('Y-m-d H:i:s',time());
                $data['order_requested_time']=isset($this->request->post['requested_time'])?$this->request->post['requested_time']:time();
                
                $products = json_decode(html_entity_decode($this->request->post['items']),true);                
                $productdata = array();
                foreach ($products as $product) {
                    if(isset($product['bundle_data']) && !empty($product['bundle_data'])){
                        continue;
                    }

                    $option_data = array();
                    if($this->config->get('adhocproduct_product_id') && $this->config->get('adhocproduct_product_id')==$product['product_id']){
                        $this->model_epos_epos_history->insertahocdata(array('product_name'=>$product['name'],'price'=>$product['price_value']));
                    }

                    foreach ($product['option'] as $option) {
                        if(!isset($option['type'])){
                             foreach ($option as $checkbox) {
                                $option_data[] = array(
                                    'product_option_id' => $checkbox['product_option_id'],
                                    'product_option_value_id' => $checkbox['product_option_value_id'],
                                    'option_id' => $checkbox['option_id'],
                                    'option_value_id' => $checkbox['option_value_id'],
                                    'name' => $checkbox['option_name'],
                                    'shortcode'=>$checkbox['shortcode'],
                                    'value' => $checkbox['name'],
                                    'type' => $checkbox['type'],
                                    'group'=> $checkbox['group']
                                );
                             }
                        }else{
                            if($option['type']=='text' || $option['type']=='textarea'){//INsert Comment History                           
                                $this->model_epos_epos_history->insertprodtcommentdata(array('comment'=>$option['name'],'product_id'=>$product['product_id'],'customer_id'=>(isset($data['customer_id']) && $data['customer_id']!=$this->config->get('pos_walkin_customer_id'))?$data['customer_id']:0));
                            }
                            $option_data[] = array(
                                'product_option_id' => $option['product_option_id'],
                                'product_option_value_id' => $option['product_option_value_id'],
                                'option_id' => $option['option_id'],
                                'option_value_id' => $option['option_value_id'],
                                'name' => $option['option_name'],
                                'shortcode'=>$option['shortcode'],
                                'value' => $option['name'],
                                'type' => $option['type'],
                                'group'=> $option['group']
                            );
                        }

                    }                              
                    $topping_data =array();
                    if(isset($product['toppings'])){
                        foreach ($product['toppings'] as $topping_id=>$topping_values) {
                            foreach ($topping_values as $topping_value) {
                                $topping_data[]=array(
                                    'product_topping_id'=>$topping_id,
                                    'product_topping_value_id'=>$topping_value['topping_value_id'],
                                    'name'=>(isset($topping_value['btn']) && $topping_value['btn']!='')?'':$topping_value['toppingname'],
                                    'topping_value'=>((isset($topping_value['btn']) && $topping_value['btn']!='')?$topping_value['btn']:'').' '.$topping_value['name'],
                                    'price'=>$topping_value['price'],
                                    'quantity'=>$topping_value['qty'],
                                    'shortcode'=>((isset($topping_value['btnshort']))?$topping_value['btnshort']:'').' '.$topping_value['shortcode']
                                );
                            }
                        }
                    }
                    $dressing_data= array();
                    if(isset($product['dressings'])){
                        foreach ($product['dressings'] as $dressing_id=>$dressing_values) {
                            foreach ($dressing_values as $dressing_value) {
                                $dressing_data[]=array(
                                    'product_dressing_id'=>$dressing_id,
                                    'product_dressing_value_id'=>$dressing_value['dressing_value_id'],
                                    'name'=>(isset($dressing_value['btn']))?'':$dressing_value['dressingname'],
                                    'dressing_value'=>((isset($dressing_value['btn']))?$dressing_value['btn']:'').' '.$dressing_value['name'],
                                    'shortcode'=>((isset($dressing_value['btnshort']))?$dressing_value['btnshort']:'').' '.$dressing_value['shortcode']
                                );
                            }
                        }
                    }
                    //Bundled End
                    $productdata[]=array(
                        'product_id' => $product['product_id'],
                        'cart_key' => $product['item_index'],
                        'name' => $product['name'],
                        'model' => '',
                        'option' => $option_data,
                        'toppings'=>array(),
                        'download' => array(),
                        'quantity' => $product['qty'],
                        'subtract' => '',
                        'price' => $product['price_value'],
                        'total' => $product['total_value'],
                        'bundle'=> array(),
                        'tax'=>'',
                        'reward'=>'',
                        'toppings'=>$topping_data,
                        'dressings'=>$dressing_data,
                        'main_product_id'=>(isset($product['orgproductid']))?$product['orgproductid']:0
                    );
                }
                foreach ($products as $product) {
                    if(isset($product['bundle_data']) && !empty($product['bundle_data'])){
                        $productbundle=array();                   
                        foreach ($product['bundle_data'] as $bundle_id => $bundledata){
                            foreach ($bundledata as $bundle){
                                $productbundle[$bundle['item_index']] = array($bundle_id=>$bundle['qty']);
                            }
                        }
                        $option_data = array();
                        foreach ($product['option'] as $option) {
                        if(!isset($option['type'])){
                             foreach ($option as $checkbox) {
                                $option_data[] = array(
                                    'product_option_id' => $checkbox['product_option_id'],
                                    'product_option_value_id' => $checkbox['product_option_value_id'],
                                    'option_id' => $checkbox['option_id'],
                                    'option_value_id' => $checkbox['option_value_id'],
                                    'name' => $checkbox['option_name'],
                                    'shortcode'=>$checkbox['shortcode'],
                                    'value' => $checkbox['name'],
                                    'type' => $checkbox['type'],
                                    'group'=> $checkbox['group']
                                );
                             }
                        }else{
                            if($option['type']=='text' || $option['type']=='textarea'){//INsert Comment History                           
                                $this->model_epos_epos_history->insertprodtcommentdata(array('comment'=>$option['name'],'product_id'=>$product['product_id'],'customer_id'=>(isset($data['customer_id']) && $data['customer_id']!=$this->config->get('pos_walkin_customer_id'))?$data['customer_id']:0));
                            }
                            $option_data[] = array(
                                'product_option_id' => $option['product_option_id'],
                                'product_option_value_id' => $option['product_option_value_id'],
                                'option_id' => $option['option_id'],
                                'option_value_id' => $option['option_value_id'],
                                'name' => $option['option_name'],
                                'shortcode'=>$option['shortcode'],
                                'value' => $option['name'],
                                'type' => $option['type'],
                                'group'=> $option['group']
                            );
                        }

                    }               
                        $topping_data =array();
                        if(isset($product['toppings'])){
                            foreach ($product['toppings'] as $topping_id=>$topping_values) {
                                foreach ($topping_values as $topping_value) {
                                    $topping_data[]=array(
                                        'product_topping_id'=>$topping_id,
                                        'product_topping_value_id'=>$topping_value['topping_value_id'],
                                        'name'=>(isset($topping_value['btn']) && $topping_value['btn']!='')?'':$topping_value['toppingname'],
                                        'topping_value'=>((isset($topping_value['btn']) && $topping_value['btn']!='')?$topping_value['btn']:'').' '.$topping_value['name'],
                                        'price'=>$topping_value['price'],
                                        'quantity'=>$topping_value['qty'],
                                        'shortcode'=>((isset($topping_value['btnshort']))?$topping_value['btnshort']:'').' '.$topping_value['shortcode']
                                    );
                                }
                            }
                        }
                        $dressing_data= array();
                        if(isset($product['dressings'])){
                            foreach ($product['dressings'] as $dressing_id=>$dressing_values) {
                                foreach ($dressing_values as $dressing_value) {
                                    $dressing_data[]=array(
                                        'product_dressing_id'=>$dressing_id,
                                        'product_dressing_value_id'=>$dressing_value['dressing_value_id'],
                                        'name'=>(isset($dressing_value['btn']))?'':$dressing_value['dressingname'],
                                        'dressing_value'=>((isset($dressing_value['btn']))?$dressing_value['btn']:'').' '.$dressing_value['name'],
                                        'shortcode'=>((isset($dressing_value['btnshort']))?$dressing_value['btnshort']:'').' '.$dressing_value['shortcode']
                                    );
                                }
                            }
                        }
                        //Bundled End
                        $productdata[]=array(
                            'product_id' => $product['product_id'],
                            'cart_key' => $product['item_index'],
                            'name' => $product['name'],
                            'model' => '',
                            'option' => $option_data,
                            'toppings'=>array(),
                            'download' => array(),
                            'quantity' => $product['qty'],
                            'subtract' => '',
                            'price' => $product['price_value'],
                            'total' => $product['total_value'],
                            'bundle'=> $productbundle,
                            'tax'=>'',
                            'reward'=>'',
                            'toppings'=>$topping_data,
                            'dressings'=>$dressing_data,
                            'main_product_id'=>(isset($product['orgproductid']))?$product['orgproductid']:0
                        );
                    }

                }
                $totals = array();
                $totaldata = json_decode(html_entity_decode($this->request->post['totals']),true);
                foreach ($totaldata as $code=>$total) {
                    foreach($total as $value){
                        $totals[]=array(
                            'code'=>$code,
                            'title'=>$value['name'],
                            'text'=>$value['value'],
                            'value'=>$value['value'],
                            'sort_order'=>($this->config->get($code . '_sort_order'))?$this->config->get($code . '_sort_order'):0
                        );
                    }
                }
                $order_total=  isset($totaldata['total'])?$totaldata['total'][0]['value']:0;
                $data['total'] = (float)$order_total;
                if(!isset($data['customer_id'])){
                    $data['customer_id'] = $this->config->get('pos_walkin_customer_id');
                } 
                if(isset($this->request->post['payed_amts'])){
                    $data['payment_history'] = (!is_array($this->request->post['payed_amts']))?json_decode(html_entity_decode($this->request->post['payed_amts']),true):$this->request->post['payed_amts'];
                }else{
                    $data['payment_history']=array();
                }
                $sql_vars ="invoice_prefix = '" . $this->db->escape(isset($data['invoice_prefix'])?$data['invoice_prefix']:'') . "', store_id = '" . (int) $data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int) $data['customer_id'] . "', customer_group_id = '" . (int) (isset($data['customer_group_id'])?$data['customer_group_id']:0) . "', firstname = '" . $this->db->escape((isset($data['firstname']))?$data['firstname']:'') . "', lastname = '" . $this->db->escape(isset($data['lastname'])?$data['lastname']:''). "', email = '" . $this->db->escape((isset($data['email']))?$data['email']:'') . "', telephone = '" . $this->db->escape(isset($data['telephone'])?$data['telephone']:'') . "', fax = '" . $this->db->escape(isset($data['fax'])?$data['fax']:'') . "', payment_firstname = '" . $this->db->escape(isset($data['payment_firstname'])?$data['payment_firstname']:'') . "', payment_lastname = '" . $this->db->escape(isset($data['payment_lastname'])?$data['payment_lastname']:'') . "', payment_company = '" . $this->db->escape(isset($data['payment_company'])?$data['payment_company']:'') . "', payment_company_id = '" . $this->db->escape(isset($data['payment_company_id'])?$data['payment_company_id']:'') . "', payment_tax_id = '" . $this->db->escape(isset($data['payment_tax_id'])?$data['payment_tax_id']:'') . "', payment_address_1 = '" . $this->db->escape(isset($data['payment_address_1'])?$data['payment_address_1']:'') . "', payment_address_2 = '" . $this->db->escape(isset($data['payment_address_2'])?$data['payment_address_2']:'') . "', payment_city = '" . $this->db->escape(isset($data['payment_city'])?$data['payment_city']:'') . "', payment_postcode = '" . $this->db->escape(isset($data['payment_postcode'])?$data['payment_postcode']:'') . "', payment_country = '" . $this->db->escape(isset($data['payment_country'])?$data['payment_country']:'') . "', payment_country_id = '" . (int) (isset($data['payment_country_id'])?$data['payment_country_id']:'') . "', payment_zone = '" . $this->db->escape(isset($data['payment_zone'])?$data['payment_zone']:'') . "', payment_zone_id = '" . (int) (isset($data['payment_zone_id'])?$data['payment_zone_id']:'') . "', payment_address_format = '" . $this->db->escape(isset($data['payment_address_format'])?$data['payment_address_format']:'') . "', payment_method = '" . $this->db->escape(isset($data['payment_method'])?$data['payment_method']:'') . "', payment_code = '" . $this->db->escape(isset($data['payment_code'])?$data['payment_code']:'') . "', shipping_firstname = '" . $this->db->escape(isset($data['shipping_firstname'])?$data['shipping_firstname']:'') . "', shipping_lastname = '" . $this->db->escape(isset($data['shipping_lastname'])?$data['shipping_lastname']:'') . "', shipping_company = '" . $this->db->escape(isset($data['shipping_company'])?$data['shipping_company']:'') . "', shipping_address_1 = '" . $this->db->escape(isset($data['shipping_address_1'])?$data['shipping_address_1']:'') . "', shipping_address_2 = '" . $this->db->escape(isset($data['shipping_address_2'])?$data['shipping_address_2']:'') . "', shipping_city = '" . $this->db->escape(isset($data['shipping_city'])?$data['shipping_city']:'') . "', shipping_postcode = '" . $this->db->escape(isset($data['shipping_postcode'])?$data['shipping_postcode']:'') . "', shipping_country = '" . $this->db->escape(isset($data['shipping_country'])?$data['shipping_country']:'') . "', shipping_country_id = '" . (int) (isset($data['shipping_country_id'])?$data['shipping_country_id']:'') . "', shipping_zone = '" . $this->db->escape(isset($data['shipping_zone'])?$data['shipping_zone']:'') . "', shipping_zone_id = '" . (int) (isset($data['shipping_zone_id'])?$data['shipping_zone_id']:'') . "', shipping_address_format = '" . $this->db->escape(isset($data['shipping_address_format'])?$data['shipping_address_format']:'') . "', shipping_method = '" . $this->db->escape(isset($data['shipping_method'])?$data['shipping_method']:'') . "', shipping_code = '" . $this->db->escape(isset($data['shipping_code'])?$data['shipping_code']:'') . "', comment = '" . $this->db->escape(isset($data['comment'])?$data['comment']:'') . "',order_status_id='".(int)$this->config->get('pos_order_void_status')."', total = '" . (float) (isset($data['total'])?$data['total']:'') . "', affiliate_id = '" . (int) (isset($data['affiliate_id'])?$data['affiliate_id']:'') . "', commission = '" . (float) (isset($data['commission'])?$data['commission']:'') . "', language_id = '" . (int) (isset($data['language_id'])?$data['language_id']:'') . "', currency_id = '" . (int) (isset($data['currency_id'])?$data['currency_id']:'') . "', currency_code = '" . $this->db->escape(isset($data['currency_code'])?$data['currency_code']:'') . "', currency_value = '" . (float)(isset($data['currency_value'])?$data['currency_value']:'')  . "', ip = '" . $this->db->escape(isset($data['ip'])?$data['ip']:'') . "', forwarded_ip = '" . $this->db->escape(isset($data['forwarded_ip'])?$data['forwarded_ip']:'') . "', user_agent = '" . $this->db->escape(isset($data['user_agent'])?$data['user_agent']:'') . "', accept_language = '" . $this->db->escape(isset($data['accept_language'])?$data['accept_language']:'') . "',  date_modified = '" . date("Y-m-d H:i:s", time()) . "',requested_time='" . date("Y-m-d H:i:s", isset($data['order_requested_time']) ? $data['order_requested_time'] : time()) . "', system_id = '" . (int) ((isset($data['system_id']))?$data['system_id']:0)  . "', user_id = '" . (int) ((isset($data['user_id']))?$data['user_id']:0) . "',online_order_id=' ".(isset($data['online_order_id'])?$data['online_order_id']:0)."',peoples='". (int)(isset($data['peoples'])?$data['peoples']:0)."',order_from='". (int)(isset($data['order_from'])?$data['order_from']:0)."',change_due='". (float)(isset($data['change_due'])?$data['change_due']:0)."',payment_history = '" . $this->db->escape(serialize(isset($data['payment_history'])?$data['payment_history']:array())) . "'";
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET ".$sql_vars." WHERE order_id='".$order_id."'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
                foreach ($productdata as $product) { 
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int) $order_id . "', product_id = '" . (int) $product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int) $product['quantity'] . "', price = '" . (float) $product['price'] . "', total = '" . (float) $product['total'] . "', tax = '" . (float) $product['tax'] . "', reward = '" . (int) $product['reward'] . "',cart_key='".$this->db->escape($product['cart_key'])."',main_product_id = '" . (int) $product['main_product_id'] . "'");
                    $order_product_id = $this->db->getLastId();
                    $this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_product_id = '" . (int) $order_product_id . "'");
                    foreach ($product['option'] as $option) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_option_id = '" . (int) $option['product_option_id'] . "', product_option_value_id = '" . (int) $option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "',`group`='".(int)$option['group']."'");
                    }
                    $this->db->query("DELETE FROM " . DB_PREFIX . "order_product_toppings WHERE order_product_id = '" . (int) $order_product_id . "'");
                    foreach ($product['toppings'] as $topping) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_product_toppings SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_topping_id = '" . (int) $topping['product_topping_id'] . "', product_topping_value_id = '" . (int) $topping['product_topping_value_id'] . "', name = '" . $this->db->escape($topping['name']) . "', `value` = '" . $this->db->escape($topping['topping_value']) . "', `price` = '" . (float)$topping['price'] . "',`quantity`='".(int)$topping['quantity']."'");
                    }
                    $this->db->query("DELETE FROM " . DB_PREFIX . "order_product_dressings WHERE order_product_id = '" . (int) $order_product_id . "'");
                    foreach ($product['dressings'] as $dressing) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_product_dressings SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_dressing_id = '" . (int) $dressing['product_dressing_id'] . "', product_dressing_value_id = '" . (int) $dressing['product_dressing_value_id'] . "', name = '" . $this->db->escape($dressing['name']) . "', `value` = '" . $this->db->escape($dressing['dressing_value']) . "'");
                    }
                    $this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_product_id = '" . (int) $order_product_id . "'");
                    foreach ($product['download'] as $download) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int) ($download['remaining'] * $product['quantity']) . "'");
                    }
                    $this->db->query("DELETE FROM " . DB_PREFIX . "order_bundle WHERE order_product_id = '" . (int) $order_product_id . "'");
                    foreach ($product['bundle'] as $bundle_product) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle SET  order_product_id = '" . (int) $order_product_id . "',order_id = '" . (int) $order_id . "',bundle_id = '" . (int) $bundle_product['bundled_id'] . "',bundle_product_id = '" . (int) $bundle_product['product_id'] . "', name = '" . $this->db->escape($bundle_product['name']) . "', model = '" . $this->db->escape($bundle_product['model']) . "', quantity = '" . (int) $bundle_product['quantity'] . "'");

                    $order_bundle_product_id = $this->db->getLastId();

                    foreach ($bundle_product['option'] as $bunoption) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle_option SET order_bundle_product_id = '" . (int) $order_bundle_product_id . "', product_option_id = '" . (int) $bunoption['product_option_id'] . "', product_option_value_id = '" . (int) $bunoption['product_option_value_id'] . "', name = '" . $this->db->escape($bunoption['name']) . "', `value` = '" . $this->db->escape($bunoption['value']) . "', `type` = '" . $this->db->escape($bunoption['type']) . "'");
                    }
                    if(isset($bundle_product['toppings'])){
                        foreach ($bundle_product['toppings'] as $buntopping) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle_toppings SET order_bundle_product_id = '" . (int) $order_bundle_product_id . "', product_topping_id = '" . (int) $buntopping['product_topping_id'] . "', product_topping_value_id = '" . (int) $buntopping['product_topping_value_id'] . "', name = '" . $this->db->escape($buntopping['name']) . "', `value` = '" . $this->db->escape($buntopping['topping_value']) . "', `price` = '" . (float)$buntopping['price'] . "',`quantity`='".(int)$buntopping['quantity']."'");
                        }
                    }
                    if(isset($bundle_product['dressings'])){
                        foreach ($bundle_product['dressings'] as $bundressing) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle_dressings SET order_bundle_product_id = '" . (int) $order_bundle_product_id . "',  product_dressing_id = '" . (int) $bundressing['product_dressing_id'] . "', product_dressing_value_id = '" . (int) $bundressing['product_dressing_value_id'] . "', name = '" . $this->db->escape($bundressing['name']) . "', `value` = '" . $this->db->escape($bundressing['dressing_value']) . "'");
                        }
                    }
                }
                    
                }  
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "'");
                foreach ($totals as $total) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', text = '" . $this->db->escape($total['text']) . "', `value` = '" . (float) $total['value'] . "', sort_order = '" . (int) $total['sort_order'] . "'");
                }
                
                //(int)$this->config->get('pos_order_void_status')
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = ".(int)$this->config->get('pos_order_void_status').", notify = '0', comment = '', date_added = '" . date("Y-m-d H:i:s", time()) . "'");
                if(isset($data['live_order_id'])){
                    $this->model_epos_order->deleteLiveorder(array('live_order_id'=>$data['live_order_id'])); 
                }                 
                 $this->websocket->connect('d-'.$this->request->post['order_id']);
            }
            $json = array('method'=>'ordercancel','pushid'=>$this->request->post['pushid']);
            $this->response->setOutput($this->jsonp(json_encode($json)));
        }
    }  
    public function getLastdaytime(){
        $current_time = strtotime('now');        
        $last_days_times = $this->postcode->getLastDaysOpeningtimes(); 
        foreach ($last_days_times as $last_days_time) {
            if($current_time > strtotime($last_days_time['end'])){
                $yester_day_close_time = $last_days_time['end'];
                break;
            }
        }
         if(isset($yester_day_close_time)){
             echo $yester_day_close_time."--";
            $ordes_query =  $this->db->query("SELECT display_id FROM `" . DB_PREFIX . "order`  WHERE  date_added > '" . $yester_day_close_time . "' ORDER BY display_id DESC  LIMIT 0,1");
            if(!empty($ordes_query->rows)){
                echo '#O-'.$ordes_query->row['display_id']; 
            }else{
                //return 0;
                echo "No-0";
            }
             //echo $yester_day_close_time;
         }else{
             echo "No";
         }
         
    }
    /*
     * Enable/ Disable Onliner Ordering ,collection.delivery,etc
     */
    public function onlinesettings(){
        $json = array();
        //echo date("y-m-d H:i:a",strtotime('now'));
        //echo $this->postcode->checkisDisabledfromEpos('online');
        //die();
        //$this->log->write(print_r($this->request->post,true));
        if(isset($this->request->post)){
            $json['updatedtime'] = strtotime('now');
            $this->request->post['updatedtime'] = strtotime('now'); 
            $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'onlineorder_settings'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'onlineorder_settings', `value` = '" . $this->db->escape(serialize($this->request->post)) . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'websetting_modified'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'config', `key` = 'websetting_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");      	
             $this->websocket->connect('s-');
        }
        //$yesope = $this->postcode->getYesterdayOpeningdaytime();   
        //$this->log->write(print_r($this->request->post,true));
        $this->response->setOutput($this->jsonp(json_encode($json))); 
    }
    public function sendSMS(){
        $json = array();
        if(isset($this->request->post['message']) && isset($this->request->post['mobile'])){
            $this->load->model('tool/sms');
            $json =  $this->model_tool_sms->sendSingleSMS($this->request->post['message'],$this->request->post['mobile']);
        }
        $this->response->setOutput($this->jsonp(json_encode($json))); 
    }
    public function sendBulkSMS(){
        $json = array();
        if(isset($this->request->post['message']) && isset($this->request->post['mobiles'])){
            $this->load->model('tool/sms');
            $json =  $this->model_tool_sms->sendBulkSMS($this->request->post['message'],$this->request->post['mobiles']);
        }
        $this->response->setOutput($this->jsonp(json_encode($json))); 
    }
     public function sendPushnotification(){
        if($this->config->get('firebase_api_key')) {
        require_once DIR_SYSTEM .'vendor/fcm/fcm.php'; 
        $json = array();
        if(isset($this->request->post['message']) && isset($this->request->post['customers'])){
            $this->load->model('account/customer');
            $devicetokens = array();
            foreach ($this->request->post['customers'] as $customer_id) {
                    $customer_info = $this->model_account_customer->getCustomer($customer_id);

                    if ($customer_info) {
                            $devicetokens[] = $customer_info['fcm_token'];
                    }
            }
            if ($devicetokens) {
                $push_data = array('devicetoken'=>$devicetokens, 'title'=> (isset($this->request->post['subject'])?$this->request->post['subject']:''),'message'=>$this->request->post['message']);
                if(isset($this->request->post['image'])){
                    $push_data['image'] = $this->request->post['image'];
                }
                // $this->log->write(print_r($push_data,true));
                    $fcm = new FCM($this->config->get('firebase_api_key'));
                //$this->log->write(print_r($push_data,true));
                $json['response'] =  $fcm->send($push_data);
           }
        }
        }
        $this->response->setOutput($this->jsonp(json_encode($json))); 
    }
     public function customerstatus(){
        $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET is_blacklisted='".(int)$this->request->post['status']."',date_modified = '" . date("Y-m-d H:i:s", time()) . "'  WHERE customer_id=".(int)$this->request->post['customer_id']."");
        $json = array('method'=>'customerstatus','pushid'=>$this->request->post['pushid']);
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
     public function smsbalance(){ 
        $this->load->model('tool/sms');
        $json = $this->model_tool_sms->balance();
        $this->response->setOutput($this->jsonp(json_encode($json))); 
     }
      public function getrewardreports(){
        $json = array();
        if(isset($this->request->post)){
            $data = array();
            //$this->log->write(print_r($this->request->post,true));
            $data['filter_date_start']=date("Y-m-d H:i:s",$this->request->post['reportstarttime']);
            $data['filter_date_end']=date("Y-m-d H:i:s",$this->request->post['reportendtime']);
            $this->load->model('epos/customer');
            //$this->log->write(print_r($data,true));
            $results = $this->model_epos_customer->getTodaysRewardPoints($data);
            //$this->log->write(print_r($results,true));
            foreach ($results as $result) {
                $json[] = array(
                        'customer_id'    =>$result['customer_id'],
                        'customer'       => $result['customer'],
                        'email'          => $result['email'],
                        'customer_group' => $result['customer_group'],                       
                        'points'         => $result['points'],
                        'order_id'       => $result['order_id'],
                        'description'   =>$result['description'],
                        'total'          => $this->currency->format($result['total'], $this->config->get('config_currency')),
                        'date_added'    =>$result['date_added']
                );
            }
            //$this->log->write(print_r($json,true));
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    public function savezreport() {
        $json =array();
        //$this->log->write(print_r('push report',true));
        if(isset($this->request->post)){
            foreach($this->request->post as $index=>$value){
                if(is_array($value)){
                    $data[$index]=$value;
                }else{
                    $data[$index]=json_decode(html_entity_decode($value),true);
                }
            }
            /*
            if(is_array($this->request->post)){
                $data=$this->request->post;
            }else{
                $data=json_decode(html_entity_decode($data),true);
            }
            */
            //$this->log->write(print_r($data,true));
            $this->db->query("INSERT INTO `" . DB_PREFIX . "day_sales_report` SET report_time=" . (int)$data['report_time'].",`details`='".$this->db->escape(serialize($data))."',`system_id`='".(int)$data['system_id']."',`user_id`='".(int)$data['user_id']."'");
            //$this->config->get('');
            $json = array('pushid'=>(int)$data['pushid'],'method'=>'savezreport','success'=>'success');
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
      public function sendEmails(){
        if(isset($this->request->post))
        {
            $email_data = json_decode(html_entity_decode($this->request->post['data']),true);
            //$this->log->write(print_r($email_data,true)); 
            $message = '<html dir="ltr" lang="en">' . "\n";
            $message .= '  <head>' . "\n";
            $message .= '    <title>' . $email_data['subject'] . '</title>' . "\n";
            $message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
            $message .= '  </head>' . "\n";
            $message .= '  <body>' . html_entity_decode($email_data['message'], ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
            $message .= '</html>' . "\n";
            $store_name = $this->config->get('config_name');				
            foreach ($this->request->post['emails'] as $email) {
                $mail = new Mail();	
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->hostname = $this->config->get('config_smtp_host');
                $mail->username = $this->config->get('config_smtp_username');
                $mail->password = $this->config->get('config_smtp_password');
                $mail->port = $this->config->get('config_smtp_port');
                $mail->timeout = $this->config->get('config_smtp_timeout');				
                $mail->setTo($email);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender($store_name);
                $mail->setSubject(html_entity_decode($email_data['subject'], ENT_QUOTES, 'UTF-8'));					
                $mail->setHtml($message);
                $mail->send();
            }        
            $this->response->setOutput($this->jsonp(json_encode(array('success'=>'Email Successfully Send'))));
        }
    }
     public function updatestock() {        
        if(isset($this->request->post) && isset($this->request->post['syskey']) && isset($this->request->post['data']) && !empty($this->request->post['data'])){
            foreach ($this->request->post['data'] as $pid=>$data) {
                if(isset($data['barcode']) && $data['barcode']!=''){
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET sku='".$data['barcode']."',quantity = (quantity + " . (int) $data['qty'] . "), date_modified = NOW() WHERE product_id = '" . (int) $pid . "'");
                }else{
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity + " . (int) $data['qty'] . "), date_modified = NOW() WHERE product_id = '" . (int) $pid . "'");
}
            }            
            //update product modified settings for updae on epos restart
            $this->cache->delete('product');
            $this->cache->delete('etmlist');
            $this->response->setOutput($this->jsonp(json_encode(array('success'=>true))));
        }         
    }
    public function updatedateonlinepaymts() {
        if(isset($this->request->post['ep_id']) && isset($this->request->post['order_id']) && isset($this->request->post['status'])) {
            $this->db->query("UPDATE  " . DB_PREFIX . "epos_payonline  SET   status = '" .$this->request->post['status'] . "',order_id='".(int)$this->request->post['order_id']."' WHERE ep_id='". (int)$this->request->post['ep_id'] ."'");            
        }
        $this->response->setOutput($this->jsonp(json_encode(array('success'=>true))));
    } 
     public function liveorderstatusupdate() {
        $this->load->model('epos/order');
        $this->model_epos_order->updateLiveorderstatus($this->request->post); 
        $this->response->setOutput($this->jsonp(json_encode(array('success'=>true))));
    } 
    
    public function updatestuartdelivery() {
        $this->load->model('module/stuart');
        $this->load->model('checkout/order');
        $this->load->model('account/order');
        $order_info = $this->model_checkout_order->getOrder($this->request->post['order_id']);
        $this->log->write(print_r(array($this->request->post),true));
        if($this->request->post['action']==0){//Cancel Delivery
            $this->log->write(print_r(array('STUART ASSIGN'),true));
            $package_size = isset($this->request->post['psize'])?$this->request->post['psize']:'';  
            $pickuptime = isset($this->request->post['time'])?date("Y-m-d H:i:s", $this->request->post['time']) : date('Y-m-d H:i:s',  strtotime($order_info['requestedtime']));
            $package_description='';
            $response = $this->model_module_stuart->createJob($order_info,$package_size,$pickuptime,$package_description);
            if(isset($response['success'])){
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET date_modified = NOW() WHERE order_id = '" . (int)$this->request->post['order_id']. "'");
                $response['stuart_details']=$this->model_account_order->getStuartdetails($this->request->post['order_id']);
                $this->websocket->connect('o-'.$this->request->post['order_id']);
            }
        }else{ 
            $this->log->write(print_r(array('STUART CANCEL'),true));
            $response = $this->model_module_stuart->cancelDelivery($this->request->post['order_id']);
            if ($response){
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET date_modified = NOW() WHERE order_id = '" . (int)$this->request->post['order_id']. "'");
                $this->websocket->connect('o-'.$this->request->post['order_id']);
            }
        }
        $response['sesuccess']=true;
        $this->response->setOutput($this->jsonp(json_encode($response)));   
    }
    public function addnote() {
        if(isset($this->request->post['system_id'])){
            $data = $this->request->post;
            $this->db->query("INSERT INTO " . DB_PREFIX . "epos_notifications SET note_id='".(int) $data['note_id']."',note = '" .(isset($data['note'])?$this->db->escape($data['note']):'') ."',system_id='".(isset($data['system_id'])?$data['system_id']:0)."',order_id='".(isset($data['order_id'])?(int)$data['order_id']:0)."',added_time='".$this->db->escape(isset($data['added_time'])?date("Y-m-d H:i:s", $data['added_time']):date("Y-m-d H:i:s", time()))."',time_interval='".(isset($data['time_interval'])?(int)$data['time_interval']:0)."',user_id='".(isset($data['user_id'])?(int)$data['user_id']:0)."'");          
            $this->websocket->connect('n-');
        }
        
        $this->response->setOutput($this->jsonp(json_encode(array('success'=>true))));
    }
     public function updatenote() {
        if(isset($this->request->post['system_id'])){
            $data = $this->request->post;
            $this->db->query("UPDATE " . DB_PREFIX . "epos_notifications SET note = '" .(isset($data['note'])?$this->db->escape($data['note']):'') ."',system_id='".(isset($data['system_id'])?$data['system_id']:0)."',order_id='".(isset($data['order_id'])?(int)$data['order_id']:0)."',added_time='".$this->db->escape(isset($data['added_time'])?date("Y-m-d H:i:s", $data['added_time']):date("Y-m-d H:i:s", time()))."',time_interval='".(isset($data['time_interval'])?(int)$data['time_interval']:0)."',user_id='".(isset($data['user_id'])?(int)$data['user_id']:0)."' WHERE note_id='".(int) $data['note_id']."'");          
             $this->websocket->connect('n-');
        }
       
        $this->response->setOutput($this->jsonp(json_encode(array('success'=>true))));
    }
     public function eposkitchenitemupdate() {
        //$this->log->write(print_r($this->request->post,true));
        if(isset($this->request->post['kitchen_item_id']) && isset($this->request->post['item_status'])) {
            $this->db->query("UPDATE  " . DB_PREFIX . "kitchen_items  SET   item_status = '" . $this->request->post['item_status'] . "' WHERE kitchen_item_id='". $this->request->post['kitchen_item_id'] ."'");
            $this->websocket->connect('o-'.$this->request->post['kitchen_item_id']);
        }
         $this->response->setOutput($this->jsonp(json_encode(array('success'=>true))));
    }
    public function eposkitchenliveorderupdate() {
        if(isset($this->request->post['live_order_id']) && isset($this->request->post['track_id']) ) {// && 
            $this->db->query("UPDATE  " . DB_PREFIX . "live_orders_to_kitchen  SET   status = '" .(int) $this->request->post['status_id'] . "' WHERE live_order_id='". (int)$this->request->post['live_order_id'] ."' AND track_id='".(int)$this->request->post['track_id']."' AND system_id='".$this->request->post['system_id']."'");
            $this->websocket->connect('lo-'.$this->request->post['live_order_id']);
        }
         $this->response->setOutput($this->jsonp(json_encode(array('success'=>true))));
    }
    public function eposkitchenorderupdate() {
        if(isset($this->request->post['orderindex']) && isset($this->request->post['item_status'])) {            
           $this->log->write(print_r($this->request->post,true));
           //$this->log->write(print_r($this->config->get('pos_kitchen_item_status'),true));
            if(!isset($this->request->post['live']) || $this->request->post['live']=='0'){
                $this->db->query("UPDATE  " . DB_PREFIX . "live_orders_to_kitchen  SET status = '" . $this->request->post['item_status'] . "' WHERE order_id='". (int)$this->request->post['orderindex'] ."' AND system_id='".$this->request->post['screen_id']."'");
                $this->db->query("UPDATE  " . DB_PREFIX . "kitchen_items  SET   item_status = '" . $this->request->post['item_status'] . "' WHERE order_id='". (int)$this->request->post['orderindex'] ."' AND system_id='".$this->request->post['screen_id']."'");

                //$otherscreenqty = $this->db->query("SELECT order_product_id FROM " . DB_PREFIX . "kitchen_items  WHERE item_status = '" . $this->request->post['item_status'] . "' AND order_id='". (int)$this->request->post['orderindex'] ."' AND system_id='".$this->request->post['screen_id']."'");
                
                //$this->db->query("UPDATE  " . DB_PREFIX . "kitchen_items  SET   item_status = '" . $this->request->post['item_status'] . "' WHERE order_id='". (int)$this->request->post['orderindex'] ."' AND order_product_id IN (SELECT order_product_id FROM " . DB_PREFIX . "kitchen_items  WHERE item_status = '" . $this->request->post['item_status'] . "' AND order_id='". (int)$this->request->post['orderindex'] ."' AND system_id='".$this->request->post['screen_id']."')");
                //$this->log->write("UPDATE  " . DB_PREFIX . "kitchen_items  SET   item_status = '" . $this->request->post['item_status'] . "' WHERE order_id='". (int)$this->request->post['orderindex'] ."' AND order_product_id IN (SELECT order_product_id FROM " . DB_PREFIX . "kitchen_items  WHERE item_status = '" . $this->request->post['item_status'] . "' AND order_id='". (int)$this->request->post['orderindex'] ."' AND system_id='".$this->request->post['screen_id']."')");
                
                $order_status_id = 0;
                foreach($this->config->get('pos_kitchen_item_status') as $kitchen_statuses){
                    if($kitchen_statuses['name']==$this->request->post['item_status']){
                        if($kitchen_statuses['main_status']!='' && (int)$kitchen_statuses['main_status']>0){
                            $order_status_id = $kitchen_statuses['main_status'];
                        }                    
                        break;
                    }
                }
                if($order_status_id!=0){ 
                    //$this->log->write(print_r(array('order_status_id'=>$order_status_id),true));
                    $uncompletde_qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "live_orders_to_kitchen  WHERE status!= '" . $this->request->post['item_status'] . "' AND order_id='". (int)$this->request->post['orderindex'] ."'");
                    if(empty($uncompletde_qry->rows)){
                        $this->load->model('checkout/order');
                        $this->model_checkout_order->changeOrderStatus((int)$this->request->post['orderindex'] ,array('order_status_id'=>(int)$order_status_id,'notify'=>0,'comment'=>''));
                    }                
                } 
            }else{
                $this->db->query("UPDATE  " . DB_PREFIX . "live_orders_to_kitchen  SET status = '" . $this->request->post['item_status'] . "' WHERE live_order_id='". (int)$this->request->post['orderindex'] ."' AND system_id='".$this->request->post['screen_id']."'");
                $this->db->query("UPDATE  " . DB_PREFIX . "kitchen_items  SET   item_status = '" . $this->request->post['item_status'] . "' WHERE live_order_id='". (int)$this->request->post['orderindex'] ."' AND system_id='".$this->request->post['screen_id']."'");
            }
                       
            $this->websocket->connect('o-'.$this->request->post['orderindex']);
        }
         $this->response->setOutput($this->jsonp(json_encode(array('success'=>true))));
    }
    
     public function getkitchenitemdatas(){ 
        $json = array();
        if(isset($this->request->get['order_id'])){
           $dbquery = $this->db->query("SELECT * FROM  " . DB_PREFIX . "kitchen_items  WHERE order_id  = '" . $this->request->get['order_id'] . "'");
           if($dbquery->rows) {
               foreach($dbquery->rows as $liveitemdata){
                   if(!isset($json[$liveitemdata['system_id']])){
                       //$json[$liveitemdata['system_id']]=array();
                   }
                   $data = unserialize($liveitemdata['item']);
                   $data['item_status']=$liveitemdata['item_status'];
                   $data['kitchen_item_id']=$liveitemdata['kitchen_item_id'];
                   //$json[$liveitemdata['system_id']][]=$data;
                   $json[$liveitemdata['order_product_id']]=$data;
               }
           }
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    public function getlivekitchenitemdatas(){ 
        $json = array();
        if(isset($this->request->get['live_order_id'])){
           $dbquery = $this->db->query("SELECT * FROM  " . DB_PREFIX . "kitchen_items  WHERE live_order_id  = '" . $this->request->get['live_order_id'] . "'");
           if($dbquery->rows) {
               foreach($dbquery->rows as $liveitemdata){
                   $data = unserialize($liveitemdata['item']);
                   $data['item_status']=$liveitemdata['item_status'];
                   $data['kitchen_item_id']=$liveitemdata['kitchen_item_id'];
                   //$json[$liveitemdata['system_id']][]=$data;
                   $json[$liveitemdata['item_index']]=$data;
               }
           }
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    public function deletenote() {
        if(isset($this->request->post['system_id'])){
            $this->db->query("DELETE FROM  " . DB_PREFIX . "epos_notifications WHERE note_id='".(int) $this->request->post['note_id']."'");            
             $this->websocket->connect('n-');
        }
        $this->response->setOutput($this->jsonp(json_encode(array('success'=>true))));
    }
   
   
    public function eposkitchencourseupdate(){
        //live order id, course id
        
        $json['success'] ='OK';
        $this->response->setOutput($this->jsonp(json_encode($json))); 
        
    }
    public function trackkey() {
        $this->load->model('module/key_tracker');
       $this->log->write(print_r($this->request->post,true));
        $trckdata = array(
            'key_hash'    => md5($this->config->get('config_browserapp_api')),
            'provider'    =>$this->request->post['provider'],
            'subsystem'   => $this->request->post['subsystem']
        );
        $this->model_module_key_tracker->log($trckdata); 
        $json = array('success'=>true,'method'=>'trackkey','pushid'=>$this->request->post['pushid']);
        $this->response->setOutput($this->jsonp(json_encode($json)));
        
    }
}