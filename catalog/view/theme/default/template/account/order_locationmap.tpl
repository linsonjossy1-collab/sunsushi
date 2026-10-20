  <input type="hidden" id='lastposition' value=''/>
  <?php if($config_browserapp_api && $map) { $map_set = true; ?>
    <style type="text/css">
    .gmap {
        height: 500px;
        width: 600px;
        display: block;
    }
    </style>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

    <script async
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $config_browserapp_api; ?>&loading=async&callback=initMap&libraries=drawing&v=weekly">
</script>
    <div id="order_track_map_outter">
        <div id="order_track_map" class="gmap"></div>
    </div>
    <script type="text/javascript">
    var map;
    var directionsService;
    var directionsRendererFrom;
    var directionsRendererTO;
    var mapbath = '<?php echo $map_image_base_api; ?>';
    var mapicons;
    var start_position = <?php echo $store_position; ?>;//{ lat: 54.00089566511362, lng: -1.562413289009098 };// Replace with Takeaway postcode
    var end_position =  <?php echo $customer_position; ?>;//{ lat: 54.00119900000001, lng: -1.5536311 };// Replace with Customer postcode
    var markers = [];
    function initMap() {
        directionsService = new google.maps.DirectionsService();
        directionsRendererFrom = new google.maps.DirectionsRenderer({
            polylineOptions: {
              strokeColor: "#9f9f9f",
              strokeWeight: 6,
              strokeOpacity: 0.7
            },
            suppressMarkers: true
        });
        directionsRendererTO = new google.maps.DirectionsRenderer({
            polylineOptions: {
              strokeColor: "#13abda",
              strokeWeight: 7,
              strokeOpacity: 0.8
            },
            suppressMarkers: true
        });

        var center = new google.maps.LatLng( end_position.lat,end_position.lng);
        var mapOptions = {
          zoom:16,
          center: center
        };
        
        
        
        mapicons = {
        shop: new google.maps.MarkerImage( mapbath + 'app_icons_shop.png',
            // (width,height)
            new google.maps.Size(40,40),
            // The origin point (x,y)
            new google.maps.Point(0, 0),
            // The anchor point (x,y)
            new google.maps.Point(22, 32)),
        drive: new google.maps.MarkerImage(
            // URL
            mapbath + 'app_icons_driver.png',
            // (width,height)
            new google.maps.Size(40,40),
            // The origin point (x,y)
            new google.maps.Point(0, 0),
            // The anchor point (x,y)
            new google.maps.Point(22, 32)),
        home: new google.maps.MarkerImage(
            // URL
            mapbath + 'app_icons_home.png',
            // (width,height)
            new google.maps.Size(40,40),
            // The origin point (x,y)
            new google.maps.Point(0, 0),
            // The anchor point (x,y)
            new google.maps.Point(22, 32))
        };
        map = new google.maps.Map(document.getElementById('order_track_map'), mapOptions);
        
        //draw_takaway_path({ lat: 54.00356379999999, lng: -1.5576532 });
        document.getElementById('lastposition').value = '<?php echo $driver_postion;?>';
        draw_takaway_path(<?php echo $driver_postion;?>);
        
    }
    function drawRouteFrom(driver_postion) {
        var mrequest = {
            origin:start_position,
            destination:driver_postion,
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.IMPERIAL
        };
        directionsService.route(mrequest, function(response, status) {
            if(status == google.maps.DirectionsStatus.OK) {
                directionsRendererFrom.setDirections(response); 
                var leg = response.routes[0].legs[0];
                makeMarker(leg.start_location, mapicons.shop, "Takeaway", map);
            }
        });
    }
    function drawRouteTo(driver_postion) {
        var mrequest = {
            origin:driver_postion,
            destination:end_position,
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.IMPERIAL
        };
        directionsService.route(mrequest, function(response, status) {
            if(status == google.maps.DirectionsStatus.OK) {
                directionsRendererTO.setDirections(response); 
                var leg = response.routes[0].legs[0];
                makeMarker(leg.start_location, mapicons.drive, "Driver", map);
                makeMarker(leg.end_location, mapicons.home, 'Home', map);
            }
        });
    }
        
    function makeMarker(position, icon, title, map) {
        markers[markers.length] =  new google.maps.Marker({
            position: position,
            map: map,
            icon: icon,
            title: title
        });
    }
    
    function draw_takaway_path( driver_postion) {
        // Clear marker and Routes
      //  directionsRendererFrom.setMap(null); 
     //   directionsRendererTO.setMap(null); 
        for(var i=0; i < markers.length; i++){
            markers[i].setMap(null);
        }
        markers = new Array();
        
        directionsRendererFrom.setMap(map);
        drawRouteFrom(driver_postion);
        directionsRendererTO.setMap(map);
        drawRouteTo(driver_postion);
    }

    var dInterVal = setInterval(function(){
        getdriverlocation();
    }, 10000);
    

function getdriverlocation()  {
  $.ajax({ cache: false,
      url: "index.php?route=account/order/location",
      data: { order_id:<?php echo $order_id;?> },
      dataType: "json",
  }).done(function (json) {
    if(json) {
      //draw_takaway_path(json);
      
      var lastposition = document.getElementById('lastposition').value;
      if(lastposition!==JSON.stringify(json)) {
        draw_takaway_path(json);
        document.getElementById('lastposition').value = JSON.stringify(json);
      }
      console.log(lastposition);
    }
  }).fail(function (jqXHR, textStatus) {
      $('#error_div').html('<div class="warning">Cannot connect to driver location</div>');
      clearInterval(dInterVal);
  });
}
</script>
 <?php } ?>