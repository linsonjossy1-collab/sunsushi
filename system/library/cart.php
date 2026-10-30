<?php

#[\AllowDynamicProperties]
class Cart {

    private $config;
    private $db;
    private $data = array();
    private $customer;
    private $session;
    private $tax;
    private $weight;
    // Per-request cache of getProducts() (see getProducts() below).
    private $etm_products_key = null;

    public function __construct($registry) {
        $this->config = $registry->get('config');
        $this->customer = $registry->get('customer');
        $this->session = $registry->get('session');
        $this->db = $registry->get('db');
        $this->tax = $registry->get('tax');
        $this->weight = $registry->get('weight');

        if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
            $this->session->data['cart'] = array();
        }
    }

    /*
     * getProducts() used to rebuild the whole basket from the database on every call
     * (the stock OpenCart cache "if (!$this->data)" was commented out). It is called
     * dozens of times per page (countProducts, getSubTotal, getTotal, getTaxes,
     * hasStock, hasShipping, each total model, header, cart modules...), and deal
     * products re-run their bundle/option queries each time, so a basket with e.g.
     * the Ramen & Katsu Deal made checkout take several seconds.
     *
     * Cache the result for the rest of the request, keyed on every input the build
     * reads. Any change to those inputs rebuilds exactly as before. Only used on GET
     * requests (page loads); POST requests (add to cart, order confirm, payment
     * callbacks) always rebuild, as before.
     */
    public function getProducts($cart=array()) {
        $etm_key = $this->etmProductsCacheKey();
        if ($etm_key !== null && $this->etm_products_key === $etm_key) {
            return $this->data;
        }
        $etm_cart_before = $this->session->data['cart'];
        $etm_result = $this->etmBuildProducts($cart);
        // Do not cache a build that changed the basket itself (it removes unavailable
        // items via remove(), which also resets $this->data mid-build).
        $this->etm_products_key = ($etm_key !== null && $this->session->data['cart'] === $etm_cart_before) ? $etm_key : null;
        return $etm_result;
    }

    private function etmProductsCacheKey() {
        if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) !== 'GET') {
            return null;
        }
        $etm_logged = $this->customer->isLogged();
        return md5(serialize(array(
            isset($this->session->data['cart']) ? $this->session->data['cart'] : null,
            isset($this->session->data['shipping_method']) ? $this->session->data['shipping_method'] : null,
            isset($this->session->data['upselling_cart_products']) ? $this->session->data['upselling_cart_products'] : null,
            isset($this->session->data['cartdiscount']) ? $this->session->data['cartdiscount'] : null,
            $etm_logged,
            $etm_logged ? $this->customer->getCustomerGroupId() : null,
            $this->config->get('config_customer_group_id'),
            $this->config->get('config_language_id'),
        )));
    }

    // The original getProducts() body, unchanged.
    private function etmBuildProducts($cart=array()) {
        //if (!$this->data) {
            if(!empty($cart)){
                $cart_items = $cart;
            }else{
             
            }
               $cart_items = $this->session->data['cart'];
              //print_r($cart_items); 
            //print_r($cart_items);
            //Filter Deal Items From cart
            foreach($cart_items as $ckey=>$qty){
                $product = explode(':', $ckey);
                if(isset($product[2]) && $product[2]!=''){//if deal
                    $dealdata = unserialize(base64_decode($product[2]));    
                    //print_r($dealdata);
                    for($k=1;$k<=$qty;$k++){
                        foreach($dealdata as $dkey=>$ddata){
                            foreach($ddata as $bundid=>$bundqty){
                                if(isset($cart_items[$dkey])){
                                    if((int)$cart_items[$dkey]>(int)$bundqty){
                                        $cart_items[$dkey] -=(int)$bundqty;
                                    }else{
                                        unset($cart_items[$dkey]);
                                    }                            
                                } 
                            }                                               
                        }
                    }                    
                }
            }
            //print_r($cart_items);
            if ($this->customer->isLogged()) {
                $customer_group_id = $this->customer->getCustomerGroupId();
            } else {
                $customer_group_id = $this->config->get('config_customer_group_id');
            }
            foreach ($cart_items as $key => $quantity) {
                $product = explode(':', $key);
                $product_id = $product[0];
                $stock = true;
                $bundled_details = array();
                //$specialdealprice = 0;
                //$specialdealstatus = false;
                $option_price = 0;
                $option_points = 0;
                $option_weight = 0;
                $topping_price =0;
                $bund_topping_price=0;
                $bundled_option_price = 0;
                $option_data = array();$topping_data=array();$dressing_data=array();
                $deal_item_prices=array();
                $extra_deal_prices = 0;
                $deal_status = false;
                if (isset($product[2]) and $product[2] != '') {
                    $bindledatas = unserialize(base64_decode($product[2]));
                    //print_r($bindledatas);
                   // print_r($bindledatas);
                    //$bundle_itemqty = array(key=>array(bundle_id=>qty,bundleid=>qty),key=>array(bundle_id=>qty,bundleid=>qty))                                                            
                    foreach($bindledatas as $bundle_itemkey=>$bundledta){
                        foreach($bundledta as $prodtbundle_id=>$prodtbundle_qty){
                            for($bq=0;$bq<$prodtbundle_qty;$bq++){
                                //unserialize(base64_decode($bundle_itemkey))
                                $bundledproduct = explode(':', $bundle_itemkey);//unserialize(base64_decode($bundle_itemkey)); 
                                //print_r($bundledproduct);
                                $bundled_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int) $bundledproduct[0] . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
                                //print_r($bundled_product_query->rows);

                                //if(isset($bundledproduct[6]) && $bundledproduct[6]!=''){
                                $prodtbundeleqry = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product_bundle pb WHERE pb.bundle_id = '" . (int) $prodtbundle_id . "' ");

                                //}                                             
                                $prodtdata = $this->getProductdata($bundle_itemkey);

                                $option_data = $prodtdata['options'];
                                $bun_toppingdata=$prodtdata['toppings'];
                                $bun_dressingdata = $prodtdata['dressings'];
                                $price = $bundled_product_query->row['price'];

                                $option_points = $prodtdata['option_points'];
                                $option_weight = $prodtdata['option_weight'];
                                $bund_topping_price +=$prodtdata['topping_price'];
                                
                                $bundled_option_price += $prodtdata['option_price'];
                                foreach($option_data as $op) {
                                    if(isset($op['free_with_deal']) && $op['free_with_deal']) {
                                        //free option for deal products
                                        if($op['price_prefix']=='-') {
                                            $bundled_option_price += $op['price'];         
                                        } else {
                                            $bundled_option_price -= $op['price'];                    
                                        }
                                    }
                                }
                                $deal_item_prices[] = (float)$bundled_product_query->row['price'];
                                $deal_status  = true;
                                //Check and remove excluded option price
                                //Also Check Topping Price Con fition
                                //Deal Price Calculation
                                /*
                                if($prodtbundeleqry->num_row) {
                                    switch($prodtbundeleqry->row['special_price']){
                                        case 'N'://Normal Product Price          
                                            $deal_item_prices[] = (float)$bundled_product_query->row['price'];
                                            break;
                                        case 'I'://Individual Price
                                            $specialdealprice +=(float)$bundled_product_query->row['price']; 
                                            $deal_item_prices[] = (float)$bundled_product_query->row['price'];
                                            $specialdealstatus = true;
                                            break;
                                        case 'H'://Half & half
                                            $specialdealprice +=round(((float)$bundled_product_query->row['price']/2),2);  
                                            $deal_item_prices[] = (float)$bundled_product_query->row['price'];
                                            $specialdealstatus = true;
                                            break;
                                        case 'L'://Half and half with largest price
                                            $specialdealstatus = true;
                                            $half_large_price = true;
                                            $deal_item_prices[] = (float)$bundled_product_query->row['price'];
                                            break;
                                    }
                                }
                                */
                                //
                                $prodtbundeleitemqry = $this->db->query("SELECT extra_price FROM " . DB_PREFIX . "product_bundle_items  WHERE bundle_id = '" . (int) $prodtbundle_id . "' AND product_id='".(int) $bundledproduct[0]."' ");
                                if((float)$prodtbundeleitemqry->row['extra_price']>0){ // check half price for half and half
                                    //$specialdealprice +=(float)$prodtbundeleitemqry->row['extra_price'];
                                    $extra_deal_prices += (float)$prodtbundeleitemqry->row['extra_price'];
                                }                        

                                $discount_quantity = 0;
                                $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND quantity <= '" . (int) $discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

                                if ($product_discount_query->num_rows) {
                                    $price = $product_discount_query->row['price'];
                                }
                                // Product Specials
                                $product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
                                if ($product_special_query->num_rows) {
                                    $price = $product_special_query->row['price'];
                                }
                                // Reward Points
                                $product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "'");
                                if ($product_reward_query->num_rows) {
                                    $reward = $product_reward_query->row['points'];
                                } else {
                                    $reward = 0;
                                }
                                if (!$bundled_product_query->row['quantity'] || ($bundled_product_query->row['quantity'] < $quantity)) {
                                    $stock = false;
                                }
                                $bundled_details[] = array(
                                    'key'=>$bundle_itemkey,
                                    'main_product_id' => $product_id,
                                    'product_id' => $bundled_product_query->row['product_id'],
                                    'bundled_id' => $prodtbundle_id ,
                                    'name' => $bundled_product_query->row['name'],
                                    'shortcode'=> $bundled_product_query->row['shortcode'],
                                    'model' => $bundled_product_query->row['model'],
                                    'shipping' => $bundled_product_query->row['shipping'],
                                    'image' => $bundled_product_query->row['image'],
                                    'option' => $option_data,
                                    'download' => array(),
                                    'quantity' => $prodtbundle_qty,// $quantity,
                                    'minimum' => $bundled_product_query->row['minimum'],
                                    'subtract' => $bundled_product_query->row['subtract'],
                                    'stock' => $stock,
                                    'price' => ($price + $option_price),
                                    'total' => ($price + $option_price) * $quantity,
                                    'reward' => $reward * $quantity,
                                    'points' => ($bundled_product_query->row['points'] ? ($bundled_product_query->row['points'] + $option_points) * $quantity : 0),
                                    'tax_class_id' => $bundled_product_query->row['tax_class_id'],
                                    'weight' => ($bundled_product_query->row['weight'] + $option_weight) * $quantity,
                                    'weight_class_id' => $bundled_product_query->row['weight_class_id'],
                                    'length' => $bundled_product_query->row['length'],
                                    'width' => $bundled_product_query->row['width'],
                                    'height' => $bundled_product_query->row['height'],
                                    'length_class_id' => $bundled_product_query->row['length_class_id'],
                                    'toppings'=>$bun_toppingdata,
                                    'dressings'=>$bun_dressingdata,
                                    'unit_price'=>$price
                                );
                            }
                            
                        }
                    }                                                            
                    $option_data = array();
                } 
                else {
                    $prodtdata = $this->getProductdata($key);
                    $option_data = $prodtdata['options'];
                    $topping_data=$prodtdata['toppings'];
                    $dressing_data = $prodtdata['dressings'];
                    $option_points = $prodtdata['option_points'];
                    $option_weight = $prodtdata['option_weight'];
                    $option_price = $prodtdata['option_price'];
                    $topping_price=$prodtdata['topping_price'];                    
                } 

                //Toppings                                                       
                $product_query = $this->db->query("SELECT *,(p.quantity- (SELECT SUM(quantity)  FROM " . DB_PREFIX . "live_stock_data ls WHERE ls.product_id=p.product_id)) as blocked_qty FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int) $product_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
                //freeoptions array
                $group_links =array();
                if ($product_query->num_rows) {
                    
                    if($product_query->row['blocked_qty']!=null){
                        $product_query->row['quantity'] = $product_query->row['blocked_qty'];
                    }

                    
                    if ($this->customer->isLogged()) {
                        $customer_group_id = $this->customer->getCustomerGroupId();
                    } else {
                        $customer_group_id = $this->config->get('config_customer_group_id');
                    }

                    $price = $product_query->row['price'];
                    //Shipping wise prize
                    if(isset($this->session->data['shipping_method'])){ 
                        $shipping_seld = explode('.',$this->session->data['shipping_method']['code']) ;
                        $specific_prices = unserialize($product_query->row['shipping_prices']);
                        //print_r($specific_prices);
                        if(!empty($specific_prices) && $specific_prices[$shipping_seld[0]] && (float)$specific_prices[$shipping_seld[0]]){
                            $price = $specific_prices[$shipping_seld[0]];
                        }
                    }
                    //
                    // Product Discounts
                    $discount_quantity = 0;

                    foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
                        $product_2 = explode(':', $key_2);

                        if ($product_2[0] == $product_id) {
                            $discount_quantity += $quantity_2;
                        }
                    }

                    $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND quantity <= '" . (int) $discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

                    if ($product_discount_query->num_rows) {
                        $price = $product_discount_query->row['price'];
                    }

                    // Product Specials
                    $product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

                    if ($product_special_query->num_rows) {
                        $price = $product_special_query->row['price'];
                    }

                    // Reward Points
                    $product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "'");

                    if ($product_reward_query->num_rows) {
                        $reward = $product_reward_query->row['points'];
                    } else {
                        $reward = 0;
                    }

                    // Downloads		
                    $download_data = array();

                    $download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int) $product_id . "' AND dd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

                    foreach ($download_query->rows as $download) {
                        $download_data[] = array(
                            'download_id' => $download['download_id'],
                            'name' => $download['name'],
                            'filename' => $download['filename'],
                            'mask' => $download['mask'],
                            'remaining' => $download['remaining']
                        );
                    }

                    // Stock
                    if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $quantity)) {
                        $stock = false;
                    }
                     $adhoc_product='';
                    if (isset($product[3]) && $product[3]!='') {
                        $adhoc_product = unserialize(base64_decode($product[3]));
                        $price = $adhoc_product['price'];
                    }
                    //Deal Special Price
                    //echo $product_query->row['dealcondition'];
                    if($deal_status && !empty($deal_item_prices)){
                       // print_r($deal_item_prices);
                        $dealconditions = unserialize($product_query->row['deal_condition_criteria']);
                        switch ($product_query->row['dealcondition']){
                            case 'I':
                                $total_ind_price = 0;
                                foreach($deal_item_prices as $ind_price){
                                    $total_ind_price +=$ind_price;
                                }
                                $price = $total_ind_price;
                                break;
                            case 'H':
                                $total_ind_price = 0;
                                foreach($deal_item_prices as $ind_price){
                                    $total_ind_price +=$ind_price;
                                }
                                $price = round(($total_ind_price/2),2);
                                $bundled_option_price = round(($bundled_option_price/2),2);
                                //echo $price;
                                break;
                            case 'L':
                                rsort($deal_item_prices);
                                $price = $deal_item_prices[0];
                                $bundled_option_price = round(($bundled_option_price/2),2);
                                break;                                                          
                             case 'B':
                                rsort($deal_item_prices);
                                $price = $deal_item_prices[0];
                                $bundled_option_price = round(($bundled_option_price/2),2);
                                break;  
                        }
                        if(!empty($dealconditions) && isset($dealconditions['amount']) && $dealconditions['amount']!=''){
                            $price +=$dealconditions['amount'];
                        }
                        $price +=$extra_deal_prices;
                    }                    
                    if($product_query->row['minimum']!=='' && (int)$product_query->row['minimum']>1 && $quantity<(int)$product_query->row['minimum']){
                         $this->remove($key);
                         //trigger_error($quantity.'--'.json_encode($product_query->row));
                         continue;
                    }
                    $price_applied_quantity = $quantity;
                    if(isset($this->session->data['upselling_cart_products'][$product_id]) && $this->session->data['upselling_cart_products'][$product_id]['free_qty']>0){
                       $price_applied_quantity =($this->session->data['upselling_cart_products'][$product_id]['free_qty']>=$quantity)?0:($quantity-$this->session->data['upselling_cart_products'][$product_id]['free_qty']);
                    }
                    $this->data[$key] = array(
                        'key' => $key,
                        'product_id' => $product_query->row['product_id'],
                        'name' => (($adhoc_product!='') ? $adhoc_product['name'] : $product_query->row['name']),
                        'shortcode' => (($adhoc_product!='') ? $adhoc_product['name'] : $product_query->row['shortcode']),
                        'model' => $product_query->row['model'],
                        'shipping' => $product_query->row['shipping'],
                        'image' => $product_query->row['image'],
                        'option' => $option_data,
                        'download' => $download_data,
                        'quantity' => $quantity,
                        'minimum' => $product_query->row['minimum'],
                        'subtract' => $product_query->row['subtract'],
                        'stock' => $stock,
                        'price' => ($price + $option_price + $bundled_option_price+$topping_price+$bund_topping_price),
                        'total' => (($price + $option_price + $bundled_option_price+$topping_price+$bund_topping_price) * $price_applied_quantity) - ($this->getDiscount($key)),
                        'reward' => $reward * $quantity,
                        'points' => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $quantity : 0),
                        'tax_class_id' => $product_query->row['tax_class_id'],
                        'weight' => ($product_query->row['weight'] + $option_weight) * $quantity,
                        'weight_class_id' => $product_query->row['weight_class_id'],
                        'length' => $product_query->row['length'],
                        'width' => $product_query->row['width'],
                        'height' => $product_query->row['height'],
                        'length_class_id' => $product_query->row['length_class_id'],
                        'bundled_data' => $bundled_details,
                        'shipping_methodes' => unserialize($product_query->row['shipping_methodes']),
                        'payment_methodes' => unserialize($product_query->row['payment_methodes']),
                        'specific_time'=> $product_query->row['specific_time'],
                        'available_times' => unserialize($product_query->row['available_time']), 
                        'minimum_amount' => unserialize($product_query->row['minimum_amount']),
                        'shipping_time' => unserialize($product_query->row['shipping_time']),
                        'toppings'=>$topping_data,
                        'dressings'=>$dressing_data,
                        'available_qty'=>$product_query->row['quantity'],
                         'unit_price'=>$price
                    );
                } 
                else {
                    $this->remove($key);
                }
            }
        //}
        //print_r($this->data); 
        return $this->data;
    }
    
     private function getProductdata($key,$bundle=array()) {
        $product = explode(':', $key);
        $product_id = $product[0];
        $options = (isset($product[1]) && $product[1] != '')?unserialize(base64_decode($product[1])):array();
        $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int) $product_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
        $topping_price =0;
        $topping_data = array();
        if (isset($product[4]) and $product[4] != '') {
            //echo base64_decode($product[4]);
            $producttoppings = unserialize(base64_decode($product[4]));
            //print_r($producttoppings);die();
            foreach ($producttoppings as $product_topping_id=>$toppings) {
                $topping_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "topping` t LEFT JOIN " . DB_PREFIX . "topping_description td ON (t.topping_id = td.topping_id) WHERE t.topping_id ='".(int)$product_topping_id."'  AND td.language_id = '" . (int)$this->config->get('config_language_id') . "'");
                if ($topping_query->num_rows) {
                    foreach ($toppings as $product_topping_value_id=>$qty) {
                        $topping_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "topping_value tv LEFT JOIN " . DB_PREFIX . "topping_value_description tvd ON(tvd.topping_value_id=tv.topping_value_id) WHERE tv.topping_value_id = '" . (int)$product_topping_value_id . "' AND tvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ");
                        //if ($topping_value_query->num_rows) {
                             $topping_price += $topping_value_query->row['price']*$qty;
                             $topping_data[] = array(
                                'product_topping_id' => $product_topping_id,
                                'product_topping_value_id' => $product_topping_value_id,
                                'topping_id' => $topping_query->row['topping_id'],
                                'topping_value_id' => $topping_value_query->row['topping_value_id'],
                                'name' => $topping_query->row['name'],
                                'topping_value' => $topping_value_query->row['name'],
                                'quantity' => $qty,
                                'price' => $topping_value_query->row['price']*$qty,
                                'unitprice' => $topping_value_query->row['price']
                            );
                        //}
                    }
                }
            }
        }
        $dressing_data = array();
        if (isset($product[5]) and $product[5] != '') {
            $productdressings = unserialize(base64_decode($product[5]));
            foreach ($productdressings as $product_dressing_id=>$dressings){
                $dressing_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "dressing` d LEFT JOIN " . DB_PREFIX . "dressing_description dd ON (d.dressing_id = dd.dressing_id) WHERE d.dressing_id ='".(int)$product_dressing_id."'  AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
                if ($dressing_query->num_rows) {
                    foreach ($dressings as $product_dressing_value_id=>$qty) {
                        $dressing_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "dressing_value dv LEFT JOIN " . DB_PREFIX . "dressing_value_description dvd ON(dvd.dressing_value_id=dv.dressing_value_id) WHERE dv.dressing_value_id = '" . (int)$product_dressing_value_id . "' AND dvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ");
                            $dressing_data[] = array(
                                'product_dressing_id' => $product_dressing_id,
                                'product_dressing_value_id' => $product_dressing_value_id,
                                'dressing_id' => $dressing_query->row['dressing_id'],
                                'dressing_value_id' => $dressing_value_query->row['dressing_value_id'],
                                'name' => $dressing_query->row['name'],
                                'dressing_value' => $dressing_value_query->row['name'],
                                'quantity' => 1,
                            );
                    }
                }
            }
        }
        $freeoptions = unserialize($product_query->row['free_options']);
        $opt_groups=unserialize($product_query->row['option_groups']);
        $group_links=array();
        if(isset($opt_groups['group_links']) && !empty($opt_groups['group_links'])){
            $group_links = $opt_groups['group_links'];
        }
        $linked_options = array();
        $link_key=1;
        foreach($group_links as $group_link){
            foreach($group_link as $option){
                foreach ($option as $subkey=>$suboptn) {
                    $linked_options[$link_key]=$subkey;
                    $link_key++;
                }
            }
        }
        //print_r($freeoptions);
        $option_price = 0;
        $option_points = 0;
        $option_weight = 0;

        $option_data = array();

        foreach ($options as $product_option_id => $option_value) {
            $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, po.default, od.name, o.type, po.free_with_deal FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int) $product_option_id . "' AND po.product_id = '" . (int) $product_id . "' AND od.language_id = '" . (int) $this->config->get('config_language_id') . "'");
            if ($option_query->num_rows) {
                //print_r($option_query->rows);

                if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
                    $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name,ovd.shortcode, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int) $option_value . "' AND pov.product_option_id = '" . (int) $product_option_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "'");
                    $free_status= false;
                    if(!empty($freeoptions)){
                        foreach ($freeoptions as $optkey=>$free_optn) {
                            if($free_optn['qty']>0){
                                if(in_array($option_query->row['option_id'], $free_optn['items'])){
                                    $free_status = true;
                                    $freeoptions[$optkey]['qty'] = $freeoptions[$optkey]['qty']-1;
                                }
                            }else{
                                $free_status= false;
                            }
                        }
                    }
                    $option_name =  $option_query->row['name'];
                    foreach($group_links as $group_link){
                        if(isset($group_link[$option_query->row['option_id']])){
                            if(isset($group_link[$option_query->row['option_id']][$product_option_id])){
                                $option_name = $group_link[$option_query->row['option_id']][$product_option_id];
                                break;
                            }
                        }
                    }
                    $groupkey = array_search($product_option_id, $linked_options);
                    if ($option_value_query->num_rows) {
                       //echo $option_name.'___';
                        if ($option_value_query->row['price_prefix'] == '+' && !$free_status) {
                            $option_price += $option_value_query->row['price'];
                        } elseif ($option_value_query->row['price_prefix'] == '-' && !$free_status) {
                            $option_price -= $option_value_query->row['price'];
                        }

                        if ($option_value_query->row['points_prefix'] == '+') {
                            $option_points += $option_value_query->row['points'];
                        } elseif ($option_value_query->row['points_prefix'] == '-') {
                            $option_points -= $option_value_query->row['points'];
                        }

                        if ($option_value_query->row['weight_prefix'] == '+') {
                            $option_weight += $option_value_query->row['weight'];
                        } elseif ($option_value_query->row['weight_prefix'] == '-') {
                            $option_weight -= $option_value_query->row['weight'];
                        }

                        if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
                            $stock = false;
                        }

                        $option_data[] = array(
                            'product_option_id' => $product_option_id,
                            'product_option_value_id' => $option_value,
                            'option_id' => $option_query->row['option_id'],
                            'option_value_id' => $option_value_query->row['option_value_id'],
                            'name' => $option_name,//$option_query->row['name'],
                            'shortcode' => $option_value_query->row['shortcode'],
                            'option_value' => $option_value_query->row['name'],                           
                            'type' => $option_query->row['type'],
                            'quantity' => $option_value_query->row['quantity'],
                            'subtract' => $option_value_query->row['subtract'],
                            'price' => $option_value_query->row['price'],
                            'price_prefix' => $option_value_query->row['price_prefix'],
                            'points' => $option_value_query->row['points'],
                            'points_prefix' => $option_value_query->row['points_prefix'],
                            'weight' => $option_value_query->row['weight'],
                            'weight_prefix' => $option_value_query->row['weight_prefix'],
                            'default' => $option_query->row['default'],
                            'group'=>($groupkey!=false)?$groupkey:0,
                            'free_with_deal' => $option_query->row['free_with_deal']
                        );
                    }
                } 
                elseif ($option_query->row['type'] == 'checkbox' && is_array($option_value)) {
                    $option_name =  $option_query->row['name'];
                    foreach($group_links as $group_link){
                        if(isset($group_link[$option_query->row['option_id']])){
                            if(isset($group_link[$option_query->row['option_id']][$product_option_id])){
                                $option_name = $group_link[$option_query->row['option_id']][$product_option_id];
                                break;
                            }
                        }
                    }
                    //echo $option_name.'--';
                    $groupkey = array_search($product_option_id, $linked_options);
                    foreach ($option_value as $product_option_value_id) {
                        //echo $option_name.'___';
                        $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name,ovd.shortcode, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int) $product_option_value_id . "' AND pov.product_option_id = '" . (int) $product_option_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "'");
                            $free_status= false;
                            if(!empty($freeoptions)){
                                foreach ($freeoptions as $optkey=>$free_optn) {
                                    if($free_optn['qty']>0){
                                        if(in_array($option_query->row['option_id'], $free_optn['items'])){
                                            $free_status = true;
                                            $freeoptions[$optkey]['qty'] = $freeoptions[$optkey]['qty']-1;
                                        }
                                    }else{
                                        $free_status= false;
                                    }
                                }
                            }
                        if ($option_value_query->num_rows) {
                           // echo $option_name.'__';
                            if ($option_value_query->row['price_prefix'] == '+' && !$free_status) {
                                $option_price += $option_value_query->row['price'];
                            } elseif ($option_value_query->row['price_prefix'] == '-' && !$free_status) {
                                $option_price -= $option_value_query->row['price'];
                            }

                            if ($option_value_query->row['points_prefix'] == '+') {
                                $option_points += $option_value_query->row['points'];
                            } elseif ($option_value_query->row['points_prefix'] == '-') {
                                $option_points -= $option_value_query->row['points'];
                            }

                            if ($option_value_query->row['weight_prefix'] == '+') {
                                $option_weight += $option_value_query->row['weight'];
                            } elseif ($option_value_query->row['weight_prefix'] == '-') {
                                $option_weight -= $option_value_query->row['weight'];
                            }

                            if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
                                $stock = false;
                            }

                            $option_data[] = array(
                                'product_option_id' => $product_option_id,
                                'product_option_value_id' => $product_option_value_id,
                                'option_id' => $option_query->row['option_id'],
                                'option_value_id' => $option_value_query->row['option_value_id'],
                                'name' => $option_name,//$option_query->row['name'],
                                'shortcode' => $option_value_query->row['shortcode'],                              
                                'option_value' => (($option_query->row['default']) == 1 ? 'No ' : '') . $option_value_query->row['name'],
                                'type' => $option_query->row['type'],
                                'quantity' => $option_value_query->row['quantity'],
                                'subtract' => $option_value_query->row['subtract'],
                                'price' => $option_value_query->row['price'],
                                'price_prefix' => $option_value_query->row['price_prefix'],
                                'points' => $option_value_query->row['points'],
                                'points_prefix' => $option_value_query->row['points_prefix'],
                                'weight' => $option_value_query->row['weight'],
                                'weight_prefix' => $option_value_query->row['weight_prefix'],
                                'default' => $option_query->row['default'],
                                'group'=>($groupkey!=false)?$groupkey:0,
                                'free_with_deal' => $option_query->row['free_with_deal']
                            );
                        }
                    }
                } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                    $option_data[] = array(
                        'product_option_id' => $product_option_id,
                        'product_option_value_id' => '',
                        'option_id' => $option_query->row['option_id'],
                        'option_value_id' => '',
                        'name' => $option_query->row['name'],
                        'shortcode' => $option_value_query->row['shortcode'],
                        'option_value' => $option_value,
                        'type' => $option_query->row['type'],
                        'quantity' => '',
                        'subtract' => '',
                        'price' => '',
                        'price_prefix' => '',
                        'points' => '',
                        'points_prefix' => '',
                        'weight' => '',
                        'weight_prefix' => '',
                        'group'=>0,
                        'free_with_deal' => $option_query->row['free_with_deal']
                    );
                }

            }
        }
        return array('options'=>$option_data,'toppings'=>$topping_data,'dressings'=>$dressing_data,'option_price'=>$option_price,'option_points'=>$option_points,'option_weight'=>$option_weight,'topping_price'=>$topping_price);
        
    }
    public function getcartProducts($cart=array()){
        if(!empty($cart)){
            $cart_items = $cart;
        }else{
            
        }
        $cart_items = $this->session->data['cart'];
        //print_r($cart_items);
        $return_data = array();
        foreach ($cart_items as $key => $quantity){
            $product = explode(':', $key);
            $product_id = $product[0];
            $stock = true;
            $bundled_details = array();                
            $specialdealprice = 0;
            $specialdealstatus = false;
            $option_price = 0;
            $option_points = 0;
            $option_weight = 0;
            $topping_price =0;
            $bund_topping_price=0;
            $bundled_option_price = 0;
            if ($product[2] == '') {           
                $prodtdata = $this->getProductdata($key);
                $option_data = $prodtdata['options'];
                $topping_data=$prodtdata['toppings'];
                $dressing_data = $prodtdata['dressings'];
                $option_points = $prodtdata['option_points'];
                $option_weight = $prodtdata['option_weight'];
                $option_price = $prodtdata['option_price'];
                $topping_price=$prodtdata['topping_price']; 
                $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int) $product_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");                
                $group_links =array();
                if ($product_query->num_rows) {

                    if ($this->customer->isLogged()) {
                        $customer_group_id = $this->customer->getCustomerGroupId();
                    } else {
                        $customer_group_id = $this->config->get('config_customer_group_id');
                    }

                    $price = $product_query->row['price'];
                    //Shipping wise prize
                    if(isset($this->session->data['shipping_method'])){
                        $shipping_seld = explode('.',$this->session->data['shipping_method']['code']) ;
                        $specific_prices = unserialize($product_query->row['shipping_prices']);
                        //print_r($specific_prices);
                        if(!empty($specific_prices) && $specific_prices[$shipping_seld[0]] && (float)$specific_prices[$shipping_seld[0]]){
                            $price = $specific_prices[$shipping_seld[0]];
                        }
                    }
                    //
                    // Product Discounts
                    $discount_quantity = 0;

                    foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
                        $product_2 = explode(':', $key_2);

                        if ($product_2[0] == $product_id) {
                            $discount_quantity += $quantity_2;
                        }
                    }

                    $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND quantity <= '" . (int) $discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

                    if ($product_discount_query->num_rows) {
                        $price = $product_discount_query->row['price'];
                    }

                    // Product Specials
                    $product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

                    if ($product_special_query->num_rows) {
                        $price = $product_special_query->row['price'];
                    }

                    // Reward Points
                    $product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "'");

                    if ($product_reward_query->num_rows) {
                        $reward = $product_reward_query->row['points'];
                    } else {
                        $reward = 0;
                    }

                    // Downloads		
                    $download_data = array();

                    $download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int) $product_id . "' AND dd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

                    foreach ($download_query->rows as $download) {
                        $download_data[] = array(
                            'download_id' => $download['download_id'],
                            'name' => $download['name'],
                            'filename' => $download['filename'],
                            'mask' => $download['mask'],
                            'remaining' => $download['remaining']
                        );
                    }

                    // Stock
                    if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $quantity)) {
                        $stock = false;
                    }
                    $adhoc_product='';
                    if (isset($product[3]) && $product[3]!='') {
                        $adhoc_product = unserialize(base64_decode($product[3]));
                        $price = $adhoc_product['price'];
                    }
                    if($specialdealstatus){
                        $price = $specialdealprice;
                    }              
                    $price_applied_quantity = $quantity;
                    if(isset($this->session->data['upselling_cart_products'][$product_query->row['product_id']]) && $this->session->data['upselling_cart_products'][$product_query->row['product_id']]['free_qty']>0){
                       $price_applied_quantity =($this->session->data['upselling_cart_products'][$product_query->row['product_id']]['free_qty']>=$quantity)?0:($quantity-$this->session->data['upselling_cart_products'][$product_query->row['product_id']]['free_qty']);
                    }      
                   $return_data[$key] = array(
                        'key' => $key,
                        'product_id' => $product_query->row['product_id'],
                        'name' => (($adhoc_product!='') ? $adhoc_product['name'] : $product_query->row['name']),
                        'shortcode' => (($adhoc_product!='') ? $adhoc_product['name'] : $product_query->row['shortcode']),
                        'model' => $product_query->row['model'],
                        'shipping' => $product_query->row['shipping'],
                        'image' => $product_query->row['image'],
                        'option' => $option_data,
                        'download' => $download_data,
                        'quantity' => $quantity,
                        'minimum' => $product_query->row['minimum'],
                        'subtract' => $product_query->row['subtract'],
                        'stock' => $stock,
                        'price' => ($price + $option_price + $bundled_option_price+$topping_price+$bund_topping_price),
                        'total' => (($price + $option_price + $bundled_option_price+$topping_price+$bund_topping_price) * $price_applied_quantity) - ($this->getDiscount($key)),
                        'reward' => $reward * $quantity,
                        'points' => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $quantity : 0),
                        'tax_class_id' => $product_query->row['tax_class_id'],
                        'weight' => ($product_query->row['weight'] + $option_weight) * $quantity,
                        'weight_class_id' => $product_query->row['weight_class_id'],
                        'length' => $product_query->row['length'],
                        'width' => $product_query->row['width'],
                        'height' => $product_query->row['height'],
                        'length_class_id' => $product_query->row['length_class_id'],
                        'bundled_data' => $bundled_details,
                        'shipping_methodes' => unserialize($product_query->row['shipping_methodes']),
                        'payment_methodes' => unserialize($product_query->row['payment_methodes']),
                        'toppings'=>$topping_data,
                        'dressings'=>$dressing_data,
                        'bundled_status'=>((isset($product[2]) && $product[2] != '')?true:false)
                    );
                }    
            }
        }
        foreach ($cart_items as $key => $quantity){
            $product = explode(':', $key);
            $product_id = $product[0];
            $stock = true;
            $bundled_details = array();                
            $specialdealprice = 0;
            $specialdealstatus = false;
            $option_price = 0;
            $option_points = 0;
            $option_weight = 0;
            $topping_price =0;
            $bund_topping_price=0;
            $bundled_option_price = 0;
            if ($product[2]!= '') {           
                $prodtdata = $this->getProductdata($key);
                $option_data = $prodtdata['options'];
                $topping_data=$prodtdata['toppings'];
                $dressing_data = $prodtdata['dressings'];
                $option_points = $prodtdata['option_points'];
                $option_weight = $prodtdata['option_weight'];
                $option_price = $prodtdata['option_price'];
                $topping_price=$prodtdata['topping_price']; 
                $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int) $product_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");                
                $group_links =array();
                if ($product_query->num_rows) {

                    if ($this->customer->isLogged()) {
                        $customer_group_id = $this->customer->getCustomerGroupId();
                    } else {
                        $customer_group_id = $this->config->get('config_customer_group_id');
                    }

                    $price = $product_query->row['price'];
                    //Shipping wise prize
                    if(isset($this->session->data['shipping_method'])){
                        $shipping_seld = explode('.',$this->session->data['shipping_method']['code']) ;
                        $specific_prices = unserialize($product_query->row['shipping_prices']);
                        //print_r($specific_prices);
                        if(!empty($specific_prices) && $specific_prices[$shipping_seld[0]] && (float)$specific_prices[$shipping_seld[0]]){
                            $price = $specific_prices[$shipping_seld[0]];
                        }
                    }
                    //
                    // Product Discounts
                    $discount_quantity = 0;

                    foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
                        $product_2 = explode(':', $key_2);

                        if ($product_2[0] == $product_id) {
                            $discount_quantity += $quantity_2;
                        }
                    }

                    $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND quantity <= '" . (int) $discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

                    if ($product_discount_query->num_rows) {
                        $price = $product_discount_query->row['price'];
                    }

                    // Product Specials
                    $product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

                    if ($product_special_query->num_rows) {
                        $price = $product_special_query->row['price'];
                    }

                    // Reward Points
                    $product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "'");

                    if ($product_reward_query->num_rows) {
                        $reward = $product_reward_query->row['points'];
                    } else {
                        $reward = 0;
                    }

                    // Downloads		
                    $download_data = array();

                    $download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int) $product_id . "' AND dd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

                    foreach ($download_query->rows as $download) {
                        $download_data[] = array(
                            'download_id' => $download['download_id'],
                            'name' => $download['name'],
                            'filename' => $download['filename'],
                            'mask' => $download['mask'],
                            'remaining' => $download['remaining']
                        );
                    }

                    // Stock
                    if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $quantity)) {
                        $stock = false;
                    }
                    $adhoc_product='';
                    if (isset($product[3]) && $product[3]!='') {
                        $adhoc_product = unserialize(base64_decode($product[3]));
                        $price = $adhoc_product['price'];
                    }
                    if($specialdealstatus){
                        $price = $specialdealprice;
                    }           
                    $price_applied_quantity = $quantity;
                    if(isset($this->session->data['upselling_cart_products'][$product_query->row['product_id']]) && $this->session->data['upselling_cart_products'][$product_query->row['product_id']]['free_qty']>0){
                       $price_applied_quantity =($this->session->data['upselling_cart_products'][$product_query->row['product_id']]['free_qty']>=$quantity)?0:($quantity-$this->session->data['upselling_cart_products'][$product_query->row['product_id']]['free_qty']);
                    }            
                   $return_data[$key] = array(
                        'key' => $key,
                        'product_id' => $product_query->row['product_id'],
                        'name' => (($adhoc_product!='') ? $adhoc_product['name'] : $product_query->row['name']),
                        'shortcode' => (($adhoc_product!='') ? $adhoc_product['name'] : $product_query->row['shortcode']),
                        'model' => $product_query->row['model'],
                        'shipping' => $product_query->row['shipping'],
                        'image' => $product_query->row['image'],
                        'option' => $option_data,
                        'download' => $download_data,
                        'quantity' => $quantity,
                        'minimum' => $product_query->row['minimum'],
                        'subtract' => $product_query->row['subtract'],
                        'stock' => $stock,
                        'price' => ($price + $option_price + $bundled_option_price+$topping_price+$bund_topping_price),
                        'total' => (($price + $option_price + $bundled_option_price+$topping_price+$bund_topping_price) * $price_applied_quantity) - ($this->getDiscount($key)),
                        'reward' => $reward * $quantity,
                        'points' => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $quantity : 0),
                        'tax_class_id' => $product_query->row['tax_class_id'],
                        'weight' => ($product_query->row['weight'] + $option_weight) * $quantity,
                        'weight_class_id' => $product_query->row['weight_class_id'],
                        'length' => $product_query->row['length'],
                        'width' => $product_query->row['width'],
                        'height' => $product_query->row['height'],
                        'length_class_id' => $product_query->row['length_class_id'],
                        'bundled_data' => unserialize(base64_decode($product[2])),
                        'shipping_methodes' => unserialize($product_query->row['shipping_methodes']),
                        'payment_methodes' => unserialize($product_query->row['payment_methodes']),
                        'toppings'=>$topping_data,
                        'dressings'=>$dressing_data,
                        'bundled_status'=>((isset($product[2]) && $product[2] != '')?true:false)
                    );
                }    
            }
        }
        return $return_data;
        //
    }
    public function add($product_id, $qty = 1, $option = array()) {
        //print_r($option);
        $bundle = '';
        $adhoc = '';
        $topping='';
        $dressing='';
       
        if(isset($option['minimum_qty'])){
            $minimum_qty = $option['minimum_qty'];
            unset($option['minimum_qty']);
        }
        if (isset($option['bundled'])) {
            $bundle = base64_encode(serialize($option['bundled']));
            unset($option['bundled']);
        }

        if (isset($option['adhoc'])) {
            $adhoc = base64_encode(serialize($option['adhoc']));
            unset($option['adhoc']);
        }
         if (isset($option['topping'])) { 
            $topping = base64_encode(serialize($option['topping']));
            unset($option['topping']);
        } 
        if (isset($option['dressing'])) { 
            $dressing = base64_encode(serialize($option['dressing']));
            unset($option['dressing']);
        } 
        if (isset($option['main_product_id'])) {
            $deal_product_id=$option['main_product_id'];
            $bundle_id =$option['bundle_id'];
            $bundleidval = $option['bundleidval'];
            unset($option['main_product_id']);            
            unset($option['bundle_id']);  
        }
        if (isset($option['dealcomplete'])) {
            unset($option['dealcomplete']);            
            $dealcomplete=true;
        }
        if (!$option) {
            $key = (int) $product_id;
            $key = (int) $product_id.':';
        } else {
            $key = (int) $product_id . ':' . base64_encode(serialize($option));
            $key=$key;
        }

        $key.=':'.(($bundle != '')?$bundle:'').':'.(($adhoc != '')?$adhoc:'').':'.(($topping != '')?$topping:'').':'.(($dressing != '')?$dressing:'');
        //echo $skey.'</br>';
        /*
        if ($bundle != '') {
            $key .= ((!$option) ? ':' : '' ) . ':' . $bundle;
        }
        if ($adhoc != '') {
            $key .= ((!$option) ? ':' : '' ) . '' . (($bundle == '') ? ':' : '' ) . ':' . $adhoc;
        }
        if ($topping!= '') {
            $key .= ((!$option) ? ':' : '' ) . '' . (($bundle == '') ? ':' : '' ) . '' . (($adhoc == '') ? ':' : '' ).':'.$topping;
        }
        if ($dressing!= '') {
            $key .= ((!$option) ? ':' : '' ) . '' . (($bundle == '') ? ':' : '' ) . '' . (($adhoc == '') ? ':' : '' ).''.(($topping == '') ? ':' : '' ).':'.$dressing;
        }
        */
        //echo $key;
        if (isset($deal_product_id)) {
            $key.=':'.$deal_product_id.'_'.$bundle_id.'_'.$bundleidval;
            if(isset($this->session->data['added_bundle'][$deal_product_id])){
                if(isset($this->session->data['added_bundle'][$deal_product_id][$key])){
                    if(isset($this->session->data['added_bundle'][$deal_product_id][$key][$bundle_id])){
                        $this->session->data['added_bundle'][$deal_product_id][$key][$bundle_id] +=(int)$qty;
                    }else{
                        $this->session->data['added_bundle'][$deal_product_id][$key]=array($bundle_id=>(int)$qty);
                    }
                }else{
                    $this->session->data['added_bundle'][$deal_product_id][$key]=array($bundle_id=>(int)$qty);
                }
            }else{
                if(!isset($this->session->data['added_bundle'])){
                    $this->session->data['added_bundle'] =  array();
                }
                $this->session->data['added_bundle'][$deal_product_id]=array();
                $this->session->data['added_bundle'][$deal_product_id][$key]=array($bundle_id=>(int)$qty);
            }
            /*
            if(!empty($this->session->data['added_bundle']) && isset($this->session->data['added_bundle'][$deal_product_id])){
                $this->session->data['added_bundle'][$deal_product_id][$key] +=(int)$qty;
            }else{
                $this->session->data['added_bundle']=array();
                $this->session->data['added_bundle'][$deal_product_id][$key]=(int)$qty;
            }
            */
        }
        
        if ((int) $qty && ((int) $qty > 0)) {
            if (!isset($deal_product_id) && isset($minimum_qty)) {
            if (!isset($this->session->data['cart'][$key])) {
                   $qty = $minimum_qty;
               }
            }
            if (!isset($this->session->data['cart'][$key])) {
                $this->session->data['cart'][$key] = (int) $qty;
            } else {
                $this->session->data['cart'][$key] += (int) $qty;
            }
            $this->trackChanges('add', $key, $qty);
        }
        //Check Dela Complete Added
        if(isset($dealcomplete)){    //Add Deal Item       
            $key=$deal_product_id.'::'.base64_encode(serialize($this->session->data['added_bundle'][$deal_product_id])).':::';
            if(isset($minimum_qty) && $minimum_qty > $qty) {
                $qty = $minimum_qty;
            }
            if (!isset($this->session->data['cart'][$key])) {
                $this->session->data['cart'][$key] = (int) $qty;
            } else {
                $this->session->data['cart'][$key] += (int) $qty;
            }
            $this->session->data['added_bundle'][$deal_product_id]=array();
            unset($this->session->data['added_bundle'][$deal_product_id]);
        }
        
        
        $this->data = array();
        $this->etm_products_key = null;
        return $key;
    }

    public function update($key, $qty) {
        $deal_key = explode(':', $key);
        if(isset($deal_key[2]) && $deal_key[2]!=''){//deal
            if($qty>$this->session->data['cart'][$key]){//Item add
                $neqty = $qty-$this->session->data['cart'][$key];
                $dealdata = unserialize(base64_decode($deal_key[2]));  
                foreach($dealdata as $dkey=>$ddata){
                    foreach($ddata as $bundid=>$bundqty){
                        $this->session->data['cart'][$dkey]+=(int)$bundqty*$neqty;                        
                    }                                               
                }
            }else{//Item Minus
                $neqty = $this->session->data['cart'][$key]-$qty;
                $dealdata = unserialize(base64_decode($deal_key[2]));  
                foreach($dealdata as $dkey=>$ddata){
                    foreach($ddata as $bundid=>$bundqty){
                        $this->session->data['cart'][$dkey]-=(int)$bundqty*$neqty;
                        if($this->session->data['cart'][$dkey]<=0){
                            unset($this->session->data['cart'][$dkey]);
                        }
                    }                                               
                }
            }
        }
        if ((int) $qty && ((int) $qty > 0)) {
            $this->session->data['cart'][$key] = (int) $qty;

            $this->trackChanges('update', $key, $qty);
        } else {
            $this->remove($key);
        }

        $this->data = array();
        $this->etm_products_key = null;
    }

    public function remove($key) {
        $neqty = $this->session->data['cart'][$key];
        $deal_key = explode(':', $key);
        if(isset($deal_key[2]) && $deal_key[2]!=''){//deal
            $dealdata = unserialize(base64_decode($deal_key[2]));  
            foreach($dealdata as $dkey=>$ddata){
                foreach($ddata as $bundid=>$bundqty){
                    $this->session->data['cart'][$dkey]-=(int)$bundqty*$neqty;
                    if($this->session->data['cart'][$dkey]<=0){
                        unset($this->session->data['cart'][$dkey]);
                    }
                }                                               
            }
        }        
        if (isset($this->session->data['cart'][$key])) {
            
            $this->trackChanges('delete', $key, 0);
            
            unset($this->session->data['cart'][$key]);

            
        }

        $this->data = array();
        $this->etm_products_key = null;
    }

    public function clear() {
        $this->session->data['cart'] = array();
        $this->data = array();
        $this->etm_products_key = null;
    }

    public function getWeight() {
        $weight = 0;

        foreach ($this->getProducts() as $product) {
            if ($product['shipping']) {
                $weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
            }
        }

        return $weight;
    }

    public function getSubTotal() {
        $total = 0;

        foreach ($this->getProducts() as $product) {
            $total += $product['total'];
        }

        return $total;
    }
    /*
    public function getTaxes() {
        $tax_data = array();

        foreach ($this->getProducts() as $product) {
            if ($product['tax_class_id']) {
                $tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

                foreach ($tax_rates as $tax_rate) {
                    if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
                        $tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
                    } else {
                        $tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
                    }
                }
            }
        }

        return $tax_data;
    }
    */
     public function getTaxes() {
        $tax_class_data = array();
        $tax_data = array();        
        foreach ($this->getProducts() as $product) {            
            if(isset($product['bundled_data']) && !empty($product['bundled_data'])){
                if($product['tax_class_id']){          
                    if(!isset($tax_class_data[$product['tax_class_id']])) {
                        $query = $this->db->query("SELECT tr2.rate, tr2.type, tr1.priority FROM " . DB_PREFIX . "tax_rule tr1 LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id)  WHERE tr1.tax_class_id = '" . (int)$product['tax_class_id'] . "'  ORDER BY tr1.priority ASC LIMIT 1");
		
                        if(isset($query->row['rate'])) {
                            $tax_class_data[$product['tax_class_id']] = (float)$query->row['rate']/100;
                        } else {
                            $tax_class_data[$product['tax_class_id']] = 0;
                        }
                    }             
                    $tax_cal_price = $product['total'];                       
                    foreach($product['bundled_data'] as $bundle_item){
                        if(!$bundle_item['tax_class_id']){                                
                            $tax_cal_price -=$bundle_item['price']*$product['quantity'];                              
                        }
                        foreach($bundle_item['option'] as $optiondata){
                            $tax_cal_price -=$optiondata['price']*$product['quantity'];
                        }

                    }   
                    
                    $prr = round(($tax_cal_price/(1+($tax_class_data[$product['tax_class_id']]))),2);     
                    //$prr = round(($tax_cal_price/1.2),2);     
                    $tax_rates = $this->tax->getRates($prr, $product['tax_class_id']);
                    foreach ($tax_rates as $tax_rate) {
                        if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
                            $tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount']);
                        } else {
                            $tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount']);
                        }
                    }
                }                    
            }
            else{
                if($product['tax_class_id']){
                    if(!isset($tax_class_data[$product['tax_class_id']])) {
                        $query = $this->db->query("SELECT tr2.rate, tr2.type, tr1.priority FROM " . DB_PREFIX . "tax_rule tr1 LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id)  WHERE tr1.tax_class_id = '" . (int)$product['tax_class_id'] . "'  ORDER BY tr1.priority ASC LIMIT 1");
		
                        if(isset($query->row['rate'])) {
                            $tax_class_data[$product['tax_class_id']] = (float)$query->row['rate']/100;
                        } else {
                            $tax_class_data[$product['tax_class_id']] = 0;
                        }
                    }     
                    $prr = round(($product['price']/(1+($tax_class_data[$product['tax_class_id']]))),2);
                    //$prr = round(($product['price']/1.2),2);                   
                    $tax_rates = $this->tax->getRates($prr, $product['tax_class_id']); 
                    foreach ($tax_rates as $tax_rate) {
                        if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
                            $tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
                        } else {
                            $tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
                        }
                    }
                }
        }
        }               
        return $tax_data;
    }
    public function getTotal() {
        $total = 0;

        foreach ($this->getProducts() as $key => $product) {
            $total += ($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'] ) - ($this->getDiscount($key) === false ? 0 : $this->getDiscount($key));
        }

        return $total;
    }

    public function countProducts() {
        $product_total = 0;

        $products = $this->getProducts();

        foreach ($products as $product) {
            $product_total += $product['quantity'];
        }

        return $product_total;
    }

    public function hasProducts() {
        return count($this->session->data['cart']);
    }

    public function hasStock() {
        $stock = true;

        foreach ($this->getProducts() as $product) {
            if (!$product['stock']) {
                $stock = false;
            }
        }

        return $stock;
    }

    public function hasShipping() {
        $shipping = false;

        foreach ($this->getProducts() as $product) {
            if ($product['shipping']) {
                $shipping = true;

                break;
            }
        }

        return $shipping;
    }

    public function hasDownload() {
        $download = false;

        foreach ($this->getProducts() as $product) {
            if ($product['download']) {
                $download = true;

                break;
            }
        }

        return $download;
    }

    public function setDiscount($key, $value) {
        $this->session->data['cartdiscount'][$key] = $value;
    }

    public function getDiscount($key) {
        if (isset($this->session->data['cartdiscount'][$key])) {
            return $this->session->data['cartdiscount'][$key];
        } else {
            return 0;
        }
    }

    // FOR edit history
    private function trackChanges($action, $key, $qty) {
        if (!isset($this->session->data['track'])) {
             $this->session->data['track']=array();
        }
        if (!isset($this->session->data['sub-token'])) {
            $this->session->data['sub-token'] = 0;
        }
        switch ($action) {
            case 'add':
                if(isset($this->session->data['track'][$this->session->data['sub-token']]['cart'][$key])) {
                    $this->session->data['track'][$this->session->data['sub-token']]['cart'][$key] += (int) $qty;
                }
                else {
                    $this->session->data['track'][$this->session->data['sub-token']]['cart'][$key] = (int) $qty;
                }
                
                break;
            case 'update':
                foreach ($this->session->data['track'] as $sub_token => $sub_tracks) {
                    if ($sub_token == $this->session->data['sub-token']) {
                        continue;
                    }
                    if (isset($sub_tracks['cart'][$key])) {
                        if ($qty < $sub_tracks['cart'][$key] ) {
                            if ($sub_tracks['cart'][$key] - $qty > 0  && $qty > 0 ) {
                                if (isset($this->session->data['track'][$this->session->data['sub-token']]['removed'][$key])) {
                                    $this->session->data['track'][$this->session->data['sub-token']]['removed'][$key]['qty'] += $sub_tracks['cart'][$key] - $qty;
                                } else {
                                    $this->session->data['track'][$this->session->data['sub-token']]['removed'][$key]['qty'] = $sub_tracks['cart'][$key] - $qty;
                                    $cart_products = $this->getProducts();
                                    if(isset($cart_products[$key])) {
                                        $this->session->data['track'][$this->session->data['sub-token']]['removed'][$key]['product_name'] = $cart_products[$key]['name'];
                                    }
                                }
                                $this->session->data['track'][$sub_token]['cart'][$key] = $qty;
                               
                            } else {
                                if (isset($this->session->data['track'][$this->session->data['sub-token']]['removed'][$key])) {
                                    $this->session->data['track'][$this->session->data['sub-token']]['removed'][$key]['qty'] += $sub_tracks['cart'][$key];
                                } else {
                                    $this->session->data['track'][$this->session->data['sub-token']]['removed'][$key]['qty'] = $sub_tracks['cart'][$key];
                                    $cart_products = $this->getProducts();
                                    if(isset($cart_products[$key])) {
                                        $this->session->data['track'][$this->session->data['sub-token']]['removed'][$key]['product_name'] = $cart_products[$key]['name'];
                                    }
                                }
                               // $qty -= $sub_tracks['cart'][$key];
                                unset($this->session->data['track'][$sub_token]['cart'][$key]);
                                
                            }
                            $qty = 0;
                        } else {
                            $qty -= $sub_tracks['cart'][$key];
                        }
                    }
                    
                }
                if ($qty > 0) {
                    $this->session->data['track'][$this->session->data['sub-token']]['cart'][$key] = $qty;
                }

                break;
            case 'delete':
                if (isset($this->session->data['track'][$this->session->data['sub-token']]['cart'][$key])) {
                    unset($this->session->data['track'][$this->session->data['sub-token']]['cart'][$key]);
                    // break;
                }
                foreach ($this->session->data['track'] as $sub_token => $sub_tracks) {
                    if ($sub_token == $this->session->data['sub-token']) {
                        continue;
                    }
                    if (isset($sub_tracks['cart'][$key])) {
                        if (isset($this->session->data['track'][$this->session->data['sub-token']]['removed'][$key])) {
                            $this->session->data['track'][$this->session->data['sub-token']]['removed'][$key]['qty'] += $this->session->data['track'][$sub_token]['cart'][$key];
                        } else {
                            $this->session->data['track'][$this->session->data['sub-token']]['removed'][$key] = array();
                            $this->session->data['track'][$this->session->data['sub-token']]['removed'][$key]['qty'] = $this->session->data['track'][$sub_token]['cart'][$key];
                            $cart_products = $this->getProducts();
                            if(isset($cart_products[$key])) {
                                $this->session->data['track'][$this->session->data['sub-token']]['removed'][$key]['product_name'] = $cart_products[$key]['name'];
                            }
                        }
                        
                        unset($this->session->data['track'][$sub_token]['cart'][$key]);
                    }
                }
                break;
        }
        if (!isset($this->session->data['track'][$this->session->data['sub-token']]['removed'])) {
            $this->session->data['track'][$this->session->data['sub-token']]['removed'] = array();
        }
        if (!isset($this->session->data['track'][$this->session->data['sub-token']]['time'])) {
            $this->session->data['track'][$this->session->data['sub-token']]['time'] = time();
        }
        // Delete All previous remove index
        foreach ($this->session->data['track'] as $sub_token => $sub_tracks) {
            if ($sub_token == $this->session->data['sub-token']) {
                continue;
            }
            unset($this->session->data['track'][$sub_token]['removed']);
        }
    }

}

?>