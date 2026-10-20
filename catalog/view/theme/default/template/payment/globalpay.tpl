<form action="<?php echo $action; ?>" method="POST" class="form-horizontal" id="globalpay_form_redirect">
  <input type=hidden name="MERCHANT_ID" value="<?php echo $merchant_id; ?>" />
  <input type=hidden name="ORDER_ID" value="<?php echo $order_id; ?>" />
  <input type=hidden name="CURRENCY" value="<?php echo $currency; ?>" >
  <input type=hidden name="AMOUNT" value="<?php echo $amount; ?>" >
  <input type=hidden name="TIMESTAMP" value="<?php echo $timestamp; ?>" />
  <input type=hidden name="SHA1HASH" value="<?php echo $hash; ?>">
  <input type=hidden name="AUTO_SETTLE_FLAG" value="<?php echo $settle; ?>">
  <input type=hidden name="RETURN_TSS" value="<?php echo $tss; ?>">
  <input type=hidden name="BILLING_CODE" value="<?php echo $billing_code; ?>">
  <input type=hidden name="BILLING_CO" value="<?php echo $payment_country; ?>">
  <input type=hidden name="SHIPPING_CODE" value="<?php echo $shipping_code; ?>">
  <input type=hidden name="SHIPPING_CO" value="<?php echo $shipping_country; ?>">
  <input type=hidden name="MERCHANT_RESPONSE_URL" value="<?php echo $response_url; ?>">
  <input type="hidden" name="HPP_CUSTOMER_EMAIL" value="<?php echo $email; ?>">
  <input type="hidden" name="HPP_CUSTOMER_PHONENUMBER_MOBILE" value="<?php echo $telephone; ?>">
  <input type="hidden" name="HPP_BILLING_STREET1" value="<?php echo $payment_address_1; ?>">
  <input type="hidden" name="HPP_BILLING_STREET2" value="<?php echo $payment_address_2; ?>">
  <input type="hidden" name="HPP_BILLING_CITY" value="<?php echo $payment_city; ?>">
  <input type="hidden" name="HPP_BILLING_POSTALCODE" value="<?php echo $payment_postcode; ?>">
  <input type="hidden" name="HPP_BILLING_COUNTRY" value="<?php echo $payment_country_code; ?>">
  <input type=hidden name="HPP_3DS2_BILLING_DETAILS_REQUIRED" value="false">
  <input type=hidden name="COMMENT1" value="OpenCart">
  <?php if (isset($globalpay_account)) { ?>
  <input type=hidden name="ACCOUNT" value="<?php echo $globalpay_account; ?>" >
  <?php } ?>
</form>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary button" />
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
  $('#globalpay_form_redirect').submit();
});
//--></script>