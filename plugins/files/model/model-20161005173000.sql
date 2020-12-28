/*
ts:2016-10-05 17:30:00
*/


INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'files', 'Files', 'Files');
SELECT `id` INTO @d_2016_10_05_files_resource_id FROM `engine_resources` o where o.`alias` = 'files';
INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'files_view',           'Files / View',           'Files View',           @d_2016_10_05_files_resource_id);
INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'files_edit',           'Files / Edit',           'Files Edit',           @d_2016_10_05_files_resource_id);
INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'files_delete',         'Files / Delete',         'Files Delete',         @d_2016_10_05_files_resource_id);
INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'files_edit_directory', 'Files / Edit directory', 'Files Edit Directory', @d_2016_10_05_files_resource_id);

SELECT `id` INTO @t_2016_10_05_admin_role_id FROM `engine_project_role` `r` WHERE `r`.`role` = 'Administrator';

INSERT INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
 (@t_2016_10_05_admin_role_id, (SELECT `id` FROM `engine_resources` WHERE `alias` = 'files')),
 (@t_2016_10_05_admin_role_id, (SELECT `id` FROM `engine_resources` WHERE `alias` = 'files_view')),
 (@t_2016_10_05_admin_role_id, (SELECT `id` FROM `engine_resources` WHERE `alias` = 'files_edit')),
 (@t_2016_10_05_admin_role_id, (SELECT `id` FROM `engine_resources` WHERE `alias` = 'files_delete')),
 (@t_2016_10_05_admin_role_id, (SELECT `id` FROM `engine_resources` WHERE `alias` = 'files_edit_directory'));
