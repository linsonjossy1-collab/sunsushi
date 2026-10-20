<?php
class ModelToolSystemtag extends Model {	
	public function replace($content) {
            
            preg_match_all("/\[([^\]]*)\]/", $content, $matches);
            
            $match_value = array();
            foreach($matches[1] as $match){                            
                $value = $this->config->get($match);
                if($value){
                    $match_value[] = $value;
                }else{
                      $match_value[] = '['.$match.']'; 
                }
            }

            $content = str_replace($matches[0],$match_value, $content);
            
            return $content;
        }
}
?>