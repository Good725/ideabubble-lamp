/*
ts:2019-02-13 13:55:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  (
    'messaging_send_message_per_minute',
    'Message Send Limit Per Minute',
    'messaging',
    '20',
    '20',
    '20',
    '20',
    '20',
    'Message Send Limit Per Minute',
    'text',
    'Message Settings',
    ''
  );
