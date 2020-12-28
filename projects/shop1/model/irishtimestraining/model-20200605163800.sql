/*
ts:2020-06-05 16:38:00
*/

UPDATE
    `plugin_reports_reports`
SET
    `sql`='SELECT
\n    CONCAT(\'<a href="/admin/contacts3?contact=\', st.id, \'" target="_blank">\', `st`.`first_name`, \' \', `st`.`last_name`, \'</a>\')  AS `Student`,
\n    bk.booking_id AS `Booking ID`,
\n    org.first_name as `Organization`,
\n    cg.category AS `Course Category`,
\n    co.title AS `Course Title`,
\n    sc.`name` AS `Schedule Title`,
\n    DATE_FORMAT(\'{!Date!}\', \'%d/%m/%Y\') AS `Date`
\n    __PHP1__
\n  FROM plugin_courses_schedules sc
\n    INNER JOIN plugin_courses_courses co ON sc.course_id = co.id
\n    INNER JOIN plugin_courses_categories cg ON co.category_id = cg.id
\n    INNER JOIN plugin_ib_educate_booking_has_schedules bs ON sc.id = bs.schedule_id
\n    INNER JOIN plugin_ib_educate_bookings bk ON bs.booking_id = bk.booking_id AND bk.`delete` = 0 AND bk.booking_status <> 3
\n    INNER JOIN plugin_contacts3_contacts st ON bk.contact_id = st.id
\n    INNER JOIN plugin_contacts3_residences re ON st.residence = re.address_id
\n    LEFT  JOIN plugin_contacts3_relations relations ON st.id = relations.child_id
\n    LEFT  JOIN plugin_contacts3_contacts org ON relations.parent_id = org.id
\n    __PHP2__
\n
\n  WHERE sc.id = \'{!Schedule!}\' __PHP3__
\n  GROUP BY `Booking ID`, `Date`, `sc`.id
\n  ORDER BY `Student`'
WHERE
        `name` = 'My Roll Call';