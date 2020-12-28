/*
ts:2016-07-11 17:00:00
*/

SET @mtee_204_page_id = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` in ('sizeguide', 'sizeguide.html') AND `publish` = 1 AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1);

UPDATE IGNORE `engine_settings`
SET
  `value_live`  = @mtee_204_page_id,
  `value_stage` = @mtee_204_page_id,
  `value_test`  = @mtee_204_page_id,
  `value_dev`   = @mtee_204_page_id
WHERE
  `variable` = 'default_size_guide'
;