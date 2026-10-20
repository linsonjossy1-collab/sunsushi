<?php
class ControllerPaymentJusteatCard extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['continue'] = $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/justeat_card.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/justeat_card.tpl';
		} else {
			$this->template = 'default/template/payment/justeat_card.tpl';
		}	
		
		$this->render();
	}
	
	public function confirm() {
		$this->load->model('checkout/order');
		// echo "Test";
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('justeat_card_order_status_id'));
                // echo "Test 2";
	}
        protected function epos() {
            $this->data['button_confirm'] = $this->language->get('button_confirm');
                $this->data['paymentcode'] = $this->session->data['payment_method']['code'];
		$this->data['continue'] = $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/justeat_card_epos.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/justeat_card_epos.tpl';
		} else {
			$this->template = 'default/template/payment/justeat_card_epos.tpl';
		}	
		
		$this->render();
        }
}
?>