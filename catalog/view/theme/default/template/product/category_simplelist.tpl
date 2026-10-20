<?php echo $header;   ?>
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

<?php    // print_r($allproducts);die(); ?>

<?php    echo $postcodecategory;;?>
<div id="catpage">
<div id="bgclr">

<script>
$(function () {
  $('.customize_option_single').bind('click', function() {

 $('.dialog_'+$(this).attr('id')).dialog({
                                                autoOpen: true,
                                                height: 300,
                                                width: 350,
                                                modal: true,
                                                buttons: {
                                                    "OK": function() {
                                                        $(this).dialog('close');
                                                    },
                                                },
                                            });
 
});
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
        //$('#columnleftcat').removeClass('pgleftbottomfixed');
        //$('#columnrightcat').removeClass('pgrightbottomfixed');
      } else {
        // otherwise remove it
        $('#columnleftcat').removeClass('fixed');
        //$('#columnleftcat').removeClass('pgleftbottomfixed');
        //$('#columnrightcat').removeClass('pgrightbottomfixed');
       // $('#columnleftcat').removeClass('fixed');
      }
    });
  }  
});
</script>
<script>
$(function () {
  
  var msie6 = $.browser == 'msie' && $.browser.version < 7;
  
  if (!msie6) {
    var top = $('#columnrightcat').offset().top - parseFloat($('#columnrightcat').css('margin-top').replace(/auto/, 0));
    $(window).scroll(function (event) {
      // what the y position of the scroll is
      var y = $(this).scrollTop();
      
      // whether that's below the form
      if (y >= top) {
        // if so, ad the fixed class
        //$('#columnleftcat').removeClass('pgleftbottomfixed');
        //$('#columnrightcat').removeClass('pgrightbottomfixed');
        $('#columnrightcat').addClass('fixed');
      } else {
        // otherwise remove it
        //$('#columnleftcat').removeClass('pgleftbottomfixed');
        //$('#columnrightcat').removeClass('pgrightbottomfixed');
        $('#columnrightcat').removeClass('fixed');
        
      }
    });
  }  
});
</script>
<script>
$(function () {
  
  var msie6 = $.browser == 'msie' && $.browser.version < 7;
  
  if (!msie6) {
    var bottom = $('#footer_bg').position().top - $(window).height() ;
    var top = $('#content').offset().top - parseFloat($('#content').css('margin-top').replace(/auto/, 0));
    $(window).scroll(function (event) {
      // what the y position of the scroll is
      var y = $(this).scrollTop();
      
      // whether that's below the form
      //if (y >= top) {
        // if so, ad the fixed class
        //$('#content').addClass('fixed');
      //} else {
        // otherwise remove it
      //  $('#content').removeClass('fixed');
      //  
    //  }
        //$('#columnleftcat').removeClass('pgleftbottomfixed');
        //$('#columnrightcat').removeClass('pgrightbottomfixed');
     if (y >= top  ) {
        // if so, ad the fixed class
        //if( y <= bottom){
        if(y<=bottom){
        $('#content').addClass('fixed');
        $('#columnleftcat').removeClass('pgleftbottomfixed');
        $('#columnrightcat').removeClass('pgrightbottomfixed');
        $('#content').removeClass('pgcentrebottomfixed');
        }else{
        $('#columnleftcat').removeClass('fixed');
         $('#columnrightcat').removeClass('fixed');
        $('#content').removeClass('fixed');
         $('#content').addClass('pgcentrebottomfixed');
         $('#columnleftcat').addClass('pgleftbottomfixed');
         $('#columnrightcat').addClass('pgrightbottomfixed');
        } 
         
      } else {
      //alert('exceedes');
        // otherwise remove it
        $('#columnleftcat').removeClass('fixed');
         $('#columnrightcat').removeClass('fixed');
        $('#content').removeClass('fixed');
         //$('#columnleftcat').addClass('pgleftbottomfixed');
         //$('#columnrightcat').addClass('pgrightbottomfixed');
      }
    });
  }  
});
</script>

<div id="columnleftcat"><?php echo $column_left; ?></div><div id="columnrightcat"><?php echo $column_right; ?></div>
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
            
            <div class="cat_name"> <h3 style="background: url('<?php echo $cat_products['bg_image']; ?>')no-repeat scroll 0 0 rgba(0, 0, 0, 0);"><?php echo $cat_products['name'];?></h3></div>
            <div class="cat_desc">
                <?php echo $cat_products['description'];?>
            </div>
             <?php if(isset($cat_products['sub_cats'])) { ?>
                    <?php $i=0; ?>
                    <?php foreach($cat_products['sub_cats'] as $sub_category) { 
                    $i++;
                    ?>
                    <?php if(isset($sub_category['sub_2_cat'])) { ?>
                    <div class="product-info_simple <?php echo($sub_category['vegetarian']!=0?'prodt_isveg':''); ?> <?php echo 'haschillies_'.$sub_category['hot_count']; ?> <?php echo(($i%2)==0?'even':'odd'); ?>" id="product-info_<?php echo $sub_category['category_id']; ?>" >
                        <div class="product_name">
                            <?php echo $sub_category['name']; ?>

                        </div>
                        <div class="product_description">
                            <?php if($sub_category['description']!='..') { echo $sub_category['description']; }  ?>
                        </div>
                    <?php foreach($sub_category['sub_2_cat'] as $sub_2_cat){ ?>
                     
                        <div class="option">


                            <div class="productdetails">
                                <div class="product_optionname"><?php echo $sub_2_cat['name']; ?><br/>
                                   <span class="sub_cat_desc"><?php if($sub_2_cat['description']!='..') { echo $sub_2_cat['description']; }  ?></span>
                                </div>

                                <div class="product_option_currency"> <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?></div>
                                <div class="product_option_price">
                                    <?php echo $sub_2_cat['pr_least_price']; ?>
                                </div>


                                <div class="cart">
                                    <a id="<?php echo $sub_2_cat['category_id']; ?>" class="category_prodt_popup button"><span><?php echo $button_cart; ?></span></a>
                                </div>
                            </div>
                        </div>
                        <div style="display: none;" id="cat_prodt_dialog_<?php echo $sub_2_cat['category_id']; ?>" title="<?php echo $sub_2_cat['name']; ?>">
                        </div>

                    
                    <?php } ?>
                    </div>
                    <?php } else { ?>
                    <div class="product-info_simple <?php echo($sub_category['vegetarian']!=0?'prodt_isveg':''); ?> <?php echo 'haschillies_'.$sub_category['hot_count']; ?> <?php echo(($i%2)==0?'even':'odd'); ?>" id="product-info_<?php echo $sub_category['category_id']; ?>" >
                        <div class="product_name">
                            <?php echo $sub_category['name']; ?>

                        </div>
                        <div class="product_description">
                            <?php if($sub_category['description']!='..') { echo $sub_category['description']; }  ?>
                        </div>
                        <div class="option">


                            <div class="productdetails">
                                <div class="product_optionname"><?php //echo $all_products['name']; ?></div>

                                <div class="product_option_currency"> <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?></div>
                                <div class="product_option_price">
                                    <?php echo $sub_category['pr_least_price']; ?>
                                </div>


                                <div class="cart">
                                    <a id="<?php echo $sub_category['category_id']; ?>" class="category_prodt_popup button"><span><?php echo $button_cart; ?></span></a>
                                </div>
                            </div>
                        </div>
                        <div style="display: none;" id="cat_prodt_dialog_<?php echo $sub_category['category_id']; ?>" title="<?php echo $sub_category['name']; ?>">
                        </div>

                    </div> 
                    <?php } ?>
                    <?php } ?>

                    <?php } ?>
            <?php if(isset($cat_products['products'])) { ?>
            <?php $i=0; ?>
            <?php foreach($cat_products['products'] as $all_products) {
            $i++;
            ?>
            <div class="product-info_simple <?php echo($all_products['vegetarian']!=0?'prodt_isveg':''); ?> <?php echo 'haschillies_'.$all_products['hot']; ?> <?php echo(($i%2)==0?'even':'odd'); ?> <?php echo(($all_products['product_options'])?'':'without_opt');?>" id="product-info_<?php echo $all_products['product_id']; ?>" >

                <div class="product_name">
                    <?php echo $all_products['name']; ?> 

                </div>
                <div class="product_description">
                    <?php if($all_products['description']!='..') { echo $all_products['description']; }  ?> 
                </div>    


                <input type="hidden" name="product_id" size="2" value="<?php echo $all_products['product_id']; ?>" />
                <?php if($all_products['special_deal']==1){ ?> 
                 <div class="option">


                            <div class="productdetails">
                                <div class="product_optionname"><?php echo "Choose Options"; ?></div>

                                <div class="product_option_currency"> <?php  echo $this->currency->getSymbolLeft().$this->currency->getSymbolRight();?></div>
                                <div class="product_option_price">
                                    <?php echo $all_products['price']; ?>
                                </div>



                                <a id="<?php echo $cat_products['category_id'].'_'.$all_products['product_id']; ?>" class="bundled_prodt_popup button"><span><?php echo $button_cart; ?></span></a>
                            </div>
                        </div>
                        <div id="bundled_prodt_<?php echo $cat_products['category_id'].'_'.$all_products['product_id']?>" title="<?php echo $all_products['name']; ?>" style="display: none">
                        </div>
                <?php } else { ?>
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
                            <div style="display: none;" id="dialog_<?php echo $div_id; ?>" title="<?php echo $all_products['name']; ?>">
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
                }}
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
    

<div style="clear:both;"></div>

</div>
</div>
<?php echo $footer; ?>

