/*
ts:2020-02-17 15:52:00
*/

DELETE
FROM `engine_role_permissions`
where `role_id` =
      (SELECT id from engine_project_role where role = 'Administrator' limit 1)
  and resource_id = (SELECT id FROM engine_resources where alias = 'messaging_see_under_developed_features' limit 1);