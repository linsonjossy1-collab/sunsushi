<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
    <h4 class="modal-title"><?php echo $category_details['name']; ?></h4>
</div>
<div class="prgloader"></div>
<div class="modal-body popoptionmodel">
     <div class="product_image_inner" style="background:url(<?php echo $thumb; ?>) no-repeat center;"></div>
              <div class="product_description">
                    <?php if($description!='..') { echo $description; }  ?> 
                </div>
    <div id="cat_popup_products_<?php echo $category_details['category_id']; ?>" class="cat_popup_prodt_options">
        <div class="prod_extra_option_det">
            <div class="prod_extra_option_name col-sm-4">
                 <b> <?php echo $category_details['name'];?>:</b>
            </div>
            <div class="prod_extra_option_det col-sm-8">
                <select class="prodt_sel_element form-control selectpicker"  name="product_id">
                    <?php foreach($category_details['products'] as $products) { ?>
                    <option  id="<?php echo $category_details['category_id'].'_'.$products['product_id'];?>" value="<?php echo $category_details['category_id'].'_'.$products['product_id'];?>"><?php echo $products['name']; ?> (<?php  echo $this->currency->getSymbolLeft().' '.$products['price'];?>)
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <?php foreach($category_details['products'] as $products) { ?>
            <div class="cat_prodt_option" id="cat_prodt_option_<?php echo $category_details['category_id'].'_'.$products['product_id'];?>">

            </div>
        <?php } ?> 
    </div>
</div>
        <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="float: left">Close</button>
        <a  onclick="etmAddtocart('cat_prodt_dialog_<?php echo $category_details['category_id']; ?>')" prod_id="<?php echo $category_id; ?>" class="popupcartbtn button button_cart"><?php echo $button_cart; ?></a>
    </div> 
<script type="text/javascript">
   $(function() {
       $('.selectpicker').selectpicker();
        $('.prodt_sel_element').change(function() {
            var divid = $(this).val();
            $('.prgloader').show();
            $('.cat_prodt_option').html('');
            $('#cat_prodt_option_'+divid).load("index.php?route=product/product/ajaxcategoryproductoptions",{cat_id: divid}, function(response, statusTxt, xhr) {
                $('.prgloader').hide();
            });
        });
        $('#cat_prodt_option_<?php echo  $category_details['category_id'].'_'.$category_details['products'][0]['product_id']; ?>').load("index.php?route=product/product/ajaxcategoryproductoptions",{cat_id:'<?php echo $category_details['category_id'].'_'.$category_details['products'][0]['product_id']; ?>'},
            function(response, statusTxt, xhr) {
                $('.prgloader').hide();
            }
        );

    });
</script>