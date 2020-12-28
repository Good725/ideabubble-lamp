/*
ts:2017-01-24 09:29:00
*/

INSERT INTO `plugin_reports_reports`
  (`name`, `summary`, `sql`, `widget_sql`, `category`, `sub_category`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `link_url`, `link_column`, `report_type`, `autoload`, `checkbox_column`, `action_button_label`, `action_button`, `action_event`, `checkbox_column_label`, `autosum`, `column_value`, `autocheck`, `custom_report_rules`, `bulk_message_sms_number_column`, `bulk_message_email_column`, `bulk_message_subject_column`, `bulk_message_subject`, `bulk_message_body_column`, `bulk_message_body`, `bulk_message_interval`, `rolledback_to_version`, `php_modifier`, `generate_documents`, `generate_documents_template_file_id`, `generate_documents_pdf`, `generate_documents_office_print`, `generate_documents_office_print_bulk`, `generate_documents_tray`, `generate_documents_helper_method`, `generate_documents_link_to_contact`, `totals_columns`, `totals_group`)
  VALUES
  ('Newsletter subscriptions', '', 'select \n		c.first_name as `First Name`, c.last_name as `Last Name`, c.email as `Email`, c.last_modification as `Date` \n	from plugin_contacts_contact c \n		inner join plugin_contacts_mailing_list m on c.mailing_list \n	where m.`name` = \'Newsletter\' and c.deleted = 0 and (c.first_name != \'\' or c.last_name != \'\' or c.email != \'\') and c.last_modification >= \'{!From!}\' and c.last_modification < DATE_ADD(\'{!To!}\', INTERVAL 1 DAY)\n	order by c.first_name, c.last_name', '', 0, 0, 0, NULL, NULL, now(), now(), 1, 0, '', '', 'sql', 0, 0, '', 0, '', '', 0, '', 0, '', '', '', '', '', '', '', '', NULL, '', 0, 0, 0, 0, 0, NULL, '', '', '', '');

INSERT INTO plugin_reports_parameters
  (report_id, `type`, `name`, `value`, `delete`, `is_multiselect`)
  VALUES
  ((select id from plugin_reports_reports where name='Newsletter subscriptions' limit 1), 'date', 'From', '', 0, 0);

INSERT INTO plugin_reports_parameters
  (report_id, `type`, `name`, `value`, `delete`, `is_multiselect`)
  VALUES
  ((select id from plugin_reports_reports where name='Newsletter subscriptions' limit 1), 'date', 'To', '', 0, 0);
