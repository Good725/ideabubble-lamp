/*
ts:2017-03-16 16:11:00
*/

UPDATE `engine_settings`
SET
  `value_live`  = '/admin/events/edit_event/new',
  `value_stage` = '/admin/events/edit_event/new',
  `value_test`  = '/admin/events/edit_event/new',
  `value_dev`   = '/admin/events/edit_event/new'
WHERE
  `variable` = 'cms_heading_button_link';

UPDATE `engine_settings`
SET
  `value_live`  = 'Create Event',
  `value_stage` = 'Create Event',
  `value_test`  = 'Create Event',
  `value_dev`   = 'Create Event'
WHERE
  `variable` = 'cms_heading_button_text';