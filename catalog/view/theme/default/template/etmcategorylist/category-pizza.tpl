<!--Default -->
<?php //print_r($this->data);?>
<div class="product-info_simple col-sm-12" id="product-info_<?php echo $category_id; ?>" >
    <div class="product_name">
        <?php echo $name; ?> 

    </div>
    <span class="<?php echo($vegetarian!=0?'prodt_isveg':''); ?> <?php echo 'haschillies_'.$hot_count; ?>">
        
    </span>
    <div class="product_description">
        <?php if($description!='..') { echo $description; }  ?> 
    </div> 
    <div class="options">
    <?php foreach($subcategories as $category){  ?>
    <div class="option">


                            <div class="productdetails">
                                <div class="product_optionname"><?php echo $category['name']; ?><br/>
                                   <span class="sub_cat_desc"><?php if($category['description']!='..') { echo $category['description']; }  ?></span>
                                </div>

                                <div class="product_option_price">
                                    <?php echo $category['least_price']; ?> <a id="<?php echo $category['category_id']; ?>" class="category_prodt_popup button"><span>+</span></a>
                                </div>

 
                                
                            </div>
                        </div>
                        <div style="display: none;" id="cat_prodt_dialog_<?php echo $category['category_id']; ?>" title="<?php echo $category['name']; ?>">
                        </div>
    <?php } ?>
    </div>
</div>
