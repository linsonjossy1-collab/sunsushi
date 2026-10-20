<?php echo $header;
$this->load->model('catalog/product');
if(isset($_REQUEST['postcode'])){
$postcode=$_REQUEST['postcode'];
$postcode_distance=$_REQUEST['d'];
$_SESSION['postcode']=$postcode;
$_SESSION['postcode_distance']=$postcode_distance;
}else{
if(isset($_SESSION['postcode'])){
$postcode=$_SESSION['postcode'];
}else{
$_SESSION['postcode']='';
}

}
?>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/jquery-bubble-popup-v3.css" />
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-bubble-popup-v3.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-ui-1.8.2.custom.min.js"></script>
<script>
    $(function() {
    // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
		
    jQuery('#cart_total').click();
    jQuery('#cart_total').hide();
    $( "#dialog-form" ).dialog({
    autoOpen: false,
    height: 300,
    width: 350,
    modal: true
			
});

$('a#shoppingcart_button').CreateBubblePopup({
align: 'right',
													
innerHtml: '', 
innerHtmlStyle: {
color:'#FFFFFF', 
'text-align':'center'
},
																	
themeName: 	'all-black',
themePath: 	'images/jquerybubblepopup-themes'
});
// Note: you need to use quotation marks for each CSS property of "innerHtmlStyle" with a "-"

// Note: 
// you can add more mouseover/mouseout events to the shopping cart button, 
// new events don't interfere with bubble popup events.

$('a#shoppingcart_button').mouseover(function(){
$('#status').css({
'background-color':'#ECFFD9',
border:'#006600 1px solid'
});
$('#status').html('your mouse is over a button');
});
$('div.jquerybubblepopup').mouseout(function(){
              
$('#status').css({
'background-color':'#FFFFFF',
border:'#FFFFFF 1px solid'
});
$('#status').html('');
});


// when shoppingcart button is clicked then new bubble popup is shown with new options;
// finally, if the bubble popup is not closed, pause for 10 seconds then hide it...



$( "a#create-user" )			
.click(function() {
var n=$(this).attr("class");
var content = $('span#prod_modal_list'+n).html();
$('#dialog-form').html('');
$('#dialog-form').append(content);
$( "#dialog-form" ).dialog( "open" );
});
                       
});
function addToCartProduct(n){

$.ajax({
url: 'index.php?route=checkout/cart/update',
type: 'post',
data: $('.product-carts'+n+' input[type=\'text\'], .product-carts'+n+' input[type=\'hidden\'], .product-carts'+n+' input[type=\'radio\']:checked, .product-carts'+n+' input[type=\'checkbox\']:checked, .product-carts'+n+' select, .product-carts'+n+' textarea'),
dataType: 'json',
success: function(json) {
$('.success, .warning, .attention, information, .error').remove();
//jQuery('.jquerybubblepopup-azure').hide();	

if (json['error']) {
if (json['error']['warning']) {
$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
$('.warning').fadeIn('slow');
}
				
for (i in json['error']) {
$('#option-' + i).after('<span class="error">' + json['error'][i] + '</span>');
}
}	 
						
if (json['success']) {
$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
$('.success').fadeIn('slow');
			
$('#cart-total').html(json['total']);
jQuery('#cart-total').click();	

var content = $('div.related'+n).html();
var details= content.split('<div id="related_details">');

if(details[1]!=undefined){
jQuery('.jquerybubblepopup-azure').html('');
jQuery('.jquerybubblepopup-azure').append(content);
   }else{
  
   jQuery('.jquerybubblepopup-azure').hide();
   }
	//jQuery('div#login_box_content').append(content);
 
$('html, body').animate({ scrollTop: 0 }, 'slow'); 
}	
}
});

}
function closepopup(){
jQuery('.jquerybubblepopup-azure').hide();
}
function addToCartProducts(n){

$.ajax({
url: 'index.php?route=checkout/cart/update',
type: 'post',
data: $('.relateds'+n+' input[type=\'text\'], .relateds'+n+' input[type=\'hidden\'], .relateds'+n+' input[type=\'radio\']:checked, .relateds'+n+' input[type=\'checkbox\']:checked, .relateds'+n+' select, .relateds'+n+' textarea'),
dataType: 'json',
success: function(json) {
$('.success, .warning, .attention, information, .error').remove();
	

if (json['error']) {
if (json['error']['warning']) {
$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
$('.warning').fadeIn('slow');
}
				
for (i in json['error']) {
$('#option-' + i).after('<span class="error">' + json['error'][i] + '</span>');
}
}	 
						
if (json['success']) {
$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
$('.success').fadeIn('slow');
			
$('#cart-total').html(json['total']);
jQuery('#cart-total').click();	


 
$('html, body').animate({ scrollTop: 0 }, 'slow'); 
}	
}
});

}
</script>

<div id="bgclr">
    <?php echo $column_left; ?><?php echo $column_right; ?>
    <div id="content"><?php echo $content_top; ?>
        <div class="breadcrumb">
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
            <?php } ?>
        </div>
        <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
        <?php if ($thumb || $description) { ?>
        <div class="category-info">
            <?php if ($thumb) { ?>
            <div class="image"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></div>
            <?php } ?>
            <?php if ($description) { ?>
            <?php echo $description; ?>
            <?php } ?>
        </div>
        <?php } ?>

        <?php if ($products) { ?>
        <div class="product-filter">
            <div class="display"><b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display('grid');"><?php echo $text_grid; ?></a></div>
            <div class="limit"><b><?php echo $text_limit; ?></b>
                <select onchange="location = this.value;">
                    <?php foreach ($limits as $limits) { ?>
                    <?php if ($limits['value'] == $limit) { ?>
                    <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
            </div>
            <div class="sort"><b><?php echo $text_sort; ?></b>
                <select onchange="location = this.value;">
                    <?php foreach ($sorts as $sorts) { ?>
                    <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
                    <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="product-compare"><a href="<?php echo $compare; ?>" id="compare_total"><?php echo $text_compare; ?></a></div>
        <div id="dialog-form" title="PARAGON TAKEAWAY">

        </div>

        <div class="product-grid">
            <?php
            $fl=1;
             $j=1;
             $l=0;
            ?>
           
            <?php foreach ($products as $product) {
            $options=$this->model_catalog_product->getProductOptions($product['product_id']);
           $opt_list='product_optionlist'.$j;
            $productcart='product-carts'.$j;
            $rela_list='related'.$j;
            
            ?>
            <div>
                <?php if ($product['thumb']) { ?>
                <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
                <?php } ?>
                <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
                <div id="price_range"><?php //echo $product['pricerange'] ?></div>
                <div class="description"><?php echo $product['description']; ?></div>
                <?php if ($product['price']!='£0.00') {

                ?>
                <div class="price">
                    <?php if (!$product['special']) { ?>
                    <?php echo $product['price']; ?>
                    <?php } else { ?>
                    <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
                    <?php } ?>
                    <?php if ($product['tax']) { ?>
                    <br />
                    <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                    <?php } ?>
                </div>
                <?php } ?>
                <?php if ($product['rating']) { ?>
                <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
                <?php } 

                ?>
                
                <div class="cart">

                   <a id="shoppingcart_button" class="<?php echo $fl ?>" href="<?php echo $product['href']; ?>"><span>More Details</span></a>
                 
                    <?php 

                    $product_id=$product['product_id'];
                    $flag=0;
                    foreach($products_options as $prd_options){
                    if($prd_options['product_id']==$product['product_id']){
                    $flag=1;

                    }
                    }
                    if($flag!=1){
                    ?>
                    <a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><span><?php echo $button_cart; ?></span></a>
                    <?php
                    }
                    ?>
                </div>
                <div class="wishlist"></div>
                <div class="compare"></div>
                <div id="login_box">
                   
                    <div id="login_box_content">
                       
                    </div>
                </div>
                <div id="related-prd" class="<?php echo $rela_list ?>" style="display: none">
                    <div id="related-popup">
                    <h2>Related Products</h2>
                    <?php
                    $results = $this->model_catalog_product->getProductRelated($product_id);
                    foreach ($results as $result) {
                     $l++;
                    $rela_lists='relateds'.$l;
                    $prd_id=$result['product_id'];
                   
                    ?>
                    <div id="related_details">
                    <span id="prd_opt_name"><?php echo $result['name']; ?></span>
                    <div class="image"><img src="<?php echo 'http://www.delichez.com.php53-23.ord1-1.websitetestlink.com/image/'.$result['image']; ?>" height="100px" width="164px" title="<?php echo $result['name']; ?>" alt="<?php echo $result['name']; ?>" /></div>
                     <div class="price">
                         Price:£
                          <?php echo $result['price']; ?>
                     </div>
                     <?php
                    foreach ($this->model_catalog_product->getProductOptions($prd_id) as $optionlist) { 
                   
                    ?>
                    <div id="related-prds" class="<?php echo $rela_lists ?>">
                       
                    <?php
                   
                    if ($optionlist['type'] == 'select') { ?>
        <div id="option-<?php echo $optionlist['product_option_id']; ?>" class="option">
          <?php if ($optionlist['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $optionlist['name']; ?>:</b><br />
          <select name="option[<?php echo $optionlist['product_option_id']; ?>]">
            <option value=""><?php echo $text_select; ?></option>
            <?php foreach ($optionlist['option_value'] as $option_values) { ?>
            <option value="<?php echo $option_values['product_option_value_id']; ?>"><?php echo $option_values['name']; ?>
            <?php if ($option_values['price']!='0.00') { ?>
            (<?php echo $option_values['price']; ?>)
            <?php } ?>
            </option>
            <?php } ?>
          </select>
        </div>
        <br />
        <?php } ?>
        <?php if ($optionlist['type'] == 'radio') { ?>
        <div id="option-<?php echo $optionlist['product_option_id']; ?>" class="option">
          <?php if ($optionlist['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $optionlist['name']; ?>:</b><br />
          <?php foreach ($optionlist['option_value'] as $option_values) { ?>
          <input type="radio" name="option[<?php echo $optionlist['product_option_id']; ?>]" value="<?php echo $option_values['product_option_value_id']; ?>" id="option-value-<?php echo $option_values['product_option_value_id']; ?>" />
          <label for="option-value-<?php echo $option_values['product_option_value_id']; ?>"><?php echo $option_values['name']; ?>
            <?php if ($option_values['price']!='0.00') { ?>
            (<?php echo $option_values['price']; ?>)
            <?php } ?>
          </label>
          <br />
          <?php } ?>
        </div>
        <br />
        <?php } ?>
        <?php if ($optionlist['type'] == 'checkbox') { ?>
        <div id="option-<?php echo $optionlist['product_option_id']; ?>" class="option">
          <?php if ($optionlist['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $optionlist['name']; ?>:</b><br />
          <?php foreach ($optionlist['option_value'] as $option_values) { ?>
          <input type="checkbox" name="option[<?php echo $optionlist['product_option_id']; ?>][]" value="<?php echo $option_values['product_option_value_id']; ?>" id="option-value-<?php echo $option_values['product_option_value_id']; ?>" />
          <label for="option-value-<?php echo $option_values['product_option_value_id']; ?>"><?php echo $option_values['name']; ?>
            <?php if ($option_values['price']!='0.00') { ?>
            (<?php echo $option_values['price']; ?>)
            <?php } ?>
          </label>
          <br />
          <?php } ?>
        </div>
        <br />
        <?php } ?>
        <?php if ($optionlist['type'] == 'image') { ?>
        <div id="option-<?php echo $optionlist['product_option_id']; ?>" class="option">
          <?php if ($optionlist['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $optionlist['name']; ?>:</b><br />
            <table class="option-image">
              <?php foreach ($optionlist['option_value'] as $option_values) { ?>
              <tr>
                <td style="width: 1px;"><input type="radio" name="option[<?php echo $optionlist['product_option_id']; ?>]" value="<?php echo $option_values['product_option_value_id']; ?>" id="option-value-<?php echo $option_values['product_option_value_id']; ?>" /></td>
                <td><label for="option-value-<?php echo $option_values['product_option_value_id']; ?>"><img src="<?php echo $option_values['image']; ?>" alt="<?php echo $option_values['name'] . ($option_values['price'] ? ' ' . $option_values['price_prefix'] . $option_values['price'] : ''); ?>" /></label></td>
                <td><label for="option-value-<?php echo $option_values['product_option_value_id']; ?>"><?php echo $option_values['name']; ?>
                    <?php if ($option_values['price']) { ?>
                    (<?php echo $option_values['price_prefix']; ?><?php echo $option_values['price']; ?>)
                    <?php } ?>
                  </label></td>
              </tr>
              <?php } ?>
            </table>
        </div>
        <br />
        <?php } ?>
        <?php if ($optionlist['type'] == 'text') { ?>
        <div id="option-<?php echo $optionlist['product_option_id']; ?>" class="option">
          <?php if ($optionlist['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $optionlist['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $optionlist['product_option_id']; ?>]" value="<?php echo $optionlist['option_value']; ?>" />
        </div>
        <br />
        <?php } ?>
        <?php if ($optionlist['type'] == 'textarea') { ?>
        <div id="option-<?php echo $optionlist['product_option_id']; ?>" class="option">
          <?php if ($optionlist['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $optionlist['name']; ?>:</b><br />
          <textarea name="option[<?php echo $optionlist['product_option_id']; ?>]" cols="40" rows="5"><?php echo $optionlist['option_value']; ?></textarea>
        </div>
        <br />
        
        <?php
        
        } ?>
        <div>
        
Qty:  
        <input type="text" name="quantity" size="2" value="1" />
          <input type="hidden" name="product_id" size="2" value="<?php echo $prd_id; ?>" />
          &nbsp;<a id="button-cart"  onclick="addToCartProducts('<?php echo $l ?>');" class="<?php echo $l ?>"><span><?php echo $button_cart; ?></span></a></div>
           </span>
        </div>
        
           
                    <?php
                    }
                    ?>
                    </div>
                    <?php
                    }
                    ?>
                    
                    <div style="clear:both"></div><a class="close_button" onclick="closepopup();">x</a>
                </div>
                    
                </div>
            </div>
           
           <?php if ($options) { ?>
           <div class="options" id="<?php echo $opt_list ?>" style="display: none">
                <span class="<?php echo $productcart ?>" id="product-carts">
        <h2><?php echo $text_option; ?></h2>
        <br />
        <?php 
       
        foreach ($options as $option) { 
       
       
        ?>
        <?php if ($option['type'] == 'select') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <select name="option[<?php echo $option['product_option_id']; ?>]">
            <option value=""><?php echo $text_select; ?></option>
            <?php foreach ($option['option_value'] as $option_value) { ?>
            <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']!='0.00') { ?>
            (<?php echo $option_value['price']; ?>)
            <?php } ?>
            </option>
            <?php } ?>
          </select>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'radio') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']!='0.00') { ?>
            (<?php echo $option_value['price']; ?>)
            <?php } ?>
          </label>
          <br />
          <?php } ?>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'checkbox') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']!='0.00') { ?>
            (<?php echo $option_value['price']; ?>)
            <?php } ?>
          </label>
          <br />
          <?php } ?>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'image') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
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
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'textarea') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5"><?php echo $option['option_value']; ?></textarea>
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'file') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <a id="button-option-<?php echo $option['product_option_id']; ?>" class="button"><span><?php echo $button_upload; ?></span></a>
          <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'date') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="date" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'datetime') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="datetime" />
        </div>
        <br />
        <?php } ?>
        <?php if ($option['type'] == 'time') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="time" />
        </div>
        <br />
        <?php } ?>
        <?php } ?>
        <div>
        
Qty:  
        <input type="text" name="quantity" size="2" value="1" />
          <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
          &nbsp;<a id="button-cart"  onclick="addToCartProduct('<?php echo $j ?>');" class="<?php echo $j ?>"><span><?php echo $button_cart; ?></span></a></div>
           </span>
      </div>
          
      <?php } ?>
             
            <?php 

            if($flag==1){
            $i=0;
            $product_option_id=array();
            $option_id=array();
            $option_value_id=array();
            $product_option_value_id=array();
            $price=array();
            $name=array();

            foreach($products_options as $options){

            $product_option_id[$i]=$options['product_option_id'];
            $option_id[$i]=$options['option_id'];
            $option_value_id[$i]=$options['option_value_id'];
            $product_option_value_id[$i]=$options['product_option_value_id'];
            $price[$i]=$options['price'];
            $name[$i]=$options['name'];
            $i++;
            }

            $n=$i;
            $id[0]=$option_id[0];
            $flag1=0;
            $flag2=0;
            $k=1;
            for($i=0;$i<$n;$i++){
            if($id[0]==$option_id[$i]){
            $flag1++;
            }else{
            $flag2++;
            }
            }
           
            $prod_modal_list="prod_modal_list".$fl;
            ?>
            <span id="<?php echo $prod_modal_list ?>" style="display: none">
                <ul id='prod_list'>
                    <?php
                    foreach($products_options as $options1){

                    if($options1['product_id']==$product['product_id']){
                    echo "<li>";

                        echo '<div id="opt-name">'.$options1['name'].'</div>';
                        $price1=$options1['price'];
                        echo '<div id="opt-price">'.'£'.$price1.'</div>';
                        $product_option_id1=$options1['product_option_id'];
                        $option_id1=$options1['option_id'];
                        $option_value_id1=$options1['option_value_id'];
                        $product_option_value_id1=$options1['product_option_value_id'];

                        $prd_option_id11=$product_option_value_id1;
                        $optionvalue12='option-value-'.$prd_option_id11;
                        $optionname11='option['.$product_option_id1.']';
                        $optionvalue11='option-value-'.$prd_option_id11;
                        $productcart='product-cart'.$k;
                        ?>
                        <span class="<?php echo $productcart ?>" id="product-cart">
                            <input type="hidden" name="quantity" size="2" value="1" />
                            <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
                            <input type="hidden" id="<?php echo $optionvalue11 ?>" value="<?php echo  $prd_option_id11 ?>" name="<?php echo $optionname11 ?>" >
                            <a id="button-cart" onclick="addToCartProduct('<?php echo $k ?>');" class="<?php echo $k ?>"><span><?php echo $button_cart; ?></span></a>
                        </span>

                        <?php
                        echo "</li>";
                    }
                    $k++;
                    }
                    }
                    echo "</ul>";
                echo "</span>";
            $fl++;
            $j++;
            
           
            } ?>
            
        </div>
        
        <div class="pagination"><?php echo $pagination; ?></div>
        <?php } ?>
        <?php if (!$categories && !$products) { ?>
        <div class="content"><?php echo $text_empty; ?></div>
        <div class="buttons">
            <div class="right"><a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_continue; ?></span></a></div>
        </div>
        <?php } ?>
        <?php echo $content_bottom; ?></div>

    <script type="text/javascript"><!--
function display(view) {
if (view == 'list') {
$('.product-grid').attr('class', 'product-grid');
		
$('.product-grid > div').each(function(index, element) {
	
			
html += '<div class="left">';
			
var image = $(element).find('.image').html();
			
if (image != null) { 
html += '<div class="image">' + image + '</div>';
}
			
var price = $(element).find('.price').html();
			
if (price != null) {
html += '<div class="price">' + price  + '</div>';
}
					
html += '  <div class="name">' + $(element).find('.name').html() + '</div>';
html += '  <div class="description">' + $(element).find('.description').html() + '</div>';
			
var rating = $(element).find('.rating').html();
			
if (rating != null) {
html += '<div class="rating">' + rating + '</div>';
}
				
html += '</div>';

html  = '<div class="right">';
html += '  <div class="cart">' + $(element).find('.cart').html() + '</div>';
html += '  <div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
html += '  <div class="compare">' + $(element).find('.compare').html() + '</div>';
html += '</div>';		

						
$(element).html(html);
});		
		
$('.display').html('<b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');
		
$.cookie('display', 'list'); 
} else {
$('.product-grid').attr('class', 'product-grid');
		
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
html += '<div class="price">' + price  + '</div>';
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
    <div style="clear:both;"></div>
</div>
<?php echo $footer; ?>