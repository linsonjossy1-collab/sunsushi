<?php

class ControllerAccountReward extends Controller {

    public function index() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/reward', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->language->load('account/reward');

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

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_reward'),
            'href' => $this->url->link('account/reward', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $this->load->model('account/reward');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['column_date_added'] = $this->language->get('column_date_added');
        $this->data['column_description'] = $this->language->get('column_description');
        $this->data['column_points'] = $this->language->get('column_points');

        $this->data['text_total'] = $this->language->get('text_total');
        $this->data['text_empty'] = $this->language->get('text_empty');

        $this->data['button_continue'] = $this->language->get('button_continue');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->data['rewards'] = array();

        $data = array(
            'sort' => 'date_added',
            'order' => 'DESC',
            'start' => ($page - 1) * 10,
            'limit' => 10
        );

        $reward_total = $this->model_account_reward->getTotalRewards($data);

        $results = $this->model_account_reward->getRewards($data);

        foreach ($results as $result) {
            $this->data['rewards'][] = array(
                'order_id' => $result['order_id'],
                'points' => $result['points'],
                'description' => $result['description'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'href' => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL')
            );
        }

        $pagination = new Pagination();
        $pagination->total = $reward_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('account/reward', 'page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();

        $this->data['total'] = (int) $this->customer->getRewardPoints();

        $this->data['continue'] = $this->url->link('account/account', '', 'SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/reward.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/reward.tpl';
        } else {
            $this->template = 'default/template/account/reward.tpl';
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

    public function smreward() {
        $this->load->model('setting/setting');
        $this->load->model('account/reward');
        $this->language->load('account/smreward');
        $msmedia = $this->request->get['smedia'];
          $rewards = $this->model_setting_setting->getSetting('rewardpoints');
         $chk_socialmedia_offers = $this->model_account_reward->getSocialmediaOffresbyname($msmedia);
//print_r($chk_socialmedia_offers);

        // echo   $customer['customer_id'];die();
        // echo$id=$this->customer->getId();die();
        if (is_array($rewards)) {
            foreach ($rewards as $reward) {
                // facebook
                if ($msmedia == 'facebook' and $reward['code'] == 'FACEBOOK' and $reward['points'] != 0) {

                    if (!$this->customer->isLogged()) {
//echo "notlogged";//die();
                        // switch ($reward_type) {
                       //print_r($chk_socialmedia_offers);
                        if ($chk_socialmedia_offers['status'] == 1) {
                           // echo  'coupon';
                            $this->session->data['addcoupontype'] = 'facebook';
                            $this->data['account_coupon_messege'] = $this->language->get('account_coupon_login_messege');
                            //break; 
                        }
                        if ($chk_socialmedia_offers['status']== 0 || $chk_socialmedia_offers['reward_add'] == 1) {
                             //echo  'points also';
                            $this->session->data['rewardpoint'] = $reward;
                            $this->data['account_messege'] = $this->language->get('text_pls_login');
                            // break;
                        }






                        break;
                    } 
                    else {
                        //  switch ($reward_type) {
                        //echo "logged";
                        if ($chk_socialmedia_offers['status'] == 1) {
                            // case 'coupon':
                            $customer_id = $this->customer->isLogged();
                            $couponcheck = $this->model_account_reward->getCustomercoupon($msmedia, $customer_id);
                            //print_r($couponcheck);die();
                            if (empty($couponcheck)) {
                                $couponcode = $this->model_account_reward->addCoupon($msmedia, $customer_id);
                                $this->data['account_coupon_messege'] = $this->language->get('text_facebook_coupon_code') . $couponcode;
                            } else {
                                //$this->data['account_coupon_messege'] = '';
                            }//}
                        }// break;
                        // case 'point':
                        if ($chk_socialmedia_offers['status'] == 0 or $chk_socialmedia_offers['reward_add'] == 1) {
                            $this->model_account_reward->addReward($reward['name'], $reward['points']);
                            $this->data['account_messege'] = $this->language->get('text_sucess');

                            //    break;
                            //}
                        }


                        break;
                    }
                }
                //die();
                // tweet

                if ($msmedia == 'tweet' and $reward['code'] == 'TWITTER' and $reward['points'] != 0) {
                    if (!$this->customer->isLogged()) {
                        //switch ($reward_type) {
                        // case 'coupon':
                        if ($chk_socialmedia_offers['status'] == 1) {
                            $this->session->data['addcoupontype'] = 'tweet';
                            //$this->data['account_coupon_messege'] = $this->language->get('text_pls_login');
                             $this->data['account_coupon_messege'] = $this->language->get('account_coupon_login_messege');
                            // break;
                        }//case 'point':
                        if ($chk_socialmedia_offers['status'] == 0 or $chk_socialmedia_offers['reward_add'] == 1) {
                            $this->session->data['rewardpoint'] = $reward;
                            $this->data['account_messege'] = $this->language->get('text_pls_login');
                            // break;
                        }//}
                        break;
                    } else {
                        // switch ($reward_type) {
                        //  case 'coupon':
                        if ($chk_socialmedia_offers['status'] == 1) {
                            $customer_id = $this->customer->isLogged();
                            $couponcheck = $this->model_account_reward->getCustomercoupon($msmedia, $customer_id);
                            // print_r($couponcheck);
                            if (empty($couponcheck)) {
                                $couponcode = $this->model_account_reward->addCoupon($msmedia, $customer_id);
                                 $this->data['account_coupon_messege'] = $this->language->get('text_twitter_coupon_code') . $couponcode;
                            } else {
                                $this->data['account_coupon_messege'] = '';
                            }//}
                            // break;
                        }
                        if ($chk_socialmedia_offers['status'] == 0 or $chk_socialmedia_offers['reward_add'] == 1) {
                            // case 'point':
                            $this->model_account_reward->addReward($reward['name'], $reward['points']);
                            $this->data['account_messege'] = $this->language->get('text_sucess');
                        }
                        // break;
                        // }
                        break;
                    }
                }
            }
        }
        $this->data['heading_title'] = $this->language->get('heading_title');
//echo "hii";
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/smreward.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/smreward.tpl';
        } else {
            $this->template = 'default/template/account/smreward.tpl';
        }
        $this->children = array();
        $this->response->setOutput($this->render());
        // exit();
    }

}

?>