<?php
require_once DIR_SYSTEM .'vendor/fcm/fcm.php';
class ModelCheckoutOrder extends Model {

    public function addOrder($data) {  
        if($this->config->get('config_order_id_sequence')){
            $display_order_id = $this->getTodaysLastOrderSequence()+1;
        }
        $sql_vars=' SET ';
        if (!isset($data['order_id']) && $this->config->get('config_order_id_unixtimestap')) {
            $order_id = $this->unixtime();
            $sql_vars .="order_id=" . $order_id."," ;
        }

        //$this->log->write(print_r($data['products'] ,true));

        $sql_vars .="invoice_prefix = '" . $this->db->escape(isset($data['invoice_prefix'])?$data['invoice_prefix']:'') . "', store_id = '" . (int) $data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int) $data['customer_id'] . "', customer_group_id = '" . (int) (isset($data['customer_group_id'])?$data['customer_group_id']:0) . "', firstname = '" . $this->db->escape((isset($data['firstname']))?$data['firstname']:'') . "', lastname = '" . $this->db->escape(isset($data['lastname'])?$data['lastname']:''). "', email = '" . $this->db->escape((isset($data['email']))?$data['email']:'') . "', telephone = '" . $this->db->escape(isset($data['telephone'])?$data['telephone']:'') . "', fax = '" . $this->db->escape(isset($data['fax'])?$data['fax']:'') . "', payment_firstname = '" . $this->db->escape(isset($data['payment_firstname'])?$data['payment_firstname']:'') . "', payment_lastname = '" . $this->db->escape(isset($data['payment_lastname'])?$data['payment_lastname']:'') . "', payment_company = '" . $this->db->escape(isset($data['payment_company'])?$data['payment_company']:'') . "', payment_company_id = '" . $this->db->escape(isset($data['payment_company_id'])?$data['payment_company_id']:'') . "', payment_tax_id = '" . $this->db->escape(isset($data['payment_tax_id'])?$data['payment_tax_id']:'') . "', payment_address_1 = '" . $this->db->escape(isset($data['payment_address_1'])?$data['payment_address_1']:'') . "', payment_address_2 = '" . $this->db->escape(isset($data['payment_address_2'])?$data['payment_address_2']:'') . "', payment_city = '" . $this->db->escape(isset($data['payment_city'])?$data['payment_city']:'') . "', payment_postcode = '" . $this->db->escape(isset($data['payment_postcode'])?$data['payment_postcode']:'') . "', payment_country = '" . $this->db->escape(isset($data['payment_country'])?$data['payment_country']:'') . "', payment_country_id = '" . (int) (isset($data['payment_country_id'])?$data['payment_country_id']:'') . "', payment_zone = '" . $this->db->escape(isset($data['payment_zone'])?$data['payment_zone']:'') . "', payment_zone_id = '" . (int) (isset($data['payment_zone_id'])?$data['payment_zone_id']:'') . "', payment_address_format = '" . $this->db->escape(isset($data['payment_address_format'])?$data['payment_address_format']:'') . "', payment_method = '" . $this->db->escape(isset($data['payment_method'])?$data['payment_method']:'') . "', payment_code = '" . $this->db->escape(isset($data['payment_code'])?$data['payment_code']:'') . "', shipping_firstname = '" . $this->db->escape(isset($data['shipping_firstname'])?$data['shipping_firstname']:'') . "', shipping_lastname = '" . $this->db->escape(isset($data['shipping_lastname'])?$data['shipping_lastname']:'') . "', shipping_company = '" . $this->db->escape(isset($data['shipping_company'])?$data['shipping_company']:'') . "', shipping_address_1 = '" . $this->db->escape(isset($data['shipping_address_1'])?$data['shipping_address_1']:'') . "', shipping_address_2 = '" . $this->db->escape(isset($data['shipping_address_2'])?$data['shipping_address_2']:'') . "', shipping_city = '" . $this->db->escape(isset($data['shipping_city'])?$data['shipping_city']:'') . "', shipping_postcode = '" . $this->db->escape(isset($data['shipping_postcode'])?$data['shipping_postcode']:'') . "', shipping_country = '" . $this->db->escape(isset($data['shipping_country'])?$data['shipping_country']:'') . "', shipping_country_id = '" . (int) (isset($data['shipping_country_id'])?$data['shipping_country_id']:'') . "', shipping_zone = '" . $this->db->escape(isset($data['shipping_zone'])?$data['shipping_zone']:'') . "', shipping_zone_id = '" . (int) (isset($data['shipping_zone_id'])?$data['shipping_zone_id']:'') . "', shipping_address_format = '" . $this->db->escape(isset($data['shipping_address_format'])?$data['shipping_address_format']:'') . "', shipping_method = '" . $this->db->escape(isset($data['shipping_method'])?$data['shipping_method']:'') . "', shipping_code = '" . $this->db->escape(isset($data['shipping_code'])?$data['shipping_code']:'') . "', comment = '" . $this->db->escape(isset($data['comment'])?$data['comment']:'') . "', total = '" . (float) (isset($data['total'])?$data['total']:'') . "', affiliate_id = '" . (int) (isset($data['affiliate_id'])?$data['affiliate_id']:'') . "', commission = '" . (float) (isset($data['commission'])?$data['commission']:'') . "', language_id = '" . (int) (isset($data['language_id'])?$data['language_id']:'') . "', currency_id = '" . (int) (isset($data['currency_id'])?$data['currency_id']:'') . "', currency_code = '" . $this->db->escape(isset($data['currency_code'])?$data['currency_code']:'') . "', currency_value = '" . (float)(isset($data['currency_value'])?$data['currency_value']:'')  . "', ip = '" . $this->db->escape(isset($data['ip'])?$data['ip']:'') . "', forwarded_ip = '" . $this->db->escape(isset($data['forwarded_ip'])?$data['forwarded_ip']:'') . "', user_agent = '" . $this->db->escape(isset($data['user_agent'])?$data['user_agent']:'') . "', accept_language = '" . $this->db->escape(isset($data['accept_language'])?$data['accept_language']:'') . "', date_added = '" . $this->db->escape(isset($data['date_added'])?$data['date_added']:date("Y-m-d H:i:s", time())) . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "',requested_time='" . date("Y-m-d H:i:s", isset($data['order_requested_time']) ? $data['order_requested_time'] : time()) . "', system_id = '" . (int) ((isset($data['system_id']))?$data['system_id']:0)  . "', user_id = '" . (int) ((isset($data['user_id']))?$data['user_id']:0) . "',online_order_id=' ".(isset($data['online_order_id'])?$data['online_order_id']:0)."',peoples='". (int)(isset($data['peoples'])?$data['peoples']:0)."',order_from='". (int)(isset($data['order_from'])?$data['order_from']:0)."',change_due='". (float)(isset($data['change_due'])?$data['change_due']:0)."',payment_history = '" . $this->db->escape(serialize(isset($data['payment_history'])?$data['payment_history']:array())) . "',previous_paymemts='".$this->db->escape(serialize(isset($data['previous_paymentdata'])?$data['previous_paymentdata']:array()))."'";
        if(isset($data['dinein_table_id'])){
            $sql_vars .=",dinein_table_id='".(int)$data['dinein_table_id']."'";
        }
                
        if(isset($data['order_id'])){
           // echo "bb";
           $order_id = $data['order_id']; 
           $this->db->query("UPDATE `" . DB_PREFIX . "order`  ".$sql_vars." WHERE order_id=".$order_id."");           
        }else{

            //echo "INSERT INTO `" . DB_PREFIX . "order` ".$sql_vars;
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order` ".$sql_vars);
            $order_id = $this->db->getLastId();//(!$order_id)?$this->db->getLastId():$order_id;
            if(!isset($display_order_id)){
                $display_order_id = ($this->config->get('config_order_id_unixtimestap'))?substr($order_id, -5):$order_id;
            }
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET display_id='".$display_order_id."'  WHERE order_id=".$order_id."");
        }
//echo "ss";die();
        $_SESSION['shipping'] = $this->db->escape($data['shipping_method']);
        $order_current_products = array();
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
         $this->db->query("DELETE FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int) $order_id . "'");
        $consolidateditems = array();
        //$this->log->write(print_r($data['products'],true));
        foreach ($data['products'] as $product) {
            
            //$prodt_qry = $this->db->query("SELECT order_product_id,quantity FROM " . DB_PREFIX . "order_product WHERE order_id=".$order_id." AND cart_key='".$this->db->escape($product['cart_key'])."'");
            /*
            if($prodt_qry->row){//Update Order Product
                $this->db->query("UPDATE " . DB_PREFIX . "order_product SET name = '" . $this->db->escape($product['name']) . "', quantity = '" . (int) $product['quantity'] . "', price = '" . (float) $product['price'] . "', total = '" . (float) $product['total'] . "', tax = '" . (float) $product['tax'] . "', reward = '" . (int) $product['reward'] . "' WHERE order_product_id=".$prodt_qry->row['order_product_id']."");
                $order_product_id = $prodt_qry->row['order_product_id'];
            }else{
              */  
                
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int) $order_id . "', product_id = '" . (int) $product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int) $product['quantity'] . "', price = '" . (float) $product['price'] . "', total = '" . (float) $product['total'] . "', tax = '" . (float) $product['tax'] . "', reward = '" . (int) $product['reward'] . "',cart_key='".$this->db->escape($product['cart_key'])."',main_product_id = '" . (int) (isset($product['main_product_id'])?$product['main_product_id']:'') . "'");
                $order_product_id = $this->db->getLastId();
            //}
            if(!isset($product['bundle']) || empty($product['bundle'])){
                $consolidateditems[$product['cart_key']]=$order_product_id;
            }    
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_product_id = '" . (int) $order_product_id . "'");
            foreach ($product['option'] as $option) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_option_id = '" . (int) $option['product_option_id'] . "', product_option_value_id = '" . (int) $option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "',`group`='".(int)(isset($option['group'])?$option['group']:0)."',`shortcode`='".(isset($option['shortcode'])?$option['shortcode']:'')."'");
            }
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_product_toppings WHERE order_product_id = '" . (int) $order_product_id . "'");
            if(isset($product['toppings'])){
                foreach ($product['toppings'] as $topping) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_product_toppings SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_topping_id = '" . (int) $topping['product_topping_id'] . "', product_topping_value_id = '" . (int) $topping['product_topping_value_id'] . "', name = '" . $this->db->escape($topping['name']) . "', `value` = '" . $this->db->escape($topping['topping_value']) . "', `price` = '" . (float)$topping['price'] . "',`quantity`='".(int)$topping['quantity']."',`shortcode`='".$this->db->escape((isset($topping['shortcode']))?$topping['shortcode']:'')."'");
                }
            }            
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_product_dressings WHERE order_product_id = '" . (int) $order_product_id . "'");
            if(isset($product['dressings'])){
                foreach ($product['dressings'] as $dressing) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_product_dressings SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_dressing_id = '" . (int) $dressing['product_dressing_id'] . "', product_dressing_value_id = '" . (int) $dressing['product_dressing_value_id'] . "', name = '" . $this->db->escape($dressing['name']) . "', `value` = '" . $this->db->escape($dressing['dressing_value']) . "',`shortcode`='".$this->db->escape((isset($dressing['shortcode']))?$dressing['shortcode']:'')."'");
                }
            }            
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_product_id = '" . (int) $order_product_id . "'");
            foreach ($product['download'] as $download) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int) ($download['remaining'] * $product['quantity']) . "'");
            }
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_bundle WHERE order_product_id = '" . (int) $order_product_id . "'");
            /*
            foreach ($product['bundle'] as $bundle_product) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle SET  order_product_id = '" . (int) $order_product_id . "',order_id = '" . (int) $order_id . "',bundle_id = '" . (int) $bundle_product['bundled_id'] . "',bundle_product_id = '" . (int) $bundle_product['product_id'] . "', name = '" . $this->db->escape($bundle_product['name']) . "', model = '" . $this->db->escape($bundle_product['model']) . "', quantity = '" . (int) $bundle_product['quantity'] . "'");

                $order_bundle_product_id = $this->db->getLastId();

                foreach ($bundle_product['option'] as $bunoption) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle_option SET order_bundle_product_id = '" . (int) $order_bundle_product_id . "', product_option_id = '" . (int) $bunoption['product_option_id'] . "', product_option_value_id = '" . (int) $bunoption['product_option_value_id'] . "', name = '" . $this->db->escape($bunoption['name']) . "', `value` = '" . $this->db->escape($bunoption['value']) . "', `type` = '" . $this->db->escape($bunoption['type']) . "',`shortcode`='".$this->db->escape((isset($bunoption['shortcode']))?$bunoption['shortcode']:'')."'");
                }
                if(isset($bundle_product['toppings'])){
                    foreach ($bundle_product['toppings'] as $buntopping) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle_toppings SET order_bundle_product_id = '" . (int) $order_bundle_product_id . "', product_topping_id = '" . (int) $buntopping['product_topping_id'] . "', product_topping_value_id = '" . (int) $buntopping['product_topping_value_id'] . "', name = '" . $this->db->escape($buntopping['name']) . "', `value` = '" . $this->db->escape($buntopping['topping_value']) . "', `price` = '" . (float)$buntopping['price'] . "',`quantity`='".(int)$buntopping['quantity']."',`shortcode`='".$this->db->escape((isset($buntopping['shortcode']))?$buntopping['shortcode']:'')."'");
                    }
                }
                if(isset($bundle_product['dressings'])){
                    foreach ($bundle_product['dressings'] as $bundressing) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle_dressings SET order_bundle_product_id = '" . (int) $order_bundle_product_id . "',  product_dressing_id = '" . (int) $bundressing['product_dressing_id'] . "', product_dressing_value_id = '" . (int) $bundressing['product_dressing_value_id'] . "', name = '" . $this->db->escape($bundressing['name']) . "', `value` = '" . $this->db->escape($bundressing['dressing_value']) . "',`shortcode`='".$this->db->escape((isset($bundressing['shortcode']))?$bundressing['shortcode']:'')."'");
                    }
                }
            }
            */
            if(isset($product['bundle']) && !empty($product['bundle'])){
                foreach ($product['bundle'] as $bkey=>$bundle_product){
                    $bundle_order_product_id = $consolidateditems[$bkey];
                    foreach($bundle_product as $bundle_id=>$qty){
                        $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle SET  order_product_id = '" . (int) $order_product_id . "',order_id = '" . (int) $order_id . "',bundle_id = '" . (int) $bundle_id . "', quantity = '" . (int) $qty . "', bundle_order_product_id = '" . (int) $bundle_order_product_id . "'");
                    }
                }
                //
            }
            
            //New Code for deals
            /*
             if(isset($product['bundle'])){
                foreach ($product['bundle'] as $sub_item) {
                    
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int) $order_id . "', product_id = '" . (int) $sub_item['product_id'] . "', name = '" . $this->db->escape($sub_item['name']) . "', model = '" . $this->db->escape($sub_item['model']) . "', quantity = '" . (int) $sub_item['quantity'] . "', price = '" . (float) $sub_item['price'] . "', total = '" . (float) $sub_item['total'] . "', tax = '" . (float) $product['tax'] . "', reward = 0,cart_key='".$this->db->escape((isset($product['cart_key'])?$product['cart_key']:''))."',main_product_id = '" . (int) $product['product_id'] . "',bundle_id='".(int)(isset($sub_item['bundle_id'])?$sub_item['bundle_id']:0)."'");
                    $order_product_id = $this->db->getLastId();
                    $this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_product_id = '" . (int) $order_product_id . "'");
                    if(isset($sub_item['option'])){
                        foreach ($sub_item['option'] as $sub_otion) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_option_id = '" . (int) $sub_otion['product_option_id'] . "', product_option_value_id = '" . (int) $sub_otion['product_option_value_id'] . "', name = '" . $this->db->escape($sub_otion['name']) . "', `value` = '" . $this->db->escape($sub_otion['value']) . "', `type` = '" . $this->db->escape($sub_otion['type']) . "',`group`='".(int)(isset($sub_otion['group'])?$sub_otion['group']:0)."',`shortcode`='".(isset($sub_otion['shortcode'])?$sub_otion['shortcode']:'')."'");
                        }
                    }
                    $this->db->query("DELETE FROM " . DB_PREFIX . "order_product_toppings WHERE order_product_id = '" . (int) $order_product_id . "'");
                    if(isset($sub_item['toppings'])){
                        foreach($sub_item['toppings'] as $sub_topping){
                            $this->db->query("INSERT INTO " . DB_PREFIX . "order_product_toppings SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_topping_id = '" . (int) $sub_topping['product_topping_id'] . "', product_topping_value_id = '" . (int) $sub_topping['product_topping_value_id'] . "', name = '" . $this->db->escape($sub_topping['name']) . "', `value` = '" . $this->db->escape($sub_topping['topping_value']) . "', `price` = '" . (float)$sub_topping['price'] . "',`quantity`='".(int)$sub_topping['quantity']."',`shortcode`='".$this->db->escape((isset($sub_topping['shortcode']))?$sub_topping['shortcode']:'')."'");
                        }                
                    }
                    $this->db->query("DELETE FROM " . DB_PREFIX . "order_product_dressings WHERE order_product_id = '" . (int) $order_product_id . "'");
                    if(isset($sub_item['dressings'])){
                        foreach($sub_item['dressings'] as $sub_dressing){
                            $this->db->query("INSERT INTO " . DB_PREFIX . "order_product_dressings SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_dressing_id = '" . (int) $sub_dressing['product_dressing_id'] . "', product_dressing_value_id = '" . (int) $sub_dressing['product_dressing_value_id'] . "', name = '" . $this->db->escape($sub_dressing['name']) . "', `value` = '" . $this->db->escape($sub_dressing['dressing_value']) . "',`shortcode`='".$this->db->escape((isset($sub_dressing['shortcode']))?$sub_dressing['shortcode']:'')."'");
                        }
                    }
                    //INSERT INTO order_bundle
                    //$order_product_id $sub_item['bundle_id'] 
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle SET  order_product_id = '" . (int) $order_product_id . "',order_id = '" . (int) $order_id . "',bundle_id = '" . (int) $bundle_product['bundled_id'] . "',bundle_product_id = '" . (int) $bundle_product['product_id'] . "', name = '" . $this->db->escape($bundle_product['name']) . "', model = '" . $this->db->escape($bundle_product['model']) . "', quantity = '" . (int) $bundle_product['quantity'] . "'");
                }        
             }        
           */
            //New Code for deals
            $order_current_products[]=$order_product_id;
        }
        if(!empty($order_current_products)){
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "' AND order_product_id NOT IN(".  implode(',', $order_current_products).") ");
        }else{
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
        }        
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int) $order_id . "'");
        foreach ($data['vouchers'] as $voucher) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int) $order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int) $voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float) $voucher['amount'] . "'");
        }
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "'");
        foreach ($data['totals'] as $total) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', text = '" . $this->db->escape($total['text']) . "', `value` = '" . (float) $total['value'] . "', sort_order = '" . (int) $total['sort_order'] . "'");
        }
        if(isset($data['splits'])){ 
            foreach ($data['splits'] as $split) {
                foreach($split['other'] as $type=>$other_split){
                    $splitype = ($type=='equal')?'E':'C';
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_split` SET order_id=".(int)$order_id." , split_id=".(int)$split['split_id'].",payment_amount='". (float)$split['amount']."',split_type='".$splitype."',status='1',`print_status`=0");
                }
                $order_split_id = $this->db->getLastId();
                
                if(!empty($split['items'])){
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_split` SET order_id=".(int)$order_id." , split_id=".(int)$split['split_id'].",payment_amount='". (float)$split['amount']."',split_type='I',status='1',`print_status`=0");
                    $order_split_id = $this->db->getLastId();
                    foreach ($split['items'] as $split_product){
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "order_split_product` SET order_split_id=".(int)$order_split_id.",product_id=".(int)$split_product['product_id']." ,quantity=".(int)$split_product['qty']."");
                    }
                }
                $this->db->query("UPDATE `" . DB_PREFIX . "order_split` SET payment_history = '" . $this->db->escape(serialize(isset($split['payments'])?$split['payments']:array())) . "' WHERE split_id = '" . (int)$split['split_id']. "'");
                if(isset($split['payments']) && !empty($split['payments'])){
                    foreach ($split['payments'] as $payment=>$amount){
                        if($amount>0){
                            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_split_total` SET order_split_id=".(int)$order_split_id.",code='payment' ,title='".$payment."' ,text='',value='".(float)$amount."',sort_order=''");
                        }
                    }
                }
            }
        }
         //Pending Orders Add
        //$pending_data=array();
       //if(isset($this->session->data['edited_pending_id'])){
        
            $index=1;
            $ind_qry=$this->db->query("SELECT `index` FROM " . DB_PREFIX . "edited_pending_orders WHERE order_id='".(int)$order_id."' ORDER BY `id` DESC LIMIT 1 ");
            if(!empty($ind_qry->row)){
                $index = $ind_qry->row['index']+1;
            }
            $this->db->query("INSERT INTO " . DB_PREFIX . "edited_pending_orders SET `order_id` = '" . (int) $order_id . "',`index`=".(int)$index.",`data`='".$this->db->escape(serialize($data))."',`payed_total`='".(float)(($data['payment_code']!='cod')?$data['total']:0)."',`user_id`='".(int) ((isset($data['user_id']))?$data['user_id']:0)."',`edited_time`='".date("Y-m-d H:i:s", time()) ."'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '19', notify = '0', comment = '', date_added = '" . date("Y-m-d H:i:s", time()) . "'");
            if(isset($data['cod_pay'])){ 
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '".(int) $data['order_status_id']."', notify = '0', comment = '".$data['cod_pay']."', date_added = '" . date("Y-m-d H:i:s", time()) . "'");
            }
       //}
       //Comment History Add
               if(isset($data['ordercomments']) && !empty($data['ordercomments']) && isset($data['order_from']) && $data['order_from']==2){
                    $this->load->model('tool/epos_history');        
                    //$comments = explode("\n", $data['comment']);
                    if(isset($data['order_from']) && $data['order_from']==2){
                        $this->db->query("DELETE FROM " . DB_PREFIX . "comment_history WHERE `customer_id` = '".(int)$data['customer_id']."' AND is_profile_comment=1");
                    }                     
                    foreach ($data['ordercomments'] as $comment) {
                        $this->model_tool_epos_history->insertcommentdata(array('comment'=>$comment['comment'],'customer_id'=>$data['customer_id'],'is_profile_comment'=>$comment['is_profile']));
                    }
                }
                if(isset($data['shipping_charge'])){
                    if(isset($data['shipping_address_id']) && $data['shipping_address_id']!=''){
                        $this->db->query("UPDATE " . DB_PREFIX . "address SET delivery_charge = '" . $data['shipping_charge'] . "' WHERE address_id = '" . (int)$data['shipping_address_id'] . "'");
                    }
                    if(isset($data['shipping_postcode']) && $data['shipping_postcode']!=''){
                        $this->db->query("UPDATE " . DB_PREFIX . "uk_postcodes SET delivery_charge = '" . $data['shipping_charge'] . "' WHERE postcode = '" .$data['shipping_postcode'] . "'");
                    }
                }
       //
        //  $this->db->query("INSERT INTO " . DB_PREFIX . "edited_pending_orders SET `order_id` = '" . (int) $order_id . "',`index`='0',`data`='".$this->db->escape(serialize($this->session->data))."',`payed_total`='".(float)(($data['payment_code']!='cod')?$data['total']:'0')."',`user_id`='".(int) ((isset($this->session->data['user_id']))?$this->session->data['user_id']:0)."',`edited_time`='".date("Y-m-d H:i:s", time()) ."'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'customer_modified'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'customer_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");   
        return $order_id;
    }

    public function getOrder($order_id) {
        $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "'");

        if ($order_query->num_rows) {
            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int) $order_query->row['payment_country_id'] . "'");

            if ($country_query->num_rows) {
                $payment_iso_code_2 = $country_query->row['iso_code_2'];
                $payment_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $payment_iso_code_2 = '';
                $payment_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int) $order_query->row['payment_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $payment_zone_code = $zone_query->row['code'];
            } else {
                $payment_zone_code = '';
            }

            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int) $order_query->row['shipping_country_id'] . "'");

            if ($country_query->num_rows) {
                $shipping_iso_code_2 = $country_query->row['iso_code_2'];
                $shipping_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $shipping_iso_code_2 = '';
                $shipping_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int) $order_query->row['shipping_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $shipping_zone_code = $zone_query->row['code'];
            } else {
                $shipping_zone_code = '';
            }

            $this->load->model('localisation/language');

            $language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

            if ($language_info) {
                $language_code = $language_info['code'];
                $language_filename = $language_info['filename'];
                $language_directory = $language_info['directory'];
            } else {
                $language_code = '';
                $language_filename = '';
                $language_directory = '';
            }

            return array(
                'order_id' => $order_query->row['order_id'],
                'display_id' => $order_query->row['display_id'],
                'invoice_no' => $order_query->row['invoice_no'],
                'invoice_prefix' => $order_query->row['invoice_prefix'],
                'store_id' => $order_query->row['store_id'],
                'store_name' => $order_query->row['store_name'],
                'store_url' => $order_query->row['store_url'],
                'customer_id' => $order_query->row['customer_id'],
                'firstname' => $order_query->row['firstname'],
                'lastname' => $order_query->row['lastname'],
                'telephone' => $order_query->row['telephone'],
                'fax' => $order_query->row['fax'],
                'email' => $order_query->row['email'],
                'payment_firstname' => $order_query->row['payment_firstname'],
                'payment_lastname' => $order_query->row['payment_lastname'],
                'payment_company' => $order_query->row['payment_company'],
                'payment_address_1' => $order_query->row['payment_address_1'],
                'payment_address_2' => $order_query->row['payment_address_2'],
                'payment_postcode' => $order_query->row['payment_postcode'],
                'payment_city' => $order_query->row['payment_city'],
                'payment_zone_id' => $order_query->row['payment_zone_id'],
                'payment_zone' => $order_query->row['payment_zone'],
                'payment_zone_code' => $payment_zone_code,
                'payment_country_id' => $order_query->row['payment_country_id'],
                'payment_country' => $order_query->row['payment_country'],
                'payment_iso_code_2' => $payment_iso_code_2,
                'payment_iso_code_3' => $payment_iso_code_3,
                'payment_address_format' => $order_query->row['payment_address_format'],
                'payment_method' => $order_query->row['payment_method'],
                'payment_code' => $order_query->row['payment_code'],
                'shipping_firstname' => $order_query->row['shipping_firstname'],
                'shipping_lastname' => $order_query->row['shipping_lastname'],
                'shipping_company' => $order_query->row['shipping_company'],
                'shipping_address_1' => $order_query->row['shipping_address_1'],
                'shipping_address_2' => $order_query->row['shipping_address_2'],
                'shipping_postcode' => $order_query->row['shipping_postcode'],
                'shipping_city' => $order_query->row['shipping_city'],
                'shipping_zone_id' => $order_query->row['shipping_zone_id'],
                'shipping_zone' => $order_query->row['shipping_zone'],
                'shipping_zone_code' => $shipping_zone_code,
                'shipping_country_id' => $order_query->row['shipping_country_id'],
                'shipping_country' => $order_query->row['shipping_country'],
                'shipping_iso_code_2' => $shipping_iso_code_2,
                'shipping_iso_code_3' => $shipping_iso_code_3,
                'shipping_address_format' => $order_query->row['shipping_address_format'],
                'shipping_method' => $order_query->row['shipping_method'],
                'shipping_code' => $order_query->row['shipping_code'],
                'comment' => $order_query->row['comment'],
                'total' => $order_query->row['total'],
                'order_status_id' => $order_query->row['order_status_id'],
                'order_status' => $order_query->row['order_status'],
                'language_id' => $order_query->row['language_id'],
                'language_code' => $language_code,
                'language_filename' => $language_filename,
                'language_directory' => $language_directory,
                'currency_id' => $order_query->row['currency_id'],
                'currency_code' => $order_query->row['currency_code'],
                'currency_value' => $order_query->row['currency_value'],
                'ip' => $order_query->row['ip'],
                'forwarded_ip' => $order_query->row['forwarded_ip'],
                'user_agent' => $order_query->row['user_agent'],
                'accept_language' => $order_query->row['accept_language'],
                'date_modified' => $order_query->row['date_modified'],
                'date_added' => $order_query->row['date_added'],
                'requested_time' => $order_query->row['requested_time'],
                'delivery_time' => $order_query->row['delivery_time'],
                'print_status' => $order_query->row['print_status'],
                'print_areas' => unserialize($order_query->row['print_areas']),
                'system_id' => $order_query->row['system_id'],
                'order_from' => $order_query->row['order_from'],
                'user_id' => $order_query->row['user_id'],
                'dinein_table_id'=>$order_query->row['dinein_table_id'],
                    'live_order_id'=>$order_query->row['live_order_id'],
                'included_orders'=>unserialize($order_query->row['included_orders']),
            );
        } else {
            return false;
        }
    }

    public function confirm($order_id, $order_status_id, $comment = '', $notify = true) {
        $order_info = $this->getOrder($order_id);

        if ($order_info && !$order_info['order_status_id']) {
            // Fraud Detection
            if ($this->config->get('config_fraud_detection')) {
                $this->load->model('checkout/fraud');

                $risk_score = $this->model_checkout_fraud->getFraudScore($order_info);

                if ($risk_score > $this->config->get('config_fraud_score')) {
                    $order_status_id = $this->config->get('config_fraud_status_id');
                }
            }

            // Blacklist
            $status = false;

            $this->load->model('account/customer');

            if ($order_info['customer_id']) {
                $results = $this->model_account_customer->getIps($order_info['customer_id']);

                foreach ($results as $result) {
                    if ($this->model_account_customer->isBlacklisted($result['ip'])) {
                        $status = true;

                        break;
                    }
                }
            } else {
                $status = $this->model_account_customer->isBlacklisted($order_info['ip']);
            }

            if ($status) {
                $order_status_id = $this->config->get('config_order_status_id');
            }
             //Pending Orders edit
            if($order_status_id!=1){//update edited order
                   $this->db->query("DELETE FROM `" . DB_PREFIX . "edited_pending_orders` WHERE order_id ='".(int)$order_id."'");
            }
            //Pending Orders edit
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $order_status_id . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "' WHERE order_id = '" . (int) $order_id . "'");
            if(isset($this->session->data['tableordering']) && isset($this->session->data['dine_table_id'])){
                $this->createLiveOrder($order_id,$order_info['payment_code']);
                 $this->websocket->connect('o-'.$order_id);
            }
            //Auto ADD Reward Points .... 
            $this->websocket->connect('o-'.$order_id);
            $this->load->model('setting/setting');
            if($this->config->get('reward_points_status')) {
                if($this->config->get('config_reward_points_autoadd') && $this->config->get('config_reawrd_order_status_id')==$order_status_id) {
                    if((float)$order_info['total']>=(float) $this->config->get('config_reward_points_min_order_amount')){
                        $this->load->model('account/reward');
                        $reward_qry=$this->db->query("SELECT SUM(reward) as reward FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
                        $this->model_account_reward->addReward('Order ID #'.$order_id, $reward_qry->row['reward'],$order_id);
                    }
                }
            }
            /*
            $this->load->model('setting/setting');
            $rewards = $this->model_setting_setting->getSetting('rewardpoints');
            if(!empty($rewards) and isset($rewards[5])){
                if($rewards[5]['autoadd']==1 and $this->config->get('config_reawrd_order_status_id')==$order_status_id){
                    if((float)$order_info['total']>=(float)$rewards[5]['amount']){
                         $this->load->model('account/reward');
                         $reward_qry=$this->db->query("SELECT SUM(reward) as reward FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
                         $this->model_account_reward->addReward('Order ID #'.$order_id, $reward_qry->row['reward'],$order_id);
                    }
                }
            }
            */
            
            //Auto Reward Points ADD ....            
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '" . (int) $order_status_id . "', notify = '1', comment = '" . $this->db->escape(($comment) ? $comment : '') . "', date_added = '" . date("Y-m-d H:i:s", time()) . "'");

            $order_product_query = $this->db->query("SELECT SUM(quantity) as qty , product_id  FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "' GROUP BY product_id");
              if($this->config->get('config_stockmanagement_status')){
                $this->db->query("DELETE FROM" . DB_PREFIX . " live_stock_data WHERE order_id = " . (int) $order_id);
            foreach ($order_product_query->rows as $order_product) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "live_stock_data SET order_id='" . (int) $order_id . "',product_id='".(int)$order_product['product_id']."',quantity='".(int)$order_product['qty']."'");
                } 
            }
            //NEW CODE
            /*
            foreach ($order_product_query->rows as $order_product) {
                $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int) $order_product['quantity'] . ") WHERE product_id = '" . (int) $order_product['product_id'] . "' AND subtract = '1'");

                $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $order_product['order_product_id'] . "'");

                foreach ($order_option_query->rows as $option) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int) $order_product['quantity'] . ") WHERE product_option_value_id = '" . (int) $option['product_option_value_id'] . "' AND subtract = '1'");
                }
            }
            */
            //NEW CODE
            $this->cache->delete('product');

            // Downloads
            $order_download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int) $order_id . "'");

            // Gift Voucher
            $this->load->model('checkout/voucher');

            $order_voucher_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int) $order_id . "'");

            foreach ($order_voucher_query->rows as $order_voucher) {
                $voucher_id = $this->model_checkout_voucher->addVoucher($order_id, $order_voucher);

                $this->db->query("UPDATE " . DB_PREFIX . "order_voucher SET voucher_id = '" . (int) $voucher_id . "' WHERE order_voucher_id = '" . (int) $order_voucher['order_voucher_id'] . "'");
            }

            // Send out any gift voucher mails
            if ($this->config->get('config_complete_status_id') == $order_status_id) {
                $this->model_checkout_voucher->confirm($order_id);
            }

            // Order Totals			
            $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order ASC");

            foreach ($order_total_query->rows as $order_total) {

                $this->load->model('total/' . $order_total['code']);

                if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
                    $this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);
                }
            }
            if($this->posclient->isPosClient()){
                return ;
            }
            // Twilio voice call
            if ($this->config->get('config_twilio_order_alert')) {
                $this->load->model('setting/twilio');
                $this->model_setting_twilio->startcall(1, $order_id);
            }
            
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status_update_notification WHERE type='orderalert'");
            //$this->log->write(print_r($query->row,true));
            $notification_data =unserialize($query->row['notify_data']);
            
            $owner_email_status = (isset($notification_data['owner_email']) && isset($notification_data['owner_email']['status']) && $notification_data['owner_email']['status']=='1')?true:false;
            $customer_email_status = (isset($notification_data['customer_email']) && isset($notification_data['customer_email']['status']) && $notification_data['customer_email']['status']=='1')?true:false;
            $owner_sms_status =(isset($notification_data['owner_sms']) && isset($notification_data['owner_sms']['status']) && $notification_data['owner_sms']['status']=='1')?true:false;
            $customer_sms_status =(isset($notification_data['customer_sms']) && isset($notification_data['customer_sms']['status']) && $notification_data['customer_sms']['status']=='1')?true:false;
            $this->sendNotification($order_id, $order_status_id,$order_info);
            // Send out order confirmation mail
            $language = new Language($order_info['language_directory']);
            $language->load($order_info['language_filename']);
            $language->load('mail/order');

            $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int) $order_status_id . "' AND language_id = '" . (int) $order_info['language_id'] . "'");

            if ($order_status_query->num_rows) {
                $order_status = $order_status_query->row['name'];
            } else {
                $order_status = '';
            }

            $subject = sprintf($language->get('text_new_subject'), $order_info['store_name'], ($this->config->get('config_order_id_hide')?'':$order_id));

            // HTML Mail
            $template = new Template();

            $template->data['title'] = sprintf($language->get('text_new_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), ($this->config->get('config_order_id_hide')?'':$order_id));

            $template->data['text_greeting'] = sprintf($language->get('text_new_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
            $template->data['text_storephone'] = $language->get('text_storephone') . $this->config->get('config_telephone');
            $template->data['text_orderfooter'] = $language->get('text_orderfooter');
            $template->data['text_orderfooter1'] = $order_info['store_name'] . $language->get('text_orderfooter1');


            $template->data['text_link'] = $language->get('text_new_link');
            $template->data['text_download'] = $language->get('text_new_download');
            $template->data['text_order_detail'] = $language->get('text_new_order_detail');
            $template->data['text_instruction'] = $language->get('text_new_instruction');
            $template->data['text_order_id'] = $language->get('text_new_order_id');
            $template->data['text_date_added'] = $language->get('text_new_date_added');
            $template->data['text_payment_method'] = $language->get('text_new_payment_method');
            $template->data['text_shipping_method'] = $language->get('text_new_shipping_method');
            $template->data['text_email'] = $language->get('text_new_email');
            $template->data['text_telephone'] = $language->get('text_new_telephone');
            $template->data['text_ip'] = $language->get('text_new_ip');
            $template->data['text_payment_address'] = $language->get('text_new_payment_address');
            $template->data['text_shipping_address'] = $language->get('text_new_shipping_address');
            $template->data['text_product'] = $language->get('text_new_product');
            $template->data['text_model'] = $language->get('text_new_model');
            $template->data['text_quantity'] = $language->get('text_new_quantity');
            $template->data['text_price'] = $language->get('text_new_price');
            $template->data['text_total'] = $language->get('text_new_total');
            $template->data['text_footer'] = $language->get('text_new_footer');
            $template->data['text_powered'] = $language->get('text_new_powered');

            $template->data['logo'] = HTTP_IMAGE . $this->config->get('config_logo');
            $template->data['store_name'] = $order_info['store_name'];
            $template->data['store_address'] = $this->config->get('config_address');
            if($this->config->get('config_facebook_url')){
                $template->data['fb_url'] = $this->config->get('config_facebook_url');
            }
            if($this->config->get('config_twitter_url')){
                $template->data['twitter_url'] = $this->config->get('config_twitter_url');
            }
            if($this->config->get('config_linkedin_url')){
                $template->data['linkedin_url'] = $this->config->get('config_linkedin_url');
            }
            if($this->config->get('config_gplus_url')){
                $template->data['gplus_url'] = $this->config->get('config_gplus_url');
            }
            //config_instagram_url
             if($this->config->get('config_instagram_url')){
                $template->data['instagram_url'] = $this->config->get('config_instagram_url');
            }
            if($this->config->get('config_iphone_appurl')){
                $template->data['iphoneapp_url'] = $this->config->get('config_iphone_appurl');
            }
            if($this->config->get('config_android_appurl')){
                $template->data['androidapp_url'] = $this->config->get('config_android_appurl');
            }
            if($this->config->get('config_windows_appurl')){
                $template->data['windows_appurl'] = $this->config->get('config_windows_appurl');
            }
            $template->data['header_image'] = HTTP_IMAGE . $this->config->get('config_email_header');
            $template->data['offer_image'] = HTTP_IMAGE . $this->config->get('config_email_offer');
            $template->data['footer_image'] = HTTP_IMAGE . $this->config->get('config_email_footer');
            $template->data['email_register_header'] = HTTP_IMAGE . $this->config->get('config_email_register_header');
            $template->data['email_forgotten_header'] = HTTP_IMAGE . $this->config->get('config_email_forgotten_header');
            $template->data['store_telephone'] = $this->config->get('config_telephone');
            $template->data['store_url'] = $order_info['store_url'];
            $template->data['customer_id'] = $order_info['customer_id'];
            $template->data['offer_url'] = $order_info['store_url'] .  $this->config->get('config_email_offer_url');
            if($order_info['store_url']!=''){
                $template->data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;
            }else{
                $template->data['link']='';
            }
            if ($order_download_query->num_rows && $order_info['store_url']!='') {
                $template->data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
            } else {
                $template->data['download'] = '';
            }
            $template->data['config_order_id_hide'] = $this->config->get('config_order_id_hide');

            $template->data['order_id'] = ($this->config->get('config_order_id_hide')?'':$order_id);
            $template->data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
            $template->data['payment_method'] = $order_info['payment_method'];
            $template->data['shipping_method'] = $order_info['shipping_method'];
            $template->data['email'] = $order_info['email'];
            $template->data['telephone'] = $order_info['telephone'];
            $template->data['ip'] = $order_info['ip'];

            if ($comment && $notify) {
                $template->data['comment'] = nl2br($comment);
            } else {
                $template->data['comment'] = '';
            }

            if ($order_info['comment']) {
                $template->data['comment']  .= nl2br($order_info['comment']);
            }

            if ($order_info['payment_address_format']) {
                $format = $order_info['payment_address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}';
            }

            $find = array(
                '{firstname}',
                '{lastname}',
                '{company}',
                '{address_1}',
                '{address_2}',
                '{city}',
                '{postcode}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );

            $replace = array(
                'firstname' => $order_info['payment_firstname'],
                'lastname' => $order_info['payment_lastname'],
                'company' => $order_info['payment_company'],
                'address_1' => $order_info['payment_address_1'],
                'address_2' => $order_info['payment_address_2'],
                'city' => $order_info['payment_city'],
                'postcode' => $order_info['payment_postcode'],
                'zone' => $order_info['payment_zone'],
                'zone_code' => $order_info['payment_zone_code'],
                'country' => $order_info['payment_country']
            );

            $template->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

            if ($order_info['shipping_address_format']) {
                $format = $order_info['shipping_address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}';
            }

            $find = array(
                '{firstname}',
                '{lastname}',
                '{company}',
                '{address_1}',
                '{address_2}',
                '{city}',
                '{postcode}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );

            $replace = array(
                'firstname' => $order_info['shipping_firstname'],
                'lastname' => $order_info['shipping_lastname'],
                'company' => $order_info['shipping_company'],
                'address_1' => $order_info['shipping_address_1'],
                'address_2' => $order_info['shipping_address_2'],
                'city' => $order_info['shipping_city'],
                'postcode' => $order_info['shipping_postcode'],
                'zone' => $order_info['shipping_zone'],
                'zone_code' => $order_info['shipping_zone_code'],
                'country' => $order_info['shipping_country']
            );

            $template->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

            // Products
            $template->data['products'] = array();
            $bundled_products =array();
            $bundled_data=array();
            $order_products = $this->getFormattedOrderProducts($order_id);
            //$this->log->write(print_r($order_products,true));
            foreach($order_products as $order_product){
                $order_product['price']=$this->currency->format($order_product['price'] + ($this->config->get('config_tax') ? $order_product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']);
                $order_product['total']=$this->currency->format($order_product['total'] + ($this->config->get('config_tax') ? ($order_product['tax'] * $order_product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']);                
                $template->data['products'][]=$order_product;
                
            }
            /*REMOVE THIS CODE ON UPDATION
            foreach ($order_product_query->rows as $product) {
                $option_data = array();

                $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $product['order_product_id'] . "'");

                foreach ($order_option_query->rows as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['value'];
                    } else {
                        $value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
                    }

                    $option_data[] = array(
                        'name' => $option['name'],
                        'value' => html_entity_decode($value)
                    );
                }
                 $order_topping_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $product['order_product_id'] . "'");
                
                $topping_data =$order_topping_query->rows; 
               
                $order_dressing_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_dressings WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $product['order_product_id'] . "'");
                
                $dressing_data = $order_dressing_query->rows;
                
                $bundled_products=$this->getOrderBundleProducts($order_id, $product['order_product_id']);
                if(!empty($bundled_products)){
                 
                    $bundled_data=array();
                    $bundled_product_options=array();
                   foreach ($bundled_products as $k=>$bundled_product) {
                       $bundled_data[$k]=$bundled_product;
                       $bundled_product_options=$this->getOrderBundledProductoptions($bundled_product['order_bundle_product_id']);
                       if(!empty($bundled_product_options)){
                         $bundled_data[$k]['options']= $bundled_product_options; 
                       }
                        $bundled_product_toppings=$this->getOrderBundledProducttoppings($bundled_product['order_bundle_product_id']);
                       if(!empty($bundled_product_toppings)){
                         $bundled_data[$k]['toppings']= $bundled_product_toppings; 
                       } 
                       $bundled_product_dressings=$this->getOrderBundledProductdressings($bundled_product['order_bundle_product_id']);
                       if(!empty($bundled_product_dressings)){
                         $bundled_data[$k]['dressings']= $bundled_product_dressings; 
                       } 
                   }
                }
                
                $template->data['products'][] = array(
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                      'bundle'=>(isset($bundled_data))?$bundled_data:array(),
                    'toppings'=>$topping_data,
                    'dressings'=>$dressing_data,
                    'quantity' => $product['quantity'],
                    'price' => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'total' => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
                );
                 unset($bundled_data);
            }
            */
            // Vouchers
            $template->data['vouchers'] = array();

            foreach ($order_voucher_query->rows as $voucher) {
                $template->data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'amount' => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
                );
            }

            $template->data['totals'] = $order_total_query->rows;

            // requested Time
            $template->data['text_requested_time'] = $language->get('text_requested_time');
            if(strtotime($order_info['requested_time']) <= strtotime($order_info['date_added']  )) {
                $template->data['requested_time'] = $language->get('text_requested_time_asap');
            }
            else {
                $template->data['requested_time'] = date(" h:i A (l) - (d/m/y)", strtotime($order_info['requested_time']));
            }
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/order.tpl')) {
                $html = $template->fetch($this->config->get('config_template') . '/template/mail/order.tpl');
            } else {
                $html = $template->fetch('default/template/mail/order.tpl');
            }
            // Text Mail
            $text = sprintf($language->get('text_new_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8')) . "\n\n";
             if (!$this->config->get('config_order_id_hide')) {
            $text .= $language->get('text_new_order_id') . ' ' . $order_id . "\n";
             }
            $text .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n";
            $text .= $language->get('text_new_order_status') . ' ' . $order_status . "\n\n";

            if ($comment && $notify) {
                $text .= $language->get('text_new_instruction') . "\n\n";
                $text .= $comment . "\n\n";
            }

            // Products
            $text .= $language->get('text_new_products') . "\n";
            $bundled_data=array();   
             foreach($order_products as $product){
                $text .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . ') ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";
                foreach($product['option'] as $option){
                    $text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($option['value']) > 20 ? utf8_substr($option['value'], 0, 20) . '..' : $option['value']) . "\n";
                }
                foreach ($product['toppings'] as $topping) {
                    $text .= chr(9) . '-' . $topping['name'] . ' ' . $topping['value'] . ' x '.$topping['quantity']."\n";
                }
                foreach ($product['dressings'] as $drssing) {
                    $text .= chr(9) . '-' . $drssing['name'] . ' ' . $drssing['value'] ."\n";
                }
                if(isset($product['bundle']) && !empty($product['bundle'])){
                    foreach ($product['bundle'] as $bundle) {
                        $text .= chr(9) . '-' . $bundle['name']  . "\n";
                        if(isset($bundle['options'])) {
                            foreach ($bundle['options'] as $bun_option) {
                                 $text .= chr(9) . '--' . $bun_option['name'] . ' ' . (utf8_strlen($bun_option['value']) > 20 ? utf8_substr($bun_option['value'], 0, 20) . '..' : $bun_option['value']) . "\n";
                            }
                        }
                        if(isset($bundle['toppings'])) {
                            foreach ($bundle['toppings'] as $bun_topping) {
                                 $text .= chr(9) . '--' . $bun_topping['quantity'] . ' x ' . (utf8_strlen($bun_topping['value']) > 20 ? utf8_substr($bun_topping['value'], 0, 20) . '..' : $bun_topping['value']) . "\n";
                            }
                        }
                        if(isset($bundle['dressings'])) {
                            foreach ($bundle['dressings'] as $bun_dressing) {
                                 $text .= chr(9) . '--' . (utf8_strlen($bun_dressing['value']) > 20 ? utf8_substr($bun_dressing['value'], 0, 20) . '..' : $bun_dressing['value']) . "\n";
                            }
                        }
                    }
                }
            }
            /*REMOVE THIS CODE ON UPDATION
            foreach ($order_product_query->rows as $product) {
                $text .= $product['quantity'] . 'x ' . $product['name'] . '  ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";

                $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . $product['order_product_id'] . "'");

                foreach ($order_option_query->rows as $option) {
                    $text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($option['value']) > 20 ? utf8_substr($option['value'], 0, 20) . '..' : $option['value']) . "\n";
                }            
                
                 $order_topping_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $product['order_product_id'] . "'");
                 
                $topping_data = array(); 
                foreach ($order_topping_query->rows as $topping) {
                    $text .= chr(9) . '-' . $topping['name'] . ' ' . $topping['value'] . ' x '.$topping['quantity']."\n";
                }
                $order_dressing_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_dressings WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $product['order_product_id'] . "'");
                foreach ($order_dressing_query->rows as $drssing) {
                    $text .= chr(9) . '-' . $drssing['name'] . ' ' . $drssing['value'] ."\n";
                } 
                
             $bundled_products=$this->getOrderBundleProducts($order_id, $product['order_product_id']);
                if(!empty($bundled_products)){
                   foreach ($bundled_products as $k=>$bundled_product) {
                       $bundled_data[$k]=$bundled_product;
                       $bundled_product_options=$this->getOrderBundledProductoptions($bundled_product['order_bundle_product_id']);
                       if(!empty($bundled_product_options)){
                         $bundled_data[$k]['options']= $bundled_product_options; 
                       } 
                       $bundled_product_toppings=$this->getOrderBundledProducttoppings($bundled_product['order_bundle_product_id']);
                       if(!empty($bundled_product_toppings)){
                         $bundled_data[$k]['toppings']= $bundled_product_toppings; 
                       } 
                       $bundled_product_dressings=$this->getOrderBundledProductdressings($bundled_product['order_bundle_product_id']);
                       if(!empty($bundled_product_dressings)){
                         $bundled_data[$k]['dressings']= $bundled_product_dressings; 
                       }
                   }
                }
                if(!empty($bundled_data)){
                    foreach ($bundled_data as $bundle) {
                        $text .= chr(9) . '-' . $bundle['name']  . "\n";
                        if(isset($bundle['options'])) {
                            foreach ($bundle['options'] as $bun_option) {
                                 $text .= chr(9) . '--' . $bun_option['name'] . ' ' . (utf8_strlen($bun_option['value']) > 20 ? utf8_substr($bun_option['value'], 0, 20) . '..' : $bun_option['value']) . "\n";
                            }
                        }
                         if(isset($bundle['toppings'])) {
                            foreach ($bundle['toppings'] as $bun_topping) {
                                 $text .= chr(9) . '--' . $bun_topping['quantity'] . ' x ' . (utf8_strlen($bun_topping['value']) > 20 ? utf8_substr($bun_topping['value'], 0, 20) . '..' : $bun_topping['value']) . "\n";
                            }
                        }
                        if(isset($bundle['dressings'])) {
                            foreach ($bundle['dressings'] as $bun_dressing) {
                                 $text .= chr(9) . '--' . (utf8_strlen($bun_dressing['value']) > 20 ? utf8_substr($bun_dressing['value'], 0, 20) . '..' : $bun_dressing['value']) . "\n";
                            }
                        }
                    }
                }
            }
            */
            foreach ($order_voucher_query->rows as $voucher) {
                $text .= '1x ' . $voucher['description'] . ' ' . $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
            }

            $text .= "\n";

            $text .= $language->get('text_new_order_total') . "\n";

            foreach ($order_total_query->rows as $total) {
                $text .= $total['title'] . ': ' . html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
            }

            $text .= "\n";

            if ($order_info['customer_id'] && $order_info['store_url']!='') {
                $text .= $language->get('text_new_link') . "\n";
                $text .= $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id . "\n\n";
            }

            if ($order_download_query->num_rows && $order_info['store_url']!='') {
                $text .= $language->get('text_new_download') . "\n";
                $text .= $order_info['store_url'] . 'index.php?route=account/download' . "\n\n";
            }

            if ($order_info['comment']) {
                $text .= $language->get('text_new_comment') . "\n\n";
                $text .= $order_info['comment'] . "\n\n";
            }

            $text .= $language->get('text_new_footer') . "\n\n";

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($order_info['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($order_info['store_name']);
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setHtml($html);
            $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
            if(!$customer_email_status && $notify)
            {
                $mail->send();
            }
            

            // Admin Alert Mail
            if (!$owner_email_status && $this->config->get('config_alert_mail')) {

                $subject = sprintf($language->get('text_new_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), ($this->config->get('config_order_id_hide')?'':$order_id));

                $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                $mail->setTo($this->config->get('config_email'));
                if($notify)
                {
                    $mail->send();
                }

                // Send to additional alert emails
                $emails = explode(',', $this->config->get('config_alert_emails'));

                foreach ($emails as $email) {
                    if ($email && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
                        $mail->setTo($email);
                        if($notify)
                        {
                            $mail->send();
                        }
                    }
                }
            }
            // Mail to printer
            if ($this->config->get('config_order_mail_toprinter')) {

                $subject = sprintf($language->get('text_new_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $order_id);

                // Text 
                $text = $language->get('text_new_received') . "\n\n";
                if (!$this->config->get('config_order_id_hide')) {
                $text .= $language->get('text_new_order_id') . ' ' . $order_id . "\n";
                }
                $text .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . " : " . date($language->get('time_format')) . "\n";
                $text .= $language->get('text_new_order_status') . ' ' . $order_status . "\n\n";
                $requested_time = "ASAP";
                if (strtotime($order_info["requested_time"]) > strtotime($order_info["date_added"])) {
                    $requested_time = date("d-m-y H:i", strtotime($order_info["requested_time"]));
                }
                $text .= 'Requested Time :' . $requested_time . "\n";
                $text .= $language->get('text_new_products') . "\n";
                $bundled_data=array();   
                foreach($order_products as $product){
                    $text .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . ') ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";
                    foreach($product['option'] as $option){
                         if ($option['type'] != 'file') {
                              $value = $option['value'];
                          } else {
                              $value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
                          }
                          $text .= chr(9) . '-' . $value . "\n";
                    }
                    foreach ($product['toppings'] as $topping) {
                        $text .= chr(9) . '-' . $topping['value'] . ' x '.$topping['quantity']."\n";
                    }
                    foreach ($product['dressings'] as $drssing) {
                         $text .= chr(9) . '-' . $drssing['name'] . ' ' . $drssing['value'] ."\n";
                    }
                    if(isset($product['bundle']) && !empty($product['bundle'])){
                        foreach ($product['bundle'] as $bundle) {
                            $text .=  chr(9) . '-'.$bundle['name']  . "\n";
                            if(isset($bundle['options'])) {
                                foreach ($bundle['options'] as $bun_option) {
                                     $text .=chr(9) . '--'.$bun_option['name'] . ' : ' . $bun_option['value'] . "\n";
                                }
                            }
                            if(isset($bundle['toppings'])) {
                                foreach ($bundle['toppings'] as $bun_topping) {
                                     $text .= chr(9) . '--' . $bun_topping['quantity'] . ' x ' . (utf8_strlen($bun_topping['value']) > 20 ? utf8_substr($bun_topping['value'], 0, 20) . '..' : $bun_topping['value']) . "\n";
                                }
                            }
                            if(isset($bundle['dressings'])) {
                                foreach ($bundle['dressings'] as $bun_dressing) {
                                     $text .= chr(9) . '--' .  (utf8_strlen($bun_dressing['value']) > 20 ? utf8_substr($bun_dressing['value'], 0, 20) . '..' : $bun_dressing['value']) . "\n";
                                }
                            }
                        }
                    }
                }
                
                  /*REMOVE THIS CODE ON UPDATION
                foreach ($order_product_query->rows as $product) {
                    $text .= $product['quantity'] . 'x ' . $product['name'] . '  ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";

                    $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . $product['order_product_id'] . "'");

                    foreach ($order_option_query->rows as $option) {
                        if ($option['type'] != 'file') {
                            $value = $option['value'];
                        } else {
                            $value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
                        }


                        $text .= chr(9) . '-' . $value . "\n";
                    }
                    
                    $order_topping_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $product['order_product_id'] . "'");
                    $topping_data = array(); 
                    foreach ($order_topping_query->rows as $topping) {
                        $text .= chr(9) . '-' . $topping['value'] . ' x '.$topping['quantity']."\n";
                    }
                    $order_dressing_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_dressings WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $product['order_product_id'] . "'");
                    foreach ($order_dressing_query->rows as $drssing) {
                        $text .= chr(9) . '-' . $drssing['name'] . ' ' . $drssing['value'] ."\n";
                    }
               
                     $bundled_products=$this->getOrderBundleProducts($order_id, $product['order_product_id']);
                if(!empty($bundled_products)){
                    
                    
                   foreach ($bundled_products as $k=>$bundled_product) {
                       $bundled_data[$k]=$bundled_product;
                       $bundled_product_options=$this->getOrderBundledProductoptions($bundled_product['order_bundle_product_id']);
                       if(!empty($bundled_product_options)){
                         $bundled_data[$k]['options']= $bundled_product_options; 
                       } 
                       $bundled_product_toppings=$this->getOrderBundledProducttoppings($bundled_product['order_bundle_product_id']);
                       if(!empty($bundled_product_toppings)){
                         $bundled_data[$k]['toppings']= $bundled_product_toppings; 
                       } 
                       $bundled_product_dressings=$this->getOrderBundledProductdressings($bundled_product['order_bundle_product_id']);
                       if(!empty($bundled_product_dressings)){
                         $bundled_data[$k]['dressings']= $bundled_product_dressings; 
                       }
                   }
                }
                 if(!empty($bundled_data)){
                    foreach ($bundled_data as $bundle) {
                        $text .=  chr(9) . '-'.$bundle['name']  . "\n";
                        if(isset($bundle['options'])) {
                            foreach ($bundle['options'] as $bun_option) {
                                
                                 $text .=chr(9) . '--'.$bun_option['name'] . ' : ' . $bun_option['value'] . "\n";
                                 
                            }
                        }
                         if(isset($bundle['toppings'])) {
                            foreach ($bundle['toppings'] as $bun_topping) {
                                 $text .= chr(9) . '--' . $bun_topping['quantity'] . ' x ' . (utf8_strlen($bun_topping['value']) > 20 ? utf8_substr($bun_topping['value'], 0, 20) . '..' : $bun_topping['value']) . "\n";
                            }
                        }
                        if(isset($bundle['dressings'])) {
                            foreach ($bundle['dressings'] as $bun_dressing) {
                                 $text .= chr(9) . '--' .  (utf8_strlen($bun_dressing['value']) > 20 ? utf8_substr($bun_dressing['value'], 0, 20) . '..' : $bun_dressing['value']) . "\n";
                            }
                        }
                    }
                }
                }
                */
            
            
                foreach ($order_voucher_query->rows as $voucher) {
                    $text .= '1x ' . $voucher['description'] . ' ' . $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
                }

                $text .= "\n";

                $text .= $language->get('text_new_order_total') . "\n";

                foreach ($order_total_query->rows as $total) {
                    $text .= $total['title'] . ': ' . html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
                }


                if ($order_info['comment']) {
                    $text .= $language->get('text_new_comment') . "\n\n";
                    $text .= $order_info['comment'] . "\n\n";
                }


                $text .= $language->get('text_new_telephone') . $template->data['telephone'] . "\n";

                $text .= $language->get('text_requested_time') . $template->data['requested_time'] . "\n";


                $text .= $language->get('text_new_shipping_address') . ' : ' . "\n" . str_replace('<br />', "\n", $template->data['shipping_address']);

                $text .= "\n";
                $text .= $language->get('text_new_payment_address') . ' : ' . "\n" . str_replace('<br />', "\n", $template->data['payment_address']);

                $text .= "\n";

                $text .= 'Previous Orders : ' . $previous_orders;
                $text .= "\n";

                $text .= 'Payment Method : ' . $order_info['payment_method'];
                $text .= "\n";

                $mail = new Mail();
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->hostname = $this->config->get('config_smtp_host');
                $mail->username = $this->config->get('config_smtp_username');
                $mail->password = $this->config->get('config_smtp_password');
                $mail->port = $this->config->get('config_smtp_port');
                $mail->timeout = $this->config->get('config_smtp_timeout');
                $mail->setTo($this->config->get('config_toprinter_email'));
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender($order_info['store_name']);
                $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
                if($notify)
                {
                    $mail->send();
                }
            }
            // SMS TO CUSTOMER & USING DEFAUT SMI API & Yes
            if (!$customer_sms_status && $this->config->get('config_default_sms_api') && $this->config->get('config_sms_new_order_customer')) { 
                $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "'");
                $ph_msg = '';
                $ph_msg .=html_entity_decode($this->config->get('config_name')) . "\n";
                $ph_msg .= $language->get('text_new_order_id') . ' ' . $order_id . "\n";

                $ph_msg .= 'Delivery:- ' . $order_query->row['shipping_method'] . "\n\n";
                foreach($order_products as $product){
                    $text = $product['quantity'] . ' x ' . $product['name'] . "\n";
                    foreach($product['option'] as $option){
                        $text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($option['value']) > 20 ? utf8_substr($option['value'], 0, 20) . '..' : $option['value']) . "\n";
                    }
                    $ph_msg .=$text;
                }


                $ph_msg .= "\n\n";
               // $ph_msg .= $order_query->row['shipping_address_1'] . "\n";
               // $ph_msg .= $order_query->row['shipping_address_2'] . "\n";
              //  $ph_msg .= 'PH:- ' . $order_query->row['telephone'] . "\n";
                
                /*if ($order_query->row['payment_code'] == 'cod') {
                    $ph_msg .='PAID' . "\n";
                } else {
                    $ph_msg .='UNPAID' . "\n";
                }
                */
                $ph_msg .=$order_query->row['payment_method'] . "\n";
                $this->load->model('tool/sms');
                $response = $this->model_tool_sms->sendSingleSMS($ph_msg, $order_query->row['telephone'],$this->config->get('config_default_sms_api'));
                $this->log->write('sms_customer New :' . print_r($response,true));
            }
            
            if (!$owner_sms_status && $this->config->get('config_sms_owner_order_alert') && $this->config->get('config_sms_owner_phone') &&  $this->config->get('config_default_owner_sms_api')) {
                $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "'");
                $ph_msg = '';
                $ph_msg .=html_entity_decode($this->config->get('config_name')) . "\n";
                $ph_msg .= $language->get('text_new_order_id') . ' ' . $order_id . "\n";

                $ph_msg .= 'Delivery:- ' . $order_query->row['shipping_method'] . "\n\n";
                foreach($order_products as $product){
                    $text = $product['quantity'] . ' x ' . $product['name'] . "\n";
                    foreach($product['option'] as $option){
                        $text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($option['value']) > 20 ? utf8_substr($option['value'], 0, 20) . '..' : $option['value']) . "\n";
                    }
                    $ph_msg .=$text;
                }
                
                
                $ph_msg .= "\n\n";
                $ph_msg .= $order_query->row['shipping_address_1'] . "\n";
                $ph_msg .= $order_query->row['shipping_address_2'] . "\n";
                $ph_msg .= 'PH:- ' . $order_query->row['telephone'] . "\n";
                
                /*if ($order_query->row['payment_code'] == 'cod') {
                    $ph_msg .='PAID' . "\n";
                } else {
                    $ph_msg .='UNPAID' . "\n";
                }
                */
                $ph_msg .=$order_query->row['payment_method'] . "\n";
                $this->load->model('tool/sms');
                $response = $this->model_tool_sms->sendSingleSMS($ph_msg, $this->config->get('config_sms_owner_phone'),$this->config->get('config_default_owner_sms_api'));
                $this->log->write('sms_owner_phone :' . print_r($response,true));
            }
            
            
            // OWNER APP PUSH
            if($this->config->get('ownerappapi_firebase_api_key')) {
                $devicetokens = array();
                $query = $this->db->query("SELECT device_token  FROM  " . DB_PREFIX . "app_owner  group by device_token");	
                foreach ($query->rows  as $owner) {
                    $devicetokens[]=$owner['device_token'];
        }
		$push_data = array('devicetoken'=>$devicetokens, 'title'=> $this->config->get('config_ownerappapi_notification_title'),'message'=>'New Order Placed', 'data'=>array(
                    'order_id' =>$order_id,
                    'customer_id'=>$order_info['customer_id'],
                    'action' => 'listctrl'
                    
                ));
                $fcm = new FCM($this->config->get('ownerappapi_firebase_api_key'));	
                $response = $fcm->send($push_data);
                //$this->log->write(print_r($response,true));
    }
    
        }
    }

    public function update($order_id, $order_status_id, $comment = '', $notify = false) {
        $order_info = $this->getOrder($order_id);
        if ($order_info && $order_info['order_status_id']) {
            // Fraud Detection
            if ($this->config->get('config_fraud_detection')) {
                $this->load->model('checkout/fraud');

                $risk_score = $this->model_checkout_fraud->getFraudScore($order_info);

                if ($risk_score > $this->config->get('config_fraud_score')) {
                    $order_status_id = $this->config->get('config_fraud_status_id');
                }
            }

            // Blacklist
            $status = false;

            $this->load->model('account/customer');

            if ($order_info['customer_id']) {

                $results = $this->model_account_customer->getIps($order_info['customer_id']);

                foreach ($results as $result) {
                    if ($this->model_account_customer->isBlacklisted($result['ip'])) {
                        $status = true;

                        break;
                    }
                }
            } else {
                $status = $this->model_account_customer->isBlacklisted($order_info['ip']);
            }

            if ($status) {
                $order_status_id = $this->config->get('config_order_status_id');
            }
             // prevent status to pending 
            if (  $order_info['order_status_id'] !=  $this->config->get('config_order_status_id') && $order_status_id == $this->config->get('config_order_status_id')  ) {
                $order_status_id =  $order_info['order_status_id'] ;
            }
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $order_status_id . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "' WHERE order_id = '" . (int) $order_id . "'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '" . (int) $order_status_id . "', notify = '" . (int) $notify . "', comment = '" . $this->db->escape($comment) . "', date_added = '" . date("Y-m-d H:i:s", time()) . "'");

            // Send out any gift voucher mails
            if ($this->config->get('config_complete_status_id') == $order_status_id) {
                $this->load->model('checkout/voucher');

                $this->model_checkout_voucher->confirm($order_id);
            }

            if ($notify) {
                $language = new Language($order_info['language_directory']);
                $language->load($order_info['language_filename']);
                $language->load('mail/order');

                $subject = sprintf($language->get('text_update_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

                $message = $language->get('text_update_order') . ' ' . $order_id . "\n";
                $message .= $language->get('text_update_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

                $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int) $order_status_id . "' AND language_id = '" . (int) $order_info['language_id'] . "'");

                if ($order_status_query->num_rows) {
                    $message .= $language->get('text_update_order_status') . "\n\n";
                    $message .= $order_status_query->row['name'] . "\n\n";
                }

                if ($order_info['customer_id']) {
                    $message .= $language->get('text_update_link') . "\n";
                    $message .= $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id . "\n\n";
                }

                if ($comment) {
                    $message .= $language->get('text_update_comment') . "\n\n";
                    $message .= $comment . "\n\n";
                }

                $message .= $language->get('text_update_footer');

                #mail template starts here
                $template = new Template();

                $template->data['updated_shipping_text'] = '';

                if($order_info['store_id']) {
                    $this->load->model('setting/setting');
                    $storeinfo = $this->model_setting_setting->getSetting('config');
                    $store_telephone = $storeinfo['config_telephone'];
                } else {
                    $store_telephone = $this->config->get('config_telephone');
                }

                if($order_info['delivery_time']!=='0000-00-00 00:00:00' && strtotime($order_info['delivery_time']) > strtotime($order_info['date_added']) ) {
                    $delivery_time = date('d/m/Y h:i A', $order_info['delivery_time']);
                    if($order_info['shipping_code'] == 'home.home') {
                        $template->data['updated_shipping_text'] = sprintf($language->get('text_delivery_time_home'),$delivery_time );
                    } else {
                        $template->data['updated_shipping_text'] = sprintf($language->get('text_delivery_time_collect'),$delivery_time );
                    }
                } else {
                    $delivery_time = '';
                }

                $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
                $order_status_text = '';
                if ($order_status_query->num_rows) {
                    $order_status_text = $order_status_query->row['name'];
                }

                if($order_status_id==$this->config->get('config_complete_status_id')) {
                    $template->data['text_order_update'] = sprintf($language->get('text_accepted'),$order_info['store_name'],$order_status_text);
                    $template->data['text_content'] = sprintf($language->get('text_content'), $store_telephone);						
                } else if($order_status_id==$this->config->get('remoteprint_rejected_order_status_id') || $order_status_id==$this->config->get('pos_order_cancel_status')) {
                    $template->data['text_order_update'] = sprintf($language->get('text_cancelled'),$order_info['store_name'],$order_status_text);
                    $template->data['text_content'] = '';
                } else {
                    $template->data['text_order_update'] = sprintf($language->get('text_order_update'),$order_info['store_name'],$order_status_text);
                    $template->data['text_content'] = sprintf($language->get('text_content'), $store_telephone);
                }

                $template->data['text_order_detail'] = $language->get('text_order_detail');
                $template->data['text_order_id'] = $language->get('text_order_id');
                $template->data['text_requested_time'] = $language->get('text_requested_time');
                $template->data['text_link'] = $language->get('text_link');
                $template->data['title'] = $language->get('text_title');
                $template->data['text_date_added'] = $language->get('text_date_added');
                
                $template->data['text_payment_method'] = $language->get('text_payment_method');
                $template->data['text_shipping_method'] = $language->get('text_shipping_method');
                $template->data['text_requested_time'] = $language->get('text_requested_time');
                $template->data['text_telephone'] = $language->get('text_telephone');
                $template->data['text_ip'] = $language->get('text_ip');
                $template->data['text_payment_address'] = $language->get('text_payment_address');
                $template->data['text_shipping_address'] = $language->get('text_shipping_address');
                $template->data['text_product'] = $language->get('text_product');
                $template->data['text_quantity'] = $language->get('text_quantity');
                $template->data['text_price'] = $language->get('text_price');
                $template->data['text_total'] = $language->get('text_total');
                $template->data['text_email'] = $language->get('text_email');

                if($this->config->get('config_email_header')) {
                    $template->data['header_image'] = HTTP_IMAGE . $this->config->get('config_email_header');
                } else {
                    $template->data['header_image'] = '';
                }
                if($this->config->get('config_email_offer')) {
                    $template->data['offer_image'] = HTTP_IMAGE . $this->config->get('config_email_offer');
                } else {
                    $template->data['offer_image'] = '';
                }
                if($this->config->get('config_email_footer')) {
                    $template->data['footer_image'] = HTTP_IMAGE . $this->config->get('config_email_footer');
                } else {
                    $template->data['footer_image'] = '';
                }
                
                if($order_info['store_url']!=''){
                    $template->data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;
                }else{
                    $template->data['link']='';
                }

                $template->data['offer_url'] = $order_info['store_url'] .  $this->config->get('config_email_offer_url');
                $template->data['order_id'] = ($this->config->get('config_order_id_hide')?'':$order_id);
                $template->data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
                $template->data['payment_method'] = $order_info['payment_method'];
                $template->data['shipping_method'] = $order_info['shipping_method'];
                $template->data['email'] = $order_info['email'];
                $template->data['telephone'] = $order_info['telephone'];
                $template->data['ip'] = $order_info['ip'];
                $template->data['comment'] = nl2br($comment);
                $template->data['store_name'] = $order_info['store_name'];
                $template->data['store_url'] = $order_info['store_url'];
                $template->data['customer_id'] = $order_info['customer_id'];
                $template->data['store_telephone'] = $this->config->get('config_telephone');

                if(strtotime($order_info['date_added']) < strtotime($order_info['requested_time'])) {
                    $template->data['requested_time'] = date('d/m/Y h:i a',strtotime($order_info['requested_time']));
                } else {
                    $template->data['requested_time'] = 'As soon as possible';//$order_info['requested_time'];
                }


                if ($order_info['payment_address_format']) {
                    $format = $order_info['payment_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}';
                }

                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );

                $replace = array(
                    'firstname' => $order_info['payment_firstname'],
                    'lastname' => $order_info['payment_lastname'],
                    'company' => $order_info['payment_company'],
                    'address_1' => $order_info['payment_address_1'],
                    'address_2' => $order_info['payment_address_2'],
                    'city' => $order_info['payment_city'],
                    'postcode' => $order_info['payment_postcode'],
                    'zone' => $order_info['payment_zone'],
                    'zone_code' => $order_info['payment_zone_code'],
                    'country' => $order_info['payment_country']
                );
                $template->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                if($order_info['shipping_code']!=='home.home') {
                    $template->data['shipping_address'] = '';
                } else {
                    $replace = array(
                        'firstname' => $order_info['shipping_firstname'],
                        'lastname' => $order_info['shipping_lastname'],
                        'company' => $order_info['shipping_company'],
                        'address_1' => $order_info['shipping_address_1'],
                        'address_2' => $order_info['shipping_address_2'],
                        'city' => $order_info['shipping_city'],
                        'postcode' => $order_info['shipping_postcode'],
                        'zone' => $order_info['shipping_zone'],
                        'zone_code' => $order_info['shipping_zone_code'],
                        'country' => $order_info['shipping_country']
                    );

                    $template->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                }

                $template->data['products'] = array();
                $bundled_products =array();
                $bundled_data=array();
                $order_products = $this->getFormattedOrderProducts($order_id);
                foreach($order_products as $order_product){
                    $order_product['price']=$this->currency->format($order_product['price'] + ($this->config->get('config_tax') ? $order_product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']);
                    $order_product['total']=$this->currency->format($order_product['total'] + ($this->config->get('config_tax') ? ($order_product['tax'] * $order_product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']);                
                    $template->data['products'][]=$order_product;                
                }
                
                $template->data['vouchers'] = array();

                $order_voucher_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int) $order_id . "'");
                foreach ($order_voucher_query->rows as $voucher) {
                    $template->data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
                    );
                }
                
                $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order ASC");
                $template->data['totals'] = $order_total_query->rows;

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/order_update.tpl')) {
                    $html = $template->fetch($this->config->get('config_template') . '/template/mail/order_update.tpl');
                } else {
                    $html = $template->fetch('default/template/mail/order_update.tpl');
                }

            #mail template ends here

                $mail = new Mail();
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->hostname = $this->config->get('config_smtp_host');
                $mail->username = $this->config->get('config_smtp_username');
                $mail->password = $this->config->get('config_smtp_password');
                $mail->port = $this->config->get('config_smtp_port');
                $mail->timeout = $this->config->get('config_smtp_timeout');
                $mail->setTo($order_info['email']);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender($order_info['store_name']);
                $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                $mail->setHtml($html);
                $mail->send();
            }
        }
        $this->websocket->connect('o-'.$order_id);
    }
     public function getOrderBundleProducts($order_id,$product_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int)$order_id . "' AND order_product_id='".(int)$product_id."'");
		$query = $this->db->query("SELECT ob.*,pd.shortcode FROM " . DB_PREFIX . "order_bundle ob LEFT JOIN " . DB_PREFIX . "order_product op ON(ob.order_product_id=op.order_product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON(op.product_id=pd.product_id) WHERE ob.order_id = '" . (int)$order_id . "' AND ob.order_product_id='".(int)$product_id."'");
		return $query->rows;
	}
         public function getOrderBundledProductoptions($order_bundle_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle_option WHERE order_bundle_product_id = '" . (int)$order_bundle_product_id . "'");
		
		return $query->rows;
	}
 public function getOrderBundledProducttoppings($order_bundle_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle_toppings WHERE order_bundle_product_id = '" . (int)$order_bundle_product_id . "'");
		
		return $query->rows;
	}
         public function getOrderBundledProductdressings($order_bundle_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle_dressings WHERE order_bundle_product_id = '" . (int)$order_bundle_product_id . "'");
		
		return $query->rows;
	}
    public function deliveryTime($order_id) {

        $this->language->load('checkout/success');
        if ($this->config->get('config_live_delivery_storeclosed') || $this->postcode->isStoreOpened($this->config->get('config_store_id'))) {

            $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "' AND order_status_id<>1");
            if ($order_query->row) {
                $output = array('order_status' => $order_query->row['order_status'], 'order_status_id' => $order_query->row['order_status_id']);
                if (strtotime($order_query->row['delivery_time']) > 0) {
                    $output['delivery_time'] = date("l (d-m-y) h:i A", strtotime($order_query->row['delivery_time']));
                }

                // $output['requested_time'] = date("l (d-m-y) h:i A",strtotime ($order_query->row['requested_time']));
                $output['comment'] = $this->getOrderHistorieComment($order_id, $order_query->row['order_status_id']);
                return $output;
            } else {
                $live_delivery_timeout = (int) $this->config->get('config_live_delivery_timeout');

                if ($live_delivery_timeout > 0) {
                    $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "' AND order_status_id=1 AND '" . date("Y-m-d H:i:s") . "' > date_added  + INTERVAL $live_delivery_timeout SECOND ");
                    //  echo ("SELECT * FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "' AND order_status_id<>1 AND '" . date("Y-m-d H:i:s") . "' > date_added  + INTERVAL $live_delivery_timeout SECOND ");
                    if ($order_query->row) {

                        $this->update($order_id, $this->config->get('config_live_delivery_timeout_orderstatus'));
                        $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "' AND order_status_id<>1");
                        if ($order_query->row) {
                            $output = array('order_status' => $order_query->row['order_status'], 'order_status_id' => $order_query->row['order_status_id']);
                            $output['comment'] = $this->language->get('text_timeout');
                            return $output;
                        }
                    }
                }
                return array();
            }
        } else {
            // Store closed
            $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "' ");
            if ($order_query->row) {
                $output = array('order_status' => $order_query->row['order_status'], 'order_status_id' => $order_query->row['order_status_id']);
                $output['requested_time'] = date("l (d-m-y) h:i A", strtotime($order_query->row['requested_time']));
                $output['comment'] = $this->language->get('text_store_closed');
                return $output;
            }
        }
    }

    public function getOrderHistorieComment($order_id, $order_status_id) {

        $query = $this->db->query("SELECT  oh.comment FROM " . DB_PREFIX . "order_history oh  WHERE order_status_id = " . $order_status_id . " AND order_id=" . $order_id . " ORDER BY order_history_id DESC LIMIT 1");
        if ($query->row) {
            return $query->row['comment'];
        } else {
            return '';
        }
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
    
        public function delete(){
            

            
	    $store_opens =  $this->postcode->getLastDaysOpeningtimes() ;
            
            $delday =time();
            
            switch ($this->config->get('config_order_id_delete_previous')){
                case '0':
                    return;
                case 'all':
                    $delday = time();
                    break;
                case 'yday':
                    $delday = strtotime($store_opens[0]['end']);
                    break;
                case 'lweek':
                     $delday = strtotime("-1 week", strtotime($store_opens[0]['end']));
                    break;
                 case 'lmonth':
                     $delday = strtotime("-1 month", strtotime($store_opens[0]['end']));
                    break;
                    
            }
           
            $ordes_query =  $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order`  WHERE  order_status_id IN (" . implode(',', $this->config->get('config_order_id_delete_status')) .") AND requested_time < '" . date("Y-m-d H:i:s",$delday) . "'" );
                    if($ordes_query->num_rows  ) {
                        foreach($ordes_query->rows as $order){
                            $this->deleteOrder($order['order_id']);
                        }
                    }
            
        }
        
        private function deleteOrder($order_id) {

                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order` SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_product` SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_option` SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_download` SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_voucher` SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_total` SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_history` SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_fraud` SELECT * FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int)$order_id . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_bundle` SELECT * FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int)$order_id . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_product_dressings` SELECT * FROM " . DB_PREFIX . "order_product_dressings WHERE order_id = '" . (int)$order_id . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "back_order_product_toppings` SELECT * FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int)$order_id . "'"); 

		$this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int)$order_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int)$order_id . "'");
                //$this->db->query("DELETE FROM " . DB_PREFIX . "order_bundle_option WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int)$order_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_split WHERE order_id = '" . (int)$order_id . "'");
        }
    public function updatePendingOrder($data,$order_id){
        //$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int) $data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int) $data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int) $data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float) $data['total'] . "', affiliate_id = '" . (int) $data['affiliate_id'] . "', commission = '" . (float) $data['commission'] . "', language_id = '" . (int) $data['language_id'] . "', currency_id = '" . (int) $data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float) $data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" . $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = '" . date("Y-m-d H:i:s", time()) . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "',requested_time='" . date("Y-m-d H:i:s", isset($data['order_requested_time']) ? $data['order_requested_time'] : time()) . "', system_id = '" . (int) ((isset($this->session->data['system_id']))?$this->session->data['system_id']:0) . "', user_id = '" . (int) ((isset($this->session->data['user_id']))?$this->session->data['user_id']:0) . "' WHERE `order_id`='".(int)$order_id."'");
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int) $data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int) $data['customer_id'] . "', customer_group_id = '" . (int) $data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int) $data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int) $data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int) $data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int) $data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float) $data['total'] . "', affiliate_id = '" . (int) $data['affiliate_id'] . "', commission = '" . (float) $data['commission'] . "', language_id = '" . (int) $data['language_id'] . "', currency_id = '" . (int) $data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float) $data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" . $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = '" . date("Y-m-d H:i:s", time()) . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "',requested_time='" . date("Y-m-d H:i:s", isset($data['order_requested_time']) ? $data['order_requested_time'] : time()) . "', system_id = '" . (int) ((isset($this->session->data['system_id']))?$this->session->data['system_id']:0) . "', user_id = '" . (int) ((isset($this->session->data['user_id']))?$this->session->data['user_id']:0) . "' WHERE `order_id`='".(int)$order_id."'");
        $_SESSION['shipping'] = $this->db->escape($data['shipping_method']);
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int)$order_id . "'");
	$this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
	$this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
        foreach ($data['products'] as $product) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int) $order_id . "', product_id = '" . (int) $product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int) $product['quantity'] . "', price = '" . (float) $product['price'] . "', total = '" . (float) $product['total'] . "', tax = '" . (float) $product['tax'] . "', reward = '" . (int) $product['reward'] . "'");

            $order_product_id = $this->db->getLastId();

            foreach ($product['option'] as $option) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_option_id = '" . (int) $option['product_option_id'] . "', product_option_value_id = '" . (int) $option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "',`shortcode`='".(isset($option['shortcode'])?$option['shortcode']:'')."'");
}
            foreach ($product['toppings'] as $topping) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_product_toppings SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_topping_id = '" . (int) $topping['product_topping_id'] . "', product_topping_value_id = '" . (int) $topping['product_topping_value_id'] . "', name = '" . $this->db->escape($topping['name']) . "', `value` = '" . $this->db->escape($topping['topping_value']) . "', `price` = '" . (float)$topping['price'] . "',`quantity`='".(int)$topping['quantity']."'");
            }
            foreach ($product['download'] as $download) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int) ($download['remaining'] * $product['quantity']) . "'");
            }
              foreach ($product['bundle'] as $bundle_product) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle SET  order_product_id = '" . (int) $order_product_id . "',order_id = '" . (int) $order_id . "',bundle_id = '" . (int) $bundle_product['bundled_id'] . "',bundle_product_id = '" . (int) $bundle_product['product_id'] . "', name = '" . $this->db->escape($bundle_product['name']) . "', model = '" . $this->db->escape($bundle_product['model']) . "', quantity = '" . (int) $bundle_product['quantity'] . "'");

                $order_bundle_product_id = $this->db->getLastId();

                foreach ($bundle_product['option'] as $bunoption) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle_option SET order_bundle_product_id = '" . (int) $order_bundle_product_id . "', product_option_id = '" . (int) $bunoption['product_option_id'] . "', product_option_value_id = '" . (int) $bunoption['product_option_value_id'] . "', name = '" . $this->db->escape($bunoption['name']) . "', `value` = '" . $this->db->escape($bunoption['value']) . "', `type` = '" . $this->db->escape($bunoption['type']) . "',`shortcode`='".(isset($bunoption['shortcode'])?$bunoption['shortcode']:'')."'");
                }
            }
        }

        foreach ($data['vouchers'] as $voucher) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int) $order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int) $voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float) $voucher['amount'] . "'");
        }

        foreach ($data['totals'] as $total) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', text = '" . $this->db->escape($total['text']) . "', `value` = '" . (float) $total['value'] . "', sort_order = '" . (int) $total['sort_order'] . "'");
        }

        //Pending Orders Add
        //$pending_data=array();
        $pending_data=array('products'=>$data['products'],'totals'=>$data['totals']);
        $index=0;
        $ind_qry=$this->db->query("SELECT index FROM " . DB_PREFIX . "edited_pending_orders WHERE order_id='".$order_id."' ORDER BY id DESC LIMIT 1 ");
        if(!empty($ind_qry->row)){
            $index = $ind_qry->row['index']+1;
        }
        $this->db->query("INSERT INTO " . DB_PREFIX . "edited_pending_orders SET `order_id` = '" . (int) $order_id . "',`index`='0',`data`='".$this->db->escape(serialize($this->session->data))."',`payed_total`='".(float)(($data['payment_code']!='cod')?$data['total']:0)."',`user_id`='".(int) ((isset($this->session->data['user_id']))?$this->session->data['user_id']:0)."',`edited_time`='".date("Y-m-d H:i:s", time()) ."'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '19', notify = '0', comment = '', date_added = '" . date("Y-m-d H:i:s", time()) . "'");
        //$this->session->data['edited_order_id']=$this->db->getLastId();
        //Pending Orders Add

    } 
    public function isOnlineOrderAdded($online_order_id) {
             $ordes_query =  $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order`  WHERE  online_order_id=" .$online_order_id );
             if($ordes_query->num_rows  ) {
                 return true;
                 
             }
             else {
                 return false;
             }
        }
        public function onlineOrderHistory($order_id, $order_status_id, $comment = '') {
            $order_info = $this->getOrder($order_id);
            if ($order_info) {

                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $order_status_id . "', date_modified = '" . date("Y-m-d H:i:s",  time()) . "' WHERE order_id = '" . (int) $order_id . "'");

                $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '" . (int) $order_status_id . "', notify = '1', comment = '" . $this->db->escape(($comment) ? $comment : '') . "', date_added = '" . date("Y-m-d H:i:s",  time()) . "'");
            }
            
        }
         public function getOrderStatuses($data = array()) {
      	if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
			$sql .= " ORDER BY name";	
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
			
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}				

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			
			$query = $this->db->query($sql);
			
			return $query->rows;
		} else {
			$order_status_data = $this->cache->get('order_status.' . (int)$this->config->get('config_language_id'));
		
			if (!$order_status_data) {
				$query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
	
				$order_status_data = $query->rows;
			
				$this->cache->set('order_status.' . (int)$this->config->get('config_language_id'), $order_status_data);
			}	
	
			return $order_status_data;				
		}
	}
        
        public function changeOrderStatus($order_id,$data){
            //Auto ADD Reward Points .... 
            $order_info = $this->getOrder($order_id);
            $this->load->model('setting/setting');
            if($this->config->get('reward_points_status')) {
                if($this->config->get('config_reward_points_autoadd') && $this->config->get('config_reawrd_order_status_id')==$data['order_status_id']) {
                    if((float)$order_info['total']>=(float) $this->config->get('config_reward_points_min_order_amount')){
                        $this->load->model('account/reward');
                        $reward_qry=$this->db->query("SELECT SUM(reward) as reward FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
                        $this->model_account_reward->addReward('Order ID #'.$order_id, $reward_qry->row['reward'],$order_id,$order_info['customer_id']);
                    }
                }
            }
            /*
            $rewards = $this->model_setting_setting->getSetting('rewardpoints');
            if(!empty($rewards) and isset($rewards[5])){
                if($rewards[5]['autoadd']==1 and $this->config->get('config_reawrd_order_status_id')==$data['order_status_id']){
                    if((float)$order_info['total']>=(float)$rewards[5]['amount']){ 
                        //echo "222";
                        $this->load->model('account/reward');  
                        $reward_qry=$this->db->query("SELECT SUM(reward) as reward FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
                        //echo $reward_qry->row['reward'];
                        $this->model_account_reward->addReward('Order ID #'.$order_id, $reward_qry->row['reward'],$order_id,$order_info['customer_id']);
                    }
                }
            }
            */
            // BLOCK UNWANTED NOTIFY
            if (isset($data['notify']) && !empty($data['notify'])) {
                if(empty(trim($data['comment'])) && $order_info['order_status_id']==(int)$data['order_status_id']){
                    $data['notify']=0;
                }
            }
            
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = NOW(), user_id = '" . (int) ((isset($data['user_id']))?$data['user_id']:0) . "' WHERE order_id = '" . (int)$order_id . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");
                
//NEW CODE UPDATE STOCK check order status
            if($this->config->get('config_stockmanagement_status') && $this->config->get('config_stock_completed_status') && !empty($this->config->get('config_stock_completed_status')) && isset($this->config->get('config_stock_completed_status')[$data['order_status_id']])){
                $stock_qry = $this->db->query("SELECT SUM(quantity) as qty , product_id FROM live_stock_data WHERE order_id=".$order_id." GROUP BY product_id");
                if(!empty($stock_qry->rows)){
                    foreach($stock_qry->rows as $stockrow){
                        $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int) $stockrow['qty'] . ") WHERE product_id = '" . (int) $stockrow['product_id'] . "' AND subtract = '1'");
                    }
                }
                $this->db->query("DELETE FROM" . DB_PREFIX . " live_stock_data WHERE order_id = " . (int) $order_id);
            }
             if ($this->config->get('config_complete_status_id') == $data['order_status_id']) { 
                $this->load->model('checkout/voucher');

                $this->model_checkout_voucher->confirm($order_id);
            }
            //NEW CODE
                /*
                if ($this->config->get('config_complete_status_id') == $data['order_status_id']) {
			$this->load->model('sale/voucher');

			$results = $this->getOrderVouchers($order_id);
			
			foreach ($results as $result) {
				$this->model_sale_voucher->sendVoucher($result['voucher_id']);
			}
		}
                */
                if(!isset($data['notify']) && (int)$order_info['order_status_id']!=(int)$data['order_status_id'] && !empty($this->config->get('config_status_change_notificn'))){
                    //check foreach noticin status
                    foreach($this->config->get('config_status_change_notificn') as $notificn){
                        if((int)$data['order_status_id']==(int)$notificn['order_status_id'] && isset($notificn['email'])){
                            $data['notify']=1;
                            break;
                        }
                    }
                }
                $this->log->write( print_r("Notify 000", true));
                if(!isset($data['printonly'])){
                    $this->load->model('module/orderstatusupdate');
                    $this->model_module_orderstatusupdate->sendNotification($order_id,$data['order_status_id'],$order_info);
                }
                
                $default_notify=false;     
            if ($default_notify && isset($data['notify']) && !empty($data['notify'])) {
                    $language = new Language($order_info['language_directory']);
                    $language->load($order_info['language_filename']);
                    $language->load('mail/order');
                    $order_info['store_name'] = ($order_info['store_name']=='' || $order_info['store_name']=='EPOS')?$this->config->get('config_name'):$order_info['store_name'];
                    $subject = sprintf($language->get('text_update_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

                    $message = $language->get('text_update_order') . ' ' . $order_id . "\n";
                    $message .= $language->get('text_update_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

                    $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int) $data['order_status_id'] . "' AND language_id = '" . (int) $order_info['language_id'] . "'");

                    $order_status_text = '';
                    if ($order_status_query->num_rows) {
                        $message .= $language->get('text_update_order_status') . "\n\n";
                        $message .= $order_status_query->row['name'] . "\n\n";
                        $order_status_text = $order_status_query->row['name'];
                    }
                    if (isset($data['reject']) && isset($order_status_query->row['name'])) {
                        $subject = html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8').' - '.'ORDER # '.$order_id.' - '.$order_status_query->row['name'];
                    }
                    if ($order_info['customer_id']) {
                        $message .= $language->get('text_update_link') . "\n";
                        $message .= $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id . "\n\n";
                    }

                    if (isset($data['comment']) && $data['comment']!='') {
                        $message .= $language->get('text_update_comment') . "\n\n";
                        $message .= $data['comment'] . "\n\n";
                    }

                    $message .= $language->get('text_update_footer');

                    #new update mail starts here
                    $template = new Template();

                    $template->data['updated_shipping_text'] = '';

                    if($order_info['store_id']) {
                        $this->load->model('setting/setting');
                        $storeinfo = $this->model_setting_setting->getSetting('config');
                        $store_telephone = $storeinfo['config_telephone'];
                    } else {
                        $store_telephone = $this->config->get('config_telephone');
                    }

                    $template->data['updated_shipping_text'] = '';
                    if($delivery_time) {
                        if($order_info['shipping_code'] == 'home.home') {
                            $template->data['updated_shipping_text'] = sprintf($language->get('text_delivery_time_home'),$delivery_time );
                        } else {
                            $template->data['updated_shipping_text'] = sprintf($language->get('text_delivery_time_collect'),$delivery_time );
                        }
                    }

                    if($data['order_status_id']==$this->config->get('config_complete_status_id')) {
                        $template->data['text_order_update'] = sprintf($language->get('text_accepted'),$order_info['store_name'],$order_status_text);
                        $template->data['text_content'] = sprintf($language->get('text_content'), $store_telephone);						
                    } else if($data['order_status_id']==$this->config->get('remoteprint_rejected_order_status_id') || $data['order_status_id']==$this->config->get('pos_order_cancel_status')) {
                        $template->data['text_order_update'] = sprintf($language->get('text_cancelled'),$order_info['store_name'],$order_status_text);
                        $template->data['text_content'] = '';
                    } else {
                        $template->data['text_order_update'] = sprintf($language->get('text_order_update'),$order_info['store_name'],$order_status_text);
                        $template->data['text_content'] = sprintf($language->get('text_content'), $store_telephone);
                    }

                    $template->data['text_order_detail'] = $language->get('text_order_detail');
                    $template->data['text_order_id'] = $language->get('text_order_id');
                    $template->data['text_requested_time'] = $language->get('text_requested_time');
                    $template->data['text_link'] = $language->get('text_link');
                    $template->data['title'] = $language->get('text_title');
                    $template->data['text_date_added'] = $language->get('text_date_added');
                    
                    $template->data['text_payment_method'] = $language->get('text_payment_method');
                    $template->data['text_shipping_method'] = $language->get('text_shipping_method');
                    $template->data['text_requested_time'] = $language->get('text_requested_time');
                    $template->data['text_telephone'] = $language->get('text_telephone');
                    $template->data['text_ip'] = $language->get('text_ip');
                    $template->data['text_payment_address'] = $language->get('text_payment_address');
                    $template->data['text_shipping_address'] = $language->get('text_shipping_address');
                    $template->data['text_product'] = $language->get('text_product');
                    $template->data['text_quantity'] = $language->get('text_quantity');
                    $template->data['text_price'] = $language->get('text_price');
                    $template->data['text_total'] = $language->get('text_total');
                    $template->data['text_email'] = $language->get('text_email');

                    if($this->config->get('config_email_header')) {
                        $template->data['header_image'] = HTTP_IMAGE . $this->config->get('config_email_header');
                    } else {
                        $template->data['header_image'] = '';
                    }
                    if($this->config->get('config_email_offer')) {
                        $template->data['offer_image'] = HTTP_IMAGE . $this->config->get('config_email_offer');
                    } else {
                        $template->data['offer_image'] = '';
                    }
                    if($this->config->get('config_email_footer')) {
                        $template->data['footer_image'] = HTTP_IMAGE . $this->config->get('config_email_footer');
                    } else {
                        $template->data['footer_image'] = '';
                    }
                    
                    if($order_info['store_url']!=''){
                        $template->data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;
                    }else{
                        $template->data['link']='';
                    }

                    $template->data['offer_url'] = $order_info['store_url'] .  $this->config->get('config_email_offer_url');
                    $template->data['order_id'] = ($this->config->get('config_order_id_hide')?'':$order_id);
                    $template->data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
                    $template->data['payment_method'] = $order_info['payment_method'];
                    $template->data['shipping_method'] = $order_info['shipping_method'];
                    $template->data['requested_time'] = $order_info['requested_time'];
                    $template->data['email'] = $order_info['email'];
                    $template->data['telephone'] = $order_info['telephone'];
                    $template->data['ip'] = $order_info['ip'];
                    $template->data['comment'] = nl2br($data['comment']);
                    $template->data['store_name'] = $order_info['store_name'];
                    $template->data['store_url'] = $order_info['store_url'];
                    $template->data['customer_id'] = $order_info['customer_id'];
                    $template->data['store_telephone'] = $this->config->get('config_telephone');


                    if ($order_info['payment_address_format']) {
                        $format = $order_info['payment_address_format'];
                    } else {
                        $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}';
                    }

                    $find = array(
                        '{firstname}',
                        '{lastname}',
                        '{company}',
                        '{address_1}',
                        '{address_2}',
                        '{city}',
                        '{postcode}',
                        '{zone}',
                        '{zone_code}',
                        '{country}'
                    );

                    $replace = array(
                        'firstname' => $order_info['payment_firstname'],
                        'lastname' => $order_info['payment_lastname'],
                        'company' => $order_info['payment_company'],
                        'address_1' => $order_info['payment_address_1'],
                        'address_2' => $order_info['payment_address_2'],
                        'city' => $order_info['payment_city'],
                        'postcode' => $order_info['payment_postcode'],
                        'zone' => $order_info['payment_zone'],
                        'zone_code' => $order_info['payment_zone_code'],
                        'country' => $order_info['payment_country']
                    );
                    $template->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                    if($order_info['shipping_code']!=='home.home') {
                        $template->data['shipping_address'] = '';
                    } else {
                        $replace = array(
                            'firstname' => $order_info['shipping_firstname'],
                            'lastname' => $order_info['shipping_lastname'],
                            'company' => $order_info['shipping_company'],
                            'address_1' => $order_info['shipping_address_1'],
                            'address_2' => $order_info['shipping_address_2'],
                            'city' => $order_info['shipping_city'],
                            'postcode' => $order_info['shipping_postcode'],
                            'zone' => $order_info['shipping_zone'],
                            'zone_code' => $order_info['shipping_zone_code'],
                            'country' => $order_info['shipping_country']
                        );
            
                        $template->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
            
                    }

                    $template->data['products'] = array();
                    $bundled_products =array();
                    $bundled_data=array();
                    $order_products = $this->getFormattedOrderProducts($order_id);
                    foreach($order_products as $order_product){
                        $order_product['price']=$this->currency->format($order_product['price'] + ($this->config->get('config_tax') ? $order_product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']);
                        $order_product['total']=$this->currency->format($order_product['total'] + ($this->config->get('config_tax') ? ($order_product['tax'] * $order_product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']);                
                        $template->data['products'][]=$order_product;                
                    }
                    
                    $template->data['vouchers'] = array();

                    $order_voucher_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int) $order_id . "'");
                    foreach ($order_voucher_query->rows as $voucher) {
                        $template->data['vouchers'][] = array(
                            'description' => $voucher['description'],
                            'amount' => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
                        );
                    }
                    
                    $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order ASC");
                    $template->data['totals'] = $order_total_query->rows;

                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/order_update.tpl')) {
                        $html = $template->fetch($this->config->get('config_template') . '/template/mail/order_update.tpl');
                    } else {
                        $html = $template->fetch('default/template/mail/order_update.tpl');
                    }
                #new update mail ends here

                    $mail = new Mail();
                    $mail->protocol = $this->config->get('config_mail_protocol');
                    $mail->parameter = $this->config->get('config_mail_parameter');
                    $mail->hostname = $this->config->get('config_smtp_host');
                    $mail->username = $this->config->get('config_smtp_username');
                    $mail->password = $this->config->get('config_smtp_password');
                    $mail->port = $this->config->get('config_smtp_port');
                    $mail->timeout = $this->config->get('config_smtp_timeout');
                    $mail->setTo($order_info['email']);
                    $mail->setFrom($this->config->get('config_email'));
                    $mail->setSender($order_info['store_name']);
                    $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                    $mail->setHtml($html);
                    $mail->send();
		}
                 // PUSH NOTIFICATION
            if($default_notify && $this->config->get('firebase_api_key')) {
                $this->load->model('account/customer');
                $customer_info = $this->model_account_customer->getCustomer($order_info['customer_id']);

                $devicetokens = array()		;
                if ($customer_info && $customer_info['fcm_token']) {

                    $devicetokens[] = $customer_info['fcm_token'];
                    // #TODO Change to Push languge file
                    $language = new Language($order_info['language_directory']);
                    $language->load($order_info['language_filename']);
                    $language->load('mail/order');
                    $subject = sprintf($language->get('text_subject'), $order_info['store_name'], $order_id);

                    $message  = $language->get('text_order') . ' ' . $order_id . "\n";
                    $message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

                    $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$data['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

                    if ($order_status_query->num_rows) {
                            $message .= $language->get('text_order_status') . "\n";
                            $message .= $order_status_query->row['name'] . "\n";
                    }
                    if ($data['comment']) {
                            $message .= $language->get('text_comment') . "\n";
                            $message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n";
                    }

                    $push_data = array('devicetoken'=>$devicetokens, 'title'=> $subject,'message'=>$message, 'data'=>array(
                        'order_id' =>$order_id,
                        'action' => 'order_update'
                    ));
               // $this->log->write(print_r($push_data, true));

                $fcm = new FCM($this->config->get('firebase_api_key'));
                $response = $fcm->send($push_data);
                //$this->log->write(  print_r($response, true));
                }
            }
                
                // OWNER APP PUSH
            if($default_notify && $this->config->get('ownerappapi_firebase_api_key')) {
                $devicetokens = array();
                $query = $this->db->query("SELECT device_token  FROM  " . DB_PREFIX . "app_owner  group by device_token");	
                foreach ($query->rows  as $owner) {
                    $devicetokens[]=$owner['device_token'];
                }
                $ownerpush = "Status has been changed to " . $order_status_query->row['name'] . " of order id: " . $order_id;
		$push_data = array('devicetoken'=>$devicetokens, 'title'=> $this->config->get('config_ownerappapi_notification_title'),'message'=>$ownerpush, 'data'=>array(
                    'order_id' =>$order_id,
                    'customer_id'=>$order_info['customer_id'],
                    'action' => 'orderList'
                    
                ));
                $fcm = new FCM($this->config->get('ownerappapi_firebase_api_key'));	
                $response = $fcm->send($push_data);
                //$this->log->write(print_r($response,true));
            }
            
             // ###### HUBRISE CODE STARTS HERE  ###########

            if($this->config->get('config_hubrise_status') && $order_info['order_from']==5) {
                $this->load->model('module/hubriseapi');
                /*statusconfirmed_timeseller_notescollection_codeprivate_refcustom_fields*/
                $hubrisedata = array('order_status_id' => $data['order_status_id'],
                                                );
                if($data['comment']) {
                        $hubrisedata['seller_notes'] = $data['comment'];
                }
                if(isset($data['delivery_time']) && $data['delivery_time']!=='0000-00-00 00:00:00') {
                    //$hubrisedata['confirmed_time'] = date(DATE_ISO8601, strtotime($data['delivery_time']));
                    //$hubrisedata['confirmed_time'] = date('Y-m-d\TH:i:s\Z',strtotime($data['delivery_time']));

                    $datetime = new DateTime($data['delivery_time']);
                    $hubrisedata['confirmed_time'] =  $datetime->format(DateTime::ATOM); 

                }
                $this->model_module_hubriseapi->updateorderstatus($order_id, $hubrisedata); 
            }
            
            if($default_notify && isset($data['notify_sms'])){      
                 //$this->log->write(print_r("SMS SEND",true));
                    $message='';
                    $this->load->model('tool/sms');
                    $language = new Language($order_info['language_directory']);
		    $language->load($order_info['language_filename']);
                    $language->load('mail/order');
                    //$message  = $language->get('text_order') . ' ' . $order_id . "\n";
                    $message  = $order_info['store_name'] . " Order Status:\n #" . $order_id . "\n";
                    $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$data['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
                    if ($order_status_query->num_rows) {
                            //$message .= $language->get('text_order_status') . "\n";
                            //$message .= $order_status_query->row['name'] . "\n";
                            $message .= $order_status_query->row['name'] . "\n";
                    }
                    if ($data['comment']) {
                          //  $message .= $language->get('text_comment') . "\n";
                            $message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n";
                    }                                        
                    $this->log->write(print_r("SMS SEND AAA",true));
                    $response =  $this->model_tool_sms->sendSingleSMS($message,$order_info['telephone']);
                    $this->log->write(print_r("SMS SEND BBB",true));
                    $this->log->write(print_r($response,true));
                  
                }  
        }
         public function syncOrder($data) { 

        $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int) $data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int) $data['customer_id'] . "', customer_group_id = '" . (int) $data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']). "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int) $data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int) $data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int) $data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int) $data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float) $data['total'] . "',order_status_id='" . (int) $data['order_status_id'] . "', affiliate_id = '" . (int) $data['affiliate_id'] . "', commission = '" . (float) $data['commission'] . "', language_id = '" . (int) $data['language_id'] . "', currency_id = '" . (int) $data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float) $data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" . $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = '" .  $this->db->escape($data['date_added']) . "', date_modified = '" . $this->db->escape($data['date_modified']) . "',requested_time='" . date("Y-m-d H:i:s", isset($data['order_requested_time']) ? $data['order_requested_time'] : time()) . "', system_id = '" . (int) $data['system_id']  . "', user_id = '" . $data['user_id'] . "',online_order_id='".(isset($data['online_order_id'])?$data['online_order_id']:'')."',offline_order_id='".(isset($data['offline_order_id'])?$data['offline_order_id']:'')."'");
        
        $order_id = $this->db->getLastId();
        //$_SESSION['shipping'] = $this->db->escape($data['shipping_method']);
        foreach ($data['products'] as $product) {
              $product = (array)$product;
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int) $order_id . "', product_id = '" . (int) $product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int) $product['quantity'] . "', price = '" . (float) $product['price'] . "', total = '" . (float) $product['total'] . "', tax = '" . (float) $product['tax'] . "', reward = '" . (int) $product['reward'] . "'");

            $order_product_id = $this->db->getLastId();

            foreach ($product['option'] as $option) {
                 $option = (array)$option;
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_option_id = '" . (int) $option['product_option_id'] . "', product_option_value_id = '" . (int) $option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
            }
            foreach ($product['toppings'] as $topping) {
                $topping = (array)$topping;
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_product_toppings SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_topping_id = '" . (int) $topping['product_topping_id'] . "', product_topping_value_id = '" . (int) $topping['product_topping_value_id'] . "', name = '" . $this->db->escape($topping['name']) . "', `value` = '" . $this->db->escape($topping['topping_value']) . "', `price` = '" . (float)$topping['price'] . "',`quantity`='".(int)$topping['quantity']."'");
            }
            foreach ($product['download'] as $download) {
                 $download = (array)$download;
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int) ($download['remaining'] * $product['quantity']) . "'");
            }
            foreach ($product['bundle'] as $bundle_product) {
                 $bundle_product = (array)$bundle_product;
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle SET  order_product_id = '" . (int) $order_product_id . "',order_id = '" . (int) $order_id . "',bundle_id = '" . (int) $bundle_product['bundled_id'] . "',bundle_product_id = '" . (int) $bundle_product['product_id'] . "', name = '" . $this->db->escape($bundle_product['name']) . "', model = '" . $this->db->escape($bundle_product['model']) . "', quantity = '" . (int) $bundle_product['quantity'] . "'");

                $order_bundle_product_id = $this->db->getLastId();

                foreach ($bundle_product['option'] as $bunoption) {
                    $bunoption = (array)$bunoption;
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_bundle_option SET order_bundle_product_id = '" . (int) $order_bundle_product_id . "', product_option_id = '" . (int) $bunoption['product_option_id'] . "', product_option_value_id = '" . (int) $bunoption['product_option_value_id'] . "', name = '" . $this->db->escape($bunoption['name']) . "', `value` = '" . $this->db->escape($bunoption['value']) . "', `type` = '" . $this->db->escape($bunoption['type']) . "'");
                }
            }
        }

        foreach ($data['vouchers'] as $voucher) {
            $voucher = (array)$voucher;
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int) $order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int) $voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float) $voucher['amount'] . "'");
        }

        foreach ($data['totals'] as $total) {
            $total = (array)$total;
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', text = '" . $this->db->escape($total['text']) . "', `value` = '" . (float) $total['value'] . "', sort_order = '" . (int) $total['sort_order'] . "'");
        }

          
        return $order_id;
    }
    public function checkOnlineOrderSameDB() {
             $ordes_query =  $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order`  WHERE  system_id=0 AND print_status=0 AND order_status_id=1" );
             if($ordes_query->num_rows) {
                 return $ordes_query->row['order_id'];
             }
             else {
                 return false;
             }
    }
     public function checkAnotherSystemsOrders() {
             $ordes_query =  $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order`  WHERE system_id<>0 AND  print_status=0 AND order_status_id!=0" );
             if($ordes_query->num_rows  ) {
                 return $ordes_query->row['order_id'];
             }
             else {
                 return false;
             }
    }
    public function setPosOrderprintAreas($order_id,$print_system_id,$print_area){
        $print_settings=  $this->posclient->getPosprintSettings($print_area,$print_system_id);
        if(!empty($print_settings)){
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `print_areas`='" . $this->db->escape(serialize($print_settings)) . "'  WHERE `order_id`=".(int)$order_id."");
        }
        if($print_settings){
            
        }
    }
    public function updateOrderprintAreas($order_id,$system_id,$print_area){
        $qry = $this->db->query("SELECT print_areas FROM`" . DB_PREFIX . "order`  WHERE `order_id`=".(int)$order_id."");
        $print_settings = unserialize($qry->row['print_areas']);
        $delete_status = false;
        //$system_id = (isset($this->session->data['system_id']))?$this->session->data['system_id']:0;
        if(empty($print_settings) && $print_settings==''){
            $print_settings=  $this->posclient->getPosprintSettings($print_area,$system_id);
            //print_r($print_settings);
        }else{ 
            if(isset($print_settings[$system_id]) && isset($print_settings[$system_id][$print_area])){
                unset($print_settings[$system_id][$print_area]);
                if(empty($print_settings[$system_id])){
                    unset($print_settings[$system_id]);
                }
                $delete_status = true;
            }
            $print_datas = $this->posclient->getPosprintSettings($print_area,$system_id);
            if(!empty($print_datas) && !$delete_status){
                foreach ($print_datas as $key=>$print_data) {
                    if(isset($print_settings[$key])){
                        $print_settings[$key][$print_area]=$print_data[$print_area];
                    }else{
                        $print_settings +=array($key=>$print_data);
                    }
                }
            }
        }
        //print_r($print_settings);
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `print_status`=".(!empty($print_settings)?0:1).", `print_areas`='" . $this->db->escape(serialize($print_settings)) . "'   WHERE `order_id`=".(int)$order_id."");
    
        //$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `print_status`=".(!empty($print_settings)?0:1).", `print_areas`='" . $this->db->escape(serialize($print_settings)) . "'   WHERE `order_id`=".(int)$order_id."");
    }
    public function deleteOrderprintAreas($order_id){
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `print_status`='1', `print_areas`=''   WHERE `order_id`=".(int)$order_id."");
    }
    
    public function getLastOrderModified() {
        $query = $this->db->query("SELECT MAX(date_modified) last_modified, MAX(date_added) last_added FROM `" . DB_PREFIX . "order` ");
	return  ( $query->row['last_modified'] > $query->row['last_added'] ? $query->row['last_modified']:$query->row['last_added']) ;
    }
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
            $yes_end_times = explode(':', $yesterday_open_time['end']);
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
            $yesterdayendtime = $yesterdayendtime+10800;
            $ordes_query =  $this->db->query("SELECT display_id FROM `" . DB_PREFIX . "order`  WHERE  date_added > '" . date('Y-m-d H:i:s',$yesterdayendtime) . "' ORDER BY display_id DESC  LIMIT 0,1");
             if(!empty($ordes_query->rows)){
                 return $ordes_query->row['display_id']; 
             }
        }
        return 0 ; 
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
    }
    //New Functions for formated order datas
    public function getOrderToppings($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_toppings WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
        return $query->rows;
    }
    public function getOrderDressings($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_dressings WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
        return $query->rows;
    }
    public function getFormattedOrderProducts($order_id){
            $query = $this->db->query("SELECT *,REPLACE(name,'&amp;','and') AS name FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
            $deal_prodcuts = $this->getDealProducts($order_id);
            $product_data = array();
            if ($query->num_rows) {
                foreach($query->rows as $product){
                    $product['option']= $this->getOrderOptions($order_id,$product['order_product_id']);
                    $product['toppings']=$this->getOrderToppings($order_id,$product['order_product_id']);
                    $product['dressings']=$this->getOrderDressings($order_id,$product['order_product_id']);
                    $product_data[$product['order_product_id']]=$product;
                    $product_data[$product['order_product_id']]['bundle']=array();
                }
            }
          //  print_r($product_data);
            //print_r($deal_prodcuts);
            foreach($deal_prodcuts as $order_product_id=>$dealprodcuts){
                $product_data[$order_product_id]['bundle']=array();
                foreach($dealprodcuts as $deal_prodcut){
                    $product_details = $this->getOrderProductdata($deal_prodcut['bundle_order_product_id']);
                    $product_details['bundle_id']=$deal_prodcut['bundle_id'];
                    $product_details['quantity']=$deal_prodcut['quantity'];
                    $product_details['options']= $this->getOrderOptions($order_id,$deal_prodcut['bundle_order_product_id']);
                    $product_details['toppings']=$this->getOrderToppings($order_id,$deal_prodcut['bundle_order_product_id']);// & Dressings
                    $product_details['dressings']=$this->getOrderDressings($order_id,$deal_prodcut['bundle_order_product_id']);// & Dressings                       
                    $product_data[$order_product_id]['bundle'][] = $product_details;
                    $product_data[$deal_prodcut['bundle_order_product_id']]['quantity']-=(int)$deal_prodcut['quantity']*(int)$product_data[$order_product_id]['quantity'];
                    if($product_data[$deal_prodcut['bundle_order_product_id']]['quantity']<=0){
                        unset($product_data[$deal_prodcut['bundle_order_product_id']]);
                    }
                }
            }
            
            //print_r($product_data);
            return $product_data;
        }
        public function getConsolidatedOrderProducts($order_id){ 
            $deal_prodcuts = $this->getDealProducts($order_id);
            $op_query = $this->db->query("SELECT *,REPLACE(name,'&amp;','and') AS name FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");                        
            $product_data = array();
             $k=0;
            foreach($op_query->rows as $order_product){
                if(isset($deal_prodcuts[$order_product['order_product_id']])){
                    $order_product['name'] = html_entity_decode($order_product['name']);
                    $order_product['total'] = number_format($order_product['total'], 2);
                    $order_product['qty']=$order_product['quantity'];
                    $order_product['total_value']=$order_product['total'];
                    $order_product['price_value']=$order_product['price'];
                    $order_product['item_index'] =$k;
                    $order_product['main_index'] =$k;
                    $order_product['main_product_id']=$order_product['main_product_id'];                    
                    $order_product ['bundle'] = $deal_prodcuts[$order_product['order_product_id']];
                    $order_options = $this->getOrderOptions($order_id,$order_product['order_product_id']);
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
                    $order_product['option']= $option_data;
                    $order_toppings = $this->getOrderToppings($order_id,$order_product['order_product_id']);
                    $topping_data = array();
                    if(!empty($order_toppings)){
                        foreach($order_toppings as $data){
                            $topping_data[$data['product_topping_id']][$data['product_topping_value_id']]=array('name'=>$data['value'],'qty'=>$data['quantity'],'total_value'=>$data['price'],'price_value'=>$data['price'],'price'=>$data['price'],'topping_id'=>$data['product_topping_id'],'topping_value_id'=>$data['product_topping_value_id'],'toppingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                        }
                    }
                    $order_product['toppings']=$topping_data;
                    $order_dressings=$this->getOrderDressings($order_id,$order_product['order_product_id']);
                    $dressing_data = array();
                    if(!empty($order_dressings)){
                        foreach($order_dressings as $data){
                            $dressing_data[$data['product_dressing_id']][$data['product_dressing_value_id']]=array('name'=>$data['value'],'qty'=>1,'total_value'=>'','price_value'=>'','dressing_id'=>$data['product_dressing_id'],'dressing_value_id'=>$data['product_dressing_value_id'],'dressingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                        }
                    }
                    $order_product['dressings']=$dressing_data;
                    $product_data[$order_product['order_product_id']] = $order_product;
                    $k++;
                }
            }
           
            foreach($op_query->rows as $order_product){
                if(!isset($deal_prodcuts[$order_product['order_product_id']])){
                    $order_product['name'] = html_entity_decode($order_product['name']);
                    $order_product['total'] = number_format($order_product['total'], 2);
                    $order_product['qty']=$order_product['quantity'];
                    $order_product['total_value']=$order_product['total'];
                    $order_product['price_value']=$order_product['price'];
                    $order_product['item_index'] =$k;
                    $order_product['main_index'] =$k;
                    $order_product['main_product_id']=$order_product['main_product_id'];
                   
                    $order_options = $this->getOrderOptions($order_id,$order_product['order_product_id']);
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
                    $order_product['option']= $option_data;
                    $order_toppings = $this->getOrderToppings($order_id,$order_product['order_product_id']);
                    $topping_data = array();
                    if(!empty($order_toppings)){
                        foreach($order_toppings as $data){
                            $topping_data[$data['product_topping_id']][$data['product_topping_value_id']]=array('name'=>$data['value'],'qty'=>$data['quantity'],'total_value'=>$data['price'],'price_value'=>$data['price'],'price'=>$data['price'],'topping_id'=>$data['product_topping_id'],'topping_value_id'=>$data['product_topping_value_id'],'toppingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                        }
                    }
                    $order_product['toppings']=$topping_data;
                    $order_dressings=$this->getOrderDressings($order_id,$order_product['order_product_id']);
                    $dressing_data = array();
                    if(!empty($order_dressings)){
                        foreach($order_dressings as $data){
                            $dressing_data[$data['product_dressing_id']][$data['product_dressing_value_id']]=array('name'=>$data['value'],'qty'=>1,'total_value'=>'','price_value'=>'','dressing_id'=>$data['product_dressing_id'],'dressing_value_id'=>$data['product_dressing_value_id'],'dressingname'=>$data['name'],'shortcode'=>$data['shortcode']);
                        }
                    }
                    $order_product['dressings']=$dressing_data;
                    $product_data[$order_product['order_product_id']] = $order_product;
                    $k++;
                }
            }
            
            $return_data = array();
           
            foreach ($product_data as $data){
                $bundle_data = array(); 
                if(isset($data['bundle'])){
                    foreach($data['bundle'] as $bdata){
                        if(!isset($bundle_data[$bdata['bundle_id']])){
                            $bundle_data[$bdata['bundle_id']]=[];
                        }
                        $bundle_data[$bdata['bundle_id']][]=array('item_index'=>$product_data[$bdata['bundle_order_product_id']]['item_index'],'qty'=>$bdata['quantity']);
                    }
                }
                $data['bundle_data'] = $bundle_data;
                unset($data['bundle']);
                $return_data [] = $data;                
            }               
            
            return $return_data;
           //
        }
        public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
        private function getDealProducts($order_id){
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_bundle WHERE order_id = '" . (int)$order_id . "'");
            $bundle_data = array();
            if ($query->num_rows) {
                foreach($query->rows as $bundled_product){
                    if(isset($bundle_data[$bundled_product['order_product_id']])){
                        $bundle_data[$bundled_product['order_product_id']][]=$bundled_product;
                    }else{
                        $bundle_data[$bundled_product['order_product_id']]=array();
                        $bundle_data[$bundled_product['order_product_id']][]=$bundled_product;
                    }
                }
            }
            return $bundle_data;
        }
        private function getOrderProductdata($order_product_id){
            $query = $this->db->query("SELECT *,REPLACE(name,'&amp;','and') AS name FROM " . DB_PREFIX . "order_product WHERE order_product_id = '" . (int)$order_product_id . "'");
            return $query->row;
        }
        
    //bipin
    public function addOrderToStatuscheck($order_id, $customer_id, $status= 0)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_staus_check (order_id,customer_id,status,added_date) VALUES (" . (int) $order_id . ",".(int) $customer_id.",". (int)$status.",'".date("Y-m-d H:i:s", time())."')");
        return true;
    }

    public function deleteOrderToStatuscheck($order_id)
    {
        $this->db->query("DELETE FROM" . DB_PREFIX . " order_staus_check WHERE order_id = " . (int) $order_id);
        return true;
    }
    public function getPendingOrders($customer_id = '')
    {
        $wait_time = $this->config->get('stripe_response_wait_time');
        if($wait_time < 15)
        {
            $wait_time = 15;
        }
        $wait_time *= 60;
        $order_query = "SELECT * FROM " . DB_PREFIX . "order_staus_check WHERE customer_id = '".$customer_id."' AND status = 0 AND added_date > NOW() - INTERVAL " .$wait_time. " SECOND";
        $query = $this->db->query($order_query);
        return $query->rows;
    }
    public function addCheckoutHistory($data)
    {
        $order_details = json_encode($data);
        $insert_query = "INSERT INTO " . DB_PREFIX . "checkout_order_history SET order_id = '". (int)$data['order_id']."', order_details ='".$this->db->escape($order_details)."', created_at ='" . date("Y-m-d H:i:s", time()) ."'";
        $this->db->query($insert_query);
    }
    public function getOrdersOfCustomer($user_id)
    {
        $interval = 15;
        if($this->config->get('config_hold_checkout_time') > 0)
        {
            $interval = $this->config->get('config_hold_checkout_time');
        }
        $interval *= 60;
        $order_query = "SELECT * FROM `order` WHERE `customer_id` = '".$user_id."' ".
                    " AND `payment_code`!='cod'".
                    " AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) ."' ".
                    " AND `date_added` > NOW() - INTERVAL ".$interval." SECOND AND order_status_id NOT IN ('0','7','10') ORDER BY `order_id` DESC";
        $query = $this->db->query($order_query);
        return $query;
    }
    //bipin
    //bipin
    public function getMissingOrderList($customer_id = '')
    {
        if($customer_id != ''){
            $order_query = "SELECT * FROM `order` WHERE `customer_id` = '".$customer_id."' AND `order_status_id` = 0 ORDER BY `order_id` DESC";
            $query = $this->db->query($order_query);
            if($query->num_rows > 0){
                return $query->rows;
            }
            else{
                return false;
            }
        }
        else{
            $order_query = "SELECT * FROM `order` WHERE `order_status_id` = 0 AND `date_added` < NOW()- INTERVAL 300 SECOND  ORDER BY `order_id` DESC";
            $query = $this->db->query($order_query);
            if($query->num_rows > 0){
                return $query->rows;
            }
            else{
                return false;
            } 
        }       
    }
    //bipin
    //bipin
    public function updateOrderStatusCron($order_id, $order_status_id, $comment = '', $notify = false) {
        $order_info = $this->getOrder($order_id);

        if ($order_info) {
            // Fraud Detection
            if ($this->config->get('config_fraud_detection')) {
                $this->load->model('checkout/fraud');

                $risk_score = $this->model_checkout_fraud->getFraudScore($order_info);

                if ($risk_score > $this->config->get('config_fraud_score')) {
                    $order_status_id = $this->config->get('config_fraud_status_id');
                }
            }

            // Blacklist
            $status = false;

            $this->load->model('account/customer');

            if ($order_info['customer_id']) {

                $results = $this->model_account_customer->getIps($order_info['customer_id']);

                foreach ($results as $result) {
                    if ($this->model_account_customer->isBlacklisted($result['ip'])) {
                        $status = true;

                        break;
                    }
                }
            } else {
                $status = $this->model_account_customer->isBlacklisted($order_info['ip']);
            }

            if ($status) {
                $order_status_id = $this->config->get('config_order_status_id');
            }

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $order_status_id . "', date_modified = '" . date("Y-m-d H:i:s", time()) . "' WHERE order_id = '" . (int) $order_id . "'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '" . (int) $order_status_id . "', notify = '" . (int) $notify . "', comment = '" . $this->db->escape($comment) . "', date_added = '" . date("Y-m-d H:i:s", time()) . "'");

            // Send out any gift voucher mails
            if ($this->config->get('config_complete_status_id') == $order_status_id) {
                $this->load->model('checkout/voucher');

                $this->model_checkout_voucher->confirm($order_id);
            }
        }
    }
      
    public function createLiveOrder($order_id,$payment){
            $order_info = $this->getOrder($order_id);
            $cart_products = $this->cart->getProducts();
            $this->log->write(print_r($cart_products,true));
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
            $live_qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "live_orders` WHERE `dinein_table_id`=".(int)$this->session->data['dine_table_id']." ORDER BY `date_added` DESC LIMIT 1");

            if(!empty($live_qry->row)){ 
                $previous_data = json_decode(zlib_decode(base64_decode($live_qry->row['data'])),true);
                $live_delquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "live_order_deleted_track WHERE `live_order_id`='".(int)$previous_data['live_order_id']."'");
                if(!empty($live_delquery->rows)){
                    $this->db->query("DELETE  FROM " . DB_PREFIX . "live_orders WHERE `live_order_id`='".(int)$previous_data['live_order_id']."'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "live_order_deleted_track SET live_order_id = '" . (int)$previous_data['live_order_id'] . "', deleted_time = '" . date("Y-m-d H:i:s", time()) . "'");
                    //return;
                    $live_order_id =(int)rand(10, 80).time().rand(100, 990);
                    $live_data['live_order_id'] =$live_order_id;//rand(10, 99).time().rand(1001, 9999);
                    $live_data['pushid'] =$live_order_id;
                }else{
               
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
                    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = 20, date_modified = '" . date("Y-m-d H:i:s", time()) . "' WHERE order_id = '" . (int) $previous_data['order_id'] . "'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$previous_data['order_id'] . "', order_status_id = 20, notify = 0, comment = '', date_added = NOW()");
            
                }
            }
            else{
                $live_order_id =(int)(rand(10, 80).time().rand(100, 990));
                $live_data['live_order_id'] =$live_order_id;//rand(10, 99).time().rand(1001, 9999);
                $live_data['pushid'] =$live_order_id;
                $live_data['currency']=$this->currency->getSymbolLeft();
                $live_data['customer']=array(); 
            }

            $live_data['lock'] = 0;
            $live_data['dinein_table_id']=(int)$this->session->data['dine_table_id'];
            $live_data['table_id']=(int)$this->session->data['dine_table_id'];
            $live_data['dinein_table_name']=$this->session->data['tableordering']; //REPLACE
            $live_data['seletcedmenu'] = $this->config->get('config_waiterapp_menu') ;//Get From Mdoule  
            
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
                        if(!isset($option_data[$option['product_option_id']])){
                            $option_data[$option['product_option_id']]=array();
                        }
                        $option_data[$option['product_option_id']][$option['product_option_value_id']]=$optdata;
                    }else{
                        $option_data[$option['product_option_id']]=$optdata;
                    }
                }   
               
                //dressings
                $dressing_data = array();
                //toppings
                $topping_data = array();
                
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
                        
                
            }
            $live_data['currenttrack']=$track_index+1;
            $live_data['itemindex'] = $item_index;
            $live_data['currentitem']=$item_index-1;
            $live_data['items']=$live_items;      
            $live_data['totals'] =array();

            $this->log->write(print_r($live_items,true));
            
            $live_data['formateditems'] = $formateditems;
             $live_data['order_payed']=0;       
            $live_data['order_tme'] = date('Y-mm-dd H:i:s',time());
            $live_data['lasteditedtime'] = time()*1000;
            $live_data['date_modified']=time()*1000;
            $live_data['savedorder']=true;
            
            $live_data['app_order'] = true;
            
            $live_data['totalitemcount']= $live_data['cartitemcount']=$itemqty;

            $live_data['track']=$track_data;
            
                
            $live_data['shipping_method']='DINE';
            $live_data['shipping_code']='dine.dine';
            
            
            $live_data['webtableorder']=1;        
            $live_data['org_payment_methode'] =$payment;
            $live_data['store_name'] = $order_info['store_name'];
            $live_data['customer'] =array(
                'customer_id'=>$order_info['customer_id'],
                'address_id'=>0,
                'store_id'=>$order_info['store_id'],
                'firstname'=>$order_info['firstname'],
                'lastname'=>$order_info['lastname'],
                'email'=>$order_info['email'],
                'telephone'=>$order_info['telephone'],
                'address_1'=>$order_info['payment_address_1'],
                'address_2'=>$order_info['payment_address_2'],
                'postcode'=>$order_info['shipping_postcode'],
                'shipping_firstname'=>$order_info['shipping_firstname'],
                'shipping_lastname'=>$order_info['shipping_lastname'],
                'shipping_address_1'=>$order_info['shipping_address_1'],
                'shipping_address_2'=>$order_info['shipping_address_2'],
                'shipping_street'=>'',
                'shipping_city'=>$order_info['shipping_city'],
                'shipping_postcode'=>$order_info['shipping_postcode'],
                'shipping_method'=>$order_info['shipping_method'],
                'shipping_code'=>'dine',

            );
        $live_data['prevpayments'] = array('code'=>$order_info['payment_code'],'name'=>$order_info['payment_method']);
       
        if($order_info['payment_code']!='cod'){
            $live_data['editorder']=1;
            $live_data['prevtotal'] = number_format($order_info['total'],2);
            $live_data['edited_order_payed']=number_format($order_info['total'],2);
            if(!isset($live_data['payed_amts'])){
                $live_data['payed_amts']=[];
            }            
            $live_data['payed_amts'][] = array('code'=>$order_info['payment_code'],'name'=>$order_info['payment_method'],'amount'=>number_format($order_info['total'],2));
        }
        
            $this->load->model('epos/order');
            $data = array(
                'table_id'=>(int)$this->session->data['dine_table_id'],
                'data'=>base64_encode(zlib_encode(json_encode($live_data),ZLIB_ENCODING_DEFLATE)),
                'lock'=>0,
                'system_id'=>0,
                'live_order_id'=>(isset($live_order_id))?$live_order_id:$live_data['live_order_id']
            );
            //$this->log->write(print_r($data,true));
            $this->model_epos_order->updateLiveorder($data); 
            
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET live_order_id = '" . (int) $live_data['live_order_id'] . "',order_status_id = 20,included_orders='".$this->db->escape(serialize($included_orders))."' WHERE order_id = '" . (int) $order_id . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = 20, notify = 0, comment = '', date_added = NOW()");
    
            $this->websocket->connect('l');
    }
    //bipin
     public function getTotalOrders($data = array()) {
      	$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`";
		if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}
		if (!empty($data['filter_order_id'])) {
			$sql .= " AND order_id = '" . (int)$data['filter_order_id'] . "'";
		}
		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if (!empty($data['filter_total'])) {
			$sql .= " AND total = '" . (float)$data['filter_total'] . "'";
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
        
    private function sendNotification($order_id, $order_status_id,$order_info) {
        //$this->log->write(print_r("notufyykkk",true));
        $store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status_update_notification WHERE type='orderalert'");
        //$this->log->write(print_r($query->row,true));
        $notification_data =unserialize($query->row['notify_data']);
        //$this->log->write(print_r($notification_data,true));
        $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
        $order_status_name = '';
        if ($order_status_query->num_rows) {
            $order_status_name = $order_status_query->row['name'];
        }
        $order_products = $this->getFormattedOrderProducts($order_id);
        $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order ASC");
        $order_totals = $order_total_query->rows;
        //product data for notification

        $product_data = '<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;"><tbody>';

        foreach ($order_products as $product) {
            $product_data .= '<tr>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">' . $product['name'];

            foreach ($product['option'] as $option) {
                $product_data .= '<br />&nbsp;<small> - ' . $option['name'] . ': ' . $option['value'] . '</small>';
            }

            foreach ($product['toppings'] as $topping) {
                $product_data .= '<br />-<small> ' . $topping['quantity'] . ' X ' . $topping['name'] . ':' . $topping['value'] . '</small><br />';
            }

            foreach ($product['dressings'] as $dressing) {
                $product_data .= '<br />-<small>' . $dressing['name'] . ':' . $dressing['value'] . '</small><br />';
            }

            foreach ($product['bundle'] as $bundle) {
                $product_data .= '<br /><b><small>' . $bundle['name'] . ':</small></b><br />';

                foreach ($bundle['options'] as $bundleoption) {
                    $product_data .= '-- <small>' . $bundleoption['name'] . ': ' . $bundleoption['value'] . '</small><br />';
                }

                foreach ($bundle['toppings'] as $bundtopping) {
                    $product_data .= '-<small> ' . $bundtopping['quantity'] . ' X ' . $bundtopping['name'] . ':' . $bundtopping['value'] . '</small><br />';
                }

                foreach ($bundle['dressings'] as $bundressing) {
                    $product_data .= '-<small>' . $bundressing['name'] . ':' . $bundressing['value'] . '</small><br />';
                }
            }

            $product_data .= '</td>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['quantity'] . '</td>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['price'] . '</td>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['total'] . '</td>';
            $product_data .= '</tr>';
        }

        $product_data .= '</tbody><tfoot>';

        foreach ($order_totals as $total) {
            $product_data .= '<tr>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;" colspan="3"><b>' . $total['title'] . ':</b></td>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $total['text'] . '</td>';
            $product_data .= '</tr>';
        }

        $product_data .= '</tfoot></table>';
        
        $plain_product_data = '';
        foreach ($order_products as $product) {
            $plain_product_data .= $product['name'] . "\n";
            foreach ($product['option'] as $option) {
                $plain_product_data .= " - " . $option['name'] . ": " . $option['value'] . "\n";
            }
            foreach ($product['toppings'] as $topping) {
                $plain_product_data .= " - " . $topping['quantity'] . " X " . $topping['name'] . ":" . $topping['value'] . "\n";
            }
            foreach ($product['dressings'] as $dressing) {
                $plain_product_data .= " - " . $dressing['name'] . ":" . $dressing['value'] . "\n";
            }
            foreach ($product['bundle'] as $bundle) {
                $plain_product_data .= $bundle['name'] . ":\n";
                foreach ($bundle['options'] as $bundleoption) {
                    $plain_product_data .= " -- " . $bundleoption['name'] . ": " . $bundleoption['value'] . "\n";
                }
                foreach ($bundle['toppings'] as $bundtopping) {
                    $plain_product_data .= " - " . $bundtopping['quantity'] . " X " . $bundtopping['name'] . ":" . $bundtopping['value'] . "\n";
                }
                foreach ($bundle['dressings'] as $bundressing) {
                    $plain_product_data .= " - " . $bundressing['name'] . ":" . $bundressing['value'] . "\n";
                }
            }
            $plain_product_data .= "Quantity: " . $product['quantity'] . ", Price: " . $product['price'] . ", Total: " . $product['total'] . "\n\n";
        }
        foreach ($order_totals as $total) {
            $plain_product_data .= $total['title'] . ": " . $total['text'] . "\n";
        }
        
        
        $requested_time = $order_info['requested_time'];
        if(strtotime($order_info['requested_time']) <= strtotime($order_info['date_added'])) {
            $requested_time = 'AS SOON AS POSSIBLE';
        }
        
        //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status_update_notification");
         
        $address_data = '';
        if($order_info['shipping_address_1']){
            $address_data .= $order_info['shipping_address_1'] . "\n";
        }
        if($order_info['shipping_address_2']){
            $address_data .= $order_info['shipping_address_2'] . "\n";
        }
        if($order_info['shipping_city']){
            $address_data .= $order_info['shipping_city'] . "\n";
        }
        if($order_info['shipping_postcode']){
            $address_data .= $order_info['shipping_postcode'] . "\n";
        }   


        $store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
        if(isset($notification_data['owner_email']) && isset($notification_data['owner_email']['status']) && $notification_data['owner_email']['status']=='1'){
            //$this->log->write( print_r("email notify owner", true));
            $template = new Template();
            $template->data['store_name'] = $store_name;
            if($this->config->get('config_email_header')) {
                $template->data['header_image'] = HTTP_IMAGE . $this->config->get('config_email_header');
            } else {
                $template->data['header_image'] = '';
            }
            if($this->config->get('config_email_offer')) {
                $template->data['offer_image'] = HTTP_IMAGE . $this->config->get('config_email_offer');
            } else {
                $template->data['offer_image'] = '';
            }
            if($this->config->get('config_email_footer')) {
                $template->data['footer_image'] = HTTP_IMAGE . $this->config->get('config_email_footer');
            } else {
                $template->data['footer_image'] = '';
            }
            $template->data['offer_url'] = $order_info['store_url'] .  $this->config->get('config_email_offer_url');
            $template->data['message'] = str_replace(
                array('{NAME}', '{ORDER_ID}', '{STATUS}', '{REQUESTED_TIME}', '{DELIVERY_TIME}','{PRODUCTS}','{STORE_NAME}','{STORE_PHONE}','{STORE_EMAIL}','{PAYMENT_NAME}','{SHIPPING_NAME}','{CUSTOMER_ADDRESS}'),
                array($order_info['firstname'], $order_id, $order_status_name, $requested_time, $order_info['delivery_time'],$product_data,$store_name,$this->config->get('config_telephone'),$this->config->get('config_email'),$order_info['payment_method'],$order_info['shipping_method'],$address_data),
                html_entity_decode($notification_data['owner_email']['content'])
            );

                   if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/order_update_notification.tpl')) {
                        $html = $template->fetch($this->config->get('config_template') . '/template/mail/order_update_notification.tpl');
                    } else {
                        $html = $template->fetch('default/template/mail/order_update_notification.tpl');
                    }
                    $subject =($notification_data['owner_email']['subject']!='')?$notification_data['owner_email']['subject']:$store_name.' Order - '.$order_id;

                        $mail = new Mail();
                        $mail->protocol = $this->config->get('config_mail_protocol');
                        $mail->parameter = $this->config->get('config_mail_parameter');
                        $mail->hostname = $this->config->get('config_smtp_host');
                        $mail->username = $this->config->get('config_smtp_username');
                        $mail->password = $this->config->get('config_smtp_password');
                        $mail->port = $this->config->get('config_smtp_port');
                        $mail->timeout = $this->config->get('config_smtp_timeout');
                        //$mail->setTo($order_info['email']);
                        $mail->setTo($this->config->get('config_tblreservation_email'));//$this->config->get('config_tblreservation_email')
                        $mail->setFrom($this->config->get('config_email'));
                        $mail->setSender($store_name);
                        $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                        $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                        $mail->setHtml($html);
                        $mail->send();
        }
        if(isset($notification_data['customer_email']) && isset($notification_data['customer_email']['status']) && $notification_data['customer_email']['status']=='1'){
            //$this->log->write( print_r("email notify customer", true));
            $template = new Template();
            $template->data['store_name'] = $store_name;
            if($this->config->get('config_email_header')) {
                $template->data['header_image'] = HTTP_IMAGE . $this->config->get('config_email_header');
            } else {
                $template->data['header_image'] = '';
            }
            if($this->config->get('config_email_offer')) {
                $template->data['offer_image'] = HTTP_IMAGE . $this->config->get('config_email_offer');
            } else {
                $template->data['offer_image'] = '';
            }
            if($this->config->get('config_email_footer')) {
                $template->data['footer_image'] = HTTP_IMAGE . $this->config->get('config_email_footer');
            } else {
                $template->data['footer_image'] = '';
            }
             $template->data['offer_url'] = $order_info['store_url'] .  $this->config->get('config_email_offer_url');
            $template->data['message'] = str_replace(
                array('{NAME}', '{ORDER_ID}', '{STATUS}', '{REQUESTED_TIME}', '{DELIVERY_TIME}','{PRODUCTS}','{STORE_NAME}','{STORE_PHONE}','{STORE_EMAIL}','{PAYMENT_NAME}','{SHIPPING_NAME}','{CUSTOMER_ADDRESS}'),
                array($order_info['firstname'], $order_id, $order_status_name, $requested_time, $order_info['delivery_time'],$product_data,$store_name,$this->config->get('config_telephone'),$this->config->get('config_email'),$order_info['payment_method'],$order_info['shipping_method'],$address_data),
                html_entity_decode($notification_data['customer_email']['content'])
            );

                     if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/order_update_notification.tpl')) {
                        $html = $template->fetch($this->config->get('config_template') . '/template/mail/order_update_notification.tpl');
                    } else {
                        $html = $template->fetch('default/template/mail/order_update_notification.tpl');
                    }
                    $subject =($notification_data['customer_email']['subject']!='')?$notification_data['customer_email']['subject']:$store_name.' Order - '.$order_id;

                        $mail = new Mail();
                        $mail->protocol = $this->config->get('config_mail_protocol');
                        $mail->parameter = $this->config->get('config_mail_parameter');
                        $mail->hostname = $this->config->get('config_smtp_host');
                        $mail->username = $this->config->get('config_smtp_username');
                        $mail->password = $this->config->get('config_smtp_password');
                        $mail->port = $this->config->get('config_smtp_port');
                        $mail->timeout = $this->config->get('config_smtp_timeout');
                        //$mail->setTo($order_info['email']);
                        $mail->setTo($reservation_data['email']);//$this->config->get('config_tblreservation_email')
                        $mail->setFrom($this->config->get('config_email'));
                        $mail->setSender($store_name);
                        $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                        $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                        $mail->setHtml($html);
                        $mail->send();
        }
        if(isset($notification_data['owner_sms']) && isset($notification_data['owner_sms']['status']) && $notification_data['owner_sms']['status']=='1'){
            $this->load->model('tool/sms');
            //$this->log->write( print_r("Sms Notify owner", true));
            $message = str_replace(
                array('{NAME}', '{ORDER_ID}', '{STATUS}', '{REQUESTED_TIME}', '{DELIVERY_TIME}', '{PRODUCTS}','{STORE_NAME}','{STORE_PHONE}','{STORE_EMAIL}','{PAYMENT_NAME}','{SHIPPING_NAME}','{CUSTOMER_ADDRESS}'),
                array($order_info['firstname'], $order_id, $order_status_name, $requested_time, $order_info['delivery_time'], $plain_product_data,$store_name,$this->config->get('config_telephone'),$this->config->get('config_email'),$order_info['payment_method'],$order_info['shipping_method'],$address_data),
                $notification_data['owner_sms']['content']
            );
            //$this->log->write($message);
            $response =  $this->model_tool_sms->sendSingleSMS($message,$this->config->get('config_phone'));
            //$this->log->write( print_r($response, true));
        }
        if(isset($notification_data['customer_sms']) && isset($notification_data['customer_sms']['status']) && $notification_data['customer_sms']['status']=='1'){
            $this->load->model('tool/sms');
            //$this->log->write( print_r("Sms Notify Customer", true));
            $message = str_replace(
                        array('{NAME}', '{ORDER_ID}', '{STATUS}', '{REQUESTED_TIME}', '{DELIVERY_TIME}', '{PRODUCTS}','{STORE_NAME}','{STORE_PHONE}','{STORE_EMAIL}','{PAYMENT_NAME}','{SHIPPING_NAME}','{CUSTOMER_ADDRESS}'),
                        array($order_info['firstname'], $order_id, $order_status_name, $requested_time, $order_info['delivery_time'], $plain_product_data,$store_name,$this->config->get('config_telephone'),$this->config->get('config_email'),$order_info['payment_method'],$order_info['shipping_method'],$address_data),
                        $notification_data['customer_sms']['content']
                    );
            //$this->log->write($message);
            $response =  $this->model_tool_sms->sendSingleSMS($message,$reservation_data['phone']);
            //$this->log->write( print_r($response, true));
        }  
    }
}

?>