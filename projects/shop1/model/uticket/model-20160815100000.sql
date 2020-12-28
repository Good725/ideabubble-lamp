/*
ts:2016-08-15 10:00:00
*/


-- Rename dashboards
UPDATE IGNORE `plugin_dashboards` SET
  `title`='My Orders',
  `date_modified` = NOW(),
  `modified_by` = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE `title`='My Orders Dashboard';

UPDATE IGNORE `plugin_dashboards` SET
  `title`='My Sales',
  `date_modified` = NOW(),
  `modified_by` = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE `title`='Events Dashboard';

-- Update references
UPDATE IGNORE `plugin_reports_reports` SET
  `sql` = "SELECT
\n	CONCAT(
\n		'<div class=\"text-center\"><h3>Total Orders</h3><span style=\"font-size: 2em;\">',
\n		`count`,
\n		'</span><hr /><a href=\"/admin/dashboards/view_dashboard/',
\n		(SELECT ifnull(`id`, '') FROM `plugin_dashboards` WHERE `title` = 'My Orders' AND `deleted` = 0),
\n		'\" style=\"color: #fff;\">View Dashboard</a></div>'
\n	) AS ` `
\nFROM (
\n	SELECT COUNT(*) AS `count`
\n	FROM   `plugin_events_orders` `order`
\n	JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
\n	WHERE  `account`.`owner_id` = @user_id
\n	AND    `order`.`status` = 'PAID'
\n	AND    `order`.`deleted` = 0
\n) AS `counter`"
WHERE `name` = 'Total Orders';

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = "SELECT
\n	CONCAT(
\n		'<div class=\"text-center\"><h3>Total Tickets</h3><span style=\"font-size: 2em;\">',
\n		`total`.`total`,
\n		'</span><hr /><a href=\"/admin/dashboards/view_dashboard/',
\n		(SELECT IFNULL(`id`, '') FROM `plugin_dashboards` WHERE `title` = 'My Orders' AND `deleted` = 0),
\n		'\" style=\"color: #fff;\">View Dashboard</a></div>'
\n	) as ` `
\nFROM (
\n	SELECT    IFNULL(SUM(`event`.`quantity` - IFNULL(`sold`.`sold`, 0)),0) AS `total`
\n	FROM      `plugin_events_events` `event`
\n	LEFT JOIN `plugin_events_events_sold` `sold` ON `sold`.`event_id` = `event`.`id`
\n	WHERE `event`.`owned_by` = @user_id
\n	AND   `event`.`deleted`    = 0
\n	AND   `event`.`is_onsale`  = 1
\n) AS `total`;"
WHERE `name` = 'Total Tickets';

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = "SELECT
\n	CONCAT(
\n		'<div class=\"text-center\"><h3>Total Revenue</h3><span style=\"font-size: 2em;\">',
\n		`total`,
\n		'</span><hr /><a href=\"/admin/dashboards/view_dashboard/',
\n		(SELECT IFNULL(`id`, '') FROM `plugin_dashboards` WHERE `title` = 'My Sales' AND `deleted` = 0),
\n		'\" style=\"color: #fff;\">View Dashboard</a></div>'
\n	) AS ` `
\nFROM (
\n	SELECT IFNULL(SUM(`order`.`total`),0) AS `total`
\n	FROM   `plugin_events_orders` `order`
\n	JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
\n	WHERE  `account`.`owner_id` = @user_id
\n	AND    `order`.`status` = 'PAID'
\n	AND    `order`.`deleted` = 0
\n) AS `total`"
WHERE `name` = 'Total Revenue';
