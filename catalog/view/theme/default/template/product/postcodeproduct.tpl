<div class="demo" style="display: none">
    <div id="dialog-form" title="Enter Your Postal Code">
        <form>
            <input type="text" name="postcodepopup" id="postcodepopup" class="" value="" />
            <span id="message4" class="mess4" style="display: none" >Postcode is not valid.</span>
        </form>
    </div>
</div>

<div class="demo1" style="display: none">
    <div id="dialog-form1" title="We are closed now">
        <span id="message1">Sorry we are closed. </span>
        <span id="message2"></span>
        <span id="message3">Please check our opening hours.</span>
        <span id="message5" class="mess5" style="display: none"><br />Sorry we do not deliver to your post code.</span>
    </div>
</div>

<div class="demo2" style="display: none">
    <div id="dialog-form2" title="<?php echo $name; ?>">

    </div>
</div>

<div class="demo3" style="display: none">
    <div id="dialog-form3" title="<?php echo $name; ?>">
        <span id="message7">Sorry we do not deliver to your post code. </span>
        <span id="message8">You can place an order and Collect from Store.</span>
    </div>
</div>
<script type="text/javascript"><!--
var postcode = '<?php echo $postcode; ?>';
var nostore = '<?php echo $nostore; ?>';
 $(function() {
  $('input#postcodepopup').keypress(function(e) {
 if (e.keyCode == '13') {
    e.preventDefault();
    $("#dialog-form").parents('.ui-dialog').first().find('.ui-button').first().click();
   //  $(':button:contains("GO")').click();
  }
});
$('#button-cart').bind('click', function() {
   <?php if(isset($order_msg)) { ?>
    onlineorderpopup();
    return true;
    <?php } ?> 
    if (postcode != ''){
        addtocart_afterpostcodecheck();
    }
else{
    if (nostore == 0){
        $('.demo').show();
        $("#dialog-form").dialog({
            autoOpen: true,
            height: 300,
            width: 350,
            modal: true,
            resizable: false,
            buttons: {
            "GO": function() {
            
                $('.mess4').hide();
                postcode = $("#postcodepopup").val();
                postcode = postcode_formater(postcode);
                $('#postcodepopup').val(postcode);
                var url = '<?php echo $store_url; ?>'; 
                var regPostcode = /^([a-zA-Z]){1}([0-9][0-9]|[0-9]|[a-zA-Z][0-9][a-zA-Z]|[a-zA-Z][0-9][0-9]|[a-zA-Z][0-9]){1}([ ])([0-9][a-zA-z][a-zA-z]){1}$/;
                if (regPostcode.test(postcode) == false){
               
                     $('.mess4').show();
                     $('#postcodepopup').focus();
                     return false;
                 }
                 else {
                     jQuery.post(url + "index.php?route=checkout/shipping_postcodes", { postcode: postcode }, function(data) {
                     var json = jQuery.parseJSON(data);
                     if(json['postcode']=='') {
                         $('.mess4').show();
                          //alert("postcode is not valid");
                     }
                     else if (json['redirect'] ){
                          document.location.href = json['redirect'] + 'menus?postcode=' + json['postcode'] + '&d=' + json['distance'];
                     }
                     else if(json['nostore']=='1'){
                      
                         $("#dialog-form").dialog("close");
                      
                          $('.demo1').show();
                         if( json['delivery_type']== "collect") {
                            $('.mess5').show();
                         }
                        
                         $("#dialog-form1").dialog({
                                autoOpen: true,
                                height: 300,
                                width: 350,
                                modal: true,
                                resizable: false,
                                buttons: {
                                    "Continue": function() {
                                      $(this).dialog("close");
                                      $('.mess5').hide();
                                      addtocart_afterpostcodecheck();
                                    },
                                },
                              });
                      }
                      else if( json['delivery_type']== "collect") {
                            $("#dialog-form").dialog("close");
                            $('.demo3').show();
                            $("#dialog-form3").dialog({
                                autoOpen: true,
                                height: 300,
                                width: 350,
                                modal: true,
                                resizable: false,
                                buttons: {
                                    "Continue": function() {
                                      $(this).dialog("close");
                                      addtocart_afterpostcodecheck();
                                    },
                                },
                              });
                      }
                      else {
                        $("#dialog-form").dialog("close");
                        addtocart_afterpostcodecheck();
                      }
              });
              }
           },
         },
        });
       }
       else {
          addtocart_afterpostcodecheck();
       }
     }
});
 });
function postcode_formater(postcode) {
        postcode = postcode.replace(/ /g, "");
        var l = postcode.slice( - 3);
        var f = postcode.slice(0, (postcode.length - 3));
        postcode = f + ' ' + l;
        postcode = postcode.toUpperCase();
        return postcode;
}
function addtocart_afterpostcodecheck() {
 var url = location.protocol + '//' + location.host + '/';
    $.ajax({
        url: url+'index.php?route=checkout/cart/add',
        type: 'post',
        data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, information, .error').remove();
            if (json['error']) {
                if (json['error']['option']) {
                    for (i in json['error']['option']) {
                        $('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                    }
                }
            }

            if (json['success']) {
                $('#cart').load(url+'index.php?route=module/cart #cart > *');
               // $('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/chopstick/image/close.png" alt="" class="close" /></div>');
               // $('.success').fadeIn('slow');
                $('#cart-total').html(json['total']);
                
                $('html, body').animate({ scrollTop: 0 }, 'slow');
                
            }
        }
     });
}
//--></script>