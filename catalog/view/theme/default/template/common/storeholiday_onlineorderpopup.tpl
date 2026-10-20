<div class="modal fade" tabindex="-1" role="dialog" id="online_order_msg_popup">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Online Order Closed</h4>
      </div>
      <div class="modal-body">
<div>Today Is HOLIDAY 
<br/>
Please Check Opening Hours,AND PLACE ORDER IN NEXT DAY 
<br/>
<h3><div class="cat_name">Opening Hours </div></h3>
<div >
 <ul>
            <?php foreach($store_times as $day=>$store_time){ ?>
            <li> <span class="pop-day"> <?php echo $day; ?> </span>
                      <?php $d=1; ?>
                      <?php if(!empty($store_time)){ ?>
                          <?php foreach($store_time as $time){ ?>
                          <span class="pop-time"><?php echo $time['start'].' - '.$time['end']; ?>
                          <?php if($d<count($store_time)){ ?>
                          &AMP;
                          <?php } ?>
                          </span>
                          <?php $d++; ?>
                          <?php } ?>
                      <?php }else{ ?>
                      <span class="pop-time">Closed</span>
                      <?php } ?>
                    </li>
            <?php } ?>
          </ul>

</div>
</div>
    </div>
        <div class="modal-footer">
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