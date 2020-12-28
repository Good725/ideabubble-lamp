/*
ts:2020-07-09 16:08:00
*/

ALTER TABLE `engine_remote_sync`
    ADD COLUMN `delete` TINYINT NULL DEFAULT 0 AFTER `synced`;