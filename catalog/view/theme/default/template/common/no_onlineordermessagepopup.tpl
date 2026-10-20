<div class="modal fade" tabindex="-1" role="dialog" id="online_order_msg_popup">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center">Online Order Closed</h4>
      </div>
      <div class="modal-body">

<div class="text-center"><h5>Sorry, We are currently not accepting online orders.</h5><h3>Please contact the restaurant for any help</h3>
<br/>
    <?php if(isset($store_info)) { ?>

      <h4 style="line-height: 25px;font-weight: 700;"><span class="store_name_home"><?php echo $store_info['config_name']; ?></span><br/>
      <?php echo $store_info['config_address']; ?></br>
      <a href="tel:<?php echo $store_info['config_telephone']; ?>" style="font-size: 20px;color: #000;"><?php echo $store_info['config_telephone']; ?></a></br></h4>
<?php } else { ?>
    <h4 style="line-height: 25px;font-weight: 700;">   <span class="store_name_home">   
    <?php echo $this->config->get('config_name');?></span>
    <br/>
    <?php echo $this->config->get('config_address');?>
    <br/>
    <a href="tel:<?php echo $store_info['config_telephone']; ?>" style="font-size: 20px;color: #000;"><?php echo $this->config->get('config_telephone');?></a></h4>
    <br/>
    <?php } ?>


</div>
</div>
      <div class="modal-footer" style="display:none;">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
function onlineorderpopup(){
  //  alert(message);
     
        $('#online_order_msg_popup').modal('show');
    
    }  
</script>