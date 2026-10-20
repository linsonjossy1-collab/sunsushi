<!--Default -->
<div class="cat_name"><img src="<?php echo $image; ?>" alt="<?php echo $image; ?>" title="<?php echo $image; ?>"  />
<h3><span><?php echo $name;?></span></h3></div>
<div class="cat_desc">
  <?php echo $description;?>
</div>
<?php //print_r($category_layouts);?>
<?php foreach($category_layouts as $category_layout){  ?>
<div id="cat_sub_<?php echo $category_layout;?>" class="prod_main <?php if(isset($category_shippings) && isset($category_shippings[$category_layout])){ foreach($category_shippings[$category_layout] as $catshipping){ echo "p".$catshipping." "; } } ?>"></div>
<script type="text/javascript">
    $(function() {
        carprogresscount += 1;
        if(typeof(Storage) !== "undefined" && sessionStorage.cached_cat_<?php echo $category_layout;?>) {
            $('#cat_sub_<?php echo $category_layout; ?>').html(sessionStorage.cached_cat_<?php echo $category_layout;?>);
            carprogresscount -= 1;
            carprogressshow();
        }
        else {
            $.get( "index.php?route=product/category/getetmmaillayout&category_id=<?php echo $category_layout;?>", function(data) {
                $('#cat_sub_<?php echo $category_layout; ?>').html(data);
                carprogresscount -= 1;
                carprogressshow();
                if(typeof(Storage) !== "undefined"){
                    sessionStorage.cached_cat_<?php echo $category_layout;?> = data;
                }
            }).fail(function() {
                 // alert( "error" );
            });
        }
    });
</script>
<?php } ?>
<?php foreach($product_layouts as $product_layout){  ?>
   <?php echo $product_layout;?>
<?php } ?>