var url = $('base').attr('href');
var operators = ['+', '-', 'x', '�'];
var decimalAdded = false;
//var map;
//var gdir;
var geocoder = null;
var addressMarker;
var red_url='';
var xhr;

/*  Maps */
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var map;

$(function() {
   
    $(document).on('click','.playsound',function(){
         play_single_sound();
    });
    $(document).on('click','#button-confirm',function(){
        $('#mask').addClass('mask');
        open_cashdrawer();
         $.ajax({
            url: 'index.php?route=epos/print/epos&order_id='+$('#confirm_print').attr('order_id')+'&custprint=confirm',
            dataType: 'html',
            success: function(data) {
               //$('#printepos').show();
               $('#printepos').html(data);
               $('#printpdf').html(url+"index.php?route=epos/print/pdf&order_id="+$('#confirm_print').attr('order_id'));
               $('#printhtml').html(url+"index.php?route=epos/print/html&order_id="+$('#confirm_print').attr('order_id'));
               $('#printtext').html(url+"index.php?route=epos/print/text&order_id="+$('#confirm_print').attr('order_id'));
               print_order(); 
               //showmainpage();
               $.ajax({
                    url: 'index.php?route=epos/epos/updateprintedorders&order_id='+$('#confirm_print').attr('order_id'),
                    dataType: 'json',
                    success: function(json) {
                        $.ajax({ 
                                type: "post",
                                data:$('#epos_payment_complete input[type=\'hidden\'],'),
                                url: url+'index.php?route=epos/success',	
                                dataType: 'json',
                                success: function(data) {
                                     showmainpage();
                                }
                        });
                    }    
                }); 
            }
        });
       // print_order();
        //ajax load print
       
    });
    $(document).on('click','#split_page_confirm',function(){
         $('#mask').addClass('mask');
         $.ajax({
            url: 'index.php?route=epos/confirm/savesplits&confirm=1',
            type: 'post',
            data: {split:splitteddata},
            dataType: 'json',
            success: function(json) {
                if(split_confirm_print!==0){
                   $.ajax({
                     url: 'index.php?route=epos/print/epos&order_id='+order_data['order_id']+'&custprint=confirm',
                     dataType: 'html',
                     success: function(data) {
                        //$('#printepos').show();
                        $('#printepos').html(data);
                        $('#printpdf').html(url+"index.php?route=epos/print/pdf&order_id="+order_data['order_id']);
                        $('#printhtml').html(url+"index.php?route=epos/print/html&order_id="+order_data['order_id']);
                        $('#printtext').html(url+"index.php?route=epos/print/text&order_id="+order_data['order_id']);
                        print_order(); 
                        showmainpage();
                        $.ajax({
                             url: 'index.php?route=epos/epos/updateprintedorders&order_id='+order_data['order_id'],
                             dataType: 'json',
                             success: function(json) {
                             
                             }    
                         }); 
                       
                     }
                });
                }else{
                   showmainpage();
                }
            }
        });
    });
   // $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
    $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
    $(document).on('click','#cash_drawer_top_btn',function(){
        play_single_sound();
        $.colorbox.close();
        if(!$(this).hasClass('hder-inactive')){
           open_cashdrawer();
        }
    });
    $(document).on('click','.deal-to-cart-plus', function() {
		$('#cart').addClass('active');
		play_single_sound();
		$('#cart').load('index.php?route=module/cart #cart > *', function() {
                    cartresize();
                });		
		$('#cart').live('mouseleave', function() {
			$(this).removeClass('active');
		});
	});
    $(document).on('dialogclose','.right-cart-smart-deals', function(event) {
            $('.smart-deal-parent-div').html('');
            
    });
    $(document).on('click','#header_links', function() {
           play_single_sound();
            if ($("#header-popup").is(":hidden")) {
               $.colorbox({ fixed:true, inline: true, width :"35%",height:"auto", top: $('#header').height(),right:0 ,opacity:0.1, href: "#header_btns",
                onLoad: function() {
                   $('#cboxClose').remove();
                   $('#colorbox').addClass('headerlink_popup'); 
                },
               onCleanup:function() {
                  $('#colorbox').removeClass('headerlink_popup'); 
               }
           });
            }else{                
                $.colorbox.close();
            }
        });
    $(document).on('click','.update-disp-settings', function() {
             play_single_sound();
             $.ajax({
                  url: 'index.php?route=epos/system/updatedisplaysettings&category_count=' + $('#cat-display-count').val() +'&prodt_count='+$('#prodt-display-count').val(),
                  dataType: 'json',
                  success: function(json) {
                     if (json['success']) {
                         $.colorbox.close();
                     }
                 }
             });
         });
    $(document).on('click','.update-table-settings', function() {
            play_single_sound();
             $.ajax({
                  url: 'index.php?route=epos/system/updatetablesettings&table_height=' + $('#table-display-height').val() +'&table_width='+$('#table-display-width').val(),
                  dataType: 'json',
                  success: function(json) {
                     if (json['success']) {
                         $.colorbox.close();
                     }
                 }
             });
         });
    $(document).on('click','.dine-tbl-chng', function() {
             $('#colorbox').removeClass('headerlink_popup'); 
            play_single_sound();
              $('#change-table-div').load("index.php?route=epos/liveorders/ajaxstoretables",function(){
                 $.colorbox({ inline: true,height:parseInt($(window).height()-$('#header').height()-$('#footer').height()-10), width: "80%", opacity: 0.5, href: '#change-table-div', closeButton: true,overlayClose:false,escKey:false,
                    onCleanup: function(){
                        $('#change-table-div').html('');
                    }}); 
          })
        }); 
    $(document).on('click','.pos-change-table', function() {
            play_single_sound();
            prev_table_id = $('.pos-change-table.currenttble').attr('table_id');
            prev_live_id =  $('.pos-change-table.currenttble').attr('live_order_id');
            dinedid = $(this).attr('table_id');
            html = '';
            //alert(prev_table_id);
            if($(this).hasClass('dineinediting')){
               return false;
            }
            else if($(this).hasClass('dineined')){
               html +='<a class="table-swap-btn" onclick="swapdineintables('+prev_table_id+','+prev_live_id+','+dinedid+','+$(this).attr('live_order_id')+');">SWAP</a>';
               html +='<a class="table-merge-btn" onclick="mergedineintables('+prev_table_id+','+prev_live_id+','+dinedid+','+$(this).attr('live_order_id')+');">MERGE</a>';
               $('.table-seln-msgs').html(html);
            }
            else if($(this).hasClass('currenttble')){
              $('.table-seln-msgs').html('');
              $.colorbox.close();
            }
            else{
                $.ajax({    url: 'index.php?route=epos/epos/changedineinedtable&table_id='+dinedid,
                 dataType: 'json',
                 success: function(json) {
                     // location = 'index.php?route=epos/main';
                     if (json['success']) {
                         $('.table-seln-msgs').html('');
                         $.colorbox.close();
                         $('#mask').addClass('mask');
                          window.ajaxstatus = '0';
                         $('#eposmaincontent').load('index.php?route=epos/epos',function(){
                                cartresize();
                                $('#mask').removeClass('mask');
                         });
                       
                     }
                 }
             });
            }
        });
    $(document).on('click','.unloc_tble_yes', function(){
           play_single_sound();
            $.ajax({
                    url: 'index.php?route=epos/epos/unlocktable',
                    type:'post',
                    dataType: 'json',
                    data: { live_order_id:$(this).attr('live_order_id')},
                    success: function(json) {
                       if (json['success']) {
                          getliveorders();
                          $.colorbox.close();
                       }
                 }
             });
        });

var productcach = [];
$(document).on("click",'.top-product-search',function(){
    $('.product_search_top').show();
    $('#product_search').click();
});
if($.virtualkeyboard!=0){  
     $(document).on("click","#product_search",function(){
     play_single_sound();
    // jsKeyboard.init("virtualKeyboard");
    showKeyboard('product_search',function(){
         if (xhr != null) {
            xhr.abort();
        }
        if($("#product_search").val() in productcach){
            json = productcach[$("#product_search").val()];
            productsearchresult(json);
        }
        xhr = $.ajax({
            url: 'index.php?route=epos/epos/jsonautocomplete&filter_name=' + $("#product_search").val(),
            dataType: 'json',
            success: function(json) {
                productcach[$("#product_search").val()] = json;
                productsearchresult(json);
                // $('#p1').html(html);
            }
        });
     });
});
     $(document).on("click","#ordr-commnts",function(){
	 play_single_sound();
	 //jsKeyboard.init("virtualKeyboard");	
         showKeyboard('ordr-commnts'); 
});
     $(document).on("click","#mainpagecommon",function(){
         play_single_sound() ;
         //jsKeyboard.init("virtualKeyboard");
         showKeyboard('mainpagecommon',function(){
              searcheposcustomer('common',$('#mainpagecommon').val());
         });
     });
     $(document).on("click","#mainpagecname",function(){
         play_single_sound() ;
        // jsKeyboard.init("virtualKeyboard");
         showKeyboard('mainpagecname',function(){
              searcheposcustomer('name',$('#mainpagecname').val());
         });
     });
     $(document).on("click","#mainpagetelephone",function(){
         play_single_sound() ;
         //jsKeyboard.init("virtualKeyboard");
         showKeyboard('mainpagetelephone',function(){
                searcheposcustomer('phone',$('#mainpagetelephone').val());
         });
     });
     $(document).on("click","#mainpagestore_card_no",function(){
         play_single_sound() ;
         //jsKeyboard.init("virtualKeyboard");
         showKeyboard('mainpagestore_card_no',function(){
                searcheposcustomer('store_card_no',$('#mainpagestore_card_no').val());
         });
     });   
     $(document).on("click","#change_customer_search",function(){
         play_single_sound() ;
        // jsKeyboard.init("virtualKeyboard");
         showKeyboard('change_customer_search',function(){
              searcheposcustomer('common',$('#change_customer_search').val()); 
         });
     });
}//else{
$(document).on("change","#product_search",function(){
         if (xhr != null) {
            xhr.abort();
        }
        if($("#product_search").val() in productcach){
            json = productcach[$("#product_search").val()];
            productsearchresult(json);
        }
        xhr = $.ajax({
            url: 'index.php?route=epos/epos/jsonautocomplete&filter_name=' + $("#product_search").val(),
            dataType: 'json',
            success: function(json) {
                 productcach[$("#product_search").val()] = json;
                 productsearchresult(json);
                // $('#p1').html(html);
            }
        });
    
});
$(document).on('change keyup paste','#mainpagecommon',function() {
            searcheposcustomer('common',$('#mainpagecommon').val());
      });
$(document).on('change keyup paste','#mainpagecname',function() {
        searcheposcustomer('name',$('#mainpagecname').val());
     });
$(document).on('change keyup paste','#mainpagetelephone',function() {
           searcheposcustomer('phone',$('#mainpagetelephone').val());
     });
$(document).on('change keyup paste','#mainpagestore_card_no', function() {
        searcheposcustomer('store_card_no',$('#mainpagestore_card_no').val());
      }); 
$(document).on('change keyup paste','#change_customer_search', function() {
        searcheposcustomer('common',$('#change_customer_search').val());
});       
$('.js-calc').live('click', function() {
           play_single_sound();
          $('#colorbox').removeClass('headerlink_popup'); 
          $('#calc-show').load("index.php?route=epos/epos/jscalculator",function(){
               //setInterval(function () { 
                  $.colorbox({ inline: true,opacity: 0,left:'0px', top: $('#header').height(), href: '#calc-show', closeButton: false, overlayClose:false,escKey:false,
                    onLoad: function() {
                      $('#cboxClose').remove();
                      $('#colorbox').addClass('calcltr-popup'); 
                    },
                    onCleanup: function(){
                        $('#calc-show').html('');
                        $('#colorbox').removeClass('calcltr-popup'); 
                    }
                   });
               //}, 1000);  
          });
        });     
$('#calculator span').live('click',function(e) {
		// Get the input and button values
                play_single_sound();
                if($(this).hasClass('close')){
                    return false;
                }
            	var input = document.querySelector('.screen');
		var inputVal = input.innerHTML;
		//var btnVal = this.innerHTML;
		var btnVal = this.innerHTML;
		// Now, just append the key values (btnValue) to the input string and finally use javascript's eval function to get the result
		// If clear key is pressed, erase everything
                //alert(btnVal);
		if(btnVal == 'C') {
			input.innerHTML = '';
			decimalAdded = false;
		}
		
		// If eval key is pressed, calculate and display the result
		else if(btnVal == '=') {
			var equation = inputVal;
			var lastChar = equation[equation.length - 1];
			//alert(equation);
			// Replace all instances of x and � with * and / respectively. This can be done easily using regex and the 'g' tag which will replace all instances of the matched character/substring
			equation = equation.replace(/x/g, '*').replace(/�/g, '/');
			
			// Final thing left to do is checking the last character of the equation. If it's an operator or a decimal, remove it
			if(operators.indexOf(lastChar) > -1 || lastChar == '.')
				equation = equation.replace(/.$/, '');
			
			if(equation)
				input.innerHTML = eval(equation);
				
			decimalAdded = false;
		}
		else if(operators.indexOf(btnVal) > -1) {
			// Operator is clicked
			// Get the last character from the equation
			var lastChar = inputVal[inputVal.length - 1];
			
			// Only add operator if input is not empty and there is no operator at the last
			if(inputVal != '' && operators.indexOf(lastChar) == -1) 
				input.innerHTML += btnVal;
			
			// Allow minus if the string is empty
			else if(inputVal == '' && btnVal == '-') 
				input.innerHTML += btnVal;
			
			// Replace the last operator (if exists) with the newly pressed operator
			if(operators.indexOf(lastChar) > -1 && inputVal.length > 1) {
				// Here, '.' matches any character while $ denotes the end of string, so anything (will be an operator in this case) at the end of string will get replaced by new operator
				input.innerHTML = inputVal.replace(/.$/, btnVal);
			}
			
			decimalAdded =false;
		}
		
		// Now only the decimal problem is left. We can solve it easily using a flag 'decimalAdded' which we'll set once the decimal is added and prevent more decimals to be added once it's set. It will be reset when an operator, eval or clear key is pressed.
		else if(btnVal == '.') {
			if(!decimalAdded) {
				input.innerHTML += btnVal;
				decimalAdded = true;
			}
		}
		
		// if any other key is pressed, just append it
		else {
			input.innerHTML += btnVal;
		}
		
		// prevent page jumps
		e.preventDefault();
	} ) ; 
$(document).on('click','#qck_chkbtn_add',function(){
    play_single_sound();
    $('#name-error').html('');
    $('#colorbox').removeClass('headerlink_popup'); 
    $.colorbox({ inline: true,overlayClose:false,escKey:false, opacity: 0.5, href: "#quick_btn_add_div"});
});
$(document).on('click','#quick_btn_continue',function(){
    if($('#quick_chk_btn_name').val()==''){
        $('#name-error').html('Name is not valid');
        $.colorbox.resize();
        return false;
    }
    if($('#quick_chk_btn_name').val().length>20){
        $('#name-error').html('Max Name Length is 20 ');
        $.colorbox.resize();
        return false;
    }
    $.colorbox.close();
    $.ajax({
         url: 'index.php?route=epos/quick_checkout/addnewquickbutton',
         type:'post',
         dataType: 'json',
         data:{ name:$('#quick_chk_btn_name').val() },
         success: function(json) {
             if(json['success']){
                 //showmainpage();
                  window.ajaxstatus = '1';
                 $('#mask').addClass('mask');
                 $('#eposmaincontent').load('index.php?route=epos/epos/main',function(){
                    $('#mask').removeClass('mask');
                 });
             }
         }
    }); 
});
$(document).on('on','.qck_chk_sel', function() {
    if($(this).hasClass('disabled')){
        return true;    
    } 
    $.ajax({
               url: 'index.php?route=epos/quick_checkout/editquickbuttondata',
               type: 'post',
               dataType: 'json',
               data:{ quick_btn_id: $(this).attr('quick_btn_id') } ,
               success: function(json) {
                   if(json['success']){
                       $('#mask').addClass('mask');
                        window.ajaxstatus = '0';
                       $('#eposmaincontent').load('index.php?route=epos/epos',function(){
                           cartresize();
                           $('#mask').removeClass('mask');
                       });
                       //location = url + 'index.php?route=epos/epos';
                   }
               }
    });
});
$(document).on('click','.qck_chk_delete',function(){
    $.ajax({
         url: 'index.php?route=epos/quick_checkout/deletequichcheckoutbutton',
         type:'post',
         dataType: 'json',
         data:{ buttonid:$(this).attr('quick_btn_id') },
         success: function(json) {
             if(json['success']){
                 $('#mask').addClass('mask');
                $('#eposmaincontent').load('index.php?route=epos/quick_checkout',function(){
                    $('#mask').removeClass('mask');
                });
                //location = url + 'index.php?route=epos/quick_checkout';
             }
         }
    }); 
}); 
$(document).on('click','#quick_checkout_save',function() {
         play_single_sound();
          $.ajax({
                     url: 'index.php?route=epos/quick_checkout/checksettings',
                     //type: 'post',
                     dataType: 'json',
                     success: function(json) {
                       if(json['success']){
                          if($('.paymentmethod-cell.PaymentSeleted input[type=\'hidden\']').val()=='cash'){
                              $.colorbox({ inline: true,overlayClose:false,escKey:false, opacity: 0.5, href: "#quick_checkout_skip_popup"});
                          }else{
                                $.ajax({
                                    url: 'index.php?route=epos/quick_checkout/updatedetails&skip=0',
                                   //type: 'post',
                                    dataType: 'json',
                                    success: function(json) {
                                          if(json['success']){
                                              $('#mask').addClass('mask');
                                              $('#eposmaincontent').load('index.php?route=epos/quick_checkout',function(){
                                                  $('#mask').removeClass('mask');
                                              });
                                          }
                                    }
                                }); 
                          }
                            
                       }else{
                           if(json['error_shipping']){
                               $('.shippingmethod-cell').removeClass('ShippingSeleted');
                            }    
                            if(json['error_payment']){
                                $('.paymentmethod-cell').removeClass('PaymentSeleted');
                            }  
                           $('.common-notfcn').html(json['error']);
                       }    
                     }
                }); 
       //});
    });
$(document).on('click','.quck_save', function() {
         if($(this).hasClass('skip_confirm')){
             skipval=1;
         }else{
             skipval=0;
         }    
         $.ajax({
                     url: 'index.php?route=epos/quick_checkout/updatedetails&skip='+skipval,
                     //type: 'post',
                     dataType: 'json',
                     success: function(json) {
                           if(json['success']){
                               $('#mask').addClass('mask');
                               $('#eposmaincontent').load('index.php?route=epos/quick_checkout',function(){
                                   $('#mask').removeClass('mask');
                               });
                           }
                          
                     }
         }); 
     });
$(document).on('click','.main_quick_btn', function() { 
   $.ajax({
        url: 'index.php?route=epos/quick_checkout/quickbuttonorder&quick_btn='+$(this).attr('quick_btn_id'),
        dataType: 'json',
        success: function(json) {
            if(json['success']){
               if(json['success']=='main'){
                   showmainpage();
               }else if(json['success']=='confirm'){
                   $('#mask').addClass('mask');
                    clearpreviouspayments();
                    $('#eposmaincontent').load('index.php?route=epos/confirm',function(){
                        $('#mask').removeClass('mask');
                    });
               }
            }
        }
    }); 
});   
$(document).on('click','.category-cell', function() {
    play_single_sound();
    // clear product and option
    $('#c1 li').removeClass('cat_selected');
    $(this).addClass('cat_selected');
    $('#p1').html('');
    $("#product-epos-popupbox").html('');
    $("#product-epos-productoptions").html('');
    data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
    //alert($(data).val())
    cat_id=$(this).attr('cat_id') ;
    products_load(cat_id);//
    categories(cat_id);

});
$(document).on('click','.product-cell', function() {
    play_single_sound();
    productcellid = $(this).attr('id');
    $("#product-epos-popupbox").html('');
    $("#product-epos-productoptions").html('');
    data = '#' + productcellid + ' input[name=\'product_id\'] ';
    $('#tab-product-epos input[name=\'product_id\']').attr('value', $(data).val());
    if ($('#' + productcellid + ' input[name=\'option\'] ').val() == 0) {
        addtocart_epos();
        return;
    }
    product_options($(data).val());
});
$(document).on('click','.single-product-option', function() {
    play_single_sound();
    product_option_id = $(this).attr('parentid');
    nonrequiredoptionremoveonly = false;
    if (!$('#option-' + product_option_id).hasClass('requiredoption')) {
        if ($(this).hasClass('OptionSeleted')) {
            nonrequiredoptionremoveonly = true;
        }
    }
    $('#option-' + product_option_id + ' .single-product-option').removeClass('OptionSeleted');
    if (!nonrequiredoptionremoveonly) {
        $(this).addClass('OptionSeleted');
    }
});
$(document).on('click','.multi-product-option', function() {
    play_single_sound();
    if ($(this).hasClass('MultiOptionSeleted')) {
        $(this).removeClass('MultiOptionSeleted');
    }
    else {
        $(this).addClass('MultiOptionSeleted');
    }

    product_option_id = $(this).attr('parentid');
});
$(document).on('click','.bundled-single-product', function() {
    play_single_sound();
    product_option_id = $(this).attr('div_id');
    nonrequiredoptionremoveonly = false;
    if (!$('#bundled_div_' + product_option_id).hasClass('requiredoption')) {
        if ($(this).hasClass('OptionSeleted')) {
            nonrequiredoptionremoveonly = true;
        }
    }
    $('#bundled_div_' + product_option_id + ' .bundled-single-product').removeClass('OptionSeleted');
    if (!nonrequiredoptionremoveonly) {
        $(this).addClass('OptionSeleted');
    }
    var div_id = $(this).attr('bund_id');
    bundprodtcellid = $(this).attr('id');
    $('#bundled_product_options_' + div_id).html('');
    arr = div_id.split('_')
    data = '#' + bundprodtcellid + ' input[type=\'hidden\'] ';
    bunldechk = '#' + bundprodtcellid + ' input[name=\'chkbundledoptions\'] ';
    if ($(bunldechk).val() == 0) {
        $('#bundled_product_options_' + div_id).html('');
        return false;
    }
    $.ajax({
        url: 'index.php?route=epos/epos/jsonbundledproductoptions&product_id=' + $(data).val(), //+ '&ind=' + $(this).attr('div_id')
        dataType: 'json',
        success: function(json) {
            html = '';
            tot_bun_opt_count = 0;
             bun_opt_header_height=20;
            bun_opt_height=60;
            bun_cal_height = 0;
            bun_pop_page_count = 1;
            colorbox_pos = getpopuppositions();
            opt_max_height =colorbox_pos.height-100;// $('.bund_product_required').height()-90;//540-100;// use colorbox_pos.height//(popup height )
            // $('#bundled_product_options_' + div_id).html('');
            if (json[0]['option'] != '') {
                bun_total_option_count = json[0]['option'].length;
                for (i in json[0]['option']) {
                    bun_prodt_option = json[0]['option'][i];
                    tot_bun_opt_count +=1;
                    
                    option_per_item = Math.ceil(bun_prodt_option['option_value'].length/bunprodoption_dispaly_items);
                    bun_cal_height += (option_per_item * bun_opt_height) + bun_opt_header_height + (option_per_item+1)*3 ; 
                    if((bun_cal_height+70)>=opt_max_height){
                        html +='<div class="main_options bun_opt_pop_page_'+product_option_id+'_'+bun_pop_page_count+' epos-bunpropt-btns">';
                      if(bun_pop_page_count>1){
                          html +='<div class="left"><a onclick="showbundpreviousoptionpage('+product_option_id+','+bun_pop_page_count+')" class="button popsmall_btn playsound"><i class="fa fa-caret-square-o-left"></i>PREVIOUS</a></div>';
                      } 
                       html +='<div class="right"><a onclick="showbundnextoptionpage('+product_option_id+','+bun_pop_page_count+')" class="button popsmall_btn playsound"><i class="fa fa-caret-square-o-right"></i>NEXT</a></div>';
                       bun_pop_page_count +=1;
                       bun_cal_height  = (option_per_item * bun_opt_height) + bun_opt_header_height + (option_per_item+1)*3  ;
                      html +='</div>'; 
                     } 
                    if (bun_prodt_option['type'] == 'select' || bun_prodt_option['type'] == 'radio' || bun_prodt_option['type'] == 'checkbox') {
                        required = '';
                        requiredhtml = '';
                        if (bun_prodt_option['required'] == 1) {
                            requiredhtml += '<span class="required"><i class="fa fa-star"></i></span> ';
                            required = 'bun_product_requiredoption';
                        }
                        html += '<div id="bundled-product-option-' + bun_prodt_option['product_option_id'] + '" class="main_options ' + required + ' bun_opt_pop_page_'+product_option_id+'_'+bun_pop_page_count+'">';
                        html += '<span class="optionname">' + requiredhtml + bun_prodt_option['name'] + '</span>';
                        html += '<ul class="Po1 " id="option[' + bun_prodt_option['product_option_id'] + ']">';
                        bun_opt_row = 1 ;
                        for (j = 0; j < bun_prodt_option['option_value'].length; j++) {
                            option_value = bun_prodt_option['option_value'][j];
                            if (bun_prodt_option['type'] == 'checkbox') {
                                html += '<li id="' + arr[1] + '_' + product_option_id + '_' + i + '_' + j + '" class="bundled-multi-product-option prbunoptionrow_'+product_option_id+'_'+tot_bun_opt_count+'_'+bun_opt_row+' prbunoptiorows" parentid="' + bun_prodt_option['product_option_id'] + '" ProductOptionvalueID="' + option_value['product_option_value_id'] + '">' + option_value['name'];
                                html += '<input type="hidden" name="bundledoption[' + json[0]['bund_id'] + '_' + json[0]['indkey'] + '_' + json[0]['product_id'] + '][' + bun_prodt_option['product_option_id'] + '][]" value="' + option_value['product_option_value_id'] + '" />';
                            }
                            else {
                                html += '<li id="' + arr[1] + '_' + product_option_id + '_' + i + '_' + j + '"  class="bundled-single-product-option prbunoptionrow_'+product_option_id+'_'+tot_bun_opt_count+'_'+bun_opt_row+' prbunoptiorows" parentid="' + bun_prodt_option['product_option_id'] + '" ProductOptionvalueID="' + option_value['product_option_value_id'] + '">' + option_value['name'];
                                html += '<input type="hidden" name="bundledoption[' + json[0]['bund_id'] + '_' + json[0]['indkey'] + '_' + json[0]['product_id'] + '][' + bun_prodt_option['product_option_id'] + ']" value="' + option_value['product_option_value_id'] + '" />';
                            }
                            if (option_value['price']) {
                                html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
                            }

                            html += '</li>';
                        }

                        html += '</ul>';
                        html += '</div>';
                    }
                    if(bun_pop_page_count>1 && tot_bun_opt_count==bun_total_option_count){
                        html +='<div class="main_options bun_opt_pop_page_'+product_option_id+'_'+bun_pop_page_count+' epos-bunpropt-btns"><div class="left"><a onclick="showbundpreviousoptionpage('+product_option_id+','+bun_pop_page_count+')" class="button popsmall_btn playsound"><i class="fa fa-caret-square-o-left"></i>PREVIOUS</a></div></div>';
                    }
                }
                bunpagehtml ='';
                if(bun_pop_page_count>1){
                  bunpagehtml +='<div class="bunoptn-pge-disp">';
                  for(p=1;p<=bun_pop_page_count;p++){
                    bunpagehtml +='<span id="bunoptnpgeitem-'+product_option_id+ '_'+p+'" class="bunoptn-pge-item"><i class="fa fa-file-o"></i>'+p+'<input type="hidden" name="optionpgetabno" value="'+product_option_id+ '_'+p+'"></span>';
                  }
                  bunpagehtml +='</div>';
                 }
                $('#bundled_product_options_' + div_id).html(bunpagehtml+html);
                
                $('.main_options').hide();
                $('#bunoptnpgeitem-'+product_option_id+ '_1').addClass('selected');
                for(r=1;r<=tot_bun_opt_count;r++){
                   // $('.prbunoptionrow_'+product_option_id+ '_'+r+'_1').show();
                     $('.bun_opt_pop_page_'+product_option_id+ '_1').show();
                }
            }
            else {
                $('#bundled_product_options_' + div_id).html('');
            }
        }
    });
});
$(document).on('click','.bundled-single-product-option', function() {
    play_single_sound();
    product_option_id = $(this).attr('parentid');
    nonrequiredoptionremoveonly = false;
    if (!$('#bundled-product-option-' + product_option_id).hasClass('bun_product_requiredoption')) {
        if ($(this).hasClass('OptionSeleted')) {
            nonrequiredoptionremoveonly = true;
        }
    }
    $('#bundled-product-option-' + product_option_id + ' .bundled-single-product-option').removeClass('OptionSeleted');
    if (!nonrequiredoptionremoveonly) {
        $(this).addClass('OptionSeleted');
    }
});
$(document).on('click','.bundled-multi-product-option', function() {
    play_single_sound();
    if ($(this).hasClass('MultiOptionSeleted')) {
        $(this).removeClass('MultiOptionSeleted');
    }
    else {
        $(this).addClass('MultiOptionSeleted');
    }
    product_option_id = $(this).attr('parentid');
});
$(document).bind('cbox_cleanup', function() {
    $('#product-epos-popupbox').html('');
});
$(document).on('click','.cart-itemrow', function(e) {
    $('.cart-itemrow').removeClass('cartitem_selected');
    $(this).addClass('cartitem_selected');
    play_single_sound();
    cartitemid=$(this).attr('cartitem');
    divid = '#cart-qty-div-' + cartitemid;
    $('.cart_additional_btns').hide();
    $('.cart_buttons_'+cartitemid).show();
});
$(document).on('click','.cart_qty_plus', function() {
    play_single_sound();
    qty = parseInt($('.cartitem_selected  input[type=\'hidden\']').val());
    $('#cart').load('index.php?route=epos/cart&update=' + $(this).attr('cartitemkey') + '&qty=' + (qty+1) + ' #cart > *',
                   function(){
                       cartresize();
                       $('.cartitem_selected').focus();
                       $('.paymentmethod-cell').removeClass('notallowed');
                       $('.shippingmethod-cell').removeClass('notallowed');
                   }  
    );
});
$(document).on('click','.cart_qty_minus', function() {
    play_single_sound();
    selcartkey=$(this).attr('cartitemkey');
    curr_qty = parseInt($('.cartitem_selected  input[type=\'hidden\']').val());
    qty = curr_qty-1;
    //    if(previous_trackitems.length>0){
//        console.log(selcartkey);
//        console.log(previous_trackitems);
//        alert(qty+'--'+previous_trackitems[selcartkey]);
        if(typeof(previous_trackitems[selcartkey])!="undefined" && qty<previous_trackitems[selcartkey]){
            $.colorbox({ html: "<h3>You Are Not Allowed to Modify this item ,Because This order is already splitted </h3><div><a class=\"button ftr_permsn_no popbtns playsound\" onclick=\"$.colorbox.close();\"><i class=\"fa fa-times\"></i>OK</a> </div>", width: "30%", opacity: 0.5 ,overlayClose:false,escKey:false,
                           onLoad: function() {
                             $('#cboxClose').remove();
                             $('#colorbox').addClass('removecart_popup'); 
                           },
                           onCleanup:function() {
                               $('#colorbox').removeClass('removecart_popup'); 
                           }
            });
            return false; 
        }
    //}
    if (isNaN(qty) || qty == 0) {
                $.colorbox({ html: "<h3>Remove product ?</h3><div><a class=\"button ftr_permsn_yes popbtns playsound\" onclick=\"$('#cart').load('index.php?route=epos/cart&update=" + selcartkey + "&qty=" + qty + " #cart > *',function(){ $('.mini-cart-info tbody').height('"+ getcartheght()+"px');$('.mini-cart-info tbody').scrollTop($('.mini-cart-info tbody')[0].scrollHeight);$('.paymentmethod-cell').removeClass('notallowed');$('.shippingmethod-cell').removeClass('notallowed');}); $.colorbox.close();\"><i class=\"fa fa-check\"></i>Yes</a> <a class=\"button ftr_permsn_no popbtns playsound\" onclick=\"$.colorbox.close();\"><i class=\"fa fa-times\"></i>No</a> </div>", width: "30%", opacity: 0.5 ,overlayClose:false,escKey:false,
                           onLoad: function() {
                             $('#cboxClose').remove();
                             $('#colorbox').addClass('removecart_popup'); 
                           },
                           onCleanup:function() {
                               $('#colorbox').removeClass('removecart_popup'); 
                           }
                          });
            }
            else {
                $('#cart').load('index.php?route=epos/cart&update=' + selcartkey + '&qty=' + qty + ' #cart > *',
                   function(){
                       cartresize();
                       $('.cartitem_selected').focus();
                       $('.paymentmethod-cell').removeClass('notallowed');
                       $('.shippingmethod-cell').removeClass('notallowed');
                   }  
              );
            }
});
$(document).on('click','.citem-remove', function() {
      selcartkey = $(this).attr('cartitemkey');
    if(typeof(previous_trackitems[selcartkey])!="undefined"){
           $.colorbox({ html: "<h3>You Are Not Allowed to Modify this item ,Because This order is already splitted </h3><div><a class=\"button ftr_permsn_no popbtns playsound\" onclick=\"$.colorbox.close();\"><i class=\"fa fa-times\"></i>OK</a> </div>", width: "30%", opacity: 0.5 ,overlayClose:false,escKey:false,
                           onLoad: function() {
                             $('#cboxClose').remove();
                             $('#colorbox').addClass('removecart_popup'); 
                           },
                           onCleanup:function() {
                               $('#colorbox').removeClass('removecart_popup'); 
                           }
                          });
            return false;
    }
    $.colorbox({ html: "<h3>Remove product ?</h3><div><a class=\"button ftr_permsn_yes popbtns playsound\" onclick=\"$('#cart').load('index.php?route=epos/cart&update=" + selcartkey + "&qty=0 #cart > *',function(){ $('.mini-cart-info tbody').height('"+ getcartheght()+"px');$('.mini-cart-info tbody').scrollTop($('.mini-cart-info tbody')[0].scrollHeight);$('.paymentmethod-cell').removeClass('notallowed');$('.shippingmethod-cell').removeClass('notallowed');}); $.colorbox.close();\"><i class=\"fa fa-check\"></i>Yes</a> <a class=\"button ftr_permsn_no popbtns playsound\" onclick=\"$.colorbox.close();\"><i class=\"fa fa-times\"></i>No</a> </div>", width: "30%", opacity: 0.5 ,overlayClose:false,escKey:false,
                           onLoad: function() {
                             $('#cboxClose').remove();
                             $('#colorbox').addClass('removecart_popup'); 
                           },
                           onCleanup:function() {
                               $('#colorbox').removeClass('removecart_popup'); 
                           }
                          });
});
$(document).on('click','.cartitemadttions', function() {
    play_single_sound();
    $.colorbox.close();
});
$(document).on('click','.paymentmethod-cell', function() {
    play_single_sound();
    $('.common-notfcn').html('');
    if($(this).hasClass('notallowed') || $(this).hasClass('ship_pay_empty')){
        return false;
    }
    $('.paymentmethod-cell').removeClass('PySeleted');
    $(this).addClass('PySeleted');
    $.ajax({
        url: 'index.php?route=epos/payment_method/validate',
        type: 'post',
        data: $('.paymentmethod-cell.PySeleted input[type=\'hidden\'],  textarea'),
        dataType: 'json',
        beforeSend: function() {
            $('#button-payment-method').attr('disabled', true);
            $('#button-payment-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
        },
        complete: function() {
            $('#button-payment-method').attr('disabled', false);
            $('.wait').remove();
        },
        success: function(json) {
            $('.warning, .error').remove();
            
            if (json['redirect']) {
                $('.common-notfcn').html($('.paymentmethod-cell.PySeleted .paymentmethod-name').html() +' is Not Allowed');
            } else if (json['error']) {
                if (json['error']['warning']) {
                    $('.common-notfcn').html(json['error']['warning']);
                }
            } else {
                $('.paymentmethod-cell').removeClass('PaymentSeleted');
                $('.paymentmethod-cell.PySeleted').addClass('PaymentSeleted');
                $('.paymentmethod-cell.PaymentSeleted').removeClass('PySeleted');
                $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() {
                    cartresize();
                });
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
$(document).on('click','.shippingmethod-cell', function() {
    play_single_sound();
    $('.common-notfcn').html('');
    if($(this).hasClass('notallowed') || $(this).hasClass('ship_pay_empty')){
        return false;
    }
    $('.shippingmethod-cell').removeClass('ShSeleted');
    $(this).addClass('ShSeleted');
    $.ajax({
        url: 'index.php?route=epos/shipping_method/validate',
        type: 'post',
        data: $('.shippingmethod-cell.ShSeleted input[type=\'hidden\'], .order-comments textarea'),
        dataType: 'json',
        beforeSend: function() {
            $('#button-shipping-method').attr('disabled', true);
            $('#button-shipping-method').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
        },
        complete: function() {
            $('#button-shipping-method').attr('disabled', false);
            $('.wait').remove();
        },
        success: function(json) {
            $('.warning, .error').remove();
            if (json['redirect']) {
                $('.common-notfcn').html($('.shippingmethod-cell.ShSeleted .shippingmethod-name').html() +' is Not Allowed');
            } else if (json['error']) {
                if (json['error']['warning']) {
                     $('.common-notfcn').html(json['error']['warning']);
                }
            } else {
                $('.shippingmethod-cell').removeClass('ShippingSeleted');
                $('.shippingmethod-cell .shipping-error').html('');
                $('.shippingmethod-cell.ShSeleted').addClass('ShippingSeleted');
                $.ajax({
					url: 'index.php?route=epos/shipping_method/requestedtime',
                                        type: 'post',
                                        data: $('.shippingmethod-cell.ShSeleted input[type=\'hidden\'], #shipping-method textarea'),
					dataType: 'html',
					success: function(html) {
						$('#requested_time').html(html);
                                                 $('.shippingmethod-cell.ShippingSeleted').removeClass('ShSeleted');
                                                $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() {
                                                        cartresize();
                                                });
                                                

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				}); 
                
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
$(document).on('change','#order_requested_time', function() {
    play_single_sound();
    $.ajax({
        url: 'index.php?route=epos/shipping_method/setrequestedtime',
        type: 'post',
        data: $('#order_requested_time'),
        dataType: 'json',
        success: function(json) {
          
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
$(document).on('click','#button-find', function() {
    play_single_sound();
    $.ajax({
        url: 'index.php?route=epos/login_customer/validate',
        type: 'post',
        data: $('#cutomer-login :input'),
        dataType: 'json',
        beforeSend: function() {
            $('#button-login').attr('disabled', true);
            $('#button-login').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
        },
        complete: function() {
            $('#button-login').attr('disabled', false);
            $('.wait').remove();
        },
        success: function(json) {
            $('.warning, .error').remove();
            if (json['error']) {
                $('#cutomer-login .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');

                $('.warning').fadeIn('slow');
            }
            else {
                $('#mask').addClass('mask');
                $('#eposmaincontent').load('index.php?route=epos/epos',function(){
                    $('#mask').removeClass('mask');
                    cartresize();
                });
                $.colorbox.close();
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
$(document).on('click','#address-list li', function() {
    play_single_sound();
    addr_div = $(this).attr('id');
    add_post_code=$(this).attr('postcode');
    $.ajax({
        url: 'index.php?route=epos/payment_address/validate&payment_address=existing',
        type: 'post',
        data: $('#' + $(this).attr('id') + ' input[type=\'hidden\']'),
        dataType: 'json',
        success: function(json) {
            $('.warning, .error').remove();

            if (json['redirect']) {
                 $('#mask').addClass('mask');
                 $('#eposmaincontent').load(json['redirect'],function(){
                     $('#mask').removeClass('mask');
                 });
            } else if (json['error']) {
                if (json['error']['warning']) {
                    $('#shipping-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                    $('.warning').fadeIn('slow');
                }
            } else {
                $('.paymtaddr_selected .fa-check').remove();
                $('.paymnt-curr-addr').removeClass('paymtaddr_selected');
                $('.address-cell').removeClass('paymtaddr_selected');
                $('#'+addr_div).addClass('paymtaddr_selected');
                $('#'+addr_div).append('<i class="fa fa-check"></i>');
                if($('.customer_recent_orders th').hasClass('paymtaddr_selected')){
                    $('#button_reorder').removeClass('btndisabled');
                    $('.button_newcust_icon').html('<i class="fa fa-retweet"></i>');
                    $('.button_reorder_text').html('Reorder');
                }else{
                    $('#button_reorder').removeClass('btndisabled');
                    $('.button_newcust_icon').html('<i class="fa fa-exchange"></i>');
                    $('.button_reorder_text').html('Change');
                    
                }
                deliverydetails(add_post_code);
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
$(document).on('click','.address_change_btn', function() {
    $.ajax({
        url: 'index.php?route=epos/payment_address/validate&payment_address=existing',
        type: 'post',
        data: {address_id:$(this).attr('address_id')},
        dataType: 'json',
        success: function(json) {
            $('.warning, .error').remove();

            if (json['redirect']) {
                $('#mask').addClass('mask');
                $('#eposmaincontent').load(json['redirect'],function(){
                    $('#mask').removeClass('mask');
                });
                
            } else if (json['error']) {
                if (json['error']['warning']) {
                    $('#shipping-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

                    $('.warning').fadeIn('slow');
                }
            } else {
               $('#mask').addClass('mask');
               $('#eposmaincontent').load('index.php?route=epos/epos',function(){
                   $('#mask').removeClass('mask');
                   cartresize();
               });
               $.colorbox.close();
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
$(document).on('click','#button_reorder',function(){
    if($(this).hasClass('btndisabled')){
        $.colorbox.close();
        return false;
    }
    $.ajax({
        url: 'index.php?route=epos/customer_orders/reorder',
        type: 'post',
        data: $('.paymtaddr_selected input[type=\'hidden\']'),
        dataType: 'json',
        success: function(json) {
            $('#mask').addClass('mask');
            $('#eposmaincontent').load('index.php?route=epos/epos',function(){
                $('#mask').removeClass('mask');
                cartresize();
            });
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
$(document).on('click','.customer_recent_orders th',function(){
         if($(this).hasClass('paymtaddr_selected')){
            $('.customer_recent_orders th').removeClass('paymtaddr_selected');
            if($('.address-cell').hasClass('paymtaddr_selected') ||  $('.paymnt-curr-addr').hasClass('paymtaddr_selected')){
                $('.button_reorder_text').html('Change');
                $('.button_newcust_icon').html('<i class="fa fa-exchange"></i>');
               
            }else{
                $('#button_reorder').addClass('btndisabled');
            }
        }else{
            $('.customer_recent_orders th').removeClass('paymtaddr_selected');
            $(this).addClass('paymtaddr_selected');   
             $('#button_reorder').removeClass('btndisabled');
            $('.button_newcust_icon').html('<i class="fa fa-retweet"></i>');
              $('.button_reorder_text').html('Reorder');
            
        }
});
$(document).on('click','.customer-current-address', function(){
    $('.paymtaddr_selected .fa-check').remove();
     $('.address-cell').removeClass('paymtaddr_selected');
     $('.address_change_btn').hide();
     $('.paymnt-curr-addr').addClass('paymtaddr_selected');
     $('.button_newcust_icon').html('<i class="fa fa-exchange"></i>');
     $('.paymnt-curr-addr').append('<i class="fa fa-check"></i>');
     if($('.customer_recent_orders th').hasClass('paymtaddr_selected')){
         $('#button_reorder').removeClass('btndisabled');
         $('.button_newcust_icon').html('<i class="fa fa-retweet"></i>');
         $('.button_reorder_text').html('Reorder');
     }else{
          $('#button_reorder').addClass('btndisabled');
     }
     deliverydetails($('#add_postcode').val()); 
});
$(document).on('click','#btn_takeout_cust', function() {
   $.colorbox.close(); 
   $('#mask').addClass('mask');
    window.ajaxstatus = '0';
     clearpreviouspayments();
   $('#eposmaincontent').load('index.php?route=epos/epos&dtype=walkin',function(){
       $('#mask').removeClass('mask');
       cartresize();
   });
});
$(document).on('click','.searcus_change',function(){
     play_single_sound();
    $.ajax({
                    url: 'index.php?route=epos/epos&dtype=phonein',
                    type:'post',
                    data: { customer_id:$(this).attr('customerid')},
                    success: function() {
                       jsKeyboard.hide();
                       $.colorbox.close();  
                       $('#main_page_search_res').html('');
                      $('#mask').addClass('mask');
                       window.ajaxstatus = '0';
                       $('#eposmaincontent').load('index.php?route=epos/epos',function(){
                           $('#mask').removeClass('mask');
                           cartresize();
                       });
                    }
             });
});
$(document).on('click','#change-new-address', function() {
    play_single_sound();
    dtype = $(this).attr('dtype');
    $.ajax({
        url: 'index.php?route=epos/payment_address/validate&payment_address=new',
        type: 'post',
        data: $('#payment-new input[type=\'text\'], #payment-new input[type=\'hidden\'], #payment-new input[type=\'checkbox\']:checked, #payment-new select '),
        dataType: 'json',
        success: function(json) {
            $('.warning, .error').remove();

            if (json['redirect']) {
                $('#mask').addClass('mask');
                $('#eposmaincontent').load(json['redirect'],function(){
                    $('#mask').removeClass('mask');
                });
            } else if (json['error']) {
                if (json['error']['warning']) {
                    $('#payment-new').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

                    $('.warning').fadeIn('slow');
                }

                if (json['error']['firstname']) {
                    $('#payment-new input[name=\'firstname\'] ').after('<span class="error">' + json['error']['firstname'] + '</span>');
                }

                if (json['error']['lastname']) {
                    $('#payment-new input[name=\'lastname\'] ').after('<span class="error">' + json['error']['lastname'] + '</span>');
                }

                if (json['error']['house']) {
                    $('#payment-new input[name=\'house\']').after('<span class="error">' + json['error']['house'] + '</span>');
                }

                if (json['error']['address_1']) {
                    $('#payment-new input[name=\'address_1\'] ').after('<span class="error">' + json['error']['address_1'] + '</span>');
                }

                if (json['error']['city']) {
                    $('#payment-new input[name=\'city\'] ').after('<span class="error">' + json['error']['city'] + '</span>');
                }

                if (json['error']['postcode']) {
                    $('#payment-new input[name=\'postcode\'] + br').after('<span class="error">' + json['error']['postcode'] + '</span>');
                }


            } else {

                $('#mask').addClass('mask');
                 $('#eposmaincontent').load('index.php?route=epos/epos&dtype='+dtype,function(){
                     $('#mask').removeClass('mask');
                     cartresize();
                 });
                $.colorbox.close();


            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
$(document).on('click','#button-register-change', function() {
    play_single_sound();
    dtype = $(this).attr('dtype');
    var telephone = $('#register-new input[name=\'telephone\']').val();

    var address_1 = $('#register-new input[name=\'address_1\']').val();

    var postcode = $('#register-new input[name=\'postcode\']').val();
    postcode = postcode_formater(postcode);
    var regPostcode = /[A-Z]{1,2}[0-9]{1,2}[A-Z]{0,1} ?[0-9][A-Z]{2}/i;
    if(regPostcode.test(postcode) == false){
        alert("Please enter the Post Code");     
        return (false);
    }
    $('#register-new input[name=\'postcode\']').val(postcode);
    if ($('#register-new input[name=\'firstname\']').val() == "") {
        //alert("Please Enter the Firstname");
        //$('#register-new input[name=\'firstname\']').focus();
        //return (false);
    }
    if ($('#register-new input[name=\'lastname\']').val() == "") {
        alert("Please Enter the Lastname");
        $('#register-new input[name=\'lastname\']').focus();
        return (false);
    }

    if (telephone == "") {
        alert("Please enter the Phone");
        $('#register-new input[name=\'telephone\']').focus();
        return (false);
    }
    if (!checkUKTelephone(telephone)) {
        alert(telNumberErrors[telNumberErrorNo]);
        $('#register-new input[name=\'telephone\']').focus();
        return false;
    }

    if ($('#register-new input[name=\'house\']').val() == "") {
        alert("Please Enter the House");
        $('#register-new input[name=\'house\']').focus();
        return (false);
    }
    
    if (address_1 == "") {
        alert("Please Enter Your Adress");
        $('#register-new input[name=\'address_1\']').focus();
        return (false);
    }
    if ((address_1.length < 3) || (address_1.length > 128)) {
        alert("Address must be between 3 and 128 characters!");
        $('#register-new input[name=\'address_1\']').focus();
        return (false);
    }

    $.ajax({
        url: 'index.php?route=epos/register_customer/validate',
        type: 'post',
        data: $('#register-new  input[type=\'text\'],  #register-new  input[type=\'checkbox\']:checked, #register-new  input[type=\'radio\']:checked, #register-new  input[type=\'hidden\'], #register-new  select'),
        dataType: 'json',
        beforeSend: function() {
            $('#button-register').attr('disabled', true);
            $('#button-register').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
        },
        complete: function() {
            $('#button-register').attr('disabled', false);
            $('.wait').remove();
        },
        success: function(json) {
            $('.warning, .error').remove();

            if (json['redirect']) {
                $('#mask').addClass('mask');
                $('#eposmaincontent').load(json['redirect'],function(){
                    $('#mask').removeClass('mask');
                });
            } else if (json['error']) {
                if (json['error']['warning']) {
                    $('#register-new .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

                    $('.warning').fadeIn('slow');
                }

                if (json['error']['firstname']) {
                    $('#register-new input[name=\'firstname\'] + br').after('<span class="error">' + json['error']['firstname'] + '</span>');
                }

                if (json['error']['lastname']) {
                    $('#register-new input[name=\'lastname\'] + br').after('<span class="error">' + json['error']['lastname'] + '</span>');
                }


                if (json['error']['house']) {
                    $('#register-new input[name=\'house\'] + br').after('<span class="error">' + json['error']['house'] + '</span>');
                }

                if (json['error']['telephone']) {
                    $('#register-new input[name=\'telephone\'] + br').after('<span class="error">' + json['error']['telephone'] + '</span>');
                }


                if (json['error']['address_1']) {
                    $('#register-new input[name=\'address_1\'] + br').after('<span class="error">' + json['error']['address_1'] + '</span>');
                }

                if (json['error']['city']) {
                    $('#register-new input[name=\'city\'] + br').after('<span class="error">' + json['error']['city'] + '</span>');
                }

                if (json['error']['postcode']) {
                    $('#register-new input[name=\'postcode\'] + br').after('<span class="error">' + json['error']['postcode'] + '</span>');
                }


            } else {
                $('#mask').addClass('mask');
                 $('#eposmaincontent').load('index.php?route=epos/epos&dtype='+dtype,function(){
                     $('#mask').removeClass('mask');
                     cartresize();
                 });
                $.colorbox.close();
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
$(document).on('click','#cartreadycheckout', function() {
    play_single_sound();
    $('#content-part2').load(url + 'index.php?route=epos/checkout');
    $('#content-part2-cat').hide();
    $('#content-part2-prod').hide();

});
$(document).on('click','#cartreadycheckountback', function() {
    play_single_sound();
    $('#content-part2').html('');
    $('#content-part2-cat').show();
    $('#content-part2-prod').show();

});
$(document).on('click','.optn-pge-item', function() {
    play_single_sound();
    $('.optn-pge-item').removeClass('selected');
    $(this).addClass('selected');
    $('.main_options').hide();
    $('.opt_pop_page_'+$('.optn-pge-item.selected input[type=\'hidden\']').val()).show();
});
$(document).on('click','.bunoptn-pge-item', function() {
    play_single_sound();
    $('.bunoptn-pge-item').removeClass('selected');
    $(this).addClass('selected');
    $('.main_options').hide();
    //alert($('.bunoptn-pge-item.selected input[type=\'hidden\']').val());
    $('.bun_opt_pop_page_'+$('.bunoptn-pge-item.selected input[type=\'hidden\']').val()).show();
});
$(document).on('click','.btn_finish_and_pay', function() {
        play_single_sound();
        $('#mask').addClass('mask');
         window.ajaxstatus = '0';
          clearpreviouspayments();
        $('#eposmaincontent').load('index.php?route=epos/confirm',{comment:$('#ordr-commnts').val()},function(){
            $('#mask').removeClass('mask'); 
        });
    
});
$(document).on('click','.discount_remove', function() {
    play_single_sound();
    $('.disc_error').html('');
         $.ajax({
                url: 'index.php?route=epos/epos/removeadhocdiscount&discount_id='+$(this).attr('dicount_id'),
                type: 'get',
                dataType: 'json',
                success: function(json) {
                    $.colorbox.close();
                    $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
                        cartresize();
                        $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
                            $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
                            checkoutfunctions(); 
                        });
                    });
                }
            });
});
$(document).on('click','.adh_spl_li', function() {
    play_single_sound();
    $('.disc_error').html('');
     if($(this).hasClass('hasgroup')){
        $('.adh_spl_li').removeClass('adhoc_spl_disc_slected');
        $('.adh_spl_li i').remove();
        $.ajax({
            url: 'index.php?route=epos/epos/adhocgroupdiscount&discount_id='+$(this).attr('dicount_id'),
            type: 'get',
            dataType: 'json',
            success: function(json) { 
                  if(json['success']){
                      $.colorbox.close();
                        $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
                            $('.mini-cart-info tbody').height('' + getcartheght() + 'px');
                            $(".mini-cart-info tbody").scrollTop($(".mini-cart-info tbody")[0].scrollHeight);
                            $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
                                $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
                                checkoutfunctions(); 
                            });
                        });
                  }else if(json['error']){
                        $('.disc_error').html(json['error']);
                  } 
                
            }
        });
        return false;
    }
     $('.adhoc-disc-type i').remove();
    $('.adhoc-disc-type').removeClass('adhoc_disc_type_selected');
    $('.adh_spl_li i').remove();
    if($(this).hasClass('adhoc_spl_disc_slected')){
        $('.adh_spl_li').removeClass('adhoc_spl_disc_slected');
    }else{
        $('.adh_spl_li').removeClass('adhoc_spl_disc_slected');
        $(this).addClass('adhoc_spl_disc_slected');
        $('.adhoc_spl_disc_slected .disc_amt_det').before('<i class="fa fa-check"></i>');
    }
    return ;
});
$(document).on('click','#current_item_discount', function() {
    play_single_sound();
    $('.disc_error').html('');
    if(!$('.adh_spl_li').hasClass('adhoc_spl_disc_slected')){
        if($('#adhoc_discount_name').val()==''){
            $('.disc_error').html('Please Enter New Discount Name;');
            return false;
        }else{
            $('.disc_error').html('');
        }
        if(isNaN($('#adhoc_discount_amt').val()) || $('#adhoc_discount_amt').val()==''){
            $('.disc_error').html('Please Enter valid Discount Amount;');
            return false;
        }else{
            $('.disc_error').html('');
        }
    }
        var adhocdata={};
    
        adhocdata.discount_amt=$('#selected_prodt_price').val();
        adhocdata.item_name=$('.adhoc_selected_item').html();
        adhocdata.discount_name=$('#adhoc_discount_name').val();
        adhocdata.discount_amount=$('#adhoc_discount_amt').val();
        adhocdata.discount_type=$('.adhoc_disc_type_selected').attr('id');
    if($('.adh_spl_li').hasClass('adhoc_spl_disc_slected')){
        adhocdata.discount_id=$('.adhoc_spl_disc_slected').attr('dicount_id');
        $.ajax({
                    url: 'index.php?route=epos/epos/addadhocitemdiscount',
                    type: 'post',
                    data:adhocdata,
                    dataType: 'json',
                    success: function(json) {
                        $.colorbox.close();
                        $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
                            cartresize();
                            $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
                                $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
                                checkoutfunctions(); 
                            });
                        });
                    }
        });
    }else{
            $.ajax({
                     url: 'index.php?route=epos/epos/checkadhocdiscountuser',
                     dataType: 'json',
                     success: function(json) {
                      
                       if(json['success']){
                          $.ajax({
                                url: 'index.php?route=epos/epos/addadhocitemdiscount',
                                type: 'post',
                                data:adhocdata,
                                dataType: 'json',
                                success: function(json) {
                                    $.colorbox.close();
                                    $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
                                        cartresize();
                                        $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
                                            $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
                                            checkoutfunctions(); 
                                        });
                                    });
                                }
                            });
                        }else{
                            //alert('no permission');
                            $('.pin-validate').removeClass('adhocitemuser');
                            $('.pin-validate').removeClass('adhocorderuser');
                             $('.disc_error').html('You Have No Permission to Give Discounts');
                             $.colorbox({ inline: true, overlayClose:false,escKey:false, opacity: 0.5, href: "#security_key_popup",
                                 onCleanup:function(){
                                    $('#pin_focus').val('');
                                    $('.pin-star-entry').html('');
                                    //$('.pin-validate').removeClass('adhocitemuser');
                                    //$('.pin-validate').removeClass('adhocorderuser');
                                }
                             });
                             $('.pin-validate').addClass('adhocitemuser');
                       }    
                     }
        });
        }
    
        
       
    return;    
    $.colorbox.close();
});
$(document).on('click','#entire_order_discount', function() {
    play_single_sound();
    $('.disc_error').html('');
     if(!$('.adh_spl_li').hasClass('adhoc_spl_disc_slected')){
        if($('#adhoc_discount_name').val()==''){
            $('.disc_error').html('Please Enter New Discount Name;');
            return false;
        }else{
            $('.disc_error').html('');
        }
        if(isNaN($('#adhoc_discount_amt').val()) || $('#adhoc_discount_amt').val()==''){
            $('.disc_error').html('Please Enter valid Discount Amount;');
            return false;
        }else{
            $('.disc_error').html('');
        }
    }
    var adhocdata={};
    
     adhocdata.discount_name=$('#adhoc_discount_name').val();
     adhocdata.discount_amount=$('#adhoc_discount_amt').val();
     adhocdata.discount_type=$('.adhoc_disc_type_selected').attr('id');
     if($('.adh_spl_li').hasClass('adhoc_spl_disc_slected')){
        adhocdata.discount_id=$('.adhoc_spl_disc_slected').attr('dicount_id');
        $.ajax({
                url: 'index.php?route=epos/epos/addadhocorderdiscount',
                type: 'post',
                data:adhocdata,
                dataType: 'json',
                success: function(json) {
                    $.colorbox.close();
                    $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
                        cartresize();
                        $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
                            $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
                            checkoutfunctions(); 
                        });
                    });
                }
     });
    }else{
         $.ajax({
                     url: 'index.php?route=epos/epos/checkadhocdiscountuser',
                     dataType: 'json',
                     success: function(json) {
                      
                       if(json['success']){
                           $.ajax({
                                url: 'index.php?route=epos/epos/addadhocorderdiscount',
                                type: 'post',
                                data:adhocdata,
                                dataType: 'json',
                                success: function(json) {
                                    $.colorbox.close();
                                    $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
                                        cartresize();
                                        $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
                                            $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
                                            checkoutfunctions(); 
                                        });
                                    });
                                }
                            });
                        }else{
                            $('.pin-validate').removeClass('adhocitemuser');
                            $('.pin-validate').removeClass('adhocorderuser');
                             $('.disc_error').html('You Have No Permission to Give Discounts');
                             $.colorbox({ inline: true, overlayClose:false,escKey:false, opacity: 0.5, href: "#security_key_popup",
                                 onCleanup:function(){
                                    $('#pin_focus').val('');
                                    $('.pin-star-entry').html('');
                                    //$('.pin-validate').removeClass('adhocitemuser');
                                   // $('.pin-validate').removeClass('adhocorderuser');
                                }
                             });
                            $('.pin-validate').addClass('adhocorderuser');
                       }    
                     }
        });
     }
     
    return;
    $.colorbox.close();
});
$(document).on('click','#cancel-adhoc-discount', function() {
    play_single_sound();
    $.colorbox.close();
    $('.pin-validate').removeClass('adhocitemuser');
    $('.pin-validate').removeClass('adhocorderuser');
    return ;
    $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
        cartresize();
        $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
           $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
           checkoutfunctions(); 
           
        });
    });
});
$(document).on('click','.group_disc_li',function(){
      play_single_sound();
      $('.disc_error').html('');
       if(!$('.adh_spl_li').hasClass('adhoc_spl_disc_slected')){
        if($('#adhoc_discount_name').val()==''){
            $('.disc_error').html('Please Enter New Discount Name;');
            return false;
        }else{
            $('.disc_error').html('');
        }
        if(isNaN($('#adhoc_discount_amt').val()) || $('#adhoc_discount_amt').val()==''){
            $('.disc_error').html('Please Enter valid Discount Amount;');
            return false;
        }else{
            $('.disc_error').html('');
        }
    }
       
     var disocuntdata={};
     disocuntdata.group_id=$(this).attr('group_id');
     disocuntdata.discount_name=$('#adhoc_discount_name').val();
     disocuntdata.discount_amount=$('#adhoc_discount_amt').val();
     disocuntdata.discount_type=$('.adhoc_disc_type_selected').attr('id');
     if($('.adh_spl_li').hasClass('adhoc_spl_disc_slected')){
        disocuntdata.discount_id=$('.adhoc_spl_disc_slected').attr('dicount_id');
        $.ajax({
                url: 'index.php?route=epos/epos/addadhocdiscountforgroups',
                type: 'post',
                data:disocuntdata,
                dataType: 'json',
                success: function(json) {
                    if(json['success']){
                            $.colorbox.close();
                            $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
                                cartresize();
                                $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
                                    $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
                                    checkoutfunctions(); 
                                });
                            });
                    }else if(json['error']){
                        $('.disc_error').html(json['error']);
                    } 
                   
                }
     });
    }else{
         $.ajax({
                     url: 'index.php?route=epos/epos/checkadhocdiscountuser',
                     dataType: 'json',
                     success: function(json) {
                      
                       if(json['success']){
                           $.ajax({
                                url: 'index.php?route=epos/epos/addadhocdiscountforgroups',
                                type: 'post',
                                data:disocuntdata,
                                dataType: 'json',
                                success: function(json) {
                                    if(json['success']){
                                        $.colorbox.close();
                                        $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
                                            cartresize();
                                            $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
                                                $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
                                                checkoutfunctions(); 
                                            });
                                        });
                                    }else if(json['error']){
                                        $('.disc_error').html(json['error']);
                                    }
                                }
                            });
                        }else{
                            $('.pin-validate').removeClass('adhocitemuser');
                            $('.pin-validate').removeClass('adhocorderuser');
                             $('.disc_error').html('You Have No Permission to Give Discounts');
                             $.colorbox({ inline: true, overlayClose:false,escKey:false, opacity: 0.5, href: "#security_key_popup",
                                 onCleanup:function(){
                                    $('#pin_focus').val('');
                                    $('.pin-star-entry').html('');
                                    //$('.pin-validate').removeClass('adhocitemuser');
                                   // $('.pin-validate').removeClass('adhocorderuser');
                                }
                             });
                            $('.pin-validate').addClass('adhocorderuser');
                       }    
                     }
        });
     }
     
    return;  
  });
$(document).on('click','#cartreadycancel', function() {
        //parent.jQuery.colorbox.close();
        play_single_sound();
        if($(this).hasClass('hder-inactive')){
            return false;
        }
        $('#colorbox').removeClass('headerlink_popup'); 
        $.colorbox({inline: true, opacity: 0.5, href: "#cancelcart_popup",overlayClose:false,escKey:false,
            onCleanup:function() {
                $('.pin-validate').removeClass('orderkill');  
                $('.pin-validate').removeAttr('live_id');
                $('.user_pin_entry').remove();
                $('#pin-pop-close').removeClass('orderkill');  
                $('#system_noclose_msg').html('');
                $('.pos_current_liveorders').html('');
                $('.systemoff_popup_data').show(); 
            }
        }); 
 });
 $(document).on('click','#pin-pop-close', function() {
    $('.pin-star-entry').html('');
    $('.pin_val_error').html('');
    $('#pin_focus').val('');
    if($(this).hasClass('orderkill')){
        $('.pin-validate').removeClass('orderkill');  
        $('.pin-validate').removeAttr('live_id');
        $('.user_pin_entry').remove();
        $('#pin-pop-close').removeClass('orderkill');  
        $('.systemoff_popup_data').show(); 
    }else{
        $.colorbox.close();
    }
}); 
$(document).on('click','#system_off_btn', function() {
        //parent.$.colorbox.close(); 
        play_single_sound();
        if($(this).hasClass('hder-inactive')){
            return false;
        }
       $('#colorbox').removeClass('headerlink_popup'); 
        $.colorbox({ inline: true,overlayClose:false,escKey:false, opacity: 0.5, href: "#systemoff_popup",
                onCleanup:function() {
                    $('.pin-validate').removeClass('orderkill');  
                    $('.pin-validate').removeAttr('live_id');
                    $('.user_pin_entry').remove();
                    $('#pin-pop-close').removeClass('orderkill');  
                    $('#system_noclose_msg').html('');
                    $('.pos_current_liveorders').html('');
                    $('.systemoff_popup_data').show(); 
                }
        });
});
$(document).on('click','#cancelcart_yes', function() {
         play_single_sound();
         $('#colorbox').removeClass('headerlink_popup'); 
          $.ajax({
                     url: 'index.php?route=epos/epos/checkordercancellation',
                     dataType: 'json',
                     success: function(json) {
                      
                       if(json['success']){
                          $('#colorbox').removeClass('headerlink_popup'); 
                           $.colorbox({ inline: true, overlayClose:false,escKey:false, opacity: 0.5, href: "#security_key_popup" });
                           
                       }else{
                            window.ajaxstatus = '1';
                           $.colorbox.close();
                           $.ajax({
                                url: 'index.php?route=epos/epos/cancel',
                                dataType: 'json',
                                success: function(json) {
                                     $('#mask').addClass('mask');
                                     $('#eposmaincontent').load('index.php?route=epos/epos/main',function(){
                                         $('#mask').removeClass('mask');
                                     });
                                }
                            }); 
                           
                           //location = 'index.php?route=epos/epos/cancel&token=<?php echo $token; ?>';
                       }    
                     }
          }); 
    }); 
$(document).on('click','#systemoff_yes_close', function() {
        play_single_sound();
        $.ajax({
            url: 'index.php?route=epos/epos/checkliveorderexists',
            dataType: 'json',
            success: function(json) {
                if(json['noclose']){
                    currenordershtml = '';
                    $('#system_noclose_msg').html('YOU ARE NOT ABLE TO CLOSE THE SYSTEM BECAUSE  LIVE ORDERS EXISTS.PLEASE CLOSE THE ORDER').fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                    if(json['liveorders'].length>0){
                                currenordershtml +='<table class="form">';
                                for(i in json['liveorders']){
                                    if(i%2==0){
                                        trclass="even";
                                    }else{
                                        trclass="odd";
                                    }
                                    currentorder = json['liveorders'][i];
                                    currenordershtml +='<tr id="current_live_'+currentorder['live_order_id']+'" class="'+trclass+'"><td># '+currentorder['live_order_id']+'</td><td>'+currentorder['date_added']+'</td><td>'+currentorder['system_name']+'</td><td><a class="button popsmall_btn postcode_find_btn orderkillbtn" onclick="killliveorder('+currentorder['live_order_id']+')"><i class="fa fa-times"></i>Kill</a></td></tr>';
                                }
                                currenordershtml +='</table>';
                            }
                    $('.pos_current_liveorders').html(currenordershtml);
                    parent.jQuery.colorbox.resize();
                }else{
                    showsalespage();
                }    
            }
        }); 
});
$(document).on('click','#userlogout_btn', function() {
           play_single_sound();
           if($(this).hasClass('hder-inactive')){
            return false;
          }
           $('#colorbox').removeClass('headerlink_popup'); 
           $.colorbox({inline: true,overlayClose:false,escKey:false, opacity: 0.5, href: "#userlogoutpopup"});
});
$(document).on('click','#cartreadyrefresh', function() {
    play_single_sound();
    if($(this).hasClass('hder-inactive')){
            return false;
    }
    parent.$.colorbox.close(); 
    $('#colorbox').removeClass('headerlink_popup'); 
     location = 'index.php?route=epos/epos/eposmainframe';
});
$(document).on('click','#editing-history', function() { 
         play_single_sound();
         if($(this).hasClass('hder-inactive')){
            return false;
        }
          $.colorbox.close();
         $("#order-epos-edited-order").load("index.php?route=epos/epos/ordereditingdetails&order_id="+$(this).attr('order_id'), function() {
                $.colorbox({ inline: true, opacity: 0.5, href: "#order-epos-edited-order",
	            onLoad: function() {
                     $('#cboxClose').remove();
                     $('#edit-history-tabs a').tabs();
                     $('#colorbox').addClass('history-popup'); 
            },
            onCleanup:function() {
                    $('#colorbox').removeClass('history-popup'); 
             }
            });
         }); 
 }); 
$(document).on("click","#postcodelookup",function(){
     $('.post_del_map').remove();
    $('#address_1').val('');
    $('#city').val('');
            addr_postcode = postcode_formater($('#postcode').val());
            //check postcode validation
            regPostcode = /[A-Z]{1,2}[0-9]{1,2}[A-Z]{0,1} ?[0-9][A-Z]{2}/i;
            if(regPostcode.test(addr_postcode) === false){
                $('.post_code_warning').html('Invalid Postcode');
              return ;
            }
            //deliverydetails(addr_postcode); 
            $('#postcode').val(addr_postcode);
            $('#selcted_shipp_home').removeClass('no_delivery');
             $.ajax({
                url: 'index.php?route=epos/epos/checkdeliveryarea',
                type: 'post',
                data: {postcode: addr_postcode},
                dataType: 'json',
                success: function(json) {
                     $('.post_code_warning').html('');
                    if(json['error']){
                        $('#selcted_shipp_home').removeClass('selected_shipping');
                        $('#selcted_shipp_collect').addClass('selected_shipping');
                        $('.post_code_warning').html('No matching Postcode in our database');
                        blinkdivwithclass('post_code_warning');
                    }else{
                         distclass='';
                         $('#address_1').val(json['street']);
                         $('#city').val(json['city']);
                         $('.post_code_warning').html('');
                         $('#postcodelookup').after('<span class="post_del_map"><a onclick="showdeliveryrout(\''+store_postalcode+'\',\''+addr_postcode+'\',\'en_US\',\'\');"><i class="fa fa-map-marker"></i></a></span>');
                        if(json['air_distance']!=undefined){
                            distclass='airdistance';
                           // getdeliverydistance(''+store_postalcode+'',''+addr_postcode+'',''+json['air_distance']+'');
                            //return;
                        }
                         if(json['status']=='yes'){
                            $('.delivery_details').html('<span class="'+distclass+'">Distance:'+json['distance']+' miles</span>');
                         }else{
                            $('#selcted_shipp_home').removeClass('selected_shipping');
                            $('#selcted_shipp_collect').addClass('selected_shipping');
                            $('#selcted_shipp_home').addClass('no_delivery');
                            $('.delivery_details').html('<span class="del_status_msg">'+json['message']+' </span> Distance :'+json['distance']+' miles');
                         }
                         blinkdivwithclass('del_status_msg');
                    }  
                }
            });
      });
$(document).on('change','#postcode',function(){
            //deliverydetails($('#postcode').val());
        });
$(document).on('change','#ppostcode',function(){
            //deliverydetails($('#postcode').val());
        });
$(document).on("click","#ppostcodelookup",function(){
            addr_postcode = postcode_formater($('#ppostcode').val());
            //check postcode validation
            $('#paddress_1').val('');
            $('#pcity').val('');
            $('#ppostcode').val(addr_postcode);
             $.ajax({
                url: 'index.php?route=epos/epos/checkdeliveryarea',
                type: 'get',
                data: {postcode: addr_postcode},
                dataType: 'json',
                success: function(json) {
                     $('.ppost_code_warning').html('');
                    if(json['error']){
                        $('.ppost_code_warning').html('No matching Postcode in our database');
                    }else {
                        $('#paddress_1').val(json['street']);
                        $('#pcity').val(json['city']);
                        $('.ppost_code_warning').html('');
                    } 
                }
            });
      });     
$(document).on('click','#pending-print-btn', function() {
         play_single_sound();
          var print_order_id = $(this).attr('order_id');
            $.ajax({
                     url: 'index.php?route=epos/print/epos&order_id='+print_order_id+'&custprint=order',
                     dataType: 'html',
                     success: function(data) {
                        //$('#printepos').show();
                        $('#printepos').html(data);
                        $('#printpdf').html(url+"index.php?route=epos/print/pdf&order_id="+print_order_id);
                        $('#printhtml').html(url+"index.php?route=epos/print/html&order_id="+print_order_id);
                        $('#printtext').html(url+"index.php?route=epos/print/text&order_id="+print_order_id);
                        //$('#printepos').hide();
                        $.colorbox.close();  
                        print_order(); 
                        $.ajax({
                             url: 'index.php?route=epos/epos/updateprintedorders&order_id='+print_order_id,
                             dataType: 'json',
                             success: function(json) {
                             
                             }    
                         }); 
                       
                     }
                }); 
        }); 
$(document).on('click','.online-order-print', function() {
         play_single_sound();
          var print_order_id = $(this).attr('order_id');
            $.ajax({
                     url: 'index.php?route=epos/print/epos&order_id='+print_order_id+'&custprint='+$(this).attr('printarea'),
                     dataType: 'html',
                     success: function(data) {
                        //$('#printepos').show();
                        $('#printepos').html(data);
                        $('#printpdf').html(url+"index.php?route=epos/print/pdf&order_id="+print_order_id);
                        $('#printhtml').html(url+"index.php?route=epos/print/html&order_id="+print_order_id);
                        $('#printtext').html(url+"index.php?route=epos/print/text&order_id="+print_order_id);
                        //$('#printepos').hide();
                         $.colorbox.close();  
                         print_order(); 
                         $.ajax({
                             url: 'index.php?route=epos/epos/updateprintedorders&order_id='+print_order_id,
                             dataType: 'json',
                             success: function(json) {
                             
                             }    
                         });    
                       
                     }
                }); 
        }); 
$(document).on('click','#checkout-print-btn', function() {
         play_single_sound(); 
            $.ajax({
                     url: 'index.php?route=epos/print/epos&livecart=yes',
                     type: 'post',
                     data:{comment:$('#ordr-commnts').val()}, 
                     dataType: 'html',
                     success: function(data) {
                        //$('#printepos').show();
                        if(data!==''){
                            $('#printepos').html(data);
                            $('#printpdf').html(url+"index.php?route=epos/print/pdf&livecart=yes");
                            $('#printhtml').html(url+"index.php?route=epos/print/html&livecart=yes");
                            $('#printtext').html(url+"index.php?route=epos/print/text&livecart=yes");
                            print_live_order(); 
                            liveordersave();
                       }
                     }
                }); 
        }); 
$(document).on('click','.order_update', function(){ 
          play_single_sound();
          order_data ={order_id:$('#pend_order_id').val(),order_status_id:$('#pend_order_status_id').val(),comment:$('#lastorder_cmt').val()};
          $.colorbox.close();
          $.ajax({
                     url: 'index.php?route=epos/epos/changeorderstaus',
                     type: 'post',
                     data: order_data,
                     dataType: 'json',
                     success: function(data) {
                        getpendingorderlist();
                     }
                }); 
    });
$(document).on('click','.single_order_status', function(){ 
        $('.pos-order-history').hide();
         $('.order-status-change-div').show();
});
$(document).on('click','.order_status_li', function(){ 
        $('.single_order_status').html($(this).html());
        $('#pend_order_status_id').val($(this).attr('stat_id'));
        $('.order-status-change-div').hide();
        $('.pos-order-history').show();
});
$(document).on('click','.pin-login-num', function() {
    play_single_sound();
    $('#pin_focus').val($('#pin_focus').val()+$(this).attr('val'));
    $('.pin-star-entry').append('&#8226;');
});
$(document).on('click','.pin-clear', function() {
         play_single_sound();
            $('#pin_focus').val($("#pin_focus").val().substring(0,($("#pin_focus").val().length-1)));
            $('.pin-star-entry').html($(".pin-star-entry").html().substring(0,($(".pin-star-entry").html().length-1)));
});
$(document).on('click','.pin-accept', function() {
         play_single_sound();
            if($('#pin_focus').val()==''){
              return false;    
            }    
            $('#loginform').submit();
        });    
$(document).on('click','.pin-validate', function() {
         play_single_sound();
            if($('#pin_focus').val()==''){
              return false;    
            }    
            if($(this).hasClass('adhocitemuser')){
                action = 'adhoc';
                redurl='order';
            }else if($(this).hasClass('adhocorderuser')){
                action = 'adhoc';
                redurl='item';
            }else{
                action = 'cancel';
                redurl='';
            }
         $.ajax({
                     url: 'index.php?route=epos/login/jsonvalidatepin',
                     type:'post',
                     dataType: 'json',
                     data:{ pincode:$('#pin_focus').val(),permisiion:action },
                     success: function(json) {
                      if(json['error']){
                           $('.pin_val_error').html(json['error']);
                        }else if(json['auth_error']){
                            $('.pin_val_error').html(json['auth_error']);
                        }else{
                            if($('.pin-validate').hasClass('orderkill')){
                                deleteliveorder($('.pin-validate').attr('live_id'));
                                return false;
                            }
                             $.colorbox.close();
                             if(redurl=='item'){
                                $('.pin-validate').removeClass('adhocuser');
                                $.ajax({
                                        url: 'index.php?route=epos/epos/addadhocitemdiscount',
                                        type: 'post',
                                        data:adhocdata,
                                        dataType: 'json',
                                        success: function(json) {
                                            $('.pin-star-entry').html('');
                                            $('.pin_val_error').html('');
                                            $('#pin_focus').val('');
                                            $.colorbox.close();
                                            $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
                                                cartresize();
                                                $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
                                                    $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
                                                    checkoutfunctions(); 
                                                    $('.pin-validate').removeClass('adhocitemuser');
                                                });
                                            });
                                        }
                                });
                            }else if(redurl=='order'){
                                $.ajax({
                                url: 'index.php?route=epos/epos/addadhocorderdiscount',
                                type: 'post',
                                data:adhocdata,
                                dataType: 'json',
                                success: function(json) {
                                    $('.pin-star-entry').html('');
                                    $('.pin_val_error').html('');
                                    $('#pin_focus').val('');
                                    $.colorbox.close();
                                    $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() { 
                                        cartresize();
                                        $('#content-part3').load(url + 'index.php?route=epos/checkout',function(){
                                            $('#content-part2-prod').height($(window).height()-$('#header').height()-$('#footer').height()-$('#content-part3').height()-20);
                                            checkoutfunctions(); 
                                            $('.pin-validate').removeClass('adhocorderuser');
                                        });
                                    });
                                }
                            });
                            }else{
                                $('.pin-star-entry').html('');
                                $('.pin_val_error').html('');
                                $('#pin_focus').val('');
                                $.colorbox.close();
                                $.ajax({
                                 url: 'index.php?route=epos/epos/cancel',
                                 dataType: 'json',
                                 success: function(json) {
                                     showmainpage();
                                 }
                             }); 
                            
                            } 
                           
                        }    
                     }
          });    
    });
$(document).on('click','#adhoc-discount-btn',function() {
          play_single_sound();
          $('#disc_selected_item').html('<span class="adhoc_selected_item" style="display:none" item_key="'+$('.cartitem_selected .citem-remove').attr('cartitemkey')+'">'+$('.cartitem_selected .pr_name').html()+'</span><input type="hidden" id="selected_prodt_price" value="'+$('.cartitem_selected .prodt_org_total').html()+'">');
          $('#current_item_discount span').html($('.cartitem_selected .pr_name').html());   
          $.colorbox({inline: true,opacity: 0.5,height:$(window).height()-$('#footer').height()-$('#header').height(),width:($('.eposrightside').width()+$('.centerborder').width()),href: "#adhoc-discount-popup",transition:"elastic",left:$('.eposleftside').width(),bottom:$('#footer').height(),top:$('#header').height(),overlayClose:false,escKey:false});
    });
$(document).on('click','#adhoc-product-btn', function() {
         play_single_sound();
         $.colorbox({inline: true,opacity: 0.5,href: "#adhoc-product-popup",transition:"elastic", width:"40%",overlayClose:false,escKey:false});
     });    
$(document).on('click','#add-adhoc-product', function() {
         play_single_sound();
               if($('#adhoc_product_name').val()==''){
                   $('.error-adhoc-product-name').show();
                   return false;
               }else{
                   $('.error-adhoc-product-name').hide();
               }    
               if(isNaN($('#adhoc_product_price').val()) || $('#adhoc_product_price').val()==''){
                 $('.error-adhoc-product-price').show();  
                 return false;
               }else{
                $('.error-adhoc-product-price').hide(); 
               }   
                $.ajax({
                     url: 'index.php?route=epos/epos/addadhocproduct',
                     dataType: 'json',
                     data:{product_name:$('#adhoc_product_name').val(),price:$('#adhoc_product_price').val()} ,
                     success: function(json) {
                       $.colorbox.close();
                       $('#cart').load('index.php?route=epos/cart', function() {
                            cartresize();
                            $('.paymentmethod-cell').removeClass('notallowed');
                            $('.shippingmethod-cell').removeClass('notallowed');
                       });
                     }
                }); 
     });
$(document).on('click','#new-order-ftr-btn', function() {
         play_single_sound();
         liveordersave();
        
});
$(document).on('click','.adhoc-disc-type', function() {
        $('.adh_spl_li').removeClass('adhoc_spl_disc_slected');
        $('.adhoc-disc-type').removeClass('adhoc_disc_type_selected');
        $('.adhoc-disc-type i').remove();
        $('.adh_spl_li i').remove();
        $(this).addClass('adhoc_disc_type_selected');
        $('.adhoc_disc_type_selected .adh-add-text').before('<i class="fa fa-check"></i>');
         return false;
          disc_type = $(this).attr('id');
          play_single_sound();
                 if($('#adhoc_discount_name').val()==''){
                   $('.error-adhoc-disc-name').show();
                   return false;
               }else{
                  $('.error-adhoc-disc-name').hide();
               }    
               //alert($('#adhoc_discount_amt').val());
               if(isNaN($('#adhoc_discount_amt').val()) || $('#adhoc_discount_amt').val()==''){
                 $('.error-adhoc-disc-amt').show();  
                 return false;
               }else{
                $('.error-adhoc-disc-amt').hide(); 
                 //return false;
               }   
                $.ajax({
                     url: 'index.php?route=epos/epos/addadhocdiscount',
                     dataType: 'json',
                     data:{discount_name:$('#adhoc_discount_name').val(),discount_amount:$('#adhoc_discount_amt').val(),discount_type:disc_type } ,
                     success: function(json) {
                       $.colorbox.close();
                       $('#cart').load('index.php?route=epos/cart', function() {
                           cartresize();
                            $('.paymentmethod-cell').removeClass('notallowed');
                            $('.shippingmethod-cell').removeClass('notallowed');
                       });
                     }
                }); 
     
     });  
$(document).on('click','.mainwalksbutn', function() {
     play_single_sound() ;
     $.colorbox.close();
     $('#main_page_search_res').html(''); 
     showtakeoutcontent($(this).attr('customerid'));
     //location = url + 'index.php?route=epos/epos&dtype=walkin&waklin_customer_id=' + $(this).attr('customerid');
 });
$(document).on('click','.mainphonesbutn', function() {
     play_single_sound() ;
     $.colorbox.close();
     $('#main_page_search_res').html(''); 
     showexistingcustomercontent($(this).attr('customerid'))
});
$(document).on('click','.order-to-assign', function() {
    play_single_sound() ;
    if($(this).hasClass('none')){
     return false;
    }    
    $("#order-delivery-details").load( "index.php?route=epos/epos/eposunassignedorderlist", function() {
          $.colorbox({ inline: true, opacity: 0.5,overlayClose: false ,escKey:false,closeButton:false, href: "#order-delivery-details",
                  onLoad: function() {
                                           $('#cboxClose').remove();         
                                      }  
                 });           
     });
 });
$(document).on('click','.order-assigned-popup', function() {
    play_single_sound() ;
    if($(this).hasClass('none')){
     return false;
    }  
    $("#order-delivery-details").load( "index.php?route=epos/epos/eposassignedorderlist", function() {
          $.colorbox({ inline: true, opacity: 0.5,overlayClose: false ,escKey:false,closeButton:false, href: "#order-delivery-details",
               onLoad: function() {
                                           $('#cboxClose').remove();         
                                   }  
          });           
     });
 });
$(document).on('click','.order-delivered-popup', function() {
   play_single_sound() ;
    if($(this).hasClass('none')){
     return false;
    }  
    $("#order-delivery-details").load( "index.php?route=epos/epos/eposdeliveredorderlist", function() {
          $.colorbox({ inline: true, opacity: 0.5,overlayClose: false ,escKey:false,closeButton:false, href: "#order-delivery-details",
                 onLoad: function() {
                                           $('#cboxClose').remove();         
                                    }  
          });           
     });
 });
$(document).bind('cbox_cleanup', function() {
        $('#order-delivery-details').html('');
    });   
$(document).on('click','.order_assign_btn', function() {
    play_single_sound() ;
    row_id = $(this).attr('orederid');
    if($('#order-to-deliveres-' + row_id + ' input[type=\'hidden\']').val()==0){
         for (i = 0; i < 3; i++) {   
            $('.order-assign-driver-cell').fadeTo('slow', 0.5).fadeTo('slow', 1.0);
         }  
         return false;
    }    
    $.ajax({
		url: 'index.php?route=epos/epos/assigndriver',
		type: 'post',
		dataType: 'json',
		data: $('#order-to-deliveres-' + row_id + ' input[type=\'text\'],#order-to-deliveres-' + row_id + ' input[type=\'hidden\'], #order-to-deliveres-' + row_id + '  select, #order-to-deliveres-' + row_id + ' textarea'),
		beforeSend: function() {
			
		},
		complete: function() {
			
		},
		success: function(json) {
                    if(json['success']){
			showmainpage();
                    }
		}
	});
 });
$(document).on('click','.assignedorder_update_btn', function() {
    play_single_sound() ; 
    row_id = $(this).attr('orederid');
    $.ajax({
		url: 'index.php?route=epos/epos/assigndriver',
		type: 'post',
		dataType: 'json',
		data: $('#order-to-deliveres-' + row_id + ' input[type=\'text\'],#order-to-deliveres-' + row_id + ' input[type=\'hidden\'], #order-to-deliveres-' + row_id + '  select, #order-to-deliveres-' + row_id + ' textarea'),
		beforeSend: function() {
			
		},
		complete: function() {
			
		},
		success: function(json) {
                    if(json['success']){
			$.colorbox.close();
                         $("#order-delivery-details").load( "index.php?route=epos/epos/eposassignedorderlist", function() {
                           $.colorbox({inline: true, opacity: 0.5,overlayClose: false ,escKey:false,closeButton:false,href: "#order-delivery-details"});           
                         });
                    }
		}
	});
 }); 
$(document).on('click','.live-order-edit', function() {
    play_single_sound() ;
    live_ids = $(this).attr('id').split('-');
    $.ajax({
		url: 'index.php?route=epos/liveorders/editliveorder',
		type: 'post',
		dataType: 'json',
		data:{live_order_id:live_ids[1]} ,
		success: function(json) {
                    if(json['success']){
			$('#mask').addClass('mask');
                         window.ajaxstatus = '0';
                         $('#eposmaincontent').load('index.php?route=epos/epos&dtype='+json['dtype'],function(){
                             cartresize();
                             $('#mask').removeClass('mask');
                         });
	            }
		}
	});
});
$(document).on('click','.pos-sel-table', function() {
   play_single_sound() ;     
   if($(this).hasClass('dineinediting')){
       editpop  = '<div class="unlock-table-popup">';
       editpop += '<div class="unlock-table-popup-title"><h3>Do You Want to Unlock Table</h3></div>'; 
       editpop +='<div class="unlock-tble-btns"><a live_order_id="'+$(this).attr('live_order_id')+'" class="unloc_tble_yes playsound">Yes</a><a class="ftr_permsn_no playsound" onclick="parent.$.colorbox.close();">No</a> </div>';
       editpop += '</div>'; 
       $.colorbox( { html:editpop, width: "30%", opacity: 0.5,closeButton:false,overlayClose:false,escKey:false,
                   onLoad: function() {
                      $('#cboxClose').remove();
                   }    
            });
   }else if($(this).hasClass('dineined')){
     live_id = $(this).attr('live_order_id');
     table_id = $(this).attr('table_id');
            $.ajax({
		url: 'index.php?route=epos/liveorders/editliveorder',
		type: 'post',
		dataType: 'json',
		data:{live_order_id:live_id ,table_id:table_id } ,
		success: function(json) {
                    if(json['success']){
			$('#mask').addClass('mask');
                         window.ajaxstatus = '0';
                        $('#eposmaincontent').load('index.php?route=epos/epos&dtype=dinein',function(){
                            cartresize();
                            $('#mask').removeClass('mask');
                        });
	            }
		}
	});
   }else {
     $('#mask').addClass('mask');
      window.ajaxstatus = '0';
     $('#eposmaincontent').load('index.php?route=epos/epos&dtype=walkin&table_id='+$(this).attr('table_id'),function(){
         cartresize();
         $('#mask').removeClass('mask');
     });    
     //location = url + 'index.php?route=epos/epos&dtype=walkin&table_id='+$(this).attr('table_id');
   }    
   
}); 
$(document).on('click','.pending-order-edit', function() {
    play_single_sound() ;
    $.colorbox.close();
    pending_id = $(this).attr('id').split('-');
    $.ajax({
		url: 'index.php?route=epos/epos/editpendingorder',
		type: 'post',
		dataType: 'json',
		data:{order_id:pending_id[1]} ,
		success: function(json) {
                    if(json['success']){
			 $('#mask').addClass('mask');
                          window.ajaxstatus = '0';
                         $('#eposmaincontent').load('index.php?route=epos/epos&dtype='+json['dtype'],function(){
                             cartresize();
                             $('#mask').removeClass('mask');
                         });
	            }
		}
	});
});
$(document).on('click','.pending-order-view', function() {
      play_single_sound() ;
      pending_id = $(this).attr('pendid').split('-');
      pop_height=$(window).height()-$('#footer').height()-$('#header').height();
      pop_width = $(window).width()-$('.epos-live-orders').width()-10;
      $("#order-epos-lastorder").load( "index.php?route=epos/epos/pendingorderdetails&order_id="+pending_id[1], function() {
        if($("#order-epos-lastorder").html()!='') {
          $.colorbox({ inline: true,top:$('#header').height(),height:pop_height,width:"98%",opacity: 0.5, href: "#order-epos-lastorder",
	       onLoad: function() {
                 $('#cboxClose').remove();
                 $('#colorbox').addClass('lastorderepo'); 
               },
               onCleanup:function() {
                 $('#colorbox').removeClass('lastorderepo'); 
               }
	    }); 
        }
      });
});
$(document).on('click','.take-out-order', function() {
    play_single_sound();
    if($(this).hasClass('disabled')){
        return true;    
    } 
     $.ajax({
		url: 'index.php?route=epos/liveorders/editliveorder',
		type: 'post',
		dataType: 'json',
		data:{ live_order_id: $(this).attr('live_order_id') } ,
		success: function(json) {
                    if(json['success']){
                        $('#mask').addClass('mask');
                         window.ajaxstatus = '0';
			$('#eposmaincontent').load('index.php?route=epos/epos&dtype=walkin',function(){
                            cartresize();
                            $('#mask').removeClass('mask');
                        });
                    }
		}
	});
});    
$(document).on('click','.btn_quick_btn',function(){
    parent.$.colorbox.close();
    $('#mask').addClass('mask');
    $('#eposmaincontent').load('index.php?route=epos/quick_checkout',function(){
        $('#mask').removeClass('mask');
    });
  
});
$(document).on('click','#table_layout_edit',function(){ 
    getliveorders();  
    window.editdisplay = '1';
  });
$(document).on('click','#table_layout_save',function(){ 
    tableproperties = [];
    $('.store_table_edit').each(function(){
        tableposid=$(this).attr('id');
        divposition =$('#'+tableposid).position();
        tableproperties[$(this).attr('table_id')] = { top:divposition.top, left:divposition.left, height:$('#'+tableposid).height(),width:$('#'+tableposid).width() };
    });
    window.editdisplay = '0';
    $.ajax({
     url: 'index.php?route=epos/restaurant/savelayoutsettings',
     type: 'post',
     data: { data:tableproperties},
     dataType: 'json',
        success: function(json) {
          getliveorders();  

        }
    });
}); 
$(document).on('click','#report-save-btn', function() {
    play_single_sound();
    $.colorbox({ inline: true,overlayClose:false,escKey:false, opacity: 0.5, href: "#close_restaurant_popup",
            onCleanup:function() {
              $('#close_restaurant_nomsg').html('');
                $('.pin-validate').removeClass('orderkill');  
                $('.pin-validate').removeAttr('live_id');
                $('.user_pin_entry').remove();
                $('#pin-pop-close').removeClass('orderkill');  
               $('#close_restaurant_nomsg').html('');
                $('.pos_current_liveorders').html('');
                $('.systemoff_popup_data').show(); 
            }
            });
        
});
$(document).on('click','#report-skip-btn', function() {
    $('#mask').addClass('mask');
    $('#eposmaincontent').load('index.php?route=epos/sales&skip=1',function(){
        $('#mask').removeClass('mask');
    });
});
$(document).on('click','.yes-btn-li', function() {
           $('.yes-btn-li').removeClass('selected');
           $(this).addClass('selected');
           $.ajax({
                        url: 'index.php?route=epos/sales/getreporthtml&start_time='+$('#report_start_time').val()+'&end_time='+$(".selected").attr('value'),
                        dataType: 'html',
                        success: function(data) {
                          
                           $('#sale-report-parent-div').html(data);
                            var remheight =$(window).height()-$('#header').height()-$('#footer').height()-30 ;
                            $('#content').height(remheight);  
                            $('.prodt-sales-report').height(remheight-100);  
                            $('.sales-payment-report').height(remheight-100);  
                            $('.sales-delivery-report').height(remheight-100);
                            $('.no-sales-report').height(remheight-100);
                          
                        }
                    
                });
       });
//Login.tpl
$(document).on('submit','#loginform',function(event){
    $('#mask').addClass('mask');
    var $form = $(this), 
    serializedData = $form.serialize();
    $.ajax({
        url: "index.php?route=epos/login",
        type: "post",
        data: serializedData,
        success: function(response, textStatus, jqXHR){
            $('#mask').removeClass('mask');
             window.ajaxstatus = '1';
            document.getElementById("eposmaincontent").innerHTML = response;
            window.eposcache['maincontent'] =response;
            showmainpage();
          }
    });

    event.preventDefault();
});
$('#loginform input').keydown(function(e) {
        if (e.keyCode == 13) {
            $('#loginform').submit();
        }
    });  
//OLD SPLIT START    
$(document).on('click','#order-split-button',function(){
    play_single_sound();
    $('#mask').addClass('mask');
   $('#eposmaincontent').load('index.php?route=epos/confirm&split=E',function(){
        $('#mask').removeClass('mask');
   }); 
    return false;
    $.colorbox({inline: true,opacity: 0.5,href: "#order-split-popup",transition:"elastic", width:"40%",overlayClose:false,escKey:false});
});
$(document).on('click','#split_order_equal',function(){
   $.colorbox.close(); 
   $('#mask').addClass('mask');
   $('#eposmaincontent').load('index.php?route=epos/confirm&split=E',function(){
        $('#mask').removeClass('mask');
   }); 
});

$(document).on('click','.split_paymt_btn_li',function(){
    clearpreviouspayments();
    $('.split_paymt_btn_li').removeClass('payment_seltd');
    $(this).addClass('payment_seltd');
    if(!$('.split_selected').hasClass('payed')){//.split_payment_selection
        $('#order_split_payments .split_payment_selection').load('index.php?route=epos/confirm/getsplitpaymentmethod&payment_code='+$(this).attr('code')+'&split_id='+$('.split_selected').attr('split_id')+'&payment_amount='+$('.split_selected #splitind_amount').val(),function(){
            $('.main_pay_system').trigger('click');
        });
    }  
});
$(document).on('click','#split_order_product',function(){
   $.colorbox.close(); 
   $('#mask').addClass('mask');
   $('#eposmaincontent').load('index.php?route=epos/confirm&split=P',function(){
        $('#mask').removeClass('mask');
   }); 
});
$(document).on('click','#product_split_btn',function(){
     $.ajax({ 
           //type: "post",
           url: 'index.php?route=epos/confirm/splitproducts',	
           dataType: 'json',
           success: function(json) {
              
           }
    });
});
//OLD SPLIT END
$(document).on('click','.splitcountbtn', function() {
    play_single_sound();
    $('#split_warnings').html('');
    if($(this).hasClass('cal-num-accept')){//Accept value
        //alert($("#equalsplitcount").val());
        if($("#equalsplitcount").val()==''){
            $('#split_warnings').html("Entered Data is not valid");
            return false;
        }
        splittedtotal =getsplittedtotal();
        splitcounts = getsplitcounts();//splitteddata.length;
        if(splittedtotal>=order_data['total_amount']){
            $('#split_warnings').html("Splitted Amount exceedes Order Total");
            return false;
        }
        if($('#colorbox').hasClass('amountseln')){
            if((splittedtotal+eval($("#equalsplitcount").val())) > order_data['total_amount']){
                 $('#split_warnings').html("Splitted Amount exceedes Order Total");
                return;
            }
             splitteddata[eval(splitcounts)]={amount:$("#equalsplitcount").val(),products:{},payments:{},split_type:'Custom'};
        }else{
            
            split_amounts   = geteqaulsplittedamounts(eval(order_data['total_amount']-splittedtotal),eval($("#equalsplitcount").val()));
            for(i=0;i<$("#equalsplitcount").val();i++){
                splitteddata[eval(splitcounts)]={amount:split_amounts[eval(i+1)],products:{},payments:{},split_type:'Equal'};
                //splittedtotal = splittedtotal+eval($("#equalsplitcount").val());
                splitcounts ++;
            }
        }
        
        $.colorbox.close();
        getsplitmaincart();
        splittedorderdetails();
    }else if($(this).hasClass('cal-num-clear')){//Clear value
        $('#equalsplitcount').val($("#equalsplitcount").val().substring(0,($("#equalsplitcount").val().length-1)));
        $('#pop_split_count').html($('#equalsplitcount').val());
    }else{
         if($('#colorbox').hasClass('countseln') && $(this).attr('value')=='.'){
             return false;
         }
         $('#equalsplitcount').val($('#equalsplitcount').val()+$(this).attr('value'));
         $('#pop_split_count').html($('#equalsplitcount').val());
    }
});
$(document).on('click','#splitted_left_btns li.splitted', function() {
    //alert("Ss");
    $('#splitted_left_btns li').removeClass('split_selected');
    $(this).addClass('split_selected');
    showsplitdetails($(this).attr('split_id'));
});
$(document).on('click','#split_main_cart_items .itemrow .prname', function(){
    div_id = $(this).attr('row_id'); 
    //alert(div_id);
    $('#split_main_cart_items .itemrow').removeClass('splitselected');
    if($('#cart_row_'+div_id).hasClass('cartitem_selected')){
        $('#cart_row_'+div_id +' .splitted_qty_det').hide();
        $('#cart_row_'+div_id ).removeClass('cartitem_selected');
    }else{
        $('#cart_row_'+div_id ).addClass('cartitem_selected splitselected');
        $('.splitselected .splitted_qty_det').show();
    }
    
});//splitquantity
$(document).on('click','.splitquantity', function(){
    rowid = $(this).attr('row_id');
    maxxqty = $(this).attr('qty');
    if($(this).hasClass('minus') && $('#cart_row_'+rowid+' #split_seld_qty').val()!=1){
        $('#cart_row_'+rowid+' #split_seld_qty').val(eval($('#cart_row_'+rowid+' #split_seld_qty').val())-1);
        $('#cart_row_'+rowid+' .spliited_org_qty').html($('#cart_row_'+rowid+' #split_seld_qty').val());
    }else if($(this).hasClass('add') && $('#cart_row_'+rowid+' #split_seld_qty').val()<maxxqty){
        $('#cart_row_'+rowid+' #split_seld_qty').val(eval($('#cart_row_'+rowid+' #split_seld_qty').val())+1);
        $('#cart_row_'+rowid+' .spliited_org_qty').html($('#cart_row_'+rowid+' #split_seld_qty').val());
    }
});
$(document).on('click','#item_split_btn',function(){
    $('#split_warnings').html('');
    if($('.split_btns').hasClass('splitdisabled')){
            return false;
    }
    if(!$('.itemrow').hasClass('cartitem_selected')){
         $('#split_warnings').html("not selected");
        return false;
    }
    splittedtotal =getsplittedtotal();
    splitcounts = getsplitcounts();//splitteddata.length;
    prodt_amt = 0;
    sel_prodts = {};
    $('.cartitem_selected').each(function(){
       div_id = $(this).attr('id'); 
       prodt_splitamt = eval($('#'+div_id+ ' #split_seld_qty').val()).toFixed(2) * eval($('#'+div_id+ ' .split_sel_price').val()).toFixed(2);
       prodt_amt +=  prodt_splitamt;//(eval($('#'+div_id+ ' #split_seld_qty').val()).toFixed(2) * eval($('#'+div_id+ ' .split_sel_price').val()).toFixed(2));
      // console.log(prodt_amt);
       //console.log($('#'+div_id+ ' #split_seld_qty').val());
      // console.log("----"); 
       sel_prodts[$(this).attr('cartkey')] = $('#'+div_id+ ' #split_seld_qty').val();  
    });
   // console.log(splittedtotal+prodt_amt); 
    
    if((splittedtotal+prodt_amt)>order_data['total_amount']){
         $('#split_warnings').html("Splitted Amount exceedes Order Total");
        return false;
    }
    //console.log(splitcounts);
    splitteddata[splitcounts]={amount:prodt_amt,products:sel_prodts,payments:{},split_type:'item'};
    //console.log(splitteddata);
    getsplitmaincart();
    splittedorderdetails();
});
$(document).on('click','#split_save_btn',function(){
    play_single_sound();
    if($(this).hasClass('splitdisabled')){
        return false;
    }
    clearpreviouspayments();
    $.ajax({
        url: 'index.php?route=epos/confirm/savesplits',
        type: 'post',
        data: {split:splitteddata},
        dataType: 'json',
        success: function(json) {
           showmainpage();
           //showeposcartpage();
        }
    });
});
$(document).on('click','#equal_split_btn',function(){
    
          if($('.split_btns').hasClass('splitdisabled')){
            return false;
          }
          $('.split_btns').removeClass('split_btn_selected');
          $(this).addClass('split_btn_selected');
          $.colorbox({ inline: true,opacity: 0,width:"320px", href: '#split_count_sel_popup', closeButton: false, escKey:false,title:'Choose No',
                onLoad: function() {
                    $("#colorbox").addClass("countseln");
                    $('.splitdot').addClass("splitdisabled");
                },
               onCleanup:function() {
                 $('#equalsplitcount').val('');
                 $('#pop_split_count').html('');
                 $("#colorbox").removeClass("countseln");
                 $('.splitdot').removeClass("splitdisabled");
               }
           });
      });
$(document).on('click','#custom_split_btn',function(){
          if($('.split_btns').hasClass('splitdisabled')){
            return false;
          }
          $('.split_btns').removeClass('split_btn_selected');
          $(this).addClass('split_btn_selected');
          $.colorbox({ inline: true,opacity: 0,width:"320px", href: '#split_count_sel_popup', closeButton: false, escKey:false,title:'Choose Amount',
                onLoad: function() {
                    $("#colorbox").addClass("amountseln");
                },
               onCleanup:function() {
                 $('#equalsplitcount').val('');
                 $('#pop_split_count').html('');
                 $("#colorbox").removeClass("amountseln");
               }
           });
         
      });
 $(document).on('click','#split-round-btn',function(){
    splittedtotal =getsplittedtotal();
    $('#split-round-bal').html(order_data['currency_symbol']+''+eval(order_data['total_amount']-splittedtotal).toFixed(2)+' Can be added as Round Off value');
    $('#split-rounoff-div').show(); 
});    
$(document).on('click','#split-round-confirm',function(){
    window.splitstatus = 1; 
    splittedtotal =getsplittedtotal();
    $('#split-round-bal').html('');
    $('#split-rounoff-div').hide();
    $('#mask').addClass('mask');
      $.ajax({
        url: 'index.php?route=epos/confirm/roundoffsplit',
        type: 'post',
        data: {roundoff_value:eval(order_data['total_amount']-splittedtotal).toFixed(2),split:splitteddata},
        dataType: 'json',
        success: function(json) {
            if(json['success']){
                clearpreviouspayments();
                $('.common-notfcn').html('');
                $('#eposmaincontent').load('index.php?route=epos/confirm',function(){
                   $('#mask').removeClass('mask');
                });
            }
        }
    });
});      
//Checkout Functions
 $(document).on('click','#coupn-rewrd-popup-btn', function() {
        play_single_sound();   
        $('#colorbox').removeClass('headerlink_popup');     
        $.colorbox({inline: true,  opacity: 0.5,width:"50%", href: ".coupon-voucher-popup",overlayClose:false,escKey:false}); 
    });
 $(document).on('click','#coupon_button', function() {
         play_single_sound();
        $('#coupon-voucher .warning').remove();
        $('#coupon-voucher .success').remove();
        $('#coupon_button').attr('disabled', true);
        $('#coupon_button').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
	$.ajax({
		url: 'index.php?route=epos/checkout/validateCoupon',
                type: 'post',
                data: $('#coupon input[type=\'text\'], #coupon input[type=\'hidden\']'),
                dataType: 'json',
                success: function(json) {
                      //  $('#confirm .checkout-content').html(html);
                      if (json['error']) {
				if (json['error']['warning']) {
					$('#coupon-voucher').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').fadeIn('slow');
				}			
		      }
                      else if(json['success']) {
                     
                            $('#coupon-voucher').prepend('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                            
			    $('.success').fadeIn('slow');
                            
			    		$('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() {
                                         cartresize();
                  
                                       });
                      }

                },
                complete: function() {
                      $('#coupon_button').attr('disabled', false);
                      $('.wait').remove();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
        });
});
 $(document).on('click','#voucher_button', function() {
         play_single_sound();
        $('#coupon-voucher .warning').remove();
        $('#coupon-voucher .success').remove();
	$.ajax({
		url: 'index.php?route=epos/checkout/validateVoucher',
                type: 'post',
                data: $('#voucher input[type=\'text\'], #voucher input[type=\'hidden\']'),
                dataType: 'json',
                success: function(json) {
                      //  $('#confirm .checkout-content').html(html);
                      if (json['error']) {
				if (json['error']['warning']) {
					$('#coupon-voucher').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').fadeIn('slow');
				}			
		      }
                      else if(json['success']) {
                     
                            $('#coupon-voucher').prepend('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                            
			    $('.success').fadeIn('slow');
                            
			    		$('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() {
                                           cartresize();
                                        });
                      }

                },
                error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
        });
});
 $(document).on('click','#reward_button', function() {
         play_single_sound();
        $('#coupon-voucher .warning').remove();
        $('#coupon-voucher .success').remove();
	$.ajax({
		url: 'index.php?route=epos/checkout/validateReward',
                type: 'post',
                data: $('#reward input[type=\'text\'], #reward input[type=\'hidden\']'),
                dataType: 'json',
                success: function(json) {
                      //  $('#confirm .checkout-content').html(html);
                      if (json['error']) {
				if (json['error']['warning']) {
					$('#coupon-voucher').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').fadeIn('slow');
				}			
		      }
                      else if(json['success']) {
                     
                            $('#coupon-voucher').prepend('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                            
			    $('.success').fadeIn('slow');
                            
			    $('#cart').load(url + 'index.php?route=epos/cart #cart > *', function() {
                                 cartresize();
                            });
                      }

                },
                error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
        });
});  
 $(document).on('click','.del_route_close', function(){
   $.colorbox.close(); 
});    
//optval_item_pgn
$(document).on('click','.optval_item_pgn', function(){
    $('.optval_item_pgn').removeClass('selected');
    $(this).addClass('selected');
    $('#option-'+$(this).attr('prodt_optid')+' .proptionul').hide();//prodt_optid
    $('.optvalrow-'+$(this).attr('page_id')).show();
});
//pendorderselect
$(document).on('click','.pendorderselect', function(){
    if($(this).hasClass('selected')){
        $(this).removeClass('selected');
        $(this).html('<i class="fa fa-square-o"></i>');
    }else{
        $(this).addClass('selected');
        $(this).html('<i class="fa fa-check-square"></i>');
    }
});
$(document).on('click','.pendorderupdate', function(){
    pendorderids = new Array();
    selcount=0;
    $('.pendorderselect.selected').each(function(index, element) {
        pendorderids[selcount] = $(this).attr('order_id');
        selcount ++;
    });
    if(pendorderids.length<=0){
        return false;
    }
    $.ajax({
        url: 'index.php?route=epos/epos/changependingorderstaus' ,
        dataType: 'json',
        type: 'post',
        data: {order_ids: pendorderids},
        success: function(json) {
            getpendingorderlist();
        }
    });
});
});
/*
function initialize() {
    if (GBrowserIsCompatible()) {     
        map = new GMap2(document.getElementById("map_canvas"));
        gdir = new GDirections(map, document.getElementById("directions"));
        //GEvent.addListener(gdir, "addoverlay", onGDirectionsLoad);
        GEvent.addListener(gdir, "error", handleErrors);
    }
}
function handleErrors(){
   if (gdir.getStatus().code == G_GEO_UNKNOWN_ADDRESS)
     alert("No corresponding geographic location could be found for one of the specified addresses. This may be due to the fact that the address is relatively new, or it may be incorrect.\nError code: " + gdir.getStatus().code);
   else if (gdir.getStatus().code == G_GEO_SERVER_ERROR)
     alert("A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known.\n Error code: " + gdir.getStatus().code);
   else if (gdir.getStatus().code == G_GEO_MISSING_QUERY)
     alert("The HTTP q parameter was either missing or had no value. For geocoder requests, this means that an empty address was specified as input. For directions requests, this means that no query was specified in the input.\n Error code: " + gdir.getStatus().code);
   else if (gdir.getStatus().code == G_GEO_BAD_KEY)
     alert("The given key is either invalid or does not match the domain for which it was given. \n Error code: " + gdir.getStatus().code);
   else if (gdir.getStatus().code == G_GEO_BAD_REQUEST)
     alert("A directions request could not be successfully parsed.\n Error code: " + gdir.getStatus().code);
   else alert("An unknown error occurred.");
    }
function setDirections(fromAddress, toAddress, locale) {
      gdir.load("from: " + fromAddress + " to: " + toAddress,
                { "locale": locale });
}
*/

function initialize() {
  directionsDisplay = new google.maps.DirectionsRenderer();
  var chicago = new google.maps.LatLng(postcode_lattd, postcode_longtd);
  var mapOptions = {
    zoom:7,
    center: chicago
  };
  map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
  directionsDisplay.setMap(map);
  $('#directions').html('');
  directionsDisplay.setPanel(document.getElementById('directions'));
}

function calcRoute(fromAddress, toAddress) {
  var start =  fromAddress; // document.getElementById('start').value;
  var end = toAddress; //document.getElementById('end').value;
  var request = {
      origin:start,
      destination:end,
      travelMode: google.maps.TravelMode.DRIVING,
      unitSystem: google.maps.UnitSystem.IMPERIAL
  };
  directionsService.route(request, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(response);
    }
  });
}

function setDirections(fromAddress, toAddress) {
     // gdir.load("from: " + fromAddress + " to: " + toAddress,                { "locale": locale });
                calcRoute(fromAddress, toAddress);
}

function onlineorderadd(){
    $.ajax({
		url: 'index.php?route=epos/curl',
		type: 'post',
		dataType: 'json',
                success: function(json){                   
                }
    });   
}
function showdeliveryrout(fromAddress, toAddress, locale,delperson) {
    $.colorbox({ inline: true,opacity: 0.5,height:$(window).height()-$('#footer').height()-$('#header').height(),width:"90%",href: '#del_rout_popup', closeButton: true,
                        onComplete: function(){
                           initialize();
                           $('.home_del_details').html(''+delperson+','+toAddress+'');
                           setDirections(fromAddress, toAddress);
                           $.colorbox.resize();
                        },
                        onCleanup:function(){
                            $('#colorbox').removeClass('lastorderepo');
                        }
                   });
} 
function productsearchresult(json){
    $('#p1_cache').html('');
    pro_length = $(json).length;
    displays = display_splits(pro_length, pro_display_per_items);
    var product_row = 0;
    html = '';
    products_displayul = 0;
    perrowitem = 0;
    previouscontent = '';
    singlerowhtml ='';
    if(json==''){
        noitemhtml ='';
        for(p=2;p<(pro_display_per_items);p++){
           noitemhtml +='<li class="prodt-non-item"></li>';
        }
        noitemhtml += '<li class="product-previouscell  prodt-disabled">&#8249;</li>';       
        noitemhtml += '<li class="product-nextcell  prodt-disabled">&#8250;</li>';
        //$('#p1_cache').append('<ul id="p_cache_0">' +noitemhtml+'</ul>');
         $('#p1').html(noitemhtml);
        return 0;
    }
    for (i in json) {
        product = json[i];
         prodt_back= '';
        html += '<li class="product-cell ' + product_row + '" id="c1_0' + product['product_id'] + '">';
        if (product['pos_image']) {
            prodt_back ='style=background:url("'+product['pos_image']+'")';
            html +='<div class="prodt_itemhead">';
            html +='  <span class="center product-name">' + product['name'] + '</span>';

            html +='</div>'; 
            html += '<span class="center product-price">' + product['price'] + '</span>';
            html += '<div class="prodt_itemdet" '+prodt_back+'></div>';
        }
        else {
            html +='<div class="prodt_itemnameonly">' + product['name'] + '</div>'; 
            html +='<span class="center product-price">' + product['price'] + '</span>';
        }
        html += '  <input type="hidden" name="product_id" value="' + product['product_id'] + '" />';
        if (product['option'] != '' || product['special_deal'] == 1) {
            html += '  <input type="hidden" name="option" value="1" />';
        }
        else {
            html += '  <input type="hidden" name="option" value="0" />';
        }

        html += '</li>';
        product_row += 1;
        perrowitem++;
        
        if (displays[products_displayul] == perrowitem) {
            if (products_displayul == 0 && displays[products_displayul] != pro_display_per_items) {
                html += '<li class="product-previouscell ' + product_row + ' prodt-disabled">&#8249;</li>';
                html += '<li class="product-nextcell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul + 1) + '\').html());">&#8250;</li>';
            }
            else if (products_displayul != 0 && displays[products_displayul] == (pro_display_per_items - 1)) {
                html += '<li class="product-previouscell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul - 1) + '\').html());">&#8249;</li>';
            }
            else if (products_displayul != 0 && displays[products_displayul] == (pro_display_per_items - 2)) {
               html += '<li class="product-previouscell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul - 1) + '\').html());">&#8249;</li>';
                if(displays.length==(products_displayul+1)){
                     html += '<li class="product-nextcell ' + product_row + ' prodt-disabled">&#8250;</li>';
                }else{
                     html += '<li class="product-nextcell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul + 1) + '\').html());">&#8250;</li>';
                }
                
               
            }
            $('#p1_cache').append('<ul id="p_cache_' + products_displayul + '">' + previouscontent + html + singlerowhtml+'</ul>');
            html = '';
            perrowitem = 0;
            products_displayul++;
        }
    }
    if(displays.length==1){
        for(p=2;p<(pro_display_per_items-perrowitem);p++){
           singlerowhtml +='<li class="prodt-non-item"></li>';
        }
        singlerowhtml += '<li class="product-previouscell ' + product_row + ' prodt-disabled">&#8249;</li>';       
        singlerowhtml += '<li class="product-nextcell ' + product_row + ' prodt-disabled">&#8250;</li>';
    }
    if (html.length > 0) {
        if (products_displayul == 0 && displays[products_displayul] != pro_display_per_items) {
            html += '<li class="product-previouscell ' + product_row + ' prodt-disabled">&#8249;</li>';
            html += '<li class="product-nextcell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul + 1) + '\').html());">&#8250;</li>';
        }
        else if (products_displayul != 0 && displays[products_displayul] == (pro_display_per_items - 1)) {
            html += '<li class="product-previouscell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul - 1) + '\').html());">&#8249;</li>';
        }
        else if (products_displayul != 0 && displays[products_displayul] == (pro_display_per_items - 2)) {
             for(p=0;p<(displays[products_displayul]-perrowitem);p++){
                 html +='<li class="prodt-non-item"></li>';
             }
            html += '<li class="product-previouscell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul - 1) + '\').html());">&#8249;</li>';
            if(displays.length==(products_displayul+1)){
                 html += '<li class="product-nextcell ' + product_row + ' prodt-disabled">&#8250;</li>';
            }else{
                html += '<li class="product-nextcell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul + 1) + '\').html());">&#8250;</li>';
            } 
            
        }
        $('#p1_cache').append('<ul id="p_cache_' + products_displayul + '">' + previouscontent+html + singlerowhtml+ '</ul>');

    }

    $('#p1').html($('#p_cache_0').html());
}
function focusIt(t) {
        // define where the cursor is to write character clicked.
         jsKeyboard.currentElement = $(t);
         jsKeyboard.show();
}
function showKeyboard(id,fun) {
        onscreenkeyboard(id);
        return false;
         clean($("#keyboard_text"));
         jsKeyboard.keyboardelement=$('#'+id);
         jsKeyboard.currentElement = $("#keyboard_text");
         jsKeyboard.show();
         jsKeyboard.currentElement.focus(); 
         jsKeyboard.autocomplete=fun;
}
var isCleaned = false;
function clean(t) {
         if (!isCleaned) {
             $(t).text("");
             isCleaned = true;
         }
}
function getcartheght(){
    p=$('.mini-cart-info').position();
    t=$('.mini-cart-total').position();
    //alert(parseInt(t.top-p.top-35));
    if(t){
        return parseInt(t.top-p.top-35);
    }else{
        return 200;
    }
    
}
function getpopuppositions(bundle){
    lef_top_pos = $('.maintable .eposrightside').position();
    postn = { 'top':parseInt(lef_top_pos.top),'left':parseInt(lef_top_pos.left) ,'width':parseInt($(window).width()-lef_top_pos.left-10),'height':parseInt($(window).height()-$('#header').height()-$('#footer').height()-10)};
    return postn;
}
function changeCategorydisplay(action){
        if(action=='plus'){
            $('#cat-display-count').val(parseInt($('#cat-display-count').val())+1);
        }else{
            $('#cat-display-count').val(parseInt($('#cat-display-count').val())-1);
        }
        cat_display_per_items = $('#cat-display-count').val();
        categories(0);
        
    }
function changeProductdisplay(action){
        if(action=='plus'){
             $('#prodt-display-count').val(parseInt($('#prodt-display-count').val())+1);
        }else{
             $('#prodt-display-count').val(parseInt($('#prodt-display-count').val())-1);
        }
        pro_display_per_items = $('#prodt-display-count').val();
        products_load(0)
    }
function changeTableheight(action){
    if(action=='plus'){
        $('#table-display-height').val(parseInt($('#table-display-height').val())+1);
    }else{
        $('#table-display-height').val(parseInt($('#table-display-height').val())-1);
    }
        table_disp_height = $('#table-display-height').val();
    getliveorders();
    getmainpagequickbuttons();
        
}    
function changeTablewidth(action){
        if(action=='plus'){
            $('#table-display-width').val(parseInt($('#table-display-width').val())+1);
        }else{
            $('#table-display-width').val(parseInt($('#table-display-width').val())-1);
        }
        table_disp_width = $('#table-display-width').val();
    getliveorders();
    getmainpagequickbuttons();
}     
function display_splits(lengthr, cat_display_per_items) {
    var displays = new Array();
    j = 0;
    while (lengthr > 0) {
        if (j == 0 && lengthr <= cat_display_per_items) {//1 row
            displays[j] = cat_display_per_items;
            lengthr = 0;
           // alert(cat_display_per_items);
        }
        else if (j == 0 && lengthr > cat_display_per_items) {
            displays[j] = cat_display_per_items - 2;
            lengthr = lengthr - (cat_display_per_items - 2)
        }
        else if (j != 0 && lengthr <= (cat_display_per_items - 2)) {
            displays[j] = cat_display_per_items - 2;
            lengthr = lengthr - (cat_display_per_items - 2)
        }
        else {
            //alert(j);
            displays[j] = cat_display_per_items - 2;
            lengthr = lengthr - (cat_display_per_items - 2)
        }
        //  alert ( '' + j + ':' + lengthr + ' - ' + displays[j]);
        j++;
    }
    
    return displays;
}
function products_load(cat_id) {
    if (typeof (Storage) !== "undefined") {
        if (typeof (localStorage['lPo_' + cat_id]) !== "undefined") {
            json = JSON.parse(localStorage['lPo_' + cat_id]);
            products = [];
            for (i in json) {
                products[i] = JSON.parse(localStorage['lP_' + json[i]]);
            }
            products_html(products);
            return;
        }
    }
    $.ajax({
        url: 'index.php?route=epos/epos/jsonautocomplete&filter_category_id=' + cat_id,
        dataType: 'json',
        success: function(json) {
            products_html(json);
        }
    });
}
function products_html(json) {
    $('#p1_cache').html('');
    pro_length = $(json).length;
    displays = display_splits(pro_length, pro_display_per_items);
    var product_row = 0;
    html = '';
    products_displayul = 0;
    perrowitem = 0;
    previouscontent = '';
    singlerowhtml ='';
    if(json==''){
        noitemhtml ='';
        for(p=2;p<(pro_display_per_items);p++){
           noitemhtml +='<li class="prodt-non-item"></li>';
        }
        noitemhtml += '<li class="product-previouscell  prodt-disabled">&#8249;</li>';       
        noitemhtml += '<li class="product-nextcell  prodt-disabled">&#8250;</li>';
        //$('#p1_cache').append('<ul id="p_cache_0">' +noitemhtml+'</ul>');
         $('#p1').html(noitemhtml);
        return 0;
    }
    for (i in json) {
        product = json[i];
        prodt_back= '';
        html += '<li class="product-cell ' + product_row + '" id="c1_0' + product['product_id'] + '">';
        if (product['pos_image']) {
            prodt_back ='style=background:url("'+product['pos_image']+'")';
            html +='<div class="prodt_itemhead">';
            html +='  <span class="center product-name">' + product['name'] + '</span>';

            html +='</div>'; 
            html += '<span class="center product-price">' + product['price'] + '</span>';
            html += '<div class="prodt_itemdet" '+prodt_back+'></div>';
        }
        else {
            html +='<div class="prodt_itemnameonly">' + product['name'] + '</div>'; 
            html +='<span class="center product-price">' + product['price'] + '</span>';
        }
        html += '  <input type="hidden" name="product_id" value="' + product['product_id'] + '" />';
        if (product['option'] != '' || product['special_deal'] == 1) {
            html += '  <input type="hidden" name="option" value="1" />';
        }
        else {
            html += '  <input type="hidden" name="option" value="0" />';
        }
         html += '</li>';
        product_row += 1;
        perrowitem++;
        
        if (displays[products_displayul] == perrowitem) {
            if (products_displayul == 0 && displays[products_displayul] != pro_display_per_items) {
                html += '<li class="product-previouscell ' + product_row + ' prodt-disabled">&#8249;</li>';
                html += '<li class="product-nextcell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul + 1) + '\').html());">&#8250;</li>';
            }
            else if (products_displayul != 0 && displays[products_displayul] == (pro_display_per_items - 1)) {
                html += '<li class="product-previouscell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul - 1) + '\').html());">&#8249;</li>';
            }
            else if (products_displayul != 0 && displays[products_displayul] == (pro_display_per_items - 2)) {
               html += '<li class="product-previouscell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul - 1) + '\').html());">&#8249;</li>';
                if(displays.length==(products_displayul+1)){
                     html += '<li class="product-nextcell ' + product_row + ' prodt-disabled">&#8250;</li>';
                }else{
                     html += '<li class="product-nextcell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul + 1) + '\').html());">&#8250;</li>';
                }
            }
            $('#p1_cache').append('<ul id="p_cache_' + products_displayul + '">' + previouscontent + html + singlerowhtml+'</ul>');
            html = '';
            perrowitem = 0;
            products_displayul++;
        }
        
    }
    if(displays.length==1){
        for(p=2;p<(pro_display_per_items-perrowitem);p++){
           singlerowhtml +='<li class="prodt-non-item"></li>';
        }
        singlerowhtml += '<li class="product-previouscell ' + product_row + ' prodt-disabled">&#8249;</li>';       
        singlerowhtml += '<li class="product-nextcell ' + product_row + ' prodt-disabled">&#8250;</li>';
    }
    if (html.length > 0) {
        if (products_displayul == 0 && displays[products_displayul] != pro_display_per_items) {
            html += '<li class="product-previouscell ' + product_row + ' prodt-disabled">&#8249;</li>';
            html += '<li class="product-nextcell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul + 1) + '\').html());">&#8250;</li>';
        }
        else if (products_displayul != 0 && displays[products_displayul] == (pro_display_per_items - 1)) {
            html += '<li class="product-previouscell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul - 1) + '\').html());">&#8249;</li>';
        }
        else if (products_displayul != 0 && displays[products_displayul] == (pro_display_per_items - 2)) {
             for(p=0;p<(displays[products_displayul]-perrowitem);p++){
                 html +='<li class="prodt-non-item"></li>';
             }
            html += '<li class="product-previouscell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul - 1) + '\').html());">&#8249;</li>';
            if(displays.length==(products_displayul+1)){
                 html += '<li class="product-nextcell ' + product_row + ' prodt-disabled">&#8250;</li>';
            }else{
                html += '<li class="product-nextcell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (products_displayul + 1) + '\').html());">&#8250;</li>';
            } 
        }
        $('#p1_cache').append('<ul id="p_cache_' + products_displayul + '">' + previouscontent+html + singlerowhtml+ '</ul>');
    }
    $('#p1').html($('#p_cache_0').html());
}
function categories(cat_id) {
    if (typeof (Storage) !== "undefined") {
        if (typeof (localStorage['lCa_' + cat_id]) !== "undefined") {
            json = JSON.parse(localStorage['lCa_' + cat_id]);
            categories_html(json, cat_id);
            return 0;
        }
    }
    data = {};
    if (cat_id != 0) {
        data = {'category_id': cat_id};

    }
    $.ajax({
        url: 'index.php?route=epos/epos/jsoncategories&category_id='+cat_id,
        dataType: 'json',
        success: function(json) {
            categories_html(json, cat_id);            
        }
    });
}
function categories_html(json, cat_id) {
    //cat_display_per_items=14
    
    if(cat_id != 0 && pos_subcategory_show==0){
        return false;;
    }
   
    cat_length = $(json['categories']).length;
    displays = display_splits(cat_length, cat_display_per_items);
    var category_row = 0;
    html = '';
    cat_displayul = 0;
    catperrowitem = 0;
    previouscontent = '';
    singlerowhtml ='';
    catrowcount = 0;
    catitemcount =0;
    subcat_class = '';
       
   
    //return false;
    if (json && json['categories']) {
        $('#c1_cache').html('');
    if (cat_id != 0) {
        subcat_class = 'sub_category';
            category_row += 1;
            catperrowitem++;
            html += '<li class="category-cell-back  ' + subcat_class + '" id="cat_sub_back" onclick="categories(0);"><i class="fa fa-reply"></i></li>';
    }
        //html = '<li class="category-nextcell cat_prev cat-disbled"><span class="cat_arrow">&#8249;</span></li>';//
        
        for (i in json['categories']) {
            //itemcount++;
            cat_back = '';
            category = json['categories'][i];          
            html += '<li class="category-cell  ' + subcat_class + '" cat_id="'+category['category_id'] +'" id="c1_' + category['category_id'] + '">';
            
             if (category['pos_image']) {
                cat_back ='style=background:url("'+category['pos_image']+'")';
                html += '<div class="cat_itemhead">';
                html += '<span class="center category-name">' + category['name'] + '</span>';
                html += '</div>';
                html += '<div class="cat_itemdet" '+cat_back+'>';
                html += '</div>';
            }
            else {
                html += '<div class="cat_itemheadonly">' + category['name'] + '</div>';
            }
            
            html += '  <input type="hidden" name="category_id" value="' + category['category_id'] + '" />';
            html += '</li>';
            category_row += 1;
            catperrowitem++;
            if (displays[cat_displayul] == catperrowitem) {
                if (cat_displayul == 0 && displays[cat_displayul] != cat_display_per_items) {
                    //html += '<li class="product-previouscell ' + product_row + ' prodt-disabled">&#8249;</li>';
                    html += '<li class="category-nextcell cat_prev '+category_row+' cat-disbled"><span class="cat_arrow">&#8249;</span></li>';//
                    //html += '<li class="product-nextcell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (cat_displayul + 1) + '\').html());">&#8250;</li>';
                    html += '<li class="category-nextcell  cat_next '+category_row+'" onClick="$(\'#c1\').html( $(\'#c_cache_' + (cat_displayul + 1) + '\').html());"><span class="cat_arrow">&#8250;</span></li>';
                }
                else if (cat_displayul != 0 && displays[cat_displayul] == (cat_display_per_items - 1)) {
                   // html += '<li class="product-previouscell ' + product_row + '" onClick="$(\'#p1\').html( $(\'#p_cache_' + (cat_displayul - 1) + '\').html());">&#8249;</li>';
                    html += '<li class="category-nextcell cat_prev ' + category_row + '" onClick="$(\'#c1\').html( $(\'#c_cache_' + (cat_displayul - 1) + '\').html());">&#8249;</li>';
                }
                else if (cat_displayul != 0 && displays[cat_displayul] == (cat_display_per_items - 2)) {
                   html += '<li class="category-nextcell cat_prev ' + category_row + '" onClick="$(\'#c1\').html( $(\'#c_cache_' + (cat_displayul - 1) + '\').html());">&#8249;</li>';
                    if(displays.length==(cat_displayul+1)){
                         html += '<li class="category-nextcell  cat_next '+category_row+' cat-disabled">&#8250;</li>';
                    }else{
                         html += '<li class="category-nextcell  cat_next '+category_row+'" onClick="$(\'#c1\').html( $(\'#c_cache_' + (cat_displayul + 1) + '\').html());">&#8250;</li>';
                    }
                }
                $('#c1_cache').append('<ul id="c_cache_' + cat_displayul + '">' + previouscontent + html + singlerowhtml+'</ul>');
                html = '';
                catperrowitem = 0;
                cat_displayul++;
            }
            /*
            if(itemcount%cat_display_per_items==0){
                
                if(itemcount<cat_length){
                    
                    html += '<li class="category-nextcell  cat_next" onClick="$(\'#c1\').html( $(\'#c_cache_' + (rowcount + 1) + '\').html());"><span class="cat_arrow">&#8250;</span></li>';
                    $('#c1_cache').append('<ul id="c_cache_' + rowcount + '">' + html + '</ul>');
                    html = '';
                    rowcount++;
                    html += '<li class="category-nextcell  cat_prev" onClick="$(\'#c1\').html( $(\'#c_cache_' + (rowcount - 1) + '\').html());"><span class="cat_arrow">&#8249;</span></li>';
                }else{
                     html += '<li class="category-nextcell  cat_next cat-disbled"><span class="cat_arrow">&#8250;</span></li>'
                     $('#c1_cache').append('<ul id="c_cache_' + rowcount + '">' + html + '</ul>');
                     //html = '';
                }
            }
            */
        }
        /*if(displays.length==1){
            for(p=2;p<(cat_display_per_items-catperrowitem);p++){
               singlerowhtml +='<li class="cat-non-item"></li>';
            }
            singlerowhtml += '<li class="category-nextcell  cat_prev ' + category_row + ' cat-disbled">&#8249;</li>';       
            singlerowhtml += '<li class="category-nextcell  cat_next ' + category_row + ' cat-disbled">&#8250;</li>';
        }*/
    if (html.length > 0) {
        if (cat_displayul == 0 && displays[cat_displayul] != cat_display_per_items) {
            html += '<li class="category-nextcell  cat_prev ' + category_row + ' cat-disbled">&#8249;</li>';
            html += '<li class="category-nextcell  cat_next ' + category_row + '" onClick="$(\'#c1\').html( $(\'#c_cache_' + (cat_displayul + 1) + '\').html());">&#8250;</li>';
        }
        else if (cat_displayul != 0 && displays[cat_displayul] == (cat_display_per_items - 1)) {
            html += '<li class="category-nextcell  cat_prev ' + category_row + '" onClick="$(\'#c1\').html( $(\'#c_cache_' + (cat_displayul - 1) + '\').html());">&#8249;</li>';
        }
        else if (cat_displayul != 0 && displays[cat_displayul] == (cat_display_per_items - 2)) {
             for(p=0;p<(displays[cat_displayul]-catperrowitem);p++){
                 html +='<li class="cat-non-item"></li>';
             }
            html += '<li class="category-nextcell  cat_prev ' + category_row + '" onClick="$(\'#c1\').html( $(\'#c_cache_' + (cat_displayul - 1) + '\').html());">&#8249;</li>';
            if(displays.length==(cat_displayul+1)){
                 html += '<li class="category-nextcell  cat_prev ' + category_row + ' cat-disbled">&#8250;</li>';
            }else{
                html += '<li class="category-nextcell  cat_next ' + category_row + '" onClick="$(\'#c1\').html( $(\'#c_cache_' + (cat_displayul + 1) + '\').html());">&#8250;</li>';
            } 
        }
        $('#c1_cache').append('<ul id="c_cache_' + cat_displayul + '">' + previouscontent+html + singlerowhtml+ '</ul>');
    }
        /*
        if(itemcount%cat_display_per_items!=0 ){
           for(c=0;c<(cat_display_per_items-(itemcount%cat_display_per_items));c++){
                html +='<li class="cat-non-item"></li>';
           } 
           html += '<li class="category-nextcell  cat_next cat-disbled"><span class="cat_arrow">&#8250;</span></li>'
           $('#c1_cache').append('<ul id="c_cache_' + rowcount + '">' + html + '</ul>');
        }
        */
         $('#c1').html($('#c_cache_0').html());
    }
}
function addtocart_epos() {
    play_single_sound();
    $.ajax({
        url: url + 'index.php?route=epos/cart/add&customertype='+$('#customertype').val(),
        type: 'post',
        data: $('#tab-product-epos input[type=\'text\'], #tab-product-epos input[type=\'hidden\'], #tab-product-epos input[type=\'radio\']:checked, #tab-product-epos input[type=\'checkbox\']:checked, #tab-product-epos select, #tab-product-epos textarea '),
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
                $('#cart').html(json['success']);
                cartresize();
                smartoffers();
                /* // */
                $('#product-epos-bundled-products').html('');
                $('#product-epos-bundled-product-options').html('');
                // Load Home page
                if(pos_product_homepage!=0){
                categories(0);
                products_load(0);
                }
                $('.paymentmethod-cell').removeClass('notallowed');
                $('.shippingmethod-cell').removeClass('notallowed');
            }
        }
    });
}
function product_options(product_id) {
    if (typeof (Storage) !== "undefined") {
        if (typeof (localStorage['lPrOp_' + product_id]) !== "undefined") {
            json = JSON.parse(localStorage['lPrOp_' + product_id]);
            products_option_html(json);
            return;
        }
    }
    $.ajax({
        url: 'index.php?route=epos/epos/jsonproduct&product_id=' + product_id,
        dataType: 'json',
        success: function(json) {
            products_option_html(json);
        }
    });
}
function products_option_html(json) {
    //console.log(prodoption_dispaly_items);
    prodoption_dispaly_items =6;
    tot_opt_count = 0;
    colorbox_pos = getpopuppositions();
    pop_max_height = colorbox_pos.height;//TODO Dynamic add in fn (600)
    opt_header_height = 20;//$('#product-epos-popupbox .optionname').css("height");
    opt_height =55;// $('#product-epos-popupbox li').css("height");
    cal_height = 0;
    pop_page_count = 1;
    opt_tab_height=10;
    firstoptstatus = false; 
    optioncal_height =0;
    if (json[0]['option']) {
        html = '';
        total_options = json[0]['option'].length;
        for (i in json[0]['option']) {
             option = json[0]['option'][i];
            if(option['grouped_status']==1){
                groption = option['option_groups'][0];
                option_per_item = Math.ceil(groption['option_value'].length/prodoption_dispaly_items);
                //console.log(option_per_item);
                cal_height += (option_per_item * opt_height) + 50 + (option_per_item+1)*3 +opt_tab_height; 
                if((cal_height+100)>=pop_max_height){
                    if(i==1){
                        firstoptstatus=true;
                    }
                 html +='<div class="main_options opt_pop_page_'+pop_page_count+'  epos-propt-btns">';   
                   if(pop_page_count>1){
                         html +='<div class="left"><a onclick="showpreviousoptionpage('+pop_page_count+')" class="prev-prodt-opt-btn button popsmall_btn playsound"><i class="fa fa-caret-square-o-left"></i>PREVIOUS</a></div>';
                   } 
                   html +='<div class="right"><a onclick="shownextoptionpage('+pop_page_count+')" class="next-prodt-opt-btn button popsmall_btn playsound"><i class="fa fa-caret-square-o-right"></i>NEXT</a></div>';
                   pop_page_count +=1;
                   cal_height  = (option_per_item * opt_height) + 50 + (option_per_item+1)*3 +opt_tab_height ;
                  html +='</div>';  
                }
            }else{
               
                option_per_item = Math.ceil(option['option_value'].length/prodoption_dispaly_items);
                //console.log(option_per_item);
                cal_height += (option_per_item * opt_height) + opt_header_height + (option_per_item+1)*3 +opt_tab_height; 
                if((cal_height+100)>=pop_max_height){
                    if(i==1){
                        firstoptstatus=true;
                    }
                    html +='<div class="main_options opt_pop_page_'+pop_page_count+'  epos-propt-btns">';   
                   if(pop_page_count>1){
                         html +='<div class="left"><a onclick="showpreviousoptionpage('+pop_page_count+')" class="prev-prodt-opt-btn button popsmall_btn playsound"><i class="fa fa-caret-square-o-left"></i>PREVIOUS</a></div>';
                   } 
                    html +='<div class="right"><a onclick="shownextoptionpage('+pop_page_count+')" class="next-prodt-opt-btn button popsmall_btn playsound"><i class="fa fa-caret-square-o-right"></i>NEXT</a></div>';
                    pop_page_count +=1;
                    cal_height  = (option_per_item * opt_height) + opt_header_height + (option_per_item+1)*3 +opt_tab_height ;
                    html +='</div>';  
                }
            }
            if(option['grouped_status']==1){
                optionvalue_row = 1;
                html += '<div id="option-' + option['product_option_id'] + '" class="main_options  opt_pop_page_'+pop_page_count+'">';
                for(g in option['option_groups']){
                    groupdetails =option['option_groups'][g];
                    grpclass ='';
                    if(g==0){
                        grpclass='group_selected';
                    }
                   // cal_height +=50;
                    html += '<span id="optiongroupnames_'+i+'_'+g+'" class="optionname optiongroup optiongroupnames_'+i+' '+grpclass+'" onclick="showoptiongroups('+i+','+g+')">'+groupdetails['name']+'</span>';
                }
                 for(g in option['option_groups']){
                    groption =option['option_groups'][g];
                    
                    dispstyle='block';
                    if(g!=0){
                        dispstyle='none';
                    }
                    if (groption['type'] == 'select' || groption['type'] == 'radio' || groption['type'] == 'checkbox')  {
                        // html += '<div id="option-' + option['product_option_id'] + '" class="main_options ' + required + ' opt_pop_page_'+pop_page_count+'">';
                             html += '<ul class="Po1 optgroups_'+i+' optgroup_'+i+'_'+g+' opgroupopnseltd" style="display:'+dispstyle+'" id="option[' + groption['product_option_id'] + ']">';
                // opt_val_count = 1;
                opt_row = 1 ;
                for (j = 0; j < groption['option_value'].length; j++) {
                    if(j!==0 && (j+1)%prodoption_dispaly_items===0){
                        optioncal_height += opt_height+5 ; 
                    }
                    if((optioncal_height+100+opt_height)>=pop_max_height){
                        optionvalue_row +=1;
                        html +='</ul>';
                        optioncal_height = opt_header_height + (option_per_item+1)*3 +opt_tab_height; 
                        html += '<ul style="display:none" class="Po1 optvalrow-'+groption['product_option_id']+'-'+optionvalue_row+'  proptionul" id="option[' + groption['product_option_id'] + ']">';
                    }
                    option_value = groption['option_value'][j];
                    if (groption['type'] == 'checkbox') {
                        html += '<li class="multi-product-option proptionrow_'+tot_opt_count+'_'+opt_row+' proptiorows" parentid="' + groption['product_option_id'] + '" ProductOptionvalueID="' + option_value['product_option_value_id'] + '">' + option_value['name'];
                        html += '<input type="hidden" name="option[' + groption['product_option_id'] + '][]" value="' + option_value['product_option_value_id'] + '" />';
                    }
                    else {
                        html += '<li class="single-product-option proptionrow_'+tot_opt_count+'_'+opt_row+' proptiorows" parentid="' + option['product_option_id'] + '" ProductOptionvalueID="' + option_value['product_option_value_id'] + '">' + option_value['name'];
                        html += '<input type="hidden" name="option[' + groption['product_option_id'] + ']" value="' + option_value['product_option_value_id'] + '" />';
                    }
                    if (option_value['price']) {
                        html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
                    }

                    html += '</li>';
                }

                html += '</ul>';
                if(optionvalue_row>1){
                html +='<div class="optionvalue_pagination">';
                    for(ov=1;ov<=optionvalue_row;ov++){
                        selclass ='';
                        if(ov==1){
                            selclass="selected";
                        }
                        html +='<span page_id="'+groption['product_option_id']+'-'+ov+'" prodt_optid="'+groption['product_option_id']+'"  class="optval_item_pgn '+selclass+'">'+ov+'</span>'; 
                    }
                    html +='</div>'
                }
                //New
                html += '</div>';
                // html += '<br />';
            }
                    tot_opt_count +=1;
                    if(pop_page_count>1 && tot_opt_count==total_options){
                          html +='<div class="main_options opt_pop_page_'+pop_page_count+' epos-propt-btns"><div class="left"><a onclick="showpreviousoptionpage('+pop_page_count+')" class="prev-prodt-opt-btn button popsmall_btn playsound"><i class="fa fa-caret-square-o-left"></i>PREVIOUS</a></div></div>';
                     } 
                 }
            }
            else{
                if (option['type'] == 'select' || option['type'] == 'radio' || option['type'] == 'checkbox')  {
                required = '';
                requiredhtml = '';
                if (option['required'] == 1) {
                    requiredhtml += '<span class="required"><i class="fa fa-star"></i></span> ';
                    required = 'requiredoption';
                }
                
                html += '<div id="option-' + option['product_option_id'] + '" class="main_options ' + required + ' opt_pop_page_'+pop_page_count+'">';
                html += '<span class="optionname">' + requiredhtml + option['name'] + '</span>';
                //New
                optionvalue_row = 1;
                optioncal_height = opt_header_height + (option_per_item+1)*3 +opt_tab_height; 
                html += '<ul class="Po1 optvalrow-'+option['product_option_id']+'-'+optionvalue_row+' proptionul" id="option[' + option['product_option_id'] + ']">';
                // opt_val_count = 1;
                opt_row = 1 ;
                for (j = 0; j < option['option_value'].length; j++) {
                    if(j!==0 && (j+1)%prodoption_dispaly_items===0){
                        optioncal_height += opt_height+5 ; 
                    }
                    if((optioncal_height+100+opt_height)>=pop_max_height){
                        optionvalue_row +=1;
                        html +='</ul>';
                        optioncal_height = opt_header_height + (option_per_item+1)*3 +opt_tab_height; 
                        html += '<ul style="display:none" class="Po1 optvalrow-'+option['product_option_id']+'-'+optionvalue_row+'  proptionul" id="option[' + option['product_option_id'] + ']">';
                    }
                    option_value = option['option_value'][j];
                    if (option['type'] == 'checkbox') {
                        html += '<li class="multi-product-option proptionrow_'+tot_opt_count+'_'+opt_row+' proptiorows" parentid="' + option['product_option_id'] + '" ProductOptionvalueID="' + option_value['product_option_value_id'] + '">' + option_value['name'];
                        html += '<input type="hidden" name="option[' + option['product_option_id'] + '][]" value="' + option_value['product_option_value_id'] + '" />';
                    }
                    else {
                        html += '<li class="single-product-option proptionrow_'+tot_opt_count+'_'+opt_row+' proptiorows" parentid="' + option['product_option_id'] + '" ProductOptionvalueID="' + option_value['product_option_value_id'] + '">' + option_value['name'];
                        html += '<input type="hidden" name="option[' + option['product_option_id'] + ']" value="' + option_value['product_option_value_id'] + '" />';
                    }
                    if (option_value['price']) {
                        html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
                    }

                    html += '</li>';
                }

                html += '</ul>';
                if(optionvalue_row>1){
                html +='<div class="optionvalue_pagination">';
                    for(ov=1;ov<=optionvalue_row;ov++){
                        selclass ='';
                        if(ov==1){
                            selclass="selected";
                        }
                        html +='<span page_id="'+option['product_option_id']+'-'+ov+'" prodt_optid="'+option['product_option_id']+'"  class="optval_item_pgn '+selclass+'">'+ov+'</span>'; 
                    }
                    html +='</div>'
                }
                //New
                html += '</div>';
               
                    
                
               
                // html += '<br />';
            }

            if (option['type'] == 'image') {
                html += '<div id="option-' + option['product_option_id'] + '">';
                if (option['required']) {
                    html += '<span class="required"><i class="fa fa-star"></i></span> ';
                }

                html += option['name'] + '<br />';
                html += '<select name="option[' + option['product_option_id'] + ']">';
                html += '<option value=""><?php echo $text_select; ?></option>';
                for (j = 0; j < option['option_value'].length; j++) {
                    option_value = option['option_value'][j];
                    html += '<option value="' + option_value['product_option_value_id'] + '">' + option_value['name'];
                    if (option_value['price']) {
                        html += ' (' + option_value['price_prefix'] + option_value['price'] + ')';
                    }

                    html += '</option>';
                }

                html += '</select>';
                html += '</div>';
                // html += '<br />';
            }

            if (option['type'] == 'text') {
                html += '<div id="option-' + option['product_option_id'] + '">';
                if (option['required']) {
                    html += '<span class="required"><i class="fa fa-star"></i></span> ';
                }

                html += option['name'] + '<br />';
                html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" />';
                html += '</div>';
                //  html += '<br />';
            }

            if (option['type'] == 'textarea') {
                html += '<div id="option-' + option['product_option_id'] + '">';
                if (option['required']) {
                    html += '<span class="required"><i class="fa fa-star"></i></span> ';
                }

                html += option['name'] + '<br />';
                html += '<textarea name="option[' + option['product_option_id'] + ']" cols="40" rows="5">' + option['option_value'] + '</textarea>';
                html += '</div>';
                // html += '<br />';
            }

            if (option['type'] == 'date') {
                html += '<div id="option-' + option['product_option_id'] + '">';
                if (option['required']) {
                    html += '<span class="required"><i class="fa fa-star"></i></span> ';
                }

                html += option['name'] + '<br />';
                html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="date" />';
                html += '</div>';
                //html += '<br />';
            }

            if (option['type'] == 'datetime') {
                html += '<div id="option-' + option['product_option_id'] + '">';
                if (option['required']) {
                    html += '<span class="required"><i class="fa fa-star"></i></span> ';
                }

                html += option['name'] + '<br />';
                html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="datetime" />';
                html += '</div>';
                // html += '<br />';
            }

            if (option['type'] == 'time') {
                html += '<div id="option-' + option['product_option_id'] + '">';
                if (option['required']) {
                    html += '<span class="required"><i class="fa fa-star"></i></span> ';
                }

                html += option['name'] + '<br />';
                html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="time" />';
                html += '</div>';
                //   html += '<br />';
            }
            tot_opt_count +=1;
            if(pop_page_count>1 && tot_opt_count==total_options){
                  html +='<div class="main_options opt_pop_page_'+pop_page_count+' epos-propt-btns"><div class="left"><a onclick="showpreviousoptionpage('+pop_page_count+')" class="prev-prodt-opt-btn button popsmall_btn playsound"><i class="fa fa-caret-square-o-left"></i>PREVIOUS</a></div></div>';
             } 
            }
            
            //html += prevhtml+nexthtml;
        }
        if(firstoptstatus){
                startopt=1;
                optminus=0;
        }else{
                startopt=1;
                optminus=0;
        }
         pagehtml ='';
        if(pop_page_count>1){
            pagehtml +='<div class="optn-pge-disp">';
            
            for(p=startopt;p<=pop_page_count;p++){
                pagehtml +='<span id="optnpgeitem_'+p+'" class="optn-pge-item"><i class="fa fa-file-o"></i>'+(p-optminus)+'<input type="hidden" name="optionpgetabno" value="'+p+'"></span>';
            }
            pagehtml +='</div>';
        }
        if (json[0]['bundled_items'])  {

            var bundledidval = 1;
            var bundledcount = json[0]['bundled_items'].length;
            for (b = 0; b < json[0]['bundled_items'].length; b++) {
                qty = json[0]['bundled_items'][b]['bundled_qty'];
                if (qty > 1) {
                    bundledcount += (qty - 1);
                }
                requiredhtml = '<span class="required"><i class="fa fa-star"></i></span>';
                required = 'bund_product_required';
                for (k = 0; k < qty; k++) {
                    html += '<div id="bundled_div_' + bundledidval + '" class="bundled_products_div ">';
                    if (bundledidval < bundledcount) {
                        finsish = 0;
                    }
                    else {
                        finsish = 1;
                    }
                     bunrow=1;
                     bunprodtcount=0;
                     rowstatus = false;
                     totbubdprodutcs=json[0]['bundled_items'][b]['products'].length;
                    // html +='<div class="bundle-count-div">'+bundledidval+'/'+bundledcount+'</div>';
                    html += '<div id="bundled_prodts_' + bundledidval + '" class="' + required + '">';
                    html += '<div class="optionname">' + json[0]['bundled_items'][b]['bundle_name'] + '</div>';
                    html += '<ul class="Po1 " id="option_' + json[0]['bundled_items'][b]['bundled_id'] + '_' + k + '">';
                    tot_bun_pages= Math.ceil(json[0]['bundled_items'][b]['products'].length/bunpro_dispaly_items);
                    for (bp in json[0]['bundled_items'][b]['products']) {
                        bunprodtcount +=1;
                        html += '<li class="bundled-single-product bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+'"  id="' + json[0]['bundled_items'][b]['bundled_id'] + '_' + k + '_' + json[0]['bundled_items'][b]['products'][bp]['product_id'] + '" bund_id="' + k + '_' + $(data).val() + '_' + json[0]['bundled_items'][b]['bundled_id'] + '" div_id="' + bundledidval + '">' + json[0]['bundled_items'][b]['products'][bp]['name'];
                        html += '<input type="hidden" name="bundled_product[]" value="' + json[0]['bundled_items'][b]['bundled_id'] + '_' + k + '_' + json[0]['bundled_items'][b]['products'][bp]['product_id'] + '" />';
                        html += '<input type="hidden" name="chkbundledoptions" value="' + json[0]['bundled_items'][b]['products'][bp]['options'] + '">';
                        html += '</li>';
                      if((bunprodtcount)%bunpro_dispaly_items==0){
                          if(bunprodtcount<totbubdprodutcs){
                              if(bunrow==1){
                                 html += '<li  class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrowprev rowdisabled">&#8249;</li>'; 
                              }else{
                               html += '<li onclick="showprevbundrowprodts('+bundledidval+','+(bunrow-1)+')" class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrowprev">&#8249;</li>';   
                              }
                               html += '<li onclick="shownextbundrowprodts('+bundledidval+','+(bunrow-1)+')" class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrownext">&#8250;</li>';
                               bunrow +=1
                          }else{
                               if(bunrow==1){
                                 html += '<li  class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrowprev rowdisabled">&#8249;</li>'; 
                              }else{
                               html += '<li onclick="showprevbundrowprodts('+bundledidval+','+(bunrow-1)+')" class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrowprev">&#8250;</li>';   
                              }
                               //html += '<li  class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrowprev rowdisabled">Prev</li>';
                               html += '<li class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrownext rowdisabled">&#8250;</li>';
                          }
                      }
                    }
                    //html += '<li>'+bunrow+'</li>'; 
                     if((bunprodtcount)%bunpro_dispaly_items!=0){
                         // remcount = (tot_bun_pages*bunpro_dispaly_items)-bunprodtcount-1;
                          for(r=0;r<(bunpro_dispaly_items-(bunprodtcount%bunpro_dispaly_items));r++){
                              html += '<li class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bun-non-item"></li>';
                          }
                          //  alert(tot_bun_pages*(bunpro_dispaly_items+2));
                            if(bunrow==1){
                                 html += '<li  class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrowprev rowdisabled">&#8249;</li>'; 
                            }else{
                               html += '<li onclick="showprevbundrowprodts('+bundledidval+','+(bunrow-1)+')" class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrowprev">&#8249;</li>';
                            }
                           html += '<li class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrownext rowdisabled">&#8250;</li>';
                        
                     }
                    html += '</ul>';
                    html += '<div class="bun-prodt-page-disp-count">';
                    html += '<i class="fa fa-file-o"></i>';
                    for(bk=0;bk<bunrow;bk++){
                        html += '<span class="bunpagerow_'+bundledidval+'_'+bk+'">'+(bk+1)+'</span>';
                    }
                    html += '</div>';

                    html += '<ul class="Po1 " id="option_' + json[0]['bundled_items'][b]['bundled_id'] + '_' + k + '">';
                    html += '</ul>';
                    html += '</div>';

                    html += '<div id="bundled_product_options_' + k + '_' + $(data).val() + '_' + json[0]['bundled_items'][b]['bundled_id'] + '"  class="bund-prodt-option-div">';
                    html += '</div>';
                     html += '<div style="clear:both"></div>';
                    if (bundledidval < bundledcount) {
                        html += '<div class="prodt-popup-btns"><div class="left"><a class="button popup_cancel_btn popbtns playsound" onclick="optioncancel();" ><i class="fa fa-times"></i>Cancel</a>';
                        if (bundledidval > 1) {
                            html += '<a class="button bund_prevs popbtns playsound" onclick="slidebundledprevious(' + (bundledidval - 1) + ')" ><i class="fa fa-caret-square-o-left"></i>Previous Item('+(bundledidval -1)+'/'+bundledcount+')</a>';
                        }
                        html += '</div>';
                        //html +='<div class="bundle-count-div">'+bundledidval+'/'+bundledcount+'</div>';
                        html += '<div class="right">';
                        if (k > 0) {
                            html += '<a class="button bund_same popbtns playsound" onclick="addpreviousbundleditem(' + (bundledidval) + ',\'' + json[0]['bundled_items'][b]['bundled_id'] + '_' + (k - 1) + '\',\'' + json[0]['bundled_items'][b]['bundled_id'] + '_' + (k) + '\',' + finsish + ')" ><i class="fa fa-repeat"></i>Same</a>';
                        }
                        html += '<a class="option-ok popbtns playsound" onclick="bundlednext(' + (bundledidval + 1) + ');" ><i class="fa fa-caret-square-o-right"></i>Next Item('+(bundledidval + 1)+'/'+bundledcount+')</a>';

                        html += '</div></div>';
                    }
                    else {
                        html += '<div class="prodt-popup-btns"><div class="left"><a class="button popup_cancel_btn popbtns playsound" onclick="optioncancel();" ><i class="fa fa-times"></i>Cancel</a>';
                        if (bundledidval > 1) {
                            html += '<a class="button bund_prevs popbtns playsound" onclick="slidebundledprevious(' + (bundledidval - 1) + ')" ><i class="fa fa-caret-square-o-left"></i>Previous Item('+(bundledidval -1)+'/'+bundledcount+')</a>';
                        }
                        html += '</div>';
                        html +='<div class="bundle-count-div">'+bundledidval+'/'+bundledcount+'</div>';
                        html += '<div class="right">';
                        if (k > 0) {
                            html += '<a class="button bund_same popbtns playsound" onclick="addpreviousbundleditem(' + (bundledidval) + ',\'' + json[0]['bundled_items'][b]['bundled_id'] + '_' + (k - 1) + '\',\'' + json[0]['bundled_items'][b]['bundled_id'] + '_' + (k) + '\',' + finsish + ')" ><i class="fa fa-repeat"></i>Same</a>';
                        }
                        html += '<a class="option-ok popbtns playsound" onclick="bundledfinish(' + bundledidval + ');" ><i class="fa fa-check-square-o"></i> Finish</a>';
                        html += '</div></div>';
                    }
                    html += '</div>';
                    bundledidval += 1;
                }


            }
        }
        if (json[0]['bundled_items'].length < 1) {
            html += '<div class="prodt-popup-btns"><div class="left"><a class="button popup_cancel_btn popbtns playsound" onclick="optioncancel();" ><i class="fa fa-times"></i>Cancel</a></div><div class="right"><a class="option-ok popbtns playsound" onclick="optionvalidationUpdate();" ><i class="fa fa-check-square-o"></i>ADD</a></div></div>';
            $('#product-epos-popupbox').html(pagehtml+html);
             boxleft = colorbox_pos.left;
            boxwidth = colorbox_pos.width;
        }
        else {
            if (json[0]['option'].length > 0) {
                html += '<div class="option_button_div prodt-popup-btns"><div class="left"><a class="button popup_cancel_btn popbtns playsound" onclick="optioncancel();" ><i class="fa fa-times"></i>Cancel</a></div><div class="right"><a class="option-ok" onclick="mainoptionnext();" >Next</a></div></div>';
                $('#product-epos-popupbox').html(pagehtml+html);
                $('.bundled_products_div').hide();
                // $('#bundled_div_1').show();
            } 
            else {
                $('#product-epos-popupbox').html(pagehtml+html);
                $('.bundled_products_div').hide();
                $('#bundled_div_1').show();
            }
              boxleft = "70px";//static val change
              boxwidth = "92%";//
        }
       $.colorbox({inline: true, width: boxwidth,innerHeight:pop_max_height, left:boxleft,top:colorbox_pos.top, opacity: 0.1, href: "#product-epos-popupbox", closebutton: 'false',overlayClose:false,escKey:false,
            onOpen: function() {
                $('.main_options').hide();
                $('.opt_pop_page_'+startopt).show();
                $('#optnpgeitem_1').addClass('selected');
                for(count=1;count<=bundledidval;count++){
                    $('.bundlerows_'+count).hide();
                }
                $('.bunrow_1_0').show();
                 $('.bunpagerow_1_0').addClass('bunrowselected');
                $('#cboxClose').remove();
                
            }
        });

    } 
    else {
        $('#product-epos-popupbox').html('');
    }
}
function bundlednext(divid) {
    play_single_sound();
    var requredvalied = true;
    if ($('#bundled_div_' + (divid - 1) + ' .bund_product_required').length > 0) {
    $('#bundled_div_' + (divid - 1) + ' .bund_product_required').each(function(index, element) {
            bundprodtid=$(this).attr('id');
            //alert($(this).attr('id'));
            if ($('#' + bundprodtid + ' .bundled-single-product').length > 0 && $('#' + bundprodtid + ' .bundled-single-product.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                    $('#' +bundprodtid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
        });
    }
    if($('.bunoptn-pge-item').length>0){
     $('.bunoptn-pge-item').each(function(){
        var optdiv = $(this).attr('id').split('-');
          if ($('.bun_opt_pop_page_'+optdiv[1]+'.bun_product_requiredoption').length > 0) {
        $('.bun_opt_pop_page_'+optdiv[1]+'.bun_product_requiredoption').each(function(index, element) {
            bundoptionid=$(this).attr('id');
           if ($('#' + bundoptionid + ' .bundled-single-product-option').length > 0 && $('#' + bundoptionid + ' .bundled-single-product-option.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                      //$(this).animate({backgroundColor: '#F21E52'}, 'slow');
                       $('#' +bundoptionid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                      $('#bunoptnpgeitem-'+optdiv[1]).animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
            if ($('#' + bundoptionid + ' .bundled-multi-product-option').length > 0 && $('#' + bundoptionid + ' .bundled-multi-product-option.MultiOptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                   //$(this).animate({backgroundColor: '#F21E52'}, 'slow');
                    $('#' +bundoptionid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                   $('#bunoptnpgeitem-'+optdiv[1]).animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
          });
         }
    });
  }else{
    if ($('#bundled_div_' + (divid - 1) + ' .bun_product_requiredoption').length > 0) {


        $('#bundled_div_' + (divid - 1) + ' .bun_product_requiredoption').each(function(index, element) {
            bunreq_optnid=$(this).attr('id');
            if ($('#' + bunreq_optnid + ' .bundled-single-product-option').length > 0 && $('#' + bunreq_optnid + ' .bundled-single-product-option.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                     $('#' +bunreq_optnid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                }
            }
            if ($('#' + bunreq_optnid + ' .bundled-multi-product-option').length > 0 && $('#' + bunreq_optnid + ' .bundled-multi-product-option.MultiOptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                     $('#' +bunreq_optnid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                }
            }
        });
    }
  }   
    if (!requredvalied) {
        return;
    }
    if ($('#product-epos-bundled-products  #add_bun_prodt_' + (divid - 1)).length > 0) {
        $('#add_bun_prodt_' + (divid - 1)).html('');
        $('#add_bun_prodt_options_' + (divid - 1)).html('');
        $('#bundled_div_' + (divid - 1) + ' .bundled-single-product').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                $("#add_bun_prodt_" + (divid - 1)).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        //prbunoptiorows
        $('#bundled_div_' + (divid - 1) + ' .prbunoptiorows').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                $("#add_bun_prodt_options_" + (divid - 1)).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
            if ($(this).hasClass('MultiOptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                $("#add_bun_prodt_options_" + (divid - 1)).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        /*
        $('#bundled_div_' + (divid - 1) + ' .bundled-single-product-option').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                $("#add_bun_prodt_options_" + (divid - 1)).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        $('#bundled_div_' + (divid - 1) + ' .bundled-multi-product-option').each(function(index, element) {
            if ($(this).hasClass('MultiOptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                $("#add_bun_prodt_options_" + (divid - 1)).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        */
    }
    else {
        $("#product-epos-bundled-products").append('<div id="add_bun_prodt_' + (divid - 1) + '"></div>');
        $("#product-epos-bundled-product-options").append('<div id="add_bun_prodt_options_' + (divid - 1) + '"></div>');
        $('#bundled_div_' + (divid - 1) + ' .bundled-single-product').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                $("#add_bun_prodt_" + (divid - 1)).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        $('#bundled_div_' + (divid - 1) + ' .prbunoptiorows').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                $("#add_bun_prodt_options_" + (divid - 1)).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
            if ($(this).hasClass('MultiOptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                 $("#add_bun_prodt_options_" + (divid - 1)).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        /*
        $('#bundled_div_' + (divid - 1) + ' .bundled-single-product-option').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                $("#add_bun_prodt_options_" + (divid - 1)).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        $('#bundled_div_' + (divid - 1) + ' .bundled-multi-product-option').each(function(index, element) {
            if ($(this).hasClass('MultiOptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                 $("#add_bun_prodt_options_" + (divid - 1)).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
       */
        //}
    }
    $('.bundled_products_div').hide();
    // 
    $('#bundled_div_' + divid).show();
    $('.bunrow_' + divid +'_0').show();
    $('.bunpagerow_' + divid +'_0').addClass('bunrowselected');
    //$.colorbox.resize();
}
function bundledfinish(divid) {
    play_single_sound();
    var requredvalied = true;
    if ($('#bundled_div_' + divid + ' .bund_product_required').length > 0) {
        $('#bundled_div_' + divid + ' .bund_product_required').each(function(index, element) {
            bundlproductid=$(this).attr('id');
            if ($('#' + bundlproductid + ' .bundled-single-product').length > 0 && $('#' + bundlproductid + ' .bundled-single-product.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $('#' +bundlproductid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                }
            }

        });
    }
      if($('.bunoptn-pge-item').length>0){
     $('.bunoptn-pge-item').each(function(){
        var optdiv = $(this).attr('id').split('-');
          if ($('.bun_opt_pop_page_'+optdiv[1]+'.bun_product_requiredoption').length > 0) {
        $('.bun_opt_pop_page_'+optdiv[1]+'.bun_product_requiredoption').each(function(index, element) {
            bundleprodt_id=$(this).attr('id');
           if ($('#' + bundleprodt_id + ' .bundled-single-product-option').length > 0 && $('#' + bundleprodt_id + ' .bundled-single-product-option.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                       $('#' +bundleprodt_id+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                      $('#bunoptnpgeitem-'+optdiv[1]).animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
            if ($('#' + bundleprodt_id + ' .bundled-multi-product-option').length > 0 && $('#' + bundleprodt_id + ' .bundled-multi-product-option.MultiOptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                   $('#' +bundleprodt_id+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                   $('#bunoptnpgeitem-'+optdiv[1]).animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
          });
         }
    });
  }else{
    if ($('#bundled_div_' + divid + ' .bun_product_requiredoption').length > 0) {
        $('#bundled_div_' + divid + ' .bun_product_requiredoption').each(function(index, element) {
            bundleprodt_id = $(this).attr('id');
            if ($('#' + bundleprodt_id + ' .bundled-single-product-option').length > 0 && $('#' + bundleprodt_id + ' .bundled-single-product-option.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                     $('#' +bundleprodt_id+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                }
            }
            if ($('#' + bundleprodt_id + ' .bundled-multi-product-option').length > 0 && $('#' + bundleprodt_id + ' .bundled-multi-product-option.MultiOptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                     $('#' +bundleprodt_id+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                }
            }
        });
    }
  }  
    if (!requredvalied) {
        return;
    }
    if ($('#product-epos-bundled-products #add_bun_prodt_' + divid).length > 0) {
        $('#add_bun_prodt_' + divid).html('');
        $('#add_bun_prodt_options_' + divid).html('');
        //$("#product-epos-bundled-product-options").append('<div id="add_bun_prodt_options_' + (divid - 1) + '"></div>');
        $('#bundled_div_' + divid + ' .bundled-single-product').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                $("#add_bun_prodt_" + divid).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
                //alert($(data).attr('name'));
            }
        });
        $('#bundled_div_' + divid + ' .prbunoptiorows').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                //alert($(data).attr('name'));
                $("#add_bun_prodt_options_" + divid).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
            if ($(this).hasClass('MultiOptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                //alert($(data).attr('name'));
                $("#add_bun_prodt_options_" + divid).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        /*
        $('#bundled_div_' + divid + ' .bundled-single-product-option').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                //alert($(data).attr('name'));
                $("#add_bun_prodt_options_" + divid).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        $('#bundled_div_' + divid + ' .bundled-multi-product-option').each(function(index, element) {
            if ($(this).hasClass('MultiOptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                //alert($(data).attr('name'));
                $("#add_bun_prodt_options_" + divid).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        */
    } else {
        $("#product-epos-bundled-products").append('<div id="add_bun_prodt_' + divid + '"></div>');
        $("#product-epos-bundled-product-options").append('<div id="add_bun_prodt_options_' + divid + '"></div>');
        $('#bundled_div_' + divid + ' .bundled-single-product').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                $("#add_bun_prodt_" + divid).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
                //alert($(data).attr('name'));
            }
        });
        $('#bundled_div_' + divid + ' .prbunoptiorows').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                //alert($(data).attr('name'));
                $("#add_bun_prodt_options_" + divid).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
            if ($(this).hasClass('MultiOptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                //alert($(data).attr('name'));
                $("#add_bun_prodt_options_" + divid).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
         });
         /*
        $('#bundled_div_' + divid + ' .bundled-single-product-option').each(function(index, element) {
            if ($(this).hasClass('OptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                //alert($(data).attr('name'));
                $("#add_bun_prodt_options_" + divid).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        $('#bundled_div_' + divid + ' .bundled-multi-product-option').each(function(index, element) {
            if ($(this).hasClass('MultiOptionSeleted')) {
                data = '#' + $(this).attr('id') + ' input[type=\'hidden\'] ';
                //alert($(data).attr('name'));
                $("#add_bun_prodt_options_" + divid).append('<input type="hidden" name="' + $(data).attr('name') + '" value="' + $(data).val() + '" />');
            }
        });
        */
    }
    $('#product-epos-popupbox').html('');
    $.colorbox.close();
    addtocart_epos();
}
function mainoptionnext() {
play_single_sound();
    var requredvalied = true;
    if ($('#product-epos-popupbox .requiredoption').length > 0) {
        $('#product-epos-popupbox .requiredoption').each(function(index, element) {
            mainoptionid=$(this).attr('id');
            if ($('#' + mainoptionid + ' .single-product-option').length > 0 && $('#' + mainoptionid + ' .single-product-option.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                    $('#' +mainoptionid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
            if ($('#' + mainoptionid + ' .multi-product-option').length > 0 && $('#' + mainoptionid + ' .multi-product-option.MultiOptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                    $('#' +mainoptionid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
    });
    }
    if (!requredvalied) {
        return;
    }
    $("#product-epos-productoptions").html('');
    $("#product-epos-popupbox .proptiorows").each(function(index, element) {
        if ($(this).hasClass("OptionSeleted")){
            product_option_id = $(this).attr('parentid');
            ProductOptionvalueID = $(this).attr('ProductOptionvalueID');
            $("#product-epos-productoptions").append('<input type="hidden" name="option[' + product_option_id + ']" value="' + ProductOptionvalueID + '" />');
        }
        if ($(this).hasClass("MultiOptionSeleted")){
            product_option_id = $(this).attr('parentid');
            ProductOptionvalueID = $(this).attr('ProductOptionvalueID');
            $("#product-epos-productoptions").append('<input type="hidden" name="option[' + product_option_id + '][]" value="' + ProductOptionvalueID + '" />');
        }
    });
    /*
    $("#product-epos-popupbox .OptionSeleted").each(function(index, element) {
        product_option_id = $(this).attr('parentid');
        ProductOptionvalueID = $(this).attr('ProductOptionvalueID');
        $("#product-epos-productoptions").append('<input type="hidden" name="option[' + product_option_id + ']" value="' + ProductOptionvalueID + '" />');
    });
    $("#product-epos-popupbox .MultiOptionSeleted").each(function(index, element) {
        product_option_id = $(this).attr('parentid');
        ProductOptionvalueID = $(this).attr('ProductOptionvalueID');
        $("#product-epos-productoptions").append('<input type="hidden" name="option[' + product_option_id + '][]" value="' + ProductOptionvalueID + '" />');
    });
    */
    $('.main_options').hide();
    $('.option_button_div').hide();
    $('#bundled_div_1').show();
    //$.colorbox.resize();
}
function optionvalidationUpdate() {
    play_single_sound();
    var requredvalied = true;
    if($('.optn-pge-item').length>0){
       $('.optn-pge-item').each(function(){
        var optdiv = $(this).attr('id').split('_');
          if ($('.opt_pop_page_'+optdiv[1]+'.requiredoption').length > 0) {
        $('.opt_pop_page_'+optdiv[1]+'.requiredoption').each(function(index, element) {
            mainoptnid=$(this).attr('id');
            if ($('#' + mainoptnid + ' .single-product-option').length > 0 && $('#' + mainoptnid + ' .single-product-option.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                     $('#' +mainoptnid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                     $('#optnpgeitem_'+optdiv[1]).animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
            if ($('#' + mainoptnid + ' .multi-product-option').length > 0 && $('#' + mainoptnid + ' .multi-product-option.MultiOptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $('#' +mainoptnid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                    $('#optnpgeitem_'+optdiv[1]).animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
        });
    }
        
    });
    }else{ 
       if ($('#product-epos-popupbox .requiredoption').length > 0) {
        $('#product-epos-popupbox .requiredoption').each(function(index, element) {
            mainoptnid = $(this).attr('id');
            if ($('#' + mainoptnid + ' .single-product-option').length > 0 && $('#' + mainoptnid + ' .single-product-option.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                    $('#' +mainoptnid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
            if ($('#' + mainoptnid + ' .multi-product-option').length > 0 && $('#' + mainoptnid + ' .multi-product-option.MultiOptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $('#' +mainoptnid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                    
                }
            }
        });
    }
    }
    if (!requredvalied) {
        return;
    }

    $("#product-epos-productoptions").html('');
    $("#product-epos-popupbox .proptiorows").each(function(index, element) {
        if ($(this).hasClass("OptionSeleted")){
            product_option_id = $(this).attr('parentid');
            ProductOptionvalueID = $(this).attr('ProductOptionvalueID');
            $("#product-epos-productoptions").append('<input type="hidden" name="option[' + product_option_id + ']" value="' + ProductOptionvalueID + '" />');
        }
        if ($(this).hasClass("MultiOptionSeleted")){
            product_option_id = $(this).attr('parentid');
            ProductOptionvalueID = $(this).attr('ProductOptionvalueID');
            $("#product-epos-productoptions").append('<input type="hidden" name="option[' + product_option_id + '][]" value="' + ProductOptionvalueID + '" />');
        }
        
    });
    /*
     * 
    $("#product-epos-popupbox .OptionSeleted").each(function(index, element) {
        product_option_id = $(this).attr('parentid');
        ProductOptionvalueID = $(this).attr('ProductOptionvalueID');
        $("#product-epos-productoptions").append('<input type="hidden" name="option[' + product_option_id + ']" value="' + ProductOptionvalueID + '" />');
    });
    $("#product-epos-popupbox .MultiOptionSeleted").each(function(index, element) {
        product_option_id = $(this).attr('parentid');
        ProductOptionvalueID = $(this).attr('ProductOptionvalueID');
        $("#product-epos-productoptions").append('<input type="hidden" name="option[' + product_option_id + '][]" value="' + ProductOptionvalueID + '" />');
    });
    */
    $.colorbox.close();
    $('#product-epos-popupbox').html('');
    addtocart_epos();
}
function optioncancel() {
play_single_sound();
    $.colorbox.close();
    $('#product-epos-popupbox').html('');
}
function slidebundledprevious(divid) {
    play_single_sound();
    $('.bundled_products_div').hide();
    $('.option_button_div').hide();
    $('#bundled_div_' + divid).show();
}
function addpreviousbundleditem(divid, oldid, newid, finish) {
play_single_sound();
    if ($('#product-epos-bundled-products  #add_bun_prodt_' + (divid-1)).length > 0) {
        $("#product-epos-bundled-products").append('<div id="add_bun_prodt_' + (divid) + '"></div>');
        str = $('#add_bun_prodt_' + (divid-1)).html();
        newstr = str.replace('' + oldid + '', '' + newid + '');
        $("#add_bun_prodt_" + (divid)).append(newstr);
    }
    if ($('#product-epos-bundled-product-options  #add_bun_prodt_options_' + (divid-1)).length > 0) {
        $("#product-epos-bundled-product-options").append('<div id="add_bun_prodt_options_' + (divid) + '"></div>');
        html = $('#add_bun_prodt_options_' + (divid-1)).html();
        newhtml = html.replace('bundledoption[' + oldid, 'bundledoption[' + newid);
        $('#add_bun_prodt_options_' + (divid-1) + ' input[type="hidden"]').each(function() {
            newhtml = newhtml.replace('bundledoption[' + oldid, 'bundledoption[' + newid);
        });
        $("#add_bun_prodt_options_" + (divid)).append(newhtml);
}
    if (finish == 0) {
        $('#bundled_div_' + (divid)).hide();//
        $('#bundled_div_' + (divid+1)).show();
        $('.bunrow_' + (divid+1)+'_0').show();
    } else {
        $('#product-epos-popupbox').html('');
        $.colorbox.close();
        addtocart_epos();
    }
}
function postcode_formater(postcode) {
    postcode = postcode.replace(/ /g, "");
    var l = postcode.slice(-3);
    var f = postcode.slice(0, (postcode.length - 3));
    postcode = f + ' ' + l;
    postcode = postcode.toUpperCase();
    return postcode;
}
function dealsuggestionproductspopup(product_id,bundle_id){
    $('.right-cart-smart-deals').dialog('close');
    $('.deal-suggested-product-popup').load("index.php?route=product/product/ajaxdealsuggestedproducts",{product_id: product_id,bundle_id:bundle_id},function(){
        $('.deal-suggested-product-popup').dialog({
            autoOpen: true,
            height: 'auto',
            width: '70%',
            modal: true,
        });
    })    
}
function suggesteddealadd(div_id){
    var requredvalied = true;
    if ($('#' + div_id + ' .requiredoption').length > 0) {
        $('#' + div_id + ' .requiredoption').each(function(index, element) {

            if ($('#' + div_id +  ' .single-product-option').length > 0 && $('#' + div_id +  ' .single-product-option.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                }
            }
            if ($('#' + div_id + ' .multi-product-option').length > 0 && $('#' + div_id +  ' .multi-product-option.MultiOptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                }
            }
        });
    }
    if (!requredvalied) {
        return;
    }

    $("#product-epos-productoptions").html('');
    $('#' + div_id + '.OptionSeleted').each(function(index, element) {
        product_option_id = $(this).attr('parentid');
        ProductOptionvalueID = $(this).attr('ProductOptionvalueID');
        $("#product-epos-productoptions").append('<input type="hidden" name="option[' + product_option_id + ']" value="' + ProductOptionvalueID + '" />');
    });
    $('#' + div_id + ' .MultiOptionSeleted').each(function(index, element) {
        product_option_id = $(this).attr('parentid');
        ProductOptionvalueID = $(this).attr('ProductOptionvalueID');
        $("#product-epos-productoptions").append('<input type="hidden" name="option[' + product_option_id + '][]" value="' + ProductOptionvalueID + '" />');
    });
    $.colorbox.close();
    $('#product-epos-popupbox').html('');
    addtocart_epos();
}
function dealdiscardclick(product_id){
    $('#cart').load('index.php?route=module/cart&deal_discarded_id='+product_id+'#cart > *',function(){
                        cartresize();
                        $.colorbox.close();
    }); 
}
function dealapprove(main_product_id,bundle_product_id,popup){
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
                             $('#cart').load('index.php?route=epos/cart #cart > *',function(){
                                cartresize();
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
    $.colorbox.close();
    $('#smart-suggested-product-options-'+product_id).load("index.php?route=product/product/dealsuggestedproductoptions",{product_id: product_id,parent_id:parent_id,bundle_id:bundle_id},function(){
      $.colorbox({inline: true, width: "80%", opacity: 0.5, href: '#smart-suggested-product-options-'+product_id, closeButton: false,overlayClose:false,escKey:false});
    });
}
function smartoffers(){
      $.colorbox.close();
      $('#smart-deals-parent-div').html('');
      $('#smart-deals-parent-div').load("index.php?route=module/smartoffers",function(html){
      if(html){
          $.colorbox({inline: true, width: "80%", opacity: 0.5, href: "#smart-deals-parent-div", closeButton: false,overlayClose:false,escKey:false});
      }
    })
}
//function for driver assignment order list
function getdeliveryorderlist(){ 
    if(window.ajaxstatus =='0'){
        return false;
    }
    $.ajax({
        url: 'index.php?route=epos/epos/deliveryorderlist',
        dataType: 'json',
        success: function(json) {
            if(json){
                if(parseInt(json['total_deliveryorders'])-parseInt(json['total_assigned_deliveryorders'])==0){
                    unassignedclass='none';
                }else{
                    unassignedclass='';
                }
                if(parseInt(json['total_assigned_deliveryorders'])==0){
                    assignedclass='none';
                }else{
                    assignedclass='';
                }
                if(parseInt(json['total_deliveredorders'])==0){
                    deliveredclass='none';
                }else{
                    deliveredclass='';
                }
              //  alert(parseInt(json['total_deliveryorders'])+parseInt(json['total_deliveredorders']));
                html = '';
                html +='<div class="del-order-list">';
                html +='<ul>';
                html +='<li class="order-to-assign '+unassignedclass+'" style="background-color:#B8153F;font-weight: bold;">';
                html +='<i class="fa fa-chain-broken"></i>';
                html +='<span>To assign</span>';
                html +='<span>('+(json['total_deliveryorders']-json['total_assigned_deliveryorders'])+')</span>';
                html +='</li>';
                html +='<li style="background-color:#E5C305;font-weight: bold;" class="order-assigned-popup '+assignedclass+'">';
                html +='<i class="fa fa-link"></i>';
                html +='<span>Assigned</span>';
                html +='<span>('+json['total_assigned_deliveryorders']+')</span>';
                html +='</li>';
                html +='<li style="background-color:#9DB211;font-weight: bold;"  class="order-delivered-popup '+deliveredclass+'">';
                html +='<i class="fa fa-check"></i>';
                html +='<span>Delivered</span>';
                html +='<span>('+json['total_deliveredorders']+')</span>';
                html +='</li>';
                html +='</ul>';
                html +='</div>  ';
               $('.pos-delivery-order-list').html(html); 
               getpendingorderlist();
            }
        }
    });    
}
//
//
function getliveorders(){ 
    if(window.ajaxstatus =='0' || window.editdisplay == '1'){
        return false;
    }
      $.ajax({
        url: 'index.php?route=epos/liveorders/jsonliveorders',
        dataType: 'json',
        success: function(json) {
          html = '';
          if(json){
              if(json['unlock']==1){
                  unlockclass = 'unlock';
              }else{
                  unlockclass = '';
              }
              
            //if background image 
            if(json['store_tables'].length>0){
              html +='<div class="pos-table-selection" style=background:url("'+json['layout_img']+'") >';
              // html +='<ul class="tble">';
              for(i in json['store_tables']){
                  tabledata = json['store_tables'][i];
                  properties= tabledata['properties'];
                  if(window.editdisplay=='1'){
                    table_class = 'store_table_edit';
                  }else{
                    if(properties.length==0){
                        table_class = 'pos-sel-table ';
                    }else{
                        table_class = 'pos-sel-table store_table';
                    }
                    
                  }
                    if(properties.length==0){
                        table_class +=' notsetted';
                    }else{
                        table_class +=' setted';
                    }
                  if(json['layout_img']==0){
                      table_class +=' tablenobg';
                  }
                  if(typeof(json['dinein_tables'][tabledata['table_id']])!= "undefined"){
                      if(json['dinein_tables'][tabledata['table_id']]['lock']==1){
                          html +='<a id="store_table_'+tabledata['table_id']+'" style="width:'+properties.width+'px;height:'+properties.height+'px;top:'+properties.top+'px;left:'+properties.left+'px;line-height:'+properties.height+'px;" class="'+table_class+' dineinediting '+unlockclass+'" live_order_id="'+json['dinein_tables'][tabledata['table_id']]['live_id']+ '" table_id="'+tabledata['table_id']+'"><span class="table_name">'+tabledata['name']+' <i class="fa fa-lock"></i></span></a>';
                      }else{
                          //html +='<a id="store_table_'+tabledata['table_id']+'" style="width:'+properties.width+'px;height:'+properties.height+'px;top:'+properties.top+'px;left:'+properties.left+'px" class="'+table_class+' dineined" live_order_id="'+json['dinein_tables'][tabledata['table_id']]['live_id']+ '" table_id="'+tabledata['table_id']+'"><span class="table_name">'+tabledata['name']+'</span><span  class="table_time"># '+json['dinein_tables'][tabledata['table_id']]['live_id']+' -'+json['dinein_tables'][tabledata['table_id']]['duration']+'</span></a>';
                          html +='<a id="store_table_'+tabledata['table_id']+'" style="width:'+properties.width+'px;height:'+properties.height+'px;top:'+properties.top+'px;left:'+properties.left+'px" class="'+table_class+' dineined" live_order_id="'+json['dinein_tables'][tabledata['table_id']]['live_id']+ '" table_id="'+tabledata['table_id']+'"><span class="table_name">'+tabledata['name']+'</span><span><i class="fa shipimg_dine"></i></span><span class="table_time">'+json['dinein_tables'][tabledata['table_id']]['duration']+'</span></a>';
                      }
                  }else{
                       html +='<a id="store_table_'+tabledata['table_id']+'" style="width:'+properties.width+'px;height:'+properties.height+'px;top:'+properties.top+'px;left:'+properties.left+'px;line-height:'+properties.height+'px;" class="'+table_class+'" table_id="'+tabledata['table_id']+'"><span class="table_name">'+tabledata['name']+'</span></a>';
                  }
               }
              //blank li's 
              // html +='</ul>';
              html +='</div>';
              if(window.editdisplay=='1'){
                 html +='<div id="tablelayout_save"><div class="right"><a class="button" id="table_layout_save">Save</a></div></div>';
              }else if(window.editdisplay=='2'){
                  html +='<div id="tablelayout_save"><div class="right"><a class="button" id="table_layout_edit">Edit</a></div></div>';
              }
            }  
           $('#tab_main_header').height($(window).height()-$('#header').height()-$('#footer').height()-20);    //  
           $('#main_page_search_res').height($(window).height()-$('#header').height()-$('#footer').height()-$('.epos-user-main-det').height()-200);    
           $('.epos-live-orders').height($(window).height()-$('#header').height()-$('#footer').height()-70);    
          $('.epos-live-orders').html(html);
          $('.pos-table-selection').height($('.epos-live-orders').height()-$('#tablelayout_save').height()-30);    
          imgcss = ($(window).width()-$('.epos-order-det').width()-100)+'px '+($('#epos_live_tables').height()-100)+'px';
          $('.pos-table-selection').css('background-size',imgcss);
          //console.log(imgcss);
          //edittablelayout(window.editdisplay);
        }
         }
       });
}    
function gettakeoutorders(){  
    if(window.ajaxstatus =='0'){
        return false;
    }
      $.ajax({
        url: 'index.php?route=epos/liveorders/gettakeoutorders',
        dataType: 'json',
        success: function(json) {
          html = '';
          if(json){
              if(json.length>0){
                  html ='';
                for(j in json){
                    takeoutorder = json[j];
                    html +='<li class="take-out-order" live_order_id="'+takeoutorder['live_order_id']+'">';
                    html +='<i class="fa fa-floppy-o"></i>';
                    html +='<span class="takeout_order_det"># '+takeoutorder['live_order_id']+'-'+takeoutorder['cart_total']+'<br/>'+takeoutorder['date_added'];
                    html +='</span>';
                    html +='</li>';
                }
                for(i=json.length;i<4;i++){
                    html +='<li class="take-out-order disabled"></li>';
                }
                $('.take-out-orders').html(html);
               } 
            }
         }
       });
}    
function getmainpagequickbuttons(){ 
    if(window.ajaxstatus =='0'){
        return false;
    }
      $.ajax({
        url: 'index.php?route=epos/quick_checkout/jsonquichcheckoutbuttons',
        dataType: 'json',
        success: function(json) {
          html = '';
          if(json){
              
            if(json['quick_meals'].length>0){
                html +='<div class="pos-quickbtn-selection">';
                html +='<ul class="quick_btn_ul">';
                 for(i in json['quick_meals']){
                     btndata = json['quick_meals'][i];
                     html +='<li style="width:'+table_disp_width+'px;height:'+table_disp_height+'px" class="quick_btn_li main_quick_btn" quick_btn_id="'+btndata['id']+'">'+btndata['name']+'</li>';
                 }
                html +='</ul>';
                html +='</div>';
            }
           $('#main_page_search_res').height($(window).height()-$('#header').height()-$('#footer').height()-$('.epos-user-main-det').height()-200);     
           $('#tab_main_header').height($(window).height()-$('#header').height()-$('#footer').height()-20);    //    
           $('.epos-quick-btns').height($(window).height()-$('#header').height()-$('#footer').height()-$('.epos-user-main-det').height()-200);    
           //$('.epos-quick-btns').height($(window).height()-$('#header').height()-$('#footer').height()-60);    
          $('.epos-quick-btns').html(html);
       
        }
         }
       });
}
//functions for next/prev btns in pending orderlist
function getpendingorderlist(){ 
    if(window.ajaxstatus =='0'){
        return false;
    }
      $.ajax({
        url: 'index.php?route=epos/epos/jsonpendingorderslist',
        dataType: 'json',
        success: function(json) {
           // alert(json.length);
          html = ''; 
          if(json.length>0){
                ordercount =0 ;
                rowcount = 1;
                previtemperrow =0;
                rowheight = 35;//thead height
                divmax_height = $(window).height()- $('#header').height()-$('#footer').height()-$('.pos-todays-orders').height()-40;
                itemperrow = 0;
               //alert(divmax_height);
               html = '';
               html +='<div id="pos-pending-order-list">';
               html +='<table>';
               html +='<thead>';
               html +='<tr class="main-head"> <td>#</td>';
               //html +='<td>Customer</td><td>Total</td>';
               html +='<td>Requested Time</td><td>Delivery</td><td>Status<span class="pendorderupdate"><i class="fa fa-archive"></i></span></td></tr>';
               html +='</thead>';
               html +='<tbody>'; 
            for(i in json){
                ordercount +=1;
                if(i%2==0){
                    rownoclass='even';
                }
                else{
                    rownoclass='odd';
                }
              
               var orderdetails =  json[i];
                neworder_class ='';
                 rowheight += 40 
                 itemperrow +=1;
                   html +='<tr class="order-cach_'+rowcount+' pendingrow '+rownoclass+'">';
                   html +='<td class="'+neworder_class+' pending-order-view" pendid="pendgordr-'+orderdetails['order_id']+'">';
                   if(orderdetails['system_id']==0){
                       html +='<span class="pending_online"><i class="fa fa-wifi"></i></span>';
                   }
                   html +=''+orderdetails['order_id']+'';
                   if(orderdetails['new_order']==1){
                       html +='<span class="pending_new"><i class="fa fa-star"></i></span>';
                   }
                   html +='</td>';
                  // html +='<td>'+orderdetails['customer']+'</td>';
                  // html +='<td>'+orderdetails['total']+'</td>';
                   html +='<td class="pending_req_time pending-order-view" pendid="pendgordr-'+orderdetails['order_id']+'">'+orderdetails['requested_time']+'</td>';
                   html +='<td class="pending-order-view" pendid="pendgordr-'+orderdetails['order_id']+'"><i class="fa shipimg_'+orderdetails['shipping_code']+'"></i>';
                   html +=''+orderdetails['shipping_methode']+'</td>';
                   html +='<td>'+orderdetails['order_status']+'<span class="pendorderselect" order_id="'+orderdetails['order_id']+'"><i class="fa fa-square-o"></i></span></td>';
                   html +='</tr>';
                    if(rowheight>=divmax_height){
                   if(rowcount==1){
                       prevstats = true;
                       prevclass = 'rowdisabled';
                   }else{
                       prevstats = false;
                        prevclass = '';
                   }
                   if(json.length==ordercount){
                       nextstatus=true;
                       nextclass = 'rowdisabled';
                   }else{
                       nextstatus = false;
                       nextclass = '';
                   }
                   html +='<tr class="order-cach_'+rowcount+'  pendingrow">';
           //if(rowcount>1){
                        html +='<td class="pen-prev-btn '+prevclass+'" colspan="2" onclick="showprevpendinglist('+rowcount+','+prevstats+');">&#8249;</td>';
                        html +='<td class="pen-next-btn '+nextclass+'" colspan="2" onclick="shownextpendinglist('+rowcount+','+nextstatus+');">&#8250;</td>';
                   //}    //
                   html +='</tr>';
                   rowcount +=1;
                   rowheight = 50;
                   previtemperrow=itemperrow;
                   itemperrow = 0;
                }   
                
            }
            if(rowcount==1){
                while((rowheight)<divmax_height){
                    html +='<tr class="order-cach_'+rowcount+'  pendingrow"></tr>';
                    rowheight += 40 
                }
                 html +='<td class="pen-prev-btn rowdisabled" colspan="2" >&#8249;</td>';
                 html +='<td class="pen-next-btn rowdisabled" colspan="2">&#8250</td>';
            }
            if(rowcount>1){
                for(i=1;i<=(previtemperrow-itemperrow);i++){
                     html +='<tr class="order-cach_'+rowcount+'  pendingrow"></tr>';
                }
                html +='<tr class="order-cach_'+rowcount+'  pendingrow">';
                html +='<td class="pen-prev-btn" colspan="2" onclick="showprevpendinglist('+rowcount+');">&#8249;</td>';
                html +='<td class="pen-next-btn rowdisabled" colspan="2" onclick="shownextpendinglist('+rowcount+','+true+');">&#8250</td>';
                html +='</tr>';
            } 
               html +='</tbody>';
               html +=' </table>';
          }
          else{
             $('.pos-pending-orders').height($(window).height()- $('#header').height()-$('#footer').height()-$('.pos-todays-orders').height()-40);
          }
          $('.pos-pending-orders').html(html);
           $('.pendingrow').hide();
           $('.order-cach_1').show();
        }
    }); 
}  
function shownextpendinglist(row_no,status){
    play_single_sound();
    if(status){
        return false;
    }
    $('.pendingrow').hide();
    $('.order-cach_'+(row_no+1)).show();
}
function showprevpendinglist(row_no,status){
    play_single_sound();
    if(status){
        return false;
    }
    $('.pendingrow').hide();
    $('.order-cach_'+(row_no-1)).show();    
}
function shownextbundrowprodts(bundid,row_id){
    play_single_sound();
    $('.bundlerows_'+bundid).hide();
    $('.bunrow_'+bundid+'_'+(row_id+1)).show();
    $('.bunpagerow_'+bundid+'_'+(row_id)).removeClass('bunrowselected');
    $('.bunpagerow_'+bundid+'_'+(row_id+1)).addClass('bunrowselected');
}
function showprevbundrowprodts(bundid,row_id){
    play_single_sound();
    $('.bundlerows_'+bundid).hide();
    $('.bunrow_'+bundid+'_'+(row_id-1)).show();
      $('.bunpagerow_'+bundid+'_'+(row_id)).removeClass('bunrowselected');
    $('.bunpagerow_'+bundid+'_'+(row_id-1)).addClass('bunrowselected');
}   
function shownextoptionrow(opt_count,rowno){
    play_single_sound();
    //$('.proptiorows').hide();
    $('.proptionrow_'+opt_count+'_'+(rowno)).hide();  
    $('.proptionrow_'+opt_count+'_'+(rowno+1)).show();   
}
function showprevoptionrow(opt_count,rowno){
    play_single_sound();
    //$('.proptiorows').hide();
    $('.proptionrow_'+opt_count+'_'+(rowno)).hide();  
    $('.proptionrow_'+opt_count+'_'+(rowno-1)).show(); 
}
function shownextbundoptionrow(bund,opt_count,rowno){
    //$('.proptiorows').hide();
   // $('.prbunoptionrow_'+product_option_id+ '_'+r+'_1').show();
   play_single_sound();
    $('.prbunoptionrow_'+bund+'_'+opt_count+'_'+rowno).hide();  
    $('.prbunoptionrow_'+bund+'_'+opt_count+'_'+(rowno+1)).show();  
 
}
function showprevbundoptionrow(bund,opt_count,rowno){
    play_single_sound();
   $('.prbunoptionrow_'+bund+'_'+opt_count+'_'+rowno).hide();  
    $('.prbunoptionrow_'+bund+'_'+opt_count+'_'+(rowno-1)).show();  
} 
function shownextoptionpage(row){
    play_single_sound();
     var requredvalied = true;
    if ($('.opt_pop_page_'+row+'.requiredoption').length > 0) {
        $('.opt_pop_page_'+row+'.requiredoption').each(function(index, element) {
            curroptionid=$(this).attr('id');
            if ($('#' + curroptionid + ' .single-product-option').length > 0 && $('#' + curroptionid + ' .single-product-option.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                    $('#' +curroptionid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
            if ($('#' + curroptionid + ' .multi-product-option').length > 0 && $('#' + curroptionid + ' .multi-product-option.MultiOptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                    $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                    $('#' +curroptionid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
            // $('#'+$(this).attr('id') +' .OptionSeleted').length

        });
    }
    if (!requredvalied) {
        return;
    }
    $('.optn-pge-item').removeClass('selected');
    $('#optnpgeitem_'+(row+1)).addClass('selected');
    $('.opt_pop_page_'+row).hide();  
    $('.opt_pop_page_'+(row+1)).show();  
}
function showpreviousoptionpage(row){
    play_single_sound();
     $('.optn-pge-item').removeClass('selected');
    $('#optnpgeitem_'+(row-1)).addClass('selected');
    $('.opt_pop_page_'+row).hide();  
    $('.opt_pop_page_'+(row-1)).show();  
} 
function showbundnextoptionpage(bund,row){
    play_single_sound();
    var requredvalied =true;
    if ($('.bun_opt_pop_page_'+bund+'_'+row+'.bun_product_requiredoption').length > 0) {
        $('.bun_opt_pop_page_'+bund+'_'+row+'.bun_product_requiredoption').each(function(index, element) {
            curroptionid = $(this).attr('id');
           if ($('#' + curroptionid + ' .bundled-single-product-option').length > 0 && $('#' + curroptionid + ' .bundled-single-product-option.OptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                      $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);;
                      $('#' +curroptionid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                     // $('#bunoptnpgeitem-'+bund+'_'+row).animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
            if ($('#' + curroptionid + ' .bundled-multi-product-option').length > 0 && $('#' + curroptionid + ' .bundled-multi-product-option.MultiOptionSeleted').length == 0) {
                requredvalied = false;
                for (i = 0; i < 3; i++) {
                   $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                   $('#' +curroptionid+' .optionname').animate({backgroundColor: '#F21E52'}, 'slow');
                  // $('#bunoptnpgeitem-'+bund+'_'+row).animate({backgroundColor: '#F21E52'}, 'slow');
                }
            }
          });
         } 
    
     if (!requredvalied) {
        return;
    }
    $('.bunoptn-pge-item').removeClass('selected');
    $('#bunoptnpgeitem-'+bund+'_'+(row+1)).addClass('selected');
    $('.bun_opt_pop_page_'+bund+'_'+row).hide();  
    $('.bun_opt_pop_page_'+bund+'_'+(row+1)).show();  
}
function showbundpreviousoptionpage(bund,row){
    play_single_sound();
    $('.bunoptn-pge-item').removeClass('selected');
    $('#bunoptnpgeitem-'+bund+'_'+(row-1)).addClass('selected');
    $('.bun_opt_pop_page_'+bund+'_'+row).hide();  
    $('.bun_opt_pop_page_'+bund+'_'+(row-1)).show();  
} 
function swapdineintables(table1,livid1,table2,liveid2){
    play_single_sound();
    $.ajax({    url: 'index.php?route=epos/epos/swapdineinedtables&table1='+table1+'&livid1='+livid1+'&table2='+table2+'&liveid2='+liveid2,
                 dataType: 'json',
                 success: function(json) {
                     // location = 'index.php?route=epos/main';
                     if (json['success']) {
                         $('.table-seln-msgs').html('');
                         $.colorbox.close();
                         $('#mask').addClass('mask');
                         $('#eposmaincontent').load('index.php?route=epos/epos',function(){
                             cartresize();
                             $('#mask').removeClass('mask');
                         });
                     }
                 }
             });
}
function mergedineintables(table1,livid1,table2,liveid2){
    play_single_sound();
     $.ajax({    url: 'index.php?route=epos/epos/mergedineinedtables&table1='+table1+'&livid1='+livid1+'&table2='+table2+'&liveid2='+liveid2,
                 dataType: 'json',
                 success: function(json) {
                     if (json['success']) {
                         $('.table-seln-msgs').html('');
                         $.colorbox.close();
                         $('#mask').addClass('mask');
                         $('#eposmaincontent').load('index.php?route=epos/epos',function(){
                             $('#mask').removeClass('mask');
                             cartresize();
                         });
                     }
                 }
             });
}
function checkoutfunctions(){
   
}

function showlastorder(){
  play_single_sound() ;  
  $.colorbox.close();
  $("#order-epos-lastorder").load( "index.php?route=epos/epos/lastorderdetails", function() {
  if($("#order-epos-lastorder").html()!='') {
     $.colorbox({ inline: true, opacity: 0.5, href: "#order-epos-lastorder",
	   onLoad: function() {
             $('#cboxClose').remove();
             $('#colorbox').addClass('lastorderepo'); 
            },
            onCleanup:function() {
             $('#colorbox').removeClass('lastorderepo'); 
             }
	 }); 
  }
  });
}
function cancelandreorder(order_id){
    play_single_sound() ;
    var headerfooterheight = 270;  //dynamic
    $.ajax({
    url: 'index.php?route=epos/epos/cancelandreorder&order_id='+order_id,
            dataType: 'json',
            success: function(json) {
                if(json['success']){
                  $.colorbox.close();
                  $('#cart').load(url + 'index.php?route=epos/cart #cart > *',function() {
                    	cartresize();
                    });
                }
      }
      });  
}
function canclelastorder(order_id){
    play_single_sound() ;
    $.ajax({
    url: 'index.php?route=epos/epos/cancellastorder&order_id='+order_id,
            dataType: 'json',
            success: function(json) {
                   parent.$.colorbox.close();
                                  
      }
      });  
}
function pushlocalorder(){
   
    $.ajax({
              url: 'index.php?route=module/pushorderclient/checklastupdatedid',
              dataType: 'json',
              success: function(json) {
                  if(json['response']==true){
                     document.title = "None";
                     document.title = "pushorder";
                      pushorder('asas');
                  }else{
                    $('#mask').addClass('mask');
                    $('#eposmaincontent').load( red_url,function(){
                        $('#mask').removeClass('mask');
                    });
                  }     
              }    
          });          
}    
function pushorder(conn){
    if(conn=='notconnected'){
        document.getElementById("order-push-sug-div").style.display = "block"; 
    }else{
         document.getElementById("order-push-sug-div").style.display = "none"; 
         $.ajax({
                     url: 'index.php?route=module/pushorderclient/checkneworders',
                    // type: 'post',
                     dataType: 'json',
                     success: function(json) {
                         if(json['success']){
                              progressper = 0;
                               document.getElementById("order-push-div").style.display = "block"; 
                               $( "#orderpush-progress").progressbar({
                                  value: 0,
                                  change: function() {
                                     $(".orderpush-progress-label").text("Pushing Order " +$("#orderpush-progress").progressbar("value") + "%" );
                                  },
                                  complete: function() {
                                      $( "#orderpush-progress" ).hide();
                                      document.getElementById("order-push-div").style.display = "none"; 
                                      $('#mask').addClass('mask');
                                      $('#eposmaincontent').load(red_url,function(){
                                          $('#mask').removeClass('mask');
                                      });
                                  }
                                });
                                prog_count = Math.ceil(json['success']/5);
                                progressper = Math.ceil(100/prog_count);
                                orderupdate(progressper,1,prog_count);
                        }
                     }
           }); 
    }    
   

}    
//var progressbar_per =0;
function orderupdate(prog_per,prog_count,tot_count){
   if(prog_count > tot_count){
      return false;
   }
   progressbar_per =prog_per*prog_count;
        $.ajax({
            url: 'index.php?route=module/pushorderclient/pushorderonline',
           //type: 'post',
            dataType:'json',
            success: function(json) {
               $("#orderpush-progress").progressbar("value",progressbar_per);
               orderupdate(prog_per,(prog_count+1),tot_count);
            }
        });
}    
function cancelonlineprint(online_id){
  $.ajax({
		url: 'index.php?route=epos/epos/cancelprintorder&order_id='+online_id,
		dataType: 'json',
		success: function(json) {
                    //alert("SSS");
                    parent.$.colorbox.close();
		}
	});
} 
function deliverydetails(postcode){
            if (xhr != null) {
             xhr.abort();
            }
             $('.delivery_details').html('');
             postcode = postcode_formater(postcode);
             var regPostcode = /[A-Z]{1,2}[0-9]{1,2}[A-Z]{0,1} ?[0-9][A-Z]{2}/i;
             if(regPostcode.test(postcode) == true){
                    play_single_sound();
                    xhr = $.ajax({
                            url: 'index.php?route=epos/epos/checkdeliveryarea',
                            type:'post',
                            dataType: 'json',
                            data: { postcode:postcode},
                            success: function(json) {
                               if(json) {
                                   //alert("sss");
                                   
                                   if(json['status']=='yes'){
                                       $('.delivery_details').html('Distance:'+json['distance']+' miles');
                                    }else{
                                         $('.delivery_details').html('<span class="del_status_msg">'+json['message']+' </span> Distance :'+json['distance']+' miles');
                                    }
                                  blinkdivwithclass('del_status_msg');
                               }
                            }
                     });
             }
    } 
function blinkdivwithclass(divclass){
    
    if ($('.'+divclass).css('opacity') < 0.5 || $('.'+divclass).css('display')=='none' ) {
        return ;
    }
    for (i = 0; i < 10; i++) {
        
        $('.'+divclass).animate('slow');
        $('.'+divclass).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
    }
}    
function phone_calling(phonenumber){
   
    $.ajax({
        url: url + 'index.php?route=epos/epos/phonecoming&phone='+phonenumber,
        cache: false,
        dataType: 'json',
        success: function(jsondata) {
            if(jsondata) {
                html = '<div> <span class="phonein_num"><i class="fa fa-phone"></i>' + jsondata['phone'] + '</span> [' + jsondata['phone_time'] + '] </div>';
                html += '<div><span class="phonein_num">' + jsondata['customer_name'] + '</span> </div>';
                html += '<div class="phone_pop_spl_close" onclick="phone_calling_end()"><a class=""><i class="fa fa-times"></i></a></div>';
                $("#phone_calling").html( html );
                window.eposcall.phone = jsondata['phone'];
                window.eposcall.customer_id = jsondata['customer_id'];
                $("#phone_calling").show();
                blinkdivwithclass('phone_saveo');
                $('.phonecalls').show();
                blinkdivwithclass('phonecalls');
               
            }
        }
   });
}
function phone_calling_end(){
    window.eposcall.phone = "";
    window.eposcall.customer_id = "";
    $("#phone_calling").html('');
    $("#phone_calling").hide();
    $('.phonecalls').hide();
}
function play_single_sound() {
    if(typeof(window.epossettings)!== "undefined" && typeof(window.epossettings.sound)!== "undefined" && window.epossettings.sound=='1'){
        etmjs.paysound('1');
    }
}
function print_order() {
 
    if(typeof(window.epossettings)!== "undefined"){
       if(window.epossettings.printer=="epos"){
            etmjs.esc_receipt($('#printepos').html());
       }else if(window.epossettings.printer=="pdf"){
           etmjs.print_pdf($('#printpdf').html());
       }else if(window.epossettings.printer=="text"){
           etmjs.print_pdf($('#printtext').html());
       }
    }
}
function print_live_order() {
 
    if(typeof(window.epossettings)!== "undefined"){
       if(window.epossettings.printer=="epos"){
            etmjs.esc_receipt_live($('#printepos').html());
       }else if(window.epossettings.printer=="pdf"){
           etmjs.print_pdf($('#printpdf').html());
       }else if(window.epossettings.printer=="text"){
           etmjs.print_pdf($('#printtext').html());
       }
    }
}
function open_cashdrawer() {
    if(typeof(window.epossettings)!== "undefined"){
       if(window.epossettings.cashdrawer=="1"){
            etmjs.esc_cashdrawer();
       }
    }
}
function sendsms(to,msg,schedule){
   
    if(typeof(window.epossettings)!== "undefined"){
        etmjs.send_sms(to,msg,schedule);
    }else{
    }
    
    redirectsms(); 
}
function searcheposcustomer(searchtype,searchterm){
    if (xhr != null) {
        xhr.abort();
    }
    xhr = $.getJSON(  url +  'index.php?route=epos/epos/jsonautocompletecustomer&'+searchtype+'=y&term='+searchterm, function( data, status, xhr ) {
        //cacheCommon[ term ] = ;
        //response( data );\
        reshtml = '';   
        reshtml +='<ul class="search_res_ul">';
        for(i in data){
             json = data[i];
             if(walkin_customer_id==json['customer_id']){
                 continue;
             }
             reshtml +='<li class="common_search_res_li  '+((i%2==0)?'even':'odd')+'"> ';
             reshtml +='<div class="search_customer_details"><a>'+json['telephone']+'</a><br/><a>'+json['firstname']+'</a></div>';
             reshtml +='<div class="cus_search_btns cus_btn dinein mainpage"  style="display:none" customerid="'+json['customer_id']+'"><a class="b" onclick="showdineincontent('+json['customer_id']+');" ><i class="fa shipimg_dine"></i>Eat-In</a></div>';
             reshtml +='<div class="cus_search_btns cus_btn walkin mainpage"  style="display:none" customerid="'+json['customer_id']+'"><a class="b" onclick="showtakeoutcontent('+json['customer_id']+');"><i class="fa shipimg_carry"></i>Take Out</a></div>';
             reshtml +='<div class="cus_search_btns cus_btn phonein mainpage" style="display:none" customerid="'+json['customer_id']+'"><a class="b" onclick="showdeliverycustomercontent('+json['customer_id']+',\'\',\'phonein\');"><i class="fa shipimg_home"></i>Delivery</a></div>';
             reshtml +='<div class="cus_search_btns cus_btn colectin mainpage" style="display:none" customerid="'+json['customer_id']+'"><a class="b" onclick="showdeliverycustomercontent('+json['customer_id']+',\'\',\'collect\');"><i class="fa shipimg_collect"></i>Collect</a></div>';
             reshtml +='</li>';

        }  
        reshtml +='</ul>';

        $('#main_page_search_res').html(reshtml);
                              
    });
}   
//main.tpl
function liveordersave(){  
    $.ajax({
            url: 'index.php?route=epos/liveorders/neworder',
            type: 'post',
            data:{comment:$('#ordr-commnts').val()}, 
            dataType: 'json',
            success: function(json) {
                if(json['success']){
                    $('#mask').addClass('mask');
                     window.ajaxstatus = '1'; 
                    $('#eposmaincontent').load('index.php?route=epos/epos/main',function(){
                        $('#mask').removeClass('mask');
                        /* PHONE */
                        if (window.eposcall.phone!='') {
                            $('.phonecalls').show();
                            blinkdivwithclass('phonecalls');
                        }
                    });
                }
            }
        }); 
}
function eposTime() {
         var posdate = dateFormat(new Date(), "dddd, mmmm dS, yyyy");
         var postime= dateFormat(new Date(), "h:MM:ss TT");
     // Saturday, June 9th, 2007, 5:46:21 PM
         $('#eposDate').html(posdate);
         $('#eposTime').html(postime);
}
function print_report() {
    if(typeof(window.epossettings)!== "undefined" && typeof(window.epossettings.printer)!== "undefined"){
            etmjs.esc_report($('#report-print-div').html());
    }
}
function onscreenkeyboard(id){
    if(typeof(window.epossettings)!== "undefined" && $.virtualkeyboard!=0){  
            etmjs.onscreenkeyboard(id);
    }
}
function paysplit(payment,split_id,subpay,split_orderid){
    play_single_sound();
    window.splitstatus = 1; 
    $('#split_cancel_btn').hide();
    $('#split_to_home_btn').show();
    //alert(order_data['total_amount']+'------'+eval(parseFloat($('#main_payment_amount').val())+parseFloat($('#sub_payment_amount').val())));
    
    if(subpay===1){//
       
        if(eval($('#order_toral_amt').val())>eval(parseFloat($('#main_payment_amount').val())+parseFloat($('#sub_payment_amount').val()))){
            $('#split_warnings').html("Please Collect complete amount");
            return false;
        }
        splitteddata[split_id]['payments'][payment] = eval(parseFloat($('#main_payment_amount').val()));
        splitteddata[split_id]['payments']['cash'] = eval(parseFloat($('#sub_payment_amount').val())); 
    }
    else{
       
        splitteddata[split_id]['payments'][payment] = eval(parseFloat($('#order_toral_amt').val()));
    }
   
    splitteddata[split_id]['payed']='PAID';
    if(split_payment_print===0){
        printsplitonly(split_id,split_orderid); 
    }else{
        printsplitandorder(split_id,split_orderid);
    } 
     
    all_payed = true;
    for(i in splitteddata){
        if(splitteddata[i]['payed']===undefined){
            all_payed = false;
        }
    }
    //console.log("444");
    splittedtotal =getsplittedtotal();
    //console.log("555");
    bal_amt = eval(parseFloat(order_data['total_amount']-splittedtotal).toFixed(2));
    //console.log(bal_amt);
    if(all_payed===true && bal_amt==0){
        $.ajax({
            url: 'index.php?route=epos/confirm/savesplits&confirm=1',
            type: 'post',
            data: {split:splitteddata},
            dataType: 'json',
            success: function(json) {
                if(split_confirm_print!==0){
                   $.ajax({
                     url: 'index.php?route=epos/print/epos&order_id='+split_orderid,
                     dataType: 'html',
                     success: function(data) {
                        //$('#printepos').show();
                        $('#printepos').html(data);
                        $('#printpdf').html(url+"index.php?route=epos/print/pdf&order_id="+split_orderid);
                        $('#printhtml').html(url+"index.php?route=epos/print/html&order_id="+split_orderid);
                        $('#printtext').html(url+"index.php?route=epos/print/text&order_id="+split_orderid);
                        print_order(); 
                        showmainpage();
                        $.ajax({
                             url: 'index.php?route=epos/epos/updateprintedorders&order_id='+split_orderid,
                             dataType: 'json',
                             success: function(json) {
                             
                             }    
                         }); 
                       
                     }
                });
                }else{
                   showmainpage();
                }
            }
        });
    }else{
        backtosplit();
    }
    return false;
}
function getsplitmaincart(){
        split_html = '';
        product_data = order_data['products'];
        split_html = '<div id="split_maincart_info"><table><tbody id="split_main_cart_items">';
        rowid =1;
        for(i in product_data){
            // console.log(i);
            remqty = getsplititemremainingqty(i);
            
            data = product_data[i];
            // console.log((data));
            prodt_cartqty =eval(data['quantity']);
            if(eval(prodt_cartqty-remqty)>0){
                split_html +='<tr id="cart_row_'+rowid+'" class="rowhead itemrow" cartkey="'+i+'" >';
                split_html +='<td row_id="'+rowid+'" class="prname">'+data['name']+'</br>';
                split_html +=''+data['option'].replace(/(?:\r\n|\r|\n)/g, '<br />')+'';
                split_html +='</td>';
                split_html +='<td class="prquantity"><span class="splitted_qty_det" style="display:none"><a row_id="'+rowid+'" qty="'+eval(prodt_cartqty-remqty)+'" class="splitquantity minus">-</a><input type="hidden" id="split_seld_qty" value="1"><span class="spliited_org_qty">1</span><a row_id="'+rowid+'" qty="'+eval(prodt_cartqty-remqty)+'"  class="splitquantity add">+</a></span></br><span class="splitprodt_cartqty">'+eval(prodt_cartqty-remqty)+'</span>';
                split_html +='<input type="hidden" class="split_sel_price" value="'+data['disc_price']+'"><span class="splitprodt_price">'+data['total']+'</span></td>';
                split_html +='</tr>';
                rowid ++;
                
           }
        }
        split_html += '</tbody></table></div>';
        
        split_html += '<div id="split_maincart_total"><table><tbody>';
        split_html += '<tr><td class="splt_tot_text">Total</td>';
        split_html += '<td class="splt_tot_amt">'+order_data['formated_total_amount']+'</td></tr>';
        split_html += '</tbody></table>';
        splittedtotal = getsplittedtotal();
        if((splittedtotal!==0) && splittedtotal<order_data['total_amount']){
             split_html += '<div class="split_bal_amt"><span id="split-round-btn"><i class="fa fa-thumbs-o-down"></i></span><span class="split_det">Balance</span><span class="split_bal">'+order_data['currency_symbol']+' '+eval(order_data['total_amount']-splittedtotal).toFixed(2)+'</span></div>';
        }
        split_html += '</div>';
        $('#split_left_cart').html(split_html);
        sc=$('#split_maincart_info').position();
        st=$('#split_maincart_total').position();
        if(t){
            $('#split_maincart_info tbody').height('' + parseInt(st.top-sc.top-35) + 'px');
        }else{
            $('#split_maincart_info tbody').height('200px');
        }
         split_all_payed = true;
        for(i in splitteddata){
            if(splitteddata[i]['payed']===undefined){
                split_all_payed = false;
            }
        }
        bal_amt = eval(order_data['total_amount']-splittedtotal);
        if(split_all_payed===true && bal_amt==0){
            $('#split_page_confirm').show();
        }else{
            $('#split_page_confirm').hide();
        }
         if(window.splitstatus==1){
            $('#split_cancel_btn').hide();
            $('#split_to_home_btn').show();
        }else{
            $('#split_cancel_btn').show();
            $('#split_to_home_btn').hide();
        }
    }
function  splittedorderdetails(){//details top splitted btns
    splitcounts = getsplitcounts(); 
    if(splitcounts==0 && window.splitstatus==0){
        $('#split_save_btn').addClass('splitdisabled');
        $('#split_print_btn').addClass('splitdisabled');
    }else{
        $('#split_save_btn').removeClass('splitdisabled');
        $('#split_print_btn').removeClass('splitdisabled');
    }
    $('#split_warnings').html('');
    splittedhtml = '';
    splittedhtml +='<ul>';
    
    itemcount = 0;
    rowcount = 1;
    max_div_height = $('#confirm_split_right').height();
    splititemheight = 0;
    //tot_bun_pages= Math.ceil(splitteddata.length/18);
    for(i in splitteddata){
        itemcount++;
        data = splitteddata[i];
        display ='block';
        payedclass='';
        if(data['payed']!==undefined){
           payedclass='splitpayed'; 
        }
        if(rowcount!=1){
            display='none';
        }
        splittedhtml +='<li split_id='+i+' class="splitted splittop_'+rowcount+' '+payedclass+' " style="display:'+display+'">';
        splittedhtml +='<span class="split_detid">#'+order_data['order_id']+'  ('+eval(eval(i)+1)+')</span>';
        splittedhtml +='<input type="hidden" value="'+data['amount']+'" id="splitind_amount"><span class="split_detamt">'+order_data['currency_symbol']+''+eval(data['amount']).toFixed(2)+'</span>';
        if(data['payed']!==undefined){
            splittedhtml +='<span class="split_paydet">'+data['payed']+'</span>';
        }
        splittedhtml +='</li>';
        if((itemcount)%18==0){
            if(itemcount<splitcounts){
                if(rowcount==1){
                 splittedhtml +='<li class="split_sel_prev disabled splittop_'+(rowcount)+'" >&#8249;</li>';
                }else{
                 splittedhtml +='<li class="split_sel_prev splittop_'+(rowcount)+'" style="display:none" onclick="showsplitprevrow('+eval(rowcount-1)+');">&#8249;</li>';
                }
                 splittedhtml +='<li class="split_sel_next splittop_'+(rowcount)+'" style="display:'+display+'" onclick="showsplitnextrow('+eval(rowcount+1)+');">&#8250;</li>';
                 rowcount +=1
            }else{
                 if(rowcount==1){
                   //html += '<li  class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrowprev rowdisabled">&#8249;</li>'; 
                   splittedhtml +='<li class="split_sel_prev disabled splittop_'+(rowcount)+'" >&#8249;</li>';
                }else{
                    // html += '<li onclick="showprevbundrowprodts('+bundledidval+','+(bunrow-1)+')" class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrowprev">&#8250;</li>';   
                    ssplittedhtml +='<li class="split_sel_prev splittop_'+(rowcount)+'" style="display:none" onclick="showsplitprevrow('+eval(rowcount-1)+');">&#8249;</li>';
                }
                 
                 //html += '<li class="bundlerows_'+bundledidval+' bunrow_'+bundledidval+'_'+(bunrow-1)+' bunrownext rowdisabled">&#8250;</li>';
                 splittedhtml +='<li class="split_sel_next disabled splittop_'+(rowcount)+'" style="display:'+display+'">&#8250;</li>';
            }
       }
        
    }
     
    if(itemcount%18!=0 ){
        for(c=0;c<(18-(itemcount%18));c++){
            splittedhtml +='<li class="split_sel_nonitem splittop_'+rowcount+'" style="display:'+display+'"></li>';
        } 
        if(rowcount==1){
              splittedhtml +='<li class="split_sel_prev disabled splittop_'+(rowcount)+'" >&#8249;</li>';
        }else{
           splittedhtml +='<li class="split_sel_prev splittop_'+(rowcount)+'" style="display:none" onclick="showsplitprevrow('+eval(rowcount-1)+');">&#8249;</li>';
        }
       splittedhtml +='<li class="split_sel_next disabled splittop_'+(rowcount)+'" style="display:'+display+'">&#8250;</li>';
    }
    splittedhtml +='</ul>';
    //splitted_top_btns
    splitpayedamt = getsplitpayedtotal();
    
    if(splitpayedamt<order_data['total_amount'] && splitteddata.length>0){
       splittedhtml +='<div class="splitpayed_amt"><span class="splitpayed_amt_left"> #'+order_data['order_id']+'</span><span class="splitpayed_amt_right"> TO PAY '+order_data['currency_symbol']+' '+eval(order_data['total_amount']-splitpayedamt).toFixed(2)+'</span></div>'; 
    }
    
    $('#splitted_left_btns').html(splittedhtml);
    $('#splitted_left_btns li.splitted').first().addClass('split_selected');
    splittedtotal = getsplittedtotal();
     if(splittedtotal>=order_data['total_amount']){
         $('.split_btns').addClass('splitdisabled');
     }else{
         $('.split_btns').removeClass('splitdisabled');
     }
    showsplitdetails($('.split_selected').attr('split_id'));
}   
function showsplitdetails(split_id){ //splitted btn click show products && totals
    //split_type:'item'
    details = '';
    disp_count=0;
    for(i in splitteddata){
    //data = splitteddata[i]; 
    divdisp='none';
    if(i===split_id){
        divdisp='block';
        disp_count++;
    }
    details +='<div id="splitted_item_details_'+i+'" style="display:'+divdisp+'" class="splitted-items-table">';
    details +='<table class="splitted_item_tble">';
    
    split_products= splitteddata[i]['products'];
    //console.log(split_products);
    product_data =order_data['products'];
    
    details += '<tbody>';
    details +='<tr class="splitted_head"><td># '+order_data['order_id']+' ('+eval(parseInt(i)+1)+')</td><td></td></tr>';
    if(splitteddata[i]['split_type']!='item'){
        details +='<tr class="rowhead">';
        details +='<td class="prname">'+splitteddata[i]['split_type']+' Split</td>';
        details +='<td class="prquantity"><span class="splitprodt_cartqty"></span>';
        details +='<span class="splitprodt_price">'+order_data['currency_symbol']+''+eval(splitteddata[i]['amount'])+'</span></td>';
        details +='</tr>';
    }
   for(ckey in product_data){
       if(split_products[ckey]!= undefined){  
            data = product_data[ckey];
            prodt_cartqty =eval(data['quantity']);
            //console.log(splitteddata[split_id]['products'][i]);
            details +='<tr class="rowhead">';
            details +='<td class="prname">'+data['name']+'</br>';
            details +=''+data['option'].replace(/(?:\r\n|\r|\n)/g, '<br />')+'';
            details +='</td>';
            details +='<td class="prquantity"><span class="prody_org_qty">'+splitteddata[i]['products'][ckey]+'</span>';
            details +='<span class="cart_org_price">'+order_data['currency_symbol']+''+eval(data['disc_price']*splitteddata[i]['products'][ckey])+'</span></td>';
            details +='</tr>';
        }
    }
    details += '</tbody>';
    details +='</table>';
    details +='<table class="splitted_total_tble">';
    details += '<tbody>';
    details +='<tr><td>Total</td><td>'+order_data['currency_symbol']+''+splitteddata[i]['amount']+'</td></tr>';
    payed_total = splitteddata[i]['payments'];
    //if(payed_total.length>0){
      for (p in payed_total){
        details +='<tr>';
        details +='<td>'+p+'</td>';
        details +='<td>'+order_data['currency_symbol']+''+payed_total[p]+'</td>';
        details +='</tr>';
      }
    //}
    details += '</tbody>';
    details +='</table>';
    details +='<div class="splitted_bottom">';
    if(splitteddata[i]['payed']===undefined){
        details +='<a id="split_remove_btn" onclick="removesplit('+split_id+');" class="paymt-pg-backbtn popbtns"><i class="fa fa-times"></i>Delete</a><a class="paymt-pg-backbtn split_pay_btn popbtns" onclick="getsplitpaymentmethodes('+i+')"><i class="fa pay_confirm"></i>PAY #'+eval(parseInt(i)+1)+'</a>';
    }else{
        details +='<span class="splitt_payed_status">PAID</span>';
    }
    
    details +='</div>';
    details +='</div>';
    }
    $('#splitted_items').html(details);
    $('.splitted_item_tble tbody').height(''+parseInt($('#confirm_split_right').height()-$('#splitted_item_details_'+split_id+' .splitted_total_tble').height()-$('#splitted_item_details_'+split_id+' .splitted_bottom').height())+'px');   
}    
function getsplittedtotal(){
    splittedtotal =0;
    //if(splitteddata.length>0){
         for(i in splitteddata){
            data = splitteddata[i];
            splittedtotal = eval(splittedtotal+eval(data['amount']));
         }
    //}
    return splittedtotal.toFixed(2);
}
function getsplititemremainingqty(itemkey){
    qty = 0;
    //if(splitteddata.length>0){
         for(k in splitteddata){
             split_products= splitteddata[k]['products'];
             for(j in split_products){
                 if(itemkey==j){
                     qty = eval(qty+eval(split_products[j]));
                 }
             }
         }
    //}
    return qty;
}
function getsplitpaymentmethodes(split_id){
    $('.splitto_back').show();
    $('#order_split_details').hide();
    $('#order_split_payments .split_payment_selection').html('');
    $('#order_split_payments').show();
    sc=$('#split_maincart_info').position();
    st=$('#split_maincart_total').position();
    $('#split_maincart_info  table').html($('#splitted_item_details_'+split_id+' .splitted_item_tble').html());
    $('#split_maincart_total table').html($('#splitted_item_details_'+split_id+' .splitted_total_tble').html());
    $('.split_btns').hide();//$('#split_seln_btns .split_btns').addClass('splitdisabled');
    $('#split_save_btn').hide();
    $('#split_print_btn').hide();
    $('#split_cancel_btn').hide();
    $('#split_to_home_btn').hide();
    $('.split_paymt_btn_li').first().trigger('click');
    if(t){
        $('#split_maincart_info tbody').height('' + parseInt(st.top-sc.top-35) + 'px');
    }else{
        $('#split_maincart_info tbody').height('200px');
    }
    $('.split_payment_selection').height(''+parseInt($('#confirm_split_right').height())+'px');
}
function geteqaulsplittedamounts(amount,split_count){
    split_amounts = {};
    splitamt = Number((amount/split_count).toString().match(/^\d+(?:\.\d{0,2})?/));
    rem_amt = eval(amount - (splitamt*split_count)).toFixed(2);//0.01
    for(i=1;i<=split_count;i++){
        split_amounts[i] = splitamt;
    }
    if(rem_amt>0){
        for(i=split_count;i>0;i--){
            if(eval(rem_amt/i)>=0.01){
                amt=eval(split_amounts[i]+0.01).toFixed(2);
                split_amounts[i] = eval(amt);
            }
        }
    }
    return split_amounts; 
} 
function showsplitnextrow(splitrowid){
    $('.splittop_'+eval(splitrowid-1)).hide();
    $('.splittop_'+splitrowid).show();
}
function showsplitprevrow(splitrowid){
    $('.splittop_'+eval(splitrowid+1)).hide();
    $('.splittop_'+splitrowid).show();
}
function backtosplit(){
    clearpreviouspayments();
    $('#order_split_payments').hide();
    $('#order_split_details').show();
    //$('#split_seln_btns .split_btns').removeClass('splitdisabled');
    $('.split_btns').show();
    $('.splitto_back').hide();
    $('#split_save_btn').show();
    $('#split_print_btn').show();
    $('#split_cancel_btn').show();
    getsplitmaincart(); 
    splittedorderdetails();
}
function removesplit(split_id){
    newsplits = [];
    sid = 0
    for(i in splitteddata){
      if(i!=split_id){
          data = splitteddata[i];
          newsplits[sid]=data;
          sid++;
      }        
    }
    splitteddata =newsplits;
    backtosplit();
    
}
function getsplitcounts(){
    var size = 0, key;
    for (key in splitteddata) {
        if (splitteddata.hasOwnProperty(key)) size++;
    }
    return size;
}
function printallsplits(order_id){
    window.splitstatus = 1; 
    $('#split_cancel_btn').hide();
    $('#split_to_home_btn').show();
    $.ajax({
            url: 'index.php?route=epos/print/printsplits&order_id='+order_id+'&custprint=split',
            type: 'post',
            data: {split:splitteddata},
            dataType: 'html',
            success: function(data) {
               $('#printepos').html(data);
               print_order(); 
            }
    }); 
}
function printsplitonly(splitid,order_id){
    window.splitstatus = 1; 
    $.ajax({
            url: 'index.php?route=epos/print/printsplitonly&splitindex='+splitid+'&order_id='+order_id,
            type: 'post',
            data: {split:splitteddata},
            dataType: 'html',
            success: function(data) {
               // $('#printepos').html(data);
               print_splitonly(data); 
            }
    }); 
}
function printsplitandorder(splitid,order_id){
     $.ajax({
            url: 'index.php?route=epos/print/printsplits&splitindex='+splitid+'&order_id='+order_id,
            type: 'post',
            data: {split:splitteddata},
            dataType: 'html',
            success: function(data) {
               $('#printepos').html(data);
               print_order(); 
            }
    }); 
}
function print_splitonly(data) {
 
    if(typeof(window.epossettings)!== "undefined"){
       if(window.epossettings.printer=="epos"){
            etmjs.esc_receiptsplit(data);
       }else if(window.epossettings.printer=="pdf"){
           etmjs.print_pdf(data);
       }else if(window.epossettings.printer=="text"){
           etmjs.print_pdf(data);
       }
    }
}
function getsplitpayedtotal(){
    
    splitpayed_total =0;
    //if(splitteddata.length>0){
         for(i in splitteddata){
            data = splitteddata[i];
            if(data['payed']!==undefined){
                splitpayed_total += eval(splitpayed_total+eval(data['amount']));
                //console.log(data['amount']);
            }
            
         }
    //}
    return splitpayed_total.toFixed(2);
}
function showoptiongroups(pid,subid){
    $('.optgroups_'+pid).hide();
    $('.optgroup_'+pid+'_'+subid).show();
    $('.optiongroupnames_'+pid).removeClass('group_selected');
    $('#optiongroupnames_'+pid+'_'+subid).addClass('group_selected');
}