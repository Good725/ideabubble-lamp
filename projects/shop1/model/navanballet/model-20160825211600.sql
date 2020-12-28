/*
ts:2016-08-25 21:16:00
*/

INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('Admin', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT (SELECT id FROM plugin_dashboards WHERE `title` = 'Admin'), id FROM engine_project_role WHERE `role` IN ('Administrator'));

INSERT INTO `plugin_reports_reports`
  SET
    `name` ='Total Classes',
    `summary` = '',
    `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Classes</h3><span style=\"font-size: 2em;\">\', \n		`total`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT count(*) AS `total` FROM plugin_courses_schedules s WHERE s.`delete` = 0 AND s.publish = 1 AND s.start_date BETWEEN {!DASHBOARD-FROM!} AND DATE_ADD({!DASHBOARD-TO!}, INTERVAL 1 DAY)\n) AS `total`',
    `widget_sql` = '',
    `date_created` = NOW(),
    `date_modified` = NOW(),
    `publish` = 1,
    `delete` = 0,
    `report_type` = 'sql';

INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Classes',
		`report_id` = (SELECT id FROM plugin_reports_reports WHERE `name` = 'Total Classes' AND `delete` = 0),
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
  ((SELECT id FROM plugin_dashboards WHERE `title` = 'Admin'), (SELECT id FROM plugin_reports_reports WHERE `name` = 'Total Classes' AND `delete` = 0), 2, 1, null, 1, 0);

INSERT INTO `plugin_reports_reports`
  SET
    `name` ='Total Bookings',
    `summary` = '',
    `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Bookings</h3><span style=\"font-size: 2em;\">\', \n		`total`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT count(*) AS `total` FROM plugin_courses_bookings b INNER JOIN plugin_courses_bookings_has_schedules hs ON b.id = hs.booking_id WHERE b.`status` = \'Confirmed\' AND b.deleted = 0 AND hs.deleted = 0 AND b.created BETWEEN {!DASHBOARD-FROM!} AND DATE_ADD({!DASHBOARD-TO!}, INTERVAL 1 DAY)\n) AS `total`',
    `widget_sql` = '',
    `date_created` = NOW(),
    `date_modified` = NOW(),
    `publish` = 1,
    `delete` = 0,
    `report_type` = 'sql';

INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Bookings',
		`report_id` = (SELECT id FROM plugin_reports_reports WHERE `name` = 'Total Bookings' AND `delete` = 0),
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
  ((SELECT id FROM plugin_dashboards WHERE `title` = 'Admin'), (SELECT id FROM plugin_reports_reports WHERE `name` = 'Total Bookings' AND `delete` = 0), 2, 2, null, 1, 0);

INSERT INTO `plugin_reports_reports`
  SET
    `name` ='Total Revenue',
    `summary` = '',
    `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><h3>Total Revenue</h3><span style=\"font-size: 2em;\">\', \n		`total`, \n		\'</span><hr /><a href=\"/admin/dashboards/view_dashboard/\', \n		(SELECT `id` FROM `plugin_dashboards` WHERE `title` = \'Admin\' AND `deleted` = 0), \n		\'\" style=\"color: #fff;\">View Dashboard</a></div>\' \n	) AS ` ` \nFROM ( \n	SELECT SUM(pays.amount * txt.income * payst.income) AS `total` FROM plugin_transactions_transactions tx INNER JOIN plugin_transactions_types txt ON tx.type_id = txt.id INNER JOIN plugin_transactions_payments pays ON (tx.id = pays.to_transaction_id OR tx.id = pays.from_transaction_id) INNER JOIN plugin_transactions_paymenttypes payst ON pays.paymenttype_id = payst.id WHERE tx.deleted = 0 AND tx.`status` <> \'Cancelled\' AND pays.deleted = 0 AND pays.`status` = \'Completed\' AND pays.created BETWEEN {!DASHBOARD-FROM!} AND DATE_ADD({!DASHBOARD-TO!}, INTERVAL 1 DAY)\n) AS `total`',
    `widget_sql` = '',
    `date_created` = NOW(),
    `date_modified` = NOW(),
    `publish` = 1,
    `delete` = 0,
    `report_type` = 'sql';

INSERT INTO `plugin_reports_sparklines`
	SET
		`title` = 'Total Revenue',
		`report_id` = (SELECT id FROM plugin_reports_reports WHERE `name` = 'Total Revenue' AND `delete` = 0),
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
  ((SELECT id FROM plugin_dashboards WHERE `title` = 'Admin'), (SELECT id FROM plugin_reports_reports WHERE `name` = 'Total Revenue' AND `delete` = 0), 2, 3, null, 1, 0);
