<?php
class ControllerCommonSeoUrl extends Controller {
        private $urlrewrite = array();
        private $routesnotallowed = array();
	public function index() {
		// Add rewrite to url class
                $this->rewriteRules();
                
                $this->response->setUrlRewrite($this->urlrewrite);
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}
		
		// Decode URL
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);
			
			foreach ($parts as $part) {
                                if(isset($this->urlrewrite[$part])) {
                                     $this->request->get['route'] = $this->urlrewrite[$part];
                                   //  echo $this->request->get['route'];
                                }
                                else {
                                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

                                    if ($query->num_rows) {
                                            $url = explode('=', $query->row['query']);

                                            if ($url[0] == 'product_id') {
                                                    $this->request->get['product_id'] = $url[1];
                                            }

                                            if ($url[0] == 'category_id') {
                                                    if (!isset($this->request->get['path'])) {
                                                            $this->request->get['path'] = $url[1];
                                                    } else {
                                                            $this->request->get['path'] .= '_' . $url[1];
                                                    }
                                            }	

                                            if ($url[0] == 'manufacturer_id') {
                                                    $this->request->get['manufacturer_id'] = $url[1];
                                            }

                                            if ($url[0] == 'information_id') {
                                                    $this->request->get['information_id'] = $url[1];
                                            }	
//                                            if ($url[0] == 'r') {
//                                                    $this->request->get['route'] = $url[1];
//                                            }
                                    } else {
                                            $this->request->get['route'] = 'error/not_found';	
                                    }
                                }
			}
			
			if (isset($this->request->get['product_id'])) {
				$this->request->get['route'] = 'product/product';
			} elseif (isset($this->request->get['path'])) {
				$this->request->get['route'] = 'product/category';
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$this->request->get['route'] = 'product/manufacturer/info';
			}elseif (!isset ($this->request->get['route']) && isset($this->request->get['information_id'])) {
				$this->request->get['route'] = 'information/information';
			}
			
			if (isset($this->request->get['route'])) {
				return $this->forward($this->request->get['route']);
			}
		}
                if (isset($this->request->get['route'])) {
                    if (in_array($this->request->get['route'],$this->routesnotallowed)) {
                        $this->log->write($this->request->get['route']);
                        $this->redirect($this->url->link('error/not_found', '', 'SSL'));
                    }
                }
	}
	
	public function rewrite($link) {
		if ($this->config->get('config_seo_url')) {
			$url_data = parse_url(str_replace('&amp;', '&', $link));
		
			$url = ''; 
			
			$data = array();
			
			parse_str($url_data['query'], $data);
			
			foreach ($data as $key => $value) {
				if (isset($data['route'])) {
					if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");
					
						if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
							
							unset($data[$key]);
						}					
					} elseif ($key == 'path') {
						$categories = explode('_', $value);
						
						foreach ($categories as $category) {
							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int)$category . "'");
					
							if ($query->num_rows) {
                                                            if($this->config->get('config_cat_prodpage_set')){
                                                                $url = '/' . $query->row['keyword'];
                                                            }
                                                            else {
                                                                $url .= '/' . $query->row['keyword'];
                                                            }
							}							
						}
						
						unset($data[$key]);
					}
                                        else {
                                             $keyword = array_search($data['route'], $this->urlrewrite, true);
                                             if($keyword!= false) {
                                                 $url .= '/' . $keyword;
//                                                    unset($data[$key]);
                                             }
                                            
                                        }
				}
			}
		
			if ($url) {
				unset($data['route']);
			
				$query = '';
			
				if ($data) {
					foreach ($data as $key => $value) {
						$query .= '&' . $key . '=' . $value;
					}
					
					if ($query) {
						$query = '?' . trim($query, '&');
					}
				}

				return $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace('/index.php', '', $url_data['path']) . $url . $query;
			} else {
				return $link;
			}
		} else {
			return $link;
		}		
	}
        
        public function rewriteRules() {
            $seoroutes = $this->cache->get('seoroutes');
            if(!$seoroutes){ 
                $routes = array();
                $ignore = array('module/mobileapp5','module/waiterapp5','module/mobileapp6');
                $common = array('add','insert','update','delete','edit','info','success','validate','calculate');
                $routes_index_key = '';
                $routes_index_val = '';
                $files = glob(DIR_APPLICATION . 'controller/*/*.php');
                $routesnotallowed = array();
                foreach ($files as $file) {
                    $file_content= file_get_contents($file);
                    preg_match_all("/public\s+function\s+(?<fname>\w+)/", $file_content, $matches);
                    $data = explode('/', dirname($file));
                    $dirname = end($data) ;
                    $ctrlname =  basename($file, '.php');
                    if($dirname == 'affiliate' || $dirname == 'epos2') {
                        continue;
                    }
                    $permission = $dirname . '/' . $ctrlname;

                    if (!in_array($permission, $ignore)) {
                        $routes_index_key = '';
                        $routes_index_val = '';
                        foreach ($matches['fname'] as $method) {
                            $method = strtolower($method);
                            if ( $method=='__construct') {
                                continue;
                            }
                            if ($method=='index') {

                                if(!in_array($ctrlname, $common) &&  !isset($routes[$ctrlname])) {
                                    $routes_index_key = $ctrlname;
                                    $routes_index_val = $permission;
                                }
                                else {
                                    if(!isset($routes[$dirname.'-'.$ctrlname])) {
                                        $routes_index_key = $dirname.'-'.$ctrlname;
                                        $routes_index_val = $permission;
                                    }
                                    else {
                                        $routes_index_key = $dirname.'-'.$ctrlname.'-e';
                                        $routes_index_val = $permission;
                                    }
                                }
                                $routesnotallowed[] = $permission;
                                continue;
                            }
                            if(!in_array($method, $common) &&  !isset($routes[$method])) {
                                 $routes[$method] =  $permission . '/'. $method;
                            }
                            else {
                                 if(!isset($routes[$ctrlname.'-'.$method])) {
                                     $routes[$ctrlname.'-'.$method] =  $permission . '/'. $method;
                                 }
                                 else {
                                      $routes[$dirname.'-' .$ctrlname.'-'.$method] =  $permission . '/'. $method;
                                 }
                            }
                        }
                        if($routes_index_key) {
                            $routes[$routes_index_key] = $routes_index_val;
                        }
                    }
                }
                $seo_urlrewrite = array();
                foreach($routes as $key => $value) {
                    $seo_urlrewrite[$this->renamepath($key)] = $value; 
                }
                uasort($seo_urlrewrite, function($a, $b) {
                    return strlen($b) - strlen($a);
                });
                $seoroutes = array('urlrewrite' => $seo_urlrewrite,'routesnotallowed'=>$routesnotallowed);
            
                $this->log->write(print_r($seoroutes,true));
                $this->cache->set('seoroutes',   $seoroutes );

            }

            $this->urlrewrite = $seoroutes['urlrewrite'];
            $this->routesnotallowed = $seoroutes['routesnotallowed'];
        }
        private function renamepath($rnpath){
            $replace = array('common'=>'cmn','information'=>'info','module'=>'mdl','product'=>'prd','category'=>'cat','total'=>'tol','payment'=>'pymt','shipping'=>'del','method'=>'mtd','validate'=>'vld');
            foreach($replace as $key => $value) {
                $rnpath = str_replace($key, $value, $rnpath);
                $rnpath = str_replace('_', '-', $rnpath);
            }
            return $rnpath;
        }
}
?>