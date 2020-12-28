/*
ts:2019-11-13 13:00:00
*/

INSERT INTO `plugin_todos_grading_schemas`
(`title`,   `date_created`,      `date_modified`,   `created_by`, `modified_by`) VALUES
('Schema 1', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, '1',          '1');

SELECT `id` INTO @schema_1_id FROM `plugin_todos_grading_schemas` WHERE `title` = 'Schema 1' ORDER BY `id` DESC LIMIT 1;

INSERT INTO `plugin_todos_grades`
(`grade`, `percent_min`, `percent_max`) VALUES
('Unsuccessful',  '0',  '49'),
('Pass',         '50',  '64'),
('Merit',        '65',  '79'),
('Distinction',  '80', '100');

INSERT INTO `plugin_todos_schemas_have_grades`
(`schema_id`, `grade_id`, `order`) VALUES
(@schema_1_id, (SELECT `id` FROM `plugin_todos_grades` WHERE `grade` = 'Unsuccessful' ORDER BY `id` DESC LIMIT 1), 1),
(@schema_1_id, (SELECT `id` FROM `plugin_todos_grades` WHERE `grade` = 'Pass'         ORDER BY `id` DESC LIMIT 1), 2),
(@schema_1_id, (SELECT `id` FROM `plugin_todos_grades` WHERE `grade` = 'Merit'        ORDER BY `id` DESC LIMIT 1), 3),
(@schema_1_id, (SELECT `id` FROM `plugin_todos_grades` WHERE `grade` = 'Distinction'  ORDER BY `id` DESC LIMIT 1), 4);