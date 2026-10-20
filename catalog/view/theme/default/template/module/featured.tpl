<div class="jcarousel-wrapper">
    <div id="featuredprd" class="jcarousel fautrd_carousel">
        <ul class="">
            <?php foreach ($products as $product) { ?>
                <li>
                    
                    <div class="in_fff">
                        <a href="<?php echo $product['href']; ?>">
                            <!-- <?php echo $product['name']; ?> -->
                            <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                        </a>
                    </div>
                    <?php if ($product['price']) { ?>
                        <div class="price">
                          <?php if (!$product['special']) { ?>
                          <?php if ($product['option_price']=='0') { ?>
                          <?php echo $product['price']; ?>
                          <?php } else { ?>
                            <?php echo $product['option_price']; ?>
                          <?php }  ?>

                          <?php } else { ?>
                          <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
                          <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="name">        
                        <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                        <div class="forder"><a href="<?php echo $product['href']; ?>"><img width="149" height="35" class="img-responsive" src="/catalog/view/theme/default/stylesheet/images/forder.png" alt="<?php echo $product['name']; ?>" /></a></div>
                    </div>
                </li>
             <?php } ?> 
       </ul>
    </div>
    <a href="#" class="jcarousel-control-prev1"></a>
    <a href="#" class="jcarousel-control-next1"></a>
</div>
    <!-- <a href="#" class="jcarousel-control-prev1">&lsaquo; Prev</a>
    <a href="#" class="jcarousel-control-next1">Next &rsaquo;</a> -->



    <script type="text/javascript">
  (function($) {
    $(function() {
       // var jcarousel = $('#featuredprd');

        $('#featuredprd')
            .on('jcarousel:reload jcarousel:create', function () {
                var carousel = $(this),
                    width = carousel.innerWidth();

                if (width >= 600) {
                    width = width / 3;
                } else if (width >= 350) {
                    width = width / 2;
                }

                carousel.jcarousel('items').css('width', Math.ceil(width) + 'px');
            })
            .jcarousel({
                wrap: 'circular'
            }).jcarouselAutoscroll({ 
                interval: 7000,
                target: '+=1',
                autostart: true
            });;

        $('.jcarousel-control-prev1')
            .jcarouselControl({
                target: '-=1'
            });

        $('.jcarousel-control-next1')
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