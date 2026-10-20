<form class="form-horizontal" role="form" action="<?php echo $verofy_action; ?>" method="post">
    <input type="hidden" name="ekashu_seller_id" value="<?php echo $hashcode_input['ekashu_seller_id']; ?>"/>
    <input type="hidden" name="ekashu_seller_key" value="<?php echo $hashcode_input['ekashu_seller_key'] ?>"/>
    <input type="hidden" name="ekashu_reference" value="<?php echo $hashcode_input['ekashu_reference']; ?>"/>
    <input type="hidden" name="ekashu_amount" value="<?php echo $hashcode_input['ekashu_amount']; ?>"/>
    <input type="hidden" name="ekashu_currency" value="<?php echo $hashcode_input['ekashu_currency'];  ?>"/>
    <input type="hidden" name="ekashu_auto_confirm" value="<?php echo $hashcode_input['ekashu_auto_confirm']; ?>"/>
    <input type="hidden" name="ekashu_duplicate_check" value="<?php echo $hashcode_input['ekashu_duplicate_check']; ?>"/>
    <input type="hidden" name="ekashu_card_address_required" value="<?php echo $hashcode_input['ekashu_card_address_required']; ?>"/>
    <input type="hidden" name="ekashu_card_address_verify" value="<?php echo $hashcode_input['ekashu_card_address_verify']; ?>"/>
    <input type="hidden" name="ekashu_card_zip_code_verify" value="<?php echo $hashcode_input['ekashu_card_zip_code_verify']; ?>"/>
    <input type="hidden" name="ekashu_card_title_mandatory" value="<?php echo $hashcode_input['ekashu_card_title_mandatory'];  ?>"/>
    <input type="hidden" name="ekashu_card_email_address_mandatory" value="<?php echo $hashcode_input['ekashu_card_email_address_mandatory'];   ?>"/>
    <input type="hidden" name="ekashu_viewport" value="<?php echo $hashcode_input['ekashu_viewport'];  ?>"/>
    <input type="hidden" name="ekashu_style_sheet" value="<?php echo $hashcode_input['ekashu_style_sheet'];  ?>"/>
    <input type="hidden" name="ekashu_return_url" value="<?php echo $ekashu_return_url;  ?>"/>
    <input type="hidden" name="ekashu_success_url" value="<?php echo $hashcode_input['ekashu_success_url'];  ?>"/>
    <input type="hidden" name="ekashu_callback_success_url" value="<?php echo $hashcode_input['ekashu_callback_success_url'];  ?>"/>
    <input type="hidden" name="ekashu_callback_failure_url" value="<?php echo $hashcode_input['ekashu_callback_failure_url']; ?>"/>

    <input type="hidden" name="ekashu_hash_code" value="<?php echo  $hash; ?>"/>
    <input type="hidden" name="ekashu_hash_code_type" value="<?php echo $hashcode_input['ekashu_hash_code_type']; ?>">
    <input type="hidden" name="ekashu_hash_code_version"  value="<?php echo $hashcode_input['ekashu_hash_code_version']; ?>">
      
    <input type="hidden" name="ekashu_card_email_address" value="<?php echo $cusomer_email; ?>"/>
    <input type="hidden" name="ekashu_card_first_name" value="<?php echo $cusomer_firstname; ?>"/>
    <input type="hidden" name="ekashu_card_last_name" value="<?php echo $cusomer_lastname; ?>"/>
    <input type="hidden" name="ekashu_card_address_1" value="<?php echo $cusomer_address_1; ?>"/>
    <input type="hidden" name="ekashu_card_address_2" value="<?php echo $cusomer_address_2; ?>"/>
    <input type="hidden" name="ekashu_card_city" value="<?php echo $cusomer_city; ?>"/>
    <input type="hidden" name="ekashu_card_zip_code" value="<?php echo $cusomer_postcode; ?>"/>
    <input type="hidden" name="ekashu_card_country" value="<?php echo $cusomer_country_code; ?>"/>
           
    <div class="buttons">
        <div class="right">
            <input type="submit" value="Pay" class="button" />
        </div>
    </div>        
</form>