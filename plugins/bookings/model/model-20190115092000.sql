/*
ts:2019-01-15 09:20:00
*/

UPDATE `plugin_reports_reports` SET `sql`='
    select \r\n
    stu.id as `Student ID`, \r\n
    CONCAT_WS(\' \', par.title, par.first_name, par.last_name) as `Parent Name`, \r\n
    CONCAT_WS(\' \', stu.title,stu.first_name, stu.last_name) as `Student Name`, \r\n
    CONCAT_WS(\' \', r.address1, r.address2) as `Address`, \r\n
    IF(`mobiles`.`country_dial_code` IS NOT NULL AND `mobiles`.`country_dial_code` != \'\' , CONCAT_WS(\' \', \'+\', `mobiles`.`country_dial_code`, `mobiles`.`dial_code`, `mobiles`.`value`),  `mobiles`.`value`)  as `Mobile`,  \r\n
    IF(`phones`.`country_dial_code` IS NOT NULL AND `phones`.`country_dial_code` != \'\' , CONCAT_WS(\' \', \'+\', `phones`.`country_dial_code`, `phones`.`dial_code`, `phones`.`value`), `phones`.`value`) as `Phone`,
    emails.`value` as `Email`, \r\n
    b.booking_id as `Booking ID`, \r\n
    ht.total as `Total Transaction`, \r\n
    sum(p.amount) as `Paid`, \r\n
    bs.title as `Booking Status` \r\n
            from plugin_ib_educate_bookings b \r\n
                inner join plugin_contacts3_contacts stu on b.contact_id = stu.id \r\n
                left join plugin_contacts3_family f on stu.family_id = f.family_id\r\n
                left join plugin_contacts3_contacts par on f.primary_contact_id = par.id \r\n
                left join plugin_contacts3_residences r ON stu.residence = r.address_id\r\n
                left join plugin_contacts3_contact_has_notifications mobiles ON stu.notifications_group_id = mobiles.group_id and mobiles.notification_id = 2 and mobiles.deleted = 0\r\n
                left join plugin_contacts3_contact_has_notifications emails ON stu.notifications_group_id = emails.group_id and emails.notification_id = 1 and emails.deleted = 0\r\n
                left join plugin_contacts3_contact_has_notifications phones ON stu.notifications_group_id = phones.group_id and phones.notification_id = 3 and phones.deleted = 0\r\n
                inner join plugin_ib_educate_booking_has_schedules hs on b.booking_id = hs.booking_id \r\n        left join plugin_ib_educate_bookings_status bs on hs.booking_status=bs.status_id \r\n
                inner join plugin_courses_schedules s on hs.schedule_id = s.id \r\n
                inner join plugin_courses_courses c on s.course_id = c.id \r\n
                left join plugin_courses_categories a on c.category_id = a.id \r\n
                left join plugin_courses_locations l on s.location_id = l.id \r\n
                left join plugin_bookings_transactions ht on b.booking_id = ht.booking_id and ht.deleted = 0\r\n
                left join plugin_bookings_transactions_has_schedule ths on ht.id = ths.transaction_id and ths.schedule_id = hs.schedule_id \r\n
                left join plugin_bookings_transactions_payments p on ht.id = p.transaction_id and p.deleted = 0\r\n
        where hs.deleted = 0 and hs.booking_status <> 3 and b.booking_status <> 3 and s.id in ({!schedule_id!}) \r\n
        group by b.booking_id, ht.id' WHERE (`name`='Classes Booked');
