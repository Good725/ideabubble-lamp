/*
ts:2016-07-11 13:48:00
*/

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'todos', 'To dos', 'To dos');
SELECT id INTO @todos_resource_id_20160711x FROM `engine_resources` o WHERE o.`alias` = 'todos';
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'todos_manage_all', 'Todos / Manage All', 'Manage All Todos', @todos_resource_id_20160711x);