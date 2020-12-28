/*
ts:2019-07-13 17:17:00
*/

insert ignore into engine_resources
	(type_id, `alias`, name, parent_controller, description)
	(select 0, name, friendly_name, null, note_body from engine_plugins);

insert ignore into engine_role_permissions
	(role_id, resource_id)
	(select pr.role_id, r.id from engine_plugins_per_role pr inner join engine_plugins p on pr.plugin_id = p.id inner join engine_resources r on p.`name` = r.alias where pr.enabled = 1);

insert ignore into engine_role_permissions
	(role_id, resource_id)
	(select r.id, o.id from engine_project_role r, engine_resources o where r.role = 'Administrator' and o.alias like 'settings%');

delete engine_role_permissions
	from engine_role_permissions
		inner join engine_plugins_per_role on engine_role_permissions.role_id = engine_plugins_per_role.role_id
		inner join engine_plugins on engine_plugins_per_role.plugin_id = engine_plugins.id
		inner join engine_resources on engine_plugins.`name` = engine_resources.alias and engine_role_permissions.resource_id = engine_resources.id
	where engine_plugins_per_role.enabled = 0;

drop table engine_plugins_per_role;
