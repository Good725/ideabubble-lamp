/*
ts:2018-10-03 10:15:00
*/

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'timetables', 'Timetables', 'Timetables');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'timetables_view_all', 'Timetables View All', 'Timetables View All', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'timetables'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'timetables_view_limited', 'Timetables View Limited', 'Timetables View Limited', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'timetables'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias = 'timetables');

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias = 'timetables_view_all');
