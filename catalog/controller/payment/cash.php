<?php

class ControllerPaymentCash extends Controller {

    protected function index() {
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['continue'] = $this->url->link('epos/success');
        //$this->data['continue'] = $this->url->link('checkout/success');
        $json = array();
        if (isset($this->session->data['order_id'])) {
            $this->load->model('checkout/order');

            $json = array('paymentmethod' => 'cash', 'order' => $this->model_checkout_order->getOrder($this->session->data['order_id']));
            /*
              // TEMP START

              $total_data = array();
              $total = 0;
              $taxes = $this->cart->getTaxes();

              $this->load->model('setting/extension');

              $sort_order = array();

              $results = $this->model_setting_extension->getExtensions('total');

              foreach ($results as $key => $value) {
              $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
              }

              array_multisort($sort_order, SORT_ASC, $results);

              foreach ($results as $result) {
              if ($this->config->get($result['code'] . '_status')) {
              $this->load->model('total/' . $result['code']);

              $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
              }
              }

              $sort_order = array();

              foreach ($total_data as $key => $value) {
              $sort_order[$key] = $value['sort_order'];
              }

              array_multisort($sort_order, SORT_ASC, $total_data);


              $product_data = array();

              // Reward points
              $this->load->model('setting/setting');
              $total_rewads = 0;
              $rewards = $this->model_setting_setting->getSetting('rewardpoints');
              if (is_array($rewards) && isset($rewards[0]['points']) && $rewards[0]['status'] == 1) {
              $total_rewads = floor($total) * floatval($rewards[0]['points']);
              }


              foreach ($this->cart->getProducts() as $product) {
              $option_data = array();

              foreach ($product['option'] as $option) {
              if ($option['type'] != 'file') {
              $value = $option['option_value'];
              } else {
              $value = $this->encryption->decrypt($option['option_value']);
              }

              $option_data[] = array(
              'product_option_id' => $option['product_option_id'],
              'product_option_value_id' => $option['product_option_value_id'],
              'option_id' => $option['option_id'],
              'option_value_id' => $option['option_value_id'],
              'name' => $option['name'],
              'value' => $value,
              'type' => $option['type']
              );
              }

              $productbundle=array();
              foreach ($product['bundled_data'] as $bundled_product) {
              $bundle_options = array();
              foreach ($bundled_product['option'] as $option) {
              if ($option['type'] != 'file') {
              $value = $option['option_value'];
              } else {
              $value = $this->encryption->decrypt($option['option_value']);
              }

              $bundle_options[] = array(
              'product_option_id' => $option['product_option_id'],
              'product_option_value_id' => $option['product_option_value_id'],
              'option_id' => $option['option_id'],
              'option_value_id' => $option['option_value_id'],
              'name' => $option['name'],
              'value' => $value,
              'type' => $option['type']
              );
              }


              $productbundle[] = array(
              'main_product_id'=>$bundled_product['main_product_id'],
              'product_id' => $bundled_product['product_id'],
              'bundled_id' => $bundled_product['bundled_id'],
              'name' => $bundled_product['name'],
              'model' => $bundled_product['model'],
              'option' => $bundle_options,
              'quantity' => $bundled_product['quantity'],
              'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),

              );
              }
              $product_data[] = array(
              'product_id' => $product['product_id'],
              'name' => $product['name'],
              'model' => $product['model'],
              'option' => $option_data,
              'download' => $product['download'],
              'quantity' => $product['quantity'],
              'subtract' => $product['subtract'],
              'price' => $product['price'],
              'total' => $product['total'],
              'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
              // 'reward'     => $product['reward']
              // Reward points
              'reward' => $total_rewads,
              'bundle'=> $productbundle
              );
              $total_rewads = 0;
              }

              // Gift Voucher
              $voucher_data = array();

              if (!empty($this->session->data['vouchers'])) {
              foreach ($this->session->data['vouchers'] as $voucher) {
              $voucher_data[] = array(
              'description' => $voucher['description'],
              'code' => substr(md5(mt_rand()), 0, 10),
              'to_name' => empty($voucher['to_name']) ? $data['firstname'] . ' ' . $data['lastname'] : $voucher['to_name'],
              'to_email' => empty($voucher['to_email']) ? $data['email'] : $voucher['to_email'],
              'from_name' => empty($voucher['from_name']) ? $this->config->get('config_name') : $voucher['from_name'],
              'from_email' => empty($voucher['from_email']) ? $this->config->get('config_email') : $voucher['from_email'],
              'voucher_theme_id' => $voucher['voucher_theme_id'],
              'message' => $voucher['message'],
              'amount' => $voucher['amount']
              );
              }
              }

              $json['order']['products'] = $product_data;
              $json['order']['vouchers'] = $voucher_data;
              $json['order']['totals'] = $total_data;

              // TEMP END
             */
        } else {
            $json = array('paymentmethod' => 'cash', 'order' => null);
        }
        $this->data['cashcalculator'] = $this->getChild('payment/cash/cashdetails'); //;  $this->cashdetails();
        $this->data['paymentprocess'] = json_encode($json);


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cash.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/cash.tpl';
        } else {
            $this->template = 'default/template/payment/cash.tpl';
        }

        $this->render();
    }

    public function confirm() {
        $this->load->model('checkout/order');
        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cash_order_status_id'));
    }

    public function cashdetails() {

        $total_data = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();

        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('total/' . $result['code']);

                $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
            }
        }

        $sort_order = array();

        foreach ($total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $total_data);
        $cash_data = array(
            'order_id' => $this->session->data['order_id'],
            'amount' => $total
        );
        $this->data['total'] = $total;
        if ($this->config->get('cash_drawer_status')) {
            $this->posclient->updateDrawerCash($cash_data);
            $this->data['drawer_status'] = 'yes';
        } else {
            $this->data['drawer_status'] = 'no';
        }
          

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cashdetails.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/cashdetails.tpl';
        } else {
            $this->template = 'default/template/payment/cashdetails.tpl';
        }

        $this->render();
    }
    
    protected function epos() {
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['continue'] = $this->url->link('epos/success');
        $this->data['order_total']=(isset( $this->session->data['order_total_amt'])? $this->session->data['order_total_amt']:'0.00');
        //$this->data['continue'] = $this->url->link('checkout/success');
        $json = array();
        if (isset($this->session->data['order_id'])) {
            $this->load->model('checkout/order');

            $json = array('paymentmethod' => 'cash', 'order' => $this->model_checkout_order->getOrder($this->session->data['order_id']));
        } else {
            $json = array('paymentmethod' => 'cash', 'order' => null);
        }
        $this->data['paymentcode'] = $this->session->data['payment_method']['code'];
        
        $this->data['cashcalculator'] = $this->getChild('payment/cash/cashdetails'); //;  $this->cashdetails();
        $this->data['paymentprocess'] = json_encode($json);

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cash_epos.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/cash_epos.tpl';
        } else {
            $this->template = 'default/template/payment/cash_epos.tpl';
        }

        $this->render();
    }

}

?>