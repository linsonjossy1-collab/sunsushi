<!--Product Image & Plus -->

<div class="col-xs-6 col-sm-6 col-md-3 product-info_simple productdatas-<?php echo $product_id; ?>" id="product-info_<?php echo $product_id.'_'.$parent_category_id; ?>">
 <div class="img_flexer">
    
    <div class="product-img-left">
        <div class="product_image">
        <img src="<?php echo $thumb; ?>" alt="<?php echo $thumb; ?>" title="<?php echo $thumb; ?>"<?php if (!empty($thumb_width) && !empty($thumb_height)) { ?> width="<?php echo (int)$thumb_width; ?>" height="<?php echo (int)$thumb_height; ?>"<?php } ?> loading="lazy" />
            <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
            <div class="option">
                <div class="productdetails">
                    
                </div>
            </div>
        </div>
  </div>

  <div class="product-img-right">
    <div class="product_name">
    <?php echo $name; ?>
 
                    </div>
                 <div class="product_description">
                    <?php if($description!='..') { echo $description; }  ?> 
                </div>   



                <?php if($options) { ?>
 
    
                     

                              
             <div class="optflex">
                              <div class="product_option_price">
                                  <?php if(!empty($specific_prices)){ ?>
                            <?php foreach($specific_prices as $code => $price){ ?>
                            <span class="p<?php echo $code ; ?>" style="display: <?php echo ($code=='collect'?'block':'none') ; ?>">
                                <?php echo $curr_symbol.''.number_format(($price),2); ?>
                            </span>
                            <?php   } ?>
                        <?php }else{ ?>
                            <?php  echo $curr_symbol.''.number_format(($org_price),2); ?>
                        <?php   } ?>
                                      
                                      
                                      </div>    
                                      <div class="iconfl">
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
                 </div>
              
                <div class="addtocartnb" >
                    <a class="btn btn-primary btnshowoptions" divid="<?php echo $product_id.'_'.$parent_category_id;?>">Add to Cart</a>
              </div>


            <div id="equiliserpropage" class="equiliserpropage"  style="display: none">
            <div class="remove">
            <div class="fa fa-minus-circle" title="-1" onclick="prodctdeletenew('<?php echo $product_id; ?>');"></div>
            </div>
            <div class="quantity">x&nbsp;<?php echo $product['quantity']; ?></div>
                        <div class="add btnshowoptions" divid="<?php echo $product_id.'_'.$parent_category_id;?>">
                <div class="fa fa-plus-circle" title="+1"></div>
            </div>

            </div>


                <?php  } 
                else{
                ?>
                <div class="option">
                <div class="productdetails">
                <div class="product_option_price">
                        <?php if(!empty($specific_prices)){ ?>
                            <?php foreach($specific_prices as $code => $price){ ?>
                            <span class="p<?php echo $code ; ?>" style="display: none">
                                <?php echo $curr_symbol.''.number_format(($price),2); ?>
                            </span>
                            <?php   } ?>
                        <?php }else{ ?>
                            <?php  echo $price; ?>
                        <?php   } ?>
                    
                </div> 
                <div class="iconfl">
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
                </div>
                </div>
            <div class="addtocartnb" >
                <a href="" class="btn btn-primary button-cart_wop" div_id="product-info_<?php echo $product_id.'_'.$parent_category_id;?>">Add to Cart</a>
            </div>


        <div id="equiliserpropage" class="equiliserpropage" style="display: none">
        <div class="remove">
        <div class="fa fa-minus-circle" title="-1" onclick="prodctdeletenew('<?php echo $product_id; ?>');"></div>
        </div>
        <div class="quantity">x&nbsp;<?php echo $product['quantity']; ?></div>
                <div class="add button-cart_wop" div_id="product-info_<?php echo $product_id.'_'.$parent_category_id;?>">
        <div class="fa fa-plus-circle" title="+1"></div>
        </div>

        </div>


                <?php
                }
                ?>

                <div class="loader" style="display: none"></div>
  </div>
  </div>
</div>

    <div class="modal fade etm-popup-prodts" tabindex="-1" role="dialog" id="dialog_<?php echo $product_id.'_'.$parent_category_id; ?>" aria-labelledby="<?php echo $name; ?>">
              <div class="modal-dialog" role="document">
                  <div class="modal-content"></div>
              </div>
          </div>

