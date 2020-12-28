/*
ts:2016-03-15 09:20:00
*/

UPDATE IGNORE `plugin_messaging_notification_template_targets`
SET   `target_type` = 'CMS_USER',
      `target`      = (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE `template_id` IN (SELECT `id` FROM `plugin_messaging_notification_templates` WHERE `name` = 'user-email-verification');
