/*
ts:2020-09-03 16:55:00
*/
ALTER TABLE `plugin_todos_todos2`
    ADD COLUMN `allow_manual_grading` TINYINT NOT NULL DEFAULT 0 AFTER `grading_type`;
