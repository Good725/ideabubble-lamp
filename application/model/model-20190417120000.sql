/*
ts:2019-04-17 12:00:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `group`, `type`, `options`)
VALUES
(
  'shared_footer',
  'Use front-end footer',
  '0',
  '0',
  '0',
  '0',
  '0',
  'Use the same footer for both the back and front ends of the site.',
  'Engine',
  'toggle_button',
  'Model_Settings,on_or_off'
);