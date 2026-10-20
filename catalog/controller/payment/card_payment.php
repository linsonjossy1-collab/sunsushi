<?php

class ControllerPaymentCardpayment extends Controller {

    protected function index() {
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['continue'] = $this->url->link('epos/success');
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/card_payment.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/card_payment.tpl';
        } else {
            $this->template = 'default/template/payment/card_payment.tpl';
        }

        $this->render();
    }

    public function submitdetails() {
        
        $this->load->model('checkout/order');
        $this->data['continue'] = $this->url->link('epos/success');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $total = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
        $this->data['action']='';
         if(isset($this->request->post['subpay_amt']) and $this->request->post['subpay_amt']!=''){//
             if((float)$total>((float)$this->request->post['subpay_amt']+(float)$this->request->post['mainpay_amt'])){
                 $this->response->setOutput('');
                 return ;
             }
             $this->data['amount']=(float)$total-(float)$this->request->post['subpay_amt'];
         }else{
             $this->data['amount']=$total;//(float)$this->request->post['total'];
         }
        //Condition for failed and success payment TODO ie,amount substracted 
           //Insert Total if(cashtotal in orderinfo['totals']);
        // 
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/card_payment_data.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/card_payment_data.tpl';
        } else {
            $this->template = 'default/template/payment/card_payment_data.tpl';
        }

         $this->response->setOutput($this->render());
    }
    
    public function confirm() {
        $this->load->model('checkout/order');
        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('card_payment_order_status_id'));
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
    
    public function subpaymentdetails(){
         unset($this->session->data['subpayment_status']);
         if(isset($this->request->post['sub_payment_amount']) and $this->request->post['sub_payment_amount']!=''){//total
             $cash = $this->currency->format((float)$this->request->post['sub_payment_amount']);
             $main_cash = $this->currency->format((float)$this->request->post['main_payment_amount']);
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $this->session->data['order_id'] . "', code = 'cashtotal', title = 'Cash', text = '" . $cash . "', `value` ='".$this->currency->format((float)$this->request->post['sub_payment_amount'],'','',false)."', sort_order = '" . (int) $this->config->get('cashtotal_sort_order') . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $this->session->data['order_id'] . "', code = 'cashtotal', title = 'Card Payment', text = '" . $main_cash . "', `value` = '".$this->currency->format((float) $this->request->post['main_payment_amount'],'','',false)."', sort_order = '" . (int) $this->config->get('cashtotal_sort_order') . "'");
        }
    }
}

?>