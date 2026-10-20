<div class="card-pay-div">
    <!--<div class="cod-complete payment-complete-btn">
        <i class="confirm_icon fa pay_confirm"></i>
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
    </div>-->
</div>
<form id="epos_payment_complete" method="post" action="<?php echo $continue; ?>">
    <input type="hidden" name="paymentcode" value="<?php echo $paymentcode; ?>" />
    <?php if(isset($this->session->data['subpayment_status'])){ ?>
     <input type="hidden" name="route" value="payment/card_payment/subpaymentdetails" />
     <input type="hidden" id="main_payment_amount" name="main_payment_amount" value="">
     <input type="hidden" id="sub_payment_amount" name="sub_payment_amount" value="">
    <?php } ?>
<!-- <input hidden="route" value="payment/card_payment/submitdetails" />
    <input hidden="total" value="" />
    <input hidden="subpay_amt" value="" />
    <input hidden="mainpay_amt" value="" />-->
</form>

