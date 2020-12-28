/*
ts:2018-08-30 18:00:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('checkout_student_year_dropdown', 'Student year dropdown', '0', '0', '0', '0', '0', 'both', 'Display a dropdown, asking for the student year on the checkout', 'toggle_button', 'Checkout', 0, 'Model_Settings,on_or_off')
;
