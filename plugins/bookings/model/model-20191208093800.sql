/*
ts:2019-12-08 09:38:00
*/


INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `message`)
  VALUES
  ('course-payment-plan-reminder', 'Course Payment Plan Reminder', 'EMAIL', '0', 'Payment Plan Reminder', 'We are contacting you about your $bookingid.<br />\r\n<br />\r\nThere is a payment due $duedate for this booking. Can you please pay online before $duedate? <br />\r\n<a href=\"$paylink\">click</a> to make a payment.<br />\r\n<br />\r\nBalance Due is &euro;$dueamount<br />\r\n<br />\r\nKind Regards,<br />\r\nYour College');


INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES (
  'bookings_payment_plan_reminder_days_before',
  'Payment Plan Reminder',
  'bookings',
  '3',
  '3',
  '3',
  '3',
  '3',
  'Send Payment Plan Reminder Days Before',
  'text',
  'Bookings',
  ''
);

INSERT INTO `engine_cron_tasks`
  (`title`, `frequency`, `plugin_id`, `publish`, `action`)
  VALUES
  ('Payment Plan Reminder', '{\"minute\":[\"0\"],\"hour\":[\"0\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}', (select id from engine_plugins where name='bookings'), '0', 'cron_paymentplan_reminder');
