<style>  
    #ipgCashierDiv{  
        width: 400px;  
        height: 400px;  
       /* border: 1px solid gray;  */
        margin: 10px;  
    } 
    label {  
        width: 10em;  
        display: inline­block;  
        margin: 0 0 0.5em 0;  
    } 
    input {  
        width: 15em;  
    } 

</style>  
<script type="text/javascript"  
src="<?php echo $ipg_payment_javascripturl; ?>"></script>  
<script type="text/javascript">
    var cashier = null;
    function wait_for_ipg_to_load() {
        if (window.com) {
            cashier = com.myriadpayments.api.cashier();
            cashier.init(
                {baseUrl: '<?php echo $ipg_payment_baseurl ?>'}
            );
            pay();
        }    
        else {
            setTimeout(function() { wait_for_ipg_to_load() }, 50);
        }
    }
    wait_for_ipg_to_load();
    
    /*
    var cashier = com.myriadpayments.api.cashier();
    cashier.init(
    {baseUrl: '<?php echo $ipg_payment_baseurl ?>'}
    ); */
    function handleResultSuccess(data) {
        //alert(JSON.stringify(data));
        // location = '<?php echo $ipg_payment_succuss; ?>';
    }
    function handleResultFailure(data) {
        //alert(JSON.stringify(data));
        // location = '<?php echo $ipg_payment_cancel; ?>';
    }
    function handleResultCancel(data) {
       // alert(JSON.stringify(data));
      //   location = '<?php echo $ipg_payment_cancel; ?>';
    }
    function pay() {
        var token = '<?php echo $token; ?>'; //document.getElementById("tokenIn").value;
        var merchantId = '<?php echo $merchantId; ?>';// document.getElementById("merchantIdIn").value;
        cashier.show(
                {
                    containerId: "ipgCashierDiv",
                    merchantId: merchantId,
                    token: token,
                    successCallback: handleResultSuccess,
                    failureCallback: handleResultFailure,
                    cancelCallback: handleResultCancel
                }
        );
    }
    ;
</script>
  <!-- <div> 
 <label>token:</label><input id="tokenIn" value="<?php echo $token; ?>"/><br/>  
    <label>merchantId:</label><input id="merchantIdIn" value="<?php echo $merchantId; ?>"/><br/> 
    <button onclick="pay()">Pay</button>  
</div> -->  
<div id="ipgCashierDiv">Loading..</div>  