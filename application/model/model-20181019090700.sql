/*
ts:2018-10-19 09:07:00
*/

CREATE TABLE engine_queue
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  object  VARCHAR(20),
  status ENUM('WAIT', 'PROCESSED', 'EXPIRED') NOT NULL DEFAULT 'WAIT',
  created DATETIME NOT NULL,
  processed DATETIME,
  expires DATETIME,

  KEY (status)
)
ENGINE = INNODB
CHARSET = UTF8;
