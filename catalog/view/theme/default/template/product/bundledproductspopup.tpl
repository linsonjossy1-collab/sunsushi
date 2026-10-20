<div class="modal-header">
                <h3 class="modal-title "><?php echo $product_name; ?></h3>
            </div>
            <div class="loadermenu"></div>
            <div class="modal-body popoptionmodel">
      <div class="product_image_inner" style="background:url(<?php echo $thumb; ?>) no-repeat center;"></div>
                    <div class="product_description">
                    <?php if($description!='..') { echo $description; }  ?> 
                </div>
                <div id="bundled_product_details_list_<?php echo $main_product_id; ?>">
                    <div class="bundle_added_items">
                        <ul class="list-group"></ul>
                    </div>
                    <div class="bundle_product_list" id="bundled_product_detail_div_<?php echo $main_product_id; ?>">
                        <!--New Code---->
                        <?php if(!empty($product_bundle)) { 
                            $bundledidval = 1;
                            $initemqty=0;
                            $iteminstatus =(!empty($added_bundle))?true:false; 

                            //$added_bundle
                        ?>
                            <?php foreach($product_bundle as $k=>$bundle) { ?>
                                    <?php $initemqty +=(int)$bundle['bundled_qty'] ?>
                                    <?php for($i=0;$i<$bundle['bundled_qty'];$i++) { ?>
                                        
                                    <div id="bundleitem_<?php echo $main_product_id; ?>_<?php echo $bundledidval; ?>" class="deal_items" style="display: <?php echo ($bundledidval==1)?'':'none'; ?>">
                                            
                                            <div class="deal_head"><!--For Deal Items-->
                                                <label class="col-sm-4">
                                                    <?php if($bundle['bundled_qty']==1) { ?>
                                                        <div class="bundle_name"><?php echo $bundle['bundle_name'];?></div>
                                                    <?php } else { ?>
                                                        <div class="bundle_name"><?php echo $bundle['bundle_name'].' '.($i+1);?></div>
                                                    <?php } ?>  
                                                </label>
                                                <div class="col-sm-8">
                                                    <?php $options=array();$toppings=array();$dressings = array();$bundled_condition=array(); ?>
                                                    <select class="form-control deal_product_select selectpicker" bundleidval="<?php echo $bundledidval; ?>" main_id="<?php echo $main_product_id; ?>" id="div_<?php echo $main_product_id; ?>_<?php echo $bundledidval;?>" keyid="<?php echo $bundle['bundled_id'].'_'.$i;?>" name="product_id">
                                                        <?php foreach($bundle['products'] as $key=>$product){ ?>
                                                            <?php if($fkey==0){ 
                                                                $options=$product['options'];
                                                                $toppings=$product['toppings'];
                                                                $dressings=$product['dressings'];
                                                                $bundled_condition=$product['bundled_condition'];
                                                            } ?>
                                                            <option value="<?php echo $product['product_id'];?>"><?php echo $product['name'];?>
                                                            <?php if(isset($product['extra_deal_price']) && $product['extra_deal_price']!=''){ ?>                                                    
                                                                (<?php echo $product['extra_deal_price'];?>)
                                                            <?php } ?>      
                                                        </option>
                                                            <?php $fkey +=1; ?>    
                                                        <?php } ?>  
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="bundled_prodtoptions_div_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>"  class="bundled_prodt_options" ><!--For Deal Item Options -->
                                                <div id="bundled_prodt_options_div_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>">
                                                    <div class="panel-group" id="productaccordion_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?>" role="tablist" aria-multiselectable="true">
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
                                                                    <div class="other_options pr_options_product-det_<?php echo $bundledidval; ?>" >
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
                                                                                
                                                                                <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                                                                <div class="checkbox col-sm-6">                           
                                                                                <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                                                    <input proptionid="<?php echo $product_options['product_option_id']; ?>" type="checkbox" onclick="optionMaximum('<?php echo $product_options['maximum']; ?>','optioncollapse_<?php echo $main_product_id; ?>_<?php echo $bundledidval ;?> #input-option<?php echo $product_options['product_option_id']; ?>');" name="option[<?php echo $product_options['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" <?php echo ($product_options['default']==1) ? 'checked':''  ?>  id="option-value-<?php echo $option_value['product_option_value_id']; ?>" <?php echo 'master-option-value="' . $option_value['master_option_value'] . '"' . ' option-value="' . $option_value['option_value_id'] . '"'; ?>/>
                                                                                        <?php if (isset($option_value['price']) && $option_value['price']) { ?>
                                                                                        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                                                                        <?php } ?>
                                                                                        <span class="checkboxmarker"></span>
                                                                                </label>
                                                                                </div>
                                                                                <?php } ?>
                                                                                
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
                                                    </div>
                                                    
                                                </div>
                                                <input type="hidden" name="main_product_id" value="<?php echo $main_product_id; ?>">
                                                <input type="hidden" name="bundle_id" value="<?php echo $bundle['bundled_id'];?>">
                                                <input type="hidden" name="bundleidval" value="<?php echo $bundledidval;?>">
                                                
                                            </div>
                                            
                                        </div>
                                        
                                        <?php $bundledidval +=1;?>
                                    <?php } ?>  
                            <?php } ?>  
                        <?php } ?>  
                        <!--New Code---->         
                    </div>
                </div>
            </div>
            <div class="modal-footer dlefooter">
                <!--
                                                <?php if($bundledidval==1){ ?>
                                                    <button type="button" style="float: left" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <?php }else{ ?>
                                                    <button type="button" style="float: left" class="btn btn-default" onclick="cancelDeal('<?php echo $main_product_id; ?>')">Close</button>
                                                      <?php } ?>-->
                                                      <button type="button" style="float: left" class="btn btn-default" onclick="closebundlepopup('<?php echo $main_product_id; ?>')">Close</button>
                                                 <a class="btn btn-primary popupcartbtn" id='btnbndlecart' onclick="bundleItemtocart('<?php echo $main_product_id; ?>','<?php echo $bundledidval; ?>')"><?php echo $button_cart; ?></a>
                                                <!--<?php if($total_bundled_qty==$bundledidval){ ?>
                                                    <input type="hidden" name="dealcomplete" value="<?php echo $main_product_id; ?>">
                                                    <a class="btn btn-primary popupcartbtn" id='btn<?php echo $main_product_id."_".$bundledidval; ?>' onclick="bundleItemtocart('<?php echo $main_product_id; ?>','<?php echo $bundledidval; ?>','all')"><?php echo $button_cart; ?></a>
                                                <?php }else{ ?>
                                                    <a class="btn btn-primary popupcartbtn" id='btn<?php echo $main_product_id."_".$bundledidval; ?>' onclick="bundleItemtocart('<?php echo $main_product_id; ?>','<?php echo $bundledidval; ?>')"><?php echo $button_cart; ?></a>
                                                <?php } ?> -->                                                                         
                                        </div>
            <script>
                var totbundledty='<?php echo $total_bundled_qty; ?>';
                var addedbundqty=0;
                var bundledidval=1;
                $(document).ready(function(){

                    $('.bundle_product_list .other_options .option[master-option!="0"]').hide();


                    $('.selectpicker').selectpicker();
                    $('.deal_product_select').on('change',function(){
                        $('#bundled_prodt_options_'+this.id).html(''); 
                        // $(this).after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
                        $('.loadermenu').show();
                        $('#bundled_prodt_options_'+this.id).load("index.php?route=product/product/ajaxbundledproductoptions",{ bundled_id: $(this).attr('keyid')+'_'+$(this).val(),bundleidval:$(this).attr('bundleidval'), main_product_id:$(this).attr('main_id') }, function(response, statusTxt, xhr) {
                            if(statusTxt=="success"){
                                //$('.wait').remove();
                                $('.loadermenu').hide();
                                $('#bundled_prodtoptions_'+this.id).show();
                                $('#bundled_prodt_options_'+this.id).show();
                                //$('#bundled_prodtoptions_div_'+this.id+'.bundled_prodt_options').css('overflow-y', 'auto'); 
                                //$('#bundled_prodtoptions_div_'+this.id+'.bundled_prodt_options').css('max-height', ($(window).height() * 0.8-150));
                                
                                //$('#bundled_prodtoptions_div_'+this.id+' .other_options').find('.option[master-option!="0"]').hide();
                                //$('#bundled_product_details_list_<?php echo $main_product_id; ?>').find('#bundled_prodtoptions_div_'+this.id+' .option').hide();
                                
                                $('#'+this.id).find('.option[master-option!=0]').hide();

                                //console.log(""+this.id +": "+$('#'+this.id).find('.option[master-option!=0]').length);

                                $('.selectpicker').selectpicker('refresh');
                            }
                        });
                    });
                    $('.bundle_product_list').on('change','.other_options .selectpicker',function() {
                        var master = $(this).parents('.option').attr('option');
                        var $parentdiv = $(this).parents('.deal_options').find('.option[master-option="'+master+'"]');
                        if($(this).val()) {
                            var seloption = $(this).find('option:selected').attr('option-value');
                        } else {
                            var seloption = 0;
                        }
                        //alert('tt');
                        var option_value = [seloption];
                        suboption(master,seloption,$parentdiv,option_value);
                    });
                });
                function suboption(master,seloption,$related_options,option_value) {
                      // select options
                $related_options.find('.popupoptions option[master-option-value!=0]').each(function(){
                        if ($(this).parent('span').length == 0) {
                                $(this).wrap("<span>");
                        }
                });
                $related_options.find('input[master-option-value!=0]').hide();
                $related_options.find('input[master-option-value!=0]').parent('label').parent('div').hide();
                $related_options.find('input[master-option-value!=0]').next('label').next('br').hide();
                $related_options.find('textarea[master-option-value!=0]').hide();
                $related_options.find('option[master-option-value!=0]').css("display","none");//attr('disabled','disabled');
                $related_options.find('option[master-option-value!=0]').attr('disabled','disabled');
                // image options

                if(option_value==''){
                    option_value=[];
                }
                if (typeof(option_value) == "string") {
                        option_value = [option_value];
                }
                for (var i in option_value) {
                        $related_options.find('option[master-option-value=' + option_value[i] + '], option[value=\'\']').each(function(){
                                if ($(this).parent('span').length != 0) {
                                        $(this).unwrap();
                                }
                        });
                        $related_options.find('input[master-option-value=' + option_value[i] + ']').show();
                        $related_options.find('option[master-option-value=' + option_value[i] + ']').css("display","block");//removeAttr('disabled');
                        $related_options.find('option[master-option-value=' + option_value[i] + ']').removeAttr('disabled');
                        $related_options.find('input[master-option-value=' + option_value[i] + ']').parent('label').parent('div').show();
                        $related_options.find('input[master-option-value=' + option_value[i] + ']').next('label').next('br').show();
                        $related_options.find('textarea[master-option-value=' + option_value[i] + ']').show();
                }

                $related_options.each(function(){
                        var visible_options = 0;
                        for (var i in option_value) {
                                visible_options += $(this).find('[master-option-value=' + option_value[i] + ']').length*1;
                                visible_options += $(this).find('[master-option-value=0]').length*1;
                        }
                        if ($(this).find('option, input, textarea').length != 0 && visible_options == 0) {
                                $(this).slideUp();
                        } else {
                                $(this).slideDown();
                        }
                });
                $('.selectpicker').selectpicker('refresh');
                    /*
                    if(seloption) {
                        console.log(master+'ddeefffd'); 
                        console.log(seloption);
                        console.log($parentdiv);
                        $parentdiv.show();
                        $parentdiv.find('option[master-option-value="0"]').show();
                        $parentdiv.find('option[master-option-value!="0"]').hide();
                        $parentdiv.find('option[master-option-value="'+seloption+'"]').show(); 


                        $parentdiv.each(function(){
                                var visible_options = 0;
                                for (var i in option_value) {
                                        visible_options += $(this).find('[master-option-value="' + option_value[i] + '"]').length*1;
                                        visible_options += $(this).find('[master-option-value="0"]').length*1;
                                }
                                if ($(this).find('option, input, textarea').length != 0 && visible_options == 0) {
                                        $(this).slideUp();
                                } else {
                                        $(this).slideDown();
                                }
                        });

                    } else {
                        $parentdiv.hide();
                    }  

                    $parentdiv.find('select, textarea').val('');
                    $parentdiv.find('input[type=radio], input[type=checkbox]').removeAttr('checked');

                    $('.selectpicker').selectpicker('refresh');
                    */
                }
                function bundleItemtocart(main_id,bundle_id,all)
                {
                    //$('.bundled_prodt_options').hide();  
                    if($('#btnbndlecart').hasClass('disabled')) {
                        return;
                    }
                    $('#btnbndlecart').addClass('disabled');
                    $('#bundled_product_detail_div_'+main_id+' .error').remove();    
                    if(addedbundqty==(totbundledty-1)){
                        $( "#bundled_prodtoptions_div_"+main_id+'_' + bundledidval).after('<input type="hidden" name="dealcomplete" value="<?php echo $main_product_id; ?>">');
                        //$('.popupcartbtn')
                    }
                    $.ajax({
                        url: 'index.php?route=checkout/cart/add',
                        type: 'post',
                        data: $('#bundleitem_'+main_id+'_' + bundledidval + ' input[type=\'text\'], #bundleitem_' + main_id+'_' +bundledidval + ' input[type=\'hidden\'], #bundleitem_' +main_id+'_' + bundledidval + ' input[type=\'radio\']:checked, #bundleitem_' +main_id+'_' + bundledidval + ' input[type=\'checkbox\']:checked, #bundleitem_' +main_id+'_' + bundledidval +  '  select, #bundleitem_' +main_id+'_' + bundledidval +  ' textarea'),
                        //data: $('#bundled_prodt_'+main_id+ ' input[type=\'text\'], #bundled_prodt_' + main_id+ ' input[type=\'hidden\'], #bundled_prodt_' +main_id+ ' input[type=\'radio\']:checked, #bundled_prodt_' +main_id+' input[type=\'checkbox\']:checked, #bundled_prodt_' +main_id+  '  select, #bundled_prodt_' +main_id+ ' textarea'),
                        dataType: 'json'
                    }).done(function (json) {
                        if (json['error']) {
                            if (json['error']['option']) {
                                for (i in json['error']['option']) {
                                        $('#bundleitem_'+main_id+'_' +bundledidval +'  #option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                                        $('#bundleitem_'+main_id+'_' +bundledidval+  '  #optioncus-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');                             
                                }
                                
                                $('#bundleitem_'+main_id+'_' +bundledidval+  ' .panel-heading').removeClass('collapsed');
                                $('#bundleitem_'+main_id+'_' +bundledidval+  ' .collapse').removeClass('in');
                                $('#bundleitem_'+main_id+'_' +bundledidval+  ' .panel-heading').addClass('collapsed');
                                $('#bundleitem_'+main_id+'_' +bundledidval+  ' .optionpanel .panel-heading').removeClass('collapsed');
                                $('#bundleitem_'+main_id+'_' +bundledidval+  ' .optionpanel .collapse').addClass('in');
                                $('#bundleitem_'+main_id+'_' +bundledidval+  ' .optionpanel .collapse.in').css('min-height',250);                        
                            }
                            if(json['error']['warning']!=undefined) {
                                $('#bundleitem_'+main_id+'_' +bundledidval).append('<div class="error alert alert-danger">'+json['error']['warning']+'</div>');
                                $('.dlefooter').html('<button type="button" style="float: left" class="btn btn-default" onclick="cancelDeal('+main_id+')">Cancel</button>');
                            }
                        }
                        if (json['success']) {
                            
                            selectedelem = $('#bundleitem_'+main_id+'_' + bundledidval +" .deal_product_select option:selected").text();
                            $('.bundle_added_items .list-group').append('<li class="alert alert-info" >'+selectedelem+'<span class="right glyphicon glyphicon-ok"></span><li>')
                            $('#bundleitem_'+main_id+'_' +bundledidval).hide();
                            $('#bundleitem_'+main_id+'_' +(parseInt(bundledidval)+1)).show();
                            $('#bundleitem_'+main_id+'_' +(parseInt(bundledidval)+1)+' .deal_product_select').change();
                            //All Add
                            bundledidval +=1;
                            addedbundqty +=1;
                            if(addedbundqty==totbundledty){
                                $('.modal').modal('hide');
                                $('.count').html(json['itemcount']);
                                $('.totalamt').html(json['totalamt']);
                                $('#notification').html('<div class="success"> You Have added <?php echo $product_name; ?> <img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                                $('.success').delay(1000).fadeOut();
                                $('#cart').load('index.php?route=module/cart #cart > *',function(){
                                    requestedtime_check();
                                });
                            }
                        }            
                        $('#btnbndlecart').removeClass('disabled');
                    }).fail(function (jqXHR, textStatus) 
                    {
                        $('#btn'+main_id+'_'+bundle_id).removeClass('disabled');
                    });
                }
                function closebundlepopup(main_id){
                    if(addedbundqty>0){
                        cancelDeal(main_id);
                    }else{
                        $('.modal').modal('hide');
                    }
                }
                function cancelDeal(main_id){
                    $.ajax({
                        url: 'index.php?route=checkout/cart/removebundle',
                        type: 'post',
                        data:{ main_product_id:main_id } ,
                        dataType: 'json',
                        success: function(json) {
                            if (json['success']) {
                                $('.modal').modal('hide');
                                $('#notification').html('<div class="success"> <?php echo $product_name; ?> is Removed from cart<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
                                 $('.success').delay(1000).fadeOut();
                                $('#cart').load('index.php?route=module/cart #cart > *',function(){

                                });                                     
                            }                          
                        }
                    });
                } 
            </script> 