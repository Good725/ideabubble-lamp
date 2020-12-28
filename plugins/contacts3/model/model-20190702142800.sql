/*
ts:2019-07-02 14:28:00
*/

UPDATE `engine_settings`
SET `value_live`  = '1',
    `value_stage` = '1',
    `value_test`  = '1',
    `value_dev`   = '1'
WHERE `variable` = 'display_sub_contact_types';
