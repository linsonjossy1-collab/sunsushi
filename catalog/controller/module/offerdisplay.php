<?php

class ControllerModuleOfferdisplay extends Controller {

    public function index($dispaly_settings) {
        static $module = 0;
        $offer_data = array();
        $offer_products = array();
        $offer_product_total = 0;
        $taxes = 0;
        // print_r($dispaly_settings);
        $offer_applied = FALSE;
        $offer_available = FALSE;
        $this->load->model('checkout/offers');
        $this->load->model('total/offers');
        $available_offers = $this->model_total_offers->getTotal($offer_products, $offer_product_total, $taxes);
        if (!empty($available_offers)) {
            foreach ($available_offers as $single_offer) {
                if ($single_offer['offer_id'] == $dispaly_settings['offer'] && !empty($single_offer['value'])) {
                    $offer_applied = TRUE;
                    break;
                }
            }
            foreach ($available_offers as $single_offer) {
                if (!empty($single_offer['value']) || $single_offer['value'] == '' && $single_offer['offer_id'] == $dispaly_settings['offer']) {
                    $offer_available = TRUE;
                    break;
                }
            }
        }
        if ($dispaly_settings['offer_message'] == 1) {//IF MESSAGE SHOW
            if ($dispaly_settings['offer_time_dispaly'] == 0) {//Show Message Every Time AND Offer/Coupon  Not Applied
                if (!$offer_applied) {
                    $offer_data = $this->model_checkout_offers->getOffer($dispaly_settings['offer']);
                    if($offer_data['total_amount'] >=$this->cart->getSubTotal()){
                    $message = str_replace('{AMOUNT_NEEDED}', ($offer_data['total_amount'] - $this->cart->getSubTotal()), $dispaly_settings['message_content']);
                    $message = str_replace('{OFFER_NAME}', $offer_data['name'], $message);
                    $message = str_replace('{COUPON_CODE}', $offer_data['coupon_code'], $message);
                    $offer_data['notifn'] = html_entity_decode($message, ENT_QUOTES, 'UTF-8');
                    }else{
                        $offer_data['notifn']='';
                    }
                }
            } else {//Show Message Only Offer is active and not used
                if (!$offer_applied && $offer_available) {
                    $offer_data = $this->model_checkout_offers->getOffer($dispaly_settings['offer']);
                    $message = str_replace('{AMOUNT_NEEDED}', ($offer_data['total_amount'] - $this->cart->getSubTotal()), $dispaly_settings['message_content']);
                    $message = str_replace('{OFFER_NAME}', $offer_data['name'], $message);
                    $message = str_replace('{COUPON_CODE}', $offer_data['coupon_code'], $message);
                    $offer_data['notifn'] = html_entity_decode($message, ENT_QUOTES, 'UTF-8');
                    
                }
            }
        }
        // $this->data['offers']=$this->model_checkout_offers->getOffer($dispaly_settings['offer']);
        $this->data['offer_data'] = $offer_data;
        //print_r($offer_data);
        $this->data['module'] = $module++;
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/offerdisplay.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/offerdisplay.tpl';
        } else {
            $this->template = 'default/template/module/offerdisplay.tpl';
        }

        $this->render();
    }

}

?>