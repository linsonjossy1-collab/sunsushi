<?php

class ModelTotalPaymentCommision extends Model {

    public function getTotal(&$total_data, &$total, &$taxes) {

        if (isset($this->session->data['payment_method'])) { 
            if ($this->config->get($this->session->data['payment_method']['code'] .'_commision')) {
                    $this->load->language('payment/'.$this->session->data['payment_method']['code']);
                    $total_data[] = array(
                        'code' => 'payment_commision',
                        'title' => $this->language->get('text_payment_commision'),
                        'text' => $this->currency->format($this->config->get($this->session->data['payment_method']['code'] .'_commision')),
                        'value' => $this->config->get($this->session->data['payment_method']['code'] .'_commision'),
                        'sort_order' => $this->config->get('payment_commision_sort_order')
                    );
                    $total += $this->config->get($this->session->data['payment_method']['code'] .'_commision');
            }
           
        }
        
    }

}

?>