/*
ts:2019-08-30 15:01:00
*/

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (0, 'grades_edit', 'Todos / Grades edit', 'Todos / Grades edit',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'todos'));

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'grades_edit'));

UPDATE `engine_resources`
SET `alias` = 'todos_view_my_todos',
    `name`  = 'Todos / View my todos'
WHERE (`alias` = 'todos_list_limited');


insert into `engine_cron_tasks`
set `title`     = 'Assessment Status',
    `frequency` = '{\"minute\":[\"0\"],\"hour\":[\"*\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}',
    `plugin_id` = (select id
                   from engine_plugins
                   where `name` = 'todos'),
    `publish`   = '1',
    `delete`    = '0',
    `action`    = 'cron';

DELETE
FROM `engine_resources`
WHERE (`alias` = 'todos_edit_from');
