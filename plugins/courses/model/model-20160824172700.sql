/*
ts:2016-08-24 17:27:00
*/

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'courses', 'Courses', 'Courses / Full Access');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_limited_access', 'Courses / Limited Access', 'Courses / Limited Access', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias = 'courses');

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('External User', 'Parent/Guardian', 'Student') AND e.alias = 'courses_limited_access');

