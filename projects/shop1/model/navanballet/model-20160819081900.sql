/*
ts:2016-08-19 08:19:00
*/

DELETE FROM engine_plugins_per_role
  WHERE
    plugin_id = (select id from engine_plugins where name = 'families') AND
    role_id = (select id from engine_project_role where role='Administrator');

INSERT INTO engine_plugins_per_role
  (plugin_id, role_id, enabled)
  VALUES
  (
    (select id from engine_plugins where name = 'families'),
    (select id from engine_project_role where role='Administrator'),
    1
  );
