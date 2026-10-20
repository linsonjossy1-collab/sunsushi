
<script type="text/javascript">
$(document).ready(function(){

$('.btn_customize').click(function() {
 if($(this).hasClass('opt_show')){
  $(this).removeClass('opt_show');
 $('.default_options_' + $(this).attr('id')).hide();
 }else{
 $(this).addClass('opt_show');
 $('.default_options_' + $(this).attr('id')).show();
 }
 });
$('.button_cancel').click(function() {
        //alert("test");
        var daialog_id = $(this).attr('prod_id');
                //alert(daialog_id);

//$('#dialog_' + daialog_id).dialog("close");  
        });
  
});

</script>
<div id="prodt-suggested-popup-options_<?php echo $product_id; ?>">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
<?php   foreach($options as $key=>$product_options) { 
                    
                    ?>
                    <div class="other_options pr_options_product-det_<?php echo $product_id; ?>">
                        <?php if ($product_options['type'] == 'select') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                            
                            <div class="prod_extra_option_name"> <b><?php echo $product_options['name']; ?> :</b>
                            <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                            </div>
                            <div class="prod_extra_option_det">
                           <select name="option[<?php echo $product_options['product_option_id']; ?>]">
                                <?php if (!$product_options['required']) { ?>  
                                <option value="">Please Select</option>
                                <?php } ?>
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
                           
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?> :</b>
                             <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                            </div>
                            <div class="prod_extra_option_det">
                           <?php foreach ($product_options['option_value'] as $option_value) { ?>
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
                        <?php if($product_options['default']==1) { ?>
                         <a id="<?php echo $product_options['product_option_id'];?>" class="btn_customize"><?php echo $product_options['name']; ?></a>
                         <div style="display: none" id="option-<?php echo $product_options['product_option_id']; ?>" class="product_options  default_options_<?php echo $product_options['product_option_id'];?>">
                         <?php if ($product_options['required']) { ?>
                          <span class="required">*</span>
                           <?php } ?>
                          <br /> 
                          <?php foreach ($product_options['option_value'] as $option_value) { ?>
                          <input type="checkbox" name="option[<?php echo $product_options['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>"  checked/>
                          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                          <?php if ($option_value['option_price']) { ?>
                         (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                         <?php } ?>
                        </label>
                          <br />
                        <?php } ?>
                       </div>
        <?php } else{ ?>
        <?php if ($product_options['required']) { ?> 
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                            
                            <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>:</b>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    <br />
                           
                            
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
                        <?php } else{ ?>
                         <a id="<?php echo $product_options['product_option_id'];?>" class="btn_customize"><?php echo $product_options['name']; ?></a>
                         <div style="display: none" id="option-<?php echo $product_options['product_option_id']; ?>" class="product_options default_options_<?php echo $product_options['product_option_id'];?>">
                         <?php if ($product_options['required']) { ?>
                          <span class="required">*</span>
                           <?php } ?>
                          
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
                          <?php } ?>
                           <?php } ?>
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
</div>
                        
                    <span class="cart"> <input type="button" onclick="dealapprove(<?php echo $parent_id; ?>,<?php echo $product_id; ?>,'smart-suggested-product-options-<?php echo $product_id; ?>');" id="button-cart_<?php echo $product_id; ?>" class="button_cart button"  value="<?php echo $button_cart; ?>"></span>  
                
                <span class="cart"> <input type="button" onclick="$('smart-suggested-product-options-<?php echo $product_id; ?>').dialog('close');" class="button_cancel button"  value="Cancel"></span>