/*
ts:2016-03-17 23:42:00
*/

SELECT `id` INTO @reports_id_20160317 FROM resources WHERE `alias` = 'reports';
INSERT INTO `resources` (`type_id`,`alias`,`name`,`parent_controller`,`description`) VALUES (2, 'reports_delete', 'Report / Delete', @reports_id_20160317, '');

SELECT `id` INTO @settings_id_20160317 FROM `resources` WHERE `alias`='settings';
SELECT `id` INTO @administrator_id_20160317 FROM `engine_project_role` WHERE `role`='Administrator';
INSERT IGNORE INTO `engine_role_permissions` (role_id, resource_id) VALUES (@administrator_id_20160317, @settings_id_20160317);
