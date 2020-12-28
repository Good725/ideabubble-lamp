/*
ts:2020-09-16 23:51:00
*/

UPDATE `plugin_reports_reports`
SET `sql` =
    'select \r\n
     students.id as `Student ID`,\r\n
     bookings.booking_id as `Interview ID`,\r\n
     CONCAT_WS(\' \', students.first_name, students.last_name) as `Student`,\r\n
     emails.`value` as `Email`,\r\n
     IF(`mobiles`.`country_dial_code` IS NOT NULL AND `mobiles`.`country_dial_code` != \'\' , CONCAT_WS(\' \', \'+\', `mobiles`.`country_dial_code`, `mobiles`.`dial_code`, `mobiles`.`value`), `mobiles`.`value`)  as `Mobile`,\r\n
     DATE_FORMAT(timeslots.datetime_start, \'%d/%M/%Y\') as `Date`,\r\n
     DATE_FORMAT(timeslots.datetime_start, \'%H:%i\') as `Time`,\r\n
     CONCAT(\'<select name=\"interview_status[\', applications.booking_id, \']\" data-selected=\"\', applications.interview_status,\'\"><option value=\"Not Scheduled\">Not Scheduled</option><option value=\"Scheduled\">Scheduled</option><option value=\"No Follow Up\">No Follow Up</option><option value=\"Interviewed\">Interviewed</option><option value=\"Accepted\">Accepted</option><option value=\"No Offer\">No Offer</option><option value=\"Cancelled\">Cancelled</option><option value=\"On Hold\">On Hold</option>\') as `Status`\r\n
        \r\n
        from plugin_ib_educate_bookings bookings\r\n
        inner join plugin_ib_educate_bookings_has_courses has_courses on bookings.booking_id = has_courses.booking_id and has_courses.deleted = 0\r\n
        inner join plugin_courses_courses courses on has_courses.course_id = courses.id\r\n
        inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\r\n
        inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\r\n
        left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\r\n
        left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n
        inner join plugin_contacts3_contacts students on bookings.contact_id = students.id and students.delete = 0\r\n
        left join plugin_contacts3_family f on students.family_id = f.family_id\r\n left join plugin_contacts3_contacts kin on f.family_id = kin.family_id and kin.id <> students.id\r\n
        left join plugin_contacts3_contact_has_notifications emails on students.notifications_group_id = emails.group_id and emails.notification_id=2\r\n
        left join plugin_contacts3_contact_has_notifications mobiles on students.notifications_group_id = mobiles.group_id and mobiles.notification_id=1\r\n
            where bookings.`delete` = 0
                and bookings.booking_status <> 3
                and timeslots.datetime_start >= \'{!Date!}\'
                and timeslots.datetime_start <= DATE_ADD(\'{!Date!}\', INTERVAL 1 DAY)
            AND schedules.id = \'{!Schedule!}\'\r\n
        order by `Time`,`Student`'
WHERE `name` ='Interview Status Bulk Update';