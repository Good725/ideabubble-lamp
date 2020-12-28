/*
ts: 2019-08-30 00:00:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `date_created`, `created_by`, `create_via_code`, `usable_parameters_in_template`)
  VALUES
  (
  'booking_subscription_confirm',
  'Subscription booking confirmation',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  'Course Subscription confirmation',
  '<p>Hello $studentname</p>

  <p><a href="$link">click</a> to confirm your course subscription</p>

  <p>Thanks</p>
  ',
  CURRENT_TIMESTAMP,
  0,
  'bookings',
  '$studentname,$link'
);
