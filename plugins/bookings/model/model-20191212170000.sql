/*
ts:2019-12-12 17:00:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `created_by`, `publish`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('booking-schedule-start-reminder', 'Booking Schedule Start Reminder', 'EMAIL', '0', 'Schedule Start Reminder', '$name,<br />\r\nYour schedule $schedule starts at $starts.<br />\r\n', '0', '0', '$name,$schedule,$starts', 'bookings');

INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES (
  'bookings_schedule_start_reminder_days_before',
  'Booking Schedule Start Reminder Days Before',
  'bookings',
  '10/1',
  '10/1',
  '10/1',
  '10/1',
  '10/1',
  'Booking Schedule Start Reminder Days Before',
  'text',
  'Bookings',
  ''
);

INSERT INTO `engine_cron_tasks`
  (`title`, `frequency`, `plugin_id`, `publish`, `action`)
  VALUES
  ('Booking Schedule Start Reminder', '{\"minute\":[\"0\"],\"hour\":[\"0\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}', (select id from engine_plugins where name='bookings'), '0', 'cron_booking_schedule_start_reminder');

ALTER TABLE plugin_ib_educate_booking_items ADD COLUMN timeslot_status_alerted SET('Late','Early Departures','Temporary Absence','Absent');
