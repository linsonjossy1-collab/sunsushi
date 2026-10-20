<?php 
class ControllerSubscribeSubscribe extends Controller {
	private $error = array();
	  
  	public function index() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE newsletter IN(1,2,3,5,6,7) AND status ='1'");				
		$flag = false;
		$subscription_msg = '';
				
        if(isset($this->request->get['token']) || isset($_POST['email']))
        {
			$token = '';
			$mail_hash = '';
			$option_hash = '';
			if($this->request->get['token'])
			{
				$response  		=  	explode('____',$this->request->get['token']);
				$mail_hash 		= 	$response['0'];
				$option_hash 	= 	$response['1'];
				$token = $this->request->get['token'];
			}

			if($query->num_rows)
			{
				foreach ($query->rows as $result) 
				{
					$email 		= $result['email'];//$result['subscription_mail'];
					$id  		= $result['customer_id'];//$result['subscription_id'];
					$subscription_status = $result['newsletter'];
					
					if($mail_hash == md5($email) || $email == $_POST['email'])
					{					
						//sms case not active
						$new_status = '';
						if($option_hash != '')
						{
							if($subscription_status <= 3){
								if($option_hash == md5('news_letter'))
								{
									if($subscription_status == 0){
										$new_status = 0;
									}
									if($subscription_status == 1){
										$new_status = 0;
									}
									if($subscription_status == 2){
										$new_status = 2;
									}
									if($subscription_status == 3){
										$new_status = 2;
									}

								}
								if($option_hash == md5('unsubscribe_mail'))
								{
									if($subscription_status == 0){
										$new_status = 0;
									}
									if($subscription_status == 1){
										$new_status = 1;
									}
									if($subscription_status == 2){
										$new_status = 0;
									}
									if($subscription_status == 3){
										$new_status = 1;
									}
								}
								
							}
							//sms case active	
							if($subscription_status <= 7 && $subscription_status > 3){
								if($option_hash == md5('news_letter'))
								{
									if($subscription_status == 4){
										$new_status = 4;
									}
									if($subscription_status == 5){
										$new_status = 4;
									}
									if($subscription_status == 6){
										$new_status = 6;
									}
									if($subscription_status == 7){
										$new_status = 6;
									}

								}
								if($option_hash == md5('unsubscribe_mail'))
								{
									if($subscription_status == 4){
										$new_status = 4;
									}
									if($subscription_status == 5){
										$new_status = 5;
									}
									if($subscription_status == 6){
										$new_status = 4;
									}
									if($subscription_status == 7){
										$new_status = 5;
									}
								}
							}
						}
						else
						{
							$new_status = '';
							if($subscription_status <= 3){
								$new_status = 0;								
							}
							//sms case active	
							if($subscription_status <= 7 && $subscription_status > 3){
								$new_status = 4;
							}
						}
						$flag = true;
						$query_update = "UPDATE `customer` SET `newsletter` = ".(int)$new_status." WHERE customer_id ='".$id."'";
						$query = $this->db->query($query_update);
						$subscription_msg = 'You have unsubscribed successfully.';
					}
				}
			}
			else{
				$subscription_msg = 'Some error occured or you have not subscribed yet!. Please try again later.';
			}
		}
    
        $this->data['home'] = $this->url->link('common/home');
        $this->data['action'] = $this->url->link('subscribe/subscribe');
		$this->document->setTitle('Unsubscribe');
		$this->data['heading_title'] = 'Unsubscribe';
		$this->data['update_status'] =$flag;
        $this->data['subscription_msg'] =$subscription_msg;

		$this->template = 'default/template/subscribe/subscribe.tpl';
		$this->children = array(
			'common/footer',
			'common/header'
		);
		$this->response->setOutput($this->render());
	}
	public function subscription()
	{
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('subscribe/subscribe/subscription', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		} 
		$this->language->load('subscribe/subscribe');

		$this->document->setTitle($this->language->get('heading_title'));
				
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('account/customer');
			if(isset($this->request->post['newsletter']) && isset($this->request->post['mail_subscription']) && isset($this->request->post['sms_subscription']))
			{
				
				// case for 1
				$subscription_value = '';
				if($this->request->post['sms_subscription'] == 0){
					if($this->request->post['mail_subscription'] == 0){
						if($this->request->post['newsletter'] == 0){
							$subscription_value = 0; 						// case for 0
						}
						if($this->request->post['newsletter'] == 1){
							$subscription_value = 1;						// case for 1						
						}
					}
					if($this->request->post['mail_subscription'] == 1){
						if($this->request->post['newsletter'] == 0){
							$subscription_value = 2;						// case for 2
						}
						if($this->request->post['newsletter'] == 1){
							$subscription_value = 3;						// case for 3	
						}
					}
				}
				if($this->request->post['sms_subscription'] == 1){
					if($this->request->post['mail_subscription'] == 0){
						if($this->request->post['newsletter'] == 0){
							$subscription_value = 4;						// case for 4
						}
						if($this->request->post['newsletter'] == 1){
							$subscription_value = 5;						// case for 5			
						}
					}
					if($this->request->post['mail_subscription'] == 1){
						if($this->request->post['newsletter'] == 0){
							$subscription_value = 6;						// case for 6
						}
						if($this->request->post['newsletter'] == 1){
							$subscription_value = 7;						// case for 7				
						}
					}

				}
			}

			$this->model_account_customer->editNewsletter($subscription_value);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

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
        	'text'      => $this->language->get('text_subscribe'),
			'href'      => $this->url->link('subscribe/subscribe/subscription', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		
		$this->data['entry_subscription'] = $this->language->get('entry_subscription');
		
		$this->data['entry_subscription_newsletter'] 	= $this->language->get('entry_subscription_newsletter');
		$this->data['entry_subscription_mail'] 			= $this->language->get('entry_subscription_mail');
		$this->data['entry_subscription_sms'] 			= $this->language->get('entry_subscription_sms');
		
		
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_back'] = $this->language->get('button_back');

    	$this->data['action'] = $this->url->link('subscribe/subscribe/subscription', '', 'SSL');
		
		$this->data['newsletter'] = $this->customer->getNewsletter();
		
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/subscribe/subscription.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/subscribe/subscription.tpl';
		} else {
			$this->template = 'default/template/subscribe/subscription.tpl';
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
}
?>