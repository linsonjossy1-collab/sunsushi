<?php echo (isset($order_msg))?$order_msg:''; ?>
<script type="text/javascript"><!--
    $(function() {
          $('input#postcodepopuph').keypress(function(e) {
            if (e.keyCode == '13') {
              e.preventDefault();
              postcode_check();
              //  $(':button:contains("GO")').click();
             }
          });
        
        $(document).on('click','.button-cart', function() {
             <?php if(isset($order_msg)) { ?>
                 onlineorderpopup();
                 return true;
             <?php } ?> 
            var prod_id = $(this).attr('prod_id')
            etmAddtocart($(this).attr('prod_id'));   
            
        });
        $(document).on('click','.button-cart_wop', function() {
             event.preventDefault();   
            <?php if(isset($order_msg)) { ?>
                   onlineorderpopup();
                   return true;
                  <?php } ?> 
            // var prod_id = $(this).attr('id');
            etmAddtocart($(this).attr('div_id'));   
            
        });
          $(document).on('click','.popupbuttonn', function() {                   
            <?php if(isset($order_msg)) { ?>
                onlineorderpopup();
                return true;
            <?php } ?> 
            divide = $(this).attr('divide')
            
             showpopupoptions(divide);
            

        });
        
        $(document).on('click','.btnaddtocart', function() {
                <?php if(isset($order_msg)) { ?>
                   onlineorderpopup();
                   return true;
                  <?php } ?> 
            // var prod_id = $(this).attr('id');
            etmAddtocart($(this).attr('divid'));   
            
        });
          $(document).on('click','.btnshowoptions', function() {   
             event.preventDefault();
            <?php if(isset($order_msg)) { ?>
                onlineorderpopup();
                return true;
            <?php } ?> 
            showpopupoptions($(this).attr('divid'),1);
            

        });
        
         $(document).on('click','.popupbuttonall', function() {                    
            <?php if(isset($order_msg)) { ?>
                onlineorderpopup();
                return true;
            <?php } ?> 
            divide = $(this).attr('divide')
            
             showpopupoptions(divide,1);
            

        });
        $(document).on('click', '.all_options_popup',function() {
             <?php if(isset($order_msg)) { ?>
              onlineorderpopup();
              return true;
              <?php } ?> 
             divide = $(this).attr('id');
             showalloptions(divide);
            // alert(postcode);
        });
        $(document).on('click','.category_prodt_popup', function() {
            <?php if(isset($order_msg)) { ?>
            onlineorderpopup();
            return true;
          <?php } ?> 
            divide = $(this).attr('id');
            showcategoryproducts(divide);
                

        });
        $(document).on('click','.button-cart_wop_wc', function() {
           <?php if(isset($order_msg)) { ?>
           onlineorderpopup();
             return true;
           <?php } ?> 
           var prod_id = $(this).attr('id');
           add_prod_tocart_afterpostcodecheck(prod_id);
           
        });
        $(document).on('click','.bundled_prodt_popup', function() {
             <?php if(isset($order_msg)) { ?>
            onlineorderpopup();
            return true;
            <?php } ?> 
            showbundledproducts($(this).attr('pid'));
        });
        $(document).on('click','.bundled_prodt_page', function() {
             <?php if(isset($order_msg)) { ?>
            onlineorderpopup();
            return true;
            <?php } ?> 
            location="deals/&dl="+$(this).attr('pid')
        });                       
        $(document).on('click','.bundled_cat_prodt_popup', function() {
             <?php if(isset($order_msg)) { ?>
            onlineorderpopup();
            return true;
            <?php } ?> 
            showcatbundledproducts($(this).attr('pid'),$(this).attr('catId'));
        });
         $(document).on( "change",".cat-pizza-product-selection", function() {
              if($(this).hasClass('default')){
                def=0;
              }else{
                def=1;
              }
              $('#cat-pizza-prodt-options-' + $(this).attr('catid')).load("index.php?route=product/product/ajaxpizzacategoryproductoptions",{prodt_id: $(this).val(),def:def},function(response,statusTxt,xhr){
              });
              
        });
        $(document).on( "click",".button-pizza-customize", function() {
               <?php if(isset($order_msg)) { ?>
                 onlineorderpopup();
                 return true;
             <?php } ?> 
         
                div_id= $(this).attr('divid');
                $('#cat-pizza-prodt-popup-' +div_id + ' .modal-content').load("index.php?route=product/product/ajaxpizzaproductpopup",{cat_id: $(this).attr('divid')},function(response,statusTxt,xhr){
                if(statusTxt=="success"){
                 $('#cat-pizza-prodt-popup-' +div_id).modal('show');
                }
              });  
              
        });
        
        $(document).on('click','.cart-add-button', function() {
             <?php if(isset($order_msg)) { ?>
                 onlineorderpopup();
                 return true;
             <?php } ?> 
            var prod_id = $(this).attr('prod_id')
            etmAddtocart($(this).attr('prod_id'));   
           
        });
        
        $(document).on('click','.button-pizza-add', function() {
             <?php if(isset($order_msg)) { ?>
                 onlineorderpopup();
                 return true;
             <?php } ?> 
            var prod_id = $(this).attr('id')
            
            etmAddtocart($(this).attr('id'));   
            
        });
        $(document).on('click','.button-cart_walloptn', function() {
            <?php if(isset($order_msg)) { ?>
                onlineorderpopup();
                return true;
            <?php } ?> 
            divide = $(this).attr('div_id')
            showpopupoptions(divide,1);
            

        });  
    });
    function removeditem(key,quantity,product_id){
         $('#cart').load('index.php?route=module/cart #cart > *',{'minus1':key,'qty':quantity},function(){
              requestedtime_check();
              $('.productdatas-'+product_id+' .quantity').html(parseInt($('.productdatas-'+product_id+' .quantity').html())-1);
              $('.productdatas-'+product_id+' .remove').html('<div class="fa fa-minus-circle" title="-1" onclick="removeditem('+"'"+key+"'"+','+$('.productdatas-'+product_id+' .quantity').html()+','+product_id+');"></div>');
              if($('.productdatas-'+product_id+' .quantity').html()=='' || $('.productdatas-'+product_id+' .quantity').html()=='0'){
                    $('.productdatas-'+product_id+' .quantity').html('');
                    $('.productdatas-'+product_id+' .addtocartnb').show();
                    $('.productdatas-'+product_id+' .equiliserpropage').hide();
                }
          });
       
      }                            
    function addtocart_afterpostcodecheck(prod_id) {
        //prod_id = $(this).attr('prod_id');
        var url = location.protocol + '//' + location.host + '/'; 
        $.ajax({
            url: url+'index.php?route=checkout/cart/add',
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
                   // $('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                   // $('.success').fadeIn('slow');
                   // $('#cart-total').html(json['total']);
                   // jQuery('#cart-total').click();
                   // $('html, body').animate({scrollTop: 0}, 'slow');
                    $('#cart').load(url+'index.php?route=module/cart #cart > *');
                   //$('html, body').animate({scrollTop: 255}, 'slow');
                }
            }
        });
    }
    function add_prod_tocart_afterpostcodecheck(prod_id) {
        //prod_id = $(this).attr('id');
        var url = location.protocol + '//' + location.host + '/';
        $.ajax({
            url: url+'index.php?route=checkout/cart/add',
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
                  // $('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                  //  $('.success').fadeIn('slow');
                  //  $('#cart-total').html(json['total']);
                  //  jQuery('#cart-total').click();
                  //  $('html, body').animate({scrollTop: 0}, 'slow');
                   $('#cart').load(url+'index.php?route=module/cart #cart > *');
                   //$('html, body').animate({scrollTop: 255}, 'slow');
                }
            }
        });
    }

    function showpopupoptions(id,all) {
        
      //   var url = location.protocol + '//' + location.host + '/';
        if(typeof(all)==='undefined'){
            all_optn = 0;
        }else{
            all_optn = 1;
        }    
        $('#product-info_'+ id+' .product_description').hide();
        $('#product-info_'+ id+' .option').hide();
        $('#product-info_'+ id+' .product_option_price').hide();
        $('#product-info_'+ id+' .loader').show();
        $('#dialog_' + id + ' .modal-content').load("index.php?route=product/product/ajaxoptions&all="+all_optn,{prod_id: id},function(response,statusTxt,xhr){
            //alert(data);
            if(statusTxt=="success"){
                $('#dialog_'+ id).modal({backdrop:'static',keyboard:false});
                $('#product-info_'+ id+' .loader').hide();
                $('#product-info_'+ id+' .product_description').show();
                $('#product-info_'+ id+' .product_option_price').show();
                $('#product-info_'+ id+' .option').show();
                //$('#dialog_'+ id+' .modal-body').css('overflow-y', 'auto'); 
                //$('#dialog_'+ id+' .modal-body').css('max-height', ($(window).height() * 0.8-100));
            }                
            if(statusTxt=="error"){
                $('#product-info_'+ id+' .loader').hide();
                $('#product-info_'+ id+' .product_description').show();
                $('#product-info_'+ id+' .product_option_price').show();
                $('#product-info_'+ id+' .option').show();
                alert("Failed");
            }
            
        });
    }
    
    
    function showalloptions(id) {
        //alert(id);
        $('#product_options_dialog_' + id + ' .modal-content').load("index.php?route=product/product/ajaxallproductoptions",{prod_id: id},function(response,statusTxt,xhr){
            //alert(data);
             if(statusTxt=="success"){
                $('#product_options_dialog_'+ id).modal({backdrop:'static',keyboard:false})
                $('#product_options_dialog_'+ id+' .modal-body').css('overflow-y', 'visible'); 
                $('#product_options_dialog_'+ id+' .modal-body').css('max-height', $(window).height() * 0.8);
            }
            
            if(statusTxt=="error")    
               alert("Failed");
        });
    }
    
    function showcategoryproducts(id) {
        //alert(id);
        $('#product-info_'+ id+' .product_description').hide();
        $('#product-info_'+ id+' .option').hide();
        $('#product-info_'+ id+' .loader').show();
        $('#cat_prodt_dialog_' + id + ' .modal-content').load("index.php?route=product/product/ajaxcategoryproductpopup",{cat_id: id},function(response,statusTxt,xhr){
            //alert(data);
            if(statusTxt=="success"){
                $('#cat_prodt_dialog_'+ id).modal({backdrop:'static',keyboard:false});
                $('#product-info_'+ id+' .loader').hide();
                $('#product-info_'+ id+' .product_description').show();
                $('#product-info_'+ id+' .option').show();
            }
            
            if(statusTxt=="error"){
                alert("Failed");
                $('#product-info_'+ id+' .loader').hide();
                $('#product-info_'+ id+' .product_description').show();
                $('#product-info_'+ id+' .option').show();
            }
              
        });
    }
    function showbundledproducts(bundled_id){
    
        // $('#bundled_prodt_'+ bundled_id).html('');
        $('#product-info_'+ bundled_id+' .product_description').hide();
        $('#product-info_'+ bundled_id+' .option').hide();
        $('#product-info_'+ bundled_id+' .loader').show();
        $('#bundled_prodt_' + bundled_id + ' .modal-content').load("index.php?route=product/product/ajaxbundledproducts",{prod_id: bundled_id},function(response,statusTxt,xhr){
            if(statusTxt=="success"){
                $('#bundled_prodt_'+ bundled_id).modal({backdrop:'static',keyboard:false});
                $('#bundled_prodt_'+ bundled_id+' .bundled_prodt_options').css('overflow-y', 'visible'); 
                $('#bundled_prodt_'+ bundled_id+' .bundled_prodt_options').css('max-height', ($(window).height() * 0.8-150));
                $('#product-info_'+ bundled_id+' .loader').hide();
                $('#product-info_'+ bundled_id+' .product_description').show();
                $('#product-info_'+ bundled_id+' .option').show();
                
            }
                
            if(statusTxt=="error"){
                alert("Failed");
                $('#product-info_'+ bundled_id+' .loader').hide();
                $('#product-info_'+ bundled_id+' .product_description').show();
                $('#product-info_'+ bundled_id+' .option').show();
            }
               
        });
    }
    function showcatbundledproducts(bundled_id,cat_id){
        console.log("DEAL CAT POPUP A");
        // $('#bundled_prodt_'+ bundled_id).html('');
         $('#product-info_'+ cat_id+' .product_description').hide();
        $('#product-info_'+ cat_id+' .option').hide();
        $('#product-info_'+ cat_id+' .loader').show();
        $('#cat_bundled_prodt_' + cat_id + ' .modal-content').load("index.php?route=product/product/ajaxbundledproducts",{prod_id: bundled_id},function(response,statusTxt,xhr){
            if(statusTxt=="success"){
                $('#cat_bundled_prodt_'+ cat_id).modal({backdrop:'static',keyboard:false});
                $('#cat_bundled_prodt_'+ cat_id+' .bundled_prodt_options').css('overflow-y', 'visible'); 
                $('#cat_bundled_prodt_'+ cat_id+' .bundled_prodt_options').css('max-height', ($(window).height() * 0.8-150));
                $('#product-info_'+ cat_id+' .loader').hide();
                $('#product-info_'+ cat_id+' .product_description').show();
                $('#product-info_'+ cat_id+' .option').show();
            }
                
            if(statusTxt=="error"){
                alert("Failed");
                $('#product-info_'+ cat_id+' .loader').hide();
                $('#product-info_'+ cat_id+' .product_description').show();
                $('#product-info_'+ cat_id+' .option').show();
            }
              
        });
    }
    function requestedtime_check(){
	if($("input[name='shipping_method']:checked").val()!=undefined){
            //selectShippingMethod($("input[name='shipping_method']:checked").val());
	}        
    }
    
//--></script>