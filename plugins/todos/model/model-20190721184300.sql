/*
ts:2019-07-21 18:43:00
*/

UPDATE `engine_plugins`
SET `name`          = 'assessments',
    `friendly_name` = 'Assessments'
WHERE (`name` = 'exams');

UPDATE `engine_resources`
SET `alias`       = 'assessments',
    `name`        = 'Assessments',
    `description` = 'Assessments'
WHERE (`alias` = 'exams');

UPDATE `engine_resources`
SET `alias` = 'assessments_list',
    `name`  = 'Assessments / List',
    `description` = 'Assessments / List'
WHERE (`alias` = 'exams_list');

UPDATE `engine_resources`
SET `alias` = 'assessments_edit',
    `name`  = 'Assessments / Edit',
    `description` = 'Assessments / Edit'
WHERE (`alias` = 'exams_edit');

UPDATE `engine_resources`
SET `alias` = 'assessments_view_results',
    `name`  = 'Assessments / View Results',
    `description` = 'Assessments / View Results'
WHERE (`alias` = 'exams_view_results');

UPDATE `engine_resources`
SET `alias` = 'assessments_list_limited',
    `name`  = 'Assessments / List Limited',
    `description` = 'Assessments / List Limited'
WHERE (`alias` = 'exams_list_limited');

UPDATE `engine_resources`
SET `alias` = 'assessments_edit_limited',
    `name`  = 'Assessments / Edit Limited',
    `description` = 'Assessments / Edit Limited'
WHERE (`alias` = 'exams_edit_limited');

SET  @assessment_id := (select id from engine_resources where alias = 'assessments' LIMIT 1);

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `parent_controller`, `description`)
VALUES ('1', 'assessments_view_results_limited', 'Assessments / View Results Limited', @assessment_id, 'Assessments / View Results Limited');

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
((SELECT id from engine_project_role where role = 'Student' limit 1), (SELECT id FROM engine_resources where alias = 'assessments_view_results_limited' limit 1));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
((SELECT id from engine_project_role where role = 'Administrator' limit 1), (SELECT id FROM engine_resources where alias = 'assessments_view_results_limited' limit 1));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT id from engine_project_role where role = 'Administrator' limit 1),
        (SELECT id FROM engine_resources where alias = 'assessments_list_limited' limit 1));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT id from engine_project_role where role = 'Administrator' limit 1),
        (SELECT id FROM engine_resources where alias = 'assessments_edit_limited' limit 1));