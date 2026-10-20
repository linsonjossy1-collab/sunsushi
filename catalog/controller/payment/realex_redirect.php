<?php

class ControllerPaymentRealexRedirect extends Controller {

	protected function index() {

		$this->language->load('payment/realex_redirect');
		$this->load->model('checkout/order');
		
	    $this->data['button_confirm'] = $this->language->get('button_confirm');
		
		 // Merchant ID
          $merchant_id = $this->config->get('realex_redirect_marchant');
         // Shared secret
          $secret_key = $this->config->get('realex_redirect_key');
		  
		  $timestamp = strftime("%Y%m%d%H%M%S");
          mt_srand((double)microtime()*1000000);
		  
		  $order_id=$this->session->data['order_id'];
		  

          $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		  
		  $curr = $order_info['currency_code'];
          $amount =$this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false)*100;
		  
		  $tmp = "$timestamp.$merchant_id.$order_id.$amount.$curr";
		  $md5hash = md5($tmp);
	      $tmp = "$md5hash.$secret_key";
		  $md5hash = md5($tmp);
		
		$this->data['md5hash'] = $md5hash;
		$this->data['order_id'] = $order_id;
		$this->data['amount'] = $amount;
		$this->data['curr'] = $curr;
		$this->data['merchant_id'] = $merchant_id;
		$this->data['timestamp'] = $timestamp;
		$this->data['realex_mode'] = $this->config->get('realex_mode');
		
		
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/realex_redirect.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/realex_redirect.tpl';
		} else {
			$this->template = 'default/template/payment/realex_redirect.tpl';
		}	

		$this->render();
	  
	}
	
	

	public function callback() {
		
		
		$timestamp = $this->request->post['TIMESTAMP'];
		$result = $this->request->post['RESULT'];
		$order_id = $this->request->post['ORDER_ID'];
		$message = $this->request->post['MESSAGE'];
		$authcode = $this->request->post['AUTHCODE'];
		$pasref = $this->request->post['PASREF'];
		$realexmd5 = $this->request->post['MD5HASH'];
		
		// Merchant ID
		  $merchant_id = $this->config->get('realex_redirect_marchant');
		 // Shared secret
		  $secret_key = $this->config->get('realex_redirect_key');
		  
		$tmp = "$timestamp.$merchant_id.$order_id.$result.$message.$pasref.$authcode";
		$md5hash = md5($tmp);
		$tmp = "$md5hash.$secret_key";
		$md5hash = md5($tmp);
		
		//Check to see if hashes match or not
		if ($md5hash != $realexmd5) {
			$this->log->write('Realex Redirect :: Callback : hashes don\'t match - response not authenticated!');
			return false;
		}  
		
		
		$this->load->model('checkout/order');
			
		$order_info = $this->model_checkout_order->getOrder($order_id);
		if ($order_info) {
			
			 if ($result == "00") {
			    $this->model_checkout_order->confirm($order_id, $this->config->get('realex_redirect_order_status_id'));
				echo "Thank you. Your transaction was successful.
					<br/><br/>
					You will be redirected to merchant site now. If it does not automatically redirect, please <a href='".$this->url->link('checkout/success')."'><b><u>click here</u></b></a> to complete your order.
					<br/><br/>
					 <script>
					   window.location = '".$this->url->link('checkout/success')."';
					 </script>
					";
			  }
			else
			{
			  $this->log->write('Realex Redirect :: Callback : Message: '.$message.' ('.$authcode.')');
			  echo "There was an error processing your subscription.  
To try again please <a href='".HTTP_SERVER."'><b><u>click here</u></b></a><br><BR>
Please contact our customer care department at <a href='mailto:".$this->config->get('config_email')."'><b><u>".$this->config->get('config_email')."</u></b></a>";
			}  	
		}
			
	}

}

?>