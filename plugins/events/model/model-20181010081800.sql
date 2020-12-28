/*
ts:2018-10-10 08:18:00
*/

select id into @edit_advanced_edit_resource_id_parent from engine_resources where `alias` = 'events';

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'events_edit_advanced', 'Events / Edit Advanced Features', 'Events Edit Advanced Features', @edit_advanced_edit_resource_id_parent);

INSERT IGNORE INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  VALUES
  (
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
    (SELECT `id` FROM `engine_resources` WHERE `alias` = 'events_edit_advanced')
  );
