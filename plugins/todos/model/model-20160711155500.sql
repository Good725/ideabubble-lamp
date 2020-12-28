/*
ts:2016-07-11 15:55:00
*/

SELECT id INTO @todos_resource_id_20160711y FROM `engine_resources` o WHERE o.`alias` = 'todos';
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'todos_edit_from', 'Todos / Edit From', 'Edit From User', @todos_resource_id_20160711y);
