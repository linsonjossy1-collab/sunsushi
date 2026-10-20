<?php
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Order;
use Stripe\Error\InvalidRequest;
use Stripe\Customer;

class ControllerAccountSavedcards extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/savedcards', '', 'SSL');

			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->language->load('account/savedcards');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/customer');

      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),     	
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_edit'),
			'href'      => $this->url->link('account/savedcards', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
		  );
		$this->data['card_detail_text'] = $this->language->get('text_edit');
		$this->data['text_clear_cards'] = $this->language->get('text_clear_cards');
		$this->data['remove_card_save_href'] = $this->url->link('account/savedcards/remove', '', 'SSL');   	
		$savedpayments = $this->customer->getPaymentCards();       
		if(isset($savedpayments['stripe'])){
			  Stripe::setApiKey($this->config->get('stripe_payments_private_key'));
			  $customer = Customer::Retrieve(
				  array("id" => $savedpayments['stripe'], "expand" => array("default_source"))
			  );
			  $this->data['customer_id'] = $customer['id'];
			  $this->data['savedcard_details'] = $customer->default_source->brand . " ending in " . $customer->default_source->last4 ;
		}
		else{
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		$this->data['text_cancel'] = $this->language->get('text_cancel');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/savedcards.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/savedcards.tpl';
		} else {
			$this->template = 'default/template/account/savedcards.tpl';
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

	public function remove()
	{
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/edit', '', 'SSL');

			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->load->model('account/customer');

		$savedpayments = $this->customer->getPaymentCards();       
		if(isset($savedpayments['stripe'])){
			  Stripe::setApiKey($this->config->get('stripe_payments_private_key'));
			  $customer = Customer::Retrieve(
				  array("id" => $savedpayments['stripe'], "expand" => array("default_source"))
			  );

			  if($customer['id'])
			  {
				$this->model_account_customer->removeSavedCard($this->customer->getId());

				$this->redirect($this->url->link('account/account', '', 'SSL'));
			  }
			  else{
				$this->redirect($this->url->link('account/account', '', 'SSL'));
			  }
		}
		else{
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}
}
?>