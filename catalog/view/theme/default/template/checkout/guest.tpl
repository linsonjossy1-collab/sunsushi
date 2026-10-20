<div class="register" id="gust">
<form id="GuestForm" method="post" class="form-horizontal" data-bv-feedbackicons-valid="glyphicon glyphicon-ok" data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
    data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
    <div class="register">
        <div class="alert alert-danger alert-dismissable alertInfo">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="false">&times;</button>
            <?php echo $text_guest_alert_willnotbeable; ?>
            <br/> &nbsp;&nbsp;&nbsp;
            <?php echo $text_guest_alert_trackorders; ?>
            <br/> &nbsp;&nbsp;&nbsp;
            <?php echo $text_guest_alert_rewards; ?>
            <br/> &nbsp;&nbsp;&nbsp;
            <?php echo $text_guest_alert_manyadvantages; ?>
            <br/>
        </div>
        <div class="regbutton">
            <span class="bodr">Not now, I will create an account later</span>
            <span class="bodr nopad">
                <a aria-controls="collapse_new_customer" aria-expanded="false" href="#collapse_new_customer" data-parent="#payment-address"
                    data-toggle="collapse" role="button" class="collapsed allopen">
                    Create an account
                </a>
            </span>
        </div>
        <h2><?php echo $text_your_details; ?></h2>
        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo $entry_firstname; ?>
                <span class="required">*</span>
            </label>
            <div class="col-lg-6">
                <input type="text" class="form-control" name="firstname" id="guest_firstname" value="" />
            </div>
        </div>

        <div class="form-group" <?php if($delivery_method=='collect.collect') { ?> style='display:none;' <?php } ?> >
            <label class="col-lg-4 control-label">
                <?php echo $entry_lastname; ?>
            </label>
            <!--<span class="required">*</span> <?php echo $entry_lastname; ?>-->
            <div class="col-lg-6">
                <input type="text" class="form-control" name="lastname" id="guest_lastname" value="" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo $entry_email; ?>
                <span class="required">*</span>
            </label>
            <div class="col-lg-6">
                <input type="text" class="form-control" name="email" id="guest_email" value="" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo $entry_telephone; ?>
                <span class="required">*</span>
            </label>
            <div class="col-lg-6">
                <input type="text" class="form-control" name="telephone" id="guest_telephone" value="" />
                <input type="hidden" name="fax" value="" />
            </div>
        </div>

        <!--<h2><?php echo $text_your_address; ?></h2>-->

        <div style="display: <?php echo (count($customer_groups) > 1 ? 'table-row' : 'none'); ?>;">
            <?php echo $entry_customer_group; ?>
            <br />
            <?php foreach ($customer_groups as $customer_group) { ?>
            <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
            <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="guest_customer_group_id<?php echo $customer_group['customer_group_id']; ?>"
                checked="checked" />
            <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>">
                <?php echo $customer_group['name']; ?>
            </label>
            <br />
            <?php } else { ?>
            <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="guest_customer_group_id<?php echo $customer_group['customer_group_id']; ?>"
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
        <?php if($delivery_method == "collect.collect") { ?>
        <input type="hidden" name="postcode" value="<?php echo $postcode; ?>" />
        <input type="hidden" name="address_1" value="" />
        <input type="hidden" name="address_2" value="" />
        <input type="hidden" name="guest_city" value="" />

        <!--
        <div class="form-group">
            <label class="col-lg-4 control-label">
                <span class="required">*</span> <?php echo $entry_postcode; ?>
                <?php echo $entry_postcode; ?>
            </label>
            <div class="col-lg-3">
                <input type="text" class="form-control postcode" name="postcode" id="guest_postcode" value="<?php echo $postcode; ?>" />
                
            </div>
            <div class="col-lg-3">
                <input onclick="search_address_by_postcode('GuestForm')" id="postcodelookupGust" class="greenButton" type="button" value="<?php echo $text_find_address; ?>">
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

        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php  echo $entry_address_1; ?>
            </label>
            <div class="col-lg-6">
                <input type="text" class="form-control" id="guest_address_1" name="address_1" value=""/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo $entry_address_2; ?>
            </label>
            <div class="col-lg-6">
                <input type="text" class="form-control" id="guest_address_2" name="address_2" value="" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-4 control-label">
                <?php echo $entry_city; ?>
            </label>
            <div class="col-lg-6">
                <input type="text" class="form-control" id="guest_city" name="city" value="" />
            </div>
        </div>

        -->

        <?php } else { ?>
        
        <h2><?php echo $text_your_address; ?></h2>
        
        <!-- Only display of postcode is neded i the current method is home delivery. This is done to avoid confusion due to the existing home delivery not availbale for a new postcode entered -->
        <div id="quickGuestAddress">
            <div class="form-group postcodelookup">
                <label class="col-lg-4 control-label">
                <span class="required">*</span>
                    <?php echo $entry_postcode; ?>
                </label>
                <!--<span class="required">*</span> <?php echo $entry_postcode; ?>-->
                <div class="col-lg-3">
                    <input type="text" class="form-control postcode" name="postcode" id="guest_postcode" value="<?php echo $postcode; ?>" required/>
                    
                </div>
                <div class="col-lg-3">
                    <!--<?php echo $text_change_address; ?>-->
                    <input onclick="search_address_by_postcode('GuestForm')" id="postcodelookupGust" class="greenButton" type="button" value="<?php echo $text_find_address; ?>">
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

            <input type="hidden" class="form-control" id="guest_house" name="house" value="" required/>
            <!--
            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <span class="required">*</span>
                    <?php  echo $entry_home; ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="guest_house" name="house" value="" required/>
                    <div id="error_house"></div>
                </div>
            </div>
            -->

            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <span class="required">*</span>
                    <?php  echo $entry_address_1; ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="guest_address_1" name="address_1" value=""/>
                    <div id="error_address1"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <?php echo $entry_address_2; ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="guest_address_2" name="address_2" value=""/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">
                    <?php echo $entry_city; ?>
                </label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" id="guest_city" name="city" value="" />
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</form>
</div>

<?php if ($shipping_required) { ?>
<input type="hidden" name="shipping_address" value="1" id="shipping" />

<?php } ?>



<script type="text/javascript"><!--
        //$('#payment-address input[name=\'customer_group_id\']:checked').live('change', function() {
        $(document).on('change', '#payment-address input[name=\'customer_group_id\']:checked', function () {
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
            search_address_by_postcode('GuestForm');
	<?php } ?>

	
});

    $("#GuestForm").on('change','.sel_reg_address',function() {
        var addr = $(this).val();
        if(addr) {
            var res = addr.split('--');
            $('#guest_address_1').val(res[1]);
            $('#guest_city').val(res[2]);
            $('#guest_postcode').val(res[0]);
            $('#guest_address_1').blur();
        }
    });

//--></script>