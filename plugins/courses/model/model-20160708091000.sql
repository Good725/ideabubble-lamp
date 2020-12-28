/*
ts:2016-07-08 09:10:00
*/

ALTER TABLE plugin_courses_schedules ADD COLUMN `fee_per` ENUM('Timeslot', 'Schedule') NOT NULL DEFAULT 'Timeslot';
ALTER TABLE plugin_courses_schedules_events ADD COLUMN `fee_amount` DECIMAL(10, 2);

UPDATE plugin_courses_schedules s
	SET s.fee_per = 'Schedule'
	WHERE s.payment_type = 1;
UPDATE plugin_courses_schedules s
	SET s.fee_per = 'Timeslot'
	WHERE s.payment_type = 2;

UPDATE plugin_courses_schedules_events e INNER JOIN plugin_courses_schedules s ON e.schedule_id = s.id
	SET e.fee_amount = s.fee_amount
	WHERE s.is_fee_required = 1 AND s.fee_per = 'Timeslot';

UPDATE plugin_courses_schedules_events e INNER JOIN plugin_courses_schedules s ON e.schedule_id = s.id
	SET e.fee_amount = null
	WHERE s.fee_per = 'Schedule' OR s.is_fee_required = 0;


