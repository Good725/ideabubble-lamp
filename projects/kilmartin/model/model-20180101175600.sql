/*
ts:2018-01-01 17:56:00
*/

insert ignore into engine_role_permissions
	(role_id,resource_id)
	(select r.id,o.id from engine_project_role r join engine_resources o where r.role='Teacher' and o.alias = 'courses_limited_access');
