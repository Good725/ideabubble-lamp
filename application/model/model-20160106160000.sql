/*
ts:2016-01-06 16:00:00
*/

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`) VALUES
('featured_item_order', 'Order Featured Items', '1', '1', '1', '1', '1', 'both', 'Order the featured Items and inactive items in Alphabetical order. Set to no will load the items by order set in the database.', 'toggle_button', 'Dashboard', 'Model_Settings,on_or_off');
