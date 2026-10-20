<script type="text/javascript">
    <?php if($session_url) { ?>
    window.location.replace("<?php echo $session_url; ?>");
    <?php }else { ?>
     window.location.href("<?php echo $continue; ?>");
    <?php } ?>
</script>