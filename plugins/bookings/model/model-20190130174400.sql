/*
ts:2019-01-30 17:44:00
*/

ALTER TABLE `plugin_ib_educate_bookings_has_applications` MODIFY COLUMN `interview_status`  ENUM('Scheduled','No Follow Up','Interviewed','Accepted','Rejected','Cancelled','Not Scheduled');

INSERT IGNORE INTO `plugin_messaging_recipient_providers` (`id`, `plugin`, `class_name`) VALUES ('CMS_CONTACT3', 'contacts3', 'Model_MessagingRecipientProviderContact3');
