<?php

class ControllerCommonMain extends Controller {

    public function index() {
        $this->document->setTitle($this->config->get('config_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));

        $this->data['title'] = $this->document->getTitle();
        $this->data['description'] = $this->document->getDescription();
        $this->data['keywords'] = $this->document->getKeywords();
        $this->data['links'] = $this->document->getLinks();
        $this->data['styles'] = $this->document->getStyles();
        $this->data['scripts'] = $this->document->getScripts();
        $this->data['lang'] = $this->language->get('code');
        $this->data['direction'] = $this->language->get('direction');
        $this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
       
        $this->language->load('common/header');
        $this->data['text_home'] = $this->language->get('text_home');
        $this->data['home'] = $this->url->link('common/home');
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $server = HTTPS_IMAGE;
        } else {
            $server = HTTP_IMAGE;
        }

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['base'] = $this->config->get('config_ssl');
        } else {
            $this->data['base'] = $this->config->get('config_url');
        }

        if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->data['icon'] = $server . $this->config->get('config_icon');
        } else {
            $this->data['icon'] = '';
        }

        $this->data['name'] = $this->config->get('config_name');

        /* if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
          $this->data['logo'] = $server . $this->config->get('config_logo');
          } else {
          $this->data['logo'] = '';
          } */

        if ($_SESSION['browser_type'] == 'mobiles') {
            if ($this->config->get('config_mobile_browser_logo') && file_exists(DIR_IMAGE . $this->config->get('config_mobile_browser_logo'))) {
                $this->data['logo'] = $server . $this->config->get('config_mobile_browser_logo');
            } else {
                $this->data['logo'] = '';
            }
        } elseif ($_SESSION['browser_type'] == 'tablets') {
            if ($this->config->get('config_tablet_browser_logo') && file_exists(DIR_IMAGE . $this->config->get('config_tablet_browser_logo'))) {
                $this->data['logo'] = $server . $this->config->get('config_tablet_browser_logo');
            } else {
                $this->data['logo'] = '';
            }
        } else {
            if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
                $this->data['logo'] = $server . $this->config->get('config_logo');
            } else {
                $this->data['logo'] = '';
            }
        }

        $this->data['heading_title'] = $this->config->get('config_title');

        // Post code search 
        if (isset($this->session->data['grouporderclient'])) {
            $this->data['grouporder'] = 1;
        } else {
            $this->data['grouporder'] = 0;
        }
        $postcode = '';
        if ($this->data['grouporder'] == 1) {
            $postcode = 'Group Order';
        } else {
            if (isset($_SESSION['postcode'])) {
                $postcode = $_SESSION['postcode'];
            }
        }
        if (isset($_SESSION['nostore'])) {
            $nostore = $_SESSION['nostore'];
        } else {
            $nostore = 0;
        }

        $this->data['postcode'] = $postcode;
        $this->data['nostore'] = $nostore;
        $this->data['name'] = $this->config->get('config_name');
        $this->data['postcodemobile'] = $this->getChild('common/postcode/mobilemain');
        // Postcode search
        date_default_timezone_set('Europe/London');


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/main.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/main.tpl';
        } else {
            $this->template = 'default/template/common/main.tpl';
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