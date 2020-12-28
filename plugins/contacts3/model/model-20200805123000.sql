/*
ts:2020-08-05 12:30:00
*/

-- Add "user_profile_organisation" permission.
INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  ('1', 'user_profile_organisation', 'User / Profile / Organisation', 'Access the organisation section of the profile', (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'user'));

-- Assign the "user_profile_organisation" permission to a few user groups.
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `id`, (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_profile_organisation' LIMIT 1)
  FROM `engine_project_role`
  WHERE `role` IN ('Super user', 'Administrator', 'Org rep');

