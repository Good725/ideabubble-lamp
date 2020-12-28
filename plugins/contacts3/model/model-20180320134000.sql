/*
ts:2018-03-20 13:40:00
*/

ALTER TABLE plugin_contacts3_contact_has_subject_preferences ADD COLUMN level_id INT;
UPDATE plugin_courses_levels SET `delete` = 1 WHERE `level` NOT IN ('Higher', 'Ordinary', 'Foundation');

ALTER TABLE plugin_contacts3_contacts ADD COLUMN cycle ENUM('Junior', 'Senior', 'Transition');

ALTER TABLE plugin_contacts3_contacts ADD COLUMN courses_i_would_like TEXT;
