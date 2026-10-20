<form method="post" action="<?php echo $tyl_action; ?>">
<input type="hidden" name="txntype" value="<?php echo $txntype; ?>"/>
<input type="hidden" name="timezone" value="<?php echo $timezone; ?>"/>
<input type="hidden" name="txndatetime" value="<?php echo $txndatetime; ?>"/>
<input type="hidden" name="hash_algorithm" value="SHA256"/>
<input type="hidden" name="hash" value="<?php echo $hash; ?>"/>
<input type="hidden" name="storename" value="<?php echo $storename; ?>"/>
<input type="hidden" name="mode" value="<?php echo $txnmode; ?>"/>
<input type="hidden" name="chargetotal" value="<?php echo $amount; ?>"/>
<input type="hidden" name="currency" value="<?php echo $curr; ?>"/>
<input type="hidden" name="oid" value="<?php echo $order_id; ?>"/>
<input type="hidden" name="responseFailURL" value="<?php echo $responseFailURL; ?>"/>
<input type="hidden" name="responseSuccessURL" value="<?php echo $responseSuccessURL; ?>"/>
<input type="hidden" name="transactionNotificationURL" value="<?php echo $transactionNotificationURL; ?>"/>
<div class="buttons">
    <div class="right">
  <!--      <input type="submit" value="<?php echo $button_confirm; ?>" class="button" /> -->
    </div>
  </div>
</form>