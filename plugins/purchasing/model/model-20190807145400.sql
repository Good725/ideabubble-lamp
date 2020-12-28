/*
ts:2019-08-07 14:54:00
*/

ALTER TABLE `plugin_purchasing_purchases`
    CHANGE COLUMN `business_id` `department_id` INT(11) NULL DEFAULT NULL;
