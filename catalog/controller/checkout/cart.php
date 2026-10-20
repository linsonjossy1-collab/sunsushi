<?php

class ControllerCheckoutCart extends Controller {

    private $error = array();

    public function index() {
         if (isset($this->request->get['remove']) && isset($this->request->get['final'])) {
            $this->cart->remove($this->request->get['remove']);
            unset($this->session->data['vouchers'][$this->request->get['remove']]);
            $this->session->data['success'] = $this->language->get('text_remove');
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['reward']);
            unset($this->session->data['product_no_times']);
            $this->redirect($this->url->link('checkout/checkoutetm'));
        }
        $this->redirect($this->url->link('checkout/checkoutetm'));
        $this->language->load('checkout/cart');

        if (!isset($this->session->data['vouchers'])) {
            $this->session->data['vouchers'] = array();
        }

        // Update
        if (!empty($this->request->post['quantity'])) {
            foreach ($this->request->post['quantity'] as $key => $value) {
                $this->cart->update($key, $value);
            }

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['reward']);

            $this->redirect($this->url->link('checkout/cart'));
        }

        // Remove
        if (isset($this->request->get['remove'])) {
            $this->cart->remove($this->request->get['remove']);

            unset($this->session->data['vouchers'][$this->request->get['remove']]);

            $this->session->data['success'] = $this->language->get('text_remove');

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['reward']);
             if(isset($this->request->get['final'])){
                $this->redirect($this->url->link('checkout/checkoutetm'));
            }else{
            $this->redirect($this->url->link('checkout/cart'));
        }

        }

        // Coupon    
        if (isset($this->request->post['coupon']) && $this->validateCoupon()) {
            $this->session->data['coupon'] = $this->request->post['coupon'];

            $this->session->data['success'] = $this->language->get('text_coupon');

            $this->redirect($this->url->link('checkout/cart'));
        }

        // Voucher
        if (isset($this->request->post['voucher']) && $this->validateVoucher()) {
            $this->session->data['voucher'] = $this->request->post['voucher'];

            $this->session->data['success'] = $this->language->get('text_voucher');

            $this->redirect($this->url->link('checkout/cart'));
        }

        // Reward
        if (isset($this->request->post['reward']) && $this->validateReward()) {
            $this->session->data['reward'] = $this->request->post['reward'];

            $this->session->data['success'] = $this->language->get('text_reward');

            $this->redirect($this->url->link('checkout/cart'));
        }

        // Shipping
        if (isset($this->request->post['shipping_method']) && $this->validateShipping()) {
            $shipping = explode('.', $this->request->post['shipping_method']);

            $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

            $this->session->data['success'] = $this->language->get('text_shipping');

            $this->redirect($this->url->link('checkout/cart'));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart'),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
            $points = $this->customer->getRewardPoints();

            $points_total = 0;
            // new reward points
            $this->load->model('setting/setting');
            $rewards = $this->model_setting_setting->getSetting('rewardpoints');
            if (is_array($rewards) && isset($rewards[1]['points']) && $rewards[1]['status'] == 1) {
                $points_total = floor($this->cart->getTotal() / floatval($rewards[1]['points']));
            }


            /* foreach ($this->cart->getProducts() as $product) {
              if ($product['points']) {
              $points_total += $product['points'];
              }
              } */

            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->data['text_next'] = $this->language->get('text_next');
            $this->data['text_next_choice'] = $this->language->get('text_next_choice');
            $this->data['text_use_coupon'] = $this->language->get('text_use_coupon');
            $this->data['text_use_voucher'] = $this->language->get('text_use_voucher');
            $this->data['text_use_reward'] = sprintf($this->language->get('text_use_reward'), $points);
            $this->data['text_shipping_estimate'] = $this->language->get('text_shipping_estimate');
            $this->data['text_shipping_detail'] = $this->language->get('text_shipping_detail');
            $this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
            $this->data['text_select'] = $this->language->get('text_select');
            $this->data['text_none'] = $this->language->get('text_none');

            $this->data['column_image'] = $this->language->get('column_image');
            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_model'] = $this->language->get('column_model');
            $this->data['column_quantity'] = $this->language->get('column_quantity');
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_total'] = $this->language->get('column_total');

            $this->data['entry_coupon'] = $this->language->get('entry_coupon');
            $this->data['entry_voucher'] = $this->language->get('entry_voucher');
            $this->data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);
            $this->data['entry_country'] = $this->language->get('entry_country');
            $this->data['entry_zone'] = $this->language->get('entry_zone');
            $this->data['entry_postcode'] = $this->language->get('entry_postcode');

            $this->data['button_update'] = $this->language->get('button_update');
            $this->data['button_remove'] = $this->language->get('button_remove');
            $this->data['button_coupon'] = $this->language->get('button_coupon');
            $this->data['button_voucher'] = $this->language->get('button_voucher');
            $this->data['button_reward'] = $this->language->get('button_reward');
            $this->data['button_quote'] = $this->language->get('button_quote');
            $this->data['button_shipping'] = $this->language->get('button_shipping');
            $this->data['button_shopping'] = $this->language->get('button_shopping');
            $this->data['button_checkout'] = $this->language->get('button_checkout');

            if (isset($this->error['warning'])) {
                $this->data['error_warning'] = $this->error['warning'];
            } elseif (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
                $this->data['error_warning'] = $this->language->get('error_stock');
            } else {
                $this->data['error_warning'] = '';
            }

            if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
                $this->data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
            } else {
                $this->data['attention'] = '';
            }

            if (isset($this->session->data['success'])) {
                $this->data['success'] = $this->session->data['success'];

                unset($this->session->data['success']);
            } else {
                $this->data['success'] = '';
            }

            $this->data['action'] = $this->url->link('checkout/cart');

            if ($this->config->get('config_cart_weight')) {
                $this->data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
            } else {
                $this->data['weight'] = '';
            }

            $this->load->model('tool/image');

            $this->data['products'] = array();

            $products = $this->cart->getProducts();

            foreach ($products as $product) {
                $product_total = 0;

                foreach ($products as $product_2) {
                    if ($product_2['product_id'] == $product['product_id']) {
                        $product_total += $product_2['quantity'];
                    }
                }

                if ($product['minimum'] > $product_total) {
                    $this->data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
                }

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
                        'value' => html_entity_decode($value)
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
                        'value' => html_entity_decode($value),
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
                $productbundle[] = array(
                    'product_id' => $bundled_product['product_id'],
                    //'thumb' => $image,
                    'name' => $bundled_product['name'],
                    'model' => $bundled_product['model'],
                    'option' => $bundle_options,
                    'quantity' => $bundled_product['quantity'],
                    'price' => $price,
                    'total' => $total,
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
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
                    $total = $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $total = false;
                }

                $this->data['products'][] = array(
                    'key' => $product['key'],
                    'thumb' => $image,
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'quantity' => $product['quantity'],
                    'stock' => $product['stock'],
                    'reward' => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
                    'price' => $price,
                    'total' => $total,
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    'remove' => $this->url->link('checkout/cart', 'remove=' . $product['key']),
                    'bundle'=>$productbundle
                );
            }

            // Gift Voucher
            $this->data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $key => $voucher) {
                    $this->data['vouchers'][] = array(
                        'key' => $key,
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount']),
                        'remove' => $this->url->link('checkout/cart', 'remove=' . $key)
                    );
                }
            }

            if (isset($this->request->post['next'])) {
                $this->data['next'] = $this->request->post['next'];
            } else {
                $this->data['next'] = '';
            }

            $this->data['coupon_status'] = $this->config->get('coupon_status');

            if (isset($this->request->post['coupon'])) {
                $this->data['coupon'] = $this->request->post['coupon'];
            } elseif (isset($this->session->data['coupon'])) {
                $this->data['coupon'] = $this->session->data['coupon'];
            } else {
                $this->data['coupon'] = '';
            }

            $this->data['voucher_status'] = $this->config->get('voucher_status');

            if (isset($this->request->post['voucher'])) {
                $this->data['voucher'] = $this->request->post['voucher'];
            } elseif (isset($this->session->data['voucher'])) {
                $this->data['voucher'] = $this->session->data['voucher'];
            } else {
                $this->data['voucher'] = '';
            }

            $this->data['reward_status'] = ($points && $points_total && $this->config->get('reward_status'));

            if (isset($this->request->post['reward'])) {
                $this->data['reward'] = $this->request->post['reward'];
            } elseif (isset($this->session->data['reward'])) {
                $this->data['reward'] = $this->session->data['reward'];
            } else {
                $this->data['reward'] = '';
            }

            $this->data['shipping_status'] = $this->config->get('shipping_status') && $this->config->get('shipping_estimator') && $this->cart->hasShipping();

            if (isset($this->request->post['country_id'])) {
                $this->data['country_id'] = $this->request->post['country_id'];
            } elseif (isset($this->session->data['shipping_country_id'])) {
                $this->data['country_id'] = $this->session->data['shipping_country_id'];
            } else {
                $this->data['country_id'] = $this->config->get('config_country_id');
            }

            $this->load->model('localisation/country');

            $this->data['countries'] = $this->model_localisation_country->getCountries();

            if (isset($this->request->post['zone_id'])) {
                $this->data['zone_id'] = $this->request->post['zone_id'];
            } elseif (isset($this->session->data['shipping_zone_id'])) {
                $this->data['zone_id'] = $this->session->data['shipping_zone_id'];
            } else {
                $this->data['zone_id'] = '';
            }

            if (isset($this->request->post['postcode'])) {
                $this->data['postcode'] = $this->request->post['postcode'];
            } elseif (isset($this->session->data['shipping_postcode'])) {
                $this->data['postcode'] = $this->session->data['shipping_postcode'];
            } else {
                $this->data['postcode'] = '';
            }

            if (isset($this->request->post['shipping_method'])) {
                $this->data['shipping_method'] = $this->request->post['shipping_method'];
            } elseif (isset($this->session->data['shipping_method'])) {
                $this->data['shipping_method'] = $this->session->data['shipping_method']['code'];
            } else {
                $this->data['shipping_method'] = '';
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

            $this->data['continue'] = $this->url->link('common/home');
            // Group Order
            if (isset($this->session->data['grouporderclient'])) {
                $this->data['checkout'] = $this->url->link('account/grouporder/clinetchekout', '', 'SSL');
            } else {
                $this->data['checkout'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
            }

            //$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/cart.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/checkout/cart.tpl';
            } else {
                $this->template = 'default/template/checkout/cart.tpl';
            }

            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_bottom',
                'common/content_top',
                'common/footer',
                'common/header',
               // 'module/freeproducts'
            );

            $this->response->setOutput($this->render());
        } else {
            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->data['text_error'] = $this->language->get('text_empty');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->url->link('common/home');

            unset($this->session->data['success']);

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
            } else {
                $this->template = 'default/template/error/not_found.tpl';
            }

            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render());
        }
    }

    private function validateCoupon() {
//        $this->load->model('checkout/coupon');
//
//        $coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);
//
//        if (!$coupon_info) {
//            $this->error['warning'] = $this->language->get('error_coupon');
//        }
//
//        if (!$this->error) {
//            return true;
//        } else {
//            return false;
//        }
         $json = array();
        if (isset($this->request->post['coupon'])) {
             $this->language->load('checkout/checkout');
             $this->load->model('checkout/coupon');
             $this->load->model('checkout/offers');
             $offer_couponinfo= $this->model_checkout_offers->getCoupon($this->request->post['coupon']);
            // $coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);

            if (!$offer_couponinfo) {
                $json['error']['warning'] = $this->language->get('error_coupon');
            }
            else {
                $this->session->data['coupon'] = $this->request->post['coupon'];
                 $this->language->load('checkout/cart');
                $json['success'] = $this->language->get('text_coupon');
            }
        }
        $this->response->setOutput(json_encode($json));
    }

    private function validateVoucher() {
        $this->load->model('checkout/voucher');

        $voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);

        if (!$voucher_info) {
            $this->error['warning'] = $this->language->get('error_voucher');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function validateReward() {
        $points = $this->customer->getRewardPoints();

        $points_total = 0;
        // new reward points
        $this->load->model('setting/setting');
        $rewards = $this->model_setting_setting->getSetting('rewardpoints');
        if (is_array($rewards) && isset($rewards[1]['points']) && $rewards[1]['status'] == 1) {
            $points_total = floor($this->cart->getTotal() / floatval($rewards[1]['points']));
        }
        /*
          foreach ($this->cart->getProducts() as $product) {
          if ($product['points']) {
          $points_total += $product['points'];
          }
          }
         */
        if (empty($this->request->post['reward'])) {
            $this->error['warning'] = $this->language->get('error_reward');
        }

        if ($this->request->post['reward'] > $points) {
            $this->error['warning'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
        }

        if ($this->request->post['reward'] > $points_total) {
            $this->error['warning'] = sprintf($this->language->get('error_maximum'), $points_total);
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function validateShipping() {
        if (!empty($this->request->post['shipping_method'])) {
            $shipping = explode('.', $this->request->post['shipping_method']);

            if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
                $this->error['warning'] = $this->language->get('error_shipping');
            }
        } else {
            $this->error['warning'] = $this->language->get('error_shipping');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function add() {
        $this->language->load('checkout/cart');

        $json = array();
        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
        } else {
            $product_id = 0;
        }

        $this->load->model('catalog/product');
 
        $product_info = $this->model_catalog_product->getProduct($product_id);
        if ($product_info) {
            if($product_info['subtract']==1){
                $currentlyin = 0;
                foreach($this->cart->getProducts() as $prodt){
                    if($prodt['product_id']==$product_id){
                        $currentlyin +=$prodt['quantity'];
                    }                    
                }
                if(($product_info['quantity']-$currentlyin)<=0){
                    $json['error']['outofstock'] = $product_info['name'].' is currently not available';                
                    if(isset($this->session->data['item_unavailable'])){
                        $this->session->data['item_unavailable'] .=','.$product_info['name'] ;
                    }else{
                        $this->session->data['item_unavailable'] = $product_info['name'];
                    }
                    $this->response->setOutput(json_encode($json));
                    return;
                }                
            }
            if (isset($this->request->post['quantity'])) {
                $quantity = $this->request->post['quantity'];
            } else {
                $quantity = 1;
            }

            if($product_info['minimum']){
                $currentlyin = $quantity;
                foreach($this->cart->getProducts() as $prodt){
                    if($prodt['product_id']==$product_id){
                        if(isset($this->request->post['option']) && $prodt['option']) {
                            foreach($prodt['option'] as $op) {
                                if(isset($this->request->post['option'][$op['product_option_id']])) {
                                    if($op['product_option_value_id']==$this->request->post['option'][$op['product_option_id']]) {
                                        $currentlyin +=$prodt['quantity'];            
                                    }

                                }
                            }
                            //$this->log->write(json_encode($prodt['option']));
                        } else {
                            $currentlyin +=$prodt['quantity'];                            
                        }
                    }                    
                }
                if($product_info['minimum'] > $currentlyin){
                    $quantity = $product_info['minimum'];
                }
            }

            if (isset($this->request->post['option'])) {
                $option = array_filter($this->request->post['option']);
            } else {
                $option = array();
            }
            $product_options = array();
            //if(!isset($this->request->post['main_product_id'])){
            $product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);
            //}
            $master_options = array();
            foreach ($product_options as $key => $product_option) {
                if($product_option['required'] && isset($option[$product_option['product_option_id']])) {
                    foreach($product_option['option_value'] as $op_value) {
                        if($op_value['product_option_value_id']==$option[$product_option['product_option_id']]) {
                            $master_options[$product_option['option_id']]=$op_value['option_value_id'];
                        }
                    }
                }
            }
            if(!isset($this->request->post['main_product_id'])){
                foreach ($product_options as $product_option) {
                    if($product_option['required']) {
                        if($product_option['master_option']) {
                            if(!isset($master_options[$product_option['master_option']])) {
                                continue;
                            }
                            $subcount=0;
                            foreach($product_option['option_value'] as $op_value) {
                                if($op_value['master_option_value']==$master_options[$product_option['master_option']]) {
                                    $subcount++;
                                }
                            }
                            if(!$subcount) {
                                continue;
                            }
                        }

                        if ( !isset($option[$product_option['product_option_id']]) || empty($option[$product_option['product_option_id']])) {
                            $json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
                        } else if(isset($product_option['minimum']) && sizeof($option[$product_option['product_option_id']]) < $product_option['minimum']){
                            $json['error']['minimum'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), 'Minimum '.$product_option['minimum']);
                        }
                    }
                    }
            }    
            $no_needed_options = array();
            foreach ($product_options as $product_option) {
                if ($product_option['default']) {
                    if(isset($option[$product_option['product_option_id']])){
                    foreach ($product_option['option_value'] as $option_value) {
                        if (!in_array($option_value['product_option_value_id'], $option[$product_option['product_option_id']])) {

                            $no_needed_options[$product_option['product_option_id']][] = $option_value['product_option_value_id'];
                        }
                    }
                    $no_needed_options[$product_option['product_option_id']]['default'] = $product_option['product_option_id'];
                    }else{
                        foreach ($product_option['option_value'] as $option_value) {
                            $no_needed_options[$product_option['product_option_id']][] = $option_value['product_option_value_id'];
                        }
                        $no_needed_options[$product_option['product_option_id']]['default'] = $product_option['product_option_id'];
                    }
                }
            }

            foreach ($no_needed_options as $key => $no_option) {
                //unset($option[$key]);
                if (isset($no_option['default'])) {
                    if (count($no_option) > 1) {
                        unset($no_option['default']);
                        $option[$key] = $no_option;
                    } else {
                        //unset($no_option['default']);
                        unset($option[$key]);
//                    
                    }
                }
            }
                
            
            if(isset($this->request->post['main_product_id'])){
                $productoptions = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);
                $bundled_options = $this->model_catalog_product->getIndealProductOptions($this->request->post['bundle_id'], $this->request->post['product_id']);               
                //
                //print_r($bundled_options);
                //print_r($productoptions);
                foreach($productoptions as $proption){
                    if($proption['required'] && isset($bundled_options[$proption['product_option_id']])) {
                            if (!isset($option[$proption['product_option_id']]) || empty($option[$proption['product_option_id']])) {
                                $json['error']['option'][$proption['product_option_id']] = sprintf($this->language->get('error_required'), $proption['name']);
                        }
                    }                
                }
                if(!$json && isset($this->request->post['dealcomplete'])){
                    $bunled_items = $this->model_catalog_product->getProductBundledItems($this->request->post['main_product_id']); 
                    $total_bundled_qty = 0; 
                    foreach ($bunled_items as $i => $item) {
                        $total_bundled_qty +=$item['bundle_quantity'];
                    }

                    if(isset($this->session->data['added_bundle'][$this->request->post['main_product_id']])) {                    
                        //$count = count($this->session->data['added_bundle'][$this->request->post['main_product_id']]);
                        $count = 0;
                        foreach($this->session->data['added_bundle'][$this->request->post['main_product_id']] as $added_bundle){
                            $count++;
                        }
                        if($count >= $total_bundled_qty) {
                            $json['error']['quantity_count'] = $total_bundled_qty;
                            $json['error']['warning'] = 'You have selected the maximum no.of main dishes for this product.';
                        }
                    } 
                }                                
            }
            //die();
           // print_r($option);
            //Bundled Products
            //Remove Bundled Code
            if (isset($this->request->post['bundledoption'])) {
                $bundledoption=$this->request->post['bundledoption'];
            }else{
                $bundledoption=array();
            }
            if (isset($this->request->post['bundledtoppings'])) {
                $bundledtoppings=$this->request->post['bundledtoppings'];
            }else{
                $bundledtoppings=array();
            }
            if (isset($this->request->post['bundledtoppingqtys'])) {
                $bundledtoppingqtys=$this->request->post['bundledtoppingqtys'];
            }else{
                $bundledtoppingqtys=array();
            }
            if (isset($this->request->post['bundledtdressings'])) {                
                $bundleddressings=$this->request->post['bundledtdressings'];
            }else{
                $bundleddressings=array();
            }
            //print_r($bundledtoppings);
            //print_r($bundledtoppingqtys);
            if (isset($this->request->post['bundled_product'])) {
                  $bundled = array();
                
                foreach ($this->request->post['bundled_product'] as $bundled_product) {
                    $bun_prodt_id=  explode('_', $bundled_product);
                    $optons=array();
                    $toppings =  array();
                    $dressings =  array();
                    if(isset($bundledoption[$bundled_product])){
                        $optons=$bundledoption[$bundled_product];
                    }
                    if(isset($bundledtoppings[$bundled_product]) && isset($bundledtoppingqtys[$bundled_product])){                        
                        foreach ($bundledtoppings[$bundled_product] as $topping_id=>$topping_values) {
                            foreach($topping_values as $topping_value_id){
                                if(isset($bundledtoppingqtys[$bundled_product][$topping_id][$topping_value_id]['quantity']) && !empty($bundledtoppingqtys[$bundled_product][$topping_id][$topping_value_id]['quantity']) && $bundledtoppingqtys[$bundled_product][$topping_id][$topping_value_id]['quantity']!=0){
                                    $toppings[$topping_id][$topping_value_id]=$bundledtoppingqtys[$bundled_product][$topping_id][$topping_value_id]['quantity'];
                                }
                            }
                        }
                    }
                    if(isset($bundleddressings[$bundled_product])){
                        foreach ($bundleddressings[$bundled_product] as $dressing_id=>$dressing_values) {
                            foreach($dressing_values as $dressing_value_id){
                                $dressings[$dressing_id][$dressing_value_id]=1;
                            }
                        }
                    }
                    $bundled[]=array('bundled_id'=>$bun_prodt_id[0],'product_id'=>$bun_prodt_id[2],'options'=>$optons,'toppings'=>$toppings,'dressings'=>$dressings);
                }
                
                foreach ($bundled as $key => $b_product) {
                    //bundled option required and minimum required                  
                    $product_bundle_options = $this->model_catalog_product->getProductOptions($b_product['product_id']);
                    foreach ($product_bundle_options as $product_bundle_option) {
                        if($product_bundle_option['required'] && isset($product_bundle_option['minimum']) && sizeof($b_product['options'][$product_bundle_option['product_option_id']]) < $product_bundle_option['minimum']){
                            $json['error']['minimum'][$product_bundle_option['product_option_id']] = sprintf($this->language->get('error_required'), 'Minimum '.$product_bundle_option['minimum']);
                        }
                        if ($product_bundle_option['required'] && empty($b_product['options'][$product_bundle_option['product_option_id']])) {                            
                            $json['error']['option'][$product_bundle_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_bundle_option['name']);
                        }
                    }

                    if (!empty($b_product['options'])) {
                        foreach ($b_product['options'] as $option_id => $bp_options) {
                            if (is_array($bp_options)) {
                                if ($this->model_catalog_product->chkDefaultoptions($option_id) == 1) {
                                    $bundled_options = $this->model_catalog_product->getBundledProductOptions($b_product['bundled_id'], $b_product['product_id']);
                                    $included_options = array();
                                    $product_option_values = $this->model_catalog_product->getproductOptionvalues($option_id); //all option
                                    foreach ($bundled_options as $option_value_id) {
                                        if (in_array($option_value_id, $product_option_values)) {
                                            $included_options[] = $option_value_id;
                                        }
                                    }
                                    if (count($included_options) != count($bp_options)) {
                                        foreach ($included_options as $included_option) {
                                            if (!in_array($included_option, $bp_options)) {
                                                $new_options[] = $included_option;
                                            }
                                        }
                                    } else {
                                        unset($bundled[$key]['options'][$option_id]);
                                    }
                                    if (isset($new_options))
                                        $bundled[$key]['options'][$option_id] = $new_options;
                                    unset($new_options);
                                }
                            }
                        }
                    }
                }
                if (!empty($bundled))
                    $option['bundled'] = $bundled;
                
            } 
            $toppings =  array();
            $max_topping = 0;
            if (isset($this->request->post['topping'])) { 
                foreach ($this->request->post['topping'] as $topping_id=>$topping_values) {
                    foreach($topping_values as $topping_value_id){
                        //echo $topping_id.'--'.$topping_value_id.'</br>';
                        if(isset($this->request->post['topping_qtys'][$topping_id][$topping_value_id]['quantity']) && !empty($this->request->post['topping_qtys'][$topping_id][$topping_value_id]['quantity']) && $this->request->post['topping_qtys'][$topping_id][$topping_value_id]['quantity']!=0){
                            $toppings[$topping_id][$topping_value_id]=$this->request->post['topping_qtys'][$topping_id][$topping_value_id]['quantity'];
                            $max_topping+= $this->request->post['topping_qtys'][$topping_id][$topping_value_id]['quantity'];
                        }
                    }
                }
                if($product_info['maximum_toppings'] && (int)$product_info['maximum_toppings'] < $max_topping) {
                    $json['error']['warning'] = 'Maximum no. quantity is '.$product_info['maximum_toppings'];
                }
            }
            //print_r($toppings);
            if (!empty($toppings)){
                    $option['topping'] = $toppings;
            } 
            $dressings =  array();
            if (isset($this->request->post['dressing'])) {
                foreach ($this->request->post['dressing'] as $dressing_id=>$dressing_values) {
                    foreach($dressing_values as $dressing_value_id){
                        $dressings[$dressing_id][$dressing_value_id]=1;
                    }
                }
            }
            if (!empty($dressings)){
                $option['dressing'] = $dressings;
            }
            if(isset($this->request->post['main_product_id'])){
                $option['main_product_id']=$this->request->post['main_product_id'];   
                $option['bundle_id']=$this->request->post['bundle_id'];   
                $option['bundleidval']=(isset($this->request->post['mobileapp']) && isset($this->request->post['bundleidval']))?$this->request->post['bundleidval']:time();   
            }
            if(isset($this->request->post['dealcomplete'])){
                $option['dealcomplete']=$this->request->post['dealcomplete'];                                              
            }
            //Deal Item Add
            if(isset($this->request->post['indealitems'])){
                $deal_product_id = (int)$this->request->post['main_product_id'];
                foreach($this->request->post['indealitems'] as $in_items){
                    if(isset($this->session->data['added_bundle'][$deal_product_id])){
                        if(isset($this->session->data['added_bundle'][$deal_product_id][$in_items['key']])){
                            if(isset($this->session->data['added_bundle'][$deal_product_id][$in_items['key']][$in_items['bundle_id']])){
                                $this->session->data['added_bundle'][$deal_product_id][$in_items['key']][$in_items['bundle_id']] +=(int)$in_items['qty'];
                            }else{
                                $this->session->data['added_bundle'][$deal_product_id][$in_items['key']]=array($in_items['bundle_id']=>(int)$in_items['qty']);
                            }
                        }else{
                            $this->session->data['added_bundle'][$deal_product_id][$in_items['key']]=array($in_items['bundle_id']=>(int)$in_items['qty']);
                        }
                    }else{
                        if(!isset($this->session->data['added_bundle'])){
                            $this->session->data['added_bundle'] =  array();
                        }
                        $this->session->data['added_bundle'][$deal_product_id]=array();
                        $this->session->data['added_bundle'][$deal_product_id][$in_items['key']]=array($in_items['bundle_id']=>(int)$in_items['qty']);
                    }
                }
            }
            
            
            if (!$json) {
                if(isset($this->request->post['key']) && isset($this->request->post['changeitem'])){
                    $quantity = $this->session->data['cart'][$this->request->post['key']];
                    $this->cart->remove($this->request->post['key']);
                }
                $json['iky'] = $this->cart->add($this->request->post['product_id'], $quantity, $option);
              
                $json['success'] = sprintf($this->language->get('text_success'),  $product_info['name']);
                $json['itemcount']=$this->cart->countProducts();
                $json['totalamt']=$this->cart->countProducts();
                $json['prname']=$product_info['name'];
                $json['product_id']=$this->request->post['product_id'];
               $this->session->data['prodadded'] = $this->request->post['product_id'];
                //Check For Related Products
                $product_related = $this->model_catalog_product->getProductRelated($this->request->post['product_id']);
                if (!empty($product_related)) {
                    $this->session->data['related_prodt'] = $product_related;
                }
                $product_side = $this->model_catalog_product->getProductSideItems($this->request->post['product_id']);
                if (!empty($product_side)) {
                    if (isset($product_side['auto']))
                        foreach ($product_side['auto'] as $p_id) {
                            $this->cart->add($p_id, 1, array());
                        }
                    if (isset($product_side['popup'])) {
                        $this->session->data['related_prodt'] = $product_side['popup'];
                    }
                }
               // unset($this->session->data['related_prodt']);
                //Check For Related Products
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);

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
                $json['totalamt']=$this->currency->format($total);

                $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
                
                //FOR GOOFLE ANALYTICS
                //$lkey
                $product_data = $this->cart->getcartProducts();
                $json['item_id']=$this->request->post['product_id'];
                $json['item_name']=$product_info['name'];
                $json['item_price']=$product_data[$json['iky']]['total'];
                $json['store_name']=$this->config->get('config_name'); 
                $json['item_category']=$this->model_catalog_product->getCatname($this->request->post['product_id']);;
        }
            else {
                $json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']));
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function quote() {
        $this->language->load('checkout/cart');

        $json = array();

        if (!$this->cart->hasProducts()) {
            $json['error']['warning'] = $this->language->get('error_product');
        }

        if (!$this->cart->hasShipping()) {
            $json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
        }

        if ($this->request->post['country_id'] == '') {
            $json['error']['country'] = $this->language->get('error_country');
        }

        if ($this->request->post['zone_id'] == '') {
            $json['error']['zone'] = $this->language->get('error_zone');
        }

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

        if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
            $json['error']['postcode'] = $this->language->get('error_postcode');
        }

        if (!$json) {
            $this->tax->setShippingAddress($this->request->post['country_id'], $this->request->post['zone_id']);

            // Default Shipping Address
            $this->session->data['shipping_country_id'] = $this->request->post['country_id'];
            $this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
            $this->session->data['shipping_postcode'] = $this->request->post['postcode'];

            if ($country_info) {
                $country = $country_info['name'];
                $iso_code_2 = $country_info['iso_code_2'];
                $iso_code_3 = $country_info['iso_code_3'];
                $address_format = $country_info['address_format'];
            } else {
                $country = '';
                $iso_code_2 = '';
                $iso_code_3 = '';
                $address_format = '';
            }

            $this->load->model('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

            if ($zone_info) {
                $zone = $zone_info['name'];
                $zone_code = $zone_info['code'];
            } else {
                $zone = '';
                $zone_code = '';
            }

            $address_data = array(
                'firstname' => '',
                'lastname' => '',
                'company' => '',
                'address_1' => '',
                'address_2' => '',
                'postcode' => $this->request->post['postcode'],
                'city' => '',
                'zone_id' => $this->request->post['zone_id'],
                'zone' => $zone,
                'zone_code' => $zone_code,
                'country_id' => $this->request->post['country_id'],
                'country' => $country,
                'iso_code_2' => $iso_code_2,
                'iso_code_3' => $iso_code_3,
                'address_format' => $address_format
            );

            $quote_data = array();

            $this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('shipping');

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('shipping/' . $result['code']);

                    $quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data);

                    if ($quote) {
                        $quote_data[$result['code']] = array(
                            'title' => $quote['title'],
                            'quote' => $quote['quote'],
                            'sort_order' => $quote['sort_order'],
                            'error' => $quote['error']
                        );
                    }
                }
            }

            $sort_order = array();

            foreach ($quote_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $quote_data);

            $this->session->data['shipping_methods'] = $quote_data;

            if ($this->session->data['shipping_methods']) {
                $json['shipping_method'] = $this->session->data['shipping_methods'];
            } else {
                $json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function country() {
        $json = array();

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

        if ($country_info) {
            $this->load->model('localisation/zone');

            $json = array(
                'country_id' => $country_info['country_id'],
                'name' => $country_info['name'],
                'iso_code_2' => $country_info['iso_code_2'],
                'iso_code_3' => $country_info['iso_code_3'],
                'address_format' => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone' => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
                'status' => $country_info['status']
            );
        }

        $this->response->setOutput(json_encode($json));
    }

    public function cartetmmobile() {
        $this->language->load('checkout/cart');

        if (!isset($this->session->data['vouchers'])) {
            $this->session->data['vouchers'] = array();
        }

        // Update
        if (!empty($this->request->post['quantity'])) {
            foreach ($this->request->post['quantity'] as $key => $value) {
                $this->cart->update($key, $value);
            }

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['reward']);

            $this->redirect($this->url->link('checkout/cart/cartetmmobile'));
        }

        // Remove
        if (isset($this->request->get['remove'])) {
            $this->cart->remove($this->request->get['remove']);

            unset($this->session->data['vouchers'][$this->request->get['remove']]);

            $this->session->data['success'] = $this->language->get('text_remove');

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['reward']);

            $this->redirect($this->url->link('checkout/cart/cartetmmobile'));
        }

        // Coupon    
        // Shipping


        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart/cartetmmobile'),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
            $points = $this->customer->getRewardPoints();

            $points_total = 0;
            // new reward points
            $this->load->model('setting/setting');
            $rewards = $this->model_setting_setting->getSetting('rewardpoints');
            if (is_array($rewards) && isset($rewards[1]['points']) && $rewards[1]['status'] == 1) {
                $points_total = floor($this->cart->getTotal() / floatval($rewards[1]['points']));
            }


            /* foreach ($this->cart->getProducts() as $product) {
              if ($product['points']) {
              $points_total += $product['points'];
              }
              } */

            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->data['text_next'] = $this->language->get('text_next');
            $this->data['text_next_choice'] = $this->language->get('text_next_choice');
            $this->data['text_use_coupon'] = $this->language->get('text_use_coupon');
            $this->data['text_use_voucher'] = $this->language->get('text_use_voucher');
            $this->data['text_use_reward'] = sprintf($this->language->get('text_use_reward'), $points);
            $this->data['text_shipping_estimate'] = $this->language->get('text_shipping_estimate');
            $this->data['text_shipping_detail'] = $this->language->get('text_shipping_detail');
            $this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
            $this->data['text_select'] = $this->language->get('text_select');
            $this->data['text_none'] = $this->language->get('text_none');

            $this->data['column_image'] = $this->language->get('column_image');
            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_model'] = $this->language->get('column_model');
            $this->data['column_quantity'] = $this->language->get('column_quantity');
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_total'] = $this->language->get('column_total');

            $this->data['entry_coupon'] = $this->language->get('entry_coupon');
            $this->data['entry_voucher'] = $this->language->get('entry_voucher');
            $this->data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);
            $this->data['entry_country'] = $this->language->get('entry_country');
            $this->data['entry_zone'] = $this->language->get('entry_zone');
            $this->data['entry_postcode'] = $this->language->get('entry_postcode');

            $this->data['button_update'] = $this->language->get('button_update');
            $this->data['button_remove'] = $this->language->get('button_remove');
            $this->data['button_coupon'] = $this->language->get('button_coupon');
            $this->data['button_voucher'] = $this->language->get('button_voucher');
            $this->data['button_reward'] = $this->language->get('button_reward');
            $this->data['button_quote'] = $this->language->get('button_quote');
            $this->data['button_shipping'] = $this->language->get('button_shipping');
            $this->data['button_shopping'] = $this->language->get('button_shopping');
            $this->data['button_checkout'] = $this->language->get('button_checkout');

            if (isset($this->error['warning'])) {
                $this->data['error_warning'] = $this->error['warning'];
            } elseif (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
                $this->data['error_warning'] = $this->language->get('error_stock');
            } else {
                $this->data['error_warning'] = '';
            }

            if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
                $this->data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
            } else {
                $this->data['attention'] = '';
            }

            if (isset($this->session->data['success'])) {
                $this->data['success'] = $this->session->data['success'];

                unset($this->session->data['success']);
            } else {
                $this->data['success'] = '';
            }

            $this->data['action'] = $this->url->link('checkout/cart/cartetmmobile');

            if ($this->config->get('config_cart_weight')) {
                $this->data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
            } else {
                $this->data['weight'] = '';
            }

            $this->load->model('tool/image');

            $this->data['products'] = array();

            $products = $this->cart->getProducts();

            foreach ($products as $product) {
                $product_total = 0;

                foreach ($products as $product_2) {
                    if ($product_2['product_id'] == $product['product_id']) {
                        $product_total += $product_2['quantity'];
                    }
                }

                if ($product['minimum'] > $product_total) {
                    $this->data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
                }

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
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
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
                $productbundle[] = array(
                    'product_id' => $bundled_product['product_id'],
                    //'thumb' => $image,
                    'name' => $bundled_product['name'],
                    'model' => $bundled_product['model'],
                    'option' => $bundle_options,
                    'quantity' => $bundled_product['quantity'],
                    'price' => $price,
                    'total' => $total,
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
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
                    $total = $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $total = false;
                }

                $this->data['products'][] = array(
                    'key' => $product['key'],
                    'thumb' => $image,
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'quantity' => $product['quantity'],
                    'stock' => $product['stock'],
                    'reward' => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
                    'price' => $price,
                    'total' => $total,
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    'remove' => $this->url->link('checkout/cart/cartetmmobile', 'remove=' . $product['key']),
                    'bundle'=>$productbundle
                );
            }

            // Gift Voucher
            $this->data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $key => $voucher) {
                    $this->data['vouchers'][] = array(
                        'key' => $key,
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount']),
                        'remove' => $this->url->link('checkout/cart', 'remove=' . $key)
                    );
                }
            }

            if (isset($this->request->post['next'])) {
                $this->data['next'] = $this->request->post['next'];
            } else {
                $this->data['next'] = '';
            }

            $this->data['coupon_status'] = $this->config->get('coupon_status');

            if (isset($this->request->post['coupon'])) {
                $this->data['coupon'] = $this->request->post['coupon'];
            } elseif (isset($this->session->data['coupon'])) {
                $this->data['coupon'] = $this->session->data['coupon'];
            } else {
                $this->data['coupon'] = '';
            }

            $this->data['voucher_status'] = $this->config->get('voucher_status');

            if (isset($this->request->post['voucher'])) {
                $this->data['voucher'] = $this->request->post['voucher'];
            } elseif (isset($this->session->data['voucher'])) {
                $this->data['voucher'] = $this->session->data['voucher'];
            } else {
                $this->data['voucher'] = '';
            }

            $this->data['reward_status'] = ($points && $points_total && $this->config->get('reward_status'));

            if (isset($this->request->post['reward'])) {
                $this->data['reward'] = $this->request->post['reward'];
            } elseif (isset($this->session->data['reward'])) {
                $this->data['reward'] = $this->session->data['reward'];
            } else {
                $this->data['reward'] = '';
            }

            $this->data['shipping_status'] = $this->config->get('shipping_status') && $this->config->get('shipping_estimator') && $this->cart->hasShipping();

            if (isset($this->request->post['country_id'])) {
                $this->data['country_id'] = $this->request->post['country_id'];
            } elseif (isset($this->session->data['shipping_country_id'])) {
                $this->data['country_id'] = $this->session->data['shipping_country_id'];
            } else {
                $this->data['country_id'] = $this->config->get('config_country_id');
            }

            $this->load->model('localisation/country');

            $this->data['countries'] = $this->model_localisation_country->getCountries();

            if (isset($this->request->post['zone_id'])) {
                $this->data['zone_id'] = $this->request->post['zone_id'];
            } elseif (isset($this->session->data['shipping_zone_id'])) {
                $this->data['zone_id'] = $this->session->data['shipping_zone_id'];
            } else {
                $this->data['zone_id'] = '';
            }

            if (isset($this->request->post['postcode'])) {
                $this->data['postcode'] = $this->request->post['postcode'];
            } elseif (isset($this->session->data['shipping_postcode'])) {
                $this->data['postcode'] = $this->session->data['shipping_postcode'];
            } else {
                $this->data['postcode'] = '';
            }

            if (isset($this->request->post['shipping_method'])) {
                $this->data['shipping_method'] = $this->request->post['shipping_method'];
            } elseif (isset($this->session->data['shipping_method'])) {
                $this->data['shipping_method'] = $this->session->data['shipping_method']['code'];
            } else {
                $this->data['shipping_method'] = '';
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

            $this->data['continue'] = $this->url->link('product/category&path=1');

//Offers
            // Group Order
            if (isset($this->session->data['grouporderclient'])) {
                $this->data['checkout'] = $this->url->link('account/grouporder/clinetchekout', '', 'SSL');
            } else {
                $this->data['checkout'] = $this->url->link('checkout/checkoutetm', '', 'SSL');
            }

            //$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/cartetmmobile.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/checkout/cartetmmobile.tpl';
            } else {
                $this->template = 'default/template/checkout/cart.tpl';
            }

            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_bottom',
                'common/content_top',
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render());
        } else {
            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->data['text_error'] = $this->language->get('text_empty');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->url->link('product/category&path=1');

            unset($this->session->data['success']);

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
            } else {
                $this->template = 'default/template/error/not_found.tpl';
            }

            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render());
        }
    }
    
    public function removebundle() {
        //print_r($this->request->post);
        //print_r($this->session->data['cart']);
        //print_r($this->session->data['added_bundle']);
        foreach($this->session->data['added_bundle'][$this->request->post['main_product_id']] as $bkey=>$added_bundle){
            foreach ($added_bundle as $bid=>$qty){
                if(isset($this->session->data['cart'][$bkey])){
                     if(($this->session->data['cart'][$bkey]-(int)$qty)>0){
                        $this->session->data['cart'][$bkey]=$this->session->data['cart'][$bkey]-(int)$qty;
                   }else{
                        $this->cart->remove($bkey);
                   }
                }               
            }
        }
        unset($this->session->data['added_bundle'][$this->request->post['main_product_id']]);
        $json=array('success'=>true);
        $this->response->setOutput(json_encode($json));
    }
}

?>