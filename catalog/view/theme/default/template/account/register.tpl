<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php echo $column_left; ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/jspostcode.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/jstelnumbers.js"></script>
<script>
	$(function() {
		var availableTags = [
			"Bath",
			"Birmingham",
			"Bradford",
			"Brighton and Hove",
			"Bristol",
			"Cambridge",
			"Canterbury",
			"Carlisle",
			"Chester",
			"Chichester",
			"Coventry and Warks",
			"Derby",
			"Durham - Sunderland",
			"Ely",
			"Exeter",
			"Gloucester",
			"Hereford",
			"Hull",
			"Lancaster",
			"Leeds",
			"Leicester",
			"Lincoln",
                        "Liverpool",
                        "London",
                        "Manchester",
                        "Newcastle",
                        "Norwich",
                        "Nottingham",
                        "Oxford",
                        "Peterborough",
                        "Plymouth",
                        "Portsmouth",
                        "Preston",
                        "Ripon",
                        "Salford",
                        "Salisbury",
                        "Sheffield",
                        "Southampton",
                        "St Albans",
                        "Stoke-on-Trent",
                        "Truro",
                        "Wakefield",
                        "Wells",
                        "Westminister",
                        "Winchester",
                        "Wolverhampton",
                        "Worcester",
                        "York"
                        
		];
		$( "#city" ).autocomplete({
			source: availableTags
		});
	});
	</script>
<script type="text/javascript">
    function valid(theForm){
    var firstname=theForm.firstname.value;
    var lastname=theForm.lastname.value;
    var email=theForm.email.value;
    var address_1=theForm.address_1.value;
    var city=theForm.city.value;
    var postcode=theForm.postcode.value;
    var zone_id=theForm.zone_id.value;
    var password=theForm.password.value;
    var confirm=theForm.confirm.value;
     postcode = postcode_formater(postcode);
                       theForm.postcode.value=postcode;
    if(firstname == ""){
    alert("Please Enter the Firstname");
    theForm.firstname.focus();
    return (false);
}
if(lastname == ""){
alert("Please Enter the Lastname");
theForm.lastname.focus();
return (false);
}
if (email == "")
                        {
                        alert("Please enter the Email");
                    theForm.email.focus();
                    return (false);
                }
                else{	apos=theForm.email.value.indexOf("@")
                dotpos=theForm.email.value.lastIndexOf(".")
                if (apos<1||dotpos-apos<2) {
                alert("Invalid Email Address.");
                theForm.email.focus();
                return (false);
            }
        }
        if (theForm.telephone.value == "")
                        {
            alert("Please enter the Phone");
        theForm.telephone.focus();
        return (false);
    }
    if (!checkUKTelephone(theForm.telephone.value)) {
    alert (telNumberErrors[telNumberErrorNo]);
    theForm.telephone.focus();
    return false;
}
if(address_1 == ""){
alert("Please Enter Your Adress");
theForm.address_1.focus();
return (false);
}        
if((address_1.length<3)||(address_1.length>128)){
alert("Address must be between 3 and 128 characters!");
theForm.address_1.focus();
return (false);
}
if(city == ""){
alert("Please Enter Your City");
theForm.city.focus();
return (false);
}        
if((city.length<2)||(city.length>128)){
alert("City must be between 2 and 128 characters!");
theForm.city.focus();
return (false);
}
if (theForm.postcode.value == "")
                        {
                            alert("Please enter the Post Code");
                            theForm.postcode.focus();
                            return (false);
                        }
                        if (!checkPostCode(theForm.postcode.value)) {
                       
                            alert ("Postcode has an invalid format");
                            theForm.postcode.focus();
                            return false;
                        }
if(zone_id == ""){
alert("Please select a region / state!");
theForm.zone_id.focus();
return (false);
}   
if(password == ""){
alert("Please Enter password");
theForm.password.focus();
return (false);
} 
if((password.length<4)||(password.length>20)){
alert("Password must be between 4 and 20 characters!");
theForm.password.focus();
return (false);
}
if(confirm == ""){
alert("Please Enter confirm password");
theForm.confirm.focus();
return (false);
} 
if(password != confirm){
alert("Password Missmatch");
theForm.confirm.focus();
return (false);
} 
if(theForm.captcha.value=="") {
            alert("enter captcha!");
            theForm.captcha.focus();
            return (false);
}
}
function postcode_formater(postcode) {
postcode = postcode.replace(/ /g,""); 
              
var l=postcode.slice(-3);  
var f=postcode.slice(0,(postcode.length - 3));
postcode =  f + ' ' +l;
postcode = postcode.toUpperCase();
             
return postcode;
}
</script>
<div id="content" class="col-sm-8 reg"><?php echo $content_top; ?>

    <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
    
    <p><?php echo $text_account_already; ?></p>

    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" onsubmit="return valid(this);">
        <h2><?php echo $text_your_details; ?></h2>
        <div class="content">
            <div class="form form-group">
                <label  class="col-sm-3 control-label">
                    <?php echo $entry_firstname; ?><span class="required">*</span> </label>
                    <div class="col-sm-7">
                    <input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo $firstname; ?>" />
                        <?php if ($error_firstname) { ?>
                        <span class="error"><?php echo $error_firstname; ?></span>
                        <?php } ?></div>
              
                 <label  class="col-sm-3 control-label">
                    <?php echo $entry_lastname; ?><span class="required">*</span></label>
                     <div class="col-sm-7">
                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $lastname; ?>" />
                        <?php if ($error_lastname) { ?>
                        <span class="error"><?php echo $error_lastname; ?></span>
                        <?php } ?></div>
              
                 <label  class="col-sm-3 control-label">
                    <?php echo $entry_email; ?><span class="required">*</span></label>
                     <div class="col-sm-7">
                    <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>" />
                        <?php if ($error_email) { ?>
                        <span class="error"><?php echo $error_email; ?></span>
                        <?php } ?></div>
              
                 <label  class="col-sm-3 control-label">
                    <?php echo $entry_telephone; ?><span class="required">*</span></label>
                     <div class="col-sm-7">
                    <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $telephone; ?>" />
                        <?php if ($error_telephone) { ?>
                        <span class="error"><?php echo $error_telephone; ?></span>
                        <?php } ?></div>
              
                 <label  class="col-sm-3 control-label">
                    <?php echo $entry_fax; ?></label>
                    <div class="col-sm-7">
                    <input type="text" class="form-control" name="fax" value="<?php echo $fax; ?>" />
                    <?php if ($error_fax) { ?><span class="error"><?php echo $error_fax; ?></span><?php } ?>
                    </div>
                        <input type="hidden" name="company">
                        <input type="hidden" name="company_id">
                        <input type="hidden" name="tax_id">
                         <!--<input type="hidden" name="address_2">-->
                          <input type="hidden" name="tax_id">
                    
              
            </div>
        </div>
        <h2><?php echo $text_your_address; ?></h2>
        <div class="content">
            <div class="form form-group">

                <!--<tr style="display: <?php echo (count($customer_groups) > 1 ? 'table-row' : 'none'); ?>;">
                    <?php echo $entry_customer_group; ?>
                    <?php foreach ($customer_groups as $customer_group) { ?>
                        <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
                        <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
                        <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
                        <br />
                        <?php } else { ?>
                        <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" />
                        <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
                        <br />
                        <?php } ?>
                        <?php } ?>
               </tr>-->
               
                <label  class="col-sm-3 control-label">
                 <?php echo $entry_postcode; ?><span id="postcode-required" class="required">*</span></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control"  id="postcode" name="postcode" value="<?php echo $postcode; ?>" />
                </div>
                <div class="col-sm-2"><a class="button" id="postcodelookup">FIND</a></div>
                <span class="error" id="error_postcode"> <?php if ($error_postcode) { echo $error_postcode;  } ?></span>

                <div id="reg_addr_div" style='display:none;'>
                    <label  class="col-sm-3 control-label">
                    <span><?php echo $entry_address; ?></label>
                    <div class="col-sm-7">
                        <select class="form-control" id="sel_reg_address"></select>
                    </div>
                </div>
                           
                 <label  class="col-sm-3 control-label">
                    <?php echo $entry_address_1; ?><span class="required">*</span></label>
                    <div class="col-sm-7">
                        <input class="form-control"  type="text" name="address_1" id="address_1" value="<?php echo $address_1; ?>">
                       <!-- <textarea name="address_1" id="address_1"><?php echo $address_1; ?></textarea>-->
                        <?php if ($error_address_1) { ?>
                        <span class="error"><?php echo $error_address_1; ?></span>
                        <?php } ?></div>
              
                <label  class="col-sm-3 control-label">
                    <?php echo $entry_address_2; ?></label>
                        <div class="col-sm-7">
                        <input class="form-control"  type="text"  name="address_2" id="address_2" value="<?php echo $address_2; ?>"></div>
                       <!-- <textarea name="address_1" id="address_1"><?php echo $address_1; ?></textarea>-->
                    
              

                    <label  class="col-sm-3 control-label">
                    <?php echo $entry_city; ?><span class="required">*</span> </label>
                     <div class="col-sm-7">
                        <input type="text" class="form-control"  id="city" name="city" value="<?php echo $city; ?>" />
                        <?php if ($error_city) { ?>
                        <span class="error"><?php echo $error_city; ?></span>
                        <?php } ?>
                    </div>
              

                <input type="hidden" name="country_id" value="<?php echo $country_id; ?>" />
                <input type="hidden" name="zone_id" value="<?php echo $zone_id; ?>" />
                <!--
                <label  class="col-sm-3 control-label">
                    <span class="required">*</span> <?php echo $entry_country; ?></label>
                     <div class="col-sm-7">
                    <select name="country_id" class="form-control" >
                            <option value=""><?php echo $text_select; ?></option>
                            <?php // foreach ($countries as $country) { ?>
                            <?php // if ($country['country_id'] == $country_id) { ?>
                            <option value="<?php //  echo $country['country_id']; ?>" selected="selected"><?php // echo $country['name']; ?></option>
                            <?php // } else { ?>
                            <option value="<?php // echo $country['country_id']; ?>"><?php // echo $country['name']; ?></option>
                            <?php // } ?>
                            <?php // } ?>
                        </select>
                        <?php if ($error_country) { ?>
                        <span class="error"><?php echo $error_country; ?></span>
                        <?php } ?></div>
              
                 <label  class="col-sm-3 control-label">
                    <span class="required">*</span> <?php echo $entry_zone; ?>
                    </label>
                    <div class="col-sm-7">
                    <select name="zone_id" id="zone_id" class="form-control">
                        </select>
                        <?php if ($error_zone) { ?>
                        <span class="error"><?php echo $error_zone; ?></span>
                        <?php } ?></div>
                        -->
              
            </div>
        </div>
        <h2><?php echo $text_your_password; ?></h2>
        <div class="content">
           <div class="form form-group">
                 <label  class="col-sm-3 control-label">
                     <?php echo $entry_password; ?><span class="required">*</span></label>
                    <div class="col-sm-7">
                    <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" />
                        <?php if ($error_password) { ?>
                        <span class="error"><?php echo $error_password; ?></span>
                        <?php } ?></div>
              
                 <label  class="col-sm-3 control-label">
                    <?php echo $entry_confirm; ?><span class="required">*</span></label>
                    <div class="col-sm-7">
                    <input type="password" class="form-control" id="confirm" name="confirm" value="<?php echo $confirm; ?>" />
                        <?php if ($error_confirm) { ?>
                        <span class="error"><?php echo $error_confirm; ?></span>
                        <?php } ?></div>
              
            </div>
            <div class="form form-group">
                    <label  class="col-sm-3 control-label">
                        <?php echo $entry_captcha; ?><span class="required">*</span></label>
                    <div class="col-sm-7">                   
                        <input type='text' name='captcha'  class="form-control"/>
                        <?php if ($error_captcha) { ?>
                        <span class="error"><?php echo $error_captcha; ?></span>
                        <?php } ?>
                        <div id='img_url'>
                            <img src="index.php?route=account/register/captcha" alt="CAPTCHA"/>
        </div>
                    </div>
            </div>
        </div>
        <h2><?php echo $text_newsletter; ?></h2>
        <label  class="col-sm-12 control-label">
        <div class="newsletter">Subscribe newsletter to receive exclusive offers and special discounts</div></label>

            <div class="form form-group">
                 <label  class="col-sm-3 control-label">
                    <?php echo $entry_newsletter; ?></label>
                    <div class="col-sm-7" id="subscribe">
                    <?php if ($newsletter) { ?>
                        <input type="radio" name="newsletter" value="1" checked="checked" />
                        <?php echo $text_yes; ?>
                        <input type="radio" name="newsletter" value="0" />
                        <?php echo $text_no; ?>
                        <?php } else { ?>
                        <input type="radio" name="newsletter" value="1"/>
                        <?php echo $text_yes; ?>
                        <input type="radio" name="newsletter" value="0" checked/>
                        <?php echo $text_no; ?>
                        <?php } ?>
              </div>
            </div>

        <?php if ($text_agree) { ?>
        <div class="col-sm-12 buttons registerterms">
            <div class="right">
            <?php if ($agree) { ?>
                <input type="checkbox" name="agree" value="1" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="agree" value="1" />
                <?php } ?>
            <?php echo $text_agree; ?>
                
                <input type="submit" value="<?php echo $button_continue; ?>" class="button" />
            </div>
        </div>
        <?php } else { ?>
        <div class="buttons">
            <div class="right">
                <input type="submit" value="<?php echo $button_continue; ?>" class="button" />
            </div>
        </div>
        <?php } ?>
    </form>
    <?php echo $content_bottom; ?></div>
<div id="terms-popup" class="modal fade" tabindex="-1" role="dialog" data-backdrop="false" aria-hidden="false" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header">Privacy Policy<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">Close</button>
			</div>
		</div>
    </div>
</div>
<script type="text/javascript"><!--
$('input[name=\'customer_group_id\']:checked').live('change', function() {
var customer_group = [];
	
<?php foreach ($customer_groups as $customer_group) { ?>
customer_group[<?php echo $customer_group['customer_group_id']; ?>] = [];
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

$('input[name=\'customer_group_id\']:checked').trigger('change');
//--></script> 
<script type="text/javascript"><!--

$(document).on('click', '.registerterms a', function () {
            $('#terms-popup .modal-body').html('')
            $('#terms-popup .modal-body').load('index.php?route=information/information/info&information_id=<?php echo $this->config->get('config_account_id'); ?>', function (data) {
                $('#terms-popup').modal('show');
                $('#terms-popup .modal-body').css('overflow-y', 'auto'); 
                $('#terms-popup .modal-body').css('max-height', $(window).height() * 0.8);
            });
});
$('select[name=\'country_id\']').bind('change', function() {
$.ajax({
url: 'index.php?route=account/register/country&country_id=' + this.value,
dataType: 'json',
beforeSend: function() {
$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
},
complete: function() {
$('.wait').remove();
},			
success: function(json) {
if (json['postcode_required'] == '1') {
$('#postcode-required').show();
} else {
$('#postcode-required').hide();
}
			
html = '<option value=""><?php echo $text_select; ?></option>';
			
if (json['zone'] != '') {
for (i = 0; i < json['zone'].length; i++) {
html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
html += ' selected="selected"';
}
	
html += '>' + json['zone'][i]['name'] + '</option>';
}
} else {
html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
}
			
$('select[name=\'zone_id\']').html(html);
},
error: function(xhr, ajaxOptions, thrownError) {
alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
}
});
});

$('select[name=\'country_id\']').trigger('change');
//--></script> 
<script type="text/javascript"><!--
  $(document).on("click","#postcodelookup",function(){
            addr_postcode = postcode_formater($('#postcode').val());
            //check postcode validation
            
            $('#postcode').val(addr_postcode);
            $('#sel_reg_address').html('<option value="">Choose address</option>');
             $.ajax({
                url: 'index.php?route=module/postcode_lookup/postcodelookup',
                type: 'get',
                data: {postcode: addr_postcode},
                dataType: 'json',
                success: function(json) {
                    $('#error_postcode').html('');
                    $('#address_1').val('');
                    $('#city').val('');
                    if(json['error']){
                        $('#error_postcode').html('No matching Postcode in our database');
                        $('#reg_addr_div').hide();
                    }else if(json['address'] && json['address']!='[]') {
                        // console.log(json['address']);
                        var obj = jQuery.parseJSON(json['address']);
                        var optn = '';
                        $.each(obj, function(key,value) {
                            optn+='<option value="'+value.postcode+'--'+value.address+'--'+value.city+'">'+value.address+','+value.city+'</option>';
                        });
                        $('#sel_reg_address').append(optn);
                        $('#reg_addr_div').show();
                        $('#error_postcode').html('');
                    }else if (json['street'] && json['city']) {
                         $('#address_1').val(json['street']);
                         $('#city').val(json['city']);
                         $('#error_postcode').html('');
                         $('#reg_addr_div').hide();
                    }else if (json['street']){
                         $('#address_1').val(json['street']);   
                         //$('.post_code_warning').html('please enter details manually');
                         $('#error_postcode').html('');
                         $('#reg_addr_div').hide();
                    }else if (json['city']){
                        $('#city').val(json['city']);
                        $('#error_postcode').html('');
                        $('#reg_addr_div').hide();
                    }else{
                        $('#error_postcode').html('please enter details manually');
                        $('#reg_addr_div').hide();
                    } 
                }
            });
      });

      
    $("#sel_reg_address").on('change',function() {
        var addr = $(this).val();
        if(addr) {
            var res = addr.split('--');
            $('#address_1').val(res[1]);
            $('#city').val(res[2]);
            $('#postcode').val(res[0]);
        }
    });
//--></script> <?php echo $column_right; ?>
<?php echo $footer; ?>