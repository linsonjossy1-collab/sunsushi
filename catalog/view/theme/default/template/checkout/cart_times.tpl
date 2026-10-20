<div class="checkout-product" id="cart-produc-times">
    <table>
        <thead>
            <tr>
                <td class="name"><?php echo $column_name; ?></td>
                <td class="model"><?php echo "Available Times"; ?></td>
                <td class="total"></td>
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
                      <?php foreach ($product['bundle'] as $bundle) { ?>
                <br /><b><small><?php 
                  echo $bundle['name'].":";
                 ?> </small></b><br />
                 <?php foreach ($bundle['option'] as $bundleoption) { ?>
              -- <small><?php 
                  echo $bundleoption['name'].":";
                 
                  ?> <?php echo $bundleoption['value']; ?></small><br />
              <?php } ?>
              <?php } ?>
                </td>
                <td>
                <?php
                    if(!empty($product['available_times'])){ 
                        foreach($product['available_times'] as $available_time){
                            echo date('h:i a',strtotime(trim($available_time['start']))).' - '.date('h:i a',strtotime(trim($available_time['end']))).'<br>';
                        }
                    }else{
                        echo $product['time_description'];
                    }
                ?>
                </td>
                <td class="total">
                    <?php if(!empty($product['available_times'])){ ?>
                    <a href="<?php echo $product['remove']; ?>"><img src="catalog/view/theme/default/image/remove.png" alt="Remove" title="Remove" /></a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
      
    </table>
</div>
<div class="payment"></div>
