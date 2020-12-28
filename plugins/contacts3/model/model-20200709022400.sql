/*
ts:2020-07-09 02:24:00
*/
DELETE FROM `engine_settings` WHERE `variable` = 'organisation_api_control_membership';
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
    'organisation_api_control_membership',
    'contacts3',
    'Organisation  API Control Special Membership ',
    '0',
    '0',
    '0',
    '0',
    '0',
    'both',
    'Organisation Integration API Control Special Membership ',
    'toggle_button',
    'CDS API',
    0,
    'Model_Settings,on_or_off');
