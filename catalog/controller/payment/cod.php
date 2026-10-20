<?php
class ControllerPaymentCod extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['continue'] = $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cod.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/cod.tpl';
		} else {
			$this->template = 'default/template/payment/cod.tpl';
		}	
		
		$this->render();
	}
	
	public function confirm() {
		$this->load->model('checkout/order');
		// echo "Test";
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cod_order_status_id'));
                // echo "Test 2";
	}
        protected function epos() {
            $this->data['button_confirm'] = $this->language->get('button_confirm');
                $this->data['paymentcode'] = $this->session->data['payment_method']['code'];
		$this->data['continue'] = $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cod_epos.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/cod_epos.tpl';
		} else {
			$this->template = 'default/template/payment/cod_epos.tpl';
		}	
		
		$this->render();
        }
}
?>