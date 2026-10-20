<div id="upsellingrow" class="col-sm-12">
    <?php if(!empty($relatedproducts)) { ?>
    <div id="upselling_popup">
        <div class="popup_heading" id="heading">
            <h4>Would you like to add any of these Items</h4>
        </div>


        <div class="scroll-wrapper">
            <button class="scroll-btn left">&#10094;</button>



            <div class="prod_main_categories">


                <?php $i=0; ?>
                <?php foreach($relatedproducts as $all_products) {
                $i++;
                ?>
                <div class="product-info_simple <?php echo(($i%2)==0?'even':'odd'); ?> <?php echo(($all_products['product_options'])?'':'without_opt');?>"
                    id="related_product-info_<?php echo $all_products['product_id']; ?>">
                    <div class="img_flexer">
                        <div class="product_name">
                            <?php echo $all_products['name']; ?>
                        </div>
                        <!--<div class="product_description"></div> -->
                        <div class="product-img-left"
                            style="background:url(<?php echo $all_products['thumb']; ?>) no-repeat center;">
                            <input type="hidden" name="product_id" size="2"
                                value="<?php echo $all_products['product_id']; ?>" />
                        </div>


                        <?php if(!empty($all_products['product_options'])) { ?>
                        <div id="option-<?php echo $all_products['product_options'][0]['product_option_id']; ?>"
                            class="option">
                            <div class="modal fade etm-popup-prodts" tabindex="-1" role="dialog"
                                id="pdialog_<?php echo $all_products['product_id']; ?>"
                                aria-labelledby="<?php echo $all_products['name']; ?>">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="pxprice">
                                <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?>
                                <?php echo $all_products['price']; ?>
                                <input type="button" value="add" class="popupbuttonn button"
                                    prod_id="<?php echo $all_products['product_id']; ?>"
                                    id="<?php echo $all_products['product_id']; ?>"
                                    ids="popupbuttonn_<?php echo $all_products['product_id']; ?>"
                                    divide="<?php echo $div_id; ?>">

                            </div>
                        </div>

                        <?php  } 
                    else{
                    ?>
                        <span class=" haschillies_0"></span>
                        <div class="pxprice">
                            <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?>
                            <?php echo $all_products['price']; ?>

                            <?php if(!isset($all_products['bundle'])) { ?>
                            <a id="<?php echo $all_products['product_id']; ?>"
                                onclick="addToCartRelated('<?php echo $all_products['product_id']; ?>')"
                                class="button-cart_wop related_prodt_wop button">Add<span></span></a>
                            <?php }else{ ?>
                            <a prod_id="<?php echo $all_products['product_id']; ?>"
                                class="bundled_prodt_popup related_prodt_wop button">Add<span></span></a>
                            <div class="modal fade etm-popup-prodts" tabindex="-1" role="dialog"
                                id="bundled_prodt_<?php echo $all_products['product_id'];?>"
                                aria-labelledby="<?php echo $name; ?>">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php
                    }
                    ?>

                    </div>

                </div>








                <?php } ?>
            </div>


            <button class="scroll-btn right">&#10095;</button>
        </div>
        <div style="clear: both"></div>
        <div class="dflex mt-4">
            <a href="menu" class="button bottom-fixed btnnothanks">Back to Menu</a>
            <input type="button" class="button bottom-fixed btnnothanks" onclick="closeupselling()" value="No,Thanks">
        </div>
    </div>
    <?php } ?>



</div>

<script>
$(document).ready(function() {

    $('.scroll-btn.left').click(function() {
        $('.prod_main_categories').animate({
            scrollLeft: $('.prod_main_categories').scrollLeft() - 300
        }, 300);
    });

    $('.scroll-btn.right').click(function() {
        $('.prod_main_categories').animate({
            scrollLeft: $('.prod_main_categories').scrollLeft() + 300
        }, 300);
    });

});
</script>