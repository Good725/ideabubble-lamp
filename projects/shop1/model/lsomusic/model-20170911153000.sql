/*
ts:2017-09-11 15:30:00
*/

INSERT INTO `plugin_reports_reports`
  (`name`, `sql`, `date_modified`, `publish`, `delete`, `report_type`, `autoload`)
  VALUES
  ('Duplicate Registrations', 'select \r\n		c.first_name, c.last_name, s.`name` as schedule, schedule_id, contact_id, count(*) as duplicates\r\n	from plugin_courses_schedules_has_students has\r\n		inner join plugin_contacts_contact c on has.contact_id = c.id\r\n		inner join plugin_courses_schedules s on has.schedule_id = s.id\r\n	group by schedule_id, contact_id\r\n	having duplicates > 1\r\n	order by duplicates desc;', NOW(), '1', '0', 'sql', '1');

