/*
ts:2020-10-10 03:19:00
*/

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
    `options`
)
VALUES
(
    'contacts3_display_contact_preferences',
    'contacts3',
    'Contact Display Contact Preferences',
    '1',
    '1',
    '1',
    '1',
    '1',
    'both',
    'Contact Display Contact Preferences',
    'toggle_button',
    'Contacts',
    0,
    'Model_Settings,on_or_off'
);
