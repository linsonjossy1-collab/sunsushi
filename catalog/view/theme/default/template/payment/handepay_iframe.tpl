<?php

	$formdata = array(
		"merchantID"        => $merchantid,
		"amount"            => $amount,
		"action"            => "SALE",
		"type"              => 1,
		"countryCode"       => $countrycode,
		"currencyCode"      => $currencycode,
		"transactionUnique" => $trans_id,
		"orderRef"          => "Order " . $trans_id,
		"redirectURL"       => $redirect,
		"callbackURL" 		=> $callback,
		"customerName"      => $bill_name,
		"customerAddress"   => $bill_addr,
		"customerPostCode"  => $bill_post_code,
		"customerEmail"     => $bill_email,
		"customerPhone"     => $bill_tel,
		"item1Description"  => "Order " . $trans_id,
		"item1Quantity"     => 1,
		"item1GrossValue"   => $amount,
                "duplicateDelay"    => 900
	);
	ksort( $formdata );

	$signature = http_build_query( $formdata, '', '&' ) . $merchantsecret;

	$formdata['signature'] = hash( 'SHA512', $signature ).'|'.implode(',', array_keys($formdata));

?>

<iframe id="handepayframe" name="handepayframe" frameBorder="0" seamless='seamless' style="width:699px; height:1073px;margin: 0 auto;display:block;"></iframe>

<form id="handepayPaymentForm" action="https://payments.handepay.co.uk/paymentform/" method="post" target="handepayframe">
	<?php
		foreach ( $formdata as $key => $value ) {

			echo "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $value . "\" />";

		}
	?>


</form>

<script>
	// detects if jquery is loaded and adjusts the form for mobile devices
	if (window.jQuery) {
		$(function(){
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
				$('form#handepayPaymentForm').append('<input type="hidden" name="formResponsive"  value="Y"/>');
				$('#handepayframe').css({ height : '1300px', width : '50%'});
			}
		});
	}
	document.getElementById('handepayPaymentForm').submit();
</script>
