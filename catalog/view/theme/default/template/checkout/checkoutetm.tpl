<?php echo $header; ?>
<?php echo $column_left; ?>

<!--<script type="text/javascript" src="catalog/view/javascript/jquery/jspostcode.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/jstelnumbers.js"></script>-->
<link rel="stylesheet" href="catalog/view/theme/default/stylesheet/css/bootstrapValidator.min.css" />
<script type="text/javascript" src="catalog/view/javascript/plugins/validator/bootstrapValidator.min.js"></script>
<div class="also_bought" style="display: <?php echo (isset($upsellstatus))?'block':'none'; ?>">
    <?php echo $also_bought; ?>
</div>
<div id="checkout" style="display: <?php echo (!isset($upsellstatus))?'block':'none'; ?>">
    <?php echo $menushipping; ?>
	<div class="checkout col-sm-12"  id="freeproductdiv">
	<?php echo $freeproducts; ?>
        
	</div>
	<div id="content" class="onepageetm col-sm-12">
	<?php echo $content_top; ?>
	</div>
	<h2>
	<span id="head-title">
		<?php echo $heading_title; ?>
	</span>
</h2>
	<div class="checkout col-sm-12">
	<div class="col-sm-6">
		<div class="checkout col-sm-12 checkout-box" id="ship_left">
			<?php if ($shipping_required) { ?>
			<div id="shipping-method">
				<div class="checkout-content">
					<?php echo $shipping_method; ?>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="finalcart checkout-box col-sm-12">
			<h4 class="col-sm-6"><i class="fa fa-shopping-bag" aria-hidden="true"></i>&nbsp;<span><?php echo $text_cart; ?></span></h4>
           
        <div class="col-sm-6" id="back_button"> <a href="<?php echo $back_menu; ?>"><i class="fa fa-chevron-left"></i>&nbsp;Back to menu</a></div>

			<div id="confirm">
				<div class="checkout-content">
					<?php echo $confirm_content; ?>
				</div>
			</div>
		</div>

	</div>
	<div class="col-sm-6">
            
		<div class="checkout col-sm-12 checkout-box" id="checkout-address">
			<h4 class="col-sm-12"><i class="fa fa-address-card" aria-hidden="true"></i>&nbsp;<span><?php echo $text_checkout_account; ?></span></h4>
			<?php if (!$logged && !isset($dine_in_order)) { ?>
			<!-- If user is not logged in -->
			<div class="panel-group checkout-content" id="payment-address" role="tablist" aria-multiselectable="true">

                            <div class="panel panel-default" <?php echo (!$this->config->get('config_guest_checkout')?'style="display:none"':''); ?>>
					<div class="panel-heading" role="tab" id="panel_guest" data-toggle="collapse" data-parent="#payment-address" href="#collapse_guest" aria-expanded="false"
							aria-controls="collapse_guest">
						<h4 class="panel-title">
							<a class="collapsed allopen" role="button">
								<span>
									<i class="fa fa-file-o" aria-hidden="true"></i>
									<?php echo $text_guest; ?>
								</span>
								<i class="fa fa-arrow-circle-down"></i>
                                                                <i class="fa fa-arrow-circle-up" style="display: none"></i>
							</a>
						</h4>
					</div>
					<div id="collapse_guest" class="panel-collapse collapse" role="tabpanel" aria-labelledby="panel_guest">
						<div class="panel-body">
							<div class="guest-checkout-div">
								<?php echo $guest_content; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="panel_existing_customer" data-toggle="collapse" data-parent="#payment-address" href="#collapse_existing_customer" aria-expanded="false" aria-controls="collapse_existing_customer">
						<h4 class="panel-title">
							<a class="collapsed allopen" role="button" >
								<span>
									<i class="fa fa-sign-in" aria-hidden="true"></i>
									<?php echo $text_existing_customer; ?>
								</span>
								<i class="fa fa-arrow-circle-down"></i>
								<i class="fa fa-arrow-circle-up" style="display: none"></i>
							</a>
						</h4>
					</div>
					<div id="collapse_existing_customer" class="panel-collapse collapse" role="tabpanel" aria-labelledby="panel_existing_customer">
						<div class="panel-body">
							<div class="login-details-div">
								<!-- style="display: none"-->
								

								<?php echo $login_content; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="panel_new_customer"  data-toggle="collapse" data-parent="#payment-address" href="#collapse_new_customer" aria-expanded="false" aria-controls="collapse_new_customer">
						<!-- class="clickable" For clickable divs.-->
						<h4 class="panel-title">
							<a class="collapsed allopen" role="button">
								<span>
									<i class="fa fa-folder-open-o" aria-hidden="true"></i>
									<?php echo $text_new_customer_register; ?>
								</span>
								<i class="fa fa-arrow-circle-down"></i>
								<i class="fa fa-arrow-circle-up" style="display: none"></i>
							</a>
						</h4>
					</div>
					<div id="collapse_new_customer" class="panel-collapse collapse" role="tabpanel" aria-labelledby="panel_new_customer">
						<div class="panel-body">
							<div class="register-details-div">
								
								<?php echo $register_content; ?>
							</div>
						</div>
					</div>
				</div>

			</div>
			<?php } else { ?>

			<!-- If user logged in -->
			<div id="payment-address" class="col-sm-12">
				<?php echo $payment_addressetm_content; ?>
			</div>

			<?php } ?>
		</div>
            <?php if (!isset($dine_in_order)) { ?>
			<div id="coupon-voucher" class="col-sm-12 checkout-box">
				<?php if ($success) { ?>
				<div class="coupon_success">
					<?php echo $success; ?>
					<img src="catalog/view/theme/default/image/close.png" alt="" class="close" />
				</div>
				<?php } ?>
				<?php if ($error_warning) { ?>
				<div class="warning">
					<?php echo $error_warning; ?>
					<img src="catalog/view/theme/default/image/close.png" alt="" class="close" />
				</div>
				<?php } ?>
				<?php if ($coupon_status || $voucher_status || $reward_status) { ?>
					<h4 class="col-sm-12"><i class="fa fa-gift" aria-hidden="true"></i>&nbsp;<span>
                                                <?php echo ($coupon_status)?$label_coupon . ' / ':'';?> <?php echo ($voucher_status)?$label_voucher . ' / ':'';?><?php echo ($reward_status)?$label_reward:''; ?></span>
                                        </h4>
				<div class="cart-module">
					<?php if ($coupon_status) { ?>
					<div id="coupon">
						<h5><?php echo $entry_coupon; ?></h5>
						<input type="text" name="coupon" value="<?php echo $coupon; ?>" id="coupon_box"/>
						<input type="hidden" name="next" value="coupon" /> 
						<input type="button" value="<?php echo $button_coupon; ?>" class="button" id="coupon_button" />
						<input type="button" <?php if(!$coupon) { ?> style='display:none;' <?php } ?> value="Remove" class="button" id="removeCoupon" />						

					</div>
					<?php } ?>
					<?php if ($voucher_status) { ?>
					<div id="voucher">
						<h5><?php echo $entry_voucher; ?></h5>
						<input type="text" name="voucher" value="<?php echo $voucher; ?>" id="voucher_box"/>
						<input type="hidden" name="next" value="voucher" />
						<input type="submit" value="<?php echo $button_voucher; ?>" class="button" id="voucher_button" />
					</div>
					<?php } ?>
					<?php if ($reward_status && (!empty($redeem_freeproducts) || $reward_pointstatus)) { ?>
					<div id="reward" class="content">
						
						<h5><?php echo $entry_reward; ?></h5>&nbsp;

						<div id="redeem_freeproducts_popup">
						<?php if(!empty($redeem_freeproducts)) { ?>  
							<div class="popup_heading" id="heading"><h4>Would you like to add any of these free Items</h4></div>
							<div  class="prod_main_categories">
								<?php
									$min_total = $total_without_reward;
									foreach($redeem_freeproducts as $all_products) {
										if(isset($redeemproducts[$all_products['product_id']])){ 
											$min_total-=$redeemproducts[$all_products['product_id']]['qty'] * $all_products['price'];
										}
									} 
								?>


								<table class="table table-striped" id="redeem_product_table">
								<?php foreach($redeem_freeproducts as $all_products) {
									$cart_qty=$all_products['cart_qty'];
									if(isset($redeemproducts[$all_products['product_id']]['qty'])){ 
										$qty=$redeemproducts[$all_products['product_id']]['qty'];
									} else {
										$qty=0;
									} ?>
								<?php while($cart_qty > 0) { ?>
								<tr>
									<td>
										<input type="checkbox" name="redeem_products[]" class="redeem_products" amt="<?php echo $all_products['amount']; ?>" price="<?php echo $all_products['price']; ?>" value="<?php echo $all_products['product_id']; ?>" <?php if($qty > 0){ echo "checked"; } else if($min_total < $all_products['price']){ echo "disabled"; } ?> />
										<input type="hidden" name="amount[]" value="<?php echo $all_products['amount']; ?>"/>
									</td>
									<td><?php echo $all_products['name']; ?></td>
									<td><?php echo $all_products['amount']; ?> point</td>
								</tr>
								<?php $qty--; $cart_qty--;?>
								<?php } ?> 
								<?php } ?> 
								</table>
									
							</div>
							<div style="clear: both"></div>

						<?php } ?>
						</div>
						<span <?php if(!$reward_pointstatus){ ?> style="display:none; "<?php } ?>>
							<input type="text" name="reward" id="reward_point" value="<?php echo $reward; ?>" />
							<input type="hidden" name="next" value="reward" />
							&nbsp;
						</span>
						<input type="submit" value="<?php echo $button_reward; ?>" class="button" id="reward_button" />

					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<div id="reward_earned" class="cart-module" <?php if(!$earn_reward) { ?> style="display:none;" <?php } ?>>
					<div class="content">
					You will earn <span class='point'> <?php echo $earn_reward; ?> </span> loyalty points for this order.<br>
					<?php if(!$logged) { ?>
					Not a member? Don't worry, you can join for free once the order is complete and still get the points.
					<?php }?>
					</div>
				</div>

			</div>
            <?php } ?>
		<?php if(isset($dine_in_order) && !$logged){ ?>
        <div class="checkout col-sm-12 checkout-box" id="checkout-address">           
            <div class="register" id="gust">
                <form id="GuestDForm" method="post" class="form-horizontal">
                    <div class="register">
                        <h4>Your  Details</h4>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">
                                <span class="required">*</span>
                                 Name 
                            </label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="firstname" id="guest_firstname" value="" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">
                                
                                Lastname
                            </label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="lastname" id="guest_lastname" value="" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">
                                <span class="required">*</span>
                                Email
                            </label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="email" id="guest_email" value="" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">
                                <span class="required">*</span>
                                Telephone
                            </label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="telephone" id="guest_telephone" value="" />
                                <input type="hidden" name="fax" value="" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php } ?>
		<div class="checkout col-sm-12 checkout-box" id="payment_right">
			<div id="payment-method">
				<div class="checkout-content">
					<?php echo $payment_method; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-12 checkout-box">
			<div id="confirmfinalbuttom">
				<div class="checkout-content">
					<?php echo $confirm_buttom; ?>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="progress" style="height: 3px;background-color:white;">
					<div id="order-confirm-progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;height: 3px;"></div>
				</div>
			</div>
		</div>
	
		</div>
	</div>
</div>

<div class="col-sm-3"></div>
<div id="confirmfinalpayment" class="col-sm-6" style="display: none">
	
	<div class="col-sm-12">
		<div class="checkout-content">
        <button type="button" id="confirmfinalpayment-back">Back</button>
		</div>
		<div class="progress" style="height: 3px;background-color:white;">
			<div id="order-confirm2-progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;height: 3px;"></div>
		</div>
	</div>
</div>
<div class="col-sm-3"></div>
<div id="notification"></div>
<?php echo $content_bottom; ?>
<div style="clear:both;"></div>
<div id="terms-popup" class="modal fade" tabindex="-1" role="dialog" data-backdrop="false" aria-hidden="false" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">Close</button>
			</div>
		</div>
    </div>
</div>
<div class="col-md-12" id="payment_method_section_container" style="display:none;">
	<h2>
		<span id="head-title">Payment</span>
	</h2>
	<div class="col-md-3"> &nbsp;</div>
	<div class="col-md-6" id="payment_method_section">
		<div id="payment-loader"></div>
	</div>
	<div class="col-md-3">&nbsp;</div>
</div>

<div id="order-cancel-popup" class="modal fade in" tabindex="-1" role="dialog" data-backdrop="false" aria-hidden="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">Are you Sure to go back to Checkout page</div>
			<div class="modal-body">
				Are you sure?
			</div>
			<div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn" style="float:left">Cancel</button>
				<button type="button" data-dismiss="modal" class="btn btn-primary" onclick='cancelPayment()'>Go Back</button>
				
			</div>
		</div>
	</div>
</div>
<!--
<div class="modal fade" tabindex="-1" role="dialog" data-target=".bs-example-modal-sm" id="mes_mod_postcode" data-backdrop="false">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" onclick="javascript: $('#mes_mod_postcode').modal('hide');" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"> <?php echo $this->config->get('config_name'); ?></h4>
      </div>
      <div class="modal-body">
          <p id="mes_p_postcode">
             No Home Delivery to this Area.
             
          </p>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="javascript: $('#mes_mod_postcode').modal('hide');">OK</button>
      </div>
    </div>
  </div>
</div
-->
<script type="text/javascript"> <!--
 function tipcustomfunction(){
        value = $('#tips').val();
        tipfunction(value);
    }
    
    function tipfunction(value){
        $.ajax({
		url: 'index.php?route=checkout/confirmetm/tips',
                type: 'post',
                data: {tips:value},
                dataType: 'json',
                success: function(json) {
                      if(json['success']) {
                            $.ajax({
					url: 'index.php?route=checkout/confirmetm/finalcart',
					dataType: 'html',
					success: function(html) {
						$('#confirm .checkout-content').html(html);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
                      }

                },
                error: function(xhr, ajaxOptions, thrownError) {
                        //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
        });
    }
 /*
$('#checkout .checkout-content input[name=\'account\']').on('change', function() {

	if ($(this).attr('value') == 'register') {
		$('#payment-address .checkout-heading span').html('<?php echo $text_checkout_account; ?>');
	} else {
		$('#payment-address .checkout-heading span').html('<?php echo $text_checkout_payment_address; ?>');
	}
});

$('.checkout-heading a').on('click', function() {
	$('.checkout-content').slideUp('slow');
	
	$(this).parent().parent().find('.checkout-content').slideDown('slow');
});
*/

<?php if ($noshippingstatus) {  ?>
    $(document).ready(function () {    
       // change_del_req();
       
       setTimeout(function() {
            change_del_req();
        }, 500); 
    });
<?php } ?>
<?php  if ($logged && isset($this->session->data['postcode']) && $this->session->data['postcode']!='') { ?>
    $(document).ready(function () {

        curraddressval = $('#existing_address_id').val();
        address_postcode = $('#payaddress_'+curraddressval+' .address_postcode').val();     
        postcode = '<?php echo $this->session->data['postcode']; ?>';
        //console.log("po-"+address_postcode+'-po2--->>'+postcode);
        //alert(address_postcode+'--->>'+postcode);
        //alert(address_postcode+'--->>'+postcode);
        if(postcode!=address_postcode){
            change_address_type('new');
        }
    });
<?php }  ?>
<?php /* if ($logged) { ?>
		$(document).ready(function () {
			$.ajax({
				url: 'index.php?route=checkout/payment_addressetm',
				dataType: 'html',
				success: function (html) {
					$('#payment-address .checkout-content').html(html);

					// $('#payment-address .checkout-content').slideDown('slow');
				},
				error: function (xhr, ajaxOptions, thrownError) {
					//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});
<?php } */ ?>

/* -- */

var cartitems =[];
  
var currentPanel = '';
var panelHideClicked = 0;
var selected_postcode = <?php echo (isset($postcode))?$postcode:'""'; ?>;
var dine_in_order = <?php echo (isset($dine_in_order))?1:0; ?>;
$(function() {
  $('.collapse').on('shown.bs.collapse', function (e) {
	currentPanel = e.currentTarget.id;       
        $('#'+e.currentTarget.previousElementSibling.id +' .fa-arrow-circle-down').hide();
        $('#'+e.currentTarget.previousElementSibling.id +' .fa-arrow-circle-up').show();
    });
});

$(function() {
  $('.collapse').on('hidden.bs.collapse', function (e) {
	currentPanel = '';
	panelHideClicked = 1;         
        $('#'+e.currentTarget.previousElementSibling.id +' .fa-arrow-circle-down').show();
        $('#'+e.currentTarget.previousElementSibling.id +' .fa-arrow-circle-up').hide();
    });
});

$(function() {
    if (typeof(Storage) !== "undefined") {
        var checkoutls = {};
        if (localStorage.checkoutls) {
            checkoutls = JSON.parse(localStorage.checkoutls);
            if(!checkoutls.currentPanel) {
                    // Login not selected
                    $('#check_commentbox').val(checkoutls.check_commentbox);
            }
            else if(checkoutls.currentPanel == "collapse_new_customer"){
                // checkoutls.currentPanel = currentPanel;
                $('#panel_new_customer').click();
                $('#reg_firstname').val(checkoutls.reg_firstname);
                $('#reg_lastname').val(checkoutls.reg_lastname);
                $('#reg_email').val(checkoutls.reg_email);
                $('#reg_telephone').val(checkoutls.reg_telephone);
                $('#reg_password').val(checkoutls.reg_password);
                $('#reg_confirmPassword').val(checkoutls.reg_confirmPassword);
                $('#reg_postcode').val(checkoutls.reg_postcode);
                $('#reg_address_1').val(checkoutls.reg_address_1);
                $('#reg_address_2').val(checkoutls.reg_address_2);
                $('#address_2').val(checkoutls.address_2);
                $('#reg_city').val(checkoutls.reg_city);
                $('#check_commentbox').val(checkoutls.check_commentbox);
            }
            else if(checkoutls.currentPanel == "collapse_guest"){
                $('#panel_guest').click();
                $('#guest_firstname').val(checkoutls.guest_firstname);
                $('#guest_lastname').val(checkoutls.guest_lastname);
                $('#guest_email').val(checkoutls.guest_email);
                $('#guest_telephone').val(checkoutls.guest_telephone);
                $('#guest_postcode').val(checkoutls.guest_postcode);
                $('#guest_address_1').val(checkoutls.guest_address_1);
                $('#guest_address_2').val(checkoutls.guest_address_2);
                $('#guest_city').val(checkoutls.guest_city);
                $('#check_commentbox').val(checkoutls.check_commentbox);
            }
            $('#check_commentbox').val(checkoutls.check_commentbox);
            if(localStorage.payaddressnew){
                $('#addne_firstname').val(checkoutls.addne_firstname);
                $('#addne_lastname').val(checkoutls.addne_lastname);
                $('#addne_address_1').val(checkoutls.addne_address_1);
                $('#addne_address_2').val(checkoutls.addne_address_2);
                $('#addne_city').val(checkoutls.addne_city);
            }
            if(checkoutls.login){
                try {
               localStorage.removeItem('checkoutls');
                }catch (error) {

                }
                
            }
            //localStorage.removeItem('checkoutls');
        }
    }
});

$(document).ready(function() {
	var validatefields = {
		firstname: {
			message: 'Please Enter the Firstname',
			validators: {
				notEmpty: {
					message: 'Please Enter the Firstname'
				},
				stringLength: {
					min: 2,
					max: 30,
					message: 'The Firstname must be more than 2 and less than 30 characters long'
				},
				regexp: {
					regexp: /^[a-zA-Z]{2,}[a-zA-Z_ ]*$/,
					message: 'The Firstname can only consist of alphabets, space and underscore'
				}
			}
		},
		address_1: {
			message: 'Please Enter Your Address',
			trigger: 'change blur',
			validators: {
				notEmpty: {
					message: 'Please Enter Your Address'
				},
				stringLength: {
					min: 3,
					max: 128,
					message: 'Address must be between 3 and 128 characters!'
				}
			}
		},
		postcode: {
			message: 'Please enter the Post Code',
			trigger: 'blur',
			validators: {
				zipCode: {
					country: 'GB',
					message: 'The value is not valid UK postcode'
				}
			}
		},
	};	
	$('#AddressNewForm').bootstrapValidator({
		message: 'This value is not valid',
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh',
		},
		fields: validatefields
	});
	// Validate the registration form when user chooses to register  
	validatefields['email'] =  {
        verbose : false,
		message: 'Please Enter a valid Email Address',
		validators: {
			notEmpty: {
				message: 'The email is required and cannot be empty'
			},
			emailAddress: {
				message: 'The input is not a valid email address'
			},
			remote: {
				url: 'index.php?route=checkout/registeretm/validateemail',
	            type: 'POST',
                message: 'The email is not available'
			}
		}
	};

	validatefields['telephone'] = {
        verbose : false,
		message: 'Please enter the Phone Number',
		validators: {
                notEmpty: {
				message: 'Please enter the Phone Number'
			}, 
				phone: {
					country: 'GB',
					message: 'Invalid UK telephone'
			}
		}
	};
        
         $('#GuestDForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        fields: validatefields
    });
    
	validatefields['password']  = {
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
				field: 'confirmPassword',
				message: 'The password and its confirm are not the same'
			}
		}
	};
	validatefields['confirm'] = {
		message: 'Please Enter confirm password',
		trigger: 'blur',
		validators: {
			notEmpty: {
				message: 'Please Enter confirm password'
			},
			identical: {
				field: 'password',
				message: 'The password and its confirm are not the same'
			}
		}
	};
    $('#RegisterForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        fields: validatefields
    });
    
    <?php if($delivery_method == "dine.dine") { ?>  
		//delete validatefields['lastname'];
		 delete validatefields['house'];
		delete validatefields['address_1'];
		delete validatefields['postcode'];
	<?php } ?>
    $('#GuestForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
		<?php if($delivery_method == "collect.collect") { ?>
		excluded: '#GuestForm input[name="postcode"], #GuestForm input[name="address_1"]', 
		<?php } ?>
        fields:validatefields
    });

	$('#RegisterForm').bootstrapValidator('revalidateField', $('#reg_email'));
	$('#GuestForm').bootstrapValidator('revalidateField', $('#guest_email'));
	
	 // Validate the login form when user chooses to login  
    $('#login').bootstrapValidator({
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
	            		type: 'POST',
                        message: '<?php echo $this->language->get("error_customer_notregistered"); ?>'
                    }
                }
            },
            password: {
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
                    }
                }
            },
        }
    });
  
	
   

  
});
/* -- */

		// Checkout
		$('#button-account').on('click', function () {

			$.ajax({
				url: 'index.php?route=checkout/' + $('input[name=\'account\']:checked').attr('value'),
				dataType: 'html',
				beforeSend: function () {
					$('#button-account').attr('disabled', true);
					$('#button-account').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
				},
				complete: function () {
					$('#button-account').attr('disabled', false);
					$('.wait').remove();
				},
				success: function (html) {

					$('.warning, .error').remove();

					$('#payment-address .checkout-content').html(html);

					$('#checkout .checkout-content').slideUp('slow');

					$('#payment-address .checkout-content').slideDown('slow');

					$('.checkout-heading a').remove();

					$('#checkout .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
				},
				error: function (xhr, ajaxOptions, thrownError) {
					//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});

	// Login
	$('#button-login').on('click', function () {
		var quickRegisterValidator = $("#login").data('bootstrapValidator');
		quickRegisterValidator.validate();
                var checkoutls = { check_commentbox:$('#check_commentbox').val(),login:true };
                try {
                localStorage.checkoutls = JSON.stringify(checkoutls);
		 }catch (error) {

                }
                
                if(quickRegisterValidator.isValid()){
			$.ajax({
				url: 'index.php?route=checkout/loginetm/validate',
				type: 'post',
				data: $('#checkout #login input[type=\'text\'],#checkout #login input[type=\'password\'], #checkout #login input[type=\'checkbox\']:checked'),
				dataType: 'json',
				beforeSend: function () {
					$('#button-login').attr('disabled', true);
					$('#button-login').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
				},
				complete: function () {
					$('#button-login').attr('disabled', false);
					$('.wait').remove();
				},
				success: function (json) {
					$('.warning, .error').remove();

					if (json['redirect']) {
						if (typeof (json['a1_s1_gkl_']) !== "undefined" && typeof (Storage) !== "undefined") { // if checkbox checked condition
							 try {
                                                        localStorage.setItem('a1_s1_gkl_', json['a1_s1_gkl_']);
                                                    }catch (error) {

                                                    }
                                                    
						}
						location = json['redirect'];
					} else if (json['error']) {
						$('#login').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');

						$('.warning').fadeIn('slow');
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});

	/*
	$('#checkout .checkout-content').slideDown('slow');
	$('#shipping-method .checkout-content').slideDown('slow');
	$('#payment-method .checkout-content').slideDown('slow');
	$('#payment-address .checkout-content').slideDown('slow');
	$('#confirm .checkout-content').slideDown('slow');
	 */
	 $('#addne_postcode').focusout( function () {
		search_address_by_postcode('AddressNewForm');
	 });
	 $('#guest_postcode').focusout( function () {
		search_address_by_postcode('GuestForm');
	 });
	 $('input#reg_postcode').keypress(function(e) {
            if (e.keyCode == '13') {
              e.preventDefault();                          
             }
          }); 
	 $('#reg_postcode').focusout( function () {
            search_address_by_postcode('RegisterForm');
	 });

	/*
	$('#postcode').on('change', function () {
		$('#postcode').val(postcode_formater($('#postcode').val()));
		$.ajax({
			url: 'index.php?route=checkout/payment_addressetm/newpostcode',
			type: 'post',
			data: { 'postcode': $('#postcode').val() },
			dataType: 'json',
			beforeSend: function () {
				$('#button-shipping-method').attr('disabled', true);
				$('#button-shipping-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},
			complete: function () {
				$('#button-shipping-method').attr('disabled', false);
				$('.wait').remove();
			},
			success: function (json) {
				$('.warning, .error').remove();

				if (json['error']) {
					if (json['error']['postcode']) {
						$('#payment-address input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
					}
					$('#postcode').focus();
				} else {

					$.ajax({
						url: 'index.php?route=checkout/shipping_methodetm',
						dataType: 'html',
						success: function (html) {
							$('#shipping-method .checkout-content').html(html);
							$('#shipping-method input[type=\'radio\']:checked').click();

						},
						error: function (xhr, ajaxOptions, thrownError) {
							//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});


				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});

	});
	*/
	
	
	$('#order_requested_time').on('change', function () {
		$.ajax({
			url: 'index.php?route=checkout/shipping_methodetm/setrequestedtime',
			type: 'post',
			data: $('#order_requested_time'),
			dataType: 'json',
			success: function (json) {
				//  $('#confirm .checkout-content').html(html);

			},
			error: function (xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	$('#payment-method input[type=\'radio\']').on('click', function () {
		$.ajax({
			url: 'index.php?route=checkout/payment_methodetm/validate',
			type: 'post',
			data: $('#payment-method input[type=\'radio\']:checked, #payment-method input[type=\'checkbox\']:checked, #payment-method textarea'),
			dataType: 'json',
			beforeSend: function () {
				$('#button-payment-method').attr('disabled', true);
				$('#button-payment-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},
			complete: function () {
				$('#button-payment-method').attr('disabled', false);
				$('.wait').remove();
			},
			success: function (json) {
				$('.warning, .error').remove();
				loadearnedreward();
				if (json['redirect']) {
					location = json['redirect'];
				} else if (json['error']) {
					if (json['error']['warning']) {
						$('#payment-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

						$('.warning').fadeIn('slow');
					}
				} else {
					// location.reload();
					$.ajax({
						url: 'index.php?route=checkout/confirmetm/finalcart',
						dataType: 'html',
						success: function (html) {
							$('#confirm .checkout-content').html(html);
						},
						error: function (xhr, ajaxOptions, thrownError) {
							//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
	$('#confirmfinalpayment-back').on('click', function () {
		orderconformprogress(0);
		$('#confirmfinalpayment .checkout-content').html('');
		$('#confirmfinalpayment').hide();
		$('#checkout').show();
		$('#button-order-confirm').attr('disabled', false);
		$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
	});

	$('#button-order-confirm').on('click', function () {
		orderconformprogress(10);
                /*
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                  event: "begin_checkout",
                  ecommerce: {
                    currency: "GBP",
                    value: '<?php echo $order_total; ?>',
                    items: cartitems
                  }
                });
                */
		$.ajax({
			url: 'index.php?route=checkout/confirmetm/validate',
			type: 'post',
			data: $('#confirmfinalbuttom input[type=\'checkbox\']:checked'),
			dataType: 'json',
			beforeSend: function () {

				$('#button-order-confirm').attr('value', '<?php echo $button_processing; ?>');
				$('#button-order-confirm').attr('disabled', true);
				$('#button-order-confirm').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},
			complete: function () {
				// $('#button-order-confirm').attr('disabled', false);
				//  $('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
				$('.wait').remove();
			},
			success: function (json) {
				$('.warning, .error').remove();
				orderconformprogress(20);
				if (json['redirect']) {
					location = json['redirect'];
				} else if (json['error']) {
					if (json['error']['warning']) {
						$('#confirmfinalbuttom .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
						$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
						$('.warning').fadeIn('slow');
						$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
						$('#button-order-confirm').attr('disabled', false);
						orderconformprogress(0);
						return false;
					}
				} else {
                        
					<?php if (!$logged) { ?>
						
						var ajax_user_url = 'index.php?route=checkout/registeretm/validate';
						var ajax_data = '';

						if(!currentPanel) { 
							/* if(!panelHideClicked) {
								currentPanel = 'collapse_new_customer'; 
							} else { */
								currentPanel = '';
							//}
						}
						//dine_in_order
						if(!currentPanel && dine_in_order==0) {
							//alert("Please select the type of customer you are!");
							// $('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
							// $('.warning').fadeIn('slow');
							$('#checkout-address').prepend('<div class="warning" style="display: none;"><?php echo $text_customer_type_warning; ?>&nbsp;<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
							$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
							$('.warning').fadeIn('slow');
							$('#button-order-confirm').attr('disabled', false);
							$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
							orderconformprogress(0);
							return false;
						}
	
						// Validate the registration process
						if(currentPanel == "collapse_new_customer") 
						{ 
							// Validate the registration form  
							//$('#RegisterForm').bootstrapValidator('validate');
							var quickRegisterValidator = $("#RegisterForm").data('bootstrapValidator');
							quickRegisterValidator.validate();
							if(quickRegisterValidator.isValid())
							{
						  
								var email=$("#email").val();
								var telephone=$("#telephone").val();
								var password=$("#password").val();
								var confirm=$("#confirm").val();
								var address_1=$("#address_1").val();
								var city=$("#city").val();
								var zone_id=$("#zone_id").val();
								//var postcode = $("#postcode").val();
								var postcode = $('#RegisterForm input[name=\'postcode\']').val();
								postcode = postcode_formater(postcode);
								$('#postcode').val(postcode);
								
								ajax_user_url = 'index.php?route=checkout/registeretm/validate';
								ajax_data = '#RegisterForm input[name=\'email\'], #RegisterForm input[type=\'text\'], #RegisterForm input[type=\'password\'], #RegisterForm input[type=\'checkbox\']:checked, #RegisterForm input[type=\'radio\']:checked, #RegisterForm input[type=\'hidden\'], #RegisterForm select';
								//ajax_data = '#RegisterForm .register input[name=\'email\'], #RegisterForm .register input[type=\'text\'], #RegisterForm .register input[type=\'password\'], #RegisterForm .register input[type=\'checkbox\']:checked, #RegisterForm .register input[type=\'radio\']:checked, #RegisterForm .register input[type=\'hidden\'], #RegisterForm .register select';
								
								// $('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
								// $('.warning').fadeIn('slow');
							}
							else
							{
								$('#button-order-confirm').attr('disabled', false); 
								$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
								$('#RegisterForm').prepend('<div class="warning" style="display: none;"><?php echo $text_checkout_from_warning; ?>&nbsp;<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
								$('.warning').fadeIn('slow');
								$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
								orderconformprogress(0);
								return false;	
							}
						}
						// END validating registration process
						
						else if(currentPanel == "collapse_existing_customer") 
						{	
							// Validate Existing customer Login 
                                                        // Validate Existing customer Login 
                                                        $('.wait').remove();
                                                        $('#login').prepend('<div class="warning" style="display: none;"><?php echo $text_customer_login_warning; ?>&nbsp;<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                                                        $('#button-order-confirm').attr('disabled', false); 
                                                        $('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
                                                        $('.warning').fadeIn('slow');
                                                        $('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
                                                        orderconformprogress(0); 
                                                        
							$('#quickLoginForm').bootstrapValidator('validate');
							var quickLoginValidator = $("#quickLoginForm").data('bootstrapValidator');
							quickLoginValidator.validate();
							if(quickLoginValidator.isValid())
							{
						  
								ajax_user_url = 'index.php?route=checkout/loginetm/validate';
								
								ajax_data = '#login input[name=\'email\'], #login input[type=\'password\'], #login input[type=\'checkbox\']:checked';
								
								$('#login').prepend('<div class="warning" style="display: none;"><?php echo $text_customer_login_warning; ?>&nbsp;<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
								$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
								$('#button-order-confirm').attr('disabled', false);
								$('.warning').fadeIn('slow');
								$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
								orderconformprogress(0);
								return false;
							}
							else {
								$('#button-order-confirm').attr('disabled', false); 
								$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
								$('#login').prepend('<div class="warning" style="display: none;"><?php echo $text_customer_login_warning; ?>&nbsp;<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
								$('.warning').fadeIn('slow');
								$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
								orderconformprogress(0);
								return false;
							}
							
						}
						
						else if(currentPanel == "collapse_guest") 
						{	
							// Validate Guest Form
							$('#GuestForm').bootstrapValidator('validate');
							var quickGuestValidator = $("#GuestForm").data('bootstrapValidator');
							quickGuestValidator.validate();
						  
							if(quickGuestValidator.isValid())
							{
								validatePostcode('GuestForm',$('#guest_postcode').val());
								
								ajax_user_url = 'index.php?route=checkout/guest/validate';
								
								ajax_data = '#GuestForm .register input[name=\'email\'], #GuestForm .register input[type=\'text\'], #GuestForm .register input[type=\'password\'], #GuestForm .register input[type=\'checkbox\']:checked, #GuestForm .register input[type=\'radio\']:checked, #GuestForm .register input[type=\'hidden\'], #GuestForm .register select';
								
								// $('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
								// $('.warning').fadeIn('slow');
							}
							else
							{
								$('#button-order-confirm').attr('disabled', false); 
								$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');

								$('#GuestForm').prepend('<div class="warning" style="display: none;"><?php echo $text_checkout_from_warning; ?>&nbsp;<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
								$('.warning').fadeIn('slow');
								$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');

								orderconformprogress(0);
								return false;
							}

						}
                                                
					if(!currentPanel && dine_in_order==1){ 
                                                   $('#GuestDForm').bootstrapValidator('validate');
							var quickGuestValidator = $("#GuestDForm").data('bootstrapValidator');
							quickGuestValidator.validate();
						  
							if(quickGuestValidator.isValid())
							{
								validatePostcode('#postcode');
								
								ajax_user_url = 'index.php?route=checkout/guest/validatedinein';
                                                                ajax_data = '#GuestDForm .register input[name=\'email\'], #GuestDForm .register input[type=\'text\'], #GuestDForm .register input[type=\'password\'],#GuestDForm .register input[type=\'hidden\']';
								
								// $('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
								// $('.warning').fadeIn('slow');
							}
							else
							{
								$('#button-order-confirm').attr('disabled', false); 
								$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');

								$('#GuestDForm').prepend('<div class="warning" style="display: none;"><?php echo $text_checkout_from_warning; ?>&nbsp;<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
								$('.warning').fadeIn('slow');
								$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');

								orderconformprogress(0);
								return false;
							}
                                                        
                                                }							
				<?php } else { ?>
					
					var selectedVal = $('#payment-address input[name=payment_address]').val();
					
					if(selectedVal == "new") 
					{
						// Validate New address Form
						$('#AddressNewForm').bootstrapValidator('validate');
						var QuickAddressNewForm = $("#AddressNewForm").data('bootstrapValidator');
							QuickAddressNewForm.validate();
							if(!QuickAddressNewForm.isValid()) {
						$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
								$('#button-order-confirm').attr('disabled', false);
								orderconformprogress(0);
								$('#payment-new').prepend('<div class="warning" style="display: none;">Please check form carefully... &nbsp;<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
						$('.warning').fadeIn('slow');
								$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
								return false;
					} 
							
						// validatePostcode('#postcode'); #TODO postcode validation
						$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
						//$('.warning').fadeIn('slow');
					}
							
					ajax_user_url = 'index.php?route=checkout/payment_addressetm/validate';
					ajax_data = '#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select';
						
				
				<?php } ?>
				//alert(ajax_user_url);
				orderconformprogress(30);
						$.ajax({

							url: ajax_user_url,
							data: $(ajax_data),
							type: 'post',
							// data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
								dataType: 'json',
									beforeSend: function() {
										$('#button-order-confirm').attr('value', '<?php echo $button_processing; ?>');
										$('#button-order-confirm').attr('disabled', true);

										$('#button-order-confirm').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
									},
					complete: function() {
						// $('#button-order-confirm').attr('disabled', false); 
						// $('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
						$('.wait').remove();
					},
					success: function(json) {
						orderconformprogress(30);
						$('.warning, .error').remove();

						if (json['redirect']) {
							location = json['redirect'];
						} else if (json['error']) {
							$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
							$('#button-order-confirm').attr('disabled', false);
							orderconformprogress(0);
							if (json['error']['warning']) {
								$('#RegisterForm').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
								$('#payment-new').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
								$('.warning').fadeIn('slow');
								$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
							}

							if (json['error']['firstname']) {
								$('#payment-address input[name=\'firstname\']').after('<span class="error">' + json['error']['firstname'] + '</span>');
							}

							if (json['error']['lastname']) {
								$('#payment-address input[name=\'lastname\']').after('<span class="error">' + json['error']['lastname'] + '</span>');
							}

							if (json['error']['email']) {
								$('#payment-address input[name=\'email\']').after('<span class="error">' + json['error']['email'] + '</span>');
							}

							if (json['error']['telephone']) {
								$('#payment-address input[name=\'telephone\']').after('<span class="error">' + json['error']['telephone'] + '</span>');
							}

							if (json['error']['company_id']) {
								$('#payment-address input[name=\'company_id\']').after('<span class="error">' + json['error']['company_id'] + '</span>');
							}

							if (json['error']['tax_id']) {
								$('#payment-address input[name=\'tax_id\']').after('<span class="error">' + json['error']['tax_id'] + '</span>');
							}

							if (json['error']['address_1']) {
								$('#payment-address input[name=\'address_1\']').after('<span class="error">' + json['error']['address_1'] + '</span>');
							}

							if (json['error']['city']) {
								$('#payment-address input[name=\'city\']').after('<span class="error">' + json['error']['city'] + '</span>');
							}

							if (json['error']['postcode']) {
								$('#payment-address input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
							}

							if (json['error']['country']) {
								$('#payment-address select[name=\'country_id\']').after('<span class="error">' + json['error']['country'] + '</span>');
							}

							if (json['error']['zone']) {
								$('#payment-address select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone'] + '</span>');
							}

							if (json['error']['password']) {
								$('#payment-address input[name=\'password\']').after('<span class="error">' + json['error']['password'] + '</span>');
							}

							if (json['error']['confirm']) {
								$('#payment-address input[name=\'confirm\']').after('<span class="error">' + json['error']['confirm'] + '</span>');
							}
						} else {
							// for shipping
							// alert('shipping');
							$.ajax({
								url: 'index.php?route=checkout/shipping_methodetm/validate',
								type: 'post',
								data: $('#shipping-method input[type=\'hidden\'], #shipping-method textarea'),
								dataType: 'json',
								beforeSend: function () {
									$('#button-shipping-method').attr('disabled', true);
									$('#button-shipping-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
								},
								complete: function () {
									$('#button-shipping-method').attr('disabled', false);
									$('.wait').remove();
								},
								success: function (json) {
									$('.warning, .error').remove();
									orderconformprogress(40);
									if (json['redirect']) {
										location = json['redirect'];
									} else if (json['error']) {
										$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
										$('#button-order-confirm').attr('disabled', false);
										orderconformprogress(0);
										if (json['error']['warning']) {
											$('#shipping-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
											$('.warning').fadeIn('slow');
											$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
										}
									} else {
										// for payment
										// alert('payment');
										$.ajax({
											url: 'index.php?route=checkout/payment_methodetm/validate',
											type: 'post',
											data: $('#payment-method input[type=\'radio\']:checked, #payment-method input[type=\'checkbox\']:checked, #payment-method textarea'),
											dataType: 'json',
											beforeSend: function () {
												$('#button-payment-method').attr('disabled', true);
												$('#button-payment-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
											},
											complete: function () {
												$('#button-payment-method').attr('disabled', false);
												$('.wait').remove();
											},
											success: function (json) {
												orderconformprogress(50);
												$('.warning, .error').remove();
												loadearnedreward();

												if (json['redirect']) {
													location = json['redirect'];
												} else if (json['error']) {
													$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
													$('#button-order-confirm').attr('disabled', false);
													orderconformprogress(0);
													if (json['error']['warning']) {
														$('#payment-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
														$('.warning').fadeIn('slow');
														$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
													}
												}
												else {
													// alert('checkActiveOrders');
													/*$.ajax({
														url: 'index.php?route=checkout/confirmetm/checkActiveOrders',
														type: 'post',
														dataType: 'json',
														beforeSend: function () {
															$('#button-order-confirm').attr('value', '<?php echo $button_processing; ?>');
															$('#button-order-confirm').attr('disabled', true);
															$('#button-order-confirm').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
														},
														complete: function () {
															$('.wait').remove();
														},
														success: function (json) {
															orderconformprogress(70);
															if (json.status == true) {
																// alert('confirmwindow');
																$.ajax({
																	url: 'index.php?route=checkout/confirmetm',
																	dataType: 'html',
																	success: function (html) {
																		orderconformprogress(90);

																		$('#confirmfinalpayment .checkout-content').html(html);
																		$('#confirmfinalpayment').show();
																		$('#checkout').hide();

																		$('#confirmfinalpayment .button').attr('value', '<?php echo $button_continue; ?>'); 
																		$('#confirmfinalpayment .button').before('<div class="loader"></div>');
																		orderconformprogress(92);
																		$('#confirmfinalpayment .button').click();
																		$('#confirmfinalpayment .button').attr('disabled', true);
																		orderconformprogress(96);
																	},
																	error: function (xhr, ajaxOptions, thrownError) {
																		//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
																	}
																});
																// $('#confirmfinalbuttom .checkout-content').prepend('<div class="warning" style="display: none;">' + json.msg + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
																// $('.warning').fadeIn('slow');
																// $('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
																// $('#confirmfinalbuttom .button').attr('value', '<?php echo $button_continue; ?>');
																// $('#confirmfinalbuttom .button').attr('disabled', false);
															}
															else {
																$('#confirmfinalbuttom .checkout-content').prepend('<div class="warning" style="display: none;">' + json.msg + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
																$('.warning').fadeIn('slow');
																$('html, body').animate({ scrollTop: $('.warning').offset().top }, 'slow');
																$('#confirmfinalbuttom .button').attr('value', '<?php echo $button_continue; ?>');
																$('#confirmfinalbuttom .button').attr('disabled', false);
																
																orderconformprogress(0);
															}
														}
													});
													*/
													// alert('confirmwindow');
													$.ajax({
														url: 'index.php?route=checkout/confirmetm',
														dataType: 'html',
														beforeSend: function(){															
															$("#payment_method_section_container").show();
															$('#confirmfinalbuttom .checkout-content').hide();
															$("#order-confirm-progress-bar").hide();
															$('#checkout').hide();
															$("#payment_method_section").css({'background':'#cccccc', 'border-radius':'10px'});
															$("#payment-loader").addClass('loader');

														},
														success: function(html) {
															$("#payment-loader").addClass('loader');
															$("#payment_method_section").html(html);                                                                                                                        
                                                                                                                        if(currentPanel == "collapse_guest"){
                                                                                                                               setdatatolocal();
                                                                                                                        }
															if($('#payment_method_section .payment').find('.button').length !==0)
															{
																$('#payment_method_section .button').click();
																$('#payment_method_section .button').hide();
																$('#payment_method_section .button').attr('disabled', true);																
																$("#payment_method_section").append('<div id="payment-loader"></div>');
																$("#payment-loader").addClass('loader');
															}
															else{
																$('#payment_method_section .payment').find(':submit').addClass('pull-right');
																$('#payment_method_section .payment').find(':submit').css('margin-top','-25px');
																$("#payment_method_section").css({'background':'#cccccc', 'border-radius':'10px'});
																$("#payment_method_section .payment").before().append('<div id="payment_cancel_button" data-toggle="modal" data-target="#order-cancel-popup" class="pull-left"><a class="btn btn-default text-center" style="margin-top: -35px;">Go Back</a></div>');
																$("#payment-loader").removeClass('loader');
															}
															window.scrollTo(50, 50);
															
															// $('#confirmfinalbuttom .checkout-content').html(html);
															// $('#confirmfinalbuttom .button').attr('value', '<?php echo $button_continue; ?>');
															// $('#confirmfinalbuttom .button').click();
															// $('#confirmfinalbuttom .button').attr('disabled', true);
														},
														error: function(xhr, ajaxOptions, thrownError) {
																//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
														}
													});
												}

											},
											error: function (xhr, ajaxOptions, thrownError) {
												//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
											}
										});
									}
								},
								error: function (xhr, ajaxOptions, thrownError) {
									//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});



						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});

	}
		},
		error: function (xhr, ajaxOptions, thrownError) {
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

});
function cancelPayment()
{
	$('#checkout').show();
	$("#payment_method_section").html('<div id="payment-loader"></div>');
	$("#payment_method_section_container").hide();
	$('#button-order-confirm').attr('disabled', false);
	$('#button-order-confirm').attr('value', '<?php echo $button_continue; ?>');
	$('#confirmfinalbuttom .checkout-content').show(); 
	$("html").scrollTop(600);
}
	$("#removeCoupon").on('click',function() {
		$("#coupon_box").val('');
		$("#coupon_button").trigger('click');
	});

	$('#coupon_button').on('click', function () {
		$('#coupon-voucher .warning').remove();
		$('#coupon-voucher .success').remove();
		$('#coupon-voucher .coupon_success').remove();
		$('#coupon_button').attr('disabled', true);
		$('#coupon_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		$.ajax({
			url: 'index.php?route=checkout/checkoutetm/validateCoupon',
			type: 'post',
			data: $('#coupon input[type=\'text\'], #coupon input[type=\'hidden\']'),
			dataType: 'json',
			success: function (json) {
				//  $('#confirm .checkout-content').html(html);
				if (json['error']) {
					if (json['error']['warning']) {
						// $('#coupon-voucher').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
						$('#notification').html('<div class="warning">' +  json['error']['warning']+ '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
						$('.warning').fadeIn('slow');
					}
				}
				else if (json['success']) {
                                        $('#freeproductdiv').load("index.php?route=module/freeproducts");
					// $('#coupon-voucher').prepend('<div class="coupon_success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					//$('.success').fadeIn('slow');

					$('#notification').html('<div class="success">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					$('.success').delay(2000).fadeOut();

					if(json['c']) {
						$('#removeCoupon').show();
					} else {
						$('#removeCoupon').hide();
					}

					$.ajax({
						url: 'index.php?route=checkout/confirmetm/finalcart',
						dataType: 'html',
						success: function (html) {
							$('#confirm .checkout-content').html(html);
							$.ajax({
								url: 'index.php?route=checkout/shipping_methodetm/requestedtime',
								type: 'post',
								data: $('#shipping-method input[type=\'radio\']:checked'),
								dataType: 'html',
								success: function (html) {
									$('#shipping-method #requested_time').html(html);

								},
								error: function (xhr, ajaxOptions, thrownError) {
									//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});
						},
						error: function (xhr, ajaxOptions, thrownError) {
							//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
					$.ajax({
						url: 'index.php?route=checkout/payment_methodetm',
						dataType: 'html',
						success: function (html) {
							$('#payment-method .checkout-content').html(html);

						},
						error: function (xhr, ajaxOptions, thrownError) {
							//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
					$('#freepdiv').load("index.php?route=module/freeproducts");
				}

			},
			complete: function () {
				$('#coupon_button').attr('disabled', false);
				$('.wait').remove();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
	//voucher
	$('#voucher_button').on('click', function () {
		$('#coupon-voucher .warning').remove();
		$('#coupon-voucher .success').remove();
		$.ajax({
			url: 'index.php?route=checkout/checkoutetm/validateVoucher',
			type: 'post',
			data: $('#voucher input[type=\'text\'], #voucher input[type=\'hidden\']'),
			dataType: 'json',
			success: function (json) {
				//  $('#confirm .checkout-content').html(html);
				if (json['error']) {
					if (json['error']['warning']) {
						// $('#coupon-voucher').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
						$('#notification').html('<div class="warning">' +  json['error']['warning']+ '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
						$('.warning').fadeIn('slow');
					}
				}
				else if (json['success']) {

					//$('#coupon-voucher').prepend('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

					//$('.success').fadeIn('slow');
					$('#notification').html('<div class="success">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					$('.success').delay(2000).fadeOut();

					$.ajax({
						url: 'index.php?route=checkout/confirmetm/finalcart',
						dataType: 'html',
						success: function (html) {
							$('#confirm .checkout-content').html(html);
						},
						error: function (xhr, ajaxOptions, thrownError) {
							//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				}

			},
			error: function (xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	$('#reward_button').on('click', function () {
		$('#coupon-voucher .warning').remove();
		$('#coupon-voucher .success').remove();
		$.ajax({
			url: 'index.php?route=checkout/checkoutetm/validateReward',
			type: 'post',
			data: $('#reward input[type=\'text\'], #reward input[type=\'hidden\'], #reward input[type=\'checkbox\']:checked'),
			dataType: 'json',
			success: function (json) {
				//  $('#confirm .checkout-content').html(html);
				if (json['error']) {
					if (json['error']['warning']) {
						//$('#coupon-voucher').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
						$('#notification').html('<div class="warning">' +  json['error']['warning']+ '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
						$('.warning').fadeIn('slow');
					}
				}
				else if (json['success']) {

					// $('#coupon-voucher').prepend('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

					//$('.success').fadeIn('slow');
					$('#notification').html('<div class="success">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					$('.success').delay(2000).fadeOut();

					$.ajax({
						url: 'index.php?route=checkout/confirmetm/finalcart',
						dataType: 'html',
						success: function (html) {
							$('#confirm .checkout-content').html(html);							
							loadearnedreward();
						},
						error: function (xhr, ajaxOptions, thrownError) {
							//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				}

			},
			error: function (xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	function postcode_formater(postcode) {
		// console.log(postcode);
		postcode = postcode.replace(/ /g, "");

		var l = postcode.slice(-3);
		var f = postcode.slice(0, (postcode.length - 3));
		postcode = f + ' ' + l;
		postcode = postcode.toUpperCase();

		return postcode;
	}


		//link-agree
		$(document).ready(function () {
			$(document).on('click', '#link-agree', function () {
				// alert("?asas");
                                $('#terms-popup .modal-body').html('')
				$('#terms-popup .modal-body').load('<?php echo $link_agree; ?>', function (data) {
					//alert(html); 
					//if(html){
					$('#terms-popup').modal('show');
                                        $('#terms-popup .modal-body').css('overflow-y', 'auto'); 
                                        $('#terms-popup .modal-body').css('max-height', $(window).height() * 0.8);
					//}
				})
			});
			$(document).on('click', '#link-agree-privacy', function () {
                                    $('#terms-popup .modal-body').html('')
                                    $('#terms-popup .modal-body').load('<?php echo $link_agree_privacy; ?>', function (data) {
					$('#terms-popup').modal('show');
                                        $('#terms-popup .modal-body').css('overflow-y', 'auto'); 
                                        $('#terms-popup .modal-body').css('max-height', $(window).height() * 0.8);
                                    });
			});
		});
                
                
function setaddressdata (formid){
    if (localStorage.checkoutls) {
		checkoutls = JSON.parse(localStorage.checkoutls);
		if(checkoutls.currentPanel!=undefined) {
			if(checkoutls.currentPanel == "collapse_new_customer"){
				$('#reg_address_1').val(checkoutls.reg_address_1);
				$('#reg_city').val(checkoutls.reg_city);
			}else if(checkoutls.currentPanel == "collapse_guest"){
				$('#guest_address_1').val(checkoutls.guest_address_1);
				$('#guest_city').val(checkoutls.guest_city);
			}		
		}
               if(formid=='AddressNewForm' && localStorage.payaddressnew){
                    $('#addne_address_1').val(checkoutls.addne_address_1);           
                    $('#addne_city').val(checkoutls.addne_city);
                } 
    }
     try {
        localStorage.removeItem('checkoutls');
    }catch (error) {

    }
    
    
}                 
function search_address_by_postcode(formid){  
	var   addr_postcode = postcode_formater($('#'+ formid +' input[name=postcode]').val());
        $('#'+ formid +' .reg_addr_div').hide();
        if(addr_postcode) {
	$('#'+ formid +' input[name=postcode]').val(addr_postcode);
		$.ajax({
		url: 'index.php?route=module/postcode_lookup/postcodelookup',
		type: 'get',
		data: {postcode: addr_postcode},
		dataType: 'json',
            }).done(function (json) {
			if(json['error']){
				$('#'+ formid +' .error_postcode').after('No matching Postcode in our database');
				$('#'+ formid +' .reg_addr_div').hide();	
			} else if(json['address'] && json['address']!='[]') {
                    if(typeof  json['address'] ==='object') {
                    var obj = json['address'];
                    } else {                        
				var obj = jQuery.parseJSON(json['address']);
                    }
                    var optn = '<option value="">Choose Address</option>';
				$.each(obj, function(key,value) {
					optn+='<option value="'+value.postcode+'--'+value.address+'--'+value.city+'">'+value.address+','+value.city+'</option>';
				});
                    $('#'+formid+' .sel_reg_address').html(optn);
                    $('#'+ formid +' .reg_addr_div').show();	
				$('#'+ formid +' .error_postcode').html('');
                    $('#'+ formid +' input[name=address_1]').val('').trigger('change');
				$('#'+ formid +' input[name=city]').val('');
                } else {
                    if (json['street'] && json['city']) {
					$('#'+ formid +' .error_postcode').html('');
                    } else {
				$('#'+ formid +' .error_postcode').html('Please enter details manually');
                    }
                    $('#'+ formid +' input[name=address_1]').val(json['street']).trigger('change');
				$('#'+ formid +' input[name=city]').val(json['city']);
			} 
                setaddressdata (formid);
			// Check postcode has delivery
			<?php if($delivery_method == "home.home") { ?>
				if (selected_postcode != addr_postcode ) { // postcode changes.
					validatePostcode(formid,addr_postcode);
				}
			<?php } ?>
            }).fail(function (jqXHR, textStatus) {
                $('#'+ formid +' .error_postcode').html('Unable to connect to the server!');
                console.log(textStatus);
	});
	}
    }
function validatePostcode(formid,postcode){ 
   // $('#postcode').val(postcode_formater($('#postcode').val()));
   var customercomments = $('#check_commentbox').val();
    $.ajax({
		url: 'index.php?route=checkout/shipping_methodetm/newpostcode',
                type: 'post',
                data: {'postcode':postcode},                      
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-method').attr('disabled', true);
			$('#button-shipping-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			$('#button-order-confirm').attr('disabled', true);
		},	
		complete: function() {
			$('#button-shipping-method').attr('disabled', false);
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .error').remove();
			
			if (json['error']) {
				if (json['error']['postcode']) {
					if(!$('#'+ formid + ' .postcodelookup').hasClass('has-error')) {
				    	$('#'+ formid + ' input[name=\'postcode\']').after('<small class="error">' + json['error']['postcode'] + '</small>');
				}
					$('#shipping-method .checkout-content').prepend('<div class="warning">' + json['error']['postcode'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				} else {
					$('#shipping-method .checkout-content').prepend('<div class="warning">Invalid Postcode!<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				}
				$('#'+ formid +' input[name=postcode]').focus();
				$('#button-order-confirm').attr('disabled', true);
				return;
			} else {
				$.ajax({
					url: 'index.php?route=checkout/shipping_methodetm',
					dataType: 'html',
					complete: function() {
                		$('#button-order-confirm').attr('disabled', false);
					},
					success: function(html) {
						$('#shipping-method .checkout-content').html(html);
						$("#shipping-method input[type=\'radio\']").attr("checked", false);
						$('#shipping-method input[type=\'radio\']:checked' ).click();
                                                 $('#check_commentbox').val(customercomments);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						$('#confirmfinalbuttom .checkout-content').prepend('<div class="warning" style="display: none;">Unable to connect to the server!<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
						//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
				loadfinalcart('');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			$('#confirmfinalbuttom .checkout-content').prepend('<div class="warning" style="display: none;">Unable to connect to the server!<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
function orderconformprogress(p,title){
	$('#order-confirm-progress-bar').css("width",p +'%');
	$('#order-confirm2-progress-bar').css("width",p +'%');
}
function setdatatolocal(){
    if (typeof(Storage) !== "undefined") {

        console.log(currentPanel);
        var checkoutls = {};
        if(!currentPanel) {
            // Login not selected
             checkoutls.check_commentbox = $('#check_commentbox').val();
        }
        else if(currentPanel == "collapse_new_customer"){
            checkoutls.currentPanel = currentPanel;
            checkoutls.reg_firstname = $('#reg_firstname').val();
            checkoutls.reg_lastname = $('#reg_lastname').val();
            checkoutls.reg_email = $('#reg_email').val();
            checkoutls.reg_telephone = $('#reg_telephone').val();
            checkoutls.reg_password = $('#reg_password').val();
            checkoutls.reg_confirmPassword = $('#reg_confirmPassword').val();
            checkoutls.reg_postcode = $('#reg_postcode').val();
            checkoutls.reg_address_1 = $('#reg_address_1').val();
            checkoutls.reg_address_2 = $('#reg_address_2').val();
            checkoutls.address_2 = $('#address_2').val();
            checkoutls.reg_city = $('#reg_city').val();
            checkoutls.check_commentbox = $('#check_commentbox').val();
        }
        else if(currentPanel == "collapse_guest"){
        
            checkoutls.currentPanel = currentPanel;
            checkoutls.guest_firstname = $('#guest_firstname').val();
            checkoutls.guest_lastname = $('#guest_lastname').val();
            checkoutls.guest_email = $('#guest_email').val();
            checkoutls.guest_telephone = $('#guest_telephone').val();
            checkoutls.guest_postcode= $('#guest_postcode').val();
            checkoutls.guest_address_1 = $('#guest_address_1').val();
            checkoutls.guest_address_2 = $('#guest_address_2').val();
            checkoutls.guest_city= $('#guest_city').val();
            checkoutls.check_commentbox = $('#check_commentbox').val();
        }
        checkoutls.check_commentbox = $('#check_commentbox').val();
        if(localStorage.payaddressnew){
             checkoutls.addne_firstname = $('#addne_firstname').val();
             checkoutls.addne_lastname = $('#addne_lastname').val();
             checkoutls.addne_address_1 = $('#addne_address_1').val();
             checkoutls.addne_address_2 = $('#addne_address_2').val();
             checkoutls.addne_city = $('#addne_city').val();
        }
          try {
            localStorage.checkoutls = JSON.stringify(checkoutls);
        }catch (error) {
        
        }
    }

}
function change_del_req(){
    setdatatolocal();
    
    $('#change_del_req_div .modal-body').load("index.php?route=checkout/shipping_postcodes/cartshipping?ch=1",function(response,statusTxt,xhr){  
        //alert(data);
        if(statusTxt=="success"){
            $('#change_del_req_div').modal({
                backdrop: 'static',
                keyboard: false
            });
            // $('#change_del_req_div').modal('show');
        }
        // alert("Succees");
           
        if(statusTxt=="error")    
           alert("Failed");
    });
}
//--> </script>

<script>

	function removeRedeemProduct(product) {
		$.ajax({
			url: 'index.php?route=checkout/checkoutetm/removefreeproduct',
			type: 'post',
			data:{product:product},
			dataType: 'json',
			success: function(json) {
				$("#redeem_freeproducts_popup").load(location.href+" #redeem_freeproducts_popup>*","");
				loadfinalcart('');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	function loadfinalcart(success) {
		if(success) {
			// $('#coupon-voucher').prepend('<div class="notification"> <div class="success" style="display: none;">' + success + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div></div>');
			// $('.success').fadeIn('slow');
			$('#notification').html('<div class="success">' + success + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
			$('.success').delay(2000).fadeOut();
		}
		
		$.ajax({
			url: 'index.php?route=checkout/confirmetm/finalcart',
			dataType: 'html',
			success: function(html) {
				$('#confirm .checkout-content').html(html);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	function loadearnedreward() {
		$.ajax({
			url: 'index.php?route=checkout/checkoutetm/showearnrewardpoint',
			success: function(data) {
				$('#reward_earned .point').html(data);
				if(!data || data=="0") {
					$('#reward_earned').hide();
				} else {
					$('#reward_earned').show();					
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	$("#reward_point").on('change',function(){
		reward = parseInt($(this).val());
		min_total = <?php echo (float)$total_without_reward; ?>;
		var total_reward = 0;
		if(isNaN(reward)) {
			$(this).val('');
			return;
		}
		if(!reward) {
			return;
		}
		if($('#redeem_product_table').length > 0) {
			var amt=0;
			var price =0;

			$("#redeem_product_table .redeem_products").attr('disabled',false);
			$.each($("#redeem_product_table .redeem_products:checked"), function(){     
				amt = parseInt($(this).attr('amt'));    
				price = parseFloat($(this).attr('price'));
				if(reward < amt || min_total < price) {
					$(this).prop('checked',false);
				} else {
					reward-=amt;
					min_total-=price;
					total_reward+=amt;
				}
			});

			if(reward > 0 ) {
				$.each($("#redeem_product_table .redeem_products:not(:checked)"), function(){     
					amt = parseInt($(this).attr('amt'));     
					price = parseFloat($(this).attr('price')); 
					if(amt <= reward && price <= min_total) {
						$(this).prop('checked',true);
						reward-= amt; 
						min_total-=price;
						total_reward+=amt;
					} else {
						$(this).attr('disabled',true);
					}
				});
			}
		}	
		max_point = Math.floor((min_total) * <?php echo (float)$this->config->get('config_redeem_points_points'); ?>) + total_reward;
                <?php if(isset($$max_point) && $max_point!=''){ ?>
		if(max_point > <?php echo $max_point; ?>) {
			max_point = <?php echo $max_point; ?>;
		}
                <?php } ?>
		$("#max_reward_point").html(max_point);
	});

	$("#redeem_freeproducts_popup").on('click','.redeem_products',function() {
		var reward = 0;
		min_total = <?php echo (float)$total_without_reward; ?>;
		current = $(this);

		$.each($("#redeem_product_table .redeem_products:checked"), function(){     
			reward+= parseInt($(this).attr('amt'));    
			min_total-= parseFloat($(this).attr('price'));
		});

		if(min_total < 0) {
			current.prop('checked',false);
			min_total+=parseFloat(current.attr('price'));
			reward-=parseInt(current.attr('amt'));  
		}

		$.each($("#redeem_product_table .redeem_products:not(:checked)"), function(){     
			if(parseFloat($(this).attr('price')) < min_total) {
				$(this).attr('disabled',false);
			} else {
				$(this).attr('disabled',true);
			}
		});

		max_point = Math.floor((min_total) * <?php echo (float)$this->config->get('config_redeem_points_points'); ?>) + reward;
		
                <?php if(isset($$max_point) && $max_point!=''){ ?>
                if(max_point > <?php echo $max_point; ?>) {
			max_point = <?php echo $max_point; ?>;
		}
                <?php } ?>
		$("#max_reward_point").html(max_point);
	});
         $("#notification").on('click','img',function() {
            $("#notification").html('');
        });
	function validatereward(product,amount) {
		qty = $('#rqty'+product).val();
		console.log(qty);
		if(qty!='' && !isNaN(qty) && qty>0) {
			$.ajax({
				url: 'index.php?route=checkout/checkoutetm/validateReward',
				type: 'post',
				dataType:'json',
				data:{product:product,qty:qty,amount:amount,reward:$('#reward_point').val()},
				success: function(json) {
                    if (json['error']) {
						if (json['error']['warning']) {
							// $('#coupon-voucher').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
							$('#notification').html('<div class="warning">' +  json['error']['warning']+ '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
							$('.warning').fadeIn('slow');
						}			
					} else if(json['success']) {
						$("#redeem_freeproducts_popup").load(location.href+" #redeem_freeproducts_popup>*","");
						loadfinalcart(json['success']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		} else {
			$('#rqty'+product).val('');
		}
	}
</script>
<script type="text/javascript">
                        
                        
            $('#upsellingrow').on('click', '.popupbuttonn' ,function() {
                var id = $(this).attr('prod_id');
                $('#upsellingrow #pdialog_' + id+' .modal-content').load("index.php?route=product/product/ajaxoptions&all=1&free=1",{prod_id: id}, function(response, statusTxt, xhr) {
                    if (statusTxt == "success") {
                        $("#pdialog_"+id).modal('show');
                        $('.modal-backdrop').appendTo("#upselling_popup");                   
                    } else if (statusTxt == "error") {
                        alert("Failed");
                    }
                });
            });
            
            $('#upsellingrow').on('click', '.bundled_prodt_popup' ,function() {
                var bundled_id = $(this).attr('prod_id');
                 $('#bundled_prodt_' + bundled_id + ' .modal-content').load("index.php?route=product/product/ajaxbundledproducts",{prod_id: bundled_id},function(response,statusTxt,xhr){
                    if(statusTxt=="success"){
                        $('#bundled_prodt_'+ bundled_id).modal({backdrop:'static',keyboard:false});
                        $('#bundled_prodt_'+ bundled_id+' .bundled_prodt_options').css('overflow-y', 'visible'); 
                        $('#bundled_prodt_'+ bundled_id+' .bundled_prodt_options').css('max-height', ($(window).height() * 0.8-150));
                        $('#product-info_'+ bundled_id+' .loader').hide();
                        $('#product-info_'+ bundled_id+' .product_description').show();
                        $('#product-info_'+ bundled_id+' .option').show();

                    }

                    if(statusTxt=="error"){
                        alert("Failed");
                        $('#product-info_'+ bundled_id+' .loader').hide();
                        $('#product-info_'+ bundled_id+' .product_description').show();
                        $('#product-info_'+ bundled_id+' .option').show();
                    }

                });
            });
            
            function addToCartRelated(product_id) {
            // addToCart(product_id,1);return;
            quantity = 1;
            $.ajax({
                url: 'index.php?route=checkout/cart/add',
                type: 'post',
                data: 'product_id=' + product_id + '&quantity=' + quantity,
                dataType: 'json',
                success: function(json) {
                    $('.success, .warning, .attention, .information, .error').remove();
                    
                    if (json['redirect']) {
                        location = json['redirect'];
                    }
                    
                    if (json['success']) {
                        $('.btnnothanks').val('Checkout');
                        $('#notification').html('<div class="success">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                        /*
                        $('#cart').load('index.php?route=module/cart #cart > *',function(){

                        });
                        */
                    related_popup();
                    }	
                }
            });
        }

        
        function related_popup() {
            $.ajax({
                    url: 'index.php?route=checkout/confirmetm/finalcart',
                    dataType: 'html',
                    success: function(html) {
                            $('#confirm .checkout-content').html(html);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                            //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
            });
            /*
            $('#upsellingrow').load('index.php?route=module/also_bought #upsellingrow> *',function(){
            });
            */
            // console.log('completed');
            
        }

        function closeupselling() {
            //$("#upselling_popup").hide();
            //$(".also_bought").hide();
            //$("#checkout").show();
            
            $.ajax({
                    url: 'index.php?route=checkout/checkoutetm/skipupsell',
                    dataType: 'html',
                    success: function(html) {
                        location.reload();
                            $('#confirm .checkout-content').html(html);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                            //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
            });
            
        }
</script>
<?php echo $footer; ?>