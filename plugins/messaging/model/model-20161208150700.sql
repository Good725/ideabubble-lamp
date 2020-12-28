/*
ts:2016-12-08 15:07:00
*/

CREATE TABLE plugin_messaging_user_unavailable
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  from_date DATETIME,
  to_date DATETIME,
  user_id INT,
  auto_reply  TINYINT NOT NULL DEFAULT 0,
  reply_message TEXT,

  UNIQUE KEY (user_id)
)
ENGINE = INNODB
CHARSET = UTF8;

ALTER TABLE plugin_messaging_messages ADD COLUMN is_spam TINYINT NOT NULL DEFAULT 0;
ALTER TABLE plugin_messaging_messages ADD COLUMN received_when_unavailable TINYINT NOT NULL DEFAULT 0;

CREATE TABLE plugin_messaging_mute_list
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  sender  VARCHAR(100),
  created_by  INT,
  created DATETIME,
  updated_by  INT,
  updated DATETIME,
  deleted TINYINT NOT NULL DEFAULT 0,

  UNIQUE KEY (sender)
)
ENGINE = INNODB
CHARSET = UTF8;
