<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="col-sm-12 plh"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
  <?php echo $description; ?>
  <!--<div class="buttons">
    <div class="right"><a href="/menu/#" class="button"><?php echo $button_continue; ?> to Menu</a></div>
  </div>-->
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>