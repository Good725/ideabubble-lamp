/*
ts:2019-07-17 12:03:00
*/

UPDATE `engine_settings`
SET `value_live`  = 'vw',
    `value_stage` = 'vw',
    `value_test`  = 'vw',
    `value_dev`   = 'vw'
WHERE (`variable` = 'cms_skin');
