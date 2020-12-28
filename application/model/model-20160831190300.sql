/*
ts:2016-08-31 19:03:00
*/

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'global_search', 'Global Search', 'Global / Search');

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias = 'global_search');
