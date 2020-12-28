/*
ts:2016-10-09 16:13:00
*/

INSERT INTO plugin_donations_products (id, name, value) VALUES (1111, "Eggs", 15.0);
INSERT INTO plugin_donations_products (id, name, value) VALUES (2222, "Flour", 20.0);
INSERT INTO plugin_donations_products (id, name, value) VALUES (1234, "Milk", 10.0);

INSERT INTO `plugin_reports_reports`
  SET
    `name` = 'Donation Stats',
    `summary` = '',
    `sql` = 'SELECT \n		c.mobile AS `Mobile`, \n\n		SUM(IF(d.`status` = \'Processing\', 1, 0)) AS `Processing Requests`,\n		SUM(IF(d.`status` = \'Processing\', IF(p.`value` IS NULL, 0, p.`value`), 0)) AS `Processing Value`,\n\n		SUM(IF(d.`status` = \'Confirmed\', 1, 0)) AS `Confirmed Requests`,\n		SUM(IF(d.`status` = \'Confirmed\', IF(p.`value` IS NULL, 0, p.`value`), 0)) AS `Confirmed Value`,\n\n		SUM(IF(d.`status` = \'Rejected\', 1, 0)) AS `Rejected Requests`,\n		SUM(IF(d.`status` = \'Rejected\', IF(p.`value` IS NULL, 0, p.`value`), 0)) AS `Rejected Value`,\n\n		SUM(IF(d.`status` = \'Completed\', 1, 0)) AS `Completed Requests`,\n		SUM(IF(d.`status` = \'Completed\', IF(p.`value` IS NULL, 0, p.`value`), 0)) AS `Completed Value`,\n\n		SUM(IF(p.id IS NULL, 1, 0)) AS `Invalid Requests`,\n		\n		COUNT(*) AS `Total Requests`,\n		SUM(IF(p.`value` IS NULL, 0, p.`value`)) AS `Total Value`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	GROUP BY d.contact_id',
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
    `report_type` = 'sql',
    `autoload` = 1,
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
    `php_modifier` = '';

INSERT INTO `plugin_messaging_notification_templates`
  SET
    `send_interval` = null,
    `name` = 'donation-sms-received-reply',
    `description` = '',
    `driver` = 'SMS',
    `type_id` = 4,
    `subject` = '',
    `sender` = '',
    `message` = 'Your request has been received',
    `overwrite_cms_message` = '0',
    `page_id` = '0',
    `header` = '',
    `footer` = '',
    `schedule` = null,
    `date_created` = NOW(),
    `date_updated` = NOW(),
    `last_sent` = null,
    `publish` = 1,
    `deleted` = 0,
    `create_via_code` = 'DONATION SMS reply',
    `usable_parameters_in_template` = null,
    `category_id` = '1';

	INSERT INTO `plugin_messaging_notification_templates`
  SET
    `send_interval` = null,
    `name` = 'donation-sms-received-invalid-reply',
    `description` = '',
    `driver` = 'SMS',
    `type_id` = 4,
    `subject` = '',
    `sender` = '',
    `message` = '$code is invalid',
    `overwrite_cms_message` = '0',
    `page_id` = '0',
    `header` = '',
    `footer` = '',
    `schedule` = null,
    `date_created` = NOW(),
    `date_updated` = NOW(),
    `last_sent` = null,
    `publish` = 1,
    `deleted` = 0,
    `create_via_code` = 'DONATION SMS reply',
    `usable_parameters_in_template` = '$code',
    `category_id` = '1';
