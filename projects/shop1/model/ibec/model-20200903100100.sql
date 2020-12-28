/*
ts:2020-09-03 10:01:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = '<div class="addthis_toolbox" style="display: none;"></div>',
  `value_test`  = '<div class="addthis_toolbox" style="display: none;"></div>',
  `value_stage` = '<div class="addthis_toolbox" style="display: none;"></div>',
  `value_live`  = '<div class="addthis_toolbox" style="display: none;"></div>'
WHERE
  `variable` = 'addthis_toolbox_html';

UPDATE
  `engine_settings`
SET
  `value_dev`   = '1',
  `value_test`  = '1',
  `value_stage` = '1',
  `value_live`  = '1'
WHERE
  `variable` IN ('auto_addthis_on_news', 'auto_addthis_on_pages');