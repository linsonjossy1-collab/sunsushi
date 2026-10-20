<?php if(isset($checkoutid)) { ?>
<script>
 var wpwlOptions = {
    billingAddress: {country: "GB"},
    mandatoryBillingFields:{
        country: true,
        state: true,
        city: true,
        postcode: true,
        street1: true,
        street2: false
    }
}
</script>
<?php if($this->config->get('truevo_payments_live_demo')) { ?>
<script src="https://truevo.eu/v1/paymentWidgets.js?checkoutId=<?php echo $checkoutid; ?>"></script>
<?php } else { ?>
<script src="https://test.truevo.eu/v1/paymentWidgets.js?checkoutId=<?php echo $checkoutid; ?>"></script>
<?php } ?>
<style>
.truevo-payment-form{
    min-height: 250px;
    margin-top: 10px;
    text-align:center;
    margin-bottom: 30px;
}
.truevo-payment-form form{
    display: inline-block !important;
    text-align:left;
}
</style>
<div class="truevo-payment-form">
    <form action="<?php echo $action; ?>" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>
</div>

<?php } else { ?>
<style>
.truevo-payment-form{
    min-height: 250px;
    margin-top: 10px;
    text-align:center;
    margin-bottom: 30px;
}
</style>
<div class="truevo-payment-form">
<h4> <?php echo $error_description; ?> </h4>
</div>
<?php } ?>