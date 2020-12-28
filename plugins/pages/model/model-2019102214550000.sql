/*
ts:2019-10-22 14:55:00
*/

ALTER TABLE `plugin_pages_pages` CHANGE COLUMN `footer` `footer` TEXT NULL DEFAULT NULL ;

-- Add permission for drafts
INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES
(1, 'pages_save_draft', 'Pages / Save draft', 'Ability to save draft versions of pages, so changes can be previewed without updating the public site.');

UPDATE `engine_resources`
SET `parent_controller` = (SELECT `r2`.`id` FROM (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'pages' LIMIT 1) `r2`)
WHERE `alias` = 'pages_save_draft';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'pages_save_draft')
)/* 1.1 */;

