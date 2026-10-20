<?php

class ControllerModuleStarterspopup extends Controller {

    protected function index() {
        $this->language->load('product/category');
        $this->language->load('product/product');
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        //print_r($this->session->data);
        $side_categories = $this->model_catalog_category->getSidecategories();
        $all_products = array();
        foreach ($this->cart->getProducts() as $key => $cart_product) {
            $key = array_search($this->model_catalog_product->getParentcategoryID($cart_product['product_id']), $side_categories);
            if ($key !== FALSE) {
                unset($side_categories[$key]);
            }
        }
      //  print_r($side_categories);
        if (!empty($side_categories)) {
            foreach ($side_categories as $key=>$category_id) {
              $all_categories = $this->model_catalog_category->getCategoryinfo($category_id);
             // print_r($all_categories);
                $all_products[$key]['category_id']=$all_categories[0]['category_id'];
                $all_products[$key]['name']=$all_categories[0]['name'];
                $all_products[$key]['description']=html_entity_decode($all_categories[0]['description']);
               // foreach ($all_categories as $key => $categories) {
                    //$all_products[$key] = $all_categories;
                     $data = array(
                        'filter_category_id' => $category_id,
                        'sort' => '',
                        'order' => '',
                        'start' => '',
                        'limit' => ''
                    );
                    $results = $this->model_catalog_product->getCatProducts($data);
                    if (empty($results)) {
                        unset($all_products[$key]);
                        continue;
                    }
                    //    $prod_opt = array();
                    foreach ($results as $result) {
                        if ($result['image']) {
                            $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
                        } else {
                            $image = false;
                        }

                        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                            $prod_price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), '', '', FALSE);
                            $org_price = $result['price'];
                        } else {
                            $price = false;
                            $prod_price = false;
                        }

                        if ((float) $result['special']) {
                            $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
                            $org_price = $result['special'];
                        } else {
                            $special = false;
                        }

                        if ($this->config->get('config_tax')) {
                            $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price']);
                        } else {
                            $tax = false;
                        }

                        if ($this->config->get('config_review_status')) {
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
                                            'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
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
                                    'required' => $option['required'],
                                    'default' => $option['default']
                                );
                            } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                                $options[] = array(
                                    'product_option_id' => $option['product_option_id'],
                                    'option_id' => $option['option_id'],
                                    'name' => $option['name'],
                                    'type' => $option['type'],
                                    'option_value' => $option['option_value'],
                                    'required' => $option['required'],
                                    'default' => $option['default']
                                );
                            }
                        }
                        $bundle_chk=$this->model_catalog_product->checkbundledproduct($result['product_id']);
                        $all_products[$key]['products'][] = array(
                            'product_id' => $result['product_id'],
                            'thumb' => $image,
                            'name' => $result['name'],
                            'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                            'price' => $prod_price,
                            'special' => $special,
                            'tax' => $tax,
                            'rating' => $result['rating'],
                            'pricerange' => $pricerange,
                            'product_options' => $options,
                            'reviews' => sprintf($this->language->get('text_reviews'), (int) $result['reviews']),
                            'special_deal'=>(!empty($bundle_chk))?1:0,
                            'vegetarian'=>$result['vegetarian'],
                            'hot'=>$result['hot_count'],
                       );
                        // $prod_opt+=$pr_options;
                    }
                //}      
               
            }
        }
      $this->data['neededcategories'] = $all_products;
       // print_r($all_products);
       
        // }

        //$this->data['starters'] = $all_products;
       // $this->data['extras'] = $all_products_extra;
        //print_r($all_products);die();
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/starterspopup.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/starterspopup.tpl';
        } else {
            $this->template = 'default/template/module/starterspopup.tpl';
        }

        $this->render();
    }

}

?>