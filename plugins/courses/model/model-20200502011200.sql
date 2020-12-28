/*
ts:2020-05-02 01:12:00
*/

INSERT INTO `plugin_reports_reports`
(`name`,
 `sql`,
 `date_modified`,
 `publish`,
 `delete`,
 `report_type`,
 `action_button`)
VALUES (
           'Cancelled Schedules',
           'SELECT\n
                `categories`.`category` as `Category`,\n
                `courses_types`.`type` as `Type`,\n
                `courses`.`title` as `Course` ,\n
                `schedules`.`name` as `Schedule`,\n
                `schedules`.`start_date` as `StartDate`,\n
                `schedules`.`date_modified` as `Cancelled Date`\n
            FROM `plugin_courses_schedules` AS `schedules`\n
                INNER JOIN `plugin_courses_schedules_status` as `schedules_status`\n
                    ON  `schedules`.`schedule_status` = `schedules_status`.`id`\n
                INNER JOIN `plugin_courses_courses` AS `courses` ON `schedules`.`course_id` = `courses`.`id`\n
                LEFT JOIN `plugin_courses_types` AS `courses_types` ON `courses`.`type_id` = `courses_types`.`id`\n
                INNER JOIN `plugin_courses_categories` AS `categories` ON `courses`.`category_id` = `categories`.`id`\n
            WHERE `schedules_status`.`title` = \'Cancelled\'\n
            AND (`schedules`.`start_date` < date_add(\'{!Before!}\', interval 1 day) or \'\' = \'{!Before!}\')\n
            AND (`schedules`.`start_date` >= \'{!After!}\' or \'\' = \'{!After!}\') and (`courses`.`id` = \'{!Course!}\' or \'\' = \'{!Course!}\')\n
            AND (`courses`.`category_id` = \'{!Category!}\' or \'\' = \'{!Category!}\')\n',
           '2020-05-02 01:12:00',
           '1',
           '0',
           'sql',
           '0'
       );

INSERT INTO `plugin_reports_parameters`
(
    `report_id`,
    `type`,
    `name`,
    `value`)
VALUES (
           (select id from plugin_reports_reports where name='Cancelled Schedules' ORDER BY id DESC LIMIT 1),
           'custom',
           'Course',
           'select id,\n
            CONCAT_WS(\' - \' , `code`, title) as course \n
            FROM plugin_courses_courses\n
            WHERE deleted = 0 order by code');
INSERT INTO `plugin_reports_parameters`
(
    `report_id`,
    `type`,
    `name`,
    `value`)
VALUES (
           (select id from plugin_reports_reports where name='Cancelled Schedules' ORDER BY id DESC LIMIT 1),
           'custom',
           'Category',
           'select\n
                id,\n
                category\n
                from plugin_courses_categories\n
            where `delete`=0 order by category');
INSERT INTO `plugin_reports_parameters`
    (`report_id`, `type`, `name`)
    VALUES (
            (select id from plugin_reports_reports where name='Cancelled Schedules' ORDER BY id DESC LIMIT 1),
            'date',
            'Before');
INSERT INTO `plugin_reports_parameters`
    (`report_id`, `type`, `name`)
    VALUES (
            (select id from plugin_reports_reports where name='Cancelled Schedules' ORDER BY id DESC LIMIT 1),
            'date',
            'After');
