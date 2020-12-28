/*
ts:2017-07-31 15:40:00
*/

-- Remove deprecated page layout
UPDATE `plugin_pages_layouts` SET `deleted` = 1 WHERE `layout` = 'attendance';

-- Update pages using the "attendance" layout to use the "content" layout and unpublish them
UPDATE
  `plugin_pages_pages`
SET
  `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  `publish` = 0
WHERE
  `layout_id` IN (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'attendance')
;