/*
ts:2016-07-05 12:00:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES
('display_feedback_form', 'Display Feedback Form', '0', '0', '0', '0', '0', 'Display a Feedback Form on the home dashboard', 'toggle_button', 'Dashboard', 'Model_Settings,on_or_off');

INSERT IGNORE INTO `plugin_messaging_notification_templates` (`name`, `driver`, `type_id`, `subject`, `sender`, `message`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
(
  'Feedback',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
  'Feedback',
  'testing@websitecms.ie',
  '<p>The following message has been submitted by $email, using the feedback form in the CMS.</p>\n\n<blockquote>$comment</blockquote>',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

DELETE FROM plugin_messaging_notification_template_targets
  WHERE x_details = 'bcc' AND template_id in (SELECT `id` FROM `plugin_messaging_notification_templates` WHERE `name` = 'Feedback');
