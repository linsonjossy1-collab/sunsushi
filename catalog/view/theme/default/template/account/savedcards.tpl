<?php echo $header; ?>

<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content" class="col-sm-8 logaccount"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  
  <div id="reach-myaccount">
    <div class='col-md-12'>
      <div><?=$card_detail_text?> : <?=$savedcard_details?> </div>
      <div>
          <a href='<?=$remove_card_save_href?>' style="color:#fff"><button class="btn button btn-danger"><?=$text_clear_cards?> </a>
          <a href='<?=$back?>' style="color:#fff"><button class="btn button btn-success"><?=$text_cancel?> </a>
      </div>
    </div>

  </div>

  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?> 
