<?php

class ControllerModuleProductzincserver extends Controller { 
    public function index() {   
     $response=array();   
     $this->load->model('setting/possetting');
    // echo "s1";
     //print_r($this->request->post);
     //echo json_encode($this->request->post);
     $prodt_last_modified = $this->request->post['prodt_modified'];
     $cat_last_modified = $this->request->post['cat_modified'];
     if(!$this->config->get('productzincserver_status') || $this->request->post['security_key']!=$this->config->get('productzincserver_securitykey')){
         echo "auth_error";
         return ;
     }
     if(isset($this->request->post['action']) and $this->request->post['action']=='warning'){
         if($this->model_setting_possetting->checkProductmodified($prodt_last_modified) || $this->model_setting_possetting->checkCategorymodified($cat_last_modified)){
            //$response['warning']= "warning"; 
             //echo json_encode($response);
             echo "success";
             return ;
         }
     }
    // echo "ss";
     //return true;
     if($this->model_setting_possetting->checkProductmodified($prodt_last_modified) || $this->model_setting_possetting->checkCategorymodified($cat_last_modified)){
        $date_modified = '';
        $table_list = array(
                       'product','product_attribute','product_bundle','product_bundle_items',
                       'product_bundle_items_options','product_description','product_discount',
                       'product_image','product_option','product_option_value','product_related',
                       'product_reward','product_side_dish','product_side_dish_to_category',
                       'product_special','product_tag','product_to_category','product_to_download',
                       'product_to_layout','product_to_store','option','option_description',
                       'option_value','option_value_description','category','category_description',
                       'category_to_layout','category_to_store'
                      );
        $filename = 'productupdated.sql.gz';
        $fp = gzopen(DIR_DOWNLOAD. $filename, 'w9');
        $schema = '# ' .$this->config->get('config_name'). "\n" .
                   '#' . "\n" .
                   '# Database For ' . DB_DATABASE . " Products Updated \n" .
                   '# Copyright (c) ' . date('Y') . ' ' . 'eTakeawayMax' . "\n" .
                   '#' . "\n" .
                   '# Database: ' . DB_DATABASE . "\n" .
                   '# Database Server: ' .DB_HOSTNAME . "\n" .
                   '#' . "\n" .
                   '# Date: ' .  date("Y-m-d H:i:s") . "\n".
                   '#-----------------------------------------'."\n\n"; 
          //if() //copy table structure command for temp storage   
         foreach($table_list as $table){
            $schema .= 'CREATE TABLE IF NOT EXISTS `edited_'.$table."` LIKE `".$table."`;\n";
        }
        foreach($table_list as $table){
            $schema .= 'TRUNCATE TABLE edited_'.$table. ";\n";
        }
        $schema .= '#--------------------------'."\n";
         gzwrite($fp, $schema);
        foreach ($table_list as $table) {
            $field_list = array();
            $fields = $this->model_setting_possetting->getFileds($table);
            foreach($fields as $field){
                 $field_list[] = '`'.$field['Field'].'`';
            }
           //echo implode(',', $field_list);die();
            $table_items= $this->model_setting_possetting->dumbData($field_list,$table);
            foreach ($table_items as $rows) {
                        $schema = 'insert into edited_' . $table . ' (' . implode(', ', $field_list) . ') values (';

                        reset($field_list);

                        while (list(, $i) = each($field_list)) {
                            $i=  preg_replace('/`/','', $i);
                            if (!isset($rows[$i])) {
                                $schema .= 'NULL, ';
                            } elseif (($rows[$i]) != null) {
                                $k = strlen($rows[$i]);
                                $hex = $rows[$i];
                                if ($k > 1000) {
                                    $rows[$i] = '0x' . bin2hex($hex);
                                    $row = addslashes($rows[$i]);
                                    $row = preg_replace("/\n#/", "\n" . '\#', $row);
                                    $schema .= $row . ', ';
                                } else {
                                    $row = addslashes($rows[$i]);
                                    $row = preg_replace("/\n#/", "\n" . '\#', $row);

                                    $schema .= '\'' . $row . '\', ';
                                }
                            } else {
                                $schema .= '\'\', ';
                            }
                        }
//die();
                        $schema = preg_replace('/, $/', '', $schema) . ');' . "\n";

                        gzwrite($fp, $schema);
                    }
        }
        gzclose($fp);
        echo "success";
      }
       
        
    }
}

?>