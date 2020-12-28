/*
ts:2016-06-07 14:40:00
*/

-- Insert the "terms of use", "support" and "privacy policy" pages, if they do not already exist
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'terms-of-use',
  'Terms of use',
  '<h1>Terms of use</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('terms-of-use', 'terms-of-use.html') AND `deleted` = 0)
LIMIT 1;

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'support',
  'Support',
  '<h1>Support</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('support', 'support.html') AND `deleted` = 0)
LIMIT 1;

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'privacy-policy',
  'Privacy policy',
  '<h1>Privacy policy</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('privacy-policy', 'privacy-policy.html') AND `deleted` = 0)
LIMIT 1;

-- If any of the pages already exist, ensure they are using the correct layout.
UPDATE IGNORE `plugin_pages_pages`
SET `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content')
WHERE `name_tag` IN ('terms-of-use', 'terms-of-use.html', 'support', 'support.html', 'privacy-policy', 'privacy-policy.html');


-- Update password reset email
UPDATE `plugin_messaging_notification_templates`
SET `message`='<p>Hi $name.</p>\n\n<p>Your username is $email</p>\n\n<p>To initiate the password reset process for your uTicket account, please <a href=\"$site_urladmin/login/reset_password_form/$validation\">click here</a>.</p>\n\n<p>If clicking the link above does not work, please copy and paste the full URL below into a new browser window:</p>\n\n<p><a href=\"$site_urladmin/login/reset_password_form/$validation\">$site_urladmin/login/reset_password_form/$validation</a></p>\n\n<p>If you have received this email in error, it is likely that another user entered your email address by mistake, while trying to reset a password. If you did not initiate the request, you do not need to take any further action and can safely disregard his email.</p>\n\n<p>If you experience any difficulties during this process, please contact us at hello@uticket.ie</p>\n\n<p>The uTicket Team</p>'
WHERE `name`='reset_cms_password';
