<!--Default Product -->
   <div class="product-info_simple <?php echo(($options)?'':'without_opt');?> " id="product-info_<?php echo $parent_category_id.'_'.$product_id; ?>" >
                <div class="product-det-right">
                <div class="product_name">
                    <?php echo $name; ?> 
                      <span class="<?php echo($vegetarian!=0?'prodt_isveg':''); ?>"></span>
                    <?php
                        if($hot!=''){
                            $allhot = explode(' ',$hot);
                            foreach($allhot as $ht){
                            ?>
                            <span class="<?php echo 'haschillies_'.$ht; ?>"></span>
                            <?php 
                            }
                        }
                    ?>   
                </div>
               
                <?php //echo $special_deal; ?> 
                <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
                <?php if($options) { ?>
                <?php if($options[0]['type']=='radio' || $options[0]['type']=='select'){ ?>
                 <div class="product_description">
                    <?php if($description!='..') { echo $description; }  ?> 
                </div>    
                <div id="option-<?php echo $options[0]['product_option_id']; ?>" class="option">
                    <?php if(isset($options[0]['option_value'])) { ?>
                    <?php foreach ($options[0]['option_value'] as $option_value) { ?>
                    <div id="product-det_<?php echo $product_id.'_'.$option_value['product_option_value_id'].'_'.$parent_category_id; ?>" class="optionspace product-det_<?php echo $product_id.'_'.$option_value['product_option_value_id'].'_'.$parent_category_id; ?>">   
                        <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
                        <div class="productdetails" value="<?php echo $option_value['product_option_value_id']; ?>">
                            <div class="product_optionname"><?php echo $option_value['name']; ?></div>
                            <?php if ($option_value['price']) { ?>
                            <?php if (!$special) { ?>
                            <div class="product_option_price">
                                <?php //echo $option_value['price']; ?> 
                            </div>
                            <?php } else { ?>
                            <div class="product_option_price">
                                <?php //echo $option_value['price']; ?> 
                            </div>
                            <?php }?>
                            <?php } else { ?>
                            <?php }?>
                            <input type="hidden" name="option[<?php echo $options[0]['product_option_id']; ?>]" size="2" value="<?php echo $option_value['product_option_value_id']; ?>" />
                            <?php if(count($options)>1){
                            $div_id=$product_id.'_'.$option_value['product_option_value_id'].'_'.$parent_category_id;
                            ?>
                            <div class="modal fade etm-popup-prodts" tabindex="-1" role="dialog" id="dialog_<?php echo $div_id; ?>" aria-labelledby="<?php echo $name; ?>">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                                <div class="product_option_price"> 
                                    <?php  
                                    echo $curr_symbol.''.number_format(($org_price+$option_value['org_price']),2);
                                    ?>
                                    <input type="button" value="+" class="popupbuttonn button" prod_id="<?php echo $div_id; ?>" id="popupbuttonn_<?php echo $div_id; ?>" divide="<?php echo $div_id; ?>" > 
                                </div>
                            <?php } 
                            else{
                            ?>
                           <div class="product_option_price"> <?php echo $curr_symbol.''.number_format(($org_price+$option_value['org_price']),2); ?>  <a id="button-cart_<?php echo $product_id; ?>"  class="button-cart button" prod_id="product-det_<?php echo $product_id.'_'.$option_value['product_option_value_id'].'_'.$parent_category_id; ?>"><span>+</span></a></div>  
                            <?php } ?>
                        </div></div>
                    <?php } ?>

                    <?php } ?>
                </div>
                <?php  }else{ ?>
                <div class="product_option_price">
                            <?php echo $price; ?> <a id="<?php echo $product_id; ?>" div_id="<?php echo $product_id.'_'.$parent_category_id; ?>" class="button-cart_walloptn button"><span>+</span></a>
                </div>
                <div class="product_description">
                    <?php if($description!='..') { echo $description; }  ?> 
                </div>
                <div id="product-det_<?php echo $product_id.'_'.$parent_category_id; ?>" class="product-det_<?php echo $product_id.'_'.$parent_category_id; ?>">
                 <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
                <div class="modal fade etm-popup-prodts" tabindex="-1" role="dialog"  id="dialog_<?php echo $product_id.'_'.$parent_category_id; ?>" aria-labelledby="<?php echo $name; ?>">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content"></div>
                    </div>
                </div>   
                </div>    
                <?php } ?> 
                <?php  } 
                else{
                ?>
                 <div class="product_option_price">
                            <?php echo $price; ?> <a id="<?php echo $product_id; ?>" div_id="product-info_<?php echo $parent_category_id.'_'.$product_id; ?>" class="button-cart_wop button"><span>+</span></a>
                        </div>
                 <div class="product_description">
                    <?php if($description!='..') { echo $description; }  ?> 
                </div>         
                <?php
                }
                ?>   
                 </div>
            </div>








