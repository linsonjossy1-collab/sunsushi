<?php

class ModelTotalRoundoff extends Model {

    public function getTotal(&$total_data, &$total, &$taxes) {
       $disc_amt = 0;
        if ($this->config->get('roundoff_status') == 0 || !$this->cart->hasProducts()) {
                return FALSE;
        }
        if (($this->config->get('roundoff_value')!='') && ($this->config->get('roundoff_cart_amt') == '' or $this->config->get('roundoff_cart_amt') <= $this->cart->getSubTotal())) {
                $disc_amt += (round(($total - floor($total)) * 100) % ($this->config->get('roundoff_value') * 100)) / 100;
        }        
        if (isset($this->session->data['split_roundoff'])) {
            $disc_amt += $this->session->data['split_roundoff'];
        }
        if ($disc_amt == 0) {
                return FALSE;
        }
        $total_data[] = array(
                    'code' => 'roundoff',
                    'title' => 'Round Off',
                    'text' => $this->currency->format(-$disc_amt),
                    'value' => -$disc_amt,
                    'sort_order' => $this->config->get('roundoff_sort_order')
        );
        $total -= $disc_amt;	
    }

}

?>