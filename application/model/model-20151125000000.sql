/*
ts:2015-11-25 00:00:00
engine.application:20151125000000
*/
-- IBCMS-729
DROP TABLE IF EXISTS `plugins_backup`;
DROP TABLE IF EXISTS `project_role_backup`;
DROP TABLE IF EXISTS `settings_backup`;
DROP TABLE IF EXISTS `plugins_backup`;
ALTER TABLE `activities` RENAME TO `engine_activities`;
ALTER TABLE `activities_actions` RENAME TO `engine_activities_actions`;
ALTER TABLE `activities_item_types` RENAME TO `engine_activities_item_types`;
ALTER TABLE `counties` RENAME TO `engine_counties`;
ALTER TABLE `csv` RENAME TO `engine_csv`;
ALTER TABLE `feeds` RENAME TO `engine_feeds`;
ALTER TABLE `labels` RENAME TO `engine_labels`;
ALTER TABLE `loginlogs` RENAME TO `engine_loginlogs`;
ALTER TABLE `page_redirects` RENAME TO `engine_page_redirects`;
ALTER TABLE `permissions` RENAME TO `engine_permissions`;
ALTER TABLE `plugins_per_role` RENAME TO `engine_plugins_per_role`;
ALTER TABLE `project_role` RENAME TO `engine_project_role`;
ALTER TABLE `role_permissions` RENAME TO `engine_role_permissions`;
ALTER TABLE `user_tokens` RENAME TO `engine_user_tokens`;
ALTER TABLE `user_group` RENAME TO `engine_user_group`;

-- WARNING the query below maynot run in all projects. move to specific project directory if needed
-- ALTER IGNORE TABLE `pages` RENAME TO `engine_pages`;
