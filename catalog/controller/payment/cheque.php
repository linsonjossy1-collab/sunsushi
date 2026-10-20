<?php

class ControllerPaymentCheque extends Controller {

    protected function index() {
        $this->language->load('payment/cheque');

        $this->data['text_instruction'] = $this->language->get('text_instruction');
        $this->data['text_payable'] = $this->language->get('text_payable');
        $this->data['text_address'] = $this->language->get('text_address');
        $this->data['text_payment'] = $this->language->get('text_payment');

        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['payable'] = $this->config->get('cheque_payable');
        $this->data['address'] = nl2br($this->config->get('config_address'));

        $this->data['continue'] = $this->url->link('checkout/success');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cheque.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/cheque.tpl';
        } else {
            $this->template = 'default/template/payment/cheque.tpl';
        }

        $this->render();
    }

     protected function epos() {
        $this->language->load('payment/cheque');

        $this->data['text_instruction'] = $this->language->get('text_instruction');
        $this->data['text_payable'] = $this->language->get('text_payable');
        $this->data['text_address'] = $this->language->get('text_address');
        $this->data['text_payment'] = $this->language->get('text_payment');

        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['payable'] = $this->config->get('cheque_payable');
        $this->data['address'] = nl2br($this->config->get('config_address'));

        $this->data['continue'] = $this->url->link('checkout/success');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cheque_epos.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/cheque_epos.tpl';
        } else {
            $this->template = 'default/template/payment/cheque_epos.tpl';
        }

        $this->render();
    }
    public function confirm() {
        $this->language->load('payment/cheque');

        $this->load->model('checkout/order');

        $comment = $this->language->get('text_payable') . "\n";
        $comment .= $this->config->get('cheque_payable') . "\n\n";
        $comment .= $this->language->get('text_address') . "\n";
        $comment .= $this->config->get('config_address') . "\n\n";
        $comment .= $this->language->get('text_payment') . "\n";

        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cheque_order_status_id'), $comment, true);
    }

    public function subpaymentdetails() {
        unset($this->session->data['subpayment_status']);
        if (isset($this->request->post['sub_payment_amount']) and $this->request->post['sub_payment_amount'] != '') {//total
            $cash = $this->currency->format((float) $this->request->post['sub_payment_amount']);
            $main_cash = $this->currency->format((float) $this->request->post['main_payment_amount']);
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $this->session->data['order_id'] . "', code = 'cashtotal', title = 'Cash', text = '" . $cash . "', `value` = '".$this->currency->format((float)$this->request->post['sub_payment_amount'],'','',false)."', sort_order = '" . (int) $this->config->get('cashtotal_sort_order') . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $this->session->data['order_id'] . "', code = 'cashtotal', title = 'Cheque', text = '" . $main_cash . "', `value` = '".$this->currency->format((float)$this->request->post['main_payment_amount'],'','',false)."', sort_order = '" . (int) $this->config->get('cashtotal_sort_order') . "'");
        }
    }

}

?>