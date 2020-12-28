/*
ts:2015-12-17 12:11:01
*/

-- Set the theme
UPDATE IGNORE `settings` SET `value_dev` = 'home_wide', `value_test` = 'home_wide', `value_stage` = 'home_wide', `value_live` = 'home_wide'
WHERE `variable` = 'template_folder_path';

UPDATE IGNORE settings SET `value_dev` = '13', `value_test` = '13', `value_stage` = '13', `value_live` = '13'
WHERE `variable` = 'assets_folder_path';

UPDATE IGNORE `settings` SET `value_dev` = 0, `value_test` = 0, `value_stage` = 0, `value_live` = 0
WHERE `variable` = 'use_config_file';
