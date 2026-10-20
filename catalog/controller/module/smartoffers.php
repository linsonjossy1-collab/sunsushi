<?php

class ControllerModuleSmartoffers extends Controller {

    public function index() {
        $this->getdealdetails();
        //print_r($this->data);
        if ($this->config->get('config_cat_prodpage_set')==0) {
            $this->data['smart_deals'] = array();
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/smartdeals.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/smartdeals.tpl';
        } else {
            $this->template = 'default/template/module/smartdeals.tpl';
        }
        $this->response->setOutput($this->render());
        /*
        die();
        $this->load->model('catalog/product');
        $all_deal_products = $this->model_catalog_product->getAllDealProducts();
        $match_array = array();
        $success_products = array();
        foreach ($all_deal_products as $main_product_id => $deal_products) {
            $cart_products = array();
            foreach ($this->cart->getProducts() as $value) {
                $bundled_chk = $this->model_catalog_product->checkbundledproduct($value['product_id']);

                if (empty($bundled_chk)) {
                    for ($i = 1; $i <= $value['quantity']; $i++) {
                        $cart_products[] = array(
                            'key' => $value['key'],
                            'product_id' => $value['product_id'],
                            'option' => $value['option']
                        );
                    }
                }
            }
            $succ_prodt = array();
            $success_status = false;
            foreach ($deal_products as $bundle_id => $deal_product) {
                $bundled_qty = count($deal_product);
                if (isset($match_array[$main_product_id]['count'])) {
                    $match_array[$main_product_id]['count'] += $bundled_qty;
                } else {
                    $match_array[$main_product_id]['count'] = $bundled_qty;
                    $match_array[$main_product_id]['match'] = 0;
                }
                foreach ($deal_product as $ind => $dealproductid) {
                    foreach ($cart_products as $key => $cart_product) {
                        $option_status = true;
                        $cart_product_option = array();
                        if (in_array($cart_product['product_id'], $dealproductid)) {
                            $bundled_options = $this->model_catalog_product->getBundledProductOptions($bundle_id, $cart_product['product_id']);
                            foreach ($cart_product['option'] as $option) {
                                if (in_array($option['product_option_value_id'], $bundled_options)) {
                                    if ($option['type'] == 'checkbox') {
                                        $cart_product_option[$option['product_option_id']][] = $option['product_option_value_id'];
                                    } else {
                                        $cart_product_option[$option['product_option_id']] = $option['product_option_value_id'];
                                    }
                                    // $cart_product_option[$option['product_option_id']]=$option['product_option_value_id'];  
                                } else {
                                    $option_status = false;
                                }
                            }
                            if ($option_status) {
                                $succ_prodt[] = array('key' => $cart_product['key'], 'product_id' => $cart_product['product_id'], 'options' => $cart_product_option);
                                //$succ_prodt[]=$cart_product['product_id'];
                                $match_array[$main_product_id]['match'] ++;
                                $success_status = true;
                                unset($all_deal_products[$main_product_id][$bundle_id][$ind]);
                                unset($cart_products[$key]);
                            }
                            // print_r($bundled_options);
                            break;
                        }
                    }
                }
                if ($success_status) {
                    $success_products[$main_product_id][$bundle_id] = array(
                        'qty' => $bundled_qty,
                        'products' => $succ_prodt
                    );
                    //$success_products[$main_product_id][$bundle_id] = $succ_prodt;
                    $succ_prodt = array();
                    $success_status = false;
                }
            }
        }
        //print_r($success_products);
        //print_r($match_array);
        //die();
        $this->data['smart_deals'] = array();
        $full_success = array();
        $suggested_items = array();
        if (!empty($success_products)) {
            foreach ($match_array as $mainproductid => $match_value) {
                if (isset($this->session->data['discrded_deals']) and in_array($mainproductid, $this->session->data['discrded_deals'])) {
                    continue;
                }
                $option['bundled'] = array();

                $bundle_items = array();

                if ((($match_value['count'] == $match_value['match']) or (($match_value['count'] - $match_value['match'] == 1) and ($match_value['match'] / $match_value['count'] > 0.5)))) {//match suggestions
                    $bundledids = array();
                    $nedded_status = false;
                    $in_cart_items = array();
                    $total_price = 0;
                    $extra_option_price = 0;

                    foreach ($success_products[$mainproductid] as $bundled_id => $bundled_products) {
                        $bundledids[] = $bundled_id;
                        if ($bundled_products['qty'] != count($bundled_products['products'])) {
                            $nedded_status = true;
                            $suggested_items = $this->model_catalog_product->getSingleBundleItems($bundled_id);
                            $sugested_bundle_id = $bundled_id;
                        }
                        foreach ($bundled_products['products'] as $bun_sub_prodt) {
                            $product_det = $this->model_catalog_product->getProduct($bun_sub_prodt['product_id']);
                            $price = $product_det['price'];
                            //$sub_options=array();
                            foreach ($this->model_catalog_product->getProductOptions($bun_sub_prodt['product_id']) as $option) {
                                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                                    $option_value_data = array();
                                    //print_r($bun_sub_prodt['options']);
                                    foreach ($option['option_value'] as $option_value) {
                                        foreach ($bun_sub_prodt['options'] as $bun_sub_option) {
                                            if (is_array($bun_sub_option)) {
                                                if (in_array($option_value['product_option_value_id'], $bun_sub_option)) {
                                                    //if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                                    $extra_option_price +=(float) $option_value['price'];
                                                    $price +=(float) $option_value['price'];
                                                    $option_value_data[] = array(
                                                        'product_option_value_id' => $option_value['product_option_value_id'],
                                                        'option_value_id' => $option_value['option_value_id'],
                                                        'name' => $option_value['name'],
                                                        // 'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                                                        'price' => (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], $product_det['tax_class_id'], $this->config->get('config_tax'))) : false,
                                                        'price_prefix' => ''
                                                    );
                                                    //}
                                                }
                                            } else {
                                                if (($option_value['product_option_value_id'] == $bun_sub_option)) {
                                                    //if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                                    $price +=(float) $option_value['price'];
                                                    $extra_option_price +=(float) $option_value['price'];
                                                    $option_value_data[] = array(
                                                        'product_option_value_id' => $option_value['product_option_value_id'],
                                                        'option_value_id' => $option_value['option_value_id'],
                                                        'name' => $option_value['name'],
                                                        // 'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                                                        'price' => (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], $product_det['tax_class_id'], $this->config->get('config_tax'))) : false,
                                                        'price_prefix' => ''
                                                    );
                                                    //}
                                                }
                                            }
                                        }
                                    }
                                    //$option_value['price_prefix']
                                    $product_det['options'][] = array(
                                        'product_option_id' => $option['product_option_id'],
                                        'product_id' => $bun_sub_prodt['product_id'],
                                        'option_id' => $option['option_id'],
                                        'name' => $option['name'],
                                        'type' => $option['type'],
                                        'option_value' => $option_value_data,
                                        'required' => $option['required']
                                    );
                                } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                                    if (in_array($option['option_value'], $bun_sub_prodt['options'])) {
                                        $price +=(float) $option_value['price'];
                                        $extra_option_price +=(float) $option_value['price'];
                                        $product_det['options'][] = array(
                                            'product_option_id' => $option['product_option_id'],
                                            'product_id' => $bun_sub_prodt['product_id'],
                                            'option_id' => $option['option_id'],
                                            'name' => $option['name'],
                                            'type' => $option['type'],
                                            'option_value' => $option['option_value'],
                                            'required' => $option['required']
                                        );
                                    }
                                }
                            }
                            // $product_det['total_price']=$this->currency->format($this->tax->calculate($price, $product_det['tax_class_id'], $this->config->get('config_tax')));
                            $total_price +=$price; //$this->currency->format($this->tax->calculate($price, $product_det['tax_class_id'], $this->config->get('config_tax')));
                            $in_cart_items[] = $product_det;
                        }
                    }

                    //print_r($in_cart_items);die();
                    if (!$nedded_status) {
                        $items = $this->model_catalog_product->getProductBundledItems($mainproductid);
                        foreach ($items as $item) {
                            if (!in_array($item['bundle_id'], $bundledids)) {
                                $suggested_items = $this->model_catalog_product->getSingleBundleItems($item['bundle_id']);
                                $sugested_bundle_id = $item['bundle_id'];
                            }
                        }
                    }

                    $deals = $this->model_catalog_product->getProduct($mainproductid);
                    $deals['price'] = $deals['price'] + $extra_option_price;
                    //$deals['price']= $this->currency->format($this->tax->calculate($deals['price']+$extra_option_price, $deals['tax_class_id'], $this->config->get('config_tax')));
                    $this->data['deals'][$mainproductid] = $deals;
                    $this->data['smart_deals'][$mainproductid] = array(
                        'available' => $in_cart_items,
                        'suggestion' => $suggested_items,
                        'suggested_bundle_id' => (isset($sugested_bundle_id) ? $sugested_bundle_id : 0),
                        'total_cart_price' => $total_price//$this->currency->format($this->tax->calculate($total_price, $this->data['deals'][$mainproductid]['tax_class_id'], $this->config->get('config_tax')))
                    );
                }
            }
        }
        if ($this->config->get('config_cat_prodpage_set')==0) {
            $this->data['smart_deals'] = array();
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/smartdeals.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/smartdeals.tpl';
        } else {
            $this->template = 'default/template/module/smartdeals.tpl';
        }
        $this->response->setOutput($this->render());
        */
    }

    public function applysmartoffer() {
        $this->load->model('catalog/product');
        $all_deal_products = $this->model_catalog_product->getAllDealProducts();
        $match_array = array();
        foreach ($all_deal_products as $main_product_id => $deal_products) {
            $cart_products = array();
            foreach ($this->cart->getProducts() as $value) {
                $bundled_chk = $this->model_catalog_product->checkbundledproduct($value['product_id']);
                if (empty($bundled_chk)) {
                    for ($i = 1; $i <= $value['quantity']; $i++) {
                        $cart_products[] = array(
                            'key' => $value['key'],
                            'product_id' => $value['product_id'],
                            'option' => $value['option']
                        );
                    }
                }
            }
            $succ_prodt = array();
            $success_status = false;
            foreach ($deal_products as $bundle_id => $deal_product) {
                $bundled_qty = count($deal_product);
                if (isset($match_array[$main_product_id]['count'])) {
                    $match_array[$main_product_id]['count'] += $bundled_qty;
                } else {
                    $match_array[$main_product_id]['count'] = $bundled_qty;
                    $match_array[$main_product_id]['match'] = 0;
                }
                foreach ($deal_product as $ind => $dealproductid) {
                    foreach ($cart_products as $key => $cart_product) {
                        $option_status = true;
                        $cart_product_option = array();
                        if (in_array($cart_product['product_id'], $dealproductid)) {
                            $bundled_options = $this->model_catalog_product->getBundledProductOptions($bundle_id, $cart_product['product_id']);
                            foreach ($cart_product['option'] as $option) {
                                if (in_array($option['product_option_value_id'], $bundled_options)) {
                                    if ($option['type'] == 'checkbox') {
                                        $cart_product_option[$option['product_option_id']][] = $option['product_option_value_id'];
                                    } else {
                                        $cart_product_option[$option['product_option_id']] = $option['product_option_value_id'];
                                    }
                                    // $cart_product_option[$option['product_option_id']]=$option['product_option_value_id'];  
                                } else {
                                    $option_status = false;
                                }
                            }
                            if ($option_status) {
                                $succ_prodt[] = array('key' => $cart_product['key'], 'product_id' => $cart_product['product_id'], 'options' => $cart_product_option);
                                //$succ_prodt[]=$cart_product['product_id'];
                                $match_array[$main_product_id]['match'] ++;
                                $success_status = true;
                                unset($all_deal_products[$main_product_id][$bundle_id][$ind]);
                                unset($cart_products[$key]);
                            }
                            // print_r($bundled_options);
                            break;
                        }
                    }
                }
                if ($success_status) {
                    $success_products[$main_product_id][$bundle_id] = array(
                        'qty' => $bundled_qty,
                        'products' => $succ_prodt
                    );
                    //$success_products[$main_product_id][$bundle_id] = $succ_prodt;
                    $succ_prodt = array();
                    $success_status = false;
                }
            }
        }
        $this->data['smart_deals'] = array();
        $full_success = array();
        $suggested_items = array();
        if (!empty($success_products)) {
            foreach ($match_array as $mainproductid => $match_value) {
                if (isset($this->session->data['discrded_deals']) and in_array($mainproductid, $this->session->data['discrded_deals'])) {
                    continue;
                }
                $option['bundled'] = array();

                $bundle_items = array();
                $already_added = false;
                if ($match_value['count'] == $match_value['match']) {
                    foreach ($success_products[$mainproductid] as $bundled_id => $bundled_products) {
                        foreach ($bundled_products['products'] as $bundled_product) {
                            $bundle_items[] = array(
                                'bundled_id' => $bundled_id,
                                'product_id' => $bundled_product['product_id'],
                                'options' => $bundled_product['options'],
                            );
                            $removed_items[] = $bundled_product['key'];
                            if (isset($this->session->data['smartdeal_suggestedid']) and in_array($mainproductid, $this->session->data['smartdeal_suggestedid'])) {
                                $this->cart->update($bundled_product['key'], ($this->session->data['cart'][$bundled_product['key']] - 1));
                                unset($this->session->data['vouchers'][$bundled_product['key']]);
                            }
                        }
                    }
                    $option['bundled'] = $bundle_items;
                    if (isset($this->session->data['smartdeal_suggestedid']) and  in_array($mainproductid, $this->session->data['smartdeal_suggestedid'])) {
                        $this->cart->add($mainproductid, 1, $option);
                        $already_added = true;
                        $added_key = array_search($mainproductid, $this->session->data['smartdeal_suggestedid']);
                        unset($this->session->data['smartdeal_suggestedid'][$added_key]);
                    }
                    $full_success[] = array(
                        'product_id' => $mainproductid,
                        'option' => $option,
                        'removed_keys' => $removed_items
                    );
                }
            }
        }
    }

    public function dsiscarddeal() {
        $deal_id = $this->request->post['product_id'];
        $this->session->data['discrded_deals'][] = $deal_id;
        //print_r($this->session->data['discrded_deals']);
        $this->response->setOutput(json_encode(array('success' => 'ok')));
    }

    public function adddealproduct() {
        $deal_id = $this->request->post['product_id'];
        $this->session->data['smartdeal_suggestedid'][] = $deal_id;
        //print_r($this->session->data['discrded_deals']);
        $this->response->setOutput(json_encode(array('success' => 'ok')));
    }
    
    public function getsmartoffers(){
        
        $this->getdealdetails();
        
        if ($this->config->get('config_cat_prodpage_set')==0 || !isset($this->data['smart_deals'])) {
            $this->data['deal_items'] = array();
        }
        else {
            $this->data['deal_items'] =  array('smart_deals'=>$this->data['smart_deals'],'deals'=>$this->data['deals']);
        }
  
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/dealmobile.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/dealmobile.tpl';
        } else {
            $this->template = 'default/template/module/dealmobile.tpl';
        }
        $this->response->setOutput($this->render());
        
       
    }
    
    public function getdealdetails() {
        $this->load->model('catalog/product');
        $all_deal_products = $this->model_catalog_product->getAllDealProducts();
        $match_array = array();
        $success_products = array();
        foreach ($all_deal_products as $main_product_id => $deal_products) {
            $cart_products = array();
            foreach ($this->cart->getProducts() as $value) {
                $bundled_chk = $this->model_catalog_product->checkbundledproduct($value['product_id']);

                if (empty($bundled_chk)) {
                    for ($i = 1; $i <= $value['quantity']; $i++) {
                        $cart_products[] = array(
                            'key' => $value['key'],
                            'product_id' => $value['product_id'],
                            'option' => $value['option']
                        );
                    }
                }
            }
            $succ_prodt = array();
            $success_status = false;
            foreach ($deal_products as $bundle_id => $deal_product) {
                $bundled_qty = count($deal_product);
                if (isset($match_array[$main_product_id]['count'])) {
                    $match_array[$main_product_id]['count'] += $bundled_qty;
                } else {
                    $match_array[$main_product_id]['count'] = $bundled_qty;
                    $match_array[$main_product_id]['match'] = 0;
                }
                foreach ($deal_product as $ind => $dealproductid) {
                    foreach ($cart_products as $key => $cart_product) {
                        $option_status = true;
                        $cart_product_option = array();
                        if (in_array($cart_product['product_id'], $dealproductid)) {
                            $bundled_options = $this->model_catalog_product->getBundledProductOptions($bundle_id, $cart_product['product_id']);
                            foreach ($cart_product['option'] as $option) {
                                if (in_array($option['product_option_value_id'], $bundled_options)) {
                                    if ($option['type'] == 'checkbox') {
                                        $cart_product_option[$option['product_option_id']][] = $option['product_option_value_id'];
                                    } else {
                                        $cart_product_option[$option['product_option_id']] = $option['product_option_value_id'];
                                    }
                                    // $cart_product_option[$option['product_option_id']]=$option['product_option_value_id'];  
                                } else {
                                    $option_status = false;
                                }
                            }
                            if ($option_status) {
                                $succ_prodt[] = array('key' => $cart_product['key'], 'product_id' => $cart_product['product_id'], 'options' => $cart_product_option);
                                //$succ_prodt[]=$cart_product['product_id'];
                                $match_array[$main_product_id]['match'] ++;
                                $success_status = true;
                                unset($all_deal_products[$main_product_id][$bundle_id][$ind]);
                                unset($cart_products[$key]);
                            }
                            // print_r($bundled_options);
                            break;
                        }
                    }
                }
                if ($success_status) {
                    $success_products[$main_product_id][$bundle_id] = array(
                        'qty' => $bundled_qty,
                        'products' => $succ_prodt
                    );
                    //$success_products[$main_product_id][$bundle_id] = $succ_prodt;
                    $succ_prodt = array();
                    $success_status = false;
                }
            }
        }
        //print_r($success_products);
        //print_r($match_array);
        //die();
        $this->data['smart_deals'] = array();
        $this->data['deals'] = array();
        $full_success = array();
        $suggested_items = array();
        if (!empty($success_products)) {
            foreach ($match_array as $mainproductid => $match_value) {
                if (isset($this->session->data['discrded_deals']) and in_array($mainproductid, $this->session->data['discrded_deals'])) {
                    continue;
                }
                $option['bundled'] = array();

                $bundle_items = array();

                //if ((($match_value['count'] == $match_value['match']) or (($match_value['count'] - $match_value['match'] == 1) and ($match_value['match'] / $match_value['count'] > 0.5)))) {//match suggestions
                if (($match_value['count'] == $match_value['match'])) {//match suggestions
                    $bundledids = array();
                    $nedded_status = false;
                    $in_cart_items = array();
                    $total_price = 0;
                    $extra_option_price = 0;

                    foreach ($success_products[$mainproductid] as $bundled_id => $bundled_products) {
                        $bundledids[] = $bundled_id;
                        if ($bundled_products['qty'] != count($bundled_products['products'])) {
                            $nedded_status = true;
                            $suggested_items = $this->model_catalog_product->getSingleBundleItems($bundled_id);
                            $sugested_bundle_id = $bundled_id;
                        }
                        foreach ($bundled_products['products'] as $bun_sub_prodt) {
                            $product_det = $this->model_catalog_product->getProduct($bun_sub_prodt['product_id']);
                            $price = $product_det['price'];
                            //$sub_options=array();
                            foreach ($this->model_catalog_product->getProductOptions($bun_sub_prodt['product_id']) as $option) {
                                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                                    $option_value_data = array();
                                    //print_r($bun_sub_prodt['options']);
                                    foreach ($option['option_value'] as $option_value) {
                                        foreach ($bun_sub_prodt['options'] as $bun_sub_option) {
                                            if (is_array($bun_sub_option)) {
                                                if (in_array($option_value['product_option_value_id'], $bun_sub_option)) {
                                                    //if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                                    $extra_option_price +=(float) $option_value['price'];
                                                    $price +=(float) $option_value['price'];
                                                    $option_value_data[] = array(
                                                        'product_option_value_id' => $option_value['product_option_value_id'],
                                                        'option_value_id' => $option_value['option_value_id'],
                                                        'name' => $option_value['name'],
                                                        // 'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                                                        'price' => (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], $product_det['tax_class_id'], $this->config->get('config_tax'))) : false,
                                                        'price_prefix' => ''
                                                    );
                                                    //}
                                                }
                                            } else {
                                                if (($option_value['product_option_value_id'] == $bun_sub_option)) {
                                                    //if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                                    $price +=(float) $option_value['price'];
                                                    $extra_option_price +=(float) $option_value['price'];
                                                    $option_value_data[] = array(
                                                        'product_option_value_id' => $option_value['product_option_value_id'],
                                                        'option_value_id' => $option_value['option_value_id'],
                                                        'name' => $option_value['name'],
                                                        // 'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                                                        'price' => (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], $product_det['tax_class_id'], $this->config->get('config_tax'))) : false,
                                                        'price_prefix' => ''
                                                    );
                                                    //}
                                                }
                                            }
                                        }
                                    }
                                    //$option_value['price_prefix']
                                    $product_det['options'][] = array(
                                        'product_option_id' => $option['product_option_id'],
                                        'product_id' => $bun_sub_prodt['product_id'],
                                        'option_id' => $option['option_id'],
                                        'name' => $option['name'],
                                        'type' => $option['type'],
                                        'option_value' => $option_value_data,
                                        'required' => $option['required']
                                    );
                                } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                                    if (in_array($option['option_value'], $bun_sub_prodt['options'])) {
                                        $price +=(float) $option_value['price'];
                                        $extra_option_price +=(float) $option_value['price'];
                                        $product_det['options'][] = array(
                                            'product_option_id' => $option['product_option_id'],
                                            'product_id' => $bun_sub_prodt['product_id'],
                                            'option_id' => $option['option_id'],
                                            'name' => $option['name'],
                                            'type' => $option['type'],
                                            'option_value' => $option['option_value'],
                                            'required' => $option['required']
                                        );
                                    }
                                }
                            }
                            // $product_det['total_price']=$this->currency->format($this->tax->calculate($price, $product_det['tax_class_id'], $this->config->get('config_tax')));
                            $total_price +=$price; //$this->currency->format($this->tax->calculate($price, $product_det['tax_class_id'], $this->config->get('config_tax')));
                            $in_cart_items[] = $product_det;
                        }
                    }

                    //print_r($in_cart_items);die();
                    if (!$nedded_status) {
                        $items = $this->model_catalog_product->getProductBundledItems($mainproductid);
                        foreach ($items as $item) {
                            if (!in_array($item['bundle_id'], $bundledids)) {
                                $suggested_items = $this->model_catalog_product->getSingleBundleItems($item['bundle_id']);
                                $sugested_bundle_id = $item['bundle_id'];
                            }
                        }
                    }

                    $deals = $this->model_catalog_product->getProduct($mainproductid);
                    $deals['price'] = $deals['price'] + $extra_option_price;
                    //$deals['price']= $this->currency->format($this->tax->calculate($deals['price']+$extra_option_price, $deals['tax_class_id'], $this->config->get('config_tax')));
                    $this->data['deals'][$mainproductid] = $deals;
                    $this->data['smart_deals'][$mainproductid] = array(
                        'available' => $in_cart_items,
                        'suggestion' => $suggested_items,
                        'suggested_bundle_id' => (isset($sugested_bundle_id) ? $sugested_bundle_id : 0),
                        'total_cart_price' => $total_price//$this->currency->format($this->tax->calculate($total_price, $this->data['deals'][$mainproductid]['tax_class_id'], $this->config->get('config_tax')))
                    );
                }
            }
        }
        
    }

}

?>