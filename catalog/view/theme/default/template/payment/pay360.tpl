<?php if(isset($payredirect_url)) { ?>
<div id="pay360-loader-warp"><div id="pay360-loader" class="loader"></div></div>
<script>
    window.location = '<?php echo $payredirect_url; ?>';        
</script>
<?php } else { ?>
<h2> Payment Failed </h2>
<script>
    setTimeout(function(){
        window.location = '<?php echo $this->url->link($response_url); ?>';        
    },2000);
</script>
<?php } ?>