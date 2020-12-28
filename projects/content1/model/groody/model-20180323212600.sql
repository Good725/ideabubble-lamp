/*
ts:2018-03-23 21:26:00
*/

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `report_type`) VALUES ('Booking Reports', 'select r.*, s.academic_status, y.academic_year, t.apt_type, u.uni_name, p.booking_period, c.country, n.country as nationality, l.course_name, h.heard_about_label, i.title\r\n	from booking_form_booking_records r\r\n		left join booking_form_academic_status s on r.academic_status_id = s.id\r\n		left join booking_form_academic_year y on r.academic_year_id = y.id\r\n		left join booking_form_apt_type t on r.apt_type_id = t.id\r\n		left join booking_form_attended_uni u on r.attended_uni_id = u.id\r\n		left join booking_form_booking_period p on r.booking_period_id = p.id\r\n		left join booking_form_countries c on r.country_id = c.id\r\n		left join booking_form_countries n on r.nationality_id = n.id\r\n		left join booking_form_course_list l on r.course_id = l.id\r\n		left join booking_form_heard_about h on r.heard_about_id = h.id\r\n		left join booking_form_title i on r.title_id = i.id\r\n	where r.date_created >= \'{!From!}\' and r.date_created < date_add(\'{!To!}\', interval 1 day)\r\n	order by r.id desc', '1', '0', 'sql');
insert into plugin_reports_parameters
	(report_id, `type`, `name`, `value`, `delete`, `is_multiselect`)
	(select id, 'date', 'From', '', 0, 0 from plugin_reports_reports where `name` = 'Booking Reports');

insert into plugin_reports_parameters
	(report_id, `type`, `name`, `value`, `delete`, `is_multiselect`)
	(select id, 'date', 'To', '', 0, 0 from plugin_reports_reports where `name` = 'Booking Reports');
