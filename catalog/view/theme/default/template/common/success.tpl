<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="col-sm-8"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
  <?php echo $text_message; ?>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
    <?php if (isset($live_delivery_time) && $live_delivery_time && !empty($order_id)) { ?>
    <div id="deliverypopup" title="Request is being processed">
        <h1>Your Order #<?php echo $order_id; ?></h1>
        <p id="deliveryacknowle" style="color: #000000 !important;"> Order is under Acknowledgement </p>
        <div id="deliverymessage" style="color: #000000 !important;">
            Please wait
        </div>
        <div id="deliverycomment" style="color: #000000 !important;"></div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {

            function loadDeliveryTime() {
                $.ajax({
                    url: 'index.php?route=checkout/success/deliverytime',
                    type: 'post',
                    data: 'orderid=<?php echo $order_id; ?>',
                    dataType: 'json',
                    success: function(json) {
                        //  $('#confirm .checkout-content').html(html);
                        if (json['order_status']) {
                            if (json['delivery_time']) {
                                $('#deliverymessage').text('Your order will be delivered at : ' + json['delivery_time']);
                            }
                            else if (json['requested_time']) {
                                $('#deliverymessage').text('Your order requested time : ' + json['requested_time'] );
                            }
                            else {
                                $('#deliverymessage').text('Your order ' + json['order_status'] );
                            }
                            $('#deliverycomment').text( json['comment']);
                            $('#deliveryacknowle').text('');
                            $('.wait').remove();
                        }
                        else {
                            setTimeout(loadDeliveryTime, 5000);

                        }

                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
            $('#deliverypopup').dialog({
                modal: true,
                buttons: {
                    Ok: function() {
                        $(this).dialog("close");
                    }
                }
            });
            $('#deliverymessage').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
            loadDeliveryTime();

        });
    </script>
    <?php } ?>
 <script type="text/javascript">
        <?php if(isset($logouted)){ ?>   
            if(typeof(Storage)!=="undefined")  {
                localStorage.removeItem('a1_s1_gkl_');
            }
        <?php } ?>   
        
    
    <?php if(isset($order_analyze)){ ?>   
            var cartitems =[];
            <?php  $k=0; foreach($cart_items as $Item) { ?>
                cartitems[cartitems.length]={
                    item_id:"<?php echo $Item['product_id']; ?>",
                    item_name:"<?php echo $Item['name']; ?>", 
                    item_category:"<?php echo $Item['item_category']; ?>", 
                    index:<?php echo $k; ?>,
                    price:<?php echo $Item['total_value']; ?>,
                    quantity: <?php echo $Item['quantity']; ?>
                };
            <?php $k++ ; }  ?> 
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: "purchase",
                ecommerce: {
                  transaction_id: '<?php echo $order_id; ?>',
                  currency: "GBP",
                  value: <?php echo $order_total; ?>,
                  items: cartitems
                }
            });

            /*
             gtag("event", "purchase", {
                    transaction_id:'<?php echo $order_id; ?>',
                    currency: "GBP",
                    value: <?php echo $order_total; ?>,
                    items: cartitems
            });
            */
    <?php } ?>          
    </script>     
  <?php echo $content_bottom; ?></div><?php  echo $column_right; ?>
<?php echo $footer; ?>