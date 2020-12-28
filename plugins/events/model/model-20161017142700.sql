/*
ts:2016-10-17 14:27:00
*/

UPDATE `plugin_reports_reports`
SET
	`sql` = 'SELECT CONCAT( ''<div class="text-center"><h3>Total Revenue</h3><span style="font-size: 2em;">'', `currency`, ROUND(`total`, 2), ''</span><hr /><a href="/admin/dashboards/view_dashboard/'', (SELECT IFNULL(`id`, '''') FROM `plugin_dashboards` WHERE `title` = ''My Sales'' AND `deleted` = 0), ''" style="color: #fff;">View Dashboard</a></div>'' ) AS ` ` FROM ( SELECT SUM( COALESCE(`peop`.`amount`, 0) ) AS `total`, CASE WHEN `peop`.`currency` = ''EUR'' THEN ''&euro;'' ELSE ''&pound;'' END AS currency FROM `plugin_events_orders_payments` `peop` LEFT JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id` INNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` WHERE  `account`.`owner_id` = @user_id AND    `peop`.`status` = ''PAID'' AND    `order`.`deleted` = 0 ) AS `total`'
WHERE
	name = 'Total Revenue';

UPDATE `plugin_reports_reports`
SET
	`sql` = 'SELECT CONCAT( ''<div class="text-center"><h3>Total Revenue</h3><span style="font-size: 2em;">'', `currency`, ROUND(`total`, 2), ''</span><hr /><a href="/admin/dashboards/view_dashboard/'', (SELECT IFNULL(`id`, '''') FROM `plugin_dashboards` WHERE `title` = ''My Sales'' AND `deleted` = 0), ''" style="color: #fff;">View Dashboard</a></div>'' ) AS ` ` FROM ( SELECT SUM( COALESCE(`peop`.`amount`, 0) ) AS `total`, CASE WHEN `peop`.`currency` = ''GBP'' THEN ''&pound;'' ELSE ''&euro;'' END AS currency FROM `plugin_events_orders_payments` `peop` LEFT JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id` INNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` WHERE `account`.`owner_id` = @user_id AND `peop`.`status` = ''PAID'' AND `order`.`deleted` = 0 AND `order`.`created` BETWEEN ''{!DASHBOARD-FROM!}'' AND ''{!DASHBOARD-TO!}'' ) AS `total`'
WHERE
	name = 'Admin Total Revenue';

UPDATE `plugin_reports_reports`
SET
	`sql` = 'SELECT CONCAT( ''<div class="text-center"><h3>Total Profit</h3><span style="font-size: 2em;">'', `currency`, ROUND(`total` / 4, 2), ''</span><hr /><a href="/admin/dashboards/view_dashboard/'', (SELECT `id` FROM `plugin_dashboards` WHERE `title` = ''Admin'' AND `deleted` = 0), ''" style="color: #fff;">View Dashboard</a></div>'' ) AS ` ` FROM ( SELECT SUM( COALESCE(`peop`.`amount`, 0) * (1 - COALESCE(pepg.`percent_charge`, 0)) - COALESCE(pepg.`fixed_charge`, 0) * (1 - COALESCE(`pepgcct`.`percent_charge`, 0)) - COALESCE(`pepgcct`.`fixed_charge`, 0) ) AS total, CASE WHEN `peop`.`currency` = ''GBP'' THEN ''&pound;'' ELSE ''&euro;'' END AS currency FROM `plugin_events_orders_payments` `peop` INNER JOIN `plugin_events_payment_gateways` `pepg` ON peop.`paymentgw` = pepg.`paymentgw` LEFT  JOIN `plugin_events_payment_gateway_credit_card_types` `pepgcct` ON peop.`credit_card_type` = `pepgcct`.`credit_card_type` INNER JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id` INNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` WHERE `peop`.`status` = ''PAID'' AND `peop`.`deleted` = 0  AND `peop`.`created` BETWEEN ''{!DASHBOARD-FROM!}'' AND ''{!DASHBOARD-TO!}'') AS `total`'
WHERE
	name = 'Admin Total Profit';

INSERT IGNORE INTO `plugin_reports_reports`
SET
	`name` ='Total Profit',
	`summary` = '',
	`sql` = 'SELECT CONCAT( ''<div class="text-center"><h3>Total Profit</h3><span style="font-size: 2em;">'', `currency`, ROUND(`total` / 4, 2), ''</span><hr /><a href="/admin/dashboards/view_dashboard/'', (SELECT `id` FROM `plugin_dashboards` WHERE `title` = ''Admin'' AND `deleted` = 0), ''" style="color: #fff;">View Dashboard</a></div>'' ) AS ` ` FROM ( SELECT SUM( COALESCE(`peop`.`amount`, 0) * (1 - COALESCE(pepg.`percent_charge`, 0)) - COALESCE(pepg.`fixed_charge`, 0) * (1 - COALESCE(`pepgcct`.`percent_charge`, 0)) - COALESCE(`pepgcct`.`fixed_charge`, 0) ) AS total, CASE WHEN `peop`.`currency` = ''GBP'' THEN ''&pound;'' ELSE ''&euro;'' END AS currency FROM `plugin_events_orders_payments` `peop` INNER JOIN `plugin_events_payment_gateways` `pepg` ON peop.`paymentgw` = pepg.`paymentgw` LEFT  JOIN `plugin_events_payment_gateway_credit_card_types` `pepgcct` ON peop.`credit_card_type` = `pepgcct`.`credit_card_type` INNER JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id` INNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` WHERE `account`.`owner_id` = @user_id AND `peop`.`status` = ''PAID'' AND `peop`.`deleted` = 0 ) AS `total`',
	`widget_sql` = '',
	`date_created` = NOW(),
	`date_modified` = NOW(),
	`publish` = 1,
	`delete` = 0,
	`report_type` = 'sql';