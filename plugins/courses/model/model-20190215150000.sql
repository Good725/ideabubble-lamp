/*
ts:2019-04-02 15:15:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES (
  'contact-form-franchisee',
  'This template is used when the user submits the contact form, while enquiring about a schedule that has a franchisee. The default recipient is the franchisee of the schedule.',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  'Enquiry for Schedule #$schedule_id: $schedule_name',
  null,
  '$schedule_id,$schedule_name',
  'courses'
);
