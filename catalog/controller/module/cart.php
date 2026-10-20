<?php

class ControllerModuleCart extends Controller {

    public function index() {
        $this->language->load('module/cart');
        if (isset($this->request->get['deal_product_id'])) {
            $this->session->data['smartdeal_suggestedid'][] = $this->request->get['deal_product_id'];
            //echo $this->request->get['deal_product_id'];
        }
        if (isset($this->request->get['deal_discarded_id'])) {

            $this->session->data['discrded_deals'][] = $this->request->get['deal_discarded_id'];
            //echo $this->request->get['deal_product_id'];
        }
        if (isset($this->request->get['remove'])) {
            $this->cart->remove($this->request->get['remove']);

            unset($this->session->data['vouchers'][$this->request->get['remove']]);
        }
        //Add Same Product	
        if (isset($this->request->get['add1'])) {
            $this->cart->update($this->request->get['add1'], ($this->request->get['qty'] + 1));
        }
        if (isset($this->request->get['minus1'])) {
            $this->cart->update($this->request->get['minus1'], ($this->request->get['qty'] - 1));
            $this->session->data['cartupdated'][$this->request->get['minus1']] = true;
        }
        // POST
        if (isset($this->request->post['add1'])) {
            $this->cart->update($this->request->post['add1'], ($this->request->post['qty'] + 1));
        }
        if (isset($this->request->post['minus1'])) {
            $this->cart->update($this->request->post['minus1'], ($this->request->post['qty'] - 1));
            $this->session->data['cartupdated'][$this->request->post['minus1']] = true;
        }
        // Totals
        $this->load->model('setting/extension');

        $total_data = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();

        // Display prices
        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);
            // print_r($sort_order);
            // print_r( $results);
            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                }

                $sort_order = array();

                foreach ($total_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $total_data);
            }
        }
        $this->data['totals'] = $total_data;
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_items'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
        $this->data['text_empty'] = $this->language->get('text_empty');
        $this->data['text_cart'] = $this->language->get('text_cart');
        $this->data['text_checkout'] = $this->language->get('text_checkout');

        $this->data['button_remove'] = $this->language->get('button_remove');
        $this->load->model('tool/image');
        $this->load->model('catalog/product');
        $this->data['products'] = array();
        //print_r($this->cart->getProducts());
        $this->load->model('module/also_bought');
        $this->data['also_bought'] = $this->model_module_also_bought->getcartrelatedproduct();
        //print_r($this->data['also_bought']);

        // ---------------------------------------------------------------
        // FIX: build a lookup of what is *actually* in the cart, keyed by
        // product_id, using the real cart keys returned by getProducts()
        // instead of guessing '$pid' / '$pid.":::::"' against the raw
        // session array. This avoids false negatives when products have
        // options/recurring data that change the real cart key.
        // ---------------------------------------------------------------
        /*
        $cart_qty_by_pid = array();
        foreach ($this->cart->getProducts() as $cart_product) {
            $cpid = $cart_product['product_id'];
            if (!isset($cart_qty_by_pid[$cpid])) {
                $cart_qty_by_pid[$cpid] = 0;
            }
            $cart_qty_by_pid[$cpid] += $cart_product['quantity'];
        }

        if (!isset($this->session->data['free_upsell_granted'])) {
            $this->session->data['free_upsell_granted'] = array();
        }

        foreach ($this->session->data['upselling_cart_products'] as $pid => $upselling) {
            if (isset($this->session->data['cartupdated'][$pid]) || isset($this->session->data['cartupdated'][$pid . ':::::'])) {
                //if any product is updated in cart then do not add free product to cart
                continue;
            }

            if (isset($upselling['free_qty']) && (int) $upselling['free_qty'] > 0) {

                if (isset($cart_qty_by_pid[$pid])) {
                    $incart_qty = $cart_qty_by_pid[$pid];
                } else if (isset($cart_qty_by_pid[$pid . ':::::'])) {
                    $incart_qty = $cart_qty_by_pid[$pid . ':::::'];
                } else {
                    $incart_qty = 0;
                }

                if ($incart_qty > 0) {
                    // Product already in cart: top it up to free_qty exactly,
                    // never add on top of what's already there.
                    if ($incart_qty < $upselling['free_qty']) {
                        //$this->cart->update($pid, $upselling['free_qty']);
                        $this->session->data['cart'][$pid] = $upselling['free_qty'];
                    }
                    $this->session->data['free_upsell_granted'][$pid] = true;
                } else {
                    // Product not in cart at all: only add the free qty once.
                    // The 'free_upsell_granted' flag stops this from
                    // re-adding free_qty on every subsequent page load
                    // (e.g. if the customer manually removes the item).
                    if (empty($this->session->data['free_upsell_granted'][$pid])) {
                       // $this->cart->add($pid, $upselling['free_qty']);
                        $this->session->data['cart'][$pid] = $upselling['free_qty'];
                        $this->session->data['free_upsell_granted'][$pid] = true;
                    }
                }
            }
        }
     
        */
        $this->data['prodtqyys'] = array();
        $also_bought_qty=0;
        foreach ($this->cart->getProducts() as $product) {
            if ($product['image']) {
                $image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $image = '';
            }
            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['option_value'];
                } else {
                    $filename = $this->encryption->decrypt($option['option_value']);

                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                }

                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                    'type' => $option['type']
                );
            }
            $productbundle = array();
            foreach ($product['bundled_data'] as $bundled_product) {
                $bundle_options = array();
                foreach ($bundled_product['option'] as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['option_value'];
                    } else {
                        $filename = $this->encryption->decrypt($option['option_value']);

                        $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                    }
                    $bundle_options[] = array(
                        'name' => $option['name'],
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                        'type' => $option['type']
                    );
                }
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($bundled_product['price'], $bundled_product['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = false;
                }
                // Display prices
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $total = $this->currency->format($this->tax->calculate($bundled_product['price'], $bundled_product['tax_class_id'], $this->config->get('config_tax')) * $bundled_product['quantity']);
                } else {
                    $total = false;
                }
                //$chk_default_options = $this->model_catalog_product->getDefaultOptions($bundled_product['product_id']);
                $has_bundled_toppings = $this->model_catalog_product->checkProductToppings($bundled_product['product_id']);
                // $new_key = $bundled_product['product_id'].':'.(isset($data[1])?$data[1]:'').':'.(isset($data[2])?$data[2]:'').':'.(isset($data[3])?$data[3]:'').':'.(!empty($bundled_product['toppings'])?base64_encode(serialize($bundled_product['toppings'])):'').':'.(!empty($bundled_product['dressings'])?base64_encode(serialize($bundled_product['dressings'])):''); 

                $productbundle[] = array(
                    'product_id' => $bundled_product['product_id'],
                    // 'thumb' => $image,
                    'name' => $bundled_product['name'],
                    'model' => $bundled_product['model'],
                    'option' => $bundle_options,
                    'quantity' => $bundled_product['quantity'],
                    'price' => $price,
                    'total' => $total,
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    'toppings' => $bundled_product['toppings'],
                    'dressings' => $bundled_product['dressings'],
                    'hastoppings' => $has_bundled_toppings,
                        // 'default' => (!empty($chk_default_options) ? 1 : 0)
                );
            }
            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
            } else {
                $price = false;
            }
            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
            } else {
                $total = false;
            }
            $chk_default_options = $this->model_catalog_product->getDefaultOptions($product['product_id']);
            $has_toppings = $this->model_catalog_product->checkProductToppings($product['product_id']);
            $this->data['products'][] = array(
                'key' => $product['key'],
                'product_id' => $product['product_id'],
                'thumb' => $image,
                'name' => $product['name'],
                'org_name' => str_replace(array("'", '"'), '', $product['name']),
                'model' => $product['model'],
                'option' => $option_data,
                'quantity' => $product['quantity'],
                'price' => $price,
                'total' => $total,
                'org_total'=>$product['price']*$product['quantity'],
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                'default' => (!empty($chk_default_options) ? 1 : 0),
                'bundle' => $productbundle,
                'toppings' => $product['toppings'],
                'hastoppings' => $has_toppings,
                'dressings' => $product['dressings'],
                'stock'=>$product['stock'],
                'category'=>$this->model_catalog_product->getCatname($product['product_id'])
            );
            if($product['subtract']=='1'){
                if(!isset($this->data['prodtqyys'][$product['product_id']])){
                    $this->data['prodtqyys'][$product['product_id']]=array('tot_qty'=>$product['available_qty'],'in_qty'=>$product['quantity']);
                }else{
                    $this->data['prodtqyys'][$product['product_id']]['in_qty'] +=$product['quantity'];
        }
            }
            if(isset($this->data['also_bought'][$product['product_id']])){
                $this->data['also_bought'][$product['product_id']]['quantity']+=$product['quantity'];
                //$this->data['also_bought'][$product['product_id']]['price'] = $total;
                if($this->data['also_bought'][$product['product_id']]['free_qty']>0){
                     if($this->data['also_bought'][$product['product_id']]['quantity']>$this->data['also_bought'][$product['product_id']]['free_qty']){
                        $this->data['also_bought'][$product['product_id']]['price'] =$this->currency->format(($this->data['also_bought'][$product['product_id']]['quantity']-$this->data['also_bought'][$product['product_id']]['free_qty'])*$product['price']);
                    }else{
                        $this->data['also_bought'][$product['product_id']]['price'] = $this->currency->format(0);
                    }
                }else{
                    $this->data['also_bought'][$product['product_id']]['price'] = $total;
                }
               $also_bought_qty +=$product['quantity'];
            }
        }
        if(!empty($this->data['also_bought']) && $also_bought_qty==0){
            $this->data['confirmfree']=1;
        }else{
            $this->data['confirmfree']=0;
        }
        // Gift Voucher
        $this->data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $key => $voucher) {
                $this->data['vouchers'][] = array(
                    'key' => $key,
                    'description' => $voucher['description'],
                    'amount' => $this->currency->format($voucher['amount'])
                );
            }
        }
        $this->data['cart'] = $this->url->link('checkout/cart');
        // $this->data['cartshipping'] = $this->getChild('checkout/shipping_postcodes/cartshipping');
        // $this->data['also_bought'] = $this->getChild('product/category/getAlsoBoughtProducts');
        $this->data['order_total'] = $this->cart->getTotal(); 
        $this->data['text_comments'] = $this->language->get('text_comments');
        if (isset($this->session->data['comment'])) {
            $this->data['comment'] = $this->session->data['comment'];
        } else {
            $this->data['comment'] = '';
        }
        $this->data['checkout'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
        if (isset($this->session->data['comment'])) {
            $this->data['notes'] = $this->session->data['comment'];
        } else {
            $this->data['notes'] = '';
        }
        $this->data['logged'] = $this->customer->isLogged();
        $this->data['text_welcome'] = sprintf($this->language->get('text_welcome'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
        $this->data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));
        $this->data['text_account'] = $this->language->get('text_account');
        //$this->data['freeproducts'] = $this->getChild('module/freeproducts');
        //
         //$this->data['delivery_method'] ='dine.dine';
        if (isset($this->session->data['order_requested_time'])) {
            if($this->session->data['order_requested_time'] > strtotime('now')) {
                $this->data['order_requested_time'] =  'Requested Time : '.date("h:i A (l)",$this->session->data['order_requested_time']);
            }else{
                $this->data['order_requested_time'] =  'Requested Time : ASAP';
            }
        }else{
            $this->data['order_requested_time'] =  '';
        }
        if(isset($this->session->data['tableordering']) && isset($this->session->data['dine_table_id']) && isset($this->session->data['delivery_method'])){
            $this->data['delivery_method'] = $this->session->data['delivery_method'];
        } 
        if(isset($this->session->data['shipping_method']) && isset($this->session->data['shipping_method']['code'])){
            $this->data['shipping_selected'] =$this->session->data['shipping_method']['code'];
        }
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/cart.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/cart.tpl';
        } else {
            $this->template = 'default/template/module/cart.tpl';
        }

        $this->response->setOutput($this->render());
    }

}

?>