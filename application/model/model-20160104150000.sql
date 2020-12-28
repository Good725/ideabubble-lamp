/*
ts:2016-01-04 15:00:00
*/

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`) VALUES
('use_header', 'Use Header', '0', '0', '0', '0', '0', 'both', 'Use a custom header for the engine', 'toggle_button', 'Engine', 'Model_Settings,on_or_off'),
('engine_header', 'Engine Header','','','','','','both','Header url','text','Engine','');
