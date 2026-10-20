<!--Pizza Categories Deal One Level --> 

<div class="col-xs-6 col-sm-6 col-md-3 product-info_simple <?php if(isset($cate_shipping_methodes)){ foreach($cate_shipping_methodes as $catshipping){ echo "p".$catshipping." "; } } ?>" id="product-info_<?php echo $category_id; ?>" >
 <div class="img_flexer" id="<?php echo $category_id; ?>">
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
                    <?php  foreach($products as $product) { ?>
                    <a class="bundled_cat_prodt_popup btn " pid="<?php echo $product['product_id']; ?>" catId="<?php echo $category_id; ?>"><?php echo $product['name']; ?> </a>
                    <div class="product_option_priceed">
                       &pound; <?php echo number_format((float)$product['price'], 2, '.', ''); ?> 
                    </div> 
                    <?php } ?>
                </div> 
                <div class="loader" style="display: none"></div>
                
  
  

</div>
           
</div>

</div>
 <div class="modal fade etm-popup-prodts"   tabindex="-1" role="dialog"  id="cat_bundled_prodt_<?php echo $category_id; ?>" aria-labelledby="<?php echo $name; ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content"></div>
            </div>
    </div>  