/*
ts:2019-06-26 17:50:00
*/
INSERT IGNORE INTO `engine_plugins_per_role` (`plugin_id`, `role_id`, `enabled`) VALUES (
  (SELECT `id` FROM `engine_plugins`      WHERE `name` = 'logistics'     LIMIT 1),
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator' LIMIT 1),
  1
);

INSERT IGNORE INTO `engine_notes_types` (`type`, `referenced_table`, `referenced_table_id`, `referenced_table_deleted`) VALUES ('Logistic transfer', 'plugin_logistics_transfer', 'id', 'deleted');

ALTER IGNORE TABLE `plugin_logistics_transfers` ADD COLUMN `booking_id` INT(11) NULL AFTER `dropoff_id`;