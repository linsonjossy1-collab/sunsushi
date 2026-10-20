<form  name="OrderForm" id="OrderForm" action="<?php echo $action; ?>" method="post">
    <input type="hidden" name="AMOUNT" id="AMOUNT" value="<?PHP echo $amount; ?>"/> 
    <input type="hidden" name="CN" value="<?PHP echo $billing_fullname; ?>"> 
    <input type="hidden" name="COM" value="<?PHP echo $description; ?>">
    <input type="hidden" name="CURRENCY" id="CURRENCY" value="<?PHP echo $currency; ?>"/>
    <input type="hidden" name="EMAIL" id="EMAIL" value="<?PHP echo $email_address; ?>">
    <input type="hidden" name="FONTTYPE" id="FONTTYPE" value="<?PHP echo $fonttype; ?>">
    <input type="hidden" name="LANGUAGE" id="LANGUAGE" value="<?PHP echo $ShopperLocale; ?>">
   <input type="hidden" name="LOGO" value="<?PHP echo $logo; ?>">
    <input type="hidden" name="ORDERID" id="ORDERID" value="<?PHP echo $order_id ?>"/> 
    <input type="hidden" name="OWNERADDRESS" id="OWNERADDRESS" value="<?PHP echo $billing_address; ?>">
    <input type="hidden" name="OWNERCTY" id="OWNERCTY" value="<?PHP echo $billing_country; ?>">
    <input type="hidden" name="OWNERTELNO" value="<?PHP echo $customer_phone_number; ?>"> 
    <input type="hidden" name="OWNERTOWN" id="OWNERTOWN" value="<?PHP echo $billing_city; ?>">
    <input type="hidden" name="OWNERZIP" id="OWNERZIP" value="<?PHP echo $billing_postcode; ?>">
    <input type="hidden" name="PMLISTTYPE" id="PMLISTTYPE" value="<?PHP echo $PMLISTTYPE ?>"/>											
    <input type="hidden" name="PSPID" id="PSPID" value="<?PHP echo $epdq_pspid ?>"/>
     <input type="hidden" name="BGCOLOR" id="BGCOLOR" value="<?PHP echo $BGCOLOR; ?>"/>
    <input type="hidden" name="BUTTONBGCOLOR" id="BUTTONBGCOLOR" value="<?PHP echo $BUTTONBGCOLOR; ?>"/>
    <input type="hidden" name="BUTTONTXTCOLOR" id="BUTTONTXTCOLOR" value="<?PHP echo $BUTTONTXTCOLOR; ?>"/>
    <input type="hidden" name="TBLBGCOLOR" id="TBLBGCOLOR" value="<?PHP echo $TBLBGCOLOR; ?>"/>
    <input type="hidden" name="TBLTXTCOLOR" id="TBLTXTCOLOR" value="<?PHP echo $TBLTXTCOLOR; ?>">
    <input type="hidden" name="TITLE" id="TITLE" value="<?PHP echo $TITLE; ?>"/>
    <input type="hidden" name="TXTCOLOR" id="TXTCOLOR" value="<?PHP echo $TXTCOLOR; ?>">
    <input type="hidden" name="SHASign" value="<?PHP echo $strHashedString_plain; ?>">
    <input type="hidden" name="ACCEPTURL" value="<?php echo $success_url; ?>">
    <input type="hidden" name="DECLINEURL" value="<?php echo $declined_url; ?>">
    <input type="hidden" name="EXCEPTIONURL" value="<?php echo $exception_url; ?>">
    <input type="hidden" name="CANCELURL" value="<?php echo $cancel_url; ?>"> 
<div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
    </div>
  </div> 
</form>