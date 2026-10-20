<div class="register" id="register">
    <form id="RegisterForm" method="post" data-toggle="validator" role="form" class="form-horizontal" data-bv-feedbackicons-valid="glyphicon glyphicon-ok" data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
        data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
    
            <h2><?php echo $text_your_details; ?></h2>
            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <span class="required">*</span>
                    <?php echo $entry_firstname; ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" name="firstname" id="reg_firstname" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <?php echo $entry_lastname; ?>
                </label>
                <!--<span class="required">*</span> <?php echo $entry_lastname; ?>-->
                <div class="col-lg-6">
                    <input type="text" class="form-control" name="lastname" id="reg_lastname" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <span class="required">*</span>
                    <?php echo $entry_email; ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" name="email" id="reg_email" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <span class="required">*</span>
                    <?php echo $entry_telephone; ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" name="telephone" id="reg_telephone" value="" />
                    <p>Enter a valid mobile number to receive exclusive discounts via SMS.</p>
                    <input type="hidden" name="fax" value="" />
                </div>
            </div>

            <h2><?php echo $text_your_password; ?></h2>

            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <span class="required">*</span>
                    <?php echo $text_your_password; ?>
                </label>
                <div class="col-lg-6">
                    <input class="form-control" type="password" name="password" id="reg_password" value="" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <span class="required">*</span>
                    <?php echo $entry_confirm; ?>
                </label>
                <div class="col-lg-6">
                    <input class="form-control" type="password" name="confirm" id="reg_confirmPassword" value="" />
                </div>
            </div>

            <h2><?php echo $text_your_address; ?></h2>

            <div style="display: <?php echo (count($customer_groups) > 1 ? 'table-row' : 'none'); ?>;">
                <?php echo $entry_customer_group; ?>
                <br />
                <?php foreach ($customer_groups as $customer_group) { ?>
                <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
                <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="reg_customer_group_id<?php echo $customer_group['customer_group_id']; ?>"
                    checked="checked" />
                <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>">
                    <?php echo $customer_group['name']; ?>
                </label>
                <br />
                <?php } else { ?>
                <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="reg_customer_group_id<?php echo $customer_group['customer_group_id']; ?>"
                />
                <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>">
                    <?php echo $customer_group['name']; ?>
                </label>
                <br />
                <?php } ?>
                <?php } ?>
            </div>

            <input type="hidden" name="company" value="" class="large-field" />
            <input type="hidden" name="company_id" value="" />
            <input type="hidden" name="tax_id" value="" />
            <input type="hidden" name="country_id" value="<?php echo $country_id; ?>" />
            <input type="hidden" name="zone_id" value="<?php echo $zone_id; ?>" />

            <!-- Allow users to enter a postcode in case the method is collect and postcode is empty. Not mandatory -->
            <!-- Only display of postcode is neded i the current method is home delivery. This is done to avoid confusion due to the existing home delivery not availbale for a new postcode entered -->
            <div class="form-group postcodelookup">
                <label class="col-lg-4 control-label">
                    <?php echo $entry_postcode; ?><span class="required">*</span>
                </label>
                
                <div class="col-lg-3">
                    <input type="text" class="form-control postcode" name="postcode" id="reg_postcode" value="<?php echo $postcode; ?>" />
                </div>
                <div class="col-lg-3">
                    <input onclick="search_address_by_postcode('RegisterForm')" id="reg_postcodelookup" class="greenButton" type="button" value="<?php echo $text_find_address; ?>">
                </div>
                <span style="text-align:center; width:100%;" class="col-lg-10 error_postcode"></span>
            </div>
            <div class="form-group reg_addr_div">
                <label class="col-lg-4 control-label">
                    <?php echo $entry_address; ?>
                </label>
                <div class="col-lg-6">
                    <select class="form-control sel_reg_address">
                    </select>
                </div>
            </div>

            <input type="hidden" class="form-control" id="reg_house" name="house" value="" />

            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <span class="required">*</span>
                    <?php echo $entry_address_1; ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="reg_address_1" name="address_1" value="" required/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <?php echo $entry_address_2; ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="reg_address_2" name="address_2" value="" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <?php echo $entry_city; ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="reg_city" name="city" value="" />
                </div>
            </div>

            <div class="form-group">
                
              <!--<label class="col-lg-4 control-label"></label>-->
                <label class="col-sm-12 control-label newss">
                    <input type="checkbox" class="form-control" name="newsletter" value="1" id="reg_newsletter" style="width: auto; display: inline-block; height: auto; margin-right: 3px ! important; position: relative;"
                    />
                    <?php echo $entry_newsletter; ?>
                </label>
            </div>
    </form>
</div>
<?php if ($shipping_required) { ?>
<input type="hidden" name="shipping_address" value="1" id="shipping" />

<?php } ?>
<script type="text/javascript"><!--
        $('#payment-address input[name=\'customer_group_id\']:checked').on('change', function () {
            var customer_group = [];
	
<?php foreach($customer_groups as $customer_group) { ?>
                customer_group[<?php echo $customer_group['customer_group_id']; ?>] =[];
                customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_display'] = '<?php echo $customer_group['company_id_display']; ?>';
                customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_required'] = '<?php echo $customer_group['company_id_required']; ?>';
                customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_display'] = '<?php echo $customer_group['tax_id_display']; ?>';
                customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_required'] = '<?php echo $customer_group['tax_id_required']; ?>';
<?php } ?>	

	if (customer_group[this.value]) {
                if (customer_group[this.value]['company_id_display'] == '1') {
                    $('#company-id-display').show();
                } else {
                    $('#company-id-display').hide();
                }

                if (customer_group[this.value]['company_id_required'] == '1') {
                    $('#company-id-required').show();
                } else {
                    $('#company-id-required').hide();
                }

                if (customer_group[this.value]['tax_id_display'] == '1') {
                    $('#tax-id-display').show();
                } else {
                    $('#tax-id-display').hide();
                }

                if (customer_group[this.value]['tax_id_required'] == '1') {
                    $('#tax-id-required').show();
                } else {
                    $('#tax-id-required').hide();
                }
            }
        });
    $(document).ready(function () {
        $('#payment-address input[name=\'customer_group_id\']:checked').trigger('change');
    <?php if ($postcode != "") { ?>
            //$('#RegisterForm #postcodelookup').trigger('click');
            search_address_by_postcode('RegisterForm');
	<?php } ?>

	
});


    $('#payment-address input[name=\'email\']').focusout(function () {
        $curInput = $(this);
        if ($curInput.val()) {
            $.ajax({
                url: 'index.php?route=checkout/registeretm/validateemail',
                type: 'post',
                data:{'email':$curInput.val()},
                //data: $('.register input[name=\'email\']'),
                dataType: 'json',
                success: function (json) {
                    //  $('#confirm .checkout-content').html(html);
                    $('#reg-email-error').html('');
                    //console.log('load data');
                    if (json['error']) {
                        if (json['error']['email']) {
                            //alert(json['error']['email']);
                            $('#reg-email-error').html(json['error']['email']);
                            $('#reg-email-error').show();
                            $curInput.focus();
                        }
                        else if (json['error']['warning']) {
                            
                            if($curInput.attr('id')!='login_email') {
                                
                                $('#reg-email-error').html(json['error']['warning']);
                                //alert(json['error']['warning']);
                                $("#login input[name=\'email\']").val($curInput.val())
                                $curInput.val('');
                                $("#login input[name=\'password\']").focus();
                            }

                        }
                    }

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

    $("#RegisterForm").on('change','.sel_reg_address',function() {
        var addr = $(this).val();
        if(addr) {
            var res = addr.split('--');
            $('#reg_address_1').val(res[1]);
            $('#reg_city').val(res[2]);
            $('#reg_postcode').val(res[0]);
            $('#reg_address_1').blur();
        }
    });
//--></script>