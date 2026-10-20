<?php

class ControllerEpos2Local extends Controller {
    private $error = array();
    private function jsonp($json, $encode = false) {
        $this->response->addHeader('Access-Control-Allow-Origin: *');
        $this->response->addHeader('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
        $this->response->addHeader('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
        $this->response->addHeader('Access-Control-Allow-Credentials: true');
        $this->response->setCompression(9);
        if (isset($this->request->post['callback'])) {
            return $this->request->post['callback'] . '(' . ($encode ? json_encode($json) : $json ) . ')';
        } else {
            return ($encode ? json_encode($json) : $json);
        }
    }
    public function index() {
        $this->load->model('catalog/product');
        $pro_last_modified = $this->model_catalog_product->getLastProductyModified();
               
        $this->load->model('catalog/category');
        $this->load->model('epos/order');
        $cat_last_modified = $this->model_catalog_category->getLastCategoryModified();
        $last_modified = (($cat_last_modified < $pro_last_modified ) ? $pro_last_modified: $cat_last_modified);
        if(isset($this->request->post['last_updated_date'])){
            
            if($this->request->post['last_updated_date']< strtotime($last_modified)){
                $this->data['menu_modified']='yes';
                
                $proIDS = $this->model_catalog_product->getAllProductIDs();
                foreach($proIDS as $proID) {
                    $this->data['productids'][] =  $proID['product_id'];
                }
                
                $this->data['productcount'] = $this->model_catalog_product->getProductCount();
            }
            if($this->config->get('toppings_modified') && $this->request->post['last_updated_date'] < $this->config->get('toppings_modified')){ 
                $this->data['toppings_modified']='yes';
            }
            if($this->config->get('dressings_modified') && $this->request->post['last_updated_date'] < $this->config->get('dressings_modified')){
                $this->data['dressings_modified']='yes';
            }
            if($this->config->get('customer_modified') && $this->request->post['last_updated_date'] < $this->config->get('customer_modified')){
                $this->data['customer_modified']='yes';
                $this->load->model('epos/customer');
                //$results = $this->model_epos_customer->getCustomers();
                $customerscnt =  $this->model_epos_customer->customersCount($this->request->post['last_updated_date']);
                $this->data['customercount'] = $customerscnt['customers'];
                $this->data['addresscount'] = $customerscnt['address'];
                $this->data['order_last_modified'] = $this->model_epos_customer->lastOrderModified();
                $this->data['customer_ids'] = array();
                //foreach ($results as $result) {
                    //$this->data['customer_ids'][] = $result['customer_id'];
                //}
            }
            if($this->config->get('postcode_modified') && $this->request->post['last_updated_date'] < $this->config->get('postcode_modified')){
                $this->data['postcode_modified']='yes';
                $this->data['postcodecnt'] = $this->postcode->postcodesCount();
            }
            $this->data['settings'] = array();
            
            if($this->config->get('users_mofified') && $this->request->post['last_updated_date'] < $this->config->get('users_mofified')){
                $this->data['settings'][] = 'users';
                $this->data['drivers_modified']='yes';
            }
            if($this->config->get('settings_modified') && $this->request->post['last_updated_date'] < $this->config->get('settings_modified')){
                $this->data['settings'][] = 'payments';
                $this->data['settings'][] = 'shippings';
                $this->data['settings'][] = 'totals';
                $this->data['settings'][] = 'settings';
                $this->data['settings'][] = 'eposhistories';
                $this->data['settings'][] = 'commenthistories';
                $this->data['settings'][] = 'offers';
            }
            if($this->config->get('discounts_modified') && $this->request->post['last_updated_date'] < $this->config->get('discounts_modified')){
                $this->data['settings'][] = 'discounts';
            }
            if($this->config->get('offers_modified') && $this->request->post['last_updated_date'] < $this->config->get('offers_modified')){
                //$this->data['settings'][] = 'offers';
            }
            if($this->config->get('posimages_modified') && $this->request->post['last_updated_date'] < $this->config->get('posimages_modified')){
                $this->data['settings'][] = 'posimages';
            }
            if($this->config->get('order_modified') && $this->request->post['last_updated_date'] < $this->config->get('order_modified')){
                $this->data['order_modified']='yes';
                $this->data['order_count']=$this->model_epos_order->getOrderCount($this->request->post['last_updated_date']);
            }
            $this->data['settings'][] = 'taxes';
            //if($this->config->get('eposhistories_modifeid') && $this->request->get['last_updated_date'] < $this->config->get('eposhistories_modifeid')){
                //$this->data['settings'][] = 'eposhistories';
            //}
        }else{
            $this->data['menu_modified']='yes';
            $this->data['toppings_modified']='yes';
            $this->data['dressings_modified']='yes';
            $this->data['customer_modified']='yes';
            $this->data['postcode_modified']='yes';
            $this->data['postcodecnt'] = $this->postcode->postcodesCount();
            $this->data['settings'] = array('users','payments','shippings','totals','settings','discounts','offers','eposhistories','commenthistories','posimages','taxes');
            $this->data['drivers_modified']='yes';
            $this->data['deliverys_modified']='yes';
            $this->data['petty_cash_modified']='yes';
            
            $proIDS = $this->model_catalog_product->getAllProductIDs();
            foreach($proIDS as $proID) {
                $this->data['productids'][] =  $proID['product_id'];
            }
                                
            $this->data['productcount'] = $this->model_catalog_product->getProductCount();
                
            $this->load->model('epos/customer');
            //$results = $this->model_epos_customer->getCustomers();
            $customerscnt =  $this->model_epos_customer->customersCount('');
            $this->data['customercount'] = $customerscnt['customers'];
            $this->data['addresscount'] = $customerscnt['address'];
            $this->data['order_last_modified'] = $this->model_epos_customer->lastOrderModified();
            $this->data['customer_ids'] = array();
            //foreach ($results as $result) {
                //$this->data['customer_ids'][] = $result['customer_id'];
            //}
            
            $this->data['order_modified']='yes';
            $this->data['order_count']=$this->model_epos_order->getOrderCount();
            
            $this->data['all_data_load'] ='yes';
        }
        
        if (isset($this->request->post['last_updated_date'])){
            $this->data['last_modified'] = $this->request->post['last_updated_date'];
        }
        else {
            $this->data['last_modified'] = 0;
        }
        if(strtotime($last_modified) > $this->data['last_modified']){
            $this->data['last_modified'] = strtotime($last_modified);
        }
        if($this->config->get('toppings_modified') &&  $this->data['last_modified']< $this->config->get('toppings_modified')){ 
            $this->data['last_modified'] = $this->config->get('toppings_modified');
        }
        if($this->config->get('dressings_modified') && $this->data['last_modified'] < $this->config->get('dressings_modified')){
            $this->data['last_modified'] = $this->config->get('dressings_modified');
        }
        if($this->config->get('customer_modified') && $this->data['last_modified'] < $this->config->get('customer_modified')){
            $this->data['last_modified'] = $this->config->get('customer_modified');
        }
        if($this->config->get('postcode_modified') && $this->data['last_modified'] < $this->config->get('postcode_modified')){
            $this->data['last_modified'] = $this->config->get('postcode_modified');
        }
        if($this->config->get('users_mofified') && $this->data['last_modified'] < $this->config->get('users_mofified')){
            $this->data['last_modified'] = $this->config->get('users_mofified');
        }
        if($this->config->get('settings_modified') && $this->data['last_modified'] < $this->config->get('settings_modified')){
            $this->data['last_modified'] = $this->config->get('settings_modified');
        }
        if($this->config->get('discounts_modified') && $this->data['last_modified'] < $this->config->get('discounts_modified')){
            $this->data['last_modified'] = $this->config->get('discounts_modified');
        }
        if($this->config->get('offers_modified') && $this->data['last_modified'] < $this->config->get('offers_modified')){
            $this->data['last_modified'] = $this->config->get('offers_modified');
        }
        if($this->config->get('posimages_modified') && $this->data['last_modified'] < $this->config->get('posimages_modified')){
            $this->data['last_modified']= $this->config->get('posimages_modified');
        }
        if($this->config->get('eposhistories_modifeid') && $this->data['last_modified'] < $this->config->get('eposhistories_modifeid')){
            $this->data['last_modified'] =  $this->config->get('eposhistories_modifeid');
        }
        if($this->config->get('order_modified') && $this->data['last_modified'] < $this->config->get('order_modified')){
            $this->data['last_modified'] =  $this->config->get('order_modified');
        }
        $this->response->setOutput($this->jsonp( $this->data, true));
    } 


   public function getMenus() {
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $json = array();
        $json['cat_last_modified'] = $this->model_catalog_category->getLastCategoryModified();

        $results = $this->model_catalog_category->getCategories(0);
        $parent_id = $results[0]['category_id'];

        //$json['menus'] = $this->getmenu($parent_id);
        foreach($results as $result){
            $json['category'][$result['category_id']] = $this->getmenu($result['category_id']);
        }
        $json['menus'] = $json['category'][$parent_id] ;
        $json['category'] = json_encode($json['category']);       
        $this->response->setOutput($this->jsonp(json_encode($json)));
    }

    private function getmenu($parent_id) {
        $category = array();
        $results = $this->model_catalog_category->getCategories($parent_id);
        if ($results) {
            foreach ($results as $result) {
                if ($result['status']) {
                    if ($result['pos_image'] && !empty($result['pos_image'])) {
                        $fontparts = explode(':',$result['pos_image']);
                        if(is_array($fontparts) && isset($fontparts[0])){
                            if ($fontparts[0]=='F'){
                                $image = $result['pos_image'];
                            }
                            else {
                                $image = 'I:data:images/png;base64,' . base64_encode(file_get_contents($this->model_tool_image->resize($result['pos_image'], $this->config->get('config_image_pos_width'), $this->config->get('config_image_pos_height'))));
                            }
                        }
                        else {
                            $image = false;
                        }
                    }
                    else {
                        $image = false;
                    }
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
                        'image' => $image, 
                        'epos_bgcolor'=>$result['epos_bgcolor'],
                        'epos_fgcolor'=>$result['epos_fgcolor'],
                        'parent_id' => $result['parent_id'],
                        'categories' => $this->getmenu($result['category_id']),
                        'products' => $products,
                        'shipping_methodes'=>unserialize($result['shipping_methodes']),
                        'sort_order' => $result['sort_order'],
                        'shortcode' => isset($result['shortcode'])?strip_tags(html_entity_decode($result['shortcode'], ENT_QUOTES, 'UTF-8')):'',
                    );
                }
            }
            return $category;
        } else {
            return NULL;
        }
    }

    private function getProduct($product_id) {
     //   $this->request->post += $this->request->get;
      //  $this->request->get['product_id'] = $this->request->post['product_id'];
        $json = array();
        $json = $this->cache->get('product.epos.' . (int) $this->config->get('config_language_id').'.'.(int)$product_id);
        if (!$json) {
       // if (isset($this->request->post['product_id'])) {
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
           // $product_id = $this->request->post['product_id'];


            $product_info = $this->model_catalog_product->getProduct($product_id);
            $option_data = array();
            foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                    $option_value_data = array();

                    foreach ($option['option_value'] as $option_value) {
                        $image = false;
                      
                        if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                            $option_value_data[] = array(
                                'product_option_value_id' => $option_value['product_option_value_id'],
                                'option_value_id' => $option_value['option_value_id'],
                                'name' => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
                                'shortcode'=> isset($option_value['shortcode'])?strip_tags(html_entity_decode($option_value['shortcode'], ENT_QUOTES, 'UTF-8')):'',
                                'image' => $image,
                                'price' => (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))) : false,
                                'price_prefix' => $option_value['price_prefix'],
                                'price_value' => ($option_value['price_prefix']=='-' ? -1 : 1 ) * (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')),'','',false) : 0,
                                'master_option_value'=>$option_value['master_option_value'],
                                'epos_fgcolor'=>$option_value['epos_fgcolor'],
                                'epos_bgcolor'=>$option_value['epos_bgcolor']
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
                        'master_option'     => $option['master_option'],
                        'master_option_value' => $option['master_option_value'],
                        'in_default'=>(isset($option['default']))?$option['default']:0, 
                        'sort_order'=>$option['sort_order']
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
                        'in_default'=>(isset($option['default']))?$option['default']:0,
                        'sort_order'=>$option['sort_order']
                    );
                }
            }
            /*if ($product_info['image']) {
                $image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
            } else {
                $image = false;
            }*/
            $image = false; 
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
                                                        'price' => (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], '1', $this->config->get('config_tax'))) : false,
                                                        'price_prefix' => '',
                                                        'price_value' => ($option_value['price_prefix']=='-' ? -1 : 1 ) *  (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], '1', $this->config->get('config_tax')),'','',false) : 0,
                                                        'master_option_value'=>$option_value['master_option_value'],
                                                        'epos_fgcolor'=>$option_value['epos_fgcolor'],
                                                        'epos_bgcolor'=>$option_value['epos_bgcolor']
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
                                                'in_default'=>(isset($option['default']))?$option['default']:0,
                                                'sort_order'=>$option['sort_order']
                                            );
                                        }
                                    }
                                }
                            }
                        } else {
                            $bundled_prod['option'] = null;
                        }
                        $bundled_produts[] = $bundled_prod;
                    }

                    $bundled_items[] = array('bundled_id' => $item['bundle_id'],
                        'bundled_qty' => $item['bundle_quantity'],
                        'bundle_name' => $item['bundle_name'],
                        'special_price' => $item['special_price'],
                        'extraprice' => $item['extraprice'],
                        'ismainitem' => $item['ismainitem'],
                        'products' => $bundled_produts
                    );
                }
              
            }
            $json = array(
                'product_id' => $product_info['product_id'],
                'name' => strip_tags(html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')),
                'model' => $product_info['model'],
                'sku'=>$product_info['sku'],
                'option' => $option_data,
                'price' => (float) $product_info['price'] ? $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))) : false,
                'price_value' => (float) $product_info['price'] ? $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')),'','',false) : 0,
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
                 'deal_criteria'=>  unserialize($product_info['deal_condition_criteria']),
                'free_toppings'=>$product_info['free_toppings'],
                'max_toppings'=>$product_info['maximum_toppings'],
                'max_dressings'=>$product_info['maximum_dressings'],
                'quantity'=>$product_info['org_quantity'],
                'subtract'=>$product_info['subtract'],
                'tax_class_id'=>$product_info['tax_class_id'] 
            );
      //  }
           $this->cache->set('product.epos.' . (int) $this->config->get('config_language_id').'.'.(int)$product_id, $json);
        }
        return  json_encode($json);
       
    }
    public function getProducts() {
       // $this->request->post = array();
        if (isset($this->request->post['page'])) {
            $page = $this->request->post['page'];

        }
        else {
            $page = 0;
        }
        $this->load->model('catalog/product');
        $proIDS = $this->model_catalog_product->getAllProductIDs();
        $proIDS_pages = array_chunk($proIDS, 5, true);
        $json = array();
        if(isset($proIDS_pages[$page])) {
            $currentpage = $proIDS_pages[$page];
            
            foreach ($currentpage as $poductid) {
               // $this->request->post['product_id'] = $poductid['product_id'];
                
                $json[$poductid['product_id']] = $this->getProduct($poductid['product_id']);
            }
            $this->response->setOutput($this->jsonp($json,true));
        }
        else {
           $this->response->setOutput($this->jsonp($json,true));
        }
    }
    public function getLastModified() {
        $json = array();
        $this->load->model('catalog/category');
        $cat_last_modified = $this->model_catalog_category->getLastCategoryModified();
        $this->load->model('catalog/product');
        $pro_last_modified = $this->model_catalog_product->getLastProductyModified();
        $json['last_modified'] = (($cat_last_modified < $pro_last_modified ) ? $pro_last_modified: $cat_last_modified);
        $this->response->setOutput($this->jsonp($json,true)); 
    }
    /*
     * return @ array('topping_id','name','sort_order','topping_values'=>array([0]=>array('topping_value_id','name','price','sort_order')))
     * 
     */
    public function getToppings(){
        $this->load->model('catalog/product');
        $toppings = $this->model_catalog_product->getToppings(true);
        $this->response->setOutput($this->jsonp($toppings,true));
    }
    /*
     ** return @ array('dressing_id','name','sortorder','dressing_values'=>array([0]=>array('dressing_value_id','name','sort_order'))) 
     */
    public function getDressings(){
        $this->load->model('catalog/product');
        $dressings = $this->model_catalog_product->getDressings(true);
        $this->response->setOutput($this->jsonp($dressings,true));
    }


    public function getCustomer() {
        $this->load->model('epos/customer');
        $this->load->model('account/order');
        $json = array();
        if (isset($this->request->post['customer_id'])) {
            $json = $this->model_epos_customer->getCustomer($this->request->post['customer_id']);
            $json['addresses'] = $this->model_epos_customer->getAddresses($this->request->post['customer_id']);
            $json['reward'] = $this->model_epos_customer->getRewardPoints($this->request->post['customer_id']);
            $json['orders'] = array();
            $results = $this->model_epos_customer->getOrders($this->request->post['customer_id']);
            foreach ($results as $result) {
                $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
                $voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);
                $products = $this->model_account_order->getOrderProducts($result['order_id']);
                $product_names = array();
                foreach ($products as $product){
                    $product_names[] = $product['name'];
                }
                $json['orders'][] = array(
                    'order_id' => $result['order_id'],
                    'order_status_id' => $result['order_status_id'],
                    'productsname' => implode(',', array_unique($product_names)),
                    'name' => $result['firstname'] . ' ' . $result['lastname'],
                    'status' => $result['status'],
                    'date_added' => $result['date_added'],//date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                    'products' => ($product_total + $voucher_total),
                    'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                    'shipping_address'=>  isset($result['shipping_address'])?$result['shipping_address']:''
                );
            }
        
        }
        $this->response->setOutput($this->jsonp($json,true));
    }
     public function getPostcode() {
         if (isset($this->request->post['p'])) {
             $page =$this->request->post['p'];
         }
         else {
             $page=0;
         }
         /* if($page>1){
             $this->response->setOutput($this->jsonp(array(),true));
             return;
         } */
         $limit =250; 
         $postcode_page = $this->cache->get('postcodes'. '.'. $page .'.' . (int)$this->config->get('config_store_id'));
         if(!$postcode_page && $page==0){
            $postcodes = $this->postcode->getPostcodes($page);
            $postcodes = array_chunk($postcodes, $limit);
            foreach($postcodes as $pkey => $postcode_page) {
                $this->cache->set('postcodes'. '.'. $pkey .'.' . (int)$this->config->get('config_store_id'), $postcode_page);
            }
            $postcode_page = $postcodes[$page];
         }
         if(empty($postcode_page)){
             $postcode_page = array();
         }
         // $json = $this->postcode->getPostcodes($page);
         $this->response->setOutput($this->jsonp($postcode_page,true));
          /*
         if (empty($json)) {
             $this->response->setOutput($this->jsonp($json,true));
         }
         else {
            $this->response->setOutput($this->jsonp($json,true));
         } */
     }
     private function loginvalidate() {
        
        if (isset($this->request->post['pincode']) && !empty($this->request->post['pincode']) && $this->request->post['pincode']!='' && !$this->user->pinlogin($this->request->post['pincode'])) {
            
            $this->error['warning'] = $this->language->get('error_pincode');
           
        }
        if (!$this->user->isLogged() && isset($this->request->post['username']) && isset($this->request->post['password']) && !$this->user->login($this->request->post['username'], $this->request->post['password'])) {
            if (isset($this->request->post['pincode']) && !empty($this->request->post['pincode'])) {
                $this->error['warning'] = $this->language->get('error_pincode');
            }
            else {
                $this->error['warning'] = $this->language->get('error_login');
            }
        }
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
     /*
      *  REGISTER SYSTEM
      */
     public function register() {
        $this->language->load('epos/system');
       
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->loginvalidate()) {
            if ($this->user->isLogged() && $this->user->hasPermission('modify', 'epos/system')) {
                if (isset($this->request->post['systemkey']) && !empty($this->request->post['systemkey']) && $this->posclient->isPosClient()) {
                    $this->posclient->editPosClientDetails($this->request->post);
                } else {
                    $this->posclient->addPosClientDetails($this->request->post);
                }
                $json = array('system_key'=>$this->session->data['system_key']);
                $this->response->setOutput($this->jsonp($json,true));
            }
            else {
                $this->error['warning'] = $this->language->get('error_login');
                $this->response->setOutput($this->jsonp($this->error,true));
            }
        }
        else {
            $this->response->setOutput($this->jsonp($this->error,true));
        }
     }
     public function login() {
        $this->session->data['user_id'] =null;
        unset($this->session->data['user_id']);
        $this->language->load('epos/system');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->loginvalidate()) {
            $this->posclient->addposUserlogs('Logined');
            $json = array('user_id'=>$this->user->getId(),'username'=>$this->user->getUserName(),'system_id'=>1);//
            $this->response->setOutput($this->jsonp($json,true));
        }
        else {
            $this->response->setOutput($this->jsonp($this->error,true));
        }
     }
     /*
      * System Details
      * params @ system_id , sytem_name , shipping_methodes , payment_methodes , normalprinters , eposprinters
     */
     public function systemdetails(){
        /*
         * TODO
         * Only Print Settings To Server
         * Change Add, Edit Sqls
         */
        $json = array(); 
        if(isset($this->request->post['system_id'])){
            //edit data
            $data['system_id'] = $this->request->post['system_id'];
            $data['sytem_name'] = $this->request->post['sytem_name'];
            $data['system_config']['payment_methodes'] = $this->request->post['payment_methodes'];
            $data['system_config']['shipping_methodes'] = $this->request->post['shipping_methodes'];
            $data['system_config']['normalprinters'] = $this->request->post['normalprinters'];
            $data['system_config']['eposprinters'] = $this->request->post['eposprinters'];
            $this->posclient->editPosClientDetails($data);
            $system_info = $this->posclient->getPosclientDetails($this->request->post['system_id']);
            $json=unserialize($system_info['system_config']);
            $json['system_id'] = $this->request->post['system_id'];
            $json['system_name'] = $system_info['system_name'];
            //$json['system_print_settings'] = unserialize($system_info['print_settings'][$this->request->post['system_id']]);
            
        }else{
            //Add Data
            $data['system_id'] = $this->request->post['system_id'];
            $data['sytem_name'] = $this->request->post['sytem_name'];
            $data['system_config']['payment_methodes'] = $this->request->post['payment_methodes'];
            $data['system_config']['shipping_methodes'] = $this->request->post['shipping_methodes'];
            $data['system_config']['normalprinters'] = $this->request->post['normalprinters'];
            $data['system_config']['eposprinters'] = $this->request->post['eposprinters'];
            $system_id = $this->posclient->editPosClientDetails($data); // return system_id
            $system_info = $this->posclient->getPosclientDetails($system_id);
            $json=unserialize($system_info['system_config']);
            $json['system_id'] = $system_id;
            $json['system_name'] = $system_info['system_name'];
            //$json['print_settings'] = unserialize($system_info['print_settings'][$system_id]);
            
            
        }
        $this->response->setOutput($this->jsonp($json,true));
     }
    /*
     * params @ system_id , pincode || (username && password)
     * return @ user_id , username, password , pincode, permissions .
     */ 
    public function userLogin() {
        $json = array();
        if(isset($this->request->post['system_id'])){
            
            if(isset($this->request->post['pincode'])) {
                if($this->user->pinlogin($this->request->post['pincode'])){
                    $permisions = $this->user->getPermission('access');
                    $epos_permission = array();
                    foreach ($permisions as $permision) {
                        if(substr($permision, 0, 3)=='epos'){
                            $epos_permission[]=$permision;
                        }
                    }
                    $json = array('user_id'=>$this->user->getId(),'username'=>$this->user->getUserName(),'firstname'=>'','lastname'=>'','pincode'=>$this->request->post['pincode'],'permissions'=>$epos_permission);
                }else{
                    $json['error']='Invalid Pincode';
                } 
                $this->response->setOutput($this->jsonp($json,true));
            }
            
            if(isset($this->request->post['username']) && isset($this->request->post['password'])) {
                if($this->user->login($this->request->post['username'], $this->request->post['password'])){
                    $permisions = $this->user->getPermission('access');
                     $epos_permission = array();
                     foreach ($permisions as $permision) {
                         if(substr($permision, 0, 3)=='epos'){
                             $epos_permission[]=$permision;
                         }
                     }
                    $json = array('user_id'=>$this->user->getId(),'username'=>$this->request->post['username'],'firstname'=>'','lastname'=>'','password'=>$this->request->post['password'],'permissions'=>$epos_permission);
                }else{
                    $json['error']='Invalid User Credentials';
                }  
            }
        }
        $this->response->setOutput($this->jsonp($json,true));
    }
    
    /*
     * get Histories
     * system_id 
     * 
     */
    public function eposhistories(){
        $json = array();
        $this->load->model('epos/epos_history');             
        //$json['comments']=$this->model_epos_epos_history->getComments();  
        $json['products']=$this->model_epos_epos_history->getadhocproducts();  
        $json['discounts']=$this->model_epos_epos_history->getAdhocDiscount();  
        $this->response->setOutput($this->jsonp(array('eposhistories'=>$json),true));
    }
    public function commenthistories(){
        //$json = array();
        $this->load->model('epos/epos_history');             
        $json=$this->model_epos_epos_history->getComments();  
        $this->response->setOutput($this->jsonp(array('commenthistories'=>$json),true));
    }
    
    /*
     * get Adhoc Discounts
     * array(discounts=>array(discount_id,name,type,amount,group_id),groups=>array(group_id,name,categories,products))
     * 
     */
    public function discounts(){
        $json = array();
        $this->load->model('epos/epos_history');             
        $json['discounts']=$this->model_epos_epos_history->getDiscounts();  
        $json['discount_groups']=$this->model_epos_epos_history->getAdhocdiscountGroups();  
        $this->response->setOutput($this->jsonp(array('eposdiscounts'=>$json),true));
    }
    public function users() {
        $user_data = array();
        $user_query = $this->db->query("SELECT user_id,user_group_id,username,firstname,lastname,email FROM " . DB_PREFIX . "user");
        if($user_query->num_rows) {
            foreach ($user_query->rows as $user) {
                $data = $user;
                $user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int) $user['user_group_id'] . "'");
                $permissions = unserialize($user_group_query->row['permission']);
                $user_permissions = array();
                if (is_array($permissions)) {
                    foreach ($permissions as $key => $value) {
                        foreach($value as $perm){                            
                            if(substr($perm,0,4)=='epos'){
                                $user_permissions[$key][] = $perm;
                            }  
                        }                                              
                    }
                }
                $data['permissions']= $user_permissions;             
                $user_data[] =$data;
                unset($data);
            }
        }
        $this->response->setOutput($this->jsonp(array('users'=>$user_data),true));        
    }
    public function payments() {
        $json = array();
        $this->load->model('setting/extension');
        $presults = $this->model_setting_extension->getExtensions('payment');
        foreach ($presults as $key => $value) {
            //if ($this->config->get('pos_paymentmethodes') and $this->config->get($value['code'] . '_status') and in_array($value['code'], $this->config->get('pos_paymentmethodes'))) {
            if($this->config->get($value['code'] . '_status'))  {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }            
        }
        array_multisort($sort_order, SORT_ASC, $results);
        foreach ($presults as $result) {
           //if ($this->config->get('pos_paymentmethodes') and $this->config->get($result['code'] . '_status') and in_array($result['code'], $this->config->get('pos_paymentmethodes'))) {
            if($this->config->get($result['code'] . '_status') && $result['code']!='epos_payments') {
                $this->load->language('payment/' . $result['code']);
                $json[$result['code']] = array(
                    'name' => $this->language->get('text_title'),
                    'code' => $result['code'],
                    'total'=> ($this->config->get($result['code'] . '_total'))?$this->config->get($result['code'] . '_total'):'',
                    'commision'=> ($this->config->get($result['code'] . '_commision'))?$this->config->get($result['code'] . '_commision'):'',
                    'host'   => ($this->config->get($result['code'] . '_host'))?$this->config->get($result['code'] . '_host'):'',
                    'api'   => ($this->config->get($result['code'] . '_api_key'))?$this->config->get($result['code'] . '_api_key'):'',
                     'sort_order'=>$this->config->get($result['code'] . '_sort_order')
                );
            }
        }
        if($this->config->get('epos_payments_status') && !empty($this->config->get('epos_payments_items'))){
            foreach ($this->config->get('epos_payments_items') as $epos_payments) {
                $json[$epos_payments['code']] = array(
                    'name' => $epos_payments['name'],
                    'code' => $epos_payments['code'],
                    'total'=> 0,
                    'commision'=> $epos_payments['charge'],
                    'host'   => '',
                    'api'   =>'',
                    'sort_order'=>$epos_payments['sort_order'],
                    'custom'=>1
                );
            }
        }
        $this->response->setOutput($this->jsonp(array('paymentmethods'=>$json),true));
        
    }
    public function shippings() {
        $json = array();
        $this->load->model('setting/extension');
        $this->load->model('setting/setting');   
        $results = $this->model_setting_extension->getExtensions('shipping');
        foreach ($results as $result) {
            if ($this->config->get('pos_deliverymethodes') and $this->config->get($result['code'] . '_status') and in_array($result['code'], $this->config->get('pos_deliverymethodes'))) {
                $this->load->language('shipping/' . $result['code']);
                $json[$result['code']] = array(
                    'name' => $this->language->get('text_title'),
                    'code' => $result['code'],
                    'settings'=>$this->model_setting_setting->getSetting($result['code'],$this->config->get('pos_to_storeid')) //TODO -> Only needed setting
                );
            }
        }
        $this->response->setOutput($this->jsonp(array('shippingmethods'=>$json),true));
        
    }
    public function totals() {
        $json = array();
        $this->load->model('setting/extension');
        $results = $this->model_setting_extension->getExtensions('total');
        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }
        array_multisort($sort_order, SORT_ASC, $results);
        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $json[] = array(
                        'code'=>$result['code'],
                        'sort_order'=>$this->config->get($result['code'] . '_sort_order'),
                        'edit_status'=>$this->config->get($result['code'] . '_edit_status')
                    );
            }
        }
        $this->response->setOutput($this->jsonp(array('totals'=>$json),true));        
    }
    public function settings() {
        $this->load->model('setting/setting');
        $this->load->model('setting/store');
        $this->load->model('tool/image');   
        $settings = array();
        $settings = $this->model_setting_setting->getSetting('pos');
        $settings['config_name']=$this->config->get('config_name');
        $settings['config_nick_name']=$this->config->get('config_nick_name');
        $settings['config_owner']=$this->config->get('config_owner');    
        $settings['config_address']=$this->config->get('config_address');
        $settings['config_email']=$this->config->get('config_email');
        $settings['config_telephone']=$this->config->get('config_telephone');
        $settings['config_postalcode']=$this->config->get('config_postalcode');
        $settings['sunday_store_times']=$this->config->get('sunday_store_times');
        $settings['monday_store_times']=$this->config->get('monday_store_times');
        $settings['tuesday_store_times']=$this->config->get('tuesday_store_times');
        $settings['wednesday_store_times']=$this->config->get('wednesday_store_times');
        $settings['thursday_store_times']=$this->config->get('thursday_store_times');
        $settings['friday_store_times']=$this->config->get('friday_store_times');
        $settings['saturday_store_times']=$this->config->get('saturday_store_times');
        $settings['config_title']=$this->config->get('config_title');
        $settings['config_country_id']=$this->config->get('config_country_id');
        $settings['config_zone_id']=$this->config->get('config_zone_id');
        $settings['config_order_status_id']=$this->config->get('config_order_status_id');
        $settings['config_complete_status_id']=$this->config->get('config_complete_status_id');
        $settings['config_order_id_unixtimestap']=$this->config->get('config_order_id_unixtimestap');
        $settings['config_order_id_hide']=$this->config->get('config_order_id_hide');
        $settings['config_order_id_sequence']=$this->config->get('config_order_id_sequence');
        $settings['config_serverapp_api']=$this->config->get('config_serverapp_api');
        $settings['config_browserapp_api']=$this->config->get('config_browserapp_api');
        $settings['currency_symbol_left'] = $this->currency->getSymbolLeft();
        $settings['currency_symbol_right'] = $this->currency->getSymbolRight();
        $settings['currency_code'] = $this->currency->getCode();
        $settings['rewards'] = $this->model_setting_setting->getSetting('rewardpoints');
        $settings['config_local_stdcode']=$this->config->get('config_local_stdcode');
        $settings['adhocdiscount_status']=1; 
        $settings['adhocproduct_status']=$this->config->get('adhocproduct_status'); 
        $settings['adhocproduct_product_id']=$this->config->get('adhocproduct_product_id');  
        $settings['home_delivery_storetime']=$this->config->get('home_delivery_storetime');       
        $settings['collect_in_storetime']=$this->config->get('collect_in_storetime');     
        $settings['mainurl']=HTTP_SERVER;
        if($this->config->get('halfandhalf_items')){
            $settings['halfandhalf_items']=$this->config->get('halfandhalf_items');     
            $settings['halfandhalf_product_id']=$this->config->get('halfandhalf_product_id');  
            $settings['dealhalfandhalf_product_id']=$this->config->get('dealhalfandhalf_product_id');  
        }        
        $settings['swap_options']=($this->config->get('pos_swap_optionvalues'))?explode(',',$this->config->get('pos_swap_optionvalues')):array(); 
        $settings['sms_api_status']=0;
        if($this->config->get('config_textmarketer_api') || $this->config->get('config_infobip_api') || $this->config->get('config_twilio_api')){
            $settings['sms_api_status']=1;
        }
        $custom_chargedata = array();
        if($this->config->get('custom_charges_status')) {
            foreach ($this->config->get('custom_chargedata') as $chargedata) {
                if(in_array($this->config->get('pos_to_storeid'), $chargedata['stores'])){
                    $custom_chargedata[]=$chargedata;
                }
            }
        }
        $settings['custom_chargedata'] = $custom_chargedata; 
        if($this->config->get('config_logo')){
            $settings['store_logo'] = 'data:images/png;base64,' . base64_encode(file_get_contents($this->model_tool_image->resize($this->config->get('config_logo'), 160, 70)));
        }else{
            $settings['store_logo']='';
        }
        if(isset($settings['pos_print_logo']) && $settings['pos_print_logo']!=''){
            $settings['print_logo'] = 'data:images/png;base64,' . base64_encode(file_get_contents($this->model_tool_image->resize($settings['pos_print_logo'], $settings['pos_print_logo_width'], $settings['pos_print_logo_height'])));
        }
        unset ($settings['pos_print_logo']);
        $postcode_details = $this->postcode->getPostcodeDetails($this->config->get('config_postalcode'));
        if(!empty($postcode_details)){
            $settings['postcode_lattd']=$postcode_details['latitude'];
            $settings['postcode_longtd']=$postcode_details['longitude'];
        }
        $table_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store_tables`");
        $pos_images = array();
        //pos_images
             
        if(isset($settings['pos_images']) && !empty($settings['pos_images'])){
            foreach ($settings['pos_images'] as $image) {
                if ($image['image'] && file_exists(DIR_IMAGE . $image['image'])) {
                    $pos_images[] =$this->model_tool_image->resize($image['image'],$this->config->get('pos_image_width'),$this->config->get('pos_image_height'));
                } 
            }
        }
        $settings['pos_images']=$pos_images;
        if(!empty($table_query->rows) && $this->config->get('restaurant_status')){
             $table_data = array();
            $alltables = array();
            foreach ($table_query->rows as $row) {
                $image = false;
                if($row['image'] && $row['image']!=''){
                    $image = 'data:images/png;base64,' . base64_encode(file_get_contents($this->model_tool_image->resize($row['image'], $this->config->get('restaurant_tablewidth'), $this->config->get('restaurant_tableheight'))));
                }
                $table_data[]=array(
                    'table_id'=>$row['table_id'],
                    'name'=>$row['name']
                    //'image'=>$image
                );
                $tablesettings = ((!empty($row['settings']) && $row['settings']!='')?unserialize($row['settings']):array());
                $alltables[]=array(
                    'F'=>  isset($tablesettings['F'])?$tablesettings['F']:0,
                    'R'=>  isset($tablesettings['R'])?$tablesettings['R']:0,
                    'C'=>  isset($tablesettings['C'])?$tablesettings['C']:0,
                    't'=>array('table_id'=>$row['table_id'],'name'=>$row['name'],'image'=>$image,'bgcolor'=>$tablesettings['bgcolor'])
                    );
            }
            //print_r($table_data);
            //print_r($alltables);
            
            $table_layout = array();
            foreach($alltables as $item){
                $table_layout[$item['F']][$item['R']][$item['C']] = $item['t'];
            }
            foreach ($table_layout as $fid=> $floor){
                $maxrow = 1;
                $maxcol = 1 ;
                foreach($floor as $rid=> $row) {
                     if ($maxrow<$rid) $maxrow = $rid;
                     foreach($row as $cid=> $col) {
                         if ($maxcol<$cid) $maxcol = $cid;
                     }
                }
                for($i=1;$i<=$maxrow; $i++) {
                    for($j=1;$j<=$maxcol;$j++) {
                        if(!isset($table_layout[$fid][$i][$j])){
                            $table_layout[$fid][$i][$j]= 0 ; // EMPTY FILL
                        }
                    }
                    ksort($table_layout[$fid][$i]);
                }
                 ksort($table_layout[$fid]);
            }
            $settings['store_tables']=$table_data;
            $settings['table_layout']=$table_layout;
            $settings['restaurant_tableheight']=$this->config->get('restaurant_tableheight');
            $settings['floor_names']=$this->config->get('floor_names');
        }
        //Order Statuses
        $order_staus_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status");
        if(!empty($order_staus_query->rows)){
            $settings['order_status']=array();
            foreach($order_staus_query->rows as $status){
                $settings['order_status'][$status['order_status_id']] = $status['name'];
            }
        }
        //Store Details
        $store_data =array();
        $store_data[0]=array(
                'store_id' => 0,
                'name' => $this->config->get('config_name'),
                'nick_name'=>$this->config->get('config_nick_name'),
                'url' => HTTP_SERVER
            );
        $results = $this->model_setting_store->getStores();
        foreach ($results as $result) {
                $store_data[$result['store_id']] = array(
                    'store_id' => $result['store_id'],
                    'name' => $result['name'],
                    'nick_name'=>'',
                    'url' => $result['url']
                );
        }
        $settings['stores']=$store_data;
        
        //Print Details
        $this->load->model('epos/order');
        if(isset($this->request->post['system_id'])){
            $settings['print_settings']=$this->model_epos_order->getPrintsettings($this->request->post['system_id']); 
            $system_info = $this->posclient->getPosclientDetails($this->request->post['system_id']);
            $settings['system_details']=unserialize($system_info['system_config']);
             $posclients_qry= $this->db->query("SELECT system_id,system_config FROM " . DB_PREFIX . "pos_client_system");
            $other_systems = array();
            foreach ($posclients_qry->rows as $posclient) {
                if(isset($posclient['system_config'])){
                    $sysconfig = unserialize($posclient['system_config']);
                    if(isset($sysconfig['print_posid']) && (int)$sysconfig['print_posid']==(int)$this->request->post['system_id']){
                        $other_systems[] = $posclient['system_id'];
                    }
                }
            }
            $settings['other_device_prints'] = $other_systems;
        }
        
        //$settings['all_print_settings']=$this->model_epos_order->getallPrintsettings();
        // Driver status
        $print_formats = $this->posclient->getAllprintformats($this->request->post);
        $settings['print_formats'] = array();
        foreach($print_formats as $print_format){
            $settings['print_formats'][$print_format['format_id']] = unserialize($print_format['data']);
            $settings['print_formats'][$print_format['format_id']]['name']=$print_format['name'];
        } 
        $settings['delivery_settings'] = array();
        $settings['delivery_settings']['order_assign_status'] =  $this->config->get('drivermanagement_order_status');
        $settings['delivery_settings']['order_alert_status'] =  $this->config->get('drivermanagement_alert_status');
        $settings['delivery_settings']['order_takeout_status'] =  $this->config->get('drivermanagement_driver_status');
        $settings['delivery_settings']['order_complete_status'] =  $this->config->get('drivermanagement_complete_status');
        $settings['delivery_settings']['order_cancel_status'] =  $this->config->get('drivermanagement_cancel_status');
        if($this->config->get('roundoff_status')){
            $settings['roundoff_status']=$this->config->get('roundoff_status');    
            $settings['roundoff_sort_order']=$this->config->get('roundoff_sort_order');    
            $settings['roundoff_cart_amt']=$this->config->get('roundoff_cart_amt');    
            $settings['roundoff_value']=$this->config->get('roundoff_value');                
        }
        if($this->config->get('tips_status')){
            $settings['tips_status']=$this->config->get('tips_status');   
        }
        // websocket
        $settings['websocket_posurl']= $this->config->get('websocket_posurl');
        $settings['websocket_server_ip']= $this->config->get('websocket_server_ip');
        $settings['websocket_server_port']= $this->config->get('websocket_server_port');
          if(isset($settings['pos_swap_buttons']) && !empty($settings['pos_swap_buttons'])){
            $pos_swap_btns = array();
            foreach ($settings['pos_swap_buttons'] as $btns) {
                $pos_swap_btns[]=array('name'=>$btns['name'],'short'=>strip_tags(html_entity_decode($btns['short'], ENT_QUOTES, 'UTF-8')));
            }
            $settings['pos_swap_buttons'] = json_encode($pos_swap_btns);
        }
            
        $settings['config_driver_user_group']=$this->config->get('config_driver_user_group');

        $settings['usergroupdata'] = array();
        $user_group_qry= $this->db->query("SELECT * FROM " . DB_PREFIX . "user_group");
        foreach($user_group_qry->rows as $ugroup){
            $settings['usergroupdata'][$ugroup['user_group_id']] =$ugroup['name'];
            
        }
        
        
        $settings['config_stockmanagement_status']=0;//$this->config->get('config_stockmanagement_status');   
        $settings['config_stock_completed_status']=$this->config->get('config_stock_completed_status');   
        
        $settings['config_waiterapp_sync']=$this->config->get('config_waiterapp_sync');  
        $settings['config_waiterapp_print_system']=$this->config->get('config_waiterapp_print_system'); 
        $settings['waiterpopupshow']=0;
        if($this->config->get('eposonlinepay_config_status')){
            $settings['eposonlinepay_config_status'] = 1;
            $settings['pos_paymentmethodes'][] = 'eposonlinepay';
        }
        
        
        $settings['otherscreens']=$this->getKitchendisplays();
        
        //Stuart elivery
        $settings['stuart_status']=($this->config->get('config_stuart_status'))?$this->config->get('config_stuart_status'):0;
        $settings['stuart_packages']=array('xsmall','small','medium','large','xlarge');
        $order_platform_settings = array(
            0=> array('name'=>'Online','from'=>['0','1']),
            1=> array('name'=>'EPOS','from'=>['2']),
            2=> array('name'=>'Just Eat','from'=>['5','COLLECT','DEL']),
            3=> array('name'=>'Deliveroo','from'=>['5','DLVRCLCT','DLVRDEL','DLVRTKWY']),
            4=> array('name'=>'Uber Eat','from'=>['5','UBECLCT','UBEDEL','UBETKWY']),
        ); 
        $settings['order_platforms'] =$order_platform_settings; 
        $this->response->setOutput($this->jsonp(array('settings'=>$settings),true));        
    }
    public function offers() {
        $offer_data = array();
        $this->load->model('total/offers');    
        //$offerdata =$this->model_total_offers->getOfferdetails($this->config->get('pos_to_storeid'));  // $this->db->query("SELECT * FROM " . DB_PREFIX . "offers o WHERE o.status = '1' ORDER BY o.sort_order ASC");
        $offer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers o WHERE ((o.date_start = '0000-00-00' OR o.date_start < NOW()) AND (o.date_end = '0000-00-00' OR o.date_end > NOW())) AND o.status = '1' ORDER BY o.sort_order ASC");
        if($offer_query->num_rows) {
            foreach ($offer_query->rows as $offer) {                
                $offer_data[]=array(
                    'offer_id'=>$offer['offer_id'],
                    'name'=>$offer['name'],
                    'offer_type'=>$offer['offer_type'],
                    'total_amount'=>(float)$offer['total_amount'],
                    'total_quantity'=> unserialize($offer['total_quantity']),
                    'offer_to_coupon'=>$offer['offer_to_coupon'],   
                    'coupon_case_sensitive'=>$offer['coupon_case_sensitive'],
                    'max_usage'=>$offer['max_usage'],
                    'customer_max_usage'=>$offer['customer_max_usage'],
                    'shipping_cost'=>$offer['shipping_cost'],
                    'date_start'=>$offer['date_start'],
                    'date_end'=>$offer['date_end'],
                    'logged'=>$offer['logged'],
                    'shipping'=>$offer['shipping'],
                    'multi_offers'=>$offer['multi_offers'],
                    'coupon_select'=>$offer['coupon_select'],
                    'product_option_apply'=>$offer['product_option_apply'],
                    'special_products_apply'=>$offer['special_products_apply'],
                    'discounted_products_apply'=>$offer['discounted_products_apply'],
                    'discount_type'=>$offer['discount_type'],
                    'discount_amount'=>unserialize($offer['discount_amount']),
                    'max_discount'=>(float)$offer['max_discount'],
                    'product_quantity_buy'=>$offer['product_quantity_buy'],
                    'product_quantity_free'=>unserialize($offer['product_quantity_free']),
                    'quantity_product_sale_type'=>$offer['quantity_product_sale_type'],
                    'sort_order'=>$offer['sort_order'],
                    'free_product_auto_add'=>$offer['free_product_auto_add'],
                    'offer_specific_days'=>unserialize($offer['offer_specific_days']),
                    'offer_first_orderonly'=>$offer['offer_first_orderonly'],
                    'applied_payments'=>unserialize($offer['applied_payments']),
                    'applied_shipping'=>unserialize($offer['applied_shipping']),
                    'applied_stores'=>$this->model_total_offers->getOfferToStore($offer['offer_id']),
                    'products'=>$this->model_total_offers->getOfferToProduct($offer['offer_id']),
                    'excluded'=> $this->model_total_offers->getExcludedoptions($offer['offer_id']),
                    'free_products'=>$this->model_total_offers->getOfferToFreeProduct($offer['offer_id']),
                    'free_product_options'=>$this->model_total_offers->getOfferToFreeProductOptions($offer['offer_id']),
                    'coupons'=>$this->model_total_offers->getOfferCouponcodes($offer['offer_id']),
                    'applied_devices'=>unserialize($offer['applied_devices']),
                );
            }
        }
        /*
        if(!empty($offerdata)) {
            foreach ($offerdata as $offer) {
                $data = $offer;  
                $data['products'] = $this->model_total_offers->getOfferToProduct($offer['offer_id']);  
                $data['excluded'] = $this->model_total_offers->getExcludedoptions($offer['offer_id']);;  
                $data['free_products'] = $this->model_total_offers->getOfferToFreeProduct($offer['offer_id']);  
                $data['free_product_options'] = $this->model_total_offers->getOfferToFreeProductOptions($offer['offer_id']);  
                $data['coupons'] = $this->model_total_offers->getOfferCouponcodes($offer['offer_id']);                  
                $offer_data[] = $data;
                unset($data);
            }                 
        }
        */
        $this->response->setOutput($this->jsonp(array('offers'=>$offer_data),true)); 
    }
    public function posimages() {
        if($this->config->get('pos_images')){
            $images = $this->config->get('pos_images');
        }else{
            $images = array();
        }
        $pos_images = array();
        $this->load->model('tool/image');        
        foreach ($images as $image) {
            if ($image['image'] && file_exists(DIR_IMAGE . $image['image'])) {
                $pos_images[] = $this->model_tool_image->resize($image['image'], 500, 500);
            } 
        }        
        $this->load->model('checkout/offers');
        $offer_images =$this->model_checkout_offers->getPosOfferImages('pos_image',500,500);
        $this->response->setOutput($this->jsonp(array('posimages'=>$pos_images+$offer_images),true)); 
    } 
    //Drivers
    //taxes
     public function taxes(){
        $tax_data = array();
        $taxquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_rule");
        if($taxquery->num_rows) {
            foreach($taxquery->rows  as $row){
                if(!isset($tax_data[$row['tax_class_id']])){
                    $tax_data[$row['tax_class_id']]=array();
                }
                $taxratequery = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_rate WHERE tax_rate_id=".$row['tax_rate_id']."");
                $tax_data[$row['tax_class_id']][$row['tax_rate_id']]=$taxratequery->row;
            }
            //['rules']=$taxquery->rows;            
        }        
        if($taxratequery->num_rows) {
            $tax_data['rates']=$taxratequery->rows;            
        }
        $this->response->setOutput($this->jsonp(array('taxes'=>$tax_data),true));
    }
    
    public function getDrivers() {
        $this->load->model('drivermanagement/drivermanagement');
        $drivers =  $this->model_drivermanagement_drivermanagement->getDrivers();
        
        $this->response->setOutput($this->jsonp(array('drivers'=>$drivers),true));        
    }
    public function getDeliverys() {
        $deliverys_data = array();
        $delivery_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "deliverys WHERE time_assigned>(NOW() - INTERVAL 7 DAY)");
        if($delivery_query->num_rows) {
            $deliverys_data = $delivery_query->rows ;
        }
        $this->response->setOutput($this->jsonp(array('deliverys'=>$deliverys_data),true));        
    }
    public function getdriver_clock_inout() {
        $deliverys_data = array();
        $delivery_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "driver_clock_inout");
        if($delivery_query->num_rows) {
            $deliverys_data = $delivery_query->rows ;
        }
        $this->response->setOutput($this->jsonp(array('driver_clock_inout'=>$deliverys_data),true));        
    }
    public function getdriver_location() {
        $deliverys_data = array();
        $delivery_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "driver_location");
        if($delivery_query->num_rows) {
            $deliverys_data = $delivery_query->rows ;
        }
        $this->response->setOutput($this->jsonp(array('driver_location'=>$deliverys_data),true));        
    }
    public function getdriver_petty_cash() {
        $deliverys_data = array();
        $delivery_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "driver_petty_cash");
        if($delivery_query->num_rows) {
            $deliverys_data = $delivery_query->rows ;
        }
        // TRas type
        $driver_pettycash_transaction_type = array();
        $driver_pettycash_transaction_type_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "driver_pettycash_transaction_type");
        if($driver_pettycash_transaction_type_query->num_rows) {
            $driver_pettycash_transaction_type = $driver_pettycash_transaction_type_query->rows ;
        }
        $this->response->setOutput($this->jsonp(array('driver_petty_cash'=>$deliverys_data,'driver_pettycash_transaction_type'=>$driver_pettycash_transaction_type),true));        
    }
    public function getCustomers() {
         $this->load->model('epos/customer');
         if (isset($this->request->post['p'])) {
             $page =$this->request->post['p'];
         }
         else {
             $page=0;
         }
        if(isset($this->request->post['last_updated_date'])){
            $date=$this->request->post['last_updated_date'];
        }else{
            $date='';
        }
         $json = $this->model_epos_customer->getCustomerdata($page,$date);
         $this->response->setOutput($this->jsonp($json,true));
    }
     public function getOrders() {
        $this->load->model('epos/order');
        if(isset($this->request->post['p'])){
            $page =$this->request->post['p'];
        }
        else {
            $page=0;
        }
        if(isset($this->request->post['last_updated_date'])){
            $date=$this->request->post['last_updated_date'];
        }else{
            $date='';
        }
        $json = $this->model_epos_order->getOrders($date,$page);
        $this->response->setOutput($this->jsonp($json,true));
    }
    private function getKitchendisplays(){
        //request pos systemID && a param for security
        $displaysystems = array();
        $querydispl = $this->db->query("SELECT * FROM " . DB_PREFIX . "pos_client_system WHERE system_name LIKE '#TV-%'");
        foreach ($querydispl->rows as $displs) {
            $displs['system_key'] = base64_encode($displs['system_id'] . ':' . $displs['systemhash']);
            unset($displs['system_config']);
            unset($displs['systemhash']);
            unset($displs['print_settings']);
            $displs['display_settings'] = unserialize($displs['display_settings']);
            $displaysystems[$displs['system_id']] = $displs;
            //$displaysystems[$disp_key]['display_settings'] = unserialize($displs['display_settings']);
        }
        return $displaysystems;
        //$this->response->setOutput($this->jsonp($displaysystems,true));
    }
    /* Export DB */
    public function exportdb() {
        $output = '';
        // Product
        
        $output .= 'TRUNCATE TABLE `products`;' . "\n\n";
        $this->load->model('catalog/product');
        $proIDS = $this->model_catalog_product->getAllProductIDs();
        $fields = '`product_id`, `product`';
        $values = '';
        $n=1;
        foreach ($proIDS as $poductid) {
            $values = '\'' . $poductid['product_id'] . '\', ';
            $value = $this->getProduct($poductid['product_id']);
            $values .= '\'' . $this->value_format($value) . '\', ';
            $output .= 'INSERT INTO `products` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
            $n++;
           // if($n==5) break;
        }
        // TOPPINGS
        $output .= 'TRUNCATE TABLE `topping`;' . "\n\n";
        $fields = '`topping_id`, `name`, `topping_values`, `swap_option`, `swap_buttons`';
        $values = '';
        $toppings = $this->model_catalog_product->getToppings(true);
        foreach ($toppings as $topping) {
            $values = '\'' . $topping['topping_id'] . '\', ';
            $values .= '\'' . $this->value_format($topping['name']) . '\', ';
            $values .= '\'' . $this->value_format($topping['topping_values']) . '\', ';
            $values .= '\'' . $this->value_format($topping['swap_option']) . '\', ';
            $values .= '\'' . $this->value_format($topping['swap_buttons']) . '\', ';
            $output .= 'INSERT INTO `topping` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
            $n++;
           // if($n==5) break;
        }
        
        // DESSINGS
        $output .= 'TRUNCATE TABLE `dressing`;' . "\n\n";
        $fields = '`dressing_id`, `name`, `swap_option`, `dressing_values`';
        $values = '';
        $dressings = $this->model_catalog_product->getDressings(true);
        foreach ($dressings as $dressing) {
            $values = '\'' . $dressing['dressing_id'] . '\', ';
            $values .= '\'' . $this->value_format($dressing['name']) . '\', ';
            $values .= '\'' . $this->value_format($dressing['swap_option']) . '\', ';
            $values .= '\'' . $this->value_format($dressing['dressing_values']) . '\', ';
            $output .= 'INSERT INTO `dressing` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
            $n++;
           // if($n==5) break;
        }
        
        // CUSTOMERS
      
        $output .= 'TRUNCATE TABLE `customer`;' . "\n\n";
        $fields = '`customer_id`, `store_id`, `firstname`, `lastname`, `email`, `telephone`, `phone_number1`, `phone_number2`, `address_id`, `customer_group_id`, `loaltycard`, `store_ac_no`, `online_customer_id`, `reward`, `orders`, `is_blacklisted`';
        $values = '';
        $query = $this->db->query("SELECT customer_id, store_id, firstname, lastname, email , telephone ,phone_number1 ,phone_number2 ,address_id , customer_group_id , loaltycard , store_ac_no , online_customer_id ,(SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward cr WHERE c.customer_id=cr.customer_id)  , '' ,is_blacklisted FROM " . DB_PREFIX . "customer c WHERE  c.customer_id <>'".(int)$this->config->get('pos_walkin_customer_id')."'");
        foreach ($query->rows as $result) {
            $values = '';			
            foreach (array_values($result) as $value) {
                    $values .= '\'' . $this->value_format($value) . '\', ';
            }

            $output .= 'INSERT INTO `customer` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
        }
        
        
        // ADDRESS
        
        $output .= 'TRUNCATE TABLE `addresses`;' . "\n\n";
        $fields = '`address_id`, `customer_id`, `firstname`, `lastname`, `address_1`, `address_2`, `postcode`, `city`, `delivery_charge`';
        $values = '';
        $query = $this->db->query("SELECT address_id,customer_id,firstname,lastname,address_1,address_2,postcode,city,'' FROM " . DB_PREFIX . "address  WHERE customer_id <>'".(int)$this->config->get('pos_walkin_customer_id')."'");
        foreach ($query->rows as $result) {
            $values = '';			
            foreach (array_values($result) as $value) {
                    $values .= '\'' . $this->value_format($value) . '\', ';
            }

            $output .= 'INSERT INTO `addresses` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
        }
        
        // POSTCODE
        $output .= 'TRUNCATE TABLE `postcodes`;' . "\n\n";
        $fields = '`postcode`, `latitude`, `longitude`, `county`, `street`, `city`, `distance`, `address`, `delivery_charge`';
        $values = '';
       // $query = $this->db->query("SELECT p.postcode, latitude, longitude, county, street, city, distance, address, delivery_charge FROM  " . DB_PREFIX . "uk_postcodes p LEFT JOIN " . DB_PREFIX . "uk_postcode_distance d ON(p.postcode=d.postcode)");
         $query = $this->db->query("SELECT p.postcode, latitude, longitude, county, street, city, distance, address, null FROM  " . DB_PREFIX . "uk_postcodes p LEFT JOIN " . DB_PREFIX . "uk_postcode_distance d ON(p.postcode=d.postcode)");
        foreach ($query->rows as $result) {
            $values = '';			
            foreach (array_values($result) as $value) {
                    $values .= '\'' . $this->value_format($value) . '\', ';
            }

            $output .= 'INSERT INTO `postcodes` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
        }
        
        // ORDERS
        $output .= 'TRUNCATE TABLE `orders`;' . "\n\n";
        $this->load->model('account/order');
        $fields = '`order_id`, `customer_id`, `date_added`, `requested_time`, `data`';
        $values = '';
       // $query = $this->db->query("SELECT p.postcode, latitude, longitude, county, street, city, distance, address, delivery_charge FROM  " . DB_PREFIX . "uk_postcodes p LEFT JOIN " . DB_PREFIX . "uk_postcode_distance d ON(p.postcode=d.postcode)");
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id!='0' ");
        foreach ($query->rows as $order_data) {
            $values = '';
            $values = '\'' . $this->value_format($order_data['order_id']) . '\', ';
            $values .= '\'' . $this->value_format($order_data['customer_id']) . '\', ';
            $values .= '\'' . $this->value_format(strtotime($order_data['date_added'])) . '\', ';
            $values .= '\'' . $this->value_format(strtotime($order_data['requested_time'])) . '\', ';
            $product_data = $this->model_account_order->getConsolidatedOrderProducts($order_data['order_id']);
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
            $order_data['payed_amts']=  unserialize($order_data['payment_history']);
            $order_data['previous_paymemts']=  unserialize($order_data['previous_paymemts']);
            $order_val = json_encode($order_data);
            // $order_val = zlib_encode($order_val, ZLIB_ENCODING_DEFLATE);
            // $values .= '\'' . $this->value_format(base64_encode($order_val)) . '\', ';
             $values .= '\'' . $this->value_format($order_val) . '\', ';
            
            $output .= 'INSERT INTO `orders` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
        }
        //END
        $output .= "\n\n";
        $this->response->addheader('Pragma: public');
        $this->response->addheader('Expires: 0');
        $this->response->addheader('Content-Description: File Transfer');
        $this->response->addheader('Content-Type: application/octet-stream');
        $this->response->addheader('Content-Disposition: attachment; filename=' . date('Y-m-d_H-i-s', time()).'_exportdb.sql');
        $this->response->addheader('Content-Transfer-Encoding: binary');
        $this->response->setOutput($output);
        
    }
    private function value_format($value){
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
        $value = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $value);
        $value = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $value);
        $value = str_replace('\\', '\\\\',	$value);
        $value = str_replace('\'', '\\\'',	$value);
       // $value = str_replace('\\\n', '\n',	$value);
       // $value = str_replace('\\\r', '\r',	$value);
       // $value = str_replace('\\\t', '\t',	$value);
        return $value;
    }
    
    public function exportdbnew() {
    
        $this->log->write("IMPORT START-->".time()."-->STRTO -->".strtotime('now'));
        $order_output = '';
        $other_output = '';
        $postcode_output = '';
        $orderdata = $this->cache->get('eposorderexportdata');
        $otherdata = $this->cache->get('eposexportdata');
        $postcodedata = false;//$this->cache->get('epospostcodeexportdata');
        
        $starttime = time();
        if($orderdata && $this->config->get('epos_cache_date')){
            $this->log->write("FROM CACH ST-->".time());
            $order_output .= $orderdata;            
            $this->log->write("FROM CACH END-->".time());
        }
        if($otherdata && $this->config->get('epos_cache_date')){
            $this->log->write("FROM CACH ST-->".time());
            $other_output .= $otherdata;            
            $this->log->write("FROM CACH END-->".time());
        }
         if($postcodedata && $this->config->get('epos_cache_date')){
            $this->log->write("FROM CACH ST-->".time());
            $postcode_output .= $postcodedata;            
            $this->log->write("FROM CACH END-->".time());
        }
        
            //$other_output .= 'TRUNCATE TABLE `customer`;' . "\n\n";
            $this->log->write("START FROM SCRATCH-->".time());
            $fields = '`customer_id`, `store_id`, `firstname`, `lastname`, `email`, `telephone`, `phone_number1`, `phone_number2`, `address_id`, `customer_group_id`, `loaltycard`, `store_ac_no`, `online_customer_id`, `reward`, `orders`, `is_blacklisted`';
            $values = '';
            $specificstatus = false;
            $modifedcustomers = array();
            if(!$otherdata || !$this->config->get('epos_cache_date')){
                $query = $this->db->query("SELECT customer_id, store_id, firstname, lastname, email , telephone ,phone_number1 ,phone_number2 ,address_id , customer_group_id , loaltycard , store_ac_no , online_customer_id ,(SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward cr WHERE c.customer_id=cr.customer_id)  , '' ,is_blacklisted FROM " . DB_PREFIX . "customer c WHERE  c.customer_id <>'".(int)$this->config->get('pos_walkin_customer_id')."'");
            }else{
                $query = $this->db->query("SELECT customer_id, store_id, firstname, lastname, email , telephone ,phone_number1 ,phone_number2 ,address_id , customer_group_id , loaltycard , store_ac_no , online_customer_id ,(SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward cr WHERE c.customer_id=cr.customer_id)  , '' ,is_blacklisted FROM " . DB_PREFIX . "customer c WHERE  c.customer_id <>'".(int)$this->config->get('pos_walkin_customer_id')."' AND date_modified>'".date('Y-m-d H:i:s',$this->config->get('epos_cache_date'))."'");
                $specificstatus = true;
            }
            
            foreach ($query->rows as $result) {
                if($specificstatus){
                    $modifedcustomers[] = $result['customer_id'];
                }                
                $values = '';			
                foreach (array_values($result) as $value) {
                    $values .= '\'' . $this->value_format($value) . '\', ';
                }

                $other_output .= 'REPLACE INTO `customer` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
            }


            // ADDRESS

            //$other_output .= 'TRUNCATE TABLE `addresses`;' . "\n\n";
            $fields = '`address_id`, `customer_id`, `firstname`, `lastname`, `address_1`, `address_2`, `postcode`, `city`, `delivery_charge`';
            $values = '';
             if(!$otherdata || !$this->config->get('epos_cache_date')){
                $query = $this->db->query("SELECT address_id,customer_id,firstname,lastname,address_1,address_2,postcode,city,'' FROM " . DB_PREFIX . "address  WHERE customer_id <>'".(int)$this->config->get('pos_walkin_customer_id')."'");
            }else{
                if(!empty($modifedcustomers)){
                    $query = $this->db->query("SELECT address_id,customer_id,firstname,lastname,address_1,address_2,postcode,city,'' FROM " . DB_PREFIX . "address  WHERE customer_id <>'".(int)$this->config->get('pos_walkin_customer_id')."' AND  customer_id IN (" . implode(',', $modifedcustomers) .") ");
                }                
            }
            
            foreach ($query->rows as $result) {
                $values = '';			
                foreach (array_values($result) as $value) {
                        $values .= '\'' . $this->value_format($value) . '\', ';
                }

                $other_output .= 'REPLACE INTO `addresses` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
            }

            // POSTCODE
            //$other_output .= 'TRUNCATE TABLE `postcodes`;' . "\n\n";
            $fields = '`postcode`, `latitude`, `longitude`, `county`, `street`, `city`, `distance`, `address`, `delivery_charge`';
            $values = '';
           // $query = $this->db->query("SELECT p.postcode, latitude, longitude, county, street, city, distance, address, delivery_charge FROM  " . DB_PREFIX . "uk_postcodes p LEFT JOIN " . DB_PREFIX . "uk_postcode_distance d ON(p.postcode=d.postcode)");
          /*
            if(!$postcodedata){
                $p_query = $this->db->query("SELECT p.postcode, latitude, longitude, county, street, city, distance, address, null FROM  " . DB_PREFIX . "uk_postcodes p LEFT JOIN " . DB_PREFIX . "uk_postcode_distance d ON(p.postcode=d.postcode)");
            }
            

            foreach ($p_query->rows as $result) {
                $values = '';			
                foreach (array_values($result) as $value) {
                        $values .= '\'' . $this->value_format($value) . '\', ';
                }

                $postcode_output .= 'INSERT INTO `postcodes` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
            }
            $this->log->write("SS2-->".time());
        */
        
            // ORDERS
            //$order_output .= 'TRUNCATE TABLE `orders`;' . "\n\n";
            $this->log->write("SECOND TIME-->".time());
            $this->load->model('account/order');
            $fields = '`order_id`, `customer_id`, `date_added`, `requested_time`, `data`';
            $values = '';
           // $query = $this->db->query("SELECT p.postcode, latitude, longitude, county, street, city, distance, address, delivery_charge FROM  " . DB_PREFIX . "uk_postcodes p LEFT JOIN " . DB_PREFIX . "uk_postcode_distance d ON(p.postcode=d.postcode)");
            
            if(!$orderdata || !$this->config->get('epos_cache_date')){
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id!='0'");
            }else{
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id!='0' AND date_modified>'".date('Y-m-d H:i:s',$this->config->get('epos_cache_date'))."' ");
            }
            
            foreach ($query->rows as $order_data) {
                $values = '';
                $values = '\'' . $this->value_format($order_data['order_id']) . '\', ';
                $values .= '\'' . $this->value_format($order_data['customer_id']) . '\', ';
                $values .= '\'' . $this->value_format(strtotime($order_data['date_added'])) . '\', ';
                $values .= '\'' . $this->value_format(strtotime($order_data['requested_time'])) . '\', ';
                $product_data = $this->model_account_order->getConsolidatedOrderProducts($order_data['order_id']);
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
                $order_data['payed_amts']=  unserialize($order_data['payment_history']);
                $order_data['previous_paymemts']=  unserialize($order_data['previous_paymemts']);
                $order_val = json_encode($order_data);
                //$order_val = zlib_encode($order_val, ZLIB_ENCODING_DEFLATE);
                //$values .= '\'' . $this->value_format(base64_encode($order_val)) . '\', ';
                $values .= '\'' . $this->value_format($order_val) . '\', ';

                $order_output .= 'REPLACE INTO `orders` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
            }
            
            
            $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'epos_cache_date'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'epos_cache_date', `value` = '" . $this->db->escape($starttime) . "'");
            //END
            $order_output .= "\n\n";
            $this->log->write("SECOND TIME END-->".time());
        
        
        $this->cache->set('eposorderexportdata', $order_output); 
        $this->cache->set('eposexportdata', $other_output);
        $this->cache->set('epospostcodeexportdata', $postcode_output);
        $output  = $order_output;
        $output .= $other_output;
        $output .= $postcode_output;
        //CHECK DELETED ORDERS
        /* - INTERVAL 3 HOUR
        if($orderdata){
            $del_query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order_deleted_track` WHERE deleted_time>'".date('Y-m-d H:i:s',$this->config->get('epos_cache_date'))."' ");
            if(!empty($del_query->rows)){
                $other_output .= 'DELETE FROM `orders` WHERE order_id IN (' . implode(',', array_unique($del_query->rows)) . ');' . "\n";
            }
        }
        */
        //CHECK DELETED ORDERS
        $this->log->write("OUTPUT-->".time());
        $this->log->write("-------------------------------------------------------------------------------------------");        
        
        
        $this->response->addheader('Pragma: public');
        $this->response->addheader('Expires: 0');
        $this->response->addheader('Content-Description: File Transfer');
        $this->response->addheader('Content-Type: application/octet-stream');
        $this->response->addheader('Content-Disposition: attachment; filename=' . date('Y-m-d_H-i-s', time()).'_exportdb.sql');
        $this->response->addheader('Content-Transfer-Encoding: binary');
        $this->response->setOutput($output);
        
    }
}
