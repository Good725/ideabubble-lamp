/*
ts:2018-08-14 11:00:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('checkout_mandatory_mobile_number', 'Mandatory mobile number', '1', '1', '1', '1', '1', 'both', 'Make it mandatory to supply a mobile number at the checkout', 'toggle_button', 'Checkout', 0, 'Model_Settings,on_or_off')
;
