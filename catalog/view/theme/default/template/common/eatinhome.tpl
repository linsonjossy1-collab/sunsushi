<?php echo $header; ?>

<div class="container" id="eatin_home">
    <div class="row">
        <div id="content" class="col-sm-12">
            <div id="hometop">
                <div class="container">
                    <a href="eatin/" onclick="$('#store_table_popup').modal('show');" class="order-now btn btn-primary">Table Order</a>                    
                </div>                
            </div>
        </div>
    </div>
    <div class="modal fade" id="store_table_popup" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    Choose Table to Order
                </div>
                <div class="modal-body">
                    <div class="form-controll">
                        <select data-hide-disabled="true"  class="form-control selectpicker">
                            <?php foreach($store_tables as $store_table){ ?>
                            <option value="<?php echo $store_table['table_id']; ?>"><?php echo $store_table['name']; ?></option>
                            <?php  } ?>
                        </select>
                    </div>
                    <div class="form-controll">
                        <input class="form-control" id="copeple" type="text" name="people" value="" placeholder="Enter No.of People">
                    </div>
                </div>
                <div class="modal-footer">
                    <button style="float: left" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button onclick="dineinorder()" style="float: right" type="button" class="btn btn-success">CONTINUE</button>
                </div>
            </div>
        </div>
    </div>
</div>
  </div>
</div>
<script type="text/javascript">
    var action = '<?php echo $action; ?>';
    $(document).ready(function(){   
        $('.selectpicker').selectpicker();            
        $('#store_table_popup').modal('show');        
        $("#store_table_popup").on("shown.bs.modal", function () {
            $('.selectpicker').selectpicker('refresh');
        });
    });
    function dineinorder(){
        //alert(action);//
        //alert(action+'?tableid='+parseInt($('.selectpicker').val()));
      window.location  = action+'&tableid='+parseInt($('.selectpicker').val())+'&people'+parseInt($('#copeple').val());
    
    };
</script>
<?php echo $footer; ?>