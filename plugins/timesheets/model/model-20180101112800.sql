/*
ts:2018-01-01 11:28:00
*/

insert ignore into engine_role_permissions
	(role_id, resource_id)
	(select r.id, o.id from engine_project_role r, engine_resources o where r.role='Manager' and o.alias in ('timesheets_list', 'timesheets_log_time'));
