/*
ts:2016-05-16 12:40:00
*/

INSERT INTO `plugin_news_categories` (`category`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `delete`) VALUES
(
  'Teachers',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
);

INSERT IGNORE INTO `engine_project_role` (`role`, `description`, `publish`, `deleted`) VALUES ('Student', '', '1', '0');

INSERT IGNORE INTO `engine_users` (`role_id`, `email`, `password`, `name`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Student' LIMIT 1),
  'student@ideabubble.ie',
  '7b3f885c8dab06a6389bdeb59b30dd871f0c31e038313550596feed8c9015d48',
  'Student',
  'Europe/Dublin',
  CURRENT_TIMESTAMP,
  '1',
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Calendar',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'calendar'),
  'Title',
  'Date',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Calendar',
  '(SELECT 	`course`  .`title`                                                 AS `Title`,     `event`.`datetime_start`                                           AS `iso_date`, 	DATE_FORMAT(`event`.`datetime_start`, \'%d %b\')                     AS `Date`,     CONCAT(\'/admin/courses/edit_schedule/?id=\', `event`.`schedule_id`) AS `Link`,     \'course\'                                                           AS `Category` FROM `plugin_courses_schedules_events` `event` JOIN `plugin_courses_schedules`        `schedule` ON `event`   .`schedule_id` = `schedule`.`id` JOIN `plugin_courses_courses`          `course`   ON `schedule`.`course_id`   = `course`  .`id` WHERE `event`   .`delete`         = 0   AND `schedule`.`delete`         = 0   AND `course`  .`deleted`        = 0   AND `event`   .`datetime_start` > CURRENT_TIMESTAMP)  UNION  (SELECT   `news`.`title`                                    AS `Title`,   `news`.`event_date`                               AS `iso_date`,   DATE_FORMAT(`news`.`event_date`, \'%d %b\')         AS `Date`,   CONCAT(\'/admin/news/add_edit_item/\', `news`.`id`) AS `Link`,   \'news\'                                            AS `Category` FROM `plugin_news` `news` LEFT JOIN `plugin_news_categories` `category` ON `news`.`category_id` = `category`.`id` WHERE `news`.`deleted` = 0 AND (\'{!role!}\' != \'Student\' OR `category`.`category` != \'Teachers\') AND `news`.`event_date` > CURRENT_TIMESTAMP)  ORDER BY `iso_date`',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Calendar' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  'sql'
);

INSERT IGNORE INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`, `delete`, `is_multiselect`) VALUES
(
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Calendar' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  'user_role',
  'role',
  '',
  '0',
  '0'
);
