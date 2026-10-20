<?php

class ControllerModuleFeatured extends Controller {

    protected function index($setting) {
        $this->language->load('module/featured');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['button_cart'] = $this->language->get('button_cart');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        $this->data['products'] = array();

        $products = explode(',', $this->config->get('featured_product'));

        if (empty($setting['limit'])) {
            $setting['limit'] = 5;
        }


        foreach ($products as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
                if ($product_info['image']) {
                    $image = $this->model_tool_image->resize($product_info['image'], $setting['image_width'], $setting['image_height']);
                } else {
                    $image = false;
                }
                $option_price_status = FALSE;
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    if (!(float) ($product_info['price'])) {
                        $option_price_status = true;
                        //echo $product_info['price'];
                    }
                    $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = false;
                }

                if ((float) $product_info['special']) {
                    $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $special = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = $product_info['rating'];
                } else {
                    $rating = false;
                }
                $product_options = $this->model_catalog_product->getProductOptions($product_id);
                $option_price = 0;
                if (!empty($product_options)) {
                    foreach ($product_options as $option) {
                        if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                            //$option_value_data = array();

                            foreach ($option['option_value'] as $option_value) {
                                if ($option_price_status) {
                                    $option_price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                                  break 2;
                                } else {
                                    $option_price = 0;
                                    break 2;
                                }
                                
                            }
                        }
                    }
                }

                $categories = $this->model_catalog_product->getCategories($product_id);
                $cat = '';

                foreach($categories as $categry) {
                    $cat = $categry['category_id'];
                    break;
                }

               // echo $option_price.'<br/>';
                $this->data['products'][] = array(
                    'product_id' => $product_info['product_id'],
                    'thumb' => $image,
                    'name' => $product_info['name'],
                    'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
                    'price' => $price,
                    'special' => $special,
                    'rating' => $rating,
                    'reviews' => sprintf($this->language->get('text_reviews'), (int) $product_info['reviews']),
                    //'href' => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
                    'option_price' => $option_price,
                    'href' => 'menu/#pd_'.$cat.'_'.$product_info['product_id'],
                    'category' => $cat
                );
            }
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/featured.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/featured.tpl';
        } else {
            $this->template = 'default/template/module/featured.tpl';
        }

        $this->render();
    }

}

?>