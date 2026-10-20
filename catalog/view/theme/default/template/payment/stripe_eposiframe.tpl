<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title ?></title>
        <link href="catalog/view/javascript/keyboard/jquery-ui.min.css" rel="stylesheet">
        <script type="text/javascript" src="catalog/view/javascript/keyboard/jquery-latest.min.js"></script>
        <script type="text/javascript" src="catalog/view/javascript/keyboard/jquery-ui.min.js"></script>
        <script type="text/javascript" src="catalog/view/javascript/keyboard/bootstrap.min.js"></script>
        
        
        <link href="catalog/view/javascript/keyboard/keyboard.css" rel="stylesheet">
        <script type="text/javascript" src="catalog/view/javascript/keyboard/jquery.keyboard.js"></script>
        <script type="text/javascript" src="catalog/view/javascript/keyboard/jquery.mousewheel.js"></script>
        
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->config->get('config_ssl'); ?>catalog/view/theme/default/stylesheet/stripetermial/layout.css" />
        <meta name="viewport" content="width=480px" />
        <meta name="viewport" content="user-scalable=no" />
    </head>
    <body> 
        <div id="wrap">
            <h2><?php echo $title ?></h2>
            <form autocomplete="off" action="" method="POST" id="payment_form">
                    
                <div class="row">
                    <div class="col-1">
                        Amount
                    </div>
                    <div class="col-2">
                        <input autocomplete="off" id="input_amount" type="text" value="<?php echo $amount; ?>" />
                    </div>
                    <div class="col-3">
                        <div id="error_amount" class="error_wrapper"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-1">Card Number</div>
                    <div class="col-2"><input autocomplete="off" id="input_number" type="text" pattern="[0-9]*"/></div>
                    <div class="col-3"><div id="error_number" class="error_wrapper"></div></div>
                </div>
                <div class="row">
                    <div class="col-1">CVC</div>
                    <div class="col-2">
                        <input autocomplete="off" id="input_cvc" type="text" pattern="[0-9]*" />
                    </div>
                    <div class="col-3"><div id="error_cvc" class="error_wrapper"></div></div>
                </div>
                <div class="row">
                    <div class="col-1">Expiration</div>
                    <div class="col-2">
                          <input autocomplete="off" id="input_exp_month" type="text" pattern="[0-9]*" placeholder="MM" />
                        <span> / </span>
                        <input autocomplete="off" id="input_exp_year" type="text" pattern="[0-9]*" placeholder="YYYY" />
                    </div>
                    <div class="col-3"> <div id="error_exp_month" class="error_wrapper"></div></div>
                </div>
                <div class="row">
                    <div id="error_exp_year" class="error_wrapper"></div>
                </div>
                <div class="row">
                    <div class="col-1">Note</div>
                    <div class="col-2"><input autocomplete="off" id="input_note" type="text" value="<?php echo $note; ?>" /></div>
                    <div class="col-3"><div id="error_note" class="error_wrapper"></div></div>
                </div>
                <div class="row">
                    <div id="transaction_error"></div>
                </div>
                <div class="row" style="display: inline-block">
                    <div class="left" id="progress_message"></div>
                    <div class="right" style="margin-top: 10px;">
                        <button id="submit_button" type="submit">Submit</button>
                    </div>
                </div>
            </form>
        </div>
<!--
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
        -->
        <script type="text/javascript" src="https://js.stripe.com/v1/"></script>
        <script type="text/javascript">
            function show_error(input, message){
                $('#input_' + input).addClass('error');
                $('#error_' + input).html(message).show();
                return false
            }

            function clear_error(input){
                $('#input_' + input).removeClass('error');
                $('#error_' + input).html('').hide();
            }

            function lock_inputs(){
                $('input').attr('disabled', 'disabled');
                $('#submit_button').attr('disabled', 'disabled');
                $('#transaction_error').hide();
            }

            function unlock_inputs(){
                $('input').removeAttr('disabled');
                $('#submit_button').removeAttr('disabled');
                $('#progress_message').hide();
            }           

            function validate(field, value){
                if(field == 'number'){
                    if(!value.length) return show_error(field, 'Please enter a credit card number');
                }

                if(field == 'cvc'){
                    if(!value.length) return show_error(field, 'Please enter a CVC');
                }

                if(field == 'exp_month'){
                    if(!value.length) return show_error(field, 'Please enter a month');
                    if(value < 1 || value > 12) return show_error(field, 'Month should be between 1 and 12');
                }                   

                if(field == 'exp_year'){
                    if(!value.length) return show_error(field, 'Please enter a year');
                }

                if(field == 'amount'){
                    if(!value.length) return show_error(field, 'Please enter an amount');
                    if(value < 0.50) return show_error(field, 'Minimum charge is $0.50');
                }

                clear_error(field);
                return true;
            }

            function sanitize(field){
                var input = $('#input_' + field);
                var value = $.trim(input.val());

                if(field == 'number'){
                    value = value.replace(/[^\d]+/g, '');
                }

                if(field == 'cvc'){
                    value = value.replace(/[^\d]+/g, '');   
                }

                if(field == 'exp_month'){
                    value = value.replace(/[^\d]+/g, '');
                    value = value.replace(/^0+/, '');
                }

                if(field == 'exp_year'){
                    value = value.replace(/[^\d]+/g, '');
                    //if(value.length == 2) value = '20' + value;
                }

                if(field == 'amount'){
                    value = value.replace(/[^\d\.]+/g, '');
                    if(value.length) value = parseFloat(value).toFixed(2);
                }

                input.val(value);
            }


            $(function(){
                $('input').attr('autocomplete', 'off');

                unlock_inputs();

                $('input').blur(function(){
                    var input = $(this);
                    var name = input.attr('id').replace(/^input_/, '');

                    sanitize(name);
                    validate(name, input.val());
                });

                $('input').keydown(function(){
                    $('#transaction_error').hide();
                });

                $('#payment_form').submit(function(){
                    $('input').blur(); //trigger sanitize/validate functions

                    if($('input.error').length > 0){
                        unlock_inputs()
                        $('input.error:first').focus();
                        return false;
                    }

                    lock_inputs();

                    var params = {};
                    params['number']    = $('#input_number').val();
                    params['cvc']       = $('#input_cvc').val();
                    params['exp_month'] = $('#input_exp_month').val();
                    params['exp_year']  = $('#input_exp_year').val();

                    var amount = $('#input_amount').val().replace(/\./g, '');

                    $('#progress_message').html('Validating card data...').show();

                    Stripe.createToken(params, amount, function(status, response){   
                       
                        if(response.error){
                            $('#transaction_error').html(response.error.message).slideDown();
                            unlock_inputs();
                            return false;
                        }

                        var charge = {};
                        charge['token'] = response['id'];
                        charge['amount'] = amount;
                        charge['note'] = $('#input_note').val();
                        $('#progress_message').html('Submitting charge...').show();
                        $.post('index.php?route=payment/stripe_payments/epossend', charge, function(response){

                            try{
                                response = JSON.parse(response);
                            }catch(err){
                                //first way to fail: server returns something that's not json
                                if(!$.trim(response).length){
                                    response = {error: 'Server returned empty response during charge attempt'};
                                }else{
                                    response = {error: 'Server returned invalid response:<br /><br />' + response};
                                }
                                
                            }
                            
                            if(response['success']){
                                $('#wrap').html("<h2>MISSION ACCOMPLISHED</h2><b>$" + response['amount'] + " is making its way to our bank account.</b><br />Transaction ID: " + response['id'] + "<br /><br />").css('background-color', '#fff');
                                $("<a href='javascript:void(0);' class='red'>Make another charge</a>").click(function(){ location.href = location.href; }).appendTo('#wrap');
                               // window.top.stripeSuccess(response); 
                                response['payment'] ='stripe';
                                window.parent.postMessage(response,'*' );
                            }else{
                                //second way to fail: stripe declined the charge
                                $('#transaction_error').html(response['error']).slideDown();
                               // window.top.stripeError(response); 
                                unlock_inputs();
                            }
                        }).error(function(response){
                            //third way to fail: server responds with an error code
                            $('#transaction_error').html('Error on charge attempt:<br /><br />' + response.responseText).slideDown();
                         //   window.top.stripeError(response); 
                            unlock_inputs();
                        });

                    });

                    return false;
                });
            });
        </script>
        <script type="text/javascript">
            Stripe.setPublishableKey('<?php echo $stripe_payments_public_key; ?>');
        </script>
        <script>
		$(function(){
			
                       $('#input_amount').keyboard({
                                autoAccept: true,
                                usePreview: false,
                                layout: 'custom',
                                customLayout: {
                                        'normal' : [                                                
                                                '0 1 2 3 4',
                                                '5 6 7 8 9',
                                                '{c} . {bksp} {accept}'
                                        ]
                                },
                        });
                        $('#input_number').keyboard({
                                autoAccept: true,
                                usePreview: false,
                                layout: 'custom',
                                customLayout: {
                                        'normal' : [                                                
                                                '0 1 2 3 4',
                                                '5 6 7 8 9',
                                                '{c} . {bksp} {accept}'
                                        ]
                                },
                        });
                         $('#input_cvc').keyboard({
                                autoAccept: true,
                                usePreview: false,
                                layout: 'custom',
                                customLayout: {
                                        'normal' : [                                                
                                                '0 1 2 3 4',
                                                '5 6 7 8 9',
                                                '{c} . {bksp} {accept}'
                                        ]
                                },
                        });
                        $('#input_exp_month').keyboard({
                                autoAccept: true,
                                usePreview: false,
                                layout: 'custom',
                                customLayout: {
                                        'normal' : [                                                
                                               '0 1 2 3 4',
                                                '5 6 7 8 9',
                                                '{c} . {bksp} {accept}'
                                        ]
                                },
                        });
                        $('#input_exp_year').keyboard({
                                autoAccept: true,
                                usePreview: false,
                                layout: 'custom',
                                customLayout: {
                                        'normal' : [                                                
                                                '0 1 2 3 4',
                                                '5 6 7 8 9',
                                                '{c} . {bksp} {accept}'
                                        ]
                                },
                        });
                       
                        //$('#input_note').keyboard({autoAccept: true,usePreview: false});
		});
	</script>
        <style>
            input[type=text], input[type=number], input[type=email]{
                color: black !important;
            }
            .ui-keyboard-button{height:4em !important;min-width:4em !important}
            .ui-keyboard-space{ width:90% important;height:50px !important; }
            .ui-keyboard-accept{width:8em !important;}
        </style>
    </body>
</html>
