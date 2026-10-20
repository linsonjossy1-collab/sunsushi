$(document).ready(function() {
	/* Search */
	$('.button-search').bind('click', function() {
		url = $('base').attr('href') + 'index.php?route=product/search';
				 
		var filter_name = $('input[name=\'filter_name\']').attr('value');
		
		if (filter_name) {
			url += '&filter_name=' + encodeURIComponent(filter_name);
		}
		
		location = url;
	});
	
	$('#header input[name=\'filter_name\']').bind('keydown', function(e) {
		if (e.keyCode == 13) {
			url = $('base').attr('href') + 'index.php?route=product/search';
			 
			var filter_name = $('input[name=\'filter_name\']').attr('value');
			
			if (filter_name) {
				url += '&filter_name=' + encodeURIComponent(filter_name);
			}
			
			location = url;
		}
	});
	
	/* Ajax Cart */
	$('#cart > .heading a').on('click', function() {
		$('#cart').addClass('active');
		
		$('#cart').load('index.php?route=module/cart #cart > *');
		
		$('#cart').on('mouseleave', function() {
			$(this).removeClass('active');
		});
	});
	$('#mobile_cart_btn').on('click', function() {
            $('#mobile_cart_btn').removeClass('mobview');
            $('html, body').animate({ scrollTop: $("#side_cart_open").offset().top }, 500);
        });
	/* Mega Menu */
	$('#menu ul > li > a + div').each(function(index, element) {
		// IE6 & IE7 Fixes
        /*
        if ($.browser.msie && ($.browser.version == 7 || $.browser.version == 6)) {
			var category = $(element).find('a');
			var columns = $(element).find('ul').length;
			
			$(element).css('width', (columns * 143) + 'px');
			$(element).find('ul').css('float', 'left');
        }	
        */	
		
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();
		
		i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());
		
		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 5) + 'px');
		}
	});

    // IE6 & IE7 Fixes
    /*
	if ($.browser.msie) {
		if ($.browser.version <= 6) {
			$('#column-left + #column-right + #content, #column-left + #content').css('margin-left', '195px');
			
			$('#column-right + #content').css('margin-right', '195px');
		
			$('.box-category ul li a.active + ul').css('display', 'block');	
		}
		
		if ($.browser.version <= 7) {
			$('#menu > ul > li').bind('mouseover', function() {
				$(this).addClass('active');
			});
				
			$('#menu > ul > li').bind('mouseout', function() {
				$(this).removeClass('active');
			});	
		}
	}
	*/
	$('.success img, .warning img, .attention img, .information img').on('click', function() {
		$(this).parent().fadeOut('slow', function() {
			$(this).remove();
		});
	});	
        
         /* Deal Suggested with options Cart Add*/
        /* Ajax Cart */
	$('.deal-to-cart-plus').on('click', function() {
		$('#cart').addClass('active');
		
		$('#cart').load('index.php?route=module/cart #cart > *');
		
		$('#cart').on('mouseleave', function() {
			$(this).removeClass('active');
		});
	});
        
         $('.right-cart-smart-deals').bind('dialogclose', function(event) {
                 $('.smart-deal-parent-div').html('');
                 //alert("closed");
        });
});

function getURLVar(urlVarName) {
	var urlHalves = String(document.location).toLowerCase().split('?');
	var urlVarValue = '';
	
	if (urlHalves[1]) {
		var urlVars = urlHalves[1].split('&');

		for (var i = 0; i <= (urlVars.length); i++) {
			if (urlVars[i]) {
				var urlVarPair = urlVars[i].split('=');
				
				if (urlVarPair[0] && urlVarPair[0] == urlVarName.toLowerCase()) {
					urlVarValue = urlVarPair[1];
				}
			}
		}
	}
	
	return urlVarValue;
} 

function addToCart(product_id, quantity) {
	quantity = typeof(quantity) != 'undefined' ? quantity : 1;

	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: 'product_id=' + product_id + '&quantity=' + quantity,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#cart-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
	});
}
function addToWishList(product_id) {
	$.ajax({
		url: 'index.php?route=account/wishlist/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information').remove();
						
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#wishlist-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow');
			}	
		}
	});
}

function addToCompare(product_id) { 
	$.ajax({
		url: 'index.php?route=product/compare/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information').remove();
						
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#compare-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
	});
}

function etmAddtocart(div_id){
    $.ajax({
            url: 'index.php?route=checkout/cart/add',
            type: 'post',
            data: $('#' + div_id + ' input[type=\'text\'], #' + div_id + ' input[type=\'hidden\'], #' + div_id + ' input[type=\'radio\']:checked, #' + div_id + ' input[type=\'checkbox\']:checked, #' + div_id +  '  select, #' + div_id +  ' textarea , .pr_options_' + div_id + ' input[type=\'text\'], .pr_options_' + div_id + ' input[type=\'hidden\'], .pr_options_' + div_id + ' input[type=\'radio\']:checked, .pr_options_' + div_id + ' input[type=\'checkbox\']:checked, .pr_options_' + div_id + '  select, .pr_options_' + div_id + ' textarea'),
            dataType: 'json',
            success: function(json) {
                $('.success, .warning, .attention, information, .error').remove();
                if (json['error']) {
                    if (json['error']['outofstock']) {  
                        $('.modal').modal('hide');
                        $('#notification').html('<div class="warning" style="font-weight: bold;background: red;color: white;padding: 13px;">' + json['error']['outofstock'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                        $('.warning').delay(5000).fadeOut();
                    }
                    if (json['error']['option']) {
                        for (i in json['error']['option']) {
                             $('#'+div_id+  '  #option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                             $('#'+div_id+  '  #optioncus-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');                             
                        }
                        $('#'+div_id+  ' .panel-heading').removeClass('collapsed');
                        $('#'+div_id+  ' .collapse').removeClass('in');
                        $('#'+div_id+  ' .panel-heading').addClass('collapsed');
                        $('#'+div_id+  ' .optionpanel .panel-heading').removeClass('collapsed');
                        $('#'+div_id+  ' .optionpanel .collapse').addClass('in');
                        $('#'+div_id+  ' .optionpanel .collapse.in').css('min-height',250);
                    }
                    if (json['error']['minimum']) {
                        for(k in json['error']['minimum']){
                            $('#'+div_id+  '  #option-' + k).after('<span class="error">' + json['error']['minimum'][k] + '</span>');
                        }                         
                    }
                }

                if (json['success']) {
                   
                    $('.modal').modal('hide');
                    
                    $('#notification').html('<div class="success">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				    $('.success').delay(2000).fadeOut();
				
                     //itemcount
                    $('.count').html(json['itemcount']);    
                    $('.productdatas-'+json['product_id']+' .addtocartnb').hide();
                    $('.productdatas-'+json['product_id']+' .equiliserpropage').show();
                    if(isNaN(parseInt($('.productdatas-'+json['product_id']+' .quantity').html()))){
                        $('.productdatas-'+json['product_id']+' .quantity').html('1');
                    }else{
                        $('.productdatas-'+json['product_id']+' .quantity').html(parseInt($('.productdatas-'+json['product_id']+' .quantity').html())+1);
                   
                    }
                    $('#cart').load('index.php?route=module/cart #cart > *',function(){
                        $('#mobile_cart_btn').addClass('mobview');
                        requestedtime_check();
                        //smartoffers(); 
                    });
                    $('.productdatas-'+json['product_id']+' .remove').html('<div class="fa fa-minus-circle" title="-1" onclick="removeditem('+"'"+json['iky']+"'"+',,'+$('.productdatas-'+json['product_id']+' .quantity').html()+','+json['product_id']+');"></div>');
                    window.dataLayer.push({
                        event: "add_to_cart",
                        ecommerce: {
                          currency: "GBP",
                          value: parseFloat(json['item_price']) * 1,
                          items: [
                            {
                              item_id: json['item_id'],
                              item_name: json['item_name'],
                              affiliation: json['store_name'] || '',
                              item_category: json['item_category'] || '',
                              quantity: 1,
                              price: parseFloat(json['item_price'])
                            }
                          ]
                        }
                    });
                     $('.btnnothanks').val('Checkout');
                }
            }
        });
    
}

function dealsuggestionproductspopup(product_id,bundle_id){
    $('.right-cart-smart-deals').dialog('close');
    $('.deal-suggested-product-popup').load("index.php?route=product/product/ajaxdealsuggestedproducts",{product_id: product_id,bundle_id:bundle_id},function(){
        $('.deal-suggested-product-popup').dialog({
            autoOpen: true,
            height: 'auto',
            width: '70%',
            resizable: false,
            modal: true
        });
    });
    
}

function suggesteddealadd(div_id){
    
    $.ajax({
            url: 'index.php?route=checkout/cart/add',
            type: 'post',
            data: $('#' + div_id + ' input[type=\'text\'], #' + div_id + ' input[type=\'hidden\'], #' + div_id + ' input[type=\'radio\']:checked, #' + div_id + ' input[type=\'checkbox\']:checked, #' + div_id +  '  select, #' + div_id +  ' textarea , .pr_options_' + div_id + ' input[type=\'text\'], .pr_options_' + div_id + ' input[type=\'hidden\'], .pr_options_' + div_id + ' input[type=\'radio\']:checked, .pr_options_' + div_id + ' input[type=\'checkbox\']:checked, .pr_options_' + div_id + '  select, .pr_options_' + div_id + ' textarea'),
            dataType: 'json',
            success: function(json) {
                $('.success, .warning, .attention, information, .error').remove();
                if (json['error']) {
                    if (json['error']['option']) {
                        for (i in json['error']['option']) {
                             $('#'+div_id+  '  #option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                             $('#'+div_id+  '  #optioncus-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                             
                        }
                    }
                }

                if (json['success']) {
                    $.ajax({
                        url: 'index.php?route=module/smartoffers/applysmartoffer',
                        type: 'post',
                        dataType: 'json',
                        success: function(json) {
                          //$('.right-cart-smart-deals').dialog("close");
                          //$('.right-cart-smart-deals').remove();
                          $('#cart').load('index.php?route=module/cart #cart > *',function(){
                                 $('#' + div_id).dialog("close");
                                 $('.etm-popup-prodts').dialog("close");
                                  smartoffers();
                            /*     $('.right-cart-smart-deals').dialog({
                                 autoOpen: true,
                                 height: 'auto',
                                 width: 'auto',
                                 modal: true,
                                 close: function() {
                                    $('.right-cart-smart-deals').remove();
                                     //alert('+close');
                                 }
                                });*/
                        });
                        }
                    });     
                }
            }
        });
    
}

function dealdiscardclick(product_id){
   $('#cart').load('index.php?route=module/cart&deal_discarded_id='+product_id+'#cart > *',function(){
                         $('.right-cart-smart-deals').dialog("close");
                    }); 
   /* $.ajax({
                url: 'index.php?route=module/cart/dsiscarddeal',
                type: 'post',
                data:{product_id:product_id} ,
                dataType: 'json',
                success: function(json) {
                    $('.right-cart-smart-deals').dialog("close");
                }
            });*/
}

function dealapprove(main_product_id,bundle_product_id,popup){
    /* $('#cart').load('index.php?route=module/cart&deal_product_id='+product_id+'#cart > *',function(){
                         $('.right-cart-smart-deals').dialog("close");
                    }); */
    $.ajax({
                url: 'index.php?route=module/smartoffers/adddealproduct',
                type: 'post',
                data:{product_id:main_product_id} ,
                dataType: 'json',
                success: function(json) {
                       if ((typeof popup == 'undefined') && (typeof bundle_product_id == 'undefined')) {
                                 //alert("SSS"); 
                                 $.ajax({
                                  url: 'index.php?route=module/smartoffers/applysmartoffer',
                                  type: 'post',
                                  dataType: 'json',
                                  success: function(json) {
                                     //$('.right-cart-smart-deals').remove();
                                     $('#cart').load('index.php?route=module/cart #cart > *',function(){
                                        /*                                     
                                        $('.right-cart-smart-deals').dialog({
                                         autoOpen: true,
                                         height: 'auto',
                                         width: 'auto',
                                         modal: true,
                                         close: function() {
                                          $('.right-cart-smart-deals').remove();
                                         }
                                        });
                                        */
                                        smartoffers(); 
                                     });
                           
                            
                                  }
                                  });
                                 
                            } else if (typeof popup == 'undefined') {//option popup
                               dealoptionpopup(bundle_product_id);
                            } else {
                               suggesteddealadd(popup)
                            }
                       
                   
                    
                    
                }
            });
}

function dealoptionpopup(product_id,parent_id,bundle_id){
     //$('.right-cart-smart-deals').dialog('close');
    $('#smart-suggested-product-options-'+product_id).load("index.php?route=product/product/dealsuggestedproductoptions",{product_id: product_id,parent_id:parent_id,bundle_id:bundle_id},function(){
        $('#smart-suggested-product-options-'+product_id).dialog({
            autoOpen: true,
            height: 'auto',
            width: '70%',
            resizable: false,
            modal: true,
            close: function() {
                $('.right-cart-smart-deals').remove();
            }


        });
    })
    
}

function smartoffers(){
      $('.smart-deals-parent-div').dialog("close");
      $('.smart-deals-parent-div').html('');
      $('.smart-deals-parent-div').load("index.php?route=module/smartoffers",function(html){
      //alert("SSS");
      if(html){
            $('.smart-deals-parent-div').dialog({
             autoOpen: true,
             height: 'auto',
             width: 'auto',
             resizable: false,
             modal: true,
             close: function() {
              //$('.smart-deals-parent-div').remove();
              $('.smart-deals-parent-div').html('');
             }
          });
      }
    
    })
}
    
function showfreeproductoptions(id) {  
    $('#freeproductoptions').load("index.php?route=product/product/ajaxfreeproductoptions",{prod_id: id},function(response,statusTxt,xhr){
    if(statusTxt=="success") {
        $('#free_products_popup').modal('hide'); 
        $('#freeproductoptions').modal('show'); 
        /*
        $('#free_dialog_'+ id).dialog({
            autoOpen: true,
            height: 'auto',
            width: '50%',
            modal: true,
        });
        */
    }
    });
}
function addFreeToCart(product_id, quantity) {
	quantity = typeof(quantity) != 'undefined' ? quantity : 1;

	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: 'product_id=' + product_id + '&quantity=' + quantity,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information, .error').remove();
			
			if (json['success']) {
                             window.location = 'index.php?route=checkout/checkoutetm';
                             return;
				$.ajax({
					url: 'index.php?route=checkout/confirmetm/finalcart',
					dataType: 'html',
					success: function(html) {
						$('#confirm .checkout-content').html(html);
                                               
					},
					error: function(xhr, ajaxOptions, thrownError) {
						//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}	
		}
	});
}

function addFreeOptionsToCart(div_id){
    $.ajax({
            url: 'index.php?route=checkout/cart/add',
            type: 'post',
            data: $('#' + div_id + ' input[type=\'text\'], #' + div_id + ' input[type=\'hidden\'], #' + div_id + ' input[type=\'radio\']:checked, #' + div_id + ' input[type=\'checkbox\']:checked, #' + div_id +  '  select, #' + div_id +  ' textarea , .pr_options_' + div_id + ' input[type=\'text\'], .pr_options_' + div_id + ' input[type=\'hidden\'], .pr_options_' + div_id + ' input[type=\'radio\']:checked, .pr_options_' + div_id + ' input[type=\'checkbox\']:checked, .pr_options_' + div_id + '  select, .pr_options_' + div_id + ' textarea'),
            dataType: 'json',
            success: function(json) {
                $('.success, .warning, .attention, information, .error').remove();
                if (json['error']) {
                    if (json['error']['option']) {
                        for (i in json['error']['option']) {
                             $('#'+div_id+  '  #option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                             $('#'+div_id+  '  #optioncus-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                             
                        }
                    }
                }

                if(json['success']) {
                    window.location = 'index.php?route=checkout/checkoutetm';
                   $.ajax({
                        url: 'index.php?route=checkout/confirmetm/finalcart',
                        dataType: 'html',
                        success: function(html) {
                                $('#confirm .checkout-content').html(html);
                                $('.etm-popup-free-options').dialog('close');
                                $('.free_products_popup').dialog('close');
                                
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                               // alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            }
    });
}

function addupsellingOptionsToCart(div_id){
    $.ajax({
            url: 'cart-add',
            type: 'post',
            data: $('#' + div_id + ' input[type=\'text\'], #' + div_id + ' input[type=\'hidden\'], #' + div_id + ' input[type=\'radio\']:checked, #' + div_id + ' input[type=\'checkbox\']:checked, #' + div_id +  '  select, #' + div_id +  ' textarea , .pr_options_' + div_id + ' input[type=\'text\'], .pr_options_' + div_id + ' input[type=\'hidden\'], .pr_options_' + div_id + ' input[type=\'radio\']:checked, .pr_options_' + div_id + ' input[type=\'checkbox\']:checked, .pr_options_' + div_id + '  select, .pr_options_' + div_id + ' textarea'),
            dataType: 'json',
            success: function(json) {
                $('.success, .warning, .attention, information, .error').remove();
                if (json['error']) {
                    if (json['error']['option']) {
                        for (i in json['error']['option']) {
                             $('#'+div_id+  '  #option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                             $('#'+div_id+  '  #optioncus-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                             
                        }
                    }
                }

                if(json['success']) {
                   // window.location = 'checkoutetm';
                   $('.btnnothanks').val('Checkout');
                   $('.modal').modal('hide');
                    $('#notification').html('<div class="success">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
                    $('.success').delay(2000).fadeOut();
                   $.ajax({
                        url: 'finalcart',
                        dataType: 'html',
                        success: function(html) {
                                $('#confirm .checkout-content').html(html);
                                $('.modal').modal('hide');
                                
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                               // alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            }
    });
}
function productminuspopup(key,quantity){
    if(quantity==1){
       $('#cart_modal_yes_button').attr('key',key);
       $('#cart_modal_yes_button').attr('qty',quantity);
       $("#product-minus-popup").modal('show');
    }else{
      $('#cart').load('index.php?route=module/cart&minus1='+key+'&qty='+quantity + ' #cart > *');
    }
  
  }
 
  $(document).ready(function() {
    $('#cart_modal_yes_button').bind('click', function() {
        $('#cart').load('index.php?route=module/cart&minus1='+$('#cart_modal_yes_button').attr('key')+'&qty='+$('#cart_modal_yes_button').attr('qty') + ' #cart > *');
        $("#product-minus-popup").modal('hide');
    });
  });