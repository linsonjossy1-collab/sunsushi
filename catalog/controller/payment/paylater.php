<?php
class ControllerPaymentPaylater extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['continue'] = $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paylater.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/paylater.tpl';
		} else {
			$this->template = 'default/template/payment/paylater.tpl';
		}	
		
		$this->render();
	}
	
	public function confirm() {
		$this->load->model('checkout/order');
		// echo "Test";
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('paylater_order_status_id'));
                // echo "Test 2";
	}     
}
?>