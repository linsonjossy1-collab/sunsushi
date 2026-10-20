<?php

class ModelSettingSms extends Model {

    private $Textmarkerurl = 'http://www.textmarketer.biz/services/rest/sms'; // url of the service
    private $username = 'myAPIusername'; // CHANGE THIS!!!
    private $password = 'myAPIpassword'; // CHANGE THIS!!
    private $textmarker_message_id, $textmarker_credits_used;

    function __construct() {
        
    }

    public function getMessageID() {
        return $this->textmarker_message_id;
    }

    public function getCreditsUsed() {
        return $this->textmarker_credits_used;
    }

    public function sendTextmarkerSMS($message)
	{
            if(!$message || !$this->config->get('config_sms_alertstore') || !$this->config->get('config_smsprovider_username') || !$this->config->get('config_smsprovider_password') ) return;
                //$mobile_no=$this->config->get('config_sms_alertstore');//447403458655
		$url_array= array('message'=>$message,'mobile_number'=>$this->config->get('config_sms_alertstore'), 'originator'=>$this->config->get('config_smsoriginator_mobileno'), // test
		'username'=>$this->config->get('config_smsprovider_username'), 'password'=>$this->config->get('config_smsprovider_password'));
        $url_string = $data = http_build_query($url_array, '', '&');
        // we're using the curl library to make the request
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $this->Textmarkerurl);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $url_string);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $responseBody = curl_exec($curlHandle);
        $responseInfo = curl_getinfo($curlHandle);
        curl_close($curlHandle);
        return $this->handleTextmarkerResponse($responseBody, $responseInfo);
    }

    private function handleTextmarkerResponse($body, $info) {
        if ($info['http_code'] == 200) { // successful submission
            $xml_obj = simplexml_load_string($body);
            // extract message id and credit usuage
            $this->textmarker_message_id = (int) $xml_obj->textmarker_message_id;
            $this->textmarker_credits_used = (int) $xml_obj->textmarker_credits_used;
            return true;
        } else {
            $this->textmarker_message_id = null;
            $this->textmarker_credits_used = null;
            // error handling
            return false;
        }
    }

}

?>