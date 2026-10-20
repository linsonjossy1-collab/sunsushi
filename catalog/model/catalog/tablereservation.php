<?php

class ModelCatalogTablereservation extends Model {

    public function addReservation($data) {
        if(!isset($data['comments'])) {
            $data['comments'] = '';
        }
         if($this->config->get('config_tblreservation_storetime')) {
            $reservation_text =  date('h:i A',$data['reservation_time']).' - '.date('h:i A',$data['reservation_time']+($this->config->get('config_tblreservation_storetime')*60));
        } else {
            $reservation_text =  date('h:i A',$data['reservation_time']);
        }
        $reservation_text .=  ' ('.date('d/m/Y',$data['reservation_time']).')';
        $this->db->query("INSERT INTO " . DB_PREFIX . "table_reservation SET reservation_date = '" . date("Y-m-d H:i:s", $data['reservation_time']) . "',reservation_peoples = '" . $this->db->escape($data['persons']) . "', reservation_name = '" . $this->db->escape($data['name']) . "',reservation_phone = '" . $this->db->escape($data['phone']) . "',reservation_email = '" . $this->db->escape($data['email']) . "'");
        //$order_id = $this->db->getLastId();
          $this->log->write(print_r(array("00"),true));
        if ($this->config->get('config_table_reservation_alert_mail')) {
            $subject = 'Table Reservation '.$this->config->get('config_name').' -' . $data['name'];
            $text  ='Hello, The following person is interested in reserving table.';
            $text .='Name : '.$data['name']."\n";
            $text .='Reservation Date : '.date("d-m-Y", $data['reservation_time'])."\n";
            $text .='Reservation Time : '.$reservation_text."\n";
            $text .='No.of Adults : '.$data['persons']."\n";
            if(isset($data['childrens']) && $data['childrens']!=''){
                $text .='No.of Childrens : '.$data['childrens']."\n";
            }
            $text .='Phone No : '.$data['phone']."\n";
            $text .='E-Mail : '.$data['email']."\n";
            $text .='Special instruction : '.$data['comments']."\n";

            $template = new Template();
            $template->data['title'] = $subject;
            $template->data['greetings'] = "Hi ".$this->config->get('config_name').",";
            $template->data['content'] = 'The following person is interested in reserving table.';
            $template->data['name'] = $data['name'];
            $template->data['reservation_time'] = $data['reservation_time'];
            $template->data['reservation_time_text'] = $reservation_text;
            $template->data['persons'] = $data['persons'];
            if(isset($data['childrens']) && $data['childrens']!=''){
                $template->data['childrens'] = $data['childrens'];
            }
            $template->data['phone'] = $data['phone'];
            $template->data['email'] = $data['email'];
            $template->data['comments'] = $data['comments'];
           $template->data['owner'] = '1';
            $template->data['footer_content'] = $this->config->get('config_name')."<br>".HTTPS_SERVER;


            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/tablereservation.tpl')) {
                $html = $template->fetch($this->config->get('config_template') . '/template/mail/tablereservation.tpl');
            } else {
                $html = $template->fetch('default/template/mail/tablereservation.tpl');
            }
            $this->log->write(print_r(array("11"),true));
            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setFrom($this->config->get('config_email'));
             $this->log->write(print_r(array("kkk"),true));
            //$mail->setReplyTo($data['email']);
             $this->log->write(print_r(array("22"),true));
            $mail->setSender($this->config->get('config_name'));
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setHtml($html);
            $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));

            if($this->config->get('config_tblreservation_email')) {
                $this->log->write(print_r(array("aaa"),true));
                $mail->setTo($this->config->get('config_tblreservation_email'));
                $mail->send();
                $from = $this->config->get('config_tblreservation_email');
            } else if($this->config->get('config_email')) {
                $from = $this->config->get('config_email');
            } else {
                $from = '';
            }

            if($from) {          
                // Send to additional alert emails
                $emails = explode(',', $this->config->get('config_table_reservation_additional_mails'));

                foreach ($emails as $email) {
                    if ($email && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
                        $mail->setTo($email); 
                        $mail->send();
                    }
                }
                  $this->log->write(print_r(array("22"),true));
                //Send an emial to customer
            $subject = 'Table Reservation '.$this->config->get('config_name').' -' . $data['name'];
            $text ='Thanks for your booking request. Please wait for confirmation of your booking from the us. In case you don’t hear from us please call us at '.$this->config->get('config_telephone').' to confirm.';
            $text .='Name : '.$data['name']."\n";
            $text .='Reservation Time : '.date("d-m-Y H:i A", $data['reservation_time'])."\n";
            $text .='People : '.$data['persons']."\n";
            $text .='Phone No : '.$data['phone']."\n";
            $text .='E-Mail : '.$data['email']."\n";
            $text .='Special instruction : '.$data['comments']."\n";

            $template = new Template();
            $template->data['owner'] = '';
            $template->data['title'] = $subject;
            $template->data['greetings'] = "Hi ".$data['name'].",";
            $template->data['content'].= "<b>Thank you for your booking at ".$this->config->get('config_name')."</b>";
            $template->data['content'].="<br>We're pleased to confirm your booking on ".$reservation_text." at ".date('H:i',$data['reservation_time']);

            if((int)$data['persons'] > 1) {
                $template->data['content'].= ' for '.$data['persons'].' people.';
            } else {
                $template->data['content'].= ' for 1 person.';                
            }
            $template->data['content'].= " .<br>";

            $template->data['store_info'] = 'We look forward to welcoming you! If we can be of further assistance please don’t hesitate to contact us.';
            $template->data['store_info'].= "<h3>".$this->config->get('config_name')."</h3>";
            $template->data['store_info'].= nl2br($this->config->get('config_address'))."<br>";
            $template->data['store_info'].= 'Telephone : '.$this->config->get('config_telephone')."<br>";
            if($this->config->get('config_tblreservation_email')) {
                $template->data['store_info'].= 'EMail : '.$this->config->get('config_tblreservation_email')."<br>";
            }
            $template->data['store_info'].= 'Web site: '.HTTPS_SERVER."<br>";
            $template->data['footer_content'] = "Call ".$this->config->get('config_telephone')." to cancel this booking. <div style='color:red;font-weight:600;'>Please note that we cannot reply to emails as this address is unmonitored </div>";

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/tablereservation.tpl')) {
                    $html = $template->fetch($this->config->get('config_template') . '/template/mail/tablereservation.tpl');
                } else {
                    $html = $template->fetch('default/template/mail/tablereservation.tpl');
                }
                
                $mail->setTo($data['email']);
                $mail->setFrom($this->config->get('config_email'));              
                //$mail->setReplyTo($from);
                $mail->setSender($this->config->get('config_name'));
                $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                $mail->setHtml($html);
                $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
                $mail->send();            
            }
        }
    }

}

?>