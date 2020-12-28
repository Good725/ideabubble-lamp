/*
ts:2020-09-26 02:10:00
*/

ALTER TABLE `plugin_todos_todos2_has_results`
    ADD COLUMN `examiner_id` INT NULL AFTER `student_id`,
    ADD COLUMN `status` ENUM('Awaiting', 'Started', 'Completed') NULL DEFAULT 'Awaiting' AFTER `comment`;
