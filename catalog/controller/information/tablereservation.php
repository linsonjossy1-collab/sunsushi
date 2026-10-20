<?php

class ControllerInformationTablereservation extends Controller {

    private $error = array();

    public function index() { 
        $this->language->load('information/tablereservation');

        $this->document->setTitle($this->language->get('heading_title'));

        /*
        $this->load->model('catalog/tablereservation');

        $this->load->model('tool/image');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->model_catalog_tablereservation->addReservation($this->request->post);
            $this->redirect($this->url->link('information/tablereservation/success'));
        }
        */

        if (isset($this->error['captcha'])) {
            $this->data['error_captcha'] = $this->error['captcha'];
        } else {
            $this->data['error_captcha'] = '';
        }
        if($this->config->get('grecaptcha_site_key')) {
            $rt =  explode('/',trim($this->request->get['route']));
            $this->data['grecaptcha'] = $this->getChild('module/grecaptcha/index',$rt[(count($rt)-1)]);
        }
        else {
            $this->data['grecaptcha'] = "";
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/tablereservation'),
            'separator' => $this->language->get('text_separator')
        );
        $this->data['persons'] = $this->config->get('config_tblreservation_max_person');

        $this->data['action'] = $this->url->link('information/tablereservation');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/tablereservation.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/information/tablereservation.tpl';
        } else {
            $this->template = 'default/template/information/tablereservation.tpl';
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

    public function getstoretimes() {
        
        $message = '';
        $requested_times = array();

        $timestamp = strtotime($this->request->get['date']);
        
        if(!$this->config->get('config_reservation_status')) {
            $message = 'Online booking not available now.';
            if($this->config->get('config_telephone')) {
                $message.= " Please call restaurant at ".$this->config->get('config_telephone');
            }
        } else {
            $today = strtolower(date('l', $timestamp));
            $current_time = strtotime('+15 minutes');

            $booking_day = date('Y-m-d',$timestamp);

            if($this->config->get('config_reservation_preorder_time') && $booking_day==date('Y-m-d')) {
                $preorder_time = explode(':',$this->config->get('config_reservation_preorder_time'));
                $no_booking_after = mktime((int) $preorder_time[0], (int) $preorder_time[1], 0, date("m"), date("d"), date("Y"));
                if($no_booking_after < $current_time) {
                    $message = 'Sorry, we cannot take any online bookings now for today.';
                    if($this->config->get('config_telephone')) {
                        $message.= " Please call restaurant at ".$this->config->get('config_telephone');
                    }
                }
            }

            
            if(!$message && $this->config->get('config_reservation_min_day')) {
                if(strtotime(date('Y-m-d')." +".((int)$this->config->get('config_reservation_min_day'))." day") > $timestamp) {
                    $message = 'Reservation must be taken before '.$this->config->get('config_reservation_min_day')." day";
                }
            }

            if(!$message && $this->config->get('config_reservation_max_day')) {
                if(strtotime("+".(int)$this->config->get('config_reservation_max_day')." day") < $timestamp) {
                    $message = 'Reservation now taken upto '.$this->config->get('config_reservation_max_day')." day";
                }
            }

            if(!$message) {

                $today_open_times = $this->config->get($today . '_store_times');

                if(!$this->config->get('config_reservation_default_time')) {
                    $store_delivery_time = 0;
                    $today_open_times = array();
                    $today_table_times = $this->config->get('config_tblreservation_timing');
                    if($today_table_times && isset($today_table_times[$today]) && $today_table_times[$today]) {
                        foreach($today_table_times[$today] as $todaytime) {
                            if($todaytime['start']) {
                                $today_open_times[] = $todaytime;
                            }
                        }
                    }
                     if(date('Y-m-d',$timestamp)=='2023-02-14'){
                        $today_open_times = $this->config->get('monday_store_times');
                    }
                    if(!$today_open_times) {
                        $message = '';
                        foreach($today_table_times as $d => $da) {
                            if(empty($da) || ($da[0]['start'])=='') {
                                $message.= ucwords($d)." / ";
                            }
                        }
                        $message = rtrim($message,"/ ");
                        if($this->config->get('config_telephone')) {
                            $message.= " Booking only by telephone ".$this->config->get('config_telephone');
                        }
                    }
                } else {
                    $store_delivery_time = $this->config->get('config_collect_storetime');
                    if(!$store_delivery_time) {
                        $store_delivery_time = 5;
                    }
                }
    
                if($today_open_times) {
                    $holidays = $this->config->get('store_holidays');
                    if ($holidays && isset($holidays[$timestamp])) {
                        $message = 'Today is Holiday!';
                        $hol_start = explode(':', $holidays[$this->request->get['date']]['start']);
                        $hol_end = explode(':', $holidays[$this->request->get['date']]['end']);
    
                        $selected_day = explode('-',$booking_day);
    
                        $holiday_start = mktime((int) $hol_start[0], (int) $hol_start[1], 0, $selected_day[1], $selected_day[2], $selected_day[0]);
                        $holiday_end = mktime((int) $hol_end[0], (int) $hol_end[1], 0, $selected_day[1], $selected_day[2], $selected_day[0]);
                        if ((int) $holiday_start > (int) $holiday_end) {
                            $holiday_end = $holiday_end+86400;
                        }
    
                    }
    
                    $reservation_holiday = $this->config->get('config_no_booking_days');
                    if($reservation_holiday) {
                        $current_date = date('d-m-Y',$timestamp);
                        foreach($reservation_holiday as $k => $holiday) {
                            
                            if($holiday['date'] == $current_date) {
    
                                $reservation_block_end = strtotime($this->request->get['date'].' '.$holiday['end']);
    
                                if($k > $reservation_block_end) {
                                    //$reservation_block_end = strtotime($reservation_block_end . ' +1 day');
                                    $reservation_block_end+= 86400;
                                }
    
                                if(isset($holiday_start)) {
                                    if($holiday_start > $k) {
                                        $holiday_start = $k;
                                        $holiday_end = $reservation_block_end;
                                        $message = 'No Reservation available on this day!';
                                    }
                                } else {
                                    $holiday_start = $k;
                                    $holiday_end = $reservation_block_end;
                                    $message = 'No Reservation available on this day!';
                                }
                            }
                        }
                    }
    
                   
    
                    if($this->config->get('config_tblreservation_storetime')) {
                        $table_booking_time = $this->config->get('config_tblreservation_storetime')*60;
                    } else {
                        $table_booking_time = 900;
                    }
    
                    foreach ($today_open_times as $key => $today_time) {
                        if (!empty(trim($today_time['start']))) {
                            $start_time = explode(':', $today_time['start']);
                            $end_time = explode(':', $today_time['end']);
            
                            $first_time = mktime((int) $start_time[0], (int) $start_time[1], 0, date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp)) + ( $store_delivery_time * 60);
                            if ($timestamp > $first_time) {
                                $first_time = $timestamp - ($timestamp % (900 )) + 900;
                            }
            
                            if ($start_time[0] > $end_time[0]) {
                                $second_time = mktime((int) $end_time[0], (int) $end_time[1], 0, date("m", $timestamp), date("d", $timestamp) + 1, date("Y", $timestamp)) - ( $store_delivery_time * 60);
                            } else {
                                $second_time = mktime((int) $end_time[0], (int) $end_time[1], 0, date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp)) - ( $store_delivery_time * 60);
                            }
    
                            if(isset($holiday_start) && $holiday_start <= $first_time && $holiday_end >= $second_time) {
                                continue; // Holiday timing
                            }
    
                            $avail_times = array();
    
                            if(isset($holiday_start)) {
                                if($holiday_start <= $first_time) {
                                    if($holiday_end >= $second_time) {
                                        continue;
                                    }
                                    $first_time = $holiday_end+60;
                                    $avail_times[] = array('first_time' => $first_time, 'second_time' => $second_time);
                                } else {
                                    if($holiday_end < $first_time) {
                                        //not affected
                                    } else if($holiday_end >= $second_time) {
                                        $second_time = $holiday_start-60;
                                    } else {
                                        $avail_times[] = array('first_time' => $first_time, 'second_time' => $holiday_start -1);
                                        $first_time = $holiday_end+60;
                                    }
                                    
                                    $avail_times[] = array('first_time' => $first_time, 'second_time' => $second_time);
                                }
                            } else {
                                $avail_times[] = array('first_time' => $first_time, 'second_time' => $second_time);
                            }
            
                            foreach($avail_times as $tim) {
                                $first_time = $tim['first_time'];
                                $second_time = $tim['second_time'];
                                if ($current_time < $second_time) { //&& 
                                    if ($current_time > $first_time) {
                                        $current_time =time()+ $table_booking_time;
                                        $this->log->write(print_r(date("d-m-y h:i:s",$current_time),true));
                                        $this->log->write(print_r(($current_time % (3600 )),true));
                                       
                                        $timeintchange = ($current_time % (3600 ));
                                        
                                        //$current_time += $timeintchange;
                                        /*
                                        if ($timeintchange < 300)  // 300 = 5 minits
                                            $current_time = $current_time - $timeintchange;
                                        else
                                            $current_time = $current_time - $timeintchange + 900;
                                        */
                                        $first_time = $current_time-$timeintchange;
                                        if((int)(date("h",$first_time))%2!=0){
                                            $first_time +=3600;
                                        }
                                    }
                                    for ($i = $first_time; $i < $second_time; $i = $i + $table_booking_time) {    
                                        $requested_times[$i] = date("l, h:i A", $i).' - '.date("h:i A", $i+$table_booking_time);
                                    }
                                    $i-= $table_booking_time;
                                    if($requested_times && isset($requested_times[$i])) {
                                        $requested_times[$i] = date("l, h:i A", $i).' - '.date("h:i A", $second_time);
                                    }
                                }
                            }
                        }
                    }
                }
                
            }           

        }

        $json['data'] = $requested_times;
        if(empty($requested_times)) {
            $json['message'] = ($message) ? $message : 'Reservation not available on this day!';
        }
        $this->response->setOutput(json_encode($json));
        
    }
    public function success(){
        
        $this->language->load('information/tablereservation');

		$this->document->setTitle($this->language->get('heading_title')); 

      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
        	'separator' => false
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('information/tablereservation'),
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

    public function validate() {
        $json = array();
        $this->language->load('information/tablereservation');
        if(!$this->config->get('config_reservation_status')) {
            $json['name'] ='Online booking not available.';
        }
        
        $this->request->post['name'] = trim($this->request->post['name']);
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
            $json['name'] = $this->language->get('error_name');
        }

        $this->request->post['email'] = trim($this->request->post['email']);
        if (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $json['email'] = $this->language->get('error_email');
        }

        if($this->request->post['reservation_time'] < strtotime('now')) {
            $json['reservation_time'] = $this->language->get('error_time');
        }
        
        $this->request->post['phone'] = preg_replace('/\D+/', '', $this->request->post['phone']);
        if ((utf8_strlen($this->request->post['phone']) < 10) || (utf8_strlen($this->request->post['phone']) > 11)) {
            $json['phone'] = $this->language->get('error_telephone');
        } else if(!preg_match('/^((\+44(\s\(0\)\s|\s0\s|\s)?)|0)7\d{3}(\s)?\d{6}$/', $this->request->post['phone'])) {
            $this->error['phone'] = 'Invalid UK Telephone';         
        }

        if($this->request->post['persons']<1) {
            $json['persons'] = $this->language->get('error_persons');
        } else if($this->request->post['persons'] > $this->config->get('config_tblreservation_max_person')) {
        	$json['persons'] = 'Maximum no.of person allowed is '.$this->config->get('config_tblreservation_max_person');
        }

        if($this->config->get('grecaptcha_site_key')) {
            $this->load->model('module/grecaptcha');
            if(!$this->model_module_grecaptcha->recaptchaverify($this->request->post)) {
                $json['captcha'] = $this->language->get('error_captcha');
            }
        }
        elseif (empty($this->session->data['res_captcha']) || ($this->session->data['res_captcha'] != $this->request->post['captcha'])) {
            $json['captcha'] = $this->language->get('error_captcha');
        }

        if(!$json) {
            $this->load->model('catalog/tablereservation');            
            $this->model_catalog_tablereservation->addReservation($this->request->post);
            $json['redirect'] = $this->url->link('information/tablereservation/success','','SSL');
            unset($this->session->data['res_captcha']);
        }
        $this->response->setOutput(json_encode($json));
    }

    public function captcha() {
        $this->load->library('captcha');

        $captcha = new Captcha();

        $this->session->data['res_captcha'] = $captcha->getCode();

        $captcha->showImage();
    }

}

?>
