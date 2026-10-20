<?php echo $header; ?>
<div id="content" class="col-sm-8">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>
  
  <div class="col-sm-12">
      
      <div class="col-sm-12" style="text-align: center" >
          <h3> <?php echo $text_message; ?></h3>
      </div>
      <div class="col-xs-12" style="text-align: center">
          <h3># <?php echo $order_id; ?></h3>
      </div>
      <div class="col-xs-12" style="text-align: center">
          <h3><?php echo $table; ?></h3>
      </div>  
      <div class="col-xs-12" style="padding: 15px;"></div>
      <!--<div class="col-sm-12" style="text-align: center;padding: 5px;">
          <a class="btn btn-commnt btn-warning" onclick="shownotifypopup()">COMMENTS</a>
      </div>--> 
      <div class="col-xs-12" style="text-align: center;padding: 5px;">
          <a class="btn btn-commnt btn-success" href="<?php  echo $tablecontinue ; ?>">NEW ORDER</a>
      </div>
      <div class="col-xs-12" style="text-align: center;padding: 5px;">
          <a class="btn btn-commnt btn-danger" href="<?php  echo $continue ; ?>">FINISH EAT-IN</a>
      </div>      
        
  </div>
     
  
</div>
<div class="modal" tabindex="-1" role="dialog"  id="store_notficicn_popup" aria-labelledby="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">                    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4  class="modal-title">Send Instructions ? </h4>
                </div>
                <div class="modal-body">
                    <div class="form form-group">
                        <input type="hidden" name="order_id" value="<?php echo $order_id ?>">
                        <div class="row">
                        <label  class="col-sm-3 control-label">Comments</label>
                        <div class="col-sm-7">
                            <textarea name="comment" rows="5" id="notcomment" style="width: 100%"></textarea>                     
                        </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="float: left">Close</button>
                    <a onclick="sendnotiifcation();"  class="button">Send</a>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    function shownotifypopup(order_id){
        $('#notify_orderid').val(order_id);
        $('#store_notficicn_popup').modal('show');
    }
    function sendnotiifcation(){
        $.ajax({
            url: 'index.php?route=checkout/eatinsuccess/sendnotification',
            type: 'post',
            data:$('#store_notficicn_popup input[type=\'hidden\'],#store_notficicn_popup input[type=\'text\'],#store_notficicn_popup textarea'),
            dataType: 'json',
            success: function(json) {
                
                $('.modal').modal('hide');                    
                $('#notification').html('<div class="success">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');				
		$('.success').delay(5000).fadeOut();
	
            }    
        })    
    };
</script>
<?php echo $footer; ?>