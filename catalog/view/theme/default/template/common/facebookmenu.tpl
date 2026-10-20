
<html>
    <head>
        <script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
        <link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
        <script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
        <script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
        <link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />
        <script type="text/javascript" src="catalog/view/javascript/jquery/tabs.js"></script>
        <script type="text/javascript" src="catalog/view/javascript/common.js"></script>
        <link rel="stylesheet" type="text/css" href="catalog/view/fbmenu/fbmenu.css" />
              <script>
            $(function() {
                 $( "#accordion" ).accordion(
                 { autoHeight: false,
                  collapsible: true}   
                 );
            });
        </script>
    </head>
    <body>
        <div id="accordion">
            <h3 class="fbabout"><a href="#">About us</a></h3><div class="aboutus">
                <div class='columnl'>

<div class='address'>
<p class='shop_address'>
<?php echo $this->config->get('config_address'); ?>
</p>
<p class='contact_details'>
Tel:
<strong>
<?php echo $this->config->get('config_telephone'); ?>
</strong>
</p>
<dl>
</dl>
</div>                    
<div class='opening_hours'>
<table class="opening-hours" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>Monday</td>
            <td>
                <table>
                    
                    <?php $monday_times=$this->config->get('monday_store_times');?> 
                    
            <?php foreach($monday_times as $monday_time) { 
            if(!empty($monday_time['start'])){
            $start=explode(':',$monday_time['start']);
            $end=explode(':',$monday_time['end']);
            ?><tr><?php
            if((int)$start[0]>=12){
            ?>
            <td><?php echo (((int)$start[0]-12)!=0?(int)($start[0]-12):12).':'.$start[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$start[0].':'.$start[1];?> AM</td>
          <?php
            }
            if((int)$end[0]>=12){
            ?>
            <td><?php echo (((int)$end[0]-12)!=0?(int)($end[0]-12):12).':'.$end[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$end[0].':'.$end[1];?> AM</td>
          <?php
            }
            ?> </tr><?php
            }
            else{
            ?>
            <tr>Closed</tr>
            <?php
            }
            } ?>
                   
                </table>    
            
            </td>
            
        </tr>
        <tr>
            <td>Tuesday</td>
            <td>
                <table>
                    
                    <?php $tuesday_times=$this->config->get('tuesday_store_times');?>    
            <?php foreach($tuesday_times as $tuesday_time) { 
            if(!empty($tuesday_time['start'])){
            $start=explode(':',$tuesday_time['start']);
            $end=explode(':',$tuesday_time['end']);
            ?><tr><?php
            if((int)$start[0]>=12){
            ?>
            <td><?php echo (((int)$start[0]-12)!=0?(int)($start[0]-12):12).':'.$start[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$start[0].':'.$start[1];?> AM</td>
          <?php
            }
            if((int)$end[0]>=12){
            ?>
             <td><?php echo (((int)$end[0]-12)!=0?(int)($end[0]-12):12).':'.$end[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$end[0].':'.$end[1];?> AM</td>
          <?php
            }
            ?> </tr><?php
            }
            else{
            ?>
            <tr>Closed</tr>
            <?php
            }
            } ?>
                   
                </table>    
            
            </td>
        </tr>
        <tr>
            <td>Wednesday</td>
            <td>
                <table>
                    
                    <?php $wednesday_times=$this->config->get('wednesday_store_times');?>    
            <?php foreach($wednesday_times as $wednesday_time) { 
            if(!empty($wednesday_time['start'])){
            $start=explode(':',$wednesday_time['start']);
            $end=explode(':',$wednesday_time['end']);
            ?><tr><?php
            if((int)$start[0]>=12){
            ?>
            <td><?php echo (((int)$start[0]-12)!=0?(int)($start[0]-12):12).':'.$start[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$start[0].':'.$start[1];?> AM</td>
          <?php
            }
            if((int)$end[0]>=12){
            ?>
           <td><?php echo (((int)$end[0]-12)!=0?(int)($end[0]-12):12).':'.$end[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$end[0].':'.$end[1];?> AM</td>
          <?php
            }
            ?> </tr><?php
            }
            else{
            ?>
            <tr>Closed</tr>
            <?php
            }
            } ?>
                   
                </table>    
            
            </td>
        </tr>
        <tr>
            <td>Thursday</td>
            <td>
                <table>
                    
                    <?php $thursday_times=$this->config->get('thursday_store_times');?>    
            <?php foreach($thursday_times as $thursday_time) { 
            if(!empty($thursday_time['start'])){
            $start=explode(':',$thursday_time['start']);
            $end=explode(':',$thursday_time['end']);
            ?><tr><?php
            if((int)$start[0]>=12){
            ?>
            <td><?php echo (((int)$start[0]-12)!=0?(int)($start[0]-12):12).':'.$start[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$start[0].':'.$start[1];?> AM</td>
          <?php
            }
            if((int)$end[0]>=12){
            ?>
            <td><?php echo (((int)$end[0]-12)!=0?(int)($end[0]-12):12).':'.$end[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$end[0].':'.$end[1];?> AM</td>
          <?php
            }
            ?> </tr><?php
            }
            else{
            ?>
            <tr>Closed</tr>
            <?php
            }
            } ?>
                   
                </table>    
            
            </td>
        </tr>
        <tr>
            <td>Friday</td>
            <td> <table>
                    
                    <?php $friday_times=$this->config->get('friday_store_times');?>    
            <?php foreach($friday_times as $friday_time) { 
            if(!empty($friday_time['start'])){
            $start=explode(':',$friday_time['start']);
            $end=explode(':',$friday_time['end']);
            ?><tr><?php
            if((int)$start[0]>=12){
            ?>
            <td><?php echo (((int)$start[0]-12)!=0?(int)($start[0]-12):12).':'.$start[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$start[0].':'.$start[1];?> AM</td>
          <?php
            }
            if((int)$end[0]>=12){
            ?>
            <td><?php echo (((int)$end[0]-12)!=0?(int)($end[0]-12):12).':'.$end[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$end[0].':'.$end[1];?> AM</td>
          <?php
            }
            ?> </tr><?php
            }
            else{
            ?>
            <tr>Closed</tr>
            <?php
            }
            } ?>
                   
                </table></td>
        </tr>
        <tr>
            <td>Saturday</td>
          <td>  <table>
                    
                    <?php $saturday_times=$this->config->get('saturday_store_times');?>    
            <?php foreach($saturday_times as $saturday_time) { 
             if(!empty($saturday_time['start'])){
            $start=explode(':',$saturday_time['start']);
            $end=explode(':',$saturday_time['end']);
            ?><tr><?php
            if((int)$start[0]>=12){
            ?>
            <td><?php echo (((int)$start[0]-12)!=0?(int)($start[0]-12):12).':'.$start[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$start[0].':'.$start[1];?> AM</td>
          <?php
            }
            if((int)$end[0]>=12){
            ?>
            <td><?php echo (((int)$end[0]-12)!=0?(int)($end[0]-12):12).':'.$end[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$end[0].':'.$end[1];?> AM</td>
          <?php
            }
            ?> </tr><?php
            }
            else{
            ?>
            <tr>Closed</tr>
            <?php
            }
            } ?>
                   
                </table></td>
        </tr>
        <tr>
            <td>Sunday</td>
           <td> <table>
                    
                    <?php $sunday_times=$this->config->get('sunday_store_times');?>    
            <?php foreach($sunday_times as $sunday_time) { 
            if(!empty($sunday_time['start'])){
            $start=explode(':',$sunday_time['start']);
            $end=explode(':',$sunday_time['end']);
            ?><tr><?php
            if((int)$start[0]>=12){
            ?>
            <td><?php echo (((int)$start[0]-12)!=0?(int)($start[0]-12):12).':'.$start[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$start[0].':'.$start[1];?> AM</td>
          <?php
            }
            if((int)$end[0]>=12){
            ?>
            <td><?php echo (((int)$end[0]-12)!=0?(int)($end[0]-12):12).':'.$end[1];?> PM</td>
            <?php
            }else{
            ?>
            <td><?php echo (int)$end[0].':'.$end[1];?> AM</td>
          <?php
            }
            ?> </tr><?php
            }
            else{
            ?>
            <tr>Closed</tr>
            <?php
            }
            } ?>
                   
                </table></td>
        </tr>
    </table>
</div>
</div>
<div class='columnr'>
<div style='clear: both;'></div>
<div id='find-us-map' style='width: 298px; height: 298px; border: 1px solid gray;'></div>
<script>
  //<![CDATA[
    function initialize_map() {
      var myOptions = {
        zoom: 16,
        mapTypeControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }
      var map = new google.maps.Map($("#find-us-map")[0], myOptions);
      findAddress(new google.maps.Geocoder(), map);
    }
    
    function loadScript() {
      var script = document.createElement("script");
      script.type = "text/javascript";
      script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + "maps.google.com/maps/api/js?sensor=false&region=GB&callback=initialize_map";
      document.body.appendChild(script);
    }
    
    function findAddress(geocoder, map) {
      var address = '<?php echo str_replace("\n",",",$this->config->get("config_address")); ?>';
      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
          var marker = new google.maps.Marker({
              map: map, 
              position: results[0].geometry.location
          });
        }
      });
    }
    
    $(function() {
      loadScript(); // load script onload to avoid hanging the browser
    });
  //]]>
</script>
</div>
</div>
            <?php
            
            foreach ($results as $categories) {
            
             echo '<h3><a href="#">' . $categories['name'] . '</a></h3><div><ul>';
              if(isset($categories['sub_cats'])) { 
              foreach($categories['sub_cats'] as $sub_category){
              if(isset($sub_category['sub_2_cat'])) {
              echo '<li class="productoursite"><a target="freshfillingz" href="/menu/#cat_'. $categories['category_id'] .'"><span class="prodname">' . $sub_category['name'] . '</span><span class="prodprice">&pound; '. number_format($sub_category['pr_least_price'], 2, '.', '') . '</span></a></li>';
              }else{
              echo '<li class="productoursite"><a target="freshfillingz" href="../menu/#cat_'. $categories['category_id'] .'"><span class="prodname">' . $sub_category['name'] . '</span><span class="prodprice">&pound; '. number_format($sub_category['pr_least_price'], 2, '.', '') . '</span></a></li>';
              }
              }
              }
              if(isset($categories['products'])) {
              foreach($categories['products'] as $all_products){
              echo '<li class="productoursite"><a target="freshfillingz" href="../menu/#cat_'. $categories['category_id'] .'"><span class="prodname">' . $all_products['name'] . '</span><span class="prodprice">&pound; '. number_format($all_products['price'], 2, '.', '') . '</span></a></li>';
              }
              }
            echo '<li class="orderoursite"><div class="order_button"><a target="ordys" href="../menu/#cat_'. $categories['category_id'] .'">Order from our website</a></div></li>';
            echo '</ul></div>';
            }
            ?>
        </div>

    </body>
</html>

