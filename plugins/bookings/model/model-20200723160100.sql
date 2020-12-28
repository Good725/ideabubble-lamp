/*
ts:2020-07-23 16:01:00
*/

-- Updating the SQL to get delegates, rather than lead bookers (for group bookings)
UPDATE
  `plugin_reports_reports`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `sql` = "SELECT
\n    IF(
\n        `d_st`.`id`,
\n        CONCAT('<a href=\"/admin/contacts3?contact=', d_st.id, '\" target=\"_blank\">', `d_st`.`first_name`, ' ', `d_st`.`last_name`, '</a>'),
\n        CONCAT('<a href=\"/admin/contacts3?contact=', st.id, '\" target=\"_blank\">', `st`.`first_name`, ' ', `st`.`last_name`, '</a>')
\n    ) AS `Student`,
\n    bk.booking_id AS `Booking ID`,
\n    org.first_name as `Organization`,
\n    cg.category AS `Course Category`,
\n    co.title AS `Course Title`,
\n    sc.`name` AS `Schedule Title`,
\n    DATE_FORMAT('{!Date!}', '%d/%m/%Y') AS `Date`
\n    __PHP1__
\n  FROM plugin_courses_schedules sc
\n    INNER JOIN plugin_courses_courses co
\n        ON sc.course_id = co.id
\n    INNER JOIN plugin_courses_categories cg
\n        ON co.category_id = cg.id
\n    INNER JOIN plugin_ib_educate_booking_has_schedules bs
\n        ON sc.id = bs.schedule_id
\n    INNER JOIN plugin_ib_educate_bookings bk
\n        ON bs.booking_id = bk.booking_id
\n        AND bk.`delete` = 0
\n        AND bk.booking_status <> 3
\n
\n    \-\- Join the lead booker (used for non-group bookings)
\n    INNER JOIN plugin_contacts3_contacts st
\n        ON bk.contact_id = st.id
\n    LEFT JOIN plugin_contacts3_residences re
\n        ON st.residence = re.address_id
\n    LEFT JOIN plugin_contacts3_relations relations
\n        ON st.id = relations.child_id
\n    LEFT JOIN plugin_contacts3_contacts org
\n        ON relations.parent_id = org.id
\n
\n    \-\- Join to the delegates
\n    LEFT JOIN `plugin_ib_educate_bookings_has_delegates` `delegate`
\n        ON `delegate`.`booking_id` = `bk`.`booking_id`
\n        AND `delegate`.`cancelled` = 0
\n        AND `delegate`.`deleted` = 0
\n    LEFT JOIN `plugin_contacts3_contacts` `d_st`
\n        ON `delegate`.`contact_id` = `d_st`.`id`
\n    __PHP2__
\n  WHERE sc.id = '{!Schedule!}' __PHP3__
\n  GROUP BY `Booking ID`, `Date`, `Student`
\n  ORDER BY `Student`"
WHERE
  `name` = 'My Roll Call';