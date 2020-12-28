/*
ts:2018-08-02 08:54:00
*/

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'user_profile_education', 'User / Profile / Education', 'User / Profile / Education', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'user_profile_preferences', 'User / Profile / Preferences', 'User / Profile / Preferences', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'user_profile_email', 'User / Profile / Email settings', 'User / Profile / Email settings', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Administrator') AND e.alias in ('user_profile_education', 'user_profile_preferences', 'user_profile_email'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Student', 'Mature Student') AND e.alias in ('user_profile_education'));

