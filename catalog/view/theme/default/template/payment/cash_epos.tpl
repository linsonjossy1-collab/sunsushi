<div class="paymentpage">
     <?php echo $cashcalculator;?>
</div>
<div class="cash_calcnbal">
    <div class="cash-rem-div">
        <span></span>
        <span class="cash-rem-amt"><?php echo $order_total;?></span> 
    </div>
    <div class="cash-bal-div">
         <span>Change Due:</span>
          <span class="cash-bal-amt"></span> 
    </div> 
</div>
<form id="epos_payment_complete" method="post" action="<?php echo $continue; ?>">
    <input type="hidden" name="paymentcode" value="<?php echo $paymentcode; ?>" />
</form>
<script type="text/javascript">
var paymentprocess = <?php echo $paymentprocess; ?>;
</script>