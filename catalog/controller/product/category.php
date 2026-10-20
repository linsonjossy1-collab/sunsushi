<?php

class ControllerProductCategory extends Controller {

    public function index() {
          
        if($_SERVER['REQUEST_URI']=='/menu/' && strpos($_SERVER['HTTP_ACCEPT'],'html') === false){
            die();
        }
         if(in_array($_SERVER['REQUEST_URI'], array('/menu/','/eatinmenu/','/bar/','/tablebooking/','/offers','/offers/'))) { 
            
            if($_SERVER['REQUEST_URI']!='/eatinmenu/'){                
                unset($this->session->data['tableordering']);
                unset($this->session->data['dine_table_id']);
               // unset($this->session->data['delivery_method']);
            }             
            if(!isset($this->session->data['REDIRECT_URL'])) {
                $this->session->data['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
            }
            else {
                if ($this->session->data['REDIRECT_URL'] != $_SERVER['REQUEST_URI']) {
                    $this->session->data['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
                   // $this->cart->clear();                                       
                    $this->log->write('Cart Clared');
                }
            }
        } 
        if(isset($this->request->get['ct'])){
            $this->session->data['delivery_method']=$this->request->get['ct']=='1'?'home.home':'collect.collect';
        }
        $this->language->load('product/category');

        $this->load->model('catalog/category'); 

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.sort_order';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );
       
         if(!$this->postcode->isStoreOpened()){
            $this->data['store_closed']=$this->language->get('text_store_closed');
        }
        //Store Times
            $this->load->model('setting/setting');
            $this->data['store_times']=$this->model_setting_setting->getStoretimes();
        //
        //Available Payments
            $this->data['payments'] = array();
            $this->load->model('setting/extension');
            $payments = $this->model_setting_extension->getExtensions('payment');
            foreach ($payments as $payment) {
                if ($this->config->get($payment['code'] . '_status')) {
                    $this->load->model('payment/' . $payment['code']);
                    if(method_exists($this->{'model_payment_' . $payment['code']}, 'isposonly')){
                        continue;
                    }
                    $this->load->language('payment/'. $payment['code']);
                    $this->data['payments'][] = $this->language->get('text_title'); 
                }
            }
        $this->data['delivery_details']=$this->model_setting_setting->getDdeliverydetails();
        
        if (isset($this->request->get['path'])) {
            $path = '';

            $parts = explode('_', (string) $this->request->get['path']);

            foreach ($parts as $path_id) {
                if (!$path) {
                    $path = $path_id;
                } else {
                    $path .= '_' . $path_id;
                }

                $category_info = $this->model_catalog_category->getCategory($path_id);

                if ($category_info) {
                    $this->data['breadcrumbs'][] = array(
                        'text' => $category_info['name'],
                        'href' => $this->url->link('product/category', 'path=' . $path),
                        'separator' => $this->language->get('text_separator')
                    );
                }
            }

            $category_id = array_pop($parts);
        } else {
            $category_id = 0;
        }

        $category_info = $this->model_catalog_category->getCategory($category_id);

        if ($category_info) {
            $this->document->setTitle($category_info['name']);
            $this->document->setDescription($category_info['meta_description']);
            $this->document->setKeywords($category_info['meta_keyword']);
            if(!isset($this->session->data['shipping_methods']) || empty($this->session->data['shipping_methods']) || !isset($this->session->data['shipping_method'])){
                $this->data['noshippingstatus']=1; 
            }
            // CACHE
            $etmlistdata = $this->cache->get('etmlist'. '.' . $category_info['category_id'] . '.' . (int)$this->config->get('config_store_id'));
            
            if(!$etmlistdata){ 
            $this->data['cached_time'] = time();   
            $this->data['heading_title'] = $category_info['name'];
            $this->data['text_refine'] = $this->language->get('text_refine');
            $this->data['text_empty'] = $this->language->get('text_empty');
            $this->data['text_quantity'] = $this->language->get('text_quantity');
            $this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
            $this->data['text_model'] = $this->language->get('text_model');
            $this->data['text_price'] = $this->language->get('text_price');
            $this->data['text_tax'] = $this->language->get('text_tax');
            $this->data['text_points'] = $this->language->get('text_points');
            $this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
            $this->data['text_display'] = $this->language->get('text_display');
            $this->data['text_list'] = $this->language->get('text_list');
            $this->data['text_grid'] = $this->language->get('text_grid');
            $this->data['text_sort'] = $this->language->get('text_sort');
            $this->data['text_limit'] = $this->language->get('text_limit');
            $this->data['text_select'] = $this->language->get('text_select');
            $this->data['text_option'] = $this->language->get('text_option');
            $this->data['button_cart'] = $this->language->get('button_cart');
            $this->data['button_wishlist'] = $this->language->get('button_wishlist');
            $this->data['button_compare'] = $this->language->get('button_compare');
            $this->data['button_continue'] = $this->language->get('button_continue');

            if ($category_info['image']) {
                $this->data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
            } else {
                $this->data['thumb'] = '';
            }

            $this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
            $this->data['compare'] = $this->url->link('product/compare');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['categories'] = array();

            //Simple List end
            $this->data['category_shippings']=array();
            $all_products = array();
            $tot_prod = array();
            if ($this->config->get('config_cat_prodpage_set')) {
                //if($category_info['name']='Menu'){
                if ($category_info['parent_id'] == 0) {
                    $all_categories = $this->model_catalog_category->getCategories($category_info['category_id']);
                } else {
                    //    echo "1";  
                    $all_categories = $this->model_catalog_category->getCategoryinfo($category_info['category_id']);
                }
                $i = 0;
                //print_r($all_categories);
                //New Layouts
                foreach ($all_categories as $key => $categories) {

                   // $this->data['category_layouts'][$categories['category_id']] = $this->getCategoryLayout($categories['category_id']);
                   $etmmailsublayout =  $this->getCategoryLayout($categories['category_id']);
                   $this->cache->set('etmlist.layout' . '.'.$categories['category_id'].'.' . (int) $this->config->get('config_store_id'), $etmmailsublayout);
                   $this->data['category_layouts'][$categories['category_id']] = $categories['category_id'];
                   $this->data['category_shippings'][$categories['category_id']]=unserialize($categories['shipping_methodes']);
                }
            }
            // print_r($this->data['category_layouts'][12]);
            //print_r($all_products);
            //Simple list end
            $results = $this->model_catalog_category->getCategories($category_id);

            foreach ($results as $result) {
                $data = array(
                    'filter_category_id' => $result['category_id'],
                    'filter_sub_category' => true
                );

                $product_total = $this->model_catalog_product->getTotalProducts($data);

                $this->data['categories'][] = array(
                    'name' => $result['name'] . ' (' . $product_total . ')',
                    'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
                );
            }

            $this->data['products'] = array();
            $this->data['products_options'] = array();
            $this->data['options'] = array();
            $data = array(
                'filter_category_id' => $category_id,
                'sort' => $sort,
                'order' => $order,
                'start' => ($page - 1) * $limit,
                'limit' => $limit
            );

            $product_total = $this->model_catalog_product->getTotalProducts($data);

            $results = $this->model_catalog_product->getProducts($data);

            foreach ($results as $result) {
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
                } else {
                    $image = false;
                }

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = false;
                }

                if ((float) $result['special']) {
                    $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
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

                $this->data['products'][] = array(
                    'product_id' => $result['product_id'],
                    'thumb' => $image,
                    'name' => $result['name'],
                    'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                    'price' => $price,
                    'special' => $special,
                    'tax' => $tax,
                    'rating' => $result['rating'],
                    'pricerange' => $pricerange,
                    //  'product_options'=>$this->model_catalog_product->getProductsoptions($result['product_id']),
                    'reviews' => sprintf($this->language->get('text_reviews'), (int) $result['reviews']),
                    'href' => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
                );
                $product_options = $this->model_catalog_product->getProductsoptions($result['product_id']);
                foreach ($product_options as $options) {
                    $this->data['products_options'][] = array(
                        'product_id' => $result['product_id'],
                        'product_option_id' => $options['product_option_id'],
                        'option_id' => $options['option_id'],
                        'option_value_id' => $options['option_value_id'],
                        'product_option_value_id' => $options['product_option_value_id'],
                        'price' => $options['price'],
                        'name' => $options['name']
                    );
                }

                foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
                    if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                        $option_value_data = array();

                        foreach ($option['option_value'] as $option_value) {
                            if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                $option_value_data[] = array(
                                    'product_option_value_id' => $option_value['product_option_value_id'],
                                    'option_value_id' => $option_value['option_value_id'],
                                    'name' => $option_value['name'],
                                    'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                                    'price' => (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax'))) : false,
                                    'price_prefix' => ''
                                );
                            }
                        }
                        //$option_value['price_prefix']
                        $this->data['options'][] = array(
                            'product_option_id' => $option['product_option_id'],
                            'product_id' => $result['product_id'],
                            'option_id' => $option['option_id'],
                            'name' => $option['name'],
                            'type' => $option['type'],
                            'option_value' => $option_value_data,
                            'required' => $option['required']
                        );
                    } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                        $this->data['options'][] = array(
                            'product_option_id' => $option['product_option_id'],
                            'product_id' => $result['product_id'],
                            'option_id' => $option['option_id'],
                            'name' => $option['name'],
                            'type' => $option['type'],
                            'option_value' => $option['option_value'],
                            'required' => $option['required']
                        );
                    }
                }
            }


            $url = '';

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['sorts'] = array();

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_default'),
                'value' => 'p.sort_order-ASC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_name_asc'),
                'value' => 'pd.name-ASC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_name_desc'),
                'value' => 'pd.name-DESC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_price_asc'),
                'value' => 'p.price-ASC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_price_desc'),
                'value' => 'p.price-DESC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_rating_desc'),
                'value' => 'rating-DESC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_rating_asc'),
                'value' => 'rating-ASC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_model_asc'),
                'value' => 'p.model-ASC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => $this->language->get('text_model_desc'),
                'value' => 'p.model-DESC',
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
            );

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $this->data['limits'] = array();

            $this->data['limits'][] = array(
                'text' => $this->config->get('config_catalog_limit'),
                'value' => $this->config->get('config_catalog_limit'),
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $this->config->get('config_catalog_limit'))
            );

            $this->data['limits'][] = array(
                'text' => 25,
                'value' => 25,
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=25')
            );

            $this->data['limits'][] = array(
                'text' => 50,
                'value' => 50,
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=50')
            );

            $this->data['limits'][] = array(
                'text' => 75,
                'value' => 75,
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=75')
            );

            $this->data['limits'][] = array(
                'text' => 100,
                'value' => 100,
                'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=100')
            );

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $pagination = new Pagination();
            $pagination->total = $product_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

            $this->data['pagination'] = $pagination->render();

            $this->data['sort'] = $sort;
            $this->data['order'] = $order;
            $this->data['limit'] = $limit;
            // CACHE
            $this->cache->set('etmlist' . '.'.$category_info['category_id'].'.' . (int) $this->config->get('config_store_id'), $this->data);
            }
            else{
                unset($etmlistdata['breadcrumbs']);
                unset($etmlistdata['delivery_details']);
                unset($etmlistdata['payments']);
                unset($etmlistdata['store_times']);
                unset($etmlistdata['store_closed']);
                $this->data +=$etmlistdata;    
            }

            if (isset($this->session->data['grouporderclient'])) {
                $this->data['grouporder'] = 1;
            } else {
                $this->data['grouporder'] = 0;
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

            // Postcode search

            $this->data['continue'] = $this->url->link('common/home');
            $this->data['postcodecategory'] = $this->getChild('common/postcode/category');
            
            $this->data['menushipping'] = $this->getChild('checkout/shipping_postcodes/menushipping');
            
            //if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/category.tpl')) {
            //	$this->template = $this->config->get('config_template') . '/template/product/category.tpl';
            //} else {
            //	$this->template = 'default/template/product/category.tpl';
            //}
            //Simple lsit
            if (isset($this->session->data['delivery_method'])) {
                $this->data['delivery_method'] = $this->session->data['delivery_method'];
            } else {
                $this->data['delivery_method'] = '';
            }
            
            if ($this->config->get('config_cat_prodpage_set')) {
                 if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/etmcategorylist/etmlist.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/etmcategorylist/etmlist.tpl';
                } else {
                    $this->template = 'default/template/etmcategorylist/etmlist.tpl';
                }
            } else {
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/category.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/product/category.tpl';
                } else {
                    $this->template = 'default/template/product/category.tpl';
                }
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
        else {
            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('product/category', $url),
                'separator' => $this->language->get('text_separator')
            );

            $this->document->setTitle($this->language->get('text_error'));

            $this->data['heading_title'] = $this->language->get('text_error');

            $this->data['text_error'] = $this->language->get('text_error');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->url->link('common/home');

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
     private function getCategoryLayout($category_id, $filter = array()) {

        $categories = $this->model_catalog_category->getCategoryinfo($category_id);
        $categories = $categories[0];
        //print_r($categories);die();
        $template = new Template();
        $template->data['name'] = $categories['name']; // echo "--";echo $categories['image'];echo "--";
        $template->data['category_id'] = $category_id;
        $template->data['image'] = $this->model_tool_image->resize($categories['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
        $template->data['thumb'] = $this->model_tool_image->resize(($categories['image']!=''?$categories['image']:'data/waiting.png'), $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
        $template->data['cate_shipping_methodes'] =unserialize($categories['shipping_methodes']);
        $template->data['description'] = html_entity_decode($categories['description']);
        $template->data['vegetarian'] = $categories['vegetarian'];
        $template->data['hot_count'] = $categories['hot_count'];
        $catprodt_least_price = $this->model_catalog_category->getCategoryProductLeastPrice($category_id);
        $template->data['prodt_least_price'] =$this->currency->format($this->tax->calculate($catprodt_least_price, $this->config->get('tax_class_id'), $this->config->get('config_tax')),'',true);
        $sub_categories = $this->model_catalog_category->getCategories($category_id);
        if (!empty($sub_categories)) {
            //print_r($sub_categories);
            // $prodt_least_price = $this->model_catalog_category->getCategoryProductLeastPrice($category_id);
            //$sub_categories['least_price']=$prodt_least_price;
            $template->data['categories'] = $sub_categories;
            
            //$template->data['categories']

            foreach ($sub_categories as $category) {
                $prodt_least_price = $this->model_catalog_category->getCategoryProductLeastPrice($category['category_id']);
                $category['least_price'] = $this->currency->format($this->tax->calculate($prodt_least_price, $this->config->get('tax_class_id'), $this->config->get('config_tax')));
                $category['description'] = html_entity_decode($category['description']);
                $category['cate_shipping_methodes'] =unserialize($category['shipping_methodes']);
                $data = array(
                    'filter_category_id' => $category['category_id'],
                    'sort' => (isset($filter['sort']) ? $filter['sort'] : ''),
                    'order' => '',
                    'start' => '',
                    'limit' => ''
                );
                $results = $this->model_catalog_product->getCatProducts($data);
                $i = 0;
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
                    if ($i == 0) {
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
                    }
                    $bundle_chk = $this->model_catalog_product->checkbundledproduct($result['product_id']);
                    $category['products'][] = array(
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
                        'href' => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id']),
                        'vegetarian'=>$result['vegetarian'],
                        'hot_count'=>$result['hot_count'],
                        'special_deal' => (!empty($bundle_chk)) ? 1 : 0
                    );
                    $i++;
                    // $prod_opt+=$pr_options;
                }
                ///////////////////////////////////////////////////////////
                $template->data['subcategories'][] = $category;
                // $template->data['category_layouts'][] = $this->getCategoryLayout($category['category_id'], $filter);
                   $etmmailsublayout =  $this->getCategoryLayout($category['category_id'], $filter);
                   $this->cache->set('etmlist.layout' . '.'.$category['category_id'].'.' . (int) $this->config->get('config_store_id'), $etmmailsublayout);
                   $template->data['category_layouts'][] = $category['category_id'];
                   $template->data['category_shippings'][$category['category_id']] = unserialize($category['shipping_methodes']);
                //echo "dd";
            }
        } else {
            $template->data['categories'] = array();
            $template->data['category_layouts'] = array();
            $template->data['subcategories'] = array();
        }

        $data = array(
            'filter_category_id' => $category_id,
            'sort' => (isset($filter['sort']) ? $filter['sort'] : ''),
            'order' => '',
            'start' => '',
            'limit' => ''
        );
        $template->data['product_layouts'] = array();
       
        $products = $this->model_catalog_product->getCatProducts($data);
       
        foreach ($products as $product) {

            $template->data['product_layouts'][] = $this->getProductLayout($product, $data);
        }
        $this->data['products'] = $products;
        $template->data['products'] = $products; 

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/etmcategorylist/' . $this->getCategoryLayoutname($category_id) . '.tpl')) {
            return $template->fetch($this->config->get('config_template') . '/template/etmcategorylist/' . $this->getCategoryLayoutname($category_id) . '.tpl');
        } else {
            return $template->fetch('default/template/etmcategorylist/category-default.tpl');
        }
    }

    private function getProductLayout($product,$filter=array()) {
        $template = new Template();
        if ($product['image']) {
            $image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
           $image = HTTPS_IMAGE.''.$product['image'];
            $image = 'data:images/jpg;base64,' . base64_encode(file_get_contents(HTTPS_IMAGE.''.$product['image']));
            $image ='data:images/png;base64,' . base64_encode(file_get_contents($this->model_tool_image->resize($product['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'))));
      
        } else {
            $image = false;
        }
         
        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
            $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
            $org_price = $this->currency->format($product['price'],'','',false);
            
        } else {
            $price = false;
            $org_price =false;
        }

        if ((float) $product['special']) {
            $special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
        } else {
            $special = false;
        }

        if ($this->config->get('config_tax')) {
            $tax = $this->currency->format((float) $product['special'] ? $product['special'] : $product['price']);
        } else {
            $tax = false;
        }

        if ($this->config->get('config_review_status')) {
            $rating = (int) $product['rating'];
        } else {
            $rating = false;
        }
        $price_range = $this->model_catalog_product->getPricerange($product['product_id']);
        if ($price_range['MIN( price )'] == $price_range['MAX( price )']) {
            $pricerange = 'Price:' . $price_range['MIN( price )'];
        } else {
            $pricerange = 'Price Range:' . $price_range['MIN( price )'] . '-' . $price_range['MAX( price )'];
        }
        $template->data['parent_category_id']=$filter['filter_category_id'];

        $template->data += array(
            'product_id' => $product['product_id'],
            'thumb' => $image,
            'name' => $product['name'],
            'description' => html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'),
            'price' => $price,
            'org_price'=>$org_price,
            'special' => $special,
            'tax' => $tax,
            'rating' => $product['rating'],
            'pricerange' => $pricerange,
            'reviews' => sprintf($this->language->get('text_reviews'), (int) $product['reviews']),
            'href' => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $product['product_id']),
            'vegetarian'=>$product['vegetarian'],
            'hot'=>$product['hot_count'],
            'curr_symbol'=>$this->currency->getSymbolLeft(),
        );
       
        $template->data['options'] = array();
        $template->data['opton_price'] = 0;
        $template->data['actual_price'] = $product['price'];
        $option_price_status = FALSE;
        foreach ($this->model_catalog_product->getProductOptions($product['product_id']) as $option) {
            if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                $option_value_data = array();

                foreach ($option['option_value'] as $option_value) {
                    if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                        if(!$option_price_status){
                            $template->data['opton_price'] = $this->currency->format($option_value['price'],'','',false);
                            $option_price_status = true;
                        }
                        $option_value_data[] = array(
                            'product_option_value_id' => $option_value['product_option_value_id'],
                            'option_value_id' => $option_value['option_value_id'],
                            'name' => $option_value['name'],
                            'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                            'price' => (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax'))) : false,
                            'org_price'=> $this->currency->format($option_value['price'],'','',false),
                            'price_prefix' => ''
                        );
                    }
                }
                //$option_value['price_prefix']
                $template->data['options'][] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_id' => $product['product_id'],
                    'option_id' => $option['option_id'],
                    'name' => $option['name'],
                    'type' => $option['type'],
                    'option_value' => $option_value_data,
                    'required' => $option['required']
                );
            } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                $template->data['options'][] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_id' => $product['product_id'],
                    'option_id' => $option['option_id'],
                    'name' => $option['name'],
                    'type' => $option['type'],
                    'option_value' => $option['option_value'],
                    'required' => $option['required']
                );
            }
        }
        //return $template->fetch('default/template/etmcategorylist/product-default.tpl');
        $template->data['product_shippings'] = unserialize($product['shipping_methodes']);   
        $template->data['specific_prices'] = unserialize($product['shipping_prices']);
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/etmcategorylist/' . $this->getProductLayoutname($product['product_id']) . '.tpl')) {
            return $template->fetch($this->config->get('config_template') . '/template/etmcategorylist/' . $this->getProductLayoutname($product['product_id']) . '.tpl');
        } else {
            return $template->fetch('default/template/etmcategorylist/product-default.tpl');
        }
        ////return $template->render();
    }

    private function getProductLayoutname($product_id) {
        $product_layout_id = $this->model_catalog_product->getProductLayoutId($product_id);
        if ($this->config->get('productcategory_layouots')) {
            foreach ($this->config->get('productcategory_layouots') as $layout) {
                if ($layout['layout_id'] == $product_layout_id) {
                    return basename($layout['path'], '.tpl');
                }
            }
        }
    }

    private function getCategoryLayoutname($category_id) {
        $category_layout_id = $this->model_catalog_category->getCategoryLayoutId($category_id);
        if ($this->config->get('productcategory_layouots')) {
            foreach ($this->config->get('productcategory_layouots') as $layout) {
                if ($layout['layout_id'] == $category_layout_id) {
                    return basename($layout['path'], '.tpl');
                }
            }
        }
    }

    public function getAlsoBoughtProducts() {

        if(!$this->config->get('also_bought_status')) {
            return '';
        }

        $this->load->model('checkout/also_bought');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');


        $this->data['also_bought_products'] = array();
        $products = $this->cart->getProducts();

        if(!isset($this->request->get['path'])) {
            $this->request->get['path'] = '';
        }
            
        foreach ($this->cart->getProducts() as $product) {
            $product_id[] = $product['product_id'];
        }

        if(isset($product_id)) {
            $results = $this->model_checkout_also_bought->getAlsoBought(implode(',',$product_id));
        } else {
            $results = '';
        }

        if($results) {

            foreach ( $results as $product) {

                $product['description'] ='';
                $product['special'] = $this->model_catalog_product->getProductSpecials($product['product_id']);
                $product['reviews'] = 0;    
                $product['rating'] = false;
    
                $category_id =  $this->model_catalog_product->getCategories($product['product_id']);    
                $data = array(
                    'filter_category_id' => $category_id[0]['category_id'],
                    'sort' => '',
                    'order' => '',
                    'start' => '',
                    'limit' => ''
                );
        
                $this->data['also_bought_products'][] = $this->getProductLayout($product, $data);
            }

            $this->id = 'also_bought';

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/also_bought.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/module/also_bought.tpl';
            } else {
                $this->template = 'default/template/module/also_bought.tpl';
            }        
            $this->render();
            
        } else {
            return '';
        }
    }
    public function getetmmaillayout(){ 
        $html = '';
        if(isset($this->request->get['category_id'])){
            $html  = $this->cache->get('etmlist.layout' . '.'.$this->request->get['category_id'].'.' . (int) $this->config->get('config_store_id'));
            if(empty($html )) {
                $this->log->write('LAYOUT ERROR '. $this->request->get['category_id'] );
            }
        }
        $this->response->setOutput($html);
    }
    
    public function deals() {
       // print_r($this->request->get);die();
        if(isset($this->request->get['dl'])){
            $main_product_id=$this->request->get['dl'];
        }else{
            $main_product_id=492;
        }
        
        foreach($this->session->data['added_bundle'][$main_product_id] as $bkey=>$added_bundle){
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
        unset($this->session->data['added_bundle']);
        $this->load->model('catalog/product');
        $this->language->load('product/product');
        $this->load->model('tool/image');
        
        $this->data['button_cart'] = $this->language->get('button_cart');
        
        $product_info = $this->model_catalog_product->getProduct($main_product_id);
        $this->data['main_product_id'] = $main_product_id ;
        $this->data['product_name'] =  $product_info ['name'];
        $bunled_items = $this->model_catalog_product->getProductBundledItems($main_product_id);
         $all_products = array();
        $added_bundle = array();
        $total_bundled_qty = 0;
        $in_bundle_qty=1;
        $bund_heads=array();
        if (!empty($bunled_items)) {
            foreach ($bunled_items as $i => $item) {
                $all_products[$i]['bundled_id'] = $item['bundle_id'];
                $all_products[$i]['bundled_qty'] = $item['bundle_quantity'];
                $all_products[$i]['bundle_name'] = $item['bundle_name'];
                $bund_heads[]=array('bundled_id'=>$item['bundle_id'],'bundle_quantity'=>$item['bundle_quantity'],'bundle_name'=>$item['bundle_name']);
                $total_bundled_qty +=$item['bundle_quantity'];
                //$all_products[$i]['mainduct_id_pr']=$item['product_id'];
                foreach ($item['products'] as $j => $bundled_product) {
                    $bunditemdata = $this->model_catalog_product->getProduct($bundled_product['product_id']);
                    if ($bunditemdata['image']) {
                        $bunditemdata['thumb'] = $this->model_tool_image->resize($bunditemdata['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
                    } else {
                        $bunditemdata['thumb'] = false;
                    }
                    //echo $bunditemdata['name']."->".$bundled_product['extra_price'];
                    $bunditemdata['extra_deal_price']=(float) $bundled_product['extra_price'] ? $this->currency->format($this->tax->calculate($bundled_product['extra_price'], '1', $this->config->get('config_tax'))):false ;
                    $all_products[$i]['products'][] = $bunditemdata;
                        $all_products[$i]['products'][$j]['options'] = $this->model_catalog_product->getBundledProductOptions($item['bundle_id'],$bundled_product['product_id']);
                    }
            }
            //$this->session->data['added_bundle']=array();
            if(!isset($this->session->data['added_bundle'][$main_product_id])){
                if(!isset($this->session->data['added_bundle'])){
                    $this->session->data['added_bundle'] =  array();
                }
                $this->session->data['added_bundle'][$main_product_id]=array();
            }else{
                //print_r($this->session->data['added_bundle'][$main_product_id]);
                foreach ($this->session->data['added_bundle'][$main_product_id] as $bkey=>$qty) {                    
                    $productkey = explode(':', $bkey);
                    if(isset($added_bundle[$bkey])){
                        $added_bundle[$bkey]['qty'] +=(int)$qty;
                    }else{
                        $prname=$this->model_catalog_product->getProductname($productkey[0]);
                        $added_bundle[$bkey]=array('name'=>$prname,'qty'=>(int)$qty);
                    }   
                    $in_bundle_qty+=(int)$qty;
                }
            } 
        }
        //print_r($added_bundle);
        $this->data['in_bundle_qty']=$in_bundle_qty;
        $this->data['total_bundled_qty']=$total_bundled_qty;
        $this->data['added_bundle'] = $added_bundle;
        $this->data['product_bundle'] = $all_products;
        $this->data['bund_heads'] =$bund_heads;
        //print_r($all_products);
        $this->data['cartshipping'] = $this->getChild('checkout/shipping_postcodes/cartshipping');
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/deals.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/product/deals.tpl';
        } else {
            $this->template = 'default/template/product/deals.tpl';
        }

            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header',
                'module/categorydeal',
                'module/cart'
            );

            $this->response->setOutput($this->render());
    }
}

?>
