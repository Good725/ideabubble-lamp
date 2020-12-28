/*
ts:2020-04-20 18:08:00
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
           'Course Waitlist',
           'SELECT \n
            DISTINCT(s.id) as schedule_id, \n
                 c.title as `Course`,\n
                 s.`name` as `Schedule`,\n
                 date_format(s.start_date, \'%d-%M-%Y\') as `Starts`,\n
                 COUNT(DISTINCT(email)) as `Waiting`\n
                       FROM plugin_courses_waitlist \n
                       INNER JOIN  plugin_courses_courses c ON plugin_courses_waitlist.course_id = c.id\n
                       INNER JOIN  plugin_courses_schedules s ON plugin_courses_waitlist.schedule_id = s.id\n
                       WHERE plugin_courses_waitlist.`deleted` = 0 and (plugin_courses_waitlist.course_id = \'{!Course!}\' or \'\' = \'{!Course!}\') \n
            GROUP BY schedule_id',
           '2020-04-20 17:51:00',
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
           (select id from plugin_reports_reports where name='Course Waitlist' ORDER BY id DESC LIMIT 1),
           'custom',
           'Course',
           'select id,\n
            CONCAT_WS(\' - \' , `code`, title) as course \n
            FROM plugin_courses_courses\n
            WHERE deleted = 0 order by code');