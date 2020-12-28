/*
ts:2019-11-19 17:30:00
*/

UPDATE
  `engine_settings`
SET
  `value_live`  = '0',
  `value_stage` = '0',
  `value_test`  = '0',
  `value_dev`   = '0'
WHERE
  `variable`    = 'sticky_mobile_footer_menu';
