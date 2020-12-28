/*
ts:2017-05-22 15:30:00
*/

UPDATE
  `engine_settings`
SET
  `value_stage` = '1',
  `value_test`  = '1',
  `value_dev`   = '1'
WHERE
  `variable` IN ('frontend_login_link', 'account_managed_course_bookings')
;
