/*
ts:2016-11-17 11:22:00
*/

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Study Offers',
		`type` = 10,
		`x_axis` = '',
		`y_axis` = '',
		`publish` = 1,
		`delete` = 0;
SELECT LAST_INSERT_ID() INTO @admin_study_offers_widget_id;

insert into `plugin_reports_reports` set `name` = 'Study Offers',
 `summary` = '',
 `sql` = 'select Discount, Starts, Ends, Details from \n(\n	select \n		d.title as `Discount`, \n		date_format(`d`.`valid_from`, \'%d/%m/%Y\') as `Starts`, \n		date_format(`d`.`valid_to`, \'%d/%m/%Y\') as `Ends`, d.id as `Id`, \n		concat(\'<a href=\"/admin/bookings/add_edit_discount/\', d.id, \'\" class=\"popinit icon-eye\" target=\"_blank\" data-placement=\"left\" rel=\"popover\" data-content=\"\', CONCAT_WS(\', \', IF(d.`code` is not null and d.`code` <> \'\', CONCAT(\'Coupon Code:\', d.`code`), NULL), IF(d.amount_type = \'Fixed\', CONCAT(\'€\', d.amount, \' Off\'), NULL), IF(d.amount_type = \'Percent\', CONCAT(ROUND(d.amount), \'% Off\'), NULL), IF(d.amount_type = \'Quantity\', CONCAT(ROUND(d.amount), \' of \', d.item_quantity_max, \' Free\'), NULL)), \'\"></a>\') as `Details` \n	from plugin_bookings_discounts d where d.`delete` = 0 order by d.title\n) s',
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
 `widget_id` = @admin_study_offers_widget_id,
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

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  ((select id from plugin_dashboards where `title` = 'Admin'), (select id from plugin_reports_reports where `name` = 'Study Offers' limit 1), 1, 1, null, 1, 0);
