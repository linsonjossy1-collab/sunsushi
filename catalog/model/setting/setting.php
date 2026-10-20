<?php 
class ModelSettingSetting extends Model {
	public function getSetting($group, $store_id = 0) {
		$data = array(); 
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
		
		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$data[$result['key']] = $result['value'];
			} else {
				$data[$result['key']] = unserialize($result['value']);
			}
		}

		return $data;
	}
        public function getStoretimes(){
            $store_times = array();
            $sunday_times=$this->config->get('sunday_store_times');
            if(!empty($sunday_times)){
                foreach($sunday_times as $sunday_time){
                    if(!empty($sunday_time['start'])){
                        $store_times['sunday'][]=array('start'=>$sunday_time['start'],'end'=>($sunday_time['end']=='00:00' || $sunday_time['end']=='23:59')?'Till Late':$sunday_time['end']);
                    }
                }
                if(empty($store_times['sunday'])){
                    $store_times['sunday']=array();
                }
            }else{
                $store_times['sunday']='Closed';
            }
            $monday_times=$this->config->get('monday_store_times');
            if(!empty($monday_times)){
                foreach($monday_times as $monday_time){
                    if(!empty($monday_time['start'])){
                        $store_times['monday'][]=array('start'=>$monday_time['start'],'end'=>($monday_time['end']=='00:00' || $monday_time['end']=='23:59')?'Till Late':$monday_time['end']);
                    }
                }
                if(empty($store_times['monday'])){
                    $store_times['monday']=array();
                }
            }else{
                $store_times['monday']='Closed';
            }
            $tuesday_times=$this->config->get('tuesday_store_times');
            if(!empty($tuesday_times)){
                foreach($tuesday_times as $tuesday_time){
                    if(!empty($tuesday_time['start'])){
                        $store_times['tuesday'][]=array('start'=>$tuesday_time['start'],'end'=>($tuesday_time['end']=='00:00' || $tuesday_time['end']=='23:59')?'Till Late':$tuesday_time['end']);
                    }
                }
                if(empty($store_times['tuesday'])){
                    $store_times['tuesday']=array();
                }
            }else{
                $store_times['tuesday']='Closed';
            }
            $wednesday_times=$this->config->get('wednesday_store_times');
            if(!empty($wednesday_times)){
                foreach($wednesday_times as $wednesday_time){
                    if(!empty($wednesday_time['start'])){
                        $store_times['wednesday'][]=array('start'=>$wednesday_time['start'],'end'=>($wednesday_time['end']=='00:00' || $wednesday_time['end']=='23:59')?'Till Late':$wednesday_time['end']);
                    }
                }
                if(empty($store_times['wednesday'])){
                    $store_times['wednesday']=array();
                }
            }else{
                $store_times['wednesday']='Closed';
            }
            $thursday_times=$this->config->get('thursday_store_times');
            if(!empty($thursday_times)){
                foreach($thursday_times as $thursday_time){
                    if(!empty($thursday_time['start'])){
                        $store_times['thursday'][]=array('start'=>$thursday_time['start'],'end'=>($thursday_time['end']=='00:00' || $thursday_time['end']=='23:59')?'Till Late':$thursday_time['end']);
                    }
                }
                if(empty($store_times['thursday'])){
                    $store_times['thursday']=array();
                }
            }else{
                $store_times['thursday']='Closed';
            }
            
            $friday_times=$this->config->get('friday_store_times');
            if(!empty($friday_times)){
                foreach($friday_times as $friday_time){
                    if(!empty($friday_time['start'])){
                        $store_times['friday'][]=array('start'=>$friday_time['start'],'end'=>($friday_time['end']=='00:00' || $friday_time['end']=='23:59')?'Till Late':$friday_time['end']);
                    }
                }
                if(empty($store_times['friday'])){
                    $store_times['friday']=array();
                }
            }else{
                $store_times['friday']='Closed';
            }
            $saturday_times=$this->config->get('saturday_store_times');
            if(!empty($saturday_times)){
                foreach($saturday_times as $saturday_time){
                    if(!empty($saturday_time['start'])){
                        $store_times['saturday'][]=array('start'=>$saturday_time['start'],'end'=>($saturday_time['end']=='00:00' || $saturday_time['end']=='23:59')?'Till Late':$saturday_time['end']);
                    }
                }
                if(empty($store_times['saturday'])){
                    $store_times['saturday']=array();
                }
            }else{
                $store_times['saturday']='Closed';
            }
            return $store_times;
            
        }
        public function getDdeliverydetails(){
            $dleivery_areas =  array();
            $delivery_postcode_options = $this->config->get('home_delivery_postcode_options');
             if (!empty($delivery_postcode_options)) {
                  foreach ($delivery_postcode_options as $postcode_option) {
                       $shipping_postcodes = explode("\n", $postcode_option['postcode']);
                        foreach ($shipping_postcodes as $ship_postcode) {
                            $dleivery_areas[]=array(
                                'cartamount'=>$this->currency->format($postcode_option['cartamount']),
                                'area'=>$postcode_option['area'],
                                'postcode'=>$ship_postcode,
                                'shippingamount'=>$postcode_option['shippingamount']
                            );
                        }
                  }
             }else {
                  $delivery_options =  $this->config->get('home_delivery_options');
                  if($delivery_options) {
                    foreach ($delivery_options as $delivery_option) {
                         $dleivery_areas[]=array(
                                  'distance'=>$delivery_option['distance'],
                                  'cartamount'=>$this->currency->format($delivery_option['cartamount']),
                                  'shippingamount'=>$delivery_option['shippingamount']
                          );
                    }
                  }
             }
             return $dleivery_areas;
        }
         public function getStoretables() {
            $table_data = array();
            $table_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store_tables`");
            if(!empty($table_query->rows)){
                foreach ($table_query->rows as $row) {
                    $tablesettings = unserialize($row['settings']);
                    $status = 1;
                    if(!empty($tablesettings) && isset($tablesettings['tb']) && $tablesettings['tb']=='1'){
                        $status = 0;
                    }
                    $table_data[$row['table_id']]=array('table_id'=>$row['table_id'],'name'=>$row['name'],'table_status'=>$status);
                }
            }
            return $table_data;
        }
}
?>