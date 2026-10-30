<div id="cart">
    <div class="heading">
        <!--<h4><?php echo $heading_title; ?></h4>-->
        <!--<a><span id="cart-total"><?php echo $text_items; ?></span></a>-->
    </div>
   
    <input type="hidden" id="alsoboghtcart" value="<?php echo $confirmfree; ?>">
    <div class="content">
        <?php if ($products || $vouchers) { ?>
            <div id="mobile-cart-total"><a id="mobile_cart_btn" class="button" onclick="javascript:$('#cart-chechout-btn').focus()"><span id="cart-total"><?php echo $text_items; ?></span></a></div>
            <div class="col-sm-12 cartreqtime">
                <?php if ($order_requested_time && order_requested_time != '') { ?>
                    <?php echo $order_requested_time; ?> <i class="fa fa-edit" onclick="changerequestedtime()"></i>
                <?php } ?>
            </div>
            <div class="col-sm-12 mini-cart-info">
                <div class="itemsbox">
                    <?php $ik = 0; ?>
                    <input type="hidden" class="order_total" value="<?php echo $order_total; ?>">
                    <?php foreach ($products as $product) {   ?>
                        <div id="pro_bg" class="cartitms" style="display: <?php echo (isset($also_bought[$product['product_id']]) ? 'none' : ''); ?> ">
                            <input type="hidden" class="item-id" value="<?php echo $product['product_id']; ?>">
                            <input type="hidden" class="item-name" value="<?php echo $product['org_name']; ?>">
                            <input type="hidden" class="item-price" value="<?php echo $product['org_total']; ?>">
                            <input type="hidden" class="item_category" value="<?php echo $product['category']; ?>">
                            <input type="hidden" class="item-index" value="<?php echo $ik; ?>">
                            <input type="hidden" class="item-quantity" value="<?php echo $product['quantity']; ?>">
                            <?php if ($product['thumb']) { ?>
                                <div class="prrimage" style="background:url('<?php echo $product['thumb']; ?>') no-repeat center;">
                                </div>
                            <?php } ?>
                            <!--<div class="image"><?php if ($product['thumb']) { ?>
            <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
            <?php } ?></div>-->


                            <div class="namebrd">
                                <div class="name"><strong class="pr_name"><?php echo $product['name']; ?></strong>
                                    <?php if (!empty($product['option'])) { ?>
                                        <span style="text-align: right"> <a onclick="showcartproductoptions('<?php echo $product['product_id']; ?>','<?php echo $product['key']; ?>')" class="cart_prodt_customize"><i class="fa fa-edit"></i></a></span>
                                    <?php } ?>



                                    <?php foreach ($product['option'] as $option) { ?>
                                        <small>-
                                            <?php echo $option['name'] . ":"; ?>
                                            <?php echo $option['value']; ?></small><br />
                                    <?php } ?>
                                    <?php foreach ($product['toppings'] as $topping) { ?>
                                        -<small> <?php echo $topping['quantity'] . " X " . $topping['name'] . ":" . $topping['topping_value']; ?></small><br />
                                    <?php } ?>
                                    <?php foreach ($product['dressings'] as $dressing) { ?>
                                        -<small><?php echo $dressing['name'] . ":" . $dressing['dressing_value']; ?></small><br />
                                    <?php } ?>
                                    <?php foreach ($product['bundle'] as $bundle) { ?>
                                        <b><small>
                                                <?php
                                                echo $bundle['name'] . ":";
                                                ?>
                                            </small></b><br />
                                        <?php foreach ($bundle['option'] as $bundleoption) { ?>
                                            -- <small>
                                                <?php
                                                echo $bundleoption['name'] . ":";

                                                ?>
                                                <?php echo $bundleoption['value']; ?></small><br />
                                        <?php } ?>
                                        <?php foreach ($bundle['toppings'] as $bundtopping) { ?>
                                            -<small> <?php echo $bundtopping['quantity'] . " x " . $bundtopping['name'] . ":" . $bundtopping['topping_value']; ?></small><br />
                                        <?php } ?>
                                        <?php foreach ($bundle['dressings'] as $bundressing) { ?>
                                            -<small><?php echo $bundressing['name'] . ":" . $bundressing['dressing_value']; ?></small><br />
                                        <?php } ?>
                                    <?php } ?>
                                </div>

                                <div id="equiliser" class="col-sm-12">
                                    <div class="remove">
                                        <div class="fa fa-minus-circle" title="-1" onclick="productminuspopup1('<?php echo $product['key']; ?>','<?php echo $product['quantity']; ?>','<?php echo $product['product_id']; ?>','<?php echo $product['org_name']; ?>','<?php echo $product['org_total']; ?>');">
                                        </div>
                                    </div>
                                    <div class="quantity">x&nbsp;<?php echo $product['quantity']; ?></div>
                                    <?php if (!isset($prodtqyys[$product['product_id']]) || (isset($prodtqyys[$product['product_id']]) && $prodtqyys[$product['product_id']]['tot_qty'] > $prodtqyys[$product['product_id']]['in_qty'])) { ?>
                                        <div class="add">
                                            <div class="fa fa-plus-circle" title="+1" onclick="productadd('<?php echo $product['key']; ?>','<?php echo $product['quantity']; ?>','<?php echo $product['product_id']; ?>');"></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="total" id="sub_price" style="text-align: right;"><?php echo $product['total']; ?></div>
                            <!--<div class="remove"><div class="fa fa-times" title="<?php echo $button_remove; ?>" onclick="(getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') ? location = 'index.php?route=checkout/cart&remove=<?php echo $product['key']; ?>' : $('#cart').load('index.php?route=module/cart&remove=<?php echo $product['key']; ?>' + ' #cart > *');" ></div></div>-->



                            <div></div>
                        </div>

                        <?php $ik++; ?>
                    <?php } ?>
                    <?php foreach ($vouchers as $voucher) { ?>
                        <div>
                            <div class="name"><?php echo $voucher['description']; ?></div>
                            <div class="quantity">x&nbsp;1</div>
                            <div class="total" style="text-align: right;"><?php echo $voucher['amount']; ?></div>
                            <div class="remove"><img src="catalog/view/theme/default/image/remove-small.png" alt="<?php echo $button_remove; ?>" title="<?php echo $button_remove; ?>" onclick="(getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') ? location = 'index.php?route=checkout/cart&remove=<?php echo $voucher['key']; ?>' : $('#cart').load('index.php?route=module/cart&remove=<?php echo $voucher['key']; ?>' + ' #cart > *');" /></div>
                        </div>
                    <?php } ?>
                </div>


                <?php if (!empty($also_bought)) { ?>
                    <div id="extrzz" class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="javascript:void(0);"
   data-toggle="collapse"
   data-target="#extrasCollapse"
   aria-expanded="true">
                                    <img src="/image/data/chopstick.png" class="img-cutlery" /> Cutlery 
                                    <span class="pull-right glyphicon glyphicon-chevron-up"></span>
                                </a>
                            </h4>
                        </div>

                        <div id="extrasCollapse" class="panel-collapse collapse in">
                            <div class="panel-body">

                                <?php foreach ($also_bought as $upsell) { ?>
                                    <div id="pro_bg" class="cartitms upsellcart">
                                        <?php if ($upsell['thumb']) { ?>
                                            <div class="prrimage" style="background:url('<?php echo $upsell['thumb']; ?>') no-repeat center;">
                                            </div>
                                        <?php } ?>

                                        <div class="namebrd">
                                            <div class="name">
                                                <strong class="pr_name"><?php echo $upsell['name']; ?></strong>
                                            </div>

                                            <div id="equiliser" class="col-sm-12">
                                                <div class="remove">
                                                    <div class="fa fa-minus-circle"
                                                        title="-1"
                                                        onclick="productminuspopup1('<?php echo $upsell['key']; ?>','<?php echo $upsell['quantity']; ?>','<?php echo $upsell['product_id']; ?>','<?php echo $upsell['name']; ?>','<?php echo $upsell['price']; ?>','upsell');">
                                                    </div>
                                                </div>

                                                <div class="quantity">
                                                    x&nbsp;<?php echo $upsell['quantity']; ?>
                                                </div>

                                                <?php if ($upsell['quantity'] < $upsell['max_buy']) { ?>
                                                    <div class="add">
                                                        <div class="fa fa-plus-circle"
                                                            title="+1"
                                                            onclick="productadd('<?php echo $upsell['key']; ?>','<?php echo $upsell['quantity']; ?>','<?php echo $upsell['product_id']; ?>','upsell');">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="total" id="sub_price" style="text-align:right;">
                                            <?php echo $upsell['price']; ?>

                                            <?php if ($upsell['free_qty'] > 0) { ?>

                                                <?php if ($upsell['quantity'] <= $upsell['free_qty']) { ?>
                                                    <span style="color:green;font-size:12px;">
                                                        <?php echo $upsell['free_qty']; ?> Free
                                                    </span>
                                                <?php } else { ?>
                                                    <span style="color:green;font-size:12px;">
                                                        <?php echo $upsell['free_qty']; ?> Free
                                                    </span>

                                                    <?php if ($upsell['quantity'] > $upsell['free_qty']) { ?>
                                                        <span style="color:red;font-size:12px;">
                                                            <?php echo $upsell['quantity'] - $upsell['free_qty']; ?> charged
                                                        </span>
                                                    <?php } ?>

                                                <?php } ?>

                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>

                <?php } ?>
</div>
             
    <div style="clear:both"></div>
             <form action="<?php echo $checkout; ?>" method="post" id="sidebarcheckoutform">
                        <!--<div>
        <textarea name="notes" cols="40" rows="2" placeholder="Special instruction if you have any...."><?php echo $notes; ?></textarea>
                        </div>-->
                    </form>

            </div>
        


               <div class="wrapup">
                    <div class="totlablck">
                        <?php foreach ($totals as $total) {
                            if ($total['text'] != '') {
                        ?>
                                <div class="alltotal <?php echo $total['title']; ?>" style="font-weight: bold;">
                                    <div id="suber"><?php echo $total['title']; ?>:&nbsp;</div>
                                    <div id="sub_amt" class="total" style="text-align: right;"><?php echo $total['text']; ?></div>
                                    <!--<div class="remove">&nbsp;</div>-->
                                </div>
                        <?php }
                        }
                        // if(isset($total['text'])){
                        // $amnt=explode('£',$total['text']);
                        // $_SESSION['total_amount']=$amnt[1];
                        // }
                        ?>
                    </div>

                    
                    <div id="cart-chechout-btn" class="checkout" tabindex='1'>
                        <a id="" onclick="checkoutsidebar()" class="webcartbtn"><?php echo $text_checkout; ?></a>
                        <a href="/menu" class="backtomenu mobilecartbtn">Go to Menu</a>
                        <a class="mobilecartbtn" onclick="checkoutsidebarmobile()"><?php echo $text_checkout; ?></a>
                        <!---
              <a  href="/checkoutetm" class="mobilecartbtn" onclick="sendgtag()"><?php echo $text_checkout; ?></a>
              -->
                    </div>
                </div>

                
        <?php } else { ?>
            <div class="empty"><?php echo $text_empty; ?></div>

            <div id="cart-chechout-btn"></div>
            </div>
        <?php } ?>
    </div>

<div id="product-minus-popup" class="modal fade" tabindex="-1" role="dialog" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">Are you Sure to remove This Product</div>
            <div class="modal-body">
                Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete_item">Delete</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
   
    <?php if (isset($shipping_selected)) { ?>
        var shipping_selected = '<?php echo $shipping_selected; ?>';

    <?php } else { ?>
        var shipping_selected = '';
    <?php } ?>
    <?php if (isset($delivery_method)) { ?>
        var delivery_method = '<?php echo $delivery_method; ?>';

    <?php } ?>

    function productminuspopup1(key, quantity, product_id, name, price) {
        // Callers pass (key, qty, product_id, name, price); name/price/id were used below
        // without being declared ("ReferenceError: price is not defined").
        var id = product_id;
        name = name || '';
        price = parseFloat(price) || 0;
        if (quantity == 1) {
            // Drop a Delete handler left over from a popup that was cancelled, otherwise
            // the next Delete would also remove that earlier item.
            $('#product-minus-popup').off('click.etmminus').modal('show')
                .one('click.etmminus', '#delete_item', function(e) {
                    //$form.trigger('submit');
                    $('#cart').load('index.php?route=module/cart #cart > *', {
                        'minus1': key,
                        'qty': quantity
                    }, function() {
                        requestedtime_check();
                        $('.productdatas-' + product_id + ' .quantity').html('');
                        $('.productdatas-' + product_id + ' .addtocartnb').show();
                        $('.productdatas-' + product_id + ' .equiliserpropage').hide();
                        window.dataLayer = window.dataLayer || [];
                        window.dataLayer.push({
                            event: "remove_from_cart",
                            ecommerce: {
                                currency: "GBP",
                                value: price,
                                items: [{
                                    item_id: id,
                                    item_name: name,
                                    quantity: 1,
                                    price: price
                                }]
                            }
                        });
                    });
                });
        } else {
            $('#cart').load('index.php?route=module/cart #cart > *', {
                'minus1': key,
                'qty': quantity
            }, function() {
                requestedtime_check();
                $('.productdatas-' + product_id + ' .quantity').html(parseInt($('.productdatas-' + product_id + ' .quantity').html()) - 1);
                $('.productdatas-' + product_id + ' .remove').html('<div class="fa fa-minus-circle" title="-1" onclick="removeditem(' + "'" + key + "'" + ',' + $('.productdatas-' + product_id + ' .quantity').html() + ',' + product_id + ');"></div>');
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    event: "remove_from_cart",
                    ecommerce: {
                        currency: "GBP",
                        value: price,
                        items: [{
                            item_id: id,
                            item_name: name,
                            quantity: 1,
                            price: price
                        }]
                    }
                });
            });
        }

    }

    function productadd(key, quantity, product_id,upsell) {
        $('#cart').load('index.php?route=module/cart #cart > *', {
            add1: key,
            qty: quantity
        }, function() {
            //$('#cart-htotal').html($('#cart-total').html()); 
            //showcarttotal();
            $('.productdatas-' + product_id + ' .quantity').html(parseInt($('.productdatas-' + product_id + ' .quantity').html()) + 1);
            if(upsell!=undefined){
                $('#cart .content').animate({
                scrollTop: $('#extrzz').position().top+20
            }, 100);
             }
        });
    }

    function checkoutsidebar() {
        if($('#alsoboghtcart').val()=='1'){
    if ($('#extrzz .extras-warning-message').length === 0) {
    $('#extrzz').prepend(
        '<div class="extras-warning-message alert alert-danger text-center">' +
            '<strong>Please select at least one cutlery before proceeding to checkout.</strong>' +
        '</div>'
    );
 }
            $('#extrzz').addClass('extrawarning'); 
            $('#cart .content').animate({
                scrollTop: $('#extrzz').position().top+20
            }, 500);
            return;
        }
        console.log(delivery_method);
        console.log('shipping_selected-->' + $("#before_bg input[name='shipping_method']:checked").val());
        if ($("#before_bg input[name='shipping_method']:checked").val() != undefined) {

            if (delivery_method != $("#before_bg input[name='shipping_method']:checked").val()) {
                selectShippingMethod($("#before_bg input[name='shipping_method']:checked").val());
            } else {
                if (delivery_method != '') {
                    if ($("#before_bg input[name='shipping_method']:checked").val() == 'home.home') {
                        var regPostcode = /[A-Z]{1,2}[0-9]{1,2}[A-Z]{0,1} ?[0-9][A-Z]{2}/i; //new reg expression for postcode validation.
                        postcode = $('#inputdelivery').val();
                        if (regPostcode.test(postcode) == false) {
                            selectShippingMethod($("#before_bg input[name='shipping_method']:checked").val());
                            $('#inputdelivery').focus();
                        } else {
                            var checkdelivery = $("#before_bg input[name='shipping_method']:checked").val();
                            $.post("index.php?route=checkout/shipping_postcodes", {
                                postcode: postcode,
                                delivery_method: checkdelivery
                            }, function(data) {
                                var json = $.parseJSON(data);
                                if (json['postcode'] == '') {
                                    $('#inputdelivery').val('')
                                    selectShippingMethod($("#before_bg input[name='shipping_method']:checked").val());
                                    $('#inputdelivery').focus();
                                    return false; // NO postcode in DB

                                } else if (json['home_delivery_error'] != undefined) {
                                    $('#inputdelivery').val('')
                                    selectShippingMethod($("#before_bg input[name='shipping_method']:checked").val());
                                    $('#postcodemessage4').html(json['home_delivery_error']);
                                    $('#inputdelivery').val('')
                                    $('#inputdelivery').focus();

                                } else {

                                    $.post("index.php?route=checkout/shipping_postcodes/set_delivery_method", {
                                        delivery_method: 'home.home'
                                    }, function(data) {
                                        $('#sidebarcheckoutform').submit();
                                        cartItems = [];
                                        $(".cartitms").each(function() {
                                            cartItems.push({
                                                item_id: $(this).find(".item-id").val(),
                                                item_name: $(this).find(".item-name").val(),
                                                index: $(this).find(".item-index").val(),
                                                item_category: $(this).find(".item_category").val(),
                                                price: parseFloat($(this).find(".item-price").val()),
                                                quantity: parseInt($(this).find(".item-quantity").val())
                                            });
                                        });
                                        window.dataLayer = window.dataLayer || [];
                                        window.dataLayer.push({
                                            event: "begin_checkout",
                                            ecommerce: {
                                                currency: "GBP",
                                                value: $(".order_total").val(),
                                                items: cartItems
                                            }
                                        });
                                    });

                                }

                            });
                        }
                    } else {
                        $.post("index.php?route=checkout/shipping_postcodes/set_delivery_method", {
                            delivery_method: 'collect.collect'
                        }, function(data) {
                            $('#sidebarcheckoutform').submit();
                            cartItems = [];
                            $(".cartitms").each(function() {
                                cartItems.push({
                                    item_id: $(this).find(".item-id").val(),
                                    item_name: $(this).find(".item-name").val(),
                                    index: $(this).find(".item-index").val(),
                                    item_category: $(this).find(".item_category").val(),
                                    price: parseFloat($(this).find(".item-price").val()),
                                    quantity: parseInt($(this).find(".item-quantity").val())
                                });
                            });
                            window.dataLayer = window.dataLayer || [];
                            window.dataLayer.push({
                                event: "begin_checkout",
                                ecommerce: {
                                    currency: "GBP",
                                    value: $(".order_total").val(),
                                    items: cartItems
                                }
                            });
                        });



                    }

                    //console.log(cartItems);
                    //alert("TEST");
                } else {
                    $('#shippingmessage5').show();
                    $('html, body').animate({
                        scrollTop: 0
                    });
                }
            }
        } else {
            $('#shippingmessage5').show();
            $('html, body').animate({
                scrollTop: 0
            });
        }

    }
function checkoutsidebarmobile(){
    if($('#alsoboghtcart').val()=='1'){
            $('#extrzz').addClass('extrawarning'); 
            $('html, body').animate({
                scrollTop: $('#extrzz').offset().top
            }, 500);
            return;
        }
    checkoutsidebar();
}
    function showcartproductoptions(prod_id, key) {
        $('.customizable_options_widget .modal-content').load("index.php?route=product/product/ajaxchangeoptions", {
            product_id: prod_id,
            key: key
        }, function(response, statusTxt, xhr) {
            if (statusTxt == "success")
                $('.customizable_options_widget').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            if (statusTxt == "error")
                alert("Failed");
        })
    }

    function sendgtag() {
        cartItems = [];
        $(".cartitms").each(function() {
            cartItems.push({
                item_id: $(this).find(".item-id").val(),
                item_name: $(this).find(".item-name").val(),
                index: $(this).find(".item-index").val(),
                item_category: $(this).find(".item_category").val(),
                price: parseFloat($(this).find(".item-price").val()),
                quantity: parseInt($(this).find(".item-quantity").val())
            });
        });
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: "begin_checkout",
            ecommerce: {
                currency: "GBP",
                value: $(".order_total").val(),
                items: cartItems
            }
        });
        //alert("testdd");
    }

    function changerequestedtime() {
        selectShippingMethod($("#before_bg input[name='shipping_method']:checked").val())
    };


    $('#extrasCollapse').on('show.bs.collapse', function() {
        $(this).prev('.panel-heading')
            .find('.glyphicon')
            .removeClass('glyphicon-chevron-down')
            .addClass('glyphicon-chevron-up');
    });

    $('#extrasCollapse').on('hide.bs.collapse', function() {
        $(this).prev('.panel-heading')
            .find('.glyphicon')
            .removeClass('glyphicon-chevron-up')
            .addClass('glyphicon-chevron-down');
    });
</script>