<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
        <h3 class="modal-title"><?php echo $product_name; ?></h3>
      </div>
<div class="modal-body popoptionmodel">
<div class="panel-group" id="productaccordion_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>" role="tablist" aria-multiselectable="true">
    <input type="hidden" name="main_product_id" value="<?php echo $main_product_id; ?>">
                                <input type="hidden" name="bundle_id" value="<?php echo $bundle_id;?>">
                                <input type="hidden" name="bundleidval" value="<?php echo $bundledidval;?>">
                                <input type="hidden" name="product_id" value="<?php echo $product_id;?>">
    <div class="panel panel-default optionpanel prodtpanels">
        <?php if(!empty($toppings) || !empty($dressings)){ ?>
            <div class="panel-heading" role="tab" id="headingoptOne_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>" data-toggle="collapse" data-parent="#productaccordion_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>" href="#optioncollapse_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>" aria-expanded="true" aria-controls="optioncollapse_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>">
                <div class="col-xs-1 optionicon">
                    <?php if(!empty($required_options)){ ?>
                      <span class="glyphicon glyphicon-question-sign"></span>
                    <?php } ?>
                </div>
                <div class="col-xs-8">
                  Options
                </div>
                 <span class="glyphicon glyphicon-chevron-down"></span>
            </div>
        <?php } ?>
        <div id="optioncollapse_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingoptOne_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>">
            <div class="panel-body deal_options">
                <?php foreach($options as $key=>$product_options) { ?>
                <div class="other_options pr_options_product-det_<?php echo $bundledidval; ?>">
                    <?php if ($product_options['type'] == 'select') { ?>
                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option" <?php echo 'master-option="' . $product_options['master_option'] . '"' . ' option="' . $product_options['option_id'] . '"'; ?>>

                        <div class="prod_extra_option_name col-sm-6"><?php echo $product_options['name']; ?> :
                        <?php if ($product_options['required']) { ?>
                        <span class="required">*</span>
                        <?php } ?>
                        </div>
                        <div class="prod_extra_option_det col-sm-6">
                            <select name="option[<?php echo $product_options['product_option_id']; ?>]" class="form-control selectpicker" id="input-option<?php echo $product_options['product_option_id']; ?>">
                            <?php //if (!$product_options['required']) { ?>  
                            <option value="">Please Select</option>
                            <?php //} ?>
                            <?php foreach ($product_options['option_value'] as $option_value) { ?>
                            <option proptionid="<?php echo $product_options['product_option_id']; ?>"  value="<?php echo $option_value['product_option_value_id']; ?>" <?php echo 'master-option-value="' . $option_value['master_option_value'] . '"' . ' option-value="' . $option_value['option_value_id'] . '"'; ?>><?php echo $option_value['name']; ?>
                                <?php if (isset($option_value['price']) && $option_value['price']) { ?>
                                (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                <?php } ?>
                            </option>
                            <?php } ?>
                        </select>
                        </div>
                    </div>

                    <?php } ?>
                    <?php if ($product_options['type'] == 'radio') { ?>
                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option" <?php echo 'master-option="' . $product_options['master_option'] . '"' . ' option="' . $product_options['option_id'] . '"'; ?>>

                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?> :</b>
                         <?php if ($product_options['required']) { ?>
                        <span class="required">*</span>
                        <?php } ?>
                        </div>

                        <div class="prod_extra_option_det" id="input-option<?php echo $product_options['product_option_id']; ?>">
                       <?php foreach ($product_options['option_value'] as $option_value) { ?>
                            <div class="checkbox col-sm-6">
                                <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>">
                                    <input proptionid="<?php echo $product_options['product_option_id']; ?>" type="radio" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" <?php echo 'master-option-value="' . $option_value['master_option_value'] . '"' . ' option-value="' . $option_value['option_value_id'] . '"'; ?> />
                                           <?php echo $option_value['name']; ?>
                                    <?php if ($option_value['price']) { ?>
                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                    <?php } ?>
                                    <span class="checkmarker"></span>
                                </label>
                            </div>
                            
                        
                        <?php } ?>
                        </div>
                    </div>
                    
                    <?php } ?>
                    <?php if ($product_options['type'] == 'checkbox') { ?>
                        <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option" <?php echo 'master-option="' . $product_options['master_option'] . '"' . ' option="' . $product_options['option_id'] . '"'; ?>>                            
                            <div class="prod_extra_option_name">
                                <label class="control-label <?php echo ($product_options['required'])?'optshow':''; ?>" divid="prodtoption_<?php echo $bundledidval; ?>_<?php echo $product_options['product_option_id']; ?>">
                                    <b><?php echo $product_options['name']; ?>:</b>
                                         <?php if ($product_options['required']) { ?>
                                    <span class="required">*</span>
                                    <?php } ?>
                                </label>
                            </div>
                           <div  class="prod_extra_option_det option-opened extraoption_prodtoption_<?php echo $bundledidval; ?>_<?php echo $product_options['product_option_id']; ?>" id="input-option<?php echo $product_options['product_option_id']; ?>">
                            <!--<ul>-->
                            <?php foreach ($product_options['option_value'] as $option_value) { ?>
                            <div class="checkbox col-sm-6"> <!--<li>-->                           
                            <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                <input proptionid="<?php echo $product_options['product_option_id']; ?>" type="checkbox" onclick="optionMaximum('<?php echo $product_options['maximum']; ?>','option-<?php echo $product_options['product_option_id']; ?>');" name="option[<?php echo $product_options['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" <?php echo ($product_options['default']==1) ? 'checked':''  ?>  id="option-value-<?php echo $option_value['product_option_value_id']; ?>" <?php echo 'master-option-value="' . $option_value['master_option_value'] . '"' . ' option-value="' . $option_value['option_value_id'] . '"'; ?>/>
                                    <?php if (isset($option_value['price']) && $option_value['price']) { ?>
                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                    <?php } ?>
                                    <span class="checkboxmarker"></span>
                            </label>
                            </div><!--</li>-->
                            <?php } ?>
                            <!--</ul>-->
                           </div> 
                        </div>
                    
                    <?php } ?>

                    <?php if ($product_options['type'] == 'image') { ?>
                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                <?php if ($product_options['required']) { ?>
                        <span class="required">*</span>
                        <?php } ?>
                                :</b>

                            </div>
                        <div class="prod_extra_option_det">
                        <table class="option-image">
                            <?php foreach ($product_options['option_value'] as $option_value) { ?>
                            <tr>
                                <td style="width: 1px;"><input type="radio" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
                                <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
                                <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                        <?php if ($option_value['price']) { ?>
                                        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                        <?php } ?>
                                    </label></td>
                            </tr>
                            <?php } ?>
                        </table>
                        </div>    
                    </div>
                    
                    <?php } ?>
                    <?php if ($product_options['type'] == 'text') { ?>
                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                 <?php if ($product_options['required']) { ?>
                        <span class="required">*</span>
                        <?php } ?>

                                :</b>

                            </div>
                        <div class="prod_extra_option_det" id="input-option<?php echo $product_options['product_option_id']; ?>">
                            <input class="form-control" type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" />
                        </div>
                    </div>
                    
                    <?php } ?>
                    <?php if ($product_options['type'] == 'textarea') { ?>
                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option" <?php echo 'master-option="' . $product_options['master_option'] . '"' . ' option="' . $product_options['option_id'] . '"'; ?>>

                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                 <?php if ($product_options['required']) { ?>
                        <span class="required">*</span>
                        <?php } ?>
                                :</b>

                            </div>
                       <div class="prod_extra_option_det" id="input-option<?php echo $product_options['product_option_id']; ?>"> 
                           <textarea class="col-xs-12 form-control" name="option[<?php echo $product_options['product_option_id']; ?>]" cols="40" rows="5"><?php echo $product_options['option_value']; ?></textarea>
                        </div>
                    </div>
                    
                    <?php } ?>
                    <?php if ($product_options['type'] == 'file') { ?>
                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                <?php if ($product_options['required']) { ?>
                        <span class="required">*</span>
                        <?php } ?>:</b></div>
                        <div class="prod_extra_option_det"><input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $product_options['product_option_id']; ?>" class="button">
                        <input type="hidden" name="product_options[<?php echo $product_options['product_option_id']; ?>]" value="" />
                    </div></div>
                    
                    <?php } ?>
                    <?php if ($product_options['type'] == 'date') { ?>
                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                <?php if ($product_options['required']) { ?>
                        <span class="required">*</span>
                        <?php } ?>
                                :</b></div>
                        <div class="prod_extra_option_det"><input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="date" />
                    </div> </div>
                    
                    <?php } ?>
                    <?php if ($product_options['type'] == 'datetime') { ?>
                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                 <?php if ($product_options['required']) { ?>
                        <span class="required">*</span>
                        <?php } ?>

                                :</b></div>
                       <div class="prod_extra_option_det"> <input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="datetime" />
                    </div> </div>
                    
                    <?php } ?>
                    <?php if ($product_options['type'] == 'time') { ?>
                    <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                        <div class="prod_extra_option_name"><b><?php echo $product_options['name']; ?>
                                 <?php if ($product_options['required']) { ?>
                        <span class="required">*</span>
                        <?php } ?>

                                :</b></div>
                       <div class="prod_extra_option_det"> <input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="time" />
                    </div></div>
                    
                    <?php } ?>


                </div> 
            <?php } ?>
            </div>
        </div>
    </div>
    <!--TOPPINGFS-->
    <?php if(!empty($toppings)) { ?>
        <?php $topping_count=0; ?>    
        <?php foreach ($toppings as $topping) { ?>
            <div class="panel panel-default prodtpanels" id="prodttopping_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id']; ?>">
                <div class="panel-heading col-xs-12" role="tab" id="prodttophead_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id']; ?>" data-toggle="collapse" data-parent="#productaccordion_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>" href="#toppingcollapse_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id']; ?>" aria-expanded="true" aria-controls="toppingcollapse_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id']; ?>">
                    <div class="col-xs-1">
                        <span style="display: none" class="toppingin glyphicon glyphicon-ok"></span>
                    </div>
                    <div class="col-xs-8">
                        <?php echo $topping['name']; ?>
                    </div>
                     <span class="glyphicon glyphicon-chevron-down"></span>
                </div>
                <div id="toppingcollapse_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id']; ?>" class="panel-collapse collapse <?php echo (empty($options) && $topping_count==0)?'in':''; ?> " role="tabpanel" aria-labelledby="prodttophead_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id']; ?>">
                    <div class="panel-body">
                        <?php foreach ($topping['topping_values'] as $topping_value) { ?>
                            <div class="row toppingavalues" id="topping_vale_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>">
                                <div class="col-xs-5">
                                    <?php echo $topping_value['name']; ?>
                                </div>
                                <div class="col-xs-3">
                                    <span class="toppngtotal">
                                        <span class="price"><?php echo $this->currency->format($topping_value['price']);?></span>
                                    </span>
                                </div>
                                <div class="col-xs-1">
                                    <div class="toppngminus" style="display: none">  
                                        <a class="button" onclick="changeToppingqty('<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>','-','<?php echo $topping_value['price'];?>','<?php  echo $bundled_condition['maximum_toppings']; ?>','productaccordion_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>','prodttopping_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id']; ?>');">-</a>                                                    
                                    </div>  
                                </div>
                                <div class="col-xs-1">
                                    <span style="display: none" class="qty">0</span>                                                
                                </div>
                                <div class="col-xs-1">
                                    <a class="button toppingplus" onclick="changeToppingqty('<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>','+','<?php echo $topping_value['price'];?>','<?php  echo $bundled_condition['maximum_toppings']; ?>','productaccordion_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>','prodttopping_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id']; ?>');">+</a>
                                    <input type="hidden" name="topping[<?php echo $topping['topping_id']; ?>][]" value="<?php echo $topping_value['topping_value_id']; ?>" id="option-value-<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>"  />
                                    <input type="hidden" class="topping_qty" price="<?php echo $topping_value['price']; ?>" name="topping_qtys[<?php echo $topping['topping_id']; ?>][<?php echo $topping_value['topping_value_id']; ?>][quantity]" value="0" />    
                                </div>                                            
                            </div>
                        <?php } ?>
                    </div>
                </div> 
            </div>
            <?php $topping_count++;  ?>
         <?php } ?>
    <?php } ?>
    <!--DRESSINGS-->
    <?php if(!empty($dressings)) { ?>
        <?php  foreach ($dressings as $dressing) { ?>
            <div class="panel panel-default prodtpanels" id="prodttdressing_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $dressing['dressing_id']; ?>">
                <div class="panel-heading" role="tab" id="prodtdresshead_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $dressing['dressing_id']; ?>" data-toggle="collapse" data-parent="#productaccordion_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>" href="#dressingcollapse_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $dressing['dressing_id']; ?>" aria-expanded="true" aria-controls="dressingcollapse_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $dressing['dressing_id']; ?>">
                    <div class="col-xs-1"><span style="display: none" class="dressingin glyphicon glyphicon-ok"></span></div>
                    <div class="col-xs-8"><?php echo $dressing['name']; ?> </div>
                    <span class="glyphicon glyphicon-chevron-down"></span>
                </div>
                <div id="dressingcollapse_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $dressing['dressing_id']; ?>" class="panel-collapse collapse <?php echo (empty($options) && $topping_count==0)?'in':''; ?> " role="tabpanel" aria-labelledby="prodtdresshead_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $dressing['dressing_id']; ?>">
                    <div class="panel-body">
                        <?php foreach ($dressing['dressing_values'] as $dressing_value) { ?>
                            <div id="option-<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $dressing['dressing_id']; ?>" class="checkbox options_dressings col-sm-6">
                                <label class="checkbox-inline checkbox-dressings">
                                    <input type="checkbox" onclick="changeDressingqty('<?php  echo $bundled_condition['maximum_dressings']; ?>','productaccordion_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>','prodttdressing_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>_<?php echo $dressing['dressing_id']; ?>');" name="dressing[<?php echo $dressing['dressing_id']; ?>][]" value="<?php echo $dressing_value['dressing_value_id']; ?>" /><?php echo $dressing_value['name']; ?>
                                </label>
                            </div>
                        <?php }  ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <div class="complt"></div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" style="float: left">Close</button>
    <a  onclick="bundleprodttocart('<?php echo $main_product_id; ?>','<?php echo $bundledidval ;?>');" class="popupcartbtn button_cart button">ADD</a>
</div>