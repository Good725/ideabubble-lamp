/*
ts:2016-05-17 14:20:00
*/

select id into @pg_role_id_h from `engine_project_role` r where r.role = 'Parent/Guardian';
select id into @st_role_id_h from `engine_project_role` r where r.role = 'Student';
select id into @homework_plugin_id_h from `engine_plugins` p where p.name = 'homework';

replace into engine_plugins_per_role (plugin_id,role_id,enabled) values (@homework_plugin_id_h, @pg_role_id_h, 1);
replace into engine_plugins_per_role (plugin_id,role_id,enabled) values (@homework_plugin_id_h, @st_role_id_h, 1);
