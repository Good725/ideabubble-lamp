/*
ts:2019-04-17 15:01:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = '1',
  `value_test`  = '1',
  `value_stage` = '1',
  `value_live`  = '1'
WHERE
  `variable`    = 'page_change_on_sidebar_submenu_open'
;