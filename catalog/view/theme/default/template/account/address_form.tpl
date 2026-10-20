<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="col-sm-8"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" >
    <h2><?php echo $text_edit_address; ?></h2>
    <div class="content">
        <div class="form-group">
            <label class="col-lg-4 control-label"> <?php echo $entry_firstname; ?><span class="required">*</span></label>
          <div class="col-lg-6"><input type="text" name="firstname" value="<?php echo $firstname; ?>" class="form-control"/>
            <?php if ($error_firstname) { ?>
            <span class="error"><?php echo $error_firstname; ?></span>
            <?php } ?></div>
        </div>
        
        <div class="form-group">
          <label class="col-lg-4 control-label"> <?php echo $entry_lastname; ?><span class="required">*</span></label>
          <div class="col-lg-6"><input type="text" name="lastname" value="<?php echo $lastname; ?>" />
            <?php if ($error_lastname) { ?>
            <span class="error"><?php echo $error_lastname; ?></span>
            <?php } ?></div>
        </div>
        
        <?php if ($tax_id_display) { ?>
        <div class="form-group">
          <label class="col-lg-4 control-label"><?php echo $entry_tax_id; ?></label>
          <div class="col-lg-6"><input type="text" name="tax_id" value="<?php echo $tax_id; ?>" />
            <?php if ($error_tax_id) { ?>
            <span class="error"><?php echo $error_tax_id; ?></span>
            <?php } ?></div>
        </div>
        <?php } ?>

        <div class="form-group">
          <label class="col-lg-4 control-label"> <?php echo $entry_postcode; ?><span id="postcode-required" class="required">*</span></label>
          <div class="col-lg-6">
            <div class="col-lg-3 col-xs-6" id="post_3">
              <input type="text"  id="postcode" name="postcode" value="<?php echo $postcode; ?>" />
            </div>
            <div class="col-lg-3 col-xs-6" >
              <span><a class="button" id="postcodelookup">FIND</a></span>              
            </div>
             <span class="error" id="error_postcode"> <?php if ($error_postcode) { echo $error_postcode;  } ?></span>
          </div>
        </div>
       <div class="form-group" id="reg_addr_div" style="display:none;">
            <label class="col-lg-4 control-label"><span>Address</label>
                <div class="col-lg-6">
                    <select style="width:100%;" id="sel_reg_address"></select>
                </div>
         </div>
        <div class="form-group">
          <label class="col-lg-4 control-label"> <?php echo $entry_address_1; ?><span class="required">*</span></label>
          <div class="col-lg-6"><input  type="text" name="address_1" id="address_1" value="<?php echo $address_1; ?>" />
            <?php if ($error_address_1) { ?>
            <span class="error"><?php echo $error_address_1; ?></span>
            <?php } ?></div>
        </div>
       <div class="form-group">
          <label class="col-lg-4 control-label"><?php echo $entry_address_2; ?></label>
          <div class="col-lg-6"><input type="text" name="address_2" value="<?php echo $address_2; ?>" /></div>
        </div>
        <div class="form-group">
          <label class="col-lg-4 control-label"> <?php echo $entry_city; ?><span class="required">*</span></label>
          <div class="col-lg-6"><input type="text" id="city" name="city" value="<?php echo $city; ?>" />
            <?php if ($error_city) { ?>
            <span class="error"><?php echo $error_city; ?></span>
            <?php } ?></div>
        </div>
        <input type="hidden" name="company_id" value="<?php echo $company_id; ?>" />
        <input type="hidden" name="company" value="<?php echo $company; ?>" />
        <input type="hidden" name="country_id" value="<?php echo $country_id; ?>" />
        <input type="hidden" name="zone_id" value="<?php echo $zone_id; ?>" />
        
        <div class="form-group">
          <label class="col-lg-4 control-label"><?php echo $entry_default; ?></label>
          <div class="col-lg-6"><?php if ($default) { ?>
            <input type="radio" name="default" value="1" checked="checked" />
            <?php echo $text_yes; ?>
            <input type="radio" name="default" value="0" />
            <?php echo $text_no; ?>
            <?php } else { ?>
            <input type="radio" name="default" value="1" />
            <?php echo $text_yes; ?>
            <input type="radio" name="default" value="0" checked="checked" />
            <?php echo $text_no; ?>
            <?php } ?></div>
        </div>
     
    </div>
    <div class="col-lg-7 buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
      <div class="right">
        <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary" />
      </div>
    </div>
  </form>
  <?php echo $content_bottom; ?>

</div>
<script type="text/javascript"><!--
function postcode_formater(postcode) {
    postcode = postcode.replace(/ /g,""); 
    l=postcode.slice(-3);  
    f=postcode.slice(0,(postcode.length - 3));
    postcode =  f + ' ' +l;
    postcode = postcode.toUpperCase();
    return postcode;
}    
$('select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=account/address/country&country_id=' + this.value,
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
                        var obj = jQuery.parseJSON(json['address']);
                        var optn = '';
                        $.each(obj, function(key,value) {
                            optn+='<option value="'+value.postcode+'--'+value.address+'--'+value.city+'">'+value.address+','+value.city+'</option>';
                        });
                        $('#error_postcode').html('');
                        $('#sel_reg_address').append(optn);
                        $('#reg_addr_div').show();
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
//--></script> 
<?php echo $column_right; ?>
<?php echo $footer; ?>