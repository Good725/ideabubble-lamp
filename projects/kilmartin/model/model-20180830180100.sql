/*
ts:2018-08-30 18:01:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = 1,
  `value_test`  = 1,
  `value_stage` = 1,
  `value_live`  = 1
WHERE
  `variable` = 'checkout_student_year_dropdown'
;