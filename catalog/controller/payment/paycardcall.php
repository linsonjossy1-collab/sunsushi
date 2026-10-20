<?php

class ControllerPaymentPaycardcall extends Controller {

    protected function index() {
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['continue'] = $this->url->link('checkout/success'); 
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paycardcall.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/paycardcall.tpl';
        } else {
            $this->template = 'default/template/payment/paycardcall.tpl';
        }

        $this->render();
    }
    public function confirm() {
        $this->load->model('checkout/order');
        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('paycardcall_order_status_id'));
    }
    
    protected function epos() {
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['continue'] = $this->url->link('epos/success');
        $this->data['paymentcode'] = $this->session->data['payment_method']['code'];
        if(isset($this->session->data['main_payment_amt'])){
            $this->data['paymenttotal']=$this->session->data['main_payment_amt'];
        }else{
            $this->data['paymenttotal']='';
        }
        unset($this->session->data['main_payment_amt']);
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/card_payment_epos.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/card_payment_epos.tpl';
        } else {
            $this->template = 'default/template/payment/card_payment_epos.tpl';
        }

        $this->render();
    }
}

?>