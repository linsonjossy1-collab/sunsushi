<script type="text/javascript">
    window.onerror = function myErrorHandler(errorMsg, url, lineNumber) {
        alert("Error occured: " + errorMsg + "\nurl: " + url + "\nline: " + lineNumber);//or any
        return false;
    }
</script> 
<style>  
    label {  
        width: 10em;  
        display: inline�block;  
        margin: 0 0 0.5em 0;  
    } 
    input {  
        width: 15em;  
    } 
</style>

<form method="get" action="<?php echo $ipg_payment_baseurl ?>">
    <input type="hidden" name="token" value="<?php echo $token; ?>"/><br/>  
    <input type="hidden" name="merchantId" value="<?php echo $merchantId; ?>"/><br/>  
    <input type="hidden" name="paymentSolutionId" value="500"/><br/>
    <input type="hidden" name="integrationMode" value="standalone"/>
    <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
</form>   