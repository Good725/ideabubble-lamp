/*
ts:2016-07-05 12:00:00
*/

UPDATE IGNORE
  `engine_settings`
SET
  `value_live`  = 1,
  `value_stage` = 1,
  `value_test`  = 1,
  `value_dev`   = 1
WHERE
  `variable` = 'display_feedback_form'
;