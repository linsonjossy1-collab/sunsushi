<?php
class ControllerPaymentReturnpayment extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['continue'] = $this->url->link('epos/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/return_payment.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/return_payment.tpl';
		} else {
			$this->template = 'default/template/payment/return_payment.tpl';
		}	
		
		$this->render();
	}
	
	public function confirm() {
		$this->load->model('checkout/posorder');
		//echo "Test";
		$this->model_checkout_posorder->confirm($this->session->data['order_id'], $this->config->get('return_payment_order_status_id'));
                //echo "Test 2";
	}
}
?>