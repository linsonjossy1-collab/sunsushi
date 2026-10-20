<script type="text/javascript" src="catalog/view/javascript/jquery/jquery.jcarousel-swipe.min.js"></script>
<?php
$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback where status=1 ORDER BY date_added  ");
$feedbacks=$query->rows;
?>
<div id="feedbk" class="col-sm-12">
    <div class="jcarousel-wrapper">
        <div id="carousel_feedback" class="jcarousel maincaorousel">
            <ul class="">
                <?php foreach($feedbacks as $feedback){ ?>
                <li class="carouselinnerfeedback">
                    <h3><?php echo html_entity_decode($feedback['feedback'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    
                    <span class="fa fa-star checked"></span>
<span class="fa fa-star checked"></span>
<span class="fa fa-star checked"></span>
<span class="fa fa-star checked"></span>
<span class="fa fa-star checked"></span>

                    <p><?php echo $feedback['name']; ?></p>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
//$('#carousel<?php echo $module; ?>').jcarousel();
  (function($) {
    $(function() {
        var jcarouselfeeback = $('#carousel_feedback');

       $('#carousel_feedback')
            .on('jcarousel:reload jcarousel:create', function () {
                var carousel = $(this),
                    width = carousel.innerWidth();

                if (width >= 600) {
                    width = width / 1;
                } else if (width >= 350) {
                    width = width / 1;
                }

                carousel.jcarousel('items').css('width', Math.ceil(width) + 'px');
            })
            .jcarousel({
                wrap: 'circular'
            }).jcarouselAutoscroll({ 
                interval: 30000,
                target: '+=1',
                autostart: true
            }).jcarouselSwipe({ perSwipe: 1 });      
    });
})(jQuery);
</script>