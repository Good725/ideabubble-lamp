/*
ts:2017-09-25 12:30:00
*/

-- Update the query to work, if the LocationIDs and TrainerIDs parameters are not set
UPDATE
  `plugin_reports_reports`
SET
  `sql` = 'SELECT
\n  CONCAT(TIME_FORMAT(`event`.`datetime_start`, \'%H:%i\'), \'-\',  TIME_FORMAT(`event`.`datetime_end`, \'%H:%i\')) AS `Time`,
\n  DATE_FORMAT(`event`.`datetime_start`, \'%W\') AS `Day`,
\n  CONCAT(\'<a href=\"/admin/courses/edit_schedule/?id=\', `schedule`.`id`, \'\">\', `schedule`.`name`, \'</a>\') AS `Class`,
\n  CONCAT(\'<a href=\"/admin/courses/edit_location/?id=\', `location`.`id`, \'\">\', `location`.`name`, \'</a>\') AS `Room`,
\n  CONCAT(\'<a href=\"/admin/contacts3/?contact=\',  `trainer`.`id`, \'\">\', `trainer`.`first_name`, \' \', `trainer`.`last_name`, \'</a>\') AS `Trainer`
\nFROM
\n  `plugin_courses_schedules_events` `event`
\n  JOIN `plugin_courses_schedules` `schedule` ON `event`.`schedule_id` = `schedule`.`id`
\n  JOIN `plugin_courses_courses` `course` ON `schedule`.`course_id` = `course`.`id`
\n  LEFT JOIN (SELECT * FROM  `plugin_courses_locations` WHERE  `delete` = 0 AND `location_type_id` = 2) `location` ON `schedule`.`location_id` = `location`.`id`
\n  LEFT JOIN (SELECT * FROM `plugin_contacts3_contacts` WHERE `delete` = 0) `trainer` ON `schedule`.`trainer_id` = `trainer`.`id`
\nWHERE
\n  `event`.`delete` = 0
\n  AND `schedule`.`delete` = 0
\n  AND `course`.`deleted` = 0
\n  AND `event`.`datetime_start` >= \"{!From!}\"
\n  AND `event`.`datetime_start` < \"{!To!}\"
\n  AND IF (\'{!LocationIDs!}\' = \'\', 0, `location`.`id`) IN (0{!LocationIDs!})
\n  AND IF (\'{!TrainerIDs!}\'  = \'\', 0, `trainer`.`id` ) IN (0{!TrainerIDs!})
\nORDER BY
\n  `datetime_start` ASC
\n;'
WHERE
  `name`='RAB'
  AND `delete` = 0
;

-- Create parameters for the the 'From' and 'To' dates.
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`, `delete`, `is_multiselect`) VALUES
((SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'RAB' AND `delete` = 0 ORDER BY `date_created` DESC LIMIT 1), 'date', 'From', '', '0', '0'),
((SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'RAB' AND `delete` = 0 ORDER BY `date_created` DESC LIMIT 1), 'date', 'To',   '', '0', '0');
