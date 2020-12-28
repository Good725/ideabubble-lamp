/*
ts:2020-01-06 08:35:00
*/
UPDATE
  `engine_settings`
SET
  `value_live`  = 0,
  `value_stage` = 0,
  `value_test`  = 0,
  `value_dev`   = 0
WHERE
  `variable` = 'course_enquiry_button';
