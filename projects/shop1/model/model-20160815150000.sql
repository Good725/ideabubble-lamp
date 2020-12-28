/*
ts:2016-08-15 15:00:00
*/
INSERT INTO `plugin_pages_layouts` (`layout`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'home-calendar',
  '0',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);

UPDATE IGNORE `plugin_pages_pages`
SET `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'home')
WHERE `name_tag` IN ('home', 'home.html');