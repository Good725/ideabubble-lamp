/*
ts:2017-02-13 16:00:00
*/

delete engine_plugins_per_role
	from engine_plugins_per_role inner join engine_plugins on engine_plugins_per_role.plugin_id = engine_plugins.id where engine_plugins.friendly_name = 'Homework';

insert into engine_plugins_per_role
  (plugin_id, role_id, enabled)
  (select engine_plugins.id, engine_project_role.id, 1 from engine_plugins, engine_project_role where engine_plugins.friendly_name = 'Homework' and engine_project_role.role in ('Administrator', 'Teacher'));

insert into engine_role_permissions
  (role_id, resource_id)
  (select o.id, r.id from engine_resources r, engine_project_role o where r.alias in ('homework', 'homework_index', 'homework_view', 'homework_edit', 'homework_delete') and o.role = 'Administrator');

insert into engine_plugins_per_role
  (plugin_id, role_id, enabled)
  (select engine_plugins.id, engine_project_role.id, 1 from engine_plugins, engine_project_role where engine_plugins.friendly_name = 'Homework' and engine_project_role.role in ('Parent/Guardian', 'Student'));

insert into engine_role_permissions
  (role_id, resource_id)
  (select o.id, r.id from engine_resources r, engine_project_role o where r.alias in ('homework_index_limited', 'homework_view_limited') and o.role in ('Parent/Guardian', 'Student'));
