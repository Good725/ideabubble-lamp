/*
ts:2020-10-08 12:09:00
*/

INSERT INTO `engine_settings`
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
  'contacts3_display_marketing_preferences',
  'contacts3',
  'Contact Display Marketing Preferences',
  '1',
  '1',
  '1',
  '1',
  '1',
  'both',
  'Contact Display Marketing Preferences',
  'toggle_button',
  'Contacts',
  0,
  'Model_Settings,on_or_off'
);
