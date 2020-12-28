/*
ts:2020-07-21 10:00:00
*/

-- Table for tracking details on each download
CREATE TABLE `plugin_courses_brochure_downloads` (
  `id`          INT(11)   NOT NULL AUTO_INCREMENT,
  `contact_id`  INT(11)   NOT NULL,
  `course_id`   INT(11)   NOT NULL,
  `schedule_id` INT(11)   NULL,
  `date`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted`     INT(1)    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`));

-- Create report for tracking downloads
DELIMITER ;;
INSERT INTO `plugin_reports_reports` (`name`, `summary`, `sql`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES (
  'Course brochure downloads',
  'List of downloads done on the course details page',
  '',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);;

UPDATE
  `plugin_reports_reports`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `sql` = "SELECT
\n    `course`.`title`       AS `Course`,
\n    `schedule`.`name`      AS `Schedule`,
\n    `contact`.`first_name` AS `First name`,
\n    `contact`.`last_name`  AS `Last name`,
\n    IFNULL(`email`.`value`, `user`.`email`) AS `Email`,
\n    IF(`phone`.`country_dial_code` IS NOT NULL AND `phone`.`country_dial_code` != \'\' , CONCAT_WS( \'\', \'+\', `phone`.`country_dial_code`, `phone`.`dial_code`, `phone`.`value`), `phone`.`value`) AS `Phone number`,
\n    DATE_FORMAT(`download`.`date`, '%d/%m/%Y') AS `Date submitted`
\n
\nFROM `plugin_courses_brochure_downloads` `download`
\nLEFT JOIN `plugin_contacts3_contacts`    `contact`  ON `download`.`contact_id`  = `contact`.`id`
\nLEFT JOIN `plugin_courses_courses`       `course`   ON `download`.`course_id`   = `course`.`id`
\nLEFT JOIN `plugin_courses_schedules`     `schedule` ON `download`.`schedule_id` = `schedule`.`id`
\n
\nLEFT JOIN `engine_users` `user` ON `contact`.`linked_user_id` = `user`.`id`
\nLEFT JOIN `plugin_contacts3_contact_has_notifications` `email`
\n    ON `contact`.`notifications_group_id` = `email`.`group_id`
\n   AND `email`.`notification_id` = (SELECT `id` FROM `plugin_contacts3_notifications` WHERE `stub` = 'email')
\n
\nLEFT JOIN `plugin_contacts3_contact_has_notifications` `phone`
\n    ON `contact`.`notifications_group_id` = `phone`.`group_id`
\n   AND `phone`.`notification_id` IN (SELECT `id` FROM `plugin_contacts3_notifications` WHERE `stub` IN ('mobile', 'landline'))
\n
\nWHERE `download`.`deleted` = 0
\nGROUP BY `download`.`id`
\nORDER BY `download`.`date` DESC;
\n"
 WHERE
  `name` = 'Course brochure downloads';;

