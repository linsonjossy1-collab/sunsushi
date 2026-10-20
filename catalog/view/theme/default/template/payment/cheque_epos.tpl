<form id="epos_payment_complete" method="post" action="<?php echo $continue; ?>">
    <input type="hidden" name="paymentcode" value="<?php echo $paymentcode; ?>" />
    <?php if(isset($this->session->data['subpayment_status'])){ ?>
     <input type="hidden" name="route" value="payment/cheque/subpaymentdetails" />
     <input type="hidden" id="main_payment_amount" name="main_payment_amount" value="">
     <input type="hidden" id="sub_payment_amount" name="sub_payment_amount" value="">
    <?php } ?>
</form>