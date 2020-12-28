/*
ts:2017-01-16 13:58:00
*/

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'contacts3', 'KES Contacts', 'KES Contacts / Full Access');

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias = 'contacts3');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_limited_family_access', 'KES Contacts / Limited Family Access', 'KES Contacts / Limited Family Access', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Parent/Guardian') AND e.alias = 'contacts3_limited_family_access');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_limited_view', 'KES Contacts / Limited View', 'KES Contacts / Limited View', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Student') AND e.alias = 'contacts3_limited_view');

