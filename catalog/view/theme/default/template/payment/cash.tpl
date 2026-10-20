
<div class="paymentpage">
     <?php echo $cashcalculator;?>
    <!--<div id="cash_balance">
        <span class="pos_finaladrstxt"> Enter Amount Recieved</span>: <span><input id="customer_amt_recieved" type="text" name="customer_amt_recieved" value="" /></span>
        <br/>
        <span class="pos_finaladrstxt"> Balance Amount</span>: <span id="actual_balance_amount_span"></span>
     
    </div>-->
</div>
<div class="cash_calcnbal">
    <div class="cash-rem-div">
        <span></span>
        <span class="cash-rem-amt"></span> 
    </div>
    <div class="cash-bal-div">
         <span>Change Due:</span>
          <span class="cash-bal-amt"></span> 
    </div>
   
    
</div>
<div class="cash-complete payment-complete-btn">
    <i class="confirm_icon fa pay_confirm"></i>
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
</div>
<script type="text/javascript">
var paymentprocess = <?php echo $paymentprocess; ?>;
$('#button-confirm').bind('click', function() {
    $.ajax({ 
		type: 'get',
		url: 'index.php?route=payment/cash/confirm',
		success: function() {
			location = '<?php echo $continue; ?>';
		}		
	});
});
</script>