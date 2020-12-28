/*
ts:2019-11-29 16:31:00
*/

UPDATE `engine_settings` SET `value_dev` = '04', `value_test` = '04', `value_stage` = '04', `value_live` = '04' WHERE `variable` = 'template_folder_path';
UPDATE `engine_settings` SET `value_dev` = '50', `value_test` = '50', `value_stage` = '50', `value_live` = '50' WHERE `variable` = 'assets_folder_path';