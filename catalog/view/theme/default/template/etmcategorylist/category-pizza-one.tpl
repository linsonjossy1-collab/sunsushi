<!--Pizza Categories One Level --> 
<div class="col-xs-6 col-sm-6 col-md-3 product-info_simple" id="product-info_<?php echo $category_id; ?>" >
 <div class="img_flexer category_prodt_popup" id="<?php echo $category_id; ?>">
 <div class="product-img-left">
     <div class="product_image">
            <img src="<?php echo $thumb; ?>" alt="<?php echo $thumb; ?>" title="<?php echo $thumb; ?>" loading="lazy" />
            <div class="option">
                <div class="productdetails">
                    
                </div>
            </div>
        </div>
<!--<a id="<?php echo $category_id; ?>" class="category_prodt_popup button"><span>Add to cart +</span></a>-->
 </div>
 <div class="product-img-right">
    <div class="product_name">
        <?php echo $name; ?>         
        <span class="<?php echo($vegetarian!=0?'prodt_isveg':''); ?>"></span>
        <?php
            if($hot_count!=''){
                $allhot = explode(' ',$hot_count);
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

                <div class="product_option_price">
                    FROM <?php echo $prodt_least_price; ?> 
                </div> 
     <div class="loader" style="display: none"></div>
  

</div>
           
</div>

</div>
 <div class="modal fade"  tabindex="-1" role="dialog"  id="cat_prodt_dialog_<?php echo $category_id; ?>" aria-labelledby="<?php echo $name; ?>">
            <div class="modal-dialog" role="document">
                <div class="modal-content"></div>
            </div>
            </div>  