/*
ts:2019-12-12 17:00:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `created_by`, `publish`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('courses-schedule-assigned', 'Courses Schedule Assigned', 'EMAIL', '0', 'New Schedule Assigned', 'Schedule Id:$schedule_id<br />\r\nSchedule: $schedule<br />\r\nTimeslots: <br />$timeslots', '0', '1', '$name,$schedule,$schedule_id,$timeslots', 'courses');
