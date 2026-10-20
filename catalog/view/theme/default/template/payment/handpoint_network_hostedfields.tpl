 <!-- Load the jQuery Validator plugin -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>

    <!-- Load the Hosted Payment Field library -->
    <script src="https://commerce-api.handpoint.com/sdk/web/v1/js/hostedfields.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn"
        crossorigin="anonymous"></script>
<!-- General styles -->
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #0074cc;
            font-size: 14pt;
            background-color: #e9ecef;
        }
div#payment_cancel_button a.btn.btn-default {
    margin-top: -83px !important;
    left: 16px;
    position: relative;
}
        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 100%;
            padding: 16px;
            background-color: #ffffff;
    border-radius: 10px;
        }
div#payment_method_section {
    padding: 15px;
}
        .tab1cards {
            display: flex;
            flex-direction: row;
        }

        form-group {
            margin: 4px 0 15px 0;
        }

        .form-group LABEL {
            display: inline-block;
            max-width: 100%;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-control {
            display: block;
            box-sizing: border-box;
            height: 34px;
            width: 100%;
            padding: 6px 12px;
            font-size: 14px;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);

            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        }

        .form-control.hf-focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
        }

        .has-error .form-control.hf-focus {
            border-color: #843534;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 6px #ce8483;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 6px #ce8483;
        }
    </style>
    <!-- Hosted Field internal styles -->
    <style class="hostedfield">
        /* Grey out when disabled */
        .hostedfield:disabled {
            cursor: not-allowed;
            background-color: #eee;
            opacity: 1;
        }


        /* Change border and text to green when valid */
        .form-control:valid,
        .hostedfield:valid {
            border-color: #28a745 !important;
            color: #28a745 !important;
        }


        /* Change border and text to red when invalid */
        .form-control:invalid,
        .hostedfield:invalid {
            border-color: #a94442 !important;
            color: #a94442 !important;
        }

        /* Change text to light grey when readonly */
        .form-control:readonly,
        .hostedfield:readonly {
            color: lightgrey !important;
        }

        /* Emulate webkit auto fill style */
        .form-control.hf-autofill,
        .hostedfield.hf-autofill {
            background-color: rgb(250, 255, 189) !important;
            background-image: none !important;
            color: rgb(0, 0, 0) !important;
        }

        /* Add light blue placeholder */
        .form-control::placeholder,
        .hostedfield::placeholder {
            color: lightblue;
        }

        /* Show hovering over the control */
        .form-control:hover,
        .hostedfield:hover {
            font-style: italic;


        }

        /* Style by id (hosted field will have '-hostedfield' appended to the id) */
        #form-card-number,
        #form-card-number-hostedfield {
            color: darkcyan;
            margin-top: 3px !important;
        }
    </style>
    <!-- Hosted Field card-number internal styles -->
    <style class="card-number">
        .hostedfield::placeholder {
            color: orange;
        }
    </style>
    <div class="card" id="handpoint_payment_frm">
            <!-- tokenise payment data and send directly to the Gateway -->
            <form id="form" novalidate="novalidate" lang="en"
                data-hostedform-tokenize='{"#form-card-expiry-date":"card-expiry-date"}{"#form-card-number":"card-number"}{"#form-card-cvv":"card-cvv"}'>
                <input type="hidden" name="merchantID" value="<?php echo $merchant_id; ?>">


                <input type="hidden" name="paymentToken" value="">
                <input type="hidden" name="customerName" value="">
                <input type="hidden" name="customerFirstname" value="">
                <input type="hidden" name="customerLastname" value="">
                <input type="hidden" name="customerAddress" value="">
                <input type="hidden" name="customerTown" value="">
                <input type="hidden" name="customerPostCode" value="">
                
                <?php  foreach($deviceData as $key=>$value) { ?>
                <input type="hidden" name="browserInfo[<?php echo $key; ?>]" id="<?php echo $key; ?>" value="<?php echo $value; ?>" />
                <?php }?>

                <div class="form-group">
                    <label for="form-customer-name">Name on card:</label>
                    <input id="form-customer-name" type="text" name="paymentToken[customerName]" autocomplete="cc-name"
                           class="form-control form-control-native hostedfield-tokenise" placeholder="Firstname Surname" value="<?php echo $customerName; ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="form-card-number">Card Number:</label>
                    <input id="form-card-number" type="hostedfield:cardNumber" name="card-number"
                        autocomplete="cc-number" class="form-control form-control-hosted " style="background: #f2f8fb;"
                        placeholder="**** **** **** ****" required>
                </div>
                <div class="form-group">
                    <label for="form-card-expiry-date">Card Expiry Date:</label>
                    <input id="form-card-expiry-date" type="hostedfield:cardExpiryDate" name="card-expiry-date"
                        autocomplete="cc-exp" class="form-control form-control-hosted"
                        data-hostedfield='{"dropdown":true}' data- hostedfield-format="N - m | y"
                        data-hostedfield-min-date="-40" data-hostedfeld-max-date="0" required>
                </div>

                <div class="form-group">
                    <label for="form-card-cvv">CVV:</label>
                    <input id="form-card-cvv" type="hostedfield:cardCVV" name="card-cvv" autocomplete="cc-csc"
                        class="form-control form-control-hosted" required>
                </div>
                
                <br />
                
            </form>
                <div class="form-group">
                    <label for="customer-firstname"><abbr title="required">*</abbr>First Name:</label>
                    <input id="customer-firstname" name="customerFirstname" class="form-control form-control-extra" value="<?php echo $customerFirstname; ?>" required >
                </div>        
                <div class="form-group">
                    <label for="customer-lastname"><abbr title="required">*</abbr>Last Name:</label>
                    <input id="customer-lastname" name="customerLastname" class="form-control form-control-extra" value="<?php echo $customerLastname; ?>" required >
                </div>        
                <div class="form-group">
                    <label for="customer-address"><abbr title="required">*</abbr>Address <small>(Registered billing address)</small>:</label>
                    <input id="customer-address" name="customerAddress" class="form-control form-control-extra" value="<?php echo $customerAddress; ?>" required >
        </div>
                <div class="form-group">
                    <label for="customer-town"><abbr title="required">*</abbr>City:</label>
                    <input id="customer-town" name="customerTown" maxlength="50" value="<?php echo $customerTown; ?>"
                           class="form-control form-control-extra" required>
                </div>
                <div class="form-group">
                    <label for="customer-postcode"><abbr title="required">*</abbr>PostCode:</label>
                    <input id="customer-postcode" name="customerPostCode" maxlength="50" value="<?php echo $customerPostCode; ?>"
                           class="form-control form-control-extra" required>
                </div>
               <br />
               <div id="error-info"></div>
               <br />
               <button form="form" id="form-submit" type="submit"
                    style="background-color: #e97d29; color: white; text-align:right">Pay Now<span></span></button>
              
        </div>
     <script>
        // This example demonstrates both automatic and manual form setup
        var automatic_setup = false;
        function wait_for_jQuery_validator_to_load() {
    if (window.jQuery.validator && jQuery(window).hostedField ) {
        console.log('hostedField');
       //   $(document).ready(function () {
            var $form = $('#form');
            var url = $('#form').attr('action');
            // Listen for events on the form to see those sent from the Hosted Payment Fields
            // (For demonstration purposes only)
            $form.on(events);

            if (automatic_setup) {
                ///////////////////////////////////////////////////////////////
                // FORM AUTOMATIC SETUP
                ///////////////////////////////////////////////////////////////

                var opts = {
                    // Auto setup the form creating all hosted fields (default)
                    autoSetup: true,

                    // Auto validate, tokenise and submit the form (default)
                    autoSubmit: true,

                    // Optional field configuration (by type)
                    fields: {
                        any: {
                            nativeEvents: true,
                        },
                        cardNumber: {
                            selector: $('#form-card-number'),
                            style: 'text-decoration: green wavy underline; margin-top: 3px',
                            stylesheet: $('style.hostedfields, style.card-number')
                        }
                    }
                };

                try {
                    // Create form, automatically creating all child Hosted Payment Fields 
                    $form.hostedForm(opts);
                } catch (e) {
                    showError('Failed to create hosted form: ' + e);
                    throw e; // Can't continue with this script

                }
                // Listen for some events from the form thrown by the auto methods
                $form.on({
                    // Let jQuery Validator check the form on submission
                    'hostedform:presubmit': function (event) {
                        console.log('Form submitting');
                        return $form.valid();
                    },

                    // Show form is valid
                    'hostedform:valid': function (event) {
                        console.log('Form valid');
                        return true;
                    },

                    // Show any validation errors
                    'hostedform:invalid': function (event, details) {
                        console.log('Form invalid');
                        showFieldErrors(details.invalid);
                        return true;
                    },
                    // Show general error
                    'hostedform:error': function (event, details) {
                        showError(details.message);
                        return true;
                    }
                });
                // Use jQuery validator to validate the form 
                $form.validate();

                // End of form automatic setup

            } else {

                /////////////////////////////////////////////////////////////// // FORM MANUAL SETUP ///////////////////////////////////////////////////////////////
                try {
                    // Create the card number field with custom options 
                    $('#form-card-number').hostedField({
                        nativeEvents: true,
                        style: 'text-decoration: green wavy underline; margin-top: 3px',
                        stylesheet: $('style.hostedfields, style.card-number')
                    });

                    // Create the remaining hosted fields
                    $('.form-control-hosted:input', $form).hostedField({ nativeEvents: true });
                } catch (e) {
                    showError('Failed to create hosted fields: ' + e);
                    throw e; // Can't continue with this script
                }
                $form.validate({
                    // Get the hosted form widget for the submitted form (Form1 only) 
                    submitHandler: function () {
                        try {
                            hideError();
                            if ($("#customer-firstname").val().length < 2) {
                                showError('You must enter Firstname');
                                return false;
                            }
                            if ($("#customer-lastname").val().length < 2) {
                                showError('You must enter Lastname');
                                return false;
                            }
                            if ($("#customer-address").val().length < 5) {
                                showError('You must enter Address');
                                return false;
                            }
                            if ($("#customer-town").val().length < 3) {
                                showError('You must enter Town');
                                return false;
                            }
                            if ($("#customer-postcode").val().length < 4) {
                                showError('You must enter Postcode');
                                return false;
                            }
                            $('#form-submit').attr("disabled", true);
                           
                            console.log('getPaymentToken');
                            // Check we have some enabled fields to submit
                            if ($($form[0].elements).filter(':enabled:not([type="hidden"])').length === 0) {
                                showError('You must enable some fields');
                                return false;
                            }
                            var hostedform = $form.hostedForm('instance');
                            var also = {
                                customerName: $('#form-customer-name').val()
                            };
                            hostedform.getPaymentDetails(also, true).then(
                                // Success validating the form and requesting a payment token 
                                function (details) {
                                    if (details.success) {
                                        $form[0].elements['paymentToken'].value = details.paymentToken;
                                        
                                        $form[0].elements['customerName'].value =  $("#form-customer-name").val();
                                        $form[0].elements['customerFirstname'].value =  $("#customer-firstname").val();
                                        $form[0].elements['customerLastname'].value =  $("#customer-lastname").val();
                                        $form[0].elements['customerAddress'].value =  $("#customer-address").val();
                                        $form[0].elements['customerTown'].value =  $("#customer-town").val();
                                        $form[0].elements['customerPostCode'].value =  $("#customer-postcode").val();
                                      

                                        
                                       $form.attr('action', "<?php echo $hostedfieldurl; ?>");
                                       $form.attr("method", "post");
                                       $form[0].submit();
                                        
                                            
                                            
                                    } else if (details.invalid) {
                                        $form.valid();
                                        showFieldErrors(details.invalid);

                                    } else {
                                        showError('There was a problem fetching the payment token. Please seek assistance.');
                                    }
                                },
                                // Failure either validating the form or requesting the payment details 
                                function (e) {
                                    showError('There was a problem fetching the payment token. Please seek assistance.');
                                }
                            );
                        } catch (e) {
                            showError('There was a problem fetching the payment token. Please seek assistance.');
                        }
                    }
                });
                // End of form manual setup
            }
            // Hide errors once all fields are valid 
            $('#form :input').on('valid', function () {
                if ($(this.form).find(':invalid').length === 0) {
                    hideError($(this.form));
                }
            })
            // Listen for some events on the none Hosted Fields
            $('.form-control-native').on('invalid', bsMarkInvalid);
            $('.form-control-native').on('valid', bsMarkValid);
            // Check we can see the Hosted Fields via their new class // (For demonstration purposes only) 
            console.log($('.form-control-hosted.hostedfield-element'));
            // Check we can see the Hosted Fields via the psuedo element // (For demonstration purposes only) 
            console.log($('.form-control:hostedfield'));
     // ON READY   });
        /////////////////////////////////////////////////////////////////// // Supporting functions ///////////////////////////////////////////////////////////////////
        // Display events that are passed from hosted field 
        var events = {
            'hostedfield:create.example': showEvent,
            'hostedfield:destroy.example': showEvent,
            'hostedfield:ready.example': showEvent,
            'hostedfield:style.example': showEvent,
            'hostedfield:placeholder.example': showEvent,
            'hostedfield:invalid.example invalid.example': showEvent,
            'hostedfield:userinvalid.example userinvalid.example': showEvent,
            'hostedfield:valid.example valid.example': showEvent,
            'hostedfield:uservalid.example uservalid.example': showEvent,
            'hostedfield:disabled.example disabled.example': showEvent,
            'hostedfield:enabled.example enabled.example': showEvent,
            'hostedfield:required.example required.example': showEvent,
            'hostedfield:optional.example optional.example': showEvent,
            'hostedfield:readonly.example readonly.example': showEvent,
            'hostedfield:readwrite.example readwrite.example': showEvent,
            'hostedfield:focus.example focus.example': showEvent,
            'hostedfield:blur.example blur.example': showEvent,
            'hostedfield:mouseenter.example mouseenter.example': showEvent,
            'hostedfield:mouseleave.example mouseleave.example': showEvent,
            'hostedfield:mouseover.example mouseover.example': showEvent,
            'hostedfield:mouseout.example mouseout.example': showEvent,
            'hostedfield:mousemove.example mousemove.example': showEvent,
            'hostedfield:keydown.example keydown.example': showEvent,
            'hostedfield:keypress.example keypress.example': showEvent,
            'hostedfield:keyup.example keyup.example': showEvent,
            'hostedfield:change.example change.example': showEvent,
            'hostedfield:input.example input.example': showEvent,
            'hostedfield:invalid.example invalid.example': bsMarkInvalid,
            'hostedfield:valid.example valid.example': bsMarkValid,
            'hostedfield:valid.example valid.example': hideError,

        };

        function isInvalid(element) {
            return !element[0].checkValidity();
        }
        function showError(msg) {
            $('#error-info').html(msg).show();
            $('#form-submit').removeAttr("disabled");
        }
        function hideError($form, msg) {
            $('#error-info', $form).hide();
        }


        function showFieldErrors(errors) {
            var msg = '<h5>Error</h5><p>The following fields are invalid:</p><ul>';
            for (var p in errors) {
                if(p.slice(0,12)!='hostedfield-') {
                msg += '<li><b>' + p + ':</b> ' + errors[p] + '</li>';
            }
            }
            msg += '</ul>'
            showError(msg);
        }
        function bsMarkInvalid(e) {
            var element = (e instanceof $.Event ? this : e); $(element).closest('.form-group').addClass('has-error');
        }
        function bsMarkValid(e) {
            var element = (e instanceof $.Event ? this : e); $(element).closest('.form-group').removeClass('has-error');
        }
        function showEvent(event) {
            console.log(event);
            console.log('Field ' + event.type + ' event: ', this, arguments);
        }

        jQuery.validator.setDefaults({
            ignore: [],
            rules: {
                'customer-name': {
                    checkValidity: true,
                    required: false
                },
                'card-details': {
                    checkValidity: true,
                    required: false
                },
                'card-number': {
                    checkValidity: true,
                    required: false,
                    
                },
                'card-expiry-date': {
                    checkValidity: true,
                    required: false
                },
                'card-start-date': {
                    checkValidity: true,
                    required: false
                },
                'card-issue-number': {
                    checkValidity: true,
                    required: false
                },
                'card-cvv': {
                    checkValidity: true,
                    required: false
                }
            },
            keyup: null, // Don\'t validate on keyup
            showErrors: function (errorMap, errorList) {
                if (errorList && errorList.length) {
                    var errors = {};
                    for (var i = 0, max_i = errorList.length; i < max_i; i++) {
                        var label = $('label[for="' + errorList[i].element.id + '"]:not(".error")').text();
                        errors[label] = errorList[i].message;
                    }
                    showFieldErrors(errors);
                }
                this.defaultShowErrors(errorMap, errorList);
            },
            highlight: bsMarkInvalid,
            unhighlight: bsMarkValid,
            errorPlacement: function (error, element) {
                $(element).closest('.form-control:not(".hostedfield-element")').after(error);
            }
        });

        $.validator.addMethod('checkValidity',
            function (value, element, params, message) {
                element.checkValidity();
                var valid = (element.validationMessage === '');
                $(element).attr('aria-invalid', !valid);
                return valid;
            },
            function (params, element) {
                return element.validationMessage;
            }
        );
        }
        else {
         console.log('No hostedField');
        setTimeout(function() { wait_for_jQuery_validator_to_load(); },50);
        }
}
setTimeout(function() { wait_for_jQuery_validator_to_load(); }, 50);

var screen_width = (window && window.screen ? window.screen.width : '0');
    var screen_height = (window && window.screen ? window.screen.height : '0');
    var screen_depth = (window && window.screen ? window.screen.colorDepth : '0');
    var identity = (window && window.navigator ? window.navigator.userAgent : '');
    var language = (window && window.navigator ? (window.navigator.language ? window.navigator.language : window.navigator.browserLanguage) : '');
    var timezone = (new Date()).getTimezoneOffset();
    var java = (window && window.navigator ? navigator.javaEnabled() : false);
    document.getElementById('deviceIdentity').value = identity;
    document.getElementById('deviceTimeZone').value = timezone;
    document.getElementById('deviceCapabilities').value = 'javascript' + (java ? ',java' : '');
    document.getElementById('deviceAcceptLanguage').value = language;
    document.getElementById('deviceScreenResolution').value = screen_width + 'x' + screen_height + 'x' + screen_depth;
    
    </script>