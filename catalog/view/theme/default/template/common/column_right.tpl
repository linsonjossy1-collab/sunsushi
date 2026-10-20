<div id="column-right" class="col-sm-5 col-md-4 col-lg-3 hidden-xs">

<div id="side_cart_open">
<?php echo $cartshipping; ?>
<?php echo $cart; ?>



 </div>
    

<?php if ($modules) { ?>
  <?php foreach ($modules as $module) { ?>
  <?php echo $module; ?>
  <?php } ?>
<?php } ?>

<button class="btop" onclick="$('html, body').animate({scrollTop:0});"><i class="fa fa-sort-asc"></i></button>

</div>