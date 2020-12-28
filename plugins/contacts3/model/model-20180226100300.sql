/*
ts:2018-02-26 10:03:00
*/

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_settings', 'KES Contacts / Settings', 'KES Contacts / Settings', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Administrator') AND e.alias = 'contacts3_settings');

