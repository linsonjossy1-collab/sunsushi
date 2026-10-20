<script type="text/javascript">
$(document).ready(function(){
<?php if(!empty($relatedproducts)) { ?>
var title='<?php echo $relatedproducts[key($relatedproducts)]['name'];?>';
$('#relatedProducts_popup').dialog({
autoOpen: true,
            height: 'auto',
            width: 'auto',
            modal: false,
            title: ''+title+' ?'


        }    
);
<?php } ?>
$('.button_cancel').click(function() {
        //alert("test");
        var daialog_id = $(this).attr('prod_id');
                //alert(daialog_id);

$('#dialog_' + daialog_id).dialog("close");  
        });
                
$('.button-cart').bind('click', function() {
  prod_id = $(this).attr('prod_id');
        
        $.ajax({
            url: 'index.php?route=checkout/cart/add',
            type: 'post',
            data: $('.product-det_' + prod_id + ' input[type=\'text\'], .product-det_' + prod_id + ' input[type=\'hidden\'], .product-det_' + prod_id + ' input[type=\'radio\']:checked, .product-det_' + prod_id + ' input[type=\'checkbox\']:checked, .product-det_' + prod_id + '  select, .product-det_' + prod_id + ' textarea'),
            dataType: 'json',
            success: function(json) {
                $('.success, .warning, .attention, information, .error').remove();
                if (json['error']) {
                    if (json['error']['option']) {
                        for (i in json['error']['option']) {
                            $('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                        }
                    }
                }

                if (json['success']) {
               // $('#relatedProducts_popup').dialog('close');
                $.ajax({
					url: 'index.php?route=checkout/shipping_methodetm/requestedtime',
                                        type: 'post',
                                        data: $('#shipping-method input[type=\'radio\']:checked'),
					dataType: 'html',
					success: function(html) {
						//$('#shipping-method #requested_time').html(html);

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
               $.ajax({
					url: 'index.php?route=checkout/shipping_methodetm',
                                        type: 'post',
                                        data: $('#shipping-method input[type=\'radio\']:checked'),
					dataType: 'html',
					success: function(html) {
						$('#shipping-method #shipping_methodes').html(html);

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
              
				$.ajax({
					url: 'index.php?route=checkout/payment_methodetm',
					dataType: 'html',
					success: function(html) {
						$('#payment-method .checkout-content').html(html);

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
                 $.ajax({
					url: 'index.php?route=checkout/confirmetm/finalcart',
					dataType: 'html',
					success: function(html) {
						$('#confirm .checkout-content').html(html);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
                    $('#cart').load('index.php?route=module/cart #cart > *');
                    //$('html, body').animate({scrollTop: 255}, 'slow');
                }
            }
        });
});    

$('.button-cart_wop').bind('click', function() {
   prod_id = $(this).attr('id');
 
        $.ajax({
            url: 'index.php?route=checkout/cart/add',
            type: 'post',
            data: $('#product-info_' + prod_id + ' input[type=\'text\'], #product-info_' + prod_id + ' input[type=\'hidden\'], #product-info_' + prod_id + ' input[type=\'radio\']:checked, #product-info_' + prod_id + ' input[type=\'checkbox\']:checked, #product-info_' + prod_id + ' select, #product-info_' + prod_id + ' textarea'),
            dataType: 'json',
            success: function(json) {
                $('.success, .warning, .attention, information, .error').remove();
                if (json['error']) {
                    if (json['error']['option']) {
                        for (i in json['error']['option']) {
                            $('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                        }
                    }
                }

                if (json['success']) {
                  // $('#cart').load('index.php?route=module/cart #cart > *');
                   $('#discount_offer_list').load('index.php?route=module/freeproducts #discount_offer_list > *'); 
                   //$('html, body').animate({scrollTop: 255}, 'slow');
                   //$('#relatedProducts_popup').dialog('close');
                    $.ajax({
					url: 'index.php?route=checkout/shipping_methodetm/requestedtime',
                                        type: 'post',
                                        data: $('#shipping-method input[type=\'radio\']:checked'),
					dataType: 'html',
					success: function(html) {
						//$('#shipping-method #requested_time').html(html);

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
                   $.ajax({
					url: 'index.php?route=checkout/shipping_methodetm',
                                        type: 'post',
                                        data: $('#shipping-method input[type=\'radio\']:checked'),
					dataType: 'html',
					success: function(html) {
						$('#shipping-method #shipping_methodes').html(html);

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
                  
				$.ajax({
					url: 'index.php?route=checkout/payment_methodetm',
					dataType: 'html',
					success: function(html) {
						$('#payment-method .checkout-content').html(html);

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
                   $.ajax({
					url: 'index.php?route=checkout/confirmetm/finalcart',
					dataType: 'html',
					success: function(html) {
						$('#confirm .checkout-content').html(html);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
                }
            }
        });
});

$('.popupbuttonn').bind('click', function() {

var id=$(this).attr('prod_id');
   $('#dialog_' + id).load("index.php?route=product/product/ajaxoptions",{prod_id: id},function(response,statusTxt,xhr){
    //alert(data);
    if(statusTxt=="success") {
    
       //$('#relatedProducts_popup').dialog('close');    
        $('#dialog_'+ id).dialog({
            autoOpen: true,
            height: 'auto',
            width: 'auto',
            modal: false,
            title: $('.dialog_'+ id).attr('title')


        });
        }
    if(statusTxt=="error")    
       alert("Failed");
    })
});
});

</script>
<div id="relatedProducts_popup" style="display: none">
<div  class="prod_main_categories" >
            
            <?php if(!empty($relatedproducts)) { ?>
            <?php $i=0; ?>
            <?php foreach($relatedproducts as $all_products) {
            $i++;
            ?>
            <div class="product-info_simple <?php echo(($i%2)==0?'even':'odd'); ?> <?php echo(($all_products['product_options'])?'':'without_opt');?>" id="product-info_<?php echo $all_products['product_id']; ?>" >

                <div class="product_name">
                    <?php echo $all_products['name']; ?> 

                </div>
                <div class="product_description">
                    
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
                }
                ?>   

            </div>







            <?php } ?>
            <?php } ?>
        </div>
    <input type="button" class="button" onclick="$('#relatedProducts_popup').dialog('close');"  value="No,THANKS">
    </div>