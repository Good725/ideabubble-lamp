/*
ts:2015-01-01 00:00:28
*/

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`, `icon`, `order`)	VALUES ('sagepay', 'SagePay', 0, 0, null, 'payments.png', 99);

INSERT INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('sagepay', 'Enable SagePay', 0, 0, 0, 0, 0, 'both', '', 'toggle_button', 'SagePay Settings', 0, 'Model_Settings,on_or_off');
INSERT INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('sagepay_3dsecure', 'Enable 3D Secure', 0, 0, 0, 0, 0, 'both', '', 'toggle_button', 'SagePay Settings', 0, 'Model_Settings,on_or_off');
INSERT INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('sagepay_test', 'Test Mode', 0, 0, 0, 0, 0, 'both', '', 'toggle_button', 'SagePay Settings', 0, 'Model_Settings,on_or_off');
INSERT INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('sagepay_vendor', 'Vendor Name', '', '', '', '', '', 'both', '', 'text', 'SagePay Settings', 0, '');
INSERT INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('sagepay_password', 'Encryption Password', '', '', '', '', '', 'both', '', 'text', 'SagePay Settings', 0, '');
