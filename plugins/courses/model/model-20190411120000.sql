/*
ts:2019-04-11 12:00:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
(
  'course_checkout_coupons',
  'Checkout coupon codes',
  'courses',
  '0',
  '0',
  '0',
  '0',
  '0',
  'Allow coupon codes to be added to the course checkout.',
  'toggle_button',
  'Courses',
  'Model_Settings,on_or_off'
);