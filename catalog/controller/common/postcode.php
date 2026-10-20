<?php 
class ControllerCommonPostcode extends Controller { 	
	public function home() { 
                $this->setdata();

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/postcodehome.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/postcodehome.tpl';
		} else {
			$this->template = 'default/template/common/postcodehome.tpl';
		}
		
		
	
		$this->response->setOutput($this->render());			
  	}
        public function button() { 
                $this->setdata();

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/postcodebutton.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/postcodebutton.tpl';
		} else {
			$this->template = 'default/template/common/postcodebutton.tpl';
		}
		
		
	
		$this->response->setOutput($this->render());			
  	}
        public function product() { 
                $this->setdata();

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/postcodeproduct.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/product/postcodeproduct.tpl';
		} else {
			$this->template = 'default/template/product/postcodeproduct.tpl';
		}
		
		
	
		$this->response->setOutput($this->render());			
  	}
        public function category() { 
                $this->setdata();

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/postcodecategory.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/product/postcodecategory.tpl';
		} else {
			$this->template = 'default/template/product/postcodecategory.tpl';
		}
		
		
	
		$this->response->setOutput($this->render());			
  	}
        public function mobilemain() { 
                $this->setdata();

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/postcodemobile.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/postcodemobile.tpl';
		} else {
			$this->template = 'default/template/common/postcodemobile.tpl';
		}
		
		
	
		$this->response->setOutput($this->render());			
  	}
      /*  private function setdata() { 
                $this->language->load('product/postcodesearch');
		
		// Post code search 
                        if(isset( $this->session->data['grouporderclient'])) {
                            $this->data['grouporder'] = 1;
                         
                        }
                        else {
                             $this->data['grouporder'] = 0;
                        }
			$postcode = '';
                        if($this->data['grouporder']==1){
                                $postcode='Group Order';
                        }
                        else {
                            if(isset($this->session->data['postcode'])){
                                $postcode=$this->session->data['postcode'];

                            }
                        }
                        if(isset($this->session->data['nostore'])){
                                $nostore=$this->session->data['nostore'];
                        }
                        else {
                            $nostore=0;
                        }
                       
			$this->data['postcode'] = $postcode;
                        $this->data['nostore'] = $nostore;
                        $this->data['name'] = $this->config->get('config_name');
                        $this->data['store_url'] = $this->config->get('config_url');
        }*/
        private function setdata() {
        $this->language->load('product/postcodesearch');

        // Post code search 
        $postalcode = '';
        if (isset($_GET['postcodeheader'])) {
            $postalcode = $this->postcodeformat($_GET['postcodeheader']);
        }
        if (isset($_GET['postcode'])) {
            $postalcode = $this->postcodeformat($_GET['postcode']);
        }
        if (isset($_GET['postcodepopup'])) {
            $postalcode = $this->postcodeformat($_GET['postcodepopup']);
        }
        if ($postalcode != '') {
            $chk_postcode = $this->postcode->getPostcodeDetails($postalcode);
            if (!empty($chk_postcode)) {
                $postcode = $postalcode;
                $this->session->data['postcode'] = $postalcode;
            }
        }
        if (isset($this->session->data['grouporderclient'])) {
            $this->data['grouporder'] = 1;
        } else {
            $this->data['grouporder'] = 0;
        }
        $postcode = '';
        if ($this->data['grouporder'] == 1) {
            $postcode = 'Group Order';
        } else {
            if (isset($this->session->data['postcode'])) {
                $postcode = $this->session->data['postcode'];
            }
        }
        if (isset($this->session->data['nostore'])) {
            $nostore = $this->session->data['nostore'];
        } else {
            $nostore = 0;
        }

        $this->data['postcode'] = $postcode;
        $this->data['nostore'] = $nostore;
        $this->data['name'] = $this->config->get('config_name');
        $this->data['store_url'] = $this->config->get('config_url');

        if ($this->config->get('config_online_orders')) {
            $this->data['order_msg'] = $this->getChild('common/onlineorders/noonlineordermessagepopup');
        } else {
            if ($this->config->get('config_holiday_online_orders')) {
                if ($this->postcode->isStoreHoliday()) {
                    $this->data['order_msg'] = $this->getChild('common/onlineorders/storeholidayonlineorderpopup');
                } 
            } 
            if ($this->config->get('config_store_time_orders') && !isset($this->data['order_msg'])) {
                if (!$this->postcode->isStoreOpened($this->config->get('config_store_id'))) {
                    $this->data['order_msg'] = $this->getChild('common/onlineorders/storetimeonlineorderpopup');
                } 
            } 
        }
    }

    private function postcodeformat($postalcode) {
        $pcode = trim($postalcode);
        $postcode = str_replace(" ", "", $pcode);
        $last = substr($postcode, -3);
        $first = substr($postcode, 0, (strlen($postcode) - 3));
        $actal_postcode = $first . ' ' . $last;
        return strtoupper($actal_postcode);
    }
    
    public function socialmediabuttons(){
         if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/socialmedia.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/socialmedia.tpl';
        } else {
            $this->template = 'default/template/account/socialmedia.tpl';
        }



        $this->response->setOutput($this->render());
    }
    
    public function facebookbutton(){
         if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/facebookbutton.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/facebookbutton.tpl';
        } else {
            $this->template = 'default/template/account/facebookbutton.tpl';
        }



        $this->response->setOutput($this->render());
    }
}
?>