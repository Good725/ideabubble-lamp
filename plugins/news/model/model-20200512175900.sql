/*
ts:2020-05-12 17:59:00
*/

/* Add the "news3" layout, if it does not already exist */
INSERT IGNORE INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT
  'news3',
  (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '04' AND `deleted` = 0),
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
FROM
  `engine_site_templates`
WHERE NOT EXISTS
  (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'news3' AND `deleted` = 0)
LIMIT 1
;

ALTER TABLE `plugin_news` ADD COLUMN `media_type` ENUM('Article', 'Video', 'Podcast', 'Blog') NULL AFTER `author`;

INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `note`, `type`, `group`, `required`, `options`)
VALUES (
  'enable_news_filters',
  'Enable filters',
  'news',
  '0', '0', '0', '0',
  'Enable filters on the news page',
  'toggle_button',
  'News',
  '0',
  'Model_Settings,on_or_off'
);
