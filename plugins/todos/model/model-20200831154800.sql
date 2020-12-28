/*
ts:2020-08-31 15:48:00
*/

ALTER TABLE `plugin_todos_todos2`
    ADD COLUMN `delivery_mode` ENUM('Classroom', 'Online') NULL DEFAULT 'Classroom' AFTER `priority`;
