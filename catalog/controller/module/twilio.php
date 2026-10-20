<?php

class ControllerModuleTwilio extends Controller {

    public function index() {

        if (isset($this->request->get['orderid'])) {
            $this->load->model('checkout/order');
            $order_id = $this->request->get['orderid'];
            $order = $this->model_checkout_order->getOrder($order_id);

            if ($order) {

                $order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");

                foreach ($order_product_query->rows as $order_product) {

                    $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $order_product['order_product_id'] . "'");

                    foreach ($order_option_query->rows as $option) {
                        $order_product['option'][] = $option;
                    }
                    $order['order_products'][] = $order_product;
                }


                //  $this->data['order'] = $order;
                $this->data['home_url'] = $this->config->get('config_url');
//                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/twilio.tpl')) {
//                    $this->template = $this->config->get('config_template') . '/template/module/twilio.tpl';
//                } else {
//                    $this->template = 'default/template/module/twilio.tpl';
//                }
//                $this->render();
                header('Content-type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
                echo '<Response>' . PHP_EOL;
                echo '     <Say> ' . $order['store_name'] . ' new Order. Order ID is  ' . $order['order_id'] . '.</Say>' . PHP_EOL;
                echo '     <Say> ' . 'Delivery - ' . $order['shipping_method'] . '.</Say>' . PHP_EOL;
                foreach ($order['order_products'] as $product) {
                    $text = $product['quantity'] . ' ' . preg_replace('/[^a-zA-Z0-9\'\.]/', ' ', html_entity_decode($product['name']));
                    if (isset($product['option'])) {
                        $text .= ' with options ';
                        foreach ($product['option'] as $option) {
                            $text .= preg_replace('/[^a-zA-Z0-9\'\.]/', ' ', html_entity_decode($option['value'])) . ', ';
                            //  $option['name'] .' is ' .
                        }
                    }
                    echo '     <Say> ' . trim($text, ', ') . '.</Say>' . PHP_EOL;
                }
                echo '     <Say> Customer ' . $order['firstname'] . ' ' . $order['lastname'] . '.</Say>' . PHP_EOL;
                echo '     <Say> ' . $order['shipping_address_1'] . '.</Say>' . PHP_EOL;
                if ($order['shipping_address_2'] != '') {
                    echo '     <Say> ' . $order['shipping_address_2'] . '.</Say>' . PHP_EOL;
                }
                echo '     <Say> Postcode ' . $order['shipping_postcode'] . '.</Say>' . PHP_EOL;
                echo '     <Say> Phone ' . $order['telephone'] . '.</Say>' . PHP_EOL;
                if ($order['payment_code'] == 'cod') {
                    echo '     <Say> Order unpaid, ' . $order['payment_method'] . '</Say>' . PHP_EOL;
                } else {
                    echo '     <Say> Order Paid, ' . $order['payment_method'] . '</Say>' . PHP_EOL;
                }
                echo '     <Gather action="' . $this->config->get('config_url') . 'index.php?route=module/twilio/orderaction&amp;orderid=' . $order['order_id'] . '" numDigits="1">' . PHP_EOL;
                echo '          <Say>For accept order, press 1.</Say>' . PHP_EOL;
                echo '          <Say>For reject order, press 2.</Say>' . PHP_EOL;
                echo '     </Gather>' . PHP_EOL;
// <!-- If customer doesn't input anything, prompt and try again. -->
                echo '     <Say>Sorry, I didn\'t get your response.</Say>' . PHP_EOL;
                echo '     <Redirect>' . $this->config->get('config_url') . 'index.php?route=module/twilio&amp;orderid=' . $order['order_id'] . '</Redirect>' . PHP_EOL;
                echo '</Response>';
            }
        }
    }

    public function orderaction() {
        if (isset($this->request->get['orderid'])) {
            $this->load->model('checkout/order');
            $order_id = $this->request->get['orderid'];
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="UTF-8"?>';

            echo '<Response>';

            $user_pushed = (int) $_REQUEST['Digits'];

            if ($user_pushed == 1) {
                $this->model_checkout_order->update($order_id,2);
                echo '<Say>Order Accepted.</Say>';
            } else if ($user_pushed == 2) {
                $this->model_checkout_order->update($order_id,8,'Rejected',true);
                echo "<Say>Order Rejected.</Say>";
            } else {
                echo "<Say>Sorry, that isn't a valid menu option.</Say>";
                echo '<Redirect>' . $this->config->get('config_url') . 'index.php?route=module/twilio&amp;orderid=' . $order_id . '</Redirect>';
            }

            echo '</Response>';
        }
    }

    public function opending() {
        if (isset($this->request->get['orderid'])) {
            $this->load->model('checkout/order');
            $order_id = $this->request->get['orderid'];
            $order = $this->model_checkout_order->getOrder($order_id);

            if ($order) {

                $order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");

                foreach ($order_product_query->rows as $order_product) {

                    $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $order_product['order_product_id'] . "'");

                    foreach ($order_option_query->rows as $option) {
                        $order_product['option'][] = $option;
                    }
                    $order['order_products'][] = $order_product;
                }



                header('Content-type: text/xml');
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<Response>';
                echo '     <Say> ' . $order['store_name'] . ' Pending Order. Order ID is  ' . $order['order_id'] . '.</Say>';
                echo '     <Say> ' . 'Delivery - ' . $order['shipping_method'] . '.</Say>';
                foreach ($order['order_products'] as $product) {
                    $text = $product['quantity'] . ' ' . preg_replace('/[^a-zA-Z0-9\'\.]/', ' ', html_entity_decode($product['name']));
                    if (isset($product['option'])) {
                        $text .= ' with options ';
                        foreach ($product['option'] as $option) {
                            $text .= preg_replace('/[^a-zA-Z0-9\'\.]/', ' ', html_entity_decode($option['value'])) . ', ';
                            //  $option['name'] .' is ' .
                        }
                    }
                    echo '     <Say> ' . trim($text, ', ') . '.</Say>';
                }
                echo '     <Say> Customer ' . $order['firstname'] . ' ' . $order['lastname'] . '.</Say>';
                echo '     <Say> ' . $order['shipping_address_1'] . '.</Say>';
                if ($order['shipping_address_2'] != '') {
                    echo '     <Say> ' . $order['shipping_address_2'] . '.</Say>';
                }
                echo '     <Say> Postcode ' . $order['shipping_postcode'] . '.</Say>';
                echo '     <Say> Phone ' . $order['telephone'] . '.</Say>';
                if ($order['payment_code'] == 'cod') {
                    echo '     <Say> Order unpaid, ' . $order['payment_method'] . '</Say>';
                } else {
                    echo '     <Say> Order Paid, ' . $order['payment_method'] . '</Say>';
                }
                echo '     <Gather action="' . $this->config->get('config_url') . 'index.php?route=module/twilio/orderaction&amp;orderid=' . $order['order_id'] . '" numDigits="1">' . PHP_EOL;
                echo '          <Say>For accept order, press 1.</Say>' . PHP_EOL;
                echo '          <Say>For reject order, press 2.</Say>' . PHP_EOL;
                echo '     </Gather>' . PHP_EOL;
// <!-- If customer doesn't input anything, prompt and try again. -->
                echo '     <Say>Sorry, I didn\'t get your response.</Say>' . PHP_EOL;
                echo '     <Redirect>' . $this->config->get('config_url') . 'index.php?route=module/twilio/opending&amp;orderid=' . $order['order_id'] . '</Redirect>' . PHP_EOL;
                echo '</Response>';
            }
        }
    }

    public function printer() {

        header('Content-type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<Response>';
        echo '     <Say> ' . $this->config->get('config_name') . '.</Say>';
        echo '     <Say> ' . 'Remote printer Request not comming ' . '.</Say>';
        echo '</Response>';
    }
    public function test() {
        $this->load->model('setting/twilio');
       
       echo $this->model_setting_twilio->startcall(1,55);
    }
    public function testsms() {
        $this->load->model('tool/sms');
        $message = 'TEST MESSAGE';
        $telephone = '07794568221';
        $response =  $this->model_tool_sms->sendSingleSMS($message,$telephone);
        $this->log->write( print_r($response, true));

    }

}

?>
