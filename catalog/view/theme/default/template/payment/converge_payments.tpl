<div class="converge-payment-form">  
    <script>
          window.location = "<?php echo $hpp_url;?>?sessionId=<?php echo $converge_sessionId; ?>&publicApiKey=<?php echo $public_key; ?>&merchanAlias=<?php echo $merchant_alias; ?>";
     </script>
     <div id="payment-loader" class="loader"></div>
</div>
