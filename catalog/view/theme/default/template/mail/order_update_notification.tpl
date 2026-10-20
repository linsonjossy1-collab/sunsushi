<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $title; ?></title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
<a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>">
<?php if($header_image) { ?>
<img class="img-responsive" alt="<?php echo $store_name; ?>" title="<?php echo $store_name; ?>" src="<?php echo $header_image; ?>">
<?php } else { echo $store_name; } ?>
</a>
<div style="width: 698px; padding: 5px; border: 1px solid #dddddd; border-bottom: 20px solid #dddddd; margin-top: -5px;">

<table width="100%" border="0" cellspacing="2" cellpadding="0">
  <tr>
     <td width="69%">

      <?php echo $message; ?>
  
    </td>
    <td width="1%">&nbsp;</td>
    <td width="30%" valign="top">
      <?php if($offer_image) { ?>
      <a href="<?php echo $offer_url; ?>" title="<?php echo $store_name; ?>"><img width="200" class="img-responsive" alt="<?php echo $store_name; ?>" title="<?php echo $store_name; ?>" src="<?php echo $offer_image; ?>"></a>
      <?php } ?>
    </td>
  </tr>
</table>

  <?php if($footer_image) { ?>  
  <img class="img-responsive" alt="<?php echo $store_name; ?>" title="<?php echo $store_name; ?>" src="<?php echo $footer_image; ?>">  
  <?php } ?>
</div>
</body>
</html>
