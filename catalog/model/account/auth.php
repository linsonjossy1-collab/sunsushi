<?php
class ModelAccountAuth extends Model {
	
	
	public function insert_auth_token($auth_token=NULL,$users_id=NULL)
	{
        // Insert the auth_token passed in into the auth table
        $curdate = date("Y-m-d H:i:s");
        $date_expires = date("Y-m-d H:i:s", strtotime("+1 day"));

		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_auth` SET `customer_auth_customer_id` = '" . (int)$users_id . "', `customer_auth_token` = '" . $this->db->escape($auth_token) . "', `customer_auth_date_created` = '" . $curdate . "',  `customer_auth_date_modified` = '" . $curdate . "',  `customer_auth_date_expires` = '" . $date_expires . "',  `customer_auth_status` = 'incomplete' ");
    }
	
	
	/* checking verification code exists in db, return array for true */
	public function checkVerificationcode($code = NULL)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_auth WHERE customer_auth_token = '" . $code . "' AND customer_auth_status = 'incomplete'");
		
		return $query->row;
	}
	

	/* updating verification attempts */
	public function updateVerificationAttempt($code = NULL,$attempts = NULL) {
            $this->db->query("UPDATE " . DB_PREFIX . "customer_auth SET customer_auth_attempts = '" . (int)$attempts . "' WHERE customer_auth_token = '" . $code . "'");
	}
					

 	/* updating verification code status */
	public function updateVerificationCodestatus($code = NULL) {
            $this->db->query("UPDATE " . DB_PREFIX . "customer_auth SET customer_auth_status = 'complete' WHERE customer_auth_token = '" . $code . "'");
	}
	
	
	public function unique_auth_token($auth_token) {
        // Check the auth_token passed in to see if it already exists in the auth table
		$uniqueAuthTokenQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_auth WHERE customer_auth_token = '" . $this->db->escape($auth_token) . "'  ");

        if($uniqueAuthTokenQuery->num_rows>0) {
            return false;
        } else {
            return true;
        }
    }
	
	
	public function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}	
	
}
	
 