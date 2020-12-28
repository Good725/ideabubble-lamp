/*
ts:2020-06-23 01:54:00
*/
DELETE FROM `engine_settings` WHERE `variable` = 'organisation_integration_api';
INSERT IGNORE INTO `engine_settings`
(
 `variable`,
 `linked_plugin_name`,
 `name`,
 `value_live`,
 `value_stage`,
 `value_test`,
 `value_dev`,
 `default`,
 `location`,
 `note`,
 `type`,
 `group`,
 `required`,
 `options`)
VALUES
(
 'organisation_integration_api',
 'contacts3',
 'Organisation Integration API',
 '0',
 '0',
 '0',
 '0',
 '0',
 'both',
 'Enable CDS API integration',
 'toggle_button',
 'CDS API',
 0,
 'Model_Settings,on_or_off');
