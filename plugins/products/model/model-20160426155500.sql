/*
ts:2016-04-26 15:55:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('show_minicart_on_add', 'Show Mini Cart on Add', 'products', '0', '0', '0', '0', '0', 'Show the mini cart, after adding a product to the cart', 'toggle_button', 'Products', 'Model_Settings,on_or_off');
