/*
ts: 2019-09-16 13:07:00
*/

ALTER TABLE engine_project_role ADD COLUMN allow_api_login TINYINT NOT NULL DEFAULT 1;
ALTER TABLE engine_project_role ADD COLUMN allow_frontend_login TINYINT NOT NULL DEFAULT 1;
DELETE engine_role_permissions
	FROM engine_role_permissions INNER JOIN engine_resources ON engine_role_permissions.resource_id = engine_resources.id
	WHERE engine_resources.alias = 'api_login';
DELETE FROM engine_resources WHERE engine_resources.alias = 'api_login';
UPDATE engine_project_role set allow_api_login=1,allow_frontend_login=1 WHERE `role`='Administrator';
