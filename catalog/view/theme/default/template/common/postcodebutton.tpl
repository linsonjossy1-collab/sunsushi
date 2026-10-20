<div class="demoH" style="display: none">
    <div id="dialog-formH" title="Enter Your Postal Code">
        <form>
            <input type="text" name="postcodepopuph" id="postcodepopuph" class="" value="" />
            <br /><span id="message4" style="display: none" >Postcode is not valid.</span>
             <div class="need1" style="text-align: center; font-size: 11px; font-style: italic;"><a href="http://www.royalmail.com/postcode-finder/" target="_blank">Don't know your postcode?</a></div>
        </form>
    </div>
</div>

<div class="demoH1" style="display: none">
    <div id="dialog-formH1" title="We are closed now">
        <span id="message1">Sorry we are closed. </span>
        <span id="message2"></span>
        <span id="message3">Please check our opening hours.</span>
        <span id="message5" style="display: none">Sorry we do not deliver to your post code.</span>
    </div>
</div>

<div class="demoH2" style="display: none">
    <div id="dialog-formH2" title="<?php echo $name; ?>">

    </div>
</div>

<div class="demoH3" style="display: none">
    <div id="dialog-formH3" title="<?php echo $name; ?>">
        <span id="message7">Sorry we do not deliver to your post code.</span>
        <span id="message8">You can place an order and Collect from Store.</span>
    </div>
</div>
<script type="text/javascript"><!--
 var postcode = '<?php echo $postcode; ?>';
 /*var nostore = '<?php echo $nostore; ?>'; */
     $(function() {
         $('#enter-post-code').click(function() {
            //$("#ordernow_homepage").removeAttr('href');
           // var urls = document.URL;
          //  var urls = urls.split('/');
          //  alert (urls);
            var url = location.protocol + '//' + location.host + '/';

             var order = '<?php echo $grouporder ?>'
             if (order == '1' || postcode!='') {
                 document.location.href = url + 'menu';
             }
             else {

                 $('.demoH').show();
                 $("#dialog-formH").dialog({
                     autoOpen: true,
                     height: 300,
                     width: 350,
                     modal: true,
                     buttons: {
                         "GO": function() {

                             $('#message4').hide();
                             postcode = $("#postcodepopuph").val();
                             postcode = postcode_formater(postcode);
                             $('#postcodepopuph').val(postcode);
                            // var urls = document.URL;
                            // var urls = urls.split('/');
                            // var url = urls[0];
                              var regPostcode = /[A-Z]{1,2}[0-9]{1,2}[A-Z]{0,1} ?[0-9][A-Z]{2}/i;//new reg expression for postcode validation.
                             if (regPostcode.test(postcode) == false) {
                                 $('#message4').show();
                                 $('#postcodepopuph').focus();
                                 return false;
                             }
                             else {
                                
                                 $.post("index.php?route=checkout/shipping_postcodes", {postcode: postcode}, function(data) {
               
                    var json = $.parseJSON(data);
                            //alert(json);
                            if (json['postcode'] == '') {
                           // alert("test");
                      //$('.demoH2').show();
                      $('#dialog-formH').dialog('close');
                      $("#dialog-formH2").dialog({
                    autoOpen: true,
                            height: 300,
                            width: 350,
                            modal: true,
                            buttons: {
                    "ok": function() {
                    $(this).dialog("close");
                    },
                    },
                    });
                    $('#postcodepopuph').val('');
                    $('#postcodepopuph').focus();
                    }
                    else if (json['store_url'] != '') {
                    document.location.href = json['store_url'] + 'menus?postcode=' + json['postcode'] + '&d=' + json['distance'];
//alert(data);                        
                            }
                    else if (json['nostore'] == '1'){
                    //alert("nostore");                        
                    // alert('No Store is Opened In This Time');
                    $('.demoH1').show();
                            if (json['delivery_type'] == "collect") {
                    $('#message4').show();
                    }
                    $('#dialog-formH').dialog('close');
                    $("#dialog-formH1").dialog({
                    autoOpen: true,
                            height: 300,
                            width: 350,
                            modal: true,
                            buttons: {
                    "Continue": function() {
                    $(this).dialog("close");
                    $('#message5').hide();
                            document.location.href ='menu/#';
                    },
                    },
                    });
                    }
                    else if (json['delivery_type'] == "collect") {
                        $('#message7').show();
                    $('.demoH4').show();
                    $('#dialog-formH').dialog('close');
                            $("#dialog-formH4").dialog({
                    autoOpen: true,
                            height: 300,
                            width: 350,
                            modal: true,
                            buttons: {
                    "Continue": function() {
                    $(this).dialog("close");
                            document.location.href ='menu/#';
                    },
                    },
                    });
                    }
                    else {
                    document.location.href ='menu/#'; //menus
                    }
                    });
                             }
                         },
                     },
                 });

             }
         });
     });
     function postcode_formater(postcode) {
         postcode = postcode.replace(/ /g, "");
         var l = postcode.slice(-3);
         var f = postcode.slice(0, (postcode.length - 3));
         postcode = f + ' ' + l;
         postcode = postcode.toUpperCase();
         return postcode;
     }

//--></script>