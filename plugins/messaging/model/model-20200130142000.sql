/*
ts:2020-01-30 14:20:00
*/

INSERT IGNORE INTO `engine_settings`
(`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`,
 `location`, `note`, `type`, `group`, `required`, `options`)
VALUES ('contacts3', 'contact_default_preferences', 'Default contact preferences', 'a:3:{i:0;s:2:"15";i:1;s:2:"16";i:2;s:2:"17";}',
        'a:3:{i:0;s:2:"15";i:1;s:2:"16";i:2;s:2:"17";}', 'a:3:{i:0;s:2:"15";i:1;s:2:"16";i:2;s:2:"17";}',
        'a:3:{i:0;s:2:"15";i:1;s:2:"16";i:2;s:2:"17";}', '', 'both',
        'When a contact is created, these values will be by default what is selected for their contact preferences',
        'multiselect', 'Contacts', 0, 'Model_Preferences,contact_default_preferences_settings');