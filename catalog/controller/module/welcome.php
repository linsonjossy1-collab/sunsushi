<?php  
class ControllerModuleWelcome extends Controller {
	protected function index($setting) {
		$this->language->load('module/welcome');
		
    	$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
                
                $this->load->model('tool/systemtag');
                $description = $this->model_tool_systemtag->replace($setting['description'][$this->config->get('config_language_id')]);
            
		$this->data['message'] = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
                $orgn =array('{DELIVERY_TIME}','{COLLECTION_TIME}'); 
                $repalce = array($this->config->get('home_delivery_storetime'),$this->config->get('collect_in_storetime'));
                $this->data['message'] = str_replace($orgn, $repalce, $this->data['message']);
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/welcome.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/welcome.tpl';
		} else {
			$this->template = 'default/template/module/welcome.tpl';
		}
		
		$this->render();
	}
}
?>