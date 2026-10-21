<div style="clear:both;" class="xxx"></div>
</div>
</div>
</div>



<div id="footer_bg">
  <div class="footer-last-links" id="bottom-links">
    <div class="container">
      <div class="col-sm-4" id="contact_info">
  <img src="image/data/hygiene.png" class="img-responsive" title="hygiene" alt="hygiene" width="259" height="144" loading="lazy" />
  </div>
          <div class="col-sm-4 applinks text-center">
        <?php if($this->config->get('config_android_appurl')){ ?><p><strong>Download our app</strong></p>
        <a class="android" href="<?php echo $this->config->get('config_android_appurl'); ?>" target="_blank"><img src="/catalog/view/theme/default/stylesheet/images/gplay.png" alt="android" title="android" width="130" height="40"></a>
        <?php } ?>
        <?php if($this->config->get('config_iphone_appurl')){ ?>
        <a class="iphone" href="<?php echo $this->config->get('config_iphone_appurl'); ?>" target="_blank"><img src="/catalog/view/theme/default/stylesheet/images/appstore.png" alt="iphone" title="iphone" width="130" height="40"></a>
        <?php } ?>
      </div>
      <div class="col-sm-4">
      <p><strong>All Major Cards Accepted</strong></p>
      <img src="imgd/card.png" class="img-responsive" width="341" height="30" loading="lazy" id="card" title="card" alt="card" style="margin:auto;    filter: contrast(0.5);">
      </div>
      <div class="col-sm-12" id="links">
        <ul>
          <li><a href="/terms-and-conditions" title="terms &amp; Conditions">Terms &amp; Conditions</a></li>
          <li><a href="/privacy-policy" title="Privacy Policy">Privacy Policy</a></li>
          <li><a title="Cookie Policy" href="/cookie-policy">Cookie Policy</a></li>
          <li><a href="/service-disclaimer" title="Service Disclaimer">Service Disclaimer</a></li>
<li><a href="/refund-policy" title="Refund Policy">Refund Policy</a></li>

          <li><a href="/index.php?route=information/contact" title="Contact Us">Contact Us</a></li>
        </ul>
      </div>
          <div class="col-sm-12 powered">
        <p><?php echo $powered; ?></p>
      </div>
    </div>

  </div>
</div>
<!--
<?php if(!isset($cookieaccepted)){ ?>
<span id="footer_cookie"> We use cookies to track usage and preferences <a onclick="acceptcookie()">I Understand</a> </span>
<?php } ?>
-->
<script type="text/javascript">
 function acceptcookie(){
    $('#footer_cookie').hide();
    $.ajax({
        url: 'index.php?route=common/footer/acceptcookie',
        type: 'post',
        data: {} ,
        dataType: 'json',
        success: function(json) {
           
        }
    });
}
</script>



<div id="fb-root"></div>

<script type="text/javascript">
 $(document).ready(function() {
$(document).on('click','.navbar-collapse.in',function(e) {
    if( $(e.target).is('a') ) {
        $(this).collapse('hide');
    }
});     
$('.cart_prodt_customize').on('click', function() {
//alert("test");
var prod_id = $(this).attr('prodid');
var key= $(this).attr('id');
$('.customizable_options_widget').load("index.php?route=product/product/ajaxcustomoptionswidgets",{product_id: prod_id,key:key},function(response,statusTxt,xhr){
    if(statusTxt=="success")
        $('.customizable_options_widget').dialog({
            autoOpen: true,
            height: 'auto',
            width: '50%',
            modal: true,
        });
    if(statusTxt=="error")    
       alert("Failed");
    })
});
$('#button_cancel_woc').on('click', function() {
              $('.customizable_options_widget').dialog("close");
        });
        
$('#button_modify_cart').on('click', function() {
    $.ajax({
        url: 'index.php?route=product/product/modifycart',
        type: 'post',
        data: $('.product_custom_options input[type=\'text\'], .product_custom_options input[type=\'hidden\'], .product_custom_options input[type=\'radio\']:checked, .product_custom_options input[type=\'checkbox\']:not(:checked), .product_custom_options select, .product_custom_options textarea'),
        dataType: 'json',
        success: function(json) {
            $('.success, .warning, .attention, information, .error').remove();
            if (json['error']) {
                if (json['error']['option']) {
                    for (i in json['error']['option']) {
                        $('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                    }
                }
            }

            if (json['success']) {
                $('#cart').load('index.php?route=module/cart #cart > *');
                $('#cart-total').html(json['total']);
                $('.customizable_options_widget').dialog("close");              
            }
           if (json['no_mod']) {
           
            $('#cart').load('index.php?route=module/cart #cart > *');
                $('#cart-total').html(json['total']);
                $('.customizable_options_widget').dialog("close");
           } 
        }
     });
 });
});

    
</script>
<script type="text/javascript">
      $(function() {
           checkitemisincart();
      });
</script>

</body></html>