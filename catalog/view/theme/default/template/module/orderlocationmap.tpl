<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
<base href="<?php echo $base; ?>" />
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-3.3.1.min.js"></script>
<style type="text/css">
div#mobilepayment {
    padding: 0px 15px;
}
input#button-confirm {
    background: #dc307c;
    color: #fff;
    border: 2px solid #000;
    padding: 8px 15px;
}
</style>
</head>
<body style="margin:0px;padding:0px;">
<div id="logo" style="background: #353535;width:100%;text-align: center;">
<div type="button" id="mobile_back_button" style="text-align:left;color:#dc307c;padding:0px 15px;width: 10%;">&#8617;&nbsp;back</div>
    <?php if ($logo) { ?>
    <img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" style="margin: 0px auto;width: 20%;"/>
    <?php } ?>
</div>
<br />
<?php echo $locationmap; ?>
<script type="text/javascript"><!--
    $('#mobile_back_button').click(function(){ location = '<?php echo $back_url; ?>';});
//--></script> 
</body>
</html>