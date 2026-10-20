<!--Deals With Imaget -->
   <div class="col-xs-6 col-sm-6 col-md-3 product-info_simple bundled_prodt_popup   <?php if(!empty($product_shippings)){ foreach($product_shippings as $catshipping){ echo "p".$catshipping." "; } } ?>" pid="<?php echo $product_id; ?>" id="product-info_<?php echo $product_id; ?>" >
    <div class="img_flexer">
         
        <div class="product-img-left">  
            <div class="product_image">
            <img src="<?php echo $thumb; ?>" alt="<?php echo $thumb; ?>" title="<?php echo $thumb; ?>" loading="lazy" />
                <div class="productdetails">
                
                </div>
            </div>
        </div>
        <div class="product-img-right">
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
            <div class="product_description truncate">
                <?php if($description!='..') { echo $description; }  ?> 
            </div>    
           
            <div class="option">
                <div class="product_option_price">
                    <?php if((float)$actual_price) { echo $price; }  ; ?>
                </div>
            </div>
            <div class="addtocartnb" style="">
            <a class="btn btn-primary btnshowoptions">Add to Cart</a>
</div>
            <div class="loader" style="display: none"></div>
         </div>                           
     </div>
    </div>
    <div class="modal fade etm-popup-prodts"   tabindex="-1" role="dialog"  id="bundled_prodt_<?php echo $product_id;?>" aria-labelledby="<?php echo $name; ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content"></div>
            </div>
    </div>