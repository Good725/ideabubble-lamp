/*
ts:2020-09-08 23:33:00
*/

INSERT INTO `engine_resources`
(`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES
(1, 'user_profile_date_of_birth', 'User / Profile / Show date of birth', 'User / Profile / Show date of birth', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user'));

INSERT INTO `engine_resources`
(`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES
(1, 'user_profile_gender', 'User / Profile / Show gender', 'User / Profile / Show gender', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user'));

INSERT INTO `engine_resources`
(`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES
(1, 'user_profile_medical_conditions', 'User / Profile / Show medical conditions', 'User / Profile / Show medical conditions', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user'));

INSERT INTO `engine_resources`
(`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES
(1, 'user_profile_nationality', 'User / Profile / Show nationality', 'User / Profile / Show nationality', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user'));

INSERT INTO engine_role_permissions
(role_id, resource_id)
(SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Administrator') AND e.alias in ('user_profile_date_of_birth', 'user_profile_gender','user_profile_medical_conditions', 'user_profile_nationality'));



