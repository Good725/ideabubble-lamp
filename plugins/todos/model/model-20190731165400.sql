/*
ts:2019-07-31 16:54:00
*/

CREATE TABLE `plugin_exams_exams_has_assigned_contacts`
(
    `exam_id`    INT(11) NOT NULL,
    `contact_id` INT(11) NOT NULL
);

ALTER TABLE `plugin_exams_exams`
    CHANGE COLUMN `type` `type` ENUM ('Assignment', 'Class Test', 'Term Assessment', 'State Exam') NULL DEFAULT NULL;

