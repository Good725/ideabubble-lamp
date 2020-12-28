/*
ts:2016-10-24 20:16:03
*/

--
-- monthly donations start
--

INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('Monthly Donation Activities', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT (select id from plugin_dashboards where title = 'Monthly Donation Activities'), id FROM engine_project_role WHERE `role` IN ('Administrator'));

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Monthly Number Of Requests',
		`type` = 3,
		`x_axis` = 'Period',
		`y_axis` = 'Quantity',
		`publish` = 1,
		`delete` = 0;

INSERT INTO `plugin_reports_reports`
  SET
    `name` = 'Monthly Number Of Requests',
    `summary` = '',
    `sql` = 'SELECT \n		DATE_FORMAT(d.created, \'%Y-%m\') AS `Period`,\n		COUNT(*) AS `Quantity`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= d.created AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > d.created\n	GROUP BY `Period`',
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
    `widget_id` = (select id from plugin_reports_widgets where name = 'Monthly Number Of Requests' limit 1),
    `chart_id` = '12',
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

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  (SELECT d.id, r.id, 1, 1, null, 1, 0 FROM plugin_reports_reports r INNER  JOIN plugin_dashboards d where r.`name` = 'Monthly Number Of Requests' AND d.title = 'Monthly Donation Activities' LIMIT 1);

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Monthly Value Of Requests',
		`type` = 3,
		`x_axis` = 'Period',
		`y_axis` = 'Value',
		`publish` = 1,
		`delete` = 0;

INSERT INTO `plugin_reports_reports`
  SET
    `name` = 'Monthly Value Of Requests',
    `summary` = '',
    `sql` = 'SELECT \n		DATE_FORMAT(d.created, \'%Y-%m\') AS `Period`,\n		SUM(IFNULL(p.value, 0)) AS `Value`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE \'{!DASHBOARD-FROM!}\' <= d.created AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > d.created\n	GROUP BY `Period`',
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
    `widget_id` = (select id from plugin_reports_widgets where name = 'Monthly Value Of Requests' limit 1),
    `chart_id` = '12',
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

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  (SELECT d.id, r.id, 1, 1, null, 1, 0 FROM plugin_reports_reports r INNER  JOIN plugin_dashboards d where r.`name` = 'Monthly Value Of Requests' AND d.title = 'Monthly Donation Activities' LIMIT 1);

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Monthly Number Of Completed Requests',
		`type` = 3,
		`x_axis` = 'Period',
		`y_axis` = 'Quantity',
		`publish` = 1,
		`delete` = 0;

INSERT INTO `plugin_reports_reports`
  SET
    `name` = 'Monthly Number Of Completed Requests',
    `summary` = '',
    `sql` = 'SELECT \n		DATE_FORMAT(d.created, \'%Y-%m\') AS `Period`,\n		COUNT(*) AS `Quantity`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE d.status = \'Completed\' AND \'{!DASHBOARD-FROM!}\' <= d.created AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > d.created\n	GROUP BY `Period`',
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
    `widget_id` = (select id from plugin_reports_widgets where name = 'Monthly Number Of Completed Requests' limit 1),
    `chart_id` = '12',
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

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  (SELECT d.id, r.id, 1, 2, null, 1, 0 FROM plugin_reports_reports r INNER  JOIN plugin_dashboards d where r.`name` = 'Monthly Number Of Completed Requests' AND d.title = 'Monthly Donation Activities' LIMIT 1);

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Monthly Value Of Paid Requests',
		`type` = 3,
		`x_axis` = 'Period',
		`y_axis` = 'Value',
		`publish` = 1,
		`delete` = 0;

INSERT INTO `plugin_reports_reports`
  SET
    `name` = 'Monthly Value Of Paid Requests',
    `summary` = '',
    `sql` = 'SELECT \n		DATE_FORMAT(d.created, \'%Y-%m\') AS `Period`,\n		SUM(IFNULL(p.value, 0)) AS `Value`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE d.status = \'Completed\' AND \'{!DASHBOARD-FROM!}\' <= d.created AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > d.created\n	GROUP BY `Period`',
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
    `widget_id` = (select id from plugin_reports_widgets where name = 'Monthly Value Of Paid Requests' limit 1),
    `chart_id` = '12',
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

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  (SELECT d.id, r.id, 1, 2, null, 1, 0 FROM plugin_reports_reports r INNER  JOIN plugin_dashboards d where r.`name` = 'Monthly Value Of Paid Requests' AND d.title = 'Monthly Donation Activities' LIMIT 1);

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Monthly Number Of Valid Requests Awaiting Decision',
		`type` = 3,
		`x_axis` = 'Period',
		`y_axis` = 'Quantity',
		`publish` = 1,
		`delete` = 0;

INSERT INTO `plugin_reports_reports`
  SET
    `name` = 'Monthly Number Of Valid Requests Awaiting Decision',
    `summary` = '',
    `sql` = 'SELECT \n		DATE_FORMAT(d.created, \'%Y-%m\') AS `Period`,\n		COUNT(*) AS `Quantity`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE d.status = \'Processing\' AND \'{!DASHBOARD-FROM!}\' <= d.created AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > d.created\n	GROUP BY `Period`',
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
    `widget_id` = (select id from plugin_reports_widgets where name = 'Monthly Number Of Valid Requests Awaiting Decision' limit 1),
    `chart_id` = '12',
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

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  (SELECT d.id, r.id, 1, 3, null, 1, 0 FROM plugin_reports_reports r INNER  JOIN plugin_dashboards d where r.`name` = 'Monthly Number Of Valid Requests Awaiting Decision' AND d.title = 'Monthly Donation Activities' LIMIT 1);

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Monthly Value Of Valid Requests Awaiting Decision',
		`type` = 3,
		`x_axis` = 'Period',
		`y_axis` = 'Value',
		`publish` = 1,
		`delete` = 0;

INSERT INTO `plugin_reports_reports`
  SET
    `name` = 'Monthly Value Of Valid Requests Awaiting Decision',
    `summary` = '',
    `sql` = 'SELECT \n		DATE_FORMAT(d.created, \'%Y-%m\') AS `Period`,\n		SUM(IFNULL(p.value, 0)) AS `Value`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE d.status = \'Processing\' AND \'{!DASHBOARD-FROM!}\' <= d.created AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > d.created\n	GROUP BY `Period`',
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
    `widget_id` = (select id from plugin_reports_widgets where name = 'Monthly Value Of Valid Requests Awaiting Decision' limit 1),
    `chart_id` = '12',
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

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  (SELECT d.id, r.id, 1, 3, null, 1, 0 FROM plugin_reports_reports r INNER  JOIN plugin_dashboards d where r.`name` = 'Monthly Value Of Valid Requests Awaiting Decision' AND d.title = 'Monthly Donation Activities' LIMIT 1);

--
-- monthly donations end
--
