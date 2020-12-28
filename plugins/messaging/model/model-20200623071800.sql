/*
ts:2020-06-23 07:18:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  (
    'messaging_max_attachment_size_mb',
    'Maximum Attachment Size(Mb)',
    'messaging',
    '25',
    '25',
    '25',
    '25',
    '25',
    'Maximum Attachment Size(Mb)',
    'text',
    'Message Settings',
    ''
  );
