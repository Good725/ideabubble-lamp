/*
ts:2018-09-06 10:10:00
*/

UPDATE `engine_settings`
SET `value_dev` = 1, `value_test` = 1, `value_stage` = 1, `value_live` = 1
WHERE `variable` = 'newsletter_subscription_captcha';