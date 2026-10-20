<div class="cod-complete payment-complete-btn">
    <i class="confirm_icon fa pay_confirm"></i>
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({ 
		type: 'get',
		url: 'index.php?route=payment/cod/confirm',
		success: function() {
			location = '<?php echo $continue; ?>';
		}		
	});
});
//--></script> 
