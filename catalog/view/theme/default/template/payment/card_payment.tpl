<div class="card-pay-div">
    <div class="cod-complete payment-complete-btn">
        <i class="confirm_icon fa pay_confirm"></i>
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
    </div>
</div>

<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
        $('.common-notfcn').html('');
	$.ajax({ 
		type: 'post',
                dataType: 'html',
                data :{ total:$('#sub_tot_amt').val() , subpay_amt:$('#sub_payment_amount').val() , mainpay_amt: $('#main_payment_amount').val()},
		url: 'index.php?route=payment/card_payment/submitdetails',
		success: function(html) {
                    if(html==''){
                         $('.common-notfcn').html("Please Check and Collect complete Amount");
                    }else{
                         $('.card-pay-div').html(html);
                    }
	           
                }		
	});
});
//--></script> 
