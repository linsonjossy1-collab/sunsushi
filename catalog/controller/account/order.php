<?php

class ControllerAccountOrder extends Controller {

    private $error = array();

    public function index() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/order', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->language->load('account/order');

        $this->load->model('account/order');
        $this->load->model('account/backorder');
        
        $this->load->model('checkout/order');
        if (isset($this->request->get['order_id'])) {
                        if (isset($this->request->get['backorder'])) {	
                           // $this->model_account_order = $this->model_account_backorder;
                        }
            $order_info = $this->model_account_order->getOrder($this->request->get['order_id']);

            if ($order_info) {
                $orderid = $this->request->get['order_id'];
                $order_products = $this->model_account_order->getFormattedOrderProducts($this->request->get['order_id']);
                //print_r($order_products);die();
                foreach($order_products as $order_product){
                    if(isset($order_product['product_modified'])){
                        continue;
                    }
                    $option_data = array();
                    foreach ($order_product['option'] as $order_option) {
                         if ($order_option['type'] == 'select' || $order_option['type'] == 'radio') {
                            $option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
                        } elseif ($order_option['type'] == 'checkbox') {
                            $option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
                        } elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
                            $option_data[$order_option['product_option_id']] = $order_option['value'];
                        } elseif ($order_option['type'] == 'file') {
                            $option_data[$order_option['product_option_id']] = $this->encryption->encrypt($order_option['value']);
                        }
                    }
                    $topping_data = array();
                    if(!empty($order_product['toppings'])){
                        foreach ($order_product['toppings'] as $order_topping) {
                            if(!isset($topping_data[$order_topping['product_topping_id']])){
                                $topping_data[$order_topping['product_topping_id']] =  array();
                            }
                            $topping_data[$order_topping['product_topping_id']][$order_topping['product_topping_value_id']] = $order_topping['quantity'];
                        }
                    }
                    if(!empty($topping_data)){
                        $option_data['topping'] = $topping_data;
                    }                    
                    if(isset($order_product['deal_item']) && $order_product['bundle']) {
                        $dealproducts = array();
                        foreach ($order_product['bundle'] as $k => $bundled_product) {
                            $bund_modified = $this->model_account_order->checkProductmodifedafterOrder($orderid,$bundled_product['product_id']);
                            if($bund_modified) {
                                continue 2;
                            }
                            $bundled_data = array();
                            $bundled_data['main_product_id'] = $order_product['product_id'];
                            $bundled_data['bundle_id'] = $bundled_product['bundle_id'];
                            $bundled_data['bundleidval'] = strtotime('now');

                            if(isset($bundled_product['options'])) {
                                foreach($bundled_product['options'] as $bundleoption) {
                                    if ($bundleoption['type'] == 'select' || $bundleoption['type'] == 'radio') {
                                        $bundled_data[$bundleoption['product_option_id']] = $bundleoption['product_option_value_id'];
                                    } elseif ($bundleoption['type'] == 'checkbox') {
                                        $bundled_data[$bundleoption['product_option_id']][] = $bundleoption['product_option_value_id'];
                                    } elseif ($bundleoption['type'] == 'text' || $bundleoption['type'] == 'textarea' || $bundleoption['type'] == 'date' || $bundleoption['type'] == 'datetime' || $bundleoption['type'] == 'time') {
                                        $bundled_data[$bundleoption['product_option_id']] = $bundleoption['value'];
                                    } elseif ($bundleoption['type'] == 'file') {
                                        $bundled_data[$bundleoption['product_option_id']] = $this->encryption->encrypt($bundleoption['value']);
                                    }
                                }
                            }
                            
                            if(isset($bundled_product['toppings'])) {
                                foreach($bundled_product['toppings'] as $order_row) {
                                    $bundled_data['topping'][$order_now['product_topping_id']][$order_row['product_topping_value_id']]= $order_row['quantity']; 
                                }
                            }

                            if(isset($bundled_product['dressings'])) {
                                foreach($bundled_product['dressings'] as $order_row) {
                                    $bundled_data['dressing'][$order_row['product_dressing_id']][$order_row['product_dressing_value_id']]= $order_row['quantity']; 
                                }
                            }   
                            $dealproducts[] =  array('product_id'=>$bundled_product['product_id'],'quantity'=>$bundled_product['quantity'],'option'=>$bundled_data);
                        }

                        $total_deal_item = count($dealproducts)-1;
                        for($i=0; $i < $total_deal_item; $i++ ) {
                            $this->cart->add($dealproducts[$i]['product_id'], $dealproducts[$i]['quantity'], $dealproducts[$i]['option']);
                        }

                        if (!empty($option_data)) {
                            $option_data['bundled'] = $bundled_data;
                        } else {
                            $order_product['product_id'] = $bundled_product['product_id']; 
                            $option_data = $bundled_data;
                        }         
                        $option_data['dealcomplete'] = $bundled_product['product_id'];      
                    }                  
                    $this->session->data['success'] = sprintf($this->language->get('text_success'), $this->request->get['order_id']);
                    $this->cart->add($order_product['product_id'], $order_product['quantity'], $option_data);
                }
                
                $this->session->data['delivery_method'] = $order_info['shipping_code'];
                $this->redirect($this->url->link('checkout/checkoutetm'));
            }
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/order', $url, 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_order_id'] = $this->language->get('text_order_id');
        $this->data['text_status'] = $this->language->get('text_status');
        $this->data['text_date_added'] = $this->language->get('text_date_added');
        $this->data['text_customer'] = $this->language->get('text_customer');
        $this->data['text_products'] = $this->language->get('text_products');
        $this->data['text_total'] = $this->language->get('text_total');
        $this->data['text_empty'] = $this->language->get('text_empty');

        $this->data['button_view'] = $this->language->get('button_view');
        $this->data['button_reorder'] = $this->language->get('button_reorder');
        $this->data['button_continue'] = $this->language->get('button_continue');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->data['orders'] = array();

        $order_total = $this->model_account_order->getTotalOrders();

        $results = $this->model_account_order->getOrders(($page - 1) * 10, 10);

        foreach ($results as $result) {
            $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
            $voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);
            if(strtotime($result['requested_time']) <= strtotime($result['date_added']  )) {
                $requested_time = 'ASAP';
            }
            else {
                //$template->data['requested_time'] = date($language->get('date_format_short') . ' h:i A', strtotime($order_info['requested_time']));
                $requested_time = date("l d F h:i A", strtotime($result['requested_time']));
            }
            $this->data['orders'][] = array(
                'order_id' => $result['order_id'],
                'name' => $result['firstname'] . ' ' . $result['lastname'],
                'status' => $result['status'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'org_date_added'=>$result['date_added'],
                'products' => ($product_total + $voucher_total),
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'href' => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL'),
                'reorder' => $this->url->link('account/order', 'order_id=' . $result['order_id'], 'SSL'),
                'shipping_code'=>$result['shipping_code'],
                'notify' => $this->url->link('account/account/sendnotification', 'order_id=' . $result['order_id'], 'SSL'),
                'requested_time'=>$requested_time
            );
        }
                // Back Orders

                $order_total += $this->model_account_backorder->getTotalOrders();
		
		$results = $this->model_account_backorder->getOrders(($page - 1) * 10, 10);
		
		foreach ($results as $result) {
			$product_total = $this->model_account_backorder->getTotalOrderProductsByOrderId($result['order_id']);
			$voucher_total = $this->model_account_backorder->getTotalOrderVouchersByOrderId($result['order_id']);

			$this->data['orders'][] = array(
				'order_id'   => $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'products'   => ($product_total + $voucher_total),
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'href'       => $this->url->link('account/order/info', 'backorder=y&order_id=' . $result['order_id'], 'SSL'),
				'reorder'    => $this->url->link('account/order', 'backorder=y&order_id=' . $result['order_id'], 'SSL')
			);
		}
                
        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('account/order', 'page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();

        $this->data['continue'] = $this->url->link('account/account', '', 'SSL');
        if ($this->config->get('config_online_orders')) {
            $this->data['order_msg'] = $this->getChild('common/onlineorders/noonlineordermessagepopup');
        } else {
            if ($this->config->get('config_holiday_online_orders')) {
                if ($this->postcode->isStoreHoliday()) {
                    $this->data['order_msg'] = $this->getChild('common/onlineorders/storeholidayonlineorderpopup');
                }
            }
            if ($this->config->get('config_store_time_orders') && !isset($this->data['order_msg'])) {
                if (!$this->postcode->isStoreOpened($this->config->get('config_store_id'))) {
                    $this->data['order_msg'] = $this->getChild('common/onlineorders/storetimeonlineorderpopup');
                }
            }
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/order_list.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/order_list.tpl';
        } else {
            $this->template = 'default/template/account/order_list.tpl';
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

    public function info() {
        $this->language->load('account/order');

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/order/info', 'order_id=' . $order_id, 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }
		if (isset($this->request->get['backorder'])) {	
                    $this->load->model('account/backorder');
                    $this->model_account_order = $this->model_account_backorder;
                }
                else {
        $this->load->model('account/order');
                }

        $order_info = $this->model_account_order->getOrder($order_id);

        if ($order_info) {


            $this->load->model('checkout/order');
            $order_status = $this->model_checkout_order->getOrderStatuses();
            $this->data['order_status_id'] =  $order_info['order_status_id'];

            $shippingcode = explode('.',$order_info['shipping_code']);
            $code = $shippingcode[0];

            $track_statuses = $this->config->get('config_track_status');
            if($track_statuses) {
                $statids = array();
                foreach($order_status as $status) {
                    $orderstatus[$status['order_status_id']] = $status['name'];
                    //$statids[] = $status['order_status_id'];
                }
                
                $sort_order = array();
                foreach ($track_statuses as $key => $value) {
                    if(isset($value[$code]) && $value[$code]) {                        
                        $sort_order[$key] = $value['sort_order'];
                        $track_status[$key]['name'] = $orderstatus[$key];
                        $track_status[$key]['id'] = $key;
                        $track_status[$key]['class'] = $value['class'];
                        $track_status[$key]['date_added'] = '';
                        $statids[] = $key;
                    }
                }

                if($statids) {
                    $query = $this->db->query("SELECT MAX(date_added) as date_added, order_status_id FROM ".DB_PREFIX."order_history WHERE order_id = '" . (int)$order_id . "' AND order_status_id IN (".implode(',',$statids).") GROUP BY order_status_id  ORDER BY date_added  ");

                    foreach($query->rows as $row) {
                        $track_status[$row['order_status_id']]['date_added'] = date('h:i a',strtotime($row['date_added']));
                    }

                    array_multisort($sort_order, SORT_ASC, $track_status);

                }
                $this->data['track_status'] = $track_status;
            } else {
                $this->data['track_status'] = array();
            }

            $this->document->setTitle($this->language->get('text_order'));

            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home'),
                'separator' => false
            );

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_account'),
                'href' => $this->url->link('account/account', '', 'SSL'),
                'separator' => $this->language->get('text_separator')
            );

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('account/order', $url, 'SSL'),
                'separator' => $this->language->get('text_separator')
            );

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_order'),
                'href' => $this->url->link('account/order/info', 'order_id=' . $this->request->get['order_id'] . $url, 'SSL'),
                'separator' => $this->language->get('text_separator')
            );

            $this->data['heading_title'] = $this->language->get('text_order');

            $this->data['text_order_detail'] = $this->language->get('text_order_detail');
            $this->data['text_invoice_no'] = $this->language->get('text_invoice_no');
            $this->data['text_order_id'] = $this->language->get('text_order_id');
            $this->data['text_date_added'] = $this->language->get('text_date_added');
            $this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
            $this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
            $this->data['text_payment_method'] = $this->language->get('text_payment_method');
            $this->data['text_payment_address'] = $this->language->get('text_payment_address');
            $this->data['text_history'] = $this->language->get('text_history');
            $this->data['text_comment'] = $this->language->get('text_comment');

            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_model'] = $this->language->get('column_model');
            $this->data['column_quantity'] = $this->language->get('column_quantity');
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_total'] = $this->language->get('column_total');
            $this->data['column_action'] = $this->language->get('column_action');
            $this->data['column_date_added'] = $this->language->get('column_date_added');
            $this->data['column_status'] = $this->language->get('column_status');
            $this->data['column_comment'] = $this->language->get('column_comment');

            $this->data['button_return'] = $this->language->get('button_return');
            $this->data['button_continue'] = $this->language->get('button_continue');

            if ($order_info['invoice_no']) {
                $this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
            } else {
                $this->data['invoice_no'] = '';
            }

            $this->data['order_id'] = $this->request->get['order_id'];
            $this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

            $this->data['locationmap'] =   $this->getChild('account/order/locationmap',$order_info); // $this->locationmap($order_info); 

            if ($order_info['payment_address_format']) {
                $format = $order_info['payment_address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
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

            $this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

            $this->data['payment_method'] = $order_info['payment_method'];

            if ($order_info['shipping_address_format']) {
                $format = $order_info['shipping_address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
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

            $this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

            $this->data['shipping_method'] = $order_info['shipping_method'];

            $this->data['products'] = array();
            $this->load->model('checkout/order');
            $odrerproducts = $this->model_checkout_order->getFormattedOrderProducts($this->request->get['order_id']);
            foreach ($odrerproducts as $odrerproduct) {
                $odrerproduct['price'] = $this->currency->format($odrerproduct['price'] + ($this->config->get('config_tax') ? $odrerproduct['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']);
                $odrerproduct['total'] = $this->currency->format($odrerproduct['total'] + ($this->config->get('config_tax') ? ($odrerproduct['tax'] * $odrerproduct['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']);
                $odrerproduct['href'] = $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $odrerproduct['product_id'], 'SSL');
                $this->data['products'][]=$odrerproduct;
            }
            

            // Voucher
            $this->data['vouchers'] = array();

            $vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);

            foreach ($vouchers as $voucher) {
                $this->data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'amount' => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
                );
            }

            $this->data['totals'] = $this->model_account_order->getOrderTotals($this->request->get['order_id']);

            $this->data['comment'] = nl2br($order_info['comment']);

            $this->data['histories'] = array();

            $results = $this->model_account_order->getOrderHistories($this->request->get['order_id']);

            foreach ($results as $result) {
                $this->data['histories'][] = array(
                    'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                    'status' => $result['status'],
                    'comment' => nl2br($result['comment'])
                );
            }

          
            
            $this->data['continue'] = $this->url->link('account/order', '', 'SSL');

            $this->data['entry_requested_time'] = 'Requested time';
            $this->data['entry_eta_time'] = 'Estimated time';

            if($order_info['shipping_code']=='home.home') {
                $this->data['entry_delivery_time'] = 'Delivery time';
                $sorting_time = ((int)$this->config->get('home_delivery_storetime') + (int)$this->config->get('home_sorting_time')+(int)$this->config->get('home_pan_time'))*60;
            } else {
                $this->data['entry_delivery_time'] = 'Collection time';
                $sorting_time = ((int)$this->config->get('collect_in_storetime') + (int)$this->config->get('collect_sorting_time')+(int)$this->config->get('collect_pan_time'))*60;
            }

            $dateadded = strtotime($order_info['date_added']);
            $requestedtime = strtotime($order_info['requested_time']);

            if($requestedtime <= $dateadded) {
                $this->data['requested_time'] = 'AS SOON AS POSSIBLE';
            } else {
                $this->data['requested_time'] = date('d/m/Y h:i A',$requestedtime);
            }
            $this->data['delivery_time'] = '';
            if(isset($order_info['delivery_time'])) {
                if( strtotime($order_info['delivery_time']) <= $dateadded) {
                    if($this->data['requested_time']=='AS SOON AS POSSIBLE') {
                        $this->data['delivery_time'] = date('d/m/Y h:i A',$requestedtime+$sorting_time);
                    } else {
                        $this->data['delivery_time'] = $this->data['requested_time'];
                    }
                } else {
                    $this->data['delivery_time'] = date('d/m/Y h:i A',strtotime($order_info['delivery_time']));
                }
            }
            $this->data['eta_time'] = ''; 
            if(isset($order_info['eta_time'])) {
                $this->data['eta_time'] = date('h:i A', $order_info['eta_time']);
            } 
            
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/order_info.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/account/order_info.tpl';
            } else {
                $this->template = 'default/template/account/order_info.tpl';
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
        } else {
            $this->document->setTitle($this->language->get('text_order'));

            $this->data['heading_title'] = $this->language->get('text_order');

            $this->data['text_error'] = $this->language->get('text_error');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home'),
                'separator' => false
            );

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_account'),
                'href' => $this->url->link('account/account', '', 'SSL'),
                'separator' => $this->language->get('text_separator')
            );

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('account/order', '', 'SSL'),
                'separator' => $this->language->get('text_separator')
            );

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_order'),
                'href' => $this->url->link('account/order/info', 'order_id=' . $order_id, 'SSL'),
                'separator' => $this->language->get('text_separator')
            );

            $this->data['continue'] = $this->url->link('account/order', '', 'SSL');

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
    
    public function status() {

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/order/info', 'order_id=' . $order_id, 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }
        if (isset($this->request->get['backorder'])) {  
            $this->load->model('account/backorder');
            $this->model_account_order = $this->model_account_backorder;
        } else {
            $this->load->model('account/order');
        }
        $order_info = $this->model_account_order->getOrder($order_id);

        $json['order_status_id'] = 0;
        $json['map'] = false;
        if ($order_info) {
            $json['order_status_id'] = $order_info['order_status_id'];
            $json['time'] = '';

            $query = $this->db->query("SELECT date_added FROM ".DB_PREFIX."order_history WHERE order_id = '".$order_id."' ORDER BY order_history_id DESC LIMIT 1");
            if(isset($query->row['date_added'])) {
                $json['time'] = date('h:i a',strtotime($query->row['date_added']));
            }

            if($order_info['eta_time']) {
                $json['eta_time'] = date('h:i A',$order_info['eta_time']);
            } else {
                $json['eta_time'] = '';
            }

            if($order_info['shipping_code']=='home.home') {
                $json['del_time'] = 'Delivery time ';
                $sorting_time = ((int)$this->config->get('home_sorting_time')+(int)$this->config->get('home_pan_time'))*60;
                $asap_time = (int)$this->config->get('home_delivery_storetime')*60;
            } else {
                $json['del_time'] = 'Collection time ';
                $sorting_time = ((int)$this->config->get('collect_sorting_time')+(int)$this->config->get('collect_pan_time'))*60;
                $asap_time = (int)$this->config->get('collect_in_storetime')*60;
            }

            $dateadded = strtotime($order_info['date_added']);
            if(strtotime($order_info['delivery_time']) <= $dateadded) {
                $requestedtime = strtotime($order_info['requested_time']);
                if($requestedtime <= $dateadded) {
                    $json['del_time'].= date('d/m/Y h:i A',$requestedtime+$sorting_time+$asap_time);
                } else {
                    $json['del_time'].= date('d/m/Y h:i A',$requestedtime);
                }
            } else {
                $json['del_time'].= date('d/m/Y h:i A',strtotime($order_info['delivery_time']));
            }
            /*
            $dispatched_status = ($this->config->get('drivermanagement_driver_status')) ? $this->config->get('drivermanagement_driver_status') : array();
            if(in_array($order_info['order_status_id'],$dispatched_status )) {
                $json['map'] = true;

                $query = $this->db->query("SELECT  latitude, longitude FROM ".DB_PREFIX."driver_location WHERE user_id = (SELECT e.user_id FROM ".DB_PREFIX."employee e LEFT JOIN ".DB_PREFIX."deliverys d ON e.employee_id = d.driver_id WHERE d.order_id = '".$order_info['order_id']."' order by d.delivery_id  DESC LIMIT 1 ) ORDER BY driver_location_id DESC LIMIT 1 ");
                if($query->rows) {
                    $departure_lat = $query->row['latitude'].','.$query->row['longitude'];
                    $store_distance = $this->postcode->getDistanceinSeconds($departure_lat,$order_info['shipping_postcode']);
                    $eta = strtotime('now')+$store_distance;
                    if($order_info['eta_time']!==$eta) {                            
                        $this->db->query("UPDATE `".DB_PREFIX."order` SET eta_time = '".$eta."', date_modified = NOW() WHERE order_id = '".$order_id."'"); 
                        $json['eta_time'] = date('h:i A',$eta);
                        $this->websocket->connect(json_encode(array()));
                    }
                }


            }
            */
        }

        $this->response->setOutput(json_encode($json));

    }

    public function last() {
        $this->language->load('account/order');

       
        //if ($order_info) {
            $this->document->setTitle($this->language->get('text_order'));

            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home'),
                'separator' => false
            );

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_account'),
                'href' => $this->url->link('account/account', '', 'SSL'),
                'separator' => $this->language->get('text_separator')
            );

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('account/order', $url, 'SSL'),
                'separator' => $this->language->get('text_separator')
            );

            $this->data['heading_title'] = $this->language->get('text_order');

            $this->data['text_order_detail'] = $this->language->get('text_order_detail');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/last_order_info.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/account/last_order_info.tpl';
            } else {
                $this->template = 'default/template/account/last_order_info.tpl';
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
        //}
    }

    public function orderdetails() {

        $this->language->load('account/order');
        if(isset($this->request->get['o'])) {
            $o = explode('or',$this->request->get['o']);

            $this->load->model('checkout/order');

            $this->load->model('account/order');

            
            $order_info = array();
            if($o[2]) {
                if ($this->customer->isLogged() && $o[2] == $this->customer->getId()) {
                    $order_info = $this->model_checkout_order->getOrder($o[1]);
                }
            } else if (!$this->customer->isLogged()) {                
                $order_info = $this->model_checkout_order->getOrder($o[1]);
            }

            if (!$order_info || $order_info['customer_id']!== $o[2]) {
                //Authentication failed

                if(isset($this->request->get['history']) || isset($this->request->get['status'])) {
                    $json = array();
                    $this->response->setOutput(json_encode($json));
                }                 

            } else if(isset($this->request->get['list'])) { 

            $shippingcode = explode('.',$order_info['shipping_code']);
            $code = $shippingcode[0];

                $statids = array();

                $json['count'] = 0;
                $track_status = $this->config->get('config_track_status');
                if($track_status) {


                    $this->load->model('checkout/order');
                    $order_status = $this->model_checkout_order->getOrderStatuses();
                foreach($order_status as $status) {
                    $orderstatus[$status['order_status_id']] = $status['name'];
                        $statids[] = $status['order_status_id'];
                }
                
                $sort_order = array();
                    foreach ($track_status as $key => $value) {
                        $sort_order[$key] = $value['sort_order'];
                        $track_status[$key]['name'] = $orderstatus[$key];
                        $track_status[$key]['id'] = $key;
                        $track_status[$key]['date_added'] = '';
                        if(isset($value[$code]) && $value[$code]) {
                            $json['count']++;
                            $track_status[$key]['status'] = 1;
                        } else {
                            $track_status[$key]['status'] = 0;
                    }
                }

                    $query = $this->db->query("SELECT MAX(date_added) as date_added, order_status_id FROM ".DB_PREFIX."order_history WHERE order_id = '" . (int)$o[1] . "' AND order_status_id IN (".implode(',',$statids).") GROUP BY order_status_id  ORDER BY date_added  ");

                    foreach($query->rows as $row) {
                        $track_status[$row['order_status_id']]['date_added'] = date('h:i a',strtotime($row['date_added']));
                    }

                    array_multisort($sort_order, SORT_ASC, $track_status);
                    $json['track_status'] = $track_status;
                    $json['text'] =  "Order #".$o[1]." ".$orderstatus[$order_info['order_status_id']];
                    $json['order_status_id'] = $order_info['order_status_id'];
            } else {
                    $json = array();
            }
                $this->response->setOutput(json_encode($json));

            } else if(isset($this->request->get['status'])) {

                $json = array();

                $results = $this->model_account_order->getOrderStatus($o[1]);

                if($results) {
                    $json['text'] =  "Order #".$o[1]." ".$results['status'];
                    $json['time'] = date('h:i a',strtotime($results['date_added']));
                    $json['order_status_id'] = $results['order_status_id'];
                    if($results['order_status_id']==$this->config->get('config_complete_status_id')) {
                        $json['clear'] = 1;
                    } else {
                        $json['clear'] = 0;
                    }                    
                }
                $this->response->setOutput(json_encode($json));

            } else if(isset($this->request->get['history'])) {
                $this->data['histories'] = array();

                $results = $this->model_account_order->getOrderHistories($o[1]);

                foreach ($results as $result) {
                    $this->data['histories'][] = array(
                        'date_added' => date('d/m/Y h:i A', strtotime($result['date_added'])),
                        'status' => $result['status'],
                        'comment' => nl2br($result['comment'])
                    );
                    }                    

                $this->response->setOutput(json_encode($this->data['histories']));
            } else {
                
                $this->data['heading_title'] = $this->language->get('text_order');

                $this->data['text_order_detail'] = $this->language->get('text_order_detail');
                $this->data['text_invoice_no'] = $this->language->get('text_invoice_no');
                $this->data['text_order_id'] = $this->language->get('text_order_id');
                $this->data['text_date_added'] = $this->language->get('text_date_added');
                $this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
                $this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
                $this->data['text_payment_method'] = $this->language->get('text_payment_method');
                $this->data['text_payment_address'] = $this->language->get('text_payment_address');
                $this->data['text_history'] = $this->language->get('text_history');
                $this->data['text_comment'] = $this->language->get('text_comment');

                $this->data['column_name'] = $this->language->get('column_name');
                $this->data['column_model'] = $this->language->get('column_model');
                $this->data['column_quantity'] = $this->language->get('column_quantity');
                $this->data['column_price'] = $this->language->get('column_price');
                $this->data['column_total'] = $this->language->get('column_total');
                $this->data['column_action'] = $this->language->get('column_action');
                $this->data['column_date_added'] = $this->language->get('column_date_added');
                $this->data['column_status'] = $this->language->get('column_status');
                $this->data['column_comment'] = $this->language->get('column_comment');

                $this->data['button_return'] = $this->language->get('button_return');
                $this->data['button_continue'] = $this->language->get('button_continue');

                if ($order_info['invoice_no']) {
                    $this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
                } else {
                    $this->data['invoice_no'] = '';
                }

                $this->data['order_id'] = $o[1];
                $this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

                if ($order_info['payment_address_format']) {
                    $format = $order_info['payment_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
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

                $this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                $this->data['payment_method'] = $order_info['payment_method'];

                if ($order_info['shipping_address_format']) {
                    $format = $order_info['shipping_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
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

                $this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                $this->data['shipping_method'] = $order_info['shipping_method'];

                $this->data['products'] = array();
                $this->load->model('checkout/order');
                $odrerproducts = $this->model_checkout_order->getFormattedOrderProducts($o[1]);
                foreach ($odrerproducts as $odrerproduct) {
                    $odrerproduct['price'] = $this->currency->format($odrerproduct['price'] + ($this->config->get('config_tax') ? $odrerproduct['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']);
                    $odrerproduct['total'] = $this->currency->format($odrerproduct['total'] + ($this->config->get('config_tax') ? ($odrerproduct['tax'] * $odrerproduct['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']);
                    $odrerproduct['href'] = $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $odrerproduct['product_id'], 'SSL');
                    $this->data['products'][]=$odrerproduct;
                        }
                
                $this->data['vouchers'] = array();

                $vouchers = $this->model_account_order->getOrderVouchers($o[1]);

                foreach ($vouchers as $voucher) {
                    $this->data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
                    );
                        }

                $this->data['totals'] = $this->model_account_order->getOrderTotals($o[1]);

                $this->data['comment'] = nl2br($order_info['comment']);

                $this->data['histories'] = array();
                /*

                $results = $this->model_account_order->getOrderHistories($o[1]);

                foreach ($results as $result) {
                    $this->data['histories'][] = array(
                        'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                        'status' => $result['status'],
                        'comment' => nl2br($result['comment'])
                    );
                        }
                */


                $this->load->model('checkout/order');
            $order_status = $this->model_checkout_order->getOrderStatuses();

            $this->data['statcount'] = 0;
            $this->data['order_status_id'] =  $order_info['order_status_id'];

            $shippingcode = explode('.',$order_info['shipping_code']);
            $code = $shippingcode[0];


            $track_statuses = $this->config->get('config_track_status');
            if($track_statuses) {
                $statids = array();
                foreach($order_status as $status) {
                    $orderstatus[$status['order_status_id']] = $status['name'];
                    //$statids[] = $status['order_status_id'];
                    }

                $sort_order = array();
                foreach ($track_statuses as $key => $value) {
                    if(isset($value[$code]) && $value[$code]) {                        
                        $sort_order[$key] = $value['sort_order'];
                        $track_status[$key]['name'] = $orderstatus[$key];
                        $track_status[$key]['id'] = $key;
                        $track_status[$key]['class'] = $value['class'];
                        $track_status[$key]['date_added'] = '';
                        $statids[] = $key;
                }
                }
                
                if($statids) {
                    $query = $this->db->query("SELECT MAX(date_added) as date_added, order_status_id FROM ".DB_PREFIX."order_history WHERE order_id = '" . (int)$order_id . "' AND order_status_id IN (".implode(',',$statids).") GROUP BY order_status_id  ORDER BY date_added  ");

                    foreach($query->rows as $row) {
                        $track_status[$row['order_status_id']]['date_added'] = date('h:i a',strtotime($row['date_added']));
            }
            
                    array_multisort($sort_order, SORT_ASC, $track_status);

                }
                $this->data['track_status'] = $track_status;
            } else {
                $this->data['track_status'] = array();
            }

            $this->data['entry_requested_time'] = 'Requested time';

            if($order_info['shipping_code']=='home.home') {
            $this->data['entry_delivery_time'] = 'Delivery time';

        } else {
            $this->data['entry_delivery_time'] = 'Collection time';

            }

            /*
            $dateadded = strtotime($order_info['date_added']);

            if(strtotime($order_info['requested_time']) <= $dateadded) {
                $this->data['requested_time'] = 'AS SOON AS POSSIBLE';
            } else {
                $this->data['requested_time'] = date('d/m/Y h:i A',strtotime($order_info['requested_time']));
            }

            if(strtotime($order_info['delivery_time']) <= $dateadded) {
                $this->data['delivery_time'] = $this->data['requested_time'];
            } else {
                $this->data['delivery_time'] = date('d/m/Y h:i A',strtotime($order_info['delivery_time']));
            }
            */


            if($order_info['eta_time']) {
                $this->data['eta_time'] = date('h:i A', $order_info['eta_time']);
            } else {
                $this->data['eta_time'] = '';
            }              

            //$this->getEta($order_id, $order_info);




            $this->data['continue'] = $this->url->link('account/order', '', 'SSL');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/order_info.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/account/order_info.tpl';
            } else {
                    $this->template = 'default/template/account/order_info.tpl';
            }

            $this->response->setOutput($this->render());
        }
        }
    }
    public function locationmap($order_info) {
        $this->data['order_id'] = $this->request->get['order_id'];
        $this->data['config_browserapp_api'] = $this->config->get('config_browserapp_api');
         $this->data['map_image_base_api'] = $this->config->get('config_ssl') .'image/map/';
         
        $this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
        $this->data['map'] = 0;

        if($this->data['date_added'] == date($this->language->get('date_format_short')) && $order_info['order_status_id']!==$this->config->get('config_complete_status_id')) {

            $takeoutorders = array();

            if($this->config->get('drivermanagement_cancel_status')) {
                foreach($this->config->get('drivermanagement_cancel_status') as $stats) {
                    //array_push($takeoutorders,$stats);
                    $takeoutorders[$stats] = 1;
                }                    
            }

            if($this->config->get('drivermanagement_complete_status')) {
                foreach($this->config->get('drivermanagement_complete_status') as $stats) {
                    //array_push($takeoutorders,$stats);
                    $takeoutorders[$stats] = 1;
                }                    
            }

            if(!isset($takeoutorders[$order_info['order_status_id']])) {

                $query = $this->db->query("SELECT  latitude, longitude FROM ".DB_PREFIX."driver_location WHERE user_id = (SELECT e.user_id FROM ".DB_PREFIX."employee e LEFT JOIN ".DB_PREFIX."deliverys d ON e.employee_id = d.driver_id WHERE d.order_id = '".$this->data['order_id'] ."' order by d.delivery_id  DESC LIMIT 1 ) ORDER BY driver_location_id DESC LIMIT 1 ");

                if($query->rows) {

                    $this->data['driver_postion'] = '{ lat: '.$query->row['latitude'].', lng: '.$query->row['longitude'].' }';

                  //  $this->data['driver_postion'] = '{ lat: 51.378577, lng: -0.584284 }';
      
                    $store_position = $this->postcode->getPostcodeDetails($this->config->get('config_postalcode'));
                    if($store_position) {
                        $this->data['store_position'] = '{ lat: '.$store_position['latitude'].', lng: '.$store_position['longitude'].' }';

                        //$position = array('lat' => $store_position['latitude'], 'lng' => $store_position['longitude']);

                        //$this->data['store_position'] = json_encode($position);
                    }
                    $customer_position = $this->postcode->getPostcodeDetails($order_info['shipping_postcode']);
                    if($customer_position) {
                        $this->data['customer_position'] = '{ lat: '.$customer_position['latitude'].', lng: '.$customer_position['longitude'].' }';   
                        //$position = array('lat' => $customer_position['latitude'], 'lng' => $customer_position['longitude']);    
                        //$this->data['customer_position'] = json_encode($position);             
                    }
                    if($store_position && $customer_position) {
                        $this->data['map'] = 1;
                    }

               }

            }

        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/order_locationmap.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/account/order_locationmap.tpl';
        } else {
                $this->template = 'default/template/account/order_locationmap.tpl';
        }
        $this->response->setOutput($this->render());
        
    }
    public function location() {
        $json = array();

        /*

        $rand = array(1,2,3);

        $location = array(
                    array('lat' => 51.3785803, 'lng' => -0.6017935,
                        ),
                    array('lat' => 51.379062, 'lng' => -0.594498,
                        ),
                    array('lat' => 51.388169, 'lng' => -0.573641,
                        ),
                    ); 

        $number = rand(0,2);

        $json = $location[$number];
        $this->response->setOutput(json_encode($json));

        */

        
        if($this->request->get['order_id']) {
            $order_id = (int)$this->request->get['order_id'];
            $query = $this->db->query("SELECT  latitude, longitude FROM ".DB_PREFIX."driver_location WHERE user_id = (SELECT e.user_id FROM ".DB_PREFIX."employee e LEFT JOIN ".DB_PREFIX."deliverys d ON e.employee_id = d.driver_id WHERE d.order_id = '".$order_id."' order by d.delivery_id  DESC LIMIT 1 )  ORDER BY driver_location_id DESC LIMIT 1");
            if($query->rows) {
                $json = array('lat' => (float)$query->row['latitude'], 'lng' => (float)$query->row['longitude']); 
            }
        }
        
        $this->response->setOutput(json_encode($json));
    }

    private function getEta($order_id,$order_info) {

        $eta = $order_info['eta_time'];        
        $dispatched_status = ($this->config->get('drivermanagement_driver_status')) ? $this->config->get('drivermanagement_driver_status') : array();

        if($order_info['shipping_code']=='home.home') {
            $this->data['entry_delivery_time'] = 'Delivery time';
            $asap_time = (int)$this->config->get('home_delivery_storetime')*60;
            $sorting_time = ((int)$this->config->get('home_sorting_time')+(int)$this->config->get('home_pan_time'))*60;
        } else {
            $this->data['entry_delivery_time'] = 'Collection time';
            $asap_time = (int)$this->config->get('collect_in_storetime')*60;
            $sorting_time = ((int)$this->config->get('collect_sorting_time')+(int)$this->config->get('collect_pan_time'))*60;
        }

        $dateadded = strtotime($order_info['date_added']);

        if(strtotime($order_info['delivery_time']) <= $dateadded) {
            $this->data['delivery_time'] = $this->data['requested_time'];
            /*
            if($this->data['requested_time']=='AS SOON AS POSSIBLE') {
                $eta = strtotime($order_info['requested_time'])+$asap_time;
            } 
            */
        } else {
            $this->data['delivery_time'] = date('d/m/Y h:i A',strtotime($order_info['delivery_time']));
        }

        if(in_array($order_info['order_status_id'],$dispatched_status )) {
            $query = $this->db->query("SELECT  latitude, longitude FROM ".DB_PREFIX."driver_location WHERE user_id = (SELECT e.user_id FROM ".DB_PREFIX."employee e LEFT JOIN ".DB_PREFIX."deliverys d ON e.employee_id = d.driver_id WHERE d.order_id = '".$order_info['order_id']."' order by d.delivery_id  DESC LIMIT 1 ) ORDER BY driver_location_id DESC LIMIT 1 ");
            if($query->rows) {
                $departure_lat = $query->row['latitude'].','.$query->row['longitude'];
                $store_distance = $this->postcode->getDistanceinSeconds($departure_lat,$order_info['shipping_postcode']);
            }
            $eta = strtotime('now')+$store_distance;
        }

        if($eta) {
            $this->data['eta_time'] = date('d/m/Y h:i A',$eta);
            if($eta!==$order_info['eta_time']) {
                $this->db->query("UPDATE `".DB_PREFIX."order` SET eta_time = '".$eta."', date_modified = NOW() WHERE order_id = '".$order_id."'");  
            }           
        } else {
            $this->data['eta_time'] = '';   
        }   
    }

}

