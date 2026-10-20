<?php 
class ModelModuleAlsoBought extends Model {
     public function getcartrelatedproduct() {
    $upselling_data = $this->config->get('upselling_data'); // upselling data rules
    if (!is_array($upselling_data)) {
        $upselling_data = array();
    }

    $side_categories = array();
    $this->load->model('catalog/product');
    $this->load->model('tool/image');
    unset($this->session->data['upselling_cart_products']); // Clear any previous session data for upselling products
    // Build a quantity map instead of just a list of product ids,
    // so we know HOW MANY of each main product are in the cart.
    $cart_quantities = array();
    foreach ($this->cart->getProducts() as $products) {
        $pid = $products['product_id'];
        if (!isset($cart_quantities[$pid])) {
            $cart_quantities[$pid] = 0;
        }
        $cart_quantities[$pid] += $products['quantity'];
    }
    $cartproduct = array_keys($cart_quantities);

    // Accumulated free quantity per related product id, across ALL matching rules.
    $free_qty_totals = array();

    foreach ($upselling_data as $rule) {
        if (isset($rule['page']) && !empty($rule['page']) && !in_array(0, $rule['page'])) {
            continue;
        }

        // How many "main" items (matching this rule) are in the cart?
        $main_qty_in_cart = 0;
        $match_main = false;

        if (!empty($rule['main']) && is_array($rule['main'])) {
            foreach ($rule['main'] as $main_id) {
                if (isset($cart_quantities[$main_id])) {
                    $main_qty_in_cart += $cart_quantities[$main_id];
                }
            }
            if ($main_qty_in_cart > 0) {
                $match_main = true;
            }
        } else {
            // No main restriction defined on the rule - treat as always matching,
            // but there's no meaningful "quantity" to base free items on.
            $match_main = true;
        }

        if ($match_main && !empty($rule['related']) && is_array($rule['related'])) {

            // Work out how many free units this rule grants.
            $free_type     = isset($rule['free_type']) ? $rule['free_type'] : 'F';
            $rule_free_qty = isset($rule['free_qty']) ? (int)$rule['free_qty'] : 0;

            if ($free_type == 'P') {
                // Proportional: main_qty * configured free_qty per main item
                $free_count = $main_qty_in_cart * $rule_free_qty;
            } else {
                // Fixed ('F'): 1 free unit per main item in the cart
                $free_count = $main_qty_in_cart;
            }

            foreach ($rule['related'] as $related_id) {
                //if (!in_array($related_id, $cartproduct)) {
                    $product_info = $this->model_catalog_product->getProduct($related_id);
                    if ($product_info) {
                        if (isset($rule['max_qty']) && (int)$rule['max_qty'] > 0) {
                            $product_info['max_buy'] = $rule['max_qty'];
                        }

                        // Key by product_id directly - this also fixes the dedupe,
                        // replacing the old array_intersect_key/array_column approach.
                        $side_categories[$related_id] = $product_info;

                        if (!isset($free_qty_totals[$related_id])) {
                            $free_qty_totals[$related_id] = 0;
                        }
                        $free_qty_totals[$related_id] += $free_count;
                    }
                //}
            }
        }
    }

    $all_products = array();
    if (!empty($side_categories)) {
        foreach ($side_categories as $key => $result) {
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price      = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                $prod_price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), '', '', FALSE);
                $org_price  = $result['price'];
            } else {
                $price      = false;
                $prod_price = false;
            }

            if (isset($result['special']) && (float) $result['special']) {
                $special   = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
                $org_price = $result['special'];
            } else {
                $special = false;
            }

            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $image = '';
            }

            $all_products[$result['product_id']] = array(
                'key'        => $result['product_id'],
                'product_id' => $result['product_id'],
                'thumb'      => $image,
                'name'       => $result['name'],
                'price'      => $this->currency->format($this->tax->calculate($prod_price, $result['tax_class_id'], $this->config->get('config_tax'))),
                'special'    => $special,
                'quantity'   => 0,
                'max_buy'    => isset($result['max_buy']) ? $result['max_buy'] : 25,
                'free_qty'   => isset($free_qty_totals[$result['product_id']]) ? $free_qty_totals[$result['product_id']] : 0,
                'total'=>$this->currency->format($this->tax->calculate(0, $result['tax_class_id'], $this->config->get('config_tax')))
            );
        }
    }
    $this->session->data['upselling_cart_products'] = $all_products; // Store the upselling products in session data
    return $all_products;
}
    
}
?>