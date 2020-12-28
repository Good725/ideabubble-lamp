/*
ts:2016-10-16 22:07:00
*/


INSERT INTO `plugin_reports_reports`
  SET
    `name` = 'Course Reminder',
    `summary` = '',
    `sql` = 'SELECT \n		DISTINCT\n		c.id AS `Course ID`, \n		c.title AS `Course`, \n		s.id AS `Schedule ID`, \n		s.`name` AS `Schedule`, \n		e.datetime_start AS `Time`,\n		CONCAT_WS(\' \', students.title, students.first_name, students.last_name) AS `Student`,\n		emails.`value` AS `Email`,\n		mobiles.`value` AS `Mobile`\n	FROM plugin_courses_courses c\n		INNER JOIN plugin_courses_schedules s ON c.id = s.course_id\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id\n		INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id\n		INNER JOIN plugin_ib_educate_booking_has_schedules hs ON hs.booking_id = b.booking_id AND hs.schedule_id = s.id\n		INNER JOIN plugin_contacts3_contacts students ON b.contact_id = students.id\n		LEFT JOIN plugin_contacts3_contact_has_notifications emails ON students.notifications_group_id = emails.group_id AND emails.notification_id = 1\n		LEFT JOIN plugin_contacts3_contact_has_notifications mobiles ON students.notifications_group_id = mobiles.group_id AND mobiles.notification_id = 2\n	WHERE \n		c.deleted = 0 AND \n		s.`delete` = 0 AND\n		e.`delete` = 0 AND\n		i.`delete` = 0 AND\n		i.attending = 1 AND\n		b.`delete` = 0 AND\n		hs.deleted = 0 AND\n		c.category_id IN ({!category_id!}) AND\n		s.course_id IN ({!course_id!}) AND\n		s.id IN ({!schedule_id!}) AND\n		e.datetime_start >= \'{!date_from!}\' AND\n		e.datetime_start < DATE_ADD(\'{!date_to!}\', INTERVAL 1 DAY)\n	ORDER BY `Course`, `Schedule`, `Student`',
    `widget_sql` = '',
    `category` = '0',
    `sub_category` = '0',
    `dashboard` = '0',
    `created_by` = null,
    `modified_by` = null,
    `date_created` = NOW(),
    `date_modified` = NOW(),
    `publish` = '1',
    `delete` = '0',
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
    `bulk_message_email_column` = 'Email',
    `bulk_message_subject_column` = '',
    `bulk_message_subject` = 'Course reminder',
    `bulk_message_body_column` = '',
    `bulk_message_body` = 'You have class {Course} {Schedule} at {Time}',
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
	SELECT last_insert_id() INTO @refid_plugin_reports_reports_20161017020651_001;
	INSERT INTO `plugin_reports_parameters` SET `report_id` = @refid_plugin_reports_reports_20161017020651_001, `type` = 'custom', `name` = 'category_id', `value` = 'select id, category from plugin_courses_categories where `delete`=0\norder by `category`', `delete` = '0', `is_multiselect` = '1';
	INSERT INTO `plugin_reports_parameters` SET `report_id` = @refid_plugin_reports_reports_20161017020651_001, `type` = 'custom', `name` = 'course_id', `value` = 'select id, title from plugin_courses_courses where deleted = 0 and category_id in ({!category_id!}) order by title', `delete` = '0', `is_multiselect` = '1';
	INSERT INTO `plugin_reports_parameters` SET `report_id` = @refid_plugin_reports_reports_20161017020651_001, `type` = 'custom', `name` = 'schedule_id', `value` = 'select id, name from plugin_courses_schedules where course_id in ({!course_id!}) order by `name`', `delete` = '0', `is_multiselect` = '1';
	INSERT INTO `plugin_reports_parameters` SET `report_id` = @refid_plugin_reports_reports_20161017020651_001, `type` = 'date', `name` = 'date_from', `value` = '16-10-2016', `delete` = '0', `is_multiselect` = '0';
	INSERT INTO `plugin_reports_parameters` SET `report_id` = @refid_plugin_reports_reports_20161017020651_001, `type` = 'date', `name` = 'date_to', `value` = '16-10-2016', `delete` = '0', `is_multiselect` = '0';

UPDATE plugin_reports_reports
  SET
    `sql` = 'SELECT\nt.id AS `Transaction Id`,\nt4.type AS `Transaction Type`,\nb.booking_id AS `Booking Id`,\nCONCAT(c3.first_name, \' \', c3.last_name) AS `Booking Name`,\nemails.`value` AS `Email`,\nmobiles.`value` AS `Mobile`,\nt.payment_due_date AS `Due Date`,\n(t.amount - COALESCE(\n(\nSELECT\nSUM(t1.amount)\nFROM\nplugin_bookings_transactions_payments AS `t1`\nJOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\nWHERE\nt1.transaction_id = t.id\n),\n0\n)) AS `Amount Due`,\ns.`name` AS `Schedule`,\nCONCAT(c.title,\' \',c.first_name,\' \',c.last_name) AS `Payee`\nFROM plugin_bookings_transactions `t`\n	INNER JOIN plugin_bookings_transactions_types `t4` ON t4.id = t.type\n	INNER JOIN plugin_ib_educate_bookings `b` ON t.booking_id = b.booking_id\n	INNER JOIN plugin_contacts3_contacts `c` ON t.contact_id = c.id\n	INNER JOIN plugin_bookings_transactions_has_schedule ts ON t.id = ts.transaction_id AND ts.deleted = 0\n	INNER JOIN plugin_ib_educate_booking_has_schedules bs ON b.booking_id = bs.booking_id AND bs.schedule_id = ts.schedule_id AND bs.deleted = 0\n	INNER JOIN plugin_courses_schedules s ON bs.schedule_id = s.id AND s.`delete` = 0\n	INNER JOIN plugin_courses_schedules_events `c4` ON s.id = c4.id\n	INNER JOIN plugin_courses_courses `c2` ON s.course_id = c2.id\n	INNER JOIN plugin_contacts3_contacts `c3` ON c3.id = b.contact_id\n	LEFT JOIN plugin_contacts3_contact_has_notifications emails ON c3.notifications_group_id = emails.group_id AND emails.notification_id = 1\n	LEFT JOIN plugin_contacts3_contact_has_notifications mobiles ON c3.notifications_group_id = mobiles.group_id AND mobiles.notification_id = 2\n\nWHERE\nt4.type = \'Booking - Pay Now\'\nAND t.payment_due_date >= \'{!From!}\'\nAND t.payment_due_date < DATE_ADD(\'{!To!}\', INTERVAL 1 DAY)\nHAVING\n`Amount Due` > 0',
    `date_modified` = NOW(),
    `bulk_message_email_column` = 'Email',
    `bulk_message_subject` = 'Outstanding Booking',
    `bulk_message_body` = '{Booking Name} has an outstanding booking {Schedule} Amount: €{Amount Due}'
  WHERE `name` = 'Outstanding Transactions - Prepay';

UPDATE plugin_reports_reports
  SET
    `sql` = 'SELECT\n	CONCAT(c.first_name, \' \', c.last_name)AS `Student`,\n	emails.`value` AS `Email`,\n	mobiles.`value` AS `Mobile`,\n	t.amount AS `Class Fee`,\n	CONCAT(\n		\'Next Class: \',\n		DATE_FORMAT(\n			c4.datetime_start,\n			\'%a %D %b - %H:%i\'\n		)\n	)AS `Next Attending`,\n	CONCAT(c1.`name`, \' \', c2.title)AS `Course`,\n	CONCAT(\n		\'Transaction #\',\n		t.id,\n		\' \',\n		t4.type\n	)AS `Transaction`,\n	(\n		t.total - COALESCE(\n			(\n				SELECT\n					SUM(t1.amount)\n				FROM\n					plugin_bookings_transactions_payments AS `t1`\n				JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n				WHERE\n					t2.credit = 1\n				AND t1.transaction_id = t.id\n			),\n			0\n		)+ COALESCE(\n			(\n				SELECT\n					SUM(t1.amount)\n				FROM\n					plugin_bookings_transactions_payments AS `t1`\n				JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n				WHERE\n					t2.credit = 0\n				AND t1.transaction_id = t.id\n			),\n			0\n		)\n	)AS `Total Due`\nFROM\n	plugin_bookings_transactions t\nINNER JOIN plugin_bookings_transactions_types t4 ON t4.id = t.type\nINNER JOIN plugin_ib_educate_bookings b ON t.booking_id = b.booking_id\nINNER JOIN plugin_contacts3_contacts c ON t.contact_id = c.id\nINNER JOIN plugin_ib_educate_booking_has_schedules bhs ON b.booking_id = bhs.booking_id\nINNER JOIN plugin_bookings_transactions_has_schedule t5 ON t.id = t5.transaction_id AND t5.schedule_id = bhs.schedule_id\nINNER JOIN plugin_courses_schedules c1 ON t5.schedule_id = c1.id\nINNER JOIN plugin_courses_schedules_events c4 ON c1.id = c4.schedule_id\nINNER JOIN plugin_courses_courses c2 ON c1.course_id = c2.id\nINNER JOIN plugin_contacts3_contacts c3 ON c3.id = b.contact_id\nLEFT JOIN plugin_contacts3_contact_has_notifications emails ON c.notifications_group_id = emails.group_id AND emails.notification_id = 1\n	LEFT JOIN plugin_contacts3_contact_has_notifications mobiles ON c.notifications_group_id = mobiles.group_id AND mobiles.notification_id = 2\nWHERE\n	t4.type = \'Booking - PAYG\'\nAND `c4`.`datetime_start` >= \'{!From!}\' 	AND `c4`.`datetime_start` <  \'{!To!}\'\nHAVING\n	`Total Due` > 0',
    `date_modified` = NOW(),
    `bulk_message_email_column` = 'Email',
    `bulk_message_subject` = 'Outstanding Booking',
    `bulk_message_body` = '{Student} has an outstanding booking {course} Amount: €{Total Due}'
  WHERE
    `name` = 'Outstanding PAYG';

UPDATE plugin_reports_parameters SET `value` = NULL WHERE report_id IN (SELECT id FROM plugin_reports_reports WHERE `name` IN ('Outstanding Transactions - Prepay', 'Outstanding PAYG'));
