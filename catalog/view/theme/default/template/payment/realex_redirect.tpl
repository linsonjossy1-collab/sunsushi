<form action=https://epage.payandshop.com/epage.cgi method=post>

<input type=hidden name="MERCHANT_ID" value="<?php echo $merchant_id; ?>">
<input type=hidden name="ORDER_ID" value="<?php echo $order_id; ?>">
<input type=hidden name="CURRENCY" value="<?php echo $curr; ?>">
<input type=hidden name="AMOUNT" value="<?php echo $amount; ?>">
<input type=hidden name="TIMESTAMP" value="<?php echo $timestamp; ?>">
<input type=hidden name="MD5HASH" value="<?php echo $md5hash; ?>">
<input type=hidden name="AUTO_SETTLE_FLAG" value="1">
<?php
 if($realex_mode=='test'){
?>
<input type=hidden name="ACCOUNT" value="internettest">
<?php }else{ ?>
<input type=hidden name="ACCOUNT" value="internet">
<?php }?>
 <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
    </div>
  </div>
</form>