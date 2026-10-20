<div class="modal fade" id="choosemethod" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
   
    <div class="modal-content rounded-5" >
        
       <button id="postchead" type="button" onclick="$('#cart_requested_times').hide();$('#cart_requested_time_ok').hide();$('#postcodeselectdiv').hide();closeshippingpopup()" style="display: none" class="small position-absolute close custompositontwo zindex rounded-2 pt-1 pb-1 border-0 shadow-sm shipback1">
            <span aria-hidden="true" class="small">Go Back</span>
        </button>
        <button id="reqhead" type="button" onclick="$('#cart_requested_times').hide();$('#cart_requested_time_ok').hide();$('#postcodeselectdiv').hide();closeshippingpopup()" style="display: none" class="small position-absolute close custompositontwo zindex rounded-2 pt-1 pb-1 border-0 shadow-sm shipback1">
            <span aria-hidden="true" class="small">Go Back</span>
        </button>
        <div class="modal-body row p-0 align-items-center">
           
             
             <div class="col-lg-12 ps-5" id="postcodeselectdiv" style="display: none"> 
             <div class="row">
                             <div class="col-sm-12" >
                             <p class="vsmall alert alert-danger text-center" id="postcodemessage4" style="display: none" role="alert"></p>                        
                    </div>
                <div class="col-sm-12">
                    <h3>ENTER DELIVERY POSTCODE</h3>
                    <input type="text" class="form-control pt-3 pb-3" id="inputdelivery" placeholder="Your Postcode" value="<?php echo $postcode?> ">
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary btn-lg coloronebg text-white pt-3 pb-3 ps-4 pe-4 shadow-sm" onclick="postcode_checknew()">SEARCH</button>
                    <a href="http://www.royalmail.com/postcode-finder/" class="mt-1 vsmall text-dark text-decoration-none text-black-50">Don't know your postcode ?</a>
                </div>

                
                </div>
            </div> 
            
             <div class="col-lg-12 ps-5" id="cart_requested_times" style="display: none">
                          
             </div>
            <div class="col-lg-12 ps-5" id="cart_requested_time_ok" style="display: none">
                <button type="submit" class="btn btn-primary btn-lg coloronebg text-white pt-3 pb-3 ps-4 pe-4 shadow-sm" onclick="confirmreqtime()">OK</button>
            </div>
            
        </div>
    </div>
    
  </div>
   
    
</div>
<div class="modal fade" tabindex="-1" role="dialog" data-target=".bs-example-modal-sm" id="mes_mod_postcode" data-backdrop="false">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" onclick="javascript: $('#mes_mod_postcode').modal('hide');" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"> <?php echo $this->config->get('config_name'); ?></h4>
      </div>
      <div class="modal-body">
          <p id="mes_p_postcode">
             No Home Delivery to this Area.
             
          </p>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="javascript: $('#mes_mod_postcode').modal('hide');">OK</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<!--
<div class="d-flex align-items-center text-white justify-content-between p-2 coloronebg rounded delselsuccess" style="display:<?php echo ($delivery_method=='')?'none !important ':'';  ?> ">
    <small class="d-flex align-items-center">
    <i class="h5 mb-0 fal fa-check-circle me-1"></i>
    <span id="deltime">
        <?php if($delivery_method=='collect.collect'){ ?>
            Collection Time : <?php echo $order_requested_time?>
        <?php }else if ($delivery_method=='home.home'){ ?>
             Delivery to <?php echo $postcode?> At <?php echo $order_requested_time?>
        <?php } ?>
    </span>
    </small>
    <a onclick="$('#choosemethod').modal('show');" class="text-uppercase text-decoration-none text-white" >Change</a>
</div> 

<div class="alert alert-danger text-white d-flex align-items-center justify-content-between p-2 delalert rounded" style="display:<?php echo ($delivery_method=='')?'':'none !important ';  ?> ">
<small class="d-flex align-items-center">
    <i class="h5 mb-0 fal fa-times-circle me-1"></i>
    <span id="deltime">Choose Delivery or Pick up</span>
    </small> 
    <a onclick="$('#choosemethod').modal('show');" class="text-uppercase text-decoration-none text-white" >CHOOSE</a>
</div>
-->
<script type="text/javascript">
    var popupselect=0;
    $(document).ready(function(){
        
        <?php if(!isset($delivery_method) || $delivery_method==''){ ?>
               //$("#choosemethod").modal("show");
        <?php } ?>
        $(document).on('change','#order_requested_time', function () {
           if(!$("#choosemethod").hasClass('in')){
               return;
           }
            $.ajax({
              url: 'index.php?route=checkout/shipping_methodetm/setrequestedtime',
              type: 'post',
              data: $('#order_requested_time'),
              dataType: 'json',
              success: function (json) {
                //  $('#confirm .checkout-content').html(html);
                $('.delalert').hide();
                $('.delselsuccess').show();
               // $("#choosemethod").modal("hide");
                $('#deltime').html(json['success']);
               // location.reload();
              },
              error: function (xhr, ajaxOptions, thrownError) {
                //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
            });
        });
    });
    function selectShippingMethodnew(code) {
        $('#mainshipping').hide();
        if (code == 'home.home') {
            $('#reqhead').hide();
            $('#postchead').show(); 
            $('#postcodeselectdiv').show();
            
        }else{
           
            $.post("index.php?route=checkout/shipping_postcodes/set_delivery_method", { delivery_method: code }, function (data) {
                var json = jQuery.parseJSON(data);
                delivery_method = json['delivery_method'];
              reloadcartnew('collect.collect');
            });
            // CHANGE REQ TIME
            $.post("index.php?route=checkout/shipping_methodetm/requestedtime", {shipping_method:code,mpop:true}, function (html) {
                     $('#reqhead').show();
                     $('#postchead').hide();
   			$('#cart_requested_times').html(html).show();;
                        $('#cart_requested_time_ok').show();
                        popupselect=1;
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
   function postcode_checknew(firsttime) {
       
       //$(".mess4").hide();
       $("#postcodemessage4").html('').hide();
       //$('#postcodealert4').hide();
       postcode = $("#inputdelivery").val();
       postcode = postcode_formater(postcode);
   
       $('#inputdelivery').val(postcode);
       
       var regPostcode = /[A-Z]{1,2}[0-9]{1,2}[A-Z]{0,1} ?[0-9][A-Z]{2}/i;//new reg expression for postcode validation.
       if (regPostcode.test(postcode) == false) {
   
           $("#postcodemessage4").html('<strong>Postcode is not valid</strong>').show();
           $('#inputdelivery').focus();
           return false;
       }
       else {
            var checkdelivery ='home.home';
            $.post("index.php?route=checkout/shipping_postcodes", {postcode: postcode, delivery_method: checkdelivery}, function(data) {
               var json = $.parseJSON(data);
               if (json['postcode'] == '') {
                   $("#postcodemessage4").html('<strong>Postcode is not valid</strong>').show();
                   $('#inputdelivery').focus();
                    return false; 
                   
               }
               else if (json['delivery_type'] == "collect") {
                    //$('#mes_p_postcode strong').html(postcode);
                    if(json['home_delivery_error']!=undefined){
                        //$('#mes_p_postcode').html(json['home_delivery_error']);  
                        $("#postcodemessage4").html('<strong>'+json['home_delivery_error']+'</strong>').show()
                    }
                   reloadcartnew('collect.collect');
                
            
               }
               else {
                   
                $('#mainshipping').hide();
                   $.post("index.php?route=checkout/shipping_postcodes/set_delivery_method", { delivery_method: 'home.home' }, function (data) {
                                   // console.log(data);
                        var json = jQuery.parseJSON(data);
                        delivery_method = json['delivery_method'];
                         reloadcartnew('home.home');
                                   //$('#before_bg :radio[value="home.home"]').prop('checked', true);
   
                    });
                    // CHANGE REQ TIME
                    $.post("index.php?route=checkout/shipping_methodetm/requestedtime", {shipping_method:'home.home',mpop:true}, function (html) {
                            $('#reqhead').show();
                            $('#postchead').hide();
                            $('#postcodeselectdiv').hide();
                            $('#cart_requested_times').html(html).show();
                            $('#cart_requested_time_ok').show();
                    });                    
               }
               
           });
       }
   
   
   }
   function closeshippingpopup() {
        $("#choosemethod").modal("hide")
    }
    function confirmreqtime(){
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.hash;
        //window.history.replaceState({}, document.title, newUrl);
        // location.reload(newUrl);
        window.location.href = newUrl;

    }
     function reloadcartnew(code) {      
     return
        //if($('#cart').length > 0) {
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
       // }
    }
</script>