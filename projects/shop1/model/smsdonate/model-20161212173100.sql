/*
ts:2016-12-12 17:31:00
*/

DELETE FROM `engine_plugins_per_role`;

INSERT INTO engine_plugins_per_role
  (plugin_id, role_id, enabled)
  (SELECT engine_plugins.id, engine_project_role.id, 1  FROM engine_plugins INNER JOIN engine_project_role WHERE engine_plugins.name IN ('contacts2', 'donations', 'dashboards', 'reports') AND engine_project_role.role IN ('Administrator', 'Super User'));


UPDATE engine_settings SET value_live = 'modern', value_stage = 'modern', value_test = 'modern', value_dev = 'modern' WHERE `variable` = 'cms_template';
UPDATE engine_settings SET value_live = '1', value_stage = '1', value_test = '1', value_dev = '1' WHERE `variable` = 'fluid_layout_cms';
UPDATE engine_settings SET value_live = '02', value_stage = '02', value_test = '02', value_dev = '02' WHERE `variable` = 'cms_skin';

