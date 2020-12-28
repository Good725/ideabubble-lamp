/*
ts:2019-12-12 17:00:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `created_by`, `publish`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('todo-alert-assignee-email', 'Alert Todo Assignees', 'EMAIL', '0', 'New Assignment', '$name,<br />\r\nAssignment: $title<br />\r\nSummary: $summary<br />\r\nDate: $date<br />\r\n<a href="$link">click to view details</a>', '0', '1', '$name,$title,$summary,$date,$todo_id,$link', 'todos');
