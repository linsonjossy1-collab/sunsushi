<form method='post' action='https://www.ipg-online.com/connect/gateway/processing' id='lloyds_payment'>
<input type='hidden' name='txntype' value='<?=$txntype?>'>
<input type='hidden' name='timezone' value='<?=$timezone?>'/>
<input type='hidden' name='txndatetime' value='<?=$txndatetime?>'/>
<input type='hidden' name='hash_algorithm' value='<?=$hash_algorithm?>'/>
<input type='hidden' name='hash' value='<?=$hash?>'/>
<input type='hidden' name='storename' value='<?=$storename?>'/>
<input type='hidden' name='mode' value='<?=$mode?>'/>
<input type='hidden' name='oid' value='<?=$oid?>'/>
<input type='hidden' name='chargetotal' value='<?=$chargetotal?>'/>
<input type='hidden' name='currency' value='<?=$currency?>'/>
<input type='hidden' name='responseSuccessURL' value='<?=$responseSuccessURL?>'/>
<input type='hidden' name='responseFailURL' value='<?=$responseFailURL?>'/>
<div class="buttons">
    <div class="right">
        <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
    </div>
  </div>
</form>