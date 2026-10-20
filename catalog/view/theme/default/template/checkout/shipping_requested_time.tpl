<?php if(!empty($all_requested_times)){ ?>
<h3> Choose your time slot</h3>
<select name="order_requested_day" id="order_requested_day" class="form-control">
    <?php foreach ($all_requested_times['days'] as  $k=>$days) { ?>
    <option key ="<?php echo $k; ?>" value="<?php echo $days['timestamp']; ?>" <?php echo ($days['timestamp']==$order_requested_day)?'selected':'';  ?> ><?php echo $days['day']; ?></option> 
    <?php } ?>
</select>
 <select name="order_requested_time" id="order_requested_time" class="form-control">
    <?php foreach ($requested_times as  $key=>$requested_time) { ?>
    <?php if ($key == $order_requested_time) { ?>
    <option value="<?php echo $key; ?>" selected="selected"><?php echo $requested_time; ?></option>
    <?php } else { ?>
    <option value="<?php echo $key; ?>"><?php echo $requested_time; ?></option>
    <?php } ?>
    <?php } ?>
</select>
<?php }else if($shipping_method!=''){ ?>
<div class="notimewarning warning">No  <?php echo $text_requested_time; ?>  slots available</div>
<?php } ?>
<script>
    $(document).ready(function(){
        $("#order_requested_day" ).change(function() {
            var key = $('option:selected', this).attr('key');
            var reqtimes = <?php echo json_encode($all_requested_times['times']);?>;
            html ='';
            for(var ind in reqtimes[key]){
                html+='<option value="'+ind+'">'+reqtimes[key][ind]+'</option>';
            }
            $('#order_requested_time').html(html);
            setdeliveryday();
        });
    });
   function changetimeslots(){
        alert("TESTT");
        //
        //
    }   
</script>

