/*
ts:2016-04-20 10:15:00
*/

UPDATE IGNORE `engine_settings` SET `value_dev` =  'accommodation', `value_test` =  'accommodation', `value_stage` =  'accommodation', `value_live` =  'accommodation'
WHERE `variable` = 'template_folder_path';

UPDATE IGNORE `engine_settings` SET `value_dev` = '29', `value_test` = '29', `value_stage` = '29', `value_live` = '29'
WHERE `variable` = 'assets_folder_path';

UPDATE IGNORE `engine_settings` SET `value_dev` =  '0', `value_test` =  '0', `value_stage` =  '0', `value_live` =  '0'
WHERE `variable` = 'use_config_file';