/*
ts:2019-08-12 14:46:00
*/

ALTER TABLE `plugin_exams_exams`
    CHANGE COLUMN `type` `type` ENUM ('Task', 'Assignment', 'Class Test', 'Class-Test', 'Term-Assessment', 'Term Assessment', 'State Exam', 'State-Exam') NULL DEFAULT NULL;

UPDATE `plugin_exams_exams`
SET `type` = 'Class-Test'
where `type` = 'Class Test';

UPDATE `plugin_exams_exams`
SET `type` = 'Term-Assessment'
where `type` = 'Term Assessment';

UPDATE `plugin_exams_exams`
SET `type` = 'State-Exam'
where `type` = 'State Exam';

ALTER TABLE `plugin_exams_exams`
    CHANGE COLUMN `type` `type` ENUM ('Task', 'Assignment', 'Class-Test', 'Term-Assessment', 'State-Exam') NULL DEFAULT NULL;

-- Allow a schedule to be linked to a content tree
ALTER TABLE `plugin_exams_exams`
    ADD COLUMN `content_id` INT NULL AFTER `location_id`;

-- Merge status table from todos
ALTER TABLE `plugin_exams_exams`
    ADD COLUMN `status` ENUM ('Open', 'Done', 'In progress') NULL DEFAULT 'Open' AFTER `mode`;

ALTER TABLE `plugin_exams_exams`
    ADD COLUMN `priority` ENUM ('Low', 'Normal', 'High') NULL DEFAULT 'Normal' AFTER `status`;

ALTER TABLE `plugin_exams_exams`
    ADD COLUMN `related_to_id` VARCHAR(80) NULL DEFAULT NULL AFTER `content_id`,
    ADD COLUMN `related_to` VARCHAR(80) NULL DEFAULT NULL AFTER `related_to_id`;

ALTER TABLE `plugin_exams_exams`
    RENAME TO `plugin_todos_todos2`;

ALTER TABLE `plugin_exams_exams_has_academicyears`
    RENAME TO `plugin_todos_todos2_has_academicyears`;

ALTER TABLE `plugin_todos_todos2_has_academicyears`
    CHANGE COLUMN `exam_id` `todo_id` INT(11) NOT NULL;

ALTER TABLE `plugin_exams_exams_has_assigned_contacts`
    RENAME TO `plugin_todos_todos2_has_assigned_contacts`;

ALTER TABLE `plugin_todos_todos2_has_assigned_contacts`
    CHANGE COLUMN `exam_id` `todo_id` INT(11) NOT NULL;

ALTER TABLE `plugin_exams_exams_has_permissions`
    RENAME TO `plugin_todos_todos2_has_permissions`;

ALTER TABLE `plugin_todos_todos2_has_permissions`
    CHANGE COLUMN `exam_id` `todo_id` INT(11) NOT NULL;

ALTER TABLE `plugin_exams_exams_has_results`
    RENAME TO `plugin_todos_todos2_has_results`;

ALTER TABLE `plugin_todos_todos2_has_results`
    CHANGE COLUMN `exam_id` `todo_id` INT(11) NOT NULL;

ALTER TABLE `plugin_exams_exams_has_schedules`
    RENAME TO `plugin_todos_todos2_has_schedules`;

ALTER TABLE `plugin_todos_todos2_has_schedules`
    CHANGE COLUMN `exam_id` `todo_id` INT(11) NOT NULL;

ALTER TABLE `plugin_exams_exams_has_subjects`
    RENAME TO `plugin_todos_todos2_has_subjects`;

ALTER TABLE `plugin_todos_todos2_has_subjects`
    CHANGE COLUMN `exam_id` `todo_id` INT(11) NOT NULL;

ALTER TABLE `plugin_exams_exams_is_favorite`
    RENAME TO `plugin_todos_todos2_is_favorite`;

ALTER TABLE `plugin_todos_todos2_is_favorite`
    CHANGE COLUMN `exam_id` `todo_id` INT(11) NOT NULL;

ALTER TABLE `plugin_exams_grades`
    RENAME TO `plugin_todos_grades`;

DELETE FROM `engine_role_permissions` where `role_id` =
(SELECT id from engine_project_role where role = 'Administrator' limit 1)
and resource_id = (SELECT id FROM engine_resources where alias = 'assessments_edit_limited' limit 1);

UPDATE `engine_resources` SET `alias` = 'todos_list', `name` = 'Todos / List',
                              `description` = 'Todos / List', `parent_controller` = @todo_resource_id
WHERE (`alias` = 'assessments_list');
UPDATE `engine_resources`SET `alias` = 'todos_edit', `name`  = 'Todos / Edit',
                             `description` = 'Todos / Edit',`parent_controller` = @todo_resource_id
WHERE (`alias` = 'assessments_edit');
UPDATE `engine_resources` SET `alias` = 'todos_view_results', `name`  = 'Todos / View Results',
                              `description` = 'Todos / View Results', `parent_controller` = @todo_resource_id
WHERE (`alias` = 'assessments_view_results');
UPDATE `engine_resources` SET `alias` = 'todos_list_limited', `name`  = 'Todos / List Limited',
                              `description` = 'Todos / List Limited', `parent_controller` = @todo_resource_id
WHERE (`alias` = 'assessments_list_limited');
UPDATE `engine_resources` SET `alias` = 'todos_edit_limited', `name`  = 'Todos / Edit Limited',
                              `description` = 'Todos / Edit Limited', `parent_controller` = @todo_resource_id
WHERE (`alias` = 'assessments_edit_limited');
UPDATE `engine_resources` SET `alias` = 'todos_view_results_limited', `name`  = 'Todos / View Results Limited',
                              `description` = 'Todos / View Results Limited', `parent_controller` = @todo_resource_id
WHERE (`alias` = 'assessments_view_results_limited');

UPDATE`plugin_todos_related_list`
SET `related_table_title_column` = 'CONCAT(first_name, \' \', last_name)'
WHERE (`title` = 'contacts2');

UPDATE `plugin_todos_related_list`
SET `related_table_title_column` = 'CONCAT(first_name, \' \', last_name)'
WHERE (`id` = '1');

INSERT INTO`plugin_todos_related_list` (`title`, `related_table_name`,
                                                                       `related_table_id_column`,
                                                                       `related_table_title_column`,
                                                                       `related_table_deleted_column`,
                                                                       `related_open_link_url`, `deleted`)
VALUES ('contacts3', 'plugin_contacts3_contacts', 'id', 'CONCAT(first_name, \' \', last_name)', 'delete',
        '/admin/contacts3/edit/', '0');

UPDATE `plugin_messaging_notification_templates`
SET `name`                          = 'todo-result-email',
    `subject`                       = '$type Result',
    `message`                       = 'Hello,\r Name: $todo\r Type: $type\r Mode: $mode\r Student: $student\r Result: $result\r Grade: $grade\r Comment: $comment\r ',
    `create_via_code`               = 'Todos',
    `usable_parameters_in_template` = '$todo, $type,$mode,$student,$result,$grade,$comment'
WHERE (`name` = 'exam-result-email');

UPDATE `engine_resources`
SET `alias`       = 'todos_content_tab',
    `name`        = 'Todos / Content tab',
    `description` = 'Todos / Content tab'
WHERE (`alias` = 'assessments_content_tab');

DELETE
FROM `engine_resources`
WHERE (`alias` = 'assessments');

ALTER TABLE `plugin_todos_todos2` ADD COLUMN `course_id` INT(11) NULL DEFAULT NULL AFTER `content_id`;

SELECT id INTO @todo_resource_id FROM `engine_resources` o WHERE o.`alias` = 'todos';

UPDATE `engine_resources`
SET `description`       = 'Todos / List',
    `parent_controller` = @todo_resource_id
WHERE (`alias` = 'todos_list');

UPDATE `engine_resources`
SET `description`       = 'Todos / Edit',
    `parent_controller` = @todo_resource_id
WHERE (`alias` = 'todos_edit');

UPDATE `engine_resources`
SET `description`       = 'Todos / View Results',
    `parent_controller` = @todo_resource_id
WHERE (`alias` = 'todos_view_results');

UPDATE `engine_resources`
SET `description`       = 'Todos / List Limited',
    `parent_controller` = @todo_resource_id
WHERE (`alias` = 'todos_list_limited');

UPDATE `engine_resources`
SET `description`       = 'Todos / Edit Limited',
    `parent_controller` = @todo_resource_id
WHERE (`alias` = 'todos_edit_limited');

UPDATE `engine_resources`
SET `description`       = 'Todos / View Results Limited',
    `parent_controller` = @todo_resource_id
WHERE (`alias` = 'todos_view_results_limited');

UPDATE `engine_resources`
SET `parent_controller` = @todo_resource_id
WHERE (`alias` = 'todos_content_tab');