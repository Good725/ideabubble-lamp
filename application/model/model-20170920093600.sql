/*
ts:2017-09-20 09:36:00
*/

select id into @user_id from engine_resources where alias = 'user' /**/;
insert into engine_resources
	(type_id, name, alias, parent_controller)
	values
	(1, 'User Edit', 'user_edit', @user_id);

insert into engine_resources
	(type_id, name, alias, parent_controller)
	values
	(1, 'User View', 'user_view', @user_id);

insert into engine_resources
	(type_id, name, alias, parent_controller)
	values
	(0, 'Groups', 'roles', 0);

select id into @group_id from engine_resources where alias = 'roles';
insert into engine_resources
	(type_id, name, alias, parent_controller)
	values
	(1, 'Group Edit', 'role_edit', @group_id);

insert into engine_resources
	(type_id, name, alias, parent_controller)
	values
	(1, 'Group View', 'role_view', @group_id);

insert into engine_resources
	(type_id, name, alias, parent_controller)
	values
	(0, 'Permissions', 'permissions', 0);

insert ignore into engine_role_permissions
	(resource_id, role_id)
	(select resources.id, roles.id from engine_resources resources, engine_project_role roles where resources.alias in ('roles', 'role_edit', 'role_view', 'permissions', 'user_edit', 'user_view') and roles.`role` in ('Super User', 'Administrator'));
