<?php  
class ControllerModuleGallery extends Controller {
	protected function index($setting) {
	
		static $module = 0;
		$this->load->language('information/gallery');
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->document->setTitle($this->language->get('heading_title'));
					
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/gallery.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/gallery.tpl';
		} else {
			$this->template = 'default/template/module/gallery.tpl';
		}
		
		$this->render();
	}
}
?>