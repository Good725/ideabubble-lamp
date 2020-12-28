/*
ts:2016-09-05 15:36:00
*/
/* Update existing Twitter panel to use the short tag */
UPDATE `plugin_panels` SET `text`='<p>{twitter_feed-BreacEolas}</p>' WHERE `title`='Twitter';

/* If the panel doesn't already exist, create it */
INSERT IGNORE INTO `plugin_panels`
  (`title`, `position`, `order_no`, `type_id`, `image`, `text`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
SELECT
  'Twitter',
  'content_left',
  '0',
  `id`,
  '0',
  '<p>{twitter_feed-BreacEolas}</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
FROM `plugin_panels_types`
WHERE `name` = 'static'
AND NOT EXISTS (SELECT `id` FROM `plugin_panels` WHERE `title` = 'Twitter' AND `deleted` = 0);
