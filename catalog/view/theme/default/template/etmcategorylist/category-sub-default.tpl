<!-- Category Sub Default -->
<?php if(isset($sub_cats['sub_2_cat'])) { ?>
<div class="product-info_simple" id="product-info_<?php echo $sub_cats['category_id']; ?>">
    <div class="product-det-left">

    </div>
    <div class="product-det-right">
        <div class="product_name">
            <?php echo $sub_cats['name']; ?>

        </div>

        <div class="product_description">
            <?php if($sub_cats['description']!='..') { echo $sub_cats['description']; }  ?>
        </div>
        <?php foreach($sub_cats['sub_2_cat'] as $sub_2_cat){ ?>

        <div class="option">


            <div class="productdetails">
                <div class="product_optionname">
                    <?php echo $sub_2_cat['name']; ?>
                    <br/>
                    <span class="sub_cat_desc">
                        <?php if($sub_2_cat['description']!='..') { echo $sub_2_cat['description']; }  ?>
                    </span>
                </div>

                <div class="product_option_currency">
                    <?php // echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?>
                </div>
                <div class="product_option_price">
                    <?php echo $sub_2_cat['pr_least_price']; ?>
                </div>


                <div class="cart">
                    <a id="<?php echo $sub_2_cat['category_id']; ?>" class="category_prodt_popup button">
                        <span>+</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="cat_prodt_dialog_<?php echo $sub_2_cat['category_id']; ?>" aria-labelledby="<?php echo $sub_2_cat['name']; ?>">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                </div>
            </div>
        </div>


        <?php } ?>
    </div>
</div>
<?php } else if(isset($sub_cats)) { // print_r($sub_cats); ?> 

<div class="product-info_simple" id="product-info_<?php echo $sub_cats['category_id']; ?>">
    <div class="product-det-left">

    </div>
    <div class="product-det-right">
        <div class="product_name">
            <?php echo $sub_cats['name']; ?>

        </div>

        <div class="product_description">
            <?php if($sub_cats['description']!='..') { echo $sub_cats['description']; }  ?>
        </div>
        <div class="option">


            <div class="productdetails">
                <div class="product_optionname">
                    <?php //echo $all_products['name']; ?>
                </div>

                <div class="product_option_currency">
                    <?php // echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?>
                </div>
                <div class="product_option_price">
                    <?php echo $sub_cats['pr_least_price']; ?>
                </div>


                <div class="cart">
                    <a id="<?php echo $sub_cats['category_id']; ?>" class="category_prodt_popup button">
                        <span>+</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="cat_prodt_dialog_<?php echo $sub_cats['category_id']; ?>" aria-labelledby="<?php echo $sub_cats['name']; ?>">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>