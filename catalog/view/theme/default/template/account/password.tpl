<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="col-sm-8"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1><br />

  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
    <!--<h3><?php echo $text_password; ?></h3>-->
    <div class="content">
        <div class="form-group">
          <label class="col-lg-4 control-label"> <?php echo $entry_password; ?><span class="required">*</span></label>
          <div class="col-lg-6"><input type="password" name="password" value="<?php echo $password; ?>" />
            <?php if ($error_password) { ?>
            <span class="error"><?php echo $error_password; ?></span>
            <?php } ?></div>
        </div>
        <div class="form-group">
          <label class="col-lg-4 control-label"> <?php echo $entry_confirm; ?><span class="required">*</span></label>
          <div class="col-lg-6"><input type="password" name="confirm" value="<?php echo $confirm; ?>" />
            <?php if ($error_confirm) { ?>
            <span class="error"><?php echo $error_confirm; ?></span>
            <?php } ?></div>
        </div>
      
    </div>
    <div class="buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
      <div class="right"><input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary" /></div>
    </div>
  </form>
  <?php echo $content_bottom; ?></div><?php echo $column_right; ?>
<?php echo $footer; ?>