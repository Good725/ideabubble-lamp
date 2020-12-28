/*
ts:2017-08-21 14:53:00
 */

 INSERT IGNORE INTO `engine_project_role`
 (`role`,`description`,`publish`,`deleted`,`access_type`,`master_group`, `default_dashboard_id`)
 VALUES('Mature Student' , '' , 1 , 0 , 'Front end', 0, 0);

SET @role_id = (SELECT id FROM `engine_project_role` WHERE `role` = 'Mature Student');

INSERT IGNORE INTO `engine_role_permissions` (`role_id`,`resource_id`) VALUES (@role_id,18),
(@role_id, 26),
(@role_id, 27),
(@role_id, 31),
(@role_id, 32),
(@role_id, 58),
(@role_id, 60),
(@role_id, 81),
(@role_id, 90),
(@role_id, 91),
(@role_id, 92),
(@role_id, 94),
(@role_id, 95),
(@role_id, 96),
(@role_id, 97);