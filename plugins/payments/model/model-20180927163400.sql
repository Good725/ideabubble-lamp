/*
ts:2018-09-27 16:34:00
*/

INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Recent Payments Realex'), 'date', 'From');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Recent Payments Realex'), 'date', 'To');
UPDATE plugin_reports_reports SET autoload=0 WHERE name='Recent Payments Realex';
UPDATE `plugin_reports_reports` SET `sql`='SELECT\r\n	`customer_name` AS `Customer`,\r\n`purchase_time` AS `Purchase`,\r\n(\r\n		CASE\r\n		WHEN paid = 1 THEN\r\n			\'Yes\'\r\n		ELSE\r\n			\'No\'\r\n		END\r\n	) AS `Paid`,\r\n	`payment_type`,\r\n	`payment_amount`,\r\nLEFT(`user_agent`,10) AS `Browser`,\r\n	`customer_email` AS `Email`,\r\n`realex_status` AS `Status`,\r\n	`customer_telephone` AS `Telephone`,\r\n`customer_address` AS `Address`\r\nFROM\r\n	plugin_payments_log\r\nWHERE purchase_time >= \'{!From!}\' AND purchase_time <= DATE_ADD(\'{!To!}\', interval 1 day)\r\nORDER BY `purchase_time` DESC\r\n' WHERE (name='Recent Payments Realex');

