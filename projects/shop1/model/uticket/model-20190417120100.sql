/*
ts:2019-04-17 12:01:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = '1',
  `value_test`  = '1',
  `value_stage` = '1',
  `value_live`  = '1'
WHERE
  `variable`    = 'shared_footer'
;