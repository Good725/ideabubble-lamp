/*
ts:2020-04-21 21:16:00
*/

UPDATE `plugin_reports_reports`
     SET `sql`='\r\n
SELECT DISTINCT\n
    GROUP_CONCAT(DISTINCT `category`.`category`)    AS `Category`,\n
    GROUP_CONCAT(DISTINCT `course`.`title`)         AS `Course`,\n
    GROUP_CONCAT(DISTINCT `schedule`.`name`)        AS `Schedule`,\n
    GROUP_CONCAT(DISTINCT `timeslot`.`id`) AS `TimeslotID`,\n
    CONCAT(`timeslot_order`.`row_number`, \' of \',`timeslot_count`.`schedule_count`) as `Timeslot Order`,\n
    DATE_FORMAT(`timeslot`.`datetime_start`, \'<span class="hidden">%Y-%m-%d</span>%e %b %Y\') AS `Timeslot Start date`, \n
    DATE_FORMAT( `schedule`.`start_date`, ''<span class="hidden">%Y-%m-%d</span>%e %b %Y'') AS `Schedule Start date`,\n
    DATE_FORMAT(`schedule`.`start_date`, ''<span class="hidden">%w</span>%a'') AS `Day`,\n
    GROUP_CONCAT(DISTINCT `parent_location`.`name`) AS `Location`,\n
    GROUP_CONCAT(DISTINCT `sub_location`.`name`)    AS `Sub location`, \n
    GROUP_CONCAT(DISTINCT CONCAT_WS(\' \', `trainer`.`first_name`, `trainer`.`last_name`)) AS `Trainer`, \n
    COUNT(DISTINCT `booking`.`booking_id`)          AS `Bookings`, \n
    CONCAT(\'€\', `schedule`.`fee_amount`)          AS `Fee`,\n
    CONCAT(\'€\', `schedule`.`fee_amount` * COUNT(DISTINCT `booking`.`booking_id`)) AS `Total income`\n
\n
FROM `plugin_ib_educate_bookings` `booking`\n
LEFT JOIN `plugin_ib_educate_booking_items`        `item` ON         `item`.`booking_id`  =         `booking`.`booking_id`\n
LEFT JOIN `plugin_courses_schedules_events`    `timeslot` ON         `item`.`period_id`   =        `timeslot`.`id` \n
LEFT JOIN `plugin_ib_educate_booking_has_schedules` `bhs` ON          `bhs`.`booking_id`  =         `booking`.`booking_id` AND `bhs`.`deleted` = 0 \n
LEFT JOIN `plugin_courses_schedules`           `schedule` ON          `bhs`.`schedule_id` =        `schedule`.`id`\n
LEFT JOIN `plugin_courses_courses`               `course` ON     `schedule`.`course_id`   =          `course`.`id`\n
LEFT JOIN `plugin_courses_categories`          `category` ON       `course`.`category_id` =        `category`.`id`\n
LEFT JOIN `plugin_courses_locations`       `sub_location` ON     `schedule`.`location_id` =    `sub_location`.`id`\n
LEFT JOIN `plugin_courses_locations`    `parent_location` ON `sub_location`.`parent_id`   = `parent_location`.`id`\n
LEFT JOIN `plugin_contacts3_contacts`           `trainer` ON     `schedule`.`trainer_id`  =         `trainer`.`id`\n
 LEFT JOIN (\n
      SELECT\n
            a.schedule_id as schedule_id,\n
            a.id as timeslot_id,\n
            COUNT(*) as row_number\n
                FROM (\n
                    SELECT id,schedule_id \n
                            FROM plugin_courses_schedules_events\n
                                 WHERE  \'{!DASHBOARD-FROM!}\' <= plugin_courses_schedules_events.datetime_start AND\n
                                     \'{!DASHBOARD-TO!}\'   >= plugin_courses_schedules_events.datetime_start ORDER BY id)  a\n
                            JOIN (\n
                                SELECT id,schedule_id\n
                                    FROM plugin_courses_schedules_events\n
                                    WHERE  \'{!DASHBOARD-FROM!}\' <= plugin_courses_schedules_events.datetime_start AND\n
                                    \'{!DASHBOARD-TO!}\'   >= plugin_courses_schedules_events.datetime_start ORDER BY id) b\n
                            ON a.schedule_id = b.schedule_id AND a.id >= b.id\n
\n
       GROUP BY a.id, a.schedule_id\n
       ORDER BY a.schedule_id, row_number) as `timeslot_order` ON `timeslot_order`.`timeslot_id` =  `timeslot`.`id`\n
  LEFT JOIN (\n
    SELECT\n
        schedule_id,\n
        COUNT(*) as schedule_count\n
        FROM plugin_courses_schedules_events\n
            WHERE plugin_courses_schedules_events.datetime_start >=  \'{!DASHBOARD-FROM!}\'\n
                AND plugin_courses_schedules_events.datetime_start <= \'{!DASHBOARD-TO!}\'\n
    GROUP BY schedule_id\n
    ) AS `timeslot_count` ON `timeslot_count`.`schedule_id` = `schedule`.`id`\n
    WHERE `booking`.`delete` = 0\n
      AND  \'{!DASHBOARD-FROM!}\'  <=`timeslot`.`datetime_start`\n
      AND \'{!DASHBOARD-TO!}\'    >= `timeslot`.`datetime_start`\n
      AND `timeslot_count`. `schedule_count` IS NOT NULL\n
  GROUP BY `timeslot`.`id` \n
  ORDER BY `schedule`.`id`, `timeslot_order`.`row_number`' WHERE (`name`='Upcoming bookings');