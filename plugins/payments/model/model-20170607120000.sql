/*
ts:2017-06-07 12:00:00
*/

INSERT IGNORE INTO `engine_settings`(`variable`, `name`, `note`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`, `options`) VALUES
(
  'enable_mobile_payments',
  'Enable Mobile Payments',
  'Allow for payments via mobile carrier',
  '0',
  '0',
  '0',
  '0',
  '0',
  'toggle_button',
  'Mobile Payments',
  'Model_Settings,on_or_off'
);
