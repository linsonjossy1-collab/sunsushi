<?php

class ControllerCheckoutCartetm extends Controller {
    private $error = array();
    public function index() {
        
        $this->data['cartshipping'] = $this->getChild('checkout/shipping_postcodes/cartshipping');
        $this->data['menushipping'] = $this->getChild('checkout/shipping_postcodes/menushipping');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/cartetm.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/cartetm.tpl';
        } else {
            $this->template = 'default/template/checkout/cartetm.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header',
            'module/cart'
        );

        $this->response->setOutput($this->render());
   }

}

?>