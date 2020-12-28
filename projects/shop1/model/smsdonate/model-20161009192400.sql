/*
ts:2016-10-09 19:24:00
*/


INSERT INTO `plugin_reports_reports`
  SET
    `name` = 'Donation Activities',
    `summary` = '',
    `sql` = 'SELECT \n		DATE_FORMAT(d.created, IF(\'{!Period!}\' = \'Year\', \'%Y\', IF(\'{!Period!}\' = \'Month\', \'%Y-%m\', \'%Y-%m-%d\'))) AS `Period`,\n		SUM(IF(d.`status` = \'Completed\', p.`value`, 0)) AS `Value`,\n		COUNT(*) AS `Total Requests`\n		\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE d.created >= \'{!From!}\' AND d.created < DATE_ADD(\'{!To!}\', INTERVAL 1 DAY)\n	GROUP BY `Period`',
    `widget_sql` = '',
    `category` = 0,
    `sub_category` = 0,
    `dashboard` = 0,
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
    `autoload` = 0,
    `checkbox_column` = 0,
    `action_button_label` = '',
    `action_button` = 0,
    `action_event` = '',
    `checkbox_column_label` = '',
    `autosum` = 0,
    `column_value` = '',
    `autocheck` = 0,
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
    `generate_documents` = 0,
    `generate_documents_template_file_id` = 0,
    `generate_documents_pdf` = 0,
    `generate_documents_office_print` = 0,
    `generate_documents_office_print_bulk` = 0,
    `generate_documents_tray` = null,
    `generate_documents_helper_method` = '',
    `generate_documents_link_to_contact` = '';

	SELECT last_insert_id() INTO @refid_plugin_reports_reports_20161009212353_001;
	INSERT INTO `plugin_reports_parameters` SET `report_id` = @refid_plugin_reports_reports_20161009212353_001, `type` = 'date', `name` = 'From', `value` = '', `delete` = 0, `is_multiselect` = 0;
	INSERT INTO `plugin_reports_parameters` SET `report_id` = @refid_plugin_reports_reports_20161009212353_001, `type` = 'date', `name` = 'To', `value` = '', `delete` = 0, `is_multiselect` = 0;
	INSERT INTO `plugin_reports_parameters` SET `report_id` = @refid_plugin_reports_reports_20161009212353_001, `type` = 'dropdown', `name` = 'Period', `value` = 'Year;Month;Day', `delete` = 0, `is_multiselect` = 0;
