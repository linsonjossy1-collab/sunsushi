<?php echo $header; ?>
<?php #echo $column_left; ?>
<?php #echo $column_right;?>


<link rel="stylesheet" href="catalog/view/theme/default/stylesheet/css/bootstrap.css"/>
<link rel="stylesheet" href="catalog/view/theme/default/stylesheet/css/bootstrapValidator.min.css" />

<div id="content" style="display: inline-block; width: 100%;" >
	<?php #echo $content_top; ?>
    
    <!-- Title and breadcrumbs -->
    <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        	<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
  
  
  
    <div style="background-color:#fff; padding: 10px 0;">
    
    
        <div align="center" class="col-sm-12" style="border:1px solid #CECDCD; padding-top:10px;" >
            <form id="newPwdForm" method="post" class="form-horizontal"  data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
                      data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                      data-bv-feedbackicons-validating="glyphicon glyphicon-refresh" >
            
            <div class="col-sm-3"  >&nbsp;</div>
            <div id="resetPassword" class="resetPassword col-sm-7" align="center">
            
              <h2><?php echo $text_rest_password; ?></h2>
            
              	<br />
              
                <div class="form-group" >
                    <div class="col-lg-12">
                        <input type="password" class="form-control"  name="newpassword" id="newpassword" value="" placeholder="<?php echo $text_new_password; ?>"  />
                    </div>
                </div>
                
              
                <div class="form-group" >
                    <div class="col-lg-12">
                        <input type="password" class="form-control"  name="confirmpassword" id="confirmpassword" value="" placeholder="<?php echo $title_confirm_password; ?>"  />
                    </div>
                </div>
                
                <div class="form-group" >
                    <div class="col-lg-12">
                    	<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />
                        <input type="hidden" name="verification_code" value="<?php echo $verification_code; ?>" />
                        <input type="button" value="<?php echo $button_reset_password; ?>" id="button-reset" class="button buttonBig" />
                    </div>
                </div>
              
			<br /><br />
            </div> 
            
            <div id="reset_success" style="display:none;"  class="col-sm-7" align="center">

				<h2 class="greenText">
                	<i class="fa fa-check-circle fa-3x greenText valignTop" style="margin-top:-15px;"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span class="valignMiddle"  ><?php echo $text_password_hasbeen_reset; ?></span>
                </h2>
                <h5 class="text-center"><?php echo $text_password_hasbeen_reset_help; ?></h5>
                <br/><br/><br/><br/>

            
            </div>
              
            </form>
            
       </div>
		
        <?php echo $content_bottom; ?>
        <div style="clear:both;"></div>
        
    </div>
</div>
<br /><br /><br /><br />




<script type="text/javascript" src="catalog/view/javascript/plugins/validator/bootstrapValidator.min.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	

	<!-- Validate the login form when user chooses to login  
    $('#newPwdForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        fields: {
            newpassword: {
                message: 'Please Enter password',
				trigger: 'blur',
                validators: {
                    notEmpty: {
                        message: 'Please Enter password'
                    },
                    stringLength: {
                        min: 4,
                        max: 20,
                        message: 'The Password must be between 4 and 20 characters!'
                    },
                    identical: {
                        field: 'confirmpassword',
                        message: 'The password and its confirm are not the same'
                    }
                }
            },
            confirmpassword: {
                message: 'Please Enter confirm password',
				trigger: 'blur',
                validators: {
                    notEmpty: {
                        message: 'Please Enter confirm password'
                    },
                    identical: {
                        field: 'newpassword',
                        message: 'The password and its confirm are not the same'
                    }
                }
            }
        }
    });
  
 
	$(document).delegate('#button-reset', 'click', function() {

		var bootstrapValidator = $("#newPwdForm").data('bootstrapValidator');
		bootstrapValidator.validate();
		if(bootstrapValidator.isValid())
		{

			$.ajax({
				url: 'index.php?route=account/forgotten/validateNewPassword', 
				type: 'post',
				data: $('.resetPassword input[type=\'password\'], .resetPassword input[type=\'hidden\']'),
				dataType: 'json',
				beforeSend: function() {
				
					$('#button-reset').attr('value', '<?php echo $button_processing; ?>');
					$('#button-reset').attr('disabled', true);
					$('#button-reset').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
				},	
				complete: function() {
					$('#button-order-confirm').attr('disabled', false);
							 
					$('.wait').remove();
				},			
				success: function(json) {
					$('.warning, .error').remove();
					
					if (json['redirect']) {
						location = json['redirect'];
					} else if (json['error']) {
						if (json['error']['warning']) {
							$('.resetElements').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
							//$(element).closest('.control-group').removeClass('success').addClass('error');
						}			
						$('#button-submit').attr('value', '<?php echo $button_reset_password; ?>');
						$('#button-submit').attr('disabled', false);
						$('.warning').fadeIn('slow');
					} 
					else if (json['success']) {
						$('#resetPassword').hide();
						$('#reset_success').show();
					}
				}
			});
			
		} else {
			return false;	
		}
		
	});

});

</script>

<?php echo $footer; ?>