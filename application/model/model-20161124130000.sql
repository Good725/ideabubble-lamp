/*
ts:2016-11-24 13:00:00
*/

UPDATE `engine_settings` SET `variable`='slaask_api_access_frontend', `name`='Enable Slaask on the front end' WHERE `variable`='slaask_api_access';

INSERT INTO `engine_settings`
(`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`, `options`) VALUES
('slaask_api_access_cms', 'Enable Slaask in the CMS', '0', '0', '0', '0', '0', 'toggle_button', 'Social Media', 'Model_Settings,on_or_off');
