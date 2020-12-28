/*
ts:2020-01-13 16:21:00
*/

UPDATE
  `engine_settings`
SET
  `value_live`  = '1',
  `value_stage` = '1',
  `value_test`  = '1',
  `value_dev`   = '1'
WHERE
  `variable`    = 'show_start_date_for_repeating_timeslots';