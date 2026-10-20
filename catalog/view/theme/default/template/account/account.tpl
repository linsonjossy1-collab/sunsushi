<?php echo $header; ?>

<?php echo (isset($order_msg))?$order_msg:''; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<?php echo $column_left; ?>
<div id="content" class="col-sm-8 logaccount"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <!--  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>

<h2>Most Recent Orders</h2>
  <div class="content">
       <?php if ($orders) { ?>
  <?php foreach ($orders as $orderitem) { ?>
  <div class="order-list">
    <div class="order-id"><b><?php echo $text_order_id; ?></b> #<?php echo $orderitem['order_id']; ?></div>
    <div class="order-status">
        <?php if(!isset($order_msg)) { ?>
        <b><?php // echo $text_status; ?></b> <?php // echo $orderitem['status']; ?> <a href="<?php echo $orderitem['href']; ?>"><img src="catalog/view/theme/default/image/info.png" alt="<?php echo $button_view; ?>" title="<?php echo $button_view; ?>" /></a>&nbsp;&nbsp;<a href="<?php echo $orderitem['reorder']; ?>"><img src="catalog/view/theme/default/image/reorder.png" alt="<?php echo $button_reorder; ?>" title="<?php echo $button_reorder; ?>" /></a>
       <?php } ?>
    </div>
    <div class="order-content">
      <div><b><?php echo $text_date_added; ?></b> <?php echo $orderitem['date_added']; ?><br />
        <b><?php echo $text_products; ?></b> <?php echo $orderitem['products']; ?></div>
      <div><b><?php echo $text_total; ?></b> <?php echo $orderitem['total']; ?><br />
        <b>Products:</b> <?php echo $orderitem['productsname']; ?></div>
         
    </div>
  </div>
  <?php } ?>
  <?php } ?>
   </div>
-->

<div id="reach-myaccount">
<a href="/index.php?route=account/reward"><div class="col-sm-4" id="first-reward-point">
<i class="fa fa-trophy" aria-hidden="true"></i>
<h2>Reward Points</h2>
<div class="content">
<b>Your total number of reward points is:</b> <?php echo $reward_points; ?>
</div>
</div></a>
<div class="col-sm-4" id="my-account-settings">
<i class="fa fa-user" aria-hidden="true"></i>
  <h2><?php echo $text_my_account; ?></h2>
  <div class="content">
    <ul>
      <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
      <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
      <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
      <li><a href="<?php echo $newsletter; ?>"><?php echo $text_subscribe; ?></a></li>
      <?php if(isset($savedcards) && isset($text_saved_cards)){ ?>
      <li><a href="<?php echo $savedcards; ?>"><?php echo $text_saved_cards; ?></a></li>
      <?php } ?>
      <li><a href="<?php echo $delete; ?>"><?php echo $text_delete; ?></a></li>
    </ul>
  </div>
</div>

<div class="col-sm-4">
<i class="fa fa-shopping-cart" aria-hidden="true"></i>
  <h2><?php echo $text_my_orders; ?></h2>
  <div class="content" id="third-my-order">
    <ul>
      <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
      <!--<li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>-->
      <?php if ($reward) { ?>
      <li><a href="<?php echo $reward; ?>"><?php echo $text_reward; ?></a></li>
      <?php } ?>
      <!--<li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
      <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>-->
    </ul>
  </div>
</div>
</div>
<script>
$(document).ready(function(){
<?php if(isset($order_msg)) { ?>
    
    onlineorderpopup();
   // return true;
    <?php } ?> 
<?php if(isset($customer_data)){ ?>
    if(typeof(Storage)!=="undefined")  {
         localStorage.setItem('a1_s1_gkl_' , '<?php echo $customer_data ; ?>');
    }
<?php } ?>     

});
</script>

  <!--<h2><?php echo $text_subscribe; ?></h2>
  <div class="content">
    <ul>
      <li><a href="<?php echo $newsletter; ?>"><?php echo $text_subscribe; ?></a></li>
    </ul>
  </div>-->
  <?php echo $content_bottom; ?></div><?php echo $column_right; ?>
<?php echo $footer; ?> 
