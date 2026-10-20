<?php
class ModelCatalogCategory extends Model {
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category c LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");
		
		return $query->row;
	}
	
	public function getCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category c LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
		
		return $query->rows;
	}
          public function getAllCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category c LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
		
		return $query->rows;
	}
        
	public function getCategoriesByParentId($category_id) {
		$category_data = array();
		
		$category_query = $this->db->query("SELECT category_id FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category WHERE parent_id = '" . (int)$category_id . "'");
		
		foreach ($category_query->rows as $category) {
			$category_data[] = $category['category_id'];
			
			$children = $this->getCategoriesByParentId($category['category_id']);
			
			if ($children) {
				$category_data = array_merge($children, $category_data);
			}			
		}
		
		return $category_data;
	}
		
	public function getCategoryLayoutId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		
		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return $this->config->get('config_layout_category');
		}
	}
					
	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category c LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");
		
		return $query->row['total'];
	}
    public function getCategoryinfo($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category c LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");
		
		return $query->rows;
	}
        
        public function getMainMenuDetails($parent_id = 0) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category c LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_times ct ON (c.category_id = ct.category_id) LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int) $parent_id . "' AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

        return $query->rows;
    }

    public function getMainMenuUrls($cat_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "url_alias ua WHERE ua.query LIKE 'category_id=" . $cat_id . "'");
        //print_r($query->row);
        if (!empty($query->row))
            return $query->row['keyword'];
        else
            return '';
    }

    public function getParentcategoryID($cat_id) {
        $query = $this->db->query("SELECT parent_id FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category c WHERE c.category_id ='" . $cat_id . "'");
        return $query->row['parent_id'];
    }

    public function getcatTimes($cat_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_times c WHERE c.category_id='" . $cat_id . "'");
        //print_r($query->row);
         return $query->row;
    }
    public function getCategoryProductLeastPrice($cat_id){
      $query = $this->db->query("SELECT MIN(price) as price FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "product p LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "product_to_category p2c ON (p.product_id = p2c.product_id)  WHERE p2c.category_id = '" . (int) $cat_id . "'");
       //echo "SELECT MIN(price) FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)  WHERE p2c.category_id = '" . (int) $cat_id . "'";
        return $query->row['price'];   
    }
     public function getSidecategories(){
        $cat_array=array();
        $query = $this->db->query("SELECT c.category_id  FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category c LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.must_in_cart = '1'  AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "'  AND c.status = '1' ");
        foreach ($query->rows as $value) {
            $cat_array[]=$value['category_id'];
        }
        return $cat_array;
    }
    public function getAllCategoryIDs() {
		$query = $this->db->query("SELECT c.category_id FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category c  LEFT JOIN " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE  c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ");
		
		return $query->rows;
}
    public function getLastCategoryModified() {
        $query = $this->db->query("SELECT MAX(date_modified) last_modified FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category ");

	    return $query->row['last_modified'];
    }

	public function getCategoryMinimumCart($category_id) {
		$sql = "SELECT minimum_amount  FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category WHERE category_id ='" . (int) $category_id . "'";
		$query = $this->db->query($sql);
		if($query->row['minimum_amount']) {
			return unserialize($query->row['minimum_amount']);
		} else {
			return array();
		}
	}
	
	public function getCategoryShippingTime($category_id) {
		$sql = "SELECT shipping_time FROM " . DB_PREFIX .((isset($this->session->data['shadow_menu']) && ($this->session->data['shadow_menu'] == $this->config->get('config_store_id')) )? $this->session->data['shadow_menu'] : '' ) .  "category WHERE category_id ='" . (int) $category_id . "'";
		$query = $this->db->query($sql);
		$category_data = array();
		if($query->row['shipping_time']) {
			$category_data = unserialize($query->row['shipping_time']);
		}
		return $category_data;
	}
}
?>