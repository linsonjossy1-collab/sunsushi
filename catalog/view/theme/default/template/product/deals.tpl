<?php echo $header;   ?> 

<div id="catpage">
    <div id="bgclr">
        <div id="contentcat" class="col-sm-8 col-md-9">
            <div id="column-left"><?php echo $categorydeal; ?></div>
            <div class="lc_offer"></div>
            <div class="col-lg-12"></div>
            <div class="col-lg-12 dealnopadding">
                <div class="flexwrap">
                     <?php $bundledidvalh = 1; ?>
                 <?php foreach($bund_heads as $m=>$bhdata) { ?>
                    <?php for($j=0;$j<$bhdata['bundle_quantity'];$j++) { ?>
                        <div class="deal_headnew <?php echo ($bundledidvalh==1)?'active':''; ?>" id="deal_headnew_<?php echo $bundledidvalh; ?>"> 
             
                                <div class="fa fa-check" style="display:none"></div>
                                <?php if($bhdata['bundled_qty']==1) { ?>
                                    <div class="bundle_name"><?php echo $bhdata['bundle_name'];?></div>
                                <?php } else { ?>
                                    <div class="bundle_name"><?php echo $bhdata['bundle_name'].' '.($j+1);?></div>
                                <?php } ?>  
                 
                        </div>
                        <?php $bundledidvalh +=1;?>
                    <?php } ?>  
                 <?php } ?>  
                 </div>
                <?php   $bundledidval = 1;
                        $initemqty=0;
                if(!empty($product_bundle)) {
                ?>
                 
                  
                <?php foreach($product_bundle as $k=>$bundle) { ?>
                     <?php $initemqty +=(int)$bundle['bundled_qty'] ?>
                    <?php for($i=0;$i<$bundle['bundled_qty'];$i++) { ?>
                        
                        <div class="deal_contentn col-lg-12" id="bundleitem_<?php echo $main_product_id; ?>_<?php echo $bundledidval; ?>" style="display: <?php echo ($bundledidval==1)?'':'none'; ?>">
                            <?php foreach($bundle['products'] as $key=>$product){ ?>
                            <?php   if(!empty($product['options'])) {  ?>
                            <div class="col-xs-6 col-sm-6 col-md-3 product-info_simple" onclick="selectbundleitem('<?php echo $main_product_id; ?>','<?php echo $bundle['bundled_id']; ?>','<?php echo $bundledidval; ?>','<?php echo $product['product_id'];?>')">
                            <?php }else{ ?>
                            <div class="col-xs-6 col-sm-6 col-md-3 product-info_simple"  onclick="bundleitemtocart('<?php echo $main_product_id; ?>','<?php echo $bundle['bundled_id']; ?>','<?php echo $bundledidval; ?>','<?php echo $product['product_id'];?>')">
                            <?php } ?>  
                                <div class="img_flexer">
                                    <div class="product-img-left">
                                    <div class="product_image">
                                        <img src="<?php echo $product['thumb']; ?>" />
                                        <div class="option">
                                            <div class="productdetails">
                                                
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-img-right">
                                        <div class="product_name">
                                            <?php echo $product['name']; ?>
                                        </div>
                                        <div class="product_description">
                                            <?php echo $product['description']; ?>
                                        </div>
                                        <div class="option">
                                            <div class="productdetails">
                                                <div class="product_option_price">
                                                    <a class="btn customadd">Add</a>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <?php } ?>  
                        </div>
                        <?php $bundledidval +=1;?>
                     <?php } ?>  
            <?php } ?>  
            <?php } ?>   
            
            </div>
        </div>
       
        
        <div id="column-right" class="col-sm-4 col-md-3 hidden-xs">
            <div id="side_cart_open">
                <?php echo $cartshipping; ?>
                <?php echo $cart; ?>
             </div>
        </div>
        <div style="clear:both;"></div>
    </div>
</div>
<div class="modal fade etm-popup-prodts" tabindex="-1" role="dialog" id="etm_deal_options">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
        </div>
    </div>
</div>
<script type="text/javascript">
    var totbundledty='<?php echo $total_bundled_qty; ?>';
    var addedbundqty=0;
    var bundledidval=1;
    $(document).ready(function(){
        $("body").addClass("etmdeals");
    });
    function selectbundleitem(main_id,bundleid,bundleidval,prdid){
        $('#etm_deal_options .modal-content').html('');
        
        console.log(main_id+'-->'+bundleid+'-->'+bundleidval+'-->'+prdid);
        $('#etm_deal_options .modal-content').load("index.php?route=product/product/bundleditemoptions",{bundled_id: bundleid,product_id: prdid,bundleidval: bundleidval,main_product_id:main_id },function(response,statusTxt,xhr){
            //alert(data);
            if(statusTxt=="success"){
                $('#etm_deal_options').modal({backdrop:'static',keyboard:false});
                /*
                $('#product-info_'+ id+' .loader').hide();
                $('#product-info_'+ id+' .product_description').show();
                $('#product-info_'+ id+' .product_option_price').show();
                $('#product-info_'+ id+' .option').show();
               */
            }                
            if(statusTxt=="error"){
                /*
                $('#product-info_'+ id+' .loader').hide();
                $('#product-info_'+ id+' .product_description').show();
                $('#product-info_'+ id+' .product_option_price').show();
                $('#product-info_'+ id+' .option').show();
                */
                alert("Failed");
            }
            
        });
    }
     function bundleprodttocart(main_id,bundledidval)
                {
                    //$('.bundled_prodt_options').hide();  
                    if($('#btnbndlecart').hasClass('disabled')) {
                        return;
                    }
                    $('#btnbndlecart').addClass('disabled');
                    //$('#bundled_product_detail_div_'+main_id+' .error').remove();    
                    if(addedbundqty==(totbundledty-1)){
                        $( "#productaccordion_"+main_id+'_' + bundledidval+' .complt').after('<input type="hidden" name="dealcomplete" value="<?php echo $main_product_id; ?>">');
                        //$('.popupcartbtn')
                    }
                    $.ajax({
                        url: 'index.php?route=checkout/cart/add',
                        type: 'post',
                        data: $('#productaccordion_'+main_id+'_' + bundledidval + ' input[type=\'text\'], #productaccordion_' + main_id+'_' +bundledidval + ' input[type=\'hidden\'], #productaccordion_' +main_id+'_' + bundledidval + ' input[type=\'radio\']:checked, #productaccordion_' +main_id+'_' + bundledidval + ' input[type=\'checkbox\']:checked, #productaccordion_' +main_id+'_' + bundledidval +  '  select, #productaccordion_' +main_id+'_' + bundledidval +  ' textarea'),
                        //data: $('#bundled_prodt_'+main_id+ ' input[type=\'text\'], #bundled_prodt_' + main_id+ ' input[type=\'hidden\'], #bundled_prodt_' +main_id+ ' input[type=\'radio\']:checked, #bundled_prodt_' +main_id+' input[type=\'checkbox\']:checked, #bundled_prodt_' +main_id+  '  select, #bundled_prodt_' +main_id+ ' textarea'),
                        dataType: 'json'
                    }).done(function (json) {
                        if (json['error']) {
                            if (json['error']['option']) {
                                for (i in json['error']['option']) {
                                        $('#productaccordion_'+main_id+'_' +bundledidval +'  #option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                                        $('#productaccordion_'+main_id+'_' +bundledidval+  '  #optioncus-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');                             
                                }
                                
                                $('#productaccordion_'+main_id+'_' +bundledidval+  ' .panel-heading').removeClass('collapsed');
                                $('#productaccordion_'+main_id+'_' +bundledidval+  ' .collapse').removeClass('in');
                                $('#productaccordion_'+main_id+'_' +bundledidval+  ' .panel-heading').addClass('collapsed');
                                $('#productaccordion_'+main_id+'_' +bundledidval+  ' .optionpanel .panel-heading').removeClass('collapsed');
                                $('#productaccordion_'+main_id+'_' +bundledidval+  ' .optionpanel .collapse').addClass('in');
                                $('#productaccordion_'+main_id+'_' +bundledidval+  ' .optionpanel .collapse.in').css('min-height',250);                        
                            }
                            if(json['error']['warning']!=undefined) {
                                //$('#productaccordion_'+main_id+'_' +bundledidval).append('<div class="error alert alert-danger">'+json['error']['warning']+'</div>');
                                //$('.dlefooter').html('<button type="button" style="float: left" class="btn btn-default" onclick="cancelDeal('+main_id+')">Cancel</button>');
                            }
                        }
                        if (json['success']) {
                            $('.modal').modal('hide');
                            //$('#column-right').append('<div class="alert alert-success" ><span class="fa fa-check"><span>'+json['prname']+'</div>');//prname
                            $('#deal_headnew_'+bundledidval+' .bundle_name').html(json['prname']);
                            $('.deal_headnew .col-sm-2').removeClass('active');
                            $('#deal_headnew_'+bundledidval).addClass('bundadd');
                            $('#deal_headnew_'+bundledidval+' .fa').show();
                            $('#deal_headnew_'+(parseInt(bundledidval)+1)).addClass('active');
                           // selectedelem = $('#'+main_id+'_' + bundledidval +" .deal_product_select option:selected").text();
                            //$('.bundle_added_items .list-group').append('<li class="alert alert-info" >'+selectedelem+'<span class="right glyphicon glyphicon-ok"></span><li>')
                            $('#bundleitem_'+main_id+'_' +bundledidval).hide();
                            $('#bundleitem_'+main_id+'_' +(parseInt(bundledidval)+1)).show();
                            $('#notification').html('<div class="success">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                            $('.success').delay(2000).fadeOut();
                             $('html, body').animate({
                                scrollTop: $("#deal_headnew_"+bundledidval).offset().top-60
                            }, 100);
                            //All Add
                            bundledidval +=1;
                            addedbundqty +=1;
                            if(addedbundqty==totbundledty){
                                $('.modal').modal('hide');
                                location = 'menu';
                            }
                        }            
                        $('#btnbndlecart').removeClass('disabled');
                    }).fail(function (jqXHR, textStatus) 
                    {
                        $('#btn'+main_id+'_'+bundle_id).removeClass('disabled');
                    });
                }
                function closebundlepopup(main_id){
                    if(addedbundqty>0){
                        cancelDeal(main_id);
                    }else{
                        $('.modal').modal('hide');
                    }
                }
                function cancelDeal(main_id){
                    $.ajax({
                        url: 'index.php?route=checkout/cart/removebundle',
                        type: 'post',
                        data:{ main_product_id:main_id } ,
                        dataType: 'json',
                        success: function(json) {
                            if (json['success']) {
                                $('.modal').modal('hide');
                                $('#notification').html('<div class="success"> <?php echo $product_name; ?> is Removed from cart<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                                $('.success').delay(1000).fadeOut();
                                $('#cart').load('index.php?route=module/cart #cart > *',function(){

                                });                                     
                            }                          
                        }
                    });
                } 
                
                 function bundleitemtocart(main_id,bundleid,bundledidval,productid)
                {
                    //console.log(main_id+'--->'+bundleidval);
                    postdata={main_product_id:main_id,bundle_id:bundleid,bundleidval:bundledidval,product_id:productid};
                    if(addedbundqty==(totbundledty-1)){
                        postdata['dealcomplete']='<?php echo $main_product_id; ?>';
                        //$("#bundleinitem_"+main_id+'_' + bundledidval+' .complt').after('<input type="hidden" name="dealcomplete" value="<?php echo $main_product_id; ?>">');
                        //$('.popupcartbtn')
                    }
                    $.ajax({
                        url: 'index.php?route=checkout/cart/add',
                        type: 'post',
                        data:postdata,
                        //data: $('#bundleinitem_'+main_id+'_' + bundledidval + ' input[type=\'text\'], #bundleinitem_' + main_id+'_' +bundledidval + ' input[type=\'hidden\'], #bundleinitem_' +main_id+'_' + bundledidval + ' input[type=\'radio\']:checked, #bundleinitem_' +main_id+'_' + bundledidval + ' input[type=\'checkbox\']:checked, #bundleinitem_' +main_id+'_' + bundledidval +  '  select, #bundleinitem_' +main_id+'_' + bundledidval +  ' textarea'),
                        //data: $('#bundled_prodt_'+main_id+ ' input[type=\'text\'], #bundled_prodt_' + main_id+ ' input[type=\'hidden\'], #bundled_prodt_' +main_id+ ' input[type=\'radio\']:checked, #bundled_prodt_' +main_id+' input[type=\'checkbox\']:checked, #bundled_prodt_' +main_id+  '  select, #bundled_prodt_' +main_id+ ' textarea'),
                        dataType: 'json'
                    }).done(function (json) {
                        if (json['error']) {
                            if (json['error']['option']) {
                               }
                            if(json['error']['warning']!=undefined) {
                                }
                        }
                        if (json['success']) {
                            //$('.modal').modal('hide');
                            //$('#column-right').append('<div class="alert alert-success" ><span class="fa fa-check"><span>'+json['prname']+'</div>');//prname
                            $('#deal_headnew_'+bundledidval+' .bundle_name').html(json['prname']);
                            $('.deal_headnew .col-sm-2').removeClass('active');
                            $('#deal_headnew_'+bundledidval).addClass('bundadd');
                            $('#deal_headnew_'+bundledidval+' .fa').show();
                            $('#deal_headnew_'+(parseInt(bundledidval)+1)).addClass('active');
                           // selectedelem = $('#'+main_id+'_' + bundledidval +" .deal_product_select option:selected").text();
                            //$('.bundle_added_items .list-group').append('<li class="alert alert-info" >'+selectedelem+'<span class="right glyphicon glyphicon-ok"></span><li>')
                            $('#bundleitem_'+main_id+'_' +bundledidval).hide();
                            $('#bundleitem_'+main_id+'_' +(parseInt(bundledidval)+1)).show();
                            $('#notification').html('<div class="success">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                            $('.success').delay(2000).fadeOut();
                             $('html, body').animate({
                                scrollTop: $("#deal_headnew_"+bundledidval).offset().top-60
                            }, 200);
                            //All Add
                            bundledidval +=1;
                            addedbundqty +=1;
                            if(addedbundqty==totbundledty){
                                $('.modal').modal('hide');
                                location = 'menu';
                            }
                        }            
                        $('#btnbndlecart').removeClass('disabled');
                    }).fail(function (jqXHR, textStatus) 
                    {
                        $('#btn'+main_id+'_'+bundle_id).removeClass('disabled');
                    });
                }
</script>
<?php echo $footer; ?>