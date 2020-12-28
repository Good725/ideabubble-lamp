/*
ts:2016-07-08 12:31:00
*/

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Total Orders',
  "SELECT
\n	CONCAT(
\n		'<div class=\"text-center\"><h3>Total Orders</h3><span style=\"font-size: 2em;\">',
\n		`count`,
\n		'</span><hr /><a href=\"/admin/dashboards/view_dashboard/',
\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'My Orders Dashboard' AND `deleted` = 0),
\n		'\" style=\"color: #fff;\">View Dashboard</a></div>'
\n	) AS ` `
\nFROM (
\n	SELECT COUNT(*) AS `count`
\n	FROM   `plugin_events_orders` `order`
\n	JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
\n	WHERE  `account`.`owner_id` = @user_id
\n	AND    `order`.`status` = 'PAID'
\n	AND    `order`.`deleted` = 0
\n) AS `counter`",
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Total Tickets',
  "SELECT
\n	CONCAT(
\n		'<div class=\"text-center\"><h3>Total Tickets</h3><span style=\"font-size: 2em;\">',
\n		`total`.`total`,
\n		'</span><hr /><a href=\"/admin/dashboards/view_dashboard/',
\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'My Orders Dashboard' AND `deleted` = 0),
\n		'\" style=\"color: #fff;\">View Dashboard</a></div>'
\n	) as ` `
\nFROM (
\n	SELECT    SUM(`event`.`quantity` - IFNULL(`sold`.`sold`, 0)) AS `total`
\n	FROM      `plugin_events_events` `event`
\n	LEFT JOIN `plugin_events_events_sold` `sold` ON `sold`.`event_id` = `event`.`id`
\n	WHERE `event`.`created_by` = 7
\n	AND   `event`.`deleted`    = 0
\n	AND   `event`.`is_onsale`  = 1
\n) AS `total`;",
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `text_color`, `background_color`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  'Total Orders',
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Total Orders' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'single_value' AND `deleted` = 0 LIMIT 1),
  'rgb(255, 255, 255)', 'rgb(56, 231, 202)',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `text_color`, `background_color`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  'Total Tickets',
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Total Tickets' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'single_value' AND `deleted` = 0 LIMIT 1),
  'rgb(255, 255, 255)', 'rgb(56, 231, 202)',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);


INSERT IGNORE INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Total Revenue',
  "SELECT
\n	CONCAT(
\n		'<div class=\"text-center\"><h3>Total Revenue</h3><span style=\"font-size: 2em;\">',
\n		`total`,
\n		'</span><hr /><a href=\"/admin/dashboards/view_dashboard/',
\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Events Dashboard' AND `deleted` = 0),
\n		'\" style=\"color: #fff;\">View Dashboard</a></div>'
\n	) AS ` `
\nFROM (
\n	SELECT SUM(`order`.`total`) AS `total`
\n	FROM   `plugin_events_orders` `order`
\n	JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
\n	WHERE  `account`.`owner_id` = @user_id
\n	AND    `order`.`status` = 'PAID'
\n	AND    `order`.`deleted` = 0
\n) AS `total`",
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `text_color`, `background_color`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  'Total Revenue',
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Total Revenue' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'single_value' AND `deleted` = 0 LIMIT 1),
  'rgb(255, 255, 255)', 'rgb(56, 231, 202)',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

UPDATE IGNORE `engine_plugins` SET `icon`='fa-calendar' WHERE `name`='events';

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `icon`) VALUES
('events/orders',   'Orders',   '1', 'fa-shopping-cart'),
('events/invoices', 'Invoices', '1', 'fa-file-text');

INSERT IGNORE INTO `engine_plugins_per_role` (`plugin_id`, `role_id`, `enabled`) VALUES
(
  (SELECT `id` FROM `engine_plugins` WHERE `name` = 'events/orders'),
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User' AND `deleted` = 0 LIMIT 1),
  '1'
),
(
  (SELECT `id` FROM `engine_plugins` WHERE `name` = 'events/orders'),
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator' AND `deleted` = 0 LIMIT 1),
  '1'
),
(
  (SELECT `id` FROM `engine_plugins` WHERE `name` = 'events/invoices'),
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Super User' AND `deleted` = 0 LIMIT 1),
  '1'
),
(
  (SELECT `id` FROM `engine_plugins` WHERE `name` = 'events/invoices'),
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator' AND `deleted` = 0 LIMIT 1),
  '1'
);

UPDATE IGNORE `engine_settings` SET `value_live`='utickethq', `value_stage`='utickethq', `value_test`='utickethq', `value_dev`='utickethq' WHERE `variable`='facebook_url';
UPDATE IGNORE `engine_settings` SET `value_live`='uticket',   `value_stage`='uticket',   `value_test`='uticket',   `value_dev`='uticket'   WHERE `variable`='twitter_url';
UPDATE IGNORE `engine_settings` SET `value_live`='utickethq', `value_stage`='utickethq', `value_test`='utickethq', `value_dev`='utickethq' WHERE `variable`='instagram_url';

UPDATE IGNORE `engine_settings` SET `value_live`='&copy; 2016 uTicket - All Rights Reserved', `value_stage`='&copy; 2016 uTicket - All Rights Reserved', `value_test`='&copy; 2016 uTicket - All Rights Reserved', `value_dev`='&copy; 2016 uTicket - All Rights Reserved' WHERE `variable`='company_copyright';
