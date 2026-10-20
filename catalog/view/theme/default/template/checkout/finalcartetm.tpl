<div class="checkout-product">
    <table>
        <thead>
            <tr>
                <td class="name"><?php echo $column_name; ?></td>
                <td class="quantity"><?php echo $column_quantity; ?></td>
                <td class="price"><?php echo $column_price; ?></td>
                <td class="total"><?php echo $column_total; ?></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product) { ?>
            <tr>
                <td class="name"><strong class="pr_name"><?php echo $product['name']; ?></strong>
                    <?php foreach ($product['option'] as $option) { ?>
                    <br />
                    &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                    <?php } ?>
                     <?php foreach ($product['toppings'] as $topping) { ?>
                     -<small> <?php echo $topping['quantity']." X ".$topping['name'].":".$topping['topping_value'];?></small><br />
                <?php } ?>
                <?php foreach ($product['dressings'] as $dressing) { ?>
                     -<small><?php echo $dressing['name'].":".$dressing['dressing_value'];?></small><br />
                <?php } ?>
                      <?php foreach ($product['bundle'] as $bundle) { ?>
                <br /><b><small><?php 
                  echo $bundle['name'].":";
                 ?> </small></b><br />
                 <?php foreach ($bundle['option'] as $bundleoption) { ?>
              -- <small><?php 
                  echo $bundleoption['name'].":";
                 
                  ?> <?php echo $bundleoption['value']; ?></small><br />
              <?php } ?>
              <?php foreach ($bundle['toppings'] as $bundtopping) { ?>
                     -<small> <?php echo $bundtopping['quantity']." X ".$bundtopping['name'].":".$bundtopping['topping_value'];?></small><br />
                <?php } ?>
                <?php foreach ($bundle['dressings'] as $bundressing) { ?>
                     -<small><?php echo $bundressing['name'].":".$bundressing['dressing_value'];?></small><br />
                <?php } ?>
              <?php } ?>
                </td>
                <td class="quantity"><?php echo $product['quantity']; ?></td>
                <td class="price"><?php echo $product['price']; ?></td>
                <td class="total"><?php echo $product['total']; ?></td>
            </tr>
            <?php } ?>
            <?php foreach ($vouchers as $voucher) { ?>
            <tr>
                <td class="name"><?php echo $voucher['description']; ?></td>
                <td class="quantity">1</td>
                <td class="price"><?php echo $voucher['amount']; ?></td>
                <td class="total"><?php echo $voucher['amount']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <?php foreach ($totals as $total) {
             if($total['text']!=''){
             ?>
            <tr>
                <td colspan="3" class="price"><b><?php echo $total['title']; ?>:</b></td>
                <td class="total"><?php echo $total['text']; ?></td>
            </tr>
            
            <?php
            }
            } ?>
        </tfoot>
    </table>
</div>

<?php if($tips_status){ ?>
<div id="tips-content">Enter a fixed tip amount : <input type="text" name="tips"  id="tips"  value="<?php // echo $tips_value; ?>" size="10" style="width: 39% !important; display: inline-block !important" />&nbsp;
<input  style="display: inline-block !important" type="button" name="tips_button"  id="tips_button" value="Add" class="button" onclick="tipcustomfunction()"/><br><br>

<div>
    Fixed Tip : 
    <a onclick="tipfunction(1)" class="tips button" 
    style="border-radius: 7px 7px 7px 7px; -webkit-border-radius: 7px 7px 7px 7px;-moz-border-radius: 7px 7px 7px 7px;
    -khtml-border-radius: 7px 7px 7px 7px;border:none !important;    padding: 8px 10px;"> <strong>£1</strong></a>
    <a onclick="tipfunction(2)" class="tips button" 
    style="border-radius: 7px 7px 7px 7px; -webkit-border-radius: 7px 7px 7px 7px;-moz-border-radius: 7px 7px 7px 7px;
    -khtml-border-radius: 7px 7px 7px 7px;border:none !important;    padding: 8px 10px;"> <strong>£2</strong></a>
    <a onclick="tipfunction(5)" class="tips button" 
    style="border-radius: 7px 7px 7px 7px; -webkit-border-radius: 7px 7px 7px 7px;-moz-border-radius: 7px 7px 7px 7px;
    -khtml-border-radius: 7px 7px 7px 7px;border:none !important;    padding: 8px 10px;"> <strong>£5</strong></a>
    <a onclick="tipfunction(10)" class="tips button" 
    style="border-radius: 7px 7px 7px 7px; -webkit-border-radius: 7px 7px 7px 7px;-moz-border-radius: 7px 7px 7px 7px;
    -khtml-border-radius: 7px 7px 7px 7px;border:none !important;    padding: 8px 10px;"> <strong>£10</strong></a>

</div>
<?php if($tips_percentages) { 
    echo ' Or Choose :';
    foreach($tips_percentages as $tips_percentage) { ?>
    <a onclick="tipfunction(<?php echo $tips_percentage['value']; ?>)" class="tips button" 
    style="border-radius: 7px 7px 7px 7px; -webkit-border-radius: 7px 7px 7px 7px;-moz-border-radius: 7px 7px 7px 7px;
    -khtml-border-radius: 7px 7px 7px 7px;border:none !important;    padding: 8px 10px;"> <strong><?php echo $tips_percentage['text']; ?></strong> <small>(<?php echo $tips_percentage['percentage']; ?>%)</small> </a>
<?php    }
}
?><!-- <input  style="display: inline-block !important" type="button" name="tips_button"  id="tips_button" value="Tip Cash" class="button" onclick="tipfunction(0)"/>-->
<br>
<h4 style="background:rgba(20,193,0,1.00);color:#fff;text-align:center;border-radius:2px;padding:2px 5px;">100% of the tips received are passed on to our staff</h4>
</div>
<?php } ?>
<div class="payment"></div>
<script>
$(document).ready(function(){
    $('.finalpricediv').html('<?php echo $finalpricetext; ?>');
    $('#button-order-confirm').val('<?php echo $button_continue; ?>');
});
</script>