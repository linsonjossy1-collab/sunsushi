<?php

class ControllerPaymentSplitpayment extends Controller {

    protected function index() {
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['continue'] = $this->url->link('epos/success');
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/split_payment.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/split_payment.tpl';
        } else {
            $this->template = 'default/template/payment/split_payment.tpl';
        }

        $this->render();
    }

   
    public function confirm() {
        $this->load->model('checkout/order');
        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('split_payment_order_status_id'));
    }
    
    protected function epos() {
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['continue'] = $this->url->link('epos/success');
        $this->data['paymentcode'] = $this->session->data['payment_method']['code'];
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/split_payment_epos.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/split_payment_epos.tpl';
        } else {
            $this->template = 'default/template/payment/split_payment_epos.tpl';
        }

        $this->render();
    }
    
}

?>