/*
ts:2020-09-25 02:21:00
*/

ALTER TABLE `plugin_todos_todos2_has_assigned_contacts`
    ADD COLUMN `role` ENUM('Student', 'Examiner') NOT NULL DEFAULT 'Student' AFTER `modified_by`;
