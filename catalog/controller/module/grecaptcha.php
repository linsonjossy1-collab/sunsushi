<?php
class ControllerModuleGrecaptcha extends Controller {
    private $error = array();
    
    public function index($action) {
        if($this->config->get('grecaptcha_site_key')) {  
            $this->data['grecaptcha_site_key'] = $this->config->get('grecaptcha_site_key'); 
            $this->data['action'] = $action; 
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/grecaptcha.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/module/grecaptcha.tpl';
            } else {
                $this->template = 'default/template/module/grecaptcha.tpl';
            }
            $this->response->setOutput($this->render());
        }
       

    }

    

}
