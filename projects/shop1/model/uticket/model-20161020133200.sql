/*
ts:2016-10-20 13:32:00
*/

UPDATE plugin_reports_reports
  SET
    `sql` = 'SELECT \r\n	CONCAT( \r\n		\'<div class=\"text-center\"><h3>Total Profit</h3><span style=\"font-size: 2em;\">\', \r\n		`total`, \r\n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \r\n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \r\n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \r\n	) AS ` ` FROM \r\n(\r\nSELECT \r\n	CONCAT_WS(\r\n		\'\', \r\n		(SELECT \r\n			CONCAT(c.symbol, ROUND(SUM(o.commission_total - o.vat_total - IFNULL(p.paymentgw_fee, 0)), 2)) AS profit\r\n			FROM plugin_events_orders o\r\n				INNER JOIN plugin_events_orders_payments p ON o.id = p.order_id\r\n				INNER JOIN plugin_currency_currencies c ON p.currency = c.currency\r\n			WHERE o.deleted = 0 AND p.deleted = 0 AND p.`status` = \'PAID\' AND o.`status` = \'PAID\' AND o.total > 0\r\n			AND c.currency = \'EUR\'),\r\n		\' \',\r\n		(SELECT \r\n			CONCAT(c.symbol, ROUND(SUM(o.commission_total - o.vat_total - IFNULL(p.paymentgw_fee, 0)), 2)) AS profit\r\n			FROM plugin_events_orders o\r\n				INNER JOIN plugin_events_orders_payments p ON o.id = p.order_id\r\n				INNER JOIN plugin_currency_currencies c ON p.currency = c.currency\r\n			WHERE o.deleted = 0 AND p.deleted = 0 AND p.`status` = \'PAID\' AND o.`status` = \'PAID\' AND o.total > 0\r\n			AND c.currency = \'GBP\')\r\n	) AS `total`\r\n) AS `total`',
    `date_modified` = NOW()
  WHERE `name` = 'Admin Total Profit';
