<?php

class ControllerCommonHeader extends Controller {

    protected function index() {
		 if (!(isset($this->request->server['HTTPS']) && ($this->request->server['HTTPS'] == 'on' ||
            $this->request->server['HTTPS'] == 1) ||   isset($this->request->server['HTTP_X_FORWARDED_PROTO']) &&  
            $this->request->server['HTTP_X_FORWARDED_PROTO'] == 'https')){
            $redirect = 'https://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $redirect);
            exit();
        }
        if($_SERVER['REQUEST_URI']=='/offers/' || $_SERVER['REQUEST_URI']=='/offers'){
             $this->session->data['offerordering'] = true;
        }
       
        $this->data['title'] = $this->document->getTitle();

       if ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')))
                ||  (isset($this->request->server['HTTP_X_FORWARDED_PROTO']) &&  $this->request->server['HTTP_X_FORWARDED_PROTO'] == 'https')){
            $this->data['base'] = $this->config->get('config_ssl');
        } else {
            $this->data['base'] = $this->config->get('config_url');
        }
        
        $this->response->addHeader('Content-Security-Policy: script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' https://*.google.com https://*.googleapis.com https://*.doubleclick.net https://*.clarity.ms https://*.cookiebot.com https://*.googlesyndication.com https://*.googletagmanager.com https://*.gstatic.com  https://*.facebook.com https://*.facebook.net https://*.fbcdn.net https://*.sagepay.com https://*.stripe.com https://*.sumup.com https://*.truevo.eu https://truevo.eu https://*.oppwa.com https://oppwa.com https://*.worldpay.com https://*.paymentsense.cloud https://*.paypal.com https://cdn.jsdelivr.net https://*.handpoint.com https://*.cloudflare.com');
        $this->data['description'] = $this->document->getDescription();
        $this->data['keywords'] = $this->document->getKeywords();
        $this->data['links'] = $this->document->getLinks();
        $this->data['styles'] = $this->document->getStyles();
        $this->data['scripts'] = $this->document->getScripts();
        $this->data['lang'] = $this->language->get('code');
        $this->data['direction'] = $this->language->get('direction');

        if (isset($this->session->data['shadow_menu'])) {
            if($this->session->data['shadow_menu']!='' && $this->session->data['shadow_menu'] == $this->config->get('config_store_id')) {
                $this->data['shadow_menu'] = "<span class='error'>Shadow menu enabled.</span>";
            } 
        }
        // Whos Online
        if ($this->config->get('config_customer_online')) {
            $this->load->model('tool/online');

            if (isset($this->request->server['REMOTE_ADDR'])) {
                $ip = $this->request->server['REMOTE_ADDR'];
            } else {
                $ip = '';
            }

            if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
                $url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
            } else {
                $url = '';
            }

            if (isset($this->request->server['HTTP_REFERER'])) {
                $referer = $this->request->server['HTTP_REFERER'];
            } else {
                $referer = '';
            }

            $this->model_tool_online->whosonline($ip, $this->customer->getId(), $url, $referer);
        }
         //Dynamic Coupon To add
        if (isset($this->session->data['addcoupontype']) && $this->customer->isLogged()) {
            $customer_id = $this->customer->isLogged();
            $this->load->model('account/reward');
             $this->language->load('account/smreward');
            $coupon_type = $this->session->data['addcoupontype'];
            $couponcheck = $this->model_account_reward->getCustomercoupon($coupon_type, $customer_id);
            if (empty($couponcheck)) {
               
                $couponcode=$this->model_account_reward->addCoupon($coupon_type, $customer_id);
                if($coupon_type== 'facebook'){
                  $this->data['account_coupon_messege'] = $this->language->get('text_facebook_coupon_code') . $couponcode;
                }else{
                    $this->data['account_coupon_messege'] = $this->language->get('text_twitter_coupon_code') . $couponcode;
                }
                unset($this->session->data['addcoupontype']); 
            }
        } 
        if($this->customer->isLogged()){
            $this->data['loginstatus'] = 1;
        }else{
            $this->data['loginstatus'] = 0;
        }
        //Dynamic Coupon  end
        $this->language->load('common/header');

        if ((isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')))
                    ||  (isset($this->request->server['HTTP_X_FORWARDED_PROTO']) &&  $this->request->server['HTTP_X_FORWARDED_PROTO'] == 'https')) {
            $server = HTTPS_IMAGE;
        } else {
            $server = HTTP_IMAGE;
        }

        if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->data['icon'] = $server . $this->config->get('config_icon');
        } else {
            $this->data['icon'] = '';
        }

        $this->data['name'] = $this->config->get('config_name');

        if(isset($this->session->data['browser_type']) && ($this->session->data['browser_type']=='mobiles' || $this->session->data['browser_type']=='tablets')){
            $this->data['mobile'] =true;
        }else{
            $this->data['mobile'] =false;
        } 
        $this->data['mobile'] =true;
        if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
        	$this->data['logo'] = $server . $this->config->get('config_logo');
        } else {
        		$this->data['logo'] = '';
        	}


        $this->data['text_home'] = $this->language->get('text_home');
        $this->data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
        $this->data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
        $this->data['text_search'] = $this->language->get('text_search');
        $this->data['text_welcome'] = sprintf($this->language->get('text_welcome'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
        $this->data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));
        $this->data['text_account'] = $this->language->get('text_account');
        $this->data['text_checkout'] = $this->language->get('text_checkout');

        $this->data['home'] = $this->url->link('common/home');
        $this->data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
        $this->data['logged'] = $this->customer->isLogged();
        $this->data['account'] = $this->url->link('account/account', '', 'SSL');
        $this->data['shopping_cart'] = $this->url->link('checkout/cart');
        $this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
        $this->data['checkout_link'] = $this->url->link('checkout/checkoutetm','', 'SSL');

        $this->data['tablereservation'] = $this->url->link('information/tablereservation', '', 'SSL');
        $this->data['feedback'] = $this->url->link('information/feedback', '', 'SSL');
        $this->data['contact'] = $this->url->link('information/contact', '', 'SSL');
        $this->data['gallery'] = $this->url->link('information/gallery', '', 'SSL');
        $this->data['grouporder'] = $this->url->link('account/grouporder', '', 'SSL');
        $this->data['voucher'] = $this->url->link('checkout/voucher', '', 'SSL');
        $this->data['loyalty'] = $this->url->link('account/login', 'a=1', 'SSL');
        $this->data['aboutus'] = $this->url->link('information/information', 'information_id=4', 'SSL');

        if (isset($this->request->get['filter_name'])) {
            $this->data['filter_name'] = $this->request->get['filter_name'];
        } else {
            $this->data['filter_name'] = '';
        }

        $this->data['cart_count'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
        $this->data['text_menu_page'] = $this->language->get('text_menu_page');
        $this->data['menupage'] = 0;
        if(isset($this->request->get['route'])) {
            if($this->request->get['route'] == 'checkout/checkoutetm') {
                $this->data['menupage'] = 1;
            } else if($this->request->get['route'] == 'product/category') {
                $this->data['menupage'] = 2;
                //$this->data['offer_msg_text'] = $this->getChild('module/freeproducts/checkoffer');
            }
        }
        $this->data['shippingmethd'] = ''; 
        if(!$this->data['menupage']) {
            if(isset($this->session->data['delivery_method'])) {
                $this->data['shippingmethd'] = $this->session->data['delivery_method'];
            }
        }
        //$total = $this->cart->getTotal();
        $total_data = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();
        $this->load->model('setting/extension');

        // Display prices
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

                    $etm_t = microtime(true); $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes); $GLOBALS['etm_perf'][] = array('header total/' . $result['code'], microtime(true) - $etm_t); // timing for ?etm_perf=1
                }

                $sort_order = array();

                foreach ($total_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $total_data);
            }

            
        //$this->data['text_items'] = sprintf($this->language->get('text_total'), $this->currency->format($total)).'('.$this->data['cart_count'].')';
        //$this->data['text_items'] = sprintf($this->language->get('text_items'),  $this->data['cart_count'] , $this->currency->format($total));
        $this->data['text_items'] = sprintf($this->language->get('text_total_items'), $this->currency->format($total),$this->data['cart_count']);
        $this->data['itemcount']=$this->cart->countProducts();
        // Menu
        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $this->data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {
                    $data = array(
                        'filter_category_id' => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    // Product counts are only shown when "Category Product Count" is enabled;
                    // skip the COUNT queries otherwise (they ran for every category on every page).
                    $product_total = $this->config->get('config_product_count') ? $this->model_catalog_product->getTotalProducts($data) : 0;

                    $children_data[] = array(
                        'category_id' => $child['category_id'],
                        'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $product_total . ')' : ''),
                        'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                    );
                }

                // Level 1
                $this->data['categories'][] = array(
                    'name' => $category['name'],
                    'children' => $children_data,
                    'column' => $category['column'] ? $category['column'] : 1,
                    'href' => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
            }
        }

        // Post code search 
        if (isset($this->session->data['grouporderclient'])) {
            $this->data['grouporder'] = 1;
        } else {
            $this->data['grouporder'] = 0;
        }
        $postcode = '';
        if ($this->data['grouporder'] == 1) {
            $postcode = 'Group Order';
        } else {
            if (isset($_SESSION['postcode'])) {
                $postcode = $_SESSION['postcode'];
            }
        }
        if (isset($_SESSION['nostore'])) {
            $nostore = $_SESSION['nostore'];
        } else {
            $nostore = 0;
        }

        $this->data['postcode'] = $postcode;
        $this->data['nostore'] = $nostore;
        $this->data['name'] = $this->config->get('config_name');
        //$this->data['postcodebutton'] = $this->getChild('common/postcode/button');
        $this->data['postcodehome'] = $this->getChild('common/postcode/home');
        $this->data['itemcount'] = $this->cart->countProducts();
        $this->data['totalamount'] = $this->cart->getSubTotal();
        // Postcode search

        $this->children = array(
            'module/language',
            'module/currency',
            'module/cart',
            'module/cartmobile'
        );
        $this->data['socialmediabuttons'] = $this->getChild('common/postcode/socialmediabuttons');
        //$this->data['facebookbutton'] = $this->getChild('common/postcode/facebookbutton');
        //$this->data['postcodebutton'] = $this->getChild('common/postcode/button');
        //$this->data['postcodehome'] = $this->getChild('common/postcode/home');
        $this->data['holiday_status'] =$this->postcode->isStoreHoliday();
        $this->data['incart']=array();
        foreach ($this->cart->getProducts() as $product) {
            if(isset($this->data['incart'][$product['product_id']])){
                $this->data['incart'][$product['product_id']]['qty']+=$product['quantity'];
            }else{
                $this->data['incart'][$product['product_id']]=array('key'=>$product['key'],'qty'=>$product['quantity']);
            }
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/header.tpl';
        } else {
            $this->template = 'default/template/common/header.tpl';
        }

        $this->render();
    }

}

?>