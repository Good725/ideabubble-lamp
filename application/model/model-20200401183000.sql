/*
ts:2020-04-01 18:30:00
*/

ALTER TABLE `engine_users` ADD COLUMN `notifications_last_checked` TIMESTAMP NULL AFTER `can_login`;
