/*
ts:2020-07-23 16:00:00
*/

-- Updating the SQL to get delegates, rather than lead bookers (for group bookings)
UPDATE
  `plugin_reports_reports`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `sql` = "SELECT
\n    IFNULL(`d_st`.`id`, `st`.`id`) AS `Student ID`,
\n    `bk`.`booking_id` AS `Booking ID`,
\n    \-\- Get the delegate name. If there are no delegates (non-group booking) show the lead booker.
\n    IF(
\n        `d_st`.`id`,
\n        CONCAT_WS(' ', `d_st`.`first_name`, `d_st`.`last_name`),
\n        CONCAT_WS(' ',   `st`.`first_name`,   `st`.`last_name`)
\n    ) AS `Student`
\n
\n     __PHP1__,
\n
\n    '          ' AS `Student Signature`
\n FROM `plugin_courses_schedules` `sc`
\n    INNER JOIN `plugin_courses_courses` `co`
\n        ON `sc`.`course_id` = `co`.`id`
\n    INNER JOIN plugin_courses_categories `cg`
\n        ON `co`.category_id = cg.id
\n    INNER JOIN plugin_ib_educate_booking_has_schedules `bs`
\n        ON `sc`.id = bs.schedule_id
\n    INNER JOIN plugin_ib_educate_bookings `bk`
\n        ON `bs`.booking_id = bk.booking_id
\n        AND bk.`delete` = 0
\n        AND bk.booking_status <> 3
\n    INNER JOIN plugin_contacts3_contacts `st`
\n        ON `bk`.contact_id = st.id
\n    LEFT JOIN plugin_contacts3_residences `re`
\n        ON `st`.residence = re.address_id
\n
\n    LEFT JOIN `plugin_ib_educate_bookings_has_delegates` `delegate`
\n        ON `delegate`.`booking_id` = `bk`.`booking_id`
\n        AND `delegate`.`cancelled` = 0
\n        AND `delegate`.`deleted` = 0
\n    LEFT JOIN `plugin_contacts3_contacts` `d_st`
\n        ON `delegate`.`contact_id` = `d_st`.`id`
\n
\n    __PHP2__
\n
\n WHERE sc.id = '{!schedule_id!}' __PHP3__
\n GROUP BY CONCAT(`Booking ID`, '-', `Student ID`)
\n ORDER BY st.first_name,st.last_name"
WHERE
  `name` = 'Print Roll Call';