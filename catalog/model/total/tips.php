<?php
class ModelTotalTips extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
	if (!$this->config->get('tips_status') && $this->config->get('tips_eposonly')) {  
		return false;
            } 
            if(!isset($this->session->data['tips'] )){
                return;
            }
            $this->load->language('total/tips');

            $total_data[] = array(
                    'code'       => 'tips',
                    'title'      => $this->language->get('text_tips'),
                    'text'       => $this->currency->format($this->session->data['tips']),
                    'value'      => $this->session->data['tips'],
                    'sort_order' => $this->config->get('total_sort_order')
            );
            $total += $this->session->data['tips'];
	}
}
?>