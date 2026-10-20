<div class="banner-main">
  <div id="banner<?php echo $module; ?>" class="col-sm-4">
    <?php foreach ($banners as $banner) { ?>
        <?php if(isset($banner['description']) || !empty($banner['product_info'])) {
           if($banner['description']){
                echo '<li class="description">'.  html_entity_decode($banner['description']) .'</li>';  //strip_tags(html_entity_decode($banner['description']))
            }
            if(!empty($banner['product_info'])){
                echo '<div class="prodct_info"><div class="product_name">'.  strip_tags(html_entity_decode($banner['product_info']['name'])) .'</div><div class="product_description">'.  html_entity_decode($banner['product_info']['description']) .'</div><div class="product_price">'.  strip_tags(html_entity_decode($banner['product_info']['price'])).'</div></div>';
            } 
        }  else{ 
           if ($banner['link']) { ?>
            <li>
            <a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" /></a><?php } else { ?>
            <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" />
           <?php } ?>
           </li>
        <?php } }?>
  </div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('#banner<?php echo $module; ?> div:first-child').css('display', 'block');
});

var banner = function() {
	$('#banner<?php echo $module; ?>').cycle({
		before: function(current, next) {
			$(next).parent().height($(next).outerHeight());
		}
	});
}

setTimeout(banner, 2000);
//--></script>