<?php
class ModelToolSMS extends Model {
	public function sendSingleSMS($message,$mobile,$sms_provider='') {
            if ($sms_provider=='') {
                $sms_provider = $this->config->get('config_default_sms_api');
            }
            switch ($sms_provider) {
                case 'textmarketer':
                    return $this->sendTextmarkerSMS($message,$mobile);
                    break;
                case 'infobip':
                    return $this->infobipSend($message,$mobile);
                    break;
                case 'twilio':
                    return $this->twilioSmsSend($message,$mobile);
                    break;     
            }
            
	}
	public function sendBulkSMS($message,$mobiles,$sms_provider='') {
            if ($sms_provider=='') {
                $sms_provider = $this->config->get('config_bulk_sms_api');
            }
            switch ($sms_provider) {
                case 'textmarketer':
                    return $this->sendTextmarkerSMS($message,$mobiles);
                    break;
                case 'infobip':
                    return $this->infobipSend($message,$mobiles);
                    break;
                case 'twilio':
                    return $this->twilioSmsSend($message,$mobiles);
                    break;   
            }
	}
		
	private function sendTextmarkerSMS($message,$mobiles){
            require_once DIR_SYSTEM .'vendor/sms/textmarketer.php';
            $textmarketer = new textmarketer();
            $textmarketer->setUsername($this->config->get('config_smsprovider_username'));
            $textmarketer->setPassword($this->config->get('config_smsprovider_password'));
            $textmarketer->setOriginator($this->config->get('config_smsoriginator_mobileno'));
            
            if(is_array($mobiles)){
                $resnponse = array();
                foreach ($mobiles as $mobile) {
                    $resnponse[] = $textmarketer->sendTextmarkerSMS($message,$mobile);
                }
                return $resnponse;
            }
            else {
               return $textmarketer->sendTextmarkerSMS($message,$mobiles);
            }
            
        }
        private function infobipSend($message,$mobiles){
            require_once DIR_SYSTEM .'vendor/sms/infobip.php';
            $infobip = new infobip();
            $infobip->setKey($this->config->get('config_infobip_authorization'));
            if(is_array($mobiles)){
                foreach ($mobiles as $key => $mobile) {
                    if('07' == substr("".$mobile,0,2) ) {
                        $mobiles[$key] = '447' . substr($mobile,2);
                    }
                }
            }
           
             // SEND Messages
            $data = array('from'=>$this->config->get('config_infobip_from'),'to'=>$mobiles,'text'=>$message,
                 'notifyUrl'=>HTTP_SERVER .'?index.php?route=module/sale',
         'notifyContentType'=>'application/json');
            //{ \"from\":\"InfoSMS\", \"to\":\"41793026727\", \"text\":\"Test SMS.\" }

            $return = $infobip->send (json_encode($data));
            $this->log->write(print_r($return,true));
            $json = array();
            $json['success']="Message sent to next instance";
            return $json;
        }
        private function twilioSmsSend($message,$mobiles){
            require_once DIR_SYSTEM .'vendor/sms/twiliosms.php';
            $twiliosms = new TwilioSMS();
            $twiliosms->setSID( $this->config->get('config_twilio_sms_sid'));
            $twiliosms->setToken($this->config->get('config_twilio_sms_token'));
            $twiliosms->setFromPhonenumber($this->config->get('config_twilio_sms_phonenumber'));      

            if(is_array($mobiles)){
                $response = array();
                foreach ($mobiles as $mobile) {
                    if('07' == substr("".$mobile,0,2) ) {
                        $mobile = '+447' . substr($mobile,2);
                        $response[] = $twiliosms->sendsms($message,$mobile);
                    }
                }
                return $response;
            }
            else {
                if('07' == substr("".$mobiles,0,2) ) {
                    $mobiles = '+447' . substr($mobiles,2);
                    return $twiliosms->sendsms($message,$mobiles);   
                }    
                
            }
        }
         public function balance(){
            require_once DIR_SYSTEM .'vendor/sms/infobip.php';
            $json = array();
            $infobip = new infobip();
            $infobip->setKey($this->config->get('config_infobip_authorization'));
            $json['infobip']=$infobip->balance();
            return $json;
        }
}
?>