<?php echo $header; ?>
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
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" >
    <h4 style="padding: 8px 0px;"><?php echo $text_your_details; ?></h4>
    <div class="content">
        <div class="form-group">
          <label class="col-lg-4 control-label"> <?php echo $entry_firstname; ?><span class="required">*</span></label>
          <div class="col-lg-6"><input type="text" name="firstname" value="<?php echo $firstname; ?>" />
            <?php if ($error_firstname) { ?>
            <span class="error"><?php echo $error_firstname; ?></span>
            <?php } ?></div>
        </div>
        <div class="form-group">
          <label class="col-lg-4 control-label"> <?php echo $entry_lastname; ?><span class="required">*</span></label>
          <div class="col-lg-6"><input type="text" name="lastname" value="<?php echo $lastname; ?>" />
            <?php if ($error_lastname) { ?>
            <span class="error"><?php echo $error_lastname; ?></span>
            <?php } ?></div>
        </div>
        <div class="form-group">
          <label class="col-lg-4 control-label"> <?php echo $entry_email; ?><span class="required">*</span></label>
          <div class="col-lg-6"><input type="text" name="email" value="<?php echo $email; ?>" />
            <?php if ($error_email) { ?>
            <span class="error"><?php echo $error_email; ?></span>
            <?php } ?></div>
        </div>
        <div class="form-group">
          <label class="col-lg-4 control-label"> <?php echo $entry_telephone; ?><span class="required">*</span></label>
          <div class="col-lg-6"><input type="text" name="telephone" value="<?php echo $telephone; ?>" />
            <?php if ($error_telephone) { ?>
            <span class="error"><?php echo $error_telephone; ?></span>
            <?php } ?></div>
        </div>
        <div class="form-group">
          <label class="col-lg-4 control-label"><?php echo $entry_fax; ?></label>
          <div class="col-lg-6"><input type="text" name="fax" value="<?php echo $fax; ?>" /></div>
        </div>
      
    </div>
    <div class="buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
      <div class="right">
        <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary" />
      </div>
    </div>
  </form>
  <?php echo $content_bottom; ?></div><?php echo $column_right; ?>
<?php echo $footer; ?>