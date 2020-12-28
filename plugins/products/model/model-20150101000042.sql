/*
ts:2015-01-01 00:00:42
*/

-- ----------------------------------------------------
-- PSYS-66 Checkout: Postage not updating on checkout screen
-- ----------------------------------------------------
ALTER IGNORE TABLE `plugin_products_postage_rate` DROP FOREIGN KEY `postage_rate_fk_1` ;

-- PCSYS-74
ALTER IGNORE TABLE plugin_products_product ADD COLUMN manufacturer_id int;
ALTER IGNORE TABLE plugin_products_product ADD COLUMN distributor_id int;
UPDATE IGNORE
	plugin_products_product
		INNER JOIN plugin_sict_product_relation ON plugin_products_product.id = plugin_sict_product_relation.product_id
		INNER JOIN plugin_sict_product ON plugin_sict_product_relation.sict_product_id = plugin_sict_product.product_id
		INNER JOIN plugin_sict_stock_and_price ON plugin_sict_product.product_id = plugin_sict_stock_and_price.product_id
	SET plugin_products_product.distributor_id = plugin_sict_stock_and_price.distributor_id,
			plugin_products_product.manufacturer_id = plugin_sict_product.manufacturer_id;


-- ----------------------------------------------------
-- PCSYS-124 Checkout Discount Incorrect
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('user_discount_overwrite', 'Set Discounts at User Level', '0', '0', '0', '0', '0', 'Control which discounts a logged-in shopper is applicable for by editing <a href=&quot;/admin/users&quot>settings for the user</a>.', 'toggle_button', 'Shop Checkout', 'Model_Settings,on_or_off');


-- ----------------------------------------------------
-- GP-23 Check out: Pay on collection, option for collection and other improvements
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('checkout_delivery_date', 'Delivery Date', '0', '0', '0', '0', '0', 'Ask the user to enter a delivery date on the checkout', 'toggle_button', 'Shop Checkout', 'Model_Settings,on_or_off');

-- ----------------------------------------------------
-- IBCMS-436  PayPal callback email
-- GP-22      Checkout> PayPal as a payment method
-- ----------------------------------------------------
ALTER IGNORE TABLE `plugin_products_carts`
ADD COLUMN `cart_data` LONGTEXT NULL           AFTER `ip_address` ,
ADD COLUMN `paid`      INT(1)   NULL DEFAULT 0 AFTER `cart_data`  ;

ALTER IGNORE TABLE `plugin_products_carts`
ADD COLUMN `form_data` LONGTEXT NULL           AFTER `cart_data`  ;

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('mandatory_product_description', 'Mandatory Description', '0', '0', '0', '0', '0', 'Make it mandatory for each product to have a description', 'toggle_button', 'Products', 'Model_Settings,on_or_off');


ALTER IGNORE TABLE `plugin_products_product` ADD COLUMN `url_title` VARCHAR(255) NULL  AFTER `title` ;
