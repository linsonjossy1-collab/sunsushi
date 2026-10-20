<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content" class="col-sm-8"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" >
    <h4 style="padding: 8px 0px;"><?php echo $text_your_details; ?></h4>
    <div class="content">
        <input type="checkbox" name="agree" value="1" />
        <?php echo $text_your_agree; ?>
        <?php if($error_agrement) { ?>
        <div class="warning"> <?php echo $error_agrement; ?></div>
        <?php } ?>
    </div>
    <div class="buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
      <div class="right">
        <input type="submit" value="<?php echo $button_delete; ?>" class="btn btn-primary" />
      </div>
    </div>
  </form>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>