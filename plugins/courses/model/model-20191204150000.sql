/*
ts:2019-12-04 15:00:00
*/

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_view_mycourses', 'Courses / My courses', 'View the screen containing a list of courses you have booked', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));


INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_view_mycourses_global', 'Courses / My courses', 'View the my_course screen, even for courses you have not booked', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));


INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias = 'courses_view_mycourses');

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias = 'courses_view_mycourses_global');

UPDATE `engine_resources` SET `name` = 'Courses / My courses / Global' WHERE `alias` = 'courses_view_mycourses_global';