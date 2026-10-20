<div  class="prod_main_categories" >
            
            <?php if(!empty($deal_suggested_products)) { ?>
            <?php $i=0; ?>
            <?php foreach($deal_suggested_products as $all_products) {
            $i++;
            ?>
            <div class="product-info_simple <?php echo(($i%2)==0?'even':'odd'); ?> <?php echo(($all_products['options'])?'':'without_opt');?>" id="deal_suggestion_popup_<?php echo $all_products['product_id']; ?>" >

                <div class="product_name">
                    <?php echo $all_products['name']; ?> 

                </div>
                <div class="product_description">
                    
                </div>    


                <input type="hidden" name="product_id" size="2" value="<?php echo $all_products['product_id']; ?>" />

                <?php if($all_products['options']) { ?>
                <div id="option-<?php echo $all_products['options'][0]['product_option_id']; ?>" class="option">
                    <div class="productdetails">
                        <div class="product_optionname"></div>

                        <div class="product_option_currency"> </div>
                        <div class="product_option_price">
                            
                        </div>
                        <div class="prodt-suggested-popup-options" id="prodt-suggested-popup-options-<?php echo $all_products['product_id']; ?>" style="display: none;" title="<?php echo $all_products['name']; ?>">
                        </div>
                           <a id="" onclick="dealoptionpopup(<?php echo $all_products['product_id']; ?>)" class="deal-to-cart-plus button"></a>
                    </div>    
                            
                            

                </div>

                <?php  } 
                else{
                ?>
                <div class="option">


                    <div class="productdetails">
                        <div class="product_optionname"></div>

                        <div class="product_option_currency"> </div>
                        <div class="product_option_price">
                            <?php echo $this->currency->format($this->tax->calculate($all_products['price'], $all_products['tax_class_id'], $this->config->get('config_tax')));; ?> 
                        </div>
                        <a id="" onclick="suggesteddealadd('deal_suggestion_popup_<?php echo $all_products['product_id']; ?>')" class="deal-to-cart-plus button"></a>
                    </div>
                </div>     
                <?php
                }
                ?>   

            </div>







            <?php } ?>
            <?php } ?>
        </div>