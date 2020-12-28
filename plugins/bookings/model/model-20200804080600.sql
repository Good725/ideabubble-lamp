/*
ts:2020-08-04 08:06:00
*/

ALTER TABLE plugin_ib_educate_bookings_has_applications ADD COLUMN delegate_id INT;
ALTER TABLE plugin_ib_educate_bookings_has_applications DROP PRIMARY KEY;
ALTER TABLE plugin_ib_educate_bookings_has_applications ADD KEY (booking_id);
ALTER TABLE plugin_ib_educate_bookings_has_applications ADD COLUMN id  int NOT NULL AUTO_INCREMENT FIRST , ADD PRIMARY KEY (id);
ALTER TABLE plugin_ib_educate_bookings_has_applications_history ADD COLUMN application_id  int NULL AFTER id, ADD INDEX (application_id);

CREATE TABLE `plugin_ib_educate_bookings_rollcall` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `delegate_id` int(11) NOT NULL,
  `booking_item_id` int(11) NOT NULL,
  `booking_id` int(11) unsigned DEFAULT NULL,
  `timeslot_id` int(11) unsigned DEFAULT NULL,
  `seat_row_id` int(11) DEFAULT NULL,
  `seat_fee` int(11) DEFAULT NULL,
  `planned_to_attend` int(1) NOT NULL DEFAULT '1',
  `delete` int(1) DEFAULT '0',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attendance_status` set('Present','Late','Early Departures','Temporary Absence','Absent') DEFAULT NULL,
  `finance_status` enum('Paid', 'Unpaid') DEFAULT NULL,
  `booking_status` int(11) DEFAULT NULL,
  `amendable` tinyint(4) NOT NULL DEFAULT '0',
  `arrived` datetime DEFAULT NULL,
  `left` datetime DEFAULT NULL,
  `status_updated` datetime DEFAULT NULL,
  `temporary_absences` text,
  `planned_arrival` datetime DEFAULT NULL,
  `planned_leave` datetime DEFAULT NULL,
  `timeslot_status_alerted` set('Late','Early Departures','Temporary Absence','Absent') DEFAULT NULL,
  KEY `booking_id` (`booking_id`),
  KEY `timeslot_id` (`timeslot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

delete from plugin_ib_educate_bookings_rollcall;
insert into plugin_ib_educate_bookings_rollcall
	(
		delegate_id, booking_item_id, booking_id, timeslot_id, seat_row_id, seat_fee, planned_to_attend, date_created,
		attendance_status, booking_status, amendable, arrived, `left`, status_updated, temporary_absences, planned_arrival, planned_leave, timeslot_status_alerted, finance_status
	)
(select
		dc.contact_id, i.booking_item_id, i.booking_id, i.period_id, i.seat_row_id, i.seat_fee, i.attending, i.date_created,
		i.timeslot_status, i.booking_status, i.amendable, i.arrived, i.`left`, i.status_updated, i.temporary_absences, i.planned_arrival, i.planned_leave, i.timeslot_status_alerted, i.timeslot_status
	from plugin_ib_educate_booking_items i
	inner join (select bookings.booking_id, bookings.contact_id, sum(if(delegates.id is null, 0, 1)) as delegate_count
	from plugin_ib_educate_bookings bookings
		left join plugin_ib_educate_bookings_has_delegates delegates
			on bookings.booking_id = delegates.booking_id	and delegates.deleted = 0
	where bookings.`delete` = 0
	group by bookings.booking_id
	having delegate_count = 0
	order by delegate_count) dc on i.booking_id = dc.booking_id
where i.`delete` = 0
);

insert into plugin_ib_educate_bookings_has_delegates
	(
		contact_id, booking_id
	)
(
select
		dc.contact_id, bookings.booking_id
from plugin_ib_educate_bookings bookings
	inner join (select bookings.booking_id, bookings.contact_id, sum(if(delegates.id is null, 0, 1)) as delegate_count
	from plugin_ib_educate_bookings bookings
		left join plugin_ib_educate_bookings_has_delegates delegates
			on bookings.booking_id = delegates.booking_id	and delegates.deleted = 0
	where bookings.`delete` = 0
	group by bookings.booking_id
	having delegate_count = 0
	order by delegate_count) dc on bookings.booking_id = dc.booking_id
where bookings.`delete` = 0
);
