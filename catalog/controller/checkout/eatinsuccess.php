<?php

class ControllerCheckoutEatinSuccess extends Controller {

    public function index() {
	
        $shipping_method = array('code' => '');
        if (isset($this->session->data['order_id']) && (!empty($this->session->data['order_id']))) {
            $this->session->data['last_order_id'] = $this->session->data['order_id'];
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
            'href' => $this->url->link('checkout/eatinsuccess'),
            'text' => $this->language->get('text_success'),
            'separator' => $this->language->get('text_separator')
        );

        
        $this->data['text_message'] = 'Thank You For Your Order';
        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['continue'] = $this->url->link('checkout/eatinsuccess/finish');
        
        $this->data['tablecontinue'] =  $this->config->get('config_url') .'eatin/&tableid='.$this->session->data['dine_table_id'];
        
        $this->data['table'] = $this->session->data['tableordering'];
        unset($this->session->data['dine_table_id']);
        unset($this->session->data['tableordering']);
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/eatinsuccess.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/eatinsuccess.tpl';
        } else {
            $this->template = 'default/template/checkout/eatinsuccess.tpl';
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
    public function finish(){
     
        unset($this->session->data['tableordering']);
        unset($this->session->data['dine_table_id']);
        unset($this->session->data['delivery_method']);
        $this->redirect($this->url->link('common/home'));
    }
    public function sendnotification(){    
             
        if ($this->request->post['order_id']  && $this->request->post['order_id']!='') {
            $note_id = '10001'.substr(time(),-6);
            $this->load->model('epos/epos_history');        
            $comment = (isset($this->request->post['table'])?$this->request->post['table']:'');
            $this->model_epos_epos_history->addnote(array('note_id'=>$note_id,'note'=>$comment."\n\r".$this->request->post['comment'],'order_id'=>$this->request->post['order_id']));             
        }        
        $this->response->setOutput(json_encode(array('success'=>'Notification Successfully sent')));
        
    }
    
}

?>
