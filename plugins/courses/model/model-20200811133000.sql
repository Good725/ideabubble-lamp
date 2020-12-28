/*
ts:2020-08-11 13:30:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES (
  'only_display_navision_courses',
  'Only display Navision courses',
  '0',
  '0',
  '0',
  '0',
  '0',
  'both',
  'When enabled, only courses that have been linked to Navision events will be available on the frontend.',
  'toggle_button',
  'NAVISION API',
  '0',
  'Model_Settings,on_or_off'
);
