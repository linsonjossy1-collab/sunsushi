<?php

class ControllerModuleFreeproducts extends Controller {

    public function index() {
        $this->load->model('total/offers');
        $this->load->model('catalog/product');
        $offer_products = array();
        $offer_product_total = 0;
        $taxes = 0;
        $offer_details = $this->model_total_offers->getTotal($offer_products, $offer_product_total, $taxes);
        //print_r($offer_details);
        $offerproducts = array();
        $this->data['offer_details'] = array();
        //print_r($offer_details);
        if (!empty($offer_details)) {
            $j = 0;
            foreach ($offer_details as $offer) {

                if ($offer['iscoupon'] == 1) {//Coupon Offers   in_array($this->session->data['coupon'], $offer['coupon_code'])
                    if ((isset($this->session->data['coupon'])) && in_array($this->session->data['coupon'], $offer['coupon_code'])) {
                        //Auto add or Product Add popup
                        if ($offer['free_product_total_count'] > 0) {
                            if (!empty($offer['free_products'])) {
                                $i = 0;
                                foreach ($offer['free_products'] as $pid) {
                                    //if ($i < $offer['free_product_total_count']) {
                                        $free_details = $this->model_total_offers->getOfferFreeProductDetails($offer['offer_id'], $pid);
                                        $free_product_details = $free_details;
                                        //print_r($free_product_details);
                                        $free_product_details['product_options'] = unserialize($free_details['product_options']);
                                        if (!empty($free_product_details)) {
                                            $offerproducts[$j]['offername'] = $offer['offer_name'];
                                            $offerproducts[$j]['offer_id'] = $offer['offer_id'];
                                            $offerproducts[$j]['iscoupon'] = $offer['iscoupon'];
                                            $offerproducts[$j]['cart_amount_nedded'] = $offer['cart_amount_nedded'];
                                            if ($free_product_details['auto_add'] == 1) {//auto added products
                                                //echo $pid;
                                                // print_r($free_product_details['product_options']);
                                                if (!empty($free_product_details['product_options'])) {
                                                    $new_options = array();
                                                    foreach ($free_product_details['product_options'] as $key => $available_option) {
                                                        if (count($available_option) == 1) {
                                                            $new_options[$key] = $available_option;
                                                        } else {
                                                            $new_options[$key] = $available_option[0];
                                                        }
                                                    }
                                                    // echo $pid;
                                                    //print_r($new_options);
                                                    $this->cart->add($pid, 1, $new_options);
                                                    $i++;
                                                    unset($offerproducts[$j]);
                                                    continue;
                                                    break 2;
                                                } else {

                                                    $this->cart->add($pid, 1, array());
                                                    $i++;
                                                     unset($offerproducts[$j]);
                                                    continue;
                                                    break 2;
                                                }
                                            } else {//Popup Products  OR MESSAGE
                                                if ($free_product_details['auto_add'] == 0) {
                                                $option_data=array(); 
                                                $offerproducts[$j]['products'][$i] = $this->model_catalog_product->getProduct($pid);
                                                if (!empty($free_product_details['product_options'])) {
                                                    $product_options = $this->model_catalog_product->getProductOptions($pid);
                                                    //CONDITION FOR CHECKING OPTIONS  (ie, NO OPTIONS)
                                                    if (!empty($product_options)) {
                                                        foreach ($product_options as $product_option) {
                                                            if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                                                                $option_value_data = array();
                                                                if (isset($free_product_details['product_options'][$product_option['product_option_id']])) {
                                                                    foreach ($product_option['option_value'] as $product_option_value) {
                                                                        if (is_array($free_product_details['product_options'][$product_option['product_option_id']])) {
                                                                            if (in_array($product_option_value['product_option_value_id'], $free_product_details['product_options'][$product_option['product_option_id']])) {
                                                                                $option_value_data[] = array(
                                                                                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                                                                                    'option_value_id' => $product_option_value['option_value_id'],
                                                                                    'name' => $product_option_value['name'],
                                                                                    'price' => (float) $product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
                                                                                    'price_prefix' => $product_option_value['price_prefix']
                                                                                );
                                                                            }
                                                                        } else {
                                                                            if ($free_product_details['product_options'][$product_option['product_option_id']] == $product_option_value['product_option_value_id']) {
                                                                                $option_value_data[] = array(
                                                                                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                                                                                    'option_value_id' => $product_option_value['option_value_id'],
                                                                                    'name' => $product_option_value['name'],
                                                                                    'price' => (float) $product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
                                                                                    'price_prefix' => $product_option_value['price_prefix']
                                                                                );
                                                                            }
                                                                        }
                                                                    }

                                                                    $option_data[] = array(
                                                                        'product_option_id' => $product_option['product_option_id'],
                                                                        'option_id' => $product_option['option_id'],
                                                                        'name' => $product_option['name'],
                                                                        'type' => $product_option['type'],
                                                                        'option_value' => $option_value_data,
                                                                        'required' => $product_option['required'],
                                                                        'default' => $product_option['default']
                                                                    );
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $offerproducts[$j]['products'][$i]['product_options'] = $option_data;
                                                $i++;
                                                }
                                            }
                                        }
                                    //}
                                }
                                $j++;
                            }
                        }
                    }
                } 
                else {//NO COUPON CODE
                    if ($offer['free_product_total_count'] > 0) {
                        if (!empty($offer['free_products'])) {
                            $i = 0;
                            foreach ($offer['free_products'] as $pid) {
                           
                                    $free_details = $this->model_total_offers->getOfferFreeProductDetails($offer['offer_id'], $pid);
                                    $free_product_details = $free_details;
                                    $free_product_details['product_options'] = unserialize($free_details['product_options']);
                                    if (!empty($free_product_details)) {
                                        $offerproducts[$j]['offername'] = $offer['offer_name'];
                                        $offerproducts[$j]['offer_id'] = $offer['offer_id'];
                                        $offerproducts[$j]['iscoupon'] = $offer['iscoupon'];
                                        $offerproducts[$j]['cart_amount_nedded'] = $offer['cart_amount_nedded'];
                                        if ($free_product_details['auto_add'] == 1) {//auto added products
                                            if (!empty($free_product_details['product_options'])) {
                                                $new_options = array();
                                                foreach ($free_product_details['product_options'] as $key => $available_option) {
                                                    if (count($available_option) == 1) {
                                                        $new_options[$key] = $available_option;
                                                    } else {
                                                        $new_options[$key] = $available_option[0];
                                                    }
                                                }

                                                $this->cart->add($pid, 1, $new_options);
                                                unset($offerproducts[$j]);
                                                continue;
                                                break 2;
                                            } else {

                                                $this->cart->add($pid, 1, array());
                                                unset($offerproducts[$j]);
                                                continue;
                                                break 2;
                                            }
                                        } else {//Popup Products  OR MESSAGE
                                            if ($free_product_details['auto_add'] == 0) {
                                                $option_data=array();
                                                $offerproducts[$j]['products'][$i] = $this->model_catalog_product->getProduct($pid);
                                                //if (!empty($free_product_details['product_options'])) {
                                                    $product_options = $this->model_catalog_product->getProductOptions($pid);
                                                    //CONDITION FOR CHECKING OPTIONS  (ie, NO OPTIONS)
                                                    if (!empty($product_options)) {
                                                        foreach ($product_options as $product_option) {
                                                            if(isset($free_product_details['product_options'][$product_option['product_option_id']])){
                                                                if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                                                                $option_value_data = array();
                                                                if (isset($free_product_details['product_options'][$product_option['product_option_id']])) {
                                                                    foreach ($product_option['option_value'] as $product_option_value) {
                                                                        if (is_array($free_product_details['product_options'][$product_option['product_option_id']])) {
                                                                            if (in_array($product_option_value['product_option_value_id'], $free_product_details['product_options'][$product_option['product_option_id']])) {
                                                                                $option_value_data[] = array(
                                                                                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                                                                                    'option_value_id' => $product_option_value['option_value_id'],
                                                                                    'name' => $product_option_value['name'],
                                                                                    'price' => (float) $product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
                                                                                    'price_prefix' => $product_option_value['price_prefix']
                                                                                );
                                                                            }
                                                                        } else {
                                                                            if ($free_product_details['product_options'][$product_option['product_option_id']] == $product_option_value['product_option_value_id']) {
                                                                                $option_value_data[] = array(
                                                                                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                                                                                    'option_value_id' => $product_option_value['option_value_id'],
                                                                                    'name' => $product_option_value['name'],
                                                                                    'price' => (float) $product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
                                                                                    'price_prefix' => $product_option_value['price_prefix']
                                                                                );
                                                                            }
                                                                        }
                                                                    }

                                                                    $option_data[] = array(
                                                                        'product_option_id' => $product_option['product_option_id'],
                                                                        'option_id' => $product_option['option_id'],
                                                                        'name' => $product_option['name'],
                                                                        'type' => $product_option['type'],
                                                                        'option_value' => $option_value_data,
                                                                        'required' => $product_option['required'],
                                                                        'default' => $product_option['default']
                                                                    );
                                                                }
                                                            }
                                                            }else{
                                                                if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                                                                    $option_value_data = array();
                                                                    foreach ($product_option['option_value'] as $product_option_value) {
                                                                        $option_value_data[] = array(
                                                                            'product_option_value_id' => $product_option_value['product_option_value_id'],
                                                                            'option_value_id' => $product_option_value['option_value_id'],
                                                                            'name' => $product_option_value['name'],
                                                                            'price' => (float) $product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
                                                                            'price_prefix' => $product_option_value['price_prefix']
                                                                        );
                                                                    }
                                                                     $option_data[] = array(
                                                                        'product_option_id' => $product_option['product_option_id'],
                                                                        'option_id' => $product_option['option_id'],
                                                                        'name' => $product_option['name'],
                                                                        'type' => $product_option['type'],
                                                                        'option_value' => $option_value_data,
                                                                        'required' => $product_option['required'],
                                                                        'default' => $product_option['default']
                                                                    );
                                                                }
                                                            }
                                                            
                                                        }
                                                    }
                                                //}
                                                $offerproducts[$j]['products'][$i]['product_options'] = $option_data;
                                            }
                                        }
                                    }
                               
                                $i++;
                            }
                           $j++;
                        }
                    }
                }
                
            }
            
        }
        //print_r($offerproducts);
        $this->data['offer_products'] = (!empty($offerproducts))?array(0=>$offerproducts[0]):array();
        //$this->log->write(print_r($offerproducts,true));
        //}
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/freeproducts.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/freeproducts.tpl';
        } else {
            $this->template = 'default/template/module/freeproducts.tpl';
        }
        $this->response->setOutput($this->render());
    }
    public function mobileapp(){ 
        $this->load->model('total/offers');
        $this->load->model('catalog/product');
        $offer_products = array();
        $offer_product_total = 0;
        $taxes = 0;
        $offer_details = $this->model_total_offers->getTotal($offer_products, $offer_product_total, $taxes);
        //print_r($offer_details);
        $offerproducts = array();
        $this->data['offer_details'] = array();
        //print_r($offer_details);
        if (!empty($offer_details)) {
            foreach ($offer_details as $offer) {
                if ($offer['iscoupon'] == 1) {
                      //TODO CHECK COUPON STSTAUS
                }
                if ($offer['free_product_total_count'] > 0 && !empty($offer['free_products'])){
                    $products = array();
                    foreach ($offer['free_products'] as $pid) {
                        $free_details = $this->model_total_offers->getOfferFreeProductDetails($offer['offer_id'], $pid);
                        $free_product_details = $free_details;
                        $free_product_details['product_options'] = unserialize($free_details['product_options']);
                        $productdata =  $this->model_catalog_product->getProduct($pid);
                        $product_options = $this->model_catalog_product->getProductOptions($pid);
                        $option_data=array();
                        if (!empty($product_options)) {
                            foreach ($product_options as $product_option) {
                                if(isset($free_product_details['product_options'][$product_option['product_option_id']])){
                                    if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                                    $option_value_data = array();
                                    if (isset($free_product_details['product_options'][$product_option['product_option_id']])) {
                                        foreach ($product_option['option_value'] as $product_option_value) {
                                            if (is_array($free_product_details['product_options'][$product_option['product_option_id']])) {
                                                if (in_array($product_option_value['product_option_value_id'], $free_product_details['product_options'][$product_option['product_option_id']])) {
                                                    $option_value_data[] = array(
                                                        'product_option_value_id' => $product_option_value['product_option_value_id'],
                                                        'option_value_id' => $product_option_value['option_value_id'],
                                                        'name' => $product_option_value['name'],
                                                        'price' => (float) $product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
                                                        'price_prefix' => $product_option_value['price_prefix']
                                                    );
                                                }
                                            } else {
                                                if ($free_product_details['product_options'][$product_option['product_option_id']] == $product_option_value['product_option_value_id']) {
                                                    $option_value_data[] = array(
                                                        'product_option_value_id' => $product_option_value['product_option_value_id'],
                                                        'option_value_id' => $product_option_value['option_value_id'],
                                                        'name' => $product_option_value['name'],
                                                        'price' => (float) $product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
                                                        'price_prefix' => $product_option_value['price_prefix']
                                                    );
                                                }
                                            }
                                        }

                                        $option_data[] = array(
                                            'product_option_id' => $product_option['product_option_id'],
                                            'option_id' => $product_option['option_id'],
                                            'name' => $product_option['name'],
                                            'type' => $product_option['type'],
                                            'option_value' => $option_value_data,
                                            'required' => $product_option['required'],
                                            'default' => $product_option['default']
                                        );
                                    }
                                }
                                }else{
                                    if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
                                        $option_value_data = array();
                                        foreach ($product_option['option_value'] as $product_option_value) {
                                            $option_value_data[] = array(
                                                'product_option_value_id' => $product_option_value['product_option_value_id'],
                                                'option_value_id' => $product_option_value['option_value_id'],
                                                'name' => $product_option_value['name'],
                                                'price' => (float) $product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
                                                'price_prefix' => $product_option_value['price_prefix']
                                            );
                                        }
                                         $option_data[] = array(
                                            'product_option_id' => $product_option['product_option_id'],
                                            'option_id' => $product_option['option_id'],
                                            'name' => $product_option['name'],
                                            'type' => $product_option['type'],
                                            'option_value' => $option_value_data,
                                            'required' => $product_option['required'],
                                            'default' => $product_option['default']
                                        );
                                    }
                                }

                            }
                        }
                        $productdata['product_options'] = $option_data;
                        $products[]=$productdata;
                    }
                }
                if(!empty($products)){
                    $offerproducts[]=array(
                        'offername'=> $offer['offer_name'],
                        'offer_id'=> $offer['offer_id'],
                        'iscoupon'=> $offer['iscoupon'],
                        'cart_amount_nedded' => $offer['cart_amount_nedded'],
                        'products'=>$products
                    );
                }               
            }
        }
        //$this->log->write(print_r($offerproducts,true));
        $this->data['offer_products'] = array('offer_products'=> (!empty($offerproducts))?array(0=>$offerproducts[0]):array());
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/mobilefreeproducts.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/mobilefreeproducts.tpl';
        } else {
            $this->template = 'default/template/module/mobilefreeproducts.tpl';
        }
       $this->render();
    }
}

?>