/*
ts:2015-11-29 10:32:00
*/
ALTER TABLE `plugin_products_carts` ADD INDEX (`id`);
ALTER TABLE `plugin_products_cart_items` MODIFY COLUMN `cart_id` VARCHAR(20);
ALTER TABLE `plugin_products_cart_items` ADD INDEX (`cart_id`);

INSERT IGNORE INTO `settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('cart_hidden_when_empty', 'Hide Empty Cart', 'products', '0', '0', '0', '0', '0', 'Hide the shopping cart when it is empty', 'toggle_button', 'Products', 'Model_Settings,on_or_off');
