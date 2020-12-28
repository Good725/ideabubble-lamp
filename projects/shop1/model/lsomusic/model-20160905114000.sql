/*
ts:2016-09-05 11:40:00
*/

UPDATE IGNORE `engine_settings` SET `value_dev` =  'courses2', `value_test` =  'courses2', `value_stage` =  'courses2', `value_live` =  'courses2'
WHERE `variable` = 'template_folder_path';

UPDATE IGNORE `engine_settings` SET `value_dev` = '26', `value_test` = '26', `value_stage` = '26', `value_live` = '26'
WHERE `variable` = 'assets_folder_path';

UPDATE IGNORE `engine_settings` SET `value_dev` =  '0', `value_test` =  '0', `value_stage` =  '0', `value_live` =  '0'
WHERE `variable` = 'use_config_file';