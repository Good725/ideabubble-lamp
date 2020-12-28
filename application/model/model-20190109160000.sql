/*
ts:2019-01-09 16:00:00
*/
DELIMITER ;;

UPDATE
  `plugin_reports_parameters`
SET
  `value`     = "(SELECT DISTINCT `s`.`id`, CONCAT_WS(' ', `s`.`name` , `c`.`title`, `a`.`category`, `l`.`name`) as `schedule`
\nFROM       `plugin_ib_educate_booking_has_schedules` `hs`
\nINNER JOIN `plugin_courses_schedules`  `s` ON  `hs`.`schedule_id` = `s`.`id`
\nINNER JOIN `plugin_courses_courses`    `c` ON  `s`.`course_id`    = `c`.`id`
\nLEFT  JOIN `plugin_courses_categories` `a` ON  `c`.`category_id`  = `a`.`id`
\nLEFT  JOIN `plugin_courses_locations`  `l` ON  `s`.`location_id`  = `l`.`id`
WHERE `hs`.`deleted` = 0)"
WHERE
  `name`      = 'schedule_id'
AND
  `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Classes Booked' LIMIT 1)
;;

UPDATE
  `plugin_reports_reports`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by` = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `sql` = "select distinct s.id, CONCAT_WS(' ', s.`name` , c.title, a.category, l.`name`) as `schedule`
\n    from plugin_ib_educate_booking_has_schedules hs
\n        inner join plugin_courses_schedules s on hs.schedule_id = s.id
\n        inner join plugin_courses_courses c on s.course_id = c.id
\n        left join plugin_courses_categories a on c.category_id = a.id
\n        left join plugin_courses_locations l on s.location_id = l.id
\n    where hs.deleted = 0;
select
\n        stu.id as `Student ID`,
\n        CONCAT_WS(' ', par.title, par.first_name, par.last_name) as `Parent Name`,
\n        CONCAT_WS(' ', stu.title,stu.first_name, stu.last_name) as `Student Name`,
\n        CONCAT_WS(' ', par.address1, par.address2) as `Address`,
\n        par.mobile as `Mobile`,
\n        par.phone as `Phone`,
\n        par.email as `Email`,
\n        b.booking_id as `Booking ID`,
\n        ht.total as `Total Transaction`,
\n        sum(p.amount) as `Paid`,
\n        bs.title as `Booking Status`
\n    from plugin_ib_educate_bookings b
\n        inner join plugin_contacts_contact stu on b.contact_id = stu.id
\n        left join plugin_family_members m on stu.id = m.contact_id
\n        left join plugin_family_families f on m.family_id = f.id
\n        left join plugin_contacts_contact par on f.primary_contact_id = par.id
\n        inner join plugin_ib_educate_booking_has_schedules hs on b.booking_id = hs.booking_id
\n        left join plugin_ib_educate_bookings_status bs on hs.booking_status=bs.status_id
\n        inner join plugin_courses_schedules s on hs.schedule_id = s.id
\n        inner join plugin_courses_courses c on s.course_id = c.id
\n        left join plugin_courses_categories a on c.category_id = a.id
\n        left join plugin_courses_locations l on s.location_id = l.id
\n        left join plugin_bookings_transactions ht on b.booking_id = ht.booking_id
\n        left join plugin_bookings_transactions_has_schedule ths on ht.id = ths.transaction_id and ths.schedule_id = hs.schedule_id
\n        left join plugin_bookings_transactions_payments p on ht.id = p.transaction_id
\n    where hs.deleted = 0 and s.id in ({!schedule_id!})
\n    group by b.booking_id, ht.id"
WHERE
  `name` = 'Classes Booked'
;;