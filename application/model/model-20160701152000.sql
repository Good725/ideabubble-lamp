/*
ts:2016-07-01 15:20:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`, `options`)
VALUES ('gravatar_enabled', 'Allow Avatars from Gravatar', '1', '1', '1', '1', '1', 'toggle_button', 'Social Media', 'Model_Settings,on_or_off');

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `location`, `note`, `type`, `group`) VALUES ('snapchat_url', 'Snapchat URL', 'both', 'Your Snapchat account name', 'text', 'Social Media');
