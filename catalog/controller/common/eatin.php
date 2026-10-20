<?php

class ControllerCommonEatin extends Controller {

    public function index() {
        //Check Location
        if(!$this->config->get('config_mobileapi_dine_menu')){
            //$this->redirect($this->config->get('config_url').$menu.'/#');
        }
        $this->document->setTitle($this->config->get('config_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));

        $this->data['heading_title'] = $this->config->get('config_title');
        $this->load->model('setting/setting');
        $this->data['store_tables']=$this->model_setting_setting->getStoretables();
        if(isset($this->request->get['br'])){
            $maincat_id =99;//$this->config->get('config_mobileapi_dine_menu');
        }else{
            $maincat_id =$this->config->get('config_mobileapi_dine_menu');
        }
        $maincat_id =$this->config->get('config_mobileapi_dine_menu');
        //$this->session->data['dine_table_id'] =5;
        //$this->session->data['delivery_method'] = 'dine.dine'; 
        if(isset($this->request->get['tableid'])){
            if(isset($this->request->get['people'])){
                $this->session->data['people_count'] = $this->request->get['people'];
            }
            $this->session->data['tableordering'] = $this->data['store_tables'][$this->request->get['tableid']]['name'];
            $this->session->data['dine_table_id'] = $this->request->get['tableid'];
            $this->session->data['delivery_method'] = 'dine.dine';
            
            $this->load->model('catalog/category');
            $menu = $this->model_catalog_category->getMainMenuUrls($maincat_id);
            //config_mobileapi_dine_menu
            $this->redirect($this->config->get('config_url').$menu.'/#');
            //Find Menu && redirct
            
        }
        
        
        $this->data['action'] = $this->config->get('config_url').'eatin/';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/eatinhome.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/eatinhome.tpl';
        } else {
            $this->template = 'default/template/common/eatinhome.tpl';
        }
        date_default_timezone_set('Europe/London');
        $this->children = array(
            'common/footer',
            'common/header',            
        );

        $this->response->setOutput($this->render());
        
    }
    public function test() {
        echo "die";
    }

}

?>