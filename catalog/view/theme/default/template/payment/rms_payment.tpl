<form action="<?php echo $url; ?>" method="post">
	<?php
		foreach ( $formdata as $key => $value ) {

			echo "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $value . "\" />";

		}
	?>
	<div class="buttons">
		<div class="right">
			<input type="submit" value="<?php echo $button_confirm; ?>" class="button"/>
		</div>
	</div>
</form>
