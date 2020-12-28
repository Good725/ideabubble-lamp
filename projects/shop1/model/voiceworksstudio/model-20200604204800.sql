/*
ts:2020-06-04 20:48:00
*/


INSERT IGNORE INTO `plugin_reports_reports` (`name`, `report_type`, `publish`, `delete`)
VALUES ('Zero Timeslots', 'sql', '1', '0');

INSERT IGNORE INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Zero Timeslots' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'Date', `value` = '', `delete` = 0, `is_multiselect` = 0;

UPDATE
    `plugin_reports_reports`
SET
    `date_modified` = CURRENT_TIMESTAMP,
    `sql` = 'SELECT\n
    `timeslots`.`id` as `ID`,\n
	`schedules`.`id` AS `Schedule ID`,\n
    CONCAT(\'<a href=\"/admin/courses/edit_schedule/?id=\', `schedules`.`id`, \'\" target=\"_blank\">\',  `schedules`.`name`, \'</a>\')  as `Schedule`,\n
    `courses`.`title` as `Course`,\n
    DATE(`timeslots`.`datetime_start`) as `Date`,\n
    TIME(`timeslots`.`datetime_start`) as `Start Time`,\n
    TIME(`timeslots`.`datetime_end`) as `End Time`\n
FROM `plugin_courses_schedules_events` `timeslots`\n
JOIN `plugin_courses_schedules` `schedules`\n
	ON  `timeslots`.`schedule_id` =  `schedules`.`id`\n
JOIN `plugin_courses_courses`  `courses`\n
	ON  `schedules`.`course_id` =  `courses`.`id`\n
        WHERE\n
		(TIME(`timeslots`.`datetime_start`) = \'00:00:00\'\n
		OR TIME(`timeslots`.`datetime_end`) = \'00:00:00\')\n
        AND `timeslots`.`delete` = 0\n
        AND `timeslots`.`publish` = 1\n
        AND `schedules`.`publish` = 1\n
        AND `schedules`.`delete` = 0\n
		AND (`timeslots`.`datetime_start` >= \'{!Date!}\' OR \'{!Date!}\' = \'\');\n'
WHERE
        `name` = 'Zero Timeslots';


