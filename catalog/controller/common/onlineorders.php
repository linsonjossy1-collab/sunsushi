<?php

class ControllerCommonOnlineOrders extends Controller {

    public function index() {
        $json = array();
        if ($this->config->get('config_online_orders')) {
            $json['order_msg'] = 'all_orders';
        } elseif ($this->config->get('config_holiday_online_orders')) {
            if ($this->postcode->isStoreHoliday()) {
                $json['order_msg'] = 'holiday_msg';
            } else {
                $json['order_msg'] = 'no_msg';
            }
        } elseif ($this->config->get('config_store_time_orders')) {
            if (!$this->postcode->isStoreOpened($this->config->get('config_store_id'))) {
                $json['order_msg'] = 'time_msg';
            } else {
                $json['order_msg'] = 'no_msg';
            }
        } else {
            $json['order_msg'] = 'no_msg';
        }
        $this->response->setOutput(json_encode($json));
    }

    public function onlineorderpopup() {
        $message_type = $this->request->post['msg'];
        if ($message_type == 'time_msg') {
            $this->data['popupcontent'] = $this->getChild('common/online_orders/storetime_onlineorderpopup');
        } elseif ($message_type == 'holiday_msg') {
            $this->data['popupcontent'] = $this->getChild('common/online_orders/storeholiday_onlineorderpopup');
        } else {
            $this->data['popupcontent'] = $this->getChild('common/online_orders/no_onlineordermessagepopup');
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/onlineorderpopup.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/onlineorderpopup.tpl';
        } else {
            $this->template = 'default/template/common/onlineorderpopup.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function noonlineordermessagepopup() {
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/no_onlineordermessagepopup.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/no_onlineordermessagepopup.tpl';
        } else {
            $this->template = 'default/template/common/no_onlineordermessagepopup.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function storeholidayonlineorderpopup() {
        $this->load->model('setting/setting');
        $this->data['store_times']=$this->model_setting_setting->getStoretimes();		
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/storeholiday_onlineorderpopup.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/storeholiday_onlineorderpopup.tpl';
        } else {
            $this->template = 'default/template/common/storeholiday_onlineorderpopup.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function storetimeonlineorderpopup() {
        $this->load->model('setting/setting');
        $this->data['store_times']=$this->model_setting_setting->getStoretimes();		
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/storetime_onlineorderpopup.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/storetime_onlineorderpopup.tpl';
        } else {
            $this->template = 'default/template/common/storetime_onlineorderpopup.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function isStoreHoliday() {
        $status = TRUE;
        $current_time = strtotime('now');
        $store_open_times = array();


        $todate = strtotime(date('d-m-Y'));
        $yes_date = strtotime(date("d-m-Y", strtotime('yesterday')));
        $tom_date = strtotime(date("d-m-Y", strtotime('tomorrow')));

        $holidays = $this->config->get('store_holidays');

        $store_holiday_times = array();
        if (isset($holidays[$yes_date])) {
            $yes_hol_start = explode(':', $holidays[$yes_date]['start']);
            $yes_hol_end = explode(':', $holidays[$yes_date]['end']);
            $store_holiday_times[0]['start'] = mktime((int) $yes_hol_start[0], (int) $yes_hol_start[1], 0, date("m"), date("d") - 1, date("Y"));
            $store_holiday_times[0]['end'] = mktime((int) $yes_hol_end[0], (int) $yes_hol_end[1], 0, date("m"), date("d") - 1, date("Y"));

            if ((int) $yes_hol_start[0] > (int) $yes_hol_end[0]) {
                $store_holiday_times[0]['end'] = mktime((int) $yes_hol_end[0], (int) $yes_hol_end[1], 0, date("m"), date("d"), date("Y"));
            }
        }
        if (isset($holidays[$todate])) {
            $tod_hol_start = explode(':', $holidays[$todate]['start']);
            $tod_hol_end = explode(':', $holidays[$todate]['end']);
            $store_holiday_times[1]['start'] = mktime((int) $tod_hol_start[0], (int) $tod_hol_start[1], 0, date("m"), date("d"), date("Y"));
            $store_holiday_times[1]['end'] = mktime((int) $tod_hol_end[0], (int) $tod_hol_end[1], 0, date("m"), date("d"), date("Y"));
            if ((int) $tod_hol_start[0] > (int) $tod_hol_end[0]) {
                $store_open_times[1]['end'] = mktime((int) $tod_hol_end[0], (int) $tod_hol_end[1], 0, date("m"), date("d") + 1, date("Y"));
            }
        }

        if (isset($holidays[$tom_date])) {
            $tom_hol_start = explode(':', $holidays[$tom_date]['start']);
            $tom_hol_end = explode(':', $holidays[$tom_date]['end']);
            $store_holiday_times[2]['start'] = mktime((int) $tom_hol_start[0], (int) $tom_hol_start[1], 0, date("m"), date("d") + 1, date("Y"));

            $store_holiday_times[2]['end'] = mktime((int) $tom_hol_end[0], (int) $tom_hol_end[1], 0, date("m"), date("d") + 1, date("Y"));
            if ((int) $tom_hol_start[0] > (int) $tom_hol_end[0]) {

                $store_holiday_times[2]['end'] = mktime((int) $tom_hol_end[0], (int) $tom_hol_end[1], 0, date("m"), date("d") + 2, date("Y"));
            }
        }

        foreach ($store_holiday_times as $store_holiday_time) {
            if ($current_time >= $store_holiday_time['start'] && $current_time <= $store_holiday_time['end']) {
                return FALSE;
            }
        }
        return $status;
    }

}

?>