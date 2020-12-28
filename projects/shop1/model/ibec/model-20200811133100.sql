/*
ts:2020-08-11 13:31:00
*/

UPDATE `engine_settings`
SET
  `value_dev`   = 1,
  `value_test`  = 1,
  `value_stage` = 1,
  `value_live`  = 1
WHERE
  `variable` = 'only_display_navision_courses';
