/*
ts:2019-11-29 13:06:00
*/

UPDATE `engine_settings`
SET `value_dev`   = '1',
    `value_test`  = '1',
    `value_stage` = '1'
WHERE `variable` = 'schedule_enable_invoice';