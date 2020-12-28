/*
ts:2016-11-11 12:31:00
*/

insert into `plugin_reports_reports` set `name` = 'Student Attendance',
`summary` = '',
`sql` = 'SELECT\n		CONCAT_WS(\' \', t.title, t.first_name, t.last_name) AS `Student`,\n		nm.`value` AS `Mobile`,\n		ne.`value` AS `Email`,\n		c.title AS `Course`, \n		s.`name` AS `Schedule`, \n		e.datetime_start AS `Date`, \n		IF(i.attending = 1, \'YES\', \'NO\') AS `Attending`,\n		i.timeslot_status AS `Attendance Detail`,\n		n.note AS `Note`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id\n		INNER JOIN plugin_ib_educate_booking_items i ON i.period_id = e.id\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id\n		INNER JOIN plugin_contacts3_contacts t ON b.contact_id = t.id\n		LEFT JOIN plugin_contacts3_contact_has_notifications nm ON t.notifications_group_id = nm.group_id AND nm.notification_id = 2\n		LEFT JOIN plugin_contacts3_contact_has_notifications ne ON t.notifications_group_id = ne.group_id AND ne.notification_id = 1\n		LEFT JOIN plugin_contacts3_notes n ON i.booking_item_id = n.link_id AND n.table_link_id = 3\n	WHERE \n		s.`delete` = 0 AND c.deleted = 0 AND e.`delete` = 0 AND i.`delete` = 0 AND\n		s.course_id in ({!Course!}) AND s.course_id in ({!Schedule!}) AND b.contact_id IN ({!Student!}) AND\n		e.datetime_start >= \'{!Date From!}\' AND e.datetime_start <= \'{!Date To!}\'\n	GROUP BY `Student`, e.id\n	ORDER BY `Student`',
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
`widget_id` = '80',
`chart_id` = '19',
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
	select last_insert_id() into @refid_plugin_reports_reports_20161111143115_001;
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_20161111143115_001,
`type` = 'custom',
`name` = 'Course',
`value` = '(SELECT\n		DISTINCT\n		c.id AS `Course Id`,\n		c.title AS `Course` \n		\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id\n	WHERE \n		s.`delete` = 0 AND c.deleted = 0 AND e.`delete` = 0\n	ORDER BY c.title)',
`delete` = '0',
`is_multiselect` = '1';
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_20161111143115_001,
`type` = 'custom',
`name` = 'Schedule',
`value` = '(SELECT\n		DISTINCT\n		s.id AS `Schedule Id`,\n		CONCAT_WS(\' \', s.`name`, \' \', pl.`name`, l.name) AS `Schedule`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id\n		LEFT JOIN plugin_courses_locations l ON s.location_id = l.id\n		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id\n	WHERE \n		s.`delete` = 0 AND c.deleted = 0 AND e.`delete` = 0 AND \n		s.course_id in ({!Course!})\n	ORDER BY s.`name`)',
`delete` = '0',
`is_multiselect` = '1';
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_20161111143115_001,
`type` = 'custom',
`name` = 'Student',
`value` = '(SELECT\n		t.id AS `Id`,\n		CONCAT_WS(\' \', t.first_name, t.last_name) AS `Student`\n	FROM plugin_courses_schedules s\n		INNER JOIN plugin_courses_courses c ON s.course_id = c.id\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id\n		INNER JOIN plugin_ib_educate_booking_items i ON i.period_id = e.id\n		INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id\n		INNER JOIN plugin_contacts3_contacts t ON b.contact_id = t.id\n	WHERE \n		s.`delete` = 0 AND c.deleted = 0 AND e.`delete` = 0 AND i.`delete` = 0 AND\n		s.course_id in ({!Course!}) AND s.course_id in ({!Schedule!})\n	ORDER BY `Student`)',
`delete` = '0',
`is_multiselect` = '1';
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_20161111143115_001,
`type` = 'date',
`name` = 'Date From',
`value` = '',
`delete` = '0',
`is_multiselect` = '0';
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_20161111143115_001,
`type` = 'date',
`name` = 'Date To',
`value` = '',
`delete` = '0',
`is_multiselect` = '0';