<?php

class ControllerAccountDeleteaccount extends Controller {

    private $error = array();

    public function index() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/edit', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->language->load('account/deleteaccount');

        $this->document->setTitle($this->language->get('heading_title'));
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->model('account/deleteaccount');
            $this->model_account_deleteaccount->deleteCustomer($this->customer->getId());
            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }
        if($this->error) {
            $this->data['error_agrement'] =  $this->error['error_agrement'];
        }
        else {
            $this->data['error_agrement'] = '';
        }
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_edit'),
            'href' => $this->url->link('account/deleteaccount', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_your_details'] = $this->language->get('text_your_details');
        $this->data['text_your_agree'] = $this->language->get('text_your_agree');
       

        $this->data['button_delete'] = $this->language->get('button_delete');
        $this->data['button_back'] = $this->language->get('button_back');

        

        $this->data['action'] = $this->url->link('account/deleteaccount', '', 'SSL');

       

        $this->data['back'] = $this->url->link('account/account', '', 'SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/deleteaccount.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/deleteaccount.tpl';
        } else {
            $this->template = 'default/template/account/edit.tpl';
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

    private function validate() {
        if (!$this->request->post['agree']) {
            $this->error['error_agrement'] = $this->language->get('error_agrement');
        }
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}

?>