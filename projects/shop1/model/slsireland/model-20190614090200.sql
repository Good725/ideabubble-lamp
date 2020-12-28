/*
ts:2019-06-14 09:02:00
*/

UPDATE `engine_settings`
SET `value_live`  = '1',
    `value_stage` = '1',
    `value_test`  = '1',
    `value_dev`   = '1'
WHERE (`variable` = 'link_contacts_to_bookings');
