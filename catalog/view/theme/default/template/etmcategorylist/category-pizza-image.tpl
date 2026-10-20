<!--Pizza image & Custom -->
<div class="category-pizza-image" id="product-info_<?php echo $category_id; ?>" >
    <div class="parent-cat-name"><?php echo $name; ?> </div>
    <div class="cat-pizza-sub" >
        <?php foreach($subcategories as $category){  ?>
        <div id="cat-pizza-selection-<?php echo $category['category_id']; ?>" class="category-info_simple">
            <div class="product-det-left">
               <div class="product_image">
                   <img src="<?php echo $category['image']; ?> " alt="<?php echo $category['image']; ?>" title="<?php echo $category['image']; ?>" /> 
               </div>
           </div>
           <div class="product-det-right">
               <div class="product_name">
                   <?php echo $category['name']; ?>

               </div>
               <div class="product_description">
                   <?php if($category['description']!='..') { echo $category['description']; }  ?> 
               </div>
           </div>   
         <div class="cat-shoose-details">
            <span><?php echo $category['name'] ?></span>
            <span><select name="product_id" class="cat-pizza-product-selection" catid="<?php echo $category_id.'_'.$category['category_id'];?>">
                    <?php $options=array();
                    $k=0; ?>
                    <?php foreach($category['products'] as $product){ ?>
                    <option value="<?php echo $product['product_id'] ?>"><?php echo $product['name'] ?></option>
                    <?php if($k==0){ 
                    $options=$product['product_options'];
                    } ?>
                    <?php $k++; ?>
                    <?php } ?>

                </select></span>
        </div>
        <?php if(!empty($options)) { ?>
         <div class="cat-pizza-options" id="cat-pizza-prodt-options-<?php echo $category_id.'_'.$category['category_id'];?>">
        
        <?php foreach ($options as $option) {  ?>
        <?php if ($option['required'] == '1') { ?>
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
          <textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5" id="limitedtextarea" onkeydown="limitText(this.form.limitedtextarea,25,25);" onkeyup="limitText(this.form.limitedtextarea,25,25);"><?php echo $option['option_value']; ?></textarea>
          </form>
        </div>
        <br />
        <?php } ?>
        <?php } ?>
        <?php } ?>
      </div>
         <?php } ?>
        </div>
        <div class="modal fade etm-popup-prodts"  tabindex="-1" role="dialog" id="cat-pizza-prodt-popup-<?php echo $category_id.'_'.$category['category_id'];?>" aria-labelledby="<?php echo $name; ?>">
            <div class="modal-dialog" role="document">
                <div class="modal-content"></div>
            </div>                
        </div>    
        <div class="cart-buttons">
        
        <input type="button" class="button-pizza-add button" id="cat-pizza-selection-<?php echo $category['category_id']; ?>"  value="+"> or 
        <input type="button" divid="<?php echo $category_id.'_'.$category['category_id'];?>" class="button-pizza-customize button" value="Customzie">
        </div>
        <?php } ?>
        
    
    </div>

</div>
