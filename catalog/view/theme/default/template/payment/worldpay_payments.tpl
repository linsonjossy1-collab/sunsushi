<script type="text/javascript" src="https://cdn.worldpay.com/v1/worldpay.js"></script>
<div class="wordpay-payment-form">
  <form class="form-horizontal" action="<?php echo $action; ?>" id="wordpaypaymentForm" method="post">
    <!-- all other fields you want to collect, e.g. name and shipping address -->
    <div id='paymentSection'></div>
    <div class="wordpay-payment-address">
    <?php if($worldpay_billing_address) { ?>
        <div class="form-group">
            
                <label class="control-label col-sm-3"><?php echo $entry_address; ?></label>
                 <div class="col-sm-9">
                <input class="form-control"  type="text" name="address1" id="address_line1" value="<?php echo $shipping_address['address_1'] ?>" placeholder="Address" />
            </div>
        </div>
            <div class="form-group">
                <label class="control-label col-sm-3"><?php echo $entry_city; ?></label>
                 <div class="col-sm-9">
                <input class="form-control"  type="text" name="city" id="address_city" value="<?php echo $shipping_address['city'] ?>" placeholder="City"/>
            </div>
        </div>
        <div class="form-group">
                <label class="control-label col-sm-3"><?php echo $entry_postcode; ?></label>
                 <div class="col-sm-9">
                <input class="form-control" type="text" name="postalCode" id="address_zip" value="<?php echo $shipping_address['postcode'] ?>" placeholder="postcode"/>
            </div>
        </div>
   
    <?php } ?>
    </div>
    <div class="right">
       <input type="hidden" name="order_id" value="<?php echo $order_id; ?>"> 
       <input type="button" id="wordpay-payment-submit" value="<?php echo $button_confirm; ?>" class="btn btn-success"  onclick="Worldpay.submitTemplateForm()" />
    </div>
 </form>
</div>
<style>
#paymentSection  {
    text-align: center;
    margin-bottom: 40px;
    margin-top: 10px;
}
div#payment_method_section {
    padding: 15px;
}
#wordpay-payment-submit {
    margin-top: -35px;
}
.wordpay-payment-form .right {
    float: right;
}
.wordpay-payment-form {
    background-color: rgb(240, 242, 245);
}
.wordpay-payment-address {
    background-color: rgb(240, 242, 245);
    padding: 0px 15px 50px 15px;
    width: 300px;
    margin: auto;
    margin-bottom: 50px;
}
.wordpay-payment-address label.col-sm-3.control-label {
    font-size: 15px;
    color: #333;
    margin: 5px auto;
    font-weight: 400;
}
#paymentSection {
    margin-bottom: 0px;
}
@media (max-width: 767px)
{
.wordpay-payment-address {
    width: 100%;
}
}
</style>
<script type="text/javascript">
function wait_for_Worldpay_to_load() {
    if (window.Worldpay) {
        Worldpay.useTemplateForm({
            'clientKey':'<?php echo $worldpay_client_key; ?>',
            'saveButton':false,
            'form':'paymentForm',
            'paymentSection':'paymentSection',
            'display':'inline',
            'reusable':true,
            'callback': function(obj) {
              if (obj && obj.token) {
                var _el = document.createElement('input');
                _el.value = obj.token;
                _el.type = 'hidden';
                _el.name = 'token';
                document.getElementById('wordpaypaymentForm').appendChild(_el);
                document.getElementById('wordpaypaymentForm').submit();
              }
            },
            'beforeSubmit': function() {
              // if you would like to disable your submit button
              document.getElementById('wordpay-payment-submit').disabled = true;
              return true;
            },
            'validationError': function(obj) {
              // re-enable your submit button on validation failure
               document.getElementById('wordpay-payment-submit').disabled = null;
            }
        });   
    }
    else {
        setTimeout(function() { wait_for_Worldpay_to_load(); },50);
    }
}
setTimeout(function() { wait_for_Worldpay_to_load(); }, 50);
</script> 