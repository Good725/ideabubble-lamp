/*
ts:2020-09-01 23:00:00
*/

INSERT IGNORE INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
VALUES
('todos_site_display_tasks', 'TODOS Site Display Tasks', 'todos', '0', '0', '0', '0', '0', 'TODOS Site Display Tasks', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

INSERT IGNORE INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
VALUES
('todos_site_display_assignments', 'TODOS Site Display Assignments', 'todos', '1', '1', '1', '1', '0', 'TODOS Site Display Assignments', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

INSERT IGNORE INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
VALUES
('todos_site_display_assesments', 'TODOS Site Display Term-Assessments', 'todos', '0', '0', '0', '0', '0', 'TODOS Site Display Assesments', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

INSERT IGNORE INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
VALUES
('todos_site_display_tests', 'TODOS Site Display Class-Tests', 'todos', '0', '0', '0', '0', '0', 'TODOS Site Display Tests', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

INSERT IGNORE INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
VALUES
('todos_site_display_exams', 'TODOS Site Display State-Exams', 'todos', '0', '0', '0', '0', '0', 'TODOS Site Display Exams', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
VALUES
('todos_site_allow_online_exams', 'TODOS Site Allow Online Exams', 'todos', '0', '0', '0', '0', '0', 'TODOS Site Allow Online Exams', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

INSERT IGNORE INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
VALUES
('todos_site_allow_oral_assignments', 'TODOS Site Allow Oral Assignments', 'todos', '0', '0', '0', '0', '0', 'TODOS Site Allow Oral and Aural Assignments', 'toggle_button', 'TO DOS', 'Model_Settings,on_or_off', 1);

UPDATE `engine_settings`
    SET `note` = 'Turn on Task type for Todo. Shows Task type on Todo creation',
        `expose_to_api` = 0 WHERE `variable` = 'todos_site_display_tasks';

UPDATE `engine_settings`
    SET `note` = 'Turn on Assignment type for Todo. Shows Assignment type on Todo creation',
        `expose_to_api` = 0 WHERE `variable` = 'todos_site_display_assignments';

UPDATE `engine_settings`
    SET `note` = 'Turn on Term-Assessments type for Todo. Shows Term-Assessments type on Todo creation',
        `expose_to_api` = 0 WHERE `variable` = 'todos_site_display_assesments';

UPDATE `engine_settings`
    SET `note` = 'Turn on State-Exams type for Todo. Shows State-Exams type on Todo creation',
        `expose_to_api` = 0 WHERE `variable` = 'todos_site_display_exams';

UPDATE `engine_settings`
    SET `note` = 'Turn on Class-Tests type for Todo. Shows Class-Tests type on Todo creation',
        `expose_to_api` = 0 WHERE `variable` = 'todos_site_display_tests';

UPDATE `engine_settings`
    SET `note` = 'Allow creating online Exams and Grading them online. Allows to create questionnaires and other type exams and adds detailed results .',
        `expose_to_api` = 0 WHERE `variable` = 'todos_site_allow_online_exams';

UPDATE `engine_settings`
    SET `note` = 'Show Aural and Oral types on selecting Todos type Task/Assingment/Class-Test' ,
        `expose_to_api` = 0 WHERE `variable` = 'todos_site_allow_oral_assignments';

UPDATE `engine_settings`
    SET `note` = 'Turn on Task type for Todo. Makes Task type for Todo viable via API',
        `expose_to_api` = 0 WHERE `variable` = 'todos_api_display_tasks';

UPDATE `engine_settings`
    SET `note` = 'Turn on Assignment type for Todo. Makes Assignment for Todo viable via API',
        `expose_to_api` = 0 WHERE `variable` = 'todos_api_display_assignments';

UPDATE `engine_settings`
    SET `note` = 'Turn on Term-Assessments type for Todo. Makes Term-Assessments for Todo viable via API',
        `expose_to_api` = 0 WHERE `variable` = 'todos_api_display_assesments';

UPDATE `engine_settings`
    SET `note` = 'Turn on State-Exams type for Todo. Makes Term-Assessments for Todo viable via API',
        `expose_to_api` = 0 WHERE `variable` = 'todos_api_display_exams';

UPDATE `engine_settings`
    SET `note` = 'Turn on Class-Tests type for Todo. Makes  Class-Tests for Todo viable via API',
        `expose_to_api` = 0 WHERE `variable` = 'todos_api_display_tests';

UPDATE `engine_settings`
    SET `note` = 'Allow creating online Exams and Grading them online. Allow Online Exams via API',
        `expose_to_api` = 0 WHERE `variable` = 'todos_api_allow_online_exams';

UPDATE `engine_settings`
    SET `note` = 'Show Aural and Oral types on selecting Todos type Task/Assingment/Class-Test via API' ,
        `expose_to_api` = 0 WHERE `variable` = 'todos_api_allow_oral_assignments';
