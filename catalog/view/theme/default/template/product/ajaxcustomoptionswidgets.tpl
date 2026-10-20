
<div class="product_custom_options">
    <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
    <input type="hidden" name="key" size="2" value="<?php echo $key; ?>" />
<?php if ($options) { ?>
<?php //print_r($options); ?>
      <div class="options" id="">
        <h2><?php //echo $text_option; ?></h2>
        <?php
        if(!empty($selected_options)) { 
        foreach ($options as $option) { 
      
        ?>
       
        <?php if ($option['type'] == 'checkbox') { ?>
        
         <div id="option-<?php echo $option['product_option_id']; ?>" class="option ">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <?php if(isset($selected_options[$option['product_option_id']])) { ?>
          <?php if(in_array($option_value['product_option_value_id'],$selected_options[$option['product_option_id']])) { ?>
          <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
         <?php } else{ ?>
         <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" checked/>
         <?php } ?>
         <?php }else {  ?>
         <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" checked/>
         <?php } ?>
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
           
          </label>
          <br />
          <?php } ?>
        </div>
        
       
        <br />
        <?php } ?>
       
        <?php } ?>
         <?php } else { ?>
         <?php foreach ($options as $option) { 
      
        ?>
       
        <?php if ($option['type'] == 'checkbox') { ?>
        
         <div id="option-<?php echo $option['product_option_id']; ?>" class="option ">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" checked="checked"  />
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
           
          </label>
          <br />
          <?php } ?>
        </div>
        
       
        <br />
        <?php } ?>
       
        <?php } ?>
         <?php } ?>
      </div>
      <?php } ?>
      <div class="cart">
        
          &nbsp;
         <a id="button_modify_cart" prod_id="<?php echo $product_id; ?>" class="button"><span>Modify Product</span></a>
         <a id="button_cancel_woc" prod_id="<?php echo $product_id; ?>" class="button"><span>Cancel</span></a>
      
        
        
        
        
      </div>
      
</div>

<script type="text/javascript">
 $(document).ready(function() {
 
 
 

});
</script>