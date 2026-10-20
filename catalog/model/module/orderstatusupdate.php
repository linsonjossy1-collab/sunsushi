<?php
class ModelModuleOrderStatusUpdate extends Model {

    public function getData() {
        $return_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status_update_notification");

        foreach ($query->rows as $result) {
            $notify_types = @unserialize($result['notify']);
            if (!is_array($notify_types)) {
                $notify_types = array();
            }

            $notify_data = @unserialize($result['notify_data']);
            if (!is_array($notify_data)) {
                $notify_data = array();
            }

            $row = array(
                'id' => $result['id'],
                'status' => unserialize($result['status']),
            );

            // Merge stored notify status flags (email_status, sms_status, etc.)
            foreach ($notify_types as $key => $value) {
                $row[$key] = $value;
            }

            // Merge any stored notify_data fields (content_email, content_sms, etc.)
            foreach ($notify_data as $key => $value) {
                $row[$key] = $value;
            }

            $return_data[] = $row;
        }
       // print_r($return_data);
        return $return_data;
    }

    public function sendNotification($order_id, $order_status_id,$order_info) {
        // Fetch the notifications for the given order status
        $this->log->write( print_r("Notify", true));
        //$this->log->write( print_r($order_info, true));
        $notifications =$this->getData();
        //$this->log->write( print_r($notifications, true));
        $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
        $order_status_name = '';
        if ($order_status_query->num_rows) {
            $order_status_name = $order_status_query->row['name'];
        }
        $this->load->model('checkout/order');
        $order_products = $this->model_checkout_order->getFormattedOrderProducts($order_id);
        $order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int) $order_id . "' ORDER BY sort_order ASC");
        $order_totals = $order_total_query->rows;
        //product data for notification

        $product_data = '<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;"><tbody>';

        foreach ($order_products as $product) {
            $product_data .= '<tr>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">' . $product['name'];

            foreach ($product['option'] as $option) {
                $product_data .= '<br />&nbsp;<small> - ' . $option['name'] . ': ' . $option['value'] . '</small>';
            }

            foreach ($product['toppings'] as $topping) {
                $product_data .= '<br />-<small> ' . $topping['quantity'] . ' X ' . $topping['name'] . ':' . $topping['value'] . '</small><br />';
            }

            foreach ($product['dressings'] as $dressing) {
                $product_data .= '<br />-<small>' . $dressing['name'] . ':' . $dressing['value'] . '</small><br />';
            }

            foreach ($product['bundle'] as $bundle) {
                $product_data .= '<br /><b><small>' . $bundle['name'] . ':</small></b><br />';

                foreach ($bundle['options'] as $bundleoption) {
                    $product_data .= '-- <small>' . $bundleoption['name'] . ': ' . $bundleoption['value'] . '</small><br />';
                }

                foreach ($bundle['toppings'] as $bundtopping) {
                    $product_data .= '-<small> ' . $bundtopping['quantity'] . ' X ' . $bundtopping['name'] . ':' . $bundtopping['value'] . '</small><br />';
                }

                foreach ($bundle['dressings'] as $bundressing) {
                    $product_data .= '-<small>' . $bundressing['name'] . ':' . $bundressing['value'] . '</small><br />';
                }
            }

            $product_data .= '</td>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['quantity'] . '</td>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['price'] . '</td>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['total'] . '</td>';
            $product_data .= '</tr>';
        }

        $product_data .= '</tbody><tfoot>';

        foreach ($order_totals as $total) {
            $product_data .= '<tr>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;" colspan="3"><b>' . $total['title'] . ':</b></td>';
            $product_data .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $total['text'] . '</td>';
            $product_data .= '</tr>';
        }

        $product_data .= '</tfoot></table>';
        
        $plain_product_data = '';
        foreach ($order_products as $product) {
            $plain_product_data .= $product['name'] . "\n";
            foreach ($product['option'] as $option) {
                $plain_product_data .= " - " . $option['name'] . ": " . $option['value'] . "\n";
            }
            foreach ($product['toppings'] as $topping) {
                $plain_product_data .= " - " . $topping['quantity'] . " X " . $topping['name'] . ":" . $topping['value'] . "\n";
            }
            foreach ($product['dressings'] as $dressing) {
                $plain_product_data .= " - " . $dressing['name'] . ":" . $dressing['value'] . "\n";
            }
            foreach ($product['bundle'] as $bundle) {
                $plain_product_data .= $bundle['name'] . ":\n";
                foreach ($bundle['options'] as $bundleoption) {
                    $plain_product_data .= " -- " . $bundleoption['name'] . ": " . $bundleoption['value'] . "\n";
                }
                foreach ($bundle['toppings'] as $bundtopping) {
                    $plain_product_data .= " - " . $bundtopping['quantity'] . " X " . $bundtopping['name'] . ":" . $bundtopping['value'] . "\n";
                }
                foreach ($bundle['dressings'] as $bundressing) {
                    $plain_product_data .= " - " . $bundressing['name'] . ":" . $bundressing['value'] . "\n";
                }
            }
            $plain_product_data .= "Quantity: " . $product['quantity'] . ", Price: " . $product['price'] . ", Total: " . $product['total'] . "\n\n";
        }
        foreach ($order_totals as $total) {
            $plain_product_data .= $total['title'] . ": " . $total['text'] . "\n";
        }
        
        
        $requested_time = $order_info['requested_time'];
        if(strtotime($order_info['requested_time']) <= strtotime($order_info['date_added'])) {
             $this->log->write( print_r("ASAP ", true));
            if($order_info['shipping_code'] == 'home.home') {
                $store_delivery_time = $this->config->get('home_delivery_storetime');
                $shippingcode='home';
            }else{
                $store_delivery_time = $this->config->get('collect_in_storetime');
                $shippingcode='collect';
            } 
            $store_delivery_time = $this->postcode->getUpdatedRequestedtime($shippingcode,$store_delivery_time);
            $asap_time = date('h:i A', (time() + ($store_delivery_time * 60)));
            $requested_time = 'AS SOON AS POSSIBLE';
            $this->log->write( print_r($requested_time, true));
        }
        if($order_info['delivery_time']!='0000-00-00 00:00:00'){
            $requested_time=$order_info['delivery_time'];
            $this->log->write( print_r("UP DEL TIME ", true));
        }else{
            $order_info['delivery_time'] = $requested_time;
        }
        //$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status_update_notification");
 
        $store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
        foreach ($notifications as $notification) {
           
            if (in_array($order_status_id, $notification['status'])) {
                // Send notification based on the notify method
                 $this->log->write( print_r("Status succ", true));
                if (isset($notification['email_status']) && $notification['email_status']=='1') {
                     $this->log->write( print_r("email notify", true));
                     $template = new Template();
                    $template->data['store_name'] = $store_name;
                    if($this->config->get('config_email_header')) {
                        $template->data['header_image'] = HTTP_IMAGE . $this->config->get('config_email_header');
                    } else {
                        $template->data['header_image'] = '';
                    }
                    if($this->config->get('config_email_offer')) {
                        $template->data['offer_image'] = HTTP_IMAGE . $this->config->get('config_email_offer');
                    } else {
                        $template->data['offer_image'] = '';
                    }
                    if($this->config->get('config_email_footer')) {
                        $template->data['footer_image'] = HTTP_IMAGE . $this->config->get('config_email_footer');
                    } else {
                        $template->data['footer_image'] = '';
                    }
                    $template->data['offer_url'] = $order_info['store_url'] .  $this->config->get('config_email_offer_url');
                    $template->data['message'] = str_replace(
                        array('{NAME}', '{ORDER_ID}', '{STATUS}', '{REQUESTED_TIME}', '{DELIVERY_TIME}','{PRODUCTS}','{STORE_NAME}','{STORE_PHONE}','{STORE_EMAIL}','{PAYMENT_NAME}','{SHIPPING_NAME}'),
                        array($order_info['firstname'], $order_id, $order_status_name, $requested_time, $order_info['delivery_time'],$product_data,$store_name,$this->config->get('config_telephone'),$this->config->get('config_email'),$order_info['payment_method'],$order_info['shipping_method']),
                        html_entity_decode($notification['email_content'])
                    );

                    
                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/order_update_notification.tpl')) {
                        $html = $template->fetch($this->config->get('config_template') . '/template/mail/order_update_notification.tpl');
                    } else {
                        $html = $template->fetch('default/template/mail/order_update_notification.tpl');
                    }
                    
                    // $this->log->write($html);
                    $subject =($notification['email_subject']!='')?$notification['email_subject']:$store_name.'Order Update';

                        $mail = new Mail();
                        $mail->protocol = $this->config->get('config_mail_protocol');
                        $mail->parameter = $this->config->get('config_mail_parameter');
                        $mail->hostname = $this->config->get('config_smtp_host');
                        $mail->username = $this->config->get('config_smtp_username');
                        $mail->password = $this->config->get('config_smtp_password');
                        $mail->port = $this->config->get('config_smtp_port');
                        $mail->timeout = $this->config->get('config_smtp_timeout');
                        $mail->setTo($order_info['email']);
                        $mail->setFrom($this->config->get('config_email'));
                        $mail->setSender($store_name);
                        $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                        $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                        $mail->setHtml($html);
                        $mail->send();
                    // Code to send email notification
                    // You can use OpenCart's Mail class to send emails
                }
                if (isset($notification['sms_status']) && $notification['sms_status']=='1') {

                    // $this->log->write( print_r("Sms Notify", true));
                    $this->load->model('tool/sms');
                    $message = str_replace(
                        array('{NAME}', '{ORDER_ID}', '{STATUS}', '{REQUESTED_TIME}', '{DELIVERY_TIME}', '{PRODUCTS}','{STORE_NAME}','{STORE_PHONE}','{STORE_EMAIL}','{PAYMENT_NAME}','{SHIPPING_NAME}'),
                        array($order_info['firstname'], $order_id, $order_status_name, $requested_time, $order_info['delivery_time'], $plain_product_data,$store_name,$this->config->get('config_telephone'),$this->config->get('config_email'),$order_info['payment_method'],$order_info['shipping_method']),
                        $notification['sms_content']
                    );
                     //$this->log->write( print_r($message, true));
                    $response =  $this->model_tool_sms->sendSingleSMS($message,$order_info['telephone']);
                    //$this->log->write( print_r($response, true));
                    // Code to send SMS notification
                    // You can integrate with an SMS gateway API to send SMS
                }
                if ($this->config->get('ownerappapi_firebase_api_key') && isset($notification['owner_push_status']) && $notification['owner_push_status']=='1') {
                        //$this->log->write( print_r("Owner Notify", true));
                            $devicetokens_OWNER = array();
                            $query = $this->db->query("SELECT device_token  FROM  " . DB_PREFIX . "app_owner  group by device_token");	
                            foreach ($query->rows  as $owner) {
                                $devicetokens_OWNER[]=$owner['device_token'];
                            }
                            $ownerpush = str_replace(
                                    array('{NAME}', '{ORDER_ID}', '{STATUS}', '{REQUESTED_TIME}', '{DELIVERY_TIME}', '{PRODUCTS}','{STORE_NAME}','{STORE_PHONE}','{STORE_EMAIL}','{PAYMENT_NAME}','{SHIPPING_NAME}'),
                                    array($order_info['firstname'], $order_id, $order_status_name, $requested_time, $order_info['delivery_time'], $plain_product_data,$store_name,$this->config->get('config_telephone'),$this->config->get('config_email'),$order_info['payment_method'],$order_info['shipping_method']),
                                    $notification['owner_push_content']
                                );
                            $push_data = array('devicetoken' => $devicetokens_OWNER, 'title' => (isset($notification['owner_push_content']) && $notification['owner_push_content']!='')?$notification['owner_push_content']:$this->config->get('config_ownerappapi_notification_title'), 'message' => $ownerpush, 'data' => array(
                                    'order_id' => $order_info['order_id'],
                                    'customer_id' => $order_info['customer_id'],
                                    'action' => 'orderList'
                            ));

                            $fcm = new FCM($this->config->get('ownerappapi_firebase_api_key'));
                            $response = $fcm->send($push_data);
                            //$this->log->write('OWNER APP - ADMIN :'.print_r($response, true));
                            
                        
                }
                if ($this->config->get('firebase_api_key') && isset($notification['customer_push_status']) && $notification['customer_push_status']=='1') {
                    //$this->log->write( print_r("Customer app Notify", true));
                    $this->load->model('account/customer');
                    $customer_info = $this->model_account_customer->getCustomer($order_info['customer_id']);

                    $devicetokens = array()		;
                    if ($customer_info && $customer_info['fcm_token']) {

                        $devicetokens[] = $customer_info['fcm_token'];
                        // #TODO Change to Push languge file
                       
                        $subject = $notification['customer_push_first_line'];

                        $message  = str_replace(
                                    array('{NAME}', '{ORDER_ID}', '{STATUS}', '{REQUESTED_TIME}', '{DELIVERY_TIME}', '{PRODUCTS}','{STORE_NAME}','{STORE_PHONE}','{STORE_EMAIL}','{PAYMENT_NAME}','{SHIPPING_NAME}'),
                                    array($order_info['firstname'], $order_id, $order_status_name, $requested_time, $order_info['delivery_time'], $plain_product_data,$store_name,$this->config->get('config_telephone'),$this->config->get('config_email'),$order_info['payment_method'],$order_info['shipping_method']),
                                    $notification['customer_push_content']
                                );
            
                        $push_data = array('devicetoken'=>$devicetokens, 'title'=> $subject,'message'=>$message, 'data'=>array(
                            'order_id' =>$order_id,
                            'action' => 'order_update',
                            'status' => $order_status_query->row['name'],
                        ));
                        // $this->log->write( print_r($push_data, true));

                        $fcm = new FCM($this->config->get('firebase_api_key'));
                        $response = $fcm->send($push_data);
                        //$this->log->write(  print_r($response, true));
                    }

                }
                if (isset($notification['driver_push_status']) && $notification['driver_push_status']=='1') {
                    // TODO: Implement driver push notification
                    // Similar to customer push, but for drivers
                }
            }
        }
        
    }

}
