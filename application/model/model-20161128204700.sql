/*
ts:2016-11-28 20:47:00
*/

UPDATE `plugin_reports_reports` SET `sql`='SELECT\r\n	`customer_name` AS `Customer`,\r\n	`customer_telephone` AS `Telephone`,\r\n	`customer_address` AS `Address`,\r\n	`customer_email` AS `Email`,\r\n	(\r\n		CASE\r\n		WHEN paid = 1 THEN\r\n			\'Yes\'\r\n		ELSE\r\n			\'No\'\r\n		END\r\n	) AS `Paid`,\r\n	payment_type AS `Payment Type`,\r\n	payment_amount AS `Payment Amount`,\r\n	purchase_time AS `Date`\r\nFROM\r\n	plugin_payments_log\r\nORDER BY `purchase_time` DESC' WHERE (`name` = 'Recent Payments');
