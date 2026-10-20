<?php if(!empty($relatedproducts)) { ?>  
<div style="display:none">
<div id="relatedProducts_popup">
    <div class="popup_heading"><h3>Do You Want Any of these Items</h3></div>
    <div  class="prod_main_categories" >
            
            
            <?php $i=0; ?>
            <?php foreach($relatedproducts as $all_products) {
            $i++;
            ?>
            <div class="product-info_simple <?php echo(($i%2)==0?'even':'odd'); ?> <?php echo(($all_products['product_options'])?'':'without_opt');?>" id="related_product-info_<?php echo $all_products['product_id']; ?>" >

                <div class="product_name">
                    <?php echo $all_products['name']; ?> 

                </div>
                <div class="product_description">
                    
                </div>    


                <input type="hidden" name="product_id" size="2" value="<?php echo $all_products['product_id']; ?>" />

                <?php if($all_products['product_options']) { ?>
                <div id="option-<?php echo $all_products['product_options'][0]['product_option_id']; ?>" class="option">


                    <?php if(isset($all_products['product_options'][0]['option_value'])) { ?>
                    <?php foreach ($all_products['product_options'][0]['option_value'] as $option_value) { ?>
                    <div id="simplelist_product_details" class="related_product-det_<?php echo $all_products['product_id'].'_'.$option_value['product_option_value_id']; ?>">   
                        <input type="hidden" name="product_id" size="2" value="<?php echo $all_products['product_id']; ?>" />
                        <div class="productdetails" value="<?php echo $option_value['product_option_value_id']; ?>">
                            <div class="product_optionname"><?php echo $option_value['name']; ?></div>
                            <?php if ($option_value['price']) { ?>
                            <?php if (!$all_products['special']) { ?>
                            <div class="product_option_currency"> <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?></div>
                            <div class="product_option_price">
                                <?php echo $option_value['price']; ?> 
                            </div>
                            <?php } else { ?>
                            <div class="product_option_currency"> <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?></div>
                            <div class="product_option_price">
                                <?php echo $option_value['price']; ?> 
                            </div>
                            <?php }?>

                            <?php } ?>
                            <input type="hidden" name="option[<?php echo $all_products['product_options'][0]['product_option_id']; ?>]" size="2" value="<?php echo $option_value['product_option_value_id']; ?>" />

                            <?php if(count($all_products['product_options'])>1){
                            $div_id=$all_products['product_id'].'_'.$option_value['product_option_value_id'];
                            ?>
                            <div style="display: none;" id="dialog_<?php echo $div_id; ?>" title="<?php echo $all_products['name']; ?>">
                            </div>

                            <div class="product_option_popup"> <input type="button" value="add" class="popupbuttonn button" prod_id="<?php echo $div_id; ?>" id="popupbuttonn_<?php echo $div_id; ?>" divide="<?php echo $div_id; ?>" ></div>
                            <?php } 
                            else{
                            ?>
                            <div class="cart"> <a id="button-cart_<?php echo $all_products['product_id']; ?>"  class="relared_podt_add button" prod_id="<?php echo $all_products['product_id'].'_'.$option_value['product_option_value_id']; ?>"><span><?php echo $button_cart; ?></span></a></div>  
                            <?php } ?>
                        </div></div>
                    <?php } ?>

                    <?php } ?>

                </div>

                <?php  } 
                else{
                ?>
                <div class="option">


                    <div class="productdetails">
                        <div class="product_optionname"><?php //echo $all_products['name']; ?></div>

                        <div class="product_option_currency"> <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?></div>
                        <div class="product_option_price">
                            <?php echo $all_products['price']; ?> 
                        </div>



                        <a id="<?php echo $all_products['product_id']; ?>" onclick="addToCart('<?php echo $all_products['product_id']; ?>')" class="related_prodt_wop button"><span><?php echo $button_cart; ?></span></a>
                    </div>
                </div>     
                <?php
                }
                ?>   

            </div>







            
            <?php } ?>
        </div>
    <div style="clear: both"></div>
    <input type="button" class="button" onclick=" $('#cboxClose').click();"  value="No,THANKS">
    </div>
</div>
<?php } ?>