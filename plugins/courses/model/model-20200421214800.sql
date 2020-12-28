/*
ts:2020-04-21 21:48:00
*/
ALTER TABLE `plugin_courses_waitlist`
    CHANGE COLUMN `created` `date_created` DATETIME NULL DEFAULT NULL ,
    CHANGE COLUMN `updated` `date_updated` DATETIME NULL DEFAULT NULL ;
ALTER TABLE `plugin_courses_waitlist`
    ADD COLUMN `created_by` INT NULL AFTER `date_updated`,
    ADD COLUMN `updated_by` INT NULL AFTER `created_by`;
