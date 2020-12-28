/*
ts:2020-09-16 01:16:00
*/

ALTER TABLE `plugin_survey`
    ADD COLUMN `shuffle_questions` TINYINT(1) NULL DEFAULT 0 AFTER `contact_id`,
    ADD COLUMN `shuffle_groups` TINYINT(1) NULL DEFAULT 0 AFTER `shuffle_questions`;