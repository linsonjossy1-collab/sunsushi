<?php
require_once( DIR_SYSTEM . 'vendor/twilio/vendor/autoload.php');
use Twilio\Rest\Client;

class ModelSettingTwilio extends Model {

    // Twilio REST API version
    private $version = "2010-04-01";
    // Set our Account SID and AuthToken
    private $sid = 'AC123';  // CHANGE THIS!!!
    private $token = 'abcd'; // CHANGE THIS!!
    // A phone number you have previously validated with Twilio
    private $from_phonenumber = '4151234567'; // CHANGE THIS!!
    private $store_phonenumber='';       
   
    public function startcall( $type = 0, $orderid = 0,$phonenumber='') {
        // Config
        $this->sid = $this->config->get('config_twilio_sid');
        $this->token = $this->config->get('config_twilio_token');
        $this->from_phonenumber = $this->config->get('config_twilio_phonenumber');
        $this->store_phonenumber = $this->config->get('config_twilio_storephone');
        
        if(!$this->sid || !$this->token || !$this->from_phonenumber ||  !$this->store_phonenumber ) return;
        $url = '';
        switch ($type) {
            case 0:
                $url = $this->config->get('config_url') . 'index.php?route=module/twilio/printer';
            case 1:
                $url = $this->config->get('config_url') . 'index.php?route=module/twilio&orderid=' . $orderid;
            case 2:
                $url = $this->config->get('config_url') . 'index.php?route=module/twilio/opending&orderid=' . $orderid;
        }
        if ($url != '') {
           
            $twilio  = new Client($this->sid, $this->token);
            try {
            $call = $twilio->calls
               ->create(
                       ($phonenumber==''?$this->store_phonenumber:$phonenumber), // The number of the phone receiving call
                        $this->from_phonenumber, // The number of the phone initiating the call
                        ["url" =>$url] // The URL Twilio will request when the call is answered
                );
                return 'Started call: ' . $call->sid;
            } catch (Exception $e) {
               return 'Error: ' . $e->getMessage();
        }
    }
    }

}

?>