<!--DEALS IN PAGE -->
   <div class="col-xs-6 col-sm-6 col-md-3 product-info_simple bundled_prodt_page   <?php if(!empty($product_shippings)){ foreach($product_shippings as $catshipping){ echo "p".$catshipping." "; } } ?>" pid="<?php echo $product_id; ?>" id="product-info_<?php echo $product_id; ?>" >
    <div class="img_flexer">
         
        <div class="product-img-left">  
            <div class="product_image">
            <img src="<?php echo $thumb; ?>" alt="<?php echo $thumb; ?>" title="<?php echo $thumb; ?>"<?php if (!empty($thumb_width) && !empty($thumb_height)) { ?> width="<?php echo (int)$thumb_width; ?>" height="<?php echo (int)$thumb_height; ?>"<?php } ?> loading="lazy" />
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
            <div class="product_description">
                <?php if($description!='..') { echo $description; }  ?> 
            </div>    
           
            <div class="option">
                <div class="product_option_price">
                    <?php if((float)$actual_price) { echo $price; }  ; ?>
                </div>
            </div>
            <div class="loader" style="display: none"></div>
         </div>                           
     </div>
    </div>
    