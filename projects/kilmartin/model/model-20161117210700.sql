/*
ts:2016-11-17 21:07:00
*/

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Messages From Parents',
		`type` = 10,
		`x_axis` = '',
		`y_axis` = '',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @supervisor_parent_messages_widget_id;

insert into `plugin_reports_reports` set `name` = 'Messages From Parents',
`summary` = '',
`sql` = 'SELECT\n		CONCAT(p.first_name, \' \', p.last_name) AS `Parent Name`,\n		CONCAT(c.first_name, \' \', c.last_name) AS `Student Name`,\n		e.`value` AS `Email`,\n		m.`value` AS `Mobile`,\n		pe.`value` AS `Parent Email`,\n		pm.`value` AS `Parent Mobile`,\n		txt.message AS `Message`,\n		txt.date_created AS `Date`\n	FROM plugin_contacts3_contacts `c`\n		LEFT JOIN plugin_contacts3_contact_has_notifications e ON c.notifications_group_id = e.group_id AND e.notification_id = 1\n		LEFT JOIN plugin_contacts3_contact_has_notifications m ON c.notifications_group_id = m.group_id AND m.notification_id = 2\n		LEFT JOIN plugin_contacts3_family f ON c.family_id = f.family_id\n		LEFT JOIN plugin_contacts3_contacts p ON f.primary_contact_id = p.id\n		LEFT JOIN plugin_contacts3_contact_has_notifications pe ON p.notifications_group_id = pe.group_id AND pe.notification_id = 1\n		LEFT JOIN plugin_contacts3_contact_has_notifications pm ON p.notifications_group_id = pm.group_id AND pm.notification_id = 2\n		INNER JOIN plugin_messaging_messages txt ON txt.sender = pm.`value`\n	ORDER BY txt.date_created DESC',
`widget_sql` = 'SELECT\n		CONCAT(p.first_name, \' \', p.last_name) AS `Parent`,\n		txt.date_created AS `Date`,\n		CONCAT(\'<span class=\"popinit icon-eye\" data-placement=\"left\" rel=\"popover\" data-content=\"\', txt.message, \'\" data-html=\"true\"></span>\') AS `Message`\n	FROM plugin_contacts3_contacts `c`\n		LEFT JOIN plugin_contacts3_contact_has_notifications e ON c.notifications_group_id = e.group_id AND e.notification_id = 1\n		LEFT JOIN plugin_contacts3_contact_has_notifications m ON c.notifications_group_id = m.group_id AND m.notification_id = 2\n		LEFT JOIN plugin_contacts3_family f ON c.family_id = f.family_id\n		LEFT JOIN plugin_contacts3_contacts p ON f.primary_contact_id = p.id\n		LEFT JOIN plugin_contacts3_contact_has_notifications pe ON p.notifications_group_id = pe.group_id AND pe.notification_id = 1\n		LEFT JOIN plugin_contacts3_contact_has_notifications pm ON p.notifications_group_id = pm.group_id AND pm.notification_id = 2\n		INNER JOIN plugin_messaging_messages txt ON txt.sender = pm.`value`\n	ORDER BY txt.date_created DESC',
`category` = '0',
`sub_category` = '0',
`dashboard` = '0',
`created_by` = null,
`modified_by` = null,
`date_created` = NOW(),
`date_modified` = NOW(),
`publish` = '1',
`delete` = '0',
`widget_id` = @supervisor_parent_messages_widget_id,
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
	select last_insert_id() into @refid_plugin_reports_reports_20161117230656_001;

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  ((select id from plugin_dashboards where `title` = 'Supervisor'), (select id from plugin_reports_reports where `name` = 'Messages From Parents' limit 1), 1, 1, null, 1, 0);
