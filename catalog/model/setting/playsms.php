<?php

class ModelSettingPlaysms extends Model {

    private $playsms_url = ''; // url of the service
    private $playsms_u = ''; // CHANGE THIS!!!
    private $playsms_p = ''; // CHANGE THIS!!
    private $playsms_message_id, $playsms_credits_used;

    public function getMessageID() {
        return $this->playsms_message_id;
    }

    public function getCreditsUsed() {
        return $this->playsms_credits_used;
    }

    public function playsmsAction($params_array) {

        $url_string = $data = http_build_query($params_array, '', '&');
        // we're using the curl library to make the request
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $this->playsms_url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $url_string);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $responseBody = curl_exec($curlHandle);
        $responseInfo = curl_getinfo($curlHandle);
        curl_close($curlHandle);

        if ($responseInfo['http_code'] == 200) { // successful submission
            $output = unserialize($responseBody);
            return $output;
        } else {

            return false;
        }
    }

    public function sendSMS($sms_array) {
        $token = $this->getTocken();
        if ($token) {
            $this->playsms_url = $this->config->get('playsms_url') . '/';
            // broadcast sms
            $this->playsms_url = $this->config->get('playsms_url') . '/index.php?app=webservices';

            $output = $this->playsmsAction(array('u' => $this->config->get('playsms_u'), 'h' => $token, 'ta' => 'pv',
                'format' => 'serialize', 'from' => $this->config->get('playsms_from'), 'unicode' => '1', 'nofooter' => '1',
                'to' => $sms_array['to'],
                'msg' => $sms_array['msg'],
                'schedule' => $sms_array['schedule']));
            $now = date("y-m-d h:i:s");

            if (isset($output['data'])) {
                foreach ($output['data'] as $que) {

                    $this->addSmsQueue($que['queue'], $que['smslog_id'], $que['to'], $sms_array['msg'], $sms_array['schedule'], $now, ($que['status'] == 'OK' ? 1 : 0));
                }
            }

            return $output;
        }
    }

    public function getTocken($params = array()) {
        if (isset($params['playsms_url'])) {
            $this->playsms_url = $params['playsms_url'] . '/index.php?app=webservices';
            $output = $this->playsmsAction(array('u' => $params['playsms_u'], 'p' => $params['playsms_p'], 'ta' => 'get_token', 'format' => 'serialize'));
        } else {
            if (isset($this->session->data['playsms_tocken'])) {
                return $this->session->data['playsms_tocken'];
            }
            $this->playsms_url = $this->config->get('playsms_url') . '/index.php?app=webservices';
            $output = $this->playsmsAction(array('u' => $this->config->get('playsms_u'), 'p' => $this->config->get('playsms_p'), 'ta' => 'get_token', 'format' => 'serialize'));
        }
        if ($output && isset($output['status']) && $output['status'] == 'OK') {
            $this->session->data['playsms_tocken'] = $output['token'];
            return $this->session->data['playsms_tocken'];
        } else {
            return false;
        }
    }

    public function addSmsQueue($queue_code, $smslog_id, $to, $msg, $schedule, $now, $flag, $footer = '', $unicode = 1) {

        $this->db->query("INSERT INTO " . DB_PREFIX . "playsms_queue SET "
             . "`queue_code` ='" . $queue_code . "',
            `smslog_id`=" . (int) $smslog_id . ",
            `datetime_entry` ='" . $now . "',
            `datetime_scheduled` ='" . $schedule . "',
            `flag`=" . (int) $flag . ",
            `sender_id`='" . $to . "',
            `footer` ='" . $footer . "',
            `message`='" . mysql_real_escape_string($msg) . "' ,
            `sms_type` = 'text',
            `unicode` =" . (int) $unicode . "");
    }
    public function getSmsQueue($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "playsms_queue r";																																					  
		
		$sort_data = array(
                        'r.queue_code',
                        'r.smslog_id',
                        'r.datetime_entry',
                        'r.datetime_scheduled',
                        'r.datetime_update',
			'r.flag'
			
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY r.datetime_entry";	
		}
			
		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}																																							  
																																							  
		$query = $this->db->query($sql);																																				
		
		return $query->rows;
        
    }
    public function getTotalSmsQueue() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "playsms_queue");
		
	return $query->row['total'];
    }
    public function deleteSmsQueue($queue_id) {
        $query = $this->db->query("DELETE FROM " . DB_PREFIX . "playsms_queue WHERE queue_id='" . $queue_id . "'");
	return true;
    }
   

}

?>