<?php

class ControllerInformationContact extends Controller {

    private $error = array();

    public function index() {
        $this->language->load('information/contact');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/contact'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_location'] = $this->language->get('text_location');
        $this->data['text_contact'] = $this->language->get('text_contact');
        $this->data['text_address'] = $this->language->get('text_address');
        $this->data['text_telephone'] = $this->language->get('text_telephone');
        $this->data['text_fax'] = $this->language->get('text_fax');

        

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['action'] = $this->url->link('information/contact');
        $this->data['store'] = $this->config->get('config_name');
        $this->data['address'] = nl2br($this->config->get('config_address'));
        $this->data['telephone'] = $this->config->get('config_telephone');
        $this->data['fax'] = $this->config->get('config_fax');
                    $this->load->model('setting/setting');
            $this->data['store_times']=$this->model_setting_setting->getStoretimes();
            $this->data['delivery_details']=$this->model_setting_setting->getDdeliverydetails();
        $this->data['postalcode'] = $this->config->get('config_postalcode');
        $this->data['contact_email'] = $this->config->get('config_contactus_email');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/contact.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/information/contact.tpl';
        } else {
            $this->template = 'default/template/information/contact.tpl';
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

    public function success() {
        $this->language->load('information/contact');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/contact'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_message'] = $this->language->get('text_message');

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['continue'] = $this->url->link('common/home');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/success.tpl';
        } else {
            $this->template = 'default/template/common/success.tpl';
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
