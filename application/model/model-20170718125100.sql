/*
ts:2017-07-18 12:51:00
*/

CREATE TABLE IF NOT EXISTS engine_object_views
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  session_id  VARCHAR(500),
  user_id INT,
  `type`  VARCHAR(50),
  object_id  VARCHAR(500),
  visited DATETIME,

  KEY (`type`, object_id(100))
)

ENGINE = INNODB
CHARSET = UTF8;
