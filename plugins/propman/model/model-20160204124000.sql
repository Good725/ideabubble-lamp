/*
ts:2016-02-02 10:10:00
*/
INSERT IGNORE INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `sender`, `date_created`, `date_updated`, `created_by`, `publish`, `deleted`)
SELECT 'new_booking_admin', 'Email sent to the administrator when someone makes a booking', 'EMAIL', `type`.`id`, 'New Booking', 'testing@websitecms.ie',  CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), `users`.`id`, '1', '0'
FROM `plugin_messaging_notification_types` `type`
LEFT JOIN `users` ON 1 = 1
WHERE `type`.`title` = 'email'
AND `users`.`email` = 'super@ideabubble.ie';


INSERT IGNORE INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `sender`, `date_created`, `date_updated`, `created_by`, `publish`, `deleted`)
SELECT 'new_booking_customer', 'Email sent to the end user when they make a booking', 'EMAIL', `type`.`id`, 'Thank you for booking with us', 'testing@websitecms.ie',  CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), `users`.`id`, '1', '0'
FROM `plugin_messaging_notification_types` `type`
LEFT JOIN `users` ON 1 = 1
WHERE `type`.`title` = 'email'
AND `users`.`email` = 'super@ideabubble.ie';
