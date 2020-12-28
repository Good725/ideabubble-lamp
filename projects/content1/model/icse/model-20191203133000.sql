/*
ts:2019-12-03 13:30:00
*/
UPDATE
  `engine_settings`
SET
  `value_live`  = 'icse',
  `value_stage` = 'icse',
  `value_test`  = 'icse',
  `value_dev`   = 'icse'
WHERE
  `variable` = 'cms_skin';
