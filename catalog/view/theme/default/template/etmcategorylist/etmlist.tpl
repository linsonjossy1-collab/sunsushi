<?php echo $header;   ?> 
<script language="javascript" type="text/javascript">
    window.dataLayer = window.dataLayer || [];
window.dataLayer.push({
  event: "view_item_list",
  ecommerce: {
    item_list_id: "Menu",     // Optional: internal ID for the list
    item_list_name: "View Menu",   // Recommended: human-readable name
    items: [
      
    ]
  }
});

</script>
<script language="javascript" type="text/javascript">
    function limitText(limitField, limitCount, limitNum) {
    if (limitField.value.length > limitNum) {
    limitField.value = limitField.value.substring(0, limitNum);
            } else {
    limitCount.value = limitNum - limitField.value.length;
            }
    }
</script>
<?php    echo $postcodecategory;;?>
<!--<!-- delivery selection popup -->
    <!--
<div class="modal fade" id="choosemethod" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
   
    <div class="modal-content rounded-5" >
        
        
        <button id="postchead" type="button" onclick="$('#postcodeselectdiv').hide();$('#postchead').hide();$('#mainshipping').show();" style="display: none" class="small position-absolute close custompositontwo zindex rounded-2 pt-1 pb-1 border-0 shadow-sm shipback1">
            <span aria-hidden="true" class="small">Go Back</span>
        </button>
        <button id="reqhead" type="button" onclick="$('#cart_requested_times').hide();$('#reqhead').hide();$('#mainshipping').show();" style="display: none" class="small position-absolute close custompositontwo zindex rounded-2 pt-1 pb-1 border-0 shadow-sm shipback1">
            <span aria-hidden="true" class="small">Go Back</span>
        </button>
        <div class="modal-body row p-0 align-items-center">
            <div class="col-lg-12 ps-5" id="mainshipping">
              <h3 class="fw-bold colorone text-center">Choose Delivery or Pick Up</h3>
                    <hr class="w-50 ml-0 mb-4 mt-1">
              <div class="row">
              <div class="col-sm-6 text-center">
              <h2>DELIVERY</h2>
              <svg class="svg choice__svg choice__svg--icon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" width="120" height="120" xml:space="preserve">
    <path d="M119.467,337.067c-28.237,0-51.2,22.963-51.2,51.2c0,28.237,22.963,51.2,51.2,51.2s51.2-22.963,51.2-51.2 C170.667,360.03,147.703,337.067,119.467,337.067z M119.467,422.4c-18.825,0-34.133-15.309-34.133-34.133 c0-18.825,15.309-34.133,34.133-34.133s34.133,15.309,34.133,34.133C153.6,407.091,138.291,422.4,119.467,422.4z"></path><path d="M409.6,337.067c-28.237,0-51.2,22.963-51.2,51.2c0,28.237,22.963,51.2,51.2,51.2c28.237,0,51.2-22.963,51.2-51.2 C460.8,360.03,437.837,337.067,409.6,337.067z M409.6,422.4c-18.825,0-34.133-15.309-34.133-34.133 c0-18.825,15.309-34.133,34.133-34.133c18.825,0,34.133,15.309,34.133,34.133C443.733,407.091,428.425,422.4,409.6,422.4z"></path><path d="M510.643,289.784l-76.8-119.467c-1.57-2.441-4.275-3.917-7.177-3.917H332.8c-4.719,0-8.533,3.823-8.533,8.533v213.333 c0,4.719,3.814,8.533,8.533,8.533h34.133v-17.067h-25.6V183.467h80.674l72.926,113.442v82.825h-42.667V396.8h51.2 c4.719,0,8.533-3.814,8.533-8.533V294.4C512,292.77,511.531,291.157,510.643,289.784z"></path><path d="M375.467,277.333V217.6h68.267v-17.067h-76.8c-4.719,0-8.533,3.823-8.533,8.533v76.8c0,4.719,3.814,8.533,8.533,8.533h128 v-17.067H375.467z"></path><path d="M332.8,106.667H8.533C3.823,106.667,0,110.49,0,115.2v273.067c0,4.719,3.823,8.533,8.533,8.533H76.8v-17.067H17.067v-256 h307.2v256H162.133V396.8H332.8c4.719,0,8.533-3.814,8.533-8.533V115.2C341.333,110.49,337.519,106.667,332.8,106.667z"></path><rect x="8.533" y="345.6" width="51.2" height="17.067"></rect><rect x="179.2" y="345.6" width="145.067" height="17.067"></rect><rect x="469.333" y="345.6" width="34.133" height="17.067"></rect><rect x="34.133" y="140.8" width="298.667" height="17.067"></rect><rect x="110.933" y="379.733" width="17.067" height="17.067"></rect><rect x="401.067" y="379.733" width="17.067" height="17.067"></rect><rect x="34.133" y="72.533" width="119.467" height="17.067"></rect><rect y="72.533" width="17.067" height="17.067"></rect></svg>
              <p>Order takeaway, we deliver ...</p>
              <button class="btn btn-primary btn-lg w-100 text-black customash shadow-sm" onclick="selectShippingMethodnew('home.home');">
                 ORDER FOR DELIVERY
              </button>
              </div>
              <div class="col-sm-6 text-center">
                            <h2>COLLECTION</h2>
              <svg class="svg choice__svg choice__svg--icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="120" height="120">
                    <path d="M488,264H424.7L439.361,88H448a8,8,0,0,0,8-8V64a24.028,24.028,0,0,0-24-24H304a24.028,24.028,0,0,0-24,24V80a8,8,0,0,0,8,8h8.639l4,48H160a8,8,0,0,0-7.761,6.06l-16,64a7.985,7.985,0,0,0,0,3.88L149.754,264H88a8,8,0,0,0-8,8v80H24a8,8,0,0,0-8,8V464a8,8,0,0,0,8,8H88a8,8,0,0,0,8-8v-8H263.018a43.359,43.359,0,0,0,20.7-5.278L362.04,408H488a8,8,0,0,0,8-8V272A8,8,0,0,0,488,264Zm-8,64H278.7a35.153,35.153,0,0,0-2.843-16H328a8,8,0,0,0,7.761-6.06L342.246,280H480ZM270.246,296l-4-16h59.508l-4,16Zm-1.821,54.888A35.277,35.277,0,0,0,273.786,344h51.968l-4,16H270.246l-2.186-8.746ZM296,64a8.009,8.009,0,0,1,8-8H432a8.009,8.009,0,0,1,8,8v8H296ZM423.305,88l-1.333,16H314.028L312.7,88ZM315.361,120H420.639l-12,144H346.246l13.515-54.06a7.985,7.985,0,0,0,0-3.88l-16-64A8,8,0,0,0,336,136H316.694ZM166.246,152H329.754l12,48H154.246Zm95.508,64-4,16H238.246l-4-16Zm-107.508,0h63.508l6.485,25.94A8,8,0,0,0,232,248h32a8,8,0,0,0,7.761-6.06L278.246,216h63.508l-12,48H166.246Zm95.508,64,3.011,12.045a35.254,35.254,0,0,0-30.875,6.128L183.541,328H96V280ZM96,360V344h34.207a43.232,43.232,0,0,0-18.088,32H96ZM80,456H32V368H80Zm196.056-19.325A27.3,27.3,0,0,1,263.018,440H96V392h19.227A12.788,12.788,0,0,0,128,379.227,27.258,27.258,0,0,1,155.227,352h19.666a11.287,11.287,0,0,0,6.893-2.365L231.713,310.8a19.227,19.227,0,0,1,25.4,28.772l-49.261,49.26A11.226,11.226,0,0,0,215.79,408H328.627ZM227.314,392l27.552-27.552,1.373,5.492A8,8,0,0,0,264,376h64a8,8,0,0,0,7.761-6.06L342.246,344H480v48Z"></path></svg>
              <p>Order takeaway, you collect ...</p>
              <button class="btn btn-primary btn-lg w-100 text-black customash shadow-sm" onclick="selectShippingMethodnew('collect.collect');">
                   ORDER FOR COLLECTION
              </button>
              </div>
              </div>
            </div>
             
             <div class="col-lg-12 ps-5" id="postcodeselectdiv" style="display: none">
             <div class="row">
                             <div class="col-sm-12" >
                             <p class="vsmall alert alert-danger text-center" id="postcodemessage4" style="display: none" role="alert"></p>                        
                    </div>
                <div class="col-sm-12">
                    <h3>ENTER DELIVERY POSTCODE</h3>
                    <input type="text" class="form-control pt-3 pb-3" id="inputdelivery" placeholder="Your Postcode">
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary btn-lg coloronebg text-white pt-3 pb-3 ps-4 pe-4 shadow-sm" onclick="postcode_checknew()">SEARCH</button>
                    <a href="http://www.royalmail.com/postcode-finder/" class="mt-1 vsmall text-dark text-decoration-none text-black-50">Don't know your postcode ?</a>
                </div>

                
                </div>
            </div> 
            
             <div class="col-lg-12 ps-5" id="cart_requested_times" style="display: none">
                          
             </div>
            
            
        </div>
    </div>
    
  </div>
   
    
</div>
-->

<style>
/* Product thumbnails now carry width/height attributes (reserves space, less layout shift).
   Keep the aspect ratio when the existing max-width:100% rule narrows them. */
.product_image img{height:auto}
/* Keep the footer below the fold while the menu categories are still loading,
   so it is not pushed down (layout shift) as each category arrives. */
#tabs.etm-loading{min-height:100vh}
</style>
<div id="catpage">
<div id="bgclr">
<script>
$(function () {
    shoppingcartclick = function() {
        checkoutsidebar();
        return false;
    }   
  $('.customize_option_single').bind('click', function() {

    $('.dialog_'+$(this).attr('id')).dialog({
                                            autoOpen: true,
                                            height: 300,
                                            width: 350,
                                            modal: true,
                                            resizable: false,
                                            buttons: {
                                                "OK": function() {
                                                    $(this).dialog('close');
                                                },
                                            },
                                        });
 
    });

});
</script>
<script>
var carprogresscount = 0;
// Menu category cache. sessionStorage can be missing, blocked, or full
// (QuotaExceededError on iOS Safari / in-app browsers), so every access is
// wrapped: a storage failure only skips caching and never stops the menu.
var etmMenuCache = (function () {
    var store = null;
    try {
        if (typeof(Storage) !== "undefined" && window.sessionStorage) {
            store = window.sessionStorage;
        }
    } catch (e) {
        store = null;
    }
    return {
        get: function (key) {
            if (!store) { return null; }
            try { return store.getItem(key); } catch (e) { return null; }
        },
        set: function (key, value) {
            if (!store) { return false; }
            try { store.setItem(key, value); return true; } catch (e) { return false; }
        },
        clear: function () {
            if (!store) { return; }
            try { store.clear(); } catch (e) {}
        }
    };
})();
if (etmMenuCache.get('cached_time')) {
    if (etmMenuCache.get('cached_time') != <?php echo $cached_time;?> ) {
        etmMenuCache.clear();
        etmMenuCache.set('cached_time', <?php echo $cached_time;?>);
    }
}
else {
    etmMenuCache.clear();
    etmMenuCache.set('cached_time', <?php echo $cached_time;?>);
}
function carprogressshow(){
    if(carprogresscount==0){
        $('.prgloader').hide();
        $('.product-info_simple').removeClass('odd row');
        $('.product-info_simple').removeClass('even row');
        // $('.product-info_simple').removeClass('col-sm-12');
        $('.product-info_simple:odd').addClass('odd row');
        $('.product-info_simple:even').addClass('even row');
        // $('.product-info_simple').addClass('col-sm-12');
        
    }
    else {
         $('.prgloader').show();
    }
}
// Category responses can arrive in any order. Inserting them as they arrive
// pushed already-visible categories and the footer down (layout shift), so
// responses are buffered and inserted strictly in menu order. A category that
// is still missing after ETM_CAT_WAIT_MS stops holding back the ones after it.
var ETM_CAT_WAIT_MS = 8000;
var etmCatOrder = <?php echo json_encode(isset($category_layouts) && is_array($category_layouts) ? array_map('intval', array_keys($category_layouts)) : array()); ?>;
var etmCatBuffer = {};
var etmCatPassed = {};
var etmCatNext = 0;
function etmApplyMenuFilters() {
    if (typeof checkitemisincart === 'function') {
        checkitemisincart();
    }
    $('.pcarry').hide();
    $('.pcollect').hide();
    $('.phome').hide();
    $('.pdine').hide();
    <?php if(isset($delivery_method) && $delivery_method!=''){ ?>
        var delmethode = '<?php echo $delivery_method; ?>';
        del = delmethode.split(".");
        $(".p"+del[0]).show();
    <?php }else{  ?>
        $('.pcollect').show();
    <?php } ?>
}
function etmInsertCategory(id, html) {
    // html === null means the request failed: leave the block empty, as before.
    if (html !== null) {
        $('#cat_' + id).html(html);
    }
    carprogresscount -= 1;
    carprogressshow();
}
function etmFlushCategories(force) {
    var inserted = false;
    while (etmCatNext < etmCatOrder.length) {
        var cid = etmCatOrder[etmCatNext];
        if (etmCatBuffer.hasOwnProperty(cid)) {
            var html = etmCatBuffer[cid];
            delete etmCatBuffer[cid];
            etmInsertCategory(cid, html);
            inserted = true;
        } else if (force) {
            etmCatPassed[cid] = true; // still loading: insert it whenever it arrives
        } else {
            break;
        }
        etmCatNext++;
    }
    if (etmCatNext >= etmCatOrder.length) {
        $('#tabs').removeClass('etm-loading');
    }
    if (inserted) {
        etmApplyMenuFilters();
    }
}
function etmCategoryReady(id, html) {
    if (etmCatPassed[id]) {
        delete etmCatPassed[id];
        etmInsertCategory(id, html);
        etmApplyMenuFilters();
        return;
    }
    etmCatBuffer[id] = html;
    etmFlushCategories(false);
}
$(function () {
    if (!etmCatOrder.length) {
        $('#tabs').removeClass('etm-loading');
    }
    setTimeout(function () { etmFlushCategories(true); }, ETM_CAT_WAIT_MS);
});
$(function () {
    var msie6 = $.browser == 'msie' && $.browser.version < 7;
    if ($(window).width()>= 768 && !msie6) {
        $.fn.isInViewport = function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();

            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();

            return elementBottom > viewportTop && elementTop < viewportBottom;
        };
        
        $.fn.heightInViewport = function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();

            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            if(elementTop < viewportBottom) {
                return viewportBottom - elementTop ;
            }
            else {
                return 0;
            }
        };

    }
});
</script>

    <div id="contentcat" class="col-sm-7 col-md-8 col-lg-9">
    <div id="custom-search-input">
    <div class="input-group col-md-12">
        <input type="text" onchange="searchproduct();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" id="search_product" class="form-control input-lg" placeholder="&#xF002; Search Product" />
    </div>
</div>
    <?php echo $column_left; ?>
    <div class="lc_offer"><?php echo $content_top; ?></div>
        <!--<?php if(isset($store_closed)){ ?>
        <div id="menu_closed_msg"><?php echo $store_closed; ?></div>
        <?php } ?>-->
        <div>

  <!-- Nav tabs -->
<div class="col-lg-12">

 <?php echo $menushipping; ?>
 </div>
  <div class="prgloader"></div>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#menutab" aria-controls="menutab" role="tab" data-toggle="tab">Menu</a></li>
     <li role="presentation" ><a href="#infotab" aria-controls="infotab" role="tab" data-toggle="tab">Information</a></li>
    <li role="presentation"><a href="#workinghours" aria-controls="settings" role="tab" data-toggle="tab">Working Hours</a></li>
  </ul>

  <!-- Tab panes -->

  <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="menutab">
          <div id="tabs" class="etm-loading">

        <?php foreach($category_layouts as $key=>$layout){ ?>
        <div id="cat_<?php echo  $key;?>" class="prod_main_categories  <?php if(isset($category_shippings[$key])){ foreach($category_shippings[$key] as $catshipping){ echo "p".$catshipping." "; } } ?>">
            
           <?php // echo $layout; ?>
        </div>
            <script type="text/javascript">
            $(function() {
                carprogresscount += 1;
                carprogressshow();
                var cachedcat = etmMenuCache.get('cached_cat_<?php echo  $key;?>');
                if(cachedcat) {
                    // Inserted in menu order, then cart badges + delivery/collection filter.
                    etmCategoryReady(<?php echo (int) $key; ?>, cachedcat);
                }
                else {
                    $.get( "index.php?route=product/category/getetmmaillayout&category_id=<?php echo  $key;?>", function(data) {
                        // Inserted in menu order, then cart badges + delivery/collection filter.
                        etmCategoryReady(<?php echo (int) $key; ?>, data);
                        // Optional cache write, last so a full/blocked storage cannot skip the UI updates above.
                        etmMenuCache.set('cached_cat_<?php echo  $key;?>', data);
                    }).fail(function() {
                     // alert( "error" );
                        // Release the categories queued behind this one.
                        etmCategoryReady(<?php echo (int) $key; ?>, null);
                    });
                }
            });
            </script>
        <?php } ?>
            
            

    </div>
        <?php if (!$categories && !$products) { ?>
        <div class="content"><?php echo $text_empty; ?></div>
        <div class="buttons">
            <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
        </div>
        <?php } ?>
          
      </div>
      
      
      <div role="tabpanel" class="tab-pane" id="deliveryareas">
          <ul>
            <?php foreach($delivery_details as $delivery_detail){ ?>
            <?php if(isset($delivery_detail['postcode'])){ ?>
            <li> <span class="del_span"><?php echo $delivery_detail['cartamount']; ?></span> <span class="del_span"><?php echo $delivery_detail['postcode']; ?></span> <span class="del_span"><?php echo $delivery_detail['area']; ?></span>
              <?php if((float)$delivery_detail['shippingamount']){ ?>
              <!-- <span class="del_span"><?php echo $this->currency->format($delivery_detail['shippingamount']); ?></span>-->
              <?php } ?>
            </li>
            <?php }else{  ?>
            <li> <span class="del_span">Minimum order <?php echo $delivery_detail['cartamount']; ?></span> <span class="del_span">With in <?php echo $delivery_detail['distance']; ?> Miles</span>
              <?php if((float)$delivery_detail['shippingamount']){ ?>
              <!-- <span class="del_span"><?php echo $this->currency->format($delivery_detail['shippingamount']); ?></span>-->
              <?php } ?>
            </li>
            <?php }  ?>
            <?php }  ?>
          </ul>
      </div>
      
      
      <div role="tabpanel" class="tab-pane" id="infotab">
      <h4>Minimum Order For Delivery</h4>
          <ul>
            <?php foreach($delivery_details as $delivery_detail){ ?>
            <?php if(isset($delivery_detail['postcode'])){ ?>
            <li> <span class="del_span"><?php echo $delivery_detail['cartamount']; ?></span> <span class="del_span"><?php echo $delivery_detail['postcode']; ?></span> <span class="del_span"><?php echo $delivery_detail['area']; ?></span>
              <?php if((float)$delivery_detail['shippingamount']){ ?>
              <!-- <span class="del_span"><?php echo $this->currency->format($delivery_detail['shippingamount']); ?></span>-->
              <?php } ?>
            </li>
            <?php }else{  ?>
            <li> <span class="del_span">Minimum order <?php echo $delivery_detail['cartamount']; ?></span> <span class="del_span">With in <?php echo $delivery_detail['distance']; ?> Miles</span>
              <?php if((float)$delivery_detail['shippingamount']){ ?>
              <!-- <span class="del_span"><?php echo $this->currency->format($delivery_detail['shippingamount']); ?></span>-->
              <?php } ?>
            </li>
            <?php }  ?>
            <?php }  ?>
          </ul>
          
          <h4>Payment Methods Accepted</h4>
          <ul>
            <?php foreach($payments as $payment){ ?>
                <li><?php echo $payment;?></li>
             <?php } ?>
         </ul>
      </div>
    <div role="tabpanel" class="tab-pane" id="paymentmethods"><ul>
            <?php foreach($payments as $payment){ ?>
                <li><?php echo $payment;?></li>
             <?php } ?>
         </ul></div>
      <div role="tabpanel" class="tab-pane" id="workinghours">
          <ul>
            <?php foreach($store_times as $day=>$store_time){ ?>
            <li> <span class="pop-day"> <?php echo $day; ?> </span>
                      <?php $d=1; ?>
                      <?php if(!empty($store_time)){ ?>
                          <?php foreach($store_time as $time){ ?>
                          <span class="pop-time"><?php echo $time['start'].' - '.$time['end']; ?></span>
                          <?php if($d<count($store_time)){ ?>
                          <span id="time_and">&AMP;</span>
                          <?php } ?>
                          <?php $d++; ?>
                          <?php } ?>
                      <?php }else{ ?>
                      <span class="pop-time">Closed</span>
                      <?php } ?>
                    </li>
            <?php } ?>
          </ul>
      </div>
  </div>
</div>
</div>
<?php echo $column_right; ?>
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
    $('#review .pagination a').on('click', function() {
        $('#review').fadeOut('slow');
                $('#review').load(this.href);
                $('#review').fadeIn('slow');
                return false;
        });
                ;
                //--></script> 
<div style="clear:both;"></div>
</div>
</div>
<div style="display: none" class="smart-deals-parent-div" title="Deal Available"></div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.product-info_simple item:odd').addClass('odd');
        $('.product-info_simple item:even').addClass('even');
        $('#left-side-bar-category li').on('click', function() {
            if(!$('#menutab').hasClass('active')){
                $('.nav-tabs li').removeClass('active');
                $('.nav-tabs li:first').addClass('active');
                $('.tab-pane').removeClass('active');
                $('#menutab.tab-pane').addClass('active');
            }
        });     
        $('#mobileonlymenu li').on('click', function() {
            if(!$('#menutab').hasClass('active')){
                $('.nav-tabs li').removeClass('active');
                $('.nav-tabs li:first').addClass('active');
                $('.tab-pane').removeClass('active');
                $('#menutab.tab-pane').addClass('active');
            }
        });
    });

     function changeToppingqty(top_val_id,action,price,max_toppingqty,toppings,selected,topmaxval){
        var currqty = eval($('#topping_vale_'+top_val_id+' .qty').html()); 
        if(action=='+'){  
            var selqty = 0;
            if(topmaxval!=0 && parseInt(currqty)>= parseInt(topmaxval)){
                $('#topping_vale_'+top_val_id+' .toppingplus').addClass('disabled');
                return; 
            }
            $('#'+toppings+ ' .topping_qty').each(function() {
                selqty  =parseInt(selqty) + parseInt($(this ).val());                  
            });
            if(max_toppingqty!=0 && parseInt(selqty) >= parseInt(max_toppingqty)){
               $('#'+toppings+ ' .toppingplus').addClass('disabled');
            } else {
            var new_qty = eval(currqty+1);
            $('#topping_vale_'+top_val_id+' .qty').show();
            $('#topping_vale_'+top_val_id+' .toppngminus').show();
            $('#topping_vale_'+top_val_id+' .qty').html(new_qty);
            $('#topping_vale_'+top_val_id+' .topping_qty').val(new_qty);
            $('#'+selected+ ' .toppingin').show();
            $('#topping_vale_'+top_val_id+' .price').html('<?php echo $this->currency->getSymbolleft(); ?>'+eval(new_qty*price).toFixed(2));
                var currqty = eval($('#topping_vale_'+top_val_id+' .qty').html()); 
                if(topmaxval!=0 && parseInt(currqty)>= parseInt(topmaxval)){
                    $('#topping_vale_'+top_val_id+' .toppingplus').addClass('disabled');
                   // return; 
            }
                var selqty = 0;
                $('#'+toppings+ ' .topping_qty').each(function() {
                    selqty  =parseInt(selqty) + parseInt($(this ).val());                  
                });
                if(max_toppingqty!=0 && parseInt(selqty) >= parseInt(max_toppingqty)){
                    $('#'+toppings+ ' .toppingplus').addClass('disabled');
                }
            }
        }else {
            var new_qty = eval(currqty-1);            
            $('#topping_vale_'+top_val_id+' .qty').html(new_qty);
            $('#topping_vale_'+top_val_id+' .topping_qty').val(new_qty);
            $('#topping_vale_'+top_val_id+' .price').html('<?php echo $this->currency->getSymbolleft(); ?>'+eval(new_qty*price).toFixed(2));
            if(new_qty==0){
                $('#topping_vale_'+top_val_id+' .qty').hide();
                $('#topping_vale_'+top_val_id+' .toppngminus').hide();
                $('#'+selected+ ' .toppingin').hide();
                $('#topping_vale_'+top_val_id+' .price').html('<?php echo $this->currency->getSymbolleft(); ?>'+eval(price).toFixed(2));
            }
            $('#'+toppings+ ' .toppingplus').removeClass('disabled');
        }        
    }
    function changeDressingqty(max_dressings, class_name,parentdiv){ 
        if($('#' + parentdiv + ' input[type="checkbox"]:checked').length>0){
            $('#' + parentdiv + ' .dressingin').show();
        }else{
            $('#' + parentdiv + ' .dressingin').hide();
        }
        if(max_dressings != 0){
            var dressingsqty = $('#' + class_name + ' input[type="checkbox"]:checked').length;        
            if(dressingsqty >= max_dressings){
                $("#" + class_name +"  input:checkbox:not(:checked)").attr('disabled', 'disabled');            
            }else{
                $("#" + class_name +"  input:checkbox:not(:checked)").removeAttr('disabled');
            }
        }
    }
    function optionMaximum(max_option,divid){
        if(max_option != 0 && max_option!=''){
            var checkedoptionqty = $('#' + divid + ' input[type="checkbox"]:checked').length;  
            if(checkedoptionqty >= max_option){
                $("#" + divid +"  input:checkbox:not(:checked)").attr('disabled', 'disabled');            
            }else{
                $("#" + divid +"  input:checkbox:not(:checked)").removeAttr('disabled');
            }
        }
        
    } 
    function toppingMaximum(max_option,divid,topiingdivid){
        if($('#topping-value-' + topiingdivid).is(":checked")){
            $('#topping-qty-' + topiingdivid).val(1);
            console.log("Checked");
        }else{
            $('#topping-qty-' + topiingdivid).val(0);
            console.log("Not Checked");
        }
        
        if(max_option != 0 && max_option!=''){
            var checkedoptionqty = $('#' + divid + ' input[type="checkbox"]:checked').length;  
            if(checkedoptionqty >= max_option){
                $("#" + divid +"  input:checkbox:not(:checked)").attr('disabled', 'disabled');            
            }else{
                $("#" + divid +"  input:checkbox:not(:checked)").removeAttr('disabled');
            }
        }
        
    } 
    function checkoptioncompleted(requireddata,divid){
       
        //console.log(divid);
        //
        var reqcompleted = {};
        $("#"+divid +' option:selected').each(function() {
             reqcompleted[$(this).attr('proptionid')]=$( this ).attr('proptionid');
        });
        $("#"+divid +' input:radio:checked').each(function() {
             reqcompleted[$(this).attr('proptionid')]=$( this ).attr('proptionid')
        });
        $("#"+divid +' input:checkbox:checked').each(function() {
             reqcompleted[$( this ).attr('proptionid')]=$( this ).attr('proptionid');
        });
        if(Object.keys(reqcompleted).length>=Object.keys(requireddata).length){
            $("#"+divid +' .optionicon').html('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>');
        }else{
            $("#"+divid +' .optionicon').html('<span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>');
        }
       
    }
</script>
<script type="text/javascript">   
    $(document).ready(function(){
        $('.pcollect').hide();
        $('.phome').hide();
      
        <?php if(isset($delivery_method) && $delivery_method!=''){ ?>
                var delmethode = '<?php echo $delivery_method; ?>';
                del = delmethode.split(".");
                $(".p"+del[0]).show();
        <?php } ?>
    });        
</script>
    
<?php echo $footer; ?>