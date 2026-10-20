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

    <p>
    <?php echo $text_order_update; ?><br>
    <?php if($comment) { ?>
    Comment : <b><?php echo $comment; ?></b><br>
    <?php } ?>
    </p>
    <p style="font-size:1.5em; font-weight:700;"><?php echo $updated_shipping_text; ?><p>
    <p><?php echo $text_content; ?></p>

    
    <?php if ($customer_id) { ?>
    <p style="margin-top: 2px; margin-bottom: 20px;"><?php echo $text_link; ?></p>
    <p style="margin-top: 2px; margin-bottom: 20px;"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></p>
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
          <b><?php echo $text_ip; ?></b> <?php echo $ip; ?><br /></td>
      </tr>
    </tbody>

  </table>
  
  
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
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['quantity']; ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['price']; ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['total']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $voucher['description']; ?></td>
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
