/*
ts:2019-10-07 18:30:00
*/
INSERT INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
 'Course Topics',
 CURRENT_TIMESTAMP,
 CURRENT_TIMESTAMP,
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 '1',
 '0',
 'course_topics',
 'Controller_Frontend_Courses,embed_categories_menu'
);

INSERT INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
 'News category',
 CURRENT_TIMESTAMP,
 CURRENT_TIMESTAMP,
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 '1',
 '0',
 'news_category',
 'Controller_Frontend_News,embed_news_category'
);


/* Add the "course_list2" layout, if it does not already exist */
INSERT IGNORE INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT
  'course_list2',
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
  (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'course_list2' AND `deleted` = 0)
LIMIT 1
;

/* Add the "course_detail2" layout, if it does not already exist */
INSERT IGNORE INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT
  'course_detail2',
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
  (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'course_detail2' AND `deleted` = 0)
LIMIT 1
;

/* Add the "news2" layout, if it does not already exist */
INSERT IGNORE INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT
  'news2',
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
  (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'news2' AND `deleted` = 0)
LIMIT 1
;
