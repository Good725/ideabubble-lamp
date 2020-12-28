/*
ts:2019-02-11 08:54:00
*/

ALTER TABLE `plugin_ib_educate_bookings_has_applications` MODIFY COLUMN `interview_status`  ENUM('Scheduled','No Follow Up','Interviewed','Accepted','No Offer','Cancelled','Not Scheduled','On Hold');

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `date_modified`, `publish`, `delete`, `report_type`) VALUES ('Daily Interview Roll Call', 'select \r\n		students.id as `Student ID`,\r\n		bookings.booking_id as `Interview ID`,\r\n		CONCAT_WS(\' \', students.first_name, students.last_name) as `Student`,\r\n		emails.`value` as `Email`,\r\n		mobiles.`value` as `Mobile`,\r\n		DATE_FORMAT(timeslots.datetime_start, \'%d/%M/%Y\') as `Date`,\r\n		DATE_FORMAT(timeslots.datetime_start, \'%H:%i\') as `Time`,\r\n		\'                    \' as `Status`\r\n		\r\n	from plugin_ib_educate_bookings bookings\r\n		inner join plugin_ib_educate_bookings_has_courses has_courses on bookings.booking_id = has_courses.booking_id and has_courses.deleted = 0\r\n		inner join plugin_courses_courses courses on has_courses.course_id = courses.id\r\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\r\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\r\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\r\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n		inner join plugin_contacts3_contacts students on bookings.contact_id = students.id and students.delete = 0\r\n                left join plugin_contacts3_family f on students.family_id = f.family_id\r\n left join plugin_contacts3_contacts kin on f.family_id = kin.family_id and kin.id <> students.id\r\n		left join plugin_contacts3_contact_has_notifications emails on students.notifications_group_id = emails.group_id and emails.notification_id=2\r\n		left join plugin_contacts3_contact_has_notifications mobiles on students.notifications_group_id = mobiles.group_id and mobiles.notification_id=1\r\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and applications.interview_status = \'Scheduled\' and timeslots.datetime_start >= \'{!Date!}\' and timeslots.datetime_start <= DATE_ADD(\'{!Date!}\', INTERVAL 1 DAY) AND schedules.id = \'{!Schedule!}\'\r\n	order by `Time`,`Student`', '0', '2019-02-11 09:00:24', '1', '0', 'sql');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Daily Interview Roll Call'), 'date', 'Date');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`) VALUES ((select id from plugin_reports_reports where name='Daily Interview Roll Call'), 'custom', 'Schedule', 'select distinct \r\n		schedules.id, schedules.name\r\n	from plugin_ib_educate_bookings bookings\r\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\r\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\r\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\r\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and applications.interview_status = \'Scheduled\' and timeslots.datetime_start >= \'{!Date!}\' and timeslots.datetime_start <= DATE_ADD(\'{!Date!}\', INTERVAL 1 DAY)\r\n	order by schedules.name');


INSERT INTO `plugin_reports_reports` (`name`, `sql`, `date_modified`, `publish`, `delete`, `report_type`, `action_button_label`, `action_button`, `action_event`, `custom_report_rules`) VALUES ('Interview Status Bulk Update', 'select \r\n		students.id as `Student ID`,\r\n		bookings.booking_id as `Interview ID`,\r\n		CONCAT_WS(\' \', students.first_name, students.last_name) as `Student`,\r\n		emails.`value` as `Email`,\r\n		mobiles.`value` as `Mobile`,\r\n		DATE_FORMAT(timeslots.datetime_start, \'%d/%M/%Y\') as `Date`,\r\n		DATE_FORMAT(timeslots.datetime_start, \'%H:%i\') as `Time`,\r\n		CONCAT(\'<select name=\"interview_status[\', applications.booking_id, \']\" data-selected=\"\', applications.interview_status,\'\"><option value=\"Not Scheduled\">Not Scheduled</option><option value=\"Scheduled\">Scheduled</option><option value=\"No Follow Up\">No Follow Up</option><option value=\"Interviewed\">Interviewed</option><option value=\"Accepted\">Accepted</option><option value=\"No Offer\">No Offer</option><option value=\"Cancelled\">Cancelled</option><option value=\"On Hold\">On Hold</option>\') as `Status`\r\n		\r\n	from plugin_ib_educate_bookings bookings\r\n		inner join plugin_ib_educate_bookings_has_courses has_courses on bookings.booking_id = has_courses.booking_id and has_courses.deleted = 0\r\n		inner join plugin_courses_courses courses on has_courses.course_id = courses.id\r\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\r\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\r\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\r\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n		inner join plugin_contacts3_contacts students on bookings.contact_id = students.id and students.delete = 0\r\n                left join plugin_contacts3_family f on students.family_id = f.family_id\r\n left join plugin_contacts3_contacts kin on f.family_id = kin.family_id and kin.id <> students.id\r\n		left join plugin_contacts3_contact_has_notifications emails on students.notifications_group_id = emails.group_id and emails.notification_id=2\r\n		left join plugin_contacts3_contact_has_notifications mobiles on students.notifications_group_id = mobiles.group_id and mobiles.notification_id=1\r\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and timeslots.datetime_start >= \'{!Date!}\' and timeslots.datetime_start <= DATE_ADD(\'{!Date!}\', INTERVAL 1 DAY) AND schedules.id = \'{!Schedule!}\'\r\n	order by `Time`,`Student`', '2019-02-11 09:54:11', '1', '0', 'sql', 'Update Status', '1', 'var data = {};\r\ndata.interviews = [];\r\n$(\"#report_table tbody > tr select\").each(function(){\r\n	var interview = {};\r\n	interview.booking_id = this.name.replace(\"]\", \"\").replace(\"interview_status[\", \"\");\r\n	interview.interview_status = this.value;\r\n        data.interviews.push(interview)\r\n});\r\n\r\n$.post(\r\n	\"/admin/bookings/set_interview_status_bulk\",\r\n	data,\r\n	function (response){\r\n		alert(\"Updated\");\r\n	}\r\n);\r\n', '$(\"#report_table tbody > tr select\").each(function(){\r\n    $(this).val($(this).data(\"selected\"));\r\n});');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Interview Status Bulk Update'), 'date', 'Date');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`) VALUES ((select id from plugin_reports_reports where name='Interview Status Bulk Update'), 'custom', 'Schedule', 'select distinct \r\n		schedules.id, schedules.name\r\n	from plugin_ib_educate_bookings bookings\r\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\r\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\r\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\r\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and applications.interview_status = \'Scheduled\' and timeslots.datetime_start >= \'{!Date!}\' and timeslots.datetime_start <= DATE_ADD(\'{!Date!}\', INTERVAL 1 DAY)\r\n	order by schedules.name');


INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `created_by`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('interview-accept-offer', 'Interview Accept Offer', 'EMAIL', '0', 'Interview Accept Offer', '$date<br />\r\n$course $code<br />\r\n<br />\r\nDear $student,<br />\r\n<br />\r\nFollowing your application and interview for entry to this College we are pleased to offer you a place on the above course pending your Leaving Certificate results/FETAC results (if applicable).<br />\r\nPlease note: All Non-EU students must provide evidence of your approval to remain in the state. <br />\r\n<br />\r\nAn additional Economic fee of €3,654 may apply to Non-EU Students who do not hold stamp 4, 5 & 6. This is payable to Ballyfermot College at your registration. If you have any queries please contact the college.<br />\r\nThe full examination/certification/registration fee for 2018/2019 (inclusive of deposit) is €735.<br />\r\nIf you intend taking up this offer you must accept (by post or in person), by Thursday, 15th February 2018. We would appreciate, if you have been offered more than one course in the College, that you accept your first preference course only. This is to facilitate administration and planning purposes.<br />\r\nTo ensure your place on the course the College must receive:<br />\r\n1. Completed Acceptance Form (print from offer email) return by Thursday, 15th February 2018<br />\r\n <br />\r\n2. A deposit of €40 to secure a place (non-refundable) No Cash Accepted<br />\r\n <br />\r\n3. A copy of your Leaving Certificate/FETAC/QQI results must be forwarded to the College by Monday, 20th August 2018 with your name and course name on the back. (if applicable)<br />\r\nYour registration date is Thursday 23rd August at 11.30am and the balance of fees (€695.00) must be paid on or before that date. (Please note, it is possible to pay the balance of fees by post, or in person (cheque, postal order, bank draft) or, by credit card in person, or over the phone (01-626 9421) up to 17th August 2018.<br />\r\nAn in-date Passport or original Birth Cert must be shown on your registration day. Photocopies will not be accepted.<br />\r\nAll registrations will be held in the Anna Brett Hall, Media Building on Ballyfermot Road.<br />\r\nYour starting date is Thursday 06th September at 11.00am in the Art Building<br />\r\nYours sincerely,<br />\r\n<br />\r\n$staff<br />\r\nCourse Co-ordinator<br />\r\nPlease note: Any class may be closed or amalgamated if insufficient numbers enrol or the attendance falls below the minimum number.<br />\r\n', '1', '$date,$student,$staff,$course,$code', 'bookings');
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('interview-waiting-list', 'Interview Waiting List', 'EMAIL', '0', 'Interview Waiting List', '	$date<br />\r\n<br />\r\nRe:-  $course<br />\r\n<br />\r\nApplicant: $student<br />\r\n<br />\r\nDear $student,<br />\r\n<br />\r\nFurther to your application for admission to the above course we wish to advise you that you have been placed on a priority waiting list.<br />\r\n<br />\r\nThe standard and number of applicants was very high.  We had limited places to offer and there was a large number of applicants.<br />\r\n<br />\r\nThe college will contact you should a place become available.<br />\r\n<br />\r\nYours sincerely<br />\r\n<br />\r\n<br />\r\n$staff<br />\r\nCourse Co-ordinator', 'bookings', '$date,$student,$staff,$course,$code', 'bookings');
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `created_by`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('interview-no-offer', 'Interview No Offer', 'EMAIL', '0', 'Interview No Offer', '$date:-<br />\r\n<br />\r\nRe:- $course<br />\r\n<br />\r\nDear $student<br />\r\n<br />\r\nFurther to your application and interview for the above course I regret to inform you that your application was unsuccessful.<br />\r\n<br />\r\nThe standard of applicants this year was extremely high and your unsuccessful application does not indicate, in any way, that you are unsuitable to pursue this career.<br />\r\n<br />\r\nThank you for your interest in applying to the course.  I wish you well for the future.<br />\r\n<br />\r\nYours sincerely<br />\r\n<br />\r\n$staff$ Course co-ordinator', '0', 'bookings', '$date,$student,$staff,$course,$code', 'bookings');

select id into @templates_id_20190211_1108 from plugin_files_file where name='templates' and parent_id=1;
insert into plugin_files_file (type,name,language,parent_id,deleted) values(1,'Interview_No_Offer','en',@templates_id_20190211_1108,0);
insert into plugin_files_version (file_id,name,mime_type,size,path,active,deleted) values((select id from plugin_files_file where name='Interview_No_Offer'),'Interview_No_Offer.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',0,'',1,0);
insert into plugin_files_file (type,name,language,parent_id,deleted) values(1,'Interview_Offer','en',@templates_id_20190211_1108,0);
insert into plugin_files_version (file_id,name,mime_type,size,path,active,deleted) values((select id from plugin_files_file where name='Interview_Offer'),'Interview_Offer.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',0,'',1,0);
insert into plugin_files_file (type,name,language,parent_id,deleted) values(1,'Interview_Waitinglist','en',@templates_id_20190211_1108,0);
insert into plugin_files_version (file_id,name,mime_type,size,path,active,deleted) values((select id from plugin_files_file where name='Interview_Waitinglist'),'Interview_Waitinglist.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',0,'',1,0);

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `report_type`, `generate_documents`, `generate_documents_template_file_id`, `generate_documents_helper_method`, `generate_documents_link_by_template_variable`, `generate_documents_mode`, `generate_documents_row_variable`) VALUES ('Interview Documents No Offer', 'select \r\n		students.id as `Student ID`,\r\n		bookings.booking_id as `Interview ID`,\r\n		CONCAT_WS(\' \', students.first_name, students.last_name) as `Student`,\r\n		emails.`value` as `Email`,\r\n		mobiles.`value` as `Mobile`,\r\n		courses.title as `Course`,\r\n		courses.code as `Code`,\r\n		applications.interview_status as `Status`\r\n		\r\n	from plugin_ib_educate_bookings bookings\r\n		inner join plugin_ib_educate_bookings_has_courses has_courses on bookings.booking_id = has_courses.booking_id and has_courses.deleted = 0\r\n		inner join plugin_courses_courses courses on has_courses.course_id = courses.id\r\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\r\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\r\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\r\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n		inner join plugin_contacts3_contacts students on bookings.contact_id = students.id and students.delete = 0\r\n                left join plugin_contacts3_family f on students.family_id = f.family_id\r\n left join plugin_contacts3_contacts kin on f.family_id = kin.family_id and kin.id <> students.id\r\n		left join plugin_contacts3_contact_has_notifications emails on students.notifications_group_id = emails.group_id and emails.notification_id=2\r\n		left join plugin_contacts3_contact_has_notifications mobiles on students.notifications_group_id = mobiles.group_id and mobiles.notification_id=1\r\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and applications.interview_status = \'No Offer\'\r\n	order by `Student`', '1', '0', 'sql', '1', (select id from plugin_files_file where name='Interview_No_Offer'), 'Model_Docarrayhelper->interview_details', 'contact_id', 'ROW', 'Interview ID');
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `report_type`, `generate_documents`, `generate_documents_template_file_id`, `generate_documents_helper_method`, `generate_documents_link_by_template_variable`, `generate_documents_mode`, `generate_documents_row_variable`) VALUES ('Interview Documents Accept Offer', 'select \r\n		students.id as `Student ID`,\r\n		bookings.booking_id as `Interview ID`,\r\n		CONCAT_WS(\' \', students.first_name, students.last_name) as `Student`,\r\n		emails.`value` as `Email`,\r\n		mobiles.`value` as `Mobile`,\r\n		courses.title as `Course`,\r\n		courses.code as `Code`,\r\n		applications.interview_status as `Status`\r\n		\r\n	from plugin_ib_educate_bookings bookings\r\n		inner join plugin_ib_educate_bookings_has_courses has_courses on bookings.booking_id = has_courses.booking_id and has_courses.deleted = 0\r\n		inner join plugin_courses_courses courses on has_courses.course_id = courses.id\r\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\r\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\r\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\r\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n		inner join plugin_contacts3_contacts students on bookings.contact_id = students.id and students.delete = 0\r\n                left join plugin_contacts3_family f on students.family_id = f.family_id\r\n left join plugin_contacts3_contacts kin on f.family_id = kin.family_id and kin.id <> students.id\r\n		left join plugin_contacts3_contact_has_notifications emails on students.notifications_group_id = emails.group_id and emails.notification_id=2\r\n		left join plugin_contacts3_contact_has_notifications mobiles on students.notifications_group_id = mobiles.group_id and mobiles.notification_id=1\r\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and applications.interview_status <> \'No Offer\' and applications.interview_status <> \'On Hold\' \r\n	order by `Student`', '1', '0', 'sql', '1', (select id from plugin_files_file where name='Interview_Offer'), 'Model_Docarrayhelper->interview_details', 'contact_id', 'ROW', 'Interview ID');
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `report_type`, `generate_documents`, `generate_documents_template_file_id`, `generate_documents_helper_method`, `generate_documents_link_by_template_variable`, `generate_documents_mode`, `generate_documents_row_variable`) VALUES ('Interview Documents Waiting List', 'select \r\n		students.id as `Student ID`,\r\n		bookings.booking_id as `Interview ID`,\r\n		CONCAT_WS(\' \', students.first_name, students.last_name) as `Student`,\r\n		emails.`value` as `Email`,\r\n		mobiles.`value` as `Mobile`,\r\n		courses.title as `Course`,\r\n		courses.code as `Code`,\r\n		applications.interview_status as `Status`\r\n		\r\n	from plugin_ib_educate_bookings bookings\r\n		inner join plugin_ib_educate_bookings_has_courses has_courses on bookings.booking_id = has_courses.booking_id and has_courses.deleted = 0\r\n		inner join plugin_courses_courses courses on has_courses.course_id = courses.id\r\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\r\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\r\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\r\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n		inner join plugin_contacts3_contacts students on bookings.contact_id = students.id and students.delete = 0\r\n                left join plugin_contacts3_family f on students.family_id = f.family_id\r\n left join plugin_contacts3_contacts kin on f.family_id = kin.family_id and kin.id <> students.id\r\n		left join plugin_contacts3_contact_has_notifications emails on students.notifications_group_id = emails.group_id and emails.notification_id=2\r\n		left join plugin_contacts3_contact_has_notifications mobiles on students.notifications_group_id = mobiles.group_id and mobiles.notification_id=1\r\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and applications.interview_status = \'Waiting List\'\r\n	order by `Student`', '1', '0', 'sql', '1', (select id from plugin_files_file where name='Interview_Waitinglist'), 'Model_Docarrayhelper->interview_details', 'contact_id', 'ROW', 'Interview ID');

update plugin_messaging_notification_templates set message=replace(message,'$staff', '') where name in ('interview-accept-offer', 'interview-waiting-list', 'interview-no-offer');;

select id into @templates_id_20190211_12 from plugin_files_file where name='templates' and parent_id=1;
insert into plugin_files_file (type,name,language,parent_id,deleted) values(1,'Interview_Details','en',@templates_id_20190211_12,0);
insert into plugin_files_version (file_id,name,mime_type,size,path,active,deleted) values((select id from plugin_files_file where name='Interview_Details'),'Interview_Details.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',0,'',1,0);

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `report_type`, `generate_documents`, `generate_documents_template_file_id`, `generate_documents_helper_method`, `generate_documents_link_by_template_variable`, `generate_documents_mode`, `generate_documents_row_variable`) VALUES ('Interview Documents', 'select \r\n		students.id as `Student ID`,\r\n		bookings.booking_id as `Interview ID`,\r\n		CONCAT_WS(\' \', students.first_name, students.last_name) as `Student`,\r\n		emails.`value` as `Email`,\r\n		mobiles.`value` as `Mobile`,\r\n		courses.title as `Course`,\r\n		courses.code as `Code`,\r\n		applications.interview_status as `Status`\r\n		\r\n	from plugin_ib_educate_bookings bookings\r\n		inner join plugin_ib_educate_bookings_has_courses has_courses on bookings.booking_id = has_courses.booking_id and has_courses.deleted = 0\r\n		inner join plugin_courses_courses courses on has_courses.course_id = courses.id\r\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\r\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\r\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\r\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n		inner join plugin_contacts3_contacts students on bookings.contact_id = students.id and students.delete = 0\r\n                left join plugin_contacts3_family f on students.family_id = f.family_id\r\n left join plugin_contacts3_contacts kin on f.family_id = kin.family_id and kin.id <> students.id\r\n		left join plugin_contacts3_contact_has_notifications emails on students.notifications_group_id = emails.group_id and emails.notification_id=2\r\n		left join plugin_contacts3_contact_has_notifications mobiles on students.notifications_group_id = mobiles.group_id and mobiles.notification_id=1\r\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and applications.interview_status <> \'No Offer\' and applications.interview_status <> \'On Hold\' \r\n	order by `Student`', '1', '0', 'sql', '1', (select id from plugin_files_file where name='Interview_Details'), 'Model_Docarrayhelper->interview_details', 'contact_id', 'ROW', 'Interview ID');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Interview Documents'), 'date', 'Date');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`) VALUES ((select id from plugin_reports_reports where name='Interview Documents'), 'custom', 'Schedule', 'select distinct \r\n		schedules.id, schedules.name\r\n	from plugin_ib_educate_bookings bookings\r\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\r\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\r\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\r\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\r\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and applications.interview_status = \'Scheduled\' and timeslots.datetime_start >= \'{!Date!}\' and timeslots.datetime_start <= DATE_ADD(\'{!Date!}\', INTERVAL 1 DAY)\r\n	order by schedules.name');

ALTER TABLE `plugin_ib_educate_bookings_has_applications` MODIFY COLUMN `interview_status`  ENUM('Scheduled','No Follow Up', 'Interviewed','Accepted','No Offer','Cancelled','Not Scheduled','On Hold','Offered', 'No Show', 'Waiting List');

UPDATE `plugin_reports_reports` SET `sql`='select \n		students.id as `Student ID`,\n		bookings.booking_id as `Interview ID`,\n		CONCAT_WS(\' \', students.first_name, students.last_name) as `Student`,\n		emails.`value` as `Email`,\n		mobiles.`value` as `Mobile`,\n		DATE_FORMAT(timeslots.datetime_start, \'%d/%M/%Y\') as `Date`,\n		DATE_FORMAT(timeslots.datetime_start, \'%H:%i\') as `Time`,\n		applications.interview_status as `Status`,\r\n		\' \' as `Offered`, \' \' as `No Offer`, \' \' as `Waiting List` , \' \' as `No Show`\r\n   \n		\n	from plugin_ib_educate_bookings bookings\n		inner join plugin_ib_educate_bookings_has_courses has_courses on bookings.booking_id = has_courses.booking_id and has_courses.deleted = 0\n		inner join plugin_courses_courses courses on has_courses.course_id = courses.id\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\n		inner join plugin_contacts3_contacts students on bookings.contact_id = students.id and students.delete = 0\n                left join plugin_contacts3_family f on students.family_id = f.family_id\n left join plugin_contacts3_contacts kin on f.family_id = kin.family_id and kin.id <> students.id\n		left join plugin_contacts3_contact_has_notifications emails on students.notifications_group_id = emails.group_id and emails.notification_id=2\n		left join plugin_contacts3_contact_has_notifications mobiles on students.notifications_group_id = mobiles.group_id and mobiles.notification_id=1\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and timeslots.datetime_start >= \'{!Date!}\' and timeslots.datetime_start <= DATE_ADD(\'{!Date!}\', INTERVAL 1 DAY) AND schedules.id = \'{!Schedule!}\'\n	order by `Time`,`Student`' WHERE (`name`='Interview Documents');
UPDATE `plugin_reports_reports` SET `sql`='select \n		students.id as `Student ID`,\n		bookings.booking_id as `Interview ID`,\n		CONCAT_WS(\' \', students.first_name, students.last_name) as `Student`,\n		emails.`value` as `Email`,\n		mobiles.`value` as `Mobile`,\n		DATE_FORMAT(timeslots.datetime_start, \'%d/%M/%Y\') as `Date`,\n		DATE_FORMAT(timeslots.datetime_start, \'%H:%i\') as `Time`,\n		CONCAT(\'<select name=\"interview_status[\', applications.booking_id, \']\" data-selected=\"\', applications.interview_status,\'\"><option value=\"Not Scheduled\">Not Scheduled</option><option value=\"Scheduled\">Scheduled</option><option value=\"No Follow Up\">No Follow Up</option><option value=\"No Show\">No Show</option><option value=\"Waiting List\">Waiting List</option><option value=\"Interviewed\">Interviewed</option><option value=\"Accepted\">Accepted</option><option value=\"No Offer\">No Offer</option><option value=\"Cancelled\">Cancelled</option><option value=\"On Hold\">On Hold</option><option value=\"Offered\">Offered</option>\') as `Status`\n		\n	from plugin_ib_educate_bookings bookings\n		inner join plugin_ib_educate_bookings_has_courses has_courses on bookings.booking_id = has_courses.booking_id and has_courses.deleted = 0\n		inner join plugin_courses_courses courses on has_courses.course_id = courses.id\n		inner join plugin_ib_educate_bookings_has_applications applications on bookings.booking_id = applications.booking_id\n		inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id and items.`delete` = 0\n		left join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id and timeslots.delete=0\n		left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id\n		inner join plugin_contacts3_contacts students on bookings.contact_id = students.id and students.delete = 0\n                left join plugin_contacts3_family f on students.family_id = f.family_id\n left join plugin_contacts3_contacts kin on f.family_id = kin.family_id and kin.id <> students.id\n		left join plugin_contacts3_contact_has_notifications emails on students.notifications_group_id = emails.group_id and emails.notification_id=2\n		left join plugin_contacts3_contact_has_notifications mobiles on students.notifications_group_id = mobiles.group_id and mobiles.notification_id=1\n	where bookings.`delete` = 0 and bookings.booking_status <> 3 and timeslots.datetime_start >= \'{!Date!}\' and timeslots.datetime_start <= DATE_ADD(\'{!Date!}\', INTERVAL 1 DAY) AND schedules.id = \'{!Schedule!}\'\n	order by `Time`,`Student`' WHERE (`name`='Interview Status Bulk Update');


update plugin_reports_reports set `sql`=replace(`sql`, 'emails.group_id and emails.notification_id=2', 'emails.group_id and  emails.notification_id=1') where `sql` like '%emails.group_id and emails.notification_id=2%';
update plugin_reports_reports set `sql`=replace(`sql`, 'mobiles.group_id and mobiles.notification_id=1', 'mobiles.group_id and  mobiles.notification_id=2') where `sql` like '%mobiles.group_id and mobiles.notification_id=1%';