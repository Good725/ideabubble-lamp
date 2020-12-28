/*
ts:2016-02-08 13:53:00
*/
INSERT INTO `resources`
  (`type_id`, `alias`, `name`, `parent_controller`, `description`)
  VALUES
  (0, 'propman', 'Properties', null, 'Properties');
SELECT LAST_INSERT_ID() INTO @resource_id_1211;
INSERT INTO `engine_role_permissions` (role_id, resource_id) VALUES (1, @resource_id_1211);

INSERT INTO `resources`
  (`type_id`, `alias`, `name`, `parent_controller`, `description`)
  VALUES
  (1, 'propman_bookings', 'Properties / Bookings', @resource_id_1211, 'Booking Details');
SELECT LAST_INSERT_ID() INTO @resource_id_1212;
INSERT INTO `engine_role_permissions` (role_id, resource_id) VALUES (1, @resource_id_1212);
