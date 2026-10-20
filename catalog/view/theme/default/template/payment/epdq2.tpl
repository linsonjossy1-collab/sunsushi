<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" id="payment">
  <input type="hidden" name="ACCEPTURL" value="<?php echo $accepturl; ?>" />
  <input type="hidden" name="AMOUNT" value="<?php echo $amount; ?>" />
  <input type="hidden" name="CANCELURL" value="<?php echo $cancelurl; ?>" />
  <input type="hidden" name="CN" value="<?php echo $cn; ?>" />
  <input type="hidden" name="CURRENCY" value="<?php echo $currency; ?>" />
  <input type="hidden" name="DECLINEURL" value="<?php echo $declineurl; ?>" />
  <input type="hidden" name="EMAIL" value="<?php echo $email; ?>" />
  <input type="hidden" name="EXCEPTIONURL" value="<?php echo $exceptionurl; ?>" />
  <input type="hidden" name="LANGUAGE" value="<?php echo $language; ?>" />

  <input type="hidden" name="ORDERID" value="<?php echo $orderid; ?>" />
  <input type="hidden" name="OWNERADDRESS" value="<?php echo $owneraddress; ?>" />
  <input type="hidden" name="OWNERCTY" value="<?php echo $ownercty; ?>" />
  <input type="hidden" name="OWNERTELNO" value="<?php echo $ownertelno; ?>" />
  <input type="hidden" name="OWNERTOWN" value="<?php echo $ownertown; ?>" />
  <input type="hidden" name="OWNERZIP" value="<?php echo $ownerzip; ?>" />
  <input type="hidden" name="PSPID" value="<?php echo $pspid; ?>" />
  <input type="hidden" name="SHASign" value="<?php echo $shasign; ?>" />
<div class="buttons">
    <div class="right"><a onclick="$('#payment').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></div>
 </div>
</form>


