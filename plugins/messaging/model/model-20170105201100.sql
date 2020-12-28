/*
ts:2017-01-05 20:11:00
*/

ALTER TABLE plugin_messaging_messages ADD COLUMN replyto TEXT;
ALTER TABLE plugin_messaging_notification_templates ADD COLUMN replyto TEXT;
