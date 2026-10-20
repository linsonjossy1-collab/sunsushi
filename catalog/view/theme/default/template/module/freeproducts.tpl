
<?php //print_r($offer_products);?>
<?php if(!empty($offer_products)) { ?>    
<div class="modal fade" id="free_products_popup" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Offers</h4>
                              </div>
                    <div class="modal-body" style="max-height: 400px;overflow-y: scroll;overflow-x: hidden">
    <?php foreach($offer_products as $offer_product) { ?>
    <div id="cat_<?php echo  $offer_product['offer_id'];?>" class="prod_main_categories">
        <div class="offer_title"> <h4 style="margin-top: 0px;">YOU ARE ELIGIBLE FOR <?php echo $offer_product['offername'];?> OFFER</h4></div>

        <?php if(isset($offer_product['products'])) { ?>
        <?php $i=0; ?>
        <?php foreach($offer_product['products'] as $all_products) {
        $i++;
        ?>
      <div class="product-info_simple <?php echo(($i%2)==0?'even':'odd'); ?> <?php echo(($all_products['product_options'])?'':'without_opt');?>" id="product-info_<?php echo $all_products['product_id']; ?>" >

            <div class="product_name">
                <?php echo $all_products['name']; ?> 

            </div>
            <!--<div class="product_option_popup product_option_price"> <input type="button" value="add" onclick="showfreeproductoptions('<?php echo $all_products['product_id'].'-'.$offer_product['offer_id']; ?>');" class="button" prod_id="<?php echo $all_products['product_id'].'-'.$offer_product['offer_id']; ?>" id="popupbuttonn_<?php echo $all_products['product_id'].'-'.$offer_product['offer_id']; ?>" divide="<?php echo $all_products['product_id'].'-'.$offer_product['offer_id']; ?>" ></div>-->
          
                

            <input type="hidden" name="product_id" size="2" value="<?php echo $all_products['product_id']; ?>" />

            <?php if($all_products['product_options']) { ?>
            <div class="product_option_popup product_option_price"> <input type="button" value="add" onclick="showfreeproductoptions('<?php echo $all_products['product_id'].'-'.$offer_product['offer_id']; ?>');" class="button" prod_id="<?php echo $all_products['product_id'].'-'.$offer_product['offer_id']; ?>" id="popupbuttonn_<?php echo $all_products['product_id'].'-'.$offer_product['offer_id']; ?>" divide="<?php echo $all_products['product_id'].'-'.$offer_product['offer_id']; ?>" ></div>
           
            

            <?php  } 
            else{
            ?>
            <a onclick="addFreeToCart(<?php echo $all_products['product_id']; ?>,1)"  id="<?php echo $all_products['product_id']; ?>" class="button-cart_wop button"><span>add<?php //echo $button_cart; ?></span></a> 
            <?php
            }
            ?>   

        </div>
        <?php } ?>
        
        <?php } ?>

    </div>
    <?php } ?>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
    </div>
    </div>
</div>
<div class="etm-popup-free-options modal fade" tabindex="-1"  role="dialog" id="freeproductoptions"> 
    
</div>
<script type="text/javascript">  
    $(document).ready(function(){
        $('#free_products_popup').modal('show');
        $("#free_products_popup").on("shown.bs.modal", function () {
            if ($(".modal-backdrop").length > 1) {
                $(".modal-backdrop").not(':first').remove();
            }
        });
    });
    </script> 
<?php } ?>
