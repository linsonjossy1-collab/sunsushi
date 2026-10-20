<?php echo $header; ?>

<div class="container" id="homepage">
  <div class=""><!--
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?><?php //echo $column_right; ?>-->
    
    <div id="content" class="<?php //echo $class; ?>">
      <div id="mobilonly"><?php echo $column_left; ?> </div>
      <div id="hometop">
        <div class="container">
          <?php echo $content_top; ?>

<div class="hero-content">

    <a class="order-card collection-card" href="menu/?&ct=0#" id="on">
        <div class="card-icon">
            <img src="/image/data/collection.png" alt="Click & Collect" title="Click & Collect">
        </div>

        <div class="card-text">
            <span class="card-title">CLICK & COLLECT</span>
            <span class="card-subtitle">Order online &amp; pick up</span>
        </div>

        <div class="card-arrow">
            <img src="/image/data/right.png" alt="right arrow" title="right arrow">
        </div>
    </a>


    <a class="order-card delivery-card" href="menu/?&ct=1#">
        <div class="card-icon">
            <img src="/image/data/delivery.png" alt="Order Delivery" title="Order Delivery">
        </div>

        <div class="card-text">
            <span class="card-title">ORDER DELIVERY</span>
            <span class="card-subtitle">We'll deliver to you</span>
        </div>

        <div class="card-arrow">
            <img src="/image/data/right.png" alt="right arrow" title="right arrow">
        </div>
    </a>

</div>
           <!-- =========== INFO + REVIEWS (side by side on large) =========== -->
  <div class="split-panel">

  <!-- ===================== INFO ===================== -->
  <section class="info-section">
    <div class="info-inner">


      <div class="row">
        <div class="col-12 col-md-4 info-block">
          <div class="info-one">
<i class="fa fa-map-marker" aria-hidden="true"></i>
          </div>
          <div class="info-two">
          <p class="info-label">Address:</p>
          <p class="info-text"><!--<strong>Legal Entity Name</strong> E.D GROUP LIMITED<br>-->
          <?php echo $this->config->get('config_address');?>,<br>
          <?php echo $this->config->get('config_postalcode') ; ?></p>
          </div>
        </div>
        <div class="col-12 col-md-4 info-block">
                  <div class="info-one">
<i class="fa fa-phone" aria-hidden="true"></i>
          </div>
          <div class="info-two">
          <p class="info-label">Phone Number:</p>
          <p class="info-text"><a href="tel:<?php echo $this->config->get ('config_telephone'); ?>"><?php echo $this->config->get ('config_telephone'); ?></a></p>
        </div>
        </div>
         <div class="col-12 col-md-4 info-block">
                           <div class="info-one">
     <i class="fa fa-clock-o" aria-hidden="true"></i>
          </div>
    
      <div class="info-two">
        <p class="info-label">Open Hours:</p>
        <p class="info-text" style="margin-bottom:8px;">Tuesday - Sunday</p>
        <p class="info-text">2:00 pm - 9:00 pm</p>
      </div>
        </div>
      </div>



    <!--  <a href="menu/?&amp;ct=0#" id="on" class="btn-outline-dark-ss">
        <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="4" y="6" width="9" height="13" rx="1.5" stroke="#111111" stroke-width="1.8"/>
          <path d="M14 9h4l1 4-1 6h-4" stroke="#111111" stroke-width="1.8" stroke-linejoin="round"/>
          <line x1="6.5" y1="3.5" x2="6.5" y2="6" stroke="#111111" stroke-width="1.8" stroke-linecap="round"/>
          <line x1="10.5" y1="3.5" x2="10.5" y2="6" stroke="#111111" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        <span class="label">Click &amp; Collect</span>
      </a>

      <a href="menu/?&amp;ct=1#" id="on" class="btn-outline-dark-ss">
        <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M4 8l8-4 8 4-8 4-8-4z" stroke="#111111" stroke-width="1.8" stroke-linejoin="round"/>
          <path d="M4 8v8l8 4 8-4V8" stroke="#111111" stroke-width="1.8" stroke-linejoin="round"/>
          <line x1="12" y1="12" x2="12" y2="20" stroke="#111111" stroke-width="1.8"/>
        </svg>
        <span class="label">Order Delivery</span>
      </a>-->
    </div>
  </section>

  <!-- ===================== REVIEWS ===================== -->
  <section class="dark-section reviews-section">
    <h2 class="section-heading">Reviews</h2>

    <div class="review-card">
      <p class="review-name" id="rv-name">A. Customer</p>
      <p class="review-date" id="rv-date">2025-07-01</p>
      <div class="review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
      <p class="review-body" id="rv-body">
        I&rsquo;d easily give this place a 10 out of 10. Their sushi is absolutely outstanding &ndash; really top quality. The ramen was also brilliant. You can really tell the owner cares about the quality and takes pride in what they serve. I was seriously impressed. I&rsquo;ll definitely be coming back again.
      </p>

      <div class="review-nav">
        <button type="button" id="rv-prev" aria-label="Previous review">&larr;</button>
        <button type="button" id="rv-next" aria-label="Next review">&rarr;</button>
      </div>
    </div>
  </section>

  </div><!-- /.split-panel -->


        </div>
      </div>
    </div>
  </div>
  <div style="clear:both;"></div>
      <div id="homebottom">
        <div class="container"> <?php echo $content_bottom; ?>
        
        <section class="dark-section about-section">
    <h2 class="section-heading">About Us</h2>

    <div class="row about-grid">
      <div class="col-12 col-lg-6" style="margin-bottom:30px;">
        <img class=" about-plate" src="/image/data/abt.jpg" alt="Sushi plate" title="Sushi plate">
      </div>
      <div class="col-12 col-lg-6">
        <p class="about-text">
          Fresh Sushi, Ramen & Asian Fusion in Northampton </p> <p class="about-text">Freshly prepared sushi, ramen, Katsu Curry, noodles and Asian fusion favourites available for delivery or click & collect across Northampton.</p> <p class="about-text">Fast, convenient and made to order.
        </p>

        <div class="about-buttons">
         <a class="order-card collection-card" href="menu/?&amp;ct=0#" id="on">
        <div class="card-icon">
            <img src="/imgd/collection.png" alt="Click &amp; Collect" title="Click &amp; Collect">
        </div>

        <div class="card-text">
            <span class="card-title">CLICK &amp; COLLECT</span>
            <span class="card-subtitle">Order online &amp; pick up</span>
        </div>

        <div class="card-arrow">
            <img src="/imgd/right.png" alt="right arrow" title="right arrow">
        </div>
    </a>


    <a class="order-card delivery-card" href="menu/?&amp;ct=1#">
        <div class="card-icon">
            <img src="/imgd/delivery.png" alt="Order Delivery" title="Order Delivery">
        </div>

        <div class="card-text">
            <span class="card-title">ORDER DELIVERY</span>
            <span class="card-subtitle">We'll deliver to you</span>
        </div>

        <div class="card-arrow">
            <img src="/imgd/right.png" alt="right arrow" title="right arrow">
        </div>
    </a>
        </div><!-- /.about-buttons -->
      </div><!-- /.col -->
    </div><!-- /.row -->
  </section>
  


  <section class="gallery-section">
    <div class="gallery-inner">
      <div class="row no-gutters">
        <div class="col-6">
          <img class="gallery-img" src="/image/data/y1.jpg" alt="Sushi dish" title="Sushi dish">
        </div>
        <div class="col-6">
          <img class="gallery-img" src="/image/data/y2.jpg" alt="Rice bowl dish" title="Rice bowl dish">
        </div>
      </div>
    </div>
  </section>



   </div>
      </div>
    </div>
  </div>
</div>
<?php echo $postcodehome; ?>
<?php if($this->config->get('config_home_notification_popup_content')) { ?>
<div id="notfctnPopup" class="modal fade">
  <div class="modal-dialog">
        <div class="modal-content">
          <?php if($this->config->get('config_home_notification_popup_heading')) { ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $this->config->get('config_home_notification_popup_heading'); ?></h4>
            </div>
            <?php } ?>
            <div class="modal-body">
                <?php echo htmlspecialchars_decode($this->config->get('config_home_notification_popup_content')); ?>
            </div>
        </div>
    </div>
</div>          
<script type="text/javascript">$(document).ready(function(){$("#notfctnPopup").modal("show")});</script>
<?php } ?>
<!--<script type="text/javascript">$(function(){$(".cycle-slideshow").cycle({timeout:8e3,random:!0})});</script>-->
<script type="text/javascript">$(document).ready(function(){$("body").addClass("home_page_tel"),$("#homepage .wback").each(function(a){$(this).attr("id","wback_"+a)})});</script> 

<script>
    // Simple review carousel
    var reviews = [
      {
        name: "A. Customer", date: "2025-07-01", stars: 5,
        body: "I\u2019d easily give this place a 10 out of 10. Their sushi is absolutely outstanding \u2013 really top quality. The ramen was also brilliant. You can really tell the owner cares about the quality and takes pride in what they serve. I was seriously impressed. I\u2019ll definitely be coming back again."
      },
      {
        name: "Sarah M.", date: "2025-06-18", stars: 5,
        body: "Best sushi in Northampton by a mile. The delivery was quick, everything arrived fresh and beautifully presented. The salmon nigiri melts in your mouth. Highly recommend!"
      },
      {
        name: "James T.", date: "2025-05-29", stars: 5,
        body: "Ordered for a family night in and everyone loved it. Generous portions, authentic flavours and great value. SunSushi has become our regular weekend treat."
      }
    ];

    var idx = 0;
    var nameEl = document.getElementById('rv-name');
    var dateEl = document.getElementById('rv-date');
    var bodyEl = document.getElementById('rv-body');

    function render() {
      var r = reviews[idx];
      nameEl.textContent = r.name;
      dateEl.textContent = r.date;
      bodyEl.textContent = r.body;
    }
    document.getElementById('rv-prev').addEventListener('click', function () {
      idx = (idx - 1 + reviews.length) % reviews.length; render();
    });
    document.getElementById('rv-next').addEventListener('click', function () {
      idx = (idx + 1) % reviews.length; render();
    });
  </script>
  <?php echo $footer; ?>