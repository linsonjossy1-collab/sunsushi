<?php

class ModelTotalCreditcardCheckoutTotal extends Model {

    public function getTotal(&$total_data, &$total, &$taxes) {

        if (isset($this->session->data['payment_method'])) { 
            if( $this->session->data['payment_method']['code'] == 'google_checkout') {
         
                if ($this->config->get('google_checkout_commision')) {
                    $this->load->language('total/creditcard_checkout_total');
                    $total_data[] = array(
                        'code' => 'google_checkout',
                        'title' => $this->language->get('text_creditcard_checkout'),
                        'text' => $this->currency->format($this->config->get('google_checkout_commision')),
                        'value' => $this->config->get('google_checkout_commision'),
                        'sort_order' => $this->config->get('creditcard_checkout_total_sort_order')
                    );
                    $total += $this->config->get('google_checkout_commision');
                }
            }
            elseif( $this->session->data['payment_method']['code'] == 'nochex') {
         
                if ($this->config->get('nochex_checkout_commision')) {
                    $this->load->language('total/creditcard_checkout_total');
                    $total_data[] = array(
                        'code' => 'nochex_checkout',
                        'title' => $this->language->get('text_creditcard_checkout'),
                        'text' => $this->currency->format($this->config->get('nochex_checkout_commision')),
                        'value' => $this->config->get('nochex_checkout_commision'),
                        'sort_order' => $this->config->get('creditcard_checkout_total_sort_order')
                    );
                    $total += $this->config->get('nochex_checkout_commision');
                }
            }
            elseif( $this->session->data['payment_method']['code'] == 'pp_express') {
         
                if ($this->config->get('pp_express_checkout_commision')) {
                    $this->load->language('total/creditcard_checkout_total');
                    $total_data[] = array(
                        'code' => 'pp_express_checkout',
                        'title' => $this->language->get('text_creditcard_checkout'),
                        'text' => $this->currency->format($this->config->get('pp_express_checkout_commision')),
                        'value' => $this->config->get('pp_express_checkout_commision'),
                        'sort_order' => $this->config->get('creditcard_checkout_total_sort_order')
                    );
                    $total += $this->config->get('pp_express_checkout_commision');
                }
            }
            elseif( $this->session->data['payment_method']['code'] == 'pp_standard') {
         
                if ($this->config->get('pp_standard_checkout_commision')) {
                    $this->load->language('total/creditcard_checkout_total');
                    $total_data[] = array(
                        'code' => 'pp_standard_checkout',
                        'title' => $this->language->get('text_creditcard_checkout'),
                        'text' => $this->currency->format($this->config->get('pp_standard_checkout_commision')),
                        'value' => $this->config->get('pp_standard_checkout_commision'),
                        'sort_order' => $this->config->get('creditcard_checkout_total_sort_order')
                    );
                    $total += $this->config->get('pp_standard_checkout_commision');
                }
            }
        }
        
    }

}

?>