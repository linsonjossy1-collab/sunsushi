<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content" class="col-sm-8"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb["separator"]; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
    <div class="content">
        <div class="form-group">
          <label class="col-lg-4 control-label"><?php echo $entry_subscription_newsletter ?></label>
          <div class="col-lg-6"><?php if ($newsletter == 1 || $newsletter == 3 || $newsletter == 5 || $newsletter == 7) { ?>
                <input type="radio" name="newsletter" value="1" checked="checked" />
                    <?php echo $text_yes ?>&nbsp;
                <input type="radio" name="newsletter" value="0" />
                    <?php echo  $text_no ?>
                <?php } else { ?>
                <input type="radio" name="newsletter" value="1" />
                <?php echo $text_yes ?>&nbsp;
                    <input type="radio" name="newsletter" value="0" checked="checked" />
                <?php echo  $text_no ?>
            <?php } ?></div>
        </div>
        <div class="form-group">
          <label class="col-lg-4 control-label"><?php echo $entry_subscription_mail ?></label>
          <div class="col-lg-6"><?php if ($newsletter == 2 || $newsletter == 3 || $newsletter == 6 || $newsletter == 7) { ?>
                <input type="radio" name="mail_subscription" value="1" checked="checked" />
                    <?php echo $text_yes ?>&nbsp;
                <input type="radio" name="mail_subscription" value="0" />
                    <?php echo $text_no ?>
                <?php } else { ?>
                <input type="radio" name="mail_subscription" value="1" />
                <?php echo $text_yes ?>&nbsp;
                    <input type="radio" name="mail_subscription" value="0" checked="checked" />
                <?php echo $text_no ?>
            <?php } ?></div>
        </div>
        <div class="form-group">
          <label class="col-lg-4 control-label"><?php echo $entry_subscription_sms ?></label>
          <div class="col-lg-6"><?php if ($newsletter == 4 || $newsletter == 5 || $newsletter == 6 || $newsletter == 7) { ?>
                <input type="radio" name="sms_subscription" value="1" checked="checked" />
                    <?php echo $text_yes ?>&nbsp;
                <input type="radio" name="sms_subscription" value="0" />
                    <?php echo $text_no ?>
                <?php } else { ?>
                <input type="radio" name="sms_subscription" value="1" />
                <?php echo $text_yes ?>&nbsp;
                    <input type="radio" name="sms_subscription" value="0" checked="checked" />
                <?php echo $text_no ?>
            <?php } ?></div>
        </div>
      
    </div>
    <div class="buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
      <div class="right"><input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary" /></div>
    </div>
  </form>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>