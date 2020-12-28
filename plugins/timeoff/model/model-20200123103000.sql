/*
ts:2020-01-23 10:30:00
*/

ALTER TABLE `plugin_timeoff_requests` CHANGE COLUMN `type` `type` ENUM('annual', 'bereavement', 'force majeure', 'sick', 'lieu', 'other') NULL ;
