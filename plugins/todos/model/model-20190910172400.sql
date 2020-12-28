/*
ts: 2019-09-10 17:24:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
  VALUES
  ('todos_api_display_tasks', 'TODOS API Display Tasks', 'todos', '0', '0', '0', '0', '0', 'TODOS API Display Tasks', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
  VALUES
  ('todos_api_display_assignments', 'TODOS API Display Assignments', 'todos', '1', '1', '1', '1', '0', 'TODOS API Display Assignments', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
  VALUES
  ('todos_api_display_assesments', 'TODOS API Display Term-Assessments', 'todos', '0', '0', '0', '0', '0', 'TODOS API Display Assesments', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
  VALUES
  ('todos_api_display_tests', 'TODOS API Display Class-Tests', 'todos', '0', '0', '0', '0', '0', 'TODOS API Display Tests', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
  VALUES
  ('todos_api_display_exams', 'TODOS API Display State-Exams', 'todos', '0', '0', '0', '0', '0', 'TODOS API Display Exams', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

select id into @todo_id_20190910 from engine_resources where `alias`='todos';
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'todos_edit_create_tasks', 'Todos / Edit / Create Task', 'Todos / Edit / Create Task', @todo_id_20190910);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'todos_edit_create_assignments', 'Todos / Edit / Create Assignment', 'Todos / Edit / Create Assignment', @todo_id_20190910);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'todos_edit_create_assesments', 'Todos / Edit / Create Assesment', 'Todos / Edit / Create Term-Assessment', @todo_id_20190910);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'todos_edit_create_tests', 'Todos / Edit / Create Test', 'Todos / Edit / Create Class-Test', @todo_id_20190910);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'todos_edit_create_exams', 'Todos / Edit / Create Exam', 'Todos / Edit / Create State-Exam', @todo_id_20190910);

INSERT IGNORE INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Administrator', 'Super User') AND e.alias like 'todos_edit_create_%');

