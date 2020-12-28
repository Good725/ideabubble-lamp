/*
ts:2016-02-29 15:00:00
*/
UPDATE IGNORE `settings` SET `value_dev` =  'a', `value_test` =  'a', `value_stage` =  'a', `value_live` =  'a'
WHERE `variable` = 'template_folder_path';

UPDATE IGNORE `settings` SET `value_dev` = '24', `value_test` = '24', `value_stage` = '24', `value_live` = '24'
WHERE `variable` = 'assets_folder_path';

UPDATE IGNORE `settings` SET `value_dev` =  '0', `value_test` =  '0', `value_stage` =  '0', `value_live` =  '0'
WHERE `variable` = 'use_config_file';