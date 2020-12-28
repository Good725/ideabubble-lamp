/*
ts:2019-03-21 11:25:00
*/

/* Add the "pay online" layout, if it does not already exist */
INSERT IGNORE INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT
  'pay_online',
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
  (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'pay_online' AND `deleted` = 0)
LIMIT 1
;