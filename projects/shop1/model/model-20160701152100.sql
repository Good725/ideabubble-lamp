/*
ts:2016-07-01 15:21:00
*/

UPDATE IGNORE `engine_settings`
SET
  `value_live`  = 0,
  `value_stage` = 0,
  `value_test`  = 0,
  `value_dev`   = 0
WHERE
  `variable` = 'gravatar_enabled';