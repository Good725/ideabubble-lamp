/*
ts:2020-01-27 16:01:00
*/

-- "accident" -> "incident"
UPDATE
  `plugin_pages_pages`
SET
  `name_tag`      = 'report-incident',
  `title`         = 'Report incident',
  `content`       = '<h1>Report an incident</h1><div>{incident_reporter-}</div>',
  `modified_by`   = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP
WHERE
  `name_tag`      = 'report-accident';

-- "accident" -> "incident"
UPDATE
  `plugin_pages_pages`
SET
  `name_tag`      = 'report-incident-thank-you',
  `title`         = 'Thank you',
  `content`       = '<h1>Thank you</h1>\n\n<p>Thank you for your incident report.</p>',
  `modified_by`   = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP
WHERE
  `name_tag`      = 'report-accident-thank-you';


-- Add "report an incident" link to the footer
INSERT INTO `plugin_menus` (`category`, `title`, `link_tag`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`) SELECT
  'footer',
  'Report an incident',
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'report-incident' ORDER BY `id` DESC LIMIT 1),
  `id`,
  '1',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0)
FROM
  `plugin_menus`
WHERE
  `title` = 'Contact Us' AND `category` = 'footer'
;

-- Grant administrators access to the safety plugin
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator'),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'safety')
);
