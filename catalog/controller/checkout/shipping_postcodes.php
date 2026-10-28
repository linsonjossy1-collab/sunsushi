<?php
class ControllerCheckoutShippingPostcodes extends Controller {
    public function index() {
        $this->language->load('checkout/checkout');
        unset($this->session->data['nostore']);
        unset($this->session->data['postcode']);
        $json = array();
        if(isset($this->session->data['delivery_method']) && isset($this->session->data['tableordering']) && isset($this->session->data['dine_table_id'])){
            $json['store_url'] = '';
            $json['postcode'] = '';
            $json['distance'] = 0;
            $json['delivery_type'] = 'dine.dine';
            $this->response->setOutput(json_encode($json));
            return;
        }
        if(isset($this->request->post['delivery_method']) && isset($this->session->data['delivery_method'])) {
            if($this->session->data['delivery_method']!=$this->request->post['delivery_method']) {
                unset($this->session->data['delivery_method']);
            }
        }
        if (isset($this->request->post['postcode'])) {
            $postcode = $this->request->post['postcode']; // =  'HG3 0EQ' ; //'CM1 3GP'; //  'LS6 1HY'; // ;  
            $this->load->model('setting/store');
            $stores = $this->model_setting_store->getStores();
            $stores_full = array();
            $ssl = $this->config->get('config_use_ssl');

            $this->load->model('setting/setting');
            if (empty($ssl))
                $ssl = '';
            $stores_full[] = array('store_id' => '0', 'name' => $this->config->get('config_title'), 'url' => $this->config->get('config_url'), 'ssl' => $ssl, 'config' => $this->model_setting_setting->getSetting('config', 0));
            foreach ($stores as $store) {
                if ($this->config->get('pos_to_storeid') != $store['store_id']) {
                    $store['config'] = $this->model_setting_setting->getSetting('config', $store['store_id']);
                    $stores_full[] = $store;
                }
            }
            $stores = $stores_full;
            // Distance calculations
            $distance = array();
            $postal_code = $this->postcode->postcodeformat($postcode);
            $destination_Lag = $this->postcode->getPostcodeDetails($postal_code);

            if (empty($destination_Lag) || (isset($destination_Lag['address']) && $destination_Lag['address'] == '')) {
                $destination_Lag = $this->postcode->getEtmPostcodedata($postal_code);
                //$destination_Lag = $this->postcode->getPostcodeDetails($postal_code);
            }
            if (!empty($destination_Lag)) {
                foreach ($stores as $key => $store) {
                    $store_postal_code = $this->postcode->postcodeformat($store['config']['config_postalcode']);
                    $store_Lag = $this->postcode->getPostcodeDetails($store_postal_code);
                    $distance_data = $this->postcode->getPostcodeDistance($postal_code, $store['store_id']);
                    if (!empty($store_Lag)) {

                        if (!empty($distance_data)) {

                            $stores[$key]['distance'] = $distance_data['distance'];
                        } else {

                            $stores[$key]['distance'] = $this->postcode->googleDistance($store_postal_code, $postal_code, $store['store_id']);
                        }

                        //$stores[$key]['distance'] = $this->postcode->getAirDistance($store_Lag['latitude'], $store_Lag['longitude'], $destination_Lag['latitude'], $destination_Lag['longitude']);
                    } else {
                        $postcodedetails = $this->postcode->getEtmPostcodedata($store_postal_code);
                        if (!empty($postcodedetails)) {
                            $stores[$key]['distance'] = $this->postcode->googleDistance($store_postal_code, $postal_code, $store['store_id']);
                            //$stores[$key]['distance'] = $this->postcode->getAirDistance($postcodedetails['latitude'], $postcodedetails['longitude'], $destination_Lag['latitude'], $destination_Lag['longitude']);
                        } else {
                            $stores[$key]['distance'] = NULL;
                        }
                    }
                    $distance[$key] = $stores[$key]['distance'];
                }
                asort($distance, SORT_NUMERIC);
                reset($distance);

                $neareststorekey = key($distance);
                $nearestdistance = $distance[$neareststorekey];
                $redirect = '';
                if ($stores[$neareststorekey]['store_id'] != $this->config->get('config_store_id')) {
                    $redirect = $stores[$neareststorekey]['url'];
                }
                if (empty($redirect)) {
                    $shipping_address = array('firstname' => '',
                        'lastname' => '',
                        'company' => '',
                        'company_id' => '',
                        'tax_id' => '',
                        'address_1' => '',
                        'address_2' => '',
                        'postcode' => $postal_code,
                        'city' => '',
                        'zone_id' => $this->config->get('config_zone_id'),
                        'zone' => '',
                        'zone_code' => '',
                        'country_id' => $this->config->get('config_country_id'),
                        'country' => '',
                        'iso_code_2' => '',
                        'iso_code_3' => '',
                        'address_format' => '');
                    if (!empty($shipping_address)) {
                        // Shipping Methods
                        $quote_data = array();

                        $this->load->model('setting/extension');

                        $results = $this->model_setting_extension->getExtensions('shipping');

                        foreach ($results as $result) {
                            if ($this->config->get($result['code'] . '_status')) {
                                $this->load->model('shipping/' . $result['code']);

                                $etm_t = microtime(true); $quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address); $GLOBALS['etm_perf'][] = array('shipping_postcodes getQuote/' . $result['code'], microtime(true) - $etm_t); // timing for ?etm_perf=1
                                if(isset($quote['delivery_msg_status']) && $quote['delivery_msg_status']!=''){
                                    $home_delivery_error =$quote['delivery_msg_status'];
                                }
                                // print_r($quote);
                                if ($quote) {
                                    $quote_data[$result['code']] = array(
                                        'title' => $quote['title'],
                                        'quote' => $quote['quote'],
                                        'sort_order' => $quote['sort_order'],
                                        'error' => $quote['error']
                                    );
                                }
                            }
                        }
                        $sort_order = array();

                        foreach ($quote_data as $key => $value) {
                            $sort_order[$key] = $value['sort_order'];
                        }
                        array_multisort($sort_order, SORT_ASC, $quote_data);

                    }
                    //$this->log->write(print_r($quote_data,true));
                    $json['store_url'] = '';
                    $json['postcode'] = $postal_code;
                    $json['distance'] = $nearestdistance;
                    if (isset($quote_data['home']) && !empty($quote_data['home']['quote']) && $quote_data['home']['quote']['home']['code'] != '') {
                        $json['delivery_type'] = 'home';
                    } else {
                        $json['delivery_type'] = 'collect';
                    }
                    if(isset($home_delivery_error)){
                        $json['home_delivery_error'] = $home_delivery_error;
                    }
                    $json['nostore'] = 0;
                    if (!$this->postcode->isStoreOpened($this->config->get('config_store_id')) && $this->config->get('config_store_time_orders')) {
                        $json['nostore'] = 1;
                    }

                    $this->session->data['postcode'] = $postal_code;
                    $this->session->data['nostore'] = $json['nostore'];
                } else {
                    $json['distance'] = $nearestdistance;
                    $json['store_url'] = $redirect;
                    $json['postcode'] = $postal_code;
                    $this->session->data['postcode'] = $postal_code;
                }
            } else {
                $json['store_url'] = '';
                $json['postcode'] = '';
                $json['distance'] = '';
                $json['delivery_type'] = '';
            }
        }
        if(isset($this->session->data['shipping_method']) && isset($this->session->data['shipping_method']['code']) && $this->session->data['shipping_method']['code']!=$json['delivery_type'].'.'.$json['delivery_type']) {
            unset($this->session->data['shipping_method']);
        }
        //  print_r($json); 
        $this->response->setOutput(json_encode($json));
    }



    public function cartshipping() {
        $this->language->load('checkout/checkout');
        /*
        if ($this->postcode->isStoreHoliday()) {
            $this->data['store_closed'] = $this->language->get('text_holidaymsg_head');
        } else if (!$this->postcode->isStoreOpened()) {
            $this->data['store_closed'] = $this->language->get('text_store_closed');
        } else {
            $this->data['store_closed'] = '';
        }
        */
        //$this->data['tableordering'] = false;
        if(isset($this->session->data['tableordering']) && isset($this->session->data['dine_table_id'])){
            $this->data['tableordering'] = true;
        }
        $this->data['store_closed'] = $this->postcode->getStoretimeMessage();
        $this->data['home_delivery_start_time'] = 0;
        $this->data['collection_start_time'] = 0;
        if($this->data['store_closed'] && !isset($this->data['tableordering'])) {            
            $delivery_times = $this->postcode->getrequestedtimes($this->config->get('home_delivery_storetime'),strtotime('now'),0,'home.home');
            if(!empty($delivery_times)){
                reset($delivery_times);
                $this->data['home_delivery_start_time'] = key($delivery_times);           
            }
            $collection_times = $this->postcode->getrequestedtimes($this->config->get('collect_in_storetime'),strtotime('now'),0,'collect.collect');
            if(!empty($collection_times)){
                reset($collection_times);
                $this->data['collection_start_time'] = key($collection_times);  
            }
        }
        
        //$open_time = 0;
        
  /*      
        if($this->data['store_closed']) {

            $today_open_times = $this->config->get(strtolower(date('l')).'_store_times');

            if($today_open_times) {
                foreach ($today_open_times as $today_open_time) {
                    if($today_open_time['start']) {
                        $start = strtotime(trim($today_open_time['start']));
                        if($start > strtotime('now')) {
                            $open_time = $start;
                            break;
                        }
                    }

                }
            }
            //$this->log->write(print_r($today_open_times,true));
            //$this->log->write('Open at '.$open_time);
        }
        $this->data['open_time'] = $open_time;
*/

        // $this->log->write('holiday message : ' . $this->data['store_closed']);

        if (isset($this->session->data['delivery_method'])) {
            $delivery_method = $this->session->data['delivery_method'];
        } else {
            $delivery_method = '';
        }
        $this->data['delivery_method'] = $delivery_method;


        $shipping_address = array('firstname' => '',
            'lastname' => '',
            'company' => '',
            'company_id' => '',
            'tax_id' => '',
            'address_1' => '',
            'address_2' => '',
            'postcode' => (isset($this->session->data['postcode'])?$this->session->data['postcode']:$this->config->get('config_postalcode')),
            'city' => '',
            'zone_id' => $this->config->get('config_zone_id'),
            'zone' => '',
            'zone_code' => '',
            'country_id' => $this->config->get('config_country_id'),
            'country' => '',
            'iso_code_2' => '',
            'iso_code_3' => '',
            'address_format' => '');


        $this->load->model('setting/extension');
        // Shipping 
        $results =(!isset($this->data['tableordering']))?$this->model_setting_extension->getExtensions('shipping'):array();
        $this->data['home_delivery_error'] = false;
        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('shipping/' . $result['code']);
                if (method_exists($this->{'model_shipping_' . $result['code']}, 'isposonly') || $this->postcode->checkisDisabledfromEpos($result['code'])) {
                    continue;
                }
                $etm_t = microtime(true); $quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address); $GLOBALS['etm_perf'][] = array('shipping_postcodes getQuote/' . $result['code'], microtime(true) - $etm_t); // timing for ?etm_perf=1
                // print_r($quote);
                if ($quote) {
                    if(isset($quote['delivery_msg_status']) && $quote['delivery_msg_status']!=''){
                        $this->data['home_delivery_error'] =$quote['delivery_msg_status'];
                    }
                    $quote_data[$result['code']] = array(
                        'title' => $quote['title'],
                        'code' =>$result['code'].'.'.$result['code'],
                        'quote' => $quote['quote'],
                        'sort_order' => $quote['sort_order'],
                        'error' => $quote['error']
                    );
                }
            }
        }
        //print_r($quote_data);
        $sort_order = array();

        foreach ($quote_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $quote_data);

        $this->data['shipping_methods'] = $quote_data;
        if (isset($this->session->data['postcode'])) {
            $this->data['postcode'] = $this->session->data['postcode'];
        } else {
            $this->data['postcode'] = '';
        }
        if (isset($this->session->data['order_requested_time'])) {
            $this->data['order_requested_time'] = $this->session->data['order_requested_time'];
        } else {
            $this->data['order_requested_time'] = '';
        }
          if(isset($this->data['tableordering'])){
            $this->data['requested_times'] = array();//$this->getChild('checkout/shipping_methodetm/requestedtime');
        }else{
        $this->data['requested_times'] = $this->getChild('checkout/shipping_methodetm/requestedtime');
        }

        if($this->config->get('collect_in_storetime')){
            $this->data['store_collection_time'] = $this->postcode->getUpdatedRequestedtime('collect',$this->config->get('collect_in_storetime'));
        }
        if($this->config->get('home_delivery_storetime')){
            $this->data['store_delivery_time'] = $this->postcode->getUpdatedRequestedtime('home',$this->config->get('home_delivery_storetime'));
        }
        
        if (isset($this->request->get['ch'])) {
            $this->data['chckpage'] = '1'; 
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/cartshipping.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/cartshipping.tpl';
        } else {
            $this->template = 'default/template/module/cartshipping.tpl';
        }
        $this->response->setOutput($this->render());
    }

    public function set_delivery_method() {
        $json = array();
        $this->log->write(print_r(array("SET DELIVERY METHODE"),true));
        $this->log->write(print_r(array($this->session->data),true));
        $this->log->write(print_r(array($this->request->post),true));
        $this->log->write(print_r(array($this->request->get),true));
        if (isset($this->request->post['delivery_method'])) {
            unset($this->session->data['delivery_method']);
            $delivery_method = $this->request->post['delivery_method'];
            $this->session->data['delivery_method'] = $delivery_method;
            $json['delivery_method'] = $this->session->data['delivery_method']; #$_SESSION['delivery_method'];
        }
        if (isset($this->request->post['postcode'])) {
            //unset($this->session->data['delivery_method']);
            $postcode = $this->request->post['postcode'];
            $this->session->data['postcode'] = $postcode;
            $json['postcode'] = $this->session->data['postcode'];
        }
        if (isset($this->request->post['newpostcode'])) {
            $newpostcode = $this->request->post['newpostcode'];
            $this->session->data['newpostcode'] = $newpostcode;
            $json['newpostcode'] = $this->session->data['newpostcode'];
        }
        if(isset($this->session->data['tableordering']) && isset($this->session->data['dine_table_id'])){
            $this->request->post['delivery_method'] = 'dine.dine';
            $this->session->data['delivery_method'] = $delivery_method;
            $json['delivery_method'] = $this->session->data['delivery_method']; 
        }
        $this->response->setOutput(json_encode($json));
    }
    
     public function menushipping() {
        $this->language->load('checkout/checkout');
        /*
        if ($this->postcode->isStoreHoliday()) {
            $this->data['store_closed'] = $this->language->get('text_holidaymsg_head');
        } else if (!$this->postcode->isStoreOpened()) {
            $this->data['store_closed'] = $this->language->get('text_store_closed');
        } else {
            $this->data['store_closed'] = '';
        }
        */
        //$this->data['tableordering'] = false;
        if(isset($this->session->data['tableordering']) && isset($this->session->data['dine_table_id'])){
            $this->data['tableordering'] = true;
        }
        $this->data['store_closed'] = $this->postcode->getStoretimeMessage();
        $this->data['home_delivery_start_time'] = 0;
        $this->data['collection_start_time'] = 0;
        if($this->data['store_closed'] && !isset($this->data['tableordering'])) {            
            $delivery_times = $this->postcode->getrequestedtimes($this->config->get('home_delivery_storetime'),strtotime('now'),0,'home.home');
            if(!empty($delivery_times)){
                reset($delivery_times);
                $this->data['home_delivery_start_time'] = key($delivery_times);           
            }
            $collection_times = $this->postcode->getrequestedtimes($this->config->get('collect_in_storetime'),strtotime('now'),0,'collect.collect');
            if(!empty($collection_times)){
                reset($collection_times);
                $this->data['collection_start_time'] = key($collection_times);  
            }
        }
        
        //$open_time = 0;
        
  /*      
        if($this->data['store_closed']) {

            $today_open_times = $this->config->get(strtolower(date('l')).'_store_times');

            if($today_open_times) {
                foreach ($today_open_times as $today_open_time) {
                    if($today_open_time['start']) {
                        $start = strtotime(trim($today_open_time['start']));
                        if($start > strtotime('now')) {
                            $open_time = $start;
                            break;
                        }
                    }

                }
            }
            //$this->log->write(print_r($today_open_times,true));
            //$this->log->write('Open at '.$open_time);
        }
        $this->data['open_time'] = $open_time;
*/

        // $this->log->write('holiday message : ' . $this->data['store_closed']);

        if (isset($this->session->data['delivery_method'])) {
            $delivery_method = $this->session->data['delivery_method'];
        } else {
            $delivery_method = '';
        }
        $this->data['delivery_method'] = $delivery_method;


        $shipping_address = array('firstname' => '',
            'lastname' => '',
            'company' => '',
            'company_id' => '',
            'tax_id' => '',
            'address_1' => '',
            'address_2' => '',
            'postcode' => (isset($this->session->data['postcode'])?$this->session->data['postcode']:$this->config->get('config_postalcode')),
            'city' => '',
            'zone_id' => $this->config->get('config_zone_id'),
            'zone' => '',
            'zone_code' => '',
            'country_id' => $this->config->get('config_country_id'),
            'country' => '',
            'iso_code_2' => '',
            'iso_code_3' => '',
            'address_format' => '');


        $this->load->model('setting/extension');
        // Shipping 
        $results =(!isset($this->data['tableordering']))?$this->model_setting_extension->getExtensions('shipping'):array();
        $this->data['home_delivery_error'] = false;
        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('shipping/' . $result['code']);
                if (method_exists($this->{'model_shipping_' . $result['code']}, 'isposonly') || $this->postcode->checkisDisabledfromEpos($result['code'])) {
                    continue;
                }
                $etm_t = microtime(true); $quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address); $GLOBALS['etm_perf'][] = array('shipping_postcodes getQuote/' . $result['code'], microtime(true) - $etm_t); // timing for ?etm_perf=1
                // print_r($quote);
                if ($quote) {
                    if(isset($quote['delivery_msg_status']) && $quote['delivery_msg_status']!=''){
                        $this->data['home_delivery_error'] =$quote['delivery_msg_status'];
                    }
                    $quote_data[$result['code']] = array(
                        'title' => $quote['title'],
                        'code' =>$result['code'].'.'.$result['code'],
                        'quote' => $quote['quote'],
                        'sort_order' => $quote['sort_order'],
                        'error' => $quote['error']
                    );
                }
            }
        }
        //print_r($quote_data);
        $sort_order = array();

        foreach ($quote_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $quote_data);

        $this->data['shipping_methods'] = $quote_data;
        if (isset($this->session->data['postcode'])) {
            $this->data['postcode'] = $this->session->data['postcode'];
        } else {
            $this->data['postcode'] = '';
        }
        if (isset($this->session->data['order_requested_time'])) {
            $this->data['order_requested_time'] = '  '.date('h:i A (l)',$this->session->data['order_requested_time']);
        } else {
            $this->data['order_requested_time'] = '';
        }
         
        
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/menushipping.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/menushipping.tpl';
        } else {
            $this->template = 'default/template/checkout/menushipping.tpl';
        }
        $this->response->setOutput($this->render());
    }
}
