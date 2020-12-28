/*
ts:2017-03-16 16:11:00
*/

UPDATE  `engine_settings`
SET
  `value_live`  = '/admin/bookings',
  `value_stage` = '/admin/bookings',
  `value_test`  = '/admin/bookings',
  `value_dev`   = '/admin/bookings'
WHERE
  `variable` = 'cms_heading_button_link';

UPDATE  `engine_settings`
SET
  `value_live`  = 'Create Course',
  `value_stage` = 'Create Course',
  `value_test`  = 'Create Course',
  `value_dev`   = 'Create Course'
WHERE
  `variable` = 'cms_heading_button_text';