/*
ts:2016-10-21 16:40:00
*/

insert ignore into `plugin_events_payment_gateway_credit_card_types`(`id`,`paymentgw_id`,`month_cap`,`percent_charge`,`fixed_charge`,`credit_card_type`,`created`,`updated`,`deleted`) values
	(5,1,0,'0.049','0.00','visa',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0),
	(6,1,0,'0.049','0.00','mc',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0),
	(7,2,0,'0.049','0.00','visa',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0),
	(8,2,0,'0.049','0.00','mc',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0);

UPDATE `plugin_reports_reports`
SET
	`sql` = 'SELECT
	CONCAT(
		''<div class="text-center"><h3>Total Profit</h3><span style="font-size: 2em;">'',
		REPLACE(FORMAT(`total` / 4, 2), ",", ""),
		''</span><hr /><a href="/admin/dashboards/view_dashboard/'',
		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = ''Admin'' AND `deleted` = 0),
		''" style="color: #fff;">View Dashboard</a></div>''
	) AS ` `
FROM (
	SELECT SUM(
	  COALESCE(`peop`.`amount`, 0)
          * (1 - COALESCE(pepg.`percent_charge`, 0)) - COALESCE(pepg.`fixed_charge`, 0)
	  * (1 - COALESCE(`pepgcct`.`percent_charge`, 0)) - COALESCE(`pepgcct`.`fixed_charge`, 0)
	) AS total
	FROM `plugin_events_orders_payments` `peop`
	INNER JOIN `plugin_events_payment_gateways` `pepg` ON peop.`paymentgw` = pepg.`paymentgw`
	LEFT  JOIN `plugin_events_payment_gateway_credit_card_types` `pepgcct` ON peop.`credit_card_type` = `pepgcct`.`credit_card_type`
	INNER JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id`
	INNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
	WHERE  `account`.`owner_id` = @user_id
	AND    `peop`.`status` = ''PAID''
	AND    `peop`.`deleted` = 0
) AS `total`',
	`dashboard` = 1
WHERE
	name = 'Admin Total Profit';

UPDATE `plugin_reports_reports`
SET
	`sql` = 'SELECT
	CONCAT(
		''<div class="text-center"><h3>Total Revenue</h3><span style="font-size: 2em;">'',
		REPLACE(FORMAT(`total`, 2), ",", ""),
		''</span><hr /><a href="/admin/dashboards/view_dashboard/'',
		(SELECT IFNULL(`id`, '''') FROM `plugin_dashboards` WHERE `title` = ''My Sales'' AND `deleted` = 0),
		''" style="color: #fff;">View Dashboard</a></div>''
	) AS ` `
FROM (
	SELECT
	   SUM( COALESCE(`peop`.`amount`, 0) )
	AS `total`
	FROM `plugin_events_orders_payments` `peop`
	LEFT JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id`
	INNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
	WHERE  `account`.`owner_id` = @user_id
	AND    `peop`.`status` = ''PAID''
	AND    `order`.`deleted` = 0
) AS `total`'
WHERE
	name = 'Total Revenue';

UPDATE `plugin_reports_reports`
SET
	`sql` = 'SELECT
	CONCAT(
		''<div class="text-center"><h3>Total Revenue</h3><span style="font-size: 2em;">'',
		REPLACE(FORMAT(`total`, 2), ",", ""),
		''</span><hr /><a href="/admin/dashboards/view_dashboard/'',
		(SELECT IFNULL(`id`, '''') FROM `plugin_dashboards` WHERE `title` = ''My Sales'' AND `deleted` = 0),
		''" style="color: #fff;">View Dashboard</a></div>''
	) AS ` `
FROM (
	SELECT
	   SUM( COALESCE(`peop`.`amount`, 0) )
	AS `total`
	FROM `plugin_events_orders_payments` `peop`
	LEFT JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id`
	INNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
	WHERE  `account`.`owner_id` = @user_id
	AND    `peop`.`status` = ''PAID''
	AND    `order`.`deleted` = 0
	AND `order`.`created` BETWEEN {!DASHBOARD-FROM!} AND {!DASHBOARD-TO!}
) AS `total`'
WHERE
	name = 'Admin Total Revenue';