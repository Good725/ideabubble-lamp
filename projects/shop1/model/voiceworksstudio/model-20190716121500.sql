/*
ts:2019-07-16 12:15:00
*/

UPDATE `engine_settings`
SET `value_live`  = '48',
    `value_stage` = '48',
    `value_test`  = '48',
    `value_dev`   = '48'
WHERE (`variable` = 'assets_folder_path')
;;