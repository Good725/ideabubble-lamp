/*
ts:2020-10-16 22:40:00
*/

ALTER TABLE `plugin_survey_groups`
    ADD COLUMN `type` ENUM('question', 'prompt') NULL DEFAULT 'question' AFTER `updated_on`;

