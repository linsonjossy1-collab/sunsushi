<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($payment_methods) { ?>
<p><b><?php echo $text_payment_method; ?></b></p>
<table class="radio">
  <?php foreach ($payment_methods as $payment_method) { ?>
  <?php if(isset($payment_method['msg'])) { ?>
<span id="home_delivery_msg"><?php echo $payment_method['msg']; ?></br></span>
<?php } ?>
  <tr class="highlight">
      <?php if ($payment_method['code'] != '') { ?>
    <td>
        <?php if ($payment_method['code'] == $code || !$code) { ?>
      <?php $code = $payment_method['code']; ?>
      <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
      <?php } else { ?>
      <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
      <?php } ?>
    </td>
    <td><label for="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['title']; ?></label>
    <?php if(isset($payment_method['commision']) && $payment_method['commision'] ) { ?>
            <div class="paymentcommision"><?php echo $payment_method['commision']; ?></div>
        <?php } ?>
    </td>
     <?php } ?>
  </tr>
  <?php } ?>
</table>
<br />
<?php } ?>
<script type="text/javascript"><!--
/*    
$('.colorbox').colorbox({
	width: 640,
	height: 480
});
*/
//--></script> 