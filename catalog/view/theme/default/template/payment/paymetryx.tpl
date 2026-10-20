<form action="<?php echo $action; ?>" method="POST" class="form-horizontal" id="paymetryx_form_redirect">
<?php 
 foreach ($form_data as $field => $value) {
   echo ' <input type="hidden" name="' . $field . '" value="' .htmlentities($value) . '">' . PHP_EOL; 
 }
?>
<div class="buttons">
        <div class="right">
  <input type="submit" value="<?php echo $button_confirm; ?>" class="button" /> 
        </div>
    </div>
</form>
