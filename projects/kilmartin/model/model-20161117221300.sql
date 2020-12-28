/*
ts:2016-11-17 22:13:00
*/

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'My Schedules Payment Due',
		`type` = 10,
		`x_axis` = '',
		`y_axis` = '',
		`publish` = 1,
		`delete` = 0;

insert into `plugin_reports_reports` set `name` = 'Payment Due For Schedules by Trainer',
`summary` = '',
`sql` = 'SELECT\n	t.id AS `Transaction Id`,\n	t4.type AS `Transaction Type`,\n	b.booking_id AS `Booking Id`,\n	CONCAT(c3.first_name, \' \', c3.last_name) AS `Booking Name`,\n	emails.`value` AS `Email`,\n	mobiles.`value` AS `Mobile`,\n	CONCAT(c.title,\' \',c.first_name,\' \',c.last_name) AS `Payee`,\n	s.`name` AS `Schedule`,\n	t.payment_due_date AS `Due Date`,\n	t.amount AS `Amount`,\n	(SELECT\n					IFNULL(SUM(IF(t2.credit, t1.amount, -t1.amount)), 0)\n				FROM\n					plugin_bookings_transactions_payments AS `t1`\n					JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n				WHERE\n			t1.transaction_id = t.id) AS `Paid`,\n	(t.amount - \n		IFNULL(\n			(SELECT\n					SUM(IF(t2.credit, t1.amount, -t1.amount))\n				FROM\n					plugin_bookings_transactions_payments AS `t1`\n					JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n				WHERE\n			t1.transaction_id = t.id),\n			0\n		)) AS `Amount Due`\n	\nFROM plugin_bookings_transactions `t`\n	INNER JOIN plugin_bookings_transactions_types `t4` ON t4.id = t.type\n	INNER JOIN plugin_ib_educate_bookings `b` ON t.booking_id = b.booking_id\n	INNER JOIN plugin_contacts3_contacts `c` ON t.contact_id = c.id\n	INNER JOIN plugin_bookings_transactions_has_schedule ts ON t.id = ts.transaction_id AND ts.deleted = 0\n	INNER JOIN plugin_ib_educate_booking_has_schedules bs ON b.booking_id = bs.booking_id AND bs.schedule_id = ts.schedule_id AND bs.deleted = 0\n	INNER JOIN plugin_courses_schedules s ON bs.schedule_id = s.id AND s.`delete` = 0\n	INNER JOIN plugin_courses_courses `c2` ON s.course_id = c2.id\n	INNER JOIN plugin_contacts3_contacts `c3` ON c3.id = b.contact_id\n	LEFT JOIN plugin_contacts3_contact_has_notifications emails ON c3.notifications_group_id = emails.group_id AND emails.notification_id = 1\n	LEFT JOIN plugin_contacts3_contact_has_notifications mobiles ON c3.notifications_group_id = mobiles.group_id AND mobiles.notification_id = 2\n\nWHERE\ns.id IN \n	(SELECT DISTINCT s.id\n					FROM plugin_contacts3_contacts ca\n						INNER JOIN plugin_contacts3_contact_has_notifications n \n							ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n						INNER JOIN engine_users u ON n.`value` = u.email\n						INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n						INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n						INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND bs.deleted = 0\n						INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0\n					WHERE u.id = \'{!me!}\')\n\nGROUP BY t.id\n\nHAVING `Amount Due` > 0',
`widget_sql` = 'SELECT \n		`Parent Name`, \n		`Booking Id`,  \n		CONCAT(\n			\'<span class=\"popinit icon-eye\" data-placement=\"left\" rel=\"popover\" data-content=\"<b>Student</b>: \',  `Student Name`,\n				\'<br /><b>Payee</b>: \', `Payee`,\n				\'<br /><b>Due Date</b>: \', `Due Date`, \n				\'<br /><b>Amount</b>: \', `Amount`, \n				\'<br /><b>Paid</b>:\', `Paid` , \n				\'<br /><b>Amount Due</b>:\', `Amount Due`, \n			\'\" data-html=\"true\"></span>\') AS `Details`\n	FROM\n(\n	SELECT\n	CONCAT(p.first_name, \' \', p.last_name) AS `Parent Name`,\n	t.id AS `Transaction Id`,\n	t4.type AS `Transaction Type`,\n	b.booking_id AS `Booking Id`,\n	CONCAT(c3.first_name, \' \', c3.last_name) AS `Student Name`,\n	emails.`value` AS `Email`,\n	mobiles.`value` AS `Mobile`,\n	CONCAT(c.title,\' \',c.first_name,\' \',c.last_name) AS `Payee`,\n	s.`name` AS `Schedule`,\n	t.payment_due_date AS `Due Date`,\n	t.amount AS `Amount`,\n	(SELECT\n					IFNULL(SUM(IF(t2.credit, t1.amount, -t1.amount)), 0)\n				FROM\n					plugin_bookings_transactions_payments AS `t1`\n					JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n				WHERE\n			t1.transaction_id = t.id) AS `Paid`,\n	(t.amount - \n		IFNULL(\n			(SELECT\n					SUM(IF(t2.credit, t1.amount, -t1.amount))\n				FROM\n					plugin_bookings_transactions_payments AS `t1`\n					JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n				WHERE\n			t1.transaction_id = t.id),\n			0\n		)) AS `Amount Due`\n	\nFROM plugin_bookings_transactions `t`\n	INNER JOIN plugin_bookings_transactions_types `t4` ON t4.id = t.type\n	INNER JOIN plugin_ib_educate_bookings `b` ON t.booking_id = b.booking_id\n	INNER JOIN plugin_contacts3_contacts `c` ON t.contact_id = c.id\n	INNER JOIN plugin_bookings_transactions_has_schedule ts ON t.id = ts.transaction_id AND ts.deleted = 0\n	INNER JOIN plugin_ib_educate_booking_has_schedules bs ON b.booking_id = bs.booking_id AND bs.schedule_id = ts.schedule_id AND bs.deleted = 0\n	INNER JOIN plugin_courses_schedules s ON bs.schedule_id = s.id AND s.`delete` = 0\n	INNER JOIN plugin_courses_courses `c2` ON s.course_id = c2.id\n	INNER JOIN plugin_contacts3_contacts `c3` ON c3.id = b.contact_id\n	LEFT JOIN plugin_contacts3_contact_has_notifications emails ON c3.notifications_group_id = emails.group_id AND emails.notification_id = 1\n	LEFT JOIN plugin_contacts3_contact_has_notifications mobiles ON c3.notifications_group_id = mobiles.group_id AND mobiles.notification_id = 2\n	LEFT JOIN plugin_contacts3_family f ON c.family_id = f.family_id\n	LEFT JOIN plugin_contacts3_contacts p ON f.primary_contact_id = p.id\n\nWHERE\ns.id IN \n	(SELECT DISTINCT s.id\n					FROM plugin_contacts3_contacts ca\n						INNER JOIN plugin_contacts3_contact_has_notifications n \n							ON (ca.id = n.contact_id OR ca.notifications_group_id = n.group_id) AND n.notification_id = 1\n						INNER JOIN engine_users u ON n.`value` = u.email\n						INNER JOIN plugin_courses_schedules_events e ON ca.id = e.trainer_id AND e.`delete` = 0\n						INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0\n						INNER JOIN plugin_ib_educate_booking_has_schedules bs ON s.id = bs.schedule_id AND bs.deleted = 0\n						INNER JOIN plugin_ib_educate_bookings b ON bs.booking_id = b.booking_id AND b.`delete` = 0\n					WHERE u.id = \'{!me!}\')\n\nGROUP BY t.id\n\nHAVING `Amount Due` > 0\n) s',
`category` = '0',
`sub_category` = '0',
`dashboard` = '0',
`created_by` = null,
`modified_by` = null,
`date_created` = now(),
`date_modified` = now(),
`publish` = '1',
`delete` = '0',
`widget_id` = (select id from plugin_reports_widgets where `name` = 'My Schedules Payment Due' limit 1),
`chart_id` = null,
`link_url` = '',
`link_column` = '',
`report_type` = 'sql',
`autoload` = '0',
`checkbox_column` = '0',
`action_button_label` = '',
`action_button` = '0',
`action_event` = '',
`checkbox_column_label` = '',
`autosum` = '0',
`column_value` = '',
`autocheck` = '0',
`custom_report_rules` = '',
`bulk_message_sms_number_column` = '',
`bulk_message_email_column` = '',
`bulk_message_subject_column` = '',
`bulk_message_subject` = '',
`bulk_message_body_column` = '',
`bulk_message_body` = '',
`bulk_message_interval` = '',
`rolledback_to_version` = null,
`php_modifier` = '',
`generate_documents` = '0',
`generate_documents_template_file_id` = '0',
`generate_documents_pdf` = '0',
`generate_documents_office_print` = '0',
`generate_documents_office_print_bulk` = '0',
`generate_documents_tray` = null,
`generate_documents_helper_method` = '',
`generate_documents_link_to_contact` = '';
	select last_insert_id() into @refid_plugin_reports_reports_20161118001251_001;
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_20161118001251_001,
`type` = 'user_id',
`name` = 'me',
`value` = 'logged',
`delete` = '0',
`is_multiselect` = '0';


INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  ((select id from plugin_dashboards where `title` = 'My Schedules'), (select id from plugin_reports_reports where `name` = 'Payment Due For Schedules by Trainer' limit 1), 1, 0, null, 1, 0);
