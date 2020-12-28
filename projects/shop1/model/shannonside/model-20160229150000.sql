/*
ts:2016-02-29 15:00:00
*/
UPDATE IGNORE `settings` SET `value_dev` =  'a', `value_test` =  'a', `value_stage` =  'a', `value_live` =  'a'
WHERE `variable` = 'template_folder_path';

UPDATE IGNORE `settings` SET `value_dev` = '25', `value_test` = '25', `value_stage` = '25', `value_live` = '25'
WHERE `variable` = 'assets_folder_path';

UPDATE IGNORE `settings` SET `value_dev` =  '0', `value_test` =  '0', `value_stage` =  '0', `value_live` =  '0'
WHERE `variable` = 'use_config_file';