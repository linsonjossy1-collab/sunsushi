<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-3.3.1.min.js"></script>
<style type="text/css">
a.button {
  background: #60aa44;
  border: 1px solid #60aa44;
  color: #000;
  cursor: pointer;
  display: inline-block;
  font-weight: bold;
  padding: 4px 15px;
  text-decoration: none;
  margin: 1px;
  border-radius: 2px;
  box-shadow: 1px 1px 2px 0px rgba(0, 0, 0, 0.33);
  font-size: 18px;
}
</style>
</head>
<body style="margin:0px;padding:0px;">
    <?php if(isset($continue)) { ?>
    <div class="content">
        <b><span style="color: #FF0000">Please wait...</span></b>
        <br>If you are not automatically re-directed in 10 seconds, please click <a href="<?php echo $continue; ?>" class="button" id="safariappcontinue" ><?php echo $button_continue; ?></a>
    </div>
    <script type="text/javascript"><!--
         setTimeout(() => {
            document.getElementById('safariappcontinue').click();
          }, 100);

    //--></script> 
   <?php } else { ?>
     <div class="content">
        Something went wrong, please close the app and try again
    </div>
   <?php } ?>
</body>
</html>