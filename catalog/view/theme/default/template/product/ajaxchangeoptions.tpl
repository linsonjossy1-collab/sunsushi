 <div class="modal-header">
        
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
        <h3 class="modal-title"><?php echo $product_name; ?></h3>
      </div>
      <?php $incount=(!empty($options))?1:0; ?>
      <div class="modal-body popoptionmodel">
          <div class="panel-group product_custom_options" id="productaccordion_<?php echo $product_id; ?>" role="tablist" aria-multiselectable="true" style="margin:0px auto;">
                <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
                <input type="hidden" name="key" size="2" value="<?php echo $key; ?>" />
                 <input type="hidden" name="changeitem" size="2" value="1" />
     <div class="product_image_inner" style="background:url(<?php echo $thumb; ?>) no-repeat center;">
                </div>
              <input type="hidden" name="product_id" value="<?php echo $org_product_id; ?>">  
              <div class="panel panel-default optionpanel prodtpanels"> 
                    <!--<?php if(!empty($toppings) || !empty($dressings)){ ?>
                      <div class="panel-heading" role="tab" id="headingoptOne_<?php echo $product_id; ?>" data-toggle="collapse" data-parent="#productaccordion_<?php echo $product_id; ?>" href="#optioncollapse_<?php echo $product_id; ?>" aria-expanded="true" aria-controls="optioncollapse_<?php echo $product_id; ?>">
                          <div class="col-xs-11">
                                                          <?php if(!empty($required_options)){ ?>
                                <span class="glyphicon glyphicon-question-sign"></span>
                              <?php } ?> Options
                          </div>
                           <h4 class="glyphicon glyphicon-chevron-down"></h4>
                      </div>
                    <?php } ?>-->
                    <div id="optioncollapse_<?php echo $product_id; ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingoptOne_<?php echo $product_id; ?>">
                        <div class="panel-body popupoptions optionpanelcontent">
                            <?php   foreach($options as $key=>$product_options) {  ?>
                              <div class="other_options pr_options_product-det_<?php echo $product_id; ?>">
                                  <?php if ($product_options['type'] == 'select') { ?>
                                  <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option" <?php echo 'master-option="' . $product_options['master_option'] . '"' . ' option="' . $product_options['option_id'] . '"'; ?>>

                                      <div class="prod_extra_option_name col-sm-6">                                      
                                        <?php if ($product_options['required']) { ?>
                                          <span class="required">*</span>
                                        <?php } ?>
                                        <b><?php echo $product_options['name']; ?> :</b>
                                         
                                      </div>
                                      <div class="prod_extra_option_det col-sm-6">
                                          <select name="option[<?php echo $product_options['product_option_id']; ?>]" data-hide-disabled="true"  class="form-control selectpicker" id="input-option<?php echo $product_options['product_option_id']; ?>">
                                          <?php //if (!$product_options['required']) { ?>  
                                          <option value="">Please Select</option>
                                          <?php //} ?>
                                          <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                          <option proptionid="<?php echo $product_options['product_option_id']; ?>"  value="<?php echo $option_value['product_option_value_id']; ?>" <?php echo 'master-option-value="' . $option_value['master_option_value'] . '"' . ' option-value="' . $option_value['option_value_id'] . '"'; ?>><?php echo $option_value['name']; ?>
                                              <?php if ($option_value['option_price']) { ?>
                                              (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                              <?php } ?>
                                          </option>
                                          <?php } ?>
                                      </select>
                                      </div>
                                  </div>

                                  <?php } ?>
                                  <?php if ($product_options['type'] == 'radio') { ?>
                                  <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option" <?php echo 'master-option="' . $product_options['master_option'] . '"' . ' option="' . $product_options['option_id'] . '"'; ?>>

                                      <div class="prod_extra_option_name">
                                             <?php if ($product_options['required']) { ?>
                                                <span class="required">*</span>
                                            <?php } ?>
                                            <b><?php echo $product_options['name']; ?> :</b>
                                       
                                      </div>

                                      <div class="prod_extra_option_det" id="input-option<?php echo $product_options['product_option_id']; ?>">
                                     <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                        <div class="checkbox col-sm-6">
                                            <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>">
                                                <input  proptionid="<?php echo $product_options['product_option_id']; ?>" type="radio" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" <?php echo 'master-option-value="' . $option_value['master_option_value'] . '"' . ' option-value="' . $option_value['option_value_id'] . '"'; ?> <?php echo (isset($selected_options[$product_options['product_option_id']]) && in_array($option_value['product_option_value_id'],$selected_options[$product_options['product_option_id']]))?'checked':'' ?>/>
                                                       <?php echo $option_value['name']; ?>
                                                    <?php if ($option_value['option_price']) { ?>
                                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                                    <?php } ?>
                                                    <span class="checkmarker"></span>
                                            </label>
                                        </div>
                                        
                                   
                                      <?php } ?>
                                      </div>
                                  </div>
                                  <br />
                                  <?php } ?>
                                  <?php if ($product_options['type'] == 'checkbox') { ?>
                                      <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option" <?php echo 'master-option="' . $product_options['master_option'] . '"' . ' option="' . $product_options['option_id'] . '"'; ?>>                            
                                          <div class="prod_extra_option_name">
                                              <label class="control-label <?php echo ($product_options['required'])?'optshow':''; ?>" divid="prodtoption_<?php echo $product_id; ?>_<?php echo $product_options['product_option_id']; ?>">
                                                  <?php if ($product_options['required']) { ?>
                                                  <span class="required">*</span>
                                                  <?php } ?>
                                                  <b><?php echo $product_options['name']; ?>:</b>
                                                       
                                              </label>
                                          </div>
                                         <div  class="prod_extra_option_det option-opened extraoption_prodtoption_<?php echo $product_id; ?>_<?php echo $product_options['product_option_id']; ?>" id="input-option<?php echo $product_options['product_option_id']; ?>">
                                          <!--<ul>-->
                                          <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                          <div class="checkbox col-sm-6"> <!--<li>-->                           
                                          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                              <input proptionid="<?php echo $product_options['product_option_id']; ?>" type="checkbox" onclick="optionMaximum('<?php echo $product_options['maximum']; ?>','optioncollapse_<?php echo $product_id; ?> #input-option<?php echo $product_options['product_option_id']; ?>');" name="option[<?php echo $product_options['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" <?php echo ($product_options['default']==1) ? 'checked':''  ?>  id="option-value-<?php echo $option_value['product_option_value_id']; ?>" <?php echo 'master-option-value="' . $option_value['master_option_value'] . '"' . ' option-value="' . $option_value['option_value_id'] . '"'; ?> <?php echo (isset($selected_options[$product_options['product_option_id']]) && in_array($option_value['product_option_value_id'],$selected_options[$product_options['product_option_id']]))?'checked':'' ?>/>
                                                  <?php if ($option_value['option_price']) { ?>
                                                  (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                                  <?php } ?>
                                                  <span class="checkboxmarker"></span>
                                          </label>
                                          </div><!--</li>-->
                                          <?php } ?>
                                          <!--</ul>-->
                                         </div> 
                                      </div>
                                  <br />
                                  <?php } ?>

                                  <?php if ($product_options['type'] == 'image') { ?>
                                  <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                                      <div class="prod_extra_option_name">
                                              <?php if ($product_options['required']) { ?>
                                      <span class="required">*</span>
                                      <?php } ?>
                                              <b><?php echo $product_options['name']; ?>:</b>

                                          <br /></div>
                                      <div class="prod_extra_option_det">
                                      <table class="option-image">
                                          <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                          <tr>
                                              <td style="width: 1px;"><input type="radio" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
                                              <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
                                              <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                      <?php if ($option_value['option_price']) { ?>
                                                      (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                                      <?php } ?>
                                                  </label></td>
                                          </tr>
                                          <?php } ?>
                                      </table>
                                      </div>    
                                  </div>
                                  <br />
                                  <?php } ?>
                                  <?php if ($product_options['type'] == 'text') { ?>
                                  <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                                      <div class="prod_extra_option_name">
                                               <?php if ($product_options['required']) { ?>
                                      <span class="required">*</span>
                                      <?php } ?>

                                              <b><?php echo $product_options['name']; ?>:</b>

                                          <br /></div>
                                      <div class="prod_extra_option_det" id="input-option<?php echo $product_options['product_option_id']; ?>">
                                          <input class="form-control" type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" />
                                      </div>
                                  </div>
                                  <br />
                                  <?php } ?>
                                  <?php if ($product_options['type'] == 'textarea') { ?>
                                  <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option" <?php echo 'master-option="' . $product_options['master_option'] . '"' . ' option="' . $product_options['option_id'] . '"'; ?>>

                                      <div class="prod_extra_option_name">
                                               <?php if ($product_options['required']) { ?>
                                      <span class="required">*</span>
                                      <?php } ?>
                                              <b><?php echo $product_options['name']; ?>:</b>

                                          <br /></div>
                                     <div class="prod_extra_option_det" id="input-option<?php echo $product_options['product_option_id']; ?>"> 
                                         <textarea class="col-xs-12 form-control" name="option[<?php echo $product_options['product_option_id']; ?>]" cols="40" rows="5"><?php echo $product_options['option_value']; ?></textarea>
                                      </div>
                                  </div>
                                  <br />
                                  <?php } ?>
                                  <?php if ($product_options['type'] == 'file') { ?>
                                  <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                                      <div class="prod_extra_option_name">
                                        <?php if ($product_options['required']) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                      <b><?php echo $product_options['name']; ?>:</b><br />
                                      </div>
                                      <div class="prod_extra_option_det"><input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $product_options['product_option_id']; ?>" class="button">
                                      <input type="hidden" name="product_options[<?php echo $product_options['product_option_id']; ?>]" value="" />
                                  </div></div>
                                  <br />
                                  <?php } ?>
                                  <?php if ($product_options['type'] == 'date') { ?>
                                  <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                                      <div class="prod_extra_option_name">
                                              <?php if ($product_options['required']) { ?>
                                      <span class="required">*</span>
                                      <?php } ?>
                                             <b><?php echo $product_options['name']; ?> :</b><br />
                                      </div>
                                      <div class="prod_extra_option_det"><input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="date" />
                                  </div> </div>
                                  <br />
                                  <?php } ?>
                                  <?php if ($product_options['type'] == 'datetime') { ?>
                                  <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                                      <div class="prod_extra_option_name">
                                               <?php if ($product_options['required']) { ?>
                                      <span class="required">*</span>
                                      <?php } ?>

                                             <b><?php echo $product_options['name']; ?> :</b><br />
                                      </div>
                                     <div class="prod_extra_option_det"> <input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="datetime" />
                                  </div> </div>
                                  <br />
                                  <?php } ?>
                                  <?php if ($product_options['type'] == 'time') { ?>
                                  <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option">

                                        <div class="prod_extra_option_name">
                                            <?php if ($product_options['required']) { ?>
                                                <span class="required">*</span>
                                            <?php } ?>
                                            <b><?php echo $product_options['name']; ?>:</b><br />
                                        </div>
                                     <div class="prod_extra_option_det"> <input type="text" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $product_options['option_value']; ?>" class="time" />
                                  </div></div>
                                  <br />
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
                        <div class="panel panel-default prodtpanels d-block" id="prodttopping_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>">
                            <div class="panel-heading col-xs-12" role="tab" id="prodttophead_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>" data-toggle="collapse" data-parent="#productaccordion_<?php echo $org_product_id; ?>" href="#toppingcollapse_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>" aria-expanded="true" aria-controls="toppingcollapse_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>">
                                <div class="col-xs-11">
                                    <h4 class="toppingpname"><span style="display: none" class="toppingin glyphicon glyphicon-ok"></span> <?php echo $topping['name']; ?></h4>
                                     <?php if ($topping['minimum'] && $topping['minimum']!='0') { ?>
                                            <small class="requir">Required</small>
                                     <?php } 
                                      else { ?>
                                            <small class="optional">Optional</small>
                                     <?php } ?>
                                </div>
                                <h4 class="glyphicon glyphicon-chevron-down"></h4>
                            </div>
                            <div id="toppingcollapse_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>" class="panel-collapse collapse <?php echo (empty($options) && $topping_count==0)?'in':'in'; ?> " role="tabpanel" aria-labelledby="prodttophead_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>">
                                
                                <div class="panel-body optionpanelcontent">
                                    <?php if($topping['type']=='0') { ?>
                                        <?php foreach ($topping['topping_values'] as $topping_value) { ?>
                                            <div class="row toppingavalues" id="topping_vale_<?php echo $org_product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>">
                                                <div class="col-xs-5">
                                                    <h5><?php echo $topping_value['name']; ?></h5>
                                                </div>
                                                <div class="col-xs-3">
                                                     <span class="toppngtotal">
                                                        <h5 class="price"><?php echo $this->currency->format($topping_value['price']);?></h5>
                                                    </span>
                                                </div>

                                                <div class="col-xs-4 txrtr">
                                                    <a class="button toppingplus" onclick="changeToppingqty('<?php echo $org_product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>','+','<?php echo $topping_value['price'];?>','<?php  echo $topping['maximum']; ?>','prodttopping_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>','prodttopping_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>','<?php echo $topping_value['maximum'];?>');"><i class="fa fa-plus" aria-hidden="true"></i> <span style="display:none;" class="qty">0</span></a>
                                                    <input type="hidden" name="topping[<?php echo $topping['topping_id']; ?>][]" value="<?php echo $topping_value['topping_value_id']; ?>" id="option-value-<?php echo $org_product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>"  />
                                                    <input type="hidden" class="topping_qty" price="<?php echo $topping_value['price']; ?>" name="topping_qtys[<?php echo $topping['topping_id']; ?>][<?php echo $topping_value['topping_value_id']; ?>][quantity]" value="0" />   
                                                    <span class="toppngminus" style="display: none">  
                                                        <a class="button" onclick="changeToppingqty('<?php echo $org_product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>','-','<?php echo $topping_value['price'];?>','<?php  echo $topping['maximum']; ?>','prodttopping_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>','prodttopping_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>','<?php echo $topping_value['maximum'];?>');"><i class="fa fa-trash-o" aria-hidden="true"></i></a>                                                    
                                                    </span>   
                                                </div>                                            
                                            </div>
                                        <?php } ?>
                                    <?php }else{ ?>
                                        <div class="row">
                                        <?php foreach ($topping['topping_values'] as $topping_value) { ?>
                                            <div class="col-xs-6">
                                               
                                                <input onclick="toppingMaximum('<?php  echo $topping['maximum']; ?>','toppingcollapse_<?php echo $org_product_id; ?>_<?php echo $topping['topping_id']; ?>','<?php echo $org_product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>');" type="checkbox" name="topping[<?php echo $topping['topping_id']; ?>][]" value="<?php echo $topping_value['topping_value_id']; ?>" id="topping-value-<?php echo $org_product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>"  />
                                                <input id="topping-qty-<?php echo $org_product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>" type="hidden" class="topping_qty" price="<?php echo $topping_value['price']; ?>" name="topping_qtys[<?php echo $topping['topping_id']; ?>][<?php echo $topping_value['topping_value_id']; ?>][quantity]" value="0" />   
                                                 <?php echo $topping_value['name']; ?>(<?php echo $this->currency->format($topping_value['price']);?>
                                            </div>
                                             
                                        <?php } ?>
                                        </div> 
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php $topping_count++;  ?>
                        <?php $incount++;  ?>
                     <?php } ?>
                <?php } ?>
                <!--DRESSINGS-->
                <?php if(!empty($dressings)) { ?>
                    <?php  foreach ($dressings as $dressing) { ?>
                        <div class="panel panel-default prodtpanels" id="prodttdressing_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>">
                            <div class="panel-heading" role="tab" id="prodtdresshead_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>" data-toggle="collapse" data-parent="#productaccordion_<?php echo $product_id; ?>" href="#dressingcollapse_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>" aria-expanded="true" aria-controls="dressingcollapse_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>">
                                <div class="col-xs-11"><h4><span style="display: none" class="dressingin glyphicon glyphicon-ok"></span> <?php echo $dressing['name']; ?></h4></div>
                                <h4 class="glyphicon glyphicon-chevron-down"></h4>
                            </div>
                            <div id="dressingcollapse_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>" class="panel-collapse collapse <?php echo (empty($options) && $topping_count==0)?'in':''; ?> " role="tabpanel" aria-labelledby="prodtdresshead_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>">
                                <div class="panel-body optionpanelcontent">
                                    <?php foreach ($dressing['dressing_values'] as $dressing_value) { ?>
                                        <div id="option-<?php echo $product_id.'_'.$dressing['dressing_id']; ?>" class="checkbox options_dressings col-sm-6">
                                            <label class="checkbox-inline checkbox-dressings">
                                                <input type="checkbox" onclick="changeDressingqty('<?php  echo $bundled_condition['maximum_dressings']; ?>','productaccordion_<?php echo $product_id; ?>','prodttdressing_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>');" name="dressing[<?php echo $dressing['dressing_id']; ?>][]" value="<?php echo $dressing_value['dressing_value_id']; ?>" /><?php echo $dressing_value['name']; ?>
                                            </label>
                                        </div>
                                    <?php }  ?>
                                </div>
                            </div>
                        </div>
                        <?php $incount++;  ?>
                    <?php } ?>
                <?php } ?>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default " data-dismiss="modal" style="float: left">Cancel</button>
        <a onclick="etmAddtocart('productaccordion_<?php  echo $product_id; ?>');;" class=" button" prod_id="<?php echo $all_id; ?>" para="<?php echo $all_id?>">Modify Item</a>
      </div>
<script type="text/javascript">
    
    $(document).ready(function(){
         $('.selectpicker').selectpicker();
        var requiredoption = <?php echo json_encode($required_options); ?> 
        var tootcount = <?php echo $incount;  ?>;   
        $('.optionpanelcontent').css('overflow-y', 'visible'); 
        if(($(window).height() * 0.8-100) -  (tootcount * 40) - 50<250){
            $('.optionpanelcontent').css('max-height',250);     
        }else{
            $('.optionpanelcontent').css('max-height',($(window).height() * 0.8-100) -  (tootcount * 40) - 50);     
        }
        $('.popupoptions .option[master-option!=0]').hide();        
        var option_value_id = "<?php echo (isset($option_value_id)? $option_value_id:''); ?>"; 
        var option_id = "<?php echo $option_id; ?>";
        var $related_options = $('.popupoptions .option[master-option=' + option_id + ']');
        updateOptionList($related_options, option_value_id);
        
        $('.popupoptions .option select').bind('change', function(){
                var option_value = $(this).children('option:selected').attr('option-value');
                var $related_options = $('.popupoptions .option[master-option=' + $(this).parents('.option').attr('option') + ']');
                if ($(this).val() != '') {
                        updateOptionList($related_options, option_value);
                } else {
                        $related_options.slideUp();
                }
                clearOption($related_options);
                checkoptioncompleted(requiredoption,'productaccordion_<?php echo $product_id; ?>');
                $('.selectpicker').selectpicker('refresh');
        });
        
        $('.popupoptions .option input[type=radio]').bind('change', function(){
                var option_value = $(this).attr('option-value');
                var $related_options = $('.popupoptions .option[master-option = ' + $(this).parents('.option').attr('option') + ']');
                if ($('input[name=\'' + $(this).attr('name') + '\']:checked').length!=0) {
                        updateOptionList($related_options, option_value);
                } else {
                        $related_options.slideUp();
                }
                clearOption($related_options);
                checkoptioncompleted(requiredoption,'productaccordion_<?php echo $product_id; ?>');
                $('.selectpicker').selectpicker('refresh');
        });
        
        $('.popupoptions .option input[type=checkbox]').bind('change', function(){
                var option_value = [];
                $('.popupoptions .option input[name=\'' + $(this).attr('name') + '\']:checked').each(function(){
                        option_value.push($(this).attr('option-value'));
                });
                var $related_options = $('.popupoptions .option[master-option = ' + $(this).parents('.option').attr('option') + ']');
                if ($('input[name=\'' + $(this).attr('name') + '\']:checked').length!=0) {
                        updateOptionList($related_options, option_value);
                } else {
                        $related_options.slideUp();
                }
                clearOption($related_options);
                checkoptioncompleted(requiredoption,'productaccordion_<?php echo $product_id; ?>');
                $('.selectpicker').selectpicker('refresh');
        });
        
    });
     function updateOptionList($related_options, option_value) {
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
        }
                                
        function clearOption($option_container) {
                $option_container.find('select').val('');
                $option_container.find('input[type=radio], input[type=checkbox]').removeAttr('checked');
                //$option_container.find('input[type=text], textarea').attr('value', '');
                //recalcPrice();
        } 
        function modifycartitem(){
            $.ajax({
             url: 'index.php?route=product/product/modifycart',
           type: 'post',
           data: $('.product_custom_options input[type=\'text\'], .product_custom_options input[type=\'hidden\'], .product_custom_options input[type=\'radio\']:checked, .product_custom_options input[type=\'checkbox\']:not(:checked), .product_custom_options select, .product_custom_options textarea'),
           dataType: 'json',
            success: function(json) {
                $('.success, .warning, .attention, information, .error').remove();
                if (json['error']) {
                    if (json['error']['option']) {
                        for (i in json['error']['option']) {
                            $('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                        }
                    }
                }

            if (json['success']) {
                    $('#cart').load('index.php?route=module/cart #cart > *');
                    $('#cart-total').html(json['total']);
                    $('.modal').modal('hide');            
                }
               if (json['no_mod']) {

                    $('#cart').load('index.php?route=module/cart #cart > *');
                    $('#cart-total').html(json['total']);
                    $('.modal').modal('hide');
               } 
        }
     });
    }
</script> 