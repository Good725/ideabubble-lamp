/*
ts:2016-08-03 09:51:00
*/

INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('Admin', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

SELECT LAST_INSERT_ID() INTO @admin_dashboard_id;

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT @admin_dashboard_id, id FROM engine_project_role WHERE `role` IN ('External User', 'Administrator'));

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  (SELECT d.id, r.id, 1, 1, null, 1, 0 FROM plugin_reports_reports r INNER  JOIN plugin_dashboards d where r.`name` = 'Website Traffic' AND d.title = 'Admin' LIMIT 1);

INSERT INTO `plugin_reports_reports`
  SET
    `name` ='Admin Total Revenue',
    `summary` = '',
    `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Revenue</h3><span style=\"font-size: 2em;\">\', \n		`total`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT SUM(`order`.`total`) AS `total` \n	FROM   `plugin_events_orders` `order` \n	JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` \n	AND    `order`.`status` = \'PAID\' \n	AND    `order`.`deleted` = 0 \n        WHERE `order`.`created` BETWEEN {!DASHBOARD-FROM!} AND {!DASHBOARD-TO!}\n) AS `total`',
    `widget_sql` = '',
    `date_created` = NOW(),
    `date_modified` = NOW(),
    `publish` = 1,
    `delete` = 0,
    `report_type` = 'sql';
SELECT LAST_INSERT_ID() INTO @admin_total_revenue_report_id;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Revenue',
		`report_id` = @admin_total_revenue_report_id,
		`chart_type_id` = 6,
		`x_axis` = '',
		`y_axis` = '',
		`total_field` = '',
		`total_type_id` = 2,
		`text_color` = 'rgb(255, 255, 255)',
		`background_color` = 'rgb(56, 231, 202)',
		`publish` = 1,
		`deleted` = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@admin_dashboard_id, @admin_total_revenue_report_id, 2, 2, null, 1, 0);

INSERT INTO `plugin_reports_reports`
  SET
    `name` ='Admin Total Events',
    `summary` = '',
    `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Events</h3><span style=\"font-size: 2em;\">\', \n		`total`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT COUNT(*) AS total FROM plugin_events_events e WHERE e.deleted = 0 AND `e`.`date_created` BETWEEN \'{!DASHBOARD-FROM!}\' AND \'{!DASHBOARD-TO!}\'\n) AS `total`',
    `widget_sql` = '',
    `date_created` = NOW(),
    `date_modified` = NOW(),
    `publish` = 1,
    `delete` = 0,
    `report_type` = 'sql';
SELECT LAST_INSERT_ID() INTO @admin_total_events_report_id;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Events',
		`report_id` = @admin_total_events_report_id,
		`chart_type_id` = 6,
		`x_axis` = '',
		`y_axis` = '',
		`total_field` = '',
		`total_type_id` = 2,
		`text_color` = 'rgb(255, 255, 255)',
		`background_color` = 'rgb(56, 231, 202)',
		`publish` = 1,
		`deleted` = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@admin_dashboard_id, @admin_total_events_report_id, 2, 3, null, 1, 0);

INSERT INTO `plugin_reports_reports`
  SET
    `name` ='Admin Total Tickets',
    `summary` = '',
    `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Tickets</h3><span style=\"font-size: 2em;\">\', \n		`total`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT COUNT(*) AS total FROM plugin_events_orders o INNER JOIN plugin_events_orders_items i ON o.id = i.order_id INNER JOIN plugin_events_orders_items_has_dates d ON i.id = d.order_item_id INNER JOIN plugin_events_orders_tickets t ON d.id = t.order_item_has_date_id WHERE o.deleted = 0 AND o.`status` = \'PAID\' AND t.deleted = 0 AND `o`.`created` BETWEEN \'{!DASHBOARD-FROM!}\' AND \'{!DASHBOARD-TO!}\'\n) AS `total`',
    `widget_sql` = '',
    `date_created` = NOW(),
    `date_modified` = NOW(),
    `publish` = 1,
    `delete` = 0,
    `report_type` = 'sql';
SELECT LAST_INSERT_ID() INTO @admin_total_tickets_report_id;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Tickets',
		`report_id` = @admin_total_tickets_report_id,
		`chart_type_id` = 6,
		`x_axis` = '',
		`y_axis` = '',
		`total_field` = '',
		`total_type_id` = 2,
		`text_color` = 'rgb(255, 255, 255)',
		`background_color` = 'rgb(56, 231, 202)',
		`publish` = 1,
		`deleted` = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@admin_dashboard_id, @admin_total_tickets_report_id, 2, 1, null, 1, 0);

INSERT INTO `plugin_reports_reports`
  SET
    `name` ='Admin Total Profit',
    `summary` = '',
    `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Profit</h3><span style=\"font-size: 2em;\">\', \n		`total`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT \'N/A\' AS total \n) AS `total`',
    `widget_sql` = '',
    `date_created` = NOW(),
    `date_modified` = NOW(),
    `publish` = 1,
    `delete` = 0,
    `report_type` = 'sql';
SELECT LAST_INSERT_ID() INTO @admin_total_profit_report_id;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Profit',
		`report_id` = @admin_total_profit_report_id,
		`chart_type_id` = 6,
		`x_axis` = '',
		`y_axis` = '',
		`total_field` = '',
		`total_type_id` = 2,
		`text_color` = 'rgb(255, 255, 255)',
		`background_color` = 'rgb(56, 231, 202)',
		`publish` = 1,
		`deleted` = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@admin_dashboard_id, @admin_total_profit_report_id, 2, 2, null, 1, 0);

INSERT INTO `plugin_reports_reports`
  SET
    `name` ='Admin Booking Fee Total',
    `summary` = '',
    `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Booking Fee Total</h3><span style=\"font-size: 2em;\">\', \n		`total`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT SUM(`order`.`commission_total`) AS `total` \n	FROM   `plugin_events_orders` `order` \n	JOIN   `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` \n	AND    `order`.`status` = \'PAID\' \n	AND    `order`.`deleted` = 0 \n        WHERE `order`.`created` BETWEEN {!DASHBOARD-FROM!} AND {!DASHBOARD-TO!}\n) AS `total`',
    `widget_sql` = '',
    `date_created` = NOW(),
    `date_modified` = NOW(),
    `publish` = 1,
    `delete` = 0,
    `report_type` = 'sql';
SELECT LAST_INSERT_ID() INTO @admin_total_fee_report_id;
INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Booking Fee Total',
		`report_id` = @admin_total_fee_report_id,
		`chart_type_id` = 6,
		`x_axis` = '',
		`y_axis` = '',
		`total_field` = '',
		`total_type_id` = 2,
		`text_color` = 'rgb(255, 255, 255)',
		`background_color` = 'rgb(56, 231, 202)',
		`publish` = 1,
		`deleted` = 0;
INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  (@admin_dashboard_id, @admin_total_fee_report_id, 2, 3, null, 1, 0);
