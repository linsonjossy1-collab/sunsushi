<div class="modal-dialog" role="document">
    <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
    <div class="modal-content">
        <div class="modal-body" style="max-height: 400px;overflow-y: scroll;overflow-x: hidden">
            <?php   foreach($options as $key=>$product_options) { 
                    
                    ?>
                    <div class="other_options pr_options_<?php echo $product_id; ?> " id="other_pr_options__<?php //echo $cat_products['category_id'].'_'.$all_products['product_id'];?>">
                        <?php if ($product_options['type'] == 'select') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                            
                            <div class="prod_extra_option_name col-sm-6"> <b><?php echo $product_options['name']; ?></b>
                            <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                            </div>
                            <div class="prod_extra_option_det col-sm-6">
                            <select class="form-control" name="option[<?php echo $product_options['product_option_id']; ?>]">
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
                           
                            <div class="prod_extra_option_name col-sm-6"><b><?php echo $product_options['name']; ?></b>
                             <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                            </div>
                            <div class="prod_extra_option_det col-sm-6">
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
                            
                            <div class="prod_extra_option_name col-sm-6"><b><?php echo $product_options['name']; ?>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    :</b><br />
                           
                            
                            </div>
                            <div class="prod_extra_option_det col-sm-6">
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
                           
                            <div class="prod_extra_option_name col-sm-6"><b><?php echo $product_options['name']; ?>
                                    <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    :</b>
                                 
                                <br /></div>
                            <div class="prod_extra_option_det col-sm-6">
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
                           
                            <div class="prod_extra_option_name col-sm-6"><b><?php echo $product_options['name']; ?>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    
                                    :</b>
                                
                                <br /></div>
                            <div class="prod_extra_option_det col-sm-6"><input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" />
                        </div></div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'textarea') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                           
                            <div class="prod_extra_option_name col-sm-6"><b><?php echo $product_options['name']; ?>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    :</b>
                                
                                <br /></div>
                           <div class="prod_extra_option_det col-sm-6"> <textarea name="option[<?php echo $product_options['product_option_id']; ?>]" cols="40" rows="5"><?php echo $product_options['option_value']; ?></textarea>
                        </div></div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'file') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                            
                            <div class="prod_extra_option_name col-sm-6"><b><?php echo $product_options['name']; ?>
                                    <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>:</b><br /></div>
                            <div class="prod_extra_option_det col-sm-6"><input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $product_options['product_option_id']; ?>" class="button">
                            <input type="hidden" name="product_options[<?php echo $product_options['product_option_id']; ?>]" value="" />
                        </div></div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'date') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                            
                            <div class="prod_extra_option_name col-sm-6"><b><?php echo $product_options['name']; ?>
                                    <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    :</b><br /></div>
                            <div class="prod_extra_option_det col-sm-6"><input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="date" />
                        </div> </div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'datetime') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                           
                            <div class="prod_extra_option_name col-sm-6"><b><?php echo $product_options['name']; ?>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    
                                    :</b><br /></div>
                           <div class="prod_extra_option_det col-sm-6"> <input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="datetime" />
                        </div> </div>
                        <br />
                        <?php } ?>
                        <?php if ($product_options['type'] == 'time') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                           
                            <div class="prod_extra_option_name col-sm-6"><b><?php echo $product_options['name']; ?>
                                     <?php if ($product_options['required']) { ?>
                            <span class="required">*</span>
                            <?php } ?>
                                    
                                    :</b><br /></div>
                           <div class="prod_extra_option_det col-sm-6"> <input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="time" />
                        </div></div>
                        <br />
                        <?php } ?>


                    </div>    
                    
                    <?php } ?>
                    <?php  $divid = $product_id.'-'.$offer_id; ?>
        </div>
        <div class="modal-footer">
              <span class="cart"> <input type="button" class="button_cancel button"  data-dismiss="modal" value="Cancel"></span>  
                  <span class="cart" style="float:right;"> <input type="button" onclick="addFreeOptionsToCart('freeproductoptions');" class="button <?php echo $product_id.'-'.$offer_id; ?>"  value="Add"></span>  
        </div>
    </div>
</div>


                    
                     
               