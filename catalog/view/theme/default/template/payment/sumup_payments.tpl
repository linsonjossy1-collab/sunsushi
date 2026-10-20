<div id="sumup-card"></div>
<!-- <button id="custom-submit-button" class="custom-button btn btn-primary"><?php echo $button_confirm; ?></button> -->
<div id="sumup-payment-loader">
<div id="sumup-card-failerror" ></div>
</div>
<script type="text/javascript" src="https://gateway.sumup.com/gateway/ecom/card/v2/sdk.js"></script>

<script type="text/javascript">
function call_server(type, body){
                $.ajax({
                    url: '<?php echo $paymentaction; ?>',
                    type: 'post',
                    data: {'type': type,'body':body,order_id:'<?php echo $order_id; ?>' },
                    dataType: 'json',
                    complete: function() {
                        $('.attention').remove();
                    },				
                    success: function(paymentIntent) {
                        if (paymentIntent['success']) {
                            location = paymentIntent['success'];
                        }
                        if(paymentIntent['fail']){
                            $('#sumup-card-failerror').html(paymentIntent['error_message']);
                            $('#sumup-card-failerror').addClass('warning');
                $("#sumup-payment-loader").removeClass('loader');
                            setInterval(function () {
                                location = paymentIntent['fail']; 
                            },3000);

                        }
                    } 
                });
}
function wait_for_SumUpCard_to_load() {
    if (window.SumUpCard) {
        /*
        var sumupCard = SumUpCard.mount({
        id: 'sumup-card',
        checkoutId: '<?php echo $checkoutId; ?>',
        onResponse: function (type, body) {
            // console.log('Type', type);
            // console.log('Body', body);
            $("#sumup-payment-loader").addClass('loader');
            if(type=='success') {
              sumupCard.unmount();
              call_server(type, body);
            }
                
            },
            showSubmitButton: false
        });
        document.getElementById('custom-submit-button').addEventListener('click', function(event) {
            sumupCard.submit();
        });
             * 
         */
        var sumupCard = SumUpCard.mount({
            id: 'sumup-card',
            checkoutId: '<?php echo $checkoutId; ?>',
            onResponse: function (type, body) {
                console.log('Type', type);
                console.log('Body', body);
                if(type=='success') {
                    sumupCard.unmount();
                    call_server(type, body);
                }
            },
            onPaymentMethodsLoad: function (methods) {
                
                   console.log('getAvailablePaymentMethods',methods);
            },
          });
    }
    else {
        setTimeout(function() { wait_for_SumUpCard_to_load(); },50);
    }
}
setTimeout(function() { wait_for_SumUpCard_to_load(); }, 50);
</script>