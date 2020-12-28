/*
ts:2020-04-21 01:37:00
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
        'Course Waitlist Contacts',
        'SELECT \r\n
            contacts.id as `ContactID`, \r\n
            CONCAT_WS(\'\'  \'\' , plugin_courses_waitlist.`name`, plugin_courses_waitlist.`surname`) as `Name`,\r\n
            CONCAT_WS(\'\', contacts.first_name, contacts.last_name) as `Organization`,\r\n
            plugin_courses_waitlist.email as `Email`,\r\n
            courses.title as `Course`,\r\n
            schedules.id as `ScheduleID`,\r\n
            schedules.`name` as `Schedule`\r\n
        FROM plugin_courses_waitlist \r\n
        INNER JOIN  plugin_courses_courses courses ON plugin_courses_waitlist.course_id = courses.id \r\n
        INNER JOIN  plugin_courses_schedules schedules ON plugin_courses_waitlist.schedule_id = schedules.id\r\n
        LEFT JOIN plugin_contacts3_contacts contacts ON plugin_courses_waitlist.contact_id = contacts.id\r\n
        WHERE plugin_courses_waitlist.`deleted` = 0 \r\n
           and (schedules.start_date < date_add(\'{!Before!}\', interval 1 day) or \'\' = \'{!Before!}\')\r\n
           and (schedules.start_date >= \'{!After!}\' or \'\' = \'{!After!}\') and (courses.id = \'{!Course!}\' or \'\' = \'{!Course!}\')\r\n
           and (courses.category_id = \'{!Category!}\' or \'\' = \'{!Category!}\')\r\n
         GROUP BY `ScheduleID`, `ContactID`, `Email`',
        '2020-04-21 01:37:00',
        '1',
        '0',
        'sql',
        '0');
DELETE FROM `plugin_reports_parameters` WHERE report_id = (select id from plugin_reports_reports where name='Course Waitlist Contacts' ORDER BY id DESC LIMIT 1);
INSERT INTO `plugin_reports_parameters`
(
    `report_id`,
    `type`,
    `name`,
    `value`)
VALUES (
           (select id from plugin_reports_reports where name='Course Waitlist Contacts' ORDER BY id DESC LIMIT 1),
           'custom',
           'Course',
           '((select id,\n
            CONCAT_WS(\' - \' , `code`, title) as course\n
            FROM plugin_courses_courses\n
            WHERE deleted = 0 order by code))');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`)
VALUES (
        (select id from plugin_reports_reports where name = 'Course Waitlist Contacts' ORDER BY id DESC LIMIT 1),
        'date',
        'Before');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`)
VALUES (
           (select id from plugin_reports_reports where name = 'Course Waitlist Contacts' ORDER BY id DESC LIMIT 1),
           'date',
           'After');
INSERT INTO `plugin_reports_parameters`
(
    `report_id`,
    `type`,
    `name`,
    `value`)
VALUES (
           (select id from plugin_reports_reports where name='Course Waitlist Contacts' ORDER BY id DESC LIMIT 1),
           'custom',
           'Category',
           '((select id, category from plugin_courses_categories where `delete`=0 order by category))');
INSERT INTO `plugin_reports_parameters`
(
    `report_id`,
    `type`,
    `name`,
    `value`)
VALUES (
           (select id from plugin_reports_reports where name='Course Waitlist Contacts' ORDER BY id DESC LIMIT 1),
           'custom',
           'Schedule',
           '(((select id,  name as `schedule` from plugin_courses_schedules where `delete`=0 order by id)))');
