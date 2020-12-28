/*
ts:2016-04-20 13:01:00
*/

UPDATE IGNORE `engine_settings` SET `value_dev` =  'tickets', `value_test` =  'tickets', `value_stage` =  'tickets', `value_live` =  'accommodation'
WHERE `variable` = 'template_folder_path';