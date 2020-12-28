/*
ts:2016-09-02 18:13:00
*/

insert into `plugin_reports_reports` set `name` = 'Classes Booked',
 `summary` = '',
 `sql` = 'select\n		stu.id as `Student ID`,\n		CONCAT_WS(\' \', par.title, par.first_name, par.last_name) as `Parent Name`,\n		CONCAT_WS(\' \', stu.title,stu.first_name, stu.last_name) as `Student Name`,\n		CONCAT_WS(\' \', par.address1, par.address2) as `Address`,\n		par.mobile as `Mobile`,\n		par.phone as `Phone`,\n		par.email as `Email`,\n		b.id as `Booking ID`,\n		t.total as `Total Transaction`,\n		o.outstanding as `Outstanding Balance`,\n		hs.`status` as `Booking Status`\n	from plugin_courses_bookings b\n		inner join plugin_contacts_contact stu on b.student_id = stu.id\n		inner join plugin_family_members m on stu.id = m.contact_id\n		inner join plugin_family_families f on m.family_id = f.id\n		inner join plugin_contacts_contact par on f.primary_contact_id = par.id\n		inner join plugin_courses_bookings_has_schedules hs on b.id = hs.booking_id\n		inner join plugin_courses_schedules s on hs.schedule_id = s.id\n		inner join plugin_courses_courses c on s.course_id = c.id\n		left join plugin_courses_categories a on c.category_id = a.id\n		left join plugin_courses_locations l on s.location_id = l.id\n		left join plugin_courses_bookings_has_transactions ht on b.id = ht.booking_id and (ht.booking_has_schedule_id is null or ht.booking_has_schedule_id = hs.id)\n		left join plugin_transactions_transactions t on ht.transaction_id = t.id\n		left join plugin_transactions_transactions_outstanding o on t.id = o.transaction_id\n	where hs.deleted = 0 and ht.deleted = 0 and s.id in ({!schedule_id!})',
 `widget_sql` = '',
 `category` = '0',
 `sub_category` = '0',
 `dashboard` = '0',
 `created_by` = null,
 `modified_by` = null,
 `date_created` = NOW(),
 `date_modified` = NOW(),
 `publish` = 1,
 `delete` = 0,
 `widget_id` = null,
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
 `rolledback_to_version` = null, `php_modifier` = '',
 `generate_documents` = '0',
 `generate_documents_template_file_id` = '0',
 `generate_documents_pdf` = '0',
 `generate_documents_office_print` = '0',
 `generate_documents_office_print_bulk` = '0',
 `generate_documents_tray` = null, `generate_documents_helper_method` = '',
 `generate_documents_link_to_contact` = '';

select last_insert_id() into @refid_plugin_reports_reports_20160902203021_001;

insert into `plugin_reports_parameters`
	set
		`report_id` = @refid_plugin_reports_reports_20160902203021_001,
		`type` = 'custom',
		`name` = 'schedule_id',
		`value` = '((((select distinct s.id, CONCAT_WS(\' \', s.`name` , c.title, a.category, l.`name`) as schedule\n	from plugin_courses_bookings_has_schedules hs\n		inner join plugin_courses_schedules s on hs.schedule_id = s.id\n		inner join plugin_courses_courses c on s.course_id = c.id\n		left join plugin_courses_categories a on c.category_id = a.id\n		left join plugin_courses_locations l on s.location_id = l.id\n	where hs.deleted = 0))))',
		`delete` = 0,
		is_multiselect = 0;
