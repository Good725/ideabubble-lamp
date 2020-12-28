/*
ts:2015-01-01 00:00:03
*/
-- IBCMS-685
insert into `plugin_reports_reports` set `name` = 'Duplicate Pages', `summary` = '', `sql` = 'SELECT name_tag, COUNT(*) AS duplicates, GROUP_CONCAT(ppages.id) AS page_ids FROM ppages GROUP BY name_tag HAVING duplicates > 1 ORDER BY name_tag', `widget_sql` = '', `category` = '0', `sub_category` = '0', `dashboard` = '0', `created_by` = null, `modified_by` = null, `date_created` = NOW(), `date_modified` = NOW(), `publish` = '1', `delete` = '0', `widget_id` = '15', `chart_id` = '10', `link_url` = '', `link_column` = '', `report_type` = 'sql', `autoload` = '0', `checkbox_column` = '0', `action_button_label` = '', `action_button` = '0', `action_event` = '											', `checkbox_column_label` = '', `autosum` = '0', `column_value` = '', `autocheck` = '0', `custom_report_rules` = '', `bulk_message_sms_number_column` = '', `bulk_message_email_column` = '', `bulk_message_subject_column` = '', `bulk_message_subject` = '', `bulk_message_body_column` = '', `bulk_message_body` = '', `bulk_message_interval` = '', `rolledback_to_version` = null, `php_modifier` = '';

ALTER IGNORE TABLE `ppages_layouts`
ADD COLUMN `source`        BLOB      NULL                                AFTER `layout`,
ADD COLUMN `use_db_source` INT(1)    NOT NULL DEFAULT 0                  AFTER `source`,
ADD COLUMN `publish`       INT(1)    NULL     DEFAULT '1'                AFTER `use_db_source` ,
ADD COLUMN `deleted`       INT(1)    NULL     DEFAULT 0                  AFTER `publish` ,
ADD COLUMN `date_created`  TIMESTAMP NULL     DEFAULT CURRENT_TIMESTAMP  AFTER `deleted` ,
ADD COLUMN `date_modified` TIMESTAMP NULL                                AFTER `date_created` ,
ADD COLUMN `created_by`    INT(11)   NULL                                AFTER `date_modified` ,
ADD COLUMN `modified_by`   INT(11)   NULL                                AFTER `created_by` ;
