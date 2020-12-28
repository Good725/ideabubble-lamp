/*
ts:2018-04-02 14:19:00
*/

ALTER TABLE plugin_contacts3_contacts ADD COLUMN linked_user_id INT;
ALTER TABLE plugin_contacts3_contact_has_preferences ADD COLUMN notification_type ENUM('email', 'sms', 'phone');
ALTER TABLE plugin_contacts3_preferences ADD COLUMN summary TEXT;

UPDATE plugin_contacts3_preferences SET `label` = 'Absenteeism' WHERE `label` = 'Absentee SMS + CALLS';
UPDATE plugin_contacts3_preferences SET `label` = 'Emergencies' WHERE `label` = 'Emergency';
UPDATE plugin_contacts3_preferences SET `label` = 'Course Bookings' WHERE `label` = 'Bookings';


UPDATE plugin_contacts3_preferences SET `summary` = 'Receive contact about urgent matters' WHERE `label` = 'Emergencies';
UPDATE plugin_contacts3_preferences SET `summary` = 'Receive contact in relation to monies paid and due' WHERE `label` = 'Accounts';
UPDATE plugin_contacts3_preferences SET `summary` = 'Receive contact when your child is absent or late' WHERE `label` = 'Absenteeism';
UPDATE plugin_contacts3_preferences SET `summary` = 'Receive information about your course bookings' WHERE `label` = 'Course Bookings';
UPDATE plugin_contacts3_preferences SET `summary` = 'Receive regular notices about our new updates' WHERE `label` = 'Marketing Updates';

ALTER TABLE `plugin_contacts3_contact_has_preferences`
DROP INDEX `contact_id` ,
ADD UNIQUE INDEX `contact_id` (`contact_id`, `preference_id`, `notification_type`);

ALTER TABLE plugin_contacts3_residences ADD COLUMN address_type ENUM('Personal', 'Family', 'Billing') DEFAULT 'Personal';
