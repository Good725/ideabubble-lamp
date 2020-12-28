/*
ts:2016-12-17 10:30:00
*/

DELETE FROM `engine_role_permissions` WHERE
(
  `role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator')
  AND
  `resource_id` = (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_tools_messages')
);

DELETE FROM `engine_role_permissions` WHERE
(
  `role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator')
  AND
  `resource_id` = (SELECT `id` FROM `engine_resources` WHERE `alias` = 'user_tools_help')
);

DELETE FROM `engine_role_permissions` WHERE
(
  `role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator')
  AND
  `resource_id` = (SELECT `id` FROM `engine_resources` WHERE `alias` = 'global_search')
);

DELETE FROM `engine_role_permissions` WHERE
(
  `role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Basic')
  AND
  `resource_id` = (SELECT `id` FROM `engine_resources` WHERE `alias` = 'global_search')
);

DELETE FROM `engine_role_permissions` WHERE
(
  `role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'External User')
  AND
  `resource_id` = (SELECT `id` FROM `engine_resources` WHERE `alias` = 'global_search')
);