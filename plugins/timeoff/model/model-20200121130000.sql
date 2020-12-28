/*
ts:2020-01-21 13:00:00
*/
-- Support both "absence" and "force majeure".
ALTER TABLE `plugin_timeoff_requests` CHANGE COLUMN `type` `type` ENUM('absence', 'annual', 'bereavement', 'force majeure', 'sick', 'liu', 'other') NULL ;

-- Rename all "absence" to "force majeure".
UPDATE `plugin_timeoff_requests` SET `type` = 'force majeure' WHERE `type` = 'absense';

-- Remove support for "absence".
ALTER TABLE `plugin_timeoff_requests` CHANGE COLUMN `type` `type` ENUM('annual', 'bereavement', 'force majeure', 'sick', 'liu', 'other') NULL ;
