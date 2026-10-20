
<script type="text/javascript">
$(document).ready(function(){
$('.button_cancel').click(function() {
        //alert("test");
        var daialog_id = $(this).attr('prod_id');
                //alert(daialog_id);

$('#dialog_' + daialog_id).dialog("close");  
        });
                 $('.button_cart').bind('click', function() {
            prod_id = $(this).attr('prod_id');

            $.ajax({
                url: 'index.php?route=checkout/cart/add',
                type: 'post',
                data: $('.product-det_' + prod_id + ' input[type=\'text\'], .product-det_' + prod_id + ' input[type=\'hidden\'], .product-det_' + prod_id + ' input[type=\'radio\']:checked, .product-det_' + prod_id + ' input[type=\'checkbox\']:checked, .product-det_' + prod_id + '  select, .product-det_' + prod_id + ' textarea, .pr_options_' + prod_id + ' input[type=\'text\'], .pr_options_' + prod_id + ' input[type=\'hidden\'], .pr_options_' + prod_id + ' input[type=\'radio\']:checked, .pr_options_' + prod_id + ' input[type=\'checkbox\']:checked, .pr_options_' + prod_id + '  select, .pr_options_' + prod_id + ' textarea'),
                dataType: 'json',
                success: function(json) {
                    $('.success, .warning, .attention, information, .error').remove();
                    if (json['error']) {
                        if (json['error']['option']) {
                            for (i in json['error']['option']) {
                                $('#other_pr_options_' + prod_id + '#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                            }
                        }
                    }

                    if (json['success']) {
                        $('#product_options_dialog_' + prod_id).dialog("close");
                        // $('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                        // $('.success').fadeIn('slow');
                        // $('#cart-total').html(json['total']);
                        // jQuery('#cart-total').click();
                         $('#cart').load('index.php?route=module/cart #cart > *');
                         $.ajax({
					url: 'index.php?route=checkout/shipping_methodetm/requestedtime',
                                        type: 'post',
                                        data: $('#shipping-method input[type=\'radio\']:checked'),
					dataType: 'html',
					success: function(html) {
						$('#shipping-method #requested_time').html(html);

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
				$.ajax({
					url: 'index.php?route=checkout/payment_methodetm',
					dataType: 'html',
					success: function(html) {
						$('#payment-method .checkout-content').html(html);

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
                          $.ajax({
					url: 'index.php?route=checkout/confirmetm/finalcart',
					dataType: 'html',
					success: function(html) {
						$('#confirm .checkout-content').html(html);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
                       // $('html, body').animate({scrollTop: 0}, 'slow');
                       $('html, body').animate({scrollTop: 255}, 'slow');
                    }
                }
            });
            //  }
        });

});

</script>
<?php   foreach($options as $key=>$product_options) { 
                    
                    ?>
                    <div class="other_options pr_options_<?php echo $product_id; ?>" id="other_pr_options_<?php echo $cat_products['category_id'].'_'.$all_products['product_id'];?>">
                        <?php if ($product_options['type'] == 'select') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                            
                            <div class="prod_extra_option_name"> <b><?php echo $product_options['name']; ?></b>
                            <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                            </div>
                            <div class="prod_extra_option_det">
                            :<select name="option[<?php echo $product_options['product_option_id']; ?>]">
                                <option value="">Please Select</option>
                                <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                    <?php if ($option_value['option_price']) { ?>
                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                    <?php } ?>
                                </option>
                                <?php } ?>
                            </select></div>
                        </div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'radio') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                           
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?></b>
                             <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                            </div>
                            <div class="prod_extra_option_det">
                            :<?php foreach ($product_options['option_value'] as $option_value) { ?>
                            <input type="radio" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
                            <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                <?php if ($option_value['option_price']) { ?>
                                (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                <?php } ?>
                            </label>
                            <br />
                            <?php } ?>
                            </div>
                        </div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'checkbox') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                            
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    :</b><br />
                           
                            
                            </div>
                            <div class="prod_extra_option_det">
                            <?php foreach ($product_options['option_value'] as $option_value) { ?>
                            <input type="checkbox" name="option[<?php echo $product_options['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
                            <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                <?php if ($option_value['option_price']) { ?>
                                (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                <?php } ?>
                            </label>
                            <br />
                            <?php } ?>
                           </div> 
                        </div>
                        <br />
                        <?php } ?>

                        <?php if ($product_options['type'] == 'image') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                           
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                    <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    :</b>
                                 
                                <br /></div>
                            <div class="prod_extra_option_det">
                            <table class="option-image">
                                <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                <tr>
                                    <td style="width: 1px;"><input type="radio" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
                                    <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
                                    <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                            <?php if ($option_value['option_price']) { ?>
                                            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                            <?php } ?>
                                        </label></td>
                                </tr>
                                <?php } ?>
                            </table>
                            </div>    
                        </div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'text') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                           
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    
                                    :</b>
                                
                                <br /></div>
                            <div class="prod_extra_option_det"><input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" />
                        </div></div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'textarea') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                           
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    :</b>
                                
                                <br /></div>
                           <div class="prod_extra_option_det"> <textarea name="option[<?php echo $product_options['product_option_id']; ?>]" cols="40" rows="5"><?php echo $product_options['option_value']; ?></textarea>
                        </div></div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'file') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                            
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                    <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>:</b><br /></div>
                            <div class="prod_extra_option_det"><input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $product_options['product_option_id']; ?>" class="button">
                            <input type="hidden" name="product_options[<?php echo $product_options['product_option_id']; ?>]" value="" />
                        </div></div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'date') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                            
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                    <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    :</b><br /></div>
                            <div class="prod_extra_option_det"><input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="date" />
                        </div> </div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'datetime') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                           
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    
                                    :</b><br /></div>
                           <div class="prod_extra_option_det"> <input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="datetime" />
                        </div> </div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'time') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                           
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    
                                    :</b><br /></div>
                           <div class="prod_extra_option_det"> <input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="time" />
                        </div></div>
                        <br />
                        <?php } ?>


                    </div>    
                    
                    <?php } ?>
                        
                  <span class="cart"> <input type="button" id="button-cart_<?php echo $product_id; ?>" class="button_cart button" prod_id="<?php echo $all_id; ?>" para="<?php echo $all_id?>" value="<?php echo $button_cart; ?>"></span>  
               
               <span class="cart"> <input type="button" class="button_cancel button" prod_id="<?php  echo $all_id; ?>" value="Cancel"></span>