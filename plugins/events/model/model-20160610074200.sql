/*
ts:2016-06-10 07:42:00
*/

ALTER TABLE `plugin_events_events` ADD COLUMN `email_note` TEXT;
ALTER TABLE `plugin_events_orders` ADD COLUMN `archived` DATETIME;
ALTER TABLE `plugin_events_orders` ADD COLUMN `archived_by` INT;

INSERT INTO `plugin_messaging_notification_templates` SET `send_interval` = null, `name` = 'event-ticket', `description` = '', `driver` = 'EMAIL', `type_id` = '1', `subject` = 'Ticket Details', `sender` = '', `message` = 'Your tickets are attached.', `overwrite_cms_message` = '0', `page_id` = '0', `header` = '', `footer` = '', `schedule` = null, `date_created` = '2016-06-10 07:52:54', `created_by` = '9', `date_updated` = null, `last_sent` = null, `publish` = '1', `deleted` = '0', `create_via_code` = null, `usable_parameters_in_template` = null, `doc_generate` = null, `doc_helper` = null, `doc_template_path` = null, `doc_type` = null, `category_id` = '0';
