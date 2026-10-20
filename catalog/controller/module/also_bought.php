<?php

class ControllerModuleAlsoBought extends Controller {

    public function index() { 

        $this->getrelatedproduct();

        if (isset($this->session->data['shipping_postcode'])) {
            $this->data['postcode'] = $this->session->data['shipping_postcode'];
        } elseif (isset($_SESSION['postcode'])) {
            $this->data['postcode'] = $_SESSION['postcode'];
        } else {
            $this->data['postcode'] = '';
        }
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/also_bought.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/also_bought.tpl';
        } else {
            $this->template = 'default/template/module/also_bought.tpl';
        }


        $this->response->setOutput($this->render());   
    }

    public function getmobilerelatedproduct() { 

        $this->getrelatedproduct();
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/also_boughtmobile.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/also_boughtmobile.tpl';
        } else {
            $this->template = 'default/template/module/also_boughtmobile.tpl';
        }

        $this->render();
    }

    public function getrelatedproduct() {
        
        unset($this->session->data['upsellstatus']);
        $all_products = array();
        $cartproduct = array();

        foreach ($this->cart->getProducts() as $products) {
            $cartproduct[] = $products['product_id'];
        }

        

        $upselling_data = $this->config->get('upselling_data'); // upselling data rules
        if (!is_array($upselling_data)) {
            $upselling_data = array();
        }

        $side_categories = array();
        $this->load->model('catalog/product');

        foreach ($upselling_data as $rule) {
            $match_main = false;
            if(isset($rule['page']) && !empty($rule['page']) && !in_array(1,$rule['page'])){
                continue;
            }
            

            if (!empty($rule['main']) && is_array($rule['main'])) {
                if (array_intersect($cartproduct, $rule['main'])) {
                    $match_main = true;
                }
            } else {
                $match_main = true;
            }
            if (isset($rule['cart_amount']) && $rule['cart_amount'] !== '') {
                $cart_total = $this->cart->getTotal();
                if ((float)$cart_total <= (float)$rule['cart_amount']) {
                    $match_main = true;
                }else{
                      $match_main = false;
                }
            }
            if ($match_main && !empty($rule['related']) && is_array($rule['related'])) {
                foreach ($rule['related'] as $related_id) {
                    if (!in_array($related_id, $cartproduct)) {
                        $product_info = $this->model_catalog_product->getProduct($related_id);
                        if ($product_info) {
                            $side_categories[] = $product_info;
                        }
                    }
                }
            }
        }

        if (!empty($side_categories)) {
            // $this->log->write(print_r($side_categories,true));
            // $side_categorie = array_map("unserialize", array_unique(array_map("serialize", $side_categories)));
            $side_categorie = array_intersect_key($side_categories, array_unique(array_column($side_categories, 'product_id')));
            unset($side_categories);

            foreach ($side_categorie as $key => $result) {            
                if (!in_array($result['product_id'], $cartproduct)) {
                    $image = false;

                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                        $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                        $prod_price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), '', '', FALSE);
                        $org_price = $result['price'];
                    } else {
                        $price = false;
                        $prod_price = false;
                    }

                    if ( isset($result['special']) && (float) $result['special']) {
                        $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
                        $org_price = $result['special'];
                    } else {
                        $special = false;
                    }

                    if (isset($result['special']) && $this->config->get('config_tax')) {
                        $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price']);
                    } else {
                        $tax = false;
                    }

                    if (isset($result['rating']) && $this->config->get('config_review_status')) {
                        $rating = (int) $result['rating'];
                    } else {
                        $rating = false;
                    }
                    $price_range = $this->model_catalog_product->getPricerange($result['product_id']);
                    if ($price_range['MIN( price )'] == $price_range['MAX( price )']) {
                        $pricerange = 'Price:' . $price_range['MIN( price )'];
                    } else {
                        $pricerange = 'Price Range:' . $price_range['MIN( price )'] . '-' . $price_range['MAX( price )'];
                    }
                    $options = array();

                    foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
                        if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                            $option_value_data = array();

                            foreach ($option['option_value'] as $option_value) {
                                if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                    if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price'))) { // && (float) $option_value['price']
                                        $price = $this->currency->format($this->tax->calculate($org_price + (($option_value['price_prefix'] == '+' ? 1 : -1) * $option_value['price']), $result['tax_class_id'], $this->config->get('config_tax')), '', '', FALSE);
                                        $option_price = $this->currency->format($this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax')), '', '', FALSE);
                                    } else {
                                        $price = $option_price = false;
                                    }

                                    $option_value_data[] = array(
                                        'product_option_value_id' => $option_value['product_option_value_id'],
                                        'option_value_id' => $option_value['option_value_id'],
                                        'name' => $option_value['name'],
                                        'image' => false,
                                        //'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                                        'price' => $price,
                                        'option_price' => $option_price,
                                        'price_prefix' => $option_value['price_prefix']
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
                    $bunled_items = $this->model_catalog_product->getProductBundledItems($result['product_id']);
                        
                    $all_products[$key] = array(
                        'product_id' => $result['product_id'],
                        'thumb' => HTTPS_IMAGE.''.$result['image'],
                        'name' => $result['name'],
                        // 'description' =>html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                        'price' => $prod_price,
                        'special' => $special,
                        'tax' => $tax,
                        'rating' => $rating,
                        'pricerange' => $pricerange,
                        'product_options' => $options,
                        'reviews' => isset($result['reviews'])?sprintf($this->language->get('text_reviews'), (int) $result['reviews']):'',
                            // 'href' => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
                    );
                    if(!empty($bunled_items)){
                        $all_products[$key]['bundle']=true;
                    }
                    // $prod_opt+=$pr_options;
                }
            }

        }
        
        if(!empty($all_products)){
            $this->session->data['upsellstatus']=1;
        }
        
        $this->data['relatedproducts'] = $all_products;
    }
   
}