<?php

#[\AllowDynamicProperties]
class Postcode {
    private $config;
    private $customer;
    private $session;
    private $db;
    private $tax;
    private $weight;
    private $language;
    private $cart;
    private $log;
    public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');
		$this->tax = $registry->get('tax');
		$this->weight = $registry->get('weight');
                $this->language = $registry->get('language');
                $this->cart = $registry->get('cart');
                $this->log = $registry->get('log');
	}   
   public function isStoreOpened($store_id=0) {
        $status = FALSE;
        $current_time = strtotime('now');
        $today = strtolower(date('l'));
        $yesterday = strtolower(date('l', strtotime('yesterday')));
        $tomorrow = strtolower(date('l', strtotime('tomorrow')));
        $today_times = $today . '_store_times';
        $yesterday_times = $yesterday . '_store_times';
        $tomorrow_times = $tomorrow . '_store_times';
        $store_open_times = array();
        $today_open_times = $this->config->get($today_times);
        $yesterday_open_times = $this->config->get($yesterday_times);
        $tomorrow_open_times = $this->config->get($tomorrow_times);

        $todate = strtotime(date('d-m-Y'));
        $yes_date = strtotime(date("d-m-Y", strtotime('yesterday')));
        $tom_date = strtotime(date("d-m-Y", strtotime('tomorrow')));

        if($this->isStoreHoliday()) {
            return False;
        }

        $i = 0;

        foreach ($yesterday_open_times as $yesterday_open_time) {
            if(empty($yesterday_open_time['start'])){
                continue;
            }
            $yes_start_times = explode(':', $yesterday_open_time['start']);
            $yes_end_times = explode(':', $yesterday_open_time['end']);
            $store_open_times[$i]['start']=mktime((int)$yes_start_times[0],(int) $yes_start_times[1], 0, date("m"), date("d") - 1 , date("Y"));
            $store_open_times[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 1 , date("Y"));
            if ((int)$yes_start_times[0] > (int)$yes_end_times[0])
            {
                $store_open_times[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d"), date("Y"));
            }
            $i++;
        }
        // print_r($nn);
        foreach ($today_open_times as $today_open_time) {
            if(empty($today_open_time['start'])){
                continue;
            }
            $tod_start_times = explode(':', $today_open_time['start']);
            $tod_end_times = explode(':', $today_open_time['end']);
            $store_open_times[$i]['start']=mktime((int)$tod_start_times[0], (int)$tod_start_times[1], 0, date("m"), date("d"), date("Y"));
            
            $store_open_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d"), date("Y"));
            if ((int)$tod_start_times[0] > (int)$tod_end_times[0])
            {
                
                $store_open_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+1, date("Y"));
            }    
            $i++;
        }
       
        foreach ($tomorrow_open_times as $tomorrow_open_time) {
            if(empty($tomorrow_open_time['start'])){
                continue;
            }
            $tom_start_times = explode(':', $tomorrow_open_time['start']);
            $tom_end_times = explode(':', $tomorrow_open_time['end']);
            $store_open_times[$i]['start']=mktime((int)$tom_start_times[0], (int)$tom_start_times[1], 0, date("m"), date("d")+1, date("Y"));
            
            $store_open_times[$i]['end']=mktime((int)$tom_end_times[0],(int) $tom_end_times[1], 0, date("m"), date("d")+1, date("Y"));
            if ((int)$tom_start_times[0] > (int)$tom_end_times[0])
            {
                $store_open_times[$i]['end']=mktime((int)$tom_end_times[0], (int)$tom_end_times[1], 0, date("m"), date("d")+2, date("Y"));
            }    
            $i++;
        }
        foreach ($store_open_times as $store_open_time) {
            if ($current_time >= $store_open_time['start'] && $current_time <= $store_open_time['end']) {
                $status = TRUE;
               
            }
        }
        return $status;
    }
    public function timeinbetween($H1, $M1, $H2, $M2, $current_time) {
        $first_time = mktime($H1, $M1, 0, date("m"), date("d"), date("Y"));
        $second_time = mktime($H2, $M2, 0, date("m"), date("d"), date("Y"));
        if ($H1 > $H2)
            $second_time = mktime($H2, $M2, 0, date("m"), date("d") + 1, date("Y"));
        if ($current_time < $second_time && $current_time > $first_time) {
            return true;
        } else {
            return false;
        }
    }
    public function getPostcodeFirstPart($postcode) {
        // Convert the post code to upper case and trim the variable
        $postcode = strtoupper(trim($postcode));

        // Remove any spaces
        $postcode = str_replace(" ", "", $postcode);

        // Trim the last 3 characters off the end
        $postcode_first = substr($postcode, 0, strlen($postcode) - 3);
        return $postcode_first;
    }
    public function getPostcodeLag($postcode) {
        $source_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "uk_postcodes WHERE postcode = '$postcode' LIMIT 1");
        if ($source_data->num_rows) {
            return $source_data->row;

        } else {
            return false;
        }
    }
    public function getPostcodeDetails($post_code){
 
        $source_data = $this->db->query("SELECT postcode,latitude,longitude,county,city,street,address FROM " . DB_PREFIX . "uk_postcodes WHERE postcode LIKE '$post_code'");
        if ($source_data->num_rows && !empty($source_data->row['address'])) {
            $source = $source_data->row;
            //return $source;
        } else {
            $postcodedetails=  $this->getEtmPostcodedata($post_code);
            if(!empty($postcodedetails)){
                $source = $postcodedetails; 
            }else{
                $source=array();
            }
        }
         return $source;
    }   
    function getPostcodeFirsttwo($postcode,$length=2) {
        // Convert the post code to upper case and trim the variable
        $postcode = strtoupper(trim($postcode));

        // Remove any spaces
        $postcode = str_replace(" ", "", $postcode);

        // Trim the last 3 characters off the end
        $postcode_first = substr($postcode, 0, $length);
        return $postcode_first;
    }
    function getDistance($home_postcode, $destination_postcode) {
        //$value = $this->getPostcodeFirstPart($home_postcode);
        $post_distance = $this->getPostcodeDistance($destination_postcode,(int)$this->config->get('config_store_id'));
        if(!empty($post_distance) && isset($post_distance['distance'])){
            //if((floatval($distance)))
            $distance = $post_distance['distance'];
        }else{
            $value = $this->getPostcodeDetails($home_postcode);
            $destination_data = $this->getPostcodeDetails($destination_postcode);
            if(!empty($value) && !empty($destination_data))
                $distance = $this->getAirDistance($value['latitude'], $value['longitude'], $destination_data['latitude'], $destination_data['longitude']);
            else
                $distance=NULL;
        }
        return $distance;
    }
    function getDeliveryDistance($home_postcode, $destination_postcode) {
        //$value = $this->getPostcodeFirstPart($home_postcode);
        $post_distance = $this->getPostcodeDistance($destination_postcode,(int)$this->config->get('config_store_id'));
        if(!empty($post_distance) && isset($post_distance['distance'])){
            //if((floatval($distance)))
            $distance = $post_distance['distance'];
        }else{
            $distance = $this->googleDistance($home_postcode,$destination_postcode,(int)$this->config->get('config_store_id'));
        }
        return $distance;
    }
    public function postcodeformat($postalcode) {
        $pcode = trim($postalcode);
        $postcode = str_replace(" ", "", $pcode);
        $last = substr($postcode, -3);
        $first = substr($postcode, 0, (strlen($postcode) - 3));
        $actal_postcode = $first . ' ' . $last;
        return strtoupper($actal_postcode);
    }
    public function getrequestedtimes($store_delivery_time, $current_time, $preparation_time,$shipping_method) {
        $next_time = time() + ( $preparation_time * 60);
        $today = strtolower(date('l', $next_time));
        $requested_times=array();
        if($this->isStoreHoliday()){
            return $requested_times;
        }
        
        /*
        //$language->load('checkout/checkout');
        //new
        if($shipping_method == 'home.home'){
           $today_del_time =  $today . '_delivery_time';
           if($this->config->get($today_del_time)){
               $store_delivery_time = intval(trim($this->config->get($today_del_time)));
           }
        }
        //new
        if ($shipping_method == 'home.home' and !$this->config->get('home_delivery_store_default_time')) {
            $today_times = 'home_delivery_' . $today . '_times';
        } else {
            $today_times = $today . '_store_times';
        }
        $today_open_times = $this->config->get($today_times);
        
       */
        //new
        if($shipping_method == 'home.home'){
            $shippingcode='home';
           $today_del_time =  $today . '_delivery_time';
           if($this->config->get($today_del_time)){
               $store_delivery_time = intval(trim($this->config->get($today_del_time)));
           }
        }else{
            $shippingcode='collect';
        }
        $store_delivery_time = $this->getUpdatedRequestedtime($shippingcode,$store_delivery_time);
        if($shipping_method == 'home.home' && isset($this->session->data['avilableshippingtime']) && (int)$this->session->data['avilableshippingtime']>0){
            $store_delivery_time = $this->session->data['avilableshippingtime'];
        }
        //new
        $product_times = $this->getCartDeliverytimes($shipping_method,$preparation_time);
        //print_r($product_times);
        if(!empty($product_times)){
            $asap_status =  $this->config->get('config_selected_time_only');
            foreach($product_times as $product_time){
                $first_time = $product_time['start'] + ( 0 * 60)+($preparation_time * 60);
                $second_time = $product_time['end']-(0 * 60);
                if ($current_time < $second_time) { //&& 
                    if ($current_time > $first_time) {
                        //echo "lll";
                        $current_time += ( 0 * 60)+($preparation_time * 60);
                        $timeintchange = ($current_time % (900 ));

                        if ($timeintchange < 300)  // 300 = 5 minits
                            $current_time = $current_time - $timeintchange;
                        else
                            $current_time = $current_time - $timeintchange + 900;
                        
                        $first_time = $current_time;
                    }
                    $first_time  =$first_time+( $store_delivery_time * 60);
                    if (!$asap_status && $preparation_time == 0 && $product_time['start'] < $current_time &&  $this->isStoreOpened($this->config->get('config_store_id'))) {
                        $requested_times[time() - 900] = $this->language->get('text_asap_time');
                        $asap_status  =true;
                    }
                    for ($i = $first_time; $i <= $second_time; $i = $i + 900) {
                        $requested_times[$i] = date("h:i A (l)", $i);
                    }

                    if(!isset($requested_times[$second_time])) {
                        //$requested_times[$second_time] = date("h:i A (l)", $second_time);
                    }
                }
            }
            return $requested_times;
        }else{
             return $requested_times;
        }
        return false;
    }
    public function isStoreHoliday() {
        $status = false;
        if($this->checkisDisabledfromEpos('online') || $this->config->get('config_online_orders')){
             return True;
        }
        $current_time = strtotime('now');
      
        $holidays = $this->config->get('store_holidays');

        $todate = strtotime(date('d-m-Y'));
        $yes_date = strtotime(date("d-m-Y", strtotime('yesterday')));
        $tom_date = strtotime(date("d-m-Y", strtotime('tomorrow')));
        
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
                $store_holiday_times[1]['end'] = mktime((int) $tod_hol_end[0], (int) $tod_hol_end[1], 0, date("m"), date("d") + 1, date("Y"));
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
                return True;
            }
        }
        // Store time empty for holiday
        
        $today_times = strtolower(date('l')) . '_store_times';
        $yesterday_times = strtolower(date('l', strtotime('yesterday'))) . '_store_times';
        $tomorrow_times = strtolower(date('l', strtotime('tomorrow'))) . '_store_times';
        $store_open_times = array();
        $today_open_times = $this->config->get($today_times);
        $yesterday_open_times = $this->config->get($yesterday_times);
        $tomorrow_open_times = $this->config->get($tomorrow_times);

        $i = 0;
        $holiday_chance = False;
        foreach ($today_open_times as $today_open_time) {
            if(empty($today_open_time['start'])){
                $holiday_chance = True;
            }
            
        }
        
        if ($holiday_chance) {
            $last_close_time=0;
            foreach ($yesterday_open_times as $yesterday_open_time) {
                if(empty($yesterday_open_time['start'])){
                    continue;
                }
                $yes_start_times = explode(':', $yesterday_open_time['start']);
                $yes_end_times = explode(':', $yesterday_open_time['end']);
                
                $end_time=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 1 , date("Y"));
                if ((int)$yes_start_times[0] > (int)$yes_end_times[0])
                {
                    $end_time =mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d"), date("Y"));
                }
                if ( $last_close_time < $end_time ) {
                     $last_close_time = $end_time;
                }
                 
            }
            $next_start_time=0;
            foreach ($tomorrow_open_times as $tomorrow_open_time) {
                if(empty($tomorrow_open_time['start'])){
                    continue;
                }
                $tom_start_times = explode(':', $tomorrow_open_time['start']);
                $tom_end_times = explode(':', $tomorrow_open_time['end']);
                $start_time=mktime((int)$tom_start_times[0], (int)$tom_start_times[1], 0, date("m"), date("d")+1, date("Y"));
                if ( $next_start_time < $start_time ) {
                     $next_start_time = $start_time;
                }
            }
            
            if( $last_close_time!=0 && $last_close_time >=$current_time  ){
                return False;
            }
            if($next_start_time!=0 && $next_start_time <= $current_time) {
                 return False;
            }
            return True;
            
        }

        return $status;
    }
    private function getShippingtimes($shipping_method,$ind=1){
       // $today_open_times = array();
        $today = strtolower(date('l', +($ind * 86400)));
        if ($shipping_method == 'home.home') {
            $today_times = 'home_delivery_' . $today . '_times';
        } else {
            $today_times = $today . '_store_times';
        }
        $today_open_times = $this->config->get($today_times);
        //print_r($today_open_times);
       if (!empty($today_open_times) and $today_open_times[0]['start'] != '') {
             print_r($today_open_times);
            return $today_open_times;
        } else {
            return $this->getShippingtimes($shipping_method, $ind + 1);
        }
        //return $today_open_times;
    }
    public function getLastDaysOpeningtimes() {
        //$today = array();
          $j=1;
     for ($i = 1; $i <= 7; $i++) {
       // for ($i = 1; $i <= 7; $i++) {
           // #TODO HOLIDAYS IN PROPER#//
           //echo $i.'--'; 
            //$day_name = strtolower(date("l",mktime(0,0,0,3,28,2009)-$i * (3600*24))); 
           //echo '</br>';
           $day_name = strtolower(date('l', strtotime("-".$i."days")));
           //echo "-";
            $day_times = $this->config->get($day_name . '_store_times');
            if (!empty($day_times[key($day_times)]['start'])) {
                $start_times=  explode(':', '06:00');
                $end_times=  explode(':', '06:00');
                //$times[$day_name]['start'] = date('Y-m-d H:i:s',  mktime((int)$start_times[0],(int)$start_times[1],0, date("m"), date("d")-$i, date("Y")));
               // $times[$day_name]['end'] = date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d")-$i, date("Y")));
                $store_times[]=array(
                    'day'=>strtolower(date("l",mktime((int)$start_times[0],(int)$start_times[1],0, date("m"), date("d")-$j, date("Y")))),
                    'start'=>date('Y-m-d H:i:s',  mktime((int)$start_times[0],(int)$start_times[1],0, date("m"), date("d")-$j, date("Y"))),
                    'end'=>date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d")-$j+1, date("Y")))
                 );
            }
           $j++; 
        }
       //print_r($store_times);
        return $store_times;
        /*
        
        //$today = array();
          $j=1;
     for ($i = 2; $i <= 7; $i++) {
       // for ($i = 1; $i <= 7; $i++) {
           // #TODO HOLIDAYS IN PROPER#//
           //echo $i.'--'; 
            //$day_name = strtolower(date("l",mktime(0,0,0,3,28,2009)-$i * (3600*24))); 
           //echo '</br>';
            $day_name = strtolower(date('l', -($i * 86400)));
            $day_times = $this->config->get($day_name . '_store_times');
            if (!empty($day_times[key($day_times)]['start'])) {
                $start_times=  explode(':', $day_times[key($day_times)]['start']);
                $end_times=  explode(':', $day_times[key(array_reverse($day_times, true))]['end']);
                //$times[$day_name]['start'] = date('Y-m-d H:i:s',  mktime((int)$start_times[0],(int)$start_times[1],0, date("m"), date("d")-$i, date("Y")));
               // $times[$day_name]['end'] = date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d")-$i, date("Y")));
                $store_times[]=array('day'=>strtolower(date("l",mktime((int)$start_times[0],(int)$start_times[1],0, date("m"), date("d")-$j, date("Y")))),'start'=>date('Y-m-d H:i:s',  mktime((int)$start_times[0],(int)$start_times[1],0, date("m"), date("d")-$j, date("Y"))),'end'=>date('Y-m-d H:i:s',  mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d")-$j, date("Y"))));
            }
           $j++; 
        }
      //  print_r($store_times);
        return $store_times;
         * */
        
    }    
    public function checkIsvalidpostcode($postcode){
        $len = strlen($postcode);
        //echo "SELECT * FROM " . DB_PREFIX . "uk_postcodes WHERE SUBSTR(`postcode`,0,$len) LIKE ?bb;
        $source_data = $this->db->query("SELECT postcode FROM " . DB_PREFIX . "uk_postcodes WHERE `postcode` LIKE '$postcode%'");
        if ($source_data->num_rows) {
            return TRUE;
        }else{
            return FALSE;
        }
    }
    public function getStorePostcodeDetails(){
        $postalcode = $this->config->get('config_postalcode');
        $postcode = $this->postcodeformat($postalcode);
        $source_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "uk_postcodes WHERE postcode LIKE '$postcode'");
        if ($source_data->num_rows) {
            $source = $source_data->row;
            return $source;
        } else {
            $source=array();
            return $source;
        }
    }
     public function googleDistance($origin_postcode,$destn_postcode,$store_id=0) {
           // return $this->getDistance($origin_postcode,$destn_postcode);
            $origin_postcode1 = strtoupper(trim($origin_postcode));
            $origin_postcode2 = str_replace(" ", "", $origin_postcode1);
            $destn_postcode1 = strtoupper(trim($destn_postcode));
            $destn_postcode2 = str_replace(" ", "", $destn_postcode1);
            if($origin_postcode2==$destn_postcode2){
                return 0; 
            }
            try {
                
               // $this->log->write( 'https://maps.googleapis.com/maps/api/directions/json?origin='.str_replace(' ', '', $origin_postcode).'&destination='.str_replace(' ', '', $destn_postcode).'&avoid=highways&mode=driving&units=imperial&key='.$this->config->get('config_browserapp_api'));
                //$origin_postcode = str_replace(' ', '', $origin_postcode);
                //$destn_postcode = str_replace(' ', '', $destn_postcode);
                $ch = curl_init('https://maps.googleapis.com/maps/api/directions/json?origin='.rawurlencode($origin_postcode).'&destination='.rawurlencode($destn_postcode).'&avoid=highways&mode=driving&units=imperial&key='.$this->config->get('config_browserapp_api')); 
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch,CURLOPT_FAILONERROR, 1);
                $json = curl_exec($ch);
                curl_close($ch); 
                $output = json_decode($json, TRUE);
               
                if(isset($output['routes'][0]['legs'][0]['distance']['value'])){
                    $distance_km = $output['routes'][0]['legs'][0]['distance']['value'] / 1000;
                    $distance = round(($distance_km / 1.609344),1);
                    //insert into DB
                    $this->adddistance($destn_postcode,$store_id,$distance);
                    return $distance;
                }else{
                    $this->session->data['air_disance']='ok';
                    return $this->getDistance($origin_postcode,$destn_postcode);
                }
            } catch (Exception $e) {
                return $this->getDistance($origin_postcode,$destn_postcode);
            }

         
    }
     public function getAirDistance($lat1, $long1, $lat2, $long2) {

        #$earth = 6371; #km change accordingly
        $earth = 3960; #miles
        #Point 1 cords
        $lat1 = deg2rad($lat1);
        $long1 = deg2rad($long1);

        #Point 2 cords
        $lat2 = deg2rad($lat2);
        $long2 = deg2rad($long2);

        #Haversine Formula
        $dlong = $long2 - $long1;
        $dlat = $lat2 - $lat1;

        $sinlat = sin($dlat / 2);
        $sinlong = sin($dlong / 2);

        $a = ($sinlat * $sinlat) + cos($lat1) * cos($lat2) * ($sinlong * $sinlong);

        $c = 2 * asin(min(1, sqrt($a)));

        $d = round($earth * $c);

        return $d;
    }
     public function adddistance($postcode,$store_id,$distance){
        $postcode = $this->postcodeformat($postcode);
        $this->db->query("INSERT INTO " . DB_PREFIX . "uk_postcode_distance SET postcode='" . $this->db->escape($postcode) . "',store_id='" . (int)$store_id . "',distance='" . (float)$distance . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'postcode_modified'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'postcode_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");      
    }
    public function getPostcodeDistance($post_code,$store_id=0){
        $source=array();
        $source_data = $this->db->query("SELECT pd.distance FROM " . DB_PREFIX . "uk_postcode_distance pd  WHERE pd.postcode LIKE '$post_code' AND pd.store_id='".$store_id."'");
        //echo "SELECT *,pd.distance FROM " . DB_PREFIX . "uk_postcodes p LEFT JOIN " . DB_PREFIX . "uk_postcode_distance pd ON(p.postcode=pd.postcode) WHERE p.postcode LIKE '$post_code' AND pd.store_id='".$store_id."'";
        if ($source_data->num_rows) {
            $source = $source_data->row;
            return $source;
        } else {
            return $source;
        }
    }
    /*
     * import Bulk Postcodes details to DB 
     */    
    public function importetmpostcodes($postcode,$page=0){
        $postcode = trim($postcode);
        //echo 'https://api.etakeawaymax.co.uk/postcode.php?pquery='.urlencode($postcode).'&p='.$page;
        $ch = curl_init('https://api.etakeawaymax.co.uk/postcode_address.php?pquery='.urlencode($postcode).'&p='.$page); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        $json = curl_exec($ch); 
        $output = json_decode($json,true);
        
        $return_data = array('YES'=>array(),'NOT'=>array());
        curl_close($ch); 
        //console.log($output); 
        //print_r($output); //check for data array import to db.
        if(isset($output['postcodes']) && !empty($output['postcodes'])){ //&& $output['street']!=''
            
          foreach($output['postcodes'] as $postcodedata){ 
                $distance = $this->isInShippingDistance($postcodedata['postcode'],$postcodedata['latitude'],$postcodedata['longitude']);
                if($distance!==false) {
               $this->db->query("INSERT INTO `" . DB_PREFIX . "uk_postcodes` (postcode,latitude,longitude,county,district,ward,country,street,city,address) VALUES ('".$this->db->escape($postcodedata['postcode'])."','".$this->db->escape($postcodedata['latitude'])."','".$this->db->escape($postcodedata['longitude'])."','".$this->db->escape($postcodedata['county'])."','".$this->db->escape($postcodedata['district'])."','".$this->db->escape($postcodedata['ward'])."','".$this->db->escape($postcodedata['country'])."','".$this->db->escape($postcodedata['street'])."','".$this->db->escape($postcodedata['city'])."','".$this->db->escape(json_encode($postcodedata['address']))."') ON DUPLICATE KEY UPDATE latitude='".$this->db->escape($postcodedata['latitude'])."',longitude='".$this->db->escape($postcodedata['longitude'])."',county='".$this->db->escape($postcodedata['county'])."',district='".$this->db->escape($postcodedata['district'])."',ward='".$this->db->escape($postcodedata['ward'])."',country='".$this->db->escape($postcodedata['country'])."',street='".$this->db->escape($postcodedata['street'])."',city='".$this->db->escape($postcodedata['city'])."',address='".$this->db->escape(json_encode($postcodedata['address']))."'");
                    $return_data['YES'][$postcodedata['postcode']] = $distance;
           }   
                else {
                    $return_data['NOT'][$postcodedata['postcode']] = $distance;
                }
            }   
                $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'postcode_modified'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'postcode_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");      
            
            // $this->log->write(print_r($return_data,true));
           // return json_encode($return_data);
           // return count($output['postcodes']);
            return count($return_data['YES']);
        }else{
           return false;
        }
        
    }
    public function getEtmPostcodedata($postcode){ 
        //echo urlencode($postcode);
        try {
            $ch = curl_init('https://api.etakeawaymax.co.uk/postcode_address.php?postcode='.urlencode($postcode)); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json'
            ));
            $json = curl_exec($ch); 
            $output = json_decode($json,true);
            curl_close($ch); 
            //print_r($output); 
            if(isset($output['postcodes']) && !empty($output['postcodes'])){
               //if($output['postcodes']['street']!=''){
                   // $this->db->query("INSERT INTO `" . DB_PREFIX . "uk_postcodes` SET postcode='".$this->db->escape($output['postcodes']['postcode'])."',latitude='".$this->db->escape($output['postcodes']['latitude'])."',longitude='".$this->db->escape($output['postcodes']['longitude'])."',county='".$this->db->escape($output['postcodes']['county'])."',district='".$this->db->escape($output['postcodes']['district'])."',ward='".$this->db->escape($output['postcodes']['ward'])."',country='".$this->db->escape($output['postcodes']['country'])."',street='".$this->db->escape($output['postcodes']['street'])."',city='".$this->db->escape($output['postcodes']['city'])."'  ");
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "uk_postcodes` (postcode,latitude,longitude,county,district,ward,country,street,city,address) VALUES ('".$this->db->escape($output['postcodes']['postcode'])."','".$this->db->escape($output['postcodes']['latitude'])."','".$this->db->escape($output['postcodes']['longitude'])."','".$this->db->escape($output['postcodes']['county'])."','".$this->db->escape($output['postcodes']['district'])."','".$this->db->escape($output['postcodes']['ward'])."','".$this->db->escape($output['postcodes']['country'])."','".$this->db->escape($output['postcodes']['street'])."','".$this->db->escape($output['postcodes']['city'])."','".$this->db->escape(json_encode($output['postcodes']['address']))."') ON DUPLICATE KEY UPDATE latitude='".$this->db->escape($output['postcodes']['latitude'])."',longitude='".$this->db->escape($output['postcodes']['longitude'])."',county='".$this->db->escape($output['postcodes']['county'])."',district='".$this->db->escape($output['postcodes']['district'])."',ward='".$this->db->escape($output['postcodes']['ward'])."',country='".$this->db->escape($output['postcodes']['country'])."',street='".$this->db->escape($output['postcodes']['street'])."',city='".$this->db->escape($output['postcodes']['city'])."',address='".$this->db->escape(json_encode($output['postcodes']['address']))."' ");
               //}
                    $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'postcode_modified'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = 0, `group` = 'modified_date', `key` = 'postcode_modified', `value` = '" . $this->db->escape(strtotime('now')) . "'");      
                    $result = $output['postcodes'];
                    $result['address'] = json_encode($output['postcodes']['address']); 
                    return $result;
            }
            
        } catch (Exception $e) {
            $this->log->write(print_r($e,true));
            return false;
        }
    }
    function postcodesCount() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "uk_postcodes` ");
            return (int) $query->row['total'];
    }
    function getPostcodes($page) { 
        $limit =1000;
        $limitstrat = $page * $limit;
        if ($limitstrat > 0 ) {
            $limitstrat += 1;
        }
        $query = $this->db->query("SELECT p.postcode, latitude, longitude, county, street, city, distance, address,delivery_charge FROM  " . DB_PREFIX . "uk_postcodes p LEFT JOIN " . DB_PREFIX . "uk_postcode_distance d ON(p.postcode=d.postcode)");
        $postcode_data =array();
        if(!empty($query->rows)){
            foreach($query->rows as $data){
                $data['address'] = json_decode($data['address']);
                $postcode_data[] = $data;
            }
        } 
        return  $postcode_data;
    }
    function isUKmobile($tele) {
        if (empty($tele)) {
            return false;
        }
        $matches=array();
        preg_match("^07[5789]{1}[0-9]{8}^",$tele,$matches);
        if ( count($matches) > 0 && $matches[0] == $tele){
            return  true;
        }
        else {
            return false;
        }
    }
      public function getPostcodeandstreet() {
        $postcode_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "uk_postcodes p LEFT JOIN `" . DB_PREFIX . "uk_postcode_distance` d ON (p.postcode = d.postcode)  WHERE d.distance>=0 ORDER BY d.distance ASC LIMIT 0,50");
        //echo "SELECT * FROM " . DB_PREFIX . "uk_postcodes p LEFT JOIN `" . DB_PREFIX . "uk_postcode_distance` d ON (p.postcode = d.postcode) WHERE d.store_id = '" . (int)$this->config->get('config_store_id') . "'";
        return $postcode_query->rows;
    }
    public function getTodayStoretime() {
        $status = FALSE;
        $current_time = strtotime('now');
        $today = strtolower(date('l'));
        $yesterday = strtolower(date('l', strtotime('yesterday')));
        $tomorrow = strtolower(date('l', strtotime('tomorrow')));
        $today_times = $today . '_store_times';
        $yesterday_times = $yesterday . '_store_times';
        $tomorrow_times = $tomorrow . '_store_times';
        $store_open_times = array();
        $today_open_times = $this->config->get($today_times);
        $yesterday_open_times = $this->config->get($yesterday_times);
        $tomorrow_open_times = $this->config->get($tomorrow_times);

        $i = 0;

        foreach ($yesterday_open_times as $yesterday_open_time) {
            if(empty($yesterday_open_time['start'])){
                continue;
            }
            $yes_start_times = explode(':', $yesterday_open_time['start']);
            $yes_end_times = explode(':', $yesterday_open_time['end']);
            $store_open_times[$i]['start']=mktime((int)$yes_start_times[0],(int) $yes_start_times[1], 0, date("m"), date("d") - 1 , date("Y"));
            $store_open_times[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 1 , date("Y"));
            if ((int)$yes_start_times[0] > (int)$yes_end_times[0])
            {
                $store_open_times[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d"), date("Y"));
            }
            $i++;
        }
        // print_r($nn);
        foreach ($today_open_times as $today_open_time) {
            if(empty($today_open_time['start'])){
                continue;
            }
            $tod_start_times = explode(':', $today_open_time['start']);
            $tod_end_times = explode(':', $today_open_time['end']);
            $store_open_times[$i]['start']=mktime((int)$tod_start_times[0], (int)$tod_start_times[1], 0, date("m"), date("d"), date("Y"));
            
            $store_open_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d"), date("Y"));
            if ((int)$tod_start_times[0] > (int)$tod_end_times[0])
            {
                
                $store_open_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+1, date("Y"));
            }    
            $i++;
        }
       
        foreach ($tomorrow_open_times as $tomorrow_open_time) {
            if(empty($tomorrow_open_time['start'])){
                continue;
            }
            $tom_start_times = explode(':', $tomorrow_open_time['start']);
            $tom_end_times = explode(':', $tomorrow_open_time['end']);
            $store_open_times[$i]['start']=mktime((int)$tom_start_times[0], (int)$tom_start_times[1], 0, date("m"), date("d")+1, date("Y"));
            
            $store_open_times[$i]['end']=mktime((int)$tom_end_times[0],(int) $tom_end_times[1], 0, date("m"), date("d")+1, date("Y"));
            if ((int)$tom_start_times[0] > (int)$tom_end_times[0])
            {
                $store_open_times[$i]['end']=mktime((int)$tom_end_times[0], (int)$tom_end_times[1], 0, date("m"), date("d")+2, date("Y"));
            }    
            $i++;
        }
        foreach ($store_open_times as $store_open_time) {
            if ($current_time >= $store_open_time['start'] && $current_time <= $store_open_time['end']) {
                $status = TRUE;
                return array('start'=>$store_open_time['start'],'end'=>$store_open_time['end']);
            }
        }
        return $status;
    }
    
    public function getCartDeliverytimes($shipping_method='',$preparation_time){
        unset($this->session->data['product_no_times']);
        $today_times = $this->getTodayStoretime();
        //print_r($today_times);
        if(!empty($today_times) && !empty($today_times)){
            $start_time= $today_times['start'];
            $today = strtolower(date('l',$start_time));
        }else{
            $today = strtolower(date('l'));
        }
        //echo $today;
        $yesterdaystatus = false;
        if(strtolower(date('l', strtotime('yesterday')))==$today){
              $yesterdaystatus = true;
        }
       
        //print_r($availale_days[$today]);
        if ($shipping_method == 'home.home' and !$this->config->get('home_delivery_store_default_time')) {
            $today_times = 'home_delivery_' . $today . '_times';
            $todday_storetimes = $this->config->get($today_times);
            //echo "fdg";
        } else if ($shipping_method == 'collect.collect' && $this->config->get('collect_different_time')) {
            $todday_storetimes = $this->config->get('collect_'.$today.'_times');
        } else{
            $todday_storetimes = $this->config->get($today . '_store_times');
        }
        if(!empty($todday_storetimes)){
            $store_open_times = array();
            $i=0;
            foreach($todday_storetimes as $today_open_time){
                $tod_start_times = explode(':', $today_open_time['start']);
                $tod_end_times = explode(':', $today_open_time['end']);
                if(!$yesterdaystatus){
                    $store_open_times[$i]['start']=mktime((int)$tod_start_times[0], (int)$tod_start_times[1], 0, date("m"), date("d"), date("Y"));
                    $store_open_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d") , date("Y"));
                }else{
                    $store_open_times[$i]['start']=mktime((int)$tod_start_times[0], (int)$tod_start_times[1], 0, date("m"), date("d")-1, date("Y"));
                    $store_open_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")- 1 , date("Y"));
                }
                
                if ((int)$tod_start_times[0] > (int)$tod_end_times[0])
                {   
                    if(!$yesterdaystatus){
                        $store_open_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+1, date("Y"));
                    }else{
                        $store_open_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d"), date("Y"));
                    }
                     
                } 
                $i++;
            }
            //$returntimes = array();
            //print_r($store_open_times);
            $product_available_times = array();
            $currenttime = strtotime('now');
            if(!empty($store_open_times)){                
                $categories = $this->getcategories();           
                foreach ($this->cart->getProducts() as $product) {
                    if($product['specific_time']==1){
                        //echo"kkk";
                        if(!empty($product['available_times']) && isset($product['available_times'][$today])){
                            $avl_status = true;
                            $current_available_times = $store_open_times;
                            foreach ($product['available_times'][$today] as $available_time) {
                                if($available_time['start']==''){
                                    $this->session->data['product_no_times']='yes';
                                    return false;
                                }
                                $avl_start = explode(':', $available_time['start']);
                                $avl_end = explode(':', $available_time['end']);
                                $product_start_time =  mktime((int)$avl_start[0],(int)$avl_start[1],0, date("m"), date("d"), date("Y"));
                                $product_end_time =  ((int) $avl_start[0] > (int) $avl_end[0])?mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m"), date("d")+1, date("Y")):mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m"), date("d"), date("Y"));                                                                
                                foreach($current_available_times as $key=>$current_available_time){
                                    if($product_start_time>$current_available_time['end']){ // Not in SLOT
                                        unset($store_open_times[$key]);
                                    }else {
                                        if($product_start_time>=$current_available_time['start']){
                                            $store_open_times[$key]['start'] = $product_start_time;
                                        }
                                        if($product_end_time<=$current_available_time['end']){
                                            if($product_end_time < $currenttime) {
                                                unset($store_open_times[$key]);
                                            } else {
                                            $store_open_times[$key]['end'] = $product_end_time;
                                        }
                                    }                                   
                                }                              
                            }
                            }
                        } else{
                            $this->session->data['product_no_times']='yes';
                            return false;
                        }
                    }
                    else {
                        
                        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category c  WHERE c.product_id = '" . (int) $product['product_id'] . "'");
                        $product_categories ='';
                        foreach ($query->rows as $result) {
                            $product_categories .= ','.$this->getPath($result['category_id']);
                }
                        $product_categories= array_unique(explode(',', trim($product_categories,',')));
                        $category_id = 0;
                        //print_r($product_categories);
                        foreach($product_categories as $prd_category_id) {
                            if(in_array($prd_category_id, $categories)){
                                $category_id =$prd_category_id;
                                 break;
                            }
                        }
                        //echo "CAT ID->".$category_id;
                        $cquery = $this->db->query("SELECT specific_time,available_time FROM " . DB_PREFIX . "category c WHERE c.category_id = '" . (int)$category_id . "' AND c.specific_time=1");
                        if($cquery->row){
                            $cat_times = unserialize($cquery->row['available_time']);
                            if(!empty($cat_times) && isset($cat_times[$today])){
                            $avl_status = true;
                            $current_available_times = $store_open_times;
                            foreach ($cat_times[$today] as $available_time) {
                                if($available_time['start']==''){
                                    $this->session->data['product_no_times']='yes';
                                    return false;
                                }
                                $avl_start = explode(':', $available_time['start']);
                                $avl_end = explode(':', $available_time['end']);
                                $product_start_time =  mktime((int)$avl_start[0],(int)$avl_start[1],0, date("m"), date("d"), date("Y"));
                                $product_end_time =  ((int) $avl_start[0] > (int) $avl_end[0])?mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m"), date("d")+1, date("Y")):mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m"), date("d"), date("Y"));                                                                
                                foreach($current_available_times as $key=>$current_available_time){
                                    if($product_start_time>$current_available_time['end']){ // Not in SLOT
                                        unset($store_open_times[$key]);
                                    }else {
                                        if($product_start_time>=$current_available_time['start']){
                                            $store_open_times[$key]['start'] = $product_start_time;
                                        }
                                        if($product_end_time<=$current_available_time['end']){
                                            if($product_end_time < $currenttime) {
                                                unset($store_open_times[$key]);
                                            } else {
                                                $store_open_times[$key]['end'] = $product_end_time;
                                            }
                                        }
                                    }                                   
                                }                              
                            }
                        } else{
                            $this->session->data['product_no_times']='yes';
                            return false;
                        }
                        }    
                    }
                }
                if(empty($store_open_times)){
                    $this->session->data['product_no_times']='yes';
                    return false;
                }                                                
            }            
            return $store_open_times;
        }
        else{ //Store Closed
            return false;
        }
        return false;
        if($today_times && !empty($today_times)){
            $start_time= $today_times['start']+($preparation_time * 60);
            $end_time= $today_times['end'];
            $today = strtolower(date('l',$start_time));
            if ($shipping_method == 'home.home' and !$this->config->get('home_delivery_store_default_time')) {
               $today_times = 'home_delivery_' . $today . '_times';
                $day_times = $this->config->get($today_times);
                $start_times=  explode(':', $day_times[key($day_times)]['start']);
                $end_times=  explode(':', $day_times[key(array_reverse($day_times, true))]['end']);
                if(!empty($day_times[key($day_times)]['start'])) {
                    $start_time =  mktime((int)$start_times[0],(int)$start_times[1],0, date("m"), date("d"), date("Y"))+($preparation_time * 60);
                    $end_time =  ((int) $start_times[0] > (int) $end_times[0])?mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d")+1, date("Y")):mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d"), date("Y"));
                }
            }
        }
        else{
            $today = strtolower(date('l'));
            if ($shipping_method == 'home.home' and !$this->config->get('home_delivery_store_default_time')) {
               $today_times = 'home_delivery_' . $today . '_times';
                $day_times = $this->config->get($today_times);
            }else{
                $day_times = $this->config->get($today . '_store_times');
            }
            $start_times=  explode(':', $day_times[key($day_times)]['start']);
            $end_times=  explode(':', $day_times[key(array_reverse($day_times, true))]['end']);
            if(!empty($day_times[key($day_times)]['start'])) {
                $start_time =  mktime((int)$start_times[0],(int)$start_times[1],0, date("m"), date("d"), date("Y"))+($preparation_time * 60);
                $end_time =  ((int) $start_times[0] > (int) $end_times[0])?mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d")+1, date("Y")):mktime((int)$end_times[0],(int)$end_times[1],0, date("m"), date("d"), date("Y"));
            }
        }
        $cart_products = $this->cart->getProducts();
        $categories = $this->getcategories();
        //print_r($categories);
        //print_r(array('ostart'=>  date('d-m-y H:i a',$start_time),'oend'=>  date('d-m-y H:i a',$end_time)));
        
        //SET Holiday Delivery Time Empty
        if(isset($start_time) && !empty($cart_products)){
            foreach ($cart_products as $product) {
                if($product['specific_time']==1){
                    if(!empty($product['available_times']) && isset($product['available_times'][$today])){
                        foreach ($product['available_times'][$today] as $available_time) {
                            $avl_start = explode(':', $available_time['start']);
                            $avl_end = explode(':', $available_time['end']);
                            $product_start_time =  mktime((int)$avl_start[0],(int)$avl_start[1],0, date("m"), date("d"), date("Y"));
                            $product_end_time =  ((int) $avl_start[0] > (int) $avl_end[0])?mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m"), date("d")+1, date("Y")):mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m"), date("d"), date("Y"));
                            if(($product_start_time >=$end_time)|| $product_end_time<=$start_time){
                                $this->session->data['product_no_times']='yes';
                                return false;
                            }
                            if($start_time<$product_start_time && $start_time<$end_time){
                                $start_time = $product_start_time;
                            }
                            if($product_end_time<$end_time){
                                $end_time = $product_end_time;
                            }
                        }                        
                    }else{
                         $this->session->data['product_no_times']='yes';
                         return false;
                    }
                }else{
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category c  WHERE c.product_id = '" . (int) $product['product_id'] . "'");
                    $product_categories ='';
                    foreach ($query->rows as $result) {
                        $product_categories .= ','.$this->getPath($result['category_id']);
                    }
                    $product_categories= array_unique(explode(',', trim($product_categories,',')));
                    $category_id = 0;
                    
                    foreach($product_categories as $prd_category_id) {
                        if(in_array($prd_category_id, $categories)){
                            $category_id =$prd_category_id;
                             break;
                        }
                    }
                    //echo $category_id;
                    $cquery = $this->db->query("SELECT specific_time,available_time FROM " . DB_PREFIX . "category c WHERE c.category_id = '" . (int)$category_id . "' AND c.specific_time=1");
                    if($cquery->row){
                        $cat_times = unserialize($cquery->row['available_time']);
                        //print_r($cat_times);
                        if(!empty($cat_times) && isset($cat_times[$today])){
                        foreach ($cat_times[$today] as $available_time) {
                            $avl_start = explode(':', $available_time['start']);
                            $avl_end = explode(':', $available_time['end']);
                            $product_start_time =  mktime((int)$avl_start[0],(int)$avl_start[1],0, date("m"), date("d"), date("Y"));
                            $product_end_time =  ((int) $avl_start[0] > (int) $avl_end[0])?mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m"), date("d")+1, date("Y")):mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m"), date("d"), date("Y"));
                            if(($product_start_time >=$end_time)|| $product_end_time<=$start_time){
                                $this->session->data['product_no_times']='yes';
                                return false;
                            }
                            if($start_time<$product_start_time && $start_time<$end_time){
                                $start_time = $product_start_time;
                            }
                            if($product_end_time<$end_time){
                                $end_time = $product_end_time;
                            }
                        }
                    }else{
                            $this->session->data['product_no_times']='yes';
                            return false;
                        }
                    }
                }
            }
            //echo "ss";
            unset($this->session->data['product_no_times']);
            //print_r(array('start'=>$start_time,'end'=>$end_time));
            return array('start'=>$start_time,'end'=>$end_time);
        }
        elseif(empty($cart_products)) {
            return array('start'=>$start_time,'end'=>$end_time);
        }
        else{
            return false;
        }    
    }
    public function getcategories() {
        $allcategories = array();
        $cat_ids = array();
        $categories = $this->getsubcategories(0);
        
        foreach ($categories as $category) {
            //$children_data = array();
            $children = $this->getsubcategories($category['category_id']);
            foreach ($children as $child) {
                $allcategories[] = $child;
            }
        }
         foreach ($allcategories as $cat) {
             $cat_ids[] = $cat['category_id'];
         }
        return $cat_ids;
    }
    public function getPath($category_id) {
        $query = $this->db->query("SELECT  parent_id FROM " . DB_PREFIX . "category c  WHERE c.category_id = '" . (int) $category_id . "'");
        if ($query->row['parent_id'] != 0) {
           return $category_id .','. $this->getPath($query->row['parent_id']) ;
        }
        return $category_id;
    }    
    public function getsubcategories($category_id){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
        return $query->rows;
    }
     private function isInShippingDistance($addres_postcode,$lat2, $long2){
        $store_postcode = $this->postcodeformat($this->config->get('config_postalcode'));
        $distance = null;
        $delivery_postcode_options = $this->config->get('home_delivery_postcode_options');
        foreach ($delivery_postcode_options as $postcode_option) {
            $shipping_postcodes = explode("\n", $postcode_option['postcode']);
            foreach ($shipping_postcodes as $ship_postcode) {
                if ($ship_postcode == substr($addres_postcode, 0, strlen($ship_postcode))) {
                    if($postcode_option['postcode_distance']==''){
                        $distance = 20; // MAX 20 mile uto download 
                    }
                    else {
                        $distance = $postcode_option['postcode_distance'];
                    }
                }
            }
        }
        if($distance===null) {
            $conf_home_delivery_options = $this->config->get('home_delivery_options');
            $tmp_distance = 0;
            foreach ($conf_home_delivery_options as $delivery_option) {
                if($tmp_distance < (float) $delivery_option['distance']){
                    $tmp_distance = (float) $delivery_option['distance'];
                }
            }
            if($tmp_distance==0) { // not distance
                $distance = 20; // MAX DISTANCE
            }
            else {
                $distance = $tmp_distance;
            }

        }
        $value = $this->getPostcodeDetails($store_postcode);
        if($distance > $this->getAirDistance($value['latitude'], $value['longitude'], $lat2, $long2)) {
            return $distance;
        }
        else {
            return false;
        }
        
    }
     public function getYesterdayOpeningtime() {
        //echo strtolower(date('l', strtotime('-1 days')));   
        //echo strtolower(date('l', strtotime('-2 days')));   
        $current_time = strtotime('now');        
        $yetserdaystoretime = array();
        $store_open_times = array();        
        $yesterday = strtolower(date('l', strtotime('yesterday')));        
        $yesterday_times = $yesterday . '_store_times';       
        $yesterday_open_times = $this->config->get($yesterday_times);
        $i = 0;        
        foreach ($yesterday_open_times as $yesterday_open_time) {
            if(empty($yesterday_open_time['start'])){
                continue;
            }
            $yes_start_times = explode(':', $yesterday_open_time['start']);
            $yes_end_times = explode(':', $yesterday_open_time['end']);
            $store_open_times[$i]['start']=mktime((int)$yes_start_times[0],(int) $yes_start_times[1], 0, date("m"), date("d") - 1 , date("Y"));
            $store_open_times[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 1 , date("Y"));
            if ((int)$yes_start_times[0] > (int)$yes_end_times[0])
            {
                $store_open_times[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d"), date("Y"));
            }
            $i++;
        }
        $today_status = false;
        foreach ($store_open_times as $store_open_time) {
            if ($current_time >= $store_open_time['start'] && $current_time <= $store_open_time['end']) {
                $today_status = true;
            }
        }
        if($today_status){ //if Current time is in yesterday opening times ysterday = yesterdyay-1;
            $store_open_times = array();        
            $yesterday = strtolower(date('l', strtotime('-2 days')));
            $yesterday_times = $yesterday . '_store_times';
            $yesterday_open_times = $this->config->get($yesterday_times);
            $i = 0;
            foreach ($yesterday_open_times as $yesterday_open_time) {
                if(empty($yesterday_open_time['start'])){
                    continue;
                }
                $yes_start_times = explode(':', $yesterday_open_time['start']);
                $yes_end_times = explode(':', $yesterday_open_time['end']);
                $store_open_times[$i]['start']=mktime((int)$yes_start_times[0],(int) $yes_start_times[1], 0, date("m"), date("d") - 2 , date("Y"));
                $store_open_times[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 2 , date("Y"));
                if ((int)$yes_start_times[0] > (int)$yes_end_times[0])
                {
                    $store_open_times[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d")-1, date("Y"));
                }
                $i++;
            }            
        }
        if(!empty($store_open_times)){
            //$yetserdaystoretime = array('start'=>$store_open_times[0]['start'],'end'=>$store_open_times[$i-1]['end'],'orgstart'=>date("y-m-d H:i:a",$store_open_times[0]['start']),'orgend'=>date("y-m-d H:i:a",$store_open_times[$i-1]['end']));
            $yetserdaystoretime = array('start'=>$store_open_times[0]['start'],'end'=>$store_open_times[$i-1]['end']);
        }else{
            //if not gettings yesterday time or yesterday holiday set default start and end time
            //$yetserdaystoretime = array('start'=>mktime(6,00, 0, date("m"), date("d") - 1 , date("Y")),'end'=>mktime(23, 59, 0, date("m"), date("d") - 1 , date("Y")),'orgstart'=>date("y-m-d H:i:a",mktime(6,00, 0, date("m"), date("d") - 1 , date("Y"))),'orgend'=>date("y-m-d H:i:a",mktime(23, 59, 0, date("m"), date("d") - 1 , date("Y"))));
            $yetserdaystoretime = array('start'=>mktime(6,00, 0, date("m"), date("d") - 1 , date("Y")),'end'=>mktime(23, 59, 0, date("m"), date("d") - 1 , date("Y")));
        }            
        return $yetserdaystoretime;
    }
    public function checkisDisabledfromEpos($option) {
         if($this->config->get('onlineorder_settings')){
            $updated_online_settings  = unserialize($this->config->get('onlineorder_settings'));
            if(isset($updated_online_settings[$option])  && ($updated_online_settings[$option]==FALSE || $updated_online_settings[$option]=='false' || $updated_online_settings[$option]=='0')){
                $yesterdaytime = $this->getYesterdayOpeningtime();
                if($updated_online_settings['updatedtime']>$yesterdaytime['end']){
                    return True;
                }
            }
        }
        return false;
    }
    public function getUpdatedRequestedtime($option,$store_delivery_time) {
        $newdeltime = $store_delivery_time;
        if($this->config->get('onlineorder_settings')){
            $updated_online_settings  = unserialize($this->config->get('onlineorder_settings'));
            if(isset($updated_online_settings[$option])  && ($updated_online_settings[$option]=='true' || $updated_online_settings[$option]==true || $updated_online_settings[$option]=='1') && isset($updated_online_settings[$option.'_time'])){
                $yesterdaytime = $this->getYesterdayOpeningtime();
                if($updated_online_settings['updatedtime']>$yesterdaytime['end']){
                   $newdeltime = $updated_online_settings[$option.'_time'];
                }
            }
        }
        return $newdeltime;
    }
    public function checkSetiingchangesexpired() { 
        if($this->config->get('onlineorder_settings')){
            $updated_online_settings  = unserialize($this->config->get('onlineorder_settings'));
            $yesterdaytime = $this->getYesterdayOpeningtime();
            if($updated_online_settings['updatedtime']>$yesterdaytime['end']){
               return false;
            }
        }
        return true; 
    }
    public function getStoretimeMessage() {
        $msg = '';
        $this->language->load('checkout/checkout');
        if($this->checkisDisabledfromEpos('online')){
            $msg= $this->language->get('text_onlinemsg_head'); 
        }
        $current_time = strtotime('now');
      
        $holidays = $this->config->get('store_holidays');

        $todate = strtotime(date('d-m-Y'));
        $yes_date = strtotime(date("d-m-Y", strtotime('yesterday')));
        $tom_date = strtotime(date("d-m-Y", strtotime('tomorrow')));
        
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
                $store_holiday_times[1]['end'] = mktime((int) $tod_hol_end[0], (int) $tod_hol_end[1], 0, date("m"), date("d") + 1, date("Y"));
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
                $msg = $this->language->get('text_holidaymsg_head');
            }
        }
        // Store time empty for holiday

        if(!$msg) {

            $today_times = strtolower(date('l')) . '_store_times';
            $yesterday_times = strtolower(date('l', strtotime('yesterday'))) . '_store_times';
            $tomorrow_times = strtolower(date('l', strtotime('tomorrow'))) . '_store_times';
            $store_open_times = array();
            $today_open_times = $this->config->get($today_times);
            $yesterday_open_times = $this->config->get($yesterday_times);
            $tomorrow_open_times = $this->config->get($tomorrow_times);
    
            $i = 0;
            $last_open =0;
            foreach ($today_open_times as $today_open_time) {
                if(!empty($today_open_time['start'])) {
                    $yes_start_times = explode(':', $today_open_time['start']);
                    $yes_end_times = explode(':', $today_open_time['end']);
                    $start_time=mktime((int)$yes_start_times[0], (int)$yes_start_times[1], 0, date("m"), date("d"), date("Y"));
                    $end_time=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") , date("Y"));

                    if($start_time < $current_time) {
                        if($end_time > $current_time) {
                            return '';
                        } else if($end_time < $start_time) {
                            return '';
                        }
                    } else {
                        if(!$last_open || $last_open > $start_time) {
                            $last_open = $start_time;
                            $msg = sprintf($this->language->get('text_store_open_time'),date('h:i a', $start_time));                        
                        }
                    }                  
                }
                
            }
            
            if (!$msg) {
                $last_close_time=0;
                foreach ($yesterday_open_times as $yesterday_open_time) {
                    if(empty($yesterday_open_time['start'])){
                        continue;
                    }
                    $yes_start_times = explode(':', $yesterday_open_time['start']);
                    $yes_end_times = explode(':', $yesterday_open_time['end']);
                    
                    $end_time=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 1 , date("Y"));
                    if ((int)$yes_start_times[0] > (int)$yes_end_times[0])
                    {
                        $end_time =mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d"), date("Y"));
                    }
                    if ( $last_close_time < $end_time ) {
                         $last_close_time = $end_time;
                    }
                     
                }
                $next_start_time=0;
                if(!isset($holidays[$tom_date])) {
                    foreach ($tomorrow_open_times as $tomorrow_open_time) {
                        if(empty($tomorrow_open_time['start'])){
                            continue;
                        }
                        $tom_start_times = explode(':', $tomorrow_open_time['start']);
                        $tom_end_times = explode(':', $tomorrow_open_time['end']);
                        $start_time=mktime((int)$tom_start_times[0], (int)$tom_start_times[1], 0, date("m"), date("d")+1, date("Y"));
                        if($next_start_time) {
                            if ( $next_start_time > $start_time ) {
                                $next_start_time = $start_time;
                            }
                        } else if( $next_start_time < $start_time ) {
                            $next_start_time = $start_time;
                        }
                    }
                }
                
                if( $last_close_time!=0 && $last_close_time >=$current_time  ){
                    return '';
                }
                if($next_start_time!=0 && $next_start_time <= $current_time) {
                     return '';
                }

                $msg = $this->language->get('text_store_closed');
                if($next_start_time!=0) {
                    $msg = sprintf($this->language->get('text_store_tomorrow_time'),date('h:i a', $next_start_time));
                }
                
            }
    
        }
        
        return $msg;
    }
      private function getTodaysordercount (){
        $retdata = array();
        if($this->config->get('config_ordering_limit_count') && $this->config->get('config_ordering_limit_count')!='' && $this->config->get('config_ordering_limit_time') && $this->config->get('config_ordering_limit_time')!='' && $this->config->get('config_ordering_slot_difference')){
            
            if($this->config->get('config_ordering_slot_difference')) {
                $timelt = $this->config->get('config_ordering_slot_difference') * 60;
            } else {
                $timelt = 900;
}

            $orderqry = $this->db->query("SELECT count(*) as ordercount, (UNIX_TIMESTAMP(requested_time) - ( UNIX_TIMESTAMP(requested_time)%(".$timelt."))) as untkey FROM `order` WHERE order_status_id NOT IN (0,7,10) AND requested_time>(NOW()- INTERVAL 1 DAY)  GROUP BY untkey ");        
            foreach($orderqry->rows as $data){
                $retdata[$data['untkey']] = $data['ordercount'];
            }
        }
        
        return $retdata;
    }
    public function getTodaysShippingordercount ($shippingcode='home.home', $missingorder=0, $orderid = 0){
        $retdata = array();
        $timelimit = 900;
        $orderlimit = 0;
        if($shipping_code=='home.home') {
            if($this->config->get('config_home_ordering_limit_count')) {
                $orderlimit = (int)$this->config->get('config_home_ordering_limit_count');
            }
        } else {
            $timelimit = 900; 
            if($this->config->get('config_collect_ordering_limit_count')) {
                $orderlimit = (int)$this->config->get('config_collect_ordering_limit_count');
            }
        }
        if($orderlimit){
            $orderqry = $this->db->query("SELECT count(*) as ordercount, (UNIX_TIMESTAMP(requested_time) - ( UNIX_TIMESTAMP(requested_time)%(".$timelimit."))) as untkey FROM `order` WHERE shipping_code='".$this->db->escape($shippingcode)."' AND order_status_id NOT IN (0,7,10) AND requested_time>(NOW()- INTERVAL 1 DAY)  GROUP BY untkey ");        
            foreach($orderqry->rows as $data){
                $retdata[$data['untkey']] = $data['ordercount'];
            }
            if($missingorder) {
                $date_added = date('Y-m-d H:i:s',strtotime('-15 minutes'));
                if($orderid) {
                    $orderqry = $this->db->query("SELECT count(o.order_id) as ordercount FROM  `".DB_PREFIX."order` o  WHERE order_status_id = 0 AND requested_time = '".date('Y-m-d H:i:s',$missingorder)."' AND shipping_code='".$this->db->escape($shippingcode)."' AND date_added >= '".$date_added."' AND  o.order_id!= '".$orderid."' ");
                } else {
                    $orderqry = $this->db->query("SELECT count(o.order_id) as ordercount FROM  `".DB_PREFIX."order` o  WHERE order_status_id = 0  AND requested_time = '".date('Y-m-d H:i:s',$missingorder)."' AND shipping_code='".$this->db->escape($shippingcode)."' AND date_added >= '".$date_added."' ");                
                }
                if(isset($orderqry->row['ordercount']) && $orderqry->row['ordercount']) {
                    if(isset($retdata[$missingorder])) {
                        $retdata[$missingorder]+=$orderqry->row['ordercount'];
                    } else {
                        $retdata[$missingorder] = $orderqry->row['ordercount'];
                    }
                }
            }
        }        
        return $retdata;
    }
    public function getAllRequestedtimes($shipping_method) {
        $yesterday = strtolower(date('l', strtotime('yesterday')));
        $today = strtolower(date('l'));
        $weekdays = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $weekday_cutof_satatus = false;
        $ordering_cutof_status = false;
        if($this->config->get('config_preordering_condition')){//Check for cutoff day
            if($this->config->get('config_preordering_condition')=='w' && $this->config->get('config_preordering_cutoff_day')!=''){
                $todayindex = array_search($today, $weekdays);
                $cutdayindex = array_search($this->config->get('config_preordering_cutoff_day'), $weekdays);
                if($todayindex>$cutdayindex){
                     $weekday_cutof_satatus = true;
                }else{
                    $di=0;
                    for ($tc = $todayindex; $tc < 6; $tc++) {
                        $day_name = strtolower(date('l', strtotime("+".$di."days")));
                        if($day_name==$this->config->get('config_preordering_cutoff_day')){
                            $cut_time = ($this->config->get('config_preordering_cutoff_daytime')!='')?$this->config->get('config_preordering_cutoff_daytime'):'00:00';
                            $cttimes = explode(':', $cut_time);
                            $cutimestamp = mktime((int)$cttimes[0], (int)$cttimes[1], 0, date("m"), date("d")+$di, date("Y"));
                            if(time()>=$cutimestamp){                            
                                 $weekday_cutof_satatus = true;
                            }
                            break;  
                        }
                        $di+=1;
                    }
                }
            }else{//
                if($this->config->get('config_ordering_cutoff_time')!='' && $this->config->get('config_ordering_cutoff_condition')!=''){
                    $cut_time = $this->config->get('config_ordering_cutoff_time');
                    $cttimes = explode(':', $cut_time);
                    $cutimestamp = mktime((int)$cttimes[0], (int)$cttimes[1], 0, date("m"), date("d"), date("Y"));
                    if(time()>=$cutimestamp){                            
                        $ordering_cutof_status = $this->config->get('config_ordering_cutoff_condition');
                    }
                }
            }
            
        }
       
        if(($ordering_cutof_status=='a') || ($weekday_cutof_satatus && ($this->config->get('config_preordering_days')=='' || (int)$this->config->get('config_preordering_days')==0))){
            return array();
        }
        if ($shipping_method == 'home.home' and !$this->config->get('home_delivery_store_default_time')) {
            $today_times = 'home_delivery_' . $today . '_times';
            $yesterday_times = 'home_delivery_' . $yesterday . '_times';
        }else if ($shipping_method == 'collect.collect' && $this->config->get('collect_different_time')) {
            $today_times = 'collect_' . $today . '_times';
            $yesterday_times = 'collect_' . $yesterday . '_times';
        }
        else{
            $today_times = $today . '_store_times';
            $yesterday_times = $yesterday . '_store_times';
        }
        
        $today_open_times = $this->config->get($today_times);
        $yesterday_open_times = $this->config->get($yesterday_times);
        $startday = 0; 
        
        $current_time = strtotime('now');
        $yesterdaytimes = array();
        $i=0;
        $all_available_times=array();
        $yesterdaystatus = false;
        foreach ($yesterday_open_times as $yesterday_open_time) {
            if(empty($yesterday_open_time['start'])){
                continue;
            }
            $yes_start_times = explode(':', $yesterday_open_time['start']);
            $yes_end_times = explode(':', $yesterday_open_time['end']);
            $yesterdaytimes[$i]['start']=mktime((int)$yes_start_times[0],(int) $yes_start_times[1], 0, date("m"), date("d") - 1 , date("Y"));
            $yesterdaytimes[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 1 , date("Y"));
            if ((int)$yes_start_times[0] > (int)$yes_end_times[0])
            {
                $yesterdaytimes[$i]['end']=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d"), date("Y"));
            }
            if ($current_time >= $yesterdaytimes[$i]['start'] && $current_time <= $yesterdaytimes[$i]['end']) {
                $yesterdaystatus = false;
            }
            $i++;
        }
        if($yesterdaystatus && !$weekday_cutof_satatus && $ordering_cutof_status!='c'){
            $lk = array_search($yesterday, $weekdays);            
            $all_available_times[] = array('STA'=>"YES",'day'=>"Today",'timestamp'=>strtotime('yesterday'),'times'=> $yesterdaytimes,'today'=>true);
            $startday -=1;
        }
        if(!$this->config->get('config_preordering_status')  && $yesterdaystatus){
            return $all_available_times;
        }
        //$this->config->get('config_preordering_condition')
        //
        $preorder_days =0;
        if($this->config->get('config_preordering_days') && $this->config->get('config_preordering_days')!=''){
            if($this->config->get('config_preordering_condition')=='w'){
                if($weekday_cutof_satatus){
                    $preorder_days =(int)$this->config->get('config_preordering_days')*7;
                }else{
                    $preorder_days =((int)$this->config->get('config_preordering_days')-1)*7;
                }                
            }else{
                 $preorder_days =(int)$this->config->get('config_preordering_days');
            }           
        }
        if(!$weekday_cutof_satatus && (!$yesterdaystatus) && $ordering_cutof_status!='c'){ 
            $tody_times = array();
            $i=0;
            foreach ($today_open_times as $today_open_time) {
                if(empty($today_open_time['start'])){
                    continue;
                }
                $tod_start_times = explode(':', $today_open_time['start']);
                $tod_end_times = explode(':', $today_open_time['end']);
                $tody_times[$i]['start']=mktime((int)$tod_start_times[0], (int)$tod_start_times[1], 0, date("m"), date("d"), date("Y"));

                $tody_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d"), date("Y"));
                if ((int)$tod_start_times[0] > (int)$tod_end_times[0])
                {
                    $tody_times[$i]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+1, date("Y"));
                }    
                $i++;
            }
            if(!empty($tody_times)){
                $all_available_times[] = array('STA'=>"TOD",'day'=>"Today",'timestamp'=>strtotime('now'),'times'=> $tody_times,'today'=>(!$yesterdaystatus)?true:false);;   
                 $preorder_days -=1;
            }
        }
        if(!$this->config->get('config_preordering_status')){
            return $all_available_times;
        }
        $tc = 1;
        if(!$weekday_cutof_satatus && $yesterdaystatus){
            $preorder_days -=1;
            $tc = 0;
        }
        $lk = array_search($today, $weekdays);       
       
        
        if(!$weekday_cutof_satatus && $this->config->get('config_preordering_condition') && $this->config->get('config_preordering_condition')=='w'){
            for ($n = $lk; $n < 6; $n++) {
                 $day_name = strtolower(date('l', strtotime("+".$tc."days")));
                if ($shipping_method == 'home.home' and !$this->config->get('home_delivery_store_default_time')) {                  
                    $day_times = $this->config->get('home_delivery_' . $day_name . '_times');
                }else if ($shipping_method == 'collect.collect' && $this->config->get('collect_different_time')) {
                    $day_times = $this->config->get('collect_' . $day_name . '_times');
                }
                else{
                    $day_times = $this->config->get($day_name . '_store_times');
                }
                 
                 $store_open_times = array();
                 $fw=0;
                 foreach($day_times as  $day_time){
                    if($day_time['start']!='' && $day_time['end']!=''){
                        $tod_start_times = explode(':', $day_time['start']);
                        $tod_end_times = explode(':', $day_time['end']);
                        $store_open_times[$fw]['start']=mktime((int)$tod_start_times[0], (int)$tod_start_times[1], 0, date("m"), date("d")+$tc, date("Y"));
                        $store_open_times[$fw]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+$tc, date("Y"));
                        if ((int)$tod_start_times[0] > (int)$tod_end_times[0])
                        {
                            $store_open_times[$fw]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+$tc+1, date("Y"));
                        } 
                        $fw+=1;
                    }
                 }
                 if($tc==0){
                     $day ="Today";
                 }elseif($tc==1){
                     $day ="Tomorrow".date(' d F', strtotime("+".$tc."days"));
                 }else{
                     $day =date('l d F', strtotime("+".$tc."days"));
                 }
                 
                $all_available_times[] = array('STA'=>"FW-->".$tc,'day'=>$day,'timestamp'=>strtotime("+".$tc."days"),'times'=> $store_open_times);;
                $tc+=1;
            }
        }
        if($weekday_cutof_satatus){
            $tc += 6-$lk;
        }
        //Next Two weeks
         if($ordering_cutof_status=='p'){//cut pre orders
            //return $all_available_times;
             $tc +=1;
        }
        for ($n = 0; $n < $preorder_days; $n++) {
            $day_name = strtolower(date('l', strtotime("+".$tc."days"))); 
            if ($shipping_method == 'home.home' and !$this->config->get('home_delivery_store_default_time')) {                  
                $day_times = $this->config->get('home_delivery_' . $day_name . '_times');
            }else if ($shipping_method == 'collect.collect' && $this->config->get('collect_different_time')) {
                $day_times = $this->config->get('collect_' . $day_name . '_times');
            }
            else{
                $day_times = $this->config->get($day_name . '_store_times');
            }
             
             $store_open_times = array();
             $sw=0;
             foreach($day_times as  $day_time){
                if($day_time['start']!='' && $day_time['end']!=''){
                    $tod_start_times = explode(':', $day_time['start']);
                    $tod_end_times = explode(':', $day_time['end']);
                    $store_open_times[$sw]['start']=mktime((int)$tod_start_times[0], (int)$tod_start_times[1], 0, date("m"), date("d")+$tc, date("Y"));
                    $store_open_times[$sw]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+$tc, date("Y"));
                    if ((int)$tod_start_times[0] > (int)$tod_end_times[0])
                    {
                        $store_open_times[$sw]['end']=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+$tc+1, date("Y"));
                    } 
                    $sw+=1;
                }
             }
            if($tc==0){ 
                $day ="Today";
            }elseif($tc==1){
                $day ="Tomorrow".date(' d F', strtotime("+".$tc."days"));
            }else{
                $day =date('l d F', strtotime("+".$tc."days"));
            }
            $all_available_times[] =array('STA'=>"SW-->".$tc,'day'=>$day,'timestamp'=>strtotime("+".$tc."days"),'times'=> $store_open_times);
            $tc+=1;
        }
        return $all_available_times;
        
    }
    public function getnewrequestedtimes($store_delivery_time,$current_time,$preparation_time=0,$shipping_method) {      
        if(!$this->config->get('config_preordering_status')){
            $todaystimes = $this->getrequestedtimes($store_delivery_time, $current_time, $preparation_time,$shipping_method);
            if(!empty($todaystimes)){
                $requested_days = [];
                $requested_days[]=array('day'=>'today','timestamp'=>date("d-m-y",time()));
                return  array('days'=>$requested_days,'times'=>array(0=>$todaystimes));
            }      
           return array();
        }
        $order_limit_count=1000;
        $all_day_times= $this->getAllRequestedtimes($shipping_method);
        $asap_status = false;
        $first_slots_status = false;
        $asapchek = 0;
        $timslot_dif = (int)$this->config->get('config_ordering_slot_difference');
        if(!$timslot_dif) {
            $timslot_dif = 15;
        }
        if($this->config->get('config_ordering_limit_count') && $this->config->get('config_ordering_limit_count')!='' && $this->config->get('config_ordering_limit_time') && $this->config->get('config_ordering_limit_time')!=''){
            $c =$this->config->get('config_ordering_limit_time')/$timslot_dif; 
            $todaysorders = $this->getTodaysordercount();
            $pre_order_limit = (int)$this->config->get('config_ordering_limit_count');
        }else{
            $c = 1;  
            $todaysorders=array();
            $pre_order_limit=1000;
        }  
        if($shipping_code=='home.home') {
            if($this->config->get('config_home_ordering_limit_count')) {
                $pre_order_limit = (int)$this->config->get('config_home_ordering_limit_count');
            }
        } else {
        
            if($this->config->get('config_collect_ordering_limit_count')) {
                $pre_order_limit = (int)$this->config->get('config_collect_ordering_limit_count');
            }
        }
         $todaysorders = $this->getTodaysShippingordercount($shipping_method);
        $pre_orderingtime = ($this->config->get('config_preordering_status') && $this->config->get('config_preordering_cutoff_time')!='')?(int)$this->config->get('config_preordering_cutoff_time'):0;
        
        $holidays = $this->config->get('store_holidays');

        //$this->log->write('line 1664 - '.$c.'->'.json_encode($todaysorders).'-->'.json_encode($all_day_times));

        $all_reqtimes= array();
        $all_requested_days = array();        
        $timslot_dif = $timslot_dif*60;
        foreach($all_day_times as $day){
            $holchk = strtotime(date("d-m-Y", $day['timestamp']));
            if (isset($holidays[$holchk])) {
                $availdaytimes = array();
            }else{
                $availdaytimes= $this->getCartUpdatedtimes($day['times'],$day['timestamp']);
            }
            //$this->log->write(date('h:i a',$day['timestamp']));
            //$this->log->write(json_encode($availdaytimes));
            //$this->log->write('line 1891 : '.json_encode($all_day_times));
            $requested_times = array();
            $all_avialble_times=array();
            $availdaytimes= $this->getCartUpdatedtimes($day['times'],$day['timestamp']);
            $totaldayslot = count($availdaytimes)-1;
            $totcount = 0;
            $s=1;
            $req_time=array();
            $slot_times = array();
            $all_times = array();
            $basedonday = false;
            $slotcheck = 72000;
            foreach ($availdaytimes as $dayslot => $daytime) {
                if($daytime['end']-$daytime['start'] < $slotcheck ) {
                   // $basedonday = true;
                }
                break;
            }

            foreach ($availdaytimes as $dayslot => $daytime) {
                if($pre_orderingtime>0 && $daytime['start']<($current_time+($pre_orderingtime*60*60))){
                    break;
                }     
                
                $first_time = $daytime['start'] + ( 0 * 60)+($preparation_time * 60);
                $second_time = $daytime['end']-($store_delivery_time * 60);
                if ($current_time < $second_time) { //&& 
                    if ($current_time > $daytime['start']) {
                       // echo $store_delivery_time."==".$current_time.'-->';
                        $current_time += ( 0 * 60)+($preparation_time * 60);
                        //echo $current_time.'-->';
                        $timeintchange = ($current_time % ($timslot_dif ));
                        //echo "AA-->".$timeintchange;
                        if ($timeintchange < 300)  // 300 = 5 minits
                            $current_time = $current_time - $timeintchange;
                        else
                            $current_time = $current_time - $timeintchange + $timslot_dif;
                        
                        $first_time = $current_time;
                    }
                    else{
                        $timeintchange = ($first_time % ($timslot_dif ));
                        //echo "AA-->".$timeintchange;
                        if ($timeintchange < 300)  // 300 = 5 minits
                            $first_time = $first_time - $timeintchange;
                        else
                            $first_time = $first_time - $timeintchange + $timslot_dif;
                    }
                    
                    //echo "--BB-".$first_time."-->".($first_time % (900 ));
                    
                    //
                   
                    //$all_times = array();                                    
                    //$s=1;
                    //$req_time=array();
                    //$totcount = 0; 
                    
                    for ($i = $first_time; $i <= $second_time; $i = $i + $timslot_dif) {
                        $totcount +=(empty($todaysorders) || (!isset($todaysorders[$i])))?0:(int)$todaysorders[$i];
                        $req_time[$i] = ($this->config->get('config_preordering_status'))?date("h:i A", $i):date("h:i A (l)", $i);   
                        if($s==$c){
                            if($totcount<(int)$pre_order_limit){
                                $all_times = array_replace($all_times,$req_time);
                            }else{
                                if($asapchek==0){
                                    $first_slots_status = true;
                                }                                    
                            }    
                            
                            $req_time=array();
                            if(!$basedonday) {
                                $totcount = 0;                                
                            }
                            $s=1;
                            $asapchek+1;
                        }else{
                            $s+=1;
                        }                                                                                                                                               
                    } 

                    if($req_time && $totcount < $pre_order_limit) {
                        if($dayslot === $totaldayslot) {
                            $all_times = array_replace($all_times,$req_time);
                        } else if($s > 1) {
                            $all_times = $req_time;
                        }
                    }
                    
                    $actual_first_time =  $first_time+$store_delivery_time*60;
                    foreach($all_times as $k=>$time){
                        if($k<$actual_first_time){
                            unset($all_times[$k]);
                        }
                    }
                    
                    /*
                    $asap_time = array(); 
                    if ($shipping_method == 'home.home' && empty($all_times) && !$asap_status && $preparation_time == 0 && $daytime['start'] < $current_time && $this->isStoreOpened($this->config->get('config_store_id'))) {
                        $asap_time[time()] = $this->language->get('text_asap_time'); 
                        $asap_status  =true;
                    }
                    */
                    //$requested_times =array_replace($asap_time, $all_times);
                    //$this->log->write('line 1771 - '.$day['day'].'->'.json_encode($requested_times));                                        
                }
                 $asap_time = array();  
                if (!$asap_status && $daytime['start'] < time() && $second_time >time()  && $this->isStoreOpened($this->config->get('config_store_id'))) {
                    $asap_time[time()] = $this->language->get('text_asap_time'); 
                    $asap_status  =true;

                }
                $requested_times =array_replace($asap_time, $all_times);   
                $all_avialble_times =array_replace($all_avialble_times, $requested_times);   
            }

            if($basedonday && $totcount >= $pre_order_limit ) {
                $all_avialble_times = array();
            }
            if(!empty($all_avialble_times)){
                $all_requested_days[] = array('day'=>$day['day'],'timestamp'=>date("d-m-y",$first_time)); 
                $all_reqtimes[] = $all_avialble_times;//array('date'=>$day['day'],'times'=>$requested_times);
            }            
        }
        if(!empty($all_requested_days)){
            return array('days'=>$all_requested_days,'times'=>$all_reqtimes);
        }else{
            return array();
        }        
    }
    public function getTodayStoreOpentimes() {
        $status = FALSE;
        $current_time = strtotime('now');
        $today = strtolower(date('l'));
        $yesterday = strtolower(date('l', strtotime('yesterday')));
        $tomorrow = strtolower(date('l', strtotime('tomorrow')));
        $today_times = $today . '_store_times';
        $yesterday_times = $yesterday . '_store_times';
        $tomorrow_times = $tomorrow . '_store_times';
        $store_open_times = array();
        $today_open_times = $this->config->get($today_times);
        $yesterday_open_times = $this->config->get($yesterday_times);
        $tomorrow_open_times = $this->config->get($tomorrow_times);

        $i = 0;
        
        if($yesterday_open_times) {
            foreach ($yesterday_open_times as $yesterday_open_time) {
                if(empty($yesterday_open_time['start'])){
                    continue;
                }
                $yes_start_times = explode(':', $yesterday_open_time['start']);
                $yes_end_times = explode(':', $yesterday_open_time['end']);
                if(!isset($yesstarttime)){
                    $yesstarttime=mktime((int)$yes_start_times[0],(int) $yes_start_times[1], 0, date("m"), date("d") - 1 , date("Y"));
                }            
                $yesendtime=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d") - 1 , date("Y"));
                if ((int)$yes_start_times[0] > (int)$yes_end_times[0])
                {
                    $yesendtime=mktime((int)$yes_end_times[0], (int)$yes_end_times[1], 0, date("m"), date("d"), date("Y"));
                }
                
            }
            if ($current_time >= $yesstarttime && $current_time <= $yesendtime) {
                    $status = TRUE;
                    return array('start'=>$yesstarttime,'end'=>$yesendtime);
            }

        }
        // print_r($nn);
        foreach ($today_open_times as $today_open_time) {
            if(empty($today_open_time['start'])){
                continue;
            }
            $tod_start_times = explode(':', $today_open_time['start']);
            $tod_end_times = explode(':', $today_open_time['end']);
            if(!isset($todstart)){
                $todstart=mktime((int)$tod_start_times[0], (int)$tod_start_times[1], 0, date("m"), date("d"), date("Y"));
            }                       
            $todend=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d"), date("Y"));
            if ((int)$tod_start_times[0] > (int)$tod_end_times[0])
            {
                
                $todend=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+1, date("Y"));
            }              
        }
        if(isset($todstart)){
            return array('start'=>$todstart,'end'=>$todend);
        }
        $tod_start_times = explode(':', "6:00");
        $tod_end_times = explode(':', "6:00");
        $todstart=mktime((int)$tod_start_times[0], (int)$tod_start_times[1], 0, date("m"), date("d"), date("Y"));
        $todend=mktime((int)$tod_end_times[0], (int)$tod_end_times[1], 0, date("m"), date("d")+1, date("Y"));
        return array('start'=>$todstart,'end'=>$todend);
        
    }
    public function getCartUpdatedtimes($store_open_times,$time_stamp){ 
        unset($this->session->data['product_no_times']);
        $today = strtolower(date('l',$time_stamp));
        $categories = $this->getcategories();
        foreach ($this->cart->getProducts() as $product) {
            if($product['specific_time']==1){
                if(!empty($product['available_times']) && isset($product['available_times'][$today])){
                    $avl_status = true;
                    $current_available_times = $store_open_times;
                    foreach ($product['available_times'][$today] as $available_time) {
                        if($available_time['start']==''){
                            //$this->session->data['product_no_times']='yes';
                            return array();
                        }
                        $avl_start = explode(':', $available_time['start']);
                        $avl_end = explode(':', $available_time['end']);
                        $product_start_time =  mktime((int)$avl_start[0],(int)$avl_start[1],0, date("m",$time_stamp), date("d",$time_stamp), date("Y",$time_stamp));
                        $product_end_time =  ((int) $avl_start[0] > (int) $avl_end[0])?mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m",$time_stamp), date("d",$time_stamp)+1, date("Y",$time_stamp)):mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m",$time_stamp), date("d",$time_stamp), date("Y",$time_stamp));                                                                
                        foreach($current_available_times as $key=>$current_available_time){
                            if($product_start_time>$current_available_time['end']){ // Not in SLOT
                                unset($store_open_times[$key]);
                            }else {
                                if($product_start_time>=$current_available_time['start']){
                                    $store_open_times[$key]['start'] = $product_start_time;
                                }
                                if($product_end_time<=$current_available_time['end']){
                                    $store_open_times[$key]['end'] = $product_end_time;
                                }
                            }                                   
                        }                              
                    }
                } 
                else{
                    //$this->session->data['product_no_times']='yes';
                    return array();
                }
            }
            else{

                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category c  WHERE c.product_id = '" . (int) $product['product_id'] . "'");
                $product_categories ='';
                foreach ($query->rows as $result) {
                    $product_categories .= ','.$this->getPath($result['category_id']);
                }
                $product_categories= array_unique(explode(',', trim($product_categories,',')));
                $category_id = 0;
                //print_r($product_categories);
                foreach($product_categories as $prd_category_id) {
                    if(in_array($prd_category_id, $categories)){
                        $category_id =$prd_category_id;
                        break;
                    }
                }
                //echo "CAT ID->".$category_id;
                $cquery = $this->db->query("SELECT specific_time,available_time FROM " . DB_PREFIX . "category c WHERE c.category_id = '" . (int)$category_id . "' AND c.specific_time=1");
                if($cquery->row){
                    $cat_times = unserialize($cquery->row['available_time']);
                    if(!empty($cat_times) && isset($cat_times[$today])){
                    $avl_status = true;
                    $current_available_times = $store_open_times;
                    foreach ($cat_times[$today] as $available_time) {
                        if($available_time['start']==''){
                            //$this->session->data['product_no_times']='yes';
                            return array();
                        }
                        $avl_start = explode(':', $available_time['start']);
                        $avl_end = explode(':', $available_time['end']);
                        $product_start_time =  mktime((int)$avl_start[0],(int)$avl_start[1],0, date("m",$time_stamp), date("d",$time_stamp), date("Y",$time_stamp));
                        $product_end_time =  ((int) $avl_start[0] > (int) $avl_end[0])?mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m",$time_stamp), date("d",$time_stamp)+1, date("Y",$time_stamp)):mktime((int)$avl_end[0],(int)$avl_end[1],0, date("m",$time_stamp), date("d",$time_stamp), date("Y",$time_stamp));                                                                
                        //echo $product_start_time."-->".$product_end_time;
                        //print_r($store_open_times);
                        foreach($current_available_times as $key=>$current_available_time){
                            if($product_start_time>$current_available_time['end']){ // Not in SLOT
                                unset($store_open_times[$key]);
                                //echo "UNSET";
                            }else {
                                if($product_start_time>=$current_available_time['start']){
                                    $store_open_times[$key]['start'] = $product_start_time;
                                }
                                if($product_end_time<=$current_available_time['end']){
                                    $store_open_times[$key]['end'] = $product_end_time;
                                }
                            }                                   
                        }         
                       
                    }
                }
                else{
                    //$this->session->data['product_no_times']='yes';
                    return array();
                }
                }    
            }
        }
        
        return $store_open_times;
           
    }
}
?>