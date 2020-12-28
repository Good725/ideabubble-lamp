/*
ts:2016-01-11 16:45:00
*/

INSERT IGNORE INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `sender`, `date_created`, `date_updated`, `publish`, `deleted`)
SELECT 'register_account_user',  'Email sent to the end user when they register an account', 'EMAIL', `id`, 'Email confirmation', 'testing@websitecms.ie', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), '1', '0'
FROM `plugin_messaging_notification_types` WHERE `title` = 'email';

INSERT IGNORE INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `sender`, `date_created`, `date_updated`, `publish`, `deleted`)
SELECT 'register_account_admin', 'Email sent to the administrator when someone registers for an account', 'EMAIL', `id`, 'Account registration', 'testing@websitecms.ie',  CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), '1', '0'
FROM `plugin_messaging_notification_types` WHERE `title` = 'email';
