<?php if(isset($account_coupon_messege)) { ?>
<div id="smpopupcontent">
  <h1><?php //echo $heading_title; ?></h1>
  
  <p></p>
  <div><?php //echo $account_messege; ?></div>
 
  <h2>Coupon</h2>
  <div class="content">
  <?php echo $account_coupon_messege; ?>
  <br/>
  <?php if(!$this->customer->isLogged()) { ?>
  <a class="button" href="/index.php?route=account/account">Login</a>
  <?php } else {  ?>
  <span id="message8">Coupon Details can be mailed to your email address</span>
  </br>
  <a class="button" href="/menu">GO</a>
  <?php } ?>
  </div>
  
</div>
 <?php } ?>