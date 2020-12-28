/*
ts:2019-01-14 09:53:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('courses_discounts_apply', 'Apply Discounts', 'courses', 'Minimum', 'Minimum', 'Minimum', 'Minimum', 'Minimum', 'both', '', 'select', 'Courses', 0, 'Model_KES_Discount,get_discount_modes');
