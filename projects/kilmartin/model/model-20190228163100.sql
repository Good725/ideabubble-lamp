/*
ts:2019-02-28 16:31:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = 1,
  `value_test`  = 1,
  `value_stage` = 1,
  `value_live`  = 1
WHERE
  `variable`     = 'course_rescheduler_enabled'
;