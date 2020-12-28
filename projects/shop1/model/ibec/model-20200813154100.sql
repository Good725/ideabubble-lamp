/*
ts:2020-08-13 15:40:00
*/

UPDATE
  `engine_settings`
SET
  `value_live`  = '1',
  `value_stage` = '1',
  `value_test`  = '1',
  `value_dev`   = '1'
WHERE
  `variable` = 'allow_duplicate_bookings_per_student';