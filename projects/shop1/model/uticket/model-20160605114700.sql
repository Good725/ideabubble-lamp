/*
ts:2016-06-05 11:47:00
*/

replace into engine_role_permissions
	(role_id,resource_id)
	values
	((select id from engine_project_role o where o.role = 'External User'), (select id from engine_resources r where r.`alias` = 'user_profile'));
