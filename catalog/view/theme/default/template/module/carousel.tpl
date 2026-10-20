<div class="jcarousel-wrapper" id="carouselwrapper<?php echo $module; ?>">
    <div id="carousel<?php echo $module; ?>" class="jcarousel maincaorousel">
      <ul class="">
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
      </ul>

    </div>
</div>

<script type="text/javascript">
//$('#carousel<?php echo $module; ?>').jcarousel();
  (function($) {
    $(function() {
        var jcarousel<?php echo $module; ?> = $('#carousel<?php echo $module; ?>');

        jcarousel<?php echo $module; ?>
            .on('jcarousel:reload jcarousel:create', function () {
                var carousel = $(this),
                    width = carousel.innerWidth();

                if (width >= 600) {
                    width = width / <?php echo $limit; ?>;
                } else if (width >= 350) {
                    width = width / 1;
                }

                carousel.jcarousel('items').css('width', Math.ceil(width) + 'px');
            })
            .jcarousel({
                wrap: 'circular'
            }).jcarouselAutoscroll({ 
                interval: 7000,
                target: '+=1',
                autostart: true
            });

        $('#carousel<?php echo $module; ?> .jcarousel-control-prev')
            .jcarouselControl({
                target: '-=1'
            });

        $('#carousel<?php echo $module; ?> .jcarousel-control-next')
            .jcarouselControl({
                target: '+=1'
            });

        $('.jcarousel-pagination')
            .on('jcarouselpagination:active', 'a', function() {
                $(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                $(this).removeClass('active');
            })
            .on('click', function(e) {
                e.preventDefault();
            })
            .jcarouselPagination({
                perPage: 1,
                item: function(page) {
                    return '<a href="#' + page + '">' + page + '</a>';
                }
            });
    });
})(jQuery);
</script>