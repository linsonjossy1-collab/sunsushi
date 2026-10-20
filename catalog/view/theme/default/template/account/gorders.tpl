<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <h2><?php echo $text_mebers_list; ?></h2>
    <div class="content">
      <table class="list">
            <thead>
          <tr>
              <td></td> <td><?php echo $entry_member_name; ?></td><td><?php echo $entry_member_email; ?></td><td><?php echo $entry_member_status; ?> </td>
          </tr>
            </thead>
            <tbody>
  <?php
  $i=0;
  foreach ($members as $result) { 
      $i++;
  ?>
  
      <tr>
        <td><?php echo $i; ?>.</td>
        <td><?php echo $result['member_name']; ?></td>
        <td><?php echo $result['member_email']; ?></td>
        <td><?php echo $result['member_nowstage']; ?></td>
      </tr>
   
  <?php } ?>
</tbody>

  </table>
  </div>
  <div class="buttons">
    <div class="left">
        <a href="<?php echo $delete; ?>" class="button"><?php echo $button_delete_grouporder; ?></a>
        <a href="<?php echo $refresh; ?>" class="button"><?php echo $button_refresh; ?></a>
    </div>
    <div class="right">
        <a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue_grouporder; ?></a>
    </div>
   
  </div>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>