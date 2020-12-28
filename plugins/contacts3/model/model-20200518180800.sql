/*
ts:2020-05-18 18:08:00
*/


INSERT INTO `engine_resources`
(`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES
(1, 'user_profile_documents', 'User / Profile / Documents', 'User / Profile / Documents', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user'));

INSERT INTO engine_role_permissions
(role_id, resource_id)
    (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Student', 'Mature Student') AND e.alias in ('user_profile_documents'));

