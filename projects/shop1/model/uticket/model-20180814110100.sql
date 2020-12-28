/*
ts:2018-08-14 11:01:00
*/

UPDATE `engine_settings` SET `value_dev` = '0' WHERE `variable` = 'checkout_mandatory_mobile_number';

UPDATE
  `engine_settings`
SET
  `value_dev` = '<p>I accept the <a href="/terms-and-conditions.html" target="_blank">terms &amp; conditions</a> and have read the <a href="/privacy-policy.html" target="_blank">privacy policy</a>. I agree that uTicket may share my information with the event organiser.</p>'
WHERE
  `variable` = 'checkout_terms_and_conditions'
;

UPDATE
  `engine_settings`
SET
  `value_test`  = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live`  = `value_dev`
WHERE
  `variable` IN ('checkout_mandatory_mobile_number', 'checkout_terms_and_conditions')
;

UPDATE `engine_settings` SET `value_dev` = 'hello@uticket.ie' WHERE `variable` = 'email';
UPDATE `engine_settings` SET `value_dev` = '+353 21 4193033'  WHERE `variable` = 'telephone';

UPDATE
  `engine_settings`
SET
  `value_test`  = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live`  = `value_dev`
WHERE
  `variable` IN ('email', 'telephone')
;