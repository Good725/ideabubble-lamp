/*
ts:2020-09-30 13:10:00
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
  'contacts3_list_display_student_id',
  'contacts3',
  'Contact List Display Student Id',
  '0',
  '0',
  '0',
  '0',
  '0',
  'both',
  'Display Student Id',
  'toggle_button',
  'Contacts',
  0,
  'Model_Settings,on_or_off'
);
