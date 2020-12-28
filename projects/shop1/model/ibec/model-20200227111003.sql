/*
ts:2020-02-27 11:10:03
*/

UPDATE `engine_settings`
SET `value_live`  = '1',
    `value_stage` = '1',
    `value_test`  = '1',
    `value_dev`   = '1'
WHERE (`variable` = 'default_schedule_group_bookings');
