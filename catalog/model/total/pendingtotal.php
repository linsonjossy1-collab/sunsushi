<?php

class ModelTotalPendingtotal extends Model { 

    public function getTotal(&$total_data, &$total, &$taxes) {
       if(isset($this->session->data['edited_order_payed'])){
           if($this->session->data['edited_order_payed']>0){
             
           }else{//change total name into Return Amount
           
           }
          $payed_amount = (float) $this->session->data['edited_order_payed'];
           $total_data[] = array(
                'code' => 'pendingtotal',
                'title' => 'Payed',
                'text' => $this->currency->format(-$payed_amount),
                'value' => -$payed_amount,
                'sort_order' => $this->config->get('pendingtotal_sort_order')
            );
           $total -= $payed_amount;
       }
    }

}

?>