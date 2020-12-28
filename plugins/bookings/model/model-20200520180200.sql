/*
ts:2020-05-20 18:02:00
*/

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `report_type`, `publish`, `delete`)
VALUES ('My Upcoming Courses', 'sql', '1', '0');

UPDATE
    `plugin_reports_reports`
SET
    `date_modified` = CURRENT_TIMESTAMP,
    `sql` = 'SELECT \n
  DISTINCT GROUP_CONCAT(DISTINCT `category`.`category`) AS `Category`, \n
  GROUP_CONCAT(DISTINCT `course`.`title`) AS `Course`, \n
  GROUP_CONCAT(DISTINCT `schedule`.`name`) AS `Schedule`,\n
  CONCAT(\n
    `timeslot_order`.`row_number`, \' of \',\n
    `timeslot_count`.`schedule_count`\n
  ) as `Timeslot Order`, \n
  DATE_FORMAT(\n
    `timeslot`.`datetime_start`, \'<span class="hidden">%Y-%m-%d</span>%e %b %Y\'\n
  ) AS `Timeslot Start date`, \n
DATE_FORMAT(\n
    `timeslot`.`datetime_start`, \'<span class="hidden">%H:%i</span>%H:%i\'\n
  ) AS `Timeslot Start time`,
DATE_FORMAT(\n
    `timeslot`.`datetime_end`, \'<span class="hidden">%H:%i</span>%H:%i\'\n
  ) AS `Timeslot End time`, \n
  DATE_FORMAT(\n
    `schedule`.`start_date`, \'<span class="hidden">%Y-%m-%d</span>%e %b %Y\'\n
  ) AS `Schedule Start date`,
DATE_FORMAT(\n
    `schedule`.`end_date`, \'<span class="hidden">%Y-%m-%d</span>%e %b %Y\'\n
  ) AS `Schedule End date`, \n
  DATE_FORMAT(\n
    `schedule`.`start_date`, \'<span class="hidden">%w</span>%a\'\n
  ) AS `Day`, \n
  GROUP_CONCAT(\n
    DISTINCT `parent_location`.`name`\n
  ) AS `Location`, \n
  GROUP_CONCAT(DISTINCT `sub_location`.`name`) AS `Sub location`, \n
 IF (`timeslot`.`trainer_id` IS NOT NULL, GROUP_CONCAT(\n
    DISTINCT CONCAT_WS(\n
      \' \', `timeslot_trainer`.`first_name`, `timeslot_trainer`.`last_name`\n
    )\n
  ) ,  GROUP_CONCAT(\n
    DISTINCT CONCAT_WS(\n
      \' \', `schedule_trainer`.`first_name`, `schedule_trainer`.`last_name`\n
    )\n
  ))
  AS `Trainer`, \n
  COUNT(DISTINCT `booking`.`booking_id`) AS `Bookings` \n
FROM \n
  `plugin_ib_educate_bookings` `booking` \n
  LEFT JOIN `plugin_ib_educate_booking_items` `item` ON `item`.`booking_id` = `booking`.`booking_id` \n
  LEFT JOIN `plugin_courses_schedules_events` `timeslot` ON `item`.`period_id` = `timeslot`.`id` \n
  LEFT JOIN `plugin_ib_educate_booking_has_schedules` `bhs` ON `bhs`.`booking_id` = `booking`.`booking_id` \n
  AND `bhs`.`deleted` = 0 \n
  LEFT JOIN `plugin_courses_schedules` `schedule` ON `bhs`.`schedule_id` = `schedule`.`id` \n
  LEFT JOIN `plugin_courses_courses` `course` ON `schedule`.`course_id` = `course`.`id` \n
  LEFT JOIN `plugin_courses_categories` `category` ON `course`.`category_id` = `category`.`id` \n
  LEFT JOIN `plugin_courses_locations` `sub_location` ON `schedule`.`location_id` = `sub_location`.`id` \n
  LEFT JOIN `plugin_courses_locations` `parent_location` ON `sub_location`.`parent_id` = `parent_location`.`id` \n
  JOIN `plugin_contacts3_contacts` `schedule_trainer` ON `schedule`.`trainer_id` = `schedule_trainer`.`id` \n
  JOIN `plugin_contacts3_contacts` `timeslot_trainer` ON `timeslot`.`trainer_id` = `timeslot_trainer`.`id` \n
  JOIN (\n
    SELECT \n
      `a`.`schedule_id` as `schedule_id`, \n
      `a`.`id` as `timeslot_id`, \n
      COUNT(*) as `row_number` \n
    FROM \n
      (\n
        SELECT \n
          `plugin_courses_schedules_events`.`id`, \n
          `schedule_id` \n
        FROM \n
          `plugin_courses_schedules_events` \n
          JOIN `plugin_contacts3_contacts` `event_trainer` ON `plugin_courses_schedules_events`.`trainer_id` = `event_trainer`.`id` \n
        WHERE \n
          \'{!DASHBOARD-FROM!}\' <= `plugin_courses_schedules_events`.`datetime_start` \n
          AND \'{!DASHBOARD-TO!}\' >= `plugin_courses_schedules_events`.`datetime_start` \n
          AND `event_trainer`.`linked_user_id` = @user_id \n
        ORDER BY \n
          `plugin_courses_schedules_events`.`id`\n
      ) `a` \n
      JOIN (\n
        SELECT \n
          `plugin_courses_schedules_events`.`id`, \n
          `schedule_id` \n
        FROM \n
          `plugin_courses_schedules_events` \n
          JOIN `plugin_contacts3_contacts` `event_order_trainer` ON `plugin_courses_schedules_events`.`trainer_id` = `event_order_trainer`.`id` \n
        WHERE \n
          \'{!DASHBOARD-FROM!}\' <= `plugin_courses_schedules_events`.`datetime_start` \n
          AND \'{!DASHBOARD-TO!}\' >= `plugin_courses_schedules_events`.`datetime_start` \n
          AND `event_order_trainer`.`linked_user_id` = @user_id \n
        ORDER BY \n
          `plugin_courses_schedules_events`.`id`\n
      ) `b` ON `a`.`schedule_id` = `b`.`schedule_id` \n
      AND `a`.`id` >= `b`.`id` \n
    GROUP BY \n
      `a`.`id`,\n
      `a`.`schedule_id` \n
    ORDER BY \n
      `a`.`schedule_id`, \n
      `row_number`\n
  ) as `timeslot_order` ON `timeslot_order`.`timeslot_id` = `timeslot`.`id` \n
  LEFT JOIN (\n
    SELECT \n
      `schedule_id`, \n
      COUNT(*) as `schedule_count` \n
    FROM \n
      `plugin_courses_schedules_events` \n
      LEFT JOIN `plugin_contacts3_contacts` `event_count_trainer` ON `plugin_courses_schedules_events`.`trainer_id` = `event_count_trainer`.`id` \n
    WHERE \n
      `plugin_courses_schedules_events`.`datetime_start` >= \'{!DASHBOARD-FROM!}\'\n
      AND `plugin_courses_schedules_events`.`datetime_start` <= \'{!DASHBOARD-TO!}\'\n
      AND `event_count_trainer`.`linked_user_id` = @user_id \n
    GROUP BY \n
      schedule_id\n
  ) AS `timeslot_count` ON `timeslot_count`.`schedule_id` = `schedule`.`id` \n
WHERE \n
  `booking`.`delete` = 0 \n
  AND (`schedule_trainer`.`linked_user_id` = @user_id OR `timeslot_trainer`.`linked_user_id` = @user_id)\n
  AND \'{!DASHBOARD-FROM!}\' <= `timeslot`.`datetime_start` \n
  AND \'{!DASHBOARD-TO!}\' >= `timeslot`.`datetime_start` \n
  AND `timeslot_count`.`schedule_count` IS NOT NULL \n
GROUP BY \n
  `timeslot`.`id`\n
ORDER BY \n
  `schedule`.`id`, \n
  `timeslot_order`.`row_number`\n'
WHERE
        `name` = 'My Upcoming Courses';

INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'My Upcoming Courses' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-FROM', `value` = '', `delete` = 0, `is_multiselect` = 0;
INSERT INTO plugin_reports_parameters SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'My Upcoming Courses' ORDER BY ID DESC LIMIT 1), `type` = 'date', `name` = 'DASHBOARD-TO',   `value` = '', `delete` = 0, `is_multiselect` = 0;
DELETE
FROM plugin_reports_report_sharing
WHERE group_id = (SELECT id
                  FROM engine_project_role
                  WHERE role = 'Teacher')
  AND report_id = (SELECT `id` FROM `plugin_reports_reports`
                WHERE `name` = 'My Upcoming Courses' ORDER BY ID DESC LIMIT 1);
INSERT INTO plugin_reports_report_sharing (report_id, group_id)
VALUES ((SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'My Upcoming Courses' ORDER BY ID DESC LIMIT 1),
        (SELECT id FROM engine_project_role WHERE role = 'Teacher'));
