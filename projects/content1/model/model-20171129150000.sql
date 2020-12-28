/*
ts:2017-11-29 15:00:00
*/

/* Rename the "21" theme to "30" to avoid conflicts when the theme is added to the engine */
UPDATE `engine_site_themes` SET `title` = '30', `stub`  = '30' WHERE `stub`  = '21';

UPDATE `engine_settings` SET `value_dev`   = '30' WHERE `value_dev`   = '21' AND `variable` = 'assets_folder_path';
UPDATE `engine_settings` SET `value_test`  = '30' WHERE `value_test`  = '21' AND `variable` = 'assets_folder_path';
UPDATE `engine_settings` SET `value_stage` = '30' WHERE `value_stage` = '21' AND `variable` = 'assets_folder_path';
UPDATE `engine_settings` SET `value_live`  = '30' WHERE `value_live`  = '21' AND `variable` = 'assets_folder_path';


UPDATE `engine_site_templates` SET `title` = '03' WHERE `stub`  = '03';