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
            <form id="resetPwdForm" method="post" class="form-horizontal"  data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
                      data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                      data-bv-feedbackicons-validating="glyphicon glyphicon-refresh" >
            
            <div class="col-sm-3"  >&nbsp;</div>
            <div id="resetPassword" class="resetPassword col-sm-7" align="center">
            
              <h2><?php echo $text_rest_password; ?></h2>
            
              	<br />
				<p><?php echo $text_email_help; ?></p>
              	<br />
              
                <div class="form-group resetElements">
                    <div class="col-lg-8">
                        <input type="text" class="form-control"  name="email" id="email" value="" placeholder="<?php echo $text_your_email; ?>"  />
                    </div>
                    <div class="col-lg-2" >
                        <input type="button" value="<?php echo $button_submit; ?>" id="button-submit" class="button" />
                    </div>
                </div>
              
			<br /><br />
            </div> 
            
            <div id="reset_emailSent" style="display:none;"  class="col-sm-7" align="center">

				<h2 class="greenText">
                	<i class="fa fa-check-circle fa-3x greenText valignTop" style="margin-top:-15px;"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span class="valignMiddle"  ><?php echo $text_email_sent; ?></span>
                </h2>
                <h5 class="text-center"><?php echo $text_email_sent_help; ?></h5>
                <br/><br/><br/><br/>

            
            </div>
              
            </form>
            
       </div>
		
        <?php echo $content_bottom; ?>
        <div style="clear:both;"></div>
        
    </div>
</div>
<br /><br /><br /><br />


<script src="catalog/view/javascript/bootstrap.min.js"></script> 

<script type="text/javascript" src="catalog/view/javascript/plugins/validator/bootstrapValidator.min.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	

	<!-- Validate the login form when user chooses to login  
    $('#resetPwdForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        fields: {
            email: {
                message: 'Please Enter a valid Email Address',
				trigger: 'blur',
                validators: {
                    notEmpty: {
                        message: 'The email is required and cannot be empty'
                    },
                    emailAddress: {
                        message: 'The input is not a valid email address'
                    },
                    remote: {
                        url: 'index.php?route=checkout/loginetm/validateemail',
                        message: '<?php echo $this->language->get("error_customer_notexists"); ?>'
                    }
                }
            }
        }
    });
  
 
	$(document).delegate('#button-submit', 'click', function() {

		//$('#resetPwdForm').bootstrapValidator('validate');
		
		var bootstrapValidator = $("#resetPwdForm").data('bootstrapValidator');
		bootstrapValidator.validate();
		if(bootstrapValidator.isValid())
		{
	   
	
			$.ajax({
				url: 'index.php?route=account/forgotten/validateReset', 
				type: 'post',
				data: $('.resetPassword input[type=\'text\']'),
				dataType: 'json',
				beforeSend: function() {
				
					$('#button-submit').attr('value', '<?php echo $button_processing; ?>');
					$('#button-submit').attr('disabled', true);
					$('#button-submit').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
				},	
				complete: function() {
					$('#button-order-confirm').attr('disabled', false);
							  //  $('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
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
							$('#button-submit').attr('value', '<?php echo $button_submit; ?>');
							$('#button-submit').attr('disabled', false);
							$('.warning').fadeIn('slow');
						}			
					} 
					else if (json['success']) {
						$('#resetPassword').hide();
						$('#reset_emailSent').show();
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