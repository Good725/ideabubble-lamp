/*
ts:2016-08-05 17:33:00
*/

UPDATE plugin_pages_pages
  SET data_helper_call = 'Model_Event::home_page_helper'
  WHERE `title` = 'Home' AND  `deleted` = 0;

UPDATE `engine_settings` SET
`value_live`  = '<h2>Welcome to uTicket</h2>  <p>Selling tickets made simple!</p> ',
`value_stage` = '<h2>Welcome to uTicket</h2>  <p>Selling tickets made simple!</p> ',
`value_test`  = '<h2>Welcome to uTicket</h2>  <p>Selling tickets made simple!</p> ',
`value_dev`   = '<h2>Welcome to uTicket</h2>  <p>Selling tickets made simple!</p> '
WHERE `variable`='dashboard_welcome_text';

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = "SELECT
\n	CONCAT(
\n		'<div class=\"text-center\"><h3>Total Revenue</h3><span style=\"font-size: 2em;\">',
\n		`total`,
\n		'</span><hr /><a href=\"/admin/dashboards/view_dashboard/',
\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Events Dashboard' AND `deleted` = 0),
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

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = "SELECT
\n	CONCAT(
\n		'<div class=\"text-center\"><h3>Total Tickets</h3><span style=\"font-size: 2em;\">',
\n		`total`.`total`,
\n		'</span><hr /><a href=\"/admin/dashboards/view_dashboard/',
\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'My Orders Dashboard' AND `deleted` = 0),
\n		'\" style=\"color: #fff;\">View Dashboard</a></div>'
\n	) as ` `
\nFROM (
\n	SELECT    IFNULL(SUM(`event`.`quantity` - IFNULL(`sold`.`sold`, 0)),0) AS `total`
\n	FROM      `plugin_events_events` `event`
\n	LEFT JOIN `plugin_events_events_sold` `sold` ON `sold`.`event_id` = `event`.`id`
\n	WHERE `event`.`created_by` = 7
\n	AND   `event`.`deleted`    = 0
\n	AND   `event`.`is_onsale`  = 1
\n) AS `total`;"
WHERE `name` = 'Total Tickets';