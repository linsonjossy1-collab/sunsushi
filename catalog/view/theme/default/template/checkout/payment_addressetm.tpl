<div class="register" id="existing">
<?php   $address = current($addresses);
        reset($addresses);
        if ($address['address_1'] =='') { ?>
<form id="AddressExistingForm" method="post" class="form-horizontal" data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
    data-bv-feedbackicons-invalid="glyphicon glyphicon-remove" data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
    <div id="payment-existing" class="register">

        <div class="form-group">
            <label class="col-lg-3 control-label">
                <span class="required">*</span>
                <?php echo $entry_firstname; ?>
            </label>
            <div class="col-lg-7">
                <input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo $address['firstname']; ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">
                <?php echo $entry_lastname; ?>
            </label>
            <!--<span class="required">*</span> <?php echo $entry_lastname; ?>-->
            <div class="col-lg-7">
                <input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo $address['lastname']; ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">
                <span class="required">*</span>
                <?php echo $entry_telephone; ?>
            </label>
            <div class="col-lg-7">
                <input type="text" class="form-control" name="telephone" id="telephone" value="<?php echo $telephone; ?>" />
            </div>
        </div>

        <?php if ($tax_id_display) { ?>
        <div class="form-group">
            <label class="col-lg-3 control-label">
                <?php if ($tax_id_required) { ?>
                <span class="required">*</span>
                <?php } ?>
                <?php echo $entry_lastname; ?>
            </label>
            <div class="col-lg-7">
                <input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo $address['lastname']; ?>" />
            </div>
        </div>
        <?php } ?>

        <div class="form-group reg_addr_div">
            <label class="col-lg-4 control-label">
                <?php echo $entry_address; ?>
            </label>
            <div class="col-lg-6">
                <select class="form-control sel_reg_address"></select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">
                <span class="required">*</span>
                <?php echo $entry_address_1; ?>
            </label>
            <div class="col-lg-7">
                <input type="text" class="form-control" name="address_1" id="address_1" value="<?php echo $address['address_1']; ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">
                <?php echo $entry_address_2; ?>
            </label>
            <div class="col-lg-7">
                <input type="text" class="form-control" name="address_2" id="address_2" value="<?php echo $address['address_2']; ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">
                <span class="required">*</span>
                <?php echo $entry_city; ?>
            </label>
            <div class="col-lg-7">
                <input type="text" class="form-control" name="city" id="city" value="<?php echo $address['city']; ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">
                <span class="required">*</span>
                <?php echo $entry_postcode; ?>
            </label>
            <div class="col-lg-7">
                <input type="text" class="form-control postcode" name="postcode" id="addne_postcode" value="<?php echo $postcode; ?>" />
                <input type="hidden" name="country_id" value="<?php echo $country_id; ?>" />
                <input type="hidden" name="zone_id" value="<?php echo $zone_id; ?>" />
                <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>" />
                <input type="hidden" name="payment_address" value="socialupdate" />
            </div>
        </div>

    </div>
</form>
<?php }
      else {
        if ($addresses) { ?>
    <input type="hidden" name="payment_address" value="existing" id="payment-address-existing" />
    <h2><?php echo $text_address_existing; ?></h2>
    <div id="payment-existing" class="register" >
        <input type="hidden" name="address_id" value="<?php echo $address_id; ?>" id="existing_address_id" />
        <?php foreach ($addresses as $address) { ?>
        <div class="list-group" id="payaddress_<?php echo $address['address_id']; ?>">
            <input type="hidden" class="address_postcode" name="address_postcode" value="<?php echo $address['postcode']; ?>"  />
            <a onclick="address_id_select('<?php echo $address['address_id']; ?>','<?php echo $address['postcode']; ?>')" class="list-group-item <?php echo ($address['address_id'] == $address_id)?'active':''; ?>" id="exiting_address_<?php echo $address['address_id']; ?>" >
              <h4 class="list-group-item-heading"><?php echo $address['firstname']; ?>  <?php echo $address['lastname']; ?></h4>
              <p class="list-group-item-text"><address>
                <strong><?php echo $address['address_1']; ?></strong><br>
                <?php echo $address['city']; ?><br>
                <?php echo $address['postcode']; ?>
              </address>
             </p>
            </a>
          </div>
        <?php } ?>
        <input onclick="change_address_type('new');" class="btn btn-primary" type="button" value="<?php echo $text_address_new; ?>">
    </div>

    <!-- <p>
        <input type="radio" name="payment_address" value="new" id="payment-address-new" />
        <label for="payment-address-new">
            <?php echo $text_address_new; ?>
        </label>
    </p> -->
    
    <?php } ?>
    <div id="payment-new" style="display: <?php echo ($addresses ? 'none' : 'block');  ?>;" class="col-lg-12 register">
    <form id="AddressNewForm" method="post" class="form-horizontal" data-bv-feedbackicons-valid="glyphicon glyphicon-ok" data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
        data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
            <div class="form-group">
                <label class="col-lg-3 control-label">
                    <span class="required">*</span>
                    <?php echo $entry_firstname; ?>
                </label>
                <div class="col-lg-7">
                    <input type="text" class="form-control" name="firstname" id="addne_firstname" value="<?php echo isset($address['firstname']) ? $address['firstname'] : ''; ?>" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">
                    <?php echo $entry_lastname; ?>
                </label>
                <!--<span class="required">*</span> <?php echo $entry_lastname; ?>-->
                <div class="col-lg-7">
                    <input type="text" class="form-control" name="lastname" id="addne_lastname" value="<?php echo isset($address['lastname']) ? $address['lastname'] : ''; ?>"  />
                </div>
            </div>

            <?php if ($tax_id_display) { ?>
            <div class="form-group">
                <label class="col-lg-3 control-label">
                    <?php if ($tax_id_required) { ?>
                    <span class="required">*</span>
                    <?php } ?>
                    <?php echo $entry_lastname; ?>
                </label>
                <div class="col-lg-7">
                    <input type="text" class="form-control" name="lastname" id="addne_lastname" value="" />
                </div>
            </div>
            <?php } ?>
            
            <div class="form-group postcodelookup">
                <label class="col-lg-3 control-label">
                    <span class="required">*</span>
                    <?php echo $entry_postcode; ?>
                </label>
                <div class="col-lg-4">
                    <input type="text" class="form-control postcode" name="postcode" id="addne_postcode" value="<?php echo $postcode; ?>" />
                    <input type="hidden" name="country_id" value="<?php echo $country_id; ?>" />
                    <input type="hidden" name="zone_id" value="<?php echo $zone_id; ?>" />
                    <input type="hidden" name="company_id" value="" />
                    <input type="hidden" name="company" value="" />
                </div>
                <div class="col-lg-3">
                    <input onclick="search_address_by_postcode('AddressNewForm')" class="btn btn-primary" type="button" value="<?php echo $text_find_address; ?>">
                </div>
                <span style="text-align:center; width:100%;" class="col-lg-10 error_postcode"></span>
            </div>

            <div class="form-group reg_addr_div">
                <label class="col-lg-3 control-label">
                    <?php echo $entry_address; ?>
                </label>
                <div class="col-lg-7">
                    <select class="form-control sel_reg_address"></select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">
                    <?php echo $entry_address_1; ?>
                </label>
                <div class="col-lg-7">
                    <input type="text" class="form-control" id="addne_address_1" name="address_1" value="" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">
                    <?php echo $entry_address_2; ?>
                </label>
                <div class="col-lg-7">
                    <input type="text" class="form-control" id="addne_address_2" name="address_2" value="" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">
                    <?php echo $entry_city; ?>
                </label>
                <div class="col-lg-7">
                    <input type="text" class="form-control" id="addne_city" name="city" value="" />
                </div>
            </div>
            <input onclick="change_address_type('existing');" class="btn btn-primary" type="button" value="<?php echo $text_your_address; ?>">
       
    </form>
    </div>
    
<?php } ?>
</div>
<script type="text/javascript">

    function change_address_type(address_type) {
        $('#payment-address input[name=\'payment_address\']').val(address_type);
        if (address_type == 'new') {
            $('#payment-existing').hide();
            $('#payment-new').show();
             if (typeof(Storage) !== "undefined") {
                 localStorage.payaddressnew = 1;
             }
            if($('#addne_postcode').val!=''){
                search_address_by_postcode('AddressNewForm');
            }
        } else {
            $('#payment-existing').show();
            $('#payment-new').hide();
            curraddressval = $('#existing_address_id').val();
            address_postcode = $('#payaddress_'+curraddressval+' .address_postcode').val();         
            address_id_select(curraddressval,address_postcode);            
             if (typeof(Storage) !== "undefined") {
                 localStorage.removeItem('payaddressnew');
             }
        }
    }

    $(document).ready(function () {
       if(typeof(Storage) !== "undefined" && localStorage.payaddressnew){
            $('#payment-existing').hide();
            $('#payment-new').show();
             $('#payment-address input[name=\'payment_address\']').val('new');
            search_address_by_postcode('AddressNewForm');
       }
        <?php if ($postcode != "") { ?>
                //$('#RegisterForm #postcodelookup').trigger('click');
            //search_address_by_postcode('AddressNewForm');
        <?php } ?>

    });
    function address_id_select(address_id,postcode) {
        $('#existing_address_id').val(address_id);
        $('#payment-existing .list-group-item').removeClass('active');
        $('#exiting_address_'+ address_id).addClass('active');
        <?php //if($delivery_method == "home.home") { ?>
				if (selected_postcode != postcode ) { // postcode changes.
					validatePostcode('AddressNewForm',postcode);
				}
		<?php //} ?>

    }
    
    $("#AddressExistingForm").on('change','.sel_reg_address',function() {
        var addr = $(this).val();
        if(addr) {
            var res = addr.split('--');
            $('#address_1').val(res[1]).trigger('change');
            $('#city').val(res[2]);
            $('#postcode').val(res[0]);
        }
    }); 

    $("#AddressNewForm").on('change','.sel_reg_address',function() {
        var addr = $(this).val();
        if(addr) {
            var res = addr.split('--');
            $('#addne_address_1').val(res[1]).trigger('change');
            $('#addne_city').val(res[2]);
            $('#addne_postcode').val(res[0]);
        }
    });
    
    
</script>