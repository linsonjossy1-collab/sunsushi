<?php

class ControllerCommonfacebookmenu extends Controller {

    public function index() {
        $this->language->load('product/category');

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');
        $all_categories = $this->model_catalog_category->getCategories(1);
        // $i = 0;
                //print_r($all_categories);
                foreach ($all_categories as $key => $categories) {
                    $categories['description'] = html_entity_decode($categories['description']);
                    $all_products[$key] = $categories;
                    $sub_categories = $this->model_catalog_category->getCategories($categories['category_id']);

                    if (!empty($sub_categories)) {


                        foreach ($sub_categories as $p_key => $category) {
                            $sub_2_categories = $this->model_catalog_category->getCategories($category['category_id']);
                            $category['description'] = html_entity_decode($category['description']);
                            $all_products[$key]['sub_cats'][$p_key] = $category;
                            $prodt_least_price = $this->model_catalog_category->getCategoryProductLeastPrice($category['category_id']);
                            $all_products[$key]['sub_cats'][$p_key]['pr_least_price'] = $this->currency->format($this->tax->calculate($prodt_least_price, '', $this->config->get('config_tax')), '', '', FALSE);
                            if (!empty($sub_2_categories)) {
                                foreach ($sub_2_categories as $sub_2_key => $sub_2_category) {
                                    $sub_2_category['description'] = html_entity_decode($sub_2_category['description']);
                                    $all_products[$key]['sub_cats'][$p_key]['sub_2_cat'][$sub_2_key] = $sub_2_category;
                                    $prodt_least_price = $this->model_catalog_category->getCategoryProductLeastPrice($sub_2_category['category_id']);
                                    $all_products[$key]['sub_cats'][$p_key]['sub_2_cat'][$sub_2_key]['pr_least_price'] = $this->currency->format($this->tax->calculate($prodt_least_price, '', $this->config->get('config_tax')), '', '', FALSE);
                                    $data1 = array(
                                        'filter_category_id' => $sub_2_category['category_id'],
                                        'sort' => '',
                                        'order' => '',
                                        'start' => '',
                                        'limit' => ''
                                    );
                                    $results = $this->model_catalog_product->getCatProducts($data1);
//                            if (empty($results)) {
//                                unset($all_products[$key]);
//                                continue;
//                            }
                            //    $prod_opt = array();
                            if (!empty($results)) {
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

                                    $all_products[$key]['sub_cats'][$p_key]['sub_2_cat'][$sub_2_key]['products'][] = array(
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
                                        //'href' => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
                                    );
                                    // $prod_opt+=$pr_options;
                                }
                            }
                                }
                            }  
                            else {
                                
                            
                            
                            $data = array(
                                'filter_category_id' => $category['category_id'],
                                'sort' => '',
                                'order' => '',
                                'start' => '',
                                'limit' => ''
                            );
                            $results = $this->model_catalog_product->getCatProducts($data);
//                            if (empty($results)) {
//                                unset($all_products[$key]);
//                                continue;
//                            }
                            //    $prod_opt = array();
                            if (empty($results)) {
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

                                    $all_products[$key]['sub_cats'][$p_key]['products'][] = array(
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
                                        //'href' => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
                                    );
                                    // $prod_opt+=$pr_options;
                                }
                            }
                            }
                        }
                    }
                   //  else {
                    // $categories['description'] = html_entity_decode($categories['description']);
                    //$all_products[$key] = $categories;
                    //$all_products[$i]['cat_id'] = $categories['category_id'];
                    //$all_products[$i]['cat_name'] = $categories['name'];
                    $data = array(
                        'filter_category_id' => $categories['category_id'],
                        'sort' => '',
                        'order' => '',
                        'start' => '',
                        'limit' => ''
                    );
                    $results = $this->model_catalog_product->getCatProducts($data);
//                    if (empty($results)) {
//                        unset($all_products[$key]);
//                        continue;
//                    }
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
                                   // 'default' => $option['default']
                                );
                            } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                                $options[] = array(
                                    'product_option_id' => $option['product_option_id'],
                                    'option_id' => $option['option_id'],
                                    'name' => $option['name'],
                                    'type' => $option['type'],
                                    'option_value' => $option['option_value'],
                                    'required' => $option['required'],
                                    //'default' => $option['default']
                                );
                            }
                        }

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
                            //'href' => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
                        );
                        // $prod_opt+=$pr_options;
                    }
                    //   }
                }
              //  print_r($all_products);die();
                $this->data['results'] = $all_products;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/facebookmenu.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/facebookmenu.tpl';
        } else {
            $this->template = 'default/template/common/facebookmenu.tpl';
        }
        $this->response->setOutput($this->render());
    }

}

?>