/*
ts:2016-08-03 15:20:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `location`, `note`, `type`, `group`, `options`) VALUES ('view_website', 'View Website', 'both', 'View Website From Backend', 'toggle_button', 'Dashboard','Model_Settings,on_or_off');
INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`) VALUES ('0', 'view_website_frontend', 'View Website From Dashboard');
