<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $title; ?></title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
<a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>"><img class="img-responsive" alt="<?php echo $store_name; ?>" title="<?php echo $store_name; ?>" src="<?php echo $header_image; ?>"></a>
<div style="width: 698px; padding: 5px; border: 1px solid #dddddd; border-bottom: 20px solid #dddddd; margin-top: -5px;">
  
<table width="100%" border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td width="69%">
    
    <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_greeting; ?></p>
   <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_storephone; ?></p>
  <?php if ($customer_id) { ?>
  <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_link; ?></p>
  <p style="margin-top: 0px; margin-bottom: 20px;"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></p>
  <?php } ?>
  <?php if ($download) { ?>
  <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_download; ?></p>
  <p style="margin-top: 0px; margin-bottom: 20px;"><a href="<?php echo $download; ?>"><?php echo $download; ?></a></p>
  <?php } ?>
    
    <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    <thead>
      <tr>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;" colspan="2"><?php echo $text_order_detail; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><b><?php echo $text_order_id; ?></b> <?php echo $order_id; ?><br />
          <b><?php echo $text_date_added; ?></b> <?php echo $date_added; ?><br />
          <b><?php echo $text_payment_method; ?></b> <?php echo $payment_method; ?><br />
          <?php if ($shipping_method) { ?>
          <b><?php echo $text_shipping_method; ?></b> <?php echo $shipping_method; ?>
          <?php } ?>
          <?php if ($requested_time) { ?>
          <br/><b><?php echo $text_requested_time; ?></b> <?php echo $requested_time; ?>
          <?php } ?>
        </td>
          
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><b><?php echo $text_email; ?></b> <?php echo $email; ?><br />
          <b><?php echo $text_telephone; ?></b> <?php echo $telephone; ?><br />
          </td>
      </tr>
    </tbody>
  </table>
  
 <?php if ($comment) { ?>
  <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    <thead>
      <tr>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?php echo $text_instruction; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $comment; ?></td>
      </tr>
    </tbody>
  </table>
  <?php } ?>
  
  
  <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    <thead>
      <tr>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?php echo $text_payment_address; ?></td>
        <?php if ($shipping_address) { ?>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?php echo $text_shipping_address; ?></td>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $payment_address; ?></td>
        <?php if ($shipping_address) { ?>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $shipping_address; ?></td>
        <?php } ?>
      </tr>
    </tbody>
  </table>
   
  <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    <thead>
      <tr>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?php echo $text_product; ?></td>
        <!--<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?php echo $text_model; ?></td>-->
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?php echo $text_quantity; ?></td>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?php echo $text_price; ?></td>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?php echo $text_total; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product) { ?>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $product['name']; ?>
          <?php foreach ($product['option'] as $option) { ?>
          <br />
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?>
           <?php foreach ($product['toppings'] as $topping) { ?>
                     -<small> <?php echo $topping['quantity']." X ". $topping['name'].":".$topping['value'];?></small><br />
                <?php } ?>
                <?php foreach ($product['dressings'] as $dressing) { ?>
                     -<small><?php echo $dressing['name'].":".$dressing['value'];?></small><br />
                <?php } ?>
          <?php foreach ($product['bundle'] as $bundle) { ?>  
             <br />
               <b><small><?php 
                  echo $bundle['name'].":";
                 ?> </small></b><br />
                 <?php foreach ($bundle['options'] as $bundleoption) { ?>
              -- <small><?php 
                  echo $bundleoption['name'].":";
                 
                  ?> <?php echo $bundleoption['value']; ?></small><br />
              <?php } ?>
              <?php foreach ($bundle['toppings'] as $bundtopping) { ?>
                     -<small> <?php echo $bundtopping['quantity']." X ". $bundtopping['name'].":".$bundtopping['value'];?></small><br />
                <?php } ?>
                <?php foreach ($bundle['dressings'] as $bundressing) { ?>
                     -<small><?php echo $bundressing['name'].":".$bundressing['value'];?></small><br />
                <?php } ?>
              <?php } ?>
        </td>
        <!--<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $product['model']; ?></td>-->
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['quantity']; ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['price']; ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['total']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $voucher['description']; ?></td>
        <!--<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"></td>-->
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">1</td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $voucher['amount']; ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $voucher['amount']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;" colspan="3"><b><?php echo $total['title']; ?>:</b></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
  
  
  </td>
    <td width="1%">&nbsp;</td>
    <td width="30%" valign="top"><a href="<?php echo $offer_url; ?>" title="<?php echo $store_name; ?>"><img width="200" class="img-responsive" alt="<?php echo $store_name; ?>" title="<?php echo $store_name; ?>" src="<?php echo $offer_image; ?>"></a></td>
  </tr>
</table>
  
  
  
  
  
  <p style="margin-top: 0px; margin-bottom: 20px;"> <?php echo $store_name; ?></p>
  <p style="margin-top: 0px; margin-bottom: 20px;"><a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>"><?php echo $store_url; ?></a></p>
  <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_orderfooter; ?><?php echo $text_orderfooter1; ?></p>
  
  <p style="margin-top: 0px; margin-bottom: 20px; text-align: center;"> <strong><?php echo $store_name; ?>, <?php echo $store_address; ?> <a href="tel:<?php echo $store_telephone; ?>" title="<?php echo $store_name; ?>"><?php echo $store_telephone; ?></a></strong></p>

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
