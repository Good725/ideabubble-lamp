/*
ts:2016-08-09 15:40:00
*/

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Profit</h3><span style=\"font-size: 2em;\">\', \n		ifnull(`total`,0), \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT 0 AS total \n) AS `total`'
WHERE `name` = 'Admin Total Profit'
;

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Tickets</h3><span style=\"font-size: 2em;\">\', \n		ifnull(`total`, 0), \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT COUNT(*) AS total FROM plugin_events_orders o INNER JOIN plugin_events_orders_items i ON o.id = i.order_id INNER JOIN plugin_events_orders_items_has_dates d ON i.id = d.order_item_id INNER JOIN plugin_events_orders_tickets t ON d.id = t.order_item_has_date_id WHERE o.deleted = 0 AND o.`status` = \'PAID\' AND t.deleted = 0 AND `o`.`created` BETWEEN \'{!DASHBOARD-FROM!}\' AND \'{!DASHBOARD-TO!}\'\n) AS `total`'
WHERE `name` = 'Admin Total Tickets'
;


UPDATE IGNORE `plugin_reports_reports`
SET `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Revenue</h3><span style=\"font-size: 2em;\">\', \n		ifnull(`total`, 0), \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT SUM(`order`.`total`) AS `total` \n	FROM   `plugin_events_orders` `order` \n	JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` \n	AND    `order`.`status` = \'PAID\' \n	AND    `order`.`deleted` = 0 \n        WHERE `order`.`created` BETWEEN {!DASHBOARD-FROM!} AND {!DASHBOARD-TO!}\n) AS `total`'
WHERE `name` = 'Admin Total Revenue'
;

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Events</h3><span style=\"font-size: 2em;\">\', \n		ifnull(`total`, 0), \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT COUNT(*) AS total FROM plugin_events_events e WHERE e.deleted = 0 AND `e`.`date_created` BETWEEN \'{!DASHBOARD-FROM!}\' AND \'{!DASHBOARD-TO!}\'\n) AS `total`'
WHERE `name` = 'Admin Total Events'
;

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Booking Fee Total</h3><span style=\"font-size: 2em;\">\', \n		ifnull(`total`, 0), \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT SUM(`order`.`commission_total`) AS `total` \n	FROM   `plugin_events_orders` `order` \n	JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` \n	AND    `order`.`status` = \'PAID\' \n	AND    `order`.`deleted` = 0 \n        WHERE `order`.`created` BETWEEN {!DASHBOARD-FROM!} AND {!DASHBOARD-TO!}\n) AS `total`'
WHERE `name` = 'Admin Booking Fee Total'
;