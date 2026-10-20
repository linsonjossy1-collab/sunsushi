<?php
class ModelPaymentMyposVirtual extends Model {
    public function getMethod($address, $total) {

        $this->load->language('payment/mypos_virtual');

        if ($this->config->get('mypos_virtual_test') == '1') {
            $paymentMethod = $this->config->get('mypos_virtual_developer_payment_method');
        } else {
            $paymentMethod = $this->config->get('mypos_virtual_production_payment_method');
        }

        $status = $paymentMethod == 3 ? $this->isValidIdealCurrency() : $this->isValidCardCurrency();

        $method_data = array();

        if ($status) {
            if ( $paymentMethod == 3 && $this->isValidIdealCurrency()) {
                $image = 'card_schemes_ideal_no_bg.png';
            } else if ($paymentMethod == 2) {
                $image = 'mypos_ideal_no_bg.png';
            } else {
                $image = 'card_schemes_no_bg.png';
            }

            $method_data = array(
                'code'       => 'mypos_virtual',
                'title'      => $this->language->get('text_title'), // . '<br /><img alt="mypos_checkout_logo" src="catalog/view/theme/default/image/mypos/' . $image . '" />',
                'terms'      => '',
                'sort_order' => $this->config->get('payment_mypos_virtual_sort_order'),
                'commision' => ($this->config->get('mypos_virtual_commision')?sprintf($this->language->get('text_payment_commision_charges'), $this->currency->format( $this->config->get('mypos_virtual_commision'))):'') 
            );
        }

        return $method_data;
    }

    public function isValidCardCurrency()
    {
        $currencies = array(
            'BGN', 'USD', 'EUR', 'GBP', 'CHF', 'JPY', 'RON', 'HRK', 'NOK', 'SEK', 'CZK', 'HUF', 'PLN', 'DKK', 'ISK',
        );

        foreach ($currencies as $currency) {
            if ($this->currency->has($currency)) {
                return true;
                break;
            }
        }

        return false;
    }

    public function isValidIdealCurrency()
    {
        $currencies = array(
            'EUR',
        );

        foreach ($currencies as $currency) {
            if ($this->currency->has($currency)) {
                return true;
                break;
            }
        }

        return false;
    }
}