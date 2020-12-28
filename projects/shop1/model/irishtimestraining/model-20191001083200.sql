/*
ts:2019-10-01 08:32:00
*/

UPDATE `engine_settings`
SET value_test = '1',
    value_test  = '1',
    value_dev   = '1',
    value_stage = '1'
WHERE `variable` = 'engine_enable_org_register';