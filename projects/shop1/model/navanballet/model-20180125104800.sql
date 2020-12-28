/*
ts:2018-01-25 10:48:00
*/

UPDATE `plugin_reports_reports` SET `sql`='SELECT  	ROUND(SUM(`payment_amount`), 2) AS `Money`, 	DATE_FORMAT(`purchase_time`, \'%Y %b\') AS `Date`, 	(SELECT ROUND(SUM(`payment_amount`), 2) FROM `plugin_payments_log` WHERE `paid` = 1) AS `Total` FROM 	`plugin_payments_log` WHERE 	`paid` = 1 AND `purchase_time` IS NOT NULL AND  `purchase_time` BETWEEN {!DASHBOARD-FROM!} and {!DASHBOARD-TO!}   GROUP BY MONTH(`purchase_time`)' WHERE (`name`='Total Sales');
