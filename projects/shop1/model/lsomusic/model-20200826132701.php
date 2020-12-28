<?php
/*
ts:2020-08-26 13:27:01
*/

function dalm_20200826132700()
{
    DB::query(database::UPDATE, 'update plugin_courses_schedules set attend_all_default=0')->execute();

    $applications = DB::select('*')
        ->from('plugin_ib_educate_bookings_has_applications')
        ->execute()
        ->as_array();

    foreach ($applications as $application) {
        $data = json_decode($application['data'], true);
        if (@$data['has_period']) {
            $periods = array();
            foreach ($data['has_period'] as $period) {
                $period = explode(',', $period);
                $periods[] = "'" . $period[0] . "'";
            }
            $delq = DB::query(Database::DELETE, "update plugin_ib_educate_bookings_rollcall
inner join plugin_courses_schedules_events on plugin_ib_educate_bookings_rollcall.timeslot_id=plugin_courses_schedules_events.id
set plugin_ib_educate_bookings_rollcall.`delete`=1
where plugin_ib_educate_bookings_rollcall.booking_id=" . $application['booking_id'] . "
and DATE_FORMAT(plugin_courses_schedules_events.datetime_start, '%a %H:%i') not in (" . implode(',', $periods) . ")");
            $delq->execute();

            $delq = DB::query(Database::DELETE, "update plugin_ib_educate_booking_items
inner join plugin_courses_schedules_events on plugin_ib_educate_booking_items.period_id=plugin_courses_schedules_events.id
set plugin_ib_educate_booking_items.`delete`=1
where plugin_ib_educate_booking_items.booking_id=" . $application['booking_id'] . "
and DATE_FORMAT(plugin_courses_schedules_events.datetime_start, '%a %H:%i') not in (" . implode(',', $periods) . ")");
            $delq->execute();
        }
    }

}

dalm_20200826132700();
