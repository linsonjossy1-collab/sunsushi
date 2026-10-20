<?php if(isset($checkoutid)) { ?>
<div class="sage-payment-form">
<form class="form-horizontal" action="<?php echo $action; ?>" id="sagepaymentForm" method="post">
<?php if($this->config->get('sagepay_dropin_test')=='live') { ?>
<script src="https://pi-live.sagepay.com/api/v1/js/sagepay.js"></script>
<?php } else { ?>
<script src="https://pi-test.sagepay.com/api/v1/js/sagepay.js"></script>
<?php } ?>

    <!-- all other fields you want to collect, e.g. name and shipping address -->
    <div id="sp-container-payment"></div>
    <div class="sage-payment-address">
        <div class="form-group">
                <label class="control-label col-sm-3"><?php echo $entry_firstname; ?></label>
                 <div class="col-sm-9">
                <input class="form-control"  type="text" name="firstname" id="firstname1" value="<?php echo $shipping_address['firstname'] ?>" placeholder="First Name" />
            </div>
        </div>
        <div class="form-group">
                <label class="control-label col-sm-3"><?php echo $entry_lastname; ?></label>
                 <div class="col-sm-9">
                <input class="form-control"  type="text" name="lastname" id="lastname1" value="<?php echo $shipping_address['lastname'] ?>" placeholder="Last Name" />
            </div>
        </div>
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
    </div>
    <div id="card-errors" role="alert"></div>
    <div id="submit-container" class="right">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>"> 
        <input type="hidden" name="merchantSessionKey" value="<?php echo $checkoutid; ?>"> 
       <input type="submit" id="sagepay-payment-submit" value="<?php echo $button_confirm; ?>" class="btn btn-success" />
    </div>

<script type="text/javascript">
var checkout_sagepay = null;
function wait_for_sage_to_load() {
    if (window.sagepayCheckout) {
       // sagepayCheckout({ merchantSessionKey: '' }).form();
        checkout_sagepay = sagepayCheckout({
            merchantSessionKey : '<?php echo $checkoutid; ?>',
            containerSelector:  '#sp-container-payment',
            onTokenise: function(tokenisationResult) {
                if (tokenisationResult.success) {
                    console.log('Tokenisation successful', tokenisationResult.cardIdentifier);
                    var _el = document.createElement('input');
                        _el.value = tokenisationResult.cardIdentifier;
                        _el.type = 'hidden';
                        _el.name = 'card-identifier';
                    document.getElementById('sagepaymentForm').appendChild(_el);
                    document.getElementById('sagepaymentForm').submit();
                } else {
                    console.error('Tokenisation failed', tokenisationResult.error.errorMessage);
                    $("#card-errors").html(tokenisationResult.error.errorMessage);
                    document.getElementById('sagepay-payment-submit').disabled = null;
                }
              }
          }).form({
            formSelector: '#sagepaymentForm'
          });

    }
    else {
        setTimeout(function() { wait_for_sage_to_load(); },50);
    }
}
setTimeout(function() { wait_for_sage_to_load(); }, 50);
document.querySelector('[type=submit]')
        .addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sagepay-payment-submit').disabled = true;
            checkout_sagepay.tokenise();
        });
</script> 
 </form>
</div>
<style>
    
</style>
<?php } else { ?>
<style>
.sageer-payment-form{
    min-height: 250px;
    margin-top: 10px;
    text-align:center;
    margin-bottom: 30px;
}
</style>
<div class="sageer-payment-form">
<h4> <?php echo $error_description; ?> </h4>
</div>
<?php } ?>