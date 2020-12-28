/*
ts:2016-07-05 17:00:00
*/

/* Create the dashboard */
INSERT IGNORE INTO `plugin_dashboards` (`title`, `columns`, `date_filter`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (
  'My Orders Dashboard',
  '3',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
);


/* create the widgets */

INSERT IGNORE INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES (
  'Upcoming Events',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'calendar'),
  'Title',
  'Date',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);
INSERT IGNORE INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES (
  'Orders',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'table'),
  'Title',
  'Date',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES (
  'Next Event',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'table'),
  'Title',
  'Date',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);


/* Create the reports */
INSERT IGNORE INTO `plugin_reports_reports` (`name`, `sql`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Orders',
  'SELECT\n    DATE_FORMAT(`date`.`starts`, \'<div class=\"text-center text-uppercase\">%d<br />%b</div>\') AS \'Date\',\n    `event`.`name` AS \'Event\',\n    `item`.`quantity` AS \'Tickets\'\nFROM `plugin_events_orders` `order`\nJOIN `plugin_events_orders_items`            `item`        ON `item`       .`order_id`       = `order`      .`id`\nJOIN `plugin_events_events_has_ticket_types` `ticket_type` ON `item`       .`ticket_type_id` = `ticket_type`.`id`\nJOIN `plugin_events_events`                  `event`       ON `ticket_type`.`event_id`       = `event`      .`id`\nJOIN `plugin_events_events_dates`            `date`        ON `date`       .`event_id`       = `event`      .`id`\nWHERE `buyer_id` = @user_id',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Orders' ORDER BY `id` DESC LIMIT 1),
  'sql'
);

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `sql`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Next Event',
  "SELECT
\n	CONCAT(
\n		'<div class=\"text-center\"><span style=\"font-size: 2em;\">',
\n		DATEDIFF(`date`.`starts`, CURRENT_TIMESTAMP),
\n		' DAYS</span><br />till<br /><a href=\"/events/',
\n		`event`.`url`,
\n		'\" style=\"text-decoration: underline;color: blue;\">',
\n		`event`.`name`,
\n		'</a></div>'
	) AS ' '
FROM `plugin_events_events` `event`
JOIN `plugin_events_events_dates` `date` ON `date`.`event_id` = `event`.`id`
WHERE   `event`.`publish` = 1
	AND `event`.`deleted` = 0
	AND `date`.`deleted` = 0
	AND `date`.`starts` > NOW()
ORDER BY `date`.`starts`
LIMIT 1",
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Next Event' ORDER BY `id` DESC LIMIT 1),
  'sql'
);

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `sql`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Upcoming Events',
  "SELECT
\n	`event`.`name` AS `Title`,
\n	`date`.`starts` AS `iso_date`,
\n	DATE_FORMAT(`date`.`starts`, '%d %b') AS `Date`,
\n	CONCAT('/events/', `event`.`url`) AS `Link`
\nFROM `plugin_events_events` `event`
\nJOIN `plugin_events_events_dates` `date` ON `date`.`event_id` = `event`.`id`
\nWHERE   `event`.`publish` = 1
\n	AND `event`.`deleted` = 0
\n	AND `date`.`deleted` = 0
\n	AND `date`.`starts` BETWEEN {!DASHBOARD-FROM!} and {!DASHBOARD-TO!}
\nORDER BY `date`.`starts`",
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Upcoming Events' ORDER BY `id` DESC LIMIT 1),
  'sql'
);

/* Add the widgets to the dashboard */
INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'My Orders Dashboard' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Orders' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' AND `deleted` = 0 LIMIT 1),
  '1',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT INTO `plugin_dashboards_gadgets`  (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'My Orders Dashboard' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Next Event' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' AND `deleted` = 0 LIMIT 1),
  '2',
  '2',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);


INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'My Orders Dashboard' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Upcoming Events' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' AND `deleted` = 0 LIMIT 1),
  '3',
  '3',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

/* Update events dashboard reports to only get data for the logged-in user */
UPDATE IGNORE `plugin_reports_reports`
SET `sql` = "SELECT
\n 	 ROUND(SUM(`payment`.`amount`), 2) AS `Money`,
\n	 DATE_FORMAT(`payment`.`created`, '%Y %b') AS `Date`
\nFROM
\n	 `plugin_events_orders_payments` `payment`
\nJOIN
\n	`plugin_events_orders` `order` ON `payment`.`order_id` = `order`.`id`
\nJOIN
\n	`plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
\nWHERE
\n	 `payment`.`created` IS NOT NULL
\nAND
\n	  `payment`.`created` BETWEEN {!DASHBOARD-FROM!} and {!DASHBOARD-TO!}
\nAND
\n	`account`.`owner_id` = @user_id
\nGROUP BY
\n 	`Date`
\n;"
WHERE `name` = 'Sales by Month'
;

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = "SELECT
\n 	 ROUND(SUM(`payment`.`amount`), 2) AS `Money`,
\n	 DATE_FORMAT(`payment`.`created`, '%a') AS `Date`
\nFROM
\n	 `plugin_events_orders_payments` `payment`
\nJOIN
\n	`plugin_events_orders` `order` ON `payment`.`order_id` = `order`.`id`
\nJOIN
\n	`plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
\nWHERE
\n	 `payment`.`created` IS NOT NULL
\nAND
\n	  `payment`.`created` BETWEEN {!DASHBOARD-FROM!} and {!DASHBOARD-TO!}
\nAND
\n	`account`.`owner_id` = @user_id
\nGROUP BY
\n 	DAYOFWEEK(`payment`.`created`)
\n;"
WHERE `name` = 'Sales by Day'
;

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = "SELECT ROUND(`payment`.`amount`, 2) AS `Money`, `event`.`name` AS `Event`
\nFROM	`plugin_events_orders_payments`         `payment`
\nJOIN	`plugin_events_orders`                  `order`       ON `payment`    .`order_id`       = `order`      .`id`
\nJOIN	`plugin_events_orders_items`            `order_item`  ON `order_item` .`order_id`       = `order`      .`id`
\nJOIN	`plugin_events_events_has_ticket_types` `ticket_type` ON `order_item` .`ticket_type_id` = `ticket_type`.`id`
\nJOIN	`plugin_events_events`                  `event`       ON `ticket_type`.`event_id`       = `event`      .`id`
\nJOIN	`plugin_events_accounts`                `account`     ON `order`      .`account_id`     = `account`    .`id`
\nWHERE   `payment`.`created` IS NOT NULL
\nAND `payment`.`created` BETWEEN {!DASHBOARD-FROM!} and {!DASHBOARD-TO!}
\nAND `account`.`owner_id` = @user_id
\nGROUP BY `ticket_type`.`event_id`;"
WHERE `name` = 'Sales by Event';


UPDATE IGNORE `plugin_reports_reports`
SET `sql` = "SELECT
\n	ROUND(SUM(`payment`.`amount`), 2) AS `Money`,
\n	DATE_FORMAT(`payment`.`created`, '%Y %b') AS `Date`,
\n	(
\n		SELECT ROUND(SUM(`payment`.`amount`), 2)
\n		FROM `plugin_events_orders_payments` `payment`
\n		JOIN `plugin_events_orders` `order` ON `payment`.`order_id` = `order`.`id`
\n		JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
\n		WHERE `account`.`owner_id` = @user_id
\n	) AS `Total`
\nFROM
\n	`plugin_events_orders_payments` `payment`
\nJOIN
\n	`plugin_events_orders` `order` ON `payment`.`order_id` = `order`.`id`
\nJOIN
\n	`plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
\nWHERE
\n	`payment`.`created` IS NOT NULL
\nAND 	`payment`.`created` BETWEEN {!DASHBOARD-FROM!} AND {!DASHBOARD-TO!}
\nAND	`account`.`owner_id` = @user_id"
WHERE `name` = 'Total Revenue';