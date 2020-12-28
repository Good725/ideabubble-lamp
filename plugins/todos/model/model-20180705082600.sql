/*
ts:2018-07-05 08:26:00
*/


INSERT IGNORE INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  (SELECT roles.id, resources.id FROM engine_project_role roles, engine_resources resources WHERE roles.role = 'Administrator' AND resources.alias in ('todos', 'todos_manage_all', 'todos_edit_from'));

INSERT IGNORE INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  (SELECT roles.id, resources.id FROM engine_project_role roles, engine_resources resources WHERE roles.role IN ('Teacher', 'Student', 'Mature Student', 'Parent/Guardian') AND resources.alias IN ('todos'));
