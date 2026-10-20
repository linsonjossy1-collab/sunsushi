<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php echo $column_left; ?>
<div id="content" class="col-sm-8"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1><br />

  <h4><?php echo $text_address_book; ?></h4>
  <div class="row">
  <?php foreach ($addresses as $result) { ?>
    <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <p><?php echo $result['address']; ?></p>
        <p><a href="<?php echo $result['update']; ?>" class="btn btn-primary" role="button"><?php echo $button_edit; ?></a> &nbsp; <a href="<?php echo $result['delete']; ?>" class="btn btn-danger" role="button"><?php echo $button_delete; ?></a></p>
      </div>
    </div>
  </div>
  <?php } ?>
  </div>
  <div class="buttons">
    <div class="left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
    <div class="right"><a href="<?php echo $insert; ?>" class="btn btn-primary"><?php echo $button_new_address; ?></a></div>
  </div>
  <?php echo $content_bottom; ?>

</div>
  <?php echo $column_right; ?>
<?php echo $footer; ?>