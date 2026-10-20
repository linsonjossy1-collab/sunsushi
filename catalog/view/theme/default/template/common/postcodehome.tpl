<div id="hdemoH1"  class="modal homepostcodemodal" role="dialog"  aria-labelledby="mypostcodepopup1">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="hdialog-formH1" title="We are closed now">
                    <span id="hmessage1">Sorry we are closed. </span>
                    <span id="hmessage2"></span>
                    <span id="hmessage3">Please check our opening hours.</span>
                    <span id="hmessage5" class="hmsg_5" style="display:none">Sorry we do not deliver to your post code.</span>
                </div>
            </div>   
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>    
</div>
<div id="hdemoH2"  class="modal homepostcodemodal" role="dialog"   aria-labelledby="mypostcodepopup2">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                 <?php echo $this->config->get('config_name'); ?>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="hdialog-formH2" title="<?php echo $name; ?>">
                    <span id="hmessage4">Postcode is not valid.</span>
                </div>
            </div>   
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>    
</div>
<div id="hdemoH3"  class="modal homepostcodemodal" role="dialog"  aria-labelledby="mypostcodepopup3">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                 <?php echo $this->config->get('config_name'); ?>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="hdialog-formH3" title="<?php echo $name; ?>">
                    <span id="hmessage65">Please Enter The Postcode.</span>
                </div>
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div> 
<div id="hdemoH4" class="modal homepostcodemodal" role="dialog" aria-labelledby="mypostcodepopup4">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="hdialog-formH4" title="<?php echo $name; ?>">
                    <span id="hmessage7">Sorry we do not deliver to your post code.</span>
                    <span id="hmessage8" style="display:none">You can place an order and Collect from Store.</span>
                </div>
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>    
</div>
<?php echo isset($order_msg)?$order_msg:''; ?>
<script type="text/javascript">
    //  var postcode = '<?php echo $postcode; ?>';
    $(function() {
        $('#button_search').click(function() {
            postcode_check();
        });
        $('input#postcodeheader').keypress(function(e) {
           if (e.keyCode == '13') {
             e.preventDefault();
             postcode_check();
             //  $(':button:contains("GO")').click();
            }
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
        
	  <?php if(isset($order_msg)) { ?>
       onlineorderpopup();
       return true;
       <?php } ?> 
        var postcode = $("#postcodeheader").val();
        var order = '<?php echo $grouporder ?>'
        postcode = postcode_formater(postcode);
        $('#postcodeheader').val(postcode);
        if (order == '0') {
            //var regPostcode = /^([a-zA-Z]){1}([0-9][0-9]|[0-9]|[a-zA-Z][0-9][a-zA-Z]|[a-zA-Z][0-9][0-9]|[a-zA-Z][0-9]){1}([ ])([0-9][a-zA-z][a-zA-z]){1}$/;
            var regPostcode = /[A-Z]{1,2}[0-9]{1,2}[A-Z]{0,1} ?[0-9][A-Z]{2}/i;//new reg expression for postcode validation.
                if (regPostcode.test(postcode) == false){
                        if (postcode.length == 1) {                           
                            $("#hdemoH3").modal({backdrop:0});
                            $('#postcodeheader').focus();
                            return false;
                        } 
                        else {
                            $("#hdemoH2").modal({backdrop:0});
                            $('#postcodeheader').focus();
                            return false;
                        }
                } else{
                    
                    $.post("index.php?route=checkout/shipping_postcodes", {postcode: postcode}, function(data) {  
                        var json = $.parseJSON(data);
                        if (json['postcode'] == '') {
                            $("#hdemoH2").modal({backdrop:false});
                            $('#postcodeheader').val('');
                            $('#postcodeheader').focus();
                        }else if (json['store_url'] != '') {
                            document.location.href = json['store_url'] + 'menus?postcode=' + json['postcode'] + '&d=' + json['distance'];
                        }else if (json['nostore'] == '1'){
                             document.location.href ='menu/#';
                        }else if (json['delivery_type'] == "collect") {
                            document.location.href ='menu/#';
                        }else {
                            document.location.href ='menu/#'; //menus
                        }
                    });
                }
            } else {
                alert('You Have Selected grouporder No need for Postcode search');
                            //document.location.href = url + 'menus';
            }
    }
</script>
