<!--Pizza image & Custom -->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
    <h4 class="modal-title"><?php echo $name; ?></h4>
</div>
<div class="modal-body">
<div class="product-info_simple category-pizza-image" id="product-info_<?php echo $parent_cat_id; ?>" >
    <div class="product-det-left">
        <div class="product_image">
            <img src="<?php echo $image; ?> " /> 
        </div>
    </div>
    <div class="product-det-right">
        <div class="product_name">
            <?php echo $name; ?> 

        </div>
        <div class="product_description">
            <?php if($description!='..') { echo $description; }  ?> 
        </div>
    </div>
    <div class="option" >
       
        <div id="cat-pizza-selection-<?php $subctaegory['category_id']; ?>">
         <div class="cat-shoose-details">
            <span><?php echo $subctaegory['name'] ?></span>
            <span><select name="product_id" class="cat-pizza-product-selection default" catid="pop-<?php echo $parent_cat_id.'_'.$subctaegory['category_id'];?>">
                    <?php $options=array();
                    $k=0; ?>
                    <?php foreach($subctaegory['products'] as $product){ ?>
                    <option value="<?php echo $product['product_id'] ?>"><?php echo $product['name'] ?></option>
                    <?php if($k==0){ 
                    $options=$product['product_options'];
                    } ?>
                    <?php $k++; ?>
                    <?php } ?>

                </select></span>
        </div>
        <?php if(!empty($options)) { ?>
        <div class="cat-pizza-options" id="cat-pizza-prodt-options-pop-<?php echo $parent_cat_id.'_'.$subctaegory['category_id'];?>">
        
        <?php foreach ($options as $option) {  ?>
        
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
         <br />
          <?php } ?> 
        </div>
        <?php } else { ?>
         <div id="optioncus-<?php echo $option['product_option_id']; ?>" class="option ">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b>
          <div class="option-opened">
          <ul>
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <li>
          <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>"  />
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
            <?php } ?>
          </label>
          </li>
          <?php } ?>
          </ul>
          </div>
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
          ><input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
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
          <textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5" id="limitedtextarea" onkeydown="limitText(this.form.limitedtextarea,25,25);" onkeyup="limitText(this.form.limitedtextarea,25,25);"><?php echo $option['option_value']; ?></textarea>
          </form>
        </div>
        <br />
        <?php } ?>
        
        <?php } ?>
      </div>
         <?php } ?>
        </div>
        <div class="cart-buttons">
        <input type="button" class="button-pizza-add button" id="" onclick="etmAddtocart('cat-pizza-prodt-popup-<?php echo $parent_cat_id.'_'.$subctaegory['category_id'];?>');" value="Add">
    </div>
    </div>
</div>
    </div>