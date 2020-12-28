/*
ts:2017-03-16 16:11:00
*/

UPDATE `engine_settings`
SET
  `value_live`  = '/course-list.html',
  `value_stage` = '/course-list.html',
  `value_test`  = '/course-list.html',
  `value_dev`   = '/course-list.html'
WHERE
  `variable` = 'cms_heading_button_link';

UPDATE `engine_settings`
SET
  `value_live`  = 'Create Booking',
  `value_stage` = 'Create Booking',
  `value_test`  = 'Create Booking',
  `value_dev`   = 'Create Booking'
WHERE
  `variable` = 'cms_heading_button_text';