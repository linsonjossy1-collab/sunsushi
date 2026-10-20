<?php  
class ControllerModuleTablereservation extends Controller {
	private $error = array();

    public function index() {
        $this->language->load('information/tablereservation');

       // $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/tablereservation');

        $this->load->model('tool/image');
        
        $this->data['heading_title'] = $this->language->get('heading_title');

        if($this->config->get('grecaptcha_site_key')) {
            $rt =  explode('/',trim($this->request->get['route']));
            $this->data['grecaptcha'] = $this->getChild('module/grecaptcha/index',$rt[(count($rt)-1)]);
        }
        else {
            $this->data['grecaptcha'] = "";
        }

        $this->data['action'] = $this->url->link('information/tablereservation');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/tablereservation.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/tablereservation.tpl';
        } else {
            $this->template = 'default/template/module/tablereservation.tpl';
        }

        
        $this->response->setOutput($this->render());
    }
    
}
?>