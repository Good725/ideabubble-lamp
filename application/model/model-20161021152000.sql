/*
ts:2016-10-21 15:20:00
*/

ALTER IGNORE TABLE `engine_project_role` ADD COLUMN `default_dashboard_id` INT(11) NULL DEFAULT NULL  AFTER `master_group` ;
