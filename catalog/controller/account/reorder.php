<?php 
class ControllerAccountReorder extends Controller { 
	public function index() {
		if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
	  
	  		$this->redirect($this->url->link('account/login', '', 'SSL'));
    	} 
	
	
                // for reordering 
        if(!isset($this->request->get['orderid']))      {
            $this->redirect($this->url->link('account/account', '', 'SSL'));
        }
        $this->load->model('account/order');
              
          
              $orderid =  $this->request->get['orderid'];
              $produts_items = $this->model_account_order->getOrderProducts($orderid);
                        $produts_names = array();
                        foreach($produts_items as $produts_item ) {
                            $produts_names []=  $produts_item['name'] ;
                            $option_data = array();
							
					$order_options = $this->model_account_order->getOrderOptions($orderid, $produts_item['order_product_id']);
							
					foreach ($order_options as $order_option) {
						if ($order_option['type'] == 'select' || $order_option['type'] == 'radio') {
							$option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'checkbox') {
							$option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'input' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
							$option_data[$order_option['product_option_id']] = $order_option['value'];	
						} elseif ($order_option['type'] == 'file') {
							$option_data[$order_option['product_option_id']] = $this->encryption->encrypt($order_option['value']);
						}
					}
                            $this->cart->add($produts_item['product_id'], $produts_item['quantity'], $option_data);
                        }
                      
                       
		
                
                // end reordering
              $this->redirect($this->url->link('checkout/cart', '', 'SSL'));  
		
  	
}
}
?>