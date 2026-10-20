<?php if(isset($worldpay_err)){ echo $worldpay_err; } else { ?>
<script type="text/javascript">
    // Simulate an HTTP redirect:
    window.location.replace("<?php echo $worldpay_http_url; ?>");
</script>
<?php } ?>