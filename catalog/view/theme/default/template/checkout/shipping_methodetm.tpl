<?php $warning_status=false; ?>
<?php if ($error_warning || empty($shipping_methods)) { ?>
    <div class="col-sm-12">
        <div class="col-sm-9 alert alert-warning"><?php echo $error_warning; ?></div>
        <div id="change_add" class="col-sm-3"> <a onclick="change_del_req()"><i class="fa fa-chevron-left"></i>&nbsp;change</a></div>
    </div>
    <?php $warning_status=true; ?>
<?php } ?>
<?php if(!isset($dine_order)){ ?>
<?php if (!empty($shipping_methods)) { ?>
    <div class="col-sm-12">
        
        <?php if (isset($shipping_methods[$code])) { ?>
           
                <?php if($shipping_methods[$code]['msg']) { ?>
                 <?php if(!$warning_status){ ?>
                    <div id="home_delivery_msg" class="col-sm-9 alert alert-warning" role="alert"><?php echo $shipping_methods[$code]['msg']; ?> </div>
                    <?php } ?>
                <?php } elseif (!$shipping_methods[$code]['error'] && isset($shipping_methods[$code]['quote']) && !empty($shipping_methods[$code]['quote'])) { ?>  
                    <input type="hidden" name="shipping_method" class="shipping_method" value="<?php echo $code.'.'.$code; ?>" id="<?php echo $code; ?>"  />                    
                    <h4 class="col-sm-7" id="checkdelname">
                        <?php if($code=='home'){ ?>
                            <i class="fa fa-truck" aria-hidden="true"></i>
                        <?php }else{ ?>
                            <i class="fa fa-male" aria-hidden="true"></i>
                        <?php } ?>
                        
                        <span>&nbsp;<?php echo $shipping_methods[$code]['quote'][$code]['title']; ?></span>
                    </h4>
                    <div class="col-sm-2 topprize" id="chckprice">
                        <?php if(isset($shipping_methods[$code]['quote']) && !empty($shipping_methods[$code]['quote'])){ ?>
                            <label><?php echo $shipping_methods[$code]['quote'][$code]['text']; ?></label>
                        <?php } ?>                
                    </div>
                <?php } else { ?>
                    <?php if(!$warning_status && $shipping_methods[$code]['error']) { ?>
                        <div class="col-sm-9 alert alert-warning" role="alert"><?php echo $shipping_methods[$code]['error']; ?></div>
                    <?php } ?>
                <?php } ?>
            
            <?php if(!$warning_status){ ?>
                <div id="change_add" class="col-sm-3"> <a onclick="change_del_req()"><i class="fa fa-chevron-left"></i>&nbsp;change</a></div>
            <?php } ?>
        <?php }else{ ?>
            <div class="col-sm-12">
                <div class="col-sm-9 alert alert-warning">Please Select a Shippping methode</div>
                <div id="change_add" class="col-sm-3"> <a onclick="change_del_req()"><i class="fa fa-chevron-left"></i>&nbsp;Select</a></div>
            </div
        <?php } ?>
    </div>
<?php } ?>


<div class="col-sm-12" id="requested_time">
   <?php echo $text_requested_time; ?> : <?php echo $requested_time; ?>
</div>
<div class="col-sm-12">
<b><?php echo $text_comments; ?></b>
<textarea name="comment" rows="3" style="width: 95%;" id="check_commentbox"><?php echo $comment; ?></textarea>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="change_del_req_div">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"><?php echo $text_shipping_method; ?></h4>
            <div id="shippingmessage5" class="alert alert-warning" style="display: none" role="alert">
                <strong>Select a shipping method&nbsp;<i class="fa fa-arrow-down" aria-hidden="true"></i></strong>
            </div>
          </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer" >
            <button style="display: none" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button style="display: none" type="button" class="btn btn-primary" onclick="confirmtime();">Ok</button>
          </div> 
        </div>
    </div>
</div>
<?php }else{ ?>
<div class="col-sm-12">
    <h4 class="col-sm-12" id="checkdelname">
        <i class="fa fa-cutlery" aria-hidden="true"></i>
        <span>&nbsp;<?php //echo $shipping_methods[$code]['quote'][$code]['title']; ?></span>
        
        <span>&nbsp;<?php echo $this->session->data['tableordering']; ?></span>
        <input type="hidden" name="shipping_method" class="shipping_method" value="dine.dine" id="dine"  />                    
    </h4>
    
    <input type='hidden'  id="check_commentbox" value="<?php echo $comment; ?>">
</div>
    
<?php } ?>
<form action="<?php echo $checkout; ?>" method="post" id="sidebarcheckoutform">
</form>
<script type="text/javascript">
    function confirmtime(){
        if($("#before_bg input[name='shipping_method']:checked").val()==undefined || $("#before_bg input[name='shipping_method']:checked").val()==''){
            $('#shippingmessage5').show();
            $('html, body').animate({scrollTop:0});
            return;
        }
         $.ajax({
          url: 'index.php?route=checkout/shipping_methodetm/setrequestedtime',
          type: 'post',
          data: $('#order_requested_time'),
          dataType: 'json',
          success: function (json) {
            //  $('#confirm .checkout-content').html(html);
            $('#sidebarcheckoutform').submit();
          },
          error: function (xhr, ajaxOptions, thrownError) {
            //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
    
    }
</script>