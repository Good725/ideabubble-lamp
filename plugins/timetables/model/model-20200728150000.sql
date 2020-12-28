/*
ts:2020-07-28 15:00:00
*/

-- New permission for toggling the ability to use the "timetables" -> "add slot" button
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (
  1,
  'timetables_add_slot',
  'Timetables / Add slot',
  'Ability to add slots to a timetable',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'timetables' LIMIT 1)
);

-- Enable the permission for administrators.
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES (
  (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator'),
  (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'timetables_add_slot')
);