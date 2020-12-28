/*
ts:2020-04-15 22:35:00
*/
ALTER TABLE `plugin_courses_waitlist`
    ADD COLUMN `created` DATETIME NULL AFTER `message`,
    ADD COLUMN `updated` DATETIME NULL AFTER `created`,
    ADD COLUMN `deleted` TINYINT(4) NOT NULL DEFAULT 0 AFTER `updated`;
