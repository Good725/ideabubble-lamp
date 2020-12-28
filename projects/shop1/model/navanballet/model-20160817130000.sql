/*
ts:2016-08-15 10:00:00
*/

UPDATE `engine_settings` SET `value_live`='modern', `value_stage`='modern', `value_test`='modern', `value_dev`='modern' WHERE `variable`='cms_template';
UPDATE `engine_settings` SET `value_live`='pink',   `value_stage`='pink',   `value_test`='pink',   `value_dev`='pink'   WHERE `variable`='cms_skin';

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`) VALUES
(
  'support',
  'Support',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
),
(
  'terms-of-use',
  'Terms of use',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
),
(
  'privacy-policy',
  'Privacy Policy',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
);

INSERT IGNORE INTO `plugin_menus` (`category`, `title`, `link_tag`, `date_modified`, `date_entered`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'login-form-links',
  'Terms of use',
  (SELECT IFNULL(`id`, '') FROM `plugin_pages_pages` WHERE `name_tag` = 'terms-of-use' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  1,
  0
),
(
  'login-form-links',
  'Support',
  (SELECT IFNULL(`id`, '') FROM `plugin_pages_pages` WHERE `name_tag` = 'support' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  1,
  0
),
(
  'login-form-links',
  'Privacy Policy',
  (SELECT IFNULL(`id`, '') FROM `plugin_pages_pages` WHERE `name_tag` = 'privacy-policy' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  1,
  0
);
