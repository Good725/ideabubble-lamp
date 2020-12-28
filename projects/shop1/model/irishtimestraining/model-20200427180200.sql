/*
ts:2020-04-27 18:01:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Types',
  `value_test`  = 'Types',
  `value_live`  = 'Types',
  `value_stage` = 'Types'
WHERE
  `variable` = 'search_category_label_course_categories'/*1.1*/;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Topics',
  `value_test`  = 'Topics',
  `value_live`  = 'Topics',
  `value_stage` = 'Topics'
WHERE
  `variable` = 'search_category_label_subjects'/*1.1*/;
