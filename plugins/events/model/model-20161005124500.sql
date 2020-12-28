/*
ts:2016-10-05 12:45:00
*/

ALTER IGNORE TABLE `plugin_events_orders_payments`
  ADD COLUMN `credit_card_type` VARCHAR(100) NULL AFTER `paymentgw_info`;

DROP TABLE IF EXISTS `plugin_events_payment_gateways`;

CREATE TABLE IF NOT EXISTS `plugin_events_payment_gateways` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `month_cap` INT(11) NOT NULL,
  `percent_charge` DECIMAL(10,2) NOT NULL,
  `fixed_charge` DECIMAL(10,2) NOT NULL,
  `paymentgw` VARCHAR(20) DEFAULT NULL,
  `created` DATETIME DEFAULT NULL,
  `updated` DATETIME DEFAULT NULL,
  `deleted` TINYINT(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

insert IGNORE into `plugin_events_payment_gateways`(`id`,`month_cap`,`percent_charge`,`fixed_charge`,`paymentgw`,`created`,`updated`,`deleted`) values
  (1,350,'0.00','0.012','realex',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0),
  (2,0,'0.01','0.25','stripe',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0);

DROP TABLE IF EXISTS `plugin_events_payment_gateway_credit_card_types`;

CREATE TABLE IF NOT EXISTS `plugin_events_payment_gateway_credit_card_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `paymentgw_id` INT(11) NOT NULL,
  `month_cap` INT(11) NOT NULL,
  `percent_charge` DECIMAL(10,3) NOT NULL,
  `fixed_charge` DECIMAL(10,2) NOT NULL,
  `credit_card_type` VARCHAR(100) NULL,
  `created` DATETIME DEFAULT NULL,
  `updated` DATETIME DEFAULT NULL,
  `deleted` TINYINT(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

insert ignore into `plugin_events_payment_gateway_credit_card_types`(`id`,`paymentgw_id`,`month_cap`,`percent_charge`,`fixed_charge`,`credit_card_type`,`created`,`updated`,`deleted`) values
  (1,1,0,'0.049','0.00','VISA',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0),
  (2,1,0,'0.049','0.00','MasterCard',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0),
  (3,2,0,'0.049','0.00','VISA',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0),
  (4,2,0,'0.049','0.00','MasterCard',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0);

UPDATE `plugin_reports_reports`
SET
  `sql` = 'SELECT
	CONCAT(
		''<div class="text-center"><h3>Total Profit</h3><span style="font-size: 2em;">'',
		REPLACE(FORMAT(IFNULL(`total`, 0), 2), ",", ""),
		''</span><hr /><a href="/admin/dashboards/view_dashboard/'',
		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = ''Admin'' AND `deleted` = 0),
		''" style="color: #fff;">View Dashboard</a></div>''
	) AS ` `
FROM (
	SELECT SUM(
	    (peop.amount * (1 - COALESCE(pepg.`percent_charge`, 0)) - COALESCE(pepg.`fixed_charge`, 0))
	    * (1 - COALESCE(`pepgcct`.`percent_charge`, 0)) - COALESCE(`pepgcct`.`fixed_charge`, 0)
	) AS total
	FROM `plugin_events_orders_payments` `peop`
	INNER JOIN `plugin_events_payment_gateways` `pepg` ON peop.`paymentgw` = pepg.`paymentgw`
	INNER JOIN `plugin_events_payment_gateway_credit_card_types` `pepgcct` ON peop.`credit_card_type` = `pepgcct`.`credit_card_type`
	INNER JOIN `plugin_events_orders` `order`
	INNER JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
	WHERE  `account`.`owner_id` = @user_id
	AND    `peop`.`status` = ''PAID''
	AND    `peop`.`deleted` = 0
) AS `total`',
  `dashboard` = 1
WHERE
  name = 'Admin Total Profit';