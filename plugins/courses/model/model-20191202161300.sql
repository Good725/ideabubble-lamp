/*
ts:2019-12-02 16:13:00
*/


INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_finance', 'Courses / Finance', 'Courses / Finance', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias = 'courses_finance');
