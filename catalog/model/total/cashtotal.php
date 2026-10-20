<?php

class ModelTotalCashtotal extends Model {

    public function getTotal(&$total_data, &$total, &$taxes) {
        if (isset($this->session->data['sub_cash_total'])) {
            //echo $this->session->data['adhoc_discount_name'];
            $total_data[] = array(
                'code' => 'cashtotal',
                'title' => $this->session->data['adhoc_discount_name'],
                'text' => $this->currency->format($disc_amt),
                'value' => $disc_amt,
                'sort_order' => $this->config->get('adhocdiscounts_sort_order')
            );
        }
    }

}

?>