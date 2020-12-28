/*
ts:2015-12-11 13:16:00
*/

UPDATE plugin_products_product
								INNER JOIN plugin_sict_product on plugin_products_product.product_code = plugin_sict_product.sku
							SET plugin_products_product.publish = 1
							WHERE plugin_sict_product.id IS NOT NULL;

UPDATE plugin_products_product
								LEFT JOIN plugin_sict_product on plugin_products_product.product_code = plugin_sict_product.sku
							SET plugin_products_product.publish = 0
							WHERE plugin_sict_product.id IS NULL;
