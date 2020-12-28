/*
ts:2019-05-15 08:49:00
*/

UPDATE `engine_settings` SET `value_live`= '1', `value_stage` = '1', `value_test` = '1', `value_dev` = '1' WHERE (`variable` = 'host_application');