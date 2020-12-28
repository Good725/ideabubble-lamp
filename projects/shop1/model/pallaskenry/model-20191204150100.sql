/*
ts:2019-12-04 15:01:00
*/

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Student' AND e.alias = 'courses_view_mycourses');
