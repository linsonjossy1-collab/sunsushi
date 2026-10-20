<?php 
class ModelModuleGrecaptcha extends Model {
    private $error = array();
    public function recaptchaverify($post) {
        if($this->config->get('grecaptcha_site_key')) {  
            if(isset($post['g-recaptcha-response'])) {
                $data = array('secret'=>$this->config->get('grecaptcha_secret_key'),'response'=>$post['g-recaptcha-response']);
               // $this->log->write(print_r($post,true));
                $ch = curl_init('https://www.google.com/recaptcha/api/siteverify'); 

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $json = curl_exec($ch); 
                // $this->log->write($json);
                $output = json_decode($json,true);
               // $this->log->write(print_r($output,true));
                curl_close($ch); 
                if(!isset($output['success']) || $output['success']!= true) {
                    $this->error['grecaptcha'] =' Not verify ';
                }
            }
            else {
                $this->error['grecaptcha'] = " No recaptcha";
            }
        }
        else {
            if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $post['captcha'])) {
                $this->error['captcha'] = $this->language->get('error_captcha');
            }
        }
        if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
        
    }
    
}
?>