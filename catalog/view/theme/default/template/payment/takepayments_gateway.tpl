<?php if ($loadIframe) { ?>
<iframe id="paymentgatewayframe" name="paymentgatewayframe" frameBorder="0" seamless='seamless' style="width:699px; height:1073px;margin: 0 auto;display:block;"></iframe>
<?php } ?>
<form id="paymentgatewaymoduleform" action="<?php echo $form_hosted_url; ?>" method="post" <?php if ($loadIframe) { ?> target="paymentgatewayframe" <?php } ?> >
	<?php foreach($formdata as $key=>$val) {
            echo "<input type=\"hidden\" name=\"" . $key .  "\" value=\"" . $val . "\" />" ;
         }      
	 ?>
	<?php if (!$loadIframe) { ?>
	<div class="buttons">
		<div class="pull-right">
                    <input type="submit" value="<?php echo $button_confirm ?>" id="button-confirm" class="btn btn-primary button" />
		</div>
	</div>
	<?php } ?>
</form>