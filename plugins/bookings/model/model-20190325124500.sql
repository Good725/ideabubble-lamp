/*
ts:2019-03-25 12:45:00
*/
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'applications', 'Applications', 'Applications');

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Administrator', 'Super User') AND e.alias = 'applications');
