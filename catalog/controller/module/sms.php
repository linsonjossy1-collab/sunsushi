<?php
class ControllerModuleSms extends Controller {
    public function index() {
        $this->log->write(print_r($this->request->post,true));
        if(isset($this->request->post['reportResponse'])) {
            $reportResponse = json_decode($this->request->post['reportResponse']);
            if(isset($reportResponse['results'])){
                foreach ($reportResponse['results'] as $result){
                    if(isset($result['text']) && !empty($result['text'])) {
                        $result['text'] = trim(strtoupper($result['text']));
                        if(strpos($result['text'],'STOP')!==false || strpos($result['text'],'UNSUBSCRIBE')!==false) {
                            // UNSUBCRIBE;
                            $this->log->write(print_r($result,true));
                            if($result['from']) {
                                $mobile = $result['from'];
                                // Update customer table
                                if('44' == substr("".$mobile,0,2) ) {
                                    $mobile = '0' . substr($mobile,2);
                                }
                                $this->db->query("UPDATE " . DB_PREFIX . "customer SET sms_promotional_notifications = 1 WHERE telephone = '" . $mobile. "' OR phone_number1 '" . $mobile. "' OR phone_number2 '" . $mobile. "'");
                            }
                        }
                    }
                }
            }
            echo "1";
        }
        else {
            echo "0";
        }
        
    }

}
