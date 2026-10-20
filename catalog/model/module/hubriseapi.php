<?php
class ModelModuleHubriseapi extends Model {
    private $log_file = 'hubrise.log';
    private $log;
    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log($this->log_file);
    }
    public function currencyformat($total,$code) {
        if($code) {
            return number_format((float)$total, 2, '.', '').' '.$code;            
        } else {
            return number_format((float)$total, 2, '.', '').' '.$this->config->get('currency_code');            
        }
    }

    public function removeCurrencyFormat($total,$code) {
        if($code) {
            return rtrim($total,$code);            
        } else {
            return rtrim($total," ".$this->config->get('currency_code'));            
        }
    }

    public function getcallbackauthcode() {        
        $url = 'https://manager.hubrise.com/oauth2/v1/token?code={{callback_auth_code}}&client_id={{callback_client_id}}&client_secret={{callback_client_secret}}';
    }

    public function curl_async($url,$callback, $params) {
        foreach ($params as $key => &$val) {
            if($key == 'data') {
              $val = json_encode($val);
            } else if (is_array($val)) {
              $val = implode(',', $val);
            }
            $post_params[] = $key.'='.urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $url = 'https://api.hubrise.com/'.$url;

        if($callback) {
            $url.= '?callback='.urlencode(HTTP_SERVER.'index.php?route=module/hubriseapi/callback/'.$callback);
        }
    
        $parts=parse_url($url);
    
        $fp = fsockopen($parts['host'],
            isset($parts['port'])?$parts['port']:80,
            $errno, $errstr, 30);
    
        $out = "POST ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= 'X-Access-Token:'.$this->config->get('config_hubrise_token');
        //$out.= "Authorization: JE-API-KEY ".$this->config->get('config_just_eat_api_key');
        $out.= "Connection: Close\r\n\r\n";
        if (isset($post_string)) {
            $out.= $post_string;
        }
    
        fwrite($fp, $out);
        fclose($fp);
        $this->log->write($url);
    }

    public function getauthcode() {
        $url = 'https://manager.hubrise.com/oauth2/v1/authorize?redirect_uri='.HTTPS_SERVER.'oauth_callback&client_id=459691768564.clients.hubrise.com&scope=location[orders.write,customer_list.write,catalog.read]';
        $result = $this->authorise($url,$data,$header);
    }

    public function authorise($url,$data = array(), $header= array()) {
        try {            
            $ch = curl_init($url);   

            if($data) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');     
            }
                     
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch,CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch,CURLOPT_ENCODING, '');
            curl_setopt($ch,CURLOPT_ENCODING , "deflate,gzip,br");
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $result = curl_exec($ch);
            $json = json_decode($result,true);
            if(!$json) {
                $jsonresponse['status_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $jsonresponse['response'] = $result;
                $jsonresponse['data'] = $data;
                $this->log->write($url.'-->'.$result);
                return $jsonresponse;
            }
            curl_close($ch); 
        } catch (Exception $e) {
            $this->log->write($e);
            $json['status'] = 404; 
            $json['message'] = $e;
        }  
        return $json;      
    }
    
    public function connectUrl($url,$type="GET",$data = array(), $device_id = 0) {
        $hubrise_instances = $this->config->get('hubrise_instances');
        if(isset($hubrise_instances[$device_id]) && $hubrise_instances[$device_id]['hubrise_token']){
            $config_hubrise_token = $hubrise_instances[$device_id]['hubrise_token'];
        }
        elseif (isset($hubrise_instances[0]) && $hubrise_instances[0]['hubrise_token']){
            $config_hubrise_token = $hubrise_instances[0]['hubrise_token'];
        }
        else {
            $json =[];
            $json['status'] = 500; 
            $json['response'] = 'No Token';
            return $json;
        }
        try {            
            $url = 'https://api.hubrise.com/'.$url;
            $ch = curl_init($url);   

            if($data) {
                if($type=="POST") {
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
                } else {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  
                    //curl_setopt($ch, CURLOPT_POSTFIELDS, urldecode(http_build_query($data))); 
                    //curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
                }
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);     
            }
                     
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
            curl_setopt($ch, CURLOPT_HEADER, 1);
           
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "X-Access-Token:".$config_hubrise_token,
                    "Content-Type: application/json",
                    "Host: api.hubrise.com",
                    //"Accept-Encoding: gzip, deflate",
                    //'Accept: application/json'
                ));
           
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch,CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch,CURLOPT_ENCODING, '');
            //curl_setopt($ch,CURLOPT_ENCODING , "deflate,gzip,br");
            $result = curl_exec($ch);
            $json = json_decode($result,true);
            if(!$json) {
                $jsonresponse['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $jsonresponse['response'] = $result;
                $jsonresponse['data'] = $data;
                $this->log->write($url." -> data -> ".json_encode($data).'-->'.$result.'->'.json_encode($jsonresponse));
                return $jsonresponse;
            }
            curl_close($ch); 
        } catch (Exception $e) {
            $this->log->write($e);
            $json['status'] = 404; 
            $json['response'] = $e;
        }  
        return $json;      
    }  

    public function createorder($order_id) {

        $order_id = 48041;

        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($order_id);

        if(!$order_info) {
            return;
        } 

        $data['status'] = 'new';
        $data['service_type'] = strtolower($order_info['shipping_method']);
        $c_date = str_replace('/','-',$order_info['requested_time']);
        $data['expected_time'] = date('Y-m-d\TH:i:s\Z',strtotime($c_date));

        $data['total'] = $this->currencyformat($order_info['total'],$order_info['currency_code']);

        if($order_info['customer_id']) {
            $data['customer_id'] = $order_info['customer_id'];            
        } else {
            $data['customer'] = array(
                "first_name" => $order_info['firstname'],
                "last_name" =>$order_info['lastname'],
                "address_1" =>$order_info['shipping_address_1'],
                "postal_code" =>$order_info['shipping_postcode'],
                "city" =>$order_info['shipping_city'],
                "country" =>$order_info['country'],
                "phone" => $order_info['telephone']
            );
        }
        $data['items'] = array();

        $products = $this->model_sale_order->getFormattedOrderProducts($order_id);
        foreach($products as $product) {
            $items = array(
                        'price' => $this->currencyformat($product['price'],$order_info['currency_code']),
                        'quantity' => $product['quantity'],
                        'product_name' => $product['name'],
                        'sku_name' => '',
                        'sku_ref' => '',
                    );
            foreach($product['option'] as $sub_product) {
                $items['options'][] = array('option_list_name' => $sub_product['name'],
                                        'name' => $sub_product['value'],
                                        'ref' => $sub_product['order_product_id'],
                                        'price' => $this->currencyformat($sub_product['price'],$order_info['currency_code'])
                                    );
            }
            foreach($product['toppings'] as $sub_product) {
                $items['options'][] = array('option_list_name' => $sub_product['name'],
                                        'name' => $sub_product['value'],
                                        'ref' => $sub_product['order_product_id'],
                                        'price' => $this->currencyformat($sub_product['price'],$order_info['currency_code']),
                                    );
            }
            foreach($product['dressings'] as $sub_product) {
                $items['options'][] = array('option_list_name' => $sub_product['name'],
                                        'name' => $sub_product['value'],
                                        'ref' => $sub_product['order_product_id'],
                                        'price' => $this->currencyformat($sub_product['price'],$order_info['currency_code'])
                                    );
            }
            foreach($product['bundle'] as $sub_product) {
                $items['deal_line'][] = array('deal_key' => $sub_product['order_bundle_product_id'],
                                'label' => null);

                $data['deals'][$sub_product['order_bundle_product_id']] = array(
                    'name' => $sub_product['name'],
                    'ref' => ''
                );
            }
            $data['items'][] = $items;
        }
 
        $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $order_info['order_id'] . "' AND code NOT IN ('sub_total','total') ORDER BY sort_order ASC");
        
        foreach($order_total_query->rows as $totl) {
            if($totl['value'] < 0) {
                $data['discounts'][] = array(
                    "name" => $totl['title'],
                    "ref"=> $totl['code'],
                    "pricing_effect"=> "price_off",
                    "pricing_value"=> $this->currencyformat($totl['value']*-1,$order_info['currency_code']),
                    "price_off"=> $totl['value']
                );
            } else {
                $data['charges'][] = array( 
                      "ref"=> $totl['code'],
                      "name"=> $totl['title'],
                      "type"=> $totl['code'],
                      "price"=> $this->currencyformat($totl['value'],$order_info['currency_code'])
                );
            }
        }

        $result = $this->connectUrl('v1/location/orders','POST',$data);

        if($result['status'] == 200) {
            $params = array('status' => "Success","message"=>"Accepted","data"=>$data);
            //$this->curl_async('acceptance-requested','orderAcceptance_'.$order_id,$params);
        } else {
            $params = array('status' => "Failure",'response' => $result,"data"=>$data);
            //$this->curl_async('acceptance-requested','', $params);
        }
        $params['result']= $result;
        return $params;
    }
    
    public function orderPreparation($order_id) {
        
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($order_id); 
        
        
        $result = $this->connectUrl('acceptance-requested','POST',$data);

        if($result['status'] == 202) {
            $params = array('status' => "Success","message"=>"Accepted","data"=>$data);
        } else {
            $params = array('status' => "Failure","message"=>"Cannot connect to url","data"=>$data);
        }
        return $result;
    }
    
    public function setOrderStatus($order_id,$status,$message) {
        $data = array('status' => $status,
                'TimeStamp' => $current_time,
                'Message' =>$message);
        $result = $this->connectUrl('orders/'.$order_id.'/status?partnerId='.$this->config->get('config_just_eat_subscription_id'),'PUT',$data);
        return $result;
    }    

    public function orderAcceptance($order_id) {
        $istest = ($this->config->get('config_just_eat_live'))?false:true;
        
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($order_id); 
        $c_date = str_replace('/','-',$order_info['date_added']);
        $order_info['date_added'] = date('Y-m-d\TH:i:s\Z',strtotime($c_date));
        $c_date = str_replace('/','-',$order_info['requested_time']);
        $order_info['requested_time'] = date('Y-m-d\TH:i:s\Z',strtotime($c_date));

        $RestaurantDetails = array(
            "Id" =>         $order_info['store_id'],
            "Email" =>      $this->config->get('config_email'),
            "Name" =>       $order_info['store_name'],
            "Address" =>    array("Lines" => array($this->config->get('config_address')),
                                  "PostalCode"   => trim($this->config->get('config_postalcode')),
                                  "City"         => '',
                                  "Geolocation"  => array(
                                    "Latitude" =>  0,
                                    "Longitude" =>  0
                                  )
                            ),
            "PhoneNumber" =>   $this->config->get('config_telephone'),
            "Reference"   => ''

        );         
        $location = $this->postcode->getPostcodeDetails(trim($this->config->get('config_postalcode')));
        if($location) {
            $RestaurantDetails['City'] = isset($location['address'][0]['city'])?$location['address'][0]['city']:$location['city'];
            $RestaurantDetails['Geolocation'] = array (
                "Latitude" =>  $location['latitude'],
                "Longitude" =>  $location['longitude']
            );
        }

        $location = $this->postcode->getPostcodeDetails(trim($order_info['shipping_postcode']));

        $Basket = array();
        $orderTotals = array(
            "Discount" =>  0,
            "Tips" =>  0,
            "Taxes" =>  0,
            "Items" =>  0,
            "Fees" =>  array(
                "Delivery" =>  0,
                "ServiceCharge" =>  0,
                "Other" =>  0
            )
        );


        
        $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $order_info['order_id'] . "' ORDER BY sort_order ASC");
        
        foreach($order_total_query->rows as $totl) {
            if($totl['code']=='shipping') {
                $orderTotals['Fees']['Delivery'] =  $totl['value'];
            } else if($totl['code']=='sub_total'){
                $orderTotals['Items'] =  $totl['value'];
            } else if($totl['value'] < 0) {
                $orderTotals['Discount']+= $totl['value'];
            } else if($totl['code']!='total') {        
                $orderTotals['Fees']['Other']+=$totl['value'];
            }
        }

        $Payment['Lines'] = array(
            'Type' => $order_info['payment_method'],
            'Value'=> $order_info['total'],
            'Paid' => false
        );

        $products = $this->model_sale_order->getFormattedOrderProducts($order_id);

        foreach($products as $product) {
            $sub_items = array();
            foreach($product['option'] as $sub_product) {
                $sub_items[] = array(
                    "Reference"=> $sub_product['order_product_id'],
                    "Option Reference"=> $sub_product['name'],
                    "Description"=> $sub_product['value'],
                    "Quantity"=> 1,
                    "UnitPrice"=> 0
                );
            }
            foreach($product['toppings'] as $sub_product) {
                $sub_items[] = array(
                    "Reference"=> $sub_product['order_product_id'],
                    "Option Reference"=> $sub_product['name'],
                    "Description"=> $sub_product['value'],
                    "Quantity"=> 1,
                    "UnitPrice"=> 0
                );
            }
            foreach($product['dressings'] as $sub_product) {
                $sub_items[] = array(
                    "Reference"=> $sub_product['order_product_id'],
                    "Option Reference"=> $sub_product['name'],
                    "Description"=> $sub_product['value'],
                    "Quantity"=> 1,
                    "UnitPrice"=> 0
                );
            }
            foreach($product['bundle'] as $sub_product) {
                $sub_items[] = array(
                    "Reference"=> $sub_product['order_product_id'],
                    "Option Reference"=> $sub_product['name'],
                    "Description"=> $sub_product['value'],
                    "Quantity"=> 1,
                    "UnitPrice"=> 0
                );
            }
            $items = array(
                "Reference"=> $product['order_product_id'],
                "Option Reference"=> $product['name'],
                "Description"=> "",
                "Quantity"=> $product['quantity'],
                "UnitPrice"=> $product['price'],
                "TotalPrice" => $product['quantity'] * $product['price'],
                "Items" =>$sub_items
            );
            $Basket['Items'][] = $items; 
        }

        $data = array(
                "PartnerOrderId" =>  $this->config->get('config_just_eat_subscription_id'),
                "OrderReference" =>  $order_id,
                "FriendlyOrderReference" =>  "",
                "Currency" =>  $order_info['currency_code'],
                "TotalPrice" =>  $order_info['total'],
                "Fulfilment" =>  array(
                    "Method" =>  $order_info['shipping_method'],
                    "CustomerDueDate" =>  $order_info['requested_time'],
                    "CustomerDueAsap" =>  false,
                    "Address" =>  array(
                        "Lines" =>  array($order_info['shipping_address_1']),
                        "City" =>  isset($location['address'][0]['city'])?$location['address'][0]['city']:$location['city'],
                        "PostalCode" =>  $order_info['shipping_postcode'],
                        "Geolocation" =>  array (
                            "Latitude" =>  $location['latitude'],
                            "Longitude" =>  $location['longitude']
                        )
                    ),
                    "PhoneNumber" =>  $order_info['telephone'],
                    "PickupTime" =>  $order_info['requested_time'],
                    "PrepareFor" =>  $order_info['requested_time'],
                    "PreparationTime" =>  "00:00:00"
                ),
                "Payment" =>  $Payment,
                "Customer" =>  array(
                    "Reference" =>  $order_info['customer_id'],
                    "Name" =>  $order_info['firstname'].' '.$order_info['lastname']
                ),
                "Items" =>  $Basket,
                "PriceBreakdown" =>  $orderTotals,
                "NoteFromCustomer" =>  $order_info['comment'],
                "PlacedDate" =>  $order_info['date_added'],
                "Restaurant" =>  $RestaurantDetails,
                "IsTest" =>  $istest
        );
        
        $result = $this->connectUrl('acceptance-requested','POST',$data);
        return $result;
    }

    public function getorders($data=array()) {

        $query = $this->db->query("SELECT hubrise_order_id FROM `".DB_PREFIX."order` order by order_id DESC LIMIT 1");

        $lastorder = '';
        if(isset($query->row['hubrise_order_id']) && $query->row['hubrise_order_id']) {
            $lastorder = '?cursor='.$query->row['hubrise_order_id'];
        }


        if(isset($data['status'])) {
            $results = $this->connectUrl('v1/location/orders?status='.$data['status'],'GET',$data);
        } else {
            $results = $this->connectUrl('v1/location/orders'.$lastorder,'GET',$data); 
            /* 
            if($this->config->get('config_hubrise_location')) {
                $location = json_decode($this->config->get('config_hubrise_location'),true);
                $results = $this->connectUrl('v1/locations/'.$location[0]['account']['id'].'/orders'.$lastorder,'GET',$data);
            }
            */
        }

        $count = 0;

        if($results) {
            if(isset($results['status']) && $results['status']!==200) {
                $response = $results;
            } else {

                $locations = json_decode($this->config->get('config_hubrise_location'),true);
                $loc = array();
                foreach($locations as $location) {
                    $loc[$location['id']] = $location['account']['currency']; 
                }
                $this->load->model('sale/order');

                foreach($results as $result) {
                    $result['currency_code'] = $loc[$result['location_id']];
                    //$result['currency_code'] = $this->config->get('config_hubrise_currency_code');
                    $count+=$this->addOrder($result);
                }

                $response = array('count' => $count,'status' => 200);
            }
        } else {
            $response = array('status' => 404);
        }


        return $response;

    }

    public function getOrder($order_id) {
        $data = array();
        $result = $this->connectUrl('v1/location/orders/'.$order_id,'GET',$data);
        if($result) {
           //$result['currency_code'] = $this->config->get('config_hubrise_currency_code');

            $locations = json_decode($this->config->get('config_hubrise_location'),true);
            $loc = array();
            foreach($locations as $location) {
                $loc[$location['id']] = $location['account']['currency']; 
            }
            $this->load->model('sale/order');

            $result['currency_code'] = $loc[$result['location_id']];

           $this->addOrder($result); 
        }
        return $result;
    }

    public function updateOrderbyCallback($hubrise_order_id,$result) {
        $query = $this->db->query("SELECT hubrise_order_id,order_id,order_status_id FROM `".DB_PREFIX."order` WHERE hubrise_order_id = '".$this->db->escape($hubrise_order_id)."'");
        $hubrise_statuses = $this->config->get('config_hubrise_statuses');
        if(isset($query->row['hubrise_order_id']) && $query->row['hubrise_order_id']) {
            $status_name = '';
            foreach($hubrise_statuses as $name => $statusid) {
                if($query->row['order_status_id']==$statusid) {
                    $status_name = $name;
                    break;
                }
            }
            
            if($status_name!==$result['status']) {
                $status_id = isset($hubrise_statuses[$result['status']]) ? $hubrise_statuses[$result['status']] : 0;

                $data = array('order_status_id' => $status_id, 
                                'comment' => ($result['seller_notes']) ? $result['seller_notes'] : ''
                        );
                $this->updateOrder($query->row['order_id'],$data);
            }
            return 0;
        } else {
            $this->getOrder($hubrise_order_id);
        }
    }

    public function addOrder($result) {

        $query = $this->db->query("SELECT hubrise_order_id,order_id,order_status_id FROM `".DB_PREFIX."order` WHERE hubrise_order_id = '".$this->db->escape($result['id'])."'");

        $hubrise_statuses = $this->config->get('config_hubrise_statuses');
        if(isset($query->row['hubrise_order_id']) && $query->row['hubrise_order_id']) {
            $status_name = '';
            foreach($hubrise_statuses as $name => $statusid) {
                if($query->row['order_status_id']==$statusid) {
                    $status_name = $name;
                    break;
                }
            }

            if($status_name!==$result['status']) {
                $status_id = isset($hubrise_statuses[$result['status']]) ? $hubrise_statuses[$result['status']] : 0;

                $data = array('order_status_id' => $status_id, 
                                'comment' => ($result['seller_notes']) ? $result['seller_notes'] : ''
                        );
                $this->updateOrder($query->row['order_id'],$data);
            }
            return 0;
        }

        $data = array();
        $data['store_id'] = 0;//(int)$this->config->get('config_hubrise_store');

        if(isset($result['customer'])) {
            $customer = $result['customer'];
        } else {
            $customer = array('firstname' => '', 'lastname' => '', 'email' => '', 'phone' => '');
        }

        $data['customer_id'] = 0;
        $data['customer_group_id'] = 0;
        $data['firstname'] = $customer['first_name'];
        $data['lastname'] = $customer['last_name'];
        $data['email'] = $customer['email'];
        $data['telephone'] = $customer['phone'];
        $data['fax'] = '';

        $data['payment_firstname'] = $customer['first_name'];
        $data['payment_lastname'] = $customer['last_name'];
        $data['payment_company'] = '';
        $data['payment_company_id'] = 0;
        $data['payment_tax_id'] = 0;
        $data['payment_address_1'] = $customer['address_1'];
        $data['payment_address_2'] = $customer['address_2'];
        $data['payment_city'] = $customer['city'];
        $data['payment_postcode'] = $customer['postal_code'];
        $data['payment_zone'] = '';
        $data['payment_zone_id'] = 0;
        $data['payment_country'] = $customer['country'];
        $data['payment_country_id'] = 0;
        $data['payment_address_format'] = '';

        $data['payment_method'] = 'Hubrise';
        $data['payment_code'] = 'hubrise';

        if($result['payments']) {
            $hubrise_statuses = $this->config->get('config_hubrise_payment');
            $data['payment_code'] = $hubrise_statuses[$result['payments'][0]['type']];
            $data['payment_method'] = $result['payments'][0]['name'];
        }

        $data['shipping_firstname'] = $customer['firstname'];
        $data['shipping_lastname'] = $customer['lastname'];
        $data['shipping_company'] = '';
        $data['shipping_company_id'] = 0;
        $data['shipping_tax_id'] = 0;
        $data['shipping_address_1'] = $customer['address_1'];
        $data['shipping_address_2'] = $customer['address_2'];
        $data['shipping_city'] = $customer['city'];
        $data['shipping_postcode'] = $customer['postal_code'];
        $data['shipping_zone'] = '';
        $data['shipping_zone_id'] = 0;
        $data['shipping_country'] = $customer['country'];
        $data['shipping_country_id'] = 0;
        $data['shipping_address_format'] = '';


        $shipping = $this->config->get('config_hubrise_shipping');
        $data['shipping_code'] = isset($shipping[$result['service_type']]) ? $shipping[$result['service_type']] :  $result['service_type'];
        $data['shipping_method'] = $result['service_type'];

        $data['order_requested_time'] = $result['expected_time'];
        if(isset($result['confirmed_time']) && $result['confirmed_time']!=='0000-00-00 00:00:00') {
            $data['delivery_time'] = $result['confirmed_time'];
        }

        $product_data = array();

        $subtotal = 0;

        foreach($result['items'] as $product) {
            $option_data = array();
            foreach($product['options'] as $option) {
                $option_data[] = array(
                    'product_option_id' =>0,
                    'product_option_value_id' => 0,
                    'option_id' => $option['ref'],
                    'option_value_id' => 0,
                    'name' => $option['option_list_name'],
                    'shortcode'=>'',
                    'value' => $option['name'],
                    'type' =>'select',
                    'group'=> 0
                );
            }
            $product_data[] = array(
                'product_id' => $product['sku_ref'],
                'cart_key' => '',
                'name' => $product['product_name'],
                'model' => $product['sku_name'],
                'option' => $option_data,
                'download' =>0,
                'quantity' => $product['quantity'],
                'subtract' => 0,
                'price' => $product['price'],
                'total' => $product['subtotal'],
                'tax' => ($product['tax_rate']) ? $product['tax_rate']: 0,
                'reward' => 0,
                'bundle'=> array(),
                'toppings'=> array(),
                'dressings'=> array(),
            );

            $subtotal+=$this->removeCurrencyFormat($product['subtotal'],$result['currency_code']);
        }

        $total_data = array();

        $total_data[] = array(
            'code' => 'sub_total',
            'title' => 'Sub-total',
            'text' => $this->currency->format($subtotal, $result['currency_code']),
            'value' => $subtotal,
            'sort_order' => 0
        );


        foreach ($result['charges'] as $key => $value) {
            $price_value = "-".$this->removeCurrencyFormat($value['pricing_value'],$result['currency_code']);
            $total_data[] = array(
                'code' => 'charges',
                'title' => $value['name'],
                'text'=> $this->currency->format($price_value, $result['currency_code']),
                'value' => $price_value,
                'sort_order'=> $key,
            );
        }

        foreach ($result['discounts'] as $key => $value) {
            $price_value = "-".$this->removeCurrencyFormat($value['pricing_value'],$result['currency_code']);
            $total_data[] = array(
                'code' => 'discounts',
                'title' => $value['name'],
                'text'=> $this->currency->format($price_value, $result['currency_code']),
                'value' => $price_value,
                'sort_order'=> $key,
            );
        }

        $total =  $this->removeCurrencyFormat($result['total'],$result['currency_code']);

        $total_data[] = array(
            'code' => 'total',
            'title' => 'Total',
            'text' => $this->currency->format($total, $result['currency_code']),
            'value' => $total,
            'sort_order' => 10
        );


        $data['products'] = $product_data;
        $data['order_product'] = $product_data;
        $data['vouchers'] = array();
        $data['totals'] = $total_data;
        $data['order_total'] = $total_data;
        $data['comment'] = ($result['customer_notes']) ? $result['customer_notes']: '';

        $data['order_status_id'] = isset($hubrise_statuses[$result['status']]) ? $hubrise_statuses[$result['status']] : 0;
        $data['affiliate_id'] = 0;

        //return $data;

        $this->load->model('sale/order');
        $order_id = $this->model_sale_order->addOrder($data);

        $this->db->query("UPDATE `".DB_PREFIX."order` SET hubrise_order_id = '".$this->db->escape($result['id'])."', location='".$result['location_id']."', store_name='".$result['created_by']."', store_url = 'https://api.hubrise.com' WHERE order_id = '".$order_id."'");

        return 1;

    }

    public function updateOrder($order_id,$data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = NOW(), user_id = '0' WHERE order_id = '" . (int)$order_id . "'");
        $success = $this->db->countAffected();
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '0', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");
        return $success;
    }

    public function updateorderstatus($order_id,$data) {
        $query = $this->db->query("SELECT hubrise_order_id FROM `".DB_PREFIX."order` WHERE order_id='".(int)$order_id."'");
        if(isset($query->row['hubrise_order_id']) && $query->row['hubrise_order_id']) {
            $hubrise_order_id = $query->row['hubrise_order_id'];
            if(isset($data['order_status_id']) && $data['order_status_id']) {

                $hubrise_statuses = $this->config->get('config_hubrise_statuses');
                if(isset($hubrise_statuses[$data['order_status_id']])){
                    $data['status'] = $hubrise_statuses[$data['order_status_id']];
                    unset($data['order_status_id']);
                    $this->log->write($order_id.json_encode($data));
                    $result = $this->connectUrl('v1/location/orders/'.$hubrise_order_id,'PUT',$data);   
                }
                else {
                    $result = array('error' => 'No order status found');
                }
            }
             
        } else {
            $result = array('error' => 'No order found');
        }
        //$this->log->write(json_encode($result));
        return $result;
    }

    public function callbackfunction($data=array()) {
        /*
        if(empty($data)) {
            $data = array(
                'url' => $this->config->get('config_hubrise_callback_url').'/hubrise_callback',
                'event' => array('order' => 'create','update')
            );            
        }
        */
        $result = $this->connectUrl('hubrise_callback','GET',$data);
        return $result;
    }

    public function callback() {
        $callback_url = $this->config->get('config_hubrise_callback_url');
        $data = array(
            'url' => $callback_url,
            'events' => array('order' => array('create','update'),"location"=>array("update"))
        ); 
        $result = $this->connectUrl('v1/callback','POST',$data);
        $this->log->write(json_encode($result));
        return $result;
    }

    public function getevents() {
        $data = array();
        $result = $this->connectUrl('v1/callback/events','GET',$data);
        return $result;        
    }

    public function getevent($event_id) {
        $data = array();
        $result = $this->connectUrl('v1/callback/events/'.$event_id,'GET',$data);
        return $result; 
    }

    public function webhook() {
        $secret = 'your-env-secret';

        //$requestMethod = $_SERVER['REQUEST_METHOD'] ? $_SERVER['REQUEST_METHOD']:  '';
        $receivedHmac = $_SERVER['HTTP_X_CS_SIGNATURE'] ? $_SERVER['HTTP_X_CS_SIGNATURE'] : '';
        $timestamp = $_SERVER['HTTP_X_CS_TIMESTAMP'] ? $_SERVER['HTTP_X_CS_TIMESTAMP']: '';
        
        $body = file_get_contents('php://input');

        $requestMethod = '';
        //$url = $_SERVER['REQUEST_URI'];
        $url = 'https://api.hubrise.com/v1/callback';

        $data = $requestMethod . $url . $timestamp . $body;
        $hmac = hash_hmac('sha256', $data, $secret);

        $isValid = hash_equals($hmac, $receivedHmac);

        if (!$isValid) {
            // Invalid webhook signature, ignore the request.
            die();
        }

        $webhook = json_decode($body);

        if($webhook) {
            $this->getOrders();
        }
        return $webhook;

        //file_put_contents('php://stdout', 'Webhook event received: ' . print_r($webhook, true) . "\r\n");
    }

    public function getlocation() {
        $data = array();
        $result = $this->connectUrl('v1/locations','GET',$data);
        $locations = array();
        if($result) {
            if(isset($result['status']) && $result['status']!==200) {
                //error
            } else {
                $locations =  $result;
            }
        } 

        if(!$locations) {
            $result = $this->connectUrl('v1/location','GET',$data);
            if(isset($result['status']) && $result['status']!==200) {
                //error
            } else {
                $locations[0] = $result;
            }
        }

        if($locations) {
            $this->db->query("DELETE FROM ".DB_PREFIX."setting WHERE `key`='config_hubrise_location'");

            $this->db->query("INSERT INTO ".DB_PREFIX."setting SET `key`='config_hubrise_location', `value`='".$this->db->escape(json_encode($locations))."', `group`='hubrise'");
        }
        return $locations;
    }

    public function exportOrder($order_id) {
        if(!$this->config->get('config_hubrise_exportorder')) {
            return;
        }
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);

        if(!$order_info) {
            return;
        }
        if($order_info['order_from'] > 3) {
            return;
        }

        $data = array();
        $no_price = $this->currencyformat(0, $order_info['currency_code']);

        $service_type = 'collection';
        foreach($this->config->get('config_hubrise_shipping') as $key => $value) {
            if($order_info['shipping_code'] == $value.'.'.$value) {
                $service_type = $key;
                break;
            }
        }

        $data['private_ref'] = (string)$order_id;
        $data['status'] = 'new';
        $data['service_type'] = $service_type;
        $data['service_type_ref'] = null;
        //$data['created_by'] = $order_info['store_name'];
        //$data['created_at'] = date(DATE_ISO8601, strtotime($order_info['date_added']));
        if(strtotime($order_info['date_added']) < strtotime($order_info['requested_time'])) {
            //$data['expected_time'] = date(DATE_ISO8601, strtotime($order_info['requested_time']));
            $data['expected_time'] = date_format(date_create($order_info['requested_time']), 'c');
        } else {
            $data['expected_time'] = $data['created_at'];                
        }
        if($order_info['delivery_time']=='0000-00-00 00:00:00') {
            $data['confirmed_time'] = null;            
        } else {
            //$data['confirmed_time'] = date(DATE_ISO8601, strtotime($order_info['delivery_time'])); 
            $data['confirmed_time'] = date_format(date_create($order_info['delivery_time']), 'c');
        }

        $customer_id = 0;
        if($order_info['customer_id']) {
            $customer['email'] = $order_info['email'];
            $customer['first_name'] = $order_info['firstname'];
            $customer['last_name'] = $order_info['lastname'];
            //$customer['gender'] = '';
            //$customer['birth_date'] = '';
            //$customer['company_name'] = $order_info['shipping_company'];
            $customer['phone'] = $order_info['telephone'];
            $customer['address_1'] = $order_info['shipping_address_1'];
            $customer['address_2'] = $order_info['shipping_address_2'];
            $customer['postal_code'] = $order_info['shipping_postcode'];
            $customer['city'] = $order_info['shipping_city'];
            if($order_info['shipping_zone']) {
                $customer['state'] = $order_info['shipping_zone'];
            }
            if($order_info['shipping_iso_code_2']) {
                $customer['country'] = $order_info['shipping_iso_code_2'];
            }
            //$customer['latitude'] = '';
            //$customer['longitude'] = '';
            //$customer['delivery_notes'] = '';
            //$customer['sms_marketing'] = '';
            //$customer['email_marketing'] = '';
            //$customer['custom_fields'] = array();
        }

        if($customer_id) {
            $data['customer_id'] = $customer_id;
        } else {
            $customer['email'] = $order_info['email'];
            $customer['first_name'] = $order_info['firstname'];
            $customer['last_name'] = $order_info['lastname'];
            //$customer['gender'] = '';
            //$customer['birth_date'] = '';
            //$customer['company_name'] = $order_info['shipping_company'];
            $customer['phone'] = $order_info['telephone'];
            $customer['address_1'] = $order_info['shipping_address_1'];
            $customer['address_2'] = $order_info['shipping_address_2'];
            $customer['postal_code'] = $order_info['shipping_postcode'];
            $customer['city'] = $order_info['shipping_city'];
            if($order_info['shipping_zone']) {
                $customer['state'] = $order_info['shipping_zone'];
            }
            if($order_info['shipping_iso_code_2']) {
                $customer['country'] = $order_info['shipping_iso_code_2'];
            }
            //$customer['latitude'] = '';
            //$customer['longitude'] = '';
            //$customer['delivery_notes'] = '';
            //$customer['sms_marketing'] = '';
            //$customer['email_marketing'] = '';
            $data['customer'] = $customer;

        }

        if($order_info['comment']) {
            $data['customer_notes'] = $order_info['comment'];
        }
        $data['seller_notes'] = null;
        $data['coupon_codes']  = array();
        $data['collection_code'] = (string)$order_id;
        $data['total'] = $this->currencyformat($order_info['total'], $order_info['currency_code']);

        //$data['total_discrepancy'] = null;
        //$data['payment_discrepancy'] = $this->currencyformat(0,$order_info['currency_code']);
        //$data['custom_fields'] = '{}';
        $data['items'] = array();
        //$data['deals'] = array();
        $data['loyalty_operations'] = array();
        $data['charges'] = array();
        $data['payments'][] = array(
            //"type" => $order_info['payment_code'],
            //"private_ref" => $order_info['payment_code'],
            "name" => $order_info['payment_method'],
            "ref" => $order_info['payment_code'],
            "amount" => $data['total']
        );
        $data['discounts'] = array();

        $dealcount = 1;

        $order_products = $this->model_checkout_order->getFormattedOrderProducts($order_id);

        foreach($order_products as $product) {
            $product_price = $product['price'];
            $product_query = $this->db->query("SELECT ref_code,sku,price,shipping_prices FROM `".DB_PREFIX."product` WHERE product_id = '".$product['product_id']."'");
            if(isset($product_query->row['ref_code'])) {
                $product_ref = $product_query->row['ref_code'];
                if(!$product_ref) {
                    $product_ref = $product['product_id'];
                }
                $sku = $product_query->row['sku'];
                $product['price'] = $product_query->row['price'];
                if($product_query->row['shipping_prices']) {
                    $prodctprice = unserialize($product_query->row['shipping_prices']);
                    if($prodctprice) {
                        $shppingcode = explode('.',$order_info['shipping_code']);
                        if(isset($prodctprice[$shppingcode[0]]) && $prodctprice[$shppingcode[0]]) {
                            $product['price'] = $prodctprice[$shppingcode[0]];
                        }
                    }
                }
            } else {
                $product_ref = $product['product_id'];                
            }
            $item = array(
                "private_ref" => (string)$product['order_product_id'],
                "product_name" =>  $product['name'],
                //"sku_name" =>  $product['model'],
                "sku_ref" =>  $product_ref,
                "price" =>  $this->currencyformat($product['price'], $order_info['currency_code']),
                "quantity" =>  $product['quantity'],
                "tax_rate" =>  $product['tax'],
                "customer_notes" =>  '',
                "options" => array(),
                //"points_earned" =>  $product['reward'],
                //"points_used" =>  null,
            );

            if(isset($sku)) {
                $product['sku_name'] = $sku;
            }

            if($product['option']) {
                foreach($product['option'] as $option) {  
                    $optn_query = $this->db->query("SELECT option_value_reference, price, price_prefix, points, points_prefix FROM `".DB_PREFIX."product_option_value` WHERE product_option_value_id = '".$option['product_option_value_id']."' ");
                    if(isset($optn_query->row['option_value_reference'])) {
                        $price = $optn_query->row['price'];
                        $ref = $optn_query->row['option_value_reference'];
                        if(!$ref) {
                            $ref = $option['product_option_value_id'];
                        }
                    } else {
                        $price = 0;
                        $ref = $option['product_option_value_id'];
                    }                 
                    $item['options'][] = array(
                        "option_list_name" =>  $option['name'],
                        "name" =>  $option['value'],
                        "ref" =>  $ref,
                        "price" =>  $this->currencyformat($price, $order_info['currency_code']),//"1.00 EUR",
                        "quantity" =>  1,
                        //removed => false
                    );
                }
            }
            if($product['toppings']) {
                foreach($product['toppings'] as $option) {               
                    $item['options'][] = array(
                        "option_list_name" =>  $option['name'],
                        "name" =>  $option['value'],
                        "price" =>  $this->currencyformat($option['price'], $order_info['currency_code']),
                        "quantity" =>  $option['quantity'],
                        //"ref" => $option['product_topping_value_id']
                        //removed => false
                    );
                }
            }

            if($product['dressings']) {
                foreach($product['dressings'] as $option) {               
                    $item['options'][] = array(
                        "option_list_name" =>  $option['name'],
                        "name" =>  $option['value'],
                        "price" =>  $this->currencyformat(0, $order_info['currency_code']),
                        "quantity" =>  1,
                        //"ref" => $option['product_dressing_value_id']
                        //removed => false
                    );
                }
            }

            if($product['bundle']) {
                $data['deals'][$dealcount] = array("name" => $product['name'], 
                                    "ref" => $product_ref
                                );
                $item['deal_line'] = array('deal_key' => (string)$dealcount, 'label' => null);

                $item['price'] = $this->currencyformat($product_price, $order_info['currency_code']);
                foreach($item['options'] as $k => $itemoption) {
                   $item['options'][$k]['price'] = $no_price;
                }
                $data['items'][] = $item;
                foreach($product['bundle'] as $bundle) {
                    $bundle_query = $this->db->query("SELECT ref_code,sku FROM `".DB_PREFIX."product` WHERE product_id = '".$bundle['product_id']."'");
                    if(isset($bundle_query->row['ref_code'])) {
                        $bundle_ref = $bundle_query->row['ref_code'];
                        if(!$bundle_ref) {
                            $bundle_ref = $bundle['product_id'];
                        }
                        $sku = $bundle_query->row['sku'];
                    } else {
                        $bundle_ref = $bundle['product_id'];                
                    }
                    $bundle_item = array(
                        "private_ref" => (string)$bundle['order_product_id'],
                        "product_name" =>  $bundle['name'],
                        //"sku_name" =>  $bundle['model'],
                        "sku_ref" =>  $bundle_ref,
                        "price" => $no_price,
                        //"price" =>  $this->currencyformat($bundle['price'], $order_info['currency_code']),
                        "quantity" =>  $bundle['quantity'],
                        "tax_rate" =>  $bundle['tax'],
                        "customer_notes" =>  '',
                        "options" => array(),
                        //"points_earned" =>  $bundle['reward'],
                        //"points_used" =>  null,
                    );

                    if(isset($sku)) {
                        $bundle['sku_name'] = $sku;
                    }

                    if($bundle['option']) {
                        foreach($bundle['option'] as $option) {  
                            $optn_query = $this->db->query("SELECT option_value_reference, price, price_prefix, points, points_prefix FROM `".DB_PREFIX."product_option_value` WHERE product_option_value_id = '".$option['product_option_value_id']."' ");
                            if(isset($optn_query->row['option_value_reference'])) {
                                //$price = $optn_query->row['price'];
                                $ref = $optn_query->row['option_value_reference'];
                                if(!$ref) {
                                    $ref = $option['product_option_value_id'];
                                }
                            } else {
                                //$price = 0;
                                $ref = $option['product_option_value_id'];
                            }                 
                            $bundle_item['options'][] = array(
                                "option_list_name" =>  $option['name'],
                                "name" =>  $option['value'],
                                "ref" =>  $ref,
                                "price" => $no_price,
                                //"price" =>  $this->currencyformat($price, $order_info['currency_code']),//"1.00 EUR",
                                "quantity" =>  1,
                                //removed => false
                            );
                        }
                    }
                    if($bundle['toppings']) {
                        foreach($bundle['toppings'] as $option) {               
                            $bundle_item['options'][] = array(
                                "option_list_name" =>  $option['name'],
                                "name" =>  $option['value'],
                                "price" => $no_price,
                                //"price" =>  $this->currencyformat($option['price'], $order_info['currency_code']),
                                "quantity" =>  $option['quantity'],
                                //"ref" => $option['product_topping_value_id']
                                //removed => false
                            );
                        }
                    }

                    if($bundle['dressings']) {
                        foreach($bundle['dressings'] as $option) {               
                            $bundle_item['options'][] = array(
                                "option_list_name" =>  $option['name'],
                                "name" =>  $option['value'],
                                "price" => $no_price,
                                //"price" =>  $this->currencyformat(0, $order_info['currency_code']),
                                "quantity" =>  1,
                                //"ref" => $option['product_dressing_value_id']
                                //removed => false
                            );
                        }
                    }
                    $bundle_item['deal_line'] = $item['deal_line'];
                    $data['items'][] = $bundle_item;
                }
                $dealcount++;
                //$data['items'][] = $bundle_items;
            } else {
                $data['items'][] = $item;
            }

        }

        $charges = array();
        $discount = array();

        $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order ASC");
        foreach($order_total_query->rows as $row) {
            if($row['code']=='sub_total' || $row['code']=='total') {
                continue;
            }
            if($row['code']=='shipping') {
                $type = 'delivery';
            } else {
                $type = $row['code'];
            }
            if($row['value'] > 0 || $type=='delivery') {
                $charges[] = array(
                    //"private_ref" => $row['code'],
                    "type" => $type,
                    "name" => $row['title'],
                      "ref" =>  null,
                      "price" => $this->currencyformat($row['value'],$order_info['currency_code'])
                );                
            } else {
                $discount[] = array(
                      //"private_ref" => $row['code'],
                      "name" => $row['title'],
                      "ref" =>  null,
                      "price_off" => $this->currencyformat($row['value'],$order_info['currency_code'])
                );  
            }
        }

        $data['discounts'] = $discount;
        $data['charges'] = $charges;

        //return $data;

        $this->log->write('upload order '.$order_id.json_encode($data));
        $result = $this->connectUrl('v1/location/orders','POST',$data);
        if(isset($result['id'])) {
            $this->db->query("UPDATE `".DB_PREFIX."order` o SET hubrise_order_id = '".$this->db->escape($result['id'])."', date_modified = NOW() WHERE order_id = '".(int)$order_id."' ");
        } else {
            $this->log->write('EXPORT ORDER TO HUBRISE '.json_encode($result));            
        }

        return $result;
    }
    public function setProductsInventory($productIds) {
        if($this->config->get('config_hubrise_inventory')) {
          
            $hubrise_catalogs = json_decode($this->config->get('hubrise_catalogs'),true);
            $catalog_id = array_key_first($hubrise_catalogs);
            if($catalog_id) {
                $this->load->model('catalog/product');

                $inventory = [];
                foreach ($productIds as $productId) {
                    $query = $this->db->query("SELECT  p.product_id, p.ref_code, p.subtract ,p.quantity FROM ".DB_PREFIX."product p WHERE p.status = 1 AND p.product_id='".$productId['product_id']."'");
                    if($query->row) {
                        $product = $query->row;
                        if($product['subtract']) {
                            $ref_code = ($product['ref_code']) ? $product['ref_code'] : $product['product_id'];
                            $inventory['p'.$ref_code] = [ "sku_ref"=> $ref_code,    "stock"=> $product['quantity']];
                        }
                        $product_options = $this->model_catalog_product->getProductOptions($productId['product_id']);
                        foreach ($product_options as $pok=> $product_option){
                            foreach ($product_option['product_option_value'] as $product_option_value){
                                if($product_option_value['subtract']) {
                                    $option_ref_code = ($product_option_value['option_value_reference']) ? $product_option_value['option_value_reference'] : $product_option_value['product_option_value_id'];
                                    $inventory['o'.$option_ref_code] = ['option_ref' => $option_ref_code,'stock'=> $product_option_value['quantity'] ];
                                }
                            }
                        }
                    }
                }

                $data = array_values($inventory) ;

                if($data) {
                    $result = $this->connectUrl('v1/catalogs/'.$catalog_id.'/location/inventory','PATCH',$data);
                  //  $this->log->write('HUBRISE inventory returs: ' . print_r($result,true));
                    return json_encode($result);
                }

               
            }else {
                $this->log->write('HUBRISE inventory NO catelog to sysnc ');
                return false;        

            }
        }
        return false;        
    }
}
?>