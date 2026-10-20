<!DOCTYPE html>
<html>
<head>
    <title>HPP Lightbox Demo</title>
    <meta charset="UTF-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="catalog/view/javascript/realex_hpp/rxp-js.js"></script>
    <script>
        
        // get the HPP JSON from the server-side SDK
        $(document).ready(function () {
                RealexHpp.setHppUrl('https://pay.sandbox.realexpayments.com/pay');
                RealexHpp.init(
                    "payButtonId",
                    "<?php echo $response_url; ?>",
                    <?php echo $requestJson; ?>
                );
                $('body').addClass('loaded');
            });

    </script>
</head>
<body>
  <button type="button" id="payButtonId">Checkout Now</button>
</body>
</html>