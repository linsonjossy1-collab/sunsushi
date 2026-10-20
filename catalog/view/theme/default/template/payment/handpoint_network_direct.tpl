<div class="content" id="payment">
    <h4><?php echo $text_credit_card ?></h4>

    <form action="<?php echo $process_url ?>" method="post" id="cc_payment_form" >
        <div style="background:transparent; min-width: 275px; max-width: 300px;">
            <div class="form-group">
                <label for="cardNumber"><?php echo $cc_card_number ?><span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="field-cardNumber" name="cardNumber" autocomplete="off">
            </div>
            <div class="form-group" style="display: flex; justify-content: space-between;">
                <div class="form-group" style="width: 60%">
                    <label for="cardExpiryDate"><?php echo $cc_card_expiry_date ?><span class="text-danger">*</span></label>
                    <div style="display: flex; justify-content: space-between;">
                        <select class="form-control" style="width: 46%;" id="field-cardExpiryMonth" name="cardExpiryMonth" required='required'>
                            <option value="" disabled selected>Month</option>
                            <?php foreach($monthOptions as $key=>$val) { ?>
                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                           <?php } ?>
                        </select>
                        <select class="form-control" style="width: 45%;" id="field-cardExpiryYear" name="cardExpiryYear" required='required'>
                            <option value="" disabled selected>Year</option>
                             <?php foreach($yearOptions as $key=>$val) { ?>
                             <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="width: 30%">
                    <label for="cardCVV"><?php echo $cc_card_cvv; ?> (CVC) <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="field-cardCVV" name="cardCVV" autocomplete="off" placeholder="CVC">
                </div>
            </div>
            <?php foreach($deviceData as $key=>$val) { ?>
            <input type="hidden" name="browserInfo[<?php echo $key ?>]" id="<?php echo $key ?>" value="<?php echo $value ?>" />
            <?php } ?>
        </div>
        <input type="submit" disabled value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary pull-right" />
    </form>

</div>

<script type="text/javascript" src="catalog/view/javascript/payform.js"></script>
<script type="text/javascript">
    var cardNumber = document.getElementById('field-cardNumber');

    var isValidForm = {
        cardNumber: null,
        cvv: null,
        expireDate: null
    };

    payform.cardNumberInput(cardNumber);
    cardNumber.addEventListener('change', e => {
        const validateCardNumber = payform.validateCardNumber(e.target.value);
        isValidForm.cardNumber = validateCardNumber;

        e.target.style.borderColor = validateCardNumber ? '#B0B0B0' : 'red';
        document.getElementById('button-confirm').disabled = !(isValidForm.cvv && isValidForm.expireDate && isValidForm.cardNumber);
    });

    document.getElementById('field-cardCVV').addEventListener('change', e => {
        const validateCardCVC = payform.validateCardCVC(e.target.value);
        isValidForm.cvv = validateCardCVC;

        e.target.style.borderColor = validateCardCVC ? '#B0B0B0' : 'red';
        document.getElementById('button-confirm').disabled = !(isValidForm.cvv && isValidForm.expireDate && isValidForm.cardNumber);
    });

    var cardExpiryMonthElement = document.getElementById('field-cardExpiryMonth');
    var cardExpiryYearElement = document.getElementById('field-cardExpiryYear');

    var listener = e => {
        let isValid = payform.validateCardExpiry(cardExpiryMonthElement.value, '20'+cardExpiryYearElement.value);

        isValidForm.expireDate = isValid;
        cardExpiryMonthElement.style.borderColor =  isValid ? '#B0B0B0' : 'red';
        cardExpiryYearElement.style.borderColor = isValid ? '#B0B0B0' : 'red';

        document.getElementById('button-confirm').disabled = !(isValidForm.cvv && isValidForm.expireDate && isValidForm.cardNumber);
    };

    cardExpiryMonthElement.addEventListener('change', listener);
    cardExpiryYearElement.addEventListener('change', listener);

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
