<form method="post" action="<?php echo $action; ?>">
    <input type="hidden" name="currencyiso3a" value="<?php echo $currency_code; ?>"/>
    <input type="hidden" name="mainamount" value="<?php echo $mainamount; ?>"/>
    <input type="hidden" name="sitereference" value="<?php echo $sitereference; ?>"/>
    <input type="hidden" name="authmethod" value="FINAL">
    <input type="hidden" name="version" value="2">
    <input type="hidden" name="stprofile" value="default">
    <input type="hidden" name="stdefaultprofile" value="st_paymentcardonly">
    <input type=hidden name="ruleidentifier" value="STR-6">
    <input type=hidden name="ruleidentifier" value="STR-7">
    <input type=hidden name="ruleidentifier" value="STR-8">
    <input type=hidden name="ruleidentifier" value="STR-9">
    <input type="hidden" name="successfulurlredirect" value="<?php echo $successfulurlredirect; ?>"/>
    <input type="hidden" name="declinedurlredirect" value="<?php echo $declinedurlredirect; ?>"/>
    <input type="hidden" name="successfulurlnotification" value="<?php echo $successfulurlnotification; ?>"/>
    <input type="hidden" name="declinedurlnotification" value="<?php echo $declinedurlnotification; ?>"/>
    <input type="hidden" name="orderreference" value="<?php echo $oid; ?>">
    <input type="hidden" name="sitesecuritytimestamp" value="<?php echo $sitesecuritytimestamp; ?>"/>
    <input type="hidden" name="sitesecurity" value="<?php echo $sitesecurity; ?>" />
    <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
</form>
