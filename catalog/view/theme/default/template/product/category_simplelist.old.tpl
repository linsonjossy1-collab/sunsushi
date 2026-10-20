<div id="catpage"><?php echo $header;   ?>
<?php //print_r($categoryname);?>
<link href="/catalog/view/theme/default/stylesheet/jquery.ui.all.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="/catalog/view/javascript/jquery/jquery.ui.core.js"></script>
<script type="teprint_rxt/javascript" src="/catalog/view/javascript/jquery/jquery.ui.widget.js"></script>
<script type="text/javascript" src="/catalog/view/javascript/jquery/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="/catalog/view/javascript/jquery/jquery.ui.button.js"></script>
<script type="text/javascript" src="/catalog/view/javascript/jquery/jquery.ui.draggable.js"></script>
<script type="text/javascript" src="/catalog/view/javascript/jquery/jquery.ui.position.js"></script>
<script type="text/javascript" src="/catalog/view/javascript/jquery/jquery.ui.dialog.js"></script>
<script language="javascript" type="text/javascript">
    function limitText(limitField, limitCount, limitNum) {
    if (limitField.value.length > limitNum) {
    limitField.value = limitField.value.substring(0, limitNum);
            } else {
    limitCount.value = limitNum - limitField.value.length;
            }
    }
    
</script>

<?php     //($allproducts);?>

<?php    echo $postcodecategory;;?>
<div id="bgclr">

<script>
$(function () {
  
  var msie6 = $.browser == 'msie' && $.browser.version < 7;
  
  if (!msie6) {
    var top = $('#columnleftcat').offset().top - parseFloat($('#columnleftcat').css('margin-top').replace(/auto/, 0));
    $(window).scroll(function (event) {
      // what the y position of the scroll is
      var y = $(this).scrollTop();
      
      // whether that's below the form
      if (y >= top) {
        // if so, ad the fixed class
        $('#columnleftcat').addClass('fixed');
      } else {
        // otherwise remove it
        $('#columnleftcat').removeClass('fixed');
      }
    });
  }  
});
</script>

<div id="columnleftcat"><?php echo $column_left; ?></div><?php echo $column_right; ?>
    <div id="content"><?php echo $content_top; ?>
        
        <div id="tabs">
         <?php /* ?>   <ul>
            <?php
            foreach ($allproducts as $cat_products) { ?>
                <li><a href="#cat_<?php echo $cat_products['category_id']; ?>"><?php echo $cat_products['name']; ?></a></li>
          <?php  }
            ?>
                </ul> <?php */ ?>
        <?php foreach($allproducts as $cat_products){ ?>
        <div id="cat_<?php echo  $cat_products['category_id'];?>" class="prod_main_categories">
            <div class="cat_name"> <h3><?php echo $cat_products['name'];?></h3></div>
            <div class="cat_desc">
                <?php echo $cat_products['description'];?>
            </div>
            <?php if(isset($cat_products['products'])) { ?>
            <?php $i=0; ?>
            <?php foreach($cat_products['products'] as $all_products) {
            $i++;
            ?>
            <div class="product-info_simple <?php echo(($i%2)==0?'even':'odd'); ?> <?php echo(($all_products['product_options'])?'':'without_opt');?>" id="product-info_<?php echo $all_products['product_id']; ?>" >

                <div class="product_name">
                    <?php echo $all_products['name']; ?> 

                </div>
                <div class="product_description">
                    <?php if($all_products['description']!='..') { echo $all_products['description']; }  ?> 
                </div>    


                <input type="hidden" name="product_id" size="2" value="<?php echo $all_products['product_id']; ?>" />

                <?php if($all_products['product_options']) { ?>
                <div id="option-<?php echo $all_products['product_options'][0]['product_option_id']; ?>" class="option">


                    <?php if(isset($all_products['product_options'][0]['option_value'])) { ?>
                    <?php foreach ($all_products['product_options'][0]['option_value'] as $option_value) { ?>
                    <div id="simplelist_product_details" class="product-det_<?php echo $all_products['product_id'].'_'.$option_value['product_option_value_id']; ?>">   
                        <input type="hidden" name="product_id" size="2" value="<?php echo $all_products['product_id']; ?>" />
                        <div class="productdetails" value="<?php echo $option_value['product_option_value_id']; ?>">
                            <div class="product_optionname"><?php echo $option_value['name']; ?></div>
                            <?php if ($option_value['price']) { ?>
                            <?php if (!$all_products['special']) { ?>
                            <div class="product_option_currency"> <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?></div>
                            <div class="product_option_price">
                                <?php echo $option_value['price']; ?> 
                            </div>
                            <?php } else { ?>
                            <div class="product_option_currency"> <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?></div>
                            <div class="product_option_price">
                                <?php echo $option_value['price']; ?> 
                            </div>
                            <?php }?>

                            <?php } ?>
                            <input type="hidden" name="option[<?php echo $all_products['product_options'][0]['product_option_id']; ?>]" size="2" value="<?php echo $option_value['product_option_value_id']; ?>" />

                            <?php if(count($all_products['product_options'])>1){
                            $div_id=$all_products['product_id'].'_'.$option_value['product_option_value_id'];
                            ?>
                            <div style="display: none;" id="dialog_<?php echo $div_id; ?>" title="<?php echo $option_value['name']; ?>">
                                <?php   foreach($all_products['product_options'] as $key=>$product_options) { 
                                if($key==0){
                                continue;
                                }
                                ?>
                                <div class="other_options" id="other_options_<?php echo $div_id; ?>" >
                                    <?php if ($product_options['type'] == 'select') { ?>
                                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                                        <?php if ($product_options['required']) { ?>
                                        <span class="required">*</span>
                                        <?php } ?>
                                        <div class="prod_extra_option_name"> <b><?php echo $product_options['name']; ?></b></div>
                                        <div class="prod_extra_option_det">
                                            :<select name="option[<?php echo $product_options['product_option_id']; ?>]">
                                                <option value=""><?php echo $text_select; ?></option>
                                                <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                                <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                    <?php if ( (float)$option_value['option_price']) { ?>
                                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                                    <?php } ?>
                                                </option>
                                                <?php } ?>
                                            </select></div>
                                    </div>
                                    <br />
                                    <?php } ?>
                                    <?php if ($product_options['type'] == 'radio') { ?>
                                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                                        <?php if ($product_options['required']) { ?>
                                        <span class="required">*</span>
                                        <?php } ?>
                                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?></b></div>
                                        <div class="prod_extra_option_det">
                                            :<?php foreach ($product_options['option_value'] as $option_value) { ?>
                                            <input type="radio" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
                                            <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                <?php if ( (float)$option_value['option_price']) { ?>
                                                (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                                <?php } ?>
                                            </label>
                                            <br />
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <br />
                                    <?php } ?>
                                    <?php if ($product_options['type'] == 'checkbox') { ?>
                                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                                        <?php if ($product_options['required']) { ?>
                                        <span class="required">*</span>
                                        <?php } ?>
                                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>:</b><br /></div>
                                        <div class="prod_extra_option_det">
                                            <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                            <input type="checkbox" name="option[<?php echo $product_options['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
                                            <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                <?php if ( (float)$option_value['option_price']) { ?>
                                                (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                                <?php } ?>
                                            </label>
                                            <br />
                                            <?php } ?>
                                        </div> 
                                    </div>
                                    <br />
                                    <?php } ?>

                                    <?php if ($product_options['type'] == 'image') { ?>
                                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                                        <?php if ($product_options['required']) { ?>
                                        <span class="required">*</span>
                                        <?php } ?>
                                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>:</b><br /></div>
                                        <div class="prod_extra_option_det">
                                            <table class="option-image">
                                                <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                                <tr>
                                                    <td style="width: 1px;"><input type="radio" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
                                                    <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
                                                    <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                            <?php if ( (float)$option_value['option_price']) { ?>
                                                            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                                            <?php } ?>
                                                        </label></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </div>    
                                    </div>
                                    <br />
                                    <?php } ?>
                                    <?php if ($product_options['type'] == 'text') { ?>
                                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                                        <?php if ($product_options['required']) { ?>
                                        <span class="required">*</span>
                                        <?php } ?>
                                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>:</b><br /></div>
                                        <div class="prod_extra_option_det"><input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" />
                                        </div></div>
                                    <br />
                                    <?php } ?>
                                    <?php if ($product_options['type'] == 'textarea') { ?>
                                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                                        <?php if ($product_options['required']) { ?>
                                        <span class="required">*</span>
                                        <?php } ?>
                                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>:</b><br /></div>
                                        <div class="prod_extra_option_det"> <textarea name="option[<?php echo $product_options['product_option_id']; ?>]" cols="40" rows="5"><?php echo $product_options['option_value']; ?></textarea>
                                        </div></div>
                                    <br />
                                    <?php } ?>
                                    <?php if ($product_options['type'] == 'file') { ?>
                                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                                        <?php if ($product_options['required']) { ?>
                                        <span class="required">*</span>
                                        <?php } ?>
                                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>:</b><br /></div>
                                        <div class="prod_extra_option_det"><input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $product_options['product_option_id']; ?>" class="button">
                                            <input type="hidden" name="product_options[<?php echo $product_options['product_option_id']; ?>]" value="" />
                                        </div></div>
                                    <br />
                                    <?php } ?>
                                    <?php if ($product_options['type'] == 'date') { ?>
                                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                                        <?php if ($product_options['required']) { ?>
                                        <span class="required">*</span>
                                        <?php } ?>
                                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>:</b><br /></div>
                                        <div class="prod_extra_option_det"><input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="date" />
                                        </div> </div>
                                    <br />
                                    <?php } ?>
                                    <?php if ($product_options['type'] == 'datetime') { ?>
                                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                                        <?php if ($product_options['required']) { ?>
                                        <span class="required">*</span>
                                        <?php } ?>
                                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>:</b><br /></div>
                                        <div class="prod_extra_option_det"> <input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="datetime" />
                                        </div> </div>
                                    <br />
                                    <?php } ?>
                                    <?php if ($product_options['type'] == 'time') { ?>
                                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">
                                        <?php if ($product_options['required']) { ?>
                                        <span class="required">*</span>
                                        <?php } ?>
                                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>:</b><br /></div>
                                        <div class="prod_extra_option_det"> <input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="time" />
                                        </div></div>
                                    <br />
                                    <?php } ?>


                                </div>    

                                <?php } ?>
                                    
                                <span class="cart"> <a id="button-cart_<?php echo $all_products['product_id']; ?>" class="button_cart button" prod_id="<?php echo $div_id; ?>"><span><?php echo $button_cart; ?></span></a></span>  
                                
                                <span class="cart"> <a class="button_cancel button" prod_id="<?php echo $div_id; ?>"><span>Cancel</span></a></span>
                            </div>

                            <div class="product_option_popup"> <input type="button" value="add" class="popupbuttonn button" prod_id="<?php echo $div_id; ?>" id="popupbuttonn_<?php echo $div_id; ?>" divide="<?php echo $div_id; ?>" ></div>
                            <?php } 
                            else{
                            ?>
                            <div class="cart"> <a id="button-cart_<?php echo $all_products['product_id']; ?>"  class="button-cart button" prod_id="<?php echo $all_products['product_id'].'_'.$option_value['product_option_value_id']; ?>"><span><?php echo $button_cart; ?></span></a></div>  
                            <?php } ?>
                        </div></div>
                    <?php } ?>

                    <?php } ?>

                </div>

                <?php  } 
                else{
                ?>
                <div class="option">


                    <div class="productdetails">
                        <div class="product_optionname"><?php //echo $all_products['name']; ?></div>

                        <div class="product_option_currency"> <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?></div>
                        <div class="product_option_price">
                            <?php echo $all_products['price']; ?> 
                        </div>



                        <a id="<?php echo $all_products['product_id']; ?>" class="button-cart_wop button"><span><?php echo $button_cart; ?></span></a>
                    </div>
                </div>     
                <?php
                }
                ?>   

            </div>







            <?php } ?>
            <?php } ?>
        </div>
        <?php } ?>



    </div>
        <?php if (!$categories && !$products) { ?>
        <div class="content"><?php echo $text_empty; ?></div>
        <div class="buttons">
            <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
        </div>
        <?php } ?>
        <?php echo $content_bottom; ?></div>

    <script type="text/javascript"><!--
    function display(view) {
        if (view == 'list') {
        $('.product-grid').attr('class', 'product-list');
                $('.product-list > div').each(function(index, element) {
        html = '<div class="right">';
                html += '  <div class="cart">' + $(element).find('.cart').html() + '</div>';
                html += '  <div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
                html += '  <div class="compare">' + $(element).find('.compare').html() + '</div>';
                html += '</div>';
                html += '<div class="left">';
                var image = $(element).find('.image').html();
                if (image != null) {
        html += '<div class="image">' + image + '</div>';
        }

        var price = $(element).find('.price').html();
                if (price != null) {
        html += '<div class="price">' + price + '</div>';
        }

        html += '  <div class="name">' + $(element).find('.name').html() + '</div>';
                html += '  <div class="description">' + $(element).find('.description').html() + '</div>';
                var rating = $(element).find('.rating').html();
                if (rating != null) {
        html += '<div class="rating">' + rating + '</div>';
        }

        html += '</div>';
                $(element).html(html);
        });
                $('.display').html('<b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');
                $.cookie('display', 'list');
        } else {
        $('.product-list').attr('class', 'product-grid');
                $('.product-grid > div').each(function(index, element) {
        html = '';
                var image = $(element).find('.image').html();
                if (image != null) {
        html += '<div class="image">' + image + '</div>';
        }

        html += '<div class="name">' + $(element).find('.name').html() + '</div>';
                html += '<div class="description">' + $(element).find('.description').html() + '</div>';
                var price = $(element).find('.price').html();
                if (price != null) {
        html += '<div class="price">' + price + '</div>';
        }

        var rating = $(element).find('.rating').html();
                if (rating != null) {
        html += '<div class="rating">' + rating + '</div>';
        }

        html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
                html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
                html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';
                $(element).html(html);
        });
                $('.display').html('<b><?php echo $text_display; ?></b> <a onclick="display(\'list\');"><?php echo $text_list; ?></a> <b>/</b> <?php echo $text_grid; ?>');
                $.cookie('display', 'grid');
        }
        }

        view = $.cookie('display');
                if (view) {
        display(view);
        } else {
        display('list');
        }
        //--></script> 
    <script type="text/javascript"><!--
    $('.colorbox').colorbox({
        overlayClose: true,
                opacity: 0.5
        });
                //--></script> 
    <script type="text/javascript"><!--            
//--></script>
    <?php if ($options) { ?>
    <script type="text/javascript" src="catalog/view/javascript/jquery/ajaxupload.js"></script>
    <?php foreach ($options as $option) { ?>
    <?php if ($option['type'] == 'file') { ?>
    <script type="text/javascript"><!--
    new AjaxUpload('#button-option-<?php echo $option['product_option_id']; ?>', {
        action: 'index.php?route=product/product/upload',
                name: 'file',
                autoSubmit: true,
                responseType: 'json',
                onSubmit: function(file, extension) {
        $('#button-option-<?php echo $option['product_option_id']; ?>').after('<img src="catalog/view/theme/default/image/loading.gif" class="loading" style="padding-left: 5px;" />');
                $('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', true);
        },
                onComplete: function(file, json) {
        $('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', false);
                $('.error').remove();
                if (json['success']) {
        alert(json['success']);
                $('input[name=\'option[<?php echo $option['product_option_id']; ?>]\']').attr('value', json['file']);
        }

        if (json['error']) {
        $('#option-<?php echo $option['product_option_id']; ?>').after('<span class="error">' + json['error'] + '</span>');
        }

        $('.loading').remove();
        }
        });
                //--></script>
    <?php } ?>
    <?php } ?>
    <?php } ?>
    <script type="text/javascript"><!--
    $('#review .pagination a').live('click', function() {
        $('#review').fadeOut('slow');
                $('#review').load(this.href);
                $('#review').fadeIn('slow');
                return false;
        });
                ;
                //--></script> 
    <script type="text/javascript"><!--
   //  $('#tabs a').tabs();
                //--></script> 
    <script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script> 
    <script type="text/javascript"><!--
    if ($.browser.msie && $.browser.version == 6) {
        $('.date, .datetime, .time').bgIframe();
        }

        $('.date').datepicker({dateFormat: 'yy-mm-dd'});
                $('.datetime').datetimepicker({
        dateFormat: 'yy-mm-dd',
                timeFormat: 'h:m'
        });
                $('.time').timepicker({timeFormat: 'h:m'});
                //--></script>

<div style="clear:both;"></div>

</div>

<?php echo $footer; ?>
</div>
