/*
ts:2017-03-15 09:30:00
*/

CREATE TABLE plugin_media_external_sync
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100) UNIQUE,
  settings  TEXT
)
ENGINE = INNODB
CHARSET = UTF8;
