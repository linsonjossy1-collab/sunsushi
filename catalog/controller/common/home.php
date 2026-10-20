<?php

class ControllerCommonHome extends Controller {

    public function index() {
         if(isset($this->session->data['offerordering'])){
            $this->redirect($this->config->get('config_url').'offers/#');
        }
        $this->document->setTitle($this->config->get('config_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));

        $this->data['heading_title'] = $this->config->get('config_title');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/home.tpl';
        } else {
            $this->template = 'default/template/common/home.tpl';
        }
        date_default_timezone_set('Europe/London');
        $uk = date('H:i:A');
        $today = date('l');
        $starts = strtolower($today);
        $start = $starts . '_time_start';
        $end = $starts . '_time_end';
        $start_time = $this->config->get($start);
        $end_time = $this->config->get($end);
        $uk_start = explode(':', $uk);
        $uk_tm = $uk_start[2];
        $uk_tme = (float) $uk_start[0] . '.' . $uk_start[1];
        if ($uk_tm == 'AM') {
            $start_tm = explode('AM', $start_time);
            $start_tms = (float) $start_tm[0];

            if ($uk_tme < 12) {
                if ($uk_tme < $start_tms) {
                    $time_flag = 0;
                } else {
                    $time_flag = 1;
                }
            } else {
                if ($uk_tme > $start_tms) {
                    $time_flag = 0;
                } else {
                    $time_flag = 1;
                }
            }
        } else {
            $end_tm = explode('PM', $end_time);
            $end_tms = $end_tm[0];
            $start_tm = explode('AM', $start_time);
            $start_tms = (float) $start_tm[0];
            if ($uk_tme < 12) {
                if ($uk_tme > $start_tms) {
                    $time_flag = 0;
                } else {
                    $time_flag = 1;
                }
            } else {
                if ($uk_tme < $start_tms) {
                    $time_flag = 0;
                } else {
                    $time_flag = 1;
                }
            }
        }
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
        $this->data['postcodebutton'] = $this->getChild('common/postcode/button');
        $this->data['postcodehome'] = $this->getChild('common/postcode/home');
         $this->data['socialmediabuttons'] = $this->getChild('common/postcode/socialmediabuttons');
        //$this->data['facebookbutton'] = $this->getChild('common/postcode/facebookbutton');

        // Postcode search
        $this->data['time_flag'] = $time_flag;
        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header',
            'module/homecart'
        );

        $this->response->setOutput($this->render());
    }
    public function distancecheck(){
       echo $this->postcode->getDistance('HG1 2BP', 'LS10 4JW');
    }

}

?>