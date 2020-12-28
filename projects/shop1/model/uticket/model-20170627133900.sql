/*
ts: 2017-06-27 13:39:00
*/

UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n  IFNULL(COALESCE(`order`.total - `order`.commission_total - `order`.vat_total, 0), 0) AS `Money`, \r\n  `order`.`created` AS \'Date\', \r\n  IFNULL(REPLACE(REPLACE(REPLACE(`peop`.`currency`, \'EUR\', \'€\'), \'USD\', \'$\'), \'GBP\', \'£\'), \'€\') as `currency` \r\nFROM \r\n  `plugin_events_orders_payments` `peop` \r\nLEFT JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id` \r\nINNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` \r\nWHERE `peop`.`status` = \'PAID\' \r\nAND  `account`.`owner_id` = @user_id \r\nAND `order`.`created` BETWEEN {!DASHBOARD-FROM!} AND DATE_ADD({!DASHBOARD-TO!} , INTERVAL 1 DAY)\r\nAND `order`.`deleted` = 0' WHERE (`id`='22');
UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n 	 ROUND(SUM(`payment`.`amount`), 2) AS `Money`, \r\n	 DATE_FORMAT(`payment`.`created`, \'%Y %b\') AS `Date` \r\nFROM \r\n	 `plugin_events_orders_payments` `payment` \r\nJOIN \r\n	`plugin_events_orders` `order` ON `payment`.`order_id` = `order`.`id` \r\nJOIN \r\n	`plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` \r\nWHERE \r\n	 `payment`.`created` IS NOT NULL AND payment.status = \'PAID\'\r\nAND \r\n	  `payment`.`created` BETWEEN {!DASHBOARD-FROM!} and DATE_ADD({!DASHBOARD-TO!}, INTERVAL 1 DAY)\r\nAND \r\n	`account`.`owner_id` = @user_id \r\nGROUP BY \r\n 	`Date` \r\n;' WHERE (`id`='24');
UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n 	 ROUND(SUM(`payment`.`amount`), 2) AS `Money`, \r\n	 DATE_FORMAT(`payment`.`created`, \'%a\') AS `Date` \r\nFROM \r\n	 `plugin_events_orders_payments` `payment` \r\nJOIN \r\n	`plugin_events_orders` `order` ON `payment`.`order_id` = `order`.`id` \r\nJOIN \r\n	`plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` \r\nWHERE \r\n	 `payment`.`created` IS NOT NULL AND payment.status = \'PAID\'\r\nAND \r\n	  `payment`.`created` BETWEEN {!DASHBOARD-FROM!} and DATE_ADD({!DASHBOARD-TO!}, INTERVAL 1 DAY)\r\nAND \r\n	`account`.`owner_id` = @user_id \r\nGROUP BY \r\n 	DAYOFWEEK(`payment`.`created`) \r\n;' WHERE (`id`='25');
UPDATE `plugin_reports_reports` SET `sql`='SELECT ROUND(`payment`.`amount`, 2) AS `Money`, `event`.`name` AS `Event` \r\nFROM	`plugin_events_orders_payments`         `payment` \r\nJOIN	`plugin_events_orders`                  `order`       ON `payment`    .`order_id`       = `order`      .`id` \r\nJOIN	`plugin_events_orders_items`            `order_item`  ON `order_item` .`order_id`       = `order`      .`id` \r\nJOIN	`plugin_events_events_has_ticket_types` `ticket_type` ON `order_item` .`ticket_type_id` = `ticket_type`.`id` \r\nJOIN	`plugin_events_events`                  `event`       ON `ticket_type`.`event_id`       = `event`      .`id` \r\nJOIN	`plugin_events_accounts`                `account`     ON `order`      .`account_id`     = `account`    .`id` \r\nWHERE   `payment`.`created` IS NOT NULL AND payment.status = \'PAID\'\r\nAND `payment`.`created` BETWEEN {!DASHBOARD-FROM!} and DATE_ADD({!DASHBOARD-TO!}, INTERVAL 1 DAY)\r\nAND `account`.`owner_id` = @user_id \r\nGROUP BY `ticket_type`.`event_id`;' WHERE (`id`='26');
UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n      CONCAT(\'<span class=\"calendar-event-name\">\', `event`.`name`, \'</span> <span class=\"calendar-event-qty\">\',  `item`.`quantity`, (case when `item`.`quantity` = 1 THEN \' ticket\' ELSE \' tickets\' END), \'</span>\') AS `Title`, \r\n	`date`.`starts` AS `iso_date`, \r\n	DATE_FORMAT(`date`.`starts`, \'%e %M %Y\') AS `Date`, \r\n	`event`.`url` AS `Link` \r\nFROM `plugin_events_orders` `order` \r\nJOIN `plugin_events_orders_items`            `item`        ON `item`       .`order_id`       = `order`      .`id` \r\nJOIN `plugin_events_events_has_ticket_types` `ticket_type` ON `item`       .`ticket_type_id` = `ticket_type`.`id` \r\nJOIN `plugin_events_events`                  `event`       ON `ticket_type`.`event_id`       = `event`      .`id` \r\nJOIN `plugin_events_events_dates`            `date`        ON `date`       .`event_id`       = `event`      .`id` \r\nWHERE event.deleted = 0 AND `order`.deleted = 0 AND `order`.`status` = \'PAID\' AND `buyer_id` = @user_id\r\nORDER BY `order`.created' WHERE (`id`='28');
UPDATE `plugin_reports_reports` SET `sql`='SELECT `e`.`name` AS `Title`, `d`.`starts` AS `iso_date`, DATE_FORMAT(`d`.`starts`, \'%e %M %Y\') AS `Date`, CONCAT(\'/event/\', `e`.`url`) AS `Link`  \r\n	FROM plugin_events_orders o \r\n		INNER JOIN plugin_events_orders_items i ON o.id = i.order_id \r\n		INNER JOIN plugin_events_orders_items_has_dates hd ON i.id = hd.order_item_id \r\n		INNER JOIN plugin_events_events_dates d ON hd.date_id = d.id \r\n		INNER JOIN plugin_events_events e ON d.event_id = e.id \r\n	WHERE e.deleted = 0 AND o.deleted = 0 AND o.`status` = \'PAID\' AND \r\n		d.`starts` >= NOW() AND o.buyer_id = @user_id\r\n  ORDER BY `d`.`starts`' WHERE (`id`='30');
UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n	CONCAT( \r\n		\'<div class=\"text-center\"><h3>Total Tickets Sold</h3><span style=\"font-size: 2em;\">\', \r\n		`count`, \r\n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \r\n		(SELECT ifnull(`id`, \'\') FROM `plugin_dashboards` WHERE `title` = \'My Orders\' AND `deleted` = 0), \r\n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \r\n	) AS ` ` \r\nFROM ( \r\n	SELECT COUNT(*) AS `count` \r\nFROM plugin_events_orders o \r\nINNER JOIN plugin_events_orders_items i ON o.id = i.order_id \r\nINNER JOIN plugin_events_orders_items_has_dates d ON i.id = d.order_item_id \r\nINNER JOIN plugin_events_orders_tickets t ON d.id = t.order_item_has_date_id \r\nINNER JOIN plugin_events_events_has_ticket_types ht on i.ticket_type_id = ht.id\r\nINNER JOIN plugin_events_events e ON ht.event_id = e.id\r\nWHERE o.deleted = 0 AND o.`status` = \'PAID\' AND t.deleted = 0 AND e.deleted = 0 AND `o`.`created` BETWEEN \'{!DASHBOARD-FROM!}\' AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) AND `e`.`owned_by` = @user_id \r\n) AS `counter`' WHERE (`id`='31');
UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n	CONCAT( \r\n		\'<div class=\"text-center\"><h3>Total Sales</h3><span style=\"font-size: 2em;\">&euro;\', \r\n		ifnull(`total`, 0), \r\n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \r\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \r\n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \r\n	) AS ` ` \r\nFROM ( \r\n	SELECT SUM(`order`.`total`) AS `total` \r\n	FROM   `plugin_events_orders` `order` \r\n	JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` \r\n	AND    `order`.`status` = \'PAID\' \r\n	AND    `order`.`deleted` = 0 \r\n        WHERE `order`.`created` BETWEEN {!DASHBOARD-FROM!} AND DATE_ADD({!DASHBOARD-TO!}, INTERVAL 1 DAY)\r\n) AS `total`' WHERE (`id`='34');
UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n	CONCAT( \r\n		\'<div class=\"text-center\"><h3>Total Live Events</h3><span style=\"font-size: 2em;\">\', \r\n		ifnull(`total`, 0), \r\n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \r\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \r\n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \r\n	) AS ` ` \r\nFROM ( \r\n	SELECT COUNT(*) AS total FROM plugin_events_events e WHERE e.deleted = 0 AND e.status = \'Live\' AND `e`.`date_created` BETWEEN \'{!DASHBOARD-FROM!}\' AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY)\r\n) AS `total`' WHERE (`id`='35');
UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n	CONCAT( \r\n		\'<div class=\"text-center\"><h3>Total Tickets</h3><span style=\"font-size: 2em;\">\', \r\n		ifnull(`total`, 0), \r\n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \r\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \r\n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \r\n	) AS ` ` \r\nFROM ( \r\nSELECT COUNT(*) AS total \r\nFROM plugin_events_orders o \r\nINNER JOIN plugin_events_orders_items i ON o.id = i.order_id \r\nINNER JOIN plugin_events_orders_items_has_dates d ON i.id = d.order_item_id \r\nINNER JOIN plugin_events_orders_tickets t ON d.id = t.order_item_has_date_id \r\nINNER JOIN plugin_events_events_has_ticket_types ht on i.ticket_type_id = ht.id\r\nINNER JOIN plugin_events_events e ON ht.event_id = e.id\r\nWHERE o.deleted = 0 AND o.`status` = \'PAID\' AND t.deleted = 0 AND e.deleted = 0 AND `o`.`created` BETWEEN \'{!DASHBOARD-FROM!}\' AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY)\r\n) AS `total`' WHERE (`id`='36');
UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n	CONCAT( \r\n		\'<div class=\"text-center\"><h3>Total Profit</h3><span style=\"font-size: 2em;\">\', \r\n		`total`, \r\n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \r\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \r\n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \r\n	) AS ` ` FROM \r\n(\r\nSELECT \r\n	CONCAT_WS(\r\n		\'\', \r\n		(SELECT \r\n			CONCAT(c.symbol, ROUND(SUM(o.commission_total - IFNULL(p.paymentgw_fee, 0)), 2)) AS profit\r\n			FROM plugin_events_orders o\r\n				INNER JOIN plugin_events_orders_payments p ON o.id = p.order_id\r\n				INNER JOIN plugin_currency_currencies c ON p.currency = c.currency\r\n			WHERE o.deleted = 0 AND p.deleted = 0 AND p.`status` = \'PAID\' AND o.`status` = \'PAID\' AND o.total > 0 AND `o`.`created` BETWEEN {!DASHBOARD-FROM!} AND DATE_ADD({!DASHBOARD-TO!}, INTERVAL 1 DAY)\r\n			AND c.currency = \'EUR\'),\r\n		\' \',\r\n		(SELECT \r\n			CONCAT(c.symbol, ROUND(SUM(o.commission_total - IFNULL(p.paymentgw_fee, 0)), 2)) AS profit\r\n			FROM plugin_events_orders o\r\n				INNER JOIN plugin_events_orders_payments p ON o.id = p.order_id\r\n				INNER JOIN plugin_currency_currencies c ON p.currency = c.currency\r\n			WHERE o.deleted = 0 AND p.deleted = 0 AND p.`status` = \'PAID\' AND o.`status` = \'PAID\' AND o.total > 0 AND `o`.`created` BETWEEN {!DASHBOARD-FROM!} AND DATE_ADD({!DASHBOARD-TO!}, INTERVAL 1 DAY)\r\n			AND c.currency = \'GBP\')\r\n	) AS `total`\r\n) AS `total`' WHERE (`id`='37');
UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n	CONCAT( \r\n		\'<div class=\"text-center\"><h3>Booking Fee Total</h3><span style=\"font-size: 2em;\">&euro;\', \r\n		ifnull(`total`, 0), \r\n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \r\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \r\n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \r\n	) AS ` ` \r\nFROM ( \r\n	SELECT SUM(`order`.`commission_total`) AS `total` \r\n	FROM   `plugin_events_orders` `order` \r\n	JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` \r\n	AND    `order`.`status` = \'PAID\' \r\n	AND    `order`.`deleted` = 0 \r\n        WHERE `order`.`created` BETWEEN {!DASHBOARD-FROM!} AND DATE_ADD({!DASHBOARD-TO!}, INTERVAL 1 DAY)\r\n) AS `total`' WHERE (`id`='38');
UPDATE `plugin_reports_reports` SET `sql`='SELECT COUNT(DISTINCT e.id) AS qty FROM plugin_events_events e INNER JOIN plugin_events_events_dates d ON e.id = d.event_id WHERE e.deleted = 0 AND e.`status` = \'Live\' AND (d.`ends` >= NOW() OR d.`starts` >= NOW()) AND e.owned_by = @user_id' WHERE (`id`='45');
UPDATE `plugin_reports_reports` SET `widget_sql`='SELECT COUNT(DISTINCT e.id) AS qty FROM plugin_events_events e INNER JOIN plugin_events_events_dates d ON e.id = d.event_id WHERE e.deleted = 0 AND e.`status` = \'Live\' AND (d.`ends` >= NOW() OR d.`starts` >= NOW()) AND e.owned_by = @user_id' WHERE (`id`='45');

