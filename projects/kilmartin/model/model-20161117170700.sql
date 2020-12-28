/*
ts:2016-11-17 17:07:00
*/

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Outstanding Payments',
		`type` = 10,
		`x_axis` = '',
		`y_axis` = '',
		`publish` = 1,
		`delete` = 0;


UPDATE `plugin_reports_reports` 
  SET
    `widget_sql` = 'SELECT \n		`Parent Name`, \n		`Booking Id`,  \n		CONCAT(\n			\'<span class=\"popinit icon-eye\" data-placement=\"left\" rel=\"popover\" data-content=\"<b>Student</b>: \',  `Student Name`,\n				\'<br /><b>Payee</b>: \', `Payee`,\n				\'<br /><b>Due Date</b>: \', `Due Date`, \n				\'<br /><b>Amount</b>: \', `Amount`, \n				\'<br /><b>Paid</b>:\', `Paid` , \n				\'<br /><b>Amount Due</b>:\', `Amount Due`, \n			\'\" data-html=\"true\"></span>\') AS `Details`\n	FROM\n(\n	SELECT\n		CONCAT(p.first_name, \' \', p.last_name) AS `Parent Name`,\n		t.id AS `Transaction Id`,\n		t4.type AS `Transaction Type`,\n		b.booking_id AS `Booking Id`,\n		CONCAT(c3.first_name, \' \', c3.last_name) AS `Student Name`,\n		emails.`value` AS `Email`,\n		mobiles.`value` AS `Mobile`,\n		CONCAT(c.title,\' \',c.first_name,\' \',c.last_name) AS `Payee`,\n		s.`name` AS `Schedule`,\n		t.payment_due_date AS `Due Date`,\n		t.amount AS `Amount`,\n		(SELECT\n						IFNULL(SUM(IF(t2.credit, t1.amount, -t1.amount)), 0)\n					FROM\n						plugin_bookings_transactions_payments AS `t1`\n						JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n					WHERE\n				t1.transaction_id = t.id) AS `Paid`,\n		(t.amount - \n			IFNULL(\n				(SELECT\n						SUM(IF(t2.credit, t1.amount, -t1.amount))\n					FROM\n						plugin_bookings_transactions_payments AS `t1`\n						JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n					WHERE\n				t1.transaction_id = t.id),\n				0\n			)) AS `Amount Due`\n		\n	FROM plugin_bookings_transactions `t`\n		INNER JOIN plugin_bookings_transactions_types `t4` ON t4.id = t.type\n		INNER JOIN plugin_ib_educate_bookings `b` ON t.booking_id = b.booking_id\n		INNER JOIN plugin_contacts3_contacts `c` ON t.contact_id = c.id\n		INNER JOIN plugin_bookings_transactions_has_schedule ts ON t.id = ts.transaction_id AND ts.deleted = 0\n		INNER JOIN plugin_ib_educate_booking_has_schedules bs ON b.booking_id = bs.booking_id AND bs.schedule_id = ts.schedule_id AND bs.deleted = 0\n		INNER JOIN plugin_courses_schedules s ON bs.schedule_id = s.id AND s.`delete` = 0\n		INNER JOIN plugin_courses_courses `c2` ON s.course_id = c2.id\n		INNER JOIN plugin_contacts3_contacts `c3` ON c3.id = b.contact_id\n		LEFT JOIN plugin_contacts3_contact_has_notifications emails ON c3.notifications_group_id = emails.group_id AND emails.notification_id = 1\n		LEFT JOIN plugin_contacts3_contact_has_notifications mobiles ON c3.notifications_group_id = mobiles.group_id AND mobiles.notification_id = 2\n		LEFT JOIN plugin_contacts3_family f ON c.family_id = f.family_id\n		LEFT JOIN plugin_contacts3_contacts p ON f.primary_contact_id = p.id\n\n	GROUP BY t.id\n\n	HAVING `Amount Due` > 0\n) s',
    `widget_id` = (SELECT id FROM plugin_reports_widgets WHERE `name` = 'Outstanding Payments')
  WHERE `name` = 'Payment Due';

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  ((select id from plugin_dashboards where `title` = 'Manager'), (select id from plugin_reports_reports where `name` = 'Payment Due' limit 1), 1, 1, null, 1, 0);

	

