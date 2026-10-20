<?php

class ControllerAccountGrouporder extends Controller {

    private $error = array();

    public function index() {
     //   echo $this->config->get('config_store_id');
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/grouporder', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }
        if (isset($this->session->data['grouporderid '])) {
            $this->redirect($this->url->link('account/grouporder/gorders', '', 'SSL'));
        }
        $this->load->model('account/grouporder');

        $grouporder_id = $this->model_account_grouporder->getPendingGrouporder($this->config->get('config_store_id'));
        if ($grouporder_id) {
            $this->session->data['grouporderid '] = $grouporder_id;
            $this->redirect($this->url->link('account/grouporder/gorders', '', 'SSL'));
        }
        $this->language->load('account/grouporder');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->getList();
    }

    public function insert() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/grouporder', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

       
        $this->load->model('account/grouporder');
        
        if (isset($this->session->data['grouporderid '])) {
            $this->redirect($this->url->link('account/grouporder/gorders', '', 'SSL'));
        }


        $grouporder_id = $this->model_account_grouporder->getPendingGrouporder($this->config->get('config_store_id'));
        if ($grouporder_id) {
            $this->session->data['grouporderid '] = $grouporder_id;
            $this->redirect($this->url->link('account/grouporder/gorders', '', 'SSL'));
        }
        
         $this->language->load('account/grouporder');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $membersids = $this->model_account_grouporder->addMembers($this->request->post,$this->config->get('config_store_id'));
            if (isset($this->request->post['grouporder']) && !empty($membersids)) {
                $grouporderid = $this->model_account_grouporder->setGrouporder($membersids);
                $this->session->data['grouporderid '] = $grouporderid;

                $this->redirect($this->url->link('account/grouporder/gorders', '', 'SSL'));
            } else {
                if (isset($this->request->post['grouporderdelete'])) {
                    $this->session->data['success'] = $this->language->get('text_delete');
                } else {
                    $this->session->data['success'] = $this->language->get('text_update');
                }
                $this->redirect($this->url->link('account/grouporder', '', 'SSL'));
            }
        }

        $this->getList();
    }

    public function delete() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/grouporder', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }
        if (isset($this->request->get['grouporderid'])) {
            $grouporderid = $this->request->get['grouporderid'];
            $this->load->model('account/grouporder');
            $this->model_account_grouporder->deleteGrouporder($grouporderid);
            unset($this->session->data['grouporderid ']);
        }
        $this->language->load('account/grouporder');

        $this->document->setTitle($this->language->get('heading_title'));



        $this->session->data['success'] = $this->language->get('text_grouporder_delete');

        $this->redirect($this->url->link('account/grouporder', '', 'SSL'));
    }

    private function getList() {
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

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/grouporder', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_mebers_list'] = $this->language->get('text_mebers_list');
        $this->data['entry_member_name'] = $this->language->get('entry_member_name');
        $this->data['entry_member_email'] = $this->language->get('entry_member_email');
        $this->data['entry_member_select'] = $this->language->get('entry_member_select');

        $this->data['button_moremembers'] = $this->language->get('button_moremembers');
        $this->data['button_edit'] = $this->language->get('button_edit');
        $this->data['button_delete'] = $this->language->get('button_delete');
        $this->data['button_back'] = $this->language->get('button_back');
        $this->data['button_grouporder'] = $this->language->get('button_grouporder');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->data['members'] = array();

        $results = $this->model_account_grouporder->getMembers($this->config->get('config_store_id'));
        if ($results) {
            $this->data['members'] = $results;
            $emptyrows = 10 - ( count($results) % 10);
            if (isset($this->request->post['groupordermore'])) {
                $emptyrows += 10;
            }
        } else {
            $emptyrows = 10;
        }


        for ($i = 0; $i < $emptyrows; $i++) {

            $this->data['members'][] = array('member_id' => '', 'customer_id' => '', 'store_id' => '', 'member_name' => '', 'member_email' => '');
        }


        $this->data['back'] = $this->url->link('account/account', '', 'SSL');

        $this->data['action'] = $this->url->link('account/grouporder/insert', '', 'SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/groupordermember_list.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/groupordermember_list.tpl';
        } else {
            $this->template = 'default/template/account/groupordermember_list.tpl';
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

    private function validateForm() {
        /* foreach($this->request->post['member_name'] as $key=>$member_name) {
             
         } */
        return true;
        
    }

   
    public function gorders() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/grouporder/gorders', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }
        if (!isset($this->session->data['grouporderid '])) {
            $this->redirect($this->url->link('account/grouporder/gorders', '', 'SSL'));
        }
        $this->language->load('account/grouporder');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('account/grouporder');
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

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/grouporder/gorders', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_mebers_list'] = $this->language->get('text_mebers_list');
        $this->data['entry_member_name'] = $this->language->get('entry_member_name');
        $this->data['entry_member_email'] = $this->language->get('entry_member_email');
        $this->data['entry_member_status'] = $this->language->get('entry_member_status');


        $this->data['button_refresh'] = $this->language->get('button_refresh');
        $this->data['button_delete_grouporder'] = $this->language->get('button_delete_grouporder');
        $this->data['button_continue_grouporder'] = $this->language->get('button_continue_grouporder');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }



        $this->data['members'] = $this->model_account_grouporder->getGrouporder($this->session->data['grouporderid ']);
	if(!empty($this->data['members'] ))
        foreach ($this->data['members'] as $key => $val) {
            switch ($val['grouporder_stage']) {
                case 'notvisit' :
                    $this->data['members'][$key]['member_nowstage'] = $this->language->get('entry_member_notvisited');
                    break;
                case 'visit':
                    $this->data['members'][$key]['member_nowstage'] = $this->language->get('entry_member_vististed');
                    break;
                case 'order':
                    $this->data['members'][$key]['member_nowstage'] = $this->language->get('entry_member_odered');
                    break;
                case 'closed':
                    $this->data['members'][$key]['member_nowstage'] = $this->language->get('entry_member_closed');
                    break;
            }
        }

        $this->data['delete'] = $this->url->link('account/grouporder/delete', 'grouporderid= ' . $this->session->data['grouporderid '], 'SSL');
        $this->data['refresh'] = $this->url->link('account/grouporder/gorders', '', 'SSL');
        $this->data['continue'] = $this->url->link('common/home', '', 'SSL');



        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/gorders.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/gorders.tpl';
        } else {
            $this->template = 'default/template/account/gorders.tpl';
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

    public function clinetgorders() {
        $goclientid = $this->request->get['goclientid'];
        $this->language->load('account/grouporder');
        $this->load->model('account/grouporder');
        if ($this->model_account_grouporder->isGrouporderClient($goclientid)) {
            $this->session->data['grouporderclient'] = $goclientid;
            $this->session->data['success'] = $this->language->get('text_grouporder_clent_welcome');
        } else {
            $this->session->data['success'] = $this->language->get('text_grouporder_clent_closed');
        }
       // $this->redirect($this->url->link('common/home', '', 'SSL'));
        $this->redirect($this->config->get('config_store_url') . '/menu');
    }

    public function clinetchekout() {
        $this->load->model('account/grouporder');

        $grouporderclient = $this->session->data['grouporderclient'];
        $this->model_account_grouporder->addProductsGrouporderClient($grouporderclient);
        $this->cart->clear();

        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['guest']);
        unset($this->session->data['comment']);
        unset($this->session->data['order_id']);
        unset($this->session->data['coupon']);
        unset($this->session->data['reward']);
        unset($this->session->data['voucher']);
        unset($this->session->data['vouchers']);
        $this->redirect($this->url->link('account/grouporder/clinetsuccess', '', 'SSL'));
    }

    public function clinetSuccess() {


        unset($this->session->data['grouporderclient']);
        $this->language->load('account/grouporder');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['breadcrumbs'] = array(); 

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => false
      	); 
		
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	);
				
			
					
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/success'),
        	'text'      => $this->language->get('text_success'),
        	'separator' => $this->language->get('text_separator')
      	);
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

		
    	$this->data['text_message'] = $this->language->get('text_message_contgrats') . PHP_EOL
                                      . sprintf($this->language->get('text_message_productadded'),$this->session->data['groupordermember']['firstname'] . ' ' . $this->session->data['groupordermember']['lastname'] ) . PHP_EOL   
                                      . $this->language->get('text_message');
		
		
    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = $this->url->link('common/home');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/success.tpl';
		} else {
			$this->template = 'default/template/common/success.tpl';
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

}

?>
