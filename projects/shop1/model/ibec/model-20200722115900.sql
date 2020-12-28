/*
ts:2020-07-22 11:59:00
*/
DELETE FROM `engine_settings` WHERE `variable` = 'organisation_create_external_account';
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
    'organisation_create_external_account',
    'contacts3',
    'Organisation Create CDS Account for New Organisation',
    '0',
    '0',
    '0',
    '0',
    '0',
    'both',
    'Organisation Create CDS Account for New Organisation',
    'toggle_button',
    'CDS API',
    0,
    'Model_Settings,on_or_off');
