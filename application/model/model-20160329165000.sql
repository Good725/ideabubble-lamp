/*
ts:2016-03-29 16:50:00
*/

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES (
  'show_friendly_errors',
  'Show Friendly Errors',
  '1',
  '0',
  '0',
  '0',
  '0',
  'Turn on to hide errors thrown by the code and display a generic message instead.',
  'toggle_button',
  'Website',
  'Model_Settings,on_or_off'
);
