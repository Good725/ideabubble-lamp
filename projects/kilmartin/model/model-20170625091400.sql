/*
ts:2017-06-25 09:14:00
*/

update plugin_courses_schedules s inner join plugin_courses_schedules_events e on s.id = e.schedule_id
		set e.fee_amount = s.fee_amount
	where s.fee_per = 'Timeslot' and (e.fee_amount = '' or e.fee_amount is null or e.fee_amount = 0);
