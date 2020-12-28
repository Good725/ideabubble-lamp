/*
ts:2019-05-01 08:00:00
*/

select id into @templates_id_20190501_timetable_1 from plugin_files_file where name='templates' and parent_id=1;
insert into plugin_files_file (type,name,language,parent_id,deleted) values(1,'timetable','en',@templates_id_20190501_timetable_1,0);
insert into plugin_files_version (file_id,name,mime_type,size,path,active,deleted) values((select id from plugin_files_file where name='timetable'),'timetable.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',0,'',1,0);

INSERT INTO `plugin_reports_reports`
  (`name`, `sql`, `report_type`, `generate_documents`, `generate_documents_template_file_id`, `generate_documents_helper_method`, `generate_documents_link_by_template_variable`, `generate_documents_mode`, `generate_documents_row_variable`)
  VALUES
  ('Timetable Print', 'select \r\n		distinct schedules.`name` as schedule, \r\n		CONCAT_WS(\' \', students.first_name, students.last_name) as student,\r\n		bookings.contact_id as student_id\r\n	from plugin_ib_educate_bookings bookings\r\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id\r\n		inner join plugin_courses_schedules_events timeslots  on items.period_id = timeslots.id\r\n		inner join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n                inner join plugin_courses_courses courses on schedules.course_id = courses.id\r\n		inner join plugin_contacts3_contacts students on bookings.contact_id = students.id\r\n		left join plugin_courses_locations locations on schedules.location_id = locations.id\r\n		left join plugin_courses_locations plocations on locations.parent_id = plocations.id\r\n		left join plugin_contacts3_contacts teachers on teachers.id = ifnull(timeslots.trainer_id, schedules.trainer_id)\r\n	where \r\n		items.`delete` = 0 and items.booking_status <> 3\r\n                and timeslots.datetime_start >= \'{!From!}\' and timeslots.datetime_start <= \'{!To!}\'\r\n                and (courses.category_id = \'{!Category!}\' or \'{!Category!}\' = \'\')\r\n	order by timeslots.datetime_start asc', 'sql', '1', (select id from plugin_files_file where name='timetable'), 'Model_Docarrayhelper->timetable', 'student_id', 'ROW', 'student_id');

INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Timetable Print'), 'date', 'From');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Timetable Print'), 'date', 'To');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`) VALUES ((select id from plugin_reports_reports where name='Timetable Print'), 'custom', 'Category', '(select id, category from plugin_courses_categories where `delete` = 0 order by category)');
