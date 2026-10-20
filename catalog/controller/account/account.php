<?php

class ControllerAccountAccount extends Controller {

    public function index() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        // Reward point to add

        if (isset($this->session->data['rewardpoint'])) {
            $this->load->model('account/reward');
            $reward = $this->session->data['rewardpoint'];
            $this->model_account_reward->addReward($reward['name'], $reward['points']);
            unset($this->session->data['rewardpoint']);
        }
        // Reward point end


        $this->language->load('account/account');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_my_account'] = $this->language->get('text_my_account');
        $this->data['text_my_orders'] = $this->language->get('text_my_orders');
        //$this->data['text_my_newsletter'] = $this->language->get('text_my_newsletter');
		$this->data['text_my_subscription'] = $this->language->get('text_my_subscription');
        $this->data['text_edit'] = $this->language->get('text_edit');
        $this->data['text_password'] = $this->language->get('text_password');
        $this->data['text_address'] = $this->language->get('text_address');
        $this->data['text_wishlist'] = $this->language->get('text_wishlist');
        $this->data['text_order'] = $this->language->get('text_order');
        $this->data['text_download'] = $this->language->get('text_download');
        $this->data['text_reward'] = $this->language->get('text_reward');
        $this->data['text_return'] = $this->language->get('text_return');
        $this->data['text_transaction'] = $this->language->get('text_transaction');
        //$this->data['text_newsletter'] = $this->language->get('text_newsletter');
		$this->data['text_subscribe'] = $this->language->get('text_subscribe');

        $this->data['edit'] = $this->url->link('account/edit', '', 'SSL');
        $this->data['password'] = $this->url->link('account/password', '', 'SSL');
        $this->data['address'] = $this->url->link('account/address', '', 'SSL');
        $this->data['wishlist'] = $this->url->link('account/wishlist');
        $this->data['order'] = $this->url->link('account/order', '', 'SSL');
        $this->data['download'] = $this->url->link('account/download', '', 'SSL');
        $this->data['return'] = $this->url->link('account/return', '', 'SSL');
        $this->data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
        $this->data['newsletter'] = $this->url->link('subscribe/subscribe/subscription', '', 'SSL');

        if(isset($this->session->data['remember']) && $this->customer->isLogged()){
            $this->data['customer_data']=  base64_encode($this->customer->getId());//Hash key && JSON ENCODED DATA 
        }
        if ($this->config->get('reward_status')) {
            $this->data['reward'] = $this->url->link('account/reward', '', 'SSL');
        } else {
            $this->data['reward'] = '';
        }

        if($this->customer->isLogged())
        {
            $saved_cards= $this->customer->getPaymentCards();
            if(isset($saved_cards['stripe']) && $saved_cards['stripe'])
            {
                $this->data['text_saved_cards'] = $this->language->get('text_saved_cards');
                $this->data['savedcards'] = $this->url->link('account/savedcards', '', 'SSL');
            }            
        }
        // recent orders
        $this->load->model('account/order');
        $this->load->model('account/backorder');
        
        $this->language->load('account/order');

        $this->data['text_order_id'] = $this->language->get('text_order_id');
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_order_id'] = $this->language->get('text_order_id');
        $this->data['text_status'] = $this->language->get('text_status');
        $this->data['text_date_added'] = $this->language->get('text_date_added');
        $this->data['text_customer'] = $this->language->get('text_customer');
        $this->data['text_products'] = $this->language->get('text_products');
        $this->data['text_total'] = $this->language->get('text_total');
        $this->data['text_empty'] = $this->language->get('text_empty');

        $this->data['button_view'] = $this->language->get('button_view');
        $this->data['button_reorder'] = $this->language->get('button_reorder');
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['reward_points'] = (int) $this->customer->getRewardPoints();
        $this->data['orders'] = array();


        $results = $this->model_account_order->getOrders(0, 5);

        foreach ($results as $result) {
            $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
            $voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);
            $products = $this->model_account_order->getOrderProducts($result['order_id']);
            $product_names = array();
            foreach ($products as $product) {
                $product_names[] = $product['name'];
            }

            $this->data['orders'][] = array(
                'order_id' => $result['order_id'],
                'productsname' => implode(',', array_unique($product_names)),
                'name' => $result['firstname'] . ' ' . $result['lastname'],
                'status' => $result['status'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'products' => ($product_total + $voucher_total),
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'href' => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL'),
                'reorder' => $this->url->link('account/order', 'order_id=' . $result['order_id'], 'SSL')
            );
        }
        // Back order
        $results = $this->model_account_backorder->getOrders(0, 5);

        foreach ($results as $result) {
            $product_total = $this->model_account_backorder->getTotalOrderProductsByOrderId($result['order_id']);
            $voucher_total = $this->model_account_backorder->getTotalOrderVouchersByOrderId($result['order_id']);
            $products = $this->model_account_backorder->getOrderProducts($result['order_id']);
            $product_names = array();
            foreach ($products as $product) {
                $product_names[] = $product['name'];
            }

            $this->data['orders'][] = array(
                'order_id' => $result['order_id'],
                'productsname' => implode(',', array_unique($product_names)),
                'name' => $result['firstname'] . ' ' . $result['lastname'],
                'status' => $result['status'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'products' => ($product_total + $voucher_total),
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'href' => $this->url->link('account/order/info', 'backorder=y&order_id=' . $result['order_id'], 'SSL'),
                'reorder' => $this->url->link('account/order', 'backorder=y&order_id=' . $result['order_id'], 'SSL')
            );
        }
        
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
        
        $this->data['text_delete'] = $this->language->get('text_delete');
        $this->data['delete'] = $this->url->link('account/deleteaccount', '', 'SSL');
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/account.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/account.tpl';
        } else {
            $this->template = 'default/template/account/account.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
    /*
     * Function for Login customer with local storage
     */
    public function custredatsav(){
        $json= array();
        if(isset($this->request->post['a1_s1_gkl_'])){
            $this->customer->loginID(base64_decode($this->request->post['a1_s1_gkl_']));
            $json['success']='ok';
        }
        $this->response->setOutput(json_encode($json));	
    }
    /*
    public function csv_customer() {
        $csv_customers =  $this->db->query("SELECT * FROM `" . DB_PREFIX . "customers_uno_n`  ");
        $this->load->model('account/customer'); 

        foreach ($csv_customers->rows as $csv_customer) {
            if(empty(trim($csv_customer['Name'])) && empty(trim($csv_customer['Surname']))) {
                $csv_customer['FirstName'] = 'No Name';
            }
            $customerdata =[];
            $customerdata['firstname']= trim($csv_customer['FirstName']);
            $customerdata['lastname']=  trim($csv_customer['Surname']);
            $customerdata['telephone']= $this->veifytelephone($csv_customer['Telephone1']);
            $customerdata['phone_number1']=$this->veifytelephone($csv_customer['Telephone2']);
            $customerdata['phone_number2']=$this->veifytelephone($csv_customer['Telephone3']);
            $customerdata['address_1']= trim($csv_customer['Road']);
            $customerdata['address_2']=trim($csv_customer['Prem']);
            $customerdata['city']=$csv_customer['city'];
            $customerdata['postcode']= $this->postcode->postcodeformat($csv_customer['PostCode']); 
            $customerdata['email']= strtolower(trim($csv_customer['Email'])); 
           $customer_id = $this->model_account_customer->addCustomer($customerdata);
//           print_r($customerdata);
         echo $customer_id . ' - ';
           
//                die();
        }
        echo 'Done';
    }
    private function veifytelephone($tel) {
       // echo $tel . ' - ' . strlen(($tel)) . "\n";
        $tel = trim($tel);
        if(strlen(strval($tel)) == 10) {
            return '0'. $tel;
        }
        elseif(strlen(strval($tel)) == 7 ) {

            return '0191'. $tel;
        }
        elseif(strlen(strval($tel)) < 7 ) {

            return '';
        }
        return $tel;
    }
   */

}

?>