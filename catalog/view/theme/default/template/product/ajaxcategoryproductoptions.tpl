<div id="product_info_custom_wc_<?php echo $product_id; ?>" class="product_info_custom_wc">
    <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
    <?php $incount=(!empty($options))?1:0; ?>
    <div class="panel-group catproductoptindiv" id="catproductaccordion_<?php echo $product_id; ?>" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default optionpanel prodtpanels">
            <?php if(!empty($toppings) || !empty($dressings)){ ?>
              <div class="panel-heading" role="tab" id="headingoptOne_<?php echo $product_id; ?>" data-toggle="collapse" data-parent="#catproductaccordion_<?php echo $product_id; ?>" href="#catprodtoptioncollapse_<?php echo $product_id; ?>" aria-expanded="true" aria-controls="catprodtoptioncollapse_<?php echo $product_id; ?>">
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
            <div id="catprodtoptioncollapse_<?php echo $product_id; ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingoptOne_<?php echo $product_id; ?>">
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
                                  <select name="option[<?php echo $product_options['product_option_id']; ?>]" class="form-control selectpicker" id="input-option<?php echo $product_options['product_option_id']; ?>">
                                  <?php //if (!$product_options['required']) { ?>  
                                  <option value="">Please Select</option>
                                  <?php //} ?>
                                  <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                  <option proptionid="<?php echo $product_options['product_option_id']; ?>" value="<?php echo $option_value['product_option_value_id']; ?>" <?php echo 'master-option-value="' . $option_value['master_option_value'] . '"' . ' option-value="' . $option_value['option_value_id'] . '"'; ?>><?php echo $option_value['name']; ?>
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

                              <div class="prod_extra_option_name col-xs-12">
                                <?php if ($product_options['required']) { ?>
                                    <span class="required">*</span>
                                <?php } ?>
                                <b><?php echo $product_options['name']; ?> :</b>
                              </div>

                              <div class="prod_extra_option_det" id="input-option<?php echo $product_options['product_option_id']; ?>">
                                <?php foreach ($product_options['option_value'] as $option_value) { ?>
                                <div class="checkbox col-sm-6">
                                    <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>">
                                        <input proptionid="<?php echo $product_options['product_option_id']; ?>" type="radio" name="option[<?php echo $product_options['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" <?php echo 'master-option-value="' . $option_value['master_option_value'] . '"' . ' option-value="' . $option_value['option_value_id'] . '"'; ?> />
                                            <?php echo $option_value['name']; ?>                                               
                                            <?php if ($option_value['option_price']) { ?>
                                            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                            <?php } ?><span class="checkmarker"></span>
                                    </label>
                                </div>
                                
                              
                              <?php } ?>
                              </div>
                          </div>
                          
                          <?php } ?>
                          <?php if ($product_options['type'] == 'checkbox') { ?>
                              <div id="option-<?php echo $product_options['product_option_id']; ?>" class="option" <?php echo 'master-option="' . $product_options['master_option'] . '"' . ' option="' . $product_options['option_id'] . '"'; ?>>                            
                                  <div class="prod_extra_option_name col-xs-12">
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
                                      <input proptionid="<?php echo $product_options['product_option_id']; ?>" type="checkbox" onclick="optionMaximum('<?php echo $product_options['maximum']; ?>','catprodtoptioncollapse_<?php echo $product_id; ?> #input-option<?php echo $product_options['product_option_id']; ?>');" name="option[<?php echo $product_options['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" <?php echo ($product_options['default']==1) ? 'checked':''  ?>  id="option-value-<?php echo $option_value['product_option_value_id']; ?>" <?php echo 'master-option-value="' . $option_value['master_option_value'] . '"' . ' option-value="' . $option_value['option_value_id'] . '"'; ?>/>
                                          <?php if ($option_value['option_price']) { ?>
                                          (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
                                          <?php } ?><span class="checkboxmarker"></span>
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
                                              <?php if ($option_value['option_price']) { ?>
                                              (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['option_price']; ?>)
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

                              <div class="prod_extra_option_name col-xs-12"><b><?php echo $product_options['name']; ?>
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
                <div class="panel panel-default prodtpanels" id="catprodttopping_<?php echo $product_id; ?>_<?php echo $topping['topping_id']; ?>">
                    <div class="panel-heading col-xs-12" role="tab" id="catprodttophead_<?php echo $product_id; ?>_<?php echo $topping['topping_id']; ?>" data-toggle="collapse" data-parent="#catproductaccordion_<?php echo $product_id; ?>" href="#catprodttoppingcollapse_<?php echo $product_id; ?>_<?php echo $topping['topping_id']; ?>" aria-expanded="true" aria-controls="catprodttoppingcollapse_<?php echo $product_id; ?>_<?php echo $topping['topping_id']; ?>">
                        <div class="col-xs-1">
                            <span style="display: none" class="toppingin glyphicon glyphicon-ok"></span>
                        </div>
                        <div class="col-xs-8">
                            <?php echo $topping['name']; ?>
                        </div>
                         <span class="glyphicon glyphicon-chevron-down"></span>
                    </div>
                    <div id="catprodttoppingcollapse_<?php echo $product_id; ?>_<?php echo $topping['topping_id']; ?>" class="panel-collapse collapse <?php echo (empty($options) && $topping_count==0)?'in':''; ?> " role="tabpanel" aria-labelledby="catprodttophead_<?php echo $product_id; ?>_<?php echo $topping['topping_id']; ?>">
                        <div class="panel-body optionpanelcontent">
                            <?php foreach ($topping['topping_values'] as $topping_value) { ?>
                                <div class="row toppingavalues" id="topping_vale_<?php echo $product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>">
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
                                            <a class="button" onclick="changeToppingqty('<?php echo $product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>','-','<?php echo $topping_value['price'];?>','<?php  echo $bundled_condition['maximum_toppings']; ?>','catproductaccordion_<?php echo $product_id; ?>','catprodttopping_<?php echo $product_id; ?>_<?php echo $topping['topping_id']; ?>');">-</a>                                                    
                                        </div>  
                                    </div>
                                    <div class="col-xs-1">
                                        <span style="display: none" class="qty">0</span>                                                
                                    </div>
                                    <div class="col-xs-1">
                                        <a class="button" onclick="changeToppingqty('<?php echo $product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>','+','<?php echo $topping_value['price'];?>','<?php  echo $bundled_condition['maximum_toppings']; ?>','catproductaccordion_<?php echo $product_id; ?>','catprodttopping_<?php echo $product_id; ?>_<?php echo $topping['topping_id']; ?>');">+</a>
                                        <input type="hidden" name="topping[<?php echo $topping['topping_id']; ?>][]" value="<?php echo $topping_value['topping_value_id']; ?>" id="option-value-<?php echo $product_id.'_'.$topping['topping_id'].'_'.$topping_value['topping_value_id']; ?>"  />
                                        <input type="hidden" class="topping_qty" price="<?php echo $topping_value['price']; ?>" name="topping_qtys[<?php echo $topping['topping_id']; ?>][<?php echo $topping_value['topping_value_id']; ?>][quantity]" value="0" />    
                                    </div>                                            
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
                <div class="panel panel-default prodtpanels" id="catprodttdressing_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>">
                    <div class="panel-heading" role="tab" id="prodtdresshead_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>" data-toggle="collapse" data-parent="#catproductaccordion_<?php echo $product_id; ?>" href="#catprodtdressingcollapse_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>" aria-expanded="true" aria-controls="catprodtdressingcollapse_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>">
                        <div class="col-xs-1"><span style="display: none" class="dressingin glyphicon glyphicon-ok"></span></div>
                        <div class="col-xs-8"><?php echo $dressing['name']; ?> </div>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </div>
                    <div id="catprodtdressingcollapse_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>" class="panel-collapse collapse <?php echo (empty($options) && $topping_count==0)?'in':''; ?> " role="tabpanel" aria-labelledby="prodtdresshead_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>">
                        <div class="panel-body optionpanelcontent">
                            <?php foreach ($dressing['dressing_values'] as $dressing_value) { ?>
                                <div id="option-<?php echo $product_id.'_'.$dressing['dressing_id']; ?>" class="checkbox options_dressings col-sm-4">
                                    <label class="checkbox-inline checkbox-dressings">
                                        <input type="checkbox" onclick="changeDressingqty('<?php  echo $bundled_condition['maximum_dressings']; ?>','catproductaccordion_<?php echo $product_id; ?>','catprodttdressing_<?php echo $product_id; ?>_<?php echo $dressing['dressing_id']; ?>');" name="dressing[<?php echo $dressing['dressing_id']; ?>][]" value="<?php echo $dressing_value['dressing_value_id']; ?>" /><?php echo $dressing_value['name']; ?>
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



<script type="text/javascript">
    $(function() {
        $('.selectpicker').selectpicker();
        $(document).ready(function(){
         var requiredoption = <?php echo json_encode($required_options); ?>   
        $('.popupoptions .option[master-option!=0]').hide();
        var tootcount = <?php echo $incount;  ?>;  
        $('.optionpanelcontent').css('overflow-y', 'visible'); 
        if(($(window).height() * 0.8-100) -  (tootcount * 40) - 100<250){
            $('.optionpanelcontent').css('max-height',250);     
        }else{
            $('.optionpanelcontent').css('max-height',($(window).height() * 0.8-100) -  (tootcount * 40));     
        }
        //var option_value_id = "<?php //echo $option_value_id; ?>"; 
        //var option_id = "<?php //echo $option_id; ?>";
        //var $related_options = $('.popupoptions .option[master-option=' + option_id + ']');
        //updateOptionList($related_options, option_value_id);
        
        $('.popupoptions .option select').bind('change', function(){
                var option_value = $(this).children('option:selected').attr('option-value');
                var $related_options = $('.popupoptions .option[master-option=' + $(this).parents('.option').attr('option') + ']');
                if ($(this).val() != '') {
                        updateOptionList($related_options, option_value);
                } else {
                        $related_options.slideUp();
                }
                clearOption($related_options);
                checkoptioncompleted(requiredoption,'catproductaccordion_<?php echo $product_id; ?>');
        });
        
        $('.popupoptions .option input[type=radio]').bind('change', function(){
                var option_value = $(this).attr('option-value');
                var $related_options = $('.popupoptions .option[master-option = ' + $(this).parents('.option').attr('option') + ']');
                if ($('input[name=\'' + $(this).attr('name') + '\']:checked').length != 0) {
                        updateOptionList($related_options, option_value);
                } else {
                        $related_options.slideUp();
                }
                clearOption($related_options);
                checkoptioncompleted(requiredoption,'catproductaccordion_<?php echo $product_id; ?>');
        });
        
        $('.popupoptions .option input[type=checkbox]').bind('change', function(){
                var option_value = [];
                $('.popupoptions .option input[name=\'' + $(this).attr('name') + '\']:checked').each(function(){
                        option_value.push($(this).attr('option-value'));
                });
                var $related_options = $('.popupoptions .option[master-option = ' + $(this).parents('.option').attr('option') + ']');
                if ($('input[name=\'' + $(this).attr('name') + '\']:checked').length != 0) {
                        updateOptionList($related_options, option_value);
                } else {
                        $related_options.slideUp();
                }
                clearOption($related_options);
                checkoptioncompleted(requiredoption,'catproductaccordion_<?php echo $product_id; ?>');
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
                // image options


                if (typeof(option_value) == "string") {
                        option_value = [option_value];
                }
                for (var i in option_value) {
                        $related_options.find('option[master-option-value=' + option_value[i] + '], option[value=\'\']').each(function(){
                                if ($(this).parent('span').length!= 0) {
                                        $(this).unwrap();
                                }
                        });
                        $related_options.find('input[master-option-value=' + option_value[i] + ']').show();
                        $related_options.find('option[master-option-value=' + option_value[i] + ']').css("display","block");//removeAttr('disabled');
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
        }
                                
        function clearOption($option_container) {
                $option_container.find('select').val('');
                $option_container.find('input[type=radio], input[type=checkbox]').removeAttr('checked');
                //$option_container.find('input[type=text], textarea').attr('value', '');
                //recalcPrice();
        } 
 
    });
</script>