/*
ts:2019-11-28 11:20:00
*/
UPDATE
  `engine_settings`
SET
  `value_dev`   = 0,
  `value_test`  = 0,
  `value_stage` = 0,
  `value_live`  = 0
WHERE
  `variable` = 'bookings_display_booking_warning';
