
<div class="demoH1" id="demo_popup" data-role="popup">
    <div id="dialog-formH1" title="We are closed now">
        <span id="message1">Please note: Your <?php echo $name; ?> is currently closed. </span>
        <span id="message2">You can place an order now for delivery at a later time.</span>
        <span id="message3">Please check opening hours before creating your order.</span>
        <span id="message5" class="messg5" style="display: none">Please note: Your <?php echo $name; ?> is currently no Home Delivery available in your area.</span>
        <div><input type="button"  value="continue" id="btn_continue" class="mobile_continue"></div>
    </div>
</div>
<div data-role="popup" id="demo_popup" class="demoH4" >
    <div id="dialog-formH4" title="<?php echo $name; ?>">
        <span id="message7">Please note: Your <?php echo $name; ?> is currently no Home Delivery available in your area. </span>
        <span id="message8">You can place an order and Collect from Store.</span>
        <div><input type="button"  value="continue" id="btn_continue" class="mobile_continue"></div>
    </div>
</div>
<div data-role="popup" class="demoH2">
    <div id="dialog-formH2" title="<?php echo $name; ?>">
        <span id="message4">Postcode is not valid.</span>
        <div id="demoH2_close"><input type="button" value="ok"></div>
    </div>
</div>
<div data-role="popup"  id="demoH3">
    <div id="dialog-formH3" title="<?php echo $name; ?>">
        <div id="message65">Please Enter The Postcode.</div>
        <div id="demoH3_close"><input type="button" value="ok"></div>
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function() {
        //$('#demoH3').hide();
        // var url = location.protocol + '//' + location.host + '/';
         $('input#postcodeheader').keypress(function(e) {
        if (e.keyCode == '13') {
            e.preventDefault();
            postcode_check();
            // $("#dialog-formBH").parents('.ui-dialog').first().find('.ui-button').first().click();
            //  $(':button:contains("GO")').click();
        }
        });
        $('#button_search').click(function() {
            postcode_check();
        });
        $('#demoH3_close').click(function() {
            $("#demoH3").popup('close');
        });
     $('#demoH2_close').click(function() {
            $(".demoH2").popup('close');
        });
      $('#btn_continue').click(function() {
          $("#demo_popup").popup('close');
            document.location.href = 'index.php?route=common/home';
        });
        $('.mobile_continue').click(function() {
          $("#demo_popup").popup('close');
            document.location.href = 'index.php?route=common/home';
        });
        
     <?php if(isset($_SESSION['postcode'])) {
            echo "$('#postcodeheader').val('" .$_SESSION['postcode'] ."');";
        }else{
           echo "$('#postcodeheader').val('Your Postcode');"; 
        }
        ?>

    });
    function postcode_formater(postcode) {
        postcode = postcode.replace(/ /g, "");

        var l = postcode.slice(-3);
        var f = postcode.slice(0, (postcode.length - 3));
        postcode = f + ' ' + l;
        postcode = postcode.toUpperCase();

        return postcode;
    }
    function postcode_check() {

        var postcode = $("#postcodeheader").val();
        var order = '<?php echo $grouporder ?>'
        //  var urls = document.URL;
        //  var urls = urls.split('index.php?route=common/home');
     // var url = location.protocol + '//' + location.host + '/';
      
        postcode = postcode_formater(postcode);
        $('#postcodeheader').val(postcode);
        if (order == '0') {

            var regPostcode = /^([a-zA-Z]){1}([0-9][0-9]|[0-9]|[a-zA-Z][0-9][a-zA-Z]|[a-zA-Z][0-9][0-9]|[a-zA-Z][0-9]){1}([ ])([0-9][a-zA-z][a-zA-z]){1}$/;

            if (regPostcode.test(postcode) == false)
                {

                                if (postcode.length == 1) {
                                    $("#demoH3").popup('open');
                                    $('#postcodeheader').focus();
                                    return false;
                                } else {
                                    $('.demoH2').popup('open');
                                    $('#postcodeheader').focus();
                                    return false;
                                }
                            }
                            else
                {
                                $.post('index.php?route=checkout/shipping_postcodes', {postcode: postcode},
                                        function(data) {

                                            var json = $.parseJSON(data);
                                            if (json['postcode'] == '') {
                                            $('.demoH2').popup('open');
                                            $('#postcodeheader').focus();
                                            }
                                            else if (json['store_url'] != '') {
                                                document.location.href = json['store_url'] + 'menus?postcode=' + json['postcode'] + '&d=' + json['distance'];
                                            }
                                            else if (json['nostore'] == '1') {
                                                // alert('No Store is Opened In This Time');
                                                $('.demo1').show();
                                                if (json['delivery_type'] == "Collect From Store") {
                                                    $('.messg5').show();
                                                }
                                            $('.demoH1').popup('open');
                                              
                                            }
                                            else if (json['delivery_type'] == "Collect From Store") {

                                                //$('.demoH4').show();
                                                $('.demoH4').popup('open');
                                                /*$("#dialog-formH4").dialog({
                                                    autoOpen: true,
                                                    height: 300,
                                                    width: 350,
                                                    modal: true,
                                                    buttons: {
                                                        "Continue": function() {
                                                            $(this).dialog("close");
                                                            document.location.href = url + 'menus';
                                                        },
                                                    },
                                                });*/
                                            }
                                            else {
                                                document.location.href = 'index.php?route=common/home' ;
                                            }
                                        });
                            }
                        } else {
                            alert('You Have Selected grouporder No need for Postcode search');
                            document.location.href = 'index.php?route=common/home';
                        }
                    }
</script>

