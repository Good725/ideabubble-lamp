/*
ts:2020-04-22 23:01:00
*/
ALTER TABLE `plugin_courses_waitlist`
    CHANGE COLUMN `updated_by` `modified_by` INT(11) NULL DEFAULT NULL ;
ALTER TABLE `plugin_courses_waitlist`
    CHANGE COLUMN `date_updated` `date_modified` DATETIME NULL DEFAULT NULL ;
