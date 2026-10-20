<?php echo $header; ?>
<style>
#progress .node {
    height: 10px;
    width: 10px;
    border-radius: 50%;
    display:inline-block;
    transition: all 1000ms ease;
}
#progress .activated {
    box-shadow: 0px 0px 3px 2px rgba(194, 255, 194, 0.8);
}

#progress .divider {
    height: 40px;
    width: 2px;
    margin-left: 4px;
    transition: all 800ms ease;
}

#progress li p {
    display:inline-block;
    margin-left: 25px;
}

#progress li {
    list-style: none;
    line-height: 1px;
}

#progress .time{ color:#736c6c; }
#progress .blue { background-color: rgba(82, 165, 255, 1); }
#progress .green{ background-color: rgba(92, 184, 92, 1) }
#progress .red { background-color: rgba(255, 148, 148, 1); }
#progress .grey { background-color: rgba(201, 201, 201, 1); }
</style>

<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content" class="col-sm-8"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><span id="head-title"><?php echo $heading_title; ?></span></h1>

  <div id='error_div'></div>

    <ul id="progress">
        <?php $i = 0; ?>
        <?php foreach($track_status as $key => $stat) { ?>
          <?php $c = ($stat['date_added']) ? 'green' : 'grey'; ?>
          <?php if($i > 0) { ?>
            <li><div class="divider <?php echo $c; ?>" id="d_<?php echo $stat['id']; ?>"></div></li>
          <?php } $i++; ?>
        <li> 
          <div class="node <?php echo $c; ?>" id="n_<?php echo $stat['id']; ?>"></div>
          <p><?php echo $stat['name']; ?> <small class="time" id="stat_ttime_<?php echo $stat['id']; ?>"> <?php echo $stat['date_added']; ?></small></p>
        </li>
        <?php } ?>
    </ul>

  <?php echo $locationmap; ?>
 <div id="delinfo">
 <?php if($delivery_time) { ?>
 <h4><?php echo $entry_delivery_time; ?> <?php echo $delivery_time; ?></h4>
 <?php } ?>
 <?php if(isset($eta_time) && $eta_time) { ?>
 <h4><?php echo $entry_eta_time; ?> <?php echo $eta_time; ?></h4>
 <?php } ?>
 </div>
  <table class="table">
    <thead>
      <tr>
        <td class="left" colspan="2"><?php echo $text_order_detail; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left" style="width: 50%;"><?php if ($invoice_no) { ?>
          <b><?php echo $text_invoice_no; ?></b> <?php echo $invoice_no; ?><br />
          <?php } ?>
          <b><?php echo $text_order_id; ?></b> #<?php echo $order_id; ?><br />
          <b><?php echo $text_date_added; ?></b> <?php echo $date_added; ?><br />
          <b><?php echo $entry_requested_time; ?></b> <?php echo $requested_time; ?>
        </td>
        <td class="left" style="width: 50%;"><?php if ($payment_method) { ?>
          <b><?php echo $text_payment_method; ?></b> <?php echo $payment_method; ?><br />
          <?php } ?>
          <?php if ($shipping_method) { ?>
          <b><?php echo $text_shipping_method; ?></b> <?php echo $shipping_method; ?>
          <?php } ?></td>
      </tr>
    </tbody>
  </table>
  <table class="table">
    <thead>
      <tr>
        <td class="left"><?php echo $text_payment_address; ?></td>
        <?php if ($shipping_address) { ?>
        <td class="left"><?php echo $text_shipping_address; ?></td>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left"><?php echo $payment_address; ?></td>
        <?php if ($shipping_address) { ?>
        <td class="left"><?php echo $shipping_address; ?></td>
        <?php } ?>
      </tr>
    </tbody>
  </table>
  <table class="table">
    <thead>
      <tr>
        <td class="left"><?php echo $column_name; ?></td>
          <td class="right"><?php echo $column_quantity; ?></td>
        <td class="right"><?php echo $column_price; ?></td>
        <td class="right"><?php echo $column_total; ?></td>
        <?php if ($products) { ?>
        <!--<td style="width: 1px;"></td>-->
        <?php } ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product) { ?>
      <tr>
        <td class="left"><?php echo $product['name']; ?>
          <?php foreach ($product['option'] as $option) { ?>
          <br />
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?>
            <?php foreach ($product['toppings'] as $topping) { ?>
                -<small> <?php echo $topping['quantity']." X ". $topping['name'].":".$topping['value'];?></small><br />
            <?php } ?>
            <?php foreach ($product['dressings'] as $dressing) { ?>
                -<small><?php echo $dressing['name'].":".$dressing['value'];?></small><br />
            <?php } ?>
          <?php foreach ($product['bundle'] as $bundle) { ?>
                <br />
                
                &nbsp;<b><small> - <?php echo $bundle['name']; ?></small></b><br/>
                <?php if(isset($bundle['options'])) { ?>
                <?php foreach ($bundle['options'] as $option) { ?>
                &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small><br/>
                <?php } ?>
                <?php } ?>
                <?php } ?>
        </td>
        <td class="right"><?php echo $product['quantity']; ?></td>
        <td class="right"><?php echo $product['price']; ?></td>
        <td class="right"><?php echo $product['total']; ?></td>
        <!--<td class="right"><a href="<?php //echo $product['return']; ?>"><img src="catalog/view/theme/default/image/return.png" alt="<?php echo $button_return; ?>" title="<?php echo $button_return; ?>" /></a></td>-->
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
        <td class="left"><?php echo $voucher['description']; ?></td>
        <td class="right">1</td>
        <td class="right"><?php echo $voucher['amount']; ?></td>
        <td class="right"><?php echo $voucher['amount']; ?></td>
        <?php if ($products) { ?>
        <!--<td></td>-->
        <?php } ?>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php foreach ($totals as $total) { ?>
      <tr>
    
        <td colspan="3" class="right"><b><?php echo $total['title']; ?>:</b></td>
        <td class="right"><?php echo $total['text']; ?></td>
        <?php if ($products) { ?>
        <!--<td></td>-->
        <?php } ?>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
  <?php if ($comment) { ?>
  <table class="table">
    <thead>
      <tr>
        <td class="left"><?php echo $text_comment; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left"><?php echo $comment; ?></td>
      </tr>
    </tbody>
  </table>
  <?php } ?>
  <?php if ($histories) { ?>
  <h2><?php echo $text_history; ?></h2>
  <table class="table">
    <thead>
      <tr>
        <td class="left"><?php echo $column_date_added; ?></td>
        <td class="left"><?php echo $column_status; ?></td>
        <td class="left"><?php echo $column_comment; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($histories as $history) { ?>
      <tr>
        <td class="left"><?php echo $history['date_added']; ?></td>
        <td class="left"><?php echo $history['status']; ?></td>
        <td class="left"><?php echo $history['comment']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } ?>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
  </div>
  <?php echo $content_bottom; ?></div>

<script>


setInterval(function(){
 //  refreshstatus();
}, 18000);

function refreshstatus() {
  $('#error_div').html('');
  $.ajax({ cache: false,
      url: "index.php?route=account/order/status&order_id=<?php echo $order_id;?>",
      //data: { order_id:order_id },
      dataType: "json",
  }).done(function (json) {
    if(json['order_status_id']) {
      if (typeof json['clear'] !== 'undefined' && json['clear']) {
        clearInterval(dInterVal);
      }
      <?php if(!isset($map_set)) { ?>
        if(json['map']) {
          location.reload();
        }
      <?php } ?>
      //orderprogressStatus(json['order_status_id']);
      $('#d_'+json['order_status_id']).removeClass('grey').addClass('green');
      $('#n_'+json['order_status_id']).removeClass('grey').addClass('green');
      $('#stat_ttime_'+json['order_status_id']).html(json['time']);
      laststatus(json['order_status_id']);

      var delinfo = '<h4>'+json['del_time']+'</h4>';
      if(json['eta_time']) {
        delinfo+='<h4><?php echo $entry_eta_time; ?> '+json['eta_time']+'</h4>';
      }
      $('#delinfo').html(delinfo);
    }
  }).fail(function (jqXHR, textStatus) {
      $('#error_div').html('<div class="warning">Cannot connect to server</div>');
  });
}

//orderprogressStatus('<?php echo $order_status_id; ?>');

function orderprogressStatus(id) {
    var listItems = $("#ostatuslist li");
    listItems.removeClass('done active');
    var len = listItems.length;
    var ind;
    var i = 0;
    for ( ind = 0;  ind < len; ind++ ) {
        var oid = $(listItems[ind])
        if(oid.attr('oid')==id) {
          oid.addClass('');
          ind = len;
          i = 1;
        } else {
          oid.addClass('done');
        }
    }
    if(i==0) {
      listItems.removeClass('done');
    }
  }

  laststatus('<?php echo $order_status_id; ?>');
  function laststatus(id) {
    var listItems  = $("#d_"+id).parent('li').prevAll();

    listItems.each(function(index){
      $(this).find('.grey').addClass('green').removeClass('grey');
    });
  }

</script>
<?php echo $footer; ?> 