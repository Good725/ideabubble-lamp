/*
ts:2020-05-21 18:00:00
*/

-- Add "Online" as a county option
INSERT IGNORE INTO `plugin_courses_counties`
(`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `delete`)
VALUES ('Online', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '1', '1', '0');

UPDATE `plugin_courses_locations`
SET `county_id` = (SELECT `id` FROM `plugin_courses_counties` WHERE `name` = 'Online' ORDER BY `id` DESC LIMIT 1)
WHERE `name` = 'Online';

-- Set what text appears in the schedule selector on the course details page
UPDATE
  `engine_settings`
SET
  `value_dev`   = 'county_date',
  `value_test`  = 'county_date',
  `value_stage` = 'county_date',
  `value_live`  = 'county_date'
WHERE
  `variable` = 'schedule_selector_format';

-- Remove credit-card fee from the checkout
UPDATE `engine_settings` SET `value_dev` = '0', `value_test` = '0', `value_stage` = '0', `value_live` = '0' WHERE `variable` = 'course_cc_booking_fee';

-- Make mobile numbers mandatory on the checkout
UPDATE `engine_settings` SET `value_dev` = '1', `value_test` = '1', `value_stage` = '1', `value_live` = '1' WHERE `variable` = 'checkout_mandatory_mobile_number';
UPDATE `engine_settings` SET `value_dev` = '1', `value_test` = '1', `value_stage` = '1', `value_live` = '1' WHERE `variable` = 'checkout_delegate_mandatory_mobile_number';

-- Toggle fields to show in the invoice form
UPDATE
  `engine_settings`
SET
  `value_dev`   = 'a:1:{i:0;s:17:"purchase_order_no";}',
  `value_test`  = 'a:1:{i:0;s:17:"purchase_order_no";}',
  `value_stage` = 'a:1:{i:0;s:17:"purchase_order_no";}',
  `value_live`  = 'a:1:{i:0;s:17:"purchase_order_no";}'
WHERE
  `variable` = 'invoice_payment_fields';

-- Set the invoice-form intro text
UPDATE `engine_settings`
SET
  `value_dev`   = '<p style="max-width: 620px;">In order to receive an invoice, please provide a valid Purchase Order (PO) number. If you do not have a valid PO number, please request a quote. Bookings will remain provisional until full payment or a valid PO is received</p>',
  `value_test`  = '<p style="max-width: 620px;">In order to receive an invoice, please provide a valid Purchase Order (PO) number. If you do not have a valid PO number, please request a quote. Bookings will remain provisional until full payment or a valid PO is received</p>',
  `value_stage` = '<p style="max-width: 620px;">In order to receive an invoice, please provide a valid Purchase Order (PO) number. If you do not have a valid PO number, please request a quote. Bookings will remain provisional until full payment or a valid PO is received</p>',
  `value_live`  = '<p style="max-width: 620px;">In order to receive an invoice, please provide a valid Purchase Order (PO) number. If you do not have a valid PO number, please request a quote. Bookings will remain provisional until full payment or a valid PO is received</p>'
WHERE `variable` = 'invoice_payment_intro';


-- Clear the invoice-form footer text
UPDATE `engine_settings` SET `value_dev` = '', `value_test` = '', `value_stage` = '', `value_live` = '' WHERE `variable` = 'invoice_payment_footer';

