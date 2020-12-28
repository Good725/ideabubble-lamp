/*
ts:2019-12-11 12:53:00
*/

UPDATE `plugin_reports_reports`
SET `widget_sql` = 'SELECT DATE_FORMAT(e.datetime_start, \'%m/%Y\') AS `month`, count(*) AS `qty` \r FROM plugin_courses_schedules_events e \r INNER JOIN plugin_courses_schedules s ON s.id = e.schedule_id AND s.`delete` = 0 AND e.`delete` = 0 \r INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0 \r INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 AND b.booking_status in (2, 4, 5) \r LEFT JOIN plugin_ib_educate_bookings_has_applications b_h_a on (b.booking_id = b_h_a.booking_id) \r INNER JOIN plugin_courses_courses co ON s.course_id = co.id \r INNER JOIN plugin_courses_locations l ON s.location_id = l.id \r LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id \r WHERE \'{!DASHBOARD-FROM!}\' <= e.datetime_start AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > e.datetime_start and (b_h_a.application_status is null or b_h_a.application_status != \'Enquiry\')\r GROUP BY `month` \r ORDER BY e.datetime_start ASC'
WHERE (`name` = 'Attendance By Month');
