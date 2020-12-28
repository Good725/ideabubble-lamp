/*
ts:2018-02-06 18:01:00
*/


UPDATE `engine_settings`
SET
  `value_live`  = '/available-results.html',
  `value_stage` = '/available-results.html',
  `value_test`  = '/available-results.html',
  `value_dev`   = '/available-results.html'
WHERE
  `variable` = 'cms_heading_button_link_2';

UPDATE
  `engine_settings`
SET
  `value_live`  = 'Book a Course',
  `value_stage` = 'Book a Course',
  `value_test`  = 'Book a Course',
  `value_dev`   = 'Book a Course'
WHERE
  `variable` = 'cms_heading_button_text_2';