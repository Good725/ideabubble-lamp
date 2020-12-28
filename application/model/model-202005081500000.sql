/*
ts:2020-04-27 18:00:00
*/
UPDATE
  `engine_site_themes`
SET
  `date_modified`      = CURRENT_TIMESTAMP,
  `modified_by`        = IFNULL((SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), 1),
  `email_header_color` = '#1d1a3b',
  `email_link_color`   = '#e41395'
WHERE
  `stub` = '51'
;
