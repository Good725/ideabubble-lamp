/*
ts:2020-03-03 12:00:00
*/

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES
  ('1', 'messaging_delete_template', 'Messaging / Delete template', 'Ability to delete messaging templates, except system ones', (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'messaging')),
  ('1', 'messaging_delete_system_template', 'Messaging / Delete system template', 'Ability to delete system messaging templates', (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'messaging'))
;

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES (
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator'),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'messaging_delete_template')
),(
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Super User'),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'messaging_delete_template')
), (
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Super User'),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'messaging_delete_system_template')
);