/*
ts:2019-11-04 12:31:00
*/


UPDATE plugin_ib_educate_bookings `booking`
    INNER JOIN
    plugin_ib_educate_bookings_status `booking_status` ON `booking`.booking_status = booking_status.status_id
    INNER JOIN
    plugin_ib_educate_booking_items `booking_item` ON booking.booking_id = booking_item.booking_id
    INNER JOIN
    plugin_ib_educate_bookings_status `booking_item_status` ON booking_item.booking_status = booking_item_status.status_id
    INNER JOIN
    plugin_courses_schedules_events `schedule_events` ON `booking_item`.period_id = `schedule_events`.id
SET booking_item.delete = 0
WHERE schedule_events.delete = 0
  AND booking_item.delete = 1
  AND (booking_status.title = 'Confirmed'
    OR booking_status.title = 'In Progress')
  AND (booking_item_status.title = 'Confirm'
    OR booking_item_status.title = 'In Progress');
