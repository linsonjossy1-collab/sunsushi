<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="col-sm-8"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
  <?php if ($orders) { ?>
  <?php foreach ($orders as $order) { ?>
  <div class="order-list">
    <div class="order-id"><b><?php echo $text_order_id; ?></b> #<?php echo $order['order_id']; ?></div>
    <div class="order-status"><b><?php echo $text_status; ?></b> <?php echo $order['status']; ?></div>
    <div class="order-content">
      <div><b><?php echo $text_date_added; ?></b> <?php echo $order['date_added']; ?><br />
       <b><?php echo $text_total; ?></b> <?php echo $order['total']; ?>
      </div>
      <div><b><?php echo $text_customer; ?></b> <?php echo $order['name']; ?><br />
        <b><?php echo $text_products; ?></b> <?php echo $order['products']; ?>
      <br />
      <ul class="order-prodts">
      <?php if(isset($order['productsname'])) { ?>
      <?php foreach($order['productsname'] as $ordrprodt){ ?>
        <li> <?php echo $ordrprodt; ?></li>
      <?php } ?>
      <?php } ?>
      </ul>
     
      </div>  
      <div class="order-info">
          <a href="<?php echo $order['href']; ?>" class="btn btn-default"><?php echo $button_view; ?></a>&nbsp;&nbsp;
           <?php if(!isset($order_msg)) { ?>
          <a href="<?php echo $order['reorder']; ?>" class="btn btn-primary"><?php echo $button_reorder; ?></a>
         <?php } ?>
         </div>
    </div>
  </div>
  <?php } ?>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <?php } ?>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
  </div>
  <?php echo $content_bottom; ?></div><?php echo $column_right; ?>
<?php echo $footer; ?>