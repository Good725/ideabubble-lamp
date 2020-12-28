/*
ts:2016-01-21 22:51:00
*/

ALTER TABLE `plugin_messaging_notification_templates` ADD COLUMN doc_generate TINYINT(1);
ALTER TABLE `plugin_messaging_notification_templates` ADD COLUMN doc_helper VARCHAR(200);
ALTER TABLE `plugin_messaging_notification_templates` ADD COLUMN doc_template_path VARCHAR(200);
ALTER TABLE `plugin_messaging_notification_templates` ADD COLUMN doc_type ENUM('PDF', 'DOCX');

