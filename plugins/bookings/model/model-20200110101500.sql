/*
ts:2020-01-10 10:15:00
*/

ALTER TABLE plugin_bookings_transactions_has_schedule ADD COLUMN deposit DECIMAL(10, 2);

/*existing transaction schedules has no deposit information upto now. try to set them. may not 100% accurate*/

drop temporary table if exists dtx;
create temporary table dtx as
select
		b.contact_id as `Contact Id`, CONCAT_WS(' ', c.first_name, c.last_name) as `Student`, b.booking_id as `Booking Id`, s.`name` as `Schedule`, s.id as `Schedule Id`, t.id as `Transaction Id`, t.total as `Transaction Total`
	from plugin_contacts3_contacts c
		inner join plugin_ib_educate_bookings b on b.contact_id = c.id
		inner join plugin_ib_educate_booking_has_schedules hs on b.booking_id = hs.booking_id
		inner join plugin_courses_schedules s on hs.schedule_id = s.id
		inner join plugin_bookings_transactions t on t.booking_id = b.booking_id
		inner join plugin_bookings_transactions_has_schedule ths on hs.schedule_id = ths.schedule_id and ths.transaction_id = t.id
	where s.deposit > 0 and t.total = s.deposit and b.`delete` = 0 and t.deleted = 0 and hs.deleted = 0 and ths.deleted = 0
	order by `Student`, `Transaction Id`;

update plugin_bookings_transactions_has_schedule
	inner join dtx on plugin_bookings_transactions_has_schedule.transaction_id = dtx.`Transaction Id` and plugin_bookings_transactions_has_schedule.schedule_id = dtx.`Schedule Id`
	set plugin_bookings_transactions_has_schedule.deposit = dtx.`Transaction Total`;

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `report_type`) VALUES ('Deposited Bookings', 'select \r\n		b.contact_id as `Contact Id`, CONCAT_WS(\' \', c.first_name, c.last_name) as `Student`, b.booking_id as `Booking Id`, s.`name` as `Schedule`, t.id as `Transaction Id`, t.total as `Transaction Total`\r\n	from plugin_contacts3_contacts c \r\n		inner join plugin_ib_educate_bookings b on b.contact_id = c.id\r\n		inner join plugin_ib_educate_booking_has_schedules hs on b.booking_id = hs.booking_id\r\n		inner join plugin_courses_schedules s on hs.schedule_id = s.id\r\n		inner join plugin_bookings_transactions t on t.booking_id = b.booking_id\r\n		inner join plugin_bookings_transactions_has_schedule ths on hs.schedule_id = ths.schedule_id and ths.transaction_id = t.id\r\n	where ths.deposit > 0 and b.`delete` = 0 and t.deleted = 0 and hs.deleted = 0 and ths.deleted = 0\r\n	order by `Student`, `Transaction Id`;\r\n', '1', '0', 'sql');
