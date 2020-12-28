/*
ts:2020-11-16 15:00:00
*/

DELIMITER ;;

-- Fix "total" counter in "Online course learning progress"

UPDATE `plugin_reports_reports`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `sql` = 'SELECT
\n    `course`.`title`          AS `Course`,
\n    `study_mode`.`study_mode` AS `Type`,
\n    COUNT(DISTINCT `progress`.`user_id`) AS `Learners`,
\n    CONCAT(COUNT(DISTINCT `progress`.`id`), '' / '', CAST(COUNT(DISTINCT `lesson`.`id`)* COUNT(DISTINCT `progress`.`user_id`)  as CHAR)) AS `Progress`
\nFROM      `plugin_content_content`     `content`
\nLEFT JOIN `plugin_content_content`     `subsection` ON (`subsection`.`parent_id`     = `content`.   `id` AND `subsection`.`deleted`     = 0)
\nLEFT JOIN `plugin_content_content`     `lesson`     ON (`lesson`.    `parent_id`     = `subsection`.`id` AND `lesson`.    `deleted`     = 0)
\nLEFT JOIN `plugin_content_progress`    `progress`   ON (`progress`.  `content_id`    = `content`.   `id` AND `progress`.  `is_complete` = 1 AND `progress`.`deleted` = 0)
\nJOIN `plugin_courses_schedules`        `schedule`   ON  `schedule`.  `content_id`    = `content`.   `id`
\nLEFT JOIN `plugin_courses_courses`     `course`     ON  `schedule`.  `course_id`     = `course`.    `id`
\nLEFT JOIN `plugin_courses_study_modes` `study_mode` ON  `schedule`.  `study_mode_id` = `study_mode`.`id`
\nLEFT JOIN `engine_users`               `user`       ON  `progress`.  `user_id`       = `user`.      `id`
\nGROUP BY CONCAT(`content`.`id`)
\n;'
WHERE `name` = 'Online course learning progress';;



-- Create a report, broken down by student

INSERT INTO `plugin_reports_charts` (`title`, `type`, `date_created`, `publish`, `delete`) VALUES
(
  'Online course learning progress by student',
  (SELECT IFNULL(`id`, 1) FROM `plugin_reports_chart_types` WHERE `stub` = 'bar' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  1,
  0
);;

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `date_created`, `date_modified`, `publish`, `delete`, `chart_id`,  `report_type`, `autoload`) VALUES
(
  'Online course learning progress by student',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_charts` WHERE `title` = 'Online course learning progress by student' LIMIT 1),
  'sql',
  '1'
);;

UPDATE `plugin_reports_reports`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `sql` = "SELECT
\n    CONCAT(`user`.`name`, ' ', `user`.`surname`) AS `Student`,
\n    `course`.`title`          AS `Course`,
\n    `study_mode`.`study_mode` AS `Type`,
\n    COUNT(DISTINCT `progress`.`user_id`) AS `Learners`,
\n    CONCAT(COUNT(DISTINCT `progress`.`id`), ' / ', CAST(COUNT(DISTINCT `lesson`.`id`) as CHAR)) AS `Progress`
\nFROM      `plugin_content_content`     `content`
\nLEFT JOIN `plugin_content_content`     `subsection` ON (`subsection`.`parent_id`     = `content`.   `id` AND `subsection`.`deleted`     = 0)
\nLEFT JOIN `plugin_content_content`     `lesson`     ON (`lesson`.    `parent_id`     = `subsection`.`id` AND `lesson`.    `deleted`     = 0)
\nLEFT JOIN `plugin_content_progress`    `progress`   ON (`progress`.  `content_id`    = `content`.   `id` AND `progress`.  `is_complete` = 1 AND `progress`.`deleted` = 0)
\nJOIN `plugin_courses_schedules`        `schedule`   ON  `schedule`.  `content_id`    = `content`.   `id`
\nLEFT JOIN `plugin_courses_courses`     `course`     ON  `schedule`.  `course_id`     = `course`.    `id`
\nLEFT JOIN `plugin_courses_study_modes` `study_mode` ON  `schedule`.  `study_mode_id` = `study_mode`.`id`
\nLEFT JOIN `engine_users` `user` on `progress`.`user_id` = `user`.`id`
\nGROUP BY CONCAT(`progress`.`user_id`, `content`.`id`)
\n;"
WHERE `name` = 'Online course learning progress by student';;
