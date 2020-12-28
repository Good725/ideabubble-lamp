/*
ts:2019-12-13 11:33:00
*/

UPDATE `engine_settings`
SET `value_dev`   = 'training_company',
    `value_test`  = 'training_company',
    `value_stage` = 'training_company',
    `value_live` = 'training_company'
WHERE `variable` = 'cms_platform';