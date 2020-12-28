/*
ts:2016-11-29 13:00:00
*/

ALTER TABLE `plugin_contacts_mailing_list`
CHANGE COLUMN `name` `name` VARCHAR(255) NOT NULL  ,
ADD COLUMN `summary`       TEXT      NULL                AFTER `name` ,
ADD COLUMN `date_created`  TIMESTAMP NULL                AFTER `summary` ,
ADD COLUMN `date_modified` TIMESTAMP NULL                AFTER `date_created` ,
ADD COLUMN `created_by`    INT(11)   NULL                AFTER `date_modified` ,
ADD COLUMN `modified_by`   INT(11)   NULL                AFTER `created_by` ,
ADD COLUMN `publish`       INT(1)    NOT NULL DEFAULT 1  AFTER `modified_by` ,
ADD COLUMN `deleted`       INT(1)    NOT NULL DEFAULT 0  AFTER `publish` ;
