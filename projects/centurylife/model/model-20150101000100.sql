/*
ts:2015-01-01 00:01:00
*/

-- ---------------------------------
-- CLP-12 - Contact form notification not showing on LIVE
-- ---------------------------------
UPDATE `plugin_notifications_event`
SET `description` = 'Contact us', `subject` = 'Contact us'
WHERE `name` = 'contact_form';
INSERT IGNORE INTO `plugin_notifications_event` (`name`, `description`, `from`, `subject`) VALUE ('contact-notification-callback', 'Request a call back', 'info@centurylife.ie', 'Request call back');

-- ---------------------------------
-- CLP-14 - "Contact Us" tab mislabelled
-- ---------------------------------
UPDATE `plugin_notifications_event`
SET `description` = 'Contact us', `subject` = 'Contact us'
WHERE `name` = 'contact-form';

