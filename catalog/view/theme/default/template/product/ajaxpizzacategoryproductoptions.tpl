<style type="text/css">
.ui-dialog .ui-dialog-content {
    color: #000000 !important;;
   
}
</style>
<?php foreach ($options as $option) {  ?>
<?php if ((isset($pizza))?$option['required'] == '1':$option['required'] == '1'|| $option['required'] == '0') { ?>


<?php if ($option['type'] == 'select') { ?>
<div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option">
    <?php if ($option['required']) { ?>
    <span class="required">*</span>
    <?php } ?>

    <b><?php echo $option['name']; ?>:</b>
    <select name="option[<?php echo $option['product_option_id']; ?>]">
        <option value="">Select</option>
        <?php foreach ($option['option_value'] as $option_value) { ?>
        <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
            <?php } ?>
        </option>
        <?php } ?>
    </select>

</div>

<?php } ?>
<?php if ($option['type'] == 'radio') { ?>
<div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option">
    <?php if ($option['required']) { ?>
    <span class="required">*</span>
    <?php } ?>
    <b><?php echo $option['name']; ?>:</b>
    <div class="option_values_radiobtn">
        <?php foreach ($option['option_value'] as $option_value) { ?>
        <div class="subcat-reqrd-options" >
            <div class="sub-cat-req-opname"><?php echo $option_value['name']; ?></br></div>
            <div class="sub-cat-req-opval">
                <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
                <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>">
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price']; ?>)
                    <?php } ?>
                </label>
            </div>
        </div>
        <?php } ?>
    </div>
</div>  

<?php } ?>
<?php if ($option['type'] == 'checkbox') { ?>
<?php if ($option['default'] == '1') { ?>
<div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option ">
    <?php if ($option['required']) { ?>
    <span class="required">*</span>
    <?php } ?>
    <b><?php echo $option['name']; ?>:</b>
    <?php foreach ($option['option_value'] as $option_value) { ?>
    <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" checked />
    <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
        <?php if ($option_value['price']) { ?>
        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
        <?php } ?>
    </label>

    <?php } ?> 
</div>
<?php } else { ?>
<div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option ">
    <?php if ($option['required']) { ?>
    <span class="required">*</span>
    <?php } ?>
    <b><?php echo $option['name']; ?>:</b>
    <?php foreach ($option['option_value'] as $option_value) { ?>
    <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>"  />
    <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
        <?php if ($option_value['price']) { ?>
        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
        <?php } ?>
    </label>
    <br />
    <?php } ?>
</div>
<?php } ?>

<?php } ?>
<?php if ($option['type'] == 'image') { ?>
<div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option">
    <?php if ($option['required']) { ?>
    <span class="required">*</span>
    <?php } ?>
    <b><?php echo $option['name']; ?>:</b>
    <table class="option-image">
        <?php foreach ($option['option_value'] as $option_value) { ?>
        <tr>
            <td style="width: 1px;"><input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
            <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
            <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                    <?php } ?>
                </label></td>
        </tr>
        <?php } ?>
    </table>
</div>
<br />
<?php } ?>
<?php if ($option['type'] == 'text') { ?>
<div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option">
    <?php if ($option['required']) { ?>
    <span class="required">*</span>
    <?php } ?>
    <b><?php echo $option['name']; ?>:</b
    <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
</div>
<br />
<?php } ?>
<?php if ($option['type'] == 'textarea') { ?>
<div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option">
    <?php if ($option['required']) { ?>
    <span class="required">*</span>
    <?php } ?>
    <b><?php echo $option['name']; ?>:</b><br />
    <form name="myform">
        <textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5" id="limitedtextarea" onkeydown="limitText(this.form.limitedtextarea, 25, 25);" onkeyup="limitText(this.form.limitedtextarea, 25, 25);"><?php echo $option['option_value']; ?></textarea>
    </form>
</div>
<br />
<?php } ?>
<?php } ?>
<?php } ?>
