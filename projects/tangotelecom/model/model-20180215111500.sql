/*
ts:2018-02-15 11:15:00
*/

INSERT INTO `plugin_pages_layouts` (`layout`) VALUES ('content-newscategories');

UPDATE
  `plugin_pages_pages`
SET
  `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content-newscategories')
WHERE
  `name_tag` IN ('news', 'news.html')
;
