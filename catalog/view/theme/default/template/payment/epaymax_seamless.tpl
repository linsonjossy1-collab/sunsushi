<?php echo $header; ?>
<div style="text-align: center; min-height: 250px;">
    <script type="text/javascript">
    function epaymaxresposnse(response) {
      // This function could be used to query the status of the payment from the server side and update the display accordingly.
      console.log(response); // JSON object with the API response
      document.getElementById("hidden_resposnse").value = JSON.stringify(response);
     document.getElementById("frmepaymaxresposnse").submit();
    }
  </script>
    <div id="payment-form-container">Loading...
     <script type="application/json">
    {
      "onPaymentResult":"epaymaxresposnse"
    }
  </script>
  
        </div>
        <script async="true" src="https://connect2.payxpert.com/payment/<?php echo $epaymax_customerToken; ?>/connect2pay-seamless-v1.4.2.js"
                data-mount-in="#payment-form-container" 
                integrity="sha384-IoRQG+pUfZCjRGiCiWSCclTqWsYlBvxz+seniI0eA28/9dwPRt/aIQLstBDEzMDx" crossorigin="anonymous"></script>
                  
</div>
<form action="<?php echo $returnseamless_action; ?>" method="post" id="frmepaymaxresposnse">
       <input type="hidden" name="epaymax_resposnse" id="hidden_resposnse" value="" />
       <input type="hidden" name="order_id"  value="<?php echo $order_id; ?>" />
       
</form>
<div id="payment_cancel_button"  class="pull-left"><a class="btn btn-default text-center" href="<?php echo $back_action; ?>" style="margin-top: 30px;">Go Back</a></div>        
<?php echo $footer; ?>
    
    