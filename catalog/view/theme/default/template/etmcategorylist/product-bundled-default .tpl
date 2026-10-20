<!--Deals - Default -->
<div class="product-info_simple" id="product-info_<?php echo $product_id; ?>" >
     <div class="product_name">
         <?php echo $name; ?> 
     </div>
     <span class="<?php echo($vegetarian!=0?'prodt_isveg':''); ?> <?php echo 'haschillies_'.$hot; ?>"></span>
     <?php //echo $special_deal; ?> 
     <div class="product_description">
         <?php if($description!='..') { echo $description; }  ?> 
     </div>    
     <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
     <div class="option">
         <div class="productdetails">
             <div class="product_optionname"><?php echo "Choose Options"; ?></div>
             <div class="product_option_price">
                 <?php echo $price; ?> <a id="<?php echo $product_id; ?>" class="bundled_prodt_popup button"><span>+</span></a>
             </div>
         </div>
     </div>
       
 </div>
<div class="modal fade etm-popup-prodts"  tabindex="-1" role="dialog" id="bundled_prodt_<?php echo $product_id;?>" aria-labelledby="<?php echo $name; ?>">
         <div class="modal-dialog" role="document">
             <div class="modal-content"></div>
         </div>
     </div>
