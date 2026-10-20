<?php
/* VERSION 5 ionic 3 */
class ControllerModuleWaiterapp5 extends Controller {
    private $error = array();
    private function jsonp($json, $encode = false) {
        $this->securitycheck();
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
        /// $this->log->write("WAITER APP INDEX");
        echo "Waiterapp 5";
        //die();
    }
    
    private function securitycheck(){
         //$this->log->write("WAITER SCHCE");
        if ($this->config->get('config_waiterapp_security_key')) {
            $security_key = $this->config->get('config_waiterapp_security_key');
            $pos = strpos($this->request->server['HTTP_USER_AGENT'],$security_key );
            if ($pos === false) {
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
            $this->request->server['HTTP_USER_AGENT'] = str_replace($security_key, '', $this->request->server['HTTP_USER_AGENT']);
        }
                
    }   

    public function getProductsIDs() {
        $this->load->model('catalog/product');
        $json['proIDS'] = $this->model_catalog_product->getAllProductIDs();
        $pro_last_modified = $this->model_catalog_product->getLastProductyModified();
        $json['bundled_combined_ids'] = $this->model_catalog_product->getBundledCombinedIDs();

        $this->load->model('catalog/category');
        $cat_last_modified = $this->model_catalog_category->getLastCategoryModified();

        $json['last_modified'] = (($cat_last_modified < $pro_last_modified ) ? $pro_last_modified: $cat_last_modified);

        $catg_query = $this->db->query("SELECT count(*) catg_cnt FROM " . DB_PREFIX . "category WHERE status = '1'");
        $json['totalCategories'] = $catg_query->row['catg_cnt'];
        $this->response->setOutput($this->jsonp($json, true));
    }

    public function getLastModifiedTime(){
         //$this->log->write("WAITER APP LTIM");
        $this->load->model('catalog/product');
        $pro_last_modified = $this->model_catalog_product->getLastProductyModified();
        
        $this->load->model('catalog/category');
        $cat_last_modified = $this->model_catalog_category->getLastCategoryModified();
        $prod_cnt = count($this->model_catalog_product->getAllProductIDs());

        $json['last_modified'] = (($cat_last_modified < $pro_last_modified ) ? $pro_last_modified: $cat_last_modified);
        $json['totalProducts'] = $prod_cnt;

        $catg_query = $this->db->query("SELECT count(*) catg_cnt FROM " . DB_PREFIX . "category WHERE status = '1'");
        $json['totalCategories'] = $catg_query->row['catg_cnt'];

        $this->response->setOutput($this->jsonp($json, true));
    }

    public function getLastModified() {
         //$this->log->write("WAITER APP LAST");
        $json = array();
        $json['mobile_syncmenu'] = $this->config->get('config_mobile_syncmenu');
        $this->load->model('catalog/category');
        $cat_last_modified = $this->model_catalog_category->getLastCategoryModified();
        $this->load->model('catalog/product');
        $pro_last_modified = $this->model_catalog_product->getLastProductyModified();
        $json['last_modified'] = (($cat_last_modified < $pro_last_modified ) ? $pro_last_modified: $cat_last_modified);
        $this->response->setOutput($this->jsonp($json, true));
    }

    public function login() {
        //$this->log->write("WAITER APP LOGIN");
        $json = array();
        $json['request'] = $_REQUEST;
        if (isset($this->request->post['email']) and isset($this->request->post['password'])) {
            $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($this->request->post['email'])) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($this->request->post['password']) . "'))))) OR password = '" . $this->db->escape(md5($this->request->post['password'])) . "') AND status = '1' AND approved = '1'");
            if ($customer_query->num_rows) {
                if(isset($this->request->post['fcm_token']) && $this->request->post['fcm_token']) {
                    $this->db->query("UPDATE " . DB_PREFIX . "customer SET fcm_token = '" . $this->db->escape($this->request->post['fcm_token']) . "', user_agent ='" . $this->db->escape('APP:'.$this->request->server['HTTP_USER_AGENT']) . "' WHERE customer_id = '" . (int) $customer_query->row['customer_id'] . "'");
                    $customer_query->row['fcm_token'] = $this->request->post['fcm_token'];
                }
                $json['success'] = array('customer_id' => $customer_query->row['customer_id'], 'address_id' => $customer_query->row['address_id'],
                    'fcm_token'=>$customer_query->row['fcm_token'],'subscribe_promotional_notifications'=>$customer_query->row['subscribe_promotional_notifications'],'notifications'=>$customer_query->row['notifications']);
            } else {
                $json['error'] = 'No match for E-Mail Address and/or Password';
            }
        }
        else {
            $json['error'] = 'Wrong Parameter';
        }
        //$this->log->write("WAITER APP RESPONSE");
        //$this->log->write(print_r($json,true));
        $this->response->setOutput($this->jsonp(json_encode($json)));
    } 

   
    

    /*
     * Send Order
     *
     * param @  customer_id,address_id ,products = array(),comment ,system_id
     * return @ json = array(success=>) // else // offers / messages /etc
     */

    public function sendorder() {
        if($this->config->get('config_waiterapp_sync')){
            $this->log->write(print_r($this->request->post,true));
            //$this->log->write("EPOS SYNC - ");;
            $this->session->data['cart'] = array();
            unset($this->session->data['cart']);
            if (isset($this->request->post['products'])) { 
                $POST = $this->request->post;  
                $products = json_decode(htmlspecialchars_decode($this->request->post['products']),true);
                foreach ($products as $product) {                
                    if(is_array($product)) {
                        $output = $product;
                    }
                    else {
                        parse_str($product, $output);
                    }
                    $this->request->post = $POST + $output; // + $this->request->get;
                    $this->getChild('checkout/cart/add');
                }
            }

            $cart_products = $this->cart->getProducts();
            //$this->log->write(print_r($cart_products,true));
            //$this->log->write(print_r($this->cart->getProducts(),true));
            //return;
            //

            //return;
            $live_items = array();;
            $track_data = array();
            $formateditems = array();
            $item_index = 0; //$live_data['itemindex']
            $track_index = 0;
            $live_data = array();
            $live_qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "live_orders` WHERE `dinein_table_id`=".(int)$this->request->post['address_id']." ORDER BY `date_added` DESC LIMIT 1");

            if(!empty($live_qry->row)){            
                $previous_data = json_decode(zlib_decode(base64_decode($live_qry->row['data'])),true);
                $track_index = $previous_data['currenttrack'];
                $item_index  = $previous_data['itemindex'];
                $track_data = $previous_data['track'];
                $live_items = $previous_data['items'];
                $formateditems = $previous_data['formateditems'];
                if(isset($previous_data['order_id'])){
                    $live_data['display_id']=$previous_data['display_id'];
                    $live_data['order_id']=$previous_data['order_id'];
                }
                $live_order_id = $live_qry->row['live_order_id'];
                $live_data['live_order_id'] = $live_order_id;
                $live_data['pushid'] = $previous_data['pushid'];
                $live_data['customer']=$previous_data['customer'];

                
            }
            else{
                $live_order_id =(int)(rand(10, 80).time().rand(100, 990));
                $live_data['live_order_id'] =$live_order_id;//rand(10, 99).time().rand(1001, 9999);
                $live_data['pushid'] =$live_order_id;
                $live_data['currency']=$this->currency->getSymbolLeft();
                $live_data['customer']=array(); 
            }

            $live_data['lock'] = 0;
            $live_data['dinein_table_id']=(int)$this->request->post['address_id'];
            $live_data['table_id']=(int)$this->request->post['address_id'];
            $live_data['dinein_table_name']=$this->request->post['table_name']; //REPLACE
            $live_data['seletcedmenu'] = $this->config->get('config_waiterapp_menu') ;//Get From Mdoule  
            if($this->config->get('config_waiterapp_menu')){
                //$live_data['seletcedmenu'] =$this->config->get('config_waiterapp_menu');
            }
            

            //FINDING
            //currenttrack , totalitemcount = cartitemcount, itemindex, currentitem , track , formateditems , items , seletcedmenu ,  

            //FOR MAIN DEAL
            //bundle_data = [bundle_id][bundleidval]={item_index:$scope.cart.currentitem,qty:1}
            //FOR IN DEAL
            //parent_item_index  ,, extradealprice


            $itemqty = 0;
            foreach ($cart_products as $product) {     
                $itemqty +=$product['quantity'];
                $option_data = array();           
                
                foreach ($product['option'] as $option) {
                    $optdata = array(
                        'item_index'=>$item_index,
                        'main_index'=>$item_index,
                        'product_option_value_id'=>$option['product_option_value_id'],
                        'name'=>$option['option_value'],
                        'qty'=>'',
                        'price_value'=>(($option['type']=='textarea' || $option['type']=='text')?0:$option['price']),
                        'total_value'=>(($option['type']=='textarea' || $option['type']=='text')?0:$option['price']),//CHECK
                        'class'=>'',
                        'itemtype'=>'optionvalue',
                        'option_name'=>$option['name'],
                        'product_option_id'=>$option['product_option_id'],
                        'option_id'=>$option['option_id'],
                        'option_value_id'=>$option['option_value_id'],
                        'shortcode'=>$option['shortcode'],
                        'type'=>$option['type'],
                        'group'=>0,
                    );
                    if($option['type']=='checkbox'){
                        $option_data[$option['product_option_id']]=array($option['product_option_value_id']=>$optdata);
                    }else{
                        $option_data[$option['product_option_id']]=$optdata;
                    }
                }   
               
                 //dressings
                $dressing_data = array();
                if(!empty($product['dressings'])){
                    foreach($product['dressings'] as $dressing){
                        $dressdata = array(
                            'item_index'=>$item_index,
                            'main_index'=>$item_index,
                            'product_id'=>$product['product_id'],
                            'dressing_id'=>$dressing['dressing_id'],
                            'dressing_value_id'=>$dressing['dressing_value_id'],
                            'name'=>$dressing['dressing_value'],
                            'qty'=>'',//CHECK
                            'total_value'=>0,
                            'price_value'=>0,
                            'class'=>'',
                            'dressingname'=>$dressing['name'],
                            'btn'=> isset($dressing['btnnamename'])?$dressing['btnnamename']:'',//swap button name
                            'btnshort'=>'',//swap button shortcode
                            'itemtype'=>'dressing',
                            'shortcode'=>(isset($dressing['shortcode']))?$dressing['shortcode']:''                        
                        );
                        if(!isset($dressing_data[$dressing['dressing_id']])){
                            $dressing_data[$dressing['dressing_id']]=array();
                        }
                        $dressing_data[$dressing['dressing_id']][$dressing['dressing_value_id']]=$dressdata;
                    }
                }
                //toppings
                $topping_data = array();
                if(!empty($product['toppings'])){
                    foreach($product['toppings'] as $topping){
                        $topngdata = array(
                            'item_index'=>$item_index,
                            'main_index'=>$item_index,
                            'product_id'=>$product['product_id'],
                            'topping_id'=>$topping['topping_id'],
                            'topping_value_id'=>$topping['topping_value_id'],
                            'name'=>$topping['topping_value'],
                            'qty'=>$topping['quantity'],//CHECK
                            'price'=>$topping['price'],
                            'total_value'=>0,
                            'price_value'=>$topping['price'],
                            'class'=>'',
                            'toppingname'=>$topping['name'],
                            'btn'=>'',//swap button name
                            'btnshort'=>'',//swap button shortcode
                            'itemtype'=>'topping',
                            'shortcode'=>(isset($topping['shortcode']))?$topping['name']:''                        
                        );
                        if(!isset($topping_data[$topping['topping_id']])){
                            $topping_data[$topping['topping_id']]=array();
                        }
                        $topping_data[$topping['topping_id']][$topping['topping_value_id']]=$topngdata;
                    } 
                }
                
                $live_items[$item_index]= array(
                    'item_index'=>$item_index, 
                    'main_index'=>$item_index,
                    'product_id'=>$product['product_id'],
                    'name'=>$product['name'],
                    'price'=>(isset($product['unit_price']))?$product['unit_price']:$product['price'],
                    'price_value'=>(isset($product['unit_price']))?$product['unit_price']:$product['price'],
                    'qty'=>$product['quantity'],  
                    'total_value'=>$product['total'],
                    'unit_price'=>$product['price'],
                    'class'=>'',
                    'itemtype'=>"product",
                    'option'=>$option_data,
                    'toppings'=>$topping_data,
                    'dressings'=>$dressing_data,
                    'optioncompleted'=>true,
                    'shortcode'=>$product['shortcode'],
                    'trackindex'=>$track_index,                     
                 );            
                
                $formateditems[] = $live_items[$item_index];
                if(!isset($track_data[$track_index])){
                    $track_data[$track_index] =array('added'=>array(),'deleted'=>array());
                }

                $track_data[$track_index]['added'][$item_index] =array('item_index'=>$item_index,'added_qty'=>$product['quantity']);             
                 $item_index++;
                        
                if($product['bundled_data'] && !empty($product['bundled_data'])){
                    $parent_item_index = $item_index -1;
                    $bundle_data = array();
                    $bundleitems = array();
                    $bval=0;
                    foreach($product['bundled_data'] as $bundledata){
                        if(!isset($bundle_data[$bundledata['bundled_id']])){
                            $bundle_data[$bundledata['bundled_id']] = array();
                        }
                        $bundle_data[$bundledata['bundled_id']][] =array('item_index'=>$item_index,'qty'=>$bundledata['quantity']);
                        //extradealprice   parent_item_index  
                        //bundled_id
                        
                        $option_data = array();                           
                        foreach ($bundledata['option'] as $option) {
                            $optdata = array(
                                'item_index'=>$item_index,
                                'main_index'=>$item_index,
                                'product_option_value_id'=>$option['product_option_value_id'],
                                'name'=>$option['option_value'],
                                'qty'=>'',
                                'price_value'=>$option['price'],
                                'total_value'=>$option['price'],//CHECK
                                'class'=>'',
                                'itemtype'=>'optionvalue',
                                'option_name'=>$option['name'],
                                'product_option_id'=>$option['product_option_id'],
                                'option_id'=>$option['option_id'],
                                'option_value_id'=>$option['option_value_id'],
                                'shortcode'=>$option['shortcode'],
                                'type'=>$option['type'],
                                'group'=>0,
                            );
                            if($option['type']=='checkbox'){
                                $option_data[$option['product_option_id']]=array($option['product_option_value_id']=>$optdata);
                            }else{
                                $option_data[$option['product_option_id']]=$optdata;
                            }
                        }   
                        
                        $live_items[$item_index]= array(
                            'bundle_id'=>$bundledata['bundled_id'],
                            'bundleidval'=>$bval,
                            'class'=>'',
                            'item_index'=>$item_index,
                            'itemtype'=>"product",
                            'main_index'=>$item_index,
                            'name'=>$bundledata['name'],
                            'option'=>$option_data,
                            'toppings'=>array(),
                            'dressings'=>array(),
                            'optioncompleted'=>true,
                            'price'=>$bundledata['price'],
                            'price_value'=>$bundledata['price'],
                            'product_id'=>$bundledata['product_id'],
                            'qty'=>$bundledata['quantity'],
                            'shortcode'=>$bundledata['shortcode'],
                            'total_value'=>0.00,
                            'trackindex'=>$track_index,
                            'unit_price'=>0.00,
                            'extradealprice'=>0,
                            'parent_item_index'=>$parent_item_index
                        );            
                        $bundleitems[] = $live_items[$item_index];
                        if(!isset($track_data[$track_index])){
                            $track_data[$track_index]=array('added'=>array(),'deleted'=>array());
                        }
                        $track_data[$track_index]['added'][$item_index] = array($item_index=>array('item_index'=>$item_index,'added_qty'=>$bundledata['quantity']));
                        $item_index++;  
                        $bval++;
                        
                    }
                    $live_items[$parent_item_index]['bundle_data'] = $bundle_data;
                    $formateditems[count($formateditems)-1]['bundle_data'] = $bundle_data;
                    $formateditems[count($formateditems)-1]['bundle_items'] =$bundleitems;
                }
            }
            
            //$this->log->write("LIVE ITEMS");
            
            if(isset($this->request->post['adhoc'])){
                //$this->log->write(print_r($this->request->post['adhoc'],true));
                $adhoc_items = json_decode(htmlspecialchars_decode($this->request->post['adhoc']),true);
                //$this->log->write(print_r($adhoc_items,true));
                foreach($adhoc_items as $adhoc_data){
                    $itemqty +=$adhoc_data['qty'];
                    $live_items[$item_index]= array(
                        'item_index'=>$item_index, 
                        'main_index'=>$item_index,
                        'product_id'=>($this->config->get('adhocproduct_product_id'))?$this->config->get('adhocproduct_product_id'):100000,
                        'name'=>$adhoc_data['name'],
                        'price'=>$adhoc_data['price'], 
                        'price_value'=>$adhoc_data['price'],
                        'qty'=>$adhoc_data['qty'],  
                        'total_value'=>$adhoc_data['price']*$adhoc_data['qty'],
                        'unit_price'=>$adhoc_data['price'],
                        'class'=>'',
                        'itemtype'=>"product",
                        'option'=>array(),
                        'toppings'=>array(),
                        'dressings'=>array(),
                        'optioncompleted'=>true,
                        'shortcode'=>'',
                        'trackindex'=>$track_index,                     
                     );                            
                    $formateditems[] = $live_items[$item_index];
                    if(!isset($track_data[$track_index])){
                        $track_data[$track_index] =array('added'=>array(),'deleted'=>array());
                    }
                    $track_data[$track_index]['added'][$item_index] =array('item_index'=>$item_index,'added_qty'=>$adhoc_data['qty']);             
                    $item_index++;                    
                }
            }



            $live_data['currenttrack']=$track_index+1;
            $live_data['itemindex'] = $item_index;
            $live_data['currentitem']=$item_index-1;
            $live_data['items']=$live_items;      
            $live_data['totals'] =array();


            $live_data['formateditems'] = $formateditems;

            $live_data['order_payed']=0;       
            $live_data['order_tme'] = date('Y-mm-dd H:i:s',time());
            $live_data['lasteditedtime'] = time()*1000;
            $live_data['date_modified']=time()*1000;
            $live_data['savedorder']=true;
            
            $live_data['app_order'] = true;
            
            $live_data['totalitemcount']= $live_data['cartitemcount']=$itemqty;

            $live_data['track']=$track_data;
            
            //comment
            if(isset($this->request->post['comment']) && trim($this->request->post['comment'])!=''){ 
                $live_data['comment'] =array(0=>$this->request->post['comment']);
            }       
            $live_data['shipping_method']='DINE';
            $live_data['shipping_code']='dine.dine';
            $this->log->write(print_r($live_data,true));
            
            //$this->log->write("SAVEEE");

            $this->load->model('epos/order');
            $data = array(
                'table_id'=>(int)$this->request->post['address_id'],
                'data'=>base64_encode(zlib_encode(json_encode($live_data),ZLIB_ENCODING_DEFLATE)),
                'lock'=>0,
                'system_id'=>0,
                'live_order_id'=>(isset($live_order_id))?$live_order_id:$live_data['live_order_id']
            );
            //$this->log->write(print_r($data,true));
            $this->model_epos_order->updateLiveorder($data); 
            $this->websocket->connect('l');

            //       

            $json['success'] = true;
            $this->response->setOutput($this->jsonp(json_encode($json)));
        }
        else{
            //$this->log->write("NEW ORDER");;
            $this->customer->loginID($this->request->post['customer_id']);
            //$this->log->write(print_r($this->request->post, true));
            $this->load->model('checkout/order');
            $this->load->model('epos/order');
            $this->load->model('account/address');
            $this->session->data['cart']=array();
            $this->session->data = array();
            unset($this->session->data);
            $POST = $this->request->post;  
            $json = array();
            $data= array();
            $customer_address = $this->model_account_address->getAddress($this->customer->getAddressId());
            if(isset($this->request->post['table_status'])){
                $this->db->query("INSERT INTO `" . DB_PREFIX . "store_tables` SET name='".$this->request->post['address_id']."',image='',settings=''");
                $data['dinein_table_id'] = $this->db->getLastId();
                $table_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store_tables`");
                if(!empty($table_query->rows)){
                    foreach ($table_query->rows as $row) {
                        $json['store_tables'][]=array('table_id'=>$row['table_id'],'name'=>$row['name']);
                    }
                }  
            }else{
                $data['dinein_table_id'] = $this->request->post['address_id'];
            }
            if (isset($this->request->post['products'])) {
                $products = json_decode(htmlspecialchars_decode($this->request->post['products']),true);
                foreach ($products as $product) {                
                    if(is_array($product)) {
                        $output = $product;
                    }
                    else {
                        parse_str($product, $output);
                    }
                    $this->request->post = $POST + $output; // + $this->request->get;
                    $this->getChild('checkout/cart/add');
                }
            }
            //check it is send to epos Save Full Product data if it is send to epos only
                //TODO
                //TODO
                //TODO
                //TODO
            //else save order
            //$this->log->write("SDWW");
            $total_data = array();
            $total = 0;
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
            $sort_order = array();
            foreach ($total_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }
            array_multisort($sort_order, SORT_ASC, $total_data);

            //$this->log->write("NVVSS");
            /*
            $free_items = json_decode($this->getChild('module/freeproducts/mobileapp'));  
            if(!empty($free_items) && isset($free_items['offer_products']) && !empty($free_items['offer_products'])){
                $json = array();
                $json['freeproducts'] =$free_items;
            }
            */
            //$this->log->write("LATSE");
            if(isset($json['freeproducts'])){
                $this->response->setOutput($this->jsonp(json_encode($json)));
            }else{
                foreach ($this->cart->getcartProducts() as $key=>$product) {
                    $option_data = array();
                    foreach ($product['option'] as $option) {
                        if ($option['type'] != 'file') {
                            $value = $option['option_value'];
                        } else {
                            $value = $this->encryption->decrypt($option['option_value']);
                        }

                        $option_data[] = array(
                            'product_option_id' => $option['product_option_id'],
                            'product_option_value_id' => $option['product_option_value_id'],
                            'option_id' => $option['option_id'],
                            'option_value_id' => $option['option_value_id'],
                            'name' => $option['name'],
                            'shortcode'=>$option['shortcode'],
                            'value' => $value,
                            'type' => $option['type']
                        );
                    }
                    $productbundle = array();
                    $product_data[] = array(
                        'cart_key'=>$key,
                        'product_id' => $product['product_id'],
                        'name' => $product['name'],
                        'model' => $product['model'],
                        'option' => $option_data,
                        'download' => $product['download'],
                        'quantity' => $product['quantity'],
                        'subtract' => $product['subtract'],
                        'price' => $product['price'],
                        'total' => $product['total'],
                        'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),                    
                        'reward' => 0,//(($consider_product) ? ($total_rewads + $product['reward']) : $total_rewads),
                        'bundle' => $product['bundled_data'],
                        'toppings'=>$product['toppings'],
                        'dressings'=>$product['dressings'],
                    );
                }
                $data['store_id'] =0;
                $data['store_url']='';
                $data['store_name'] = trim(str_replace('"', '', html_entity_decode($this->request->post['store_name']))); 
                $data['language_id'] = $this->config->get('config_language_id');
                $data['currency_id'] = $this->currency->getId();
                $data['currency_code'] = $this->currency->getCode();
                $data['currency_value'] = $this->currency->getValue($this->currency->getCode());
                //set customer data
                $data['customer_id'] = $this->customer->getId();
                $data['customer_group_id'] = $this->customer->getCustomerGroupId();
                $data['firstname'] = $this->customer->getFirstName();
                $data['lastname'] = $this->customer->getLastName();


                $data['shipping_firstname'] = $customer_address['firstname'];
                $data['shipping_lastname'] = $customer_address['lastname'];
                $data['shipping_address_1']='TABLE#'.$this->request->post['table_name'];
                $data['shipping_address_2']='';
                $data['shipping_city']='';
                $data['shipping_postcode']='';

                $data['payment_firstname'] = $customer_address['firstname'];
                $data['payment_lastname'] = $customer_address['lastname'];
                $data['payment_address_1']='TABLE#'.$this->request->post['table_name'];
                $data['payment_address_2']='';
                $data['payment_city']='';
                $data['payment_postcode']='';

                 $data['shipping_method']='DINE';
                $data['shipping_code']='dine.dine';

                $data['comment']=$this->request->post['comment'];
                //$data['user_agent']='WAITER APP';
                $data['system_id']='';
                $data['order_from']=3;//3 for waiter app
                $data['products'] = $product_data;
                $data['totals']=$total_data;
                $data['total']=$total;//$total_data['total'][0]['value']; 
                //$data['total'] = $total;
                $data['vouchers']=array();
                $data['order_status_id'] =$this->config->get('config_order_status_id');

                //$this->log->write("WEEEE");
                //$this->log->write(print_r($data,true));
                $order_id = $this->model_checkout_order->addOrder($data);
                $this->model_epos_order->confirm($order_id,$data['order_status_id']);
                //$this->log->write("LOPP");
                $json['success'] = true;
                $this->response->setOutput($this->jsonp(json_encode($json)));
            }
        }       
    }
  
    public function getcustomerdetails() {
        //$this->customer->loginID($this->request->post['customer_id']);
        $json = array();
        $this->load->model('account/customer');
        $json = $this->model_account_customer->getCustomer($this->request->post['customer_id']);
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

   
    public function changepassword() {
            $json = [];
          if (isset($this->request->post['email']) and isset($this->request->post['password'])) {
              // $this->log->write(print_r($this->request->post,true));
            $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($this->request->post['email'])) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($this->request->post['password']) . "'))))) OR password = '" . $this->db->escape(md5($this->request->post['password'])) . "') AND status = '1' AND approved = '1'");
            if ($customer_query->num_rows) {
               
                $this->customer->loginID($this->request->post['customer_id']);
                $this->load->model('account/customer');
                $this->model_account_customer->editPassword($this->customer->getEmail(), $this->request->post['new_password']);
               // $this->log->write($this->customer->getEmail());
              //  $this->log->write($this->request->post['new_password']); 
                $json['success'] = 'ok';
                            
            } else {
                $json['error'] = 'No match for E-Mail Address and/or Password';
            }
        }
        else {
            $json['error'] = 'Wrong Parameter';
        }
        $this->response->setOutput($this->jsonp(json_encode($json)));
        /*
            $this->customer->loginID($this->request->post['customer_id']);
            $this->load->model('account/customer');
            $this->model_account_customer->editPassword($this->customer->getEmail(), $this->request->post['password']);
            $json['success'] = 'ok';
            $this->response->setOutput($this->jsonp(json_encode($json)));
        */
    }

    /*
     * Customer All Adress
     *
     * params @ email
     * return @ json = array
     */

    public function forgottenPassword() {
        $json = array();
        $this->load->model('account/customer');
        $this->language->load('account/forgotten');
        if (!isset($this->request->post['email'])) {
		    $this->error['warning'] = $this->language->get('error_email');
        } elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
		    $this->error['warning'] = $this->language->get('error_email');
	    }

        if (isset($this->error['warning'])) {
                $json['error_warning'] = $this->error['warning'];
        } else {
                //$this->getChild('account/forgotten');
            $this->language->load('mail/forgotten');
            $password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);
            $this->model_account_customer->editPassword($this->request->post['email'], $password);

            $subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

            $message  = sprintf($this->language->get('text_greeting'), $this->config->get('config_name')) . "\n\n";
            $message .= $this->language->get('text_password') . "\n\n";
            $message .= $password."\n\n";
            $message .=$this->language->get('text_thanks')."\n";
            $message .=$this->config->get('config_name');

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($this->request->post['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($this->config->get('config_name'));
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
            $mail->send();

            //$this->session->data['success'] = $this->language->get('text_success');
            $json['text_message'] = $this->language->get('text_message');
           // $json['error_warning'] = '';
        }

        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    
    public function getstoreinfo() {
        $json = array();
        $json['name'] = $this->config->get('config_name');
        $json['owner'] = $this->config->get('config_owner');
        $json['address'] = $this->config->get('config_address');
        $json['email'] = $this->config->get('config_email');
        $json['telephone'] = $this->config->get('config_telephone');
        $json['postalcode'] = $this->config->get('config_postalcode');
        $json['coordinates'] = $this->postcode->getPostcodeLag($json['postalcode']);       
        $json['store_url'] = HTTP_SERVER;
        $json['store_tables']= $this->getstoretabledatas();
        /*
        $table_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store_tables`");
        if(!empty($table_query->rows)){
            foreach ($table_query->rows as $row) {
                $json['store_tables'][]=array('table_id'=>$row['table_id'],'name'=>$row['name']);
            }
        }   
        */
        $query = $this->db->query("SELECT ch.comment FROM " . DB_PREFIX . "comment_history ch WHERE ch.comment!='' AND ch.is_profile_comment=0 ORDER BY priority_id DESC");
        $json['comment_histories'] =  $query->rows;
        $this->response->setOutput($this->jsonp(json_encode($json)));
        
    }
    
     public function getstoretables() {
        $store_tables=$this->getstoretabledatas();         
        $this->response->setOutput($this->jsonp(json_encode($store_tables)));
    }
    
    private function getstoretabledatas(){
        $table_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store_tables`");
        $table_datas = array();
        if(!empty($table_query->rows)){
            foreach ($table_query->rows as $row) {
                //$json['store_tables'][]=array('table_id'=>$row['table_id'],'name'=>$row['name']);
                 $live_qry = $this->db->query("SELECT DISTINCT `live_order_id`,`lock`,`dinein_table_id` FROM `" . DB_PREFIX . "live_orders` WHERE `dinein_table_id`=".(int)$row['table_id']."");
                if(empty($live_qry->row)){
                    $table_datas[]=array('table_id'=>$row['table_id'],'name'=>$row['name'],'status'=>0);
                }else{
                    $table_datas[]=array('table_id'=>$row['table_id'],'name'=>$row['name'],'status'=>1,'lock'=>$live_qry->row['lock'],'covers'=>'');
                }   
            }
        }   
        return $table_datas;
    }
    /*
     *
     */

    public function getMenus() {
        $json =array();
        //$json = $this->cache->get('mobile.menus.' . (int) $this->config->get('config_language_id'));
        
        //if (!$json) {
            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            $json = array();
            $json['cat_last_modified'] = $this->model_catalog_category->getLastCategoryModified();

            $results = $this->model_catalog_category->getCategories(0);
            $parent_id = $this->config->get('config_waiterapp_menu');//1;//$results[0]['category_id']; 

            $json['menus'] = $this->getmenu($parent_id);//
            //$this->cache->set('mobile.menus.' . (int) $this->config->get('config_language_id'), $json);
	//}
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    private function getmenu($parent_id) {
        $category = array();
        $results = $this->model_catalog_category->getAllCategories($parent_id);
        if ($results) {
            $in_cats=array();
            foreach ($results as $result) {
                if ($result['status'] && !in_array($result['category_id'],$in_cats)) {
                    if ($result['mobile_app_image']) {
                        $image = $this->model_tool_image->resize($result['mobile_app_image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
                    } else {
                        $image = false;
                    }
                    $in_cats[]=$result['category_id'];
                    // Products
                    $data = array(
                        'filter_category_id' => $result['category_id'],
                        'start' => 0,
                        'limit' => 500
                    );

                    $products = array();
                    $products_sets = $this->model_catalog_product->getProducts($data);
                    foreach ($products_sets as $product) {
                        $products[] = $product['product_id'];
                    }

                    $category[] = array(
                        'category_id' => $result['category_id'],
                        'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                        'description' => strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),
                        'image' => $image,
                        'parent_id' => $result['parent_id'],
                        'categories' => $this->getmenu($result['category_id']),
                        'products' => $products
                    );
                }
            }
            return $category;
        } 
        else {
            return NULL;
        }
    }
    public function getProduct() {
        
     //   $this->request->post += $this->request->get;     
        $this->request->get['product_id'] = $this->request->post['product_id'];
        $json = array();
        // echo "aaa";
       if (isset($this->request->get['product_id'])) {
            //echo "ssss";die();
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            $product_id = $this->request->get['product_id'];
            //echo "aaa";die();

            $product_info = $this->model_catalog_product->getProduct($product_id);
            $option_data = array();
            foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                    $option_value_data = array();

                    foreach ($option['option_value'] as $option_value) {
                        
                        if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                            $option_value_data[] = array(
                                'product_option_value_id' => $option_value['product_option_value_id'],
                                'option_value_id' => $option_value['option_value_id'],
                                'name' => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
                                'shortcode'=> isset($option_value['shortcode'])?strip_tags(html_entity_decode($option_value['shortcode'], ENT_QUOTES, 'UTF-8')):'',
                                //'image' => $image,
                                'price' => (float) $option_value['price'] ? $this->currency->format($option_value['price']) : false,
                                'price_prefix' => $option_value['price_prefix'],
                                'price_value' => ($option_value['price_prefix']=='-' ? -1 : 1 ) * (float) $option_value['price'] ? $this->currency->format($option_value['price'],'','',false) : 0,
                                'master_option_value'=>$option_value['master_option_value']
                            );
                        }
                    }
                    //$option_value['price_prefix']
                    $option_data[] = array(
                        'product_option_id' => $option['product_option_id'],
                        'option_id' => $option['option_id'],
                        'name' => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
                        'type' => $option['type'],
                        'option_value' => $option_value_data,
                        'required' => $option['required'],
                        'default' => $option['default'],
                        'master_option'  => $option['master_option'],
                        'master_option_value' => $option['master_option_value'],
                        'minimum' => $option['minimum'],
                        'maximum' => $option['maximum']
                    );
                } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                    $option_data[] = array(
                        'product_option_id' => $option['product_option_id'],
                        'option_id' => $option['option_id'],
                        'name' => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
                        'type' => $option['type'],
                        'option_value' => $option['option_value'],
                        'required' => $option['required'],
                        'image'=>false,
                        'master_option'     => $option['master_option'],
                        'master_option_value' => $option['master_option_value'],
                        'minimum' => $option['minimum'],
                        'maximum' => $option['maximum']
                    );
                }
            }
            if ($product_info['mobile_app_image']) {
                $image = $this->model_tool_image->resize($product_info['mobile_app_image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
            } else {
                $image = false;
            }
            $bunled_data = $this->model_catalog_product->getProductBundledItems($product_id);
            
            $bundled_items = array();
            $specialdealstatus = false;
            if (!empty($bunled_data)) {
                foreach ($bunled_data as $item) {
                    if($item['special_price']=='1'){
                        $specialdealstatus = true;
                    }
                    $bundled_produts = array();
                    foreach ($item['products'] as $bundled_product) { 
                      //  print_r($item['products']);                        exit();
                        $bundled_prod = $this->model_catalog_product->getProduct($bundled_product['product_id']);
                        $bundled_prod['name'] = strip_tags(html_entity_decode($bundled_prod['name'])); 
                        $bundled_prod['extra_price'] = $bundled_product['extra_price'];
                        if (isset($bundled_product['optionvalues'])) {
                            // $bundled_prod['options'] = 1;

                           // $bundled_options = $this->model_catalog_product->getBundledProductOptions($item['bundle_id'], $bundled_product['product_id']);

                            if (!empty($bundled_product['optionvalues'])) { 
                                foreach ($this->model_catalog_product->getProductOptions($bundled_product['product_id']) as $option) {
                                    if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                                        $option_value_data = array();

                                        foreach ($option['option_value'] as $option_value) {
                                            if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                                if (in_array($option_value['product_option_value_id'], $bundled_product['optionvalues'])) {
                                                    $option_value_data[] = array(
                                                        'product_option_value_id' => $option_value['product_option_value_id'],
                                                        'option_value_id' => $option_value['option_value_id'],
                                                        'name' => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
                                                        'shortcode'=> isset($option_value['shortcode'])?strip_tags(html_entity_decode($option_value['shortcode'], ENT_QUOTES, 'UTF-8')):'',
                                                        'image' => false,
                                                        'price' => (float) $option_value['price'] ? $this->currency->format($option_value['price']) : false,
                                                        'price_prefix' => '',
                                                        'price_value' => ($option_value['price_prefix']=='-' ? -1 : 1 ) *  (float) $option_value['price'] ? $this->currency->format($option_value['price'],'','',false) : 0,
                                                        'master_option_value'=>$option_value['master_option_value']
                                                    );
                                                }
                                            }
                                        }
                                        //$option_value['price_prefix']
                                        if (!empty($option_value_data)) {
                                            $bundled_prod['option'][] = array(
                                                'product_option_id' => $option['product_option_id'],
                                                'product_id' => $bundled_product['product_id'],
                                                'option_id' => $option['option_id'],
                                                'name' => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
                                                'type' => $option['type'],
                                                'option_value' => $option_value_data,
                                                'required' => $option['required'],
                                                'default' => $option['default'],
                                                'image' => false,
                                                'master_option'     => $option['master_option'],
                                                'master_option_value' => $option['master_option_value'],
                                                'minimum' => $option['minimum'],
                                                'maximum' => $option['maximum']
                                            );
                                        }
                                    }
                                }
                            }
                        } else {
                            $bundled_prod['option'] = null;
                        }
                        $bundled_prod['toppings']=  $bundled_prod['product_toppings'];
                        $bundled_prod['dressings'] =  $bundled_prod['product_dressings'];
                        $bundled_prod['price'] = (float) $bundled_prod['price'] ? $this->currency->format($bundled_prod['price']) : false;
                        $bundled_prod['price_value'] = (float) $bundled_prod['price'] ? $this->currency->format($bundled_prod['price'],'','',false) : 0;
                        if ($product_info['mobile_app_image']) {
                            $bundle_image = $this->model_tool_image->resize($bundled_prod['mobile_app_image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
                        } else {
                            $bundle_image = false;
                        }
                        $bundled_prod['image'] = $bundle_image;
                        $bundled_prod['description']=strip_tags(html_entity_decode($bundled_prod['description'], ENT_QUOTES, 'UTF-8'));
                        $bundled_prod['free_options']= unserialize($bundled_prod['free_options']);
                        $bundled_prod['option_groups']= unserialize($bundled_prod['option_groups']);

                        $bundled_prod['shortcode'] =  isset($bundled_prod['shortcode'])?strip_tags(html_entity_decode($bundled_prod['shortcode'], ENT_QUOTES, 'UTF-8')):'';
                        $bundled_prod['shipping_prices'] = unserialize($bundled_prod['shipping_prices']);
                        $bundled_prod['shipping_methodes'] = unserialize($bundled_prod['shipping_methodes']);
                        $bundled_prod['payment_methodes'] = unserialize($bundled_prod['payment_methodes']);
                        $bundled_produts[] = $bundled_prod;
                    } 

                    $bundled_items[] = array('bundled_id' => $item['bundle_id'],
                        'bundled_qty' => $item['bundle_quantity'],
                        'bundle_name' => $item['bundle_name'],
                        //'special_price' => $item['special_price'],
                        //'extraprice' => $item['extraprice'],
                        //'ismainitem' => $item['ismainitem'],
                        'products' => $bundled_produts
                    );
                }
              
            }
            $shipp_prices = unserialize($product_info['shipping_prices']);
            if(!empty($shipp_prices) && isset($shipp_prices['dine']) && $shipp_prices['dine']!='' && (float)$shipp_prices['dine']){
                $product_info['price'] =$shipp_prices['dine'];
            }
            $json = array( 
                'product_id' => $product_info['product_id'],
                'name' => strip_tags(html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')),
                'model' => $product_info['model'],
                'option' => $option_data,
                'price' => (float) $product_info['price'] ? $this->currency->format($product_info['price']) : false,
                'price_value' => (float) $product_info['price'] ? $this->currency->format($product_info['price'],'','',false) : 0,
                'image' => $image,
                'isspecialdeal'=>$specialdealstatus,
                'bundled_items' => $bundled_items,
                'special_deal' => (!empty($bundled_items)) ? 1 : 0,
                'description'=>strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')),
                'toppings'=>  $product_info['product_toppings'],
                'dressings'=>  $product_info['product_dressings'],
                'epos_bgcolor'=>$product_info['epos_bgcolor'],
                'epos_fgcolor'=>$product_info['epos_fgcolor'], 
                'free_options'=>unserialize($product_info['free_options']),
                'option_groups'=>unserialize($product_info['option_groups']),
                'sort_order'=>$product_info['sort_order'],
                'shortcode'=>  isset($product_info['shortcode'])?strip_tags(html_entity_decode($product_info['shortcode'], ENT_QUOTES, 'UTF-8')):'',
                'shipping_prices'=>unserialize($product_info['shipping_prices']),
                'shipping_methodes'=>unserialize($product_info['shipping_methodes']),
                'payment_methodes'=>unserialize($product_info['payment_methodes']),
                'dealsuggestion'=>$product_info['dealsuggestion'],
                'dealcondition'=>$product_info['dealcondition'],
                'tax_class_id'=>$product_info['tax_class_id'],
                'free_toppings'=>$product_info['free_toppings'],
                'max_toppings'=>$product_info['maximum_toppings'],
                'max_dressings'=>$product_info['maximum_dressings'] 
            );
            //print_r($json);
       }
        if (isset($this->request->post['page'])) {
           return  json_encode($json);
        }
       $this->response->setOutput($this->jsonp(json_encode($json)));
       
    }
    
    
    public function getProducts() {
       // $this->request->post = array();
        if (isset($this->request->post['page'])) {
            $page = $this->request->post['page'];

        }
        else {
            $page = 0;
            $this->request->post['page'] = $page;
        }

        //pulk: code change for adding records per page feature
        if (isset($this->request->post['limit'])) {
            $limit = $this->request->post['limit'];
        }
        else {
            $limit = 10;
            $this->request->post['limit'] = $limit;
        }

        $this->load->model('catalog/product');
        $proIDS = $this->model_catalog_product->getAllProductIDs();

        //pulk: code change for adding records per page feature
        $proIDS_pages = array_chunk($proIDS, $limit, true);
        //$proIDS_pages = array_chunk($proIDS, 10, true);

        if(isset($proIDS_pages[$page])) {
            $currentpage = $proIDS_pages[$page];
            $json = array();
            foreach ($currentpage as $poductid) {
                $this->request->post['product_id'] = $poductid['product_id'];

                $json[$poductid['product_id']] = $this->getProduct();
            }
            $this->response->setOutput($this->jsonp($json,true));
        }
        else {
           // exit();
            $this->response->setOutput($this->jsonp(array(),true));

        }
       // $this->response->setOutput($this->jsonp($json,true));
    }
    
    /*
     * return @ array('topping_id','name','sort_order','topping_values'=>array([0]=>array('topping_value_id','name','price','sort_order')))
     * 
     */
    public function gettoppings(){
        $this->load->model('catalog/product');
        $toppings = $this->model_catalog_product->getToppings(false);
       // print_r($toppings);
        $this->response->setOutput($this->jsonp($toppings,true));
        
    }
    /*
     ** return @ array('dressing_id','name','sortorder','dressing_values'=>array([0]=>array('dressing_value_id','name','sort_order'))) 
     */
    public function getdressings(){
        $this->load->model('catalog/product');
        $dressings = $this->model_catalog_product->getDressings(false);
        $this->response->setOutput($this->jsonp($dressings,true));
    }
    
    /*
     ** 
     */
    public function getAppVersion(){
        $json = array();
        $detect = new Mobile_Detect;
        $versions = array();
        $update_url = '';
        if( $detect->isiOS() ){
            $versions = explode(",",$this->config->get('config_iphone_version'));
              $update_url = $this->config->get('config_ios_marketplace');
        }
        elseif( $detect->isAndroidOS() ){
          $versions = explode(",",$this->config->get('config_android_version'));
          $update_url = $this->config->get('config_android_marketplace');
        }
        
        if(in_array($this->request->post['version'],$versions)){
             $json['force_update'] = false;
        }else{
        // app needs updation
          $json['force_update'] = true;
          $json['update_url'] = $update_url;
        }
    
        //$json['versions'] =   $versions;

        /*
        $detect = new Mobile_Detect;
        if( $detect->isiOS() ){
            $this->request->post['platform']='iOS';
        }
        elseif( $detect->isAndroidOS() ){
            $this->request->post['platform']='Android';
        }
        
        if (isset($this->request->post['platform'])) {
            if($this->request->post['platform']=='iOS') {
                $json['version'] =  $this->config->get('config_iphone_version');
            }
            elseif($this->request->post['platform']=='Android') {
                $json['version'] = $this->config->get('config_android_version');
            }
        }
        $json['response']  = $this->request->post['version'];
        */
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
    
   public function getStores(){
        $json = array();
        $this->load->model('setting/store');
        $this->data['store_info'] = array();
        $storeids[] = 0;
        $storeid_results = $this->model_setting_store->getStores();
        foreach ($storeid_results as $storeid_res) {
            if($storeid_res['store_id']!= $this->config->get('pos_to_storeid')){
                $storeids[] = $storeid_res['store_id'];
            }
        }
        $this->load->model('setting/setting');
        foreach ($storeids as $id) {
             $config = $this->model_setting_setting->getSetting('config',$id);
             if($id==0) {
                 $config['config_url'] = HTTP_SERVER;
             }
             $json['store_info'][$id] = array(
                 'config_url' => $config['config_url'],
                 'config_logo' => $config['config_logo'],
                 'config_title' => $config['config_title'],
                 'config_name' => $config['config_name'],
                 'config_address' => $config['config_address'],
                 'config_postalcode' => $config['config_postalcode'],
                 'config_telephone' => $config['config_telephone'],
                 'map_image' => $config['config_url'] .'/image/mobile/map_'.$id.'.png',
             );

        }

        $this->response->setOutput($this->jsonp(json_encode($json)));
    }
}
