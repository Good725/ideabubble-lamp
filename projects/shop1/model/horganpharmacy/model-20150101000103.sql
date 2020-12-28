/*
ts:2015-01-01 00:01:03
*/

-- HPG-12 Express Repeat Prescriptions new form
INSERT IGNORE INTO `plugin_notifications_event` (`name`, `description`, `subject`) VALUES ('express_repeat_prescriptions', 'Express Repeat Prescriptions', 'Express Repeat Prescriptions');

-- HPG-11 Gift Planner form
INSERT IGNORE INTO `plugin_notifications_event` (`name`, `description`, `subject`) VALUES ('gift_planner', 'Gift Planner', 'Gift Planner');

-- HPG-34 Drop down menu with Sub Category
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('main_menu_products', 'Product-Driven Main Menu', '1', '1', '1', '1', '1', 'both', 'Have the main menu be generated automatically using products.', 'toggle_button', 'Products', '0', 'Model_Settings,on_or_off');

-- HPG-13 Checkout Options
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('checkout_delivery_options', 'Checkout Delivery Options', '1', '1', '1', '1', '1', 'both', 'Add extra delivery options  to the checkout.', 'toggle_button', 'Products', '0', 'Model_Settings,on_or_off');
