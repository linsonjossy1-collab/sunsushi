<link rel="stylesheet" type="text/css" href="<?php echo $this->config->get('config_ssl'); ?>catalog/view/theme/default/stylesheet/stripe_payments.css">
<h2><?php echo $text_credit_card; ?></h2>
<?php if(isset($savedcard)) { ?>
<div class="payment-content-area" id="saved-payment-content-area">
    <h3><?php echo $text_saved_card; ?></h3>
    <form action="" method="POST" id="payment-form-existing" class="payment-form">
        <div class="remove-buttons" ><button id="remove-button-saved" class="btn btn-danger" >
            <?php echo $text_remove_button; ?>
        </button></div>
        <span id="payment-errors-exist-saved"  class="payment-errors-exist error"></span>
        <div class="content" id="payment-existing">
            <div for="card-element"> <span class="card-type"><?php echo $payment_method_card->brand; ?></span>
                <span class="card-numend"> ending in <strong><?php echo $payment_method_card->last4; ?></strong></span>
                <span class="card-experiesend"> Expires <strong><?php echo $payment_method_card->exp_month; ?>/<?php echo $payment_method_card->exp_year; ?></strong> </span>
            </div>
            <!-- placeholder for Elements -->
            <div id="card-element-saved"></div>
            <div class="right">
              <button id="card-button-saved" data-secret="<?php echo $client_secret_saved ?>" class="buttonstripe-exist btn btn-success" >
                <?php echo $button_confirm; ?>
              </button>  
            </div>
        </div>  
    </form>
</div>
<?php } ?>
<div class="payment-content-area">
    <h3><?php echo $text_new_card; ?></h3>
    <form action="" method="POST" id="payment-form" class="payment-form">
    <div class="stripe-content" id="payment">
        <?php if(isset($stripe_billing_address)&& $stripe_billing_address){ ?>
            <div class="form-group row">
                <div class="col-md-6">
                    <input class="InputElement" type="text" placeholder="Name" name="name" id='cardholder-name' value="<?php echo $shipping_address['firstname'].' '.$shipping_address['lastname'] ?>" />
                </div>
                <div class="col-md-6">
                    <input class="InputElement" type="text" name="address_line1" id="address_line1" value="<?php echo $shipping_address['address_1'] ?>" placeholder="House Number" />
                </div>
                <div class="col-md-6">
                    <input class="InputElement" type="text" name="address_city" id="address_city" value="<?php echo $shipping_address['city'] ?>" placeholder="City"/>
                    <input class="InputElement" type="text" name="address_state" id="address_state" value="<?php echo $shipping_address['zone'] ?>" placeholder="State" />
                </div>
                <div class="col-md-6">
                    <input class="InputElement" type="text" name="address_zip" id="address_zip" value="<?php echo $shipping_address['postcode'] ?>" placeholder="ZIP"/>
                    <input class="InputElement" type="text" name="address_country" id="address_country" value="<?php echo $shipping_address['country'] ?>" placeholder="Country"/>
                </div>
            </div>
        <?php } ?>
        <div class="form-row">
            <label for="card-element">
            Credit or debit card
            </label>
            <div id="card-element">
            <!-- a Stripe Element will be inserted here. -->
            </div>
            <?php if( isset($stripe_cardsave) ) { ?>
            <div><input type="checkbox" name="stripe_save_card" id="stripe_save_card" value="1" checked="checked"><?php echo $text_save_card_later; ?></div>
            <?php } ?>
            <!-- Used to display form errors -->
            <div id="card-errors" role="alert"></div>

        </div>
        <div>&nbsp;<br></div>
    </div>
    <div>
      <div class="right">
        <button id="card-button" data-secret="<?php echo  $client_secret;  ?>" class="buttonstripe btn btn-success">
                <?php echo $button_confirm; ?>
        </button>
      </div>
    </div>
    </form>
    <div id="payment-request-button">
        <!-- A Stripe Element will be inserted here. -->
</div>
</div>
<div id="stripe_progress_message"></div>

<!-- The required Stripe lib -->
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>

    <script type="text/javascript">
    var stripe = null;
    var elements = null;
    var cardElement = null;
    var cardholderName = null;
    var clientSecret_paymentRequest = null; 
    function wait_for_stripe_to_load() {
        if (window.Stripe) {
            stripe = Stripe('<?php echo $stripe_payments_public_key; ?>');
            // elements = stripe.elements();
            // cardElement = elements.create('card');
            // cardElement.mount('#card-element');
            <?php if(isset($savedcard)) { ?>
            var cardButtonSaved = document.getElementById('card-button-saved');
            var clientSecretSaved = cardButtonSaved.dataset.secret;
            cardButtonSaved.addEventListener('click', function(ev) {
              ev.preventDefault();
               $('#payment-errors-exist-saved').text('');
               $('#card-button-saved').prop('disabled', true);
               $('#card-button').prop('disabled', true);
               $('#stripe_progress_message').html('Validating card ...').show();
              stripe.handleCardPayment(
                clientSecretSaved,
                {
                  payment_method: '<?php echo $paymentmethod_saved; ?>',
                }
              ).then(function(result) {
                if (result.error) {
                  // Display error.message in your UI.
                  $('#payment-errors-exist-saved').text(result.error.message);
                  $('#payment-errors-exist-saved').css('color','red');
                  
                  $('#card-button-saved').prop('disabled', false);
                  $('#card-button').prop('disabled', false);
                  $('#stripe_progress_message').html('').hide();
                  console.log(result.error);
                } else {
                  // The payment has succeeded. Display a success message.
                  $('#stripe_progress_message').html('Submitting ...').show();
                  $.ajax({
                        url: 'index.php?route=payment/stripe_payments/sendsaved',
                        type: 'post',
                        data: result,
                        dataType: 'json',
                        complete: function() {
                            $('.attention').remove();
                        },				
                        success: function(json) {
                            if (json['error']) {
                                $('#payment-form').find('#card-errors').text(json['error']);
                                $('#card-errors').css('color','red');
                                $('#card-button-saved').prop('disabled', false);
                                $('#card-button').prop('disabled', false);
                                $('#stripe_progress_message').html('').hide();
                            }
                            if (json['success']) {
                                location = json['success'];
                            }
                        }
                    });
                }
              });
            });
           <?php }  ?>
            elements = stripe.elements();
            cardElement = elements.create('card');
            cardElement.mount('#card-element');
            <?php if(isset($stripe_billing_address)&& $stripe_billing_address){ ?>
            cardholderName = document.getElementById('cardholder-name');
            <?php }?>
            var cardButton = document.getElementById('card-button');
            var clientSecret = cardButton.dataset.secret;

            cardButton.addEventListener('click', function(ev) {
                ev.preventDefault();
                $('#card-errors').text('');
                $('#card-button-saved').prop('disabled', true);
                $('#card-button').prop('disabled', true);
                $('#stripe_progress_message').html('Validating card ...').show();
                stripe.handleCardPayment(
                clientSecret, cardElement  <?php if(isset($stripe_billing_address)&& $stripe_billing_address){ ?> , {
                  payment_method_data: {
                    billing_details: {name: cardholderName.value}
                  }  }
                  <?php } ?>
               
              ).then(function(striperesult) {
                  console.log(striperesult);
                if (striperesult.error) {
                  // Display error.message in your UI.
                    $('#card-errors').text(striperesult.error.message);
                    $('#card-errors').css('color','red');
                  
                    $('#card-button-saved').prop('disabled', false);
                    $('#card-button').prop('disabled', false);
                    $('#stripe_progress_message').html('').hide();
                } else {
                  // The payment has succeeded. Display a success message.
                  // Then, send the payment method from the SetupIntent to your
                  // server in order to attach the payment method to a customer.
                  
                  $('#stripe_progress_message').html('Submitting ...').show();
                  <?php if(isset($savedcard)) { ?>
                          striperesult['save_customer_id'] = '<?php echo $save_customer_id; ?>';
                  <?php } ?>
                  if($('#stripe_save_card').prop('checked') == true) {
                      striperesult['stripe_save_card'] = 1;
                  }
                  $.ajax({
                        url: 'index.php?route=payment/stripe_payments/send',
                        type: 'post',
                        data: striperesult,
                        dataType: 'json',
                        complete: function() {
                            $('.attention').remove();
                        },				
                        success: function(json) {
                            if (json['error']) {
                                $('#payment-form').find('#card-errors').text(json['error']);
                                $('#card-errors').css('color','red');
                                $('#card-button-saved').prop('disabled', false);
                                $('#card-button').prop('disabled', false);
                                $('#stripe_progress_message').html('').hide();
                            }
                            if (json['success']) {
                                location = json['success'];
                            }
                        }
                    });
                }
              });
            });
            // Apple Pay
            
            var paymentRequest = stripe.paymentRequest({
                country: 'GB',
                currency: '<?php echo $currency; ?>',
                total: {
                  label: '<?php echo preg_replace("/[^A-Za-z0-9 ]/", '',$paymentRequest_label); ?>',
                  amount: <?php echo $amount; ?>,
                },
                requestPayerName: true,
                requestPayerEmail: true,
            });
            var prButton = elements.create('paymentRequestButton', {
              paymentRequest: paymentRequest,
            });

            // Check the availability of the Payment Request API first.
            paymentRequest.canMakePayment().then(function(result) {
              if (result) {
                prButton.mount('#payment-request-button');
              } else {
               
                document.getElementById('payment-request-button').style.display = 'none';
        }    
              $.ajax({
                url: 'index.php?route=payment/stripe_payments/applepay',
                type: 'get',
               // data: striperesult,
                dataType: 'json',
                complete: function() {
                    $('.attention').remove();
                },				
                success: function(json) {
                    if (json['client_secret_applepay']) {
                        clientSecret_paymentRequest  = json['client_secret_applepay'];
                    }
                    
                }
            });
            });
            paymentRequest.on('paymentmethod', function(ev) {
                // Confirm the PaymentIntent without handling potential next actions (yet).
                stripe.confirmCardPayment(
                  clientSecret_paymentRequest,
                  {payment_method: ev.paymentMethod.id},
                  {handleActions: false}
                ).then(function(confirmResult) {
                  if (confirmResult.error) {
                    // the payment interface.
                    // Report to the browser that the payment failed, prompting it to
                    // re-show the payment interface, or show an error message and close
                    ev.complete('fail');
                  } else {
                    // Report to the browser that the confirmation was successful, prompting
                    // it to close the browser payment method collection interface.
                    ev.complete('success');
                    // Let Stripe.js handle the rest of the payment flow.
                    stripe.confirmCardPayment(clientSecret_paymentRequest).then(function(striperesult) {
                     // if (striperesult.error) {
                        // The payment failed -- ask your customer for a new payment method.
                    //  } else {
                        // The payment has succeeded.
                        $.ajax({
                            url: 'index.php?route=payment/stripe_payments/applepaysend',
                            type: 'post',
                            data: striperesult,
                            dataType: 'json',
                            complete: function() {
                                $('.attention').remove();
                            },				
                            success: function(json) {
                                if (json['error']) {
                                   /* $('#payment-form').find('#card-errors').text(json['error']);
                                    $('#card-errors').css('color','red');
                                    $('#card-button-saved').prop('disabled', false);
                                    $('#card-button').prop('disabled', false);
                                    */
                                    $('#stripe_progress_message').html('').hide();
                                }
                                if (json['success']) {
                                    location = json['success'];
                                }
                            }
                        });
                   //   }
                    });
                  }
                });
              });
        }    
        else {
            setTimeout(function() { wait_for_stripe_to_load() }, 50);
        }
    }
    wait_for_stripe_to_load();
    <?php if($paymentmethod_saved) { ?>
    var removeButtonSaved = document.getElementById('remove-button-saved');
    removeButtonSaved.addEventListener('click', function(ev) {
        ev.preventDefault();
        $('#saved-payment-content-area').html('');
        $('#saved-payment-content-area').hide();
        $.ajax({
            url: 'index.php?route=payment/stripe_payments/removecard',
            type: 'post',
            data: {'payment_method_key':'<?php echo $paymentmethod_saved; ?>'},
            dataType: 'json',
            complete: function() {
                $('.attention').remove();
            },				
            success: function(json) {
                console.log(json);
            }
        });
    });
    <?php } ?>
  </script>