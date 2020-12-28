/*
ts:2020-01-14 15:15:00
*/

UPDATE `engine_settings`
SET `value_dev`   = '1',
    `value_test`  = '1',
    `value_stage` = '1',
    `value_live` = '1'
WHERE `variable` = 'only_show_primary_trainer_course_dropdown';