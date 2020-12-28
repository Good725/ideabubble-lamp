/*
ts:2020-06-04 12:01:00
*/

UPDATE `engine_settings`
SET`value_live` = '0', `value_stage` = '0', `value_test` = '0', `value_dev` = '0'
WHERE`variable` = 'checkout_billing_information_section';

UPDATE `engine_settings`
SET`value_live` = '1', `value_stage` = '1', `value_test` = '1', `value_dev` = '1'
WHERE`variable` = 'how_did_you_hear_enabled';

