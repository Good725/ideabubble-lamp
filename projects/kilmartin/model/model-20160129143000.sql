/*
ts:2016-01-29 14:30:00
*/

UPDATE `settings` SET `value_live` = '08:00',`value_stage` = '08:00',`value_test` = '08:00',`value_dev` = '08:00',`default` = '08:00' WHERE `variable` = 'schedule_start_time';
UPDATE `settings` SET `value_live` ='23:00',`value_stage` ='23:00',`value_test` ='23:00',`value_dev` ='23:00',`default` ='23:00' WHERE `variable` = 'schedule_end_time';
UPDATE `settings` SET `value_live` = '5',`value_stage` = '5',`value_test` = '5',`value_dev` = '5',`default` = '5' WHERE `variable` = 'schedule_time_interval';