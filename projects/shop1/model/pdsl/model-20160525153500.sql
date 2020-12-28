/*
ts:2015-05-25 15:35:00
*/

UPDATE IGNORE `engine_settings` SET
  `value_live` = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('book-now', 'book-now.html') LIMIT 1),
  `value_stage`= (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('book-now', 'book-now.html') LIMIT 1),
  `value_test` = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('book-now', 'book-now.html') LIMIT 1),
  `value_dev`  = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('book-now', 'book-now.html') LIMIT 1)
WHERE `variable` = 'need_help_page';

UPDATE IGNORE `engine_settings` SET
  `value_live`  = 1,
  `value_stage` = 1,
  `value_test`  = 1,
  `value_dev`   = 1
WHERE `variable` = 'show_need_help_button';