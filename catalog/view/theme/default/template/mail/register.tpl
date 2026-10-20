<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $title; ?></title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
<a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>"><img class="img-responsive" alt="<?php echo $store_name; ?>" title="<?php echo $store_name; ?>" src="<?php echo $email_register_header; ?>"></a>
<div style="width: 698px; padding: 5px; border: 1px solid #dddddd; border-bottom: 20px solid #dddddd; margin-top: -5px;">
 
<?php echo $text_dear; ?><br /><br />
<?php echo $text_approve_welcome; ?><br /><br />
<?php echo $text_accdetails; ?><br /><br />
<?php echo $text_sincerely; ?><br />
  
<p style="margin-top: 0px; margin-bottom: 20px;"> <?php echo $store_name; ?></p>
<p style="margin-top: 0px; margin-bottom: 20px;"><a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>"><?php echo $store_url; ?></a></p>
<p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_orderfooter; ?><?php echo $text_orderfooter1; ?></p>
  
<p style="margin-top: 0px; margin-bottom: 20px; text-align: center;"> <strong><?php echo $store_name; ?>, <?php echo $store_address; ?> <a href="tel:<?php echo $telephone; ?>" title="<?php echo $store_name; ?>"><?php echo $telephone; ?></a></strong></p>

<p style="margin-top: 0px; margin-bottom: 20px; text-align: center;">   
<?php if(isset($fb_url)){ ?> <a href="<?php echo $fb_url; ?>"><img width="40" height="40" alt="Facebook" title="<?php echo $store_name; ?> Facebook" src="<?php echo $store_url; ?>image/mail/email-facebook.png"></a> <?php } ?>
<?php if(isset($twitter_url)){ ?> <a href="<?php echo $twitter_url; ?>"><img width="40" height="40" alt="Twitter" title="<?php echo $store_name; ?> Twitter" src="<?php echo $store_url; ?>image/mail/email-twitter.png"></a> <?php } ?>
<?php if(isset($gplus_url)){ ?> <a href="<?php echo $gplus_url; ?>"><img width="40" height="40" alt="Google Plus" title="<?php echo $store_name; ?> Google Plus" src="<?php echo $store_url; ?>image/mail/email-gplus.png"></a> <?php } ?> 
<?php if(isset($linkedin_url)){ ?> <a href="<?php echo $linkedin_url; ?>"><img width="40" height="40" alt="Linkedin" title="<?php echo $store_name; ?> Linkedin" src="<?php echo $store_url; ?>image/mail/email-linkedin.png"></a> <?php } ?>
<?php if(isset($instagram_url)){ ?> <a href="<?php echo $instagram_url; ?>"><img width="40" height="40" alt="Instagram" title="<?php echo $store_name; ?> Instagram" src="<?php echo $store_url; ?>image/mail/email-instagram.png"></a> <?php } ?>
<?php if(isset($iphoneapp_url)){ ?> <a href="<?php echo $iphoneapp_url; ?>"><img width="130" height="40" alt="iPhone app" title="<?php echo $store_name; ?> iPhone app" src="<?php echo $store_url; ?>image/mail/email-iphoneapp.png"></a> <?php } ?>
<?php if(isset($androidapp_url)){ ?> <a href="<?php echo $androidapp_url; ?>"><img width="130" height="40" alt="Android app" title="<?php echo $store_name; ?> Android app" src="<?php echo $store_url; ?>image/mail/email-androidapp.png"></a> <?php } ?>
<?php if(isset($windows_appurl)){ ?> <a href="<?php echo $windows_appurl; ?>"><img width="130" height="40" alt="Windows app" title="<?php echo $store_name; ?> Windows app" src="<?php echo $store_url; ?>image/mail/email-windowsapp.png"></a> <?php } ?>
  </p>
  <img class="img-responsive" alt="<?php echo $store_name; ?>" title="<?php echo $store_name; ?>" src="<?php echo $footer_image; ?>">
</div>
</body>
</html>