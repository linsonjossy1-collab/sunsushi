<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_checkout_client_id; ?>&currency=<?php echo $currency; ?>" ata-order-id="<?php echo $order_id; ?>"> // Replace YOUR_CLIENT_ID with your sandbox client ID
    </script>

    <div id="paypal-button-container"></div>

    <!-- Add the checkout buttons, set up the order and approve the order -->
    <div id="paypal_progress_message"></div>
<script>
function wait_for_paypal_to_load() {
    if (window.paypal) {
      paypal.Buttons({
        createOrder: function() {
            return fetch('<?php echo $action; ?>', {
              method: 'post',
              body: JSON.stringify({order_id:<?php echo $order_id; ?>}),
              headers: {
                'content-type': 'application/json'
              }
            }).then(function(res) {
              return res.json();
            }).then(function(data) {
              return data.id; // Use the key sent by your server's response, ex. 'id' or 'token'
            });
          },
        onApprove: function(data) {
            $('#paypal_progress_message').html('processing ....').show();
            return fetch('<?php echo $capture; ?>&order_id=<?php echo $order_id; ?>', {
              method: 'post', 
              body: JSON.stringify({paypalorderID: data.orderID}),
              headers: {
                'content-type': 'application/json'
              }
            }).then(function(res) {
              return res.json();
            }).then(function(details) {
               // alert('Transaction funds captured from ' + details.payer_given_name);
               console.log(details);
                    if (details['failure']) {
                        
                        $('#paypal_progress_message').html('').hide();
                        
                        location = details['failure'];
                    }
                    if (details['success']) {
                        location = details['success'];
                    }
              
            });
        }
      }).render('#paypal-button-container'); // Display payment options on your web page
    }
    else {
        setTimeout(function() { wait_for_paypal_to_load(); },50);
    }
}
setTimeout(function() { wait_for_paypal_to_load(); }, 50);
</script>
<style>
#paypal_progress_message {
display: inline-block;
height: 60px;
width: 100%;
text-align: center;
font-size: 18px;
color: #13840a;
font-weight: bold;
margin-bottom: 15px;
}
</style>