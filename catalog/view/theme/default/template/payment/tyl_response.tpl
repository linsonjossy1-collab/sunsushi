<h2><?php echo $text_response; ?></h2>
<?php if($text_message) { ?>
<h4><?php echo $text_message; ?></h4>
<?php } ?>
<p><?php echo $text_link; ?></p>
<script>
    setTimeout(function(){
        window.location = '<?php echo $this->url->link($response_url); ?>';        
    },2000);
</script>