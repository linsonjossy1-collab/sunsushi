<div id="before_you_order">
  <div class="box-heading">Your Order</div>
  <!--<?php if(isset($store_closed) && $store_closed!=''){ ?>
    <div id="menu_closed_msg" class="alert alert-warning">
      <?php echo $store_closed; ?>
    </div>
  <?php } ?>-->
  
  <?php if(!isset($tableordering)){ ?>
  <div class="col-sm-12" id="ship_select">
    <div id="shippingmessage5" class="alert alert-warning" style="display: none" role="alert">
      <strong>Select a shipping method&nbsp;<i class="fa fa-arrow-down" aria-hidden="true"></i></strong>
    </div>
  </div>
  <div class="col-sm-12" id="before_bg">
  <div class="bixxx">
    <?php $collect_time = '<br>'; ?>
    <?php if(!$store_closed) { ?>
    <?php if(isset($store_collection_time)) { ?>
    <?php $collect_time = 'Approx: <span>'.$store_collection_time.' min</span>'; ?>
    <?php } ?>
    <?php } else if($collection_start_time){ ?>
    <?php $collect_time = 'Starts at : <span>'.date('h:i a',$collection_start_time).'</span>'; ?>
    <?php } ?>

    <?php if ($shipping_methods) { ?>
    <?php foreach ($shipping_methods as $shipping_method) { ?>
    <?php //if(!empty($shipping_method['quote'])) { ?>
    <?php // foreach ($shipping_method['quote'] as $quote) { ?>
    
    <div class="col-sm-6 deliv ddd <?php echo ($shipping_method[ 'code']==$delivery_method? 'delselected': ''); ?> <?php echo $shipping_method['title']; ?>" id="before_border">
      <label class="before_radio">
        <?php echo $shipping_method['title']; ?>
        <input type="radio" <?php echo ($shipping_method[ 'code']==$delivery_method? 'checked="checked"': ''); ?> name="shipping_method" value="<?php echo $shipping_method['code']; ?>"  onclick="selectShippingMethod('<?php echo $shipping_method['code']; ?>')">



        
        <?php if($shipping_method['code']=='home.home') { ?>
          <div id="postcodealert4" class="alert alert-success" style="display: none">
            <strong>unavailable</strong>
          </div>          
  
          <?php if($store_closed) { ?>

          <?php if($home_delivery_start_time) { ?>
          <p>Starts at: <span><?php echo date('h:i a',$home_delivery_start_time); ?></span></p>
          <?php } else { ?>
          <br>
          <?php } ?>
          <?php } else if(isset($store_delivery_time)){ ?>
          <p>Approx : <span><?php echo $store_delivery_time; ?> min</span></p>
          <?php } else {?>
          <br>
          <?php } ?>

        <?php } else { ?>
        <p>
        <?php echo $collect_time; ?></p>
        <?php } ?>
      </label>




         
    </div>
    <?php //} ?>
    <?php //} ?>
    <?php } ?>
    <?php } ?>  </div>
    <div id="collapsePostcode" class="col-sm-12" <?php echo ('home.home'!=$delivery_method?'style="display: none"': ''); ?> >
      <!--<div class="col-sm-12" id="cart_postcode_box" <?php if($postcode) { ?> style="display: none" <?php } ?>>
        <div id="dialog-formH">
          <h3>Enter Delivery PostCode</h3>
          <form>
            <input type="text" name="postcodepopuph" placeholder="Enter Postcode" id="postcodepopuph" class="" value="<?php echo $postcode; ?>" />
            <button type="button" onclick="$('#before_bg input[value=\'home.home\']').prop('checked',true); postcode_check();">CHECK DELIVERY</button>
            <div class="need1" style="text-align: center; font-size: 11px; font-style: italic;">
              <a href="http://www.royalmail.com/postcode-finder/" target="_blank">Don't know your postcode?</a>
            </div>
          </form>
        </div>
      </div>-->
      <div class="col-sm-12" id="cart_postcode_box_dispaly" <?php if(!$postcode) { ?> style="display: none" <?php } ?>>
        <h3 id="your_post">Your PostCode: <?php echo $postcode; ?></h3> <a onclick="cartpostcodechange()" id="post_change"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
      </div>
      <!--
      <div class="col-sm-12">
        <div id="postcodemessage4" class="alert alert-danger" style="display: none" role="alert">
          <strong>Postcode is not valid</strong>
        </div>
        <div id="postcodealert1" class="alert alert-danger" style="display: none">
          <strong>Sorry we are closed. </strong>
          <span>Please check our opening hours.</span>
          <span id="message5" style="display: none" class="mess5">Sorry we do not deliver to your post code.</span>
        </div>
        <div id="postcodealert3" class="alert alert-danger" style="display: none">
          <strong>Sorry we do not deliver to your post code.</strong> You can place an order and Collect from Store.
        </div>
      </div>-->
    </div>
    <div class="col-sm-12" id="cart_requested_times" s>
        <?php // echo $requested_times; ?>
    </div>
  </div>
<?php } ?>      
  <script type="text/javascript">
    var delivery_method = '<?php echo $delivery_method; ?>';
    var postcode = '<?php echo $postcode; ?>';
    function selectShippingMethod(code) {
        $('#change_del_req_div').modal('hide');
        $("#choosemethod").modal("show");
        selectShippingMethodnew(code);
        return;
        
      delivery_method = '';
      $('#cart_requested_times').html('');
      // $('#collapsePostcode').hide();
      $('#shippingmessage5').hide();
      if (code == 'home.home') {
        $('#collapsePostcode').show();
        if(postcode !='') {
          postcode_check();
        }
      }
      else {
        $('#collapsePostcode').hide();
        
        $.post("index.php?route=checkout/shipping_postcodes/set_delivery_method", { delivery_method: code }, function (data) {
            //  console.log(data);
             var json = jQuery.parseJSON(data);
             delivery_method = json['delivery_method'];
             
         });
         // CHANGE REQ TIME
         $.post("index.php?route=checkout/shipping_methodetm/requestedtime", {shipping_method:code}, function (html) {
							$('#cart_requested_times').html(html);
                                                        $('.pcollect').hide();
                                                        $('.phome').hide();
                                                        del = code.split(".");
                                                        $(".p"+del[0]).show();
                                                        
				});
        reloadcart(code);
      }
    }
    $(document).ready(function() {
       $('input#postcodepopuph').keypress(function(e) {
            if (e.keyCode == '13') {
              e.preventDefault();
              postcode_check();
              //  $(':button:contains("GO")').click();
             }
          });  
      if(postcode !='') {
        //  postcode_check("firsttime");
      } 
      $(document).on('change','#order_requested_time', function () {
        $.ajax({
          url: 'index.php?route=checkout/shipping_methodetm/setrequestedtime',
          type: 'post',
          data: $('#order_requested_time'),
          dataType: 'json',
          success: function (json) {
            //  $('#confirm .checkout-content').html(html);

          },
          error: function (xhr, ajaxOptions, thrownError) {
            //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
      });
    });
    function cartpostcodechange() {
        $('#cart_postcode_box_dispaly').hide();
        $('#cart_postcode_box').show();
    }
    function cart_button_click(){
      if(delivery_method!='') { 
        var input = $("<input>")
                .attr("type", "hidden")
                .attr("name", "delivery_method").val(delivery_method);
        $('#cart-chechout-frm').append($(input));
        var input = $("<input>")
                .attr("type", "hidden")
                .attr("name", "order_requested_time").val($('#order_requested_time').val());
        $('#cart-chechout-frm').append($(input));
        
        $('#cart-chechout-frm').submit(); 
      } else{ $('#shippingmessage5').show();$(window).scrollTop(0);$('#before_bg input[name=shipping_method]').focus(); }
    }
    function postcode_check(firsttime) {
        
        $(".mess4").hide();
        $("#postcodemessage4").hide();
        //$('#postcodealert4').hide();
        postcode = $("#postcodepopuph").val();
        postcode = postcode_formater(postcode);

        $('#postcodepopuph').val(postcode);
        
        var regPostcode = /[A-Z]{1,2}[0-9]{1,2}[A-Z]{0,1} ?[0-9][A-Z]{2}/i;//new reg expression for postcode validation.
        if (regPostcode.test(postcode) == false) {

            $("#postcodemessage4").show();
            $('#postcodepopuph').focus();
            return false;
        }
        else {
                var checkdelivery = $("#before_bg input[name='shipping_method']:checked").val();
                if(checkdelivery!=undefined || delivery_method) { 
                    $.post("index.php?route=checkout/shipping_postcodes", {postcode: postcode, delivery_method: checkdelivery}, function(data) {
                var json = $.parseJSON(data);
                if (json['postcode'] == '') {
                    $("#postcodemessage4").show();
                    //alert("postcode is not valid");
                    $('#postcodepopuph').focus();
                            return false; // NO postcode in DB
                    
                }
                else if (json['delivery_type'] == "collect") {
                    //$('#postcodealert4').show();
                            $('#mes_p_postcode strong').html(postcode);
                    if(json['home_delivery_error']!=undefined){
                        $('#mes_p_postcode').html(json['home_delivery_error']);                        
                    }
                    
                            if(checkdelivery!=undefined) {
                                    if(checkdelivery!='collect.collect') {
                                        $('#before_bg :radio[name="shipping_method"]').prop('checked',false);
                                        delivery_method = '';                     
                                        $('#mes_mod_postcode').modal('show'); 
                        return;
                    }
                            } else if(delivery_method!=='collect.collect') {
                                $('#before_bg :radio[name="shipping_method"]').prop('checked',false);
                                    delivery_method = '';                     
                                    $('#mes_mod_postcode').modal('show');
                                    return; 
                            }  else if(firsttime==undefined) {
                                    $('#mes_mod_postcode').modal('show');
                            } else {
                                    return;
                            }
                    $.post("index.php?route=checkout/shipping_postcodes/set_delivery_method", { delivery_method: 'collect.collect' }, function (data) {
                                    // console.log(data);
                         var json = jQuery.parseJSON(data);
                         delivery_method = json['delivery_method'];
                         $('#before_bg :radio[value="collect.collect"]').prop('checked', true);

                     });
                     // CHANGE REQ TIME
                     $.post("index.php?route=checkout/shipping_methodetm/requestedtime", {shipping_method:'collect.collect'}, function (html) {
                            $('#cart_requested_times').html(html);
                            $('.pcollect').hide();
                            $('.phome').hide();
                            $(".pcollect").show();
                     });
                }
                else {
                    if(firsttime!=undefined){
                        return;
                    }
                    $.post("index.php?route=checkout/shipping_postcodes/set_delivery_method", { delivery_method: 'home.home' }, function (data) {
                                    // console.log(data);
                         var json = jQuery.parseJSON(data);
                         delivery_method = json['delivery_method'];
                                    //$('#before_bg :radio[value="home.home"]').prop('checked', true);

                     });
                     // CHANGE REQ TIME
                     $.post("index.php?route=checkout/shipping_methodetm/requestedtime", {shipping_method:'home.home'}, function (html) {
                            $('#cart_requested_times').html(html);
                            $('.pcollect').hide();
                            $('.phome').hide();
                            $(".phome").show();
                     });
                }
                $('#cart_postcode_box').hide();
                $('#cart_postcode_box_dispaly h3').html('Your PostCode: '+postcode);
                $('#cart_postcode_box_dispaly').show();
                        reloadcart(checkdelivery);
            });
        }
        }


    }
    function setdeliveryday(date){
        $.ajax({
          url: 'index.php?route=checkout/shipping_methodetm/setrequestedday',
          type: 'post',
          data:{order_requested_day: $('#order_requested_day').val(),order_requested_time: $('#order_requested_time').val()}, 
          dataType: 'json',
          success: function (json) {
            //  $('#confirm .checkout-content').html(html);

          },
          error: function (xhr, ajaxOptions, thrownError) {
            //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
    }; 
    function reloadcart(code) {      
    
        if($('#cart').length > 0) {
            $.ajax({
                url: 'index.php?route=checkout/shipping_methodetm',
                dataType: 'html',
                type: 'post',
                data: {shipping_method:code},

                success: function (html) {
                $('#cart').load('index.php?route=module/cart #cart > *');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    }
    function postcode_formater(postcode) { 
        postcode = postcode.replace(/ /g, "");
        var l = postcode.slice(-3);
        var f = postcode.slice(0, (postcode.length - 3));
        postcode = f + ' ' + l;
        postcode = postcode.toUpperCase();
        return postcode;
    }
  </script>
  <!-- /.modal -->
  

</div>
                