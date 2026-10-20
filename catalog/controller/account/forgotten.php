<?php
class ControllerAccountForgotten extends Controller {
	private $error = array();

	public function index() {
		if ($this->customer->isLogged()) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

		$this->language->load('account/forgotten');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/customer');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->language->load('mail/forgotten');
			
			$password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);
			
			$this->model_account_customer->editPassword($this->request->post['email'], $password);
			
			$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));
			
                        /*
			$message  = sprintf($this->language->get('text_greeting'), $this->config->get('config_name')) . "\n\n";
			$message .= $this->language->get('text_password') . "\n\n";
			$message .= $password."\n\n";
                        $message .=$this->language->get('text_thanks')."\n";
                        $message .=$this->config->get('config_name');
                        */
                        $template = new Template();
                
                $template->data['title'] = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));
                $template->data['text_password'] =  $this->language->get('text_password');
                $template->data['password'] =  $password;
                $template->data['text_thanks'] =  $this->language->get('text_thanks');
                
                
            
            $template->data['text_storephone'] = $this->language->get('text_storephone') . $this->config->get('config_telephone');
            $template->data['logo'] = HTTP_IMAGE . $this->config->get('config_logo');
            $template->data['store_name'] = $this->config->get('config_name');
            $template->data['store_address'] = $this->config->get('config_address');
            if($this->config->get('config_facebook_url')){
                $template->data['fb_url'] = $this->config->get('config_facebook_url');
            }
            if($this->config->get('config_twitter_url')){
                $template->data['twitter_url'] = $this->config->get('config_twitter_url');
            }
            if($this->config->get('config_linkedin_url')){
                $template->data['linkedin_url'] = $this->config->get('config_linkedin_url');
            }
            if($this->config->get('config_gplus_url')){
                $template->data['gplus_url'] = $this->config->get('config_gplus_url');
            }
            //config_instagram_url
             if($this->config->get('config_instagram_url')){
                $template->data['instagram_url'] = $this->config->get('config_instagram_url');
            }
            if($this->config->get('config_iphone_appurl')){
                $template->data['iphoneapp_url'] = $this->config->get('config_iphone_appurl');
            }
            if($this->config->get('config_android_appurl')){
                $template->data['androidapp_url'] = $this->config->get('config_android_appurl');
            }
            if($this->config->get('config_windows_appurl')){
                $template->data['windows_appurl'] = $this->config->get('config_windows_appurl');
            }
            $template->data['header_image'] = HTTP_IMAGE . $this->config->get('config_email_header');
            $template->data['offer_image'] = HTTP_IMAGE . $this->config->get('config_email_offer');
            $template->data['email_register_header'] = HTTP_IMAGE . $this->config->get('config_email_register_header');
            $template->data['email_forgotten_header'] = HTTP_IMAGE . $this->config->get('config_email_forgotten_header');
            $template->data['footer_image'] = HTTP_IMAGE . $this->config->get('config_email_footer');
            $template->data['telephone'] = $this->config->get('config_telephone');
            $template->data['store_url'] = $this->config->get('config_url');
            
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/forgotten.tpl')) {
                $html = $template->fetch($this->config->get('config_template') . '/template/mail/forgotten.tpl');
            } else {
                $html = $template->fetch('default/template/mail/forgotten.tpl');
            }
            
			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');				
			$mail->setTo($this->request->post['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			//$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                        $mail->setHtml($html);
			$mail->send();
			
			$this->session->data['success'] = $this->language->get('text_success');
                        $this->data['text_message']=$this->language->get('text_message');
			//$this->redirect($this->url->link('account/login', '', 'SSL'));
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
        	'text'      => $this->language->get('text_forgotten'),
			'href'      => $this->url->link('account/forgotten', '', 'SSL'),       	
        	'separator' => $this->language->get('text_separator')
      	);
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_your_email'] = $this->language->get('text_your_email');
		$this->data['text_email'] = $this->language->get('text_email');

		$this->data['entry_email'] = $this->language->get('entry_email');

		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_back'] = $this->language->get('button_back');
		$this->data['text_rest_password'] = $this->language->get('text_rest_password');
		$this->data['text_email_help'] = $this->language->get('text_email_help');
		$this->data['button_submit'] = $this->language->get('button_submit');
		$this->data['error_customer_notexists'] = $this->language->get('error_customer_notexists');
		$this->data['text_email_sent'] = $this->language->get('text_email_sent');
		$this->data['text_email_sent_help'] = $this->language->get('text_email_sent_help');
		$this->data['button_processing'] = $this->language->get('button_processing');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		$this->data['action'] = $this->url->link('account/forgotten', '', 'SSL');
 
		$this->data['back'] = $this->url->link('account/login', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/forgot_password.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/forgot_password.tpl';
		} else {
			$this->template = 'default/template/account/forgot_password.tpl';
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

        public function validateReset() {
            $json = array();
		
		$this->load->model('account/customer');
		$this->language->load('account/forgotten');
		
		if (!isset($this->request->post['email'])) {
			$json['error']['warning'] = $this->language->get('error_email');
		} elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$json['error']['warning'] = $this->language->get('error_email');
		} else {
			// Add to activity log
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

			if ($customer_info) {
				$this->load->model('account/auth');

				### Generate an auth token
				$auth_token = "";
				while($auth_token=="") 
				{
					$auth_token = $this->model_account_auth->generateRandomString(32);

					// Verify it's unique and insert into auth table
					if(!$this->model_account_auth->unique_auth_token($auth_token)) 
					{
						// Not unique, reset and try again
						$auth_token = "";
					}
				}

				$this->model_account_auth->insert_auth_token($auth_token, $customer_info['customer_id']);  
				
	
				## Send the email with the Reset Pwd link

				$subject = sprintf($this->language->get('mail_subject'), $this->config->get('config_name'));
	 
				$message  = sprintf($this->language->get('text_greeting'), $this->config->get('config_name')) . "\n\n";
				
				#$message .= sprintf($this->language->get('mail_click_reset'), $this->url->link('account/forgotten/resetPassword&auth='.$auth_token, '', 'SSL')) . "\n\n";
				$message .= $this->language->get('mail_click_reset') . "\n\n";
				$message .= $this->url->link('account/forgotten/resetPassword&auth='.$auth_token, '', 'SSL'). "\n\n\n";

				$message .=$this->language->get('text_thanks')."\n";
				$message .=$this->config->get('config_name');
						   
				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->hostname = $this->config->get('config_smtp_host');
				$mail->username = $this->config->get('config_smtp_username');
				$mail->password = $this->config->get('config_smtp_password');
				$mail->port = $this->config->get('config_smtp_port');
				$mail->timeout = $this->config->get('config_smtp_timeout');				
				$mail->setTo($this->request->post['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($this->config->get('config_name'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setText($message, ENT_QUOTES, 'UTF-8');
				$mail->send();
				
				$json['success'] = "success";

			}
			else
			{
				$json['error']['warning'] = $this->language->get('error_email');
			}
		}

            $this->response->setOutput(json_encode($json));
	}
        
        public function resetPassword() {
		if ($this->customer->isLogged()) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
		
		$verification_code = $this->request->get['auth'];
		if (!$verification_code) {
			$this->redirect($this->url->link('common/home', '', 'SSL'));
		}
		
		$this->load->model('account/auth');
		$result = $this->model_account_auth->checkVerificationcode($verification_code);	//checking user verification code exists in db
		
		if($result)
		{
			
			#$this->model_account_auth->updateVerificationAttempt($verification_code,$result['customer_auth_attempts']+1);
			#$success = $this->frontend_user_model->updateVerificationCodestatus($verification_code); //updating verification code status
		
			$this->language->load('account/forgotten');
	
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
				'text'      => $this->language->get('title_reset_password'),
				'href'      => $this->url->link('account/resetPassword', '', 'SSL'),       	
				'separator' => $this->language->get('text_separator')
			);
			
			$this->data['heading_title'] = $this->language->get('text_rest_password');
			$this->data['text_new_password'] = $this->language->get('text_new_password');
			$this->data['title_confirm_password'] = $this->language->get('title_confirm_password');
			$this->data['button_reset_password'] = $this->language->get('button_reset_password');
			$this->data['text_password_hasbeen_reset'] = $this->language->get('text_password_hasbeen_reset');
			$this->data['text_password_hasbeen_reset_help'] = sprintf($this->language->get('text_password_hasbeen_reset_help'), $this->url->link('account/account', '', 'SSL'), $this->url->link('checkout/checkoutetm', '', 'SSL')); 
			$this->data['text_rest_password'] = $this->language->get('text_rest_password');
			$this->data['button_processing'] = $this->language->get('button_processing');
			
			$this->data['customer_id'] = $result['customer_auth_customer_id'];
			$this->data['verification_code'] = $verification_code;
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/reset_password.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/account/reset_password.tpl';
			} else {
				$this->template = 'default/template/account/reset_password.tpl';
			}
			
			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'	
			);
								
		}	else {
			$this->redirect($this->url->link('common/home', '', 'SSL'));
		}
			
		$this->response->setOutput($this->render());			   
	}
        
        public function validateNewPassword() {
            $json = array();

		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('account/account', '', 'SSL');			
		}

		$this->load->model('account/customer');
		$this->language->load('account/forgotten');

		if (!isset($this->request->post['newpassword'])) {
			$json['error']['warning'] = 'New Password is required'; #$this->language->get('error_email');
		}
		if (!isset($this->request->post['confirmpassword'])) {
			$json['error']['warning'] = 'Confirm Password is required'; #$this->language->get('error_email');
		} elseif ($this->request->post['confirmpassword'] != $this->request->post['newpassword']) {
			$json['error']['warning'] = 'Password and its confirm do not match'; #$this->language->get('error_email');
		} 

		
		if(!$json) 
		{
			$this->load->model('account/auth');
		
			$verification_code = $this->request->post['verification_code'];
			$result = $this->model_account_auth->checkVerificationcode($verification_code);	//checking user verification code exists in db
			
			if($result)
			{
				
				$this->model_account_auth->updateVerificationAttempt($verification_code, $result['customer_auth_attempts']+1);
				
				$customer_id = $result['customer_auth_customer_id'];
				$customer_details = $this->model_account_customer->getCustomer($customer_id);
		
				$password = $this->request->post['newpassword'];
				$this->model_account_customer->editPassword($customer_details['email'], $password);
			
				$success = $this->model_account_auth->updateVerificationCodestatus($verification_code); //updating verification code status

			
				$json['success'] = "success";
			}
			else
			{
				$json['error'] = "error";
			}
		}
		else
		{
			$json['error'] = "error";
		}
		
            $this->response->setOutput(json_encode($json));
	}
        
	private function validate() {
		if (!isset($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_email');
		} elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_email');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>