/*
ts:2019-06-25 09:11:00
*/

CREATE TABLE plugin_chat_rooms_has_messages_archived
(
  message_id  INT NOT NULL,
  user_id INT NOT NULL,

  KEY (message_id),
  KEY (user_id)
)
ENGINE = INNODB
CHARSET = UTF8;
