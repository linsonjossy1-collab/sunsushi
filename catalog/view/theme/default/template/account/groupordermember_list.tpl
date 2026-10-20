<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
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
  <h1><?php echo $heading_title; ?></h1>
  <h2><?php echo $text_mebers_list; ?></h2>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
  
    <div class="content">
      <table class="list"> <thead>
          <tr>
              <td></td> <td><?php echo $entry_member_name; ?></td><td><?php echo $entry_member_email; ?></td><td><?php echo $entry_member_select; ?> </td>
          </tr> </thead>
          <tbody>
  <?php
  $i=0;
  foreach ($members as $result) { 
      $i++;
  ?>
  
      <tr>
        <td><?php echo $i; ?>.</td>
        <td><input type="text" name="member_name[]" value="<?php echo $result['member_name']; ?>" /></td>
        <td><input type="text" name="member_email[]" value="<?php echo $result['member_email']; ?>" /></td>
        <td><input type="checkbox" name="member_select[]" value="<?php echo $i-1; ?>" /></td>
      </tr>
   
  <?php } ?>
  </tbody>
  </table>
  </div>
  <div class="buttons" id="my-group_buttons">
    <div class="left">
        <input type="submit" name="grouporderdelete" value="<?php echo $button_delete; ?>" class="button" />
        <input type="submit" name="groupordermore" value="<?php echo $button_moremembers; ?>" class="button my-group_buttons_second-button" />
    </div>
    <div class="right">
         <input type="submit" name="grouporder" value="<?php echo $button_grouporder; ?>" class="button" />
    </div>
   
  </div>
  </form>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>