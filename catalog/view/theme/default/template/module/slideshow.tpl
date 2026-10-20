<div class="slideshow">
  <div id="slideshow<?php echo $module; ?>" class="nivoSlider">
    <?php $id = 0; ?>
    <?php foreach ($banners as $banner) {     
        if ($banner['link']) { ?>            
            <a href="<?php echo $banner['link']; ?>">
                <?php $img = $banner['image'] ; ?>
                <div class="sliderelement" style="background-image: url('<?php echo $img; ?>');" ><img src="<?php echo $banner['image']; ?>" style="height:2px; width:4px; display:none;" alt="" /></div>                
            </a>
            <?php } else { ?>
            <?php $img = $banner['image'] ; ?>
            <div class="sliderelement" style="background-image: url('<?php echo $img; ?>');" ></div>
            <!--<img style="background-image: url('<?php echo $img; ?>');"  alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" />-->
           <?php }
           $id++; 
                  
        } ?>  
  </div>
  <?php 
    $des_id = 0;
    foreach ($banners as $banner) { 
        if($banner['description']){
            echo '<div id="description_'.$des_id .'" class="nivo-caption">'.  html_entity_decode($banner['description']) .'</div>';  //strip_tags(html_entity_decode($banner['description']))
        }
        if(!empty($banner['product_info'])){
            echo '<div class="prodct_info"><div class="product_name">'.  strip_tags(html_entity_decode($banner['product_info']['name'])) .'</div><div class="product_description">'.  html_entity_decode($banner['product_info']['description']) .'</div><div class="product_price">'.  strip_tags(html_entity_decode($banner['product_info']['price'])).'</div></div>';
        } 
        $des_id++;
    } 
?>
</div>



<script type="text/javascript">
$(document).ready(function() {
	$('#slideshow<?php echo $module; ?>').nivoSlider();
});
</script>         