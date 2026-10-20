<!--Product With Choose Option -->
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
       
        <div class="product_description">
                    <?php if($description!='..') { echo $description; }  ?> 
                </div> 
        <div id="option-<?php echo $options[0]['product_option_id']; ?>" class="option">
            <div id="product-det_<?php echo $product_id.'_1000000' ?>" class="product-det_<?php echo $product_id.'_1000000'; ?>">   
                <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
                <div class="productdetails" value="<?php echo $option_value['product_option_value_id']; ?>">
                    <div class="product_optionname">Choose Options</div>
                    <div class="product_option_price"></div>
                    <div class="modal fade etm-popup-prodts" tabindex="-1" role="dialog" id="dialog_<?php echo $product_id; ?>_1000000" aria-labelledby="<?php echo $name; ?>">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content"></div>
                        </div>
                    </div>
                    <div class="product_option_price">
                         <?php  
                                    echo $curr_symbol.''.number_format(($org_price),2);
                            ?>
                        <input type="button" value="+" class="popupbuttonall button" prod_id="<?php echo $product_id; ?>_1000000" id="popupbuttonn_<?php echo $product_id; ?>_1000000" divide="<?php echo $product_id; ?>_1000000" > 
                    </div>                   
                </div>
            </div>
        </div>
    </div>
</div>