/*
ts:2019-01-11 10:04:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`) VALUES ('course-payment-plan-due', 'Course Payment Plan due', 'EMAIL', '0', 'Payment Plan Balance Due Reminder', 'We are contacting you about your $bookingid.<br />\r\n<br />\r\nThere is a balance due today for this booking. Can you please pay online today? <br />\r\n<a href=\"$paylink\">click</a> to make a payment.<br />\r\n<br />\r\nBalance Due is €$dueamount<br />\r\n<br />\r\nKind Regards,<br />\r\nYour College');

UPDATE `plugin_reports_reports` SET `action_event`='var $tr = $(\"#report_table tbody > tr\");\r\nvar ids = [];\r\n$tr.each(function(){\r\n	if ($(this).find(\".row_check\").prop(\"checked\")) {\r\n		ids.push($(this).find(\"td\")[0].innerHTML);\r\n	}\r\n});\r\n\r\n$.post(\r\n	\"/admin/bookings/send_payment_plan_due_emails\",\r\n	{transaction_id:ids},\r\n	function (response) {\r\n		alert(\"Bookings have been emailed.\");\r\n	}\r\n);' WHERE (`name`='Payment Plan Due');

UPDATE `plugin_reports_reports` SET `checkbox_column`='1', `action_button_label`='Send email to selected bookings', `action_button`='1' WHERE (`name`='Payment Plan Due');

