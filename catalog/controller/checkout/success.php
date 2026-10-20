<?php

class ControllerCheckoutSuccess extends Controller {

    public function index() {
        if(isset($this->session->data['tableordering']) && isset($this->session->data['dine_table_id'])){
            $this->redirect($this->url->link('checkout/eatinsuccess'));
        }
	if ($this->posclient->isPosClient()) {
		$this->redirect($this->url->link('epos/success'));
	}
        if(isset($this->session->data['mobileapp_payment'])) { // FROM MOBILE APP
            $this->log->write(' MOBILE PAYMENT');
            if (isset($this->session->data['mobileapp_inappsafari'])) {
                $this->log->write(' MOBILE PAYMENT COMPLTE mobileapp_inappsafari');
                $this->data['continue'] =  $this->config->get('config_iphoneapp_url_scheme').'://?paystatus=success&order_id='.$this->session->data['order_id'];
                $this->data['button_continue'] = $this->language->get('button_continue');
            }
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/orderpaymenturl_success.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/module/orderpaymenturl_success.tpl';
            } else {
                $this->template = 'default/template/module/orderpaymenturl_success.tpl';
            }
            $this->response->setOutput($this->render());
            return;          
        }
        $preorder = '';
        $shipping_method = array('code' => '');
        if (isset($this->session->data['order_id']) && (!empty($this->session->data['order_id']))) {
            $this->session->data['last_order_id'] = $this->session->data['order_id'];
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->session->data['last_order_id']);
            $preorder = strtotime($order_info['requested_time']);
            if( $preorder <= strtotime($order_info['date_added'])) {
                $preorder = '';
            }
            $order_items=$this->model_checkout_order->getConsolidatedOrderProducts($this->session->data['last_order_id']);
            
            $this->load->model('catalog/product');
            $this->data['cart_items']=array();
            foreach ($order_items as $value) {
                $this->data['cart_items'][]=array(
                    'product_id'=>$value['product_id'],
                    'name'=>str_replace(array("'", '"'), '', $value['name']),
                    'total_value'=>$value['total_value'],
                    'item_category'=>$this->model_catalog_product->getCatname($value['product_id']),
                    'quantity'=>$value['quantity'],
                    'product_id'=>$value['product_id'],
                );
            }
            $this->data['order_total'] = $order_info['total'];
            $this->data['order_analyze']=true;
        }

        if (isset($this->session->data['order_id'])) {
            $this->cart->clear();
            $shipping_method = isset($this->session->data['shipping_method']) ? $this->session->data['shipping_method'] : array('code' => '');
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['guest']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);
            unset($this->session->data['vouchers']);
            unset($this->session->data['order_requested_day']);
             unset($this->session->data['order_requested_time']);
        }

        $this->language->load('checkout/success');

        if (!empty($this->session->data['last_order_id'])) {
            $this->document->setTitle(sprintf($this->language->get('heading_title_customer'), ($this->config->get('config_order_id_hide')? '':$this->session->data['last_order_id'])));
            $this->data['order_id'] = ($this->config->get('config_order_id_hide')? '': $this->session->data['last_order_id']);
           
        } else {
            $this->document->setTitle($this->language->get('heading_title'));
            $this->data['order_id'] = '';
        }
        $this->data['live_delivery_time']= $this->config->get ('config_live_delivery_time');
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart'),
            'text' => $this->language->get('text_basket'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/checkout', '', 'SSL'),
            'text' => $this->language->get('text_checkout'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/success'),
            'text' => $this->language->get('text_success'),
            'separator' => $this->language->get('text_separator')
        );

        if (!empty($this->session->data['last_order_id'])) {
            $this->data['heading_title'] = sprintf($this->language->get('heading_title_customer'), ($this->config->get('config_order_id_hide')? '': $this->session->data['last_order_id']));
        } else {
            $this->data['heading_title'] = $this->language->get('heading_title');
        }

        if ($this->customer->isLogged()) {
            $this->data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/order/info&order_id=' . $this->session->data['last_order_id'], '', 'SSL'), ($this->config->get('config_order_id_hide')? '':$this->session->data['last_order_id']), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('information/contact', '', 'SSL'), $this->url->link('information/contact'));
        } else {
            $this->data['text_message'] = sprintf($this->language->get('text_guest'), ($this->config->get('config_order_id_hide')? '':$this->session->data['last_order_id']), $this->url->link('information/contact'));
        }
        $mess = '';


        if($shipping_method['code']) {
            if($preorder) {
        if ($shipping_method['code'] == 'home.home') {
                    $this->data['text_message'] = $this->data['text_message'] . '</br>Your Delivery time is within ' . date('d/m/Y h:i a', $preorder);
                    $this->data['text_message'].= '. <b>Please check our second mail for a more accurate time.</b> In the rare case of your order being delayed for more than ' . $this->config->get('home_delivery_storetime') . ' minutes please give us a call at <b>' . $this->config->get('config_telephone') . '</b>. ' . $this->config->get('config_name') . ' values your continued custom.</br>';
                } else {
                    $this->data['text_message'] = $this->data['text_message'] . '</br>Your Collection time is within  '. date('d/m/Y h:i a', $preorder);
                    $this->data['text_message'].= ' .<b>Please check our second mail for a more accurate time.</b>  In case of any questions or concerns about your order please give us a call at <b>' . $this->config->get('config_telephone') . '</b>. ' . $this->config->get('config_name').' values your continued custom.' . '</br>';
                }
    
            } else {
                if ($shipping_method['code'] == 'home.home') {
                    $this->data['text_message'] = $this->data['text_message'] . '</br>Your Delivery time is within ' . $this->config->get('home_delivery_storetime') . ' minutes. <b>Please check our second mail for more accurate time.</b> In the rare case of your order being delayed for more than ' . $this->config->get('home_delivery_storetime') . ' minutes please give us a call at <b>' . $this->config->get('config_telephone') . '</b>. ' . $this->config->get('config_name') . ' values your continued custom.</br>';
                } else {
                    $this->data['text_message'] = $this->data['text_message'] . '</br>Your Collection time is within ' . $this->config->get('collect_in_storetime') . ' minutes. <b>Please check our second mail for more accurate time.</b> In case of any questions or concerns about your order please give us a call at <b>' . $this->config->get('config_telephone') . '</b>. ' . $this->config->get('config_name') . ' values your continued custom.' . '</br>';
                }    
            }

        } else {            
            $this->data['text_message'] = $this->data['text_message'];
        }
        $this->data['text_message'] = $this->data['text_message'] .'</br>You will receive an order acknowledgement email to confirm that we have received your order.';
        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['text_message'].= '<br>We will be notifying you with order status. If you are registered user you can check your order status in My account (Top right) -> <a href="'. $this->url->link('account/order').'">Order History</a>.';

        $this->data['continue'] = $this->url->link('common/home');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/success.tpl';
        } else {
            $this->template = 'default/template/common/success.tpl';
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
    public function deliveryTime() {

        if (isset($this->request->post['orderid'])) {
            $this->load->model('checkout/order');
            $json =  $this->model_checkout_order->deliveryTime($this->request->post['orderid']);
            
        }
        $this->response->setOutput(json_encode($json));
    }

    
}

?>
