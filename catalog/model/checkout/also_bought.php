<?php

class ModelCheckoutAlsoBought extends Model {

    public function getAlsoBought($product_id,$limit = 0,$days = 0) {

		$date = date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * $days)));

		$also_bought_products = $this->db->query("
			SELECT op.product_id, COUNT(op.product_id) AS bought
			FROM " . DB_PREFIX . "order_product op
			LEFT JOIN " . DB_PREFIX . "product p ON p.product_id = op.product_id
			WHERE op.order_id IN (
				SELECT op.order_id FROM " . DB_PREFIX . "order_product op
				LEFT JOIN `" . DB_PREFIX . "order` o ON o.order_id = op.order_id
				WHERE op.product_id IN (" . $product_id . ")
				AND o.store_id = 0
				AND o.date_added >= '" . $date . "'
			)
			AND op.product_id NOT IN (" . $product_id . ") 
			AND p.date_available <= NOW() AND p.status = '1'
			GROUP BY product_id ORDER BY bought DESC
			LIMIT " . $limit);

		if ($also_bought_products->rows) {

			$products = array();

			foreach ($also_bought_products->rows as $product) {
			array_push($products, $product['product_id']);
			}

			$result = $this->db->query("SELECT DISTINCT p.product_id, p.image, p.price, tax_class_id, pd.name as name, pd.description as description , vegetarian, hot_count, pd.name AS description, p.image, m.name AS manufacturer, ss.name AS stock
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
			LEFT JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id)
			WHERE p.product_id IN (" . join(',', $products) . ")
			AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "'
			AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "'
			AND ss.language_id = '" . (int) $this->config->get('config_language_id') . "'
			AND p.date_available <= NOW() AND p.status = '1'");
			return $result->rows;
		} else {
			return false;
		}
    }

}

?>