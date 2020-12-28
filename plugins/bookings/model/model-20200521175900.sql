/*
ts:2020-05-21 17:59:00
*/
-- Setting for the format of data in the schedule selector on the course-details page
INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `location`, `note`, `type`, `group`, `options`)
VALUES ('schedule_selector_format', 'Schedule-selector format', 'bookings', '', 'both', 'Format of options in the schedule selector on the course-details page', 'dropdown', 'Bookings', '{\"\":\"\-\- Please select \-\-\", \"county_date\": \"County, date\"}');

-- Setting to determine if the delegate mobile number should be mandatory
INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES ('checkout_delegate_mandatory_mobile_number', 'Mandatory delegate mobile number', '0', '0', '0', '0', '0', 'both', 'Make it mandatory to supply a mobile number for each delegate at the checkout', 'toggle_button', 'Checkout', 0, 'Model_Settings,on_or_off');

-- Setting to determine which fields should show in the invoice payment form
INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'invoice_payment_fields',
  'Fields in invoice-payment form',
  'bookings',
  '',
  '',
  '',
  '',
  '',
  'both',
  'Fields to display in the invoice-payment form',
  'multiselect',
  '0',
  'Checkout',
  '0',
  '{"has_aiq_account":"Do you have an existing AIQ account?",
"aiq_customer_code":"AIQ customer code",
"aiq_billing_email":"Billing email",
"purchase_order_no":"Purchase order number"}'
);

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'a:4:{i:0;s:15:"has_aiq_account";i:1;s:17:"aiq_customer_code";i:2;s:17:"aiq_billing_email";i:3;s:17:"purchase_order_no";}',
  `value_test`  = 'a:4:{i:0;s:15:"has_aiq_account";i:1;s:17:"aiq_customer_code";i:2;s:17:"aiq_billing_email";i:3;s:17:"purchase_order_no";}',
  `value_stage` = 'a:4:{i:0;s:15:"has_aiq_account";i:1;s:17:"aiq_customer_code";i:2;s:17:"aiq_billing_email";i:3;s:17:"purchase_order_no";}',
  `value_live`  = 'a:4:{i:0;s:15:"has_aiq_account";i:1;s:17:"aiq_customer_code";i:2;s:17:"aiq_billing_email";i:3;s:17:"purchase_order_no";}',
  `default`     = 'a:4:{i:0;s:15:"has_aiq_account";i:1;s:17:"aiq_customer_code";i:2;s:17:"aiq_billing_email";i:3;s:17:"purchase_order_no";}'
WHERE
  `variable` = 'invoice_payment_fields';

-- Text to appear at the beginning and end of the invoice-payment form
INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`)
VALUES ('invoice_payment_intro', 'Invoice-payment form intro text', '', '', '', '', '', 'both', 'Text to appear at the beginning of the invoice-payment form', 'wysiwyg', 'Checkout', 0);

INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`)
VALUES (
  'invoice_payment_footer',
  'Invoice-payment form footer text',
  '<p>If you are booking within 30 days of the course start date we require a credit card.</p>',
  '<p>If you are booking within 30 days of the course start date we require a credit card.</p>',
  '<p>If you are booking within 30 days of the course start date we require a credit card.</p>',
  '<p>If you are booking within 30 days of the course start date we require a credit card.</p>',
  '<p>If you are booking within 30 days of the course start date we require a credit card.</p>',
  'both',
  'Text to appear at the end of the invoice-payment form',
  'wysiwyg',
  'Checkout',
  '0'
);
