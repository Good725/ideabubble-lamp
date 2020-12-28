/*
ts: 2018-07-23 08:43:00
*/

ALTER TABLE plugin_messaging_messages ADD COLUMN keep_in_outbox ENUM('YES', 'NO') NOT NULL DEFAULT 'NO';
CREATE TABLE plugin_messaging_outbox_whitelist
(
  email VARCHAR(100) NOT NULL PRIMARY KEY
)
ENGINE=INNODB
CHARSET=UTF8;

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  (
    'messaging_keep_outbox',
    'Keep messages in outbox',
    'messaging',
    '0',
    '0',
    '0',
    '0',
    '0',
    'Keep messages in outbox until approved',
    'toggle_button',
    'Message Settings',
    'Model_Settings,on_or_off'
  );
