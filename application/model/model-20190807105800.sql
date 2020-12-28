/*
ts:2019-08-07 10:58:00
*/
INSERT IGNORE INTO `plugin_dashboards` (`title`, `description`, `columns`, `date_filter`, `date_created`,
                                        `date_modified`,
                                        `publish`, `deleted`)
VALUES ('Supervisor', 'Supervisor', '3', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');

INSERT IGNORE INTO engine_project_role (`role`, `description`, `default_dashboard_id`)
VALUES ('Supervisor', 'Supervisor', (select `id` from plugin_dashboards where `title` = 'Supervisor' LIMIT 1));

INSERT IGNORE INTO `engine_resources`
    (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES (1, 'purchasing_view_limited', 'Purchase view limited',
        'Purchase View Limited - Users will only see linked department POs',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'purchasing' LIMIT 1));

INSERT IGNORE INTO engine_role_permissions (role_id, resource_id)
SELECT ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor')), rp.resource_id
FROM engine_role_permissions rp
         inner join engine_project_role pr on rp.role_id = pr.id
WHERE pr.role = "Manager";

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Supervisor'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'purchasing_view_limited'));