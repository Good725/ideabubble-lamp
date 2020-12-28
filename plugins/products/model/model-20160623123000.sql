/*
ts:2016-06-23 12:30:00
*/

ALTER TABLE `plugin_products_postage_rate` ADD COLUMN `country_id` INT(11) NULL DEFAULT NULL AFTER `id` ;

INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES
('checkout_gift_option', 'Gift Card Text', 'products', '0', '0', '0', '0', '0', 'At the checkout, give the customer the option to mark their purchase as a gift and supply text to appear on the gift card', 'toggle_button', 'Shop Checkout', 'Model_Settings,on_or_off');
