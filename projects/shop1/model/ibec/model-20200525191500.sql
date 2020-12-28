/*
ts:2020-05-25 19:15:00
*/
UPDATE `engine_settings`
    SET `value_dev` = 1,
        `value_test` = 1,
        `value_stage` = 1,
        `value_live` = 1
   WHERE `variable` = 'duration_in_checkout';