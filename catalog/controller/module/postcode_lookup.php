<?php  
class ControllerModulePostcodelookup extends Controller {
	protected function index($setting) {
            
	}
        public function postcodelookup(){
        $json = array();
        if(!empty($this->request->get['postcode'])) {
            $postcode = $this->postcode->postcodeformat($this->request->get['postcode']);
            $json = $this->postcode->getPostcodeDetails($postcode);
        }
        //print_r($json);
        $this->response->setOutput(json_encode($json));
    
    }
}
?>