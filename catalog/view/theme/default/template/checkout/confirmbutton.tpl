<?php if ($text_agree) { ?>
<div class="buttons">
  <?php if(!isset($this->session->data['tableordering'])){ ?>  
  <div class="payment_message" style='background:yellow;'>Please watch out for order confirmation email from us. In case you don't receive an order confirmation email please call us to confirm the order. Please make sure you check your SPAM or junk folder in your inbox.</div>
  <br/>
  <?php } ?> 
  <?php  
  if(isset($payment_hold) && $payment_hold['status'] == 'false')
  {
    echo "<div style='color:red;'>";
    echo $payment_hold['msg'];
    echo "</div>";
  }
  else{ ?>
    <div class="right">
         <?php if ($agree) { ?>
        <input type="checkbox" name="agree" value="1" checked="checked" />
        <?php } else { ?>
        <input type="checkbox" name="agree" value="1" />
        <?php } ?>
        
        <?php echo $text_agree; ?>
    
      <div class="input-group">
      <div class="input-group-addon finalpricediv"></div>
      <?php if($this->config->get('config_notlive_banner')) { ?>
            <button data-toggle="modal" data-target="#notliveModal" class="btn btn-primary form-control" <?php echo ($shipping_method)?'':'disabled';?>><?php echo $button_continue; ?></button>
      <?php } else { ?>
      <input type="button" value="<?php echo $button_continue; ?>" id="button-order-confirm" class="btn btn-primary form-control" <?php echo ($shipping_method)?'':'disabled';?> />
      <?php } ?>
    </div>
    </div>
  </div>
  <?php } ?>
<?php } else { 
    if(isset($payment_hold) && $payment_hold['status'] == 'false')
    {
      echo $payment_hold['msg'];
    }
    else{
    ?>
  <div class="buttons">
    <div class="right">
      <div class="input-group">
        <div class="input-group-addon finalpricediv"></div>
        <?php if($this->config->get('config_notlive_banner')) { ?>
            <button data-toggle="modal" data-target="#notliveModal" class="btn btn-primary form-control" <?php echo ($shipping_method)?'':'disabled';?>><?php echo $button_continue; ?></button>
      <?php } else { ?>
        <input type="button" value="<?php echo $button_continue; ?>" id="button-order-confirm" class="btn btn-primary form-control" <?php echo ($shipping_method)?'':'disabled';?>/> 
      <?php } ?>
      </div>
    </div>
  </div>
<?php 
  }
} ?>

<?php if($this->config->get('config_notlive_banner')) { ?>
<div class="modal fade" data-backdrop="false" id="notliveModal" tabindex="-1" role="dialog" aria-labelledby="notliveModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="background:#f83821; text-align:center;">
      <div class="modal-header" style="background:#f83821;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="notliveModalLabel">Site Not Live</h4>
      </div>
      <div class="modal-body" style="color:#fff;">
        <?php if($this->config->get('config_notlive_banner_message')) { echo $this->config->get('config_notlive_banner_message'); } else { ?>
          PLEASE NOTE THIS IS NOT A REAL ORDER. NO FOOD WILL BE COOKED OR DELIVERED
        <?php } ?>                    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-secondary" id="button-order-confirm"  onclick="$('#notliveModal').modal('hide');">Continue</button>
      </div>
    </div>
  </div>
</div>
<?php } ?>
