<?php if(isset($epaymax_customerToken)) { ?>
    <?php if($epaymax_seamless) { ?>
        <div style="text-align: center;min-height: 50px;">
        &nbsp;
        </div>
        <form action="<?php echo $seamless_action; ?>" method="post" id="checkout">
       <input type="hidden" name="payxpert_validate" value="true" />
       <input type="hidden" name="epaymax_customerToken" value="<?php echo $epaymax_customerToken; ?>" />
       <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
       <div class="buttons">
         <div class="right">
           <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
         </div>
       </div>
</form>
    <?php } else { ?>
        <div style="text-align: center;min-height: 150px;">
        <h3>Loading....</h3>
      </div>
      <script>
          location = '<?php echo $action; ?>';
      </script>
    <?php } ?>
<?php } else { ?>
<div style="text-align: center;">
  <h1><?php echo $heading_title; ?></h1>
  <p><?php echo $text_response; ?></p>
  <div style="border: 1px solid #DDDDDD; margin-bottom: 20px; width: 350px; margin-left: auto; margin-right: auto;">
    
  </div>
  <p><b><?php echo $text_failure; ?></b></p>
  <p><?php echo $text_failure_wait; ?></p>
</div>
<script type="text/javascript"><!--
setTimeout('location = \'<?php echo $continue; ?>\';', <?php echo $timeout; ?>);
//--></script>
<?php } ?>