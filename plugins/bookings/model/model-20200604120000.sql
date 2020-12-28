/*
ts:2020-06-04 12:00:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES ('checkout_billing_information_section', 'Show billing-information section', '1', '1', '1', '1', '1', 'both', 'Make it mandatory to supply a mobile number for each delegate at the checkout', 'toggle_button', 'Checkout', 0, 'Model_Settings,on_or_off');
