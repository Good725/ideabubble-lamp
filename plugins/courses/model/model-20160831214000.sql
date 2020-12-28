/*
ts:2016-08-31 21:40:00
*/


insert into `plugin_messaging_notification_templates`
	set `send_interval` = null,
	`name` = 'course-booking-admin',
	`description` = '',
	`driver` = 'EMAIL',
	`type_id` = '1',
	`subject` = 'A new course booking',
	`sender` = '',
	`message` = '<h1>A new course booking has been made.</h1>\nBooking ID:$bookingid<br />\nCourse: $course<br />\nSchedule: $schedule<br />\nPayment Type: $paymenttype<br />\nDeposit: $deposit<br />\nTotal: $total\n<br />Status: $status',
	`overwrite_cms_message` = '0',
	`page_id` = '0',
	`header` = '',
	`footer` = '',
	`schedule` = null,
	`date_created` = NOW(),
	`date_updated` = NOW(),
	`last_sent` = null,
	`publish` = 1,
	`deleted` = 0,
	`create_via_code` = 'course booking',
	`usable_parameters_in_template` = '$bookingid,$course,$schedule,$paymenttype,$deposit,$total,$status',
	`doc_generate` = null,
	`doc_helper` = null,
	`doc_template_path` = null,
	`doc_type` = null,
	`category_id` = 0;
	insert into `plugin_messaging_notification_template_targets` set `template_id` = (select id from plugin_messaging_notification_templates where `name`='course-booking-admin'), `target_type` = 'CMS_CONTACT', `target` = '1', `x_details` = 'to', `date_created` = NOW();

insert into `plugin_messaging_notification_templates`
	set `send_interval` = null,
	`name` = 'course-booking-parent',
	`description` = '',
	`driver` = 'EMAIL',
	`type_id` = '1',
	`subject` = 'Course Booking Details',
	`sender` = '',
	`message` = '<h1>Your course booking details:</h1>\nBooking ID:$bookingid<br />\nCourse: $course<br />\nSchedule: $schedule<br />\nPayment Type: $paymenttype<br />\nDeposit: $deposit<br />\nTotal: $total<br />\nStatus: $status',
	`overwrite_cms_message` = '0',
	`page_id` = '0',
	`header` = '',
	`footer` = '',
	`schedule` = null,
	`date_created` = NOW(),
	`date_updated` = NOW(),
	`last_sent` = null,
	`publish` = '1',
	`deleted` = '0',
	`create_via_code` = 'course booking',
	`usable_parameters_in_template` = '$bookingid,$course,$schedule,$paymenttype,$deposit,$total,$status',
	`doc_generate` = null,
	`doc_helper` = null,
	`doc_template_path` = null,
	`doc_type` = null,
	`category_id` = '0';
