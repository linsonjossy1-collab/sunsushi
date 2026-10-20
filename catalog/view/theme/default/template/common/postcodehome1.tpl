<div class="demoH1" style="display: none">
    <div id="dialog-formH1" title="We are closed now">
        <span id="message1">Please note: Your <?php echo $name; ?> is currently closed. </span>
        <span id="message2">You can place an order now for delivery at a later time.</span>
        <span id="message3">Please check opening hours before creating your order.</span>
        <span id="message5" style="display: none"><br />Please note: Your <?php echo $name; ?> is currently no Home Delivery available in your area.</span>
    </div>
</div>

<div class="demoH2" style="display: none">
    <div id="dialog-formH2" title="<?php echo $name; ?>">
        <span id="message4">Postcode is not valid.</span>
    </div>
</div>
<div class="demoH3" style="display: none">
    <div id="dialog-formH3" title="<?php echo $name; ?>">
        <span id="message65">Please Enter The Postcode.</span>
    </div>

</div> 
<div class="demoH4" style="display: none">
    <div id="dialog-formH4" title="<?php echo $name; ?>">
        <span id="message7">Please note: Your <?php echo $name; ?> is currently no Home Delivery available in your area. </span>
        <span id="message8">You can place an order and Collect from Store.</span>
    </div>
</div>
<script>
    //  var postcode = '<?php echo $postcode; ?>';
    $(function() {
    $('#button_search').click(function() {
    postcode_check();
    });
            $('#order').click(function() {
    postcode_check();
    });
            <?php if (isset($_SESSION['postcode'])) {
    echo "$('#postcodeheader').val('".$_SESSION['postcode']."');";
    }
    ?>
    });
            function postcode_formater(postcode) {
            postcode = postcode.replace(/ /g, "");
                    var l = postcode.slice( - 3);
                    var f = postcode.slice(0, (postcode.length - 3));
                    postcode = f + ' ' + l;
                    postcode = postcode.toUpperCase();
                    return postcode;
            }
    function postcode_check(){

    var postcode = $("#postcodeheader").val();
            var order = '<?php echo $grouporder ?>'
            //  var urls = document.URL;
            //  var urls = urls.split('index.php?route=common/home');
            var url = location.protocol + '//' + location.host + '/'; ;
            postcode = postcode_formater(postcode);
            $('#postcodeheader').val(postcode);
            if (order == '0') {

    //var regPostcode = /^([a-zA-Z]){1}([0-9][0-9]|[0-9]|[a-zA-Z][0-9][a-zA-Z]|[a-zA-Z][0-9][0-9]|[a-zA-Z][0-9]){1}([ ])([0-9][a-zA-z][a-zA-z]){1}$/;
    var regPostcode = /[A-Z]{1,2}[0-9]{1,2}[A-Z]{0,1} ?[0-9][A-Z]{2}/i; //new reg expression for postcode validation.
            if (regPostcode.test(postcode) == false)
                {

                    if (postcode.length == 1) {
                    $('.demoH3').show();
                            $("#dialog-formH3").dialog({
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
                            //  alert("Postcode is not valid.");
                            $('#postcodeheader').focus();
                            return false;
                    } else {
                    $('.demoH2').show();
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
                            //  alert("Postcode is not valid.");
                            $('#postcodeheader').focus();
                            return false;
                    }

                    }
                    else
                {
                    $.ajax({
                    url:"index.php?route=checkout/shipping_postcodes",
                            type: 'post',
                            data:{postcode: postcode},
                            success: function(data) {

                    var json = $.parseJSON(data);
                            //alert(json);
                    if (json['postcode'] == '') {
                      $('.demoH4').show();
                    }
                    else if (json['store_url'] != '') {
                    document.location.href = json['store_url'] + 'menu?postcode=' + json['postcode'] + '&d=' + json['distance'];
//alert(data);                        
                    }
                    else if (json['nostore'] == '1'){
                    //alert("nostore");                        
                    // alert('No Store is Opened In This Time');
                    $('.demo1').show();
                            if (json['delivery_type'] == "collect") {
                    $('#message5').show();
                    }
                    $("#dialog-formH1").dialog({
                    autoOpen: true,
                            height: 300,
                            width: 350,
                            modal: true,
                            buttons: {
                    "Continue": function() {
                    $(this).dialog("close");
                            document.location.href = url + 'menu/#';
                    },
                    },
                    });
                    }
                    else if (json['delivery_type'] == "collect") {

                    $('.demoH4').show();
                            $("#dialog-formH4").dialog({
                    autoOpen: true,
                            height: 300,
                            width: 350,
                            modal: true,
                            buttons: {
                    "Continue": function() {
                    $(this).dialog("close");
                            document.location.href = url + 'menu/#';
                    },
                    },
                    });
                    }
                    else {
                    document.location.href = url + 'menu/#'; //menus
                    }
                    }});
                    }
                    } else {
                    alert('You Have Selected grouporder No need for Postcode search');
                            //document.location.href = url + 'menus';
                    }
                    }
</script>
