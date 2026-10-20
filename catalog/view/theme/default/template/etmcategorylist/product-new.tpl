<!--New 1 -->

   <div class="product-info_simple<?php echo(($options)?'':'without_opt');?>" id="product-info_<?php echo $parent_category_id.'_'.$product_id; ?>" >
                <div class="product-det-left">
                 <div class="product_image">
                   <img src="<?php echo $thumb; ?> "<?php if (!empty($thumb_width) && !empty($thumb_height)) { ?> width="<?php echo (int)$thumb_width; ?>" height="<?php echo (int)$thumb_height; ?>"<?php } ?> /> 

                </div>
                </div>
                <div class="product-det-right">
                <div class="product_name">
                    <?php echo $name; ?> 

                </div>
                <?php //echo $special_deal; ?> 
                <div class="product_description">
                    <?php if($description!='..') { echo $description; }  ?> 
                </div>    


                <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
              
                <?php if($options) { ?>
                <?php foreach($options as $option) { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                <?php // if ($option['required'] == '1') { ?>
                <?php if ($option['type'] == 'select') { ?>
                <div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option">
                    
                    <select name="option[<?php echo $option['product_option_id']; ?>]">
                        
                        <?php foreach ($option['option_value'] as $option_value) { ?>
                        <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                            <?php if ($option_value['price']) { ?>
                            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                            <?php } ?>
                        </option>
                        <?php } ?>
                    </select>

                </div>

                <?php } ?>
                <?php if ($option['type'] == 'radio') { ?>
                <div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option">
                    <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                    <?php } ?>
                    <b><?php echo $option['name']; ?>:</b>
                    <div class="option_values_radiobtn">
                        <?php foreach ($option['option_value'] as $option_value) { ?>
                        <div class="subcat-reqrd-options" >
                            <div class="sub-cat-req-opname"><?php echo $option_value['name']; ?></br></div>
                            <div class="sub-cat-req-opval">
                                <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
                                <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>">
                                    <?php if ($option_value['price']) { ?>
                                    (<?php echo $option_value['price']; ?>)
                                    <?php } ?>
                                </label>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>  

                <?php } ?>
                
                <?php if ($option['type'] == 'checkbox') { ?>
                <?php if ($option['default'] == '1') { ?>
                <div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option ">
                    <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                    <?php } ?>
                    <b><?php echo $option['name']; ?>:</b>
                    <?php foreach ($option['option_value'] as $option_value) { ?>
                    <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" checked />
                    <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                        <?php if ($option_value['price']) { ?>
                        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                        <?php } ?>
                    </label>

                    <?php } ?> 
                </div>
                <?php } else { ?>
                <div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option ">
                    <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                    <?php } ?>
                    <b><?php echo $option['name']; ?>:</b>
                    <?php foreach ($option['option_value'] as $option_value) { ?>
                    <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>"  />
                    <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                        <?php if ($option_value['price']) { ?>
                        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                        <?php } ?>
                    </label>
                    <br />
                    <?php } ?>
                </div>
                <?php } ?>

                <?php } ?>
                
                <?php if ($option['type'] == 'image') { ?>
                <div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option">
                    <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                    <?php } ?>
                    <b><?php echo $option['name']; ?>:</b>
                    <table class="option-image">
                        <?php foreach ($option['option_value'] as $option_value) { ?>
                        <tr>
                            <td style="width: 1px;"><input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
                            <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
                            <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                    <?php if ($option_value['price']) { ?>
                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                    <?php } ?>
                                </label></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <br />
                <?php } ?>
                <?php // } ?>
                </div>
                <?php } ?>
                <a prod_id="product-info_<?php echo $parent_category_id.'_'.$product_id; ?>" class="cart-add-button button"><span>Add</span></a>

                <?php  } 
                else{
                ?>
                <div class="option">


                    <div class="productdetails">
                        <div class="product_optionname"><?php //echo $all_products['name']; ?></div>

                        <div class="product_option_currency"> <?php  //echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?></div>
                        <div class="product_option_price">
                            <?php echo $price; ?> 
                        </div>



                        <a id="<?php echo $product_id; ?>" prod_id="product-info_<?php echo $parent_category_id.'_'.$product_id; ?>" class="button-cart_wop button"><span>+</span></a>
                    </div>
                </div>     
                <?php
                }
                ?>   
                 </div>
            </div>








